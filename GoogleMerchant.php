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
 * @copyright  Isotope eCommerce Workgroup 2009-2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
 

/**
 * Class GoogleMerchant - provides basic methods for working with Google Shopping API
 *
 * @copyright  Winans Creative 2012
 * @author     Blair Winans <blair@winanscreative.com>
 * @author     Russell Winans <russ@winanscreative.com>
 * @package    Controller
 */
 
 
class GoogleMerchant extends Controller
{

 	/**
	 * Import required classes
	 */
	protected $blnDisabled = false;
 
 	
 	/**
	 * Load database object 
	 */
	public function __construct()
	{
		parent::__construct();
		$this->import('Database');
		$this->import('Isotope');
		$this->import('Files');
	}

	/**
	 * Checks header to see if tools or quick menu functions were triggered
	 * Also checks whether or not more authorization steps are needed
	 * to be automated.  Also checks whether or not there is a javascript
	 * redirect which is triggered from the Ajaxrequest when publishing/unpublishing
	 * an item via the main products menu
	 * @param DataContainer
	 */
	public function onLoad($dc)
	{	
		if ($this->Input->get('refresh') == 'true')
 		{	
 			$this->checkAuthorization();
 			$this->redirect(str_replace('&refresh=true', '', $this->Environment->request));	
		}
		else
		{
			unset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);		
		}
		
		if ($this->Input->get('redirect') == 'isGoogle')
		{
			echo str_replace('&redirect=isGoogle', '&refresh=true&key=pub', $this->Environment->request);
			exit;
		}
	
		switch($this->Input->get('key'))
 		{
 			case 'clearcache':
 				$this->clearCache();
				$this->redirect(str_replace('&key=clearcache', '', $this->Environment->request));
 				break;
 				
 			case 'createcache':
 	 			$this->clearCache();		
				$this->constructCacheButton();
				$this->redirect(str_replace('&key=createcache', '', $this->Environment->request));
				break;
						
  			case 'removeproducts':
  				$this->clearCache();
 				$this->constructCacheButton(); 			
 				$this->resetFeeds();
 				$this->clearCache();
				$this->redirect(str_replace('&key=removeproducts', '', $this->Environment->request));	
 				break;
 				
 			case 'clearauth':
 				$this->clearAuth();
				$this->redirect(str_replace('&key=clearauth', '', $this->Environment->request));	
				break; 
								
 			case 'pub':
 				$objProduct = $this->Database->prepare("SELECT * FROM tl_iso_products WHERE id=?")
						->execute($this->Input->get('productid'));
						
				$this->saveParentVariants($objProduct);
 				$this->onProductNew($objProduct);		
 				$redirectUrl = str_replace('&key=pub', '', $this->Environment->request);
 				$redirectUrl = str_replace('&productid=' . $objProduct->id, '', $redirectUrl);
 				$this->redirect($redirectUrl);
 				break;					
		}
	}
	
	/**
	 * Clear all current authorizations 
	 */
	public function clearAuth()
	{
		$arrSet = array('google_token_tstamp' => 100);
		
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config");
		
		while ($objConfig->next())
		{
			$this->Database->prepare("UPDATE tl_iso_config %s WHERE id=" . $objConfig->id)->set($arrSet)->execute();
		}
	}
	
	/**
	 * recursively deletes all of the cache files 
	 */
	 public function clearCache()
	 {
		$objFolder = new Folder('/isotope/cache/');
		$objFolder->clear();
	 }
	 
	 
	/**
	 * Deletes all of the products in the current cache from the google products feed and
	 * clears all of the cache items.
	 * This is recursive to not trigger data size api error when performing requests to Google.
	 */
	public function resetFeeds()
	{
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");
		
		while ($objConfig->next())
		{
			$arrRequest = array();
			$counter = 0;
			$intErrors = 0;
				
			$strDir = '/isotope/cache/' . $objConfig->id . '/';
	
			$xml = '';
			$xml .= '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>' . "\n";
			$xml .= "<feed xmlns='http://www.w3.org/2005/Atom' xmlns:batch='http://schemas.google.com/gdata/batch'>" . "\n";
			
			if (is_dir(TL_ROOT . $strDir)) 
			{
	 		   foreach (scan(TL_ROOT . $strDir) as $v)
	 		   {
					// set upper limit to 500 requests per batch
					if ($counter < 500) 
					{
						$objFile = new File($strDir . $v);
						$xml .= "<entry>" . "\n";
						$xml .= "<batch:operation type='DELETE'/>" . "\n";
						$xml .= "<id>https://content.googleapis.com/content/v1/".$objConfig->google_merchant_accountID."/items/products/schema/online:en:US:". $v->basename ."</id>" . "\n";
						$xml .= "</entry>" . "\n";
						
						$v->delete();
					}
					$counter++;
	 		   }
			}
			
			$xml .= "</feed>" . "\n";
		
	
			$arrRequest[] = array
			(
				'google_merchantID' =>	$objConfig->google_merchant_accountID, 
				'xml'				=>	$xml, 
				'method'			=>	'POST', 
				'url'				=>	'https://content.googleapis.com/content/v1/ACC_ID/items/products/schema/batch?alt=atom',
				'config_id'			=>	$objConfig->id
			);
		
			if ($objConfig->google_key && $objConfig->google_secret && $objConfig->google_merchant_accountID)
			{	
				$arrResponse = $this->sendGoogleRequest($arrRequest);
			}		
						
			if (strstr($arrResponse[0]['response'],'auth'))
			{
				$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_error_wrongaccountid'],$arrResponse[$i]['response'],'Suppressed. Error in Store: ' . $objConfig->name));
				$intErrors++;
			}
			
			if (counter > 0)
			{
				$this->resetFeeds();
			}
		}
			
		//Check for errors
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");    													
		while ($objConfig->next())
		{
			if ($intErrors == 0)
			{
				$_SESSION['TL_CONFIRM'][$objConfig->name] = sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_success'], 'for store ' . $objConfig->name);
			}
		}
		
	}
	
	/**
	 * Constructs all of the individual .xml files
	 * then calls function constructCache()
	 * to make feed .xml file
	 */
	public function constructCacheButton()
	{
		$objProduct = $this->Database->execute("SELECT * FROM tl_iso_products WHERE published=1");
		
		while ($objProduct->next())
		{
			$xml = $this->generateRequests($objProduct);
			$this->cacheXML($xml, $objProduct->sku);
		}
		$this->constructCache();
	}
	
	
	/**
	 * Writes the .xml files to disk
	 * @param array
	 * @param string
	 */	
	public function cacheXML($arrData, $sku)
	{
		foreach ($arrData as $data)
		{
			$objXML = new File('/isotope/cache/' . $data['config_id'] . '/' . $sku . '.xml');

			if ($data['xml'] != ''){
				$objXML->write($data['xml']);
			}
			else
			{
				$objXML->delete();
			}
		}
	}

	/**
	 * Opens and scans the cache for a given config, returning an array of all XML contents 
	 * @param int 
	 * @return array
	 */
	public function scanCache($intConfig)
	{
		$strDir = '/isotope/cache/' . $intConfig . '/';
		$arrFiles = array();
		
		if (is_dir(TL_ROOT . $strDir)) 
		{
 		   foreach (scan(TL_ROOT . $strDir) as $v)
 		   {
				$objFile = new File($strDir . $v);
 		    	array_push($arrFiles, $objFile->getContent());
 		   }
		}
		return $arrFiles;
	}
	
	
	/**
	 * Re-writes all of the individual .xml files to a feed .xml file
	 */
	public function constructCache()
	{
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");
		
		while ($objConfig->next())
		{
			$arrFiles = $this->scanCache($objConfig->id);
			$xml = '';
			$xml  .= '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>' . "\n";
			$xml  .= '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">';
			$xml  .= '<title>'.$objConfig->google_feedtitle.'</title>' . "\n";
			$xml  .= "  <link rel='alternate' type='text/html' href='" . $objConfig->google_feedbase . "'/>" . "\n";
			$xml  .= '<updated>' . date(DATE_RFC822) . '</updated>' . "\n";
			$xml  .= '<author>' . "\n";
			$xml  .= '<name>'.$objConfig->feedAuthor.'</name>' . "\n";
			$xml  .= '</author>' . "\n";
			foreach ($arrFiles as $tempxml) 
			{
				$tempxml = str_replace('scp:','g:',$tempxml);
				$tempxml = str_replace('<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>' . "\n",'',$tempxml);
				$xml .= $tempxml;
			}		
			$xml .= '</feed>';
			
			$objXML = new File('googleapifeed-'. $objConfig->id . '.xml');
			$objXML->truncate();
			$objXML->write($xml);
			$objXML->close();
		}
	}
	 
	
	/**
	 * Remove feeds hook to preserve files
	 */
	public function preserveFeeds()
	{
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");
		
		while($objConfig->next())
		{
			$arrFeeds[] = 'googleapifeed-'. $objConfig->id . '.xml';
		}
		
		return $arrFeeds;
	}
	
	/**
	 * Saves all of the variants of a parent
	 * Triggered when parent is saved
	 * @param mixed - could be DataContainer->activerecord or Database_result
	 */
	public function saveParentVariants($objRecord)
	{	
		if($objRecord instanceof DataContainer)
		{
			$objRecord = $objRecord->activeRecord;
		}
		
		$objProductData = $this->Database->prepare("SELECT * FROM tl_iso_products WHERE pid=?")
		  							 ->execute($objRecord->id);
		
		while ($objProductData->next())
		{
			$this->onProductNew($objProductData);
		}
	} 
	
	/**
	 * Deletes all of the variants of a parent
	 * Triggered when parent is deleted
	 * @param mixed - could be DataContainer->activerecord or Database_result
	 */
	public function deleteParentVariants($dc)
	{
		if($objRecord instanceof DataContainer)
		{
			$objRecord = $objRecord->activeRecord;
		}
	
		$objProductData = $this->Database->prepare("SELECT * FROM tl_iso_products WHERE pid=?")
									 ->execute($objRecord->id);
			
		while ($objProductData->next())
		{
			$this->onProductDelete($objProductData);  
		}
	}
	
	/**
	 * Saves a product to Google Merchant
	 * Triggered when item is saved
	 * @param mixed - could be DataContainer->activerecord or Database_result
	 */
	public function onProductNew($objRecord)
	{	
		if($objRecord instanceof DataContainer)
		{
			$objRecord = $objRecord->activeRecord;
		}
	
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");
			
		$arrRequest = $this->generateRequests($objRecord);
				
		if ($this->blnDisabled)
		{
			$arrResponse = $this->sendGoogleRequest($arrRequest);
		}
				
		$intErrors = 0;
				
		for ($i=0; $i<count($arrResponse); $i++){
		
			$nameStore = $this->Database->execute("SELECT * FROM tl_iso_config WHERE id=" . $arrRequest[$i]['config_id'])->name;

	 		switch($arrResponse[$i]['method'])
	 		{
 				case 'POST':
 				case 'post':
					if (!strstr($arrResponse[$i]['response'],'error'))
					{
						$this->cacheXML($arrRequest, $objRecord->sku);
					}
					else
					{	
						if (strstr($arrResponse[$i]['response'],'auth'))
						{
							$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_error_wrongaccountid'], $arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
							$intErrors++;
						}
						elseif( (strstr($arrResponse[$i]['response'],'validation')) || (strstr($arrResponse[$i]['response'],'missing')) )
						{
							$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_error_badxml'],$arrResponse[$i]['response'],  'Error in Store: ' . $nameStore));
							$intErrors++;
						}
						else
						{
							$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_other'],$arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
							$intErrors++;
						}
					}
					break;
					
				case 'DELETE':
				case 'delete':
						if (strlen($arrResponse[$i]['response'])>0)
						{
							$errs++;
							if (strstr($arrResponse[$i]['response'],'auth'))
							{
								$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_error_wrongaccountid'],$arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
								$intErrors++;
							}
							elseif (strstr($arrResponse[$i]['response'],'notfound')) 
							{

							}
							else
							{
								$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_other'],$arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
								$intErrors++;
							}
						}
						$filepath = TL_ROOT . '/isotope/cache/' . $this->Database->execute("SELECT * FROM tl_iso_config WHERE id=" .  $arrRequest[$i]['config_id'])->id . '/' . $objProduct->sku . '.xml'; 
						@unlink($filepath);
					break;
					
				default:
					break;
			}
		}  
		
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");    													
		while ($objConfig->next())
		{
			if ($intErrors == 0)
			{
				$this->addConfirmationMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_success'], 'for store ' . $objConfig->name));
			}
		}
		
	}
	
	/**
	 * @param DataContainer
	 * deletes an item from the Google Merchant feed
	 * Triggered when item is deleted
	 */	
	public function onProductDelete($dc)
	{					
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");  
		
		$intErrors = 0; 					
		    									
		while ($objConfig->next())
		{		
			$objConfig->google_feedname = strlen($objConfig->google_feedname) ? $objConfig->google_feedname : 'products' . $objConfig->id;
			
			// Get root pages that belong to this store config.
			$objRoot = $this->Database->prepare("SELECT * FROM tl_page p WHERE type='root' AND iso_config=?")->execute($objConfig->id);
			
			if($objRoot->numRows)
			{
				//Get an array of all pages under the root so that we can compare to product categories
				$objPages = $this->Database->execute("SELECT id FROM tl_page");
				
				while($objPages->next())
				{
					$objDetails = $this->getPageDetails($objPages->id);
					if($objDetails->rootId == $objRoot->id)
					{
						$arrPages[] = $objPages->id;
					}
				}
			}
			
			$time = time();
				
			$objProduct = $this->Database->execute("SELECT p.*, (SELECT class FROM tl_iso_producttypes t WHERE p.type=t.id) AS product_class FROM tl_iso_products p LEFT JOIN tl_iso_product_categories c ON c.pid=p.id WHERE ".(count($arrPages)>0 ? "c.page_id IN (" . implode(',',$arrPages) . ") AND " : ''). "p.pid=0 AND (p.start='' OR p.start<$time) AND (p.stop='' OR p.stop>$time) AND p.published=1 AND p.id=" . $dc->activeRecord->id . " ORDER BY p.tstamp DESC");			
		    			    
	    	$arrRequest[] = array('google_merchantID'=>$objConfig->google_merchant_accountID, 
    			'xml'=>'', 
    			'method'=>'DELETE',
    			'url'=>'https://content.googleapis.com/content/v1/ACC_ID/items/products/schema/online:en:US:' . $objProduct->sku,
    			'config_id'=>$objConfig->id
	    	);
		}
		
		if ($objConfig->google_key && $objConfig->google_secret && $objConfig->google_merchant_accountID)
		{
			$arrResponse = $this->sendGoogleRequest($arrRequest);
		}
		
		for ($i=0; $i<count($arrResponse);$i++)
		{
			$nameStore = $this->Database->execute("SELECT * FROM tl_iso_config WHERE id=" . $arrRequest[$i]['config_id'])->name;
	
			if ((strlen($arrResponse[$i]['response'])>0)  && (!strstr($arrResponse[$i]['response'],'notfound')))
			{
				if (strstr($arrResponse[$i]['response'],'auth'))
				{
					$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_error_wrongaccountid'],$arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
					$intErrors++;
				}
				elseif(strstr($arrResponse[$i]['response'],'notfound')) 
				{
				
				}
				else
				{
					$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_other'],$arrResponse[$i]['response'], 'Error in Store: ' . $nameStore));
					$intErrors++;	
				}
			}
			
			$this->cacheXML($arrRequest, $objProduct->sku);
		}  

		if ($intErrors == 0)
		{
			$this->addConfirmationMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_req_success']));
		}
		
	}
	
	/**
	 * Generates a request array for a given product record
	 * Triggered when item is saved
	 * @param var - Could be Database_result or DataContainer->activeRecord
	 */
	public function generateRequests($objRecord)
	{
		$arrRequest = array();
		
		$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant=1");

		if ($objConfig->numRows < 1)
		{
			return;
		}
		
		while ($objConfig->next())
		{
			if ($objConfig->google_key && $objConfig->google_secret && $objConfig->google_merchant_accountID)
			{
				$this->blnDisabled = true;
			}
		
			$strFeedName = strlen($objConfig->google_feedname) ? $objConfig->google_feedname : 'products' . $objConfig->id;
	
			$time = time();
			$strLink = strlen($objConfig->feedBase) ? $objConfig->feedBase : $this->Environment->base;

			// Get root pages that belong to this store config.
			$arrPages = array();
			$objRoot = $this->Database->prepare("SELECT * FROM tl_page p WHERE type='root' AND iso_config=?")->execute($objConfig->id);
			
			if($objRoot->numRows)
			{
				//Get an array of all pages under the root so that we can compare to product categories
				$objPages = $this->Database->execute("SELECT id FROM tl_page");
				while($objPages->next())
				{
					$objDetails = $this->getPageDetails($objPages->id);
					if($objDetails->rootId == $objRoot->id)
					{
						$arrPages[] = $objPages->id;
					}
				}
			}

			// Get default URL
			$intJumpTo = $objConfig->google_reader;
	
			if(!strlen($intJumpTo))
			{
				//Get the first reader page we can find
				$objModules = $this->Database->prepare("SELECT iso_reader_jumpTo FROM tl_module WHERE ".(count($arrPages)>0 ? "iso_reader_jumpTo IN (" . implode(',',$arrPages) . ") AND " : ''). "iso_reader_jumpTo !=''")->limit(1)->execute();
	
				if($objModules->numRows)
				{
					$intJumpTo = $objModules->iso_reader_jumpTo;
				}
			}
			
			// get product data
			$objProductData = $this->Database->execute("SELECT p.*, (SELECT class FROM tl_iso_producttypes t WHERE p.type=t.id) AS product_class FROM tl_iso_products p LEFT JOIN tl_iso_product_categories c ON c.pid=p.id WHERE ".(count($arrPages)>0 ? "c.page_id IN (" . implode(',',$arrPages) . ") AND " : ''). "p.pid=0 AND (p.start='' OR p.start<$time) AND (p.stop='' OR p.stop>$time) AND p.published=1 AND p.id=" . $objRecord->id . " ORDER BY p.tstamp DESC");
			
			$strClass = $GLOBALS['ISO_PRODUCT'][$objProductData->product_class]['class'];
			
			// check if product and its class exists within this store config
			if (!$objProductData->numRows || $strClass == '' || !$this->classFileExists($strClass))
			{
				// if not then delete it from Google
				$arrRequest[] = array
				(
					'google_merchantID'=>$objConfig->google_merchant_accountID, 
					'xml'=>'',
					'method'=>'DELETE',
					'url'=>'https://content.googleapis.com/content/v1/ACC_ID/items/products/schema/online:en:US:' . $objRecord->sku,
					'config_id'=>$objConfig->id
				);
				$this->cacheXML($arrRequest, $objRecord->sku);
				continue;
			}
				
			$objProduct = new $strClass($objProductData->row());
		
			// if product is available
			if($objProduct->available)
			{
				// check if submission is a child
				if ($objProductReq->pid != 0)
				{
					// if it is a child check whether the child is available
					$vars = $objProduct->variant_ids;
					$blnVariantAvailable = false;
					foreach($vars as $variant)
					{
						if ($variant == $objProductReq->id)
						{
							$blnVariantAvailable = true;
						}
					}
					$objProductVariant = $this->Database->prepare("SELECT * FROM tl_iso_products WHERE id=?")
														->limit(1)
				   										->execute($objRecord->id);
				}
				  
				// if it isnt available delete 
				if ($blnVariantAvailable===false)
				{
					$arrRequest[] = array
					(
						'google_merchantID'=>$objConfig->google_merchant_accountID, 
						'xml'=>'',
						'method'=>'DELETE',
						'url'=>'https://content.googleapis.com/content/v1/ACC_ID/items/products/schema/online:en:US:' .$objRecord->sku,
						'config_id'=>$objConfig->id
					);
					$this->cacheXML($arrRequest, $objRecord->sku);
					continue;		
				}
								
				$strFile = $objProduct->sku;
				
				$objTempFeed = new Feed('temp');
				$objItem = new FeedItem($strFile);    									
		
				$strUrlKey = $objProduct->alias ? $objProduct->alias  : ($objProduct->pid ? $objProduct->pid : $objProduct->id);
				$objItem->title = $objProduct->name;
				$objItem->link = $strLink . '/' . $this->generateFrontendUrl($this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($intJumpTo)->fetchAssoc(), '/product/' . $strUrlKey);
				$objItem->published = time();

				// Prepare the description
				$strDescription = $objProduct->description;
				$strDescription = $this->replaceInsertTags($strDescription);
				$objItem->description = $this->convertRelativeUrls($strDescription, $strLink);
		
				//Google specific settings
				$objItem->condition = $objProduct->gid_condition;
				$objItem->availability = $objProduct->gid_availability;
				$objItem->brand = $objProduct->gid_brand;
				$objItem->gtin = $objProduct->gid_gtin;
				$objItem->mpn = $objProduct->gid_mpn;
				$objItem->google_product_category = $this->Database->prepare("SELECT * FROM tl_google_taxonomy WHERE id=?")
																   ->execute($objProduct->gid_google_product_category)
																   ->fullname;

				//Custom product category taxomony
				$objItem->product_type = deserialize($objProduct->gid_product_type);
		
				//Google Variants
				if ($blnVariantAvailable)
				{
				    if ($objProductVariant->price > 0)
				    {
						$objItem->price = $objProductVariant->price;
				    }
				    else
				    {
				    	$objItem->price = $objProduct->price;
				    }
			    
					$objItem->sku = $objProductVariant->sku;
					$objItem->id = $objProduct->sku;	  
					$objItem->item_group_id = $objProduct->sku;
					$objItem->color = $objProductVariant->color;
					$objItem->material = $objProductVariant->material;
					$objItem->pattern = $objProductVariant->pattern;
					$objItem->size = $objProductVariant->size;
					$objItem->gender = $objProductVariant->gender;
					$objItem->age_group = $objProductVariant->age_group;
					
					$strUrlKey = $objProductVariant->alias ? $objProductVariant->alias  : ($objProductVariant->pid ? $objProductVariant->pid : $objProductVariant->id);
					$objItem->link = $strLink . '/' . $this->generateFrontendUrl($this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($intJumpTo)->fetchAssoc(), '/product/' . $strUrlKey);
					
					//Prepare the images - @todo - Break this out into a single method so we don't need to repeat
					$arrImages = $this->getProductImages($objProductVariant);
					if(is_array($arrImages) && count($arrImages)>0)
					{
						$objItem->image_link = $this->Environment->base . $arrImages[0]['medium'];
						$objItem->addEnclosure($arrImages[0]['medium']);
						unset($arrImages[0]);
						if(count($arrImages)>0)
						{
							//Additional images
							$arrAdditional = array();
							foreach($arrImages as $additional)
							{
								$arrAdditional[] = $this->Environment->base . $additional['medium'];
							}
							$objItem->additional_image_link = $arrAdditional;
						}
					}
				 }	
			   	 else
			   	 {
		   			$strUrlKey = $objProduct->alias ? $objProduct->alias  : ($objProduct->pid ? $objProduct->pid : $objProduct->id);
		   			$objItem->link = $strLink . '/' . $this->generateFrontendUrl($this->Database->prepare("SELECT * FROM tl_page WHERE id=?")->execute($intJumpTo)->fetchAssoc(), '/product/' . $strUrlKey);
		   			
					$objItem->sku = strlen($objProduct->sku) ? $objProduct->sku : $objProduct->alias;
					$objItem->price = $objProduct->price;
					
					//Prepare the images - @todo - Break this out into a single method so we don't need to repeat
					$arrImages = $this->getProductImages($objProduct);
					if(is_array($arrImages) && count($arrImages)>0)
					{
						$objItem->image_link = $this->Environment->base . $arrImages[0]['medium'];
						$objItem->addEnclosure($arrImages[0]['medium']);
						unset($arrImages[0]);
						if(count($arrImages)>0)
						{
							//Additional images
							$arrAdditional = array();
							foreach($arrImages as $additional)
							{
								$arrAdditional[] = $this->Environment->base . $additional['medium'];
							}
							$objItem->additional_image_link = $arrAdditional;
						}
					}
				}
				$xml = '';
				$xml .= '<?xml version="1.0" encoding="' . $GLOBALS['TL_CONFIG']['characterSet'] . '"?>' . "\n";
				$xml .= "<entry xmlns='http://www.w3.org/2005/Atom' xmlns:sc='http://schemas.google.com/structuredcontent/2009' xmlns:scp='http://schemas.google.com/structuredcontent/2009/products' xmlns:app='http://www.w3.org/2007/app' xmlns:g='http://base.google.com/ns/1.0'>" . "\n";
				$xml .= '    <title>' . specialchars($objItem->title) . '</title>' . "\n";
				$xml .= "  <content type='text'>" . specialchars($objItem->description) . "</content>" . "\n";			

				$xml .= "  <link rel='alternate' type='text/html' href='" . specialchars($objItem->link) . "'/>" . "\n";		
				$xml .= '  <sc:id>' . $objItem->sku . '</sc:id>' . "\n";
				$xml .= '  <sc:image_link>' . $objItem->image_link . '</sc:image_link>' . "\n";
				
				if(count($objItem->additional_image_link))
				{
					foreach($objItem->additional_image_link as $image)
					{
						$xml .= '  <sc:additional_image_link>' . $image . '</sc:additional_image_link>' . "\n";
					}
				}

				$arrGoogleFields = array(
					'price',
					'availability',
					'condition',
					'product_type',
					'google_product_category',
					'brand',
					'gtin',
					'mpn',
					'sale_price',
					'sale_price_effective_date',
					'item_group_id',
					'color',
					'material',
					'pattern',
					'size',
					'gender',
					'age_group',
				);

				foreach($arrGoogleFields as $strKey)
				{
					if($objItem->__isset($strKey) && strlen($objItem->$strKey) )
					{
						if(is_array($objItem->$strKey) && count($objItem->$strKey))
						{
							foreach($objItem->$strKey as $value)
							{
								$xml .= '      <scp:'.$strKey.'>' . specialchars($value) . '</scp:'.$strKey.'>' . "\n";
							}
						}
						else
						{
							if ($strKey == 'price')
							{
								$xml .= '      <scp:' . $strKey . " unit='USD'" . '>' . specialchars($objItem->$strKey) . '</scp:'.$strKey.'>' . "\n";
							}
							else
							{
								$xml .= '      <scp:'.$strKey.'>' . specialchars($objItem->$strKey) . '</scp:'.$strKey.'>' . "\n";
							}
						}
					}
				}
				
				$xml .= "</entry>" . "\n";
				
				$arrRequest[] = array
				(
					'google_merchantID'=>$objConfig->google_merchant_accountID, 
					'xml'=>$xml,
					'method'=>'POST',
					'url'=>'https://content.googleapis.com/content/v1/ACC_ID/items/products/schema?alt=atom',
					'config_id'=>$objConfig->id
				);
				
			}
		}
		
		return $arrRequest;
	}


	/**
	 * Sends an API request to Google
	 * @param array
	 * @return array
	 */
 	public function sendGoogleRequest($arrData)
 	{ 	
		// check credentials
		$this->checkAuthorization($arrData);

		$arrResponse = array();
		
		// loop over all requests
		foreach ($arrData as $data)
		{
			// build request from data
			$objConfig = $this->Database->execute("SELECT * FROM tl_iso_config WHERE google_merchant_accountID=" . $data['google_merchantID']);
			
			$OA = unserialize($objConfig->google_oauth);
			$arrAccessToken = deserialize($objConfig->google_token);
			$strRequest = str_replace('ACC_ID',$data['google_merchantID'],$data['url']);
			$strMethod = $data['method'];		
		
	 		switch($strMethod)
	 		{
	 			case 'POST':
	 			case 'post':
	 				$arrResponse[] = array('response'=>$OA->post($strRequest, $data['xml'], $arrAccessToken), 'method' =>$strMethod);
	 				break;
	 			
	 			case 'GET':
	 			case 'get':
	 				$arrResponse[] = array('response'=>$OA->get($strRequest, NULL, $arrAccessToken), 'method' =>$strMethod);

	 				break;
	 				
	  			case 'DELETE':
	 			case 'delete':
	 			default:
	 				$arrResponse[] = array('response'=>$OA->delete($strRequest, NULL, $arrAccessToken), 'method' =>$strMethod);
	 				break;						
			}
 		} 	
  		return $arrResponse;  
 	}
 	

  	/**
	 * Checks the authorization and requests authorization if needed
	 * @param array
	 */
	public function checkAuthorization($arrData=array())
	{	
		$arrStores = array();
		
		// creates a session data table since authorization may redirect browser
		if (!$_SESSION['GOOGLEMERCHANT'])
		{
			$_SESSION['GOOGLEMERCHANT'] = $arrData;
		}
		else 
		{
			$arrData = $_SESSION['GOOGLEMERCHANT'];
		}	
	
		// Get the stores which are attempting to post/del data to google
		foreach ($arrData as $data)
		{
			array_push($arrStores, $this->Database->executeUncached("SELECT * FROM tl_iso_config WHERE google_merchant_accountID=" . $data['google_merchantID'])->id);
		}
	
		$arrStores = array_unique($arrStores);
		$intErrors = 0;
		
		foreach ($arrStores as $store) 
		{
			// get the store config and pull its access token and credentials
			$objConfig = $this->Database->executeUncached("SELECT * FROM tl_iso_config WHERE id=" . $store);
			
			$arrAccessToken = deserialize($objConfig->google_token);
			$timeStamp = $objConfig->google_token_tstamp;			
			$secret = $objConfig->google_secret;
			$key = $objConfig->google_key;
			
			$OA = new GoogleOAuth($key, $secret, $strOAuthToken=NULL, $strOAuthSecret=NULL);
			
			if (empty($arrAccessToken) || (time()-$timeStamp) > 3600)
			{
	 			// if there isnt a token then authorize
	 			if (!$_SESSION['oauth_token'] || !$_SESSION['oauth_token_secret'])
				{				
					// Get temporary credentials.
					$arrRequestToken = $OA->getRequestToken($this->Environment->base . $this->Environment->request . '&refresh=true');
					
					if ($OA->http_code != 200)
					{
						$this->addErrorMessage(sprintf($GLOBALS['TL_LANG']['GOOG']['google_auth_error'],'Error occurred in Store' . $store . '\'s config.'));
						continue;
					}
					
					// Save temporary credentials to session.
					$_SESSION['oauth_token'] = $arrRequestToken['oauth_token'];
					$_SESSION['oauth_token_secret'] = $arrRequestToken['oauth_token_secret'];
					
					//redirect to Google page	
					$this->redirect($OA->getAuthorizeURL($arrRequestToken['oauth_token']));
				}
				// if it was denied
				elseif ($this->Input->get('denied') != '')
				{
					$this->addErrorMessage($GLOBALS['TL_LANG']['GOOG']['google_auth_denied']);
					$intErrors++;
				}
				// if there is a token
				elseif ($this->Input->get('oauth_token') != '' && $this->Input->get('oauth_token') == $_SESSION['oauth_token']) 
				{
					// Create Google object with app key/secret and token key/secret from default phase
					$OA = new GoogleOAuth($key, $secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
					
					// Request access tokens from Google
					$arrAccessToken = $OA->getAccessToken($_SESSION['oauth_token'], $_REQUEST['oauth_verifier']);
					
					$arrSet = array
					(
						'google_oauth' => serialize($OA),
						'google_token' => $arrAccessToken,
						'google_token_tstamp' => time()
					);
							
					//update database
					$this->Database->prepare("UPDATE tl_iso_config %s WHERE id=" . $store)->set($arrSet)->executeUncached();
					// Remove no longer needed request tokens
					unset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
				}
				else
				{
					// After one store gets full authorized reload the page
					$this->redirect($this->Environment->request . '&refresh=true');
				}
			}
		}
		
		if ( $intErrors==0 )
		{
			$this->addConfirmationMessage($GLOBALS['TL_LANG']['GOOG']['google_auth_success']);
		}
		
	}
 	

	/**
	 * Return an array of the product's original and/or watermarked images
	 * @param IsotopeProduct
	 * @return array
	 */
	protected function getProductImages($objProduct)
	{
		$arrReturn = array();
		$varValue = deserialize($this->Database->execute("SELECT images FROM tl_iso_products WHERE id={$objProduct->id}")->images);

		if(is_array($varValue) && count($varValue))
		{
			foreach( $varValue as $k => $file )
			{
				$strFile = 'isotope/' . substr($file['src'], 0, 1) . '/' . $file['src'];

				if (is_file(TL_ROOT . '/' . $strFile))
				{
					$objFile = new File($strFile);

					if ($objFile->isGdImage)
					{
						foreach( (array)$this->Isotope->Config->imageSizes as $size )
						{
							$strImage = $this->getImage($strFile, $size['width'], $size['height'], $size['mode']);

							if ($size['watermark'] != '')
							{
								$strImage = IsotopeFrontend::watermarkImage($strImage, $size['watermark'], $size['position']);
							}

							$arrSize = @getimagesize(TL_ROOT . '/' . $strImage);
							if (is_array($arrSize) && strlen($arrSize[3]))
							{
								$file[$size['name'] . '_size'] = $arrSize[3];
							}

							$file['alt'] = specialchars($file['alt']);
							$file['desc'] = specialchars($file['desc']);

							$file[$size['name']] = $strImage;
						}

						$arrReturn[] = $file;
					}
				}
			}
		}

		// No image available, add default image
		if (!count($arrReturn) && is_file(TL_ROOT . '/' . $this->Isotope->Config->missing_image_placeholder))
		{
			$strImage = $this->getImage($this->Isotope->Config->missing_image_placeholder, 250, 250, 'proportional');

			$arrSize = @getimagesize(TL_ROOT . '/' . $strImage);
			if (is_array($arrSize) && strlen($arrSize[3]))
			{
				$file['medium_size'] = $arrSize[3];
			}

			$file['medium'] = $strImage;

			$arrReturn[] = $file;
		}

		return $arrReturn;
	}

 	
 }
 
?>