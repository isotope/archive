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
 * @copyright  Isotope eCommerce Workgroup 2012
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Frontend modules
 */
array_insert($GLOBALS['ISO_MOD']['config'], 1, array
(
	'postal' => array
	(
		'tables'			=> array('tl_iso_postal'),
		'icon'				=> 'system/modules/isotope_postal/html/postage-stamp.png',
	),
));


/**
 * Checkout steps
 */
$GLOBALS['ISO_CHECKOUT_STEPS']['address'][] = array('IsotopePostal', 'autocompleteCheckoutAddresses');


/**
 * Ajax requests
 */
$GLOBALS['TL_HOOKS']['dispatchAjax'][] = array('IsotopePostal', 'findCity');

