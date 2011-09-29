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
 * @copyright  Isotope eCommerce Workgroup 2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


class IsotopeOneProduct extends Frontend
{

	/**
	 * Callback for Isotope Hook "addProductToCollection"
	 */
	public function addProductToCollection($objProduct, $intQuantity, $objCollection)
	{
		$this->emptyCart($objCollection);
		return $intQuantity;
	}

	/**
	 * Callback for Isotope Hook "transferCollection"
	 */
	public function transferCollection($objOldItems, $objNewItems, $objSourceCollection, $objTargetCollection, $blnTransfer)
	{
		$this->emptyCart($objTargetCollection);
		return $blnTransfer;
	}


	protected function emptyCart($objCollection)
	{
		if ($objCollection instanceof IsotopeCart)
		{
			$arrProducts = $objCollection->getProducts();

			foreach( $arrProducts as $product )
			{
				$objCollection->deleteProduct($product);
			}
		}
	}
}

