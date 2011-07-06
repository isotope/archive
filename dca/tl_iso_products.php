<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005-2009 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Intelligent Spark 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_products']['fields']['amount'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_products']['amount'],
	'inputType'				=> 'text',
	'attributes'			=> array('legend'=>'pricing_legend', 'customer_defined'=>true, 'fixed'=>true)
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['pMin'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_products']['pMin'],
	'inputType'				=> 'text',
	'eval'					=> array('rgxp'=>'digit'),
	'attributes'			=> array('legend'=>'general_legend')
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['pMax'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_products']['pMax'],
	'inputType'				=> 'text',
	'eval'					=> array('rgxp'=>'digit'),
	'attributes'			=> array('legend'=>'general_legend')
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['message'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_products']['message'],
	'inputType'		=> 'textarea',
	'eval'			=> array('rgxp'=>'extnd'),
	'attributes'	=> array('legend'=>'options_legend', 'customer_defined'=>true)
);

$GLOBALS['TL_DCA']['tl_iso_products']['fields']['shipto_address'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_iso_products']['shipto_address'],
	'inputType'		=> 'textarea',
	'eval'			=> array('rgxp'=>'extnd'),
	'attributes'	=> array('legend'=>'options_legend', 'customer_defined'=>true)
);

class tl_iso_products_flexprice extends Controller
{
	public function verifyValue($strRegexp, $varValue, Widget $objWidget)
	{
		if (TL_MODE == 'FE' && $objProduct instanceof FlexPriceProduct)
		{
			if ($strRegexp == 'valueGreaterThanZero')
			{
				if (!(float)$varValue>0)
				{
					$objWidget->addError('Value must be greater than zero.');
				}

				return true;
			}

			return false;
		}
	}

}