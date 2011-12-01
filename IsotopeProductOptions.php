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


class IsotopeProductOptions extends Frontend
{

	/**
	 * Map the individual product settings to the dropdown options
	 * @param string
	 * @param array
	 * @param IsotopeProduct|null
	 * @return array
	 */
	public function renderProductOptions($strField, $arrData, &$objProduct=null)
	{
		if (TL_MODE == 'FE' && $objProduct instanceof IsotopeProduct)
		{
			if (count($objProduct->{$strField}))
			{
				$arrData['options'] = array();
				
				if ($arrData['attributes']['productoptions_includeBlankOption'])
				{
					$arrData['options'][''] = $arrData['attributes']['productoptions_blankOptionLabel'] ? $arrData['attributes']['productoptions_blankOptionLabel'] : '-';
				}
				
				foreach($objProduct->{$strField} as $value)
				{
					$arrData['options'][$value['value']]=$value['label'];
				}
			}
		}
		
		return $arrData;
	}
	
	
	/**
	 * Make the attribute of type "productOptions" always customer_defined
	 * @param array
	 * @param array
	 * @param IsotopeProduct
	 * @return void
	 */
	public function makeCustomerDefined($arrAttributes, $arrVariantAttributes, $objProduct)
	{
		$arrFields = &$GLOBALS['TL_DCA']['tl_iso_products']['fields'];
		
		foreach( array_merge($arrAttributes, $arrVariantAttributes) as $attribute )
		{
			if ($arrFields[$attribute]['attributes']['type'] == 'productOptions')
			{
				$arrFields[$attribute]['attributes']['customer_defined'] = true;
			}
		}
	}
}

