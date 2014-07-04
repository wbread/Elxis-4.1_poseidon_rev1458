<?php 
/**
* @version		$Id: account.php 971 2012-03-18 13:05:24Z datahell $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class accountUserController extends userController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/****************************************/
	/* PREPRE TO DISPLAY USERS CENTRAL PAGE */
	/****************************************/
	public function userscentral() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$avatar = '';
		$avsize = 0;
		if ($elxis->acl()->getLevel() > 0) {
			$params = $this->base_getParams();
			$gravatar = (int)$params->get('gravatar', 0);
			$avsize = (int)$params->get('profile_avatar_width', 80);
			$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, $avsize, $gravatar, $elxis->user()->email);
			unset($gravatar, $params);
		}

		$eDoc->setTitle($eLang->get('USERSCENTRAL').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('USERSCENTRAL'));
		$eDoc->setKeywords(array($eLang->get('USER'), $eLang->get('RECOVERPASS'), $eLang->get('REGISTRATION'), $eLang->get('LOGIN'), $eLang->get('LOGOUT')));

		$this->view->usersCentral($avatar, $avsize);
	}


	/*******************************************************/
	/* PREPARE TO REGISTER OR TO DISPLAY REGISTRATION FORM */
	/*******************************************************/
	public function register() {
		$this->base_forceSSL('user:register.html');

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRNEWUSERACC'));
		$eDoc->setKeywords(array($eLang->get('REGISTRATION'), $eLang->get('USER'), $eLang->get('USERNAME')));

		if ($elxis->getConfig('REGISTRATION') !== 1) {
			$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERREGDISABLED'), $eLang->get('ERROR'));
			return;
		}

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('REGISTRATION').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}

		$errormsg = '';
		$ok = false;
		$row = new usersDbTable();
		if (isset($_POST['firstname'])) {
			$row->gid = 5;
			$row->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$row->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$row->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			$uname = trim($_POST['uname']);
			$row->uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$row->uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->uname);
			$pword = $_POST['pword'];
			$row->pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pword2 = filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

			$eSession = eFactory::getSession();
			$sess_token = trim($eSession->get('token_fmregister'));
			$sess_captcha = trim($eSession->get('captcha_seccode'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$seccode = trim(filter_input(INPUT_POST, 'seccode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));

			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			} else if (($sess_captcha == '') || ($seccode == '') || ($seccode != $sess_captcha)) {
				$errormsg = $eLang->get('INVALIDSECCODE');
			} else if (($row->uname == '') || ($row->uname != $uname)) {
				$errormsg = $eLang->get('INVALIDUNAME');
			} else if (($row->pword == '') || ($row->pword != $pword)) {
				$errormsg = $eLang->get('INVALIDPASS');
			} else if ($row->pword != $pword2) {
				$errormsg = $eLang->get('PASSNOMATCH');
			} else {
				$row->preflang = $eLang->currentLang();
				if (!$row->fullCheck()) {
					$errormsg = $row->getErrorMsg();
				} else {
					$pwd = $row->pword;
					$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pwd);
					if (!$row->store()) {
						$errormsg = $row->getErrorMsg();
					} else {
						$ok = true;
					}
				}				
			}

			$eSession->set('token_fmregister');
		}

		if ($ok === true) {
			$this->mailNewAccount($row);
			if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 0) {
				$link = $elxis->makeURL('user:login/', '', true);
				$msg = sprintf($eLang->get('YOUMAYLOGIN'), '<strong>'.$row->uname.'</strong>')."<br />\n";
				$msg .= '<a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('CLICKTOLOGIN')."</a>\n";
			} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 2) {
				$msg = $eLang->get('REGINSPBEFLOG');
			} else {
				$msg = $eLang->get('MAILACTLINK');
			}

			$eDoc->setTitle($eLang->get('SUCCESSREG').' - '.$elxis->getConfig('SITENAME'));
			$this->view->registrationSuccess($row, $msg);
		} else {
			$this->view->registrationForm($row, $errormsg);
		}
	}


	/*****************************/
	/* ACTIVATE NEW USER ACCOUNT */
	/*****************************/
	public function activate() {
		$c = trim(filter_input(INPUT_GET, 'c', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$this->base_forceSSL('user:activate.html?c='.$c);

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRNEWUSERACC').'. '.$eLang->get('ACCOUNTACT'));
		$eDoc->setKeywords(array($eLang->get('ACCOUNTACT'), $eLang->get('REGISTRATION'), $eLang->get('USER'), $eLang->get('USERNAME')));

		if ($elxis->getConfig('REGISTRATION') !== 1) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERREGDISABLED'), $eLang->get('ACCOUNTACT'));
			return;
		}

		if ($elxis->getConfig('REGISTRATION_ACTIVATION') !== 1) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('User account activation is disabled!', $eLang->get('ACCOUNTACT'));
			return;
		}

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}
		
		if ($c == '') {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('INVACTCODE'));
			return;
		}

		$db = eFactory::getDB();
		$stmt = $db->prepareLimit("SELECT ".$db->quoteId('uid').", ".$db->quoteId('uname')." FROM #__users WHERE ".$db->quoteId('block')." = 1 AND ".$db->quoteId('activation')." = :actcode", 0, 1);
		$stmt->bindParam(':actcode', $c, PDO::PARAM_STR);
		$stmt->execute();
		$urow = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$urow || !is_array($urow) || (count($urow) == 0)) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('INVACTCODE'));
			return;
		}

		$stmt = $db->prepare("UPDATE #__users SET ".$db->quoteId('block')." = 0, ".$db->quoteId('activation')." = NULL WHERE uid = :userid");
		$stmt->bindParam(':userid', $urow['uid'], PDO::PARAM_INT);
		$ok = $stmt->execute();
		if (!$ok) {
			$eDoc->setTitle($eLang->get('ACCOUNTACT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen('The system failed to activate your account. Please contact the site administrator.');
			return;
		}

		$this->view->activationSuccess($urow['uname']);
	}


	/********************************************************/
	/* PREPARE TO DISPLAY/PROCESS PASSWORD RECOVERY REQUEST */
	/********************************************************/
	public function recoverpass() {
		$this->base_forceSSL('user:recover-pwd.html');

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('CRPASSACCFORG'));
		$eDoc->setKeywords(array($eLang->get('PASSWORD'), $eLang->get('USER'), $eLang->get('USERNAME')));

		if ($elxis->user()->gid != 7) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
			return;
		}

		if ($elxis->getConfig('PASS_RECOVER') !== 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('PASSRECOVNALL'), $eLang->get('RECOVERPASS'));
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$eDoc->setTitle($eLang->get('RECOVERPASS').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('SECLEVNALLRP'), $eLang->get('RECOVERPASS'));
			return;
		}

		$errormsg = '';
		$row = new stdClass();
		$row->uname = null;
		$row->email = null;
		if (isset($_POST['sbmrec'])) {
			$uname = trim($_POST['uname']);
			$row->uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$row->uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->uname);
			$row->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

			$eSession = eFactory::getSession();
			$sess_token = trim($eSession->get('token_fmrecover'));
			$sess_captcha = trim($eSession->get('captcha_seccode'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$seccode = trim(filter_input(INPUT_POST, 'seccode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));

			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			} else if (($sess_captcha == '') || ($seccode == '') || ($seccode != $sess_captcha)) {
				$errormsg = $eLang->get('INVALIDSECCODE');
			} else if (($row->uname == '') || ($row->uname != $uname)) {
				$errormsg = $eLang->get('INVALIDUNAME');
			} else if (($row->email == '') || !filter_var($row->email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			} else {
				$proceed = true;
				if (($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') || ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '')) {
					$parts = explode('@', $row->email);
					if (!$parts || !is_array($parts) || (count($parts) != 2)) {
						$errormsg = $eLang->get('INVALIDEMAIL');
						$proceed = false;
					}

					if ($proceed) {
						$emaildomain = strtolower($parts[1]);
						if ($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') {
							if ($emaildomain != $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN')) {
								$errormsg = sprintf($eLang->get('ONLYMAILFROMALLOW'), $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN'));
								$proceed = false;
							}
						}						
					}

					if ($proceed) {
						if ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '') {
							$exdomains = explode(',', $elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS'));
							if ($exdomains && is_array($exdomains) && (count($exdomains) > 0)) {
								foreach ($exdomains as $exdomain) {
									if ($emaildomain == $exdomain) {
										$errormsg = sprintf($eLang->get('EMAILADDRNOTACC'), $emaildomain);
										$proceed = false;
										break;
									}
								}
							}
						}
					}
					unset($parts);
				}

				if ($proceed) {
					$db = eFactory::getDB();
					$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ";
					$sql .= $db->quoteId('gid').", ".$db->quoteId('block').", ".$db->quoteId('expiredate');
					$sql .= "\n FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uname')." = :username AND ".$db->quoteId('email')." = :mail";
					$stmt = $db->prepareLimit($sql, 0, 1);
					$stmt->bindParam(':username', $row->uname, PDO::PARAM_STR);
					$stmt->bindParam(':mail', $row->email, PDO::PARAM_STR);
					$stmt->execute();
					$result = $stmt->fetch(PDO::FETCH_OBJ);
					if (!$result) {
						$errormsg = $eLang->get('USERNFOUND');
					} else if (intval($result->block) == 1) {
						$errormsg = $eLang->get('YACCBLOCKED');
					} else if ($result->expiredate < gmdate('Y-m-d H:i:s')) {
						$errormsg = $eLang->get('YACCEXPIRED');
					} else if ((intval($result->gid) == 1) && ($elxis->getConfig('SECURITY_LEVEL') > 0)) {
						$errormsg = $eLang->get('SECLEVNALLRP');
					} else {
						$newpass = $elxis->makePassword();
						$encpass = $elxis->obj('crypt')->getEncryptedPassword($newpass);
						$stmt = $db->prepare("UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('pword')." = :password WHERE ".$db->quoteId('uid')." = :userid");
						$stmt->bindParam(':password', $encpass, PDO::PARAM_STR);
						$stmt->bindParam(':userid', $result->uid, PDO::PARAM_INT);
						if (!$stmt->execute()) {
							$errormsg = 'Could not reset your password!';
						} else {
							$this->mailPassRecover($result->firstname, $result->lastname, $row->email, $newpass);
							$this->view->recoverSuccess();
							return;
						}
					}
				}
			}
			
			$eSession->set('token_fmrecover');
		}

		$this->view->recoverForm($row, $errormsg);
	}


	/***************************/
	/* CHANGE CURRENT TIMEZONE */
	/***************************/
	public function changetimezone() {
		$elxis = eFactory::getElxis();
		$eSession = eFactory::getSession();
		$sess_token = trim($eSession->get('token_fmchangetz'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token != '') && ($sess_token != '') && ($sess_token == $token)) {
			$tz = trim(filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if ($tz != '') {
				if (eFactory::getDate()->setTimezone($tz) === true) {
					eFactory::getSession()->set('timezone', $tz);
					$uid = (int)$elxis->user()->uid;
					if ($uid > 0) {
						$db = eFactory::getDB();
						$sql = "UPDATE ".$db->quoteId('#__users')." SET ".$db->quoteId('timezone')." = :tzone WHERE ".$db->quoteId('uid')." = :userid";
						$stmt = $db->prepare($sql);
						$stmt->bindParam(':tzone', $tz, PDO::PARAM_STR);
						$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
						$stmt->execute();
					}
				}
			}
		}

		$url = $elxis->makeURL('user:/');
		if (isset($_POST['redirectto'])) {
			$redirectto = trim(filter_input(INPUT_POST, 'redirectto', FILTER_SANITIZE_URL));
			if (($redirectto != '') && filter_var($redirectto, FILTER_VALIDATE_URL)) {
				$url = $redirectto;
			}
		}
		
		$eSession->set('token_fmchangetz');
		$elxis->redirect($url);
	}


	/****************************/
	/* USER LOGIN OR LOGIN FORM */
	/****************************/
	public function login($auth_method='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$elxis_uri = ($auth_method != '') ? 'user:login/'.$auth_method.'.html' : 'user:login/';
		$this->base_forceSSL($elxis_uri);

		$eDoc->setTitle($eLang->get('LOGIN').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('LOGINOWNACC'));
		$eDoc->setKeywords(array($eLang->get('LOGIN'), $eLang->get('USER'), $eLang->get('USERNAME')));

		if ($elxis->user()->gid <> 7) {
			if (ELXIS_INNER == 1) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('ALREADYLOGIN'));
				return;
			} else {
				$redir_url = $elxis->makeURL('user:/');
				$elxis->redirect($redir_url);
			}
		}

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		if ($eAuth->getError() != '') {
			$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eAuth->getError());
			return;
		}

		$auths = $eAuth->getAuths();
		if ($auth_method == '') {
			if (isset($auths['elxis'])) { $auth_method = 'elxis'; }
		} else {
			if (!isset($auths[$auth_method])) {
				$msg = sprintf($eLang->get('AUTHMETHNOTEN'), $auth_method);
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($msg);
				return;
			}
		}

		if ($auth_method != '') {
			$eAuth->setAuth($auth_method);
		}

		if ($auth_method == '') {
			$this->view->loginForm();
			return;
		}

		if (isset($_GET['etask'])) {
			$etask = trim(filter_input(INPUT_GET, 'etask', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_POST['etask'])) {
			$etask = trim(filter_input(INPUT_POST, 'etask', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$etask = '';
		}

		if (!isset($_POST['auth_method']) && ($etask == '')) {
			$this->view->loginForm($auth_method);
			return;
		}

		$required_post = array('elxis', 'gmail', 'ldap');
		if (in_array($auth_method, $required_post)) {
			if (!isset($_POST['auth_method'])) {
				$this->view->loginForm($auth_method);
				return;
			} else if ($_POST['auth_method'] != $auth_method) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen('Submitted Authentication method is wrong!');
				return;
			}
		}

		if (($etask != '') && ($etask != 'auth')) {
			$eAuth->runTask($etask);
			return;
		}

		if ($auth_method == 'elxis') {
			if (isset($_POST['modtoken'])) {
				$sess_token = trim(eFactory::getSession()->get('token_loginform'));
				$token = trim(filter_input(INPUT_POST, 'modtoken', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			} else {
				$sess_token = trim(eFactory::getSession()->get('token_fmuserlogin'));
				$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));				
			}
			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$eDoc->setTitle($eLang->get('LOGIN').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('REQDROPPEDSEC'));
				return;
			}
		}

		$uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$remember = isset($_POST['remember']) ? (int)$_POST['remember'] : 0;
		if ($remember !== 1) { $remember = 0; }
		if ($auth_method != 'elxis') { $remember = 0; }

		$return = '';
		if (isset($_POST['return']) && (trim($_POST['return']) != '')) {
			$return1 = base64_decode($_POST['return']);
			$return = filter_var($return1, FILTER_SANITIZE_URL);
			if ($return != $return1) {
				$return = '';
			} else {
				if (!filter_var($return, FILTER_VALIDATE_URL)) {
					$return = '';
				} else { //no external redirection!
					$siteurl = $elxis->getConfig('URL');
					if (strpos($return, $siteurl) === false) {
						$siteurlssl = eFactory::getURI()->secureBase(true);
						if (strpos($return, $siteurlssl) === false) { $return = ''; }
					}
				}
			}
		}

		$options = array();
		$options['auth_method'] = $auth_method;
		$options['uname'] = $uname;
		$options['pword'] = $pword;
		$options['remember'] = $remember;
		$options['return'] = $return;

		$elxis->login($options);
		if (ELXIS_INNER == 1) {
			$this->view->closeAfterLogin($return);
		} else {
			$elxis->redirect($return);
		}
	}


	/**********************************/
	/* LOGOUT USER IF HE IS LOGGED-IN */
	/**********************************/
	public function logout() {
		$this->base_forceSSL('user:logout.html');
		$elxis = eFactory::getElxis();
		if ($elxis->user()->gid <> 7) {
			$elxis->logout();
		}

		$return = '';
		if (isset($_POST['return']) && (trim($_POST['return']) != '')) {
			$return1 = base64_decode($_POST['return']);
			$return = filter_var($return1, FILTER_SANITIZE_URL);
			if ($return != $return1) {
				$return = '';
			} else {
				if (!filter_var($return, FILTER_VALIDATE_URL)) {
					$return = '';
				} else { //no external redirection!
					$siteurl = $elxis->getConfig('URL');
					if (strpos($return, $siteurl) === false) {
						$siteurlssl = eFactory::getURI()->secureBase(true);
						if (strpos($return, $siteurlssl) === false) {
							$return = '';
						}
					}
				}
			}
		}

		if ($return == '') { $return = $elxis->makeURL('user:/'); }
		$elxis->redirect($return);
	}

}

?>