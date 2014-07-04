<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / Accordion
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisAccordionHelper {

	private $collapsible = true;
	private $boxes = array();
	private $boxid = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->boxid = rand(0, 100);
	}


	/******************************************/
	/* MAKE ACCORDION FULL COLLAPSIBLE OR NOT */
	/******************************************/
	public function setCollapsible($collapsible=true) {
		$this->collapsible = (bool)$collapsible;
	}


	/************/
	/* ADD ITEM */
	/************/
	public function addItem($title, $contents='', $collapsed=true) {
		$box = new stdClass;
		$box->title = $title;
		$box->contents = $contents;
		$box->collapsed = (bool)$collapsed;

		$this->boxid++;
		$this->boxes[ $this->boxid ] = $box;		
	}


	/*************************/
	/* RENDER ACCORDION HTML */
	/*************************/
	public function render($gethtml=false, $importjs=true) {
		if ($importjs === true) { $this->importJS(); }
		$extra = ($this->collapsible) ? ' elx_accollapsible' : '';
		$buffer = '<ul class="elx_accordion'.$extra.'">'."\n";
		if ($this->boxes) {
			foreach ($this->boxes as $boxid => $box) {
				$css = ($box->collapsed) ? 'elx_accollapse' : 'elx_acexpand';
				$buffer .= "\t".'<li class="'.$css.'">'."\n";
				$buffer .= "\t\t".'<a href="#'.$boxid.'" class="elx_actitle">'.$box->title."</a>\n";
				$buffer .= "\t\t".'<ul class="elx_acitem">'."\n";
				$buffer .= "\t\t\t<li>\n";
				$buffer .= "\t\t\t\t".$box->contents;
				$buffer .= "\t\t\t</li>\n";
				$buffer .= "\t\t</ul>\n";
				$buffer .= "\t</li>\n";
			}
		}
		$buffer .= "\t</ul>\n";

		//reset boxes to free memory in case the object is not destroyed 
		//and also to avoid append current boxes on next accordions
		$this->boxes = array();

		if ($gethtml === true) {
			return $buffer;
		} else {
			echo $buffer;
		}
	}


//------ 2nd method with direct echo ---------------------------------------


	/***********************/
	/* OPEN ACCORDION HTML */
	/***********************/
	public function open($importjs=true) {
		if ($importjs === true) { $this->importJS(); }
		$extra = ($this->collapsible) ? ' elx_accollapsible' : '';
		echo '<ul class="elx_accordion'.$extra.'">'."\n";
	}


	/************************/
	/* CLOSE ACCORDION HTML */
	/************************/
	public function close() {
		echo "\t</ul>\n";
	}


	/*************/
	/* OPEN ITEM */
	/*************/
	public function openItem($title, $collapsed=true) {
		$this->boxid++;
		$css = ($collapsed) ? 'elx_accollapse' : 'elx_acexpand';
		echo "\t".'<li class="'.$css.'">'."\n";
		echo "\t\t".'<a href="#'.$this->boxid.'" class="elx_actitle">'.$title."</a>\n";
		echo "\t\t".'<ul class="elx_acitem">'."\n";
		echo "\t\t\t<li>\n";
	}


	/**************/
	/* CLOSE ITEM */
	/**************/
	public function closeItem() {
		echo "\t\t\t</li>\n";
		echo "\t\t</ul>\n";
		echo "\t</li>\n";
	}


	/***************************************/
	/* ADD REQUIRED JAVASCRIPT TO DOCUMENT */
	/***************************************/
	private function importJS() {
		$eDoc = eFactory::getDocument();

		$jsdir = eFactory::getElxis()->secureBase().'/includes/js/jquery';
		$eDoc->addJQuery();
		$eDoc->addScriptLink($jsdir.'/accordion.js');
	}

}

?>