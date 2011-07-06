<?php if(!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'iso_bestseller_mode';

$GLOBALS['TL_DCA']['tl_module']['palettes']['iso_bestsellers'] = '{title_legend},name,headline,type,iso_bestseller_mode;{display_legend},iso_bestseller_qty,iso_cols;{config_legend},iso_use_quantity,iso_category_scope;{redirect_legend},iso_reader_jumpTo,iso_addProductJumpTo;{template_legend:hide},iso_list_layout,iso_buttons;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['iso_bestseller_mode_calculated'] = 'iso_bestseller_amt,iso_bestseller_productTypes,iso_bestseller_limitByType';

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['iso_bestseller_mode_manual'] = 'iso_bestseller_amt,iso_bestseller_products';

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_mode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_mode'],
	'default'				  => 1,
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options'				  => array('calculated','manual'),
	'eval'					  => array('mandatory'=>true,'submitOnChange'=>true),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_mode']
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_amt'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_amt'],
	'default'				  => 1,
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>3, 'rgxp'=>'digit', 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_qty'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_qty'],
	'default'				  => 1,
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>10, 'rgxp'=>'digit', 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_productTypes'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_productTypes'],
	'exclude'				  => true,
	'inputType'				  => 'checkbox',
	'foreignKey'			  => 'tl_iso_producttypes.name',
	'eval'					  => array('multiple'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_limitByType'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_limitByType'],
	'default'				  => 1,
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_module']['fields']['iso_bestseller_products'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['iso_bestseller_products'],
	'exclude'					=> true,
	'inputType'					=> 'tableLookup',
	'eval' => array
	(
		'mandatory'				=> true,
		'doNotSaveEmpty'		=> true,
		'tl_class'				=> 'clr',
		'foreignTable'			=> 'tl_iso_products',
		'fieldType'				=> 'checkbox',
		'listFields'			=> array('type'=>'(SELECT name FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id)', 'name', 'sku'),
		'searchFields'			=> array('name', 'alias', 'sku', 'description'),
		'sqlWhere'				=> 'pid=0',
		'searchLabel'			=> 'Search products',
	)
);