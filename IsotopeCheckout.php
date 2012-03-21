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
 * @copyright  Isotope eCommerce Workgroup 2012
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class IsotopeCheckout extends Frontend
{
	
	/**
	 * Generate the frontend module which will inject autocompleter functionality
	 */
	public function autocompleteCheckoutAddresses($objModule=null, $blnReview=false)
	{
		if ($blnReview)
		{
			return false;
		}
		
		return "
<script src=\"system/modules/isotope_postal/html/IsotopePostal.js\"></script>
<script>
window.addEvent('domready', function() { IsotopePostal.checkoutAddresses() });
</script>";
	}
	
	
	public function findCity()
	{
		if ($this->Input->get('action') != 'isotope_postal')
		{
			return false;
		}

		$strCountry = $this->Input->get('country');
		$strPostal = $this->Input->get('postal');
		
		$objResult = $this->Database->prepare("SELECT * FROM tl_iso_postal WHERE country=? AND (postal_from=? OR (postal_to!='' AND CAST(postal_from AS UNSIGNED)<=? AND CAST(postal_to AS UNSIGNED)>=?))")
						  ->executeUncached($strCountry, $strPostal, $strPostal, $strPostal);
		
		if ($objResult->numRows == 1)
		{
			return $objResult->row();
		}
		
		return '';
	}
}

