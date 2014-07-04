<?php 
/**
* @version		$Id: auth.class.php 119 2011-02-22 20:32:10Z datahell $
* @package		Elxis
* @subpackage	User authentication
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_user/auth/auth.interface.php');


class elxisAuth {

	private $auths = array();
	private $errormsg = ''; //error message
	private $auth_current = '';
	private $auth = null; //loaded authentication class
	private $respose = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->initResponse();
		$this->loadAuthMethods();
	}


	/*****************************************/
	/* LOAD AVAILABLE AUTHENTICATION METHODS */
	/*****************************************/
	private function loadAuthMethods() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		if (defined('ELXIS_ADMIN')) {
			$this->auths['elxis'] = array();
			$this->auths['elxis']['title'] = $eLang->exist('WEBSITE') ? $eLang->get('WEBSITE') : 'Web site';
			$this->auths['elxis']['params'] = '';
			return;
		}

		$sql = "SELECT * FROM ".$db->quoteId('#__authentication')." WHERE ".$db->quoteId('published')."=1 ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) {
			$this->errormsg = $eLang->exist('NOAUTHMETHODS') ? $eLang->get('NOAUTHMETHODS') : 'There are no available Authentication methods!';
			return false;
		}

		foreach ($rows as $row) {
			$auth = $row['auth'];
			if (!file_exists(ELXIS_PATH.'/components/com_user/auth/'.$auth.'/'.$auth.'.auth.php')) { continue; }
			if ($auth == 'elxis') {
				$title = $eLang->exist('WEBSITE') ? $eLang->get('WEBSITE') : $row['title'];
			} else {
				$uppereng = strtoupper($auth);
				$title = $row['title'];
				if ($eLang->exist($uppereng)) { $title = $eLang->get($uppereng); }				
			}
			$this->auths[$auth] = array(
				'title' => $title, 
				'params' => $row['params']
			);
		}

		if (!$this->auths) {
			$this->errormsg = $eLang->exist('NOAUTHMETHODS') ? $eLang->get('NOAUTHMETHODS') : 'There are no available Authentication methods!';
			return false;
		}
	}


	/*************************************/
	/* SET CURRENT AUTHENTICATION METHOD */
	/*************************************/
	public function setAuth($auth) {
		$eLang = eFactory::getLang();

		if ((trim($auth) != '') && isset($this->auths[$auth])) {
			if ($auth == $this->auth_current) { return true; } //already loaded
			$this->loadAuthLang($auth);
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($this->auths[$auth]['params'], '', 'auth');

			$class = $auth.'Authentication';
			elxisLoader::loadFile('components/com_user/auth/'.$auth.'/'.$auth.'.auth.php');
			if (!class_exists($class, false)) {
				$this->errormsg = ucfirst($auth).' is not a valid Elxis Authentication method!';
				return false;
			}

			$this->auth_current = $auth;
			$this->auth = new $class($params);
			return true;
		}

		if (($auth != '') && $eLang->exist('AUTHMETHNOTEN')) {
			$this->errormsg = sprintf($eLang->get('AUTHMETHNOTEN'), $auth);
		} else {
			$this->errormsg = 'Invalid Authentication method!';
		}

		return false;
	}


	/***********************************************/
	/* LOAD CURRENT AUTHENTICATION METHOD LANGUAGE */
	/***********************************************/
	private function loadAuthLang($auth) {
		$eLang = eFactory::getLang();

		$clang = $eLang->currentLang();
		if (file_exists(ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.auth_'.$auth.'.php')) {
			$langfile = ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.auth_'.$auth.'.php';
		} else if (file_exists(ELXIS_PATH.'/language/en/en.auth_'.$auth.'.php')) {
			$langfile = ELXIS_PATH.'/language/en/en.auth_'.$auth.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$auth.'/language/'.$clang.'.auth_'.$auth.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_user/auth/'.$auth.'/language/'.$clang.'.auth_'.$auth.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$auth.'/language/en.auth_'.$auth.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_user/auth/'.$auth.'/language/en.auth_'.$auth.'.php';
		} else {
			$langfile = '';
		}

		if ($langfile != '') { $eLang->loadFile($langfile); }
	}


	/***********************/
	/* INITIALIZE RESPONSE */
	/***********************/
	private function initResponse() {
		$this->response = new stdClass;
		$this->response->loginsuccess = false;
		$this->response->method = '';
		$this->response->errormsg = '';
		$this->response->uid = 0;
		$this->response->firstname = null;
		$this->response->lastname = null;
		$this->response->uname = null;
		$this->response->email = null;
		$this->response->gid = 7;
		$this->response->avatar = null;
	}


	/*********************/
	/* AUTHENTICATE USER */
	/*********************/
	public function authenticate($options=array()) {
		$this->initResponse();
		if ($this->errormsg != '') {
			$this->response->errormsg = $this->errormsg;
			return false;
		}

		if (defined('ELXIS_ADMIN')) { $this->auth_current = 'elxis'; }
		
		if ($this->auth_current == '') {
			$this->response->errormsg = 'No authentication method has been set!';
			return false;
		}

		$this->response->method = $this->auth_current;
		$ok = $this->auth->authenticate($this->response, $options);
		if ($ok) {
			$this->response->loginsuccess = true;
			$this->response->errormsg = '';
			if ($this->auth_current != 'elxis') { $this->response->gid = 6; $this->response->uid = 0; }
		} else {
			$this->response->loginsuccess = false;
			if ($this->response->errormsg == '') { $this->response->errormsg = 'Authentication failed!'; }
		}

		return $this->response->loginsuccess;
	}


	/*********************/
	/* AUTHENTICATE USER */
	/*********************/
	public function loginForm() {
		if ($this->errormsg != '') {
			echo '<div class="elx_error">'.$this->errormsg."</div>\n";
			return;
		}
		if ($this->auth_current == '') {
			echo '<div class="elx_error">No authentication method has been loaded!'."</div>\n";
			return;
		}
		$this->auth->loginForm();
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/************************************/
	/* GET AUTHENTICATION METHODS ARRAY */
	/************************************/
	public function getAuths() {
		return $this->auths;
	}


	/******************************************/
	/* GET CURRENT AUTHENTICATION METHOD NAME */
	/******************************************/
	public function getAuth() {
		return $this->auth_current;
	}


	/****************************************/
	/* GET AUTHENTICATION METHOD DATA ARRAY */
	/****************************************/
	public function getAuthData($auth_method='') {
		if ($auth_method == '') { $auth_method = $this->auth_current; }
		return ($auth_method != '') ? $this->auths[$auth_method] : array();
	}


	/**********************/
	/* GET LOGIN RESPONSE */
	/**********************/
	public function getResponse() {
		return $this->response;
	}


	/**********************************/
	/* RUN CUSTOM TASK ON AUTH METHOD */
	/**********************************/
	public function runTask($etask='') {
		if ($this->auth_current == '') {
			exitPage::make('error', 'AUTH-0001', 'No authentication method has been set!');
		}
		if ($this->errormsg != '') {
			exitPage::make('error', 'AUTH-0002', $this->errormsg);
		}
		if ($etask == '') {
			exitPage::make('error', 'AUTH-0003', 'No task has been set!');
		}
		$this->auth->runTask($etask);
	}

}

?>