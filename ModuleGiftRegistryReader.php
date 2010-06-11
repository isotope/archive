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


class ModuleGiftRegistryReader extends ModuleIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'iso_registry_full';
	
	
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ISOTOPE GIFT REGISTRY READER ###';

			return $objTemplate->parse();
		}

		// Fallback template
		if (!strlen($this->iso_registry_reader))
		{
			$this->iso_registry_reader = 'iso_registry_full';
		}

		$this->strTemplate = $this->iso_registry_reader; 
		
		//GET SOME INITIAL VALUES
		
		$this->intCartId = $this->Input->get('cartid');

		
		return parent::generate();
		
	}
	
	
	/**
	 * Generate module
	 */
	protected function compile()
	{	
			
		$arrAggregateSetData = $this->getRegistryProducts();
		
		if(!sizeof($arrAggregateSetData))
		{
			$arrAggregateSetData = array();
		}
			
		
		$arrProductData = $this->getRegistryProductData($arrAggregateSetData, array('alias','name','price', 'images','available_online'), 'name');
		
		foreach($arrProductData as $data)
		{
			$arrProductIds[$data['id']] = $data['attribute_set_id'];
		}
			
		
		if(!sizeof($arrProductData))
		{
			$arrFormattedProductData = array();
		}else{
			$arrFormattedProductData = $this->formatProductData($arrProductData);	
		}
		
		
		$arrRegData = $this->getRegistryData($this->intCartId);
			
		$this->Template->registryTitle = $arrRegData['name'];
		$this->Template->registryOwnerName = $arrRegData['firstname'] . ' ' . $arrRegData['lastname'];
		$this->Template->registryDate = date('m/d/Y', $arrRegData['date']);
		$this->Template->registryDescription = $arrRegData['description'];
		$this->Template->cartJumpTo = $this->generateFrontendUrl($this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($this->iso_cart_jumpTo)->fetchAssoc());
		$this->Template->products = $arrFormattedProductData;
		$this->Template->noItemsInCart = $GLOBALS['TL_LANG']['MSC']['registry']['noItemsInRegistry'];
		
		//$product['name']
		//$product['options']
		//$product['quantity_requested']
		//$product['price']
		
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
				'quantity_remaining'=> $row['quantity_requested'] - $row['product_quantity'],
				'source_cart_id'	=> $this->intCartId,
				'add_link'			=> ($row['available_online']=='1' ? '<a href="' . $this->generateActionLinkString('add_to_cart', $row['id'], array('aset_id'=>$row['attribute_set_id'],'quantity_requested'=>1, 'source_cart_id'=>$this->intCartId), $this->Isotope->Config->cartJumpTo) . '">' . $this->generateImage('system/modules/isotope/html/addToCart.gif') . '</a>' : $GLOBALS['TL_LANG']['MSC']['notAvailableOnline']),
				'add_link_title' 	=> "Add To Cart"
			
			);

		}
		
		return $arrFormattedProductData;
		
	}
	
	
	/**
	 * Get basic cart data including the corresponding aggregate set IDs for the products in the cart currently. (if any for the customer's cart)
	 * 
	 */
	
	protected function getRegistryProducts()
	{		

		$objCartData = $this->Database->prepare("SELECT ci.* FROM tl_iso_cart c INNER JOIN tl_iso_cart_items ci ON c.id=ci.pid WHERE ci.pid=? AND c.cart_type_id=?")
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
	
	/**
	 * Get product data for the shopping cart.  In the future to save load time, store the data for each product as an array in the session after
	 * storing in the database so that we may quickly grab the session data instead, saving database calls.
	 * @param array
	 * @param array
	 * @return array
	 */
	protected function getRegistryProductData($arrAggregateSetData, $arrFieldNames, $strOrderByField)
	{
				
		$strFieldList = join(',', $arrFieldNames);
		
		foreach($arrAggregateSetData as $data)
		{			
			$arrProductsAndTables[$data['storeTable']][] = array($data['id'], $data['quantity_requested']); //Allows us to cycle thru the correct table and product ids collections.
			
			//The productID list for this storetable, used to build the IN clause for the product gathering.
			$arrProductIds[$data['storeTable']][] = $data['id'];
			
			//This is used to gather extra fields for a given product by store table.
			$arrProductExtraFields[$data['storeTable']][$data['id']]['attribute_set_id'] = $data['attribute_set_id'];
			$arrProductExtraFields[$data['storeTable']][$data['id']]['quantity_requested'] = $data['quantity_requested'];			
			$arrProductExtraFields[$data['storeTable']][$data['id']]['quantity_sold'] = $data['quantity_sold'];
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
				$arrProducts[$product['id']]['quantity_sold'] = $arrProductExtraFields[$k][$product['id']]['quantity_sold'];
			}
	
								
			$arrTotalProductsInCart = array_merge($arrTotalProductsInCart, $arrProducts);
		}
				
		return $arrTotalProductsInCart;
	}
	
	protected function getRegistryData($cartID)
	{
		// Query for results			
		$arrRegDataQuerystr = "SELECT r.name, r.date, r.description, m.firstname, m.lastname FROM tl_registry r, tl_member m, tl_iso_cart c WHERE c.id=? AND r.pid = c.id AND c.pid = m.id AND c.cart_type_id =?";

		$objRegDataQuery = $this->Database->prepare($arrRegDataQuerystr)
				   						->execute($cartID,2);
				   						
		if ($objRegDataQuery->numRows)
		{
			$arrRegData = $objRegDataQuery->fetchAssoc();
		}

		return $arrRegData;
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

