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
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_attributes']['palettes']['__selector__'][] = 'productoptions_includeBlankOption';
$GLOBALS['TL_DCA']['tl_iso_attributes']['palettes']['productOptions'] = '{attribute_legend},name,field_name,type,legend;{description_legend:hide},description;{config_legend},mandatory,multiple,size,productoptions_includeBlankOption;{search_filters_legend},fe_search';


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_iso_attributes']['subpalettes']['productoptions_includeBlankOption'] = 'productoptions_blankOptionLabel';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['productoptions_includeBlankOption'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_attributes']['productoptions_includeBlankOption'],
	'inputType'		=> 'checkbox',
	'eval'			=> array('submitOnChange'=>true, 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_iso_attributes']['fields']['productoptions_blankOptionLabel'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_attributes']['productoptions_blankOptionLabel'],
	'inputType'		=> 'text',
	'eval'			=> array('maxlength'=>255, 'tl_class'=>'clr'),
);

