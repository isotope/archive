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
	 * @param object $objOrder
	 * @param object $objCart
	 * @return boolean
	 */
	public function updateInventory($objOrder, $objCart)
	{
		$arrWarehouses = deserialize($this->Isotope->Config->warehouses, true);

		if(!$this->Isotope->Config->enableInventory || !count($arrWarehouses))
			return true;

		//TODO: determine closest eligible warehouse the items should be shipped from
		/*

		*/

		//For now just grab the first warehouse...
		$intWarehouseId = $arrWarehouses[0];

		$arrProducts = $objCart->getProducts();

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
		$arrInserts = array();
		foreach($arrRows as $row)
		{
			$arrRow = explode(',',$row);
			$arrProducts[] = $arrRow[2];
		}
		//Update latest inventory record
		$this->Database->query("INSERT INTO tl_iso_inventory (pid,tstamp,product_id,quantity) VALUES ($strInserts)");

		foreach($arrProducts as $id)
		{
			//Get the inventory... Possibly send admin noptifications
			$objQuantity = $this->Database->prepare("SELECT SUM(quantity) as quantity_in_stock FROM tl_iso_inventory WHERE product_id=?")->execute($id);
			if($objQuantity->quantity<3 && $objQuantity->quantity>0)
			{
				$strType = 'low';
				$this->sendAdminNotification($id, $strType);
			}elseif($objQuantity->quantity<=0)
			{
				$strType = 'zero';
				$this->sendAdminNotification($id, $strType);
			}
		}
	}


	/**
	 * Send an admin notification e-mail
	 * @param integer
	 * @param array
	 */
	protected function sendAdminNotification($intId, $strType)
	{
		$objEmail = new Email();

		$objEmail->from = $GLOBALS['TL_ADMIN_EMAIL'];
		$objEmail->fromName = $GLOBALS['TL_ADMIN_NAME'];
		$objEmail->subject = $GLOBALS['TL_LANG']['MSC']['inventoryAdminSubject'];

		$objProduct = $this->getProduct($intId);

		$objEmail->text = sprintf($GLOBALS['TL_LANG']['MSC']['inventoryAdminText'][$strType], $intId, $objProduct->name . "\n", $objProduct->reader_jumpTo) . "\n";
		$objEmail->sendTo($GLOBALS['TL_ADMIN_EMAIL']);

		$this->log('A product (ID ' . $intId . ') has triggered an inventory warning', 'IsotopeInventory sendAdminNotification()', TL_ACCESS);
	}

	/**
	 * Check availability of a given product based on backorder setting and inventory level
	 * @access public
	 * @param integer $intProductId
	 */
	public function checkProductAvailability($intProductId)
	{
		$objQuantity = $this->Database->execute("SELECT SUM(quantity) as quantity_in_stock FROM tl_iso_inventory WHERE product_id=$intProductId");

		if(!$objQuantity->numRows)	//if no record then assume available?
			return true;

		if($objQuantity->quantity_in_stock<=0)
			return false;
	}

	/**
	 * Shortcut for a single product by ID
	 */
	protected function getProduct($intId)
	{
		global $objPage;

		$objProductData = $this->Database->prepare("SELECT *, (SELECT class FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id) AS product_class FROM tl_iso_products WHERE pid=$intId OR id=$intId")
										 ->limit(1)
										 ->executeUncached();

		$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];

		if (!$this->classFileExists($strClass))
		{
			return null;
		}

		$objProduct = new $strClass($objProductData->row());

		$objProduct->reader_jumpTo = $this->iso_reader_jumpTo ? $this->iso_reader_jumpTo : $objPage->id;

		return $objProduct;
	}


	public function generateInventoryWizard(DataContainer $dc, $xlabel)
	{
		// Load language file for the foreign key table.
		// @todo only if file_exists==true;
		$this->loadLanguageFile('tl_iso_warehouses');

		$intId = $this->Input->get('id');

		$arrUnits = array();

		$return = $xlabel;

		$return .= '<table cellspacing="0" cellpadding="5" id="ctrl_'.$dc->field.'" class="tl_inventorywizard" summary="Inventory wizard">
  <thead>
  <tr>
    <td>'.$GLOBALS['TL_LANG']['tl_iso_products']['warehouse_name'].'</td>
    <td>'.$GLOBALS['TL_LANG']['tl_iso_products']['quantity'].'</td>
    <td>'.$GLOBALS['TL_LANG']['tl_iso_products']['new_quantity'].'</td>
  </tr>
  </thead>
  <tbody>';

		// Get current quantites from inventory
		$objQty = $this->Database->query("SELECT id, name, (SELECT SUM(quantity) FROM tl_iso_inventory WHERE tl_iso_inventory.pid=tl_iso_warehouses.id AND tl_iso_inventory.product_id=$intId) AS total_quantity FROM tl_iso_warehouses");

		if(!$objQty->numRows)
		{
			return '<em>'.$GLOBALS['TL_LANG']['MSC']['noWarehouses'].'</em>';
		}

		while($objQty->next())
		{
			$arrQty[$objQty->id] = array
			(
				'warehouse_name'	=> $objQty->name,
				'quantity'			=> $objQty->total_quantity
			);
		}

		$return .= '<tbody>';

		foreach ($arrQty as $key=>$value)
		{
			$return .= '<tr>';
			$return .= '	<td>';
			$return .= '<strong>'.$value['warehouse_name'].'</strong>';
			$return .= '	</td>';
			$return .= '	<td>';
			$return .= $value['quantity'];
			$return .= '	</td>';
			$return .= '	<td>';
			$return .= '<input type="text" name="'.$dc->field.'['.$key.']" id="ctrl_'.$dc->field.'" class="tl_text_4" value="0" onfocus="Backend.getScrollOffset();" />';
			$return .= '	</td>';
			$return .= '</tr>';
		}

		$return .= '</tbody>';

		if($this->Input->post('FORM_SUBMIT')==$dc->table)
		{
			$arrInserts = array();

			$varValue = $this->Input->post($dc->field);

			if(!is_array($varValue) || !count($varValue))
				return $varValue;

			foreach($varValue as $i=>$value)
			{
				$arrInserts[] = '('.time().','.$i.','.$dc->id.','.$value.')';

			}

			if(!count($arrInserts))
				return $varValue;

			$strInserts = implode(',', $arrInserts);

			$this->Database->query("INSERT INTO tl_iso_inventory (tstamp,pid,product_id,quantity)VALUES".$strInserts);
		}

		return $return . '</table>';
	}
}