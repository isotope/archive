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


/**
 * Class IsotopePOS
 *
 * Point-of-sale related resources class
 */
class IsotopePrintlabels extends IsotopePOS
{
		
	protected $strTemplate = "iso_labels";
	
	public function __construct()
	{
		parent::__construct();
		
		$this->import('Isotope');
	
	}
						
	public function printLabelsInterface()
	{		
		$strMessage = '';
		
		$strReturn = '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=print_labels', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_iso_orders']['print_labels'][0].'</h2>
<form action="'.$this->Environment->request.'"  id="tl_print_labels" class="tl_form" method="post">
<input type="hidden" name="FORM_SUBMIT" value="tl_print_labels" />
<div class="tl_formbody_edit">
<div class="tl_tbox block">';
					
		$objWidget = new SelectMenu($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_orders']['fields']['status'], 'status'));
	
		if($this->Input->post('FORM_SUBMIT')=='tl_print_labels')
		{					
			$varValue = $this->Input->post('status');
			
			$objOrders = $this->Database->query("SELECT id FROM tl_iso_orders WHERE status='$varValue'");		
				
			if($objOrders->numRows)
			{
				$this->printLabels($objOrders->fetchEach('id'));
			}
			else
			{
				$strMessage = '<div class="tl_error">'.$GLOBALS['TL_LANG']['MSC']['noOrders'].'</div>';
			}
		}	
	
		return $strReturn .$objWidget->parse().$strMessage.'</div>
</div>
<div class="tl_formbody_submit">
<div class="tl_submit_container">
<input type="submit" name="print_labels" id="ctrl_print_labels" value="'.$GLOBALS['TL_LANG']['MSC']['labelSubmit'].'" />
</div>
</div>
</form>
</div>';
	}
		
	public function printLabels($arrIds = array())
	{		
		if(!count($arrIds))
			return;
	
		// Include library
		require_once(TL_ROOT . '/system/config/tcpdf.php');
		require_once(TL_ROOT . '/plugins/tcpdf/tcpdf.php'); 
		
		//Initial PDF setup
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 
	
		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle($objInvoice->title);
		$pdf->SetSubject($objInvoice->title);
		$pdf->SetKeywords($objInvoice->keywords);
	
		// Remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
	
		// Set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	
		// Set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
	
		// Set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 
	
		// Set some language-dependent strings
		$pdf->setLanguageArray($l); 
	
		// Initialize document and add a page
		$pdf->AliasNbPages();
		//$pdf->AddPage();
	
		// TCPDF configuration
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_charset'] = $GLOBALS['TL_CONFIG']['characterSet'];
		$l['a_meta_language'] = $GLOBALS['TL_LANGUAGE'];
		$l['w_page'] = "page";
			
		// Set font
		$pdf->SetFont(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN);
				
		$strIds = implode(',', $arrIds);
						
		$objOrders = $this->Database->query("SELECT * FROM tl_iso_orders WHERE id IN($strIds)");
		
		$arrIDs = array();
		while($objOrders->next())
		{
			$arrIDs[] = $objOrders->uniqid;
		}
		
		//Since we are only doing 10 per page, we need to break it up into chunks if there are more than 10
		if(count($arrIDs)>10)
		{
			$arrIds = array_chunk($arrIDs, 10);
			foreach($arrIds as $ids)
			{
				$pdf->AddPage();
				$strArticle = '';
							
				$arrLinks = array();
				
				$arrChunks = array();
				
				$strArticle .= $this->generateContent($ids);
	
				// Remove form elements
				$strArticle = preg_replace('/<form.*<\/form>/Us', '', $strArticle);
				$strArticle = preg_replace('/\?pdf=[0-9]*/i', '', $strArticle);
	
				preg_match_all('/<pre.*<\/pre>/Us', $strArticle, $arrChunks);
			
				foreach ($arrChunks[0] as $strChunk)
				{
					$strArticle = str_replace($strChunk, str_replace("\n", '<br />', $strChunk), $strArticle);
				}
					
				// Remove linebreaks and tabs
				$strArticle = str_replace(array("\n", "\t"), '', $strArticle);
				$strArticle = preg_replace('/<span style="text-decoration: ?underline;?">(.*)<\/span>/Us', '<u>$1</u>', $strArticle);
		
				// Write the HTML content
				$pdf->writeHTML($strArticle, true, 0, true, 0);
			}
		} 
		else //@todo: Simplify this into a single function instead of having so much crap. Soooooooooo lazy.
		{
			$pdf->AddPage();
			$strArticle = '';
						
			$arrLinks = array();
			
			$arrChunks = array();
			
			$strArticle .= $this->generateContent($arrIDs);

			// Remove form elements
			$strArticle = preg_replace('/<form.*<\/form>/Us', '', $strArticle);
			$strArticle = preg_replace('/\?pdf=[0-9]*/i', '', $strArticle);

			preg_match_all('/<pre.*<\/pre>/Us', $strArticle, $arrChunks);
		
			foreach ($arrChunks[0] as $strChunk)
			{
				$strArticle = str_replace($strChunk, str_replace("\n", '<br />', $strChunk), $strArticle);
			}
				
			// Remove linebreaks and tabs
			$strArticle = str_replace(array("\n", "\t"), '', $strArticle);
			$strArticle = preg_replace('/<span style="text-decoration: ?underline;?">(.*)<\/span>/Us', '<u>$1</u>', $strArticle);
	
			// Write the HTML content
			$pdf->writeHTML($strArticle, true, 0, true, 0);
		}
					
		// Close and output PDF document
		$pdf->lastPage();
		$pdf->Output(standardize(ampersand($strInvoiceTitle, false)) . '.pdf', 'D');
		$this->Isotope->resetConfig(true); 	//Set store back to default.
		
		ob_end_clean();
		exit;	
	}	
	
	
	protected function generateContent($varIds)
	{				
		$this->loadLanguageFile('subdivisions');
		foreach($varIds as $id)
		{
			$objOrderData = $this->Database->prepare("SELECT * FROM tl_iso_orders WHERE uniqid=?")->limit(1)->execute($id);
			$arrOrders[] = deserialize($objOrderData->shipping_address);
		}
		if (!count($arrOrders))
		{
			$objTemplate = new FrontendTemplate('mod_message');
			$objTemplate->type = 'error';
			$objTemplate->message = $GLOBALS['TL_LANG']['ERR']['orderNotFound'];
			return;
		}
		
		$objTemplate = new BackendTemplate($this->strTemplate);
				
		$objTemplate->orders = ($arrOrders);
		$objTemplate->countries = $this->getCountries();
		
		$this->import('Isotope');
		$this->Isotope->overrideConfig($objOrderData->config_id);
		
		return $objTemplate->parse();
	}		
	
}

