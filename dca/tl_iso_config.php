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
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'createMember';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'createMember_assignDir';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{createMember_legend:hide},createMember';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['createMember_always'] = 'createMember_groups,createMember_newsletters,createMember_mail,createMember_adminMail,createMember_assignDir';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['createMember_product'] = 'createMember_groups,createMember_newsletters,createMember_mail,createMember_adminMail,createMember_assignDir';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['createMember_guest'] = 'createMember_groups,createMember_newsletters,createMember_mail,createMember_adminMail,createMember_assignDir';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['createMember_assignDir'] = 'createMember_homeDir';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember'],
	'exclude'		=> true,
	'inputType'		=> 'radio',
	'options'		=> array('always', 'product', 'guest'),
	'reference'		=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember'],
	'eval'			=> array('submitOnChange'=>true, 'includeBlankOption'=>true, 'blankOptionLabel'=>&$GLOBALS['TL_LANG']['tl_iso_config']['createMember']['never'], 'tl_class'=>'clr'),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_groups'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_groups'],
	'inputType'		=> 'checkboxWizard',
	'foreignKey'	=> 'tl_member_group.name',
	'eval'			=> array('multiple'=>true, 'tl_class'=>'clr'),
);

if (in_array('newsletter', $this->Config->getActiveModules()))
{
	$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_newsletters'] = array
	(
		'label'			=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_newsletters'],
		'inputType'		=> 'checkbox',
		'foreignKey'	=> 'tl_newsletter_channel.title',
		'eval'			=> array('multiple'=>true, 'tl_class'=>'clr'),
	);
}

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_assignDir'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_assignDir'],
	'exclude'			=> true,
	'inputType'			=> 'checkbox',
	'eval'				=> array('submitOnChange'=>true, 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_homeDir'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_homeDir'],
	'exclude'			=> true,
	'inputType'			=> 'fileTree',
	'eval'				=> array('fieldType'=>'radio', 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_mail'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_mail'],
	'inputType'		=> 'select',
	'foreignKey'	=> 'tl_iso_mail.name',
	'eval'			=> array('includeBlankOption'=>true, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['createMember_adminMail'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_config']['createMember_adminMail'],
	'inputType'		=> 'select',
	'foreignKey'	=> 'tl_iso_mail.name',
	'eval'			=> array('includeBlankOption'=>true, 'tl_class'=>'w50'),
);

