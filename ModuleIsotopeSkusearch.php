<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class ModuleIsotopeSkusearch extends ModuleIsotopeProductList
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_skusearch';


	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ISOTOPE ECOMMERCE: SKU SEARCH ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = $this->Environment->script.'?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	protected function findProducts()
	{
		$strKeywords = $this->Input->get('keywords');

		if (strpos($strKeywords, '*') === false)
		{
			$strKeywords = '%'.$strKeywords.'%';
		}
		else
		{
			$strKeywords = str_replace('*', '%', $strKeywords);
		}

		$arrProducts = array();
		$arrIds = $this->Database->prepare("SELECT id FROM tl_iso_products WHERE sku LIKE ? AND published='1'")->execute($strKeywords)->fetchEach('id');

		foreach( $arrIds as $intId )
		{
			$objProduct = $this->getProduct($intId);

			if (is_object($objProduct))
			{
				$arrProducts[] = $objProduct;
			}
		}

		// No products found, display message
		if (!count($arrProducts))
		{
			$this->iso_noProducts = sprintf($GLOBALS['TL_LANG']['MSC']['sNoResult'], $this->Input->get('keywords'));
			return array();
		}

		// One product found, redirect to reader page
		elseif (count($arrProducts) == 1 && $this->iso_reader_jumpTo > 0)
		{
			$objProduct = reset($arrProducts);

			$objProduct->reader_jumpTo = $this->iso_reader_jumpTo;

			$this->redirect($objProduct->href_reader);
		}

		// Multiple products found, show the product list
		else
		{
			return $arrProducts;
		}
	}
}

