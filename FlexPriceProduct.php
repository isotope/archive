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
 * @copyright  Isotope eCommerce Workgroup 2010-2012
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class FlexPriceProduct extends IsotopeProduct
{
	public function __construct($arrData, $arrOptions=null, $blnLocked=false)
	{
		//required for frontend widgets to behave, presumably.
		$GLOBALS['TL_DCA']['tl_iso_products']['fields']['amount']['eval']['mandatory']  = true;

		$GLOBALS['TL_DCA']['tl_iso_products']['fields']['message'] = array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_iso_products']['message'],
			'inputType'		=> 'textarea',
			'eval'			=> array('rgxp'=>'extnd'),
			'attributes'	=> array('customer_defined'=>true)
		);

		$GLOBALS['TL_DCA']['tl_iso_products']['fields']['shipto_address'] = array
		(
			'label'			=> &$GLOBALS['TL_LANG']['tl_iso_products']['shipto_address'],
			'inputType'		=> 'textarea',
			'eval'			=> array('rgxp'=>'extnd'),
			'attributes'	=> array('customer_defined'=>true)
		);

		parent::__construct($arrData, $arrOptions, $blnLocked);

	}

	/**
	 * Get a property
	 * @return mixed
	 */
	public function __get($strKey)
	{
		switch( $strKey )
		{
			case 'price':

				$fltPrice = ($this->arrOptions['amount'] ? (float)$this->arrOptions['amount'] : (float)$this->arrData['price']);

				$fltMin = (float)($this->arrData['pMin'] ? $this->arrData['pMin'] : 10);
				$fltMax = (float)($this->arrData['pMax'] ? $this->arrData['pMax'] : NULL);

				if($fltPrice>=$fltMin)
				{
					if($fltMax && $fltPrice>$fltMax)
						$fltPrice = $fltMax;

					$this->arrData['price'] = $this->Isotope->calculatePrice($fltPrice, $this, 'price', $this->arrData['tax_class']);

				}
				else
				{
					unset($this->arrOptions['amount']);
					return $this->Isotope->calculatePrice($fltMin, $this, 'price', $this->arrData['tax_class']);
				}
				 //we don't want this to show in options list.
				unset($this->arrOptions['amount']);

				return $this->arrData['price'];

				break;
		}

		return parent::__get($strKey);
	}
}