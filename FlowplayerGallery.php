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


class FlowplayerGallery extends InlineGallery
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'iso_gallery_flowplayer';


	public function generateMainImage($strType='medium')
	{
		if (!count($this->arrFiles))
			return '<div class="iso_attribute" id="' . $this->name . '_' . $strType . 'size"> </div>';

		$arrFile = reset($this->arrFiles);

		$objTemplate = new FrontendTemplate($this->strTemplate);

		$objTemplate->setData($arrFile);
		$objTemplate->mode = 'main';
		$objTemplate->type = $strType;
		$objTemplate->name = $this->name;
		$objTemplate->product_id = $this->product_id;
		$objTemplate->href_reader = $this->href_reader;

		list($objTemplate->link, $objTemplate->rel) = explode('|', $arrFile['link']);


		// Pass image size to template
		$arrSize = null;
		foreach( (array)$this->Isotope->Config->imageSizes as $size )
		{
			if ($size['name'] == $strType)
			{
				$arrSize = $size;
				break;
			}
		}

		if (is_array($arrSize))
		{
			$objTemplate->width = $arrSize['width'];
			$objTemplate->height = $arrSize['height'];
		}

		$objPlayer = new Flowplayer(array
		(
			'play' => array
			(
				'opacity'	=> 0,
			),
			'clip' => array
			(
				'baseUrl'	=> $this->Environment->base,
				'scaling'	=> 'orig',
			),
			'playlist' => array($objTemplate->$strType),
			'onPlaylistReplace' => 'function(playlist) {
        $f(\'' . $this->name . '_player\').getPlugin(\'controls\').css({display: ([\'jpg\',\'jpeg\',\'png\',\'gif\'].contains(playlist[0].extension) ? \'none\' : \'block\')});
    }'
		));

		$objPlayer->enablePlugin('controls', array
		(
			'backgroundColor'	=> '#aedaff',
			'slowForward'		=> false,
			'fastForward'		=> false,
			'scrubber'			=> false,
			'display'			=> 'none',
		));

		$objPlayer->id = $this->name . '_player';

		$objTemplate->flowplayer = $objPlayer->generate();
		$objPlayer->injectJavascript();

		return '<div id="' . $this->name . '_' . $strType . 'size">'.$objTemplate->parse().'</div>';
	}
}

