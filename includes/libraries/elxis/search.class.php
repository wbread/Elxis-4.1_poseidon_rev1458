<?php 
/**
* @version		$Id: search.class.php 1451 2013-06-17 17:46:09Z datahell $
* @package		Elxis
* @subpackage	Search
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_search/engines/engine.interface.php');


class elxisSearch {

	private $engines = array();
	private $engine_default = '';
	private $engine_current = '';
	private $engine = null; //loaded engine class
	private $errormsg = ''; //error message
	private $apc = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->loadEngines();
		if ($this->errormsg == '') {
			$cookie = false;
			if (isset($_COOKIE['elxisengine'])) {
				$cookie_engine = trim(filter_input(INPUT_COOKIE, 'elxisengine', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
				if (($cookie_engine != '') && isset($this->engines[$cookie_engine])) {
					$this->setEngine($cookie_engine);
					$cookie = true;
				}
			}
			if (!$cookie) {
				$this->setEngine( $this->engine_default );
			}
		}
	}


	/**********************/
	/* SET CURRENT ENGINE */
	/**********************/
	public function setEngine($engine) {
		$eLang = eFactory::getLang();

		if ((trim($engine) != '') && isset($this->engines[$engine])) {
			if ($engine == $this->engine_current) { $this->rememberEngine(); return true; } //already loaded
			$this->engine_current = $engine;
			$this->loadEngineLang($engine);
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($this->engines[$engine]['params'], '', 'engine');

			elxisLoader::loadFile('components/com_search/engines/'.$this->engine_current.'/'.$this->engine_current.'.engine.php');
			$class = $this->engine_current.'Engine';
			$this->engine = new $class($params);
			$this->rememberEngine();
			return true;
		}

		$this->errormsg = $eLang->exist('ENGINE_NOT_AVAIL') ? $eLang->get('ENGINE_NOT_AVAIL') : 'The requested search engine is not available!';
		return false;
	}


	/********************************/
	/* LOAD CURRENT ENGINE LANGUAGE */
	/********************************/
	private function loadEngineLang($engine) {
		$eLang = eFactory::getLang();

		$clang = $eLang->currentLang();
		if (file_exists(ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.engine_'.$engine.'.php')) {
			$langfile = ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.engine_'.$engine.'.php';
		} else if (file_exists(ELXIS_PATH.'/language/en/en.engine_'.$engine.'.php')) {
			$langfile = ELXIS_PATH.'/language/en/en.engine_'.$engine.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$engine.'/language/'.$clang.'.engine_'.$engine.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_search/engines/'.$engine.'/language/'.$clang.'.engine_'.$engine.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$engine.'/language/en.engine_'.$engine.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_search/engines/'.$engine.'/language/en.engine_'.$engine.'.php';
		} else {
			$langfile = '';
		}

		if ($langfile != '') { $eLang->loadFile($langfile); }
	}


	/**********************************/
	/* STORE CURRENT ENGINE IN COOKIE */
	/**********************************/
	private function rememberEngine() {
		$cookie_engine = '';
		if (isset($_COOKIE['elxisengine'])) {
			$cookie_engine = trim(filter_input(INPUT_COOKIE, 'elxisengine', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (($cookie_engine == '') || !isset($this->engines[$cookie_engine])) { $cookie_engine = ''; }
		}
		if ($cookie_engine != '') {
			if ($cookie_engine == $this->engine_current) { return; }
		} else {
			if ($this->engine_current == $this->engine_default) { return; }
		}

		$eSession = eFactory::getSession();
		$domain = $eSession->getCookieDomain();
		$path = $eSession->getCookiePath();
		$t = time() + 2592000;
		setcookie('elxisengine', $this->engine_current, $t, $path, $domain);
	}


	/*********************************/
	/* LOAD AVAILABLE SEARCH ENGINES */
	/*********************************/
	private function loadEngines() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		$this->apc = $elxis->getConfig('APC');
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		$lng = $eLang->currentLang();

		if (($this->apc == 1) && ($exactlev == 7)) {
			$engines = elxisAPC::fetch('engines'.$lng, 'search');
			$defengine = elxisAPC::fetch('defengine', 'search');
			if ($engines !== false) {
				$this->engines = $engines;
				if (($defengine !== false) && (trim($defengine) != '')) {
					$this->engines[$defengine]['default'] = true;
					$this->engine_default = $defengine;
				} else {
					foreach ($this->engines as $engine => $eng) {
						$this->engines[$engine]['default'] = true;
						$this->engine_default = $engine;
						break;
					}
				}
				return;
			}
		}

		$sql = "SELECT * FROM ".$db->quoteId('#__engines')." WHERE ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) {
			$this->errormsg = $eLang->exist('NO_AVAIL_ENGINES') ? $eLang->get('NO_AVAIL_ENGINES') : 'There are no available search engines!';
			return false;
		}

		$first = '';
		foreach ($rows as $row) {
			$engine = $row['engine'];
			if (!file_exists(ELXIS_PATH.'/components/com_search/engines/'.$engine.'/'.$engine.'.engine.php')) { continue; }
			if ($first == '') { $first = $engine; }
			$uppereng = strtoupper($engine);
			$title = $row['title'];
			if ($eLang->exist($uppereng)) { $title = $eLang->get($uppereng); }
			if (intval($row['defengine']) == 1) { $this->engine_default = $engine; }
			$this->engines[$engine] = array(
				'title' => $title, 
				'params' => $row['params'],
				'default' => false
			);
		}

		if ($first == '') {
			$this->errormsg = $eLang->exist('NO_AVAIL_ENGINES') ? $eLang->get('NO_AVAIL_ENGINES') : 'There are no available search engines!';
			return false;
		}

		if ($this->engine_default == '') { $this->engine_default = $first; }
		$this->engines[ $this->engine_default ]['default'] = true;

		if (($this->apc) && ($exactlev == 7)) {
			elxisAPC::store('engines'.$lng, 'search', $this->engines, 1800);
			elxisAPC::store('defengine', 'search', $this->engine_default, 1800);
		}
	}


	/********************************/
	/* GET AVAILABLE SEARCH ENGINES */
	/********************************/
	public function getEngines() {
		return $this->engines;
	}


	/*****************************/
	/* GET CURRENT SEARCH ENGINE */
	/*****************************/
	public function getCurrentEngine() {
		return ($this->engine_current != '') ? $this->engine_current : $this->engine_default;
	}


	/*****************************/
	/* GET CURRENT SEARCH ENGINE */
	/*****************************/
	public function getDefaultEngine() {
		return $this->engine_default;
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/*****************************/
	/* RETURN ENGINE'S META INFO */
	/*****************************/
	public function engineInfo() {
		if (!$this->engine) { return false; }
		return $this->engine->engineInfo();
	}


	/************************************/
	/* PERFORM SEARCH ON CURRENT ENGINE */
	/************************************/
	public function search($page=1) {
		if (!$this->engine) { return false; }
		$this->engine->search($page);
	}


	/*******************************/
	/* GET NUMBER OF TOTAL RESULTS */
	/*******************************/
	public function getTotal() {
		return $this->engine->getTotal();
	}


	/********************/
	/* GET SEARCH LIMIT */
	/********************/
	public function getLimit() {
		return $this->engine->getLimit();
	}


	/**************************/
	/* GET SEARCH LIMIT START */
	/**************************/
	public function getLimitStart() {
		return $this->engine->getLimitstart();
	}


	/***************************/
	/* GET CURRENT PAGE NUMBER */
	/***************************/
	public function getPage() {
		return $this->engine->getPage();
	}


	/***************************/
	/* GET MAXIMUM PAGE NUMBER */
	/***************************/
	public function getMaxPage() {
		return $this->engine->getMaxPage();
	}


	/****************************/
	/* GET SEARCH OPTIONS ARRAY */
	/****************************/
	public function getOptions() {
		return $this->engine->getOptions();
	}


	/******************************************/
	/* GET SEARCH SEARCH FOR THE CURRENT PAGE */
	/******************************************/
	public function getResults() {
		return $this->engine->getResults();
	}


	/****************************************/
	/* DISPLAY CURRENT ENGINE'S SEARCH FORM */
	/****************************************/
	public function searchForm() {
		$this->engine->searchForm();
	}


	/**************************/
	/* DISPLAY SEARCH RESULTS */
	/**************************/
	public function showResults() {
		if (($this->engine_current == $this->engine_default) && ($this->getTotal() > 0)) {
			$eDoc = eFactory::getDocument();
			$eDoc->setMetaTag('totalResults', $this->getTotal());
			$eDoc->setMetaTag('startIndex', $this->getLimitStart() + 1);
			$eDoc->setMetaTag('itemsPerPage', $this->getLimit());
		}
		$this->engine->showResults();
	}

}

?>