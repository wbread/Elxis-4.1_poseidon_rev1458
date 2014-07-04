<?php 
/**
* @version		$Id: default.php 683 2011-10-21 18:38:02Z datahell $
* @package		Elxis
* @subpackage	Component Wrapper
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class wrapperController {

	private $view = null;
	private $model = null;
	private $mid = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view, $model, $mid) {
		$this->view = $view;
		$this->model = $model;
		$this->mid = $mid;
	}


	/*************************************/
	/* PREPARE TO DISPLAY A WRAPPED PAGE */
	/*************************************/
	public function wrap() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$row = $this->model->getMenuItem($this->mid);
		if (!$row) { exitPage::make('404', 'CWRA-0006'); }

		if ($row->alevel > $elxis->acl()->getLevel()) {
			exitPage::make('403', 'CWRA-0007');
		}

		if ($row->secure == 1) {
			if (eFactory::getURI()->detectSSL() === true) {
				$ok = true;
			} elseif ($elxis->getConfig('SSL') == 2) {
				$ok = false;
			} else {
				$ok = true;
			}
			if (!$ok) {
				$ssl_link = $elxis->makeURL('wrapper:'.$this->mid.'.html', $row->file, true);
				$elxis->redirect($ssl_link);
			}
		}

		if (!filter_var($row->link, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
			exitPage::make('error', 'CWRA-0008', $eLang->get('SORRY_WRONG_PAGE'));
		}

		if ($elxis->getConfig('MULTILINGUISM') == 1) {
			$lng = $eLang->currentLang();
			if ($lng != $elxis->getConfig('LANG')) {
				$translation = $this->model->menuTranslate($this->mid, $lng);
				if ($translation) { $row->title = $translation; }
			}
		}

		$options = array();
		$options['height'] = 600;
		$options['resize'] = false;
		$options['scrolling'] = 'no';
		$options['frameid'] = 'elxiswrap'.rand(100,999);
		$h = (int)$row->height;
		if ($h > 0) {
			$options['height'] = $h;
		} else {
			$options['height'] = 600;
			if ($this->sameHost($row->link) === true) {
				$options['resize'] = true;
				$options['scrolling'] = 'auto';
			}
		}

		if ($options['resize'] == true) {
			$js = "\n\t\t".'if (window.addEventListener) {'."\n";
			$js .= "\t\t\t".'window.addEventListener("load", function() { elxResizeIframe(\''.$options['frameid'].'\'); }, false);'."\n";
			$js .= "\t\t".'} else if (window.attachEvent) {'."\n";
			$js .= "\t\t\t".'window.attachEvent("onload", function() { elxResizeIframe(\''.$options['frameid'].'\'); });'."\n";
			$js .= "\t\t".'} else {'."\n";
			$js .= "\t\t\t".'window.onload = function() { elxResizeIframe(\''.$options['frameid'].'\'); }'."\n";
			$js .= "\t\t".'}'."\n";
			$eDoc->addScript($js);
		}

		$eDoc->setTitle($row->title);
		$this->view->showWrapper($row, $options);
	}


	/*********************************************************/
	/* CHECK IF THE FRAME SOURCE IS ON SAME HOST AS THE SITE */
	/*********************************************************/
	private function sameHost($link) {
		$parsed = parse_url(eFactory::getElxis()->getConfig('URL'));
		if (!$parsed || !isset($parsed['host'])) { return false; }
		$parsed2 = parse_url($link);
		if (!$parsed2 || !isset($parsed2['host'])) { return false; }
		return ($parsed['host'] == $parsed2['host']) ? true : false;
	}

}

?>