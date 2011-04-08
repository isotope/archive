<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class IsotopeMember extends Frontend
{

	/**
	 * Trigger the correct function based on Isotope version and member login status
	 * @params mixed
	 * @return mixed
	 */
	public function triggerAction()
	{
		$this->import('Isotope');

		$blnCompatible = version_compare(ISO_VERSION, '0.3', '<');
		$arrParam = func_get_args();

		if (FE_USER_LOGGED_IN && $blnCompatible)
		{
			$this->import('FrontendUser', 'User');
			return call_user_func_array(array($this, 'assignGroupsCompatible'), $arrParam);
		}
		elseif (FE_USER_LOGGED_IN)
		{
			$this->import('FrontendUser', 'User');
			return call_user_func_array(array($this, 'assignGroups'), $arrParam);
		}
		elseif ($blnCompatible)
		{
			return call_user_func_array(array($this, 'addMemberCompatible'), $arrParam);
		}
		else
		{
			return call_user_func_array(array($this, 'addMember'), $arrParam);
		}
	}
	
	
	/**
	 * Create a new member on checkout
	 * @param object
	 * @param object
	 * @return bool
	 */
	protected function addMember($objOrder, $objCart)
	{
		// Cancel if createMember is not enabled in store config
		if ($this->Isotope->Config->createMember != 'always' && $this->Isotope->Config->createMember != 'product' && $this->Isotope->Config->createMember != 'guest')
			return true;

		if ($this->Isotope->Config->createMember == 'product')
		{
			$blnCreateMember = false;
			$arrProducts = $objCart->getProducts();
			
			foreach( $arrProducts as $objProduct )
			{
				if ($objProduct->createMember)
				{
					$blnCreateMember = true;
					break;
				}
			}
			
			if (!$blnCreateMember)
				return true;
		}
		elseif ($this->Isotope->Config->createMember == 'guest')
		{
			// @todo add guest option
			return true;
		}
		
		// Prepare address. This will dynamically use all fields available in both member and address
		$arrAddress = deserialize($objOrder->billing_address, true);
		$arrData = array_intersect_key($arrAddress, array_flip($this->Database->getFieldNames('tl_member')));
		unset($arrData['id'], $arrData['pid']);
		$arrData['street'] = $arrAddress['street_1'];
		$arrData['username'] = $arrData['username'] ? $arrData['username'] : $arrData['email'];
		
		// Verify the user does not yet exist (especially when using email address)
		$objMember = $this->Database->prepare("SELECT * FROM tl_member WHERE username=?")->execute($arrData['username']);
		if ($objMember->numRows)
		{
			$this->log('Could not create member for order ID '.$objOrder->id.', username "'.$arrData['username'].'" exists.', __METHOD__, TL_ERROR);
			return true;
		}
		
		
		// Create member, based on ModuleRegistration::createMember from Contao 2.9
		$arrData['tstamp'] = time();
		$arrData['login'] = '1';
		$arrData['activation'] = md5(uniqid(mt_rand(), true));
		$arrData['dateAdded'] = $arrData['tstamp'];
		$arrData['groups'] = serialize($this->Isotope->Config->createMember_groups);
		$arrData['newsletter'] = in_array('newsletter', $this->Config->getActiveModules()) ? deserialize($this->Isotope->Config->createMember_newsletters, true) : array();

		// Disable account
//		$arrData['disable'] = 1;
		
		// Create random password
		$strPassword = $this->createRandomPassword();
		$strSalt = substr(md5(uniqid(mt_rand(), true)), 0, 23);
		$arrData['password'] = sha1($strSalt . $strPassword) . ':' . $strSalt;

		// Create user
		$objNewUser = $this->Database->prepare("INSERT INTO tl_member %s")->set($arrData)->execute();
		$insertId = $objNewUser->insertId;

		// Assign home directory
		if ($this->createMember_assignDir && is_dir(TL_ROOT . '/' . $this->createMember_homeDir))
		{
			$this->import('Files');
			$strUserDir = strlen($arrData['username']) ? $arrData['username'] : 'user_' . $insertId;

			// Add the user ID if the directory exists
			if (is_dir(TL_ROOT . '/' . $this->createMember_homeDir . '/' . $strUserDir))
			{
				$strUserDir .= '_' . $insertId;
			}

			new Folder($this->createMember_homeDir . '/' . $strUserDir);

			$this->Database->prepare("UPDATE tl_member SET homeDir=?, assignDir=1 WHERE id=?")
						   ->execute($this->createMember_homeDir . '/' . $strUserDir, $insertId);
		}

		// HOOK: send insert ID and user data
		if (isset($GLOBALS['TL_HOOKS']['createNewUser']) && is_array($GLOBALS['TL_HOOKS']['createNewUser']))
		{
			foreach ($GLOBALS['TL_HOOKS']['createNewUser'] as $callback)
			{
				$this->import($callback[0]);
				$this->$callback[0]->$callback[1]($insertId, $arrData);
			}
		}
		
		$arrData['password'] = $strPassword;
		$arrData['domain'] = $this->Environment->host;
//		$arrData['link'] = $this->Environment->base . $this->Environment->request . (($GLOBALS['TL_CONFIG']['disableAlias'] || strpos($this->Environment->request, '?') !== false) ? '&' : '?') . 'token=' . $arrData['activation'];

		// Support newsletter extension
		if (count($arrData['newsletter']) > 0)
		{
			$objChannels = $this->Database->execute("SELECT title FROM tl_newsletter_channel WHERE id IN(". implode(',', array_map('intval', $arrData['newsletter'])) .")");
			$arrData['channel'] = $arrData['channels'] = implode("\n", $objChannels->fetchEach('title'));
		}
		unset($arrData['newsletter']);

		// Send activation e-mail
		if ($this->Isotope->Config->createMember_mail && $objOrder->iso_customer_email)
		{
			$this->Isotope->sendMail($this->Isotope->Config->createMember_mail, $objOrder->iso_customer_email, $GLOBALS['TL_LANGUAGE'], $arrData);
		}

		// Inform admin if no activation link is sent
		if ($this->Isotope->Config->createMember_adminMail && $objOrder->iso_sales_email)
		{
			$this->Isotope->sendMail($this->Isotope->Config->createMember_adminMail, $objOrder->iso_sales_email, $GLOBALS['TL_LANGUAGE'], $arrData);
		}

		// Assign the current order to the new member
		if (version_compare(ISO_VERSION, '0.3', '<'))
		{
			$this->Database->prepare("UPDATE tl_iso_orders SET pid=? WHERE id=?")->executeUncached($insertId, $objOrder->id);
		}
		else
		{
			$objOrder->pid = $insertId;
			$objOrder->save();
		}
		
		return true;
	}
	
	
	/**
	 * Assign current member to the product groups
	 * @param object
	 * @param object
	 * @return bool
	 */
	protected function assignGroups($objOrder, $objCart)
	{
		return true;
	}
	
	
	/**
	 * Backward-compatible function for Isotope 0.2
	 */
	protected function addMemberCompatible($orderId, $blnCheckout, $objModule)
	{
		if (!$blnCheckout || FE_USER_LOGGED_IN || !$this->Isotope->Config->createMember)
			return $blnCheckout;
		
		$objOrder = new IsotopeOrder();
		if (!$objOrder->findBy('id', $orderId))
		{
			$this->log('Could not create member for order ID '.$orderId.' (Order not found).', __METHOD__, TL_ERROR);
			return $blnCheckout;
		}
		
		$strCustomerName = '';
		$strCustomerEmail = '';

		if ($objOrder->billingAddress['email'] != '')
		{
			$strCustomerName = $objOrder->billingAddress['firstname'] . ' ' . $objOrder->billingAddress['lastname'];
			$strCustomerEmail = $objOrder->billingAddress['email'];
		}
		elseif ($objOrder->shippingAddress['email'] != '')
		{
			$strCustomerName = $objOrder->shippingAddress['firstname'] . ' ' . $objOrder->shippingAddress['lastname'];
			$strCustomerEmail = $objOrder->shippingAddress['email'];
		}
		
		if (trim($strCustomerName) != '')
		{
			$strCustomerEmail = sprintf('%s <%s>', $strCustomerName, $strCustomerEmail);
		}
		
		if ($strCustomerEmail != '')
		{
			$objOrder->iso_customer_email = $strCustomerEmail;
		}
		
		$objOrder->iso_sales_email = $GLOBALS['TL_ADMIN_NAME'] != '' ? sprintf('%s <%s>', $GLOBALS['TL_ADMIN_NAME'], $GLOBALS['TL_ADMIN_EMAIL']) : $GLOBALS['TL_ADMIN_EMAIL'];
		
		return $this->addMember($objOrder, $this->Isotope->Cart);
	}

	
	/**
	 * Backward-compatible function for Isotope 0.2
	 */
	protected function assignGroupsCompatible($orderId, $blnCheckout, $objModule)
	{
		return $blnCheckout;
	}


	/**
	 * The letter l (lowercase L) and the number 1 have been removed,
	 * as they can be mistaken for each other.
	 * From http://www.totallyphp.co.uk/code/create_a_random_password.htm
	 */
	private function createRandomPassword()
	{
	    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
	    srand((double)microtime()*1000000);
	    $i = 0;
	    $pass = '' ;

	    while ($i <= 7) {
	        $num = rand() % 33;
	        $tmp = substr($chars, $num, 1);
	        $pass = $pass . $tmp;
	        $i++;
	    }

	    return $pass;
	}
}

