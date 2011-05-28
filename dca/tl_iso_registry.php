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


$this->loadDataContainer('tl_iso_products');
$this->loadLanguageFile('tl_iso_products');


/**
 * Table tl_iso_registry
 */
$GLOBALS['TL_DCA']['tl_iso_registry'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'ctable'					  => array('tl_iso_registry_items'),
		'enableVersioning'            => false,
		'switchToEdit'                => true,
		'onload_callback' 			  => array
		(
			array('tl_iso_registry', 'checkPermission'),
		),
		'ondelete_callback'			  => array
		(
			array('tl_iso_registry', 'archiveRecord'),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'                    => 2,
			'fields'                  => array('date DESC','name'),
			'flag'                    => 1,
			'panelLayout'             => 'filter;sort,search,limit'
		),
		'label' => array
		(
			'fields'                  => array('name'),
			'label'                   => '%s',
			'label_callback'          => array('tl_iso_registry', 'getOrderLabel')
		),
		'global_operations' => array
		(
			'tools' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_iso_registry']['tools'],
				'href'                => '',
				'class'               => 'header_isotope_tools',
				'attributes'          => 'onclick="Backend.getScrollOffset();" style="display:none"',
			),
			'print_registries' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_iso_registry']['print_registries'],
				'href'                => 'key=print_registries',
				'class'               => 'header_print_registries isotope-tools',
				'attributes'          => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_iso_registry']['edit'],
				'href'                => 'act=edit',
				'icon'                => 'edit.gif'
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_iso_registry']['delete'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_iso_registry']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
			'print_registry' => array
			(
				'label'			=> &$GLOBALS['TL_LANG']['tl_iso_registry']['print_registry'],
				'href'			=> 'key=print_registry',
				'icon'			=> 'system/modules/isotope_giftregistry/html/printer.png'
			),
			'buttons' => array
			(
				'button_callback'     => array('tl_iso_registry', 'moduleOperations'),
			),
			'edit_items' => array
			(
				'label'				  => &$GLOBALS['TL_LANG']['tl_iso_registry']['edit_items'],
				'href'				  => 'table=tl_iso_registry_items',
				'icon'				  => 'system/modules/isotope_giftregistry/html/edit_items.png'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'                     => 'name,second_party_name,date,event_type,description',
	),

	// Fields
	'fields' => array
	(
		'name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_iso_registry']['name'],
			'search'                  => true,
			'exclude'				  => true,
			'sorting'				  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'mandatory'=>true),
		),
		'second_party_name' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_iso_registry']['second_party_name'],
			'search'                  => true,
			'exclude'				  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255),
		),
		'date' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_iso_registry']['date'],
			'inputType'               => 'text',
			'flag'					  => 8,
			'filter'				  => true,
			'sorting'				  => true,
			'eval'                    => array('rgxp'=>'date', 'mandatory'=>true, 'datepicker'=>$this->getDatePickerString(), 'tl_class'=>'w50 wizard')
		),
		'event_type' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_iso_registry']['event_type'],
			'inputType'				  => 'select',
			'options'				  => array('wedding', 'bar_mitzvah', 'bat_mitzvah'),
			'reference'				  => &$GLOBALS['TL_LANG']['tl_iso_registry'],
			'eval'					  => array('mandatory'=>true, 'includeBlankOption'=>true),
		),
		'description' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_iso_registry']['description'],
			'exclude'				  => true,
			'inputType'               => 'textarea',
			'eval'					  => array('rte'=>'tinyMCE','tl_class'=>'clr'),
		),
	)
);


/**
 * tl_iso_registry class.
 *
 * @extends Backend
 */
class tl_iso_registry extends Backend
{

	public function __construct()
	{
		parent::__construct();

		$this->import('Isotope');
	}


	/**
	 * Return a string of more buttons for the orders module.
	 *
	 * @todo I don't think we need that...
	 *
	 * @access public
	 * @param array $arrRow
	 * @return string
	 */
	public function moduleOperations($arrRow)
	{
		if(!count($GLOBALS['ISO_ORDERS']['operations']))
		{
			return;
		}

		foreach($GLOBALS['ISO_ORDERS']['operations'] as $k=>$v)
		{


			$objPaymentType = $this->Database->prepare("SELECT type FROM tl_iso_payment_modules WHERE id=?")
											 ->limit(1)
											 ->execute($arrRow['payment_id']);

			if($objPaymentType->numRows && $objPaymentType->type==$k)
			{
					$strClass = $v;

					if (!strlen($strClass) || !$this->classFileExists($strClass))
						return '';

					try
					{
						$objModule = new $strClass($arrRow);
						$strButtons .= $objModule->moduleOperations($arrRow['id']);
					}
					catch (Exception $e) {}

			}
		}

		return $strButtons;
	}


	/**
	* getOrderLabel function.
	*
	* @access public
	* @param array $row
	* @param string $label
	* @return string
	*/
	public function getOrderLabel($row, $label)
	{
		$this->Isotope->overrideConfig($row['config_id']);
		$strBillingAddress = $this->Isotope->generateAddressString(deserialize($row['billing_address']), $this->Isotope->Config->billing_fields);

		return '
<div style="float:left; width:40px">' . $row['id'] . '</div>
<div style="float:left; width:130px;">' . $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $row['date']) . '</div>
<div style="float:left; width:180px">' . $row['name'] . ', ' . $row['second_party_name'] .'</div>';
	}


	public function showDetails($dc, $xlabel)
	{
		$objOrder = $this->Database->prepare("SELECT * FROM tl_iso_registry WHERE id=?")->limit(1)->execute($dc->id);

		if ($objOrder->numRows)
		{
			$GLOBALS['TL_HOOKS']['outputBackendTemplate'][] = array('tl_iso_registry', 'injectPrintCSS');

			return $this->getOrderDescription($objOrder->row());
		}

		return '';
	}


	protected function getOrderDescription($row)
	{
		$this->Input->setGet('uid', $row['uniqid']);
		$objModule = new ModuleIsotopeOrderDetails($this->Database->execute("SELECT * FROM tl_module WHERE type='iso_orderdetails'"));
		return $objModule->generate(true);
	}


	/**
	* Review order page stores temporary information in this table to know it when user is redirected to a payment provider. We do not show this data in backend.
	*
	* @access public
	* @param object $dc
	* @return void
	*/
	public function checkPermission($dc)
	{
		if (strlen($this->Input->get('act')))
		{
			$GLOBALS['TL_DCA']['tl_iso_registry']['config']['closed'] = false;
		}

		// Hide archived (used and deleted) registries
		$arrModules = $this->Database->execute("SELECT id FROM tl_iso_registry WHERE archive<2")->fetchEach('id');

		if (!count($arrModules))
		{
			$arrModules = array(0);
		}

		$GLOBALS['TL_DCA']['tl_iso_registry']['list']['sorting']['root'] = $arrModules;

		// Check current action
		switch ($this->Input->get('act'))
		{
			case 'edit':
			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array($this->Input->get('id'), $arrModules))
				{
					$this->log('Not enough permissions to '.$this->Input->get('act').' tax class ID "'.$this->Input->get('id').'"', 'tl_iso_registry checkPermission()', TL_ACCESS);
					$this->redirect($this->Environment->script.'?act=error');
				}
				break;

			case 'editAll':
			case 'copyAll':
			case 'deleteAll':
				$session = $this->Session->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $arrModules);
				$this->Session->setData($session);
				break;
		}
	}


	/**
	 * Record is deleted, archive if necessary
	 */
	public function archiveRecord($dc)
	{
	}

	public function injectPrintCSS($strBuffer)
	{
		return str_replace('</head>', '<link rel="stylesheet" type="text/css" href="system/modules/isotope/html/print.css" media="print" />' . "\n</head>", $strBuffer);
	}


		/**
	 * Provides an interface to edit registry items
	 *
	 * @access public
	 * @param object $dc
	 */
    public function editRegistryItems($dc)
	{
		$objItems = $this->Database->prepare("SELECT id, pid, price, product_quantity, (SELECT name FROM tl_iso_products WHERE tl_iso_products.id=tl_iso_registry_items.product_id) AS product_name FROM tl_iso_registry_items WHERE pid=?")->execute($dc->id);

		$arrFields = array();
		$arrEditFields = array('price','product_quantity');

		$strBuffer .= '<div id="tl_buttons">
<a href="'.ampersand(str_replace('&key=edit_items', '', $this->Environment->request)).'" class="header_back" title="'.specialchars($GLOBALS['TL_LANG']['MSC']['backBT']).'">'.$GLOBALS['TL_LANG']['MSC']['backBT'].'</a>
</div>

<h2 class="sub_headline">'.sprintf($GLOBALS['TL_LANG']['tl_iso_products']['quick_edit'][1], $dc->id).'</h2>'.$this->getMessages().'

<form action="'.ampersand($this->Environment->request, true).'" id="tl_iso_order_item_edit" class="tl_form" method="post">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_iso_order_item_edit" />

<div class="tl_tbox block">
<table width="100%" border="0" cellpadding="5" cellspacing="0" summary="">
<thead>
<th align="center">' . $GLOBALS['TL_LANG']['tl_iso_products']['name'][0] . '</th>
<th align="center">'.$GLOBALS['TL_LANG']['tl_iso_orders']['price'][0].'</th>
<th>&nbsp;</th>
<th align="center">'.$GLOBALS['TL_LANG']['tl_iso_orders']['product_quantity'][0].'</th>
<th align="center">'.$GLOBALS['TL_LANG']['tl_iso_orders']['item_total'][0].'</th>';

//$strBuffer .= '<th><img src="system/themes/default/images/published.gif" width="16" height="16" alt="' . $GLOBALS['TL_LANG']['tl_iso_products']['published'][0].'" /></th></thead>';

		$globalDoNotSubmit = false;

		while($objItems->next())
		{

			$arrWidgets = array();
			$doNotSubmit = false;
			$arrSet = array();
			$arrSet['id'] = $objItems->id;
			foreach($arrEditFields as $field)
			{
				$arrWidgets[$field] = new TextField($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_products']['fields'][$field], $field.'[' . $objItems->id .']', $objItems->{$field}));
			}
			/*
			$arrWidgets['sku'] = new TextField($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_products']['fields']['sku'], 'sku[' . $objItems->id . ']', $objItems->sku));

			$arrWidgets['price'] = new TextField($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_products']['fields']['price'], 'price[' . $objItems->id . ']', $objItems->price));

			$arrWidgets['weight'] = new TextField($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_products']['fields']['weight'], 'weight[' . $objItems->id . ']', $objItems->weight));

			$arrWidgets['stock_quantity'] = new TextField($this->prepareForWidget($GLOBALS['TL_DCA']['tl_iso_products']['fields']['stock_quantity'], 'stock_quantity[' . $objItems->id . ']', $objItems->stock_quantity));
			*/

			foreach($arrWidgets as $key=>$objWidget)
			{

				switch($key)
				{
					case 'price':
					case 'product_quantity':
						$objWidget->class = 'tl_text_4';
						break;
					default:
						$objWidget->class = 'tl_text_3';
						break;
				}

				if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_order_item_edit')
				{
					$objWidget->validate();

					if ($objWidget->hasErrors())
					{
						$doNotSubmit = true;
						$globalDoNotSubmit = true;
					}
					else
					{
						$arrSet[$key] = $objWidget->value;
					}
				}
			}

			if($this->Input->post('FORM_SUBMIT') == 'tl_iso_registry_item_edit' && !$doNotSubmit)
			{
				//update the values for each
				$this->Database->prepare("UPDATE tl_iso_registry_items %s WHERE id=?")
							   ->set($arrSet)
							   ->execute($arrSet['id']);
			}

			$strBuffer .= '
<tr>
	<td align="center">'.$objItems->product_name.'</td>';
	$i=0;
	foreach($arrEditFields as $field)
	{

		$strBuffer .= ($i==1 ? '<td align="center">x</td><td align="center">'.$arrWidgets[$field]->generate().'</td><td align="center">'.$objItems->price*$objItems->{$field}.'</td>' : '<td align="center">' . $arrWidgets[$field]->generate().'</td>');
		$i++;
	}

	$strBuffer .= '</tr>';

		}	 // end $objItems->next()

		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_registry_item_edit' && !$globalDoNotSubmit)
		{
			$objOrder = new IsotopeOrder();

			if ($objOrder->findBy('id', $dc->id))
			{
				$this->import('Isotope');

				$this->Isotope->Order = $objOrder;	//Todo - separate Cart from backend order.

				$arrSet = array
				(
					'subTotal'		=> $this->Isotope->Order->subTotal,
					'taxTotal'		=> $this->Isotope->Order->taxTotal,
					'shippingTotal' => $this->Isotope->Order->shippingTotal,
					'surcharges'	=> serialize($this->Isotope->Order->getSurcharges()),
					'grandTotal'	=> $this->Isotope->Order->grandTotal
				);

				$this->Database->prepare("UPDATE tl_iso_orders %s WHERE id=?")
							   ->set($arrSet)
							   ->execute($dc->id);
			}

			if (strlen($this->Input->post('saveNclose')))
			{
				$this->redirect(str_replace('&key=edit_items', '', $this->Environment->request));
			}
			else
			{
				$this->reload();
			}
		}

		return $strBuffer . '
</table>
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['save']).'" />
  <input type="submit" name="saveNclose" id="saveNclose" class="tl_submit" accesskey="c" value="'.specialchars($GLOBALS['TL_LANG']['MSC']['saveNclose']).'" />
</div>

</div>
</form>';
	}


}



