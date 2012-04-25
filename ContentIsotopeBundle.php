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


class ContentIsotopeBundle extends ContentIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'ce_iso_bundle';


	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');
			$objTemplate->wildcard = '### ISOTOPE BUNDLE ###';
			return $objTemplate->parse();
		}
		
		$this->iso_bundle = deserialize($this->iso_bundle);

		if (!is_array($this->iso_bundle) || !count($this->iso_bundle))
		{
			return '';
		}

		return parent::generate();
	}


	protected function compile()
	{
		$arrProducts = IsotopeFrontend::getProducts(array_keys($this->iso_bundle), IsotopeFrontend::getReaderPageId(null, $this->iso_reader_jumpTo));

		if (!is_array($arrProducts) || !count($arrProducts))
		{
			$this->Template = new FrontendTemplate('mod_message');
			$this->Template->type = 'empty';
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['noProducts'];
			return;
		}

		if ($this->Input->get('bundle') == $this->id)
		{
			foreach( $arrProducts as $objProduct )
			{
				$this->Isotope->Cart->addProduct($objProduct, (int)$this->iso_bundle[$objProduct->id]);
			}

			$this->redirect(str_replace('?bundle='.$this->id, '', $this->Environment->request));
		}

		$arrBuffer = array();
		$last = count($arrProducts) - 1;
		$row = 0;
//		$rows = ceil(count($arrProducts) / $this->iso_cols) - 1;
		foreach( $arrProducts as $i => $objProduct )
		{
			$objProduct->quantity_requested = (int)$this->iso_bundle[$objProduct->id];
			$blnClear = false;

/*
			if ($i > 0 && $i % $this->iso_cols == 0)
			{
				$blnClear = true;
				$row++;
			}
*/

			$arrBuffer[] = array
			(
//				'clear'		=> (($this->iso_cols > 1 && $blnClear) ? true : false),
//				'class'		=> ('product' . ($i%2 ? ' product_even' : ' product_odd') . ($i == 0 ? ' product_first' : '') . ($i == $last ? ' product_last' : '') . ($this->iso_cols > 1 ? ' row_'.$row . ($row%2 ? ' row_even' : ' row_odd') . ($row == 0 ? ' row_first' : '') . ($row == $rows ? ' row_last' : '') : '')),
				'html'		=> $objProduct->generate((strlen($this->iso_list_layout) ? $this->iso_list_layout : $objProduct->list_template), $this),
			);
		}

		$this->Template->products = $arrBuffer;
		$this->Template->href = $this->Environment->request . '?bundle=' . $this->id;
		$this->Template->addToCart = $GLOBALS['TL_LANG']['MSC']['buttonLabel']['add_to_cart'];
	}
}

