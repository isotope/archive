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


/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['isotope']['iso_fundraiser_manager']	= 'ModuleFundraiserManager';
$GLOBALS['FE_MOD']['isotope']['iso_fundraiser_search']	= 'ModuleFundraiserSearch';
$GLOBALS['FE_MOD']['isotope']['iso_fundraiser_reader']	= 'ModuleFundraiserReader';


/**
 * Hook for additional buttons
 */
$GLOBALS['ISO_HOOKS']['buttons'][]	= array('IsotopeFundraiserFrontend', 'fundraiserButton');
$GLOBALS['ISO_HOOKS']['buttons'][]	= array('IsotopeFundraiserFrontend', 'fundraiserCartButton');


/**
 * Hook for adding additional checkout addresses
 */
$GLOBALS['ISO_HOOKS']['customAddress'][] = array('IsotopeFundraiserFrontend', 'fundraiserAddress');


/**
 * Cart Info - basically like a DCA here so it can be modular, and not sure if this is best way, but it works for now
 */
$GLOBALS['ISO_COL_MANAGER']['tl_iso_fundraiser'] = array
(
	'type'		=> 'Fundraiser',
	'class'		=> 'IsotopeFundraiser',
	'ctable'	=> 'tl_iso_fundraiser_items',
	'search'	=> array('firstname','lastname', 'name'),
	'fields'	=> array
	(
		'jumpTo'		=>	array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_iso_collectionmanager']['jumpTo'],
			'inputType'		=> 'jumpTo',
			'mandatory'		=> true,
		),
		'name'		=>	array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_iso_fundraiser']['name'],
			'inputType'		=> 'text',
			'mandatory'		=> true,
		),
		'description'		=>	array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_iso_fundraiser']['description'],
			'inputType'		=> 'textarea',
			'rows'			=> 4,
			'cols'			=> 60,
			'mandatory'		=> true,
		),
		'pid'		=>	array
		(
			'label'			=>	&$GLOBALS['TL_LANG']['tl_iso_collectionmanager']['pid'],
			'inputType'		=> 'tableLookup',
			'mandatory'		=> true,
			'single'		=> true,
			'foreignTable'	=> 'tl_member',
			'listFields'	=> array('firstname', 'lastname'),
			'searchFields'	=> array('firstname', 'lastname'),
			'sqlWhere'		=> 'disable!=1',
			'searchLabel'	=> 'Search Members',
		),
		'products'		=>	array
		(
			'label'			=>	&$GLOBALS['TL_LANG']['tl_iso_collectionmanager']['products'],
			'inputType'		=> 'collectionWizard',
			'mandatory'		=> true,
			'isCollection'		=> true,
			'multiple'		=> true,
			'foreignTable'	=> 'tl_iso_products',
			'listFields'	=> array('name', 'sku'),
			'searchFields'	=> array('name', 'alias', 'sku', 'description'),
			'sqlWhere'		=> 'published=1',
			'searchLabel'	=> 'Search For Products',
		),

	),
);


?>