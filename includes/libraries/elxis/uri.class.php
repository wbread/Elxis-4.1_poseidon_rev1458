<?php 
/**
* @version		$Id: uri.class.php 1388 2013-02-19 16:44:24Z datahell $
* @package		Elxis
* @subpackage	URI
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisUri {

	private $sef = 0;
	private $site_url = ''; //site's URL
	private $site_aurl = ''; //site's administration URL
	private $admindir = 'estia';
	private $site_lang = 'en'; //site's default language
	private $site_frontpage = 'content:/'; //site's frontpage (default route)
	private $site_ssl = false; //site's ssl switch
	private $site_url_ssl = '';
	private $site_url_ssl_force = '';
	private $ssl = false;
	private $uri_string = ''; //requested URI (frontend or backend)
	private $uri_string_real = ''; //the full real requested URI
	private $uri_string_elxis; //elxis formatted requested URI
	private $has_slash = false; //url (not counting query string) has slash at the end?
	private $query_string = ''; //query string from uri_string
	private $uri_lang = ''; //requested language (empty for default)
	private $route = ''; //URI first segment (can be empty, file, or directory)
	private $component = 'content'; //component routed from the first segment of the URI
	private $segments = array(); //Parsed URI segments without the route/component and the query string
	private $routes = array();
	private $page_routes = array();
	private $reverse_routes = array();
	private $apc = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$this->apc = (int)$elxis->getConfig('APC');
		$this->sef = (int)$elxis->getConfig('SEF');
		$this->site_url = $elxis->getConfig('URL');
		$this->site_frontpage = $elxis->getConfig('DEFAULT_ROUTE');
		if (($this->site_frontpage == '') || ($this->site_frontpage == '/') || ($this->site_frontpage == 'content')) {
			$this->site_frontpage = 'content:/';
		}
		if (defined('ELXIS_ADMIN')) {
			$this->admindir = ELXIS_ADIR;
			$this->site_aurl = $elxis->getConfig('URL').'/'.ELXIS_ADIR;
		} else {
			$this->admindir = 'estia';
			$this->site_aurl = $elxis->getConfig('URL').'/estia';
		}
		$this->site_lang = $elxis->getConfig('LANG');
		if ($elxis->getConfig('SSL') == 2) {
			$this->site_ssl = true;
		} elseif ($elxis->getConfig('SSL') == 1) {
			$this->site_ssl = defined('ELXIS_ADMIN') ? true : false;
		}
		$this->ssl = $this->detectSSL();
		$this->site_url_ssl = $this->secureURL($this->site_url, false);
		$this->site_url_ssl_force = $this->secureURL($this->site_url, true);
		$this->loadRoutes();
		$this->uri_string = $this->getURI();
		$extra = (defined('ELXIS_ADMIN')) ? '/'.ELXIS_ADIR.'/' : '/';
		$this->uri_string_real = $this->site_url_ssl.$extra.$this->uri_string;
		$this->parseURI();
		$this->makeElxisUri();
	}


	/***************************************/
	/* CHECK IF PAGE WAS REQUESTED VIA SSL */
	/***************************************/
	public function detectSSL() {
		if (isset($_SERVER['HTTPS'])) {
			if (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == 1)) { return true; }
		}
		if (isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 443)) { return true; }
		return false;
	}


	/*********************/
	/* GET REQUESTED URI */
	/*********************/
	private function getURI() {
		$parsed = defined('ELXIS_ADMIN') ? parse_url($this->site_url.'/'.ELXIS_ADIR) : parse_url($this->site_url);
		$path = '';
		if (isset($parsed['path']) && ($parsed['path'] != '')) { $path = ltrim($parsed['path'], '/'); }
		unset($parsed);

		if (isset($_SERVER['REQUEST_URI'])) {
			$uri = ltrim(filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL), '/');
			if ($uri == '') { //php 5.2 bug or frontpage, retry to make sure value is ok
				$uri = ltrim($_SERVER['REQUEST_URI'], '/');
			}
			return $this->replacerURI($uri, $path, '');
		}

		if (isset($_SERVER['QUERY_STRING'])) {
			$query_str = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_URL);
		} else if (@getenv('QUERY_STRING')) {
			$query_str = filter_input(INPUT_ENV, 'QUERY_STRING', FILTER_SANITIZE_URL);
		} else {
			$query_str = '';
		}
		if ($query_str != '') { $query_str = '?'.$query_str; }

		if (isset($_SERVER['PATH_INFO'])) {
			$uri = ltrim(filter_input(INPUT_SERVER, 'PATH_INFO', FILTER_SANITIZE_URL), '/');
			return $this->replacerURI($uri, $path, $query_str);
		} elseif (@getenv('PATH_INFO')) {
			$uri = ltrim(filter_input(INPUT_ENV, 'PATH_INFO', FILTER_SANITIZE_URL), '/');
			return $this->replacerURI($uri, $path, $query_str);
		}

		if (isset($_SERVER['PHP_SELF'])) {
			$uri = ltrim(filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL), '/');
			return $this->replacerURI($uri, $path, $query_str);
		} elseif (@getenv('PHP_SELF')) {
			$uri = ltrim(filter_input(INPUT_ENV, 'PHP_SELF', FILTER_SANITIZE_URL), '/');
			return $this->replacerURI($uri, $path, $query_str);
		} else {
			return $this->replacerURI('', $path, $query_str);
		}
	}


	/***************************/
	/* GET RELATIVE URI STRING */
	/***************************/
	private function replacerURI($uri, $path='', $query_str='') {
		if ($uri != '') {
			if ($path != '') {
				$uri = preg_replace('#^('.$path.')#i', '', $uri);
				$uri = ltrim($uri, '/');
			}
			$uri = preg_replace('#^('.ELXIS_SELF.')#i', '', $uri);
			$uri = ltrim($uri, '/');
		}
		return $this->filter_uri(urldecode($uri.$query_str));
	}


	/***********************************************/
	/* REMOVE MALICIOUS CHARACTERS FROM URI STRING */
	/***********************************************/
	private function filter_uri($uri_string) {
		$chars = array('"', '<', '>', '$', '(', ')', '`', '^', '|', '\\', ':');
		$replacements = array('', '', '', '', '', '', '', '', '', '', '');
		$uri_string = str_replace($chars, $replacements, $uri_string);
		return $uri_string;
	}


	/********************/
	/* PARSE URI STRING */
	/********************/
	private function parseURI() {
		$sLink = preg_split('/[\?]/', $this->uri_string, 2, PREG_SPLIT_NO_EMPTY);
		if (isset($sLink[1])) {
			$this->query_string = $sLink[1];
		} else {
			if (strpos($this->uri_string, '?') !== false) {
				$this->query_string = $sLink[0];
				$sLink[0] = '/';
			}
		}

		if (isset($sLink[0])) {
			$this->has_slash = (preg_match('#(\/)$#', $sLink[0])) ? true : false;
			$parts = preg_split('#[\/]#', $sLink[0], -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$this->has_slash = false;
			$parts = array();
		}

		if ($parts && (count($parts) > 0)) {
			if (strlen($parts[0]) < 3) {
				if (file_exists(ELXIS_PATH.'/language/'.$parts[0].'/'.$parts[0].'.php') && ($parts[0] == strtolower($parts[0]))) {
					$this->uri_lang = $parts[0];
					if ($this->uri_lang == $this->site_lang) { $this->uri_lang = ''; }
					array_shift($parts);
				} else {
					exitPage::make('fatal', 'URI01', 'Invalid request. Requested language does not exist!');
				}
			}
		}

		if (!$parts || (count($parts) == 0)) {
			$this->has_slash = false;
			if (defined('ELXIS_ADMIN')) {
				if ($this->query_string != '') {
					$this->uri_string = 'cpanel/?'.$this->query_string;
				} else {
					$this->uri_string = 'cpanel';
				}
				//$this->uri_string = 'cpanel';
				//$this->query_string = '';
				$fp = 'cpanel';
			} else {
				$this->uri_string = $this->site_frontpage;
				$this->query_string = '';
				$fp = str_replace(':', '/', $this->site_frontpage);
			}
			$parts2 = preg_split('/[\/]/', $fp, -1, PREG_SPLIT_NO_EMPTY);
		} else {
			$parts2 = $parts;
		}
		unset($parts);

		$rt = $parts2[0];
		if (isset($this->routes[$rt])) {
			$this->route = $rt;
			$this->component = $this->routes[$rt];
			array_shift($parts2);
		} elseif ((count($parts2) == 1) && isset($this->page_routes[$rt])) {
			$this->route = '';
			$this->component = $this->page_routes[$rt];
		} elseif (!preg_match('#\.#', $rt) && file_exists(ELXIS_PATH.'/components/com_'.$rt.'/'.$rt.'.php')) {
			$this->route = $rt;
			$this->component = $rt;
			array_shift($parts2);
		} else {
			//We could query the db to find out if the first segment is a category or item seotitle, if not -> page not found
			$this->route = '';
			$this->component = defined('ELXIS_ADMIN') ? 'cpanel' : 'content';
		}

		$this->segments = $parts2;
		if (!file_exists(ELXIS_PATH.'/components/com_'.$this->component.'/'.$this->component.'.php')) {
			exitPage::make('fatal', 'URI02', 'Invalid request! The page requests a component which does not exist.');
		}
	}


	/******************************************************************/
	/* MAKE ELXIS FORMATTED URI STRING (NO LANGUAGE, NO QUERY STRING) */
	/******************************************************************/
	private function makeElxisUri() {
		$elxis_uri = '';
		if (($this->component != '') && ($this->component != 'frontpage') && ($this->component != 'cpanel') && ($this->component != 'content')) {
			$elxis_uri .= $this->component.':';
		}
		if ($this->segments) {
			$elxis_uri .= implode('/', $this->segments);
		}
		if ($this->has_slash && ($elxis_uri != '')) { $elxis_uri .= '/'; }
		$this->uri_string_elxis = $elxis_uri;
	}


	/* PUBLIC GETTERS */


	/**********************************/
	/* GET ELXIS FORMATTED URI STRING */
	/**********************************/
	public function getElxisUri($with_lang=false, $with_query_string=false) {
		$elxis_uri = '';
		if (($with_lang === true) && ($this->uri_lang != '')) {
			$elxis_uri .= ($this->uri_string_elxis != '') ? $this->uri_lang.':'.$this->uri_string_elxis : $this->uri_lang;
		} else {
			$elxis_uri .= $this->uri_string_elxis;
		}
		if (($with_query_string === true) && ($this->query_string != '')) {
			$elxis_uri .= '?'.$this->query_string;
		}
		return $elxis_uri;
	}


	public function isDir() {
		return $this->has_slash;
	}


	public function getUriString() {
		return $this->uri_string;
	}


	public function getRealUriString() {
		return $this->uri_string_real;
	}


	public function getUriLang() {
		return $this->uri_lang;
	}


	public function getQueryString() {
		return $this->query_string;
	}


	public function getRoute() {
		return $this->route;
	}


	public function getComponent() {
		return $this->component;
	}


	public function getSegments() {
		return $this->segments;
	}


	public function secureBase($force=false) {
		return ($force) ? $this->site_url_ssl_force : $this->site_url_ssl;
	}


	/********************************/
	/* GET THE SSL VERSION OF A URL */
	/********************************/
	public function secureURL($url='', $force=false) {
		if ($this->site_ssl) {
			if ($this->ssl || $force) {
				return preg_replace('@^(http\:)@i', 'https:', $url);
			}
		}
		return $url;
	}


	/**********************/
	/* LOAD CUSTOM ROUTES */
	/**********************/
	private function loadRoutes() {
		$elxis = eFactory::getElxis();

		if ($this->apc == 1) {
			$routes = elxisAPC::fetch('routes', 'system');
			if (($routes !== false) && is_array($routes)) {
				if (isset($routes['routes'])) { $this->routes = $routes['routes']; }
				if (isset($routes['reverse_routes'])) { $this->reverse_routes = $routes['reverse_routes']; }
				if (isset($routes['page_routes'])) { $this->page_routes = $routes['page_routes']; }
				return;
			}
		}

		if ($elxis->getConfig('REPO_PATH') == '') {
			$repo_path = ELXIS_PATH.'/repository';
		} else {
			$repo_path = $elxis->getConfig('REPO_PATH');
		}

		if (file_exists($repo_path.'/other/routes.php')) {
			include($repo_path.'/other/routes.php');
			if (isset($routes) && (count($routes) > 0)) {
				$this->routes = $routes;
				$this->reverse_routes = array_flip($routes);
			}
			if (isset($page_routes)) { $this->page_routes = $page_routes; }
		}

		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('component').", ".$db->quoteId('route')." FROM ".$db->quoteId('#__components')."\n"
		."\n WHERE ".$db->quoteId('route')." IS NOT NULL";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rows) {
			foreach ($rows as $row) {
				$r = trim($row['route']);
				if ($r == '') { continue; }
				$c = str_replace('com_', '', $row['component']);
				$this->routes[$r] = $c;
				$this->reverse_routes[$c] = $r;
			}
		}

		if ($this->apc == 1) {
			$routes = array(
				'routes' => $this->routes,
				'reverse_routes' => $this->reverse_routes,
				'page_routes' => $this->page_routes
			);
			elxisAPC::store('routes', 'system', $routes, 7200);
		}
	}


	/**************************************/
	/* GENERATE URL FROM ELXIS URI STRING */
	/**************************************/
	public function makeURL($elxis_uri='', $file='', $forcessl=false, $xhtml=true, $isadmin=false) {
		if ($isadmin) {
			$url = ($forcessl) ? $this->site_url_ssl_force.'/'.$this->admindir.'/' : $this->site_aurl.'/';
		} else {
			$url = ($forcessl) ? $this->site_url_ssl_force.'/' : $this->site_url.'/';
		}

		if (!$this->sef) {
			$url .= ($file == '') ? 'index.php/' : $file.'/';
		} else if (($file != '') && ($file != 'index.php')) {
			$url .= $file.'/';
		}

		if ($elxis_uri == '') {
			if ($this->uri_lang != '') { $url .= $this->uri_lang.'/'; }
			return $url;
		}

		$parts = preg_split('#\:#', $elxis_uri, -1, PREG_SPLIT_NO_EMPTY);
		$comp = '';
		$str = '';
		if (strlen($parts[0]) < 3) { //language
			if ($parts[0] != $this->site_lang) { $url .= $parts[0].'/'; }
			array_shift($parts);
		} elseif ($this->uri_lang != '') {
			$url .= $this->uri_lang.'/';
		}

		$c = count($parts);
		$stdcomp = $isadmin ? 'cpanel' : 'content';
		if ($c == 2) {
			$comp = $parts[0];
			if ($comp != $stdcomp) {
				if (isset($this->reverse_routes[$comp])) {
					$url .= $this->reverse_routes[$comp];
				} else {
					$url .= $comp;
				}
				if (substr($parts[1], 0, 1) != '/') { $url .= '/'; }
			}
			$url .= ($xhtml === true) ? str_replace('&', '&amp;', $parts[1]) : $parts[1];
			$url = preg_replace('#\/\/$#', '/', $url);
		} elseif ($c == 1) {
			$comp = $parts[0];
			if ($comp != $stdcomp) {
				if (isset($this->reverse_routes[$comp])) {
					$url .= $this->reverse_routes[$comp];
				} else {
					$url .= ($xhtml === true) ? str_replace('&', '&amp;', $comp) : $comp;
				}
			}
		} else { //error
			return $url;
		}

		return $url;
	}


	/*****************************************************************/
	/* GENERATE ADMIN URL FROM ELXIS URI STRING (ALL SSL IF ENABLED) */
	/*****************************************************************/
	public function makeAURL($elxis_uri='', $file='', $forcessl=false, $xhtml=true) {
		return $this->makeURL($elxis_uri, $file, $this->site_ssl, $xhtml, true);
	}


	/****************************************************/
	/* GENERATE SECURE URL (simpler method for makeURL) */
	/****************************************************/
	public function makeSecureURL($elxis_uri='', $file='', $xhtml=true) {
		return $this->makeURL($elxis_uri, $file, true, $xhtml);
	}


	/**********************************************************/
	/* GENERATE SECURE ADMIN URL (simpler method for makeURL) */
	/**********************************************************/
	public function makeSecureAURL($elxis_uri='', $file='', $xhtml=true) {
		return $this->makeURL($elxis_uri, $file, true, $xhtml, true);
	}

}

?>