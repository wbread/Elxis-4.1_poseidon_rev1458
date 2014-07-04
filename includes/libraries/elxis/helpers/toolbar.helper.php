<?php 
/**
* @version		$Id: toolbar.helper.php 606 2011-09-19 16:45:42Z datahell $
* @package		Elxis
* @subpackage	Helpers / Toolbar
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisToolbarHelper {

	private $buttons = array();
	private $options = array(
		'reverse' => true, //reverse buttons order due to float right
		'maxchars' => 0 //maximum number of characters to show in button's text, less than 4 to disable
	);


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/********************/
	/* RESET EVERYTHING */
	/********************/
	public function resetAll() {
		$this->buttons = array();
		$this->options['reverse'] = true;
		$this->options['maxchars'] = 0;
	}


	/**************/
	/* SET OPTION */
	/**************/
	public function setOption($option, $value) {
		switch($option) {
			case 'reverse': $this->options['reverse'] = (bool)$value; break;
			case 'maxchars':
				$m = (int)$value;
				if ($m > 3) { $this->options['maxchars'] = $m; }
			break;
			default: break;
		}
	}


	/**********************************/
	/* GENERIC METHOD TO ADD A BUTTON */
	/**********************************/
	public function add($title, $image='', $icononly=false, $link='', $onclick='', $onmouseover='', $onmouseout='', $css='elx_toolbar') {
		$icon = '';
		if ($image != '') {
			if (strpos($image, 'http') === 0) { //custom icon
				$icon = $image;
			} else {
				$icon = eFactory::getElxis()->icon($image, 16, '');
			}
		}

		$btn = new stdClass;
		$btn->title = $title;
		$btn->icon = $icon;
		$btn->icononly = (bool)$icononly;
		$btn->link = $link;
		$btn->onclick = trim($onclick);
		$btn->onmouseover = trim($onmouseover);
		$btn->onmouseout = trim($onmouseout);
		$btn->css = ($css == '') ? 'elx_toolbar' : $css;
		$this->buttons[] = $btn;
	}


	/*********************************/
	/* GENERATE TOOLBAR BUTTONS HTML */
	/*********************************/
	public function getHTML() {
		if (!$this->buttons) { return ''; }
		if ($this->options['reverse'] === true) {
			$this->buttons = array_reverse($this->buttons);
		}

		$html = '';
		foreach ($this->buttons as $button) {
			if ($button->link == '') {
				$html .= '<a href="javascript:void(null);" class="'.$button->css.'" title="'.$button->title.'"';
			} else {
				$html .= '<a href="'.$button->link.'" class="'.$button->css.'" title="'.$button->title.'"';
			}
			if ($button->onclick != '') { $html .= ' onclick="'.$button->onclick.'"'; }
			if ($button->onmouseover != '') { $html .= ' onmouseover="'.$button->onmouseover.'"'; }
			if ($button->onmouseout != '') { $html .= ' onmouseout="'.$button->onmouseout.'"'; }
			$html .= ">\n";
			$html .= "\t<span>";

			$link_text = $button->title;
			if ($this->options['maxchars'] > 3) {
				$l = eUTF::strlen($button->title);
				if ($l > $this->options['maxchars']) {
					$n = $this->options['maxchars'] - 2;
					$link_text = eUTF::substr($button->title, 0, $n).'..';
				}
			}

			if ($button->icon != '') {
				$html .= '<img src="'.$button->icon.'" alt="'.$button->title.'" border="0" align="bottom" />';
				$html .= ($button->icononly === true) ? '' : ' '.$link_text;
				$html .= '</span>'."\n";
			} else {
				$html .= $link_text."</span>\n";
			}
			$html .= "</a>\n";
		}

		$this->resetAll();
		return $html;
	}

}

?>