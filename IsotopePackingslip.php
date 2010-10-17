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
class IsotopePackingslip extends IsotopePOS
{
		
	protected $strTemplate = "iso_packing";
	
	public function __construct()
	{
		parent::__construct();
		
		$this->import('Isotope');
	
	}
						
	public function printInvoicesInterface()
	{		
		$strMessage = '';
		
		$strReturn = '
<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=print_packing', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['tl_iso_orders']['print_packing'][0].'</h2>
<form action="'.$this->Environment->request.'"  id="tl_print_packing" class="tl_form" method="post">
<input type="hidden" name="FORM_SUBMIT" value="tl_print_packing" />
<div class="tl_formbody_edit">
<div class="tl_tbox block">';
					
		$objWidget = new SelectMenu($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_orders']['fields']['status'], 'status'));
	
		if($this->Input->post('FORM_SUBMIT')=='tl_print_packing')
		{					
			$varValue = $this->Input->post('status');
			
			$objOrders = $this->Database->query("SELECT id FROM tl_iso_orders WHERE status='$varValue'");		
				
			if($objOrders->numRows)
			{
				$this->printInvoices($objOrders->fetchEach('id'));
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
<input type="submit" name="print_invoices" id="ctrl_print_invoices" value="'.$GLOBALS['TL_LANG']['MSC']['labelSubmit'].'" />
</div>
</div>
</form>
</div>';
	}
					
	
}

