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
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


class PaymentEPayForm extends PaymentEPay
{

	/**
	 * Return the payment form.
	 *
	 * @access public
	 * @return string
	 */
	public function checkoutForm()
	{
		global $objPage;

		$objOrder = $this->Database->prepare("SELECT * FROM tl_iso_orders WHERE cart_id=?")->limit(1)->executeUncached($this->Isotope->Cart->id);
		$intTotal = round($this->Isotope->Cart->grandTotal, 2) * 100;

		$strClass = ISO_VERSION >= 0.3 ? 'IsotopeTemplate' : 'FrontendTemplate';
		$objTemplate = new $strClass('iso_payment_epayform');

		$objTemplate->headline = $GLOBALS['TL_LANG']['MSC']['pay_with_cc'][0];
		$objTemplate->message = $GLOBALS['TL_LANG']['MSC']['pay_with_cc'][1];
		$objTemplate->slabel = $GLOBALS['TL_LANG']['MSC']['pay_with_cc'][2];
		$objTemplate->error = ($this->Input->get('error') == '' ? '' : $GLOBALS['TL_LANG']['MSG']['epay'][$this->Input->get('error')]);
		$objTemplate->cancelurl = $this->generateFrontendUrl($objPage->row(), '/step/failed');

		$objTemplate->labelCard = $GLOBALS['TL_LANG']['ISO']['cc_num'];
		$objTemplate->labelDate = $GLOBALS['TL_LANG']['ISO']['cc_exp_date'];
		$objTemplate->labelCCV = $GLOBALS['TL_LANG']['ISO']['cc_ccv'];

		$strMonths = '';
		$strYears = '';
		foreach( range(1, 12) as $month )
		{
			$month = str_pad($month, 2, '0', STR_PAD_LEFT);
			$strMonths .= '<option value="' . $month . '">' . $month . '</option>';
		}

		for( $now=date('Y'), $year=$now; $year<=$now+12; $year++ )
		{
			$strYears .= '<option value="' . substr($year, -2) . '">' . $year . '</option>';
		}

		$objTemplate->months = $strMonths;
		$objTemplate->years = $strYears;

		$objTemplate->merchantnumber = $this->epay_merchantnumber;
		$objTemplate->orderid = $objOrder->id;
		$objTemplate->description = $this->Isotope->generateAddressString($this->Isotope->Cart->billingAddress, $this->Isotope->Config->billing_fields);
		$objTemplate->currency = $this->arrCurrencies[$this->Isotope->Config->currency];
		$objTemplate->amount = $intTotal;
		$objTemplate->accepturl = $this->generateFrontendUrl($objPage->row(), '/step/complete');
		$objTemplate->declineurl = $this->generateFrontendUrl($objPage->row(), '/step/process');
		$objTemplate->instantcapture = ($this->trans_type == 'auth' ? '0' : '1');
		$objTemplate->md5key = md5($this->arrCurrencies[$this->Isotope->Config->currency] . $intTotal . $objOrder->id . $this->epay_secretkey);

		return $objTemplate->parse();
	}
}

