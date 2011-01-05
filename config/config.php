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
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Back end modules
 */
$GLOBALS['BE_MOD']['isotope']['isotope_export'] = array(
	'tables'   => array('tl_iso_products'),
	'callback' => 'IsotopeExport',
	'icon'	   => 'system/modules/isotope_export/html/icon-osc.gif',
	'stylesheet' => 'system/modules/isotope_export/html/export.css',
	'javascript' => 'system/modules/isotope_export/html/export.js',
);


/**
 * Hooks
 */
//$GLOBALS['TL_HOOKS']['executePostActions'][] = array('IsotopeExport','startExport');
?>