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

class IsotopeInventory extends Controller
{	
	public function __construct()
	{
		parent::__construct();
	
		$this->import('Database');	
		$this->import('Isotope');

	}
	
	/** 
	 * hook function to update product inventory levels
	 * @access public
	 * @param integer $intOrderid
	 * @param boolean $blnCheckout
	 * @param object $objOrder
	 * @return boolean
	 */
	public function updateInventory($intOrderId, $blnCheckout, $objOrder)
	{
		if(!$blnCheckout)
			return false; 
			
		$arrWarehouses = deserialize($this->Isotope->Config->warehouses, true);
		
		if(!$this->Isotope->Config->enableInventory || !count($arrWarehouses))
			return true;
			
		//TODO: determine closest eligible warehouse the items should be shipped from
		/*
			
		*/
		
		//For now just grab the first warehouse...
		$intWarehouseId = $arrWarehouses[0];		
	
		$arrProducts = $this->Isotope->Cart->getProducts();
	
		foreach($arrProducts as $i=>$objProduct)
		{
			$strFields = $intWarehouseId.','.time().','.$objProduct->id.','.(-1)*$objProduct->quantity_requested;
			$arrRows[] = $strFields;
		}
	
		$this->updateProductInventory($arrRows);
	
		return true;
	}
	
	/** 
	 * Update a product inventory level.  Will only be called if product allows backorder or adequate quantities exist.
	 * @access protected
	 * @param integer $intWarehouseId
	 * @param integer $intProductId
	 * @param integer $intQty
	 */
	protected function updateProductInventory($arrRows)
	{				
		$strInserts = implode("),(", $arrRows);
		
		//Update latest inventory record
		$this->Database->query("INSERT INTO tl_iso_inventory (pid,tstamp,product_id,quantity) VALUES ($strInserts)");
	}
	
	/** 
	 * Check availability of a given product based on backorder setting and inventory level
	 * @access public
	 * @param integer $intProductId
	 */
	public function checkProductAvailability($intProductId)
	{
		$objQuantity = $this->Database->execute("SELECT quantity FROM tl_iso_inventory WHERE product_id=$intProductId");
		
		if(!$objQuantity->numRows)	//if no record then assume available?
			return true;
		
		if($objQuantity->quantity_in_stock<=0)
			return false;
	}
}