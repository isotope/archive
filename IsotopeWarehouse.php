<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  
 * @author    
 * @package    
 * @license    
 * @filesource
 */


/**
 * Class Warehouse
 *
 * @copyright  
 * @author     
 * @package    Model
 */

class IsotopeWarehouse extends Model
{
  protected $strTable = 'tl_iso_warehouse';

  protected $arrProducts;

  public function __construct()
  {
    parent::__construct();
  }


	/**
	 * Fetch products from database.
	 * 
	 * @access public
	 * @return array
	 */
	public function getProducts($sort, $where = null, $limit = null, $count_only = false)
	{
		$arrProducts = array();
		
	 	if ( $count_only )
		{
			$objResult = $this->Database->prepare("SELECT COUNT(*) AS count FROM tl_iso_products WHERE pid=0")->limit(1)->execute();
			return $objResult->count;
		}
		
		if (!count($arrProducts))
		{
			if ( $where )
			{
			  $query .= ' where ' . array_shift( $where );
			  $args   = array_merge( $args, $where );
			}
		
			$query .= ' order by ' . $sort;
		
			if ( $limit )
			{
			  $query .= ' limit ' . $limit;
			}
	
			$objItems = $this->Database->prepare("SELECT p.*, (SELECT quantity_in_stock FROM tl_iso_inventory i WHERE i.pid=? AND i.product_id=p.id ORDER BY tstamp DESC LIMIT 1) AS quantity FROM tl_iso_products p".$query)->execute($this->Input->get('id'), $args);
			
			while( $objItems->next() )
			{
				$arrData = array();
				
				$objProductData = $this->Database->prepare("SELECT *, (SELECT class FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id) AS product_class FROM tl_iso_products WHERE pid={$objItems->id} OR id={$objItems->id}")->limit(1)->execute();
								
				$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];
				
				try
				{
					$intQuantity = ($objItems->quantity ? $objItems->quantity : 0);
						
					$blnVariants = ($objItems->pid!=0 ? true : false);
				
					$objProduct = new $strClass($objProductData->row(), $this->blnLocked);
							
				}
				catch (Exception $e)
				{
					$objProduct = new IsotopeProduct(array('id'=>$objItems->id, 'sku'=>$objItems->sku, 'name'=>$objItems->name), $this->blnLocked);
				}
						
				if($objProduct->pid)
					$objProduct->loadVariantData($objProductData->row());
				
				$objProduct->quantity = $intQuantity;
				
				$arrProducts[] = $objProduct;
			}
		}
		
		return $arrProducts;
		
	}

	/** 
	 * Set quantity as an attribute for a product
	 */
	public function addAttributes($objProduct)
	{
		return 'quantity';
	}
	
	/**
	 * Set quantity as a variant attribute for a product
	 */
	public function addVariantAttributes($objProduct)
	{
		return 'quantity';
	}

  public function oldGetProducts( $sort = 'id', $where = null, $limit = null, $countOnly = false )
  {
    $products   = array();
    $args       = array( $this->id );

    if ( $countOnly )
    {
      $query = 'SELECT COUNT(*) AS count FROM tl_iso_products p';
    }

    else
    {
      //$query = 'select tl_iso_products.id, tl_iso_products.name, tl_iso_inventory.id as inventory_id, tl_iso_inventory.quantity_in_stock as quantity from tl_iso_products join tl_iso_inventory on tl_iso_inventory.pid = ? and tl_iso_inventory.product_id = tl_iso_products.id';
    	$query = 'SELECT p.name, p.sku, (SELECT quantity_in_stock FROM tl_iso_inventory i WHERE i.pid=? AND i.product_id=p.id) AS quantity FROM tl_iso_products p';
	}

    if ( $where )
    {
      $query .= ' where ' . array_shift( $where );
      $args   = array_merge( $args, $where );
    }

    $query .= ' order by ' . $sort;

    if ( $limit )
    {
      $query .= ' limit ' . $limit;
    }

    $records = $this->Database->prepare( $query )->execute( $args );

    if ( $countOnly )
    {
      return $records->count;
    }

    while ( $records->next() )
    {
		$records->quantity = $records->quantity ? $records->quantity : 0;
    	$records->name = $records->name ? $records->name : '<em>variant</em>';
		$products[] = $records->row();
    	
	}

    return $products;
  }



  public function searchProducts( $search = array(), $filter = array(), $search_fields = array(), $limit = 10, $countOnly = false )
  {
    $where = null;

    if ( count( $search ) or count( $filter ) )
    {
      $text   = '';
      $where  = array();

      foreach ( $search as $key => $value )
      {
        if ( in_array( $key, $search_fields ) )
        {
          $text   .= 'p.' . $key . ' like ? and ';
          $where[] = '%' . $value . '%';
        }
      }

      foreach ( $filter as $key => $value )
      {
        $text   .= 'p.' . $key . ' = ? and ';
        $where[] = $value;
      }

      $text = substr( $text, 0, -5 );
      array_unshift( $where, $text );
    }

    return $this->getProducts('p.id', $where, $limit, $countOnly);
  }
  
  	protected function generateEditLink($intRecordId, $strLinkTitle)
	{
		return '<a href="'.$this->Environment->script.'?do=iso_products&act=edit&id='.$intRecordId.'" title="'.sprintf($GLOBALS['TL_LANG']['tl_iso_products']['edit'][1],$intRecordId).'">'.$strLinkTitle.'</a>';
	}
}

