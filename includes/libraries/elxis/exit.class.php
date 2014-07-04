<?php 
/**
* @version		$Id: exit.class.php 1428 2013-05-03 18:55:07Z datahell $
* @package		Elxis
* @subpackage	Exit pages handler
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class exitPage {

	/**********************/
	/* GANERETE EXIT PAGE */
	/**********************/
	static public function make($screen='fatal', $code='', $message='') {
		$screen = trim($screen);
		if (($screen == '') || !in_array($screen, array('403', '404', 'error', 'fatal', 'offline', 'security', 'alogin'))) {
			$screen = 'fatal';
		}

		$tpl = 'system';
		$mobile = self::isMobile();
		if ($screen == 'alogin') {
			if (!defined('ELXIS_ADMIN')) {
				$screen = 'fatal';
				$code = 'EXIT-0001';
				$message = 'You can not login in the administration area from front-end!';
			}
		}

		$found = false;
		$doctype = 'xhtml5';
		if (class_exists('eRegistry', false)) {
			if (eRegistry::isLoaded('elxis')) {
				$found = true;
				$tpl = eRegistry::get('elxis')->getConfig('TEMPLATE');
				$doctype = eRegistry::get('elxis')->getConfig('DOCTYPE');
				$tpl_file = ($mobile) ? $screen.'.mobile' : $screen;
				if (!file_exists(ELXIS_PATH.'/templates/'.$tpl.'/'.$tpl_file.'.php')) {
					$tpl = 'system';
				}
			}
		}

		if (!$found) {
			$cfg = self::getElxisConfig();
			$tpl = $cfg->get('TEMPLATE');
			$doctype = $cfg->get('DOCTYPE');
			$tpl_file = ($mobile) ? $screen.'.mobile' : $screen;
			if (!file_exists(ELXIS_PATH.'/templates/'.$tpl.'/'.$tpl_file.'.php')) {
				$tpl = 'system';
			}
			unset($cfg);
		}

		if (class_exists('elxisLanguage', false)) {
			eFactory::getLang()->load('exit');
		}

		$docoptions = self::documentOptions($doctype);

		if (@ob_get_length() > 0) { ob_end_clean(); }
		header('Content-type:'.$docoptions['contenttype'].'; charset=utf-8');
		header('Expires: Sat, 5 Jan 1974 03:00:00 GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header("Pragma: no-cache");

		switch ($screen) {
			case '403':
				//header("HTTP/1.0 403 Forbidden");header("Status: 403 Forbidden");
				self::error403($tpl, $code, $message, $mobile, $docoptions);
			break;
			case '404':
				//header("HTTP/1.0 404 Not Found");
				self::error404($tpl, $code, $message, $mobile, $docoptions);
			break;
			case 'error':
				self::error($tpl, $code, $message, $mobile, $docoptions);
			break;
			case 'offline':
				self::offline($tpl, $code, $message, $mobile, $docoptions);
			break;
			case 'security':
				self::security($tpl, $code, $message, $mobile, $docoptions);
			break;
			case 'fatal':
				self::fatalError($tpl, $code, $message, $mobile, $docoptions);
			break;
			case 'alogin':
				self::adminLogin($tpl, $code, $message, $mobile, $docoptions);
			break;
			default: break;
		}
		exit();
	}


	/********************/
	/* DOCUMENT OPTIONS */
	/********************/
	static private function documentOptions($doctype) {
		$docoptions = array();
		$docoptions['dc'] = $doctype;
		switch ($doctype) {
			case 'xhtml_strict':
				$docoptions['doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
				$docoptions['contenttype'] = self::getXHTMLctype();
				$docoptions['cdata'] = true;
				$docoptions['namespace'] = 'http://www.w3.org/1999/xhtml';
			break;
			case 'xhtml_trans':
				$docoptions['doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
				$docoptions['contenttype'] = self::getXHTMLctype();
				$docoptions['cdata'] = true;
				$docoptions['namespace'] = 'http://www.w3.org/1999/xhtml';
			break;
			case 'html5':
				$docoptions['doctype'] = '<!DOCTYPE html>';
				$docoptions['contenttype'] = 'text/html';
				$docoptions['cdata'] = false;
				$docoptions['namespace'] = '';
			break;
			case 'xhtml5': default:
				$docoptions['doctype'] = '<!DOCTYPE html>';
				$docoptions['contenttype'] = self::getXHTMLctype();
				$docoptions['cdata'] = true;
				$docoptions['namespace'] = 'http://www.w3.org/1999/xhtml';
				$docoptions['metacharset'] = 'UTF-8';
			break;
		}

		return $docoptions;
	}


	/*********************************************/
	/* GET BEST CONTENT TYPE FOR XHTML DOCUMENTS */
	/*********************************************/
	private static function getXHTMLctype() {
		if (!isset($_SERVER['HTTP_ACCEPT'])) { return 'text/html'; }
		$accept = strtolower($_SERVER['HTTP_ACCEPT']);
		if (strpos($accept, 'application/xhtml+xml') !== false) {
			return 'application/xhtml+xml';
		} else {
			return 'text/html';
		}
	}


	/************************/
	/* DETECT MOBILE DEVICE */
	/************************/
	static private function isMobile() {
		if (defined('ELXIS_MOBILE')) {
			return (ELXIS_MOBILE == 1) ? true : false;
		}
		if (defined('ELXIS_ADMIN')) {
			define('ELXIS_MOBILE', 0);
			return false;
		}

		$is_mobile = false;
		if (class_exists('eRegistry', false) && (eRegistry::isLoaded('elxis'))) {
			$elxis = eFactory::getElxis();
			if ($elxis->getConfig('MOBILE') == 0) {
				define('ELXIS_MOBILE', 0);
				return false;
			}
			$is_mobile = $elxis->obj('browser')->isMobile();
		} else {
			elxisLoader::loadFile('includes/libraries/elxis/helpers/browser.helper.php');
			$browser = new elxisBrowserHelper();
			$is_mobile = $browser->isMobile();
			unset($browser);
		}

		if (isset($_GET['elxmobile'])) {
			if ($_GET['elxmobile'] == 1) {
				define('ELXIS_MOBILE', 1);
			} else {
				define('ELXIS_MOBILE', 0);
			}
		} else {
			$mob = (isset($_SESSION['elxis']['elxismobile'])) ? (int)$_SESSION['elxis']['elxismobile'] : -1;
			if ($mob == 1) {
				define('ELXIS_MOBILE', 1);
			} else if ($mob == 0) {
				define('ELXIS_MOBILE', 0);
			} else if ($is_mobile) {
				define('ELXIS_MOBILE', 1);
			} else {
				define('ELXIS_MOBILE', 0);
			}
		}

		if (class_exists('eRegistry', false)) {
			if ((eRegistry::isLoaded('eSession')) && (eRegistry::isLoaded('database')) && (eRegistry::isLoaded('elxis'))) {
				eFactory::getSession()->set('elxismobile', ELXIS_MOBILE);
			}
		}

		return (ELXIS_MOBILE == 1) ? true : false;
	}


	/***********************/
	/* MAKE ERROR 403 PAGE */
	/***********************/
	static private function error403($tpl, $code, $message, $ismobile, $docoptions) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$page = new stdClass;
		$page->title = $eLang->get('ERROR').' 403';
		$page->msgtitle = $eLang->get('FORBIDDEN');
		$page->message = (trim($message) == '') ? $eLang->get('ACCESS_NOT_ALLOWED') : $message;
		$page->url = eFactory::getURI()->getUriString();
		$page->loginlink = $elxis->makeURL('user:login/');
		$page->sitelink = $elxis->makeURL();
		$page->code = 'E403';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon();
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="'.$eLang->getinfo('LANGUAGE').'"'; }
		$page->htmlattributes .= ' lang="'.$eLang->getinfo('LANGUAGE').'" dir="'.$eLang->getinfo('DIR').'"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/403.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/403.php');
		}
	}


	/***********************/
	/* MAKE ERROR 404 PAGE */
	/***********************/
	static private function error404($tpl, $code, $message, $ismobile, $docoptions) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$page = new stdClass;
		$page->title = $eLang->get('ERROR').' 404';
		$page->msgtitle = $eLang->get('PAGE_NOT_FOUND');
		$page->message = (trim($message) == '') ? $eLang->get('PAGE_REQ_NOT_FOUND') : $message;
		$page->url = eFactory::getURI()->getUriString();
		$page->searchaction = $elxis->makeURL('search:/');
		$page->sitelink = $elxis->makeURL();
		$page->code = 'E404';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->menu = eFactory::getMenu()->getItems('mainmenu', 'frontend');
		$page->favicon = self::getFavicon();
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="'.$eLang->getinfo('LANGUAGE').'"'; }
		$page->htmlattributes .= ' lang="'.$eLang->getinfo('LANGUAGE').'" dir="'.$eLang->getinfo('DIR').'"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		$msg = 'ERROR 404 (Page not found). Reference code: '.$page->refcode."\nURL: ".$page->url;
		elxisError::logError($msg);

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/404.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/404.php');
		}
	}


	/*********************/
	/* MAKE OFFLINE PAGE */
	/*********************/
	static private function offline($tpl, $code, $message, $ismobile, $docoptions) {
		$lstatus = self::userLogin();
		
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$page = new stdClass;
		$page->title = $eLang->get('OFFLINE');
		$page->msgtitle = $eLang->get('WEBSITE_OFFLINE');
		if (trim($message) != '') {
			$page->message = $message;
		} elseif ($elxis->getConfig('OFFLINE_MESSAGE') != '') {
			$page->message = $elxis->getConfig('OFFLINE_MESSAGE');
		} else {
			$page->message = $eLang->get('WEBSITE_MAINTENANCE');
		}
		$page->url = eFactory::getURI()->getUriString();
		$page->loginaction = $elxis->makeURL('user:login/elxis.html');
		$page->sitelink = $elxis->makeURL();
		$page->code = 'OFF';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon();
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="'.$eLang->getinfo('LANGUAGE').'"'; }
		$page->htmlattributes .= ' lang="'.$eLang->getinfo('LANGUAGE').'" dir="'.$eLang->getinfo('DIR').'"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;
		$page->loginerror = '';
		switch ($lstatus) {
			case 1: 
				$page->loginerror = $eLang->get('FILL_VALID_USERPASS');
			break;
			case 2:
				$page->loginerror = $eLang->get('ONLY_ADMINS_LOGIN');
			break;
			case 0:
			default:
				if (isset($_GET['elxerror'])) {
					$page->loginerror = strip_tags(urldecode($_GET['elxerror']));
				}
			break;
		}

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/offline.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/offline.php');
		}
	}


	/**************/
	/* LOGIN USER */
	/**************/
	static private function userLogin() {
		$elxis = eFactory::getElxis();
		if ($elxis->getConfig('ONLINE') !== 2) { return 0; }
		if (!isset($_POST['uname'])) { return 0; }
		if (!isset($_POST['pword'])) { return 0; }
		$uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ((trim($uname) == '') || (trim($pword) == '')) { return 1; }

		$db = eFactory::getDB();
		$sql = "SELECT COUNT(uid) FROM #__users WHERE uname=:username AND block=0 AND gid=1";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':username', $uname, PDO::PARAM_STR);
		$stmt->execute();
		$c = (int)$stmt->fetchResult();
		if ($c !== 1) { return 2; }

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		if ($eAuth->getError() != '') { return 0; }
		if (!$eAuth->setAuth('elxis')) { return 0; }

		$options = array();
		$options['auth_method'] = 'elxis';
		$options['uname'] = $uname;
		$options['pword'] = $pword;
		$options['remember'] = 1;
		$options['return'] = '';

		$elxis->login($options);
		$elxis->redirect('');
	}


	/***************************/
	/* MAKE GENERIC ERROR PAGE */
	/***************************/
	static private function error($tpl, $code, $message, $ismobile, $docoptions) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$page = new stdClass;
		$page->title = $eLang->get('ERROR');
		$page->msgtitle = $eLang->get('ERROR_OCCURED');
		$page->message = $message;
		$page->url = eFactory::getURI()->getUriString();
		$page->sitelink = $elxis->makeURL();
		$page->code = 'ERR';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon();
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="'.$eLang->getinfo('LANGUAGE').'"'; }
		$page->htmlattributes .= ' lang="'.$eLang->getinfo('LANGUAGE').'" dir="'.$eLang->getinfo('DIR').'"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		$msg = 'ERROR (generic). Reference code: '.$page->refcode."\nURL: ".$page->url;
		elxisError::logError($msg);

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/error.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/error.php');
		}
	}


	/****************************/
	/* MAKE SECURITY BLOCK PAGE */
	/****************************/
	static private function security($tpl, $code, $message, $ismobile, $docoptions) {
		$cfg = self::getElxisConfig();

		$page = new stdClass;
		$page->title = 'Security alert';
		$page->msgtitle = 'Request dropped!';
		$page->message = (trim($message) != '') ? $message : 'Elxis defender blocked your request.';
		$page->sitelink = $cfg->get('URL');
		$page->secure_sitelink = $cfg->get('URL');
		if (isset($_SERVER['HTTPS'])) {
			if (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == 1)) {
				$page->secure_sitelink = preg_replace('@^(http\:)@i', 'https:', $page->sitelink);
			}
		}
		$page->code = 'SEC';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon($page->secure_sitelink);
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="en"'; }
		$page->htmlattributes .= ' lang="en" dir="ltr"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/security.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/security.php');
		}
	}


	/*************************/
	/* MAKE FATAL ERROR PAGE */
	/*************************/
	static private function fatalError($tpl, $code, $message, $ismobile, $docoptions) {
		$cfg = self::getElxisConfig();

		$page = new stdClass;
		$page->title = 'Fatal error';
		$page->msgtitle = 'Unrecoverable error!';
		$page->message = (trim($message) != '') ? $message : 'The system encountered an unrecoverable error and is unable to proceed.';
		$page->sitelink = $cfg->get('URL');
		$page->secure_sitelink = $cfg->get('URL');
		if (isset($_SERVER['HTTPS'])) {
			if (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == 1)) {
				$page->secure_sitelink = preg_replace('@^(http\:)@i', 'https:', $page->sitelink);
			}
		}
		$page->code = 'SEC';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon($page->secure_sitelink);
		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="en"'; }
		$page->htmlattributes .= ' lang="en" dir="ltr"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		$msg = 'FATAL ERROR. Reference code: '.$page->refcode;
		if (isset($_SERVER['REQUEST_URI'])) { $msg .= "\nURL: ".$_SERVER['REQUEST_URI']; }
		elxisError::logError($msg);

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/fatal.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/fatal.php');
		}
	}


	/**********************************/
	/* MAKE ADMINISTRATION LOGIN PAGE */
	/**********************************/
	static private function adminLogin($tpl, $code, $message, $ismobile, $docoptions) {
		$message = (int)$message;
		$lstatus = self::userAdminLogin($message);
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$page = new stdClass;
		$page->title = $eLang->get('LOGIN');
		$page->msgtitle = $eLang->get('ADMINISTRATION_LOGIN');
		$page->confirmform = 0;
		$page->buttontext = $eLang->get('LOGIN');

		if ($ismobile) {
			$page->message = $eLang->get('SORRY_ADMIN_NO_MOBILE');
		} else if ($message == 1) {
			$page->confirmform = 1;
			$page->buttontext = $eLang->get('CONFIRM');
			$page->message = $eLang->get('CONFIRM_INFO');
		} else {
			$page->message = $eLang->get('VUP_ACCESS_ADMIN');
		}

		$installed_langs = eFactory::getFiles()->listFolders('language');

		$page->loginaction = $elxis->makeAURL();
		$page->sitelink = $elxis->makeURL();
		$page->code = 'ALOG';
		$page->refcode = ($code == '') ? $page->code : $page->code.'-'.$code;
		$page->favicon = self::getFavicon();
		$page->flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
		$page->infolangs = $eLang->getallinfo($installed_langs);
		$page->loginerror = '';
		$page->return = (isset($_POST['return'])) ? base64_encode(self::getReturnURL()) : base64_encode(eFactory::getURI()->getRealUriString());
		unset($installed_langs);

		switch ($lstatus) {
			case -2: case -3: case -4:
				$page->loginerror = $eLang->get('INVALIDPASS');
			break;
			case -5: $page->loginerror = 'Something wrong happened. Please try again.'; break;
			case -6: $page->loginerror = $eLang->get('FILL_VALID_USERPASS'); break;
			case -7: $page->loginerror = $eLang->get('USERNOTFOUND'); break;
			case -8: $page->loginerror = $eLang->get('NOT_ENOUGH_PRIV'); break;
			case 0: case -1: default:
				if (isset($_GET['elxerror'])) {
					$page->loginerror = strip_tags(urldecode($_GET['elxerror']));
				}
			break;
		}

		$page->doctype = $docoptions['doctype'];
		$page->contenttype = $docoptions['contenttype'];
		$page->htmlattributes = '';
		if ($docoptions['namespace'] != '') { $page->htmlattributes .= ' xmlns="'.$docoptions['namespace'].'"'; }
		if ($docoptions['dc'] != 'html5') { $page->htmlattributes .= ' xml:lang="'.$eLang->getinfo('LANGUAGE').'"'; }
		$page->htmlattributes .= ' lang="'.$eLang->getinfo('LANGUAGE').'" dir="'.$eLang->getinfo('DIR').'"';
		$page->extrahead = ($docoptions['dc'] == 'xhtml5') ? '<meta charset="UTF-8" />'."\n" : '';
		$page->cdata = ($docoptions['dc'] == 'html5') ? false : true;

		if ($ismobile) {
			include(ELXIS_PATH.'/templates/'.$tpl.'/alogin.mobile.php');
		} else {
			include(ELXIS_PATH.'/templates/'.$tpl.'/alogin.php');
		}
	}


	/**************/
	/* LOGIN USER */
	/**************/
	static private function userAdminLogin($confirmpass=0) {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();

		if ($confirmpass == 1) {
			if (!isset($_POST['pword2'])) { return -1; }
			$pword2 = filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if (trim($pword2) == '') { return -2; }
			$uname = $elxis->user()->uname;
			$uid = $elxis->user()->uid;
			$gid = $elxis->user()->gid;

			$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('pword').", ".$db->quoteId('gid')
			."\n FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uname')." = :username";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->execute(array(':username' => $uname));
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$row) { return  -3; }
			$encpass = $elxis->obj('crypt')->getEncryptedPassword($pword2);
			if ($encpass != $row['pword']) { return -4; }
			if (($row['uid'] <> $elxis->user()->uid) || ($row['gid'] <> $elxis->user()->gid)) { return -5; }

			eFactory::getSession()->set('backauth', 1);

			$url = self::getReturnURL();
			$elxis->redirect($url);
		}

		if (!isset($_POST['uname'])) { return -1; }
		if (!isset($_POST['pword'])) { return -1; }
		$uname = filter_input(INPUT_POST, 'uname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ((trim($uname) == '') || (trim($pword) == '')) { return -6; }

		$minlevel = ($elxis->getConfig('SECURITY_LEVEL') > 1) ? 100 : 70;

		$sql = "SELECT u.uid, g.level FROM #__users u"
		."\n INNER JOIN #__groups g ON g.gid = u.gid"
		."\n WHERE u.uname=:username AND u.block=0";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':username', $uname, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { return -7; }
		if ($row['level'] < $minlevel) { return -8; }

		$url = self::getReturnURL();

		elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
		$eAuth = eRegistry::get('eAuth');
		if ($eAuth->getError() != '') { return -5; }
		if (!$eAuth->setAuth('elxis')) { return -5; }

		$options = array();
		$options['auth_method'] = 'elxis';
		$options['uname'] = $uname;
		$options['pword'] = $pword;
		$options['remember'] = 0;
		$options['return'] = $url;
		$ok = $elxis->login($options);
		if ($ok === true) {
			eFactory::getSession()->set('backauth', 1);
		}
		$elxis->redirect($url);
	}


	/*******************************/
	/* GET/CREATE LOGIN RETURN URL */
	/*******************************/
	static private function getReturnURL() {
		$elxis = eFactory::getElxis();
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

		if ($return == '') { $return = $elxis->makeAURL(); }
		return $return;
	}

	
	/***************************/
	/* GET ELXIS CONFIGURATION */
	/***************************/
	static private function getElxisConfig() {
		if (!class_exists('elxisConfig', false)) {
			elxisLoader::loadFile('configuration.php');
		}
		$cfg = new elxisConfig();
		return $cfg;
	}


	/*******************/
	/* GET FAVICON URL */
	/*******************/
	static private function getFavicon($seclink='') {
		if (file_exists(ELXIS_PATH.'/favicon.ico')) {
			$favicon = 'favicon.ico';
		} elseif (file_exists(ELXIS_PATH.'/favicon.png')) {
			$favicon = 'favicon.png';
		} elseif (file_exists(ELXIS_PATH.'/media/images/favicon.ico')) {
			$favicon = 'media/images/favicon.ico';
		} elseif (file_exists(ELXIS_PATH.'/media/images/favicon.png')) {
			$favicon = 'media/images/favicon.png';
		} else {
			return '';
		}

		if ($seclink != '') {
			return $seclink.'/'.$favicon;
		} else {
			return eFactory::getElxis()->secureBase().'/'.$favicon;
		}
	}

}

?>