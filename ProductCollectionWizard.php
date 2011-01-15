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


class ProductCollectionWizard extends Widget
{

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget';

	/**
	 * Allowed row ids
	 * @var array
	 */
	protected $arrIds = false;


	/**
	 * Make sure we know the ID for ajax upload session data
	 * @param array
	 */
	public function __construct($arrAttributes=false)
	{
		$this->strId = $arrAttributes['id'];

		parent::__construct($arrAttributes);

		$_SESSION['AJAX-FFL'][$this->strId]['type'] = 'collectionWizard';

		$this->import('Database');
	}


	/**
	 * Store config for ajax upload.
	 *
	 * @access public
	 * @param string $strKey
	 * @param mixed $varValue
	 * @return void
	 */
	public function __set($strKey, $varValue)
	{
		if (!is_object($varValue))
		{
			$_SESSION['AJAX-FFL'][$this->strId][$strKey] = $varValue;
		}

		switch ($strKey)
		{
			case 'allowedIds':
				$this->arrIds = deserialize($varValue);
				break;

			case 'searchFields':
				$arrFields = array();
				foreach( $varValue as $k => $v )
				{
					if (is_numeric($k))
					{
						$arrFields[] = $v;
					}
					else
					{
						$arrFields[] = $v . ' AS ' . $k;
					}
				}
				parent::__set($strKey, $arrFields);
				break;

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Validate input and set value
	 */
	public function validator($varInput)
	{
		if ($this->mandatory && (!is_array($varInput) || !count($varInput)))
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
		}

		return $varInput;
	}



	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate()
	{
		$GLOBALS['TL_CSS'][] = 'system/modules/collectionwizard/html/collectionwizard.css';
		$GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/collectionwizard/html/collectionwizard.js';

		$this->loadLanguageFile($this->foreignTable);

		$strClass = $this->collectionType;

        if (!$this->classFileExists($strClass))
		{
			$strClass = 'IsotopeCart';
		}
		$objCollection = new $strClass();
		$objCollection->findBy('id', $this->collectionId);
		$arrProducts = $objCollection->getProducts();
		$strResults = $this->listResults($arrProducts);

			$strResults .= '
    <tr class="search" style="display:none">
      <td colspan="' . (count($this->listFields)+5) . '"><label for="ctrl_' . $this->strId . '_search">' . ($this->searchLabel=='' ? $GLOBALS['TL_LANG']['MSC']['searchLabel'] : $this->searchLabel) . ':</label> <input type="text" id="ctrl_' . $this->strId . '_search" name="keywords" class="tl_text" autocomplete="off" /></td>
    </tr>
    <tr class="jserror">
      <td colspan="' . (count($this->listFields)+5) . '"><a href="' . $this->addToUrl('noajax=1') . '">' . $GLOBALS['TL_LANG']['MSC']['tlwJavascript'] . '</a></td>
    </tr>'
    ;


		$strBuffer = '
<table cellspacing="0" cellpadding="0" id="ctrl_' . $this->strId . '" class="tl_collectionwizard" summary="Table data">
  <thead>
    <tr>
      <th class="head_0 col_first">&nbsp;</th>
      <th class="head_1">'. $GLOBALS['TL_LANG']['tl_iso_products']['type'][0] .'</th>';

      	$i = 2;
      	foreach( $this->listFields as $k => $v )
      	{
      		$field = is_numeric($k) ? $v : $k;

      		$strBuffer .= '
  	  <th class="head_' . $i . ($i==count($this->listFields) ? ' col_last' : '') . '">' . $GLOBALS['TL_LANG'][$this->foreignTable][$field][0] . '</th>';

      		$i++;
      	}

  	  	$strBuffer .= '
    <th class="head_options">'. $GLOBALS['TL_LANG']['MSC']['optionsLabel'] .'</th>
	<th class="head_qty">'. $GLOBALS['TL_LANG']['MSC']['qtyLabel'] .'</th>
	<th class="head_qty">'. $GLOBALS['TL_LANG']['MSC']['priceLabel'] .'</th>
    </tr>
  </thead>
  <tbody>
' . $strResults . '
  </tbody>
</table>
<script type="text/javascript">
<!--//--><![CDATA[//><!--' . "
window.addEvent('domready', function() {
  new CollectionWizard('" . $this->strId . "');
});
" . '//--><!]]>
</script>';
		return $strBuffer;
	}


	public function generateAjax()
	{

		$arrKeywords = trimsplit(' ', $this->Input->post('keywords'));

		$strFilter = '';
		$arrProcedures = array();
		$arrValues = array();

		foreach( $arrKeywords as $keyword )
		{
			if (!strlen($keyword))
				continue;

			$arrProcedures[] .= implode(' LIKE ? OR ', $this->searchFields) . ' LIKE ?';
			foreach($this->searchFields as $field)
			{
				$arrValues[] = '%'.$keyword.'%';
			}
		}

		if (!count($arrProcedures))
			return '';

		$typeArray = $this->Input->post($this->strName);
		if (is_array($typeArray) && count($typeArray))
		{
			foreach($typeArray as $product)
			{
				$typeData[] = $product['product'];
			}
		}
		if (is_array($typeData) && count($typeData))
		{
			$strFilter = ") AND id NOT IN (" . implode(',', $typeData);
		} elseif (strlen($typeData))
		{
			$strFilter = ") AND id NOT IN (" . $typeData;
		}

		$objItems = $this->Database->prepare("SELECT * FROM {$this->foreignTable} WHERE (" . implode(' OR ', $arrProcedures) . $strFilter . ")" . (strlen($this->sqlWhere) ? " AND {$this->sqlWhere}" : ''))
									  ->execute($arrValues);

		while( $objItems->next() )
		{
			$objProductData = $this->Database->prepare("SELECT *, (SELECT class FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id) AS product_class FROM tl_iso_products WHERE pid={$objItems->id} OR id={$objItems->id}")->limit(1)->execute();

			$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];

			try
			{
				$objProduct = new $strClass($objProductData->row());
			}
			catch (Exception $e)
			{
				$objProduct = new IsotopeProduct(array('id'=>$objItems->product_id, 'sku'=>$objItems->product_sku, 'name'=>$objItems->product_name, 'price'=>$objItems->price));
			}

			$arrResults[] = $objProduct;
		}
		$strBuffer .= $this->listResults($arrResults, true);

		if (!strlen($strBuffer))
			return '<tr class="found empty"><td colspan="' . (count($this->listFields)+1) . '">' . sprintf($GLOBALS['TL_LANG']['MSC']['tlwNoResults'], $this->Input->post('keywords')) . '</td></tr>';

		return $strBuffer;
	}


	protected function listResults($arrProducts, $blnAjax=false)
	{
		$c=0;
		$strResults = '';

		if(count($arrProducts))
		{
			foreach( $arrProducts as $objProduct)
			{
				if (is_array($this->arrIds) && !in_array($objProduct->id, $this->arrIds))
					continue;

				$objType = $this->Database->prepare("SELECT name FROM tl_iso_producttypes WHERE id=?")->limit(1)->execute($objProduct->type);

				$strResults .= '
			    <tr class="' . ($c%2 ? 'even' : 'odd') . ($c==0 ? ' row_first' : '') . ($blnAjax ? ' found' : ' existing') . '">
			      <td class="col_0 col_first"><input type="checkbox" class="checkbox" name="' . ($blnAjax ? $this->strName . '-found' :  $this->strName . '['.$c.'][product]') .'" value="' . ($objProduct->pid ? $objProduct->pid : $objProduct->id) . '"' . ($blnAjax ? '' : $this->optionChecked($objProduct->id, $this->varValue)) . ' /></td>';

	      		$strResults .= '<td class="col_1">' . $objType->name . '</td>';

	      		$i = 2;
	      		foreach( $this->listFields as $field )
	      		{

	      			$strResults .= '
	      <td class="col_' . $i . '">' . $objProduct->$field . '</td>';

	      			$i++;
	      		}
	      		$intOrder = $blnAjax ? 'x' : $c;
	      		$strResults .= $this->getProductOptions($objProduct, $intOrder, $blnAjax);

				$strResults .= '<td class="col_qty"><input style="width:20px;" type="text" class="text qty" maxlength="3" name="' . ($blnAjax ? $this->strName . '-qty' :  $this->strName . '['.$c.'][qty]') .'" value="' . ($objProduct->quantity_requested ? $objProduct->quantity_requested : '1') . '" /></td>';

				$strResults .= '<td class="col_price"><input style="width:40px;" type="text" class="text price" name="' . ($blnAjax ? $this->strName . '-price' :  $this->strName . '['.$c.'][price]') .'" value="' . $objProduct->price . '" /></td>';

	      		$strResults .= '
	    </tr>';

	    		$c++;
			}
		}

		return $strResults;
	}

	protected function getProductOptions($objProduct, $intOrder, $blnAjax=false)
	{
		$this->loadDataContainer('tl_iso_products');
		$this->loadLanguageFile('tl_iso_products');

		$arrAttributes = $objProduct->getAttributes();
		foreach($arrAttributes as $attribute => $varValue )
		{
			if ($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$attribute]['attributes']['is_customer_defined'] && $GLOBALS['TL_DCA']['tl_iso_products']['fields'][$attribute]['attributes']['add_to_product_variants'])
			{
				$arrOptionKeys[] = $attribute;
			}
		}
		$productId = $objProduct->pid ? $objProduct->pid : $objProduct->id;
		$arrProductOptions = array();
		$arrDetails = $GLOBALS['TL_DCA']['tl_iso_products'];
		if(count($arrOptionKeys))
		{
			$objVariants = $this->Database->prepare("SELECT id, price, ". implode(', ', $arrOptionKeys) ." FROM tl_iso_products WHERE pid=?")->execute($productId);
			while($objVariants->next())
			{
				$strLabel= '';
				for($i=0; $i<count($arrOptionKeys); $i++)
				{
					switch ($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$arrOptionKeys[$i]]['eval']['rgxp'])
					{
						case 'date':
							$strLabel .= date("m/d/Y",$objVariants->$arrOptionKeys[$i]) . ' - ';
							break;
						default:
							$strLabel .= $objVariants->$arrOptionKeys[$i] . ' - ';
							break;
					}
				}
				$strLabel .= $objVariants->price;
				$arrProductOptions['options'][] = array('value'=>$objVariants->id,'label'=>$strLabel);
			}
		}

		if(count($arrProductOptions))
		{
			foreach($arrProductOptions as $name => $options)
			{
				$arrData = deserialize($options);
				$widget = new SelectMenu();
				$widget->id = $name;
				$widget->name = $blnAjax ? $this->strName . '-options' :  $this->strName . '['.$intOrder.'][product]';
				$widget->mandatory = true;
				$widget->value = $objProduct->id;
				$widget->label = $this->getOptionLabel($objProduct->type);
				$widget->options = $arrData;
				$strResults .= '
					<td class="col_option">' . $widget->parse() . '</td>';
			}
		} else
		{
			$strResults .= '
					<td class="col_option"></td>';
		}


		return $strResults;

	}

	protected function getOptionLabel($intId)
	{
      	$this->loadDataContainer('tl_iso_products');
  		$objAttributes = $this->Database->prepare("SELECT attributes FROM tl_iso_producttypes WHERE id=?")->limit(1)->execute($intId);
  		if($objAttributes->numRows)
  		{
      		$arrAttributes = deserialize($objAttributes->attributes);
      		$arrNames = array();
      		foreach( $arrAttributes as $attribute)
			{
				if ($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$attribute]['attributes']['add_to_product_variants'])
				{
					$name = $this->Database->prepare("SELECT name FROM tl_iso_attributes WHERE field_name=?")->limit(1)->execute($attribute);
					$arrNames[] = ($name->numRows ? $name->name : $GLOBALS['TL_LANG'][$this->foreignTable][$attribute][0]);

				}
			}
			$strLabel = implode('/',$arrNames);
			return $strLabel;
		}

		return;

	}

}

