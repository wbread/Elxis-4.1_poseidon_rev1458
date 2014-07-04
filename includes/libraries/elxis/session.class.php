<?php 
/**
* @version		$Id: session.class.php 1147 2012-05-20 19:27:10Z datahell $
* @package		Elxis
* @subpackage	Session
* @copyright	Copyright (c) 2006-2012 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisSession {

	private $session_name = 'PHPSESSID';
	private $session_id = '';
	private $crypt = null; //elxis crypt object (if encryption is enabled)
	private $user_agent = null;
	private $ip_address = null;
	private $session_handler = 'none';
	private $handler = null; //session handler object
	private $expire = 900; //Maximum age of unused session in seconds
	private $matchip = 0;
	private $matchbrowser = 1;
	private $matchreferer = 0;
	private $encrypt = 0;
	private $security_level = 0;
	private $now = 0; //current timestamp
	private $save_path = '';
	private $cookie_domain = 'localhost';
	private $cookie_path = '/';
	private $cookie_secure = false;
	private $cookie_httponly = false;
	private $state = 'active'; //internal state: one of 'active'|'expired'|'destroyed|'error'


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($options = array()) {
		$elxis = eFactory::getElxis();
		$this->session_handler = $elxis->getConfig('SESSION_HANDLER');
		$this->expire = (int)$elxis->getConfig('SESSION_LIFETIME');
		$this->matchip = (int)$elxis->getConfig('SESSION_MATCHIP');
		$this->matchbrowser = (int)$elxis->getConfig('SESSION_MATCHBROWSER');
		$this->matchreferer = (int)$elxis->getConfig('SESSION_MATCHREFERER');
		$this->encrypt = (int)$elxis->getConfig('SESSION_ENCRYPT');
		$this->security_level = (int)$elxis->getConfig('SECURITY_LEVEL');
		$this->now = eFactory::getDate()->getTS();
		$parsed = parse_url($elxis->getConfig('URL').'/');
		if (defined('ELXIS_ADMIN') && ($elxis->getConfig('SSL') >= 1)) {
			$this->cookie_secure = true;
		} else {
			$this->cookie_secure = (strtolower($parsed['scheme']) == 'https') ? true : false;
		}
		$this->cookie_domain = $parsed['host'];
		$this->cookie_path = (isset($parsed['path']) && ($parsed['path'] != '')) ? $parsed['path'] : '/';
		unset($parsed);
		if ($this->security_level > 0) {
			$this->cookie_httponly = true;
			if ($this->security_level > 1) {
				$this->matchbrowser = 1;
				$this->encrypt = 1;
			}
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$this->save_path = $repo_path.'/sessions/';
		if ($this->session_handler == 'files') {
			if (!file_exists($this->save_path) || !is_dir($this->save_path)) {
				trigger_error('Session save path '.$this->save_path.' does not exist!', E_USER_ERROR);
			}
			if (!is_writable($this->save_path)) {
				trigger_error('Session save path '.$this->save_path.' is not writable!', E_USER_ERROR);
			}
		}
		if ($this->session_handler == 'none') { $this->encrypt = 0; }
		if ($this->encrypt == 1) { $this->crypt = $elxis->obj('crypt'); }

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->user_agent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		}
		$this->ip_address = $elxis->obj('IP')->clientIP();

		if (session_id()) {
			session_unset();
			session_destroy();
		}

		ini_set('session.save_handler', 'files');
		ini_set('session.use_trans_sid', '0');
		ini_set('session.use_cookies', '1');
		ini_set('session.use_only_cookies', '1');
		if ($this->session_handler == 'files') {
			ini_set('session.save_path', $this->save_path);
		}
		ini_set('session.gc_maxlifetime', $elxis->getConfig('SESSION_LIFETIME'));
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 100);
		ini_set('session.hash_function', 1);
		ini_set('session.hash_bits_per_character', 6);
		$mins = (int)($elxis->getConfig('SESSION_LIFETIME') / 60);
		session_cache_expire($mins);
		session_cache_limiter(false);

		if (isset($options['encname'])) {
			$this->session_name = $options['encname'];
		} elseif (isset($options['name'])) {
			$this->session_name = $this->getHash($options['name']);
		} else {
			if (trim($this->session_name == '')) { $this->session_name = 'elxissessid'; }
		}
		session_name($this->session_name);
		if (isset($options['id'])) { session_id($options['id']); }

		session_set_cookie_params(
			$elxis->getConfig('SESSION_LIFETIME'),
			$this->cookie_path,
			$this->cookie_domain,
			$this->cookie_secure,
			$this->cookie_httponly
		);

		$this->setSaveHandler();
		$this->start();
		$this->setTimers();
		$this->state = 'active';
		$this->validate();

		//we need to do this in order to change the session expiry time every time the user clicks a page, else the expire 
		//time set by session_set_cookie_params will not refresh even if the user clicks various pages
		//Check if you need to also put this in the refresh and other functions
		setcookie(session_name(),session_id(),time() + $this->expire, $this->cookie_path, $this->cookie_domain);
	}


	/*****************/
	/* GET SEED HASH */
	/*****************/
	private function getHash($seed) {
		$string = eFactory::getElxis()->getConfig('SECRET').$seed;
		return sha1($string);
	}


	/*******************/
	/* START A SESSION */
	/*******************/
	private function start() {
		if ($this->state == 'restart') {
			session_id($this->createId());
		}
		session_cache_limiter(false);
		session_start();
		header('P3P: CP="NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM"');
		return true;
	}


	/***********************/
	/* CREATE A SESSION ID */
	/***********************/
	private function createId() {
		$id = 0;
		while (strlen($id) < 40) {
			$id .= mt_rand(0, mt_getrandmax());
		}
		return $this->getHash(uniqid($id, true));
	}


	/******************/
	/* GET SESSION ID */
	/******************/
	public function getId() {
		if ($this->state == 'destroyed') { return null; }
		return session_id();
	}
	

	/********************/
	/* GET SESSION DATA */
	/********************/
	public function get($name, $default=null, $namespace='elxis') {
		if (($this->state != 'active') && ($this->state !== 'expired')) {
			trigger_error('Tried to access session '.$name.' but session is not initialized!', E_USER_WARNING); 
			return null;
		}
		$namespace = (trim($namespace) == '') ? 'elxis' : $namespace;
		return (isset($_SESSION[$namespace][$name])) ? $_SESSION[$namespace][$name] : $default;
	}


	/********************/
	/* SET SESSION DATA */
	/********************/
	public function set($name, $value=null, $namespace='elxis') {
		if ($this->state != 'active') {
			trigger_error('Tried to set a value for session '.$name.' but session is not active!', E_USER_WARNING);
			return null;
		}
		$namespace = (trim($namespace) == '') ? 'elxis' : $namespace;
		$v = isset($_SESSION[$namespace][$name]) ?  $_SESSION[$namespace][$name] : null;
		if ($value === null) {
			if (isset($_SESSION[$namespace][$name])) { unset($_SESSION[$namespace][$name]); }
		} else {
			if (!isset($_SESSION[$namespace])) { $_SESSION[$namespace] = array(); }
			$_SESSION[$namespace][$name] = $value;
		}
		return $v;
	}


	/********************************/
	/* CHECK IF SESSION DATA EXISTS */
	/********************************/
	private function is_set($name, $namespace='elxis') {
		if ($this->state != 'active') {
			trigger_error('Tried to check if session '.$name.' exists but session is not active!', E_USER_NOTICE);
			return null;
		}
		$namespace = (trim($namespace) == '') ? 'elxis' : $namespace;
		return isset($_SESSION[$namespace][$name]);
	}


	/**********************/
	/* SET SESSION TIMERS */
	/**********************/
	private function setTimers() {
		if( !$this->is_set('session_init')) {
			$this->set('session_init', $this->now);
			$this->set('session_previous', $this->now);
			$this->set('session_now', $this->now);
		} else {
			$this->set('session_previous', $this->get('session_now', $this->now));
			$this->set('session_now', $this->now);
		}
		return true;
	}


	/***************************/
	/* UNSET DATA FROM SESSION */
	/***************************/
	private function clear($name, $namespace='elxis') {
		$namespace = (trim($namespace) == '') ? 'elxis' : $namespace;
		if (isset($_SESSION[$namespace][$name])) {
			unset($_SESSION[$namespace][$name]);
		}
		return true;
	}


	/********************/
	/* VALIDATE SESSION */
	/********************/
	private function validate($restart = false) {
		if ($restart) {
			$this->state = 'active';
			$this->set('session_ip', null);
			$this->set('session_browser', null);
		}

		if (($this->get('session_previous', 0) + $this->expire) < $this->get('session_now', 0)) {
			$this->state = 'expired';
			return false;
		}

		if ($this->matchip) {
			if ($this->ip_address !== null) {
				$ip = $this->get('session_ip');
				if ($ip === null) {
					$this->set('session_ip', $this->ip_address);
				} else if ($ip !== $this->ip_address) {
					$this->state = 'error';
					return false;
				}
			}
		}

		if ($this->matchbrowser) {
			$browser_signature = '';
			$keys = array('HTTP_USER_AGENT', 'HTTP_ACCEPT_CHARSET', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE');
			foreach ($keys as $key) {
				if (isset($_SERVER[$key])) { $browser_signature .= $_SERVER[$key]; }
			}
			$browser_signature = $this->getHash($browser_signature);
			$sbsig = $this->get('session_browser');
			if ($sbsig === null) {
				$this->set('session_browser', $browser_signature);
			} else if ($sbsig !== $browser_signature) {
				$this->state = 'error';
				return false;
			}
		}
		
		if ($this->matchreferer) {
			if (isset($_SERVER['HTTP_REFERER']) && (trim($_SERVER['HTTP_REFERER']) != '')) {
				$ref = filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
				$parts = parse_url($ref);
				if ($parts && isset($parts['host'])) {
					if ($parts['host'] != $this->cookie_domain) {
						$this->state = 'error';
						return false;
					}
				}
			}
		}

		return true;
	}


	/******************************/
	/* RESTART AN EXPIRED SESSION */
	/******************************/
	public function restart() {
		$this->destroy();
		if ($this->state != 'destroyed') { return false; }
		$this->state = 'restart';
		$id	= $this->createId();
		session_id($id);
		$this->setSaveHandler();
		$this->start();
		$this->state = 'active';
		$this->validate();
		return true;
	}


	/*********************/
	/* DESTROY A SESSION */
	/*********************/
	private function destroy() {
		if ($this->state == 'destroyed') { return true; }
		$sess_name = session_name();
		if ((string)$sess_name != '') {
			if (isset($_COOKIE[$sess_name])) {
				$t = $this->now - 2592000;
				setcookie($sess_name, '', $t, $this->cookie_path, $this->cookie_domain);
			}
		}
		session_unset();
		session_destroy();
		$this->state = 'destroyed';
		return true;
	}


	/**************************/
	/* REGENERATE THE SESSION */
	/**************************/
	public function regenerate() {
		if ($this->state != 'active') { return false; }
		session_regenerate_id(true);
		$this->session_id = session_id();
		return true;
	}


	/*********************/
	/* GET SESSION STATE */
	/*********************/
	public function getState() {
		return $this->state;
	}


	/**********************************/
	/* GET EXPIRATION TIME IN SECONDS */
	/**********************************/
    public function getExpire() {
		return $this->expire;
    }


	/******************/
	/* GET USER AGENT */
	/******************/
    public function getUA() {
		return $this->user_agent;
    }


	/******************/
	/* GET IP ADDRESS */
	/******************/
    public function getIP() {
		return $this->ip_address;
    }


	/*****************/
	/* GET IP DOMAIN */
	/*****************/
    public function getCookieDomain() {
		return $this->cookie_domain;
    }


	/*******************/
	/* GET COOKIE PATH */
	/*******************/
    public function getCookiePath() {
		return $this->cookie_path;
    }

		
/*********************  SESSION STORAGE *************************/


	/****************************/
	/* SET SESSION SAVE HANDLER */
	/****************************/
	private function setSaveHandler() {
		if ($this->session_handler == 'none') { return; }
		session_set_save_handler(
			array($this, $this->session_handler.'_open'),
			array($this, $this->session_handler.'_close'),
			array($this, $this->session_handler.'_read'),
			array($this, $this->session_handler.'_write'),
			array($this, $this->session_handler.'_destroy'),
			array($this, $this->session_handler.'_gc')
		);
	}


	public function database_open($save_path, $session_name) {
		return true;
	}


	public function database_close() {
		return true;
	}

	public function database_read($id) {
		$row = new sessionDbTable();
		if ($row->load($id)) {
			$row->last_activity = $this->now;
			$row->store();
			$sess_data = ($this->encrypt == 1) ? $this->crypt->decrypt($row->session_data) : $row->session_data;
		} else {
			$row->session_id = $id;
			$row->ip_address = $this->ip_address;
			$row->user_agent = $this->user_agent;
			$row->session_data = null;
			$row->store();
			$sess_data = '';
		}
		return (string)$sess_data;

	}


	public function database_write($id, $sess_data) {
		$sess_data = ($this->encrypt == 1) ? $this->crypt->encrypt($sess_data) : $sess_data;
		$row = new sessionDbTable();
		if (!$row->load($id)) {
			$row->session_id = $id;
			$row->ip_address = $this->ip_address;
			$row->user_agent = $this->user_agent;
		}

		$row->last_activity = $this->now;
		$row->session_data = $sess_data;
		return $row->store();
	}


	public function database_destroy($id) {
		$db = eFactory::getDB();
		$sql = "DELETE FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('session_id')." = :sessid";
		$stmt = eFactory::getDB()->prepare($sql);
		$stmt->bindParam(':sessid', $id, PDO::PARAM_STR, strlen($id));
		$ok = $stmt->execute() ? true : false;
		return $ok;
	}


	public function database_gc($maxlifetime) {
		$row = new sessionDbTable();
		$row->purge($maxlifetime);
		return true;
	}


	public function files_open($save_path, $session_name) {
		return true;
	}


	public function files_close() {
		return true;
	}


	public function files_read($id) {
		if (!file_exists($this->save_path.'sess_'.$id)) { return ''; }
		$sess_data = (string)file_get_contents($this->save_path.'sess_'.$id);
		return ($this->encrypt == 1) ? $this->crypt->decrypt($sess_data) : $sess_data;
	}


	public function files_write($id, $sess_data) {
		if ($fp = @fopen($this->save_path.'sess_'.$id, "w")) {
			$sess_data = ($this->encrypt == 1) ? $this->crypt->encrypt($sess_data) : $sess_data;
			$return = fwrite($fp, $sess_data);
			fclose($fp);
			return $return;
		} else {
			return false;
		}
	}


	public function files_destroy($id) {
		if (!file_exists($this->save_path.'sess_'.$id)) { return true; }		
		$ok = @unlink($this->save_path.'sess_'.$id);
		return $ok;
	}


	public function files_gc($maxlifetime) {
		$files = glob($this->save_path.'sess_*');
		if ($files) {
			foreach ($files as $file) {
				if ((filemtime($file) + $maxlifetime) < $this->now) {
					@unlink($file);
				}
			}
		}
		return true;
	}


	/*****************/
	/* DISABLE CLONE */
	/*****************/
    private function  __clone() {
        trigger_error('Session cloning is not allowed', E_USER_ERROR);
    }

}

?>