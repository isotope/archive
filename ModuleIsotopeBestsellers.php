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
 *
 * PHP version 5
 * @copyright  Intelligent Spark 2011
 * @author     Fred Bliss <http://www.intelligentspark.com>
 */

class ModuleIsotopeBestsellers extends ModuleIsotopeProductList
{

	protected function findProducts()
	{
		$arrCategories = $this->findCategoryProducts($this->iso_category_scope);

		if ($this->iso_bestseller_mode == 'calculated')
		{
			//$strQuery = "SELECT p.id AS id FROM tl_iso_products AS p LEFT OUTER JOIN (SELECT i.product_id AS pid, SUM(i.product_quantity) AS volume FROM tl_iso_order_items AS i JOIN tl_iso_orders AS o ON o.id = i.pid WHERE o.config_id = ? GROUP BY i.product_id) AS s ON s.pid = p.id WHERE p.pid IS NULL OR p.pid = 0 ORDER BY s.volume DESC";

			if ($this->iso_bestseller_productTypes)
			{
				$arrProductTypes = deserialize($this->iso_bestseller_productTypes, true);

				$strProductTypes = " AND p.type IN(".implode(',',$arrProductTypes).")";
			}

			if (!$this->iso_bestseller_limitByType)
			{
				$strQuery = "SELECT DISTINCT p.* FROM tl_iso_products p LEFT OUTER JOIN (SELECT i.product_id AS pid, SUM(i.product_quantity) AS volume FROM tl_iso_order_items AS i JOIN tl_iso_orders AS o ON o.id = i.pid WHERE o.config_id = ? GROUP BY i.product_id) AS s ON s.pid=p.id WHERE volume>{$this->iso_bestseller_qty}$strProductTypes AND p.id IN (" . implode(',', $arrCategories) . ")" . (BE_USER_LOGGED_IN ? '' : " AND published='1'") . " ORDER BY volume DESC";
			}
			else
			{
				foreach($arrProductTypes as $type)
				{

					$strProductTypes = " AND p.type=$type";

					$strQuery = "SELECT DISTINCT p.* FROM tl_iso_products p LEFT OUTER JOIN (SELECT i.product_id AS pid, SUM(i.product_quantity) AS volume FROM tl_iso_order_items AS i JOIN tl_iso_orders AS o ON o.id = i.pid WHERE o.config_id = ? GROUP BY i.product_id) AS s ON s.pid=p.id WHERE volume>{$this->iso_bestseller_qty}$strProductTypes AND p.id IN (" . implode(',', $arrCategories) . ")" . (BE_USER_LOGGED_IN ? '' : " AND published='1'") . " ORDER BY volume DESC";

					$objBestsellers = $this->Database->prepare($strQuery)->limit($this->iso_bestseller_amt)->execute($this->Isotope->Config->id);

					if($objBestsellers->numRows)
					{
						$arrIds = (count($arrIds) ? array_merge($arrIds, $objBestsellers->fetchEach('id')) : $objBestsellers->fetchEach('id'));
					}
				}
			}
		}
		else
		{
			$strManualIds = implode(',', deserialize($this->iso_bestseller_products, true));

			$strQuery = "SELECT DISTINCT p.id AS id, p.* FROM tl_iso_products p WHERE AND p.id IN($strManualIds)" . (BE_USER_LOGGED_IN ? '' : " AND published='1'") . " AND p.id IN (" . implode(',', $arrCategories) . ")";
		}

		if($this->Input->get('test')==1)
			echo  'query: '.$strQuery;

		if (!$objBestsellers->numRows)	//must check in case we already queried for product types individually
		{
			$objBestsellers = $this->Database->prepare($strQuery)->limit($this->iso_bestseller_amt)->execute($this->Isotope->Config->id);

			$arrIds = $objBestsellers->fetchEach('id');
		}

		return IsotopeFrontend::getProducts($arrIds, IsotopeFrontend::getReaderPageId(null, $this->iso_reader_jumpTo));
	}
}

