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
 * @author     Blair Winans <blair@winanscreative.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class ModuleIsotopeBreadcrumb extends ModuleBreadcrumb
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_breadcrumb';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ISOTOPE BREADCRUMB NAVIGATION ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		global $objPage;

		//Determine if we are on a product reader page. If not, display the normal breadcrumb.
		if(!$this->Input->get('product'))
		{
			return parent::compile();
		}

		$pages = $this->getDeepestPage();
		$items = array();
		$type = null;

		// Link to website root
		if ($this->includeRoot)
		{
			//Pop the last item off since it will be the root page
			$arrHome = array_pop($pages);

			$items[] = array
			(
				'isRoot' => true,
				'isActive' => false,
				'href' => $this->Environment->base,
				'title' => $arrHome['name'],
				'link' => $arrHome['title']
			);
		}

		// Build breadcrumb menu
		for ($i=(count($pages)-1); $i>0; $i--)
		{
			if (($pages[$i]['hide'] && !$this->showHidden) || (!$pages[$i]['published'] && !BE_USER_LOGGED_IN))
			{
				continue;
			}

			// Get href
			switch ($pages[$i]['type'])
			{
				case 'redirect':
					$href = $pages[$i]['url'];

					if (strncasecmp($href, 'mailto:', 7) === 0)
					{
						$this->import('String');
						$href = $this->String->encodeEmail($href);
					}
					break;

				case 'forward':
					$objNext = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
											  ->limit(1)
											  ->execute($pages[$i]['jumpTo']);

					if ($objNext->numRows)
					{
						$href = $this->generateFrontendUrl($objNext->fetchAssoc());
						break;
					}
					// DO NOT ADD A break; STATEMENT

				default:
					$href = $this->generateFrontendUrl($pages[$i]);
					break;
			}

			$items[] = array
			(
				'isRoot' => false,
				'isActive' => false,
				'href' => $href,
				'title' => (strlen($pages[$i]['pageTitle']) ? specialchars($pages[$i]['pageTitle']) : specialchars($pages[$i]['title'])),
				'link' => $pages[$i]['title']
			);
		}

		// Active product
		$items[] = array
		(
			'isRoot' => false,
			'isActive' => $this->showProduct ? false : true,
			'href' => $this->generateFrontendUrl($pages[0]),
			'title' => (strlen($pages[0]['pageTitle']) ? specialchars($pages[0]['pageTitle']) : specialchars($pages[0]['title'])),
			'link' => $pages[0]['title']
		);


		if($this->showProduct)
		{
			$strProductAlias = $this->Input->get('product');

			if (is_null($strProductAlias))
			{
				//@todo: Make this editable in a language file
				$strProductAlias = 'Product';
			}

			// Get product title
			$objProduct= $this->Database->prepare("SELECT name FROM tl_iso_products WHERE id=? OR alias=?")
										 ->limit(1)
										 ->execute((is_numeric($strProductAlias) ? $strProductAlias : 0), $strProductAlias);

			if ($objProduct->numRows)
			{
				$items[] = array
				(
					'isRoot' => false,
					'isActive' => true,
					'title' => specialchars($objProduct->name),
					'link' => $objProduct->name
				);
			}
		}

		$this->Template->items = $items;
	}


	protected function getPageIdFromAlias($strURL)
	{
		global $objPage;

		$strAlias = $strURL;
		$strAlias = preg_replace('/\?.*$/i', '', $strAlias);
		$strAlias = preg_replace('/' . preg_quote($GLOBALS['TL_CONFIG']['urlSuffix'], '/') . '$/i', '', $strAlias);
		$arrAlias = explode('/', $strAlias);
		// Skip index.php and empty data
		if (strtolower($arrAlias[0]) == 'index.php' || $arrAlias[0]=='')
		{
			array_shift($arrAlias);
		}

		$objCategoryPages = $this->Database->prepare("SELECT id FROM tl_page WHERE alias=?")
											   ->execute($arrAlias[0]);
		while($objCategoryPages->next())
		{
			$objPageDetails = $this->getPageDetails($objCategoryPages->id);
			//Make sure we are getting the same rootId.. Could be more than one when doing it by alias
			if($objPageDetails->rootId == $objPage->rootId)
			{
				$pageId = $objCategoryPages->id;
			}
		}

		return $pageId;

	}

	protected function getReferringPageID()
	{
		$strReferer = $this->getReferer();

		return $this->getPageIdFromAlias($strReferer);
	}



	protected function getDeepestPage()
	{
		global $objPage;

		$objProduct = IsotopeFrontend::getProductByAlias($this->Input->get('product'));
		$arrTrails = $this->getProductPageTrails($objProduct);
		$intRefId = $this->getReferringPageID();
		$arrPages = array();

		foreach($arrTrails as $arrTrail)
		{
			//We matched a category dead on.
			if($intRefId==$arrTrail[0]['id'])
			{
				$arrPages = $arrTrail;
			}
			//@todo consider even deeper categories
		}

		if(!count($arrPages))
		{
			// We didn't find any pages... Let's just get the first one
			$arrPages = $arrTrails[0];
		}

		return $arrPages;
	}


	protected function getProductPageTrails($objProduct)
	{
		$arrCats = deserialize($objProduct->pages);

		$arrReturn = array();

		// Add the referring page just in case there are no cats
		if (!is_array($arrCats) || empty($arrCats))
		{
			$arrCats = array($this->getReferringPageID());
		}

		foreach($arrCats as $cat)
		{
			$pages = array();
			$pageId = $cat;

			// Get all pages up to the root page
			do
			{
				$objPages = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
										   ->limit(1)
										   ->execute($pageId);

				$type = $objPages->type;
				$pageId = $objPages->pid;
				$pages[] = $objPages->row();
			}
			while ($pageId > 0 && $type != 'root' && $objPages->numRows);

			if ($type == 'root')
			{

				if (!$this->includeRoot)
				{
					array_pop($pages);
				}

				$arrReturn[] = $pages;
			}
		}

		return $arrReturn;
	}
}

