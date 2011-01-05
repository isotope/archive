<?php

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
 * @copyright  Intelligent Spark 2010
 * @author     Fred Bliss <http://www.intelligentspark.com>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */



/**
 * Class IsotopeExport
 *
 */
class IsotopeExport extends BackendModule
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_export';

	
		/**
	 * Generate module
	 */
	public function compile()
	{
		$this->loadLanguageFile('tl_export');
		$this->loadLanguageFile('tl_iso_products');
		
		if($this->Input->post('FORM_SUBMIT')=='tl_export')
		{
			$arrFields = $this->Input->post('export_fields');
			$strRows = '';
			$objRows = $this->Database->query("SELECT ".implode(',', $arrFields)." FROM tl_iso_products");
			
			while($objRows->next())
			{
				$arrData = $objRows->row();
				$arrFieldData = array();
				
								
				foreach($arrData as $k=>$v)
				{					
					$arrFieldData[] = (is_string($v) ? "^".mysql_escape_string(str_replace("\\","",$v))."^" : $v);
				}
				
				$strRows .= implode(",",$arrFieldData) . "\n";
			}		
			
			$strFields = "^".implode("^,^",$arrFields)."^";	
		
			$strFinal = $strFields . "\n" . $strRows;
			
			$name = "export_".time();
			
			header("application/csv");
			header("Content-disposition: attachment; filename=$name.csv");
			
			echo $strFinal;
			exit;
		}
		
		$arrAttrLabels = array();
		
		$arrFieldData = $this->Database->listFields('tl_iso_products');
		
		foreach($arrFieldData as $field)
		{
			$arrAttributeKeys[] = $field['name'];
		}
		
		$arrAttrLabels = $this->getAttributeLabels($arrAttributeKeys);
		
		foreach($arrFieldData as $field)
		{
			$strLabel = (array_key_exists($field['name'], $GLOBALS['TL_LANG']['tl_iso_products']) ? $GLOBALS['TL_LANG']['tl_iso_products'][$field['name']][0] : $arrAttrLabels[$field['name']]);
			
			if(!$strLabel)
				$strLabel = $field['name'];
			
			$arrData[] = array
			(
				'value' 	=> $field['name'],
				'label'		=> $strLabel
			);
		
		}

		$arrWidget['name'] = 'export_fields';
		$arrWidget['id'] = 'export_fields';
		$arrWidget['label'] = 'Export fields';
		$arrWidget['options'] = $arrData;
		$arrWidget['eval'] = array('multiple'=>true,'mandatory'=>true);
		
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_export']['isotope_export'];
		$this->Template->href = ampersand(str_replace('?do=isotope_export','',$this->Environment->request));
		$this->Template->button = $GLOBALS['TL_LANG']['tl_export']['goBack'];
		$this->Template->checkboxWidget = $this->getCheckboxWidget($arrWidget);
		$this->Template->sLabel = $GLOBALS['TL_LANG']['tl_export']['sLabel'];
	}
	
	/**
	 * Return the title widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getCheckboxWidget($arrWidget)
	{
		$widget = new CheckBox();
		
		$widget->name 		= $arrWidget['name'];
		$widget->id   		= $arrWidget['id'];
		$widget->label		= $arrWidget['label'];
		$widget->options 	= $arrWidget['options'];
		$widget->multiple	= $arrWidget['eval']['multiple'];
		$widget->mandatory  = $arrWidget['eval']['mandatory'];
		
		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_export')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}

	
	private function getAttributeLabels($arrAttributeKeys)
	{
		$arrLabels = array();
				
		$arrLabels = $this->Database->query("SELECT name,field_name FROM tl_iso_attributes WHERE field_name IN('".implode("','",$arrAttributeKeys)."')")->fetchAllAssoc();

		if(count($arrLabels))
		{	
			foreach($arrLabels as $label)
			{
				$arrReturn[$label['field_name']] = $label['name'];
			}
			return $arrReturn;
		}
		
		return array();
	}

}