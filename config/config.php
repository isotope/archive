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


array_insert($GLOBALS['BE_MOD']['isotope'], 3, array
(
	'inventory_warehouses' 	=> array
	(
		'tables'			=> array('tl_iso_warehouses'),//,'tl_iso_inventory'),
		'icon'				=> 'system/modules/isotope_inventory/html/office_folders.png',
		'quantities'  		=> array( 'ModuleIsotopeInventoryManager', 'compile' )
	)
));

$GLOBALS['TL_HOOKS']['iso_getAttributes'][] = array('IsotopeWarehouse','getAttributes');
$GLOBALS['TL_HOOKS']['iso_getVariantAttributes'][] = array('IsotopeWarehouse','getVariantAttributes');

$GLOBALS['TL_HOOKS']['iso_writeOrder'][] = array('IsotopeInventory','updateInventory');