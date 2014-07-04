<?php 
/**
* @version		$Id: fpage.html.php 1392 2013-02-23 19:28:45Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fpageContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**********************************/
	/* GENERATE FRONTPAGE LAYOUT HTML */
	/**********************************/
	public function showFrontpage($layout) {
		$cols = ($layout->wl > 0) ? 1 : 0;
		$cols += ($layout->wc > 0) ? 1 : 0;
		$cols += ($layout->wr > 0) ? 1 : 0;

		$this->openWrapper($cols);

		$this->openColumn($cols, $layout->wl);
		if ($layout->wl > 0) {
			$this->renderBox($layout->c1);
		}
		$this->closeColumn($layout->wl);

		$this->openColumn($cols, $layout->wc);
		if ($layout->wc > 0) {
			$this->renderCenter($layout);
		}
		$this->closeColumn($layout->wc);

		$this->openColumn($cols, $layout->wr);
		if ($layout->wr > 0) {
			$this->renderBox($layout->c3);
		}
		$this->closeColumn($layout->wr);

		$this->closeWrapper($cols);
	}


	/***********************/
	/* OPEN GLOBAL WRAPPER */
	/***********************/
	private function openWrapper($cols) {
		if ($cols > 1) {
			echo '<div style="margin:0; padding:0;">'."\n"; 
		}
	}


	/************************/
	/* CLOSE GLOBAL WRAPPER */
	/************************/
	private function closeWrapper($cols) {
		if ($cols > 1) {
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}
	}


	/***************/
	/* OPEN COLUMN */
	/***************/
	private function openColumn($cols, $width) {
		if ($width == 0) { return; }
		if ($cols > 1) {
			$float = (eFactory::getLang()->getinfo('DIR') == 'rtl') ? 'right' : 'left';
			echo '<div style="margin:0; padding:0; width:'.$width.'%; float:'.$float.';">'."\n";
		} else {
			echo '<div style="margin:0; padding:0;">'."\n";
		}
	}


	/****************/
	/* CLOSE COLUMN */
	/****************/
	private function closeColumn($width) {
		if ($width == 0) { return; }
		echo "</div>\n";
	}


	/**********************/
	/* RENDER COLUMN CELL */
	/**********************/
	private function renderBox($positions) {
		if ($positions) {
			$eDoc = eFactory::getDocument();
			foreach ($positions as $position) {
				$eDoc->modules($position);
			}
		} else {
			echo '&#160;';
		}
	}


	/******************************/
	/* RENDER CENTER COLUMN CELLS */
	/******************************/
	private function renderCenter($layout) {
		$float = (eFactory::getLang()->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		
		if (eFactory::getLang()->getinfo('DIR') == 'rtl') {
			$float = 'right';
			$margin = '0 0 0 1%';
		} else {
			$float = 'left';
			$margin = '0 1% 0 0';
		}
		
		$something = false;
		if ($layout->c2) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			$this->renderBox($layout->c2);
			echo "</div>\n";
		}

		if (($layout->c4) || ($layout->c5)) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:50%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c4);
			echo "</div>\n";
			echo '<div style="margin:0; padding:0; width:49%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c5);
			echo "</div>\n";
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}

		if (($layout->c6) || ($layout->c7)) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:66%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c6);
			echo "</div>\n";
			echo '<div style="margin:0; padding:0; width:33%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c7);
			echo "</div>\n";
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}

		if (($layout->c8) || ($layout->c9)) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:33%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c8);
			echo "</div>\n";
			echo '<div style="margin:0; padding:0; width:66%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c9);
			echo "</div>\n";
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}

		if ($layout->c10) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			$this->renderBox($layout->c10);
			echo "</div>\n";
		}

		if (($layout->c11) || ($layout->c12) || ($layout->c13)) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:33%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c11);
			echo "</div>\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:33%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c12);
			echo "</div>\n";
			echo '<div style="margin:0; padding:0; width:32%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c13);
			echo "</div>\n";
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}

		if ($layout->c14) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			$this->renderBox($layout->c14);
			echo "</div>\n";
		}

		if (($layout->c15) || ($layout->c16)) {
			echo '<div class="dspace10">'."\n";
			echo '<div style="margin:'.$margin.'; padding:0; width:50%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c15);
			echo "</div>\n";
			echo '<div style="margin:0; padding:0; width:49%; float:'.$float.';">'."\n";
			$this->renderBox($layout->c16);
			echo "</div>\n";
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		}

		if ($layout->c17) {
			$something = true;
			echo '<div class="dspace10">'."\n";
			$this->renderBox($layout->c17);
			echo "</div>\n";
		}

		if (!$something) { echo '&#160;'; }
	}

}

?>