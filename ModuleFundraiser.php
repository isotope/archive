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


class ModuleFundraiser extends ModuleIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'iso_fundraiser_manage';
	
	/**
	 * Recall product data, if db has been updated with new information.
	 * @param boolean
	 */
	protected $blnRecallProductData = false;

	
	protected $intCartId;
	
	protected $strUserId;
	
	/**
	 * URL cache array
	 * @var array
	 */
	private static $arrUrlCache = array();
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ISOTOPE GIFT REGISTRY MANAGER ###';

			return $objTemplate->parse();
		}

		// Fallback template
		if (!strlen($this->iso_fundraiser_layout))
		{
			$this->iso_fundraiser_layout = 'iso_fundraiser_manage';
		}

		$this->strTemplate = $this->iso_fundraiser_layout;

		//BUG TO BE FIXED: A session id combined with a cart type is our Cart ID.  Actual record ID field for now is not necessary unless pulling products.
		//Every time you revisit the page after closign window it is determining that we haven't been here in teh last 30 days but we have.  I need to
		//Correct the code that is not finding the cookie value and using it to grab the cart with its ID.  
		
		// Get initial values set up
		
		$this->strUserId = $this->getCustomerId();
		
		$this->intCartId = $this->userFundraiserExists($this->strUserId);
	
		if(!$this->intCartId)
		{
			$this->intCartId = $this->createNewFundraiser($this->strUserId);
			
			return parent::generate();
			
		}else{
			return parent::generate();
		}
		
		
		
	}
	
	
	/**
	 * Generate module
	 */
	protected function compile()
	{		
				
		global $objPage;

		// Call isotope_shopping_cart_onload_callback (e.g. to check permissions)
		if (is_array($GLOBALS['TL_HOOKS']['isotope_shopping_cart_onload']))
		{
			foreach ($GLOBALS['TL_HOOKS']['isotope_shopping_cart_onload'] as $callback)
			{
				if (is_array($callback))
				{
					$this->import($callback[0]);
					$this->$callback[0]->$callback[1]($arrCartItems);
				}
			}
		}
	
		$strAction = $this->Input->get('action');		
		
		switch($strAction)
		{
			case 'add_to_fundraiser':
				$intAttributeSetId = $this->getAttributeSetId($this->Input->get('aset_id'));
				
				$this->addToFundraiser($this->Input->get('id'), $intAttributeSetId, $this->Input->get('quantity_requested'));
				$this->blnRecallProductData = true;
				break;
			case 'update_fundraiser':
				$intAttributeSetId = $this->getAttributeSetId($this->Input->get('aset_id'));

				$this->updateFundraiser($this->Input->get('id'), $intAttributeSetId, $this->Input->get('quantity_requested'));
				$this->updateRegData($this->Input->get('fundraiser_title'), $this->Input->get('fundraiser_date'),$this->Input->get('fundraiser_desc'));
				$this->blnRecallProductData = true;
				break;
			case 'remove_from_fundraiser':
				//$intAttributeSetId = $this->getAttributeSetId($this->Input->get('aset_id'));

				//a new quantity of zero indicates to remove 
				$this->updateFundraiser($this->Input->get('id'), $this->Input->get('attribute_set_id'), 0);
				$this->blnRecallProductData = true;
				break;
			default:
				// Call isotope_shopping_cart_custom_action (e.g. to check permissions)
				if (is_array($GLOBALS['TL_HOOKS']['isotope_shopping_cart_custom_action']))
				{
					foreach ($GLOBALS['TL_HOOKS']['isotope_shopping_cart_custom_action'] as $callback)
					{
						if (is_array($callback))
						{
							$this->import($callback[0]);
							$this->$callback[0]->$callback[1]($strAction);
						}
					}
				}
		}
				
						
		//Hit the database for the product data for cart  This is what will happen if the user is viewing the cart directly instead of via an action
		//such as adding a product
		//$this->blnRecallProductData = true;
		$session = $this->Session->getData();
		
		if(!array_key_exists('fundraiser_data', $session['isotope']) || !sizeof($session['isotope']['fundraiser_data']) < 1 || $this->blnRecallProductData)
		{
			$arrAggregateSetData = $this->getFundraiserProducts();
			
			if(!sizeof($arrAggregateSetData))
			{
				$arrAggregateSetData = array();
			}
				
			$arrProductData = $this->getProductData($arrAggregateSetData, array('alias','name','price', 'images'), 'name');
			
			foreach($arrProductData as $data)
			{
				$arrProductIds[$data['id']] = $data['attribute_set_id'];
			}
			
			
		}	
	
		if($this->Input->post('action')=='update_fundraiser')
		{
			if(!sizeof($arrProductIds))
			{
				$arrProductIds = array();
			}
					
			foreach($arrProductIds as $k=>$v)
			{
				$this->updateFundraiser($k, $v, $this->Input->post('qty_' . $k), true);
					
			}
			
			$this->updateRegData($this->Input->post('fundraiser_title'), $this->Input->post('fundraiser_date'),$this->Input->post('fundraiser_desc'));
			
			$this->reload();
		}
		
		//actions need reload to show updated product info (until ajax comes along)
		
		if(strlen($strAction))
		{
			//referer current breaks if the back button is pressed.  Instead lets take the base of the url (index 0) and tack on .html.  Check with and without rewrites though!!
						
			$arrUrlBits = explode('/', $this->Environment->request);
						
			$strReturnUrl = $arrUrlBits[0] . '.html';
						
			$this->redirect($strReturnUrl);
		
		}
		//we can take from session here because getProductData will update the session anyway, so at this point session always has the latest data.
		//$arrProductData = $session['isotope']['cart_data'];
		
		//$arrUserTaxData contains all parameters that are needed to retrieve a given tax rule from the tax rules.  So, if a use is taxed by country, then
		//grab the tax country and get the associated rate
		//if the user is taxed by state, then grab the user's tax based on their given state
		//if the user is taxed by postal code, then grab the user's tax based on their postal code.
		//Of course, you can stack one or more tax rules as well.
		//$arrTaxRules = $this->getTaxRules($this->iso_config_id, $arrUserTaxData);
		
		if(!sizeof($arrProductData))
		{
			$arrFormattedProductData = array();
			$floatGrandTotalPrice = 0;
		}else
		{
			$arrFormattedProductData = $this->formatProductData($arrProductData);
			foreach($arrProductData as $data)
			{
				$arrPrices[] = (float)$data['price'];
				$arrQuantities[] = (int)$data['quantity_requested'];
			}
			
			for($i=0;$i<count($arrPrices);$i++)
			{
				$floatSubTotalPrice += $arrPrices[$i] * $arrQuantities[$i];
			}				
						
			$taxPriceAdjustment = 0; // $this->getTax($floatSubTotalPrice, $arrTaxRules, 'MULTIPLY');
					
			$floatGrandTotalPrice = $floatSubTotalPrice + $taxPriceAdjustment;
		}	
		
		$arrRegData = $this->getFundraiserData($this->intCartId);
		
		$this->Template->submitlabel = $GLOBALS['TL_LANG']['MSC']['fundraiser']['fundraiserManage'];
		$this->Template->fundraiserJumpTo = $this->Environment->request;
		$this->Template->products = $arrFormattedProductData;
		$this->Template->subTotalLabel = $GLOBALS['TL_LANG']['MSC']['subTotalLabel'];
		$this->Template->grandTotalLabel = $GLOBALS['TL_LANG']['MSC']['grandTotalLabel'];
		$this->Template->taxLabel = sprintf($GLOBALS['TL_LANG']['MSC']['taxLabel'], 'Sales');
		$this->Template->taxTotal = $this->Isotope->formatPriceWithCurrency($taxPriceAdjustment);
		$this->Template->subTotalPrice = $this->Isotope->formatPriceWithCurrency($floatSubTotalPrice);
		$this->Template->grandTotalPrice = $this->Isotope->formatPriceWithCurrency($floatGrandTotalPrice);
		$this->Template->noItemsInFundraiser = $GLOBALS['TL_LANG']['MSC']['fundraiser']['noItemsInFundraiser'];
		$this->Template->fundraiserTitle = $arrRegData['name'];
		$this->Template->fundraiserDate = $arrRegData['date'];
		$this->Template->fundraiserDescription = $arrRegData['description'];
		//$product['name']
		//$product['options']
		//$product['quantity_requested']
		//$product['price']
		
	}
	
	/**
	 * Get the customer's id whic is either a user Id or a session Id.
	 * 
	 * @return string
	 */
	protected function getCustomerId()
	{		
		$this->import('FrontendUser', 'User');
		
		//Check to see if the user is logged in.  If not, cart data should be found in session data.
		if (!FE_USER_LOGGED_IN)
		{	
			
			if(!strlen($this->Input->cookie($this->strCartCookie)))	
			{	
				//problem #1 - not retrieving the cookie!
				$intCookieDuration = $this->getCookieTimeWindow($this->iso_config_id);
						
				$this->strCartHash = sha1(session_id().$this->strIp.$this->iso_config_id.$this->strCartCookie);
				
				setcookie($this->strCartCookie, $this->strCartHash, (time() + ($intCookieDuration * 86400)),  $GLOBALS['TL_CONFIG']['websitePath']);
				
				//$strReturnURL = ltrim($session['referer']['current'], '/');
				//Have to set the cookie with a reload.
				//$this->reload();
				
			}else{
				return $this->Input->cookie($this->strCartCookie);
			}	
		}else{
	 		return $this->User->id;
		}
		
	}
	
	/**
	 * Get the Fundraiser Extra Data by parent Cart ID.
	 * 
	 * @return string
	 */
	protected function getFundraiserData($strCartId)
	{		
		$objRegData = $this->Database->prepare("SELECT name, description, date FROM tl_fundraiser WHERE pid=". $strCartId)
										 ->limit(1)
										 ->execute();
		
		if($objRegData->numRows < 1)
		{
			return array();
		}
		
		$arrRegData = $objRegData->fetchAllAssoc();
		
		//Get all data for record;
		foreach($arrRegData as $data)
		{
			$arrRegXtraData['name'] = $data['name'];
			$arrRegXtraData['description'] = $data['description'];
			$arrRegXtraData['date'] = $data['date'];
		}
		
		return $arrRegXtraData;

	}
	
	//!@todo images have changed, this will not work here
	protected function formatProductData($arrProductData)
	{
		global $objPage;
		
 		foreach($arrProductData as $row)
		{
			$intTotalPrice = $row['price'] * $row['quantity_requested'];
														
			$arrFormattedProductData[] = array
			(
				'id'		=> $row['id'],
				'image'				=> 'isotope/' . substr($row['alias'], 0, 1) . '/' . $row['alias'] . '/' . $GLOBALS['TL_LANG']['MSC']['imagesFolder'] . '/' . $GLOBALS['TL_LANG']['MSC']['thumbnail_images_folder'] . '/' . $row['images'],
				'name'				=> $row['name'],
				'link'				=> $this->generateProductLink($row['alias'], $row, $this->Isotope->Config->productReaderJumpTo, $row['attribute_set_id'], 'id'),
				'price'				=> $this->Isotope->formatPriceWithCurrency($row['price']),
				'total_price'		=> $this->Isotope->formatPriceWithCurrency($intTotalPrice),
				'quantity'			=> $row['quantity_requested'],
				'remove_link'		=> $this->generateActionLinkString('remove_from_fundraiser', $row['id'], array('attribute_set_id'=>$row['attribute_set_id'],'quantity'=>0), $objPage->id),
				'remove_link_title' => sprintf($GLOBALS['TL_LANG']['MSC']['removeProductLinkTitle'], $row['name'])
			
			);

		}
		
		
		return $arrFormattedProductData;
	
	}
	
	
	
	/**
	 * Get product data for the shopping cart.  In the future to save load time, store the data for each product as an array in the session after
	 * storing in the database so that we may quickly grab the session data instead, saving database calls.
	 * @param array
	 * @param array
	 * @return array
	 */
	protected function getProductData($arrAggregateSetData, $arrFieldNames, $strOrderByField)
	{
				
		$strFieldList = join(',', $arrFieldNames);
		
		//var_dump($arrAggregateSetData);
		
		foreach($arrAggregateSetData as $data)
		{
			$arrProductsAndTables[$data['storeTable']][] = array($data['id'], $data['quantity_requested']); //Allows us to cycle thru the correct table and product ids collections.
			
			//The productID list for this storetable, used to build the IN clause for the product gathering.
			$arrProductIds[$data['storeTable']][] = $data['id'];
			
			//This is used to gather extra fields for a given product by store table.
			$arrProductExtraFields[$data['storeTable']][$data['id']]['attribute_set_id'] = $data['attribute_set_id'];
			$arrProductExtraFields[$data['storeTable']][$data['id']]['quantity_requested'] = $data['quantity_requested'];
			
		}
		
		if(!sizeof($arrProductsAndTables))
		{
			$arrProductsAndTables = array();
		}		
						
		$arrTotalProductsInCart = array();
					
		foreach($arrProductsAndTables as $k=>$v)
		{
							
			$strCurrentProductList = join(',', $arrProductIds[$k]);
						
			$objProducts = $this->Database->prepare("SELECT id, " . $strFieldList . " FROM " . $k. " WHERE id IN(" . $strCurrentProductList . ") ORDER BY " . $strOrderByField . " ASC")
										  ->execute();
			
			if($objProducts->numRows < 1)
			{
				return array();
			}
			
			$arrProductsInCart = $objProducts->fetchAllAssoc();
						
			foreach($arrProductsInCart as $product)
			{
				$arrProducts[$product['id']]['id'] = $product['id'];
				
				foreach($arrFieldNames as $field)
				{
					
					$arrProducts[$product['id']][$field] = $product[$field];		
				}
				
				$arrProducts[$product['id']]['attribute_set_id'] = $arrProductExtraFields[$k][$product['id']]['attribute_set_id'];
				$arrProducts[$product['id']]['quantity_requested'] = $arrProductExtraFields[$k][$product['id']]['quantity_requested'];
			}
			
	
								
			$arrTotalProductsInCart = array_merge($arrTotalProductsInCart, $arrProducts);
		}
		
		//Retrieve current session data, only if a new product has been added or else the cart updated in some way, and reassign the cart product data
		$session = $this->Session->getData();
		
		//clean old cart data
		unset($session['isotope']['fundraiser_data']);
		
		//set new cart data
		$session['isotope']['fundraiser_data'] = $arrTotalProductsInCart;
		
		
		$session['isotope']['cart_id'] = $this->userFundraiserExists($this->strUserId);
		
		
		$this->Session->setData($session);
				
		return $arrTotalProductsInCart;
	}
	
	
	
	/**
	 * Get basic cart data including the corresponding aggregate set IDs for the products in the cart currently. (if any for the customer's cart)
	 * 
	 */
	protected function getFundraiserProducts()
	{		
		//$session = $this->Session->getData();
				
		//$this->strUserId = $this->getCustomerId();
				
		$strFieldClause = $this->determineUserIdType($this->strUserId);
				
		/*if(!array_key_exists('cart_id', $session['isotope']))
		{
			//if the cart Id doesn't exist in the session array, get it from the db based on user information cookie hash or actual user id.
			$this->intCartId = $this->userCartExists($this->strUserId);
			
		}else{
			$this->intCartId = $session['isotope']['cart_id'];
		}*/		
		
		//do not query by cart id as it won't ever be stored past session, we only need the session value from the cookie to pull the right cart for the job.
		$objCartData = $this->Database->prepare("SELECT ci.* FROM tl_iso_cart c INNER JOIN tl_iso_cart_items ci ON c.id=ci.pid WHERE ci.pid=? AND c.cart_type_id=? AND c." . $strFieldClause)
										  ->execute($this->intCartId, 2);
										  
		if($objCartData->numRows < 1)
		{
			//Create a new cart for the user.
			//$this->intCartId = $this->createNewCart($strUserId);
		}else{
			
			$arrCartData = $objCartData->fetchAllAssoc();
			
			//Get all store tables for each given attribute_set_id record;
			foreach($arrCartData as $data)
			{
				$arrAsetIds[] = $data['attribute_set_id'];
			}
											
			$arrTableInfo = $this->getStoreTables($arrAsetIds);
							
			$i = 0;

			foreach($arrCartData as $row)
			{							
				
				$arrCartData[$i]['storeTable'] = $arrTableInfo[$row['attribute_set_id']];
				$i++;
			}
			
		}
				
		return $arrCartData;
		
	}
	
	protected function getStoreTables($arrAsetIds)
	{
		$strAsetIds = join(',', $arrAsetIds);
				
		$objStoreTables = $this->Database->prepare("SELECT id, storeTable FROM tl_product_attribute_sets WHERE id IN (" . $strAsetIds . ")")
										 ->execute();
		
		if($objStoreTables->numRows < 1)
		{
			return array();
		}
		
		//return array('id' => value, 'storeTable' => value);
		
		$arrStoreTables = $objStoreTables->fetchAllAssoc();
		
		foreach($arrStoreTables as $row)
		{
			$arrTableInfo[$row['id']] = $row['storeTable'];
		}
			
		return $arrTableInfo;
		
	}
		
	protected function userFundraiserExists($strUserId)
	{
		$strClause = $this->determineUserIdType($strUserId);
						
		$objUserCart = $this->Database->prepare("SELECT id FROM tl_iso_cart WHERE cart_type_id=? AND " . $strClause)
									  ->limit(1)
									  ->execute(2);	//again this will vary later.
		
		if($objUserCart->numRows < 1)
		{
			return false;
			
		}
				
		return $objUserCart->id;
	
	}
	
	/**
	 * Add one or more units of a given product to the cart
	 * @param integer
	 * @param integer
	 * @param array
	 * @return boolean
	 */
	protected function addToFundraiser($intProductId, $intAttributeSetId, $intQuantity)
	{
	
		//$this->strUserId = $this->getCustomerId();	//either user Id or session Id.

		//echo $this->userCartExists($strUserId);
		//Step 1: If the user has an existing cart by session Id or user Id then retrieve the cart Id
		//$this->intCartId = $this->userCartExists($this->strUserId);
		
		if($this->intCartId!==false)
		{
			if($this->productExistsInFundraiser($this->intCartId, $intProductId, $intAttributeSetId))
			{
				$strMethod = 'update';
			}else{
				$strMethod = 'insert';
			}
		
		}else{
			//will this ever happen? it shouldn't.
			echo 'yes';
			//$this->intCartId = $this->createNewCart($strUserId);
			$strMethod = 'insert';
		}
		
		
		//Step 2: Insert or update if the product exists.
		switch($strMethod)
		{
			case 'insert':
				//$objTask = $this->Database->prepare("INSERT INTO tl_task %s")->set($arrSet)->execute();
				//$pid = $objTask->insertId;
				$time = time();
			
				// Insert task
				$arrSet = array
				(
					'pid'					=> $this->intCartId,
					'tstamp' 				=> $time,
					'product_id'			=> $intProductId,
					'attribute_set_id'		=> $intAttributeSetId,
					'quantity_requested'	=> $intQuantity//,
					//'options'		=> serialize($arrProductOptions)
				);
								
				$objCartItem = $this->Database->prepare("INSERT INTO tl_iso_cart_items %s")->set($arrSet)->execute();
				
				break;
				
			case 'update':
				$this->Database->prepare("UPDATE tl_iso_cart_items SET quantity_requested=(quantity_requested+" . $intQuantity . ") WHERE product_id=? AND attribute_set_id=? AND pid=?")
							   ->execute($intProductId, $intAttributeSetId, $this->intCartId);
				break;
			default:
				break;
		}
				
	}
	
	protected function createNewFundraiser($strUserId)
	{
		$time = time();
		
		$arrSet = array
		(
			'pid'						=> (FE_USER_LOGGED_IN ? $strUserId : '0'),
			'tstamp'					=> $time,
			'session'					=> (!FE_USER_LOGGED_IN ? $strUserId : ''),
			'config_id'					=> $this->iso_config_id,	
		);
		
		
		$objCart = $this->Database->prepare("INSERT INTO tl_iso_cart %s")->set($arrSet)->execute();
		
		// ************BOF GIFT REGISTRY SPECIFIC****************
		$arrRegValues = array
		(
			'pid'						=> $objCart->insertId,
			'tstamp'					=> time(),
			'name'						=> 'Insert a Name for Your Fundraiser',
			'description'				=> 'Insert a Description for your Fundraiser',
			'date'						=> time()
		);
		
		$objRegFields = $this->Database->prepare("INSERT INTO tl_fundraiser %s")->set($arrRegValues)->execute();
		// ************EOF GIFT REGISTRY SPECIFIC****************
		
		return $objCart->insertId;
	}
	
	/**
	 * Remove one or more units of a given product from the cart
	 * @param integer
	 * @param integer
	 * @param array
	 * @return boolean
	 */
	protected function updateFundraiser($intProductId, $intAttributeSetId, $intQuantity, $blnOverwriteQty = false)
	{
		//Get visitor's cart
		
		//Prepare & execute the query.
		if($intQuantity==0)
		{
			$strQuery = "DELETE FROM tl_iso_cart_items WHERE product_id=? AND attribute_set_id=? AND pid=?";

		}else{
			if($blnOverwriteQty)
			{
				$strClause = $intQuantity;
			}else{
				$strClause = "(quantity_requested+" . $intQuantity . ")";
			}
			
			$strQuery = "UPDATE tl_iso_cart_items SET quantity_requested=$strClause WHERE product_id=? AND attribute_set_id=? AND pid=?";			
			
		}
				
		$this->Database->prepare($strQuery)
					   ->execute($intProductId, $intAttributeSetId, $this->intCartId, 2);
	
		$this->blnRecallProductData = true;
		
	}
	
	protected function updateRegData($regName, $regDate, $regDesc)
	{
	
	// ************BOF GIFT REGISTRY SPECIFIC****************
		
		$arrRegDate = explode('/',$regDate);
		
		//var_dump($arrRegDate);
		
		$regTime = mktime(0, 0, 0, $arrRegDate[0], $arrRegDate[1], $arrRegDate[2]);		
		
		
		$regQuery = "UPDATE tl_fundraiser SET name=?, description=?, date=? WHERE pid=?";	
		
		$this->Database->prepare($regQuery)
					   ->execute($regName, $regDesc, $regTime, $this->intCartId);
					   
		// ************EOF GIFT REGISTRY SPECIFIC****************
	
	}
	
	protected function productExistsInFundraiser($intCartId, $intProductId, $intAttributeSetId)
	{
		//check session if not then we know we need to add it!
		$session = $this->Session->getData();
		
		//first check the session to save a db call to the cart.  It should always be in here. - future.
		//$session['isotope']['fundraiser_data'][] = array(<product keys and values>);
		
		//query for the product id for the given cart, product and attribute set.
		$objproductExistsInFundraiser = $this->Database->prepare("SELECT COUNT(*) as count FROM tl_iso_cart_items WHERE product_id=? AND pid=? AND attribute_set_id=?")
												 ->limit(1)
												 ->execute($intProductId, $intCartId, $intAttributeSetId);
	
		if($objproductExistsInFundraiser->numRows < 1)
		{
			return false;
		}
		
		if($objProductExistsInCart->count < 1)
		{
			return false;
		}
		
		return true;
		
	}
	

	
	//!@todo THIS IS WRONG!
	protected function getCookieTimeWindow($intStoreId)
	{
		$objCookieTimeWindow = $this->Database->prepare("SELECT cookie_duration FROM tl_iso_config WHERE id=?")->limit(1)->execute($intStoreId);
		
		if (!$objCookieTimeWindow->numRows)
		{
			return 0;
		}
				
		return $objCookieTimeWindow->cookie_duration;
	}
	
	
	/**
	 * Generate a URL and return it as string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateProductUrl($arrProduct, $intJumpTo, $strProductIdKey = 'id', $blnAddArchive=false)
	{
		global $objPage;
		$strCacheKey = $strProductIdKey . '_' . $arrProduct[$strProductIdKey] . '_' . $arrProduct['tstamp'];

		// Load URL from cache
		if (array_key_exists($strCacheKey, self::$arrUrlCache))
		{
			return self::$arrUrlCache[$strCacheKey];
		}

		$strUrl = ampersand($this->Environment->request, ENCODE_AMPERSANDS);

		// Get target page
		$objJump = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")->limit(1)->execute($intJumpTo);
	
		if ($objJump->numRows > 0)
		{
			$strUrl = ampersand($this->generateFrontendUrl($objJump->fetchAssoc(), '/product/' . $arrProduct['alias']));
		}
		else
		{
			$strUrl = ampersand($this->generateFrontendUrl(array('id'=>$objPage->id, 'alias'=>$objPage->alias), '/details/product/' . $arrProduct['alias']));
		}

		self::$arrUrlCache[$strCacheKey] = $strUrl;
			
		return self::$arrUrlCache[$strCacheKey];
	}

	

	/**
	 * Generate a link and return it as string
	 * @param string
	 * @param object
	 * @param boolean
	 * @return string
	 */
	protected function generateProductLink($strLink, $arrProduct, $intJumpTo, $strProductIdKey = 'id', $blnAddArchive=false)
	{
		return 	$this->generateProductUrl($arrProduct, $intJumpTo, $strProductIdKey, $blnAddArchive);
	}
}

