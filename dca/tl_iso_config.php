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

$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'enableInventory';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{inventory_legend:hide},enableInventory';

$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['enableInventory'] = 'warehouses';

//$GLOBALS['TL_DCA']['tl_iso_config'];

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['enableInventory'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['enableInventory'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('submitOnChange'=>true)
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['warehouses'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['warehouses'],
	'exclude'                 => true,
	'inputType'				  => 'checkbox',
	'eval'					  => array('multiple'=>true,'mandatory'=>true),
	'options_callback'		  => array('tl_iso_config_inventory','getWarehouses')
);

class tl_iso_config_inventory extends Backend
{
	public function getWarehouses()
	{
		$objData = $this->Database->query("SELECT id, name FROM tl_iso_warehouse");
		
		if(!$objData->numRows)
			return array();
			
		while($objData->next())
		{
			$arrReturn[$objData->id] = $objData->name;
		}
	
		return $arrReturn;
	}

}