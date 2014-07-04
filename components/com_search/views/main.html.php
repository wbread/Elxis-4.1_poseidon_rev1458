<?php 
/**
* @version		$Id: main.html.php 1416 2013-04-25 19:46:56Z datahell $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainSearchView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/********************************/
	/* SHOW SEARCH FORM AND RESULTS */
	/********************************/
	public function makePage($params) {
		$pagination = (int)$params->get('pagination', 1);
		$formposition = (int)$params->get('formposition', 1);

		$eSearch = eFactory::getSearch();

		$this->enginesLinks();

		if ((int)$params->get('summary', 0) == 1) {
			$this->searchSummary();
		}

		if ($formposition == 0) {
			$eSearch->searchForm();
		}

		if (($pagination == 0) || ($pagination == 2)) {
			$this->makePagination();
		}

		$eSearch->showResults();

		if (($pagination == 1) || ($pagination == 2)) {
			$this->makePagination();
		}

		if ($formposition == 1) {
			$eSearch->searchForm();
		}

		if ((int)$params->get('add_os', 1) == 1) {
			$this->addSearchProvider();
		}
	}


	/*****************************************/
	/* DISPLAY LINKS TO OTHER SEARCH ENGINES */
	/*****************************************/
	private function enginesLinks() {
		$eSearch = eFactory::getSearch();
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$engines = $eSearch->getEngines();
		if (count($engines) < 2) { return; }

		$current = $eSearch->getCurrentEngine();
		$options = $eSearch->getOptions();

		$q_str = '';
		if ($options && (count($options) > 0)) {
			$q_arr = array();
			foreach ($options as $key => $val) {
				if ($val != '') { $q_arr[] = $key.'='.$val; }
			}
			if (count($q_arr) > 0) { $q_str = '?'.implode('&', $q_arr); }
		}
		unset($options);

		echo '<div class="elx_engines_box">'."\n";
		echo '<span>'.$eLang->get('SEARCH_IN')."</span>\n";
		foreach ($engines as $engine => $data) {
			$link = $elxis->makeURL('search:'.$engine.'.html'.$q_str, '', false, true);
			if ($engine == $current) {
				echo '<span class="elx_engine_current">'.$data['title']."</span>\n";
			} else {
				echo '<a href="'.$link.'" title="'.$eLang->get('SEARCH_IN').' '.$data['title'].'">'.$data['title']."</a>\n";
			}
		}
		echo "</div>\n";
	}


	/****************************/
	/* DISPLAY PAGINATION LINKS */
	/****************************/
	private function makePagination() {
		$eSearch = eFactory::getSearch();

		$maxpage = $eSearch->getMaxPage();
		if ($maxpage < 2) { return; }

		$page = $eSearch->getPage();
		$engine = $eSearch->getCurrentEngine();
		$options = $eSearch->getOptions();
		$linkbase = eFactory::getElxis()->makeURL('search:'.$engine.'.html');
		if ($options) {
			$extras = array();
			foreach ($options as $key => $val) {
				if ($val != '') { $extras[] = $key.'='.$val; }
			}
			if (count($extras) > 0) {
				$linkbase .= '?'.implode('&amp;', $extras);
			}
		}

		echo eFactory::getElxis()->obj('Navigation')->navLinks($linkbase, $page, $maxpage);
	}


	/*****************************************/
	/* ADD SEARCH ENGINE PROVIDER TO BROWSER */
	/*****************************************/
	private function addSearchProvider() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MOBILE')) {
			if (ELXIS_MOBILE == 1) { return; }
		}
		$osd = $eURI->makeURL('search:osdescription.xml', 'inner.php');
		eFactory::getDocument()->addScriptLink($eURI->secureBase().'/components/com_search/extra/addengine.js');
		echo '<div id="elx_addsearchengine" style="margin: 10px 0;">'."\n";
		echo '<a href="javascript:void(null);" onclick="installSearchEngine(\''.$osd.'\');" title="'.$eLang->get('ADD_ENGINE_BROWSER').'">';
		echo '<img src="'.$eURI->secureBase().'/components/com_search/extra/browsers.png" border="0" alt="browsers" /> ';
		echo $eLang->get('ADD_TO_BROWSER')."</a>\n";
		echo "</div>\n";
	}


	/**************************/
	/* DISPLAY SEARCH SUMMARY */
	/**************************/
	private function searchSummary() {
		$eSearch = eFactory::getSearch();
		$eLang = eFactory::getLang();
		$total = $eSearch->getTotal();
		if ($total < 1) { return; }

		$start = $eSearch->getLimitStart() + 1;
		$end = $eSearch->getLimit() + $start - 1;
		if ($end > $total) { $end = $total; }

		echo '<div class="elx_search_summary">'."\n";
		printf($eLang->get('SEARCH_RETURNED'), '<strong>'.$total.'</strong>');
		echo ' ';
		printf($eLang->get('DISPLAY_FROM_TO'), '<strong>'.$start.'</strong>', '<strong>'.$end.'</strong>');
		echo "</div>\n";
	}

}

?>