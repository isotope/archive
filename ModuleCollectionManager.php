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


class ModuleCollectionManager extends BackendModule
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_iso_collectionmanager';

	/**
	 * Save input
	 * @var boolean
	 */
	protected $blnSave = true;

	/**
	 * Advanced mode
	 * @var boolean
	 */
	protected $blnAdvanced = true;


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		$this->import('BackendUser', 'User');
		$this->loadLanguageFile('tl_iso_collectionmanager');
		
		if($this->Input->get('tbl'))
		{
			$this->loadLanguageFile($this->Input->get('tbl'));
		}

		switch ($this->Input->get('act'))
		{
			case 'create':
				$this->createCollection();
				break;

			case 'edit':
				$this->editCollection();
				break;

			case 'delete':
				$this->deleteCollection();
				break;

			default:
				$this->showAllCollections();
				break;
		}

		$this->Template->request = ampersand($this->Environment->request, true);

		// Load scripts
		$GLOBALS['TL_CSS'][] = 'system/modules/isotope_collectionmanager/html/collectionmanager.css';
		$GLOBALS['TL_CSS'][] = 'plugins/tablesort/css/tablesort.css?'. TABLESORT .'|screen';
		$GLOBALS['TL_JAVASCRIPT'][] = 'plugins/tablesort/js/tablesort.js?' . TABLESORT;
	}


	/**
	 * Show all carts
	 */
	protected function showAllCollections()
	{
		//Set default type
		$session = $this->Session->getData();
		if(!strlen($session['type']['tl_iso_collectionmanager']))
		{
			$session['type']['tl_iso_collectionmanager'] = 'tl_iso_cart';
			$this->Session->setData($session);
			$this->reload();
		}
		
		$this->Template->carts = array();

		// Set default variables
		$this->Template->apply = $GLOBALS['TL_LANG']['MSC']['apply'];
		$this->Template->noCollections = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['noCollections'];
		$this->Template->createTitle = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['new'][0] . $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$session['type']['tl_iso_collectionmanager']];
		$this->Template->createLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['new'][1] . $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$session['type']['tl_iso_collectionmanager']];
		$this->Template->editLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['edit'][0];
		$this->Template->deleteLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['delete'][0];
		
		$this->Template->thTitle = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['title'][0];
		$this->Template->thDatefilter = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['datefilter'];

		$this->Template->createHref = $this->addToUrl('act=create&amp;tbl=' . $session['type']['tl_iso_collectionmanager']);


		// Get collection object
		if (($objCollection = $this->getCollections()) != true)
		{
			return;
		}

		$count = -1;
		$time = time();
		$max = ($objCollection->numRows - 1);
		$arrCollections = array();

		// List collections
		while ($objCollection->next())
		{
			$trClass = 'row_' . ++$count . (($count == 0) ? ' row_first' : '') . (($count >= $max) ? ' row_last' : '') . (($count % 2 == 0) ? ' odd' : ' even');
			$tdClass = '';

			$deleteHref = '';
			$deleteTitle = '';
			$deleteIcon = 'system/themes/' . $this->getTheme() . '/images/delete_.gif';
			$deleteConfirm = '';

			// Check delete permissions
			if ($this->User->isAdmin || $this->User->id == $objCollection->createdBy)
			{
				$deleteHref = $this->addToUrl('act=delete&amp;id=' . $objCollection->id);
				$deleteTitle = sprintf($GLOBALS['TL_LANG']['tl_iso_collectionmanager']['delete'][1], $objCollection->id);
				$deleteIcon = 'system/themes/' . $this->getTheme() . '/images/delete.gif';
				$deleteConfirm = sprintf($GLOBALS['TL_LANG']['tl_iso_collectionmanager']['delConfirm'], $objCollection->id);
			}

			$arrCollections[] = array
			(
				'id' => $objCollection->id,
				'user' => ($objCollection->lastname) ? $objCollection->lastname . ', ' . $objCollection->firstname : '',
				'title' => $objCollection->title,
				'progress' => $objCollection->progress,
				'deadline' => $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $objCollection->deadline),
				'status' => (strlen($GLOBALS['TL_LANG']['tl_iso_cart_items'][$objCollection->status]) ? $GLOBALS['TL_LANG']['tl_iso_cart_items'][$objCollection->status] : $objCollection->status),
				'creator' => sprintf($GLOBALS['TL_LANG']['tl_iso_collectionmanager']['createdBy'], $objCollection->creator),
				'editHref' => $this->addToUrl('act=edit&amp;id=' . $objCollection->id . '&amp;tbl=' . $session['type']['tl_iso_collectionmanager']),
				'editTitle' => sprintf($GLOBALS['TL_LANG']['tl_iso_collectionmanager']['edit'][1], $objCollection->id),
				'editIcon' => 'system/themes/' . $this->getTheme() . '/images/edit.gif',
				'deleteHref' => $deleteHref,
				'deleteTitle' => $deleteTitle,
				'deleteIcon' => $deleteIcon,
				'deleteConfirm' => $deleteConfirm,
				'trClass' => $trClass,
				'tdClass' => $tdClass
			);
		}

		$this->Template->carts = $arrCollections;
	}


	/**
	 * Create a cart
	 */
	protected function createCollection()
	{
		$this->loadDataContainer($this->Input->get('tbl'));
		
		$this->Template = new BackendTemplate('be_iso_collection_create');
		
		$arrConfig = $GLOBALS['ISO_COL_MANAGER'][$this->Input->get('tbl')];
		
		$this->Template->goBack = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->headline = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['new'][1]. $arrConfig['type'];
		
		$arrFields=array();
		
		foreach($arrConfig['fields'] as $field=>$data)
		{
			switch($data['inputType'])
			{
				case 'jumpTo':
					$arrFields[$field] = $this->getSelectWidget($field, $this->getJumptoOptions($data));
					break;
				case 'collectionWizard':
					$arrFields[$field] = ($field=='products') ? $this->getCollectionWizard($field, $data) : $this->getCollectionWizard($field, $data);
					break;
					
				case 'tableLookup':
					$arrFields[$field] = $this->getTableLookupWizard($field, $data);
					break;
					
				case 'textarea':
					$arrFields[$field] = $this->getTextareaWidget($field, $data);
					break;
					
				default:
					$arrFields[$field] = $this->getTextFieldWidget($field, $data);
					break;
			}
			
		}		
		$this->Template->fields = $arrFields;

		// Save cart
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager' && $this->blnSave)
		{
			//Create Collection Object
			$objCollection = new $arrConfig['class'];
			$objCollection->setData(array
			(
				'pid'			=> ($this->Input->post('pid') ? $this->Input->post('pid') : 0),
				'session'		=> '',
				'tstamp'		=> time(),
				'store_id'		=> $this->Input->post('store'),
			));
			if (!$objCollection->findBy('id', $objCollection->save(true)))
			{
				throw new Exception('Unable to create collection');
			}
			$intId = $objCollection->id;
			$arrProducts = $this->Input->post('products');
			
			foreach($arrProducts as $product)
			{
				$intProductId = $product['options'] ? $product['options'] : $product['product'];
				
				$objProductData = $this->Database->prepare("SELECT *, (SELECT class FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id) AS product_class FROM tl_iso_products WHERE id={$intProductId}")->limit(1)->execute();
							
				$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];
				
				try
				{
					$objProduct = new $strClass($objProductData->row());
				}
				catch (Exception $e)
				{
					$objProduct = new IsotopeProduct(array('id'=>$intProductId));
				}
				$objProduct->reader_jumpTo = $this->Input->post('jumpTo');
				//Product exists - update it
				if (in_array($objProduct->id, $this->getProductArray($objCollection)))
				{
					$objItem = $this->Database->prepare("SELECT id FROM {$arrConfig['ctable']} WHERE pid={$objCollection->id} AND product_id={$objProduct->id}")->limit(1)->execute();
					$objProduct->cart_id = $objItem->id;
					$arrSet = array('product_quantity'=>$product['qty'], 'price'=>$product['price']);
					$blnInsert = $objCollection->updateProduct($objProduct, $arrSet);
				}
				//Add new product
				else
				{
					$objProduct->price = $product['price'];
					$blnInsert = $objCollection->addProduct($objProduct, $product['qty']);
				}
					
			}
			
			$arrValues = array();
			foreach($arrConfig['fields'] as $field => $value)
			{
				if($field !='products' && $field !='jumpTo')
				{
					$arrValues[$field] = ($this->Input->post($field) ? $this->Input->post($field) : 0);
				}
			}
			if(count($arrValues))
			{
				$objCollection->setData($arrValues);
				$objCollection->save();
			}
			//@TODO: Merge carts if one already exists for the PID
			// Go back
			$this->redirect(str_replace('&act=create', '&act=edit', $this->Environment->request) . '&id=' . $intId);
		}

		$this->Template->submit = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['createSubmit'];
		$this->Template->titleLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['title'][0];
		$this->Template->assignLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['assignedTo'];
	}


	/**
	 * Edit a cart
	 */
	protected function editCollection()
	{
		$this->loadDataContainer($this->Input->get('tbl'));
		$this->loadLanguageFile($this->Input->get('tbl'));
		
		$this->Template = new BackendTemplate('be_iso_collection_edit');
		
		$this->Template->goBack = $GLOBALS['TL_LANG']['MSC']['goBack'];
		$this->Template->headline = sprintf($GLOBALS['TL_LANG']['tl_iso_collectionmanager']['edit'][1], $this->Input->get('id'));
		
		$arrConfig = $GLOBALS['ISO_COL_MANAGER'][$this->Input->get('tbl')];

		$objCollection = new $arrConfig['class'];

		if (!$objCollection->findBy('id',$this->Input->get('id')))
		{
			$this->log('Invalid cart ID! "' . $this->Input->get('id') . '"', 'ModuleCollectionManager editCollection()', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
				
		$arrFields=array();
		
		foreach($arrConfig['fields'] as $field=>$data)
		{
			switch($data['inputType'])
			{
				case 'jumpTo':
					$arrFields[$field] = $this->getSelectWidget($field, $this->getJumptoOptions($data));
					break;
				case 'collectionWizard':
					$arrFields[$field] = ($field=='products') ? $this->getCollectionWizard($field, $data, $this->getProductArray($objCollection)) : $this->getCollectionWizard($field, $data, $objCollection->$field);
					break;
					
				case 'tableLookup':
					$arrFields[$field] = $this->getTableLookupWizard($field, $data, $objCollection->$field);
					break;
					
				case 'textarea':
					$arrFields[$field] = $this->getTextareaWidget($field, $data, $objCollection->$field);
					break;
					
				default:
					$arrFields[$field] = $this->getTextFieldWidget($field, $data, $objCollection->$field);
					break;
			}
			
		}		
		$this->Template->fields = $arrFields;
		
		// Update cart
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager' && $this->blnSave)
		{
		
			$arrProducts = $this->Input->post('products');
			
			foreach($arrProducts as $product)
			{
				$intProductId = $product['options'] ? $product['options'] : $product['product'];
				
				$objProductData = $this->Database->prepare("SELECT *, (SELECT class FROM tl_iso_producttypes WHERE tl_iso_products.type=tl_iso_producttypes.id) AS product_class FROM tl_iso_products WHERE id={$intProductId}")->limit(1)->execute();
							
				$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];
				
				try
				{
					$objProduct = new $strClass($objProductData->row());
				}
				catch (Exception $e)
				{
					$objProduct = new IsotopeProduct(array('id'=>$intProductId));
				}
				$objProduct->reader_jumpTo = $this->Input->post('jumpTo');
				//Product exists - update it
				if (in_array($objProduct->id, $this->getProductArray($objCollection)))
				{
					$objItem = $this->Database->prepare("SELECT id FROM {$arrConfig['ctable']} WHERE pid={$objCollection->id} AND product_id={$objProduct->id}")->limit(1)->execute();
					$objProduct->cart_id = $objItem->id;
					$arrSet = array('product_quantity'=>$product['qty'], 'price'=>$product['price']);
					$objCollection->updateProduct($objProduct, $arrSet);
				}
				//Add new product
				else
				{
					$objProduct->price = $product['price'];
					$objCollection->addProduct($objProduct, $product['qty']);
				}
			}
			$arrValues = array();
			foreach($arrConfig['fields'] as $field => $value)
			{
				if($field !='products' && $field !='jumpTo')
				{
					$arrValues[$field] = ($this->Input->post($field) ? $this->Input->post($field) : 0);
				}
			}
			if(count($arrValues))
			{
				$objCollection->setData($arrValues);
				$objCollection->save();
			}
			//@TODO: Merge carts if one already exists for the PID
			
			// Go back
			$this->reload();
		}

		$this->Template->submit = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['editSubmit'];
		$this->Template->titleLabel = $GLOBALS['TL_LANG']['tl_iso_collectionmanager']['title'][0];
	}


	/**
	 * Delete a cart
	 */
	protected function deleteCollection()
	{
		$session = $this->Session->getData();
		$arrTable = $GLOBALS['ISO_COL_MANAGER'][$session['type']['tl_iso_collectionmanager']];
		
		$objCollection = $this->Database->prepare("SELECT * FROM {$session['type']['tl_iso_collectionmanager']} WHERE id=?")
								  ->limit(1)
								  ->execute($this->Input->get('id'));

		if ($objCollection->numRows < 1)
		{
			$this->log('Invalid cart ID "' . $this->Input->get('id') . '"', 'ModuleCollectionManager deleteCollection()', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		// Check if the user is allowed to delete the cart
		if (!$this->User->isAdmin)
		{
			$this->log('Not enough permissions to delete cart ID "' . $this->Input->get('id') . '"', 'ModuleCollectionManager deleteCollection()', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}

		$affected = 1;
		$strClass = $arrTable['class'];

        if (!$this->classFileExists($strClass))
		{
			$strClass = 'IsotopeCart';
		}
		$objCollection = new $strClass();
		if (!$objCollection->findBy('id',$this->Input->get('id')))
		{
			$this->log('Invalid cart ID "' . $this->Input->get('id') . '"', 'ModuleCollectionManager editCollection()', TL_ERROR);
			$this->redirect('contao/main.php?act=error');
		}
		$affected = $objCollection->delete();
		
		// Delete data and add a log entry
		if ($affected)
		{
			$this->log('DELETE FROM '.$session['type']['tl_iso_collectionmanager'].' WHERE id=' . $this->Input->get('id'), 'ModuleCollectionManager deleteCollection()', TL_GENERAL);
		}

		// Go back
		$this->redirect($this->getReferer());
	}


	/**
	 * Select all collections for the current table from the DB and return the result objects
	 * @return object
	 */
	protected function getCollections()
	{
		$where = array();
		$value = array();
		$session = $this->Session->getData();
		
		//Inner join to limit to collections with members
		$query = "SELECT c.*, c.id AS id, (SELECT lastname FROM tl_member m WHERE m.id=c.pid) AS lastname, (SELECT firstname FROM tl_member m WHERE m.id=c.pid) AS firstname FROM ". $session['type']['tl_iso_collectionmanager'] ." c INNER JOIN tl_member m ON m.id=c.pid";		
		
		// Set filter
		if ($this->Input->post('FORM_SUBMIT') == 'tl_filters')
		{
			// Search
			$session['search']['tl_iso_collectionmanager']['value'] = '';
			$session['search']['tl_iso_collectionmanager']['field'] = $this->Input->post('tl_field', true);
			
			// Types
			if ($this->Input->post('tl_type') != '')
			{
				try
				{
					$this->Database->prepare("SELECT * FROM " . $this->Input->post('tl_type'))
								   ->limit(1)
								   ->execute();

					$session['type']['tl_iso_collectionmanager'] = $this->Input->post('tl_type');
				}
				catch (Exception $e) {}
			}


			// Make sure the regular expression is valid
			if ($this->Input->postRaw('tl_value') != '')
			{
				switch ($session['search']['tl_iso_collectionmanager']['field'])
				{
					case 'firstname':
					case 'lastname':
						$strTable = 'tl_member';
						break;
					
					default:
						$strTable = $session['type']['tl_iso_collectionmanager'];
						break;						
				}
				
				try
				{
					$this->Database->prepare("SELECT * FROM ". $strTable ." WHERE " . $this->Input->post('tl_field', true) . " REGEXP ?")
								   ->limit(1)
								   ->execute($this->Input->postRaw('tl_value'));

					$session['search']['tl_iso_collectionmanager']['value'] = $this->Input->postRaw('tl_value');
				}
				catch (Exception $e) {}		
				
			}

			// Date Filter
			$session['filter']['tl_iso_collectionmanager']['tstamp'] = $this->Input->post('tstamp');

			$this->Session->setData($session);
			$this->reload();
		}

		// Add search value to query
		if (strlen($session['search']['tl_iso_collectionmanager']['value']))
		{
			switch ($session['search']['tl_iso_collectionmanager']['field'])
			{
				case 'firstname':
				case 'lastname':
					$strField = 'm.' . $session['search']['tl_iso_collectionmanager']['field'];
					break;
				
				default:
					$strField = $session['search']['tl_iso_collectionmanager']['field'];
					break;						
			}
		
			$where[] = "CAST(" . $strField . " AS CHAR) REGEXP ?";
			$value[] = $session['search']['tl_iso_collectionmanager']['value'];

			$this->Template->searchClass = ' active';
		}
		$this->loadLanguageFile($session['type']['tl_iso_collectionmanager']);
		// Search options
		foreach($GLOBALS['ISO_COL_MANAGER'][$session['type']['tl_iso_collectionmanager']]['search'] as $search)
		{
			$fields[] = $search;
		}
		$options = '';

		foreach ($fields as $field)
		{
			$options .= sprintf('<option value="%s"%s>%s</option>', $field, (($field == $session['search']['tl_iso_collectionmanager']['field']) ? ' selected="selected"' : ''), (is_array($GLOBALS['TL_LANG'][$session['type']['tl_iso_collectionmanager']]['fields'][$field]) ? $GLOBALS['TL_LANG'][$session['type']['tl_iso_collectionmanager']]['fields'][$field][0] : $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$field]));
		}

		$this->Template->searchOptions = $options;
		$this->Template->keywords = specialchars($session['search']['tl_iso_collectionmanager']['value']);
		$this->Template->search = specialchars($GLOBALS['TL_LANG']['MSC']['search']);

		// Add date value to query
		if (strlen($session['filter']['tl_iso_collectionmanager']['tstamp']))
		{
			$objDate = new Date(strtotime($session['filter']['tl_iso_collectionmanager']['tstamp']));

			$where[] = "tstamp BETWEEN ? AND ?";
			$value[] = $objDate->monthBegin;
			$value[] = $objDate->monthEnd;

			$this->Template->deadlineClass = ' active';
		}
		
		//Type Options
		$typeoptions = '';
		
		foreach($GLOBALS['ISO_COL_MANAGER'] as $cart => $data)
		{
			$typeoptions .= sprintf('<option value="%s"%s>%s</option>', $cart, (($cart == $session['type']['tl_iso_collectionmanager']) ? ' selected="selected"' : ''), (is_array($GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$cart]) ? $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$cart][0] : $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$cart]));
		}
		$this->Template->typeOptions = $typeoptions;
		$this->Template->type = specialchars($GLOBALS['TL_LANG']['MSC']['collectiontype']);

		// Filter options - Only filtering by date for the moment
		$objFilter = $this->Database->prepare("SELECT tstamp FROM ". $session['type']['tl_iso_collectionmanager'])
									->execute();
		
		while ($objFilter->next())
		{
			$objDate = new Date($objFilter->tstamp);
			$filters[$objDate->monthBegin] = sprintf('<option value="%s"%s>%s</option>', $objFilter->tstamp, (($objFilter->tstamp == $session['filter']['tl_iso_collectionmanager']['tstamp']) ? ' selected="selected"' : ''), date("F Y", $objDate->monthBegin));
		}
		

		$this->Template->datefilterOptions = (count($filters) ? implode($filters) : '');
		$this->Template->datefilter = specialchars($GLOBALS['TL_LANG']['MSC']['filter']);
		
		// Where
		if (count($where))
		{
			$query .= " WHERE " . implode(' AND ', $where);
		}

		// Order by
		$query .= " ORDER BY c.tstamp DESC";
						
		// Execute query
		$objCollection = $this->Database->prepare($query)->execute($value);

		if ($objCollection->numRows < 1)
		{
			return null;
		}

		return $objCollection;
	}


	/**
	 * Return a TextField widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getTextFieldWidget($strName, $arrData, $value=null)
	{
		$widget = new TextField();

		$widget->id = $strName;
		$widget->name = $strName;
		$widget->mandatory = $arrData['mandatory'];
		$widget->decodeEntities = true;
		$widget->value = $value;
		$widget->label = $arrData['label'][0];
		
		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($GLOBALS['TL_LANG'][$this->Input->get('tbl')][$strName][1]))
		{
			$widget->help = $arrData['label'][1];
		}

		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}
	
	
	/**
	 * Return a Textarea widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getTextareaWidget($strName, $arrData, $value=null)
	{
		$widget = new TextArea();

		$widget->id = $strName;
		$widget->name = $strName;
		$widget->mandatory = $arrData['mandatory'];
		$widget->decodeEntities = true;
		$widget->value = $value;
		$widget->rows = $arrData['rows'];
		$widget->cols = $arrData['cols'];
		$widget->label = $GLOBALS['TL_LANG'][$this->Input->get('tbl')][$strName][0];
				
		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($GLOBALS['TL_LANG'][$this->Input->get('tbl')][$strName][1]))
		{
			$widget->help = $arrData['label'][1];
		}

		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}


	/**
	 * Return the CollectionWizard widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getCollectionWizard($strName, $arrData, $value=null)
	{
		$widget = new ProductCollectionWizard($arrData);

		$widget->id = $strName;
		$widget->name = $strName;		
		$widget->value = $value;
		$widget->type = 'collectionWizard';
		$widget->searchFields = $arrData['searchFields'];
		$widget->listFields = $arrData['listFields'];
		$widget->foreignTable = $arrData['foreignTable'];
		$widget->collectionType = $GLOBALS['ISO_COL_MANAGER'][$this->Input->get('tbl')]['class'];
		$widget->collectionId = $this->Input->get('id');
		$widget->label = $arrData['label'][0];

		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($arrData['label'][1]))
		{
			$widget->help = $arrData['label'][1];
		}

		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}
	
	
	/**
	 * Return the TableLookupWizard widget as object
	 * @param mixed
	 * @return object
	 */
	protected function getTableLookupWizard($strName, $arrData, $value=null)
	{
		$widget = new TableLookupWizard($arrData);

		$widget->id = $strName;
		$widget->name = $strName;		
		$widget->value = $value;
		$widget->type = 'tableLookup';
		$widget->fieldType = ($arrData['single']) ? 'radio' : 'checkbox';
		$widget->searchFields = $arrData['searchFields'];
		$widget->listFields = $arrData['listFields'];
		$widget->foreignTable = $arrData['foreignTable'];
		$widget->label = $arrData['label'][0];
		
		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($arrData['label'][1]))
		{
			$widget->help = $arrData['label'][1];
		}

		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}


	/**
	 * Return the status widget as object
	 * @param mixed
	 * @param integer
	 * @return object
	 */
	protected function getSelectWidget($name, $arrOptions=array(), $value=null)
	{
		$widget = new SelectMenu();

		$widget->id = $name;
		$widget->name = $name;
		$widget->mandatory = true;
		$widget->value = $value;

		$widget->label = $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$name][0];

		if ($GLOBALS['TL_CONFIG']['showHelp'] && strlen($GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$name][1]))
		{
			$widget->help = $GLOBALS['TL_LANG']['tl_iso_collectionmanager'][$name][1];
		}

		$widget->options = $arrOptions;

		// Valiate input
		if ($this->Input->post('FORM_SUBMIT') == 'tl_iso_collectionmanager')
		{
			$widget->validate();

			if ($widget->hasErrors())
			{
				$this->blnSave = false;
			}
		}

		return $widget;
	}
	
	/**
	 * Return an array of IDs for the collection's products
	 * @param object
	 * @return array
	 */
	protected function getProductArray($objCollection)
	{
		$arrProducts = $objCollection->getProducts();
		$arrIDs = array();
		foreach($arrProducts as $objProduct)
		{
			$arrIDs[] = $objProduct->id;
		}
		
		return $arrIDs;
	}
	
	/**
	 * Return an array of page options for the jumpTo select list
	 * @param array
	 * @return array
	 */
	protected function getJumpToOptions($arrData)
	{
		$arrUniqueIds = array();
		$objPageIDs = $this->Database->prepare("SELECT iso_reader_jumpTo FROM tl_module WHERE iso_reader_jumpTo !='0' AND iso_reader_jumpTo!=''")->execute();
		while($objPageIDs->next())
		{
			$arrUniqueIds[$objPageIDs->iso_reader_jumpTo] = $objPageIDs->iso_reader_jumpTo;
		}
		foreach($arrUniqueIds as $id)
		{
			$objPage = $this->Database->prepare("SELECT id, title, alias FROM tl_page WHERE id=?")->execute($id);
			if($objPage->id)
			{
				$arrOptions[] = array('value'=>$objPage->id, 'label'=>$objPage->title);
			}
		}
		
		return $arrOptions;
	}

	
}

