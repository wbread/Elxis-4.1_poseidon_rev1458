<?php 
/**
* @version		$Id: framework.class.php 1455 2013-07-23 16:02:33Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2013 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisFramework {

	private $cfg = null;
	private $version = array();
	private $_session = null; //session db object
	private $_user = null; //user db object
	private $_acl = null; //ACL class instance


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->loadConfig();
		include(ELXIS_PATH.'/includes/version.php');
		$this->version = $elxis_version;
		$this->applySecurityPolicy();
	}
	

	/******************************************/
	/* PRIMITIVE APPLIANCE OF SECURITY POLICY */
	/******************************************/
	private function applySecurityPolicy() {
		if ((int)$this->cfg->get('SECURITY_LEVEL') > 1) {
			$ok = true;
			if ($this->cfg->get('REPO_PATH') == '') {
				$ok = false;
			} else {
				$ok = (stripos($this->cfg->get('REPO_PATH'), ELXIS_PATH) !== false) ? false : true;
			}
			if (!$ok) {
				$msg = 'Current Elxis security policy requires you to place the reposity folder above the www root folder!';
				exitPage::make('security', 'ELXIS-0001', $msg);
			}
		}
	}


	/**********************/
	/* INITIALIZE SESSION */
	/**********************/
	public function initSession($name=null) {
		$options = array();
		if ($name !== null) {
			$options['encname'] = $name;
		} else {
			$options['name'] = 'elxissessid';
		}

		$db = eFactory::getDB(); //make sure db connection is open
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/session.db.php');
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/users.db.php');
		elxisLoader::loadFile('includes/libraries/elxis/acl.class.php');

		$eSession = eFactory::getSession($options);
		if ($eSession->getState() !== 'active') {
			if ($eSession->getState() == 'error') { //validation error, create new session
				$eSession->restart();
				if ($eSession->getState() !== 'active') {
					exitPage::make('fatal', 'ELXIS-0002', 'Could not initialize session!');
				}
			} else {
				exitPage::make('fatal', 'ELXIS-0003', 'Could not initialize session!');
			}
		}

		$this->_session = new sessionDbTable();
		if (rand(1, 10) === 5) {
			$this->_session->purge($eSession->getExpire());
		}
		$sessid = $eSession->getId();

		if ($this->_session->load($sessid)) {
			$this->_session->refresh();
		} else {
			$this->_session->session_id = $sessid;
			$this->_session->user_agent = $eSession->getUA();
			$this->_session->ip_address = $eSession->getIP();
			$this->_session->clicks = 1;
			if (!$this->_session->insert()) {
				$msg = 'Could not save session! '.$this->_session->getErrorMsg();
				exitPage::make('fatal', 'ELXIS-0003', $msg);
			}
		}

		$tz = (string)$eSession->get('timezone');
		if ($tz != '') {
			if (eFactory::getDate()->setTimezone($tz) === false) { $tz = ''; }
		}

		$cookie_login = true;
		$this->_user = new usersDbTable();
		$this->_user->uid = $this->_session->uid;
		$this->_user->gid = (int)$this->_session->gid;
		$this->_user->timezone = $tz;
		if ((int)$this->_session->uid > 0) {
			$cookie_login = false;
			if ($this->_user->load($this->_session->uid)) {
				if ($this->_user->block > 0) { //just in case the user blocked during its session
					$this->_user = new usersDbTable();
					$this->_user->uid = 0;
					$this->_user->gid = 7;
					$this->_user->timezone = $tz;
					$this->_session->uid = 0;
					$this->_session->gid = 7;
				} else {
					$this->_user->lastvisitdate = eFactory::getDate()->getDate();
					if ($tz != '') {
						$this->_user->timezone = $tz;
					} else if (trim($this->_user->timezone) != '') {
						eFactory::getDate()->setTimezone($this->_user->timezone);
					}
					$this->_user->update();
				}
			} else {
				$this->_session->uid = 0;
				$this->_session->gid = 7;
				$this->_user->uid = 0;
				$this->_user->gid = 7;
			}
		} else if ((int)$this->_session->gid <> 7) {
			$cookie_login = false;
			$this->_user->groupname = 'External user';
			$this->_user->firstname = (string)$eSession->get('firstname');
			$this->_user->lastname = (string)$eSession->get('lastname');
			$this->_user->uname = (string)$eSession->get('uname');
			$this->_user->email = (string)$eSession->get('email');
			$this->_user->avatar = (string)$eSession->get('avatar');
		}

		if (($cookie_login == false) && ($this->getConfig('SECURITY_LEVEL') > 0)) { //regenerate session for already logged in users
			if ($eSession->regenerate()) {
				$this->_session->session_id = $eSession->getId();
				$this->_session->forceNew();
				$this->_session->store();
			}
		}

		$this->_acl = new elxisACL();
		$this->_acl->load($this->_user->uid, $this->_user->gid);

		if ($cookie_login) {
			$cookie_name = md5('remember');
			if (isset($_COOKIE[$cookie_name])) {
				$cookie_data_enc = trim(filter_input(INPUT_COOKIE, $cookie_name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
				if ($cookie_data_enc != '') {
					$key = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '';
					$cookie_data_ser = $this->obj('crypt')->decrypt($cookie_data_enc, $key);
					if ($cookie_data_ser !== false) {
						/** if user has switched user agent then the decryption key is wrong and the resulted $cookie_data_ser 
						is also wrong. To avoid php notice on serialize we check if the $cookie_data_ser is a valid serialized 
						string by checking if the "uname" string exists in it else we delete the cookie. */
						if (intval(stripos($cookie_data_ser, 'uname')) > 0) {
							$cookie_data = @unserialize($cookie_data_ser);
						} else {
							$cookie_data = array();
							@setcookie($cookie_name, '', time() - 3000, $eSession->getCookiePath(), $eSession->getCookieDomain());
						}
						if (is_array($cookie_data) && isset($cookie_data['uname']) && isset($cookie_data['pword']) && isset($cookie_data['auth_method']) && ($cookie_data['auth_method'] == 'elxis')) {
							$options = array('auth_method' => $cookie_data['auth_method'], 'uname' => $cookie_data['uname'], 'pword' => $cookie_data['pword']);
							$this->login($options);
						}
					}
				}
			}
		}
	}


	//-----------------------------------------------//


	/****************************/
	/* LOAD ELXIS CONFIGURATION */
	/****************************/
	private function loadConfig() {
		elxisLoader::loadFile('configuration.php');
		$this->cfg = new elxisConfig();
	}


	/********************************/
	/* GET A CONFIGURATION VARIABLE */
	/********************************/
	public function getConfig($var='') {
		return $this->cfg->get($var);
	}


	/**************************************************/
	/* ALIAS OF getConfig FOR BACKWARDS COMPATIBILITY */
	/**************************************************/
	public function getCfg($var='') {
		return $this->getConfig($var);
	}


	/***************************************************************/
	/* SET A NEW CONFIGURATION VARIABLE (OVERWRITE IS NOT ALLOWED) */
	/***************************************************************/
	public function setConfig($var, $value) {
		return $this->cfg->set($var, $value);
	}


	/*************************/
	/* SEND EMAIL MESSAGE(S) */
	/*************************/
	public function sendmail($subject, $body, $alt_body='', $attachment=null, $type='plain', $to=null, $cc=null, $bcc=null, $from=null, $priority=3, $debug=false) {
		require_once(ELXIS_PATH.'/includes/libraries/swift/swift_required.php');
		if (($type == '') || ($type != 'html')) {
			$type = 'text/plain';
		} else {
			$type = 'text/html';
		}
		if ($alt_body != '') { $type = 'text/html'; }

		$message = Swift_Message::newInstance();
		$message->setCharset('UTF-8');
		$message->setPriority($priority);
		$message->setSubject($subject);
		$message->setBody($body, $type);
		if ($alt_body != '') {
			$message->addPart($alt_body, 'text/plain');
		}
		
		if (is_array($attachment) && (count($attachment) > 0)) {
			foreach ($attachment as $file) {
				if (file_exists($file)) {
					$message->attach(Swift_Attachment::fromPath($file));
				}
			}
		} elseif (is_string($attachment) && ($attachment != '')) {
			if (file_exists($attachment)) {
				$message->attach(Swift_Attachment::fromPath($attachment));
			}
		}

		$send_batch = false;
		if (is_array($to) && (count($to) > 0)) {
			if (count($to) > 1) { $send_batch = true; }
			foreach ($to as $recipient) {
				$parts = preg_split('/\,/', $recipient, 2, PREG_SPLIT_NO_EMPTY);
				if (isset($parts[1])) {
					$message->addTo($parts[0], $parts[1]);
				} else {
					$message->addTo($parts[0]);
				}
			}
		} elseif (is_string($to) && ($to != '')) {
			$parts = preg_split('/\,/', $to, 2, PREG_SPLIT_NO_EMPTY);
			if (isset($parts[1])) {
				$message->addTo($parts[0], $parts[1]);
			} else {
				$message->addTo($parts[0]);
			}
		} else {
			$message->addTo($this->getConfig('MAIL_EMAIL'), $this->getConfig('MAIL_NAME'));
		}

		if (is_array($cc) && (count($cc) > 0)) {
			foreach ($cc as $recipient) {
				$parts = preg_split('/\,/', $recipient, 2, PREG_SPLIT_NO_EMPTY);
				if (isset($parts[1])) {
					$message->addCc($parts[0], $parts[1]);
				} else {
					$message->addCc($parts[0]);
				}
			}
		} elseif (is_string($cc) && ($cc != '')) {
			$parts = preg_split('/\,/', $cc, 2, PREG_SPLIT_NO_EMPTY);
			if (isset($parts[1])) {
				$message->addCc($parts[0], $parts[1]);
			} else {
				$message->addCc($parts[0]);
			}
		}

		if (is_array($bcc) && (count($bcc) > 0)) {
			foreach ($bcc as $recipient) {
				$parts = preg_split('/\,/', $recipient, 2, PREG_SPLIT_NO_EMPTY);
				if (isset($parts[1])) {
					$message->addBcc($parts[0], $parts[1]);
				} else {
					$message->addBcc($parts[0]);
				}
			}
		} elseif (is_string($bcc) && ($bcc != '')) {
			$parts = preg_split('/\,/', $bcc, 2, PREG_SPLIT_NO_EMPTY);
			if (isset($parts[1])) {
				$message->addBcc($parts[0], $parts[1]);
			} else {
				$message->addBcc($parts[0]);
			}
		}

		if (is_string($from) && ($from != '')) {
			$parts = preg_split('/\,/', $from, 2, PREG_SPLIT_NO_EMPTY);
			if (isset($parts[1])) {
				$message->setFrom(array($parts[0] => $parts[1]));
			} else {
				$message->setFrom($parts[0]);
			}
		} else {
			$message->setFrom(array($this->getConfig('MAIL_FROM_EMAIL') => $this->getConfig('MAIL_FROM_NAME')));
		}

		$headers = $message->getHeaders();
		$headers->addTextHeader('X-Mailer', 'Elxis');

		if ($debug) {
			return $message->toString();
		}

		switch ($this->getConfig('MAIL_METHOD')) {
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance(
					$this->getConfig('MAIL_SMTP_HOST'), 
					$this->getConfig('MAIL_SMTP_PORT'),
					$this->getConfig('MAIL_SMTP_SECURE')
				);
				if ($this->getConfig('MAIL_SMTP_AUTH') == 1) {
					$transport->setUsername($this->getConfig('MAIL_SMTP_USER'));
					$transport->setPassword($this->getConfig('MAIL_SMTP_PASS'));
				}
			break;
			case 'sendmail':
				//change to: newInstance('/usr/sbin/sendmail -bs'); or any other path if you use different settings
				$transport = Swift_SendmailTransport::newInstance();
			break;
			case 'mail': default:
				$transport = Swift_MailTransport::newInstance();
			break;
		}

		$mailer = Swift_Mailer::newInstance($transport);
		if ($send_batch) {
			$result = $mailer->batchSend($message);
		} else {
			$result = $mailer->send($message);
		}
		return $result;
	}


	/***********************************************/
	/* COMPOSE URL FROM ELXIS URI FORMATTED STRING */
	/***********************************************/
	public function makeURL($elxis_uri='', $file='', $forcessl=false, $xhtml=true) {
		$eURI = eFactory::getURI();
		return $eURI->makeURL($elxis_uri, $file, $forcessl, $xhtml);
	}


	/*****************************************************/
	/* COMPOSE ADMIN URL FROM ELXIS URI FORMATTED STRING */
	/*****************************************************/
	public function makeAURL($elxis_uri='', $file='', $forcessl=false, $xhtml=true) {
		$eURI = eFactory::getURI();
		return $eURI->makeURL($elxis_uri, $file, $forcessl, $xhtml, true);
	}


	/*********************/
	/* GET ELXIS VERSION */
	/*********************/
	public function getVersion() {
		return $this->version['RELEASE'].'.'.$this->version['LEVEL'];
	}


	/**************************/
	/* GET ELXIS LONG VERSION */
	/**************************/
	public function longVersion() {
		return $this->version['PRODUCT'].' '.$this->version['RELEASE'].'.'.$this->version['LEVEL'].' '
		.$this->version['STATUS'].' [ '.$this->version['CODENAME'].' ] '.$this->version['RELDATE'];
	}


	/*********************************************/
	/* GET CUSTOM INFORMATION FROM ELXIS VERSION */
	/*********************************************/
	public function fromVersion($idx='') {
		if (($idx != '') && isset($this->version[$idx])) { return $this->version[$idx]; }
		return '';
	}


	/***********************/
	/* GET ELXIS COPYRIGHT */
	/***********************/
	public function copyright() {
		return $this->version['COPYRIGHT'];
	}


	/******************************/
	/* GET ELXIS LINKED COPYRIGHT */
	/******************************/
	public function copyrightLink() {
		return $this->version['COPYRIGHTURL'];
	}


	/************************/
	/* GET ELXIS POWERED BY */
	/************************/
	public function poweredBy() {
		return $this->version['POWEREDBY'];
	}


	/**************************************/
	/* GET SITE'S BASE URL SECURE VERSION */
	/**************************************/
	public function secureBase($force=false) {
		$eURI = eFactory::getURI();
		return $eURI->secureBase($force);
	}


	/****************************/
	/* GET URL'S SECURE VERSION */
	/****************************/
	public function secureURL($url='', $force=false) {
		$eURI = eFactory::getURI();
		return $eURI->secureURL($url, $force);
	}


	/*************************************************/
	/* AN ELEGANT WAY TO INIT/GET AN OBJECT INSTANCE */
	/*************************************************/
	public function obj($name, $type='helper', $reload=false) {
		$name = ucfirst($name);
		$utype = ($type != '') ? ucfirst($type) : '';
		$className = 'elxis'.$name.$utype;
		$instance = 'e'.$name.$utype;
		if (eRegistry::isLoaded($instance)) {
			if ($reload === true) {
				eRegistry::remove($instance);
				$obj = new $className();
				eRegistry::set($obj, $instance);
				return $obj;
			} else {
				return eRegistry::get($instance);
			}
		} else {
			switch($type) {
				case 'helper':
					$file = 'includes/libraries/elxis/helpers/'.strtolower($name).'.helper.php';
				break;
				default:
					$file = 'includes/libraries/elxis/'.strtolower($name).'.class.php';
				break;
			}
			if (!elxisLoader::loadFile($file)) { return null; }
			if (!class_exists($className, false)) {
				exitPage::make('fatal', 'ELXIS-0004', 'Class '.$className.' not found!');
			}
			$obj = new $className();
			eRegistry::set($obj, $instance);
			return $obj;
		}
	}


	/********************/
	/* REDIRECT BROWSER */
	/********************/
	public function redirect($url, $msg='', $error=false) {
		if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
			$url = defined('ELXIS_ADMIN') ? $this->makeAURL() : $this->makeURL();
		}

		if (trim($msg) != '') {
			$type = ($error) ? 'elxerror' : 'elxmsg';
			$url .= (strpos($url, '?')) ? '&'.$type.'='.urlencode($msg) : '?'.$type.'='.urlencode($msg);
		}
		$url = str_replace('&amp;', '&', $url);

		if (headers_sent()) {
			echo '<script type="text/javascript">document.location.href="'.$url.'";</script>'."\n";
		} else {
			if (ob_get_length() > 0) { ob_end_clean(); }
			@header('content-type:text/html; charset=utf-8');
			@header('Location: '.$url);
		}
		exit();
	}


	/************************/
	/* GET USER DB INSTANCE */
	/************************/
	public function user() {
		return $this->_user;
	}


	/***************************/
	/* GET SESSION DB INSTANCE */
	/***************************/
	public function session() {
		return $this->_session;
	}


	/**************************/
	/* GET ACL CLASS INSTANCE */
	/**************************/
	public function acl() {
		return $this->_acl;
	}


	/**************************************************************/
	/* LOGIN USER, ON SUCCESS UPDATE SESSION, USER OBJECT AND ACL */
	/**************************************************************/
	public function login($options=array()) {
		$eSession = eFactory::getSession();

		if ($eSession->regenerate()) {
			$this->_session->session_id = $eSession->getId();
			$this->_session->forceNew();
		}

		$auth_method = isset($options['auth_method']) ? $options['auth_method'] : '';

		if (defined('ELXIS_ADMIN')) {
			$redir_url = $this->makeAURL('', '', true);
			$redir_url2 = $this->makeAURL('', '', true);
		} else {
			$redir_url = $this->makeURL('user:login/', '', true);
			if ($auth_method != 'elxis') {
				$redir_url2 = $this->makeURL('user:login/'.$auth_method.'.html', 'inner.php', true);
			} else {
				$redir_url2 = $this->makeURL('user:login/'.$auth_method.'.html', '', true);
			}
		}

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		$eAuth->setAuth($auth_method);
		if ($eAuth->getError() != '') {
			$this->redirect($redir_url, $eAuth->getError(), true);
		}

		$eAuth->authenticate($options);
		$response = $eAuth->getResponse();
		if ($response->loginsuccess !== true) {
			$this->redirect($redir_url2, $response->errormsg, true);
		}

		if ($auth_method == 'elxis') {
			$uid = (int)$response->uid;
			if (!$this->_user->load($uid)) { //just in case
				$redir_url = $this->makeURL('user:login/elxis.html', '', true);
				$this->redirect($redir_url, 'User not found!', true);
			}
			$this->_user->lastvisitdate = eFactory::getDate()->getDate();
			$this->_user->times_online++;
			$this->_user->update();
		} else {
			$this->_user->firstname = (string)$response->firstname;
			$this->_user->lastname = (string)$response->lastname;
			$this->_user->uname = (string)$response->uname;
			$this->_user->email = (string)$response->email;
			$this->_user->gid = (int)$response->gid;
			$this->_user->avatar = (string)$response->avatar;

			$eSession = eFactory::getSession();
			$eSession->set('firstname', $this->_user->firstname);
			$eSession->set('lastname', $this->_user->lastname);
			$eSession->set('uname', $this->_user->uname);
			$eSession->set('email', $this->_user->email);
			$eSession->set('avatar', $this->_user->avatar);
		}

		$this->_session->uid = (int)$response->uid;
		$this->_session->gid = (int)$response->gid;
		$this->_session->login_method = $auth_method;
		$this->_session->store();

		$this->_acl->load($this->_user->uid, $this->_user->gid);

		if (isset($options['remember']) && ((int)$options['remember'] === 1) && ($auth_method == 'elxis')) {
			$key = (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : ''; //remember in this browser only!
			$cookie_name = md5('remember');
			$cookie_data = $this->obj('crypt')->encrypt(serialize(array('uname' => $options['uname'], 'pword' => $options['pword'], 'auth_method' => $response->method)), $key);
			$expire = time() + 365*24*3600;
			$eSession = eFactory::getSession();
			setcookie($cookie_name, $cookie_data, $expire, $eSession->getCookiePath(), $eSession->getCookieDomain());
		}

		return true;
	}


	/***************************************************************/
	/* LOGOUT USER, ON SUCCESS UPDATE SESSION, USER OBJECT AND ACL */
	/***************************************************************/
	public function logout() {
		$eSession = eFactory::getSession();

		$auth_method = $this->_session->login_method;
		$eSession->restart();

		$this->_session->forceNew();

		$this->_session->session_id = $eSession->getId();
		$this->_session->uid = 0;
		$this->_session->gid = 7;
		$this->_session->login_method = null;

		if ($this->getConfig('SESSION_HANDLER') != 'database') { //already stored by the session handler
			$this->_session->store();
		} else {
			$this->_session->update();
		}

		$tz = (string)$eSession->get('timezone');
		if ($tz != '') {
			if (eFactory::getDate()->setTimezone($tz) === false) { $tz = ''; }
		}

		$this->_user = new usersDbTable();
		$this->_user->uid = 0;
		$this->_user->gid = 7;
		$this->_user->timezone = $tz;

		$this->_acl->load($this->_user->uid, $this->_user->gid);

		$cookie_name = md5('remember');
		if (isset($_COOKIE[$cookie_name])) {
			$expire = time() - 24*3600;
			setcookie($cookie_name, '', $expire, $eSession->getCookiePath(), $eSession->getCookieDomain());
		}

		$eSession->set('backauth', 0);

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		$eAuth->setAuth($auth_method);
		if ($eAuth->getError() != '') {
			$eAuth->logout();
		}

		return true;
	}


	/******************************/
	/* GENERATE A RANDOM PASSWORD */
	/******************************/
	public function makePassword($length=10) {
		list($usec, $sec) = explode(' ', microtime());
		srand((float) $sec + ((float) $usec * 100000));
		$validchars = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!-@+()";
		$password = '';
		$counter = 0;
		while ($counter < $length) {
			$char = substr($validchars, rand(0, strlen($validchars)-1), 1);
			if (!strstr($password, $char)) {
				$password .= $char;
				$counter++;
			}
		}
		return $password;
	}


	/*****************************/
	/* GET IMAGE FROM ICONS PACK */
	/*****************************/
	public function icon($name, $size=16, $pack='', $type='png') {
		if ($pack == '') { $pack = $this->getConfig('ICONS_PACK'); }
		if ($type != 'gif') { $type = 'png'; }
		$dir = $size.'x'.$size;
		if (file_exists(ELXIS_PATH.'/includes/icons/'.$pack.'/'.$dir.'/'.$name.'.'.$type)) {
			return $this->secureBase().'/includes/icons/'.$pack.'/'.$dir.'/'.$name.'.'.$type;
		}
		if (($pack != 'nautilus') && file_exists(ELXIS_PATH.'/includes/icons/nautilus/'.$dir.'/'.$name.'.'.$type)) {
			return $this->secureBase().'/includes/icons/nautilus/'.$dir.'/'.$name.'.'.$type;
		}
		return $this->secureBase().'/includes/icons/nautilus/'.$dir.'/not_found.png';
	}


	/****************************************************/
	/* CONVERT ACCESS LEVEL VALUE TO TEXT USER GROUP(S) */
	/****************************************************/
	public function alevelToGroup($alevel, $groups=array(), $html=true, $detailedgroups=false) {
		$alevel = (int)$alevel;
		if ($alevel == 0) {
			return ($html) ? '<span style="color:#008000">'.eFactory::getLang()->get('GUEST').'</span>' : eFactory::getLang()->get('GUEST');
		} elseif ($alevel == 1000) {
			return ($html) ? '<span style="color:#008080">'.eFactory::getLang()->get('EXTERNALUSER').'</span>' : eFactory::getLang()->get('EXTERNALUSER');
		} elseif ($alevel == 2000) {
			return ($html) ? '<span style="color:#ff9900">'.eFactory::getLang()->get('USER').'</span>' : eFactory::getLang()->get('USER');
		} elseif ($alevel == 100000) {
			return ($html) ? '<span style="color:#cc0000">'.eFactory::getLang()->get('ADMINISTRATOR').'</span>' : eFactory::getLang()->get('ADMINISTRATOR');
		}
		
		$level = floor($alevel/1000);
		if (!$groups) {
			$db = eFactory::getDB();
			$sql = "SELECT * FROM ".$db->quoteId('#__groups');
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		$levels = array();
		if ($groups) {
			foreach ($groups as $group) {
				$glevel = (int)$group['level'];
				$lowlevel = $glevel * 1000;
				$exactlevel = $lowlevel + $group['gid'];
				if ($alevel == $exactlevel) {//found and it is unique
					$levels = array();
					$levels[] = $group['groupname'];
					break;
				} elseif ($alevel == $lowlevel) {
					$levels[] = $group['groupname'];
				}
			}
		}

		$n = count($levels);
		if ($n > 1) {
			$txt = ($detailedgroups) ? implode(', ', $levels) : eFactory::getLang()->get('LEVEL').' '.$level;
			return ($html) ? '<span style="color:#ff0000">'.$txt.'</span>' : $txt;
		} else if ($n == 1) {
			return ($html) ? '<span style="color:#ff0000">'.$levels[0].'</span>' : $levels[0];
		} else {
			return ($html) ? '<span style="color:#ff0000">'.eFactory::getLang()->get('LEVEL').' '.$level.'</span>' : eFactory::getLang()->get('LEVEL').' '.$level;
		}
	}


	/**************************/
	/* SET OR DELETE A COOKIE */
	/**************************/
	public function setCookie($name, $value, $life=15552000) {
		$eSession = eFactory::getSession();
		$expire = time() + intval($life);
		@setcookie($name, $value, $expire, $eSession->getCookiePath(), $eSession->getCookieDomain());
	}


	/**********************************************************/
	/* GET COOKIE'S VALUE - OPTIONALLY CREATE IT IF NOT EXIST */
	/**********************************************************/
	public function getCookie($name, $defvalue='', $create=true, $life=15552000) {
		if (isset($_COOKIE[$name])) { return $_COOKIE[$name]; }
		if ($create == false) { return $defvalue; }
		$this->setcookie($name, $defvalue, $life);
		return $defvalue;
	}


	/***************************************************/
	/* UPDATE COOKIE'S VALUE IF NEW VALUE IS DIFFERENT */
	/***************************************************/
	public function updateCookie($name, $value, $life=15552000) {
		if (isset($_COOKIE[$name])) {
			if ($_COOKIE[$name] == $value) { return true; } 
		}
		$this->setcookie($name, $value, $life);
		return true;
	}

}

?>