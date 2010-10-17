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
 
 
class ModuleFundraiserSearch extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_fundraiser_search';


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### GIFT REGISTRY SEARCH ###';
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

		// Trigger the search module from a custom form
		if ((!$_GET['name'] || !$_GET['date']) && $this->Input->post('FORM_SUBMIT') == 'tl_fundraiser_search')
		{
			$_GET['name'] = $this->Input->post('name');
		}

		$objFormTemplate = new FrontendTemplate('iso_fundraiser_formsearch');

		$objFormTemplate->queryType = $this->queryType;
		$objFormTemplate->keyword = specialchars($strKeywords);
		$objFormTemplate->schoolLabel = $GLOBALS['TL_LANG']['MSC']['schoolLabel'];
		$objFormTemplate->searchFundraiser = specialchars($GLOBALS['TL_LANG']['MSC']['searchfundraiserLabel']);
		$objFormTemplate->matchAll = specialchars($GLOBALS['TL_LANG']['MSC']['matchAll']);
		$objFormTemplate->matchAny = specialchars($GLOBALS['TL_LANG']['MSC']['matchAny']);
		$objFormTemplate->id = ($GLOBALS['TL_CONFIG']['disableAlias'] && $this->Input->get('id')) ? $this->Input->get('id') : false;
		$objFormTemplate->action = ampersand($this->Environment->request);

		$this->Template->form = $objFormTemplate->parse();
		$this->Template->results = '';
		
		$strLastname = $_GET['name'];

		// Execute search if there are keywords
		if (strlen($strLastname) && $strLastname != '*')
		{
			$arrResult = null;
			
			$nameQuery = 'AND (m.lastname LIKE "' . $strLastname . '%" OR m.firstname LIKE "' . $strLastname . '%" OR r.name LIKE "' . $strLastname . '%" OR r.description LIKE "' . $strLastname . '%")';

			// Get result
			if (is_null($arrResult))
			{
				try
				{
					$objSearch = $this->Database->prepare("SELECT r.id, r.name, r.description FROM tl_iso_fundraiser r, tl_member m WHERE r.pid = m.id {$nameQuery}")->execute();
					$arrResult = $objSearch->fetchAllAssoc();
				}
				catch (Exception $e)
				{
					$this->log('Fundraiser search failed: ' . $e->getMessage(), 'ModuleFundraiserSearch compile()', TL_ERROR);
					$arrResult = array();
				}

			}
			
			$count = count($arrResult);

			// No results
			if ($count < 1)
			{
				$this->Template->header = sprintf($GLOBALS['TL_LANG']['MSC']['sEmpty'], $strLastname);

				return;
			}

			$from = 1;
			$to = $count;
			
			$this->loadLanguageFile('tl_iso_fundraiser');

			// Get results
			for ($i=($from-1); $i<$to && $i<$count; $i++)
			{
				$objTemplate = new FrontendTemplate((strlen($this->searchTpl) ? $this->searchTpl : 'search_fundraiser_default'));

				$objTemplate->id = $arrResult[$i]['id'];
				$objTemplate->name = $arrResult[$i]['name'];
				$objTemplate->description = $arrResult[$i]['description'];
				$objTemplate->href = $this->generateFrontendUrl($this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($this->jumpTo)->fetchAssoc(), '/rid/' . $arrResult[$i]['id']);
				$objTemplate->class = (($i == ($from - 1)) ? 'first ' : '') . (($i == ($to - 1) || $i == ($count - 1)) ? 'last ' : '') . (($i % 2 == 0) ? 'even' : 'odd');

				$this->Template->results .= $objTemplate->parse();
			}
			
			$strResults = (strlen($strLastname)) ? $GLOBALS['TL_LANG']['MSC']['schoolLabel'] . ' ' . $strLastname . ' ' : '';
			
			$this->Template->header = vsprintf($GLOBALS['TL_LANG']['MSC']['rResults'], array($from, $to, $count, $strResults));
		}
	}
}

?>