<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Tabs
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTabs {


	private $options = array('prefix' => 'elxtab');
	private $tabs = array();
	private $lastidx = 0;
	private $openidx = 0;


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($options=array()) {
		if ($options && (count($options) > 0)) {
			foreach ($options as $k => $v) {
				if (isset($this->options[$k])) {$this->options[$k] = $v; }
			}
		}
		$this->importJS();
	}


	/***************************************/
	/* ADD REQUIRED JAVASCRIPT TO DOCUMENT */
	/***************************************/
	private function importJS() {
		if (defined('ELXIS_TABS_LOADED')) { return; }
	
		$eDoc = eFactory::getDocument();

		$jsFile = eFactory::getElxis()->secureBase().'/includes/js/jquery/tabs.js';
		$eDoc->addJQuery();
		$eDoc->addScriptLink($jsFile);
		define('ELXIS_TABS_LOADED', 1);
	}


	/***********/
	/* ADD TAB */
	/***********/
	public function addTab($legend, $ajaxurl='') {
		$this->lastidx++;
		if (trim($legend) == '') { $legend = 'Tab '.$this->lastidx; }
		$this->tabs[ $this->lastidx ] = array($legend, $ajaxurl);
	}


	/*********************/
	/* ADD ARRAY OF TABS */
	/*********************/
	public function addTabs($legends) {
		if (is_array($legends) && (count($legends) > 0)) {
			foreach ($legends as $legend) {
				$this->addTab($legend);
			}
		}
	}


	/*******************/
	/* MAKE TABS INDEX */
	/*******************/
	public function makeIndex() {
		if (!defined('ELXIS_ADMIN')) {
			$this->tabs = array();
			$this->lastidx = 0;
			$this->openidx = 0;
			echo '<div class="elx_error">Elxis Tabs work only in the administration area!</div>';
			return;
		}
		if (!$this->tabs) {
			echo '<div class="elx_error">No tabs were set!</div>';
			return;
		}
		echo '<ul class="tabs">'."\n";
		foreach ($this->tabs as $idx => $tab) {
			$ext = ($tab[1] != '') ? ' id="ajax'.$this->options['prefix'].$idx.'"' : '';
			echo "\t".'<li><a href="#'.$this->options['prefix'].$idx.'"'.$ext.'>'.$tab[0]."</a></li>\n";
		}
		echo "</ul>\n";
	}


	/*******************/
	/* START A NEW TAB */
	/*******************/
	public function openTab() {
		if (!$this->tabs) { return; }
		$this->openidx++;
		if ($this->openidx > $this->lastidx) { return; }
		if (!isset($this->tabs[ $this->openidx ])) { return; }
		if ($this->openidx == 1) { echo '<div class="tab_container">'."\n"; }
		$tab = $this->tabs[ $this->openidx ];
		echo '<div id="'.$this->options['prefix'].$this->openidx.'" class="tab_content">'."\n";
		if ($tab[1] != '') {
			$aid = 'ajax'.$this->options['prefix'].$this->openidx;
			echo '<script type="text/javascript">'."\n";
			echo '$(document).ready(function() { $.ajaxSetup ({ cache: false });'."\n";
			echo 'var u'.$aid.' = \''.$tab[1].'\';'."\n";
			echo '$(\'#'.$aid.'\').click(function(){ $(\'#l'.$aid.'\').html(\'Loading...\').load(u'.$aid.');});'."\n";
			echo "});\n";
			echo "</script>\n";
			echo '<div id="l'.$aid.'"></div>'."\n";
		}
	}


	/******************/
	/* CLOSE OPEN TAB */
	/******************/
	public function closeTab() {
		if (!$this->tabs) { return; }
		if ($this->openidx > $this->lastidx) { return; } 
		echo "</div>\n";
		if ($this->openidx == $this->lastidx) { echo "</div>\n"; }
	}

}

?>