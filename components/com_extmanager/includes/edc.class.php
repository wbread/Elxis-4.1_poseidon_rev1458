<?php 
/**
* @version		$Id: edc.class.php 1256 2012-08-10 18:46:51Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisDC {

	private $elxisid = '';
	private $edc_url = 'http://www.elxis.net/inner.php/edc/rc/';
	private $edc_limit = 12;
	private $edc_ordering = 'c';
	private $edc_vcheck = true;
	private $cache_filters = 432000; //5 days
	private $cache_category = 43200; //12 hours
	private $cache_frontpage = 21600; //6 hours
	private $cache_extension = 3600; //60 minutes
	private $subsite = false;
	private $errormsg = '';
	private $time = 0;
	private $lang = 'en';
	private $repo_path = '';
	private $cache_path = '';


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($params=null) {
		$elxis = eFactory::getElxis();

		if ($params) {
			$this->elxisid = trim($params->get('elxisid', ''));
			$edc_url = trim($params->get('edc_url', ''));
			if (strpos($edc_url, 'http') === 0) {$this->edc_url = $edc_url; }
			$this->edc_limit = (int)$params->get('edc_limit', 12);
			if ($this->edc_limit < 1) { $this->edc_limit = 12; }
			$this->edc_ordering = trim($params->get('edc_ordering', 'c'));
			if (!in_array($this->edc_ordering, array('c', 'm', 'd', 'a', 'r'))) { $this->edc_ordering = 'c'; }
			$this->edc_vcheck = (intval($params->get('edc_vcheck', 1)) == 1) ? true : false;
		}
		unset($params);

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) { $this->subsite = true; }
		$this->time = time();
		$this->lang = eFactory::getLang()->currentLang();
		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		$this->cache_path = $this->repo_path.'/cache';
	}


	/****************/
	/* GET ELXIS ID */
	/****************/
	public function getElxisId() {
		return $this->elxisid;
	}


	/***************/
	/* GET EDC URL */
	/***************/
	public function getEdcUrl() {
		return $this->edc_url;
	}


	/******************************/
	/* GET THE LAST ERROR MESSAGE */
	/******************************/
	public function getErrorMessage() {
		return $this->errormsg;
	}


	/******************/
	/* EDC CATEGORIES */
	/******************/
	public function getCategories($sort=true) {
		$eLang = eFactory::getLang();

		$categories = array(
			1 => $eLang->get('CORE'),
			2 => $eLang->get('E_COMMERCE'),
			3 => $eLang->get('LANGUAGE'),
			4 => $eLang->get('MULTIMEDIA'),
			5 => $eLang->get('CALENDARS_EVENTS'),
			6 => $eLang->get('SEARCH_INDEXES'),
			7 => $eLang->get('LOCATION_WEATHER'),
			8 => $eLang->get('ADMINISTRATION'),
			9 => $eLang->get('SOCIAL_NETWORKS'),
			10 => $eLang->get('MENUS_NAVIGATION'),
			11 => $eLang->get('MOBILE_PHONES'),
			12 => $eLang->get('COMMUNICATION'),
			13 => $eLang->get('CONTENT'),
			14 => $eLang->get('FILE_MANAGEMENT'),
			15 => $eLang->get('EFFECTS'),
			16 => $eLang->get('ADVERTISING'),
			17 => $eLang->get('STATISTICS'),
			18 => $eLang->get('AUTH_USERS'),
			19 => $eLang->get('TEMPLATES'),
			20 => $eLang->get('ADMIN_TEMPLATES'),
			21 => $eLang->get('PUBLIC_OPINION'),
			22 => $eLang->get('BUSINESS')
		);

		if ($sort) { asort($categories); }
		$categories[23] = $eLang->get('MISCELLANEOUS'); //display it last!

		return $categories;
	}


	/***********************/
	/* GET CATEGORY'S NAME */
	/***********************/
	public function getCategoryName($catid) {
		$categories = $this->getCategories(false);
		return (isset($categories[$catid])) ? $categories[$catid] : 'Unknown';
	}


	/*****************************/
	/* GET EXTENSION'S TYPE NAME */
	/*****************************/
	public function getTypeName($type) {
		$eLang = eFactory::getLang();

		switch ($type) {
			case 'core': return $eLang->get('CORE'); break;
			case 'component': return $eLang->get('COMPONENT'); break;
			case 'module': return $eLang->get('MODULE'); break;
			case 'template': return $eLang->get('TEMPLATE'); break;
			case 'atemplate': return $eLang->get('TEMPLATE').' '.$eLang->get('BACKEND'); break;
			case 'auth': return $eLang->get('AUTH_METHOD'); break;
			case 'plugin': return $eLang->get('PLUGIN'); break;
			case 'engine': return $eLang->get('SEARCH_ENGINE'); break;
			case 'language': return $eLang->get('LANGUAGE'); break;
			case 'other': default: return $eLang->get('OTHER'); break;
		}
	}


	/*****************************/
	/* INITIAL CONNECTION TO EDC */
	/*****************************/
	public function connect() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('edcauth' => '', 'error' => '');
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$rnd = rand(1, 1000);
		$v = $elxis->getVersion();
		$p = $elxis->fromVersion('PRODUCT');
		$url = base64_encode($elxis->getConfig('URL'));
		$url = preg_replace('@(\=)+$@', '', $url);
		$options = array('task' => 'auth', 'elxisid' => $this->elxisid, 'url' => $url, 'version'=> $v, 'product' => $p, 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $options);
		} else {
			$xmldata = $this->httpget($this->edc_url, $options);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->get('DATA_EDC_FAILED');
			return $out;
		}

        if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
        $xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Could not connect to EDC server!';
				return $out;
			}
		}

		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->edcauth)) {
			$out['error'] = $eLang->get('AUTH_FAILED');
			return $out;
		}

		$edcauth = (string)$xmlDoc->edcauth;
		$out['edcauth'] = trim($edcauth);
		return $out;
	}


	/**********************/
	/* LOAD EDC FRONTPAGE */
	/**********************/
	public function getFrontpage($lng, $edcauth) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('blocks' => array(), 'rows' => array(), 'error' => '');
		if ($edcauth == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/frontpage_'.$lng.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/frontpage_'.$lng.'.php');
			if ($this->time - $ts <= $this->cache_frontpage) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$options = array('task' => 'frontpage', 'elxisid' => $this->elxisid, 'lang' => $lng, 'edcauth' => $edcauth, 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $options);
			} else {
				$xmldata = $this->httpget($this->edc_url, $options);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				return $out;
			}

        	if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
				if ($xmlDoc->getName() != 'edc') {
					$out['error'] = 'Could not load frontpage content from EDC server!';
					return $out;
				}
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->blocks)) {
				$out['error'] = 'EDC server returned no frontpage content!';
				return $out;
			}

			if (count($xmlDoc->blocks->children()) == 0) {
				$out['error'] = 'EDC server returned no frontpage content!';
				return $out;
			}

			$blocks = array();
			$extensions = array();
			foreach ($xmlDoc->blocks->children() as $block) {
				$attrs = $block->attributes();
				if ($attrs && isset($attrs['type'])) {
					$contents = (string)$block;
					if (trim($contents) == '') { continue; }
					$blocks[] = array('type' => trim($attrs['type']), 'contents' => $contents);
				}
			}
			
			if (isset($xmlDoc->extensions)) {
				if (count($xmlDoc->extensions->children()) > 0) {
					foreach ($xmlDoc->extensions->children() as $extension) {
						$ext = $this->fetchExtension($extension);
						if (!$ext) { continue; }
						$id = $ext['id'];
						$extensions[$id] = $ext;
					}
				}
			}

			$this->cacheFrontpage($lng, $blocks, $extensions);
		}

		if (!isset($blocks)) {
			include($this->cache_path.'/edc/frontpage_'.$lng.'.php');
		}

		if (!isset($blocks) || !is_array($blocks)) { return $out; }
		if (count($blocks) == 0) { return $out; }

		if (!isset($extensions)) { $extensions = array(); }

		$out['blocks'] = $blocks;
		$out['rows'] = $extensions;
		return $out;
	}


	/********************************************/
	/* GET CATEGORY'S FILTERS FROM EDC OR CACHE */
	/********************************************/
	public function getFilters($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$catid = $options['catid'];
		if ($catid < 1) { return false; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) { return false; }
		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/filters.php')) {
			$ts = filemtime($this->cache_path.'/edc/filters.php');
			if ($this->time - $ts <= $this->cache_filters) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$options = array('task' => 'filters', 'elxisid' => $this->elxisid, 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $options);
			} else {
				$xmldata = $this->httpget($this->edc_url, $options);
			}
			if (!$xmldata) { return false; }

        	if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) { return false; }
			if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
				if ($xmlDoc->getName() != 'edc') { return false; }
			}
			if (!isset($xmlDoc->category)) { return false; }
			if (count($xmlDoc->category->children()) == 0) { return false; }
			$filters = array();
			foreach ($xmlDoc->category as $category) {
				$attrs = $category->attributes();
				if ($attrs && isset($attrs['catid'])) {
					$cid = (int)$attrs['catid'];
					$filters[$cid] = array();
					if (isset($category->filter)) {
						foreach ($category->children() as $filter) {
							$fattrs = $filter->attributes();
							if ($fattrs && isset($fattrs['fid'])) {
								$fid = (int)$fattrs['fid'];
								$mltitle = '';
								if (isset($fattrs['mltitle'])) {
									$mltitle = (string)$fattrs['mltitle'];
									$mltitle = trim($mltitle); 
								}
								$title = (string)$filter;
								$title = trim($title);
								if ($title != '') {
									$filters[$cid][$fid] = array('title' => $title, 'mltitle' => $mltitle);
								}
							}
						}
					}
				}
			}
			$this->cacheFilters($filters);
		}

		if (!isset($filters)) {
			include($this->cache_path.'/edc/filters.php');
		}

		if (!isset($filters) || !is_array($filters)) { return false; }
		if (!isset($filters[$catid])) { return false; }
		if (count($filters[$catid]) == 0) { return false; }

		$out = array();
		foreach ($filters[$catid] as $fid => $arr) {
			$title = $arr['title'];
			$mltitle = $arr['mltitle'];
			if ($mltitle != '') {
				if ($eLang->exist($mltitle)) { $title = $eLang->get($mltitle); } 
			}
			$out[$fid] = $title;
		}

		asort($out);
		return $out;
	}


	/*****************************/
	/* GET A CATEGORY'S LISTINGS */
	/*****************************/
	public function getCategory($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('total' => 0, 'page' => 1, 'maxpage' => 1, 'rows' => array(), 'ordering' => $this->edc_ordering, 'error' => '');
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$catid = (int)$options['catid'];
		if ($catid < 1) { $out['error'] = 'Invalid category'; return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/category_'.$catid.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/category_'.$catid.'.php');
			if ($this->time - $ts <= $this->cache_category) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$roptions = array('task' => 'category', 'elxisid' => $this->elxisid, 'catid' => $catid, 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				return $out;
			}

        	if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
				if ($xmlDoc->getName() != 'edc') {
					$out['error'] = 'Could not load category extensions from EDC server!';
					return $out;
				}
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->extension)) {
				$this->cacheCategory($catid, array());
				return $out;
			}

			if (count($xmlDoc->extension->children()) == 0) {
				$this->cacheCategory($catid, array());
				return $out;
			}

			$extensions = array();
			foreach ($xmlDoc->extension as $extension) {
				$ext = $this->fetchExtension($extension);
				if (!$ext) { continue; }
				$id = $ext['id'];
				$extensions[$id] = $ext;
			}
			$this->cacheCategory($catid, $extensions);
		}

		if (!isset($extensions)) {
			include($this->cache_path.'/edc/category_'.$catid.'.php');
		}
		if (!isset($extensions) || !is_array($extensions)) { return $out; }
		if (count($extensions) == 0) { return $out; }

		$rows = array();		
		foreach ($extensions as $id => $extension) {
			if ($options['fid'] > 0) {
				if ($extension['fid'] <> $options['fid']) { continue; }
			}
			$rows[] = $extension;
		}

		if (!$rows) { return $out; }
		unset($extensions);

		$total = count($rows);
		$page = $options['page'];
		if ($page < 1) { $page = 1; }
		$maxpage = ($total == 0) ? 1 : ceil($total/$this->edc_limit);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($page > $maxpage) { $page = $maxpage; }
		$limitstart = (($page - 1) * $this->edc_limit);

		usort($rows, array($this, 'sortExtensions'));

		if ($total <= $this->edc_limit) {
			$out = array('total' => $total, 'page' => $page, 'maxpage' => $maxpage, 'rows' => $rows, 'ordering' => $this->edc_ordering, 'error' => '');
			return $out;
		}

		$page_rows = array();
		$end = $limitstart + $this->edc_limit;
		foreach ($rows as $key => $row) {
			if ($key < $limitstart) { continue; }
			if ($key >= $end) { break; }
			$page_rows[] = $row;
		}
		unset($rows);

		$out = array('total' => $total, 'page' => $page, 'maxpage' => $maxpage, 'rows' => $page_rows, 'ordering' => $this->edc_ordering, 'error' => '');
		return $out;
	}


	/*****************************************/
	/* GET EXTENSION'S FULL DETAILS FROM EDC */
	/*****************************************/
	public function getExtension($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('row' => array(), 'error' => '');

		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$id = (int)$options['id'];
		if ($id < 1) { $out['error'] = 'Invalid extension'; return $out; }

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/extension_'.$id.'.xml')) {
			$ts = filemtime($this->cache_path.'/edc/extension_'.$id.'.xml');
			if ($this->time - $ts <= $this->cache_extension) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$roptions = array('task' => 'view', 'elxisid' => $this->elxisid, 'id' => $id, 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				return $out;
			}

			eFactory::getFiles()->createFile('cache/edc/extension_'.$id.'.xml', $xmldata, true);
		} else {
			if (rand(1, 40) == 33) { $this->deleteExpired(); } //2,5% probability
		}

        if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
		if (!isset($xmldata)) {
			$xmlfile = $this->cache_path.'/edc/extension_'.$id.'.xml';
        	$xmlDoc = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		} else {
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		}
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Error loading extension from EDC server!';
				return $out;
			}
		}
		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->extension)) {
			$out['error'] = 'EDC response is not an extension!';
			return $out;
		}

		if (count($xmlDoc->extension->children()) == 0) {
			$out['error'] = 'EDC response is not an extension!';
			return $out;
		}

		$integers = array('id', 'catid', 'altcatid', 'fid', 'altfid', 'uid', 'downloads', 'rating_num', 'published', 'verified');
		$links = array('icon', 'authorlink', 'link', 'buylink', 'licenseurl', 'image1', 'image2', 'image3', 'demolink', 'doclink');

		$row = array();
		foreach ($xmlDoc->extension->children() as $k => $v) {
			$k = (string)$k;
			if (in_array($k, $integers)) {
				$row[$k] = intval(trim($v));
			} else if (in_array($k, $links)) {
				$link = trim($v);
				if ($link != '') {
					if (!preg_match('@^(https?\:\/\/)@i', $link)) { $link = ''; }
				}
				$row[$k] = $link;
			} else {
				$row[$k] = (string)$v;
			}
		}

		if (!isset($row['id'])) { $out['error'] = 'Missing or not acceptable value for id!'; return $out; }
		if (!isset($row['catid'])) { $out['error'] = 'Missing or not acceptable value for catid!'; return $out; }
		if (!isset($row['title'])) { $out['error'] = 'Missing or not acceptable value for title!'; return $out; }
		if (trim($row['title']) == '') {$out['error'] = 'Missing or not acceptable value for title!'; return $out; }
		if (!isset($row['type'])) { $out['error'] = 'Missing or not acceptable value for type!'; return $out; }
		if (!in_array($row['type'], array('core', 'component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine', 'language', 'other'))) {
			$row['type'] = 'other';
		}
		if (!isset($row['created']) || (trim($row['created']) == '')) { $out['error'] = 'Missing or not acceptable value for created!'; return $out; }
		if (!is_numeric($row['created'])) { $out['error'] = 'Missing or not acceptable value for created!'; return $out; }
		if (!isset($row['modified']) || (trim($row['modified']) == '')) { $out['error'] = 'Missing or not acceptable value for modified!'; return $out; }
		if (!is_numeric($row['modified'])) { $out['error'] = 'Missing or not acceptable value for modified!'; return $out; }
		if (!isset($row['version']) || (trim($row['version']) == '')) { $out['error'] = 'Missing or not acceptable value for version!'; return $out; }
		if (!is_numeric($row['version'])) { $out['error'] = 'Missing or not acceptable value for version!'; return $out; }
		if (!isset($row['name'])) { $row['name'] = ''; }
		if (($row['name'] == '') && in_array($row['name'], array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) {
			$out['error'] = 'Missing or not acceptable value for name!'; return $out;
		}
		if (!isset($row['short'])) { $row['short'] = ''; }
		if (trim($row['short'] != '')) { $row['short'] = htmlspecialchars(eUTF::trim($row['short'])); }
		if (!isset($row['author'])) { $row['author'] = ''; }
		if (trim($row['author'] != '')) { $row['author'] = htmlspecialchars(eUTF::trim($row['author'])); }
		if (!isset($row['downloads'])) { $row['downloads'] = 0; }
		if (!isset($row['price'])) { $row['price'] = ''; }
		if (!isset($row['rating_num'])) { $row['rating_num'] = 0; }
		if (!isset($row['rating'])) { $row['rating'] = '0.0'; }
		if (is_numeric($row['rating'])) { $row['rating'] = number_format($row['rating'], 1, '.', ''); } else { $row['rating'] = 0.0; }
		if (!isset($row['pcode'])) { $row['pcode'] = ''; }
		if (!isset($row['size'])) { $row['size'] = '0.00'; }
		if (is_numeric($row['size'])) { $row['size'] = number_format($row['size'], 2, '.', ''); } else { $row['size'] = 0.00; }
		if (!isset($row['category'])) { $row['category'] = $this->getCategoryName($row['catid']); }

		$out['row'] = $row;
		return $out;
	}


	/************************************/
	/* GET AUTHOR'S EXTENSIONS FROM EDC */
	/************************************/
	public function getAuthorExtensions($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('author' => array(), 'rows' => array(), 'error' => '');

		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$uid = (int)$options['uid'];
		if ($uid < 1) { $out['error'] = 'Invalid author'; return $out; }
		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$from_cache = false;
		if (file_exists($this->cache_path.'/edc/author_'.$uid.'.php')) {
			$ts = filemtime($this->cache_path.'/edc/author_'.$uid.'.php');
			if ($this->time - $ts <= $this->cache_category) { $from_cache = true; }
		}

		if (!$from_cache) {
			$rnd = rand(1, 1000);
			$roptions = array('task' => 'author', 'elxisid' => $this->elxisid, 'uid' => $uid, 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
			if (function_exists('curl_init')) {
				$xmldata = $this->curlget($this->edc_url, $roptions);
			} else {
				$xmldata = $this->httpget($this->edc_url, $roptions);
			}
			if (!$xmldata) {
				$out['error'] = $eLang->get('DATA_EDC_FAILED');
				return $out;
			}

        	if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
        	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
			if (!$xmlDoc) {
				$out['error'] = 'Invalid response from EDC server!';
				return $out;
			}
			if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
				if ($xmlDoc->getName() != 'edc') {
					$out['error'] = 'Could not load author extensions from EDC server!';
					return $out;
				}
			}

			if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
				$out['error'] = (string)$xmlDoc->error;
				return $out;
			}

			if (!isset($xmlDoc->author)) {
				$out['error'] = 'Author not found in EDC!';
				return $out;
			}

			if (count($xmlDoc->author->children()) == 0) {
				$out['error'] = 'Author not found in EDC!';
				return $out;
			}

			if (!isset($xmlDoc->extensions)) {
				$out['error'] = 'This author doesnt seem to own extensions in EDC!';
				return $out;
			}

			if (count($xmlDoc->extensions->children()) == 0) {
				$out['error'] = 'This author doesnt seem to own extensions in EDC!';
				return $out;
			}

			$author = $this->fetchAuthor($xmlDoc->author);
			if (!$author) {
				$out['error'] = 'Could not fetch author details from EDC!';
				return $out;
			}

			$extensions = array();
			foreach ($xmlDoc->extensions->children() as $extension) {
				$ext = $this->fetchExtension($extension);
				if (!$ext) { continue; }
				$id = $ext['id'];
				$extensions[$id] = $ext;
			}

			$this->cacheAuthor($uid, $author, $extensions);
		}

		if (!isset($author)) {
			include($this->cache_path.'/edc/author_'.$uid.'.php');
		}
		if (!isset($author) || !isset($extensions) || !is_array($author) || !is_array($extensions)) { return $out; }
		if (count($author) == 0) { return $out; }
		if (count($extensions) == 0) { return $out; }

		$out = array('author' => $author, 'rows' => $extensions, 'error' => '');
		return $out;
	}


	/*****************************/
	/* DOWNLOAD PACKAGE FROM EDC */
	/*****************************/
	public function downloadPackage($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('error' => 1, 'errormsg' => 'Unknown error', 'pack' => '');
		if ($this->subsite == true) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if ($options['pcode'] == '') { $out['error'] = 'No Elxis package set!'; return $out; }

		$rnd = rand(100, 999);
		$zippack = 'package_'.date('YmdHis').'_'.$rnd.'.zip';
		$remotefile = $this->edc_url.'?task=package&edcauth='.$options['edcauth'].'&pcode='.$options['pcode'].'&rnd='.$rnd;
		$zippath = $this->repo_path.'/tmp/'.$zippack;

		if (function_exists('curl_init')) {
			$fw = @fopen($zippath, 'wb');
			if (!$fw) { $out['errormsg'] = 'Repository tmp folder is not writeable!'; return $out; }
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $remotefile);
			curl_setopt($ch, CURLOPT_USERAGENT, 'Elxis CURL');
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_NOBODY, 0);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        	curl_setopt($ch, CURLOPT_TIMEOUT, 28);
        	curl_setopt($ch, CURLOPT_REFERER, $elxis->getConfig('URL'));
    		curl_setopt($ch, CURLOPT_FILE, $fw);
			$data = curl_exec($ch);
			if (0 == curl_errno($ch)) {
            	curl_close($ch);
				fclose($fw);
				$out['error'] = 0;
				$out['errormsg'] = '';
				$out['pack'] = $zippack;
			} else {
				curl_close($ch);
				$out['errormsg'] = $eLang->get('DLPACK_EDC_FAILED');
			}

			return $out;
		}

		$parsed = parse_url($remotefile);
		$port = ($parsed['scheme'] == 'https') ? 443 : 80;
		$getstr = (isset($parsed['path'])) ? $parsed['path'] : '/';
		$getstr .= (isset($parsed['query'])) ? '?'.$parsed['query'] : '';

		$fp = @fsockopen($parsed['host'], $port, $errno, $errstr, 28);
       	if (!$fp) { $out['errormsg'] = 'Could not access EDC!'; return $out; }
 	
		$req = "GET ".$getstr." HTTP/1.1\r\n";
		$req .= "Host: ".$parsed['host']."\r\n";
		$req .= "Referer: ".$elxis->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n";
        $response = '';
        fwrite($fp, $req);
        while (!feof($fp)) { $response .= fread($fp, 1024); }
        fclose($fp);
  
		$http_response_header = array();
		if (stripos($response, "\r\n\r\n") !== false) {
			$hc = explode("\r\n\r\n", $response);
			$headers = explode("\r\n", $hc[0]);
            if (!is_array($headers)) { $headers = array(); }
			if ($headers) {
				foreach($headers as $key => $header) {
					$a = "";
                    $b = "";
                    if (stripos($header, ":") !== false) {
                       	list($a, $b) = explode(":", $header);
                       	$http_response_header[trim($a)] = trim($b);
                    }
                }
            }
			$output = end($hc);
        } elseif (stripos($response, "\r\n") !== false) {
			$headers = explode("\r\n",  $response);
			if (!is_array($headers)) { $headers = array(); }
            if ($headers) {
				foreach($headers as $key => $header){
                    if($key < (count($headers) - 1)) {
                        $a = "";
                        $b = "";
                        if (stripos($header, ":") !== false) {
                            list($a, $b) = explode(":", $header);
                            $http_response_header[trim($a)] = trim($b);
                        }
                    }
                }
            }
			$output = end($headers);
        } else {
			$output = $response;
		}

		$fw = @fopen($zippath, 'wb');
		if (!$fw) { $out['errormsg'] = $eLang->get('DLPACK_EDC_FAILED'); return $out; }
		fwrite($fw, $output);
		fclose($fw);

		$out['error'] = 0;
		$out['errormsg'] = '';
		$out['pack'] = $zippack;
		return $out;
	}


	/******************/
	/* RATE EXTENSION */
	/******************/
	public function rateExtension($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('success' => false, 'error' => '');

		if ($this->subsite == true) { $out['error'] = $eLang->get('SUBSITES_NAVAIL'); return $out; }
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$id = (int)$options['id'];
		if ($id < 1) { $out['error'] = 'Invalid extension'; return $out; }
		if (($options['rating'] < 1) || ($options['rating'] > 5)) { $out['error'] = 'Invalid rate for the extension'; return $out; }

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$extids = array();
		if (isset($_COOKIE['extensions_rated'])) {
			$extids = explode(',',urldecode($_COOKIE['extensions_rated']));
			foreach ($extids as $extid) {
				$rid = (int)$extid;
				if ($rid == $id) {
					$out['error'] = $eLang->get('ALREADY_RATED');
					return $out;
				}
			}
		}

		$rnd = rand(1, 1000);
		$roptions = array('task' => 'rate', 'elxisid' => $this->elxisid, 'id' => $id, 'rating' => $options['rating'], 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $roptions);
		} else {
			$xmldata = $this->httpget($this->edc_url, $roptions);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->get('DATA_EDC_FAILED');
			return $out;
		}

        if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
       	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Wrong response from EDC server!';
				return $out;
			}
		}
		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->success)) {
			$out['error'] = 'Rate failed, unknown error!';
			return $out;
		}

		$ok = (int)$xmlDoc->success;
		if ($ok != 1) {
			$out['error'] = 'Rate failed, unknown error!';
			return $out;
		}
		$out['success'] = true;

		$extids[] = $id;
		$extstr = implode(',',$extids);
		$expire = $this->time + 10000000;
		$eSession = eFactory::getSession();
		@setcookie('extensions_rated', $extstr, $expire, $eSession->getCookiePath(), $eSession->getCookieDomain());

		return $out;
	}


	/********************/
	/* REPORT EXTENSION */
	/********************/
	public function reportExtension($options) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('success' => false, 'error' => '');

		if ($this->subsite == true) { $out['error'] = $eLang->get('SUBSITES_NAVAIL'); return $out; }
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		$id = (int)$options['id'];
		if ($id < 1) { $out['error'] = 'Invalid extension'; return $out; }
		if (($options['rcode'] < 1) || ($options['rcode'] > 10)) { $out['error'] = 'Invalid report reason'; return $out; }

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$rnd = rand(1, 1000);
		$roptions = array('task' => 'report', 'elxisid' => $this->elxisid, 'id' => $id, 'rcode' => $options['rcode'], 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $roptions);
		} else {
			$xmldata = $this->httpget($this->edc_url, $roptions);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->get('DATA_EDC_FAILED');
			return $out;
		}

        if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
       	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Wrong response from EDC server!';
				return $out;
			}
		}
		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->success)) {
			$out['error'] = 'Report failed, unknown error!';
			return $out;
		}

		$ok = (int)$xmlDoc->success;
		if ($ok != 1) {
			$out['error'] = 'Report failed, unknown error!';
			return $out;
		}
		$out['success'] = true;

		return $out;
	}


	/******************************/
	/* REGISTER SITE AT ELXIS.ORG */
	/******************************/
	public function registerSite($options, $comparams) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$out = array('elxisid' => '', 'error' => '', 'newparams' => '');

		if ($this->subsite == true) { $out['error'] = $eLang->get('SUBSITES_NAVAIL'); return $out; }
		if ($options['edcauth'] == '') { $out['error'] = $eLang->get('AUTH_FAILED'); return $out; }
		if ($this->elxisid != '') { $out['error'] = 'You already own an Elxis ID ('.$this->elxisid.')!'; return $out; }

		if (($elxis->getConfig('SECURITY_LEVEL') > 1) && ($this->subsite == true)) {
			 $out['error'] = $eLang->get('SECLEV_EDC_NOALLOW');
			 return $out;
	 	}

		$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$name = $elxis->getConfig('MAIL_NAME');
		$email = $elxis->getConfig('MAIL_EMAIL');
		$name = eUTF::trim(preg_replace($pat, '', $name));
		$email = eUTF::trim(preg_replace($pat, '', $email));
		if ($name == '') {
			 $out['error'] = 'Contact name is empty in Elxis configuration!';
			 return $out;
		}
		if ($email == '') {
			 $out['error'] = 'Contact email is empty in Elxis configuration!';
			 return $out;
		}

		$v = $elxis->getVersion();
		$rnd = rand(1, 1000);
		$url = base64_encode($elxis->getConfig('URL'));
		$url = preg_replace('@(\=)+$@', '', $url);
		$roptions = array('task' => 'register', 'name' => $name, 'email' => $email, 'url' => $url, 'version' => $v, 'edcauth' => $options['edcauth'], 'rnd' => $rnd);
		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($this->edc_url, $roptions);
		} else {
			$xmldata = $this->httpget($this->edc_url, $roptions);
		}
		if (!$xmldata) {
			$out['error'] = $eLang->get('DATA_EDC_FAILED');
			return $out;
		}

        if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
       	$xmlDoc = simplexml_load_string($xmldata, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$out['error'] = 'Invalid response from EDC server!';
			return $out;
		}
		if (version_compare(PHP_VERSION, '5.1.3') >= 0) {
			if ($xmlDoc->getName() != 'edc') {
				$out['error'] = 'Wrong response from EDC server!';
				return $out;
			}
		}
		if (isset($xmlDoc->error) && (trim($xmlDoc->error) != '')) {
			$out['error'] = (string)$xmlDoc->error;
			return $out;
		}

		if (!isset($xmlDoc->elxisid)) {
			$out['error'] = 'Registration failed, unknown error!';
			return $out;
		}

		$elxisid = trim($xmlDoc->elxisid);
		if ($elxisid == '') {
			$out['error'] = 'Registration failed, unknown error!';
			return $out;
		}
		$out['elxisid'] = $elxisid;

	    if (trim($comparams) != '') {
			$lines = explode("\n", $comparams);
		} else {
		    $lines = array();
		}

		$newraw = '';
		$found = false;
	    if ($lines) {
	    	foreach ($lines as $line) {
	        	if (trim($line) == '') { continue; }
	        	if (strpos($line, 'elxisid=') === 0) {
	        		$newraw .= 'elxisid='.$elxisid."\n";
	        		$found = true;
        		} else {
        			$newraw .= $line."\n";
       			}
	        }
	    }
	    
	    if (!$found) { $newraw .= 'elxisid='.$elxisid."\n"; }
	    $out['newparams'] = $newraw;

		return $out;
	}


	/*********************/
	/* ORDER EXTENSIONS */
	/*********************/
	private function sortExtensions($a, $b) {
		if ($this->edc_ordering == 'c') {
			if ($a['created'] == $b['created']) { return 0; }
			return ($a['created'] < $b['created']) ? 1 : -1;
		}else if ($this->edc_ordering == 'm') {
			if ($a['modified'] == $b['modified']) { return 0; }
			return ($a['modified'] < $b['modified']) ? 1 : -1;
		} else if ($this->edc_ordering == 'd') {
			if ($a['downloads'] == $b['downloads']) { return 0; }
			return ($a['downloads'] < $b['downloads']) ? 1 : -1;
		} else if ($this->edc_ordering == 'a') {
			return strcasecmp($a['downloads'], $b['downloads']);
		} else if ($this->edc_ordering == 'r') {
			if ($a['rating'] == $b['rating']) { return 0; }
			return ($a['rating'] < $b['rating']) ? 1 : -1;
		} else {
			return 0;
		}
	}


	/*********************************************/
	/* FETCH EXTENSION'S DETAILS FROM XML OBJECT */
	/*********************************************/
	private function fetchExtension($extension) {
		if (!isset($extension->id)) { return false; }
		$id = (int)$extension->id;
		if ($id < 1) { return false; }
		if (!isset($extension->catid)) { return false; }
		$catid = (int)$extension->catid;
		if ($catid < 1) { return false; }
		$fid = 0;
		if (isset($extension->fid)) { $fid = (int)$extension->fid; }

		$row = array();
		$row['id'] = $id;
		$row['catid'] = $catid;
		$row['fid'] = $fid;
		$row['type'] = 'other';
		$row['category'] = 'Unknown';
		$row['title'] = '';
		$row['name'] = '';
		$row['short'] = '';
		$row['icon'] = '';
		$row['uid'] = 0;
		$row['author'] = 'Unknown';
		$row['authorlink'] = '';
		$row['version'] = 0;
		$row['created'] = 0;
		$row['modified'] = 0;
		$row['link'] = '';
		$row['price'] = '';
		$row['buylink'] = '';
		$row['license'] = 'Unknown';
		$row['licenseurl'] = '';
		$row['downloads'] = 0;
		$row['rating_num'] = 0;
		$row['rating'] = 0;
		$row['pcode'] = '';

		if (isset($extension->type) && (trim($extension->type) != '')) {
			$row['type'] = (string)$extension->type;
			if (!in_array($row['type'], array('core', 'component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine', 'language', 'other'))) {
				$row['type'] = 'other';
			}
		}

		if (!isset($extension->title)) { return false; }
		$row['title'] = (string)$extension->title;
		if (trim($row['title']) == '') { return false; }
		if (!isset($extension->created) || (trim($extension->created) == '')) { return false; }
		$row['created'] = (string)$extension->created;
		if (!is_numeric($row['created'])) { return false; }
		if (!isset($extension->modified) || (trim($extension->modified) == '')) { return false; }
		$row['modified'] = (string)$extension->modified;
		if (!is_numeric($row['modified'])) { return false; }
		if (!isset($extension->version)) { return false; }
		$row['version'] = (string)$extension->version;
		if (($row['version'] == '') || !is_numeric($row['version'])) { return false; }
		if (!isset($extension->uid)) { return false; }
		$row['uid'] = (int)$extension->uid;
		if ($row['uid'] < 1) { return false; }
		if (isset($extension->name)) { $row['name'] = trim($extension->name); }
		if (($row['name'] == '') && in_array($row['type'], array('component', 'module', 'template', 'atemplate', 'auth', 'plugin', 'engine'))) { return false; }
		if (isset($extension->short) && (trim($extension->short) != '')) { $row['short'] = htmlspecialchars(eUTF::trim($extension->short)); }
		if (isset($extension->icon) && (strpos(trim($extension->icon), 'http') === 0)) { $row['icon'] = (string)$extension->icon; }
		if (isset($extension->author) && (trim($extension->author) != '')) { $row['author'] = htmlspecialchars(eUTF::trim($extension->author)); }
		if (isset($extension->authorlink) && (strpos(trim($extension->authorlink), 'http') === 0)) { $row['authorlink'] = (string)$extension->authorlink; }
		if (isset($extension->link) && (strpos(trim($extension->link), 'http') === 0)) { $row['link'] = (string)$extension->link; }
		if (isset($extension->buylink) && (strpos(trim($extension->buylink), 'http') === 0)) { $row['buylink'] = (string)$extension->buylink; }
		if (isset($extension->license) && (trim($extension->license) != '')) { $row['license'] = (string)$extension->license; }
		if (isset($extension->licenseurl) && (strpos(trim($extension->licenseurl), 'http') === 0)) { $row['licenseurl'] = (string)$extension->licenseurl; }
		if (isset($extension->price) && (trim($extension->price) != '')) { $row['price'] = trim($extension->price); }
		if (isset($extension->downloads)) { $row['downloads'] = (int)$extension->downloads; }
		if (isset($extension->rating_num)) { $row['rating_num'] = (int)$extension->rating_num; }
		if (isset($extension->rating)) {
			$rating = trim($extension->rating);
			if (is_numeric($rating)) { $row['rating'] = number_format($rating, 1, '.', ''); }
		}
		if (isset($extension->pcode) && (trim($extension->pcode) != '')) { $row['pcode'] = trim($extension->pcode); }
		$row['category'] = $this->getCategoryName($row['catid']);

		return $row;
	}


	/******************************************/
	/* FETCH AUTHOR'S DETAILS FROM XML OBJECT */
	/******************************************/
	private function fetchAuthor($author) {
		if (!isset($author->uid)) { return false; }
		$uid = (int)$author->uid;
		if ($uid < 1) { return false; }

		$row = array();
		$row['uid'] = $uid;
		$row['name'] = '';
		$row['avatar'] = '';
		$row['country'] = '';
		$row['city'] = '';
		$row['website'] = '';

		if (!isset($author->name)) { return false; }
		$row['name'] = (string)$author->name;
		if (trim($row['name']) == '') { return false; }
		if (isset($author->avatar) && (strpos(trim($author->avatar), 'http') === 0)) { $row['avatar'] = (string)$author->avatar; }
		if (isset($author->country) && (trim($author->country) != '')) { $row['country'] = trim($author->country); }
		if (isset($author->city) && (trim($author->city) != '')) { $row['city'] = trim($author->city); }
		if (isset($author->website) && (strpos(trim($author->website), 'http') === 0)) { $row['website'] = (string)$author->website; }

		return $row;
	}


	/**********************************/
	/* WRITE FILTERS ARRAY INTO CACHE */
	/**********************************/
	private function cacheFilters($catfilters) {
		$total_ctg = count($catfilters);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$filters = array('."\n";
		if ($total_ctg > 0) {
			$x = 1;
			foreach ($catfilters as $catid => $filters) {
				$contents .= "\t".$catid.' => array('."\n";
				$comma = ($x == $total_ctg) ? '' : ',';
				$total_flt = count($filters);
				if ($total_flt > 0) {
					$y = 1;
					foreach ($filters as $fid => $filter) {
						$comma2 = ($y == $total_flt) ? '' : ',';
						$contents .= "\t\t".$fid.' => array(\'title\' => \''.addslashes($filter['title']).'\', \'mltitle\' => \''.$filter['mltitle'].'\')'.$comma2."\n";
						$y++;
					}
				}
				$contents .= "\t".')'.$comma."\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/filters.php', $contents, true);
		return $ok;
	}


	/******************************************/
	/* WRITE CATEGORY'S EXTENSIONS INTO CACHE */
	/******************************************/
	private function cacheCategory($catid, $extensions) {
		$total_ext = count($extensions);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = -1;
			foreach ($extensions as $extension) {
				$id = $extension['id'];
				if ($n == -1) { $n = count($extension); }
				$i = 1;
				$contents .= "\t".$id.' => array(';
				foreach($extension as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/category_'.$catid.'.php', $contents, true);
		return $ok;
	}


	/****************************************/
	/* WRITE AUTHOR'S EXTENSIONS INTO CACHE */
	/****************************************/
	private function cacheAuthor($uid, $author, $extensions) {
		$total_ext = count($extensions);

		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";

		$contents .= '$author = array('."\n";
		$contents .= "\t".'\'uid\' => '.$uid.','."\n";
		$contents .= "\t".'\'name\' => \''.addslashes($author['name']).'\','."\n";
		$contents .= "\t".'\'avatar\' => \''.addslashes($author['avatar']).'\','."\n";
		$contents .= "\t".'\'country\' => \''.addslashes($author['country']).'\','."\n";
		$contents .= "\t".'\'city\' => \''.addslashes($author['city']).'\','."\n";
		$contents .= "\t".'\'website\' => \''.addslashes($author['website']).'\''."\n";
		$contents .= ');'."\n\n";

		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			foreach ($extensions as $ext) {
				$contents .= "\t".$ext['id'].' => array(';
				$contents .= '\'id\' => \''.$ext['id'].'\', \'catid\' => \''.$ext['catid'].'\', \'fid\' => \''.$ext['fid'].'\', \'uid\' => \''.$ext['uid'].'\', ';
				$contents .= '\'type\' => \''.$ext['type'].'\', \'name\' => \''.$ext['name'].'\', \'version\' => \''.$ext['version'].'\', \'price\' => \''.$ext['price'].'\', ';
				$contents .= '\'title\' => \''.addslashes($ext['title']).'\', \'short\' => \''.addslashes($ext['short']).'\', \'license\' => \''.addslashes($ext['license']).'\', ';
				$contents .= '\'licenseurl\' => \''.addslashes($ext['licenseurl']).'\', \'created\' => \''.$ext['created'].'\', \'modified\' => \''.$ext['modified'].'\', ';
				$contents .= '\'downloads\' => '.$ext['downloads'].', \'rating_num\' => '.$ext['rating_num'].', \'rating\' => '.$ext['rating'];
				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/author_'.$uid.'.php', $contents, true);
		return $ok;
	}


	/******************************/
	/* WRITE FRONTPAGE INTO CACHE */
	/******************************/
	private function cacheFrontpage($lng, $blocks, $extensions) {
		$total_blk = count($blocks);
		$total_ext = count($extensions);
		$contents = '<?php '."\n";
		$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
		$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
		$contents .= '$blocks = array('."\n";
		if ($total_blk > 0) {
			$x = 1;
			foreach ($blocks as $block) {
				$contents .= "\t".'array(\'type\' => \''.$block['type'].'\', \'contents\' => \''.addslashes($block['contents']).'\')';
				$contents .= ($x == $total_blk) ? "\n" : ",\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '$extensions = array('."\n";
		if ($total_ext > 0) {
			$x = 1;
			$n = -1;
			foreach ($extensions as $extension) {
				$id = $extension['id'];
				if ($n == -1) { $n = count($extension); }
				$i = 1;
				$contents .= "\t".$id.' => array(';
				foreach($extension as $key => $val) {
					if ($val === '') {
						$contents .= '\''.$key.'\' => \'\'';
					} else if ($key == 'version') {
						$contents .= '\''.$key.'\' => \''.$val.'\'';
					} else if (is_numeric($val)) {
						$contents .= '\''.$key.'\' => '.$val;
					} else {
						$contents .= '\''.$key.'\' => \''.addslashes($val).'\'';
					}
					
					if ($i < $n) { $contents .= ', '; }
					$i++;
				}

				$contents .= ($x == $total_ext) ? ")\n" : "),\n";
				$x++;
			}
		}
		$contents .= ');'."\n\n";
		$contents .= '?>';

		$ok = eFactory::getFiles()->createFile('cache/edc/frontpage_'.$lng.'.php', $contents, true);
		return $ok;
	}


	/*******************************/
	/* HTTP GET REQUEST USING CURL */
	/*******************************/
	private function curlget($url, $params=null) {
		$ch = curl_init();
        if ($params) {
        	curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params)); //url encodes the data
        } else {
        	curl_setopt($ch, CURLOPT_URL, $url);
        }

		curl_setopt($ch, CURLOPT_USERAGENT, 'Elxis CURL');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_REFERER, eFactory::getElxis()->getConfig('URL'));
		$result = curl_exec($ch);
		if (0 == curl_errno($ch)) {
            curl_close($ch);
            return $result;
		} else {
			curl_close($ch);
			return false;
		}
    }


	/************************************/
	/* HTTP GET REQUEST USING FSOCKOPEN */
	/************************************/
	private function httpget($url, $params=null) {
		$parseurl = parse_url($url);
		$getstr = '';
		if ($params) {
			$parr = array();
			foreach($params as $key => $val) { $parr[] = $key.'='.urlencode($val); }
			$getstr = implode('&', $parr);
			unset($parr);
		}

		$req = 'GET '.$parseurl['path'].'?'.$getstr." HTTP/1.1\r\n";
		$req .= 'Host: '.$parseurl['host']."\r\n";
		$req .= "Referer: ".eFactory::getElxis()->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12'."\r\n";
    	$req .= 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.6'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n"; 

		if (!isset($parseurl['port'])) {
			$parseurl['port'] = ($parseurl['scheme'] == 'https') ? 443 : 80;
		}

		$fp = fsockopen($parseurl['host'], $parseurl['port'], $errno, $errstr, 20);
		if (!$fp) { return false; }
		stream_set_timeout($fp, 15);
		fputs($fp, $req);
		$raw = '';
		while(!feof($fp)) {
			$raw .= fgets($fp);
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				fclose($fp);
				return false;
			}
		}
		fclose($fp);
		$result = '';
		$chunked = false;
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = $expl[1];
			if (preg_match('/Transfer\\-Encoding:\\s+chunked/i',$expl[0])) { $chunked = true; }
			unset($expl);
		}
		unset($raw);

		if ($chunked) {
			$result = $this->decodeChunked($result);
		}
		return $result;
	}


	/***************************/
	/* DECODE A CHUNKED STRING */
	/***************************/
	private function decodeChunked($chunk) {
		if (function_exists('http_chunked_decode')) {
			return http_chunked_decode($chunk);
		}

		$pos = 0;
		$len = strlen($chunk);
		$dechunk = null;
		while(($pos < $len) && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) {
            if (!$this->is_hex($chunkLenHex)) { return $chunk; }
			$pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

		return $dechunk;
	}


	/****************************/
	/* IS STRING A HEX NUMBER ? */
	/****************************/
	private function is_hex($hex) {
		$hex = strtolower(trim(ltrim($hex,"0")));
		if (empty($hex)) { $hex = 0; };
		$dec = hexdec($hex);
		return ($hex == dechex($dec));
	}


	/**********************/
	/* GLOBAL PERMISSIONS */
	/**********************/
	public function permissions() {
		$elxis = eFactory::getElxis();

		$perms = array();
		if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) {
			$perms['component_install'] = false;
			$perms['module_install'] = false;
			$perms['template_install'] = false;
			$perms['engine_install'] = false;
			$perms['auth_install'] = false;
			$perms['plugin_install'] = false;
		} else if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) {
			$perms['component_install'] = false;
			$perms['module_install'] = false;
			$perms['template_install'] = false;
			$perms['engine_install'] = false;
			$perms['auth_install'] = false;
			$perms['plugin_install'] = false;
		} else {
			$perms['component_install'] = ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) ? true : false;
			$perms['module_install'] = ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) ? true : false;
			$perms['template_install'] = ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) ? true : false;
			$perms['engine_install'] = ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) ? true : false;
			$perms['auth_install'] = ($elxis->acl()->check('com_extmanager', 'auth', 'install') > 0) ? true : false;
			$perms['plugin_install'] = ($elxis->acl()->check('com_extmanager', 'plugins', 'install') > 0) ? true : false;
		}
		return $perms;
	}


	/***************************************/
	/* EXTENSIONS SPECIFIC ALLOWED ACTIONS */
	/***************************************/
	public function extActions($row, $perms) {
		$actions = array('install' => false, 'update' => false, 'download' => false, 'buy' => false, 'is_installed' => false, 'is_updated' => false);

		if ($row['pcode'] != '') {
			$actions['download'] = true;
		} else {
			if ($row['price'] != '') { $actions['buy'] = true; }
		}

		switch ($row['type']) {
			case 'component':
				$comp = preg_replace('/^(com\_)/', '', $row['name']);
				if (file_exists(ELXIS_PATH.'/components/'.$row['name'].'/'.$comp.'.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['component_install'];
			break;
			case 'module':
				if (file_exists(ELXIS_PATH.'/modules/'.$row['name'].'/'.$row['name'].'.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['module_install'];
			break;
			case 'atemplate':
				if (file_exists(ELXIS_PATH.'/templates/admin/'.$row['name'].'/index.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['template_install'];
			break;
			case 'template':
				if (file_exists(ELXIS_PATH.'/templates/'.$row['name'].'/index.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['template_install'];
			break;
			case 'engine':
				if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$row['name'].'/'.$row['name'].'.engine.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['engine_install'];
			break;
			case 'auth':
				if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$row['name'].'/'.$row['name'].'.auth.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['auth_install'];
			break;
			case 'plugin':
				if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$row['name'].'/'.$row['name'].'.plugin.php')) { $actions['is_installed'] = true; }
				$can_install = $perms['plugin_install'];
			break;
			case 'language':
				$can_install = false;
			break;
			case 'other':
				$can_install = false;
			break;
			case 'core':
				$can_install = false;
				$row['is_installed'] = true;
			break;
			default:
				$can_install = false;
			break;
		}

		if (($row['type'] == 'core') || ($row['type'] == 'other') || ($row['type'] == 'language')) { 
			return $actions;
		}

		if ($can_install && ($row['pcode'] != '')) {
			if ($actions['is_installed'] == true) {
				$actions['update'] = true;
				if ($this->edc_vcheck) {
					$actions['update'] = false;
					elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
					$exml = new extensionXML();
					$info = $exml->quickXML($row['type'], $row['name']);
					$iversion = $info['version'];
					unset($exml, $info);
					if ($iversion > 0) {
						if ($iversion < $row['version']) {
							$actions['update'] = true;
						}
						if ($iversion == $row['version']) {
							$actions['is_updated'] = true;
						}
					}
				}
			} else {
				$actions['install'] = true;
			}
		} else if ($actions['is_installed'] == true) {
			if ($this->edc_vcheck) {
				elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
				$exml = new extensionXML();
				$info = $exml->quickXML($row['type'], $row['name']);
				$iversion = $info['version'];
				unset($exml, $info);
				if ($iversion > 0) {
					if ($iversion == $row['version']) { $actions['is_updated'] = true; }
				}
			}
		}

		return $actions;
	}


	/************************************/
	/* DELETE EXPIRED CACHED EXTENSIONS */
	/************************************/
	private function deleteExpired() {
		$eFiles = eFactory::getFiles();

		$filter = '^(extension\_)';
		$files = $eFiles->listFiles('cache/edc/', $filter, false, false, true);
		if (!$files) { return; }
		foreach ($files as $file) {
			$ts = filemtime($this->cache_path.'/edc/'.$file);
			if ($this->time - $ts > $this->cache_extension) {
				$eFiles->deleteFile('cache/edc/'.$file, true);
			}
		}
	}

}

?>