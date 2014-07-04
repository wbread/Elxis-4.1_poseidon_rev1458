<?php 
/**
* @version		$Id: install.class.php 1389 2013-02-22 19:16:35Z datahell $
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_INSTALLER') or die ('Direct access to this location is not allowed');


class elxisInstaller {

	private $ilangs = array(); //available installer languages
	private $lang = 'en';
	private $langarr = array();
	private $version = array();
	private $steps = array();
	private $step = 1;
	private $data = array();
	public $url = ''; //site URL without trailing slash
	private $error = false;
	private $errormsg = '';


	public function __construct() {
		$this->prepareEnv();
		$this->loadLanguage();
		$this->setSteps();
	}


	/***********************/
	/* PRE-PROCESS REQUEST */
	/***********************/
	public function process() {
		if ($this->step == 4) {
			$this->data['queries'] = 0;
			$this->data['import_error'] = '';
			$this->data['cfg'] = $this->catchConfigPost();
			if ($this->data['cfg']['errormsg'] == '') {
				require_once(ELXIS_PATH.'/includes/install/inc/miniloader.php');
				require_once(ELXIS_PATH.'/includes/libraries/elxis/database.class.php');
				$params = array(
					'dbtype' => $this->data['cfg']['cfg_db_type'],
					'host' => $this->data['cfg']['cfg_db_host'],
					'port' => $this->data['cfg']['cfg_db_port'],
					'dbname' => $this->data['cfg']['cfg_db_name'],
					'username' => $this->data['cfg']['cfg_db_user'],
					'password' => $this->data['cfg']['cfg_db_pass'],
					'persistent' => 0,
					'dsn' => $this->data['cfg']['cfg_db_dsn'],
					'scheme' => $this->data['cfg']['cfg_db_scheme'],
					'table_prefix' => $this->data['cfg']['cfg_db_prefix'],
					'debug' => 0
				);

				$db = new elxisDatabase($params, array(), false);
				$okcon = $db->connect($this->data['cfg']['cfg_db_dsn'], $this->data['cfg']['cfg_db_user'], $this->data['cfg']['cfg_db_pass'], array(), true); //on fail returns false
				if (!$okcon) {
					$this->data['queries'] = false;
					$this->data['import_error'] = $db->getErrorMsg();
					if ($this->data['import_error'] == '') { $this->data['import_error'] = 'Could not connect to database!'; }
				} else {
					$sqlfile = ELXIS_PATH.'/includes/install/data/'.$this->data['cfg']['cfg_db_type'].'.sql';
					$this->data['queries'] = $db->import($sqlfile);
					if ($this->data['queries'] === false) { $this->data['import_error'] = $db->getErrorMsg(); }
					$db->disconnect();	
				}
			} else {
				$this->step = 3;
			}
			return;
		}

		if ($this->step == 5) {
			$this->data['cfg'] = $this->catchConfigPost();
			if ($this->data['cfg']['errormsg'] != '') {
				$this->step = 3;
				return;
			}
			$this->data['usr'] = $this->catchUserPost();
			if ($this->data['usr']['errormsg'] != '') {
				$this->step = 4;
				return;
			}

			require_once(ELXIS_PATH.'/includes/install/inc/miniloader.php');
			require_once(ELXIS_PATH.'/includes/libraries/elxis/database.class.php');
			$params = array(
				'dbtype' => $this->data['cfg']['cfg_db_type'],
				'host' => $this->data['cfg']['cfg_db_host'],
				'port' => $this->data['cfg']['cfg_db_port'],
				'dbname' => $this->data['cfg']['cfg_db_name'],
				'username' => $this->data['cfg']['cfg_db_user'],
				'password' => $this->data['cfg']['cfg_db_pass'],
				'persistent' => 0,
				'dsn' => $this->data['cfg']['cfg_db_dsn'],
				'scheme' => $this->data['cfg']['cfg_db_scheme'],
				'table_prefix' => $this->data['cfg']['cfg_db_prefix'],
				'debug' => 0
			);

			if (!class_exists('elxisCryptHelper', false)) {
				include(ELXIS_PATH.'/includes/libraries/elxis/helpers/crypt.helper.php');
			}

			$db = new elxisDatabase($params, array(), false);
			$db->connect($this->data['cfg']['cfg_db_dsn'], $this->data['cfg']['cfg_db_user'], $this->data['cfg']['cfg_db_pass'], array());

			$eparams = array(
				'method' => $this->data['cfg']['cfg_encrypt_method'],
				'key' => $this->data['cfg']['cfg_encrypt_key']
			);
			$encObj = new elxisCryptHelper($eparams);
			$encpword = $encObj->getEncryptedPassword($this->data['usr']['u_pword']);
			unset($encObj, $eparams);

			$now = gmdate('Y-m-d H:i:s');
			$sql = "UPDATE #__users SET ".$db->quoteId('firstname')." = :x1, ".$db->quoteId('lastname')." = :x2, ".$db->quoteId('uname')." = :x3,"
			."\n ".$db->quoteId('pword')." = :x4, ".$db->quoteId('website')." = :x5, ".$db->quoteId('email')." = :x6, ".$db->quoteId('registerdate')." = :x7,"
			."\n ".$db->quoteId('lastvisitdate')." = :x8 WHERE ".$db->quoteId('uid')." = 1";
			$stmt = $db->prepare($sql);

			$stmt->bindParam(':x1', $this->data['usr']['u_firstname'], PDO::PARAM_STR);
			$stmt->bindParam(':x2', $this->data['usr']['u_lastname'], PDO::PARAM_STR);
			$stmt->bindParam(':x3', $this->data['usr']['u_uname'], PDO::PARAM_STR);
			$stmt->bindParam(':x4', $encpword, PDO::PARAM_STR);
			$stmt->bindParam(':x5', $this->data['cfg']['cfg_url'], PDO::PARAM_STR);
			$stmt->bindParam(':x6', $this->data['usr']['u_email'], PDO::PARAM_STR);
			$stmt->bindParam(':x7', $now, PDO::PARAM_STR);
			$stmt->bindParam(':x8', $now, PDO::PARAM_STR);
			$stmt->execute();

			$author_name = $this->data['usr']['u_firstname'].' '.$this->data['usr']['u_lastname'];
			$created_date = gmdate('Y-m-d H:i:s');

			$sql = "UPDATE #__content SET ".$db->quoteId('created')." = :y1, ".$db->quoteId('created_by_name')." = :y2";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':y1', $created_date, PDO::PARAM_STR);
			$stmt->bindParam(':y2', $author_name, PDO::PARAM_STR);
			$stmt->execute();

			$new_link = $this->data['cfg']['cfg_url'].'/estia/';
			$old_link = 'http://localhost/estia/';

			$sql = "UPDATE #__menu SET ".$db->quoteId('link')." = :xnew WHERE ".$db->quoteId('link')." = :xold";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xnew', $new_link, PDO::PARAM_STR);
			$stmt->bindParam(':xold', $old_link, PDO::PARAM_STR);
			$stmt->execute();

			$db->disconnect();

			$this->saveConfig();

			return;
		}
	}


	/***************************************************************/
	/* GET THE VALUE FOR A USER SUBMITTED ITEM FROM THE DATA ARRAY */
	/***************************************************************/
	public function dataValue($first, $second, $default) {
		if ($first != '') {
			if ($second != '') {
				return (isset($this->data[$first][$second])) ? $this->data[$first][$second] : $default;
			} else {
				return (isset($this->data[$first])) ? $this->data[$first] : $default;
			}
		}
		return $default;
	}


	/******************/
	/* MAKE STEP HTML */
	/******************/
	public function makehtml() {
		$fname = 'step'.$this->step.'.php';
		if (file_exists(ELXIS_PATH.'/includes/install/inc/'.$fname)) {
			include(ELXIS_PATH.'/includes/install/inc/'.$fname);
		}
	}


	/**************************/
	/* GET CURRENT STEP TITLE */
	/**************************/
	public function stepTitle() {
		$step = $this->step;
		return $this->steps[$step];
	}


	/********************/
	/* GET STEPS NUMBER */
	/********************/
	public function countSteps() {
		return count($this->steps);
	}


	/********************/
	/* GET CURRENT STEP */
	/********************/
	public function getStep() {
		return $this->step;
	}


	/********************/
	/* SET CURRENT STEP */
	/********************/
	private function setSteps() {
		$this->steps = array (
			1 => $this->getLang('ELXIS_INSTALL'),
			2 => $this->getLang('LICENSE'),
			3 => $this->getLang('SETTINGS'),
			4 => $this->getLang('DATA_IMPORT'),
			5 => $this->getLang('FINISH')
		);

		$step = 1;
		if (isset($_GET['step'])) {
			$step = (int)$_GET['step'];
		} else if (isset($_POST['step'])) {
			$step = (int)$_POST['step'];
		}
		if ($step < 1) { $step = 1; }
		if ($step > 5) { $step = 1; }
		if (!isset($this->steps[$step])) { $step = 1; }
		$this->step = $step;
	}


	/*****************************************/
	/* CATCH AND VALIDATE CONFIGURATION POST */
	/*****************************************/
	public function catchConfigPost() {
		$cfg = array(
			'errormsg' => '',
			'cfg_sitename' => '',
			'cfg_url' => '',
			'cfg_repo_path' => '',
			'cfg_lang' => '',
			'cfg_encrypt_method' => '',
			'cfg_encrypt_key' => '',
			'cfg_db_type' => '',
			'cfg_db_host' => '',
			'cfg_db_prefix' => '',
			'cfg_db_port' => 0,
			'cfg_db_name' => '',
			'cfg_db_user' => '',
			'cfg_db_pass' => '',
			'cfg_db_dsn' => '',
			'cfg_db_scheme' => '',
			'cfg_ftp' => 0,
			'cfg_ftp_host' => '',
			'cfg_ftp_port' => 21,
			'cfg_ftp_root' => '',
			'cfg_ftp_user' => '',
			'cfg_ftp_pass' => '',
			'cfg_sef' => 0
		);

		$cfg['cfg_sitename'] = filter_input(INPUT_POST, 'cfg_sitename', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$cfg['cfg_url'] = trim(filter_input(INPUT_POST, 'cfg_url', FILTER_SANITIZE_URL));
		$cfg['cfg_repo_path'] = filter_input(INPUT_POST, 'cfg_repo_path', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$cfg['cfg_repo_path'] = rtrim(str_replace('\\', '/', $cfg['cfg_repo_path']), '/');
		$cfg['cfg_lang'] = trim(filter_input(INPUT_POST, 'cfg_lang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_encrypt_method'] = trim(filter_input(INPUT_POST, 'cfg_encrypt_method', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($cfg['cfg_encrypt_method'] != 'xor') && ($cfg['cfg_encrypt_method'] != 'mcrypt')) { $cfg['cfg_encrypt_method'] = ''; }
		$cfg['cfg_encrypt_key'] = trim(filter_input(INPUT_POST, 'cfg_encrypt_key', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_type'] = trim(filter_input(INPUT_POST, 'cfg_db_type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_host'] = trim(filter_input(INPUT_POST, 'cfg_db_host', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_prefix'] = trim(filter_input(INPUT_POST, 'cfg_db_prefix', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_port'] = (isset($_POST['cfg_db_port'])) ? (int)$_POST['cfg_db_port'] : 0;
		if ($cfg['cfg_db_port'] < 0) { $cfg['cfg_db_port'] = 0; }
		$cfg['cfg_db_name'] = trim(filter_input(INPUT_POST, 'cfg_db_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_user'] = trim(filter_input(INPUT_POST, 'cfg_db_user', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_pass'] = trim(filter_input(INPUT_POST, 'cfg_db_pass', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_dsn'] = trim(filter_input(INPUT_POST, 'cfg_db_dsn', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_db_scheme'] = trim(filter_input(INPUT_POST, 'cfg_db_scheme', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_ftp'] = (isset($_POST['cfg_ftp'])) ? (int)$_POST['cfg_ftp'] : 0;
		if ($cfg['cfg_ftp'] <> 1) { $cfg['cfg_ftp'] = 0; }
		$cfg['cfg_ftp_host'] = trim(filter_input(INPUT_POST, 'cfg_ftp_host', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_ftp_port'] = (isset($_POST['cfg_ftp_port'])) ? (int)$_POST['cfg_ftp_port'] : 0;
		if ($cfg['cfg_ftp_port'] < 1) { $cfg['cfg_ftp_port'] = 21; }
		$cfg['cfg_ftp_root'] = trim(filter_input(INPUT_POST, 'cfg_ftp_root', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_ftp_root'] = rtrim($cfg['cfg_ftp_root'], '/');
		if ($cfg['cfg_ftp_root'] == '') { $cfg['cfg_ftp_port'] = '/'; }
		$cfg['cfg_ftp_user'] = trim(filter_input(INPUT_POST, 'cfg_ftp_user', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_ftp_pass'] = trim(filter_input(INPUT_POST, 'cfg_ftp_pass', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$cfg['cfg_sef'] = (isset($_POST['cfg_sef'])) ? (int)$_POST['cfg_sef'] : 0;
		if ($cfg['cfg_sef'] < 0) { $cfg['cfg_sef'] = 0; }

		if (trim($cfg['cfg_sitename']) == '') {
			$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('SITENAME'));
			return $cfg;
		}
		if (($cfg['cfg_url'] == '') || !filter_var($cfg['cfg_url'], FILTER_VALIDATE_URL)) {
			$cfg['errormsg'] = $this->getLang('INVALID_URL'); return $cfg;
		}
		if ($cfg['cfg_repo_path'] != '') {
			if (!file_exists($cfg['cfg_repo_path'].'/')) { $cfg['errormsg'] = $this->getLang('REPOPATH_NOEX'); return $cfg; }
		}
		if (($cfg['cfg_lang'] == '') || !file_exists(ELXIS_PATH.'/language/'.$cfg['cfg_lang'].'/'.$cfg['cfg_lang'].'.php')) {
			$cfg['errormsg'] = 'Invalid language!'; return $cfg;
		}
		if (($cfg['cfg_encrypt_key'] == '') || (strlen($cfg['cfg_encrypt_key']) != 16)) {
			$cfg['errormsg'] = 'Invalid encryption key!'; return $cfg;
		}

		if ($cfg['cfg_db_type'] == '') {
			$cfg['errormsg'] = 'You must select a database type!'; return $cfg;
		}
		$pdodrivers = PDO::getAvailableDrivers();
		if (!$pdodrivers) { $pdodrivers = array(); }
		if (!in_array($cfg['cfg_db_type'], $pdodrivers)) {
			$cfg['errormsg'] = 'PDO database driver '.$cfg['cfg_db_type'].' is not supported by your system!'; return $cfg;
		}
		if (!file_exists(ELXIS_PATH.'/includes/install/data/'.$cfg['cfg_db_type'].'.sql')) {
			$cfg['errormsg'] = 'PDO database driver '.$cfg['cfg_db_type'].' is not supported by the Elxis installer!'; return $cfg;
		}
		if ($cfg['cfg_db_host'] == '') {
			$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'DB '.$this->getLang('HOST')); return $cfg;
		}
		if ($cfg['cfg_db_prefix'] == '') {
			$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('TABLES_PREFIX')); return $cfg;
		}

		if ($cfg['cfg_db_name'] == '') {
			$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'DB '.$this->getLang('NAME')); return $cfg;
		}
		if ($cfg['cfg_db_scheme'] != '') {
			$cfg['cfg_db_scheme'] = str_replace('\\', '/', $cfg['cfg_db_scheme']);
			if (!is_file($cfg['cfg_db_scheme'])) {
				$cfg['errormsg'] = 'Database schema file does not exist!';
				return $cfg;
			}
		}
		if ((($cfg['cfg_db_type'] == 'sqlite') || ($cfg['cfg_db_type'] == 'sqlite2')) && ($cfg['cfg_db_scheme'] == '')) {
			$cfg['errormsg'] = 'A schema file is required for '.$cfg['cfg_db_type'];
			return $cfg;
		}
		if (($cfg['cfg_db_dsn'] == '') && ($cfg['cfg_db_scheme'] == '')) {
			if ($cfg['cfg_db_user'] == '') {
				$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'DB '.$this->getLang('USERNAME')); return $cfg;
			}
			if ($cfg['cfg_db_pass'] == '') {
				$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'DB '.$this->getLang('PASSWORD')); return $cfg;
			}
		}

		if ($cfg['cfg_ftp'] == 1) {
			if ($cfg['cfg_ftp_host'] == '') {
				$cfg['cfg_ftp_host'] = sprintf($this->getLang('FIELDNOEMPTY'), 'FTP '.$this->getLang('HOST')); return $cfg;
			}
			if ($cfg['cfg_ftp_user'] == '') {
				$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'FTP '.$this->getLang('USERNAME')); return $cfg;
			}
			if ($cfg['cfg_ftp_pass'] == '') {
				$cfg['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), 'FTP '.$this->getLang('PASSWORD')); return $cfg;
			}
		}

		return $cfg;
	}


	/********************************/
	/* CATCH AND VALIDATE USER POST */
	/********************************/
	public function catchUserPost() {
		$usr = array(
			'errormsg' => '',
			'u_firstname' => '',
			'u_lastname' => '',
			'u_email' => '',
			'u_uname' => '',
			'u_pword' => ''
		);

		$usr['u_firstname'] = filter_input(INPUT_POST, 'u_firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$usr['u_lastname'] = filter_input(INPUT_POST, 'u_lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$usr['u_email'] = trim(filter_input(INPUT_POST, 'u_email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$usr['u_uname'] = trim(filter_input(INPUT_POST, 'u_uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$usr['u_pword'] = trim(filter_input(INPUT_POST, 'u_pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$u_pword2 = trim(filter_input(INPUT_POST, 'u_pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if (trim($usr['u_firstname']) == '') {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('FIRSTNAME'));
			return $usr;
		}
		if (trim($usr['u_lastname']) == '') {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('LASTNAME'));
			return $usr;
		}
		if ($usr['u_email'] == '') {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('EMAIL'));
			return $usr;
		}
		if (!filter_var($usr['u_email'], FILTER_VALIDATE_EMAIL)) {
			$usr['errormsg'] = 'Provided email in invalid!'; return $usr;
		}

		if ($usr['u_uname'] == '') {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('USERNAME')); return $usr;
		}
		if ($usr['u_pword'] == '') {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOEMPTY'), $this->getLang('PASSWORD')); return $usr;
		}
		if ($u_pword2 == '') { $usr['errormsg'] = $this->getLang('PASS_NOMATCH'); return $usr; }
		if ($usr['u_pword'] != $u_pword2) { $usr['errormsg'] = $this->getLang('PASS_NOMATCH'); return $usr; }

		$ustr = preg_replace('/[^A-Z\-\_0-9]/i', '', $usr['u_uname']);
		$pstr = preg_replace('/[^A-Z\-\_0-9]/i', '', $usr['u_pword']);
		if ($ustr != $usr['u_uname']) {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOACCCHAR'), $this->getLang('USERNAME')); return $usr;
		}
		if (strlen($usr['u_uname']) < 4) {
			$usr['errormsg'] = 'Username is too short!'; return $usr;
		}
		if ($pstr != $usr['u_pword']) {
			$usr['errormsg'] = sprintf($this->getLang('FIELDNOACCCHAR'), $this->getLang('PASSWORD')); return $usr;
		}
		if (strlen($usr['u_pword']) < 8) {
			$usr['errormsg'] = 'Password is too short!'; return $usr;
		}

		return $usr;
	}


	/*****************************/
	/* GET LANGUAGE STRING VALUE */
	/*****************************/
	public function getLang($str) {
		if (isset($this->langarr[$str])) {
			return $this->langarr[$str];
		}
		return $str;
	}


	/************************/
	/* GET CURRENT LANGUAGE */
	/************************/
	public function currentLang() {
		return $this->lang;
	}


	/*********************/
	/* GET LANGUAGE INFO */
	/*********************/
	public function langInfo($str) {
		$linfo = $this->ilangs[ $this->lang ];
		if (isset($linfo[$str])) { return $linfo[$str]; }
		return '';
	}


	/********************/
	/* GET VERSION INFO */
	/********************/
	public function verInfo($str) {
		if (isset($this->version[$str])) {
			return $this->version[$str];
		}
		return '';
	}


	/************************/
	/* SELECT LANGUAGE HTML */
	/************************/
	public function langSelect() {
		$clang = $this->lang;
		echo '<div class="ei_curlang" id="ei_curlang">'.$this->langInfo('NAME')."</div>\n";
		echo '<div class="ei_langs">'."\n";
		foreach ($this->ilangs as $lng => $info) {
			$opac = ($lng == $clang) ? '1.0' : '0.6';
			if ($this->step < 4) {
				echo '<a href="'.$this->url.'/?step='.$this->step.'&amp;lang='.$lng.'" title="'.$info['NAME'].' - '.$info['NAME_ENG'].'" onmouseover="eiFocusLang(\''.$info['NAME'].'\', \''.$lng.'\');" onmouseout="eiFadeLang(\''.$this->langInfo('NAME').'\', \''.$lng.'\', \''.$clang.'\');">';
				echo '<img src="'.$this->url.'/includes/libraries/elxis/language/flags/'.$lng.'.png" alt="'.$info['NAME_ENG'].'" id="eiflag_'.$lng.'" border="0" style="opacity:'.$opac.'" />';
				echo "</a> \n";
			} else {
				echo '<img src="'.$this->url.'/includes/libraries/elxis/language/flags/'.$lng.'.png" alt="'.$info['NAME_ENG'].'" id="eiflag_'.$lng.'" border="0" style="opacity:'.$opac.'" />'." \n";
			}
		}
		echo "</div>\n";
	}


	/***********************************************/
	/* GET A LIST OF ALL AVAILABLE ELXIS LANGUAGES */
	/***********************************************/
	public function elxisLanguages() {
		$ilangs = $this->listFolders(ELXIS_PATH.'/language/');
		$elxislangs = array();
		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		if ($ilangs) {
			foreach ($ilangs as $ilang) {
				if (isset($langdb[$ilang])) { $elxislangs[$ilang] = $langdb[$ilang];  }
			}
		}

		return $elxislangs;
	}


	/**********************/
	/* PREPARE ENVIROMENT */
	/**********************/
	private function prepareEnv() {
		require(ELXIS_PATH.'/includes/version.php');
		$this->version = $elxis_version;

		$reload = false;
		if (ELXIS_SELF != 'index.php') { $reload = true; }
		$protocol = 'http';
		$port = 0;

		if (isset($_SERVER['SERVER_PROTOCOL'])) {
    		$protocol = (strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === false) ? 'http' : 'https';
		} else if (isset($_SERVER['HTTPS'])) {
			if (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == 1)) { $protocol = 'https'; }
		}

		if (isset($_SERVER['SERVER_PORT'])) {
			if ($protocol == 'http') {
				if ($_SERVER['SERVER_PORT'] != 80) { $port = (int)$_SERVER['SERVER_PORT']; }
			} else {
				if (($_SERVER['SERVER_PORT'] != 443) && ($_SERVER['SERVER_PORT'] != 80)) { $port = (int)$_SERVER['SERVER_PORT']; }
			}
		}

		$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
		if (strpos($host, ':') !== false) {
			$hparts = preg_split('@\:@', $host, 2, PREG_SPLIT_NO_EMPTY);
			$host = $hparts[0];
		}
		$urlpath = '';
		if (isset($_SERVER['REQUEST_URI'])) {
			$n = strpos($_SERVER['REQUEST_URI'], '?');
			if ($n !== false) {
				$urlpath = substr($_SERVER['REQUEST_URI'], 0, $n);
			} else {
				$urlpath = $_SERVER['REQUEST_URI'];
			}
		}

		if (strpos($urlpath, '.') !== false) {
			if (strrpos($urlpath, 'index.php') !== false) {
				$reload = false;
			} else {
				$reload = true;
			}
			$slashpos = strrpos($urlpath, '/');
			$urlpath = substr($urlpath, 0, $slashpos);
		}
		$urlpath = trim($urlpath, '/');
		if ($urlpath == '/') { $urlpath = ''; }

		$continue = true;
		while ($continue == true) {
			if ($urlpath == '') { $continue = false; break; }
			if (strrpos(ELXIS_PATH, $urlpath) !== false) {
				$continue = false;
				break;
			} else {
				$slashpos = strrpos($urlpath, '/');
				$urlpath = substr($urlpath, 0, $slashpos);
				$urlpath = trim($urlpath, '/');
			}
		}

		if ($urlpath == '/') { $urlpath = ''; }
		if ($urlpath != '') { $urlpath = '/'.$urlpath; }

		if ($port > 0) {
			$this->url = $protocol.'://'.$host.':'.$port.$urlpath;
		} else {
			$this->url = $protocol.'://'.$host.$urlpath;
		}

		if ($reload == true) {
			if (headers_sent()) {
				echo '<script type="text/javascript">document.location.href="'.$this->url.'";</script>'."\n";
				echo 'Redirection to the proper installation URL<br />';
				echo '<a href="'.$this->url.'" stle="font-weight:bold;">Click here</a> if you don\'t get redirected automatically.'."\n";
			} else {
				if (ob_get_length() > 0) { ob_end_clean(); }
				@header('content-type:text/html; charset=utf-8');
				@header('Location: '.$this->url);
			}
			exit();
		}
	}


	/*****************/
	/* LOAD LANGUAGE */
	/*****************/
	private function loadLanguage() {
		$ilangs = $this->listFolders(ELXIS_PATH.'/language/');

		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		if ($ilangs) {
			foreach ($ilangs as $ilang) {
				if (isset($langdb[$ilang])) {
					if (file_exists(ELXIS_PATH.'/language/'.$ilang.'/'.$ilang.'.install.php')) {
						$this->ilangs[$ilang] = $langdb[$ilang];
						$this->ilangs[$ilang]['RTLSFX'] = ($langdb[$ilang]['DIR'] == 'rtl') ? '-rtl' : '';
					}
				}
			}
		}

		$lang = '';
		if (isset($_GET['lang']) && (trim($_GET['lang']) != '')) {
			$lng = trim($_GET['lang']);
			if (isset($this->ilangs[$lng])) { $lang = $lng; }
		}
		if (($lang == '') && isset($_POST['lang']) && (trim($_POST['lang']) != '')) {
			$lng = trim($_POST['lang']);
			if (isset($this->ilangs[$lng])) { $lang = $lng; }
		}
		if ($lang == '') { $lang = 'en'; }
		
		if (!isset($this->ilangs[$lang])) {
			die('Fatal error: Language '.$lang.' was not found!');
		}

		$this->lang = $lang;

		include(ELXIS_PATH.'/language/'.$lang.'/'.$lang.'.php');
		$this->langarr = $_lang;
		unset($_lang);
		if (!isset($locale) || !is_array($locale)) { $locale = array('en_GB.utf8', 'en_GB.UTF-8', 'en_GB', 'en', 'english', 'england'); }
		include(ELXIS_PATH.'/language/'.$lang.'/'.$lang.'.install.php');
		foreach ($_lang as $k => $v) { $this->langarr[$k] = $v; }
		unset($_lang);
		$this->setLocale($locale);
	}


	/****************************/
	/* LIST FOLDERS INSIDE PATH */
	/****************************/
	private function listFolders($path) {
		$handle = @opendir($path);
		if (!$handle) { return array(); }
		$arr = array();
		while ($entry = readdir($handle)) {
			$dir = $path.$entry;
			if (($entry != '.') && ($entry != '..') && is_dir($dir)) { $arr[] = $entry; }
		}
		closedir($handle);
		if ($arr) { asort($arr); }
		return $arr;
	}


	/*************************/
	/* SET ENVIROMENT LOCALE */
	/*************************/
	private function setLocale($locale) {
		if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
			$loc = array ('en_GB.utf8', 'en_GB.utf-8', 'eng', 'english');
        	setlocale(LC_COLLATE, $loc);
        	setlocale(LC_CTYPE, $loc);
        	setlocale(LC_TIME, $loc);
        	return;
		}

		setlocale(LC_COLLATE, $locale);
		setlocale(LC_CTYPE, $locale);
        setlocale(LC_TIME, $locale);	
    }


	/*************************************/
	/* BUILD AND SAVE CONFIGURATION FILE */
	/*************************************/
	private function saveConfig() {
		$gpc = 0;
		if (function_exists('get_magic_quotes_gpc') && is_callable('get_magic_quotes_gpc')) { $gpc = get_magic_quotes_gpc(); }

		$mparts = explode('@', $this->data['usr']['u_email']);
		$mdomain = strtolower($mparts[1]);
		$sparts = parse_url($this->data['cfg']['cfg_url']);
		$sdomain = strtolower($sparts['host']);
		if ($mdomain == $sdomain) {
			$fromemail = 'elxis@'.$mparts[1];
		} else {
			$fromemail = $this->data['usr']['u_email'];
		}

		$tld = '';
		$n = strrpos($sdomain, '.');
		if ($n !== false) {
			$n = $n + 1;
			$tld = substr($sdomain, $n);
			$tld = strtoupper($tld);
		}
		unset($mparts, $sparts, $mdomain, $sdomain, $n);

		$timezone = '';
		if (($tld != '') && !in_array($tld, array('LOC', 'COM', 'NET', 'ORG', 'TV', 'INFO', 'BIZ', 'EU', 'EDU', 'GOV', 'MIL', 'TRAVEL', 'ASIA'))) {
			$timezone = $this->getTzone($tld);
			if ($timezone == '') {
				$region = strtoupper($this->langInfo('REGION'));
				$timezone = $this->getTzone($region);
			}
		} else {
			$region = strtoupper($this->langInfo('REGION'));
			$timezone = $this->getTzone($region);
		}

		if ($timezone == '') { $timezone = 'Europe/London'; }
		unset($tld);

		$cvars = array();
		$cvars['URL'] = $this->data['cfg']['cfg_url'];
		$cvars['REPO_PATH'] = $this->data['cfg']['cfg_repo_path'];
		$cvars['LANG'] = $this->data['cfg']['cfg_lang'];
		$cvars['SITELANGS'] = '';
		$cvars['SITENAME'] = $this->data['cfg']['cfg_sitename'];
		if ($gpc == 0) { $cvars['SITENAME'] = addslashes($cvars['SITENAME']); }
		$cvars['ENCRYPT_METHOD'] = $this->data['cfg']['cfg_encrypt_method'];
		$cvars['ENCRYPT_KEY'] = $this->data['cfg']['cfg_encrypt_key'];
		$cvars['DB_TYPE'] = $this->data['cfg']['cfg_db_type'];
		$cvars['DB_HOST'] = $this->data['cfg']['cfg_db_host'];
		$cvars['DB_NAME'] = $this->data['cfg']['cfg_db_name'];
		$cvars['DB_PREFIX'] = $this->data['cfg']['cfg_db_prefix'];
		$cvars['DB_USER'] = $this->data['cfg']['cfg_db_user'];
		if ($gpc == 0) { $cvars['DB_USER'] = addslashes($cvars['DB_USER']); }
		$cvars['DB_PASS'] = $this->data['cfg']['cfg_db_pass'];
		if ($gpc == 0) { $cvars['DB_PASS'] = addslashes($cvars['DB_PASS']); }
		$cvars['DB_DSN'] = $this->data['cfg']['cfg_db_dsn'];
		if ($gpc == 0) { $cvars['DB_DSN'] = addslashes($cvars['DB_DSN']); }
		$cvars['DB_SCHEME'] = $this->data['cfg']['cfg_db_scheme'];
		$cvars['DB_PORT'] = $this->data['cfg']['cfg_db_port'];
		$cvars['DB_PERSISTENT'] = 0;
		$cvars['FTP'] = $this->data['cfg']['cfg_ftp'];
		$cvars['FTP_HOST'] = $this->data['cfg']['cfg_ftp_host'];
		$cvars['FTP_PORT'] = $this->data['cfg']['cfg_ftp_port'];
		$cvars['FTP_ROOT'] = $this->data['cfg']['cfg_ftp_root'];
		$cvars['FTP_USER'] = $this->data['cfg']['cfg_ftp_user'];
		if ($gpc == 0) { $cvars['FTP_USER'] = addslashes($cvars['FTP_USER']); }
		$cvars['FTP_PASS'] = $this->data['cfg']['cfg_ftp_pass'];
		if ($gpc == 0) { $cvars['FTP_PASS'] = addslashes($cvars['FTP_PASS']); }
		$cvars['REGISTRATION_EMAIL_DOMAIN'] = '';
		$cvars['REGISTRATION_EXCLUDE_EMAIL_DOMAINS'] = '';
		$cvars['MAIL_METHOD'] = 'mail';
		$cvars['MAIL_SMTP_HOST'] = 'mail.example.com';
		$cvars['MAIL_SMTP_USER'] = '';
		$cvars['MAIL_SMTP_PASS'] = '';
		$cvars['MAIL_SMTP_SECURE'] = '';
		$cvars['MAIL_SMTP_PORT'] = 25;
		$cvars['MAIL_SMTP_AUTH'] = 1;
		$cvars['MAIL_NAME'] = $this->data['usr']['u_firstname'].' '.$this->data['usr']['u_lastname'];
		if ($gpc == 0) { $cvars['MAIL_NAME'] = addslashes($cvars['MAIL_NAME']); }
		$cvars['MAIL_EMAIL'] = $this->data['usr']['u_email'];
		$cvars['MAIL_FROM_NAME'] = 'Elxis';
		$cvars['MAIL_FROM_EMAIL'] = $fromemail;
		$cvars['MAIL_MANAGER_NAME'] = 'Technical manager';
		$cvars['MAIL_MANAGER_EMAIL'] = $this->data['usr']['u_email'];
		$cvars['OFFLINE_MESSAGE'] = '';
		$cvars['METADESC'] = 'This site is powered by Elxis CMS. Feel the power of open source!';
		$cvars['METAKEYS'] = 'elxis, cms, open source, free, multilingual, html5, elxis.org, jquery';
		$cvars['ONLINE'] = 1;
		$cvars['DOCTYPE'] = 'html5';
		$cvars['DEFAULT_ROUTE'] = 'content:/';
		$cvars['TEMPLATE'] = 'delta';
		$cvars['ATEMPLATE'] = 'iris';
		$cvars['ICONS_PACK'] = 'nautilus';
		$cvars['STATISTICS'] = 1;
		$cvars['GZIP'] = 0;
		$cvars['MULTILINGUISM'] = 0;
		$cvars['CACHE'] = 0;
		$cvars['CACHE_TIME'] = 1800;
		$cvars['APC'] = 0;
		$cvars['APCID'] = rand(1000, 9999);
		$cvars['REALNAME'] = 1;
		$cvars['REGISTRATION'] = 1;
		$cvars['REGISTRATION_ACTIVATION'] = 1;
		$cvars['PASS_RECOVER'] = 1;
		$cvars['SESSION_LIFETIME'] = 900;
		$cvars['SESSION_MATCHIP'] = 0;
		$cvars['SESSION_MATCHBROWSER'] = 1;
		$cvars['SESSION_MATCHREFERER'] = 0;
		$cvars['SESSION_ENCRYPT'] = 0;
		$cvars['SECURITY_LEVEL'] = 0;
		$cvars['SESSION_HANDLER'] = 'database';
		$cvars['SSL'] = 0;
		$cvars['ERROR_REPORT'] = 0;
		$cvars['ERROR_LOG'] = 1;
		$cvars['ERROR_ALERT'] = 0;
		$cvars['LOG_ROTATE'] = 1;
		$cvars['DEBUG'] = 0;
		$cvars['MINICSS'] = 0;
		$cvars['MINIJS'] = 0;
		$cvars['MOBILE'] = 1;
		$cvars['DEFENDER'] = 'GC';
		$cvars['TIMEZONE'] = $timezone;
		$cvars['SEF'] = $this->data['cfg']['cfg_sef'];

		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$this->data['usr']['u_uname']._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= 'class elxisConfig {'._LEND._LEND;
		foreach ($cvars as $key => $val) {
			if (is_int($val)) {
				$out .= "\t".'private $'.$key.' = '.$val.';'._LEND;
			} else {
				$out .= "\t".'private $'.$key.' = \''.$val.'\';'._LEND;
			}
		}
		$out .= _LEND;
		$out .= "\t".'public function __construct() {'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function get($var=\'\') {'._LEND;
		$out .= "\t\t".'if (($var != \'\') && isset($this->$var)) { return $this->$var; }'._LEND;
		$out .= "\t\t".'return \'\';'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function set($var, $value) {'._LEND;
		$out .= "\t\t".'if (($var == \'\') || (!is_string($var))) { return false; }'._LEND;
		$out .= "\t\t".'if (isset($this->$var)) {'._LEND;
		$out .= "\t\t\t".'if (!in_array($var, array(\'SITENAME\', \'METADESC\', \'METAKEYS\'))) { return false; }'._LEND;
		$out .= "\t\t".'}'._LEND;
		$out .= "\t\t".'$this->$var = $value;'._LEND;
		$out .= "\t\t".'return true;'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= '}'._LEND._LEND;
		$out .= '?>';

		$this->data['final']['save'] = false;
		$this->data['final']['renhtaccess'] = ($cvars['SEF'] == 1) ? 0 : -1;
		$this->data['final']['config'] = $out;
		if ($handle = @fopen(ELXIS_PATH.'/configuration.php', 'w')) {
			$bytes = @fwrite($handle, $out);
			if ($bytes) { $this->data['final']['save'] = true; }
            fclose($handle);
            if (file_exists(ELXIS_PATH.'/configuration_sample.php')) {
            	@unlink(ELXIS_PATH.'/configuration_sample.php');
            }

			if ($cvars['SEF'] == 1) {
				if (file_exists(ELXIS_PATH.'/htaccess.txt')) {
					if (file_exists(ELXIS_PATH.'/.htaccess')) {
						@unlink(ELXIS_PATH.'/.htaccess');
					}
					$ok = @rename(ELXIS_PATH.'/htaccess.txt', ELXIS_PATH.'/.htaccess');
					$this->data['final']['renhtaccess'] = $ok ? 1 : 0;
				}
  			}
        }

		if ($this->data['final']['save'] === false) {
			if ($this->data['cfg']['cfg_ftp'] == 1) {
				$ftp_root = rtrim($this->data['cfg']['cfg_ftp_root'], '/');
				if ($ftp_root == '') { $ftp_root = '/'; }
				$repo_path = rtrim($this->data['cfg']['cfg_repo_path'], '/');
				if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
				$site_root = preg_replace('#('.$ftp_root.')$#', '', ELXIS_PATH);
				$ftp_root_repo = preg_replace('#^('.$site_root.')#', '', $repo_path);

				if ($this->data['cfg']['cfg_ftp_port'] > 0) {
					$conn_id = @ftp_connect($this->data['cfg']['cfg_ftp_host'], $this->data['cfg']['cfg_ftp_port']);
				} else {
					$conn_id = @ftp_connect($this->data['cfg']['cfg_ftp_host']);
				}

				if ($conn_id) {
					$login_result = @ftp_login($conn_id, $this->data['cfg']['cfg_ftp_user'], $this->data['cfg']['cfg_ftp_pass']);
					if ($login_result) {
						$tmpFile = $repo_path.'/tmp/configuration.php';
						if (@file_put_contents($tmpFile, $this->data['final']['config'])) {
							$ftppath = $ftp_root.'/configuration.php';
							$upload = ftp_put($conn_id, $ftppath, $tmpFile, FTP_BINARY);
							if ($upload) {
								$this->data['final']['save'] = true;
								if (file_exists(ELXIS_PATH.'/configuration_sample.php')) {
									@ftp_delete($conn_id, $ftp_root.'/configuration_sample.php');
								}

								if ($cvars['SEF'] == 1) {
									if (file_exists(ELXIS_PATH.'/htaccess.txt')) {
										if (file_exists(ELXIS_PATH.'/.htaccess')) {
											@ftp_delete($conn_id, $ftp_root.'/.htaccess');
										}
										$ok = @ftp_rename($conn_id, $ftp_root.'/htaccess.txt', $ftp_root.'/.htaccess');
										$this->data['final']['renhtaccess'] = $ok ? 1 : 0;
									}
								}
							}
						}
						@unlink($tmpFile);
					}
					ftp_close($conn_id);
				}
			}
		}
	}


	/**************************/
	/* MAKE A RANDOM USERNAME */
	/**************************/
	public function makeUname() {
		$greek = array('zeus', 'hermes', 'apollo', 'athena', 'poseidon', 'hades', 'cronus', 'erebos', 'chaos', 
		'uranus', 'tartarus', 'iapetos', 'atlas', 'prometheus', 'gaia', 'talos', 'typhon', 'phobos', 'cerberus',
		'medusa', 'proteus', 'triton', 'pandora', 'electra', 'nestor', 'pythagoras', 'socrates', 'archimedes', 'hector', 
		'ajax', 'theseus', 'orpheus', 'cadmus', 'anaxagoras', 'protagoras', 'hypatia', 'homer');
		$italian = array('flora', 'janus', 'juno', 'mars', 'mercury', 'pluto', 'saturn', 'venus', 'vulcan', 
		'vesta', 'minerva', 'fauna', 'diana', 'aurora', 'luna', 'hercules', 'augustus', 'caligula', 'tiberius', 
		'titus', 'magnus', 'severus', 'claudius', 'cicero', 'seneca');
		$german = array('odin', 'loki', 'thor', 'balder', 'njord', 'buri', 'seth', 'freya', 'freyr', 'midgard', 'valhalla', 
		'hesus', 'fornjot', 'druden', 'donar', 'alfadir', 'picullus');
		$inter = array('helix', 'acropolis', 'analysis', 'genesis', 'eureka', 'abyss', 'enigma', 'amazon', 
		'anax', 'asterisk', 'helios', 'nectar', 'utopia', 'paradox', 'alpha', 'delta', 'epsilon', 'sigma', 'omega', 'olympus', 'pilot', 'harmony', 
		'acrobat', 'astronaut', 'captain', 'cosmos', 'discus', 'hypnosis', 'logic', 'micro', 'neutron', 'electron', 'proton', 
		'photon', 'neuron', 'oasis', 'panic', 'phoenix', 'planet', 'python', 'thesis', 'typhoon', 'android', 'energy', 
		'oxygen', 'bios', 'logos');

		$f = rand(0, 9);
		if ($this->langInfo('LANGUAGE') == 'el') {
			shuffle($greek);
			return $greek[$f];
		} else if ($this->langInfo('LANGUAGE') == 'it') {
			shuffle($italian);
			return $italian[$f];
		} else if ($this->langInfo('LANGUAGE') == 'de') {
			shuffle($german);
			return $german[$f];
		} else {
			$arr = array_merge($greek, $inter);
			shuffle($arr);
			return $arr[$f];
		}
	}


	/************************************/
	/* GET PROPER TIMEZONE FOR A REGION */
	/************************************/
	private function getTzone($region) {
		switch ($region) {
			case 'EG': $tzone = 'Africa/Cairo'; break;
			case 'AU': $tzone = 'Australia/Melbourne'; break;
			case 'RS': $tzone = 'Europe/Belgrade'; break;
			case 'DE': $tzone = 'Europe/Berlin'; break;
			case 'PT': $tzone = 'Europe/Lisbon'; break;
			case 'CY': $tzone = 'Europe/Nicosia'; break;
			case 'IT': $tzone = 'Europe/Rome'; break;
			case 'ES': $tzone = 'Europe/Madrid'; break;
			case 'BG': $tzone = 'Europe/Sofia'; break;
			case 'CZ': $tzone = 'Europe/Prague'; break;
			case 'GR': $tzone = 'Europe/Athens'; break;
			case 'CY': $tzone = 'Europe/Nicosia'; break;
			case 'PL': $tzone = 'Europe/Warsaw'; break;
			case 'FR': $tzone = 'Europe/Paris'; break;
			case 'NO': $tzone = 'Europe/Oslo'; break;
			case 'HU': $tzone = 'Europe/Budapest'; break;
			case 'DK': $tzone = 'Europe/Copenhagen'; break;
			case 'UA': $tzone = 'Europe/Kiev'; break;
			case 'RU': $tzone = 'Europe/Moscow'; break;
			case 'AL': $tzone = 'Europe/Tirane'; break;
			case 'EE': $tzone = 'Europe/Tallinn'; break;
			case 'LV': $tzone = 'Europe/Riga'; break;
			case 'NL': $tzone = 'Europe/Amsterdam'; break;
			case 'FI': $tzone = 'Europe/Helsinki'; break;
			case 'SI': $tzone = 'Europe/Ljubljana'; break;
			case 'SK': $tzone = 'Europe/Bratislava'; break;
			case 'RO': $tzone = 'Europe/Bucharest'; break;
			case 'BE': $tzone = 'Europe/Brussels'; break;
			case 'HR': $tzone = 'Europe/Zagreb'; break;
			case 'LT': $tzone = 'Europe/Vilnius'; break;
			case 'SE': $tzone = 'Europe/Stockholm'; break;
			case 'UK': case 'GB': case 'IS': case 'IE': $tzone = 'Europe/London'; break;
			case 'BA': $tzone = 'Europe/Sarajevo'; break;
			case 'MK': $tzone = 'Europe/Skopje'; break; //macedonia is Greek
			case 'IL': $tzone = 'Asia/Tel_Aviv'; break;
			case 'JP': $tzone = 'Asia/Tokyo'; break;
			case 'TR': $tzone = 'Asia/Istanbul'; break;
			case 'IN': $tzone = 'Asia/Calcutta'; break;
			case 'IR': $tzone = 'Asia/Tehran'; break;
			case 'ZN': $tzone = 'Asia/Shanghai'; break;
			case 'CA': $tzone = 'America/Toronto'; break;
			case 'AR': $tzone = 'America/Buenos_Aires'; break;
			case 'KR': $tzone = 'Asia/Seoul'; break;
			case 'GE': $tzone = 'Asia/Tbilisi'; break;
			case 'MX': $tzone = 'America/Mexico_City'; break;
			case 'US': $tzone = 'America/New_York'; break;
			case 'ZA': $tzone = 'Africa/Lusaka'; break;
			case 'BR': $tzone = 'America/Sao_Paulo'; break;
			default: $tzone = ''; break;
		}
		return $tzone;
	}

}

?>