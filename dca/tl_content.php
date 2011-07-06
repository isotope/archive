<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
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
 * @copyright  Andreas Schempp 2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_content']['palettes']['iso_bundle'] = '{type_legend},type,headline;{include_legend},iso_bundle;{text_legend},text;{image_legend},addImage;{redirect_legend},iso_reader_jumpTo;{template_legend},iso_list_layout;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_content']['fields']['iso_bundle'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_content']['iso_bundle'],
	'inputType'		=> 'productTree',
	'eval'			=> array('mandatory'=>true, 'fieldType'=>'text', 'variants'=>true, 'variantsOnly'=>true, 'rgxp'=>'digit', 'maxlength'=>2, 'style'=>'width:20px;text-align:center'),
);

