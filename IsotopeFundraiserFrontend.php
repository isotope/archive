<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class IsotopeFundraiserFrontend extends IsotopeFrontend
{

	/**
	 * Isotope object
	 * @var object
	 */
	protected $Isotope;


	public function __construct()
	{
		parent::__construct();

		$this->import('Isotope');
	}


	/**
	 * Callback for add_to_fundraiser button
	 *
	 * @access	public
	 * @param	object
	 * @return	void
	 */
	//!@todo $objModule is always defined, rework to use it and make sure the module config field is in palettes
	public function addToMyFundraiser($objProduct, $objModule=null)
	{
		if(!$this->Isotope->Fundraiser)
		{
			$this->Isotope->Fundraiser = new IsotopeFundraiser();
			$this->Isotope->Fundraiser->initializeFundraiser();
		}
		$this->Isotope->Fundraiser->addProduct($objProduct, ((is_object($objModule) && $objModule->iso_use_quantity && intval($this->Input->post('quantity_requested')) > 0) ? intval($this->Input->post('quantity_requested')) : 1));

		$this->jumpToOrReload($objModule->iso_fundraiser_jumpTo);
	}


	/**
	 * Callback for add_to_cart_from_fundraiser button
	 *
	 * @access	public
	 * @param	object, object, int
	 * @return	array
	 */

	public function moveToCart($objProduct, $objModule=null)
	{
		$this->Isotope->Fundraiser->transferToCart($this->Isotope->Cart, $objProduct, (intval($this->Input->post('quantity_requested')) > 0) ? intval($this->Input->post('quantity_requested')) : 1, false);

		$this->jumpToOrReload($objModule->iso_cart_jumpTo);

	}



	/**
	 * Callback for isoButton Hook.
	 */
	public function fundraiserButton($arrButtons)
	{
		if (TL_MODE == 'FE' && !$this->fundraiserExists())
		{
			return $arrButtons;
		}

		$arrButtons['fundraiser'] = array('label'=>$GLOBALS['TL_LANG']['MSC']['buttonLabel']['fundraiser'], 'callback'=>array('IsotopeFundraiserFrontend', 'addToMyFundraiser'));

		return $arrButtons;
	}





	/**
	 * Callback for isoButton Hook.
	 */
	public function fundraiserCartButton($arrButtons)
	{

		$arrButtons['fundraiserCart'] = array('label'=>$GLOBALS['TL_LANG']['MSC']['buttonLabel']['fundraiserCart'], 'callback'=>array('IsotopeFundraiserFrontend', 'moveToCart'));

		return $arrButtons;
	}



	/**
	 * Callback for Checkout Shipping Address Process
	 */
	public function fundraiserAddress($arrOptions, $field, $objModule)
	{
		if($field=='shipping_address' && $objModule->type='iso_checkout')
		{

			//Determine whether products in the cart are from a fundraiser
			$objItems = $this->Database->prepare("SELECT fundraiser_id FROM tl_iso_cart_items WHERE pid=?")->execute($this->Isotope->Cart->id);
			while($objItems->next())
			{
				if($objItems->fundraiser_id)
				{
					$objFundraiser = new IsotopeFundraiser();
					$objFundraiser->findBy('id',$objItems->fundraiser_id);
					$arrAddress = $objFundraiser->shippingAddress;

					if (!in_array($arrAddress['country'],  $this->Isotope->Config->shipping_countries))
						continue;

					$arrAddresses[$arrAddress['id']] = array
					(
						'value'		=> $arrAddress['id'],
						'label'		=> $GLOBALS['TL_LANG']['MSC']['shipToFundraiser'] . '<br /><span>' . $this->Isotope->generateAddressString($arrAddress, $this->Isotope->Config->shipping_fields) . '</span>',
					);
				}
			}
		}

		if(count($arrAddresses))
		{
			foreach($arrAddresses as $address)
				$arrOptions[] = $address;
		}

		return $arrOptions;
	}


	/**
	 * Check if the logged in user has a fundraiser or not
	 * @return bool
	 */
	public function fundraiserExists()
	{
		$this->import('FrontendUser', 'User');
		$blnExists = false;
		if (!FE_USER_LOGGED_IN)
		{
			return $blnExists;
		}
		$objRegistries = $this->Database->execute("SELECT * FROM tl_iso_fundraiser WHERE pid={$this->User->id}");
		if($objRegistries->numRows)
		{
			if(!$this->Isotope->Fundraiser)
			{
				$this->Isotope->Fundraiser = new IsotopeFundraiser();
				$this->Isotope->Fundraiser->initializeFundraiser();
			}
			$blnExists = true;
		}
		return $blnExists;
	}

}