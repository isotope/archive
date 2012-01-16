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


 /*
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'google_merchant';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{google_legend:hide},google_merchant';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['google_merchant'] = 'google_feedname,google_key,google_secret,google_merchant_accountID,google_reader';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_merchant'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_merchant'],
	'inputType'			=> 'checkbox',
	'eval'				=> array('submitOnChange'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_feedname'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_feedname'],
	'inputType'			=> 'text',
	'eval'				=> array('tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_reader'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_reader'],
	'inputType'			=> 'pageTree',
	'eval'              => array('fieldType'=>'radio', 'tl_class'=>'clr')
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_key'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_key'],
	'inputType'			=> 'text',
	'eval'				=> array('tl_class'=>'w50'),
	'explanation'		=> 'google_key',
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_secret'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_secret'],
	'inputType'			=> 'text',
	'eval'				=> array('tl_class'=>'w50'),
	'explanation'		=> 'google_secret',
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['google_merchant_accountID'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_config']['google_merchant_accountID'],
	'inputType'			=> 'text',
	'eval'				=> array('tl_class'=>'w50'),
	'explanation'		=> 'google_merchant_accountID',
);


class tl_iso_config_feeds extends Backend
{
	/**
	 * Import the back end user object
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('BackendUser', 'User');
	}

	/**
	 * Return all editable fields of table tl_member
	 * @return array
	 */
	public function getFeedTypes()
	{
		$return = array();

		foreach ($GLOBALS['ISO_FEEDS'] as $k=>$v)
		{
			$return[$k] = $GLOBALS['TL_LANG']['ISO_FEEDS'][$k];
		}
		return $return;
	}

}