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
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_iso_products']['config']['onload_callback'][] = array('GoogleMerchant', 'onLoad');
$GLOBALS['TL_DCA']['tl_iso_products']['config']['onsubmit_callback'][] = array('GoogleMerchant', 'saveParentVariants');
$GLOBALS['TL_DCA']['tl_iso_products']['config']['onsubmit_callback'][] = array('GoogleMerchant', 'onProductNew');
$GLOBALS['TL_DCA']['tl_iso_products']['config']['ondelete_callback'][] = array('GoogleMerchant', 'deleteParentVariants');
$GLOBALS['TL_DCA']['tl_iso_products']['config']['ondelete_callback'][] = array('GoogleMerchant', 'onProductDelete');


/**
 * Fields
 */
//Add the Google Authorization button onto the published field DCA
$GLOBALS['TL_DCA']['tl_iso_products']['list']['operations']['toggle']['attributes'] = 'onclick="Backend.getScrollOffset(); IsotopeGoogleRequest.toggleVisibility(this, %s); return AjaxRequest.toggleVisibility(this, %s);"';

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_condition'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_condition'],
	'exclude'                 => true,
	'default'				  => 'new',
	'inputType'               => 'select',
	'options'				  => array('new','used','refurbished'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_iso_products'],
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_availability'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_availability'],
	'exclude'                 => true,
	'default'				  => 'in stock',
	'inputType'               => 'select',
	'options'				  => array('in stock','available for order','out of stock','preorder'),
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['tl_iso_products'],
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_brand'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_brand'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_gtin'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_gtin'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'digit', 'tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_mpn'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_mpn'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50'),
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_google_product_category'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_google_product_category'],
	'exclude'                 => true,
 	'inputType' 			  => 'tableTree',
 	'eval'      			=> array(
 		'fieldType' 		=> 'radio',
 		'tableColumn'		=> 'tl_google_taxonomy.name',
 		'title'				=> &$GLOBALS['TL_LANG']['tl_google_taxonomy']['customSubTitle'],
 		'children' 			=> true,
 		'childrenOnly'		=> false,
 		'tl_class'			=> 'clr'
 	),
 	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['gid_product_type'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_products']['gid_product_type'],
	'exclude'                 => true,
	'inputType'               => 'listWizard',
	'eval'                    => array('mandatory'=>true, 'allowHtml'=>true, 'tl_class' => 'clr m12'),
	'attributes'			  => array('legend'=>'google_legend:hide', 'fixed'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_products']['list']['global_operations']['removeproducts'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_products']['removeproducts'],
	'href'				=> 'key=removeproducts',
	'class'				=> 'header_import_assets isotope-tools',
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

$GLOBALS['TL_DCA']['tl_iso_products']['list']['global_operations']['createcache'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_products']['createcache'],
	'href'				=> 'key=createcache',
	'class'				=> 'header_import_assets isotope-tools',
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

$GLOBALS['TL_DCA']['tl_iso_products']['list']['global_operations']['clearcache'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_products']['clearcache'],
	'href'				=> 'key=clearcache',
	'class'				=> 'header_import_assets isotope-tools',
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

$GLOBALS['TL_DCA']['tl_iso_products']['list']['global_operations']['clearauth'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_products']['clearauth'],
	'href'				=> 'key=clearauth',
	'class'				=> 'header_import_assets isotope-tools',
	'attributes'		=> 'onclick="Backend.getScrollOffset();"',
);

?>