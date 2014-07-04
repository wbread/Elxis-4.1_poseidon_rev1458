<?php 
/**
* @version		$Id: base.php 1403 2013-03-17 19:09:03Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class userController {

	protected $view = null;
	protected $model = null;


	protected function __construct($view=null, $task='', $model=null) {
		$this->view = $view;
		$this->model = $model;
		if (!defined('ELXIS_ADMIN')) {
			$this->makePathway($task);
		}
	}


	/*****************************/
	/* SET FRONTEND PAGE PATHWAY */
	/*****************************/
	protected function makePathway($task='') {
		$pathway = eFactory::getPathway();
		$eLang = eFactory::getLang();
		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERSCENTRAL'), 'user:/', false);
		switch($task) {
			case 'changetimezone': case 'userscentral': case '': break;
			case 'login': $pathway->addNode($eLang->get('LOGIN')); break;
			case 'logout': $pathway->addNode($eLang->get('LOGOUT')); break;
			case 'register': $pathway->addNode($eLang->get('REGISTER')); break;
			case 'recoverpass': $pathway->addNode($eLang->get('RECOVERPASS')); break;
			case 'activate': $pathway->addNode($eLang->get('ACCOUNTACT')); break;
			case 'memberslist':
				$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
				if ($page > 1) {
					$pathway->addNode($eLang->get('MEMBERSLIST'), 'user:members/', false);
					$pathway->addNode($eLang->get('PAGE').' '.$page);
				} else {
					$pathway->addNode($eLang->get('MEMBERSLIST'));
				}
			break;
			case 'profile':
				$pathway->addNode($eLang->get('MEMBERSLIST'), 'user:members/', false);
				$pathway->addNode($eLang->get('PROFILE'));
			break;
			case 'editprofile':
			case 'saveprofile':
				$pathway->addNode($eLang->get('MEMBERSLIST'), 'user:members/', false);
				$pathway->addNode($eLang->get('EDITPROFILE'));
			break;
			case 'blockaccount':
				$pathway->addNode($eLang->get('MEMBERSLIST'), 'user:members/', false);
				$pathway->addNode($eLang->get('BLOCKUSER'));
			break;
			case 'deleteaccount':
				$pathway->addNode($eLang->get('MEMBERSLIST'), 'user:members/', false);
				$pathway->addNode($eLang->get('DELETEACCOUNT'));
			break;
			default: break;
		}
	}


	/*****************/
	/* FORCE SSL/TLS */
	/*****************/ 
	protected function base_forceSSL($elxisURI) {
		$elxis = eFactory::getElxis();
		if ($elxis->getConfig('SSL') == 0) { return; }
		if (eFactory::getURI()->detectSSL() === true) { return; }
		if ($elxis->getConfig('SSL') == 2) {
			$link = $elxis->makeURL($elxisURI, '', true, false);
			$elxis->redirect($link);
		} elseif (($elxis->getConfig('SSL') == 1) && defined('ELXIS_ADMIN')) {
			$link = $elxis->makeURL($elxisURI, '', true, false);
			$elxis->redirect($link);
		}
	}


	/****************************/
	/* GET COMPONENT PARAMETERS */
	/****************************/
	protected function base_getParams() {
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_user');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$params_str = (string)$stmt->fetchResult();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($params_str, '', 'component');
		return $params;
	}


	/********************************/
	/* SEND NEW REGISTRATION EMAILS */
	/********************************/
	protected function mailNewAccount($row) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$profile_page = $elxis->makeURL('user:members/'.$row->uid.'.html');

		$subject = $eLang->get('THANKSYREG');
		$body = $eLang->get('HI').' '.$row->firstname.' '.$row->lastname."\n";
		$body .= sprintf($eLang->get('THANKREGAT'), $elxis->getConfig('SITENAME'))."\n\n";
		if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 0) {
			$body .= sprintf($eLang->get('YOUMAYLOGIN'), $row->uname)."\n\n";
			$body .= $eLang->get('LOGIN').": \t\t".$elxis->makeURL('user:login/', '', true)."\n";
			$body .= $eLang->get('USERPROFILE').": \t\t".$profile_page."\n\n\n";
		} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 2) {
			$body .= $eLang->get('REGINSPBEFLOG')."\n\n\n";
		} else {
			$body .= $eLang->get('CLICKACTIVATE')."\n";
			$body .= $elxis->makeURL('user:activate.html?c='.$row->activation, '', true)."\n\n\n";
		}
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= $eLang->get('NOREPLYMSGINFO');

		$to = $row->email.','.$row->firstname.' '.$row->lastname;
		$elxis->sendmail($subject, $body, '', null, 'plain', $to);

		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('firstname').",  ".$db->quoteId('lastname').",  ".$db->quoteId('email').",  ".$db->quoteId('preflang')
		."\n FROM  ".$db->quoteId('#__users')." WHERE  ".$db->quoteId('gid')." = 1 AND ".$db->quoteId('block')." = 0";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$admins = $stmt->fetchAll(PDO::FETCH_OBJ);

		$original_language = $eLang->currentLang();
		$curlang = $original_language;
		if ($admins && (count($admins) > 0)) {
			foreach ($admins as $admin) {
				$userlang = trim($admin->preflang);
				if (($userlang != '') && ($userlang != $curlang)) {
					$eLang->switchLanguage($userlang);
					$curlang = $userlang;
				}

				$subject = $eLang->exist('NEWUSERREG') ? $eLang->get('NEWUSERREG') : 'A new user has registered';
				$body = sprintf($eLang->get('NEWUSERREGAT'), $elxis->getConfig('SITENAME'))."\n\n";
				$body .= $eLang->get('USERNAME').": \t\t".$row->uname."\n";
				$body .= $eLang->get('FIRSTNAME').": \t\t".$row->firstname."\n";
				$body .= $eLang->get('LASTNAME').": \t\t".$row->lastname."\n";
				$body .= $eLang->get('EMAIL').": \t\t".$row->email."\n";
				$body .= $eLang->get('USERPROFILE').": \t\t".$profile_page."\n\n\n";
				if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 2) {
					$body .= $eLang->get('YMUSTMANACTLOG')."\n\n\n";
				}
				$body .= $eLang->get('REGARDS')."\n";
				$body .= $elxis->getConfig('SITENAME')."\n";
				$body .= $elxis->getConfig('URL')."\n\n\n\n";
				$body .= "_______________________________________________________________\n";
				$body .= $eLang->get('NOREPLYMSGINFO');
				
				$to = $admin->email.','.$admin->firstname.' '.$admin->lastname;
				$elxis->sendmail($subject, $body, '', null, 'plain', $to);
			}
		}
		
		if ($curlang != $original_language) {
			$eLang->switchLanguage($original_language);
		}
	}


	/***************************/
	/* SEND NEW PASSWORD EMAIL */
	/***************************/
	protected function mailPassRecover($firstname, $lastname, $email, $newpass) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$subject = $eLang->get('RECOVERPASS');
		$body = $eLang->get('HI').' '.$firstname.' '.$lastname."\n";
		$body .= $eLang->get('NEWPASSGENREQ')."\n\n";		
		$body .= $eLang->get('PASSWORD').": \t".$newpass."\n\n";
		$body .= $eLang->get('LOGIN').": \t".$elxis->makeURL('user:login/', '', true)."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= $eLang->get('NOREPLYMSGINFO');

		$to = $email.','.$firstname.' '.$lastname;
		$elxis->sendmail($subject, $body, '', null, 'plain', $to);
	}


	/**********************************/
	/* SEND ACCOUNT RE-ACTIVATE EMAIL */
	/**********************************/
	protected function mailReactivateAccount($row, $type='user') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$original_language = $eLang->currentLang();
		$curlang = $original_language;

		if ($type == 'user') {
			$userlang = trim($row->preflang);
			if (($userlang != '') && ($userlang != $curlang)) {
				$eLang->switchLanguage($userlang);
				$curlang = $userlang;
			}

			$subject = $eLang->get('ACCOUNTACT');
			$body = $eLang->get('HI').' '.$row->firstname.' '.$row->lastname."\n";
			$body .= sprintf($eLang->get('EMAILATCHANGED'), $elxis->getConfig('SITENAME'))."\n\n";
			$body .= $eLang->get('CLICKACTIVATE')."\n";
			$body .= $elxis->makeURL('user:activate.html?c='.$row->activation, '', true)."\n\n\n";
			$body .= $eLang->get('REGARDS')."\n";
			$body .= $elxis->getConfig('SITENAME')."\n";
			$body .= $elxis->getConfig('URL')."\n\n\n\n";
			$body .= "_______________________________________________________________\n";
			$body .= $eLang->get('NOREPLYMSGINFO');

			$to = $row->email.','.$row->firstname.' '.$row->lastname;
			$elxis->sendmail($subject, $body, '', null, 'plain', $to);
		} else { //admins
			$db = eFactory::getDB();
			$sql = "SELECT ".$db->quoteId('firstname').",  ".$db->quoteId('lastname').",  ".$db->quoteId('email').",  ".$db->quoteId('preflang')
			."\n FROM  ".$db->quoteId('#__users')." WHERE  ".$db->quoteId('gid')." = 1 AND ".$db->quoteId('block')." = 0";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$admins = $stmt->fetchAll(PDO::FETCH_OBJ);

			if ($admins && (count($admins) > 0)) {
				foreach ($admins as $admin) {
					$userlang = trim($admin->preflang);
					if (($userlang != '') && ($userlang != $curlang)) {
						$eLang->switchLanguage($userlang);
						$curlang = $userlang;
					}

					$subject = $eLang->get('ACCOUNTACT');
					$body = $eLang->get('HI').' '.$admin->firstname.' '.$admin->lastname."\n";
					$body = sprintf($eLang->get('UCHANGEDEMAIL'), $row->uname)."\n";
					$body .= $eLang->get('YMUSTMANACTLOG')."\n\n";
					$body .= $eLang->get('USERNAME').": \t\t".$row->uname."\n";
					$body .= $eLang->get('FIRSTNAME').": \t\t".$row->firstname."\n";
					$body .= $eLang->get('LASTNAME').": \t\t".$row->lastname."\n";
					$body .= $eLang->get('EMAIL').": \t\t".$row->email."\n\n\n\n";
					$body .= $eLang->get('REGARDS')."\n";
					$body .= $elxis->getConfig('SITENAME')."\n";
					$body .= $elxis->getConfig('URL')."\n\n\n\n";
					$body .= "_______________________________________________________________\n";
					$body .= $eLang->get('NOREPLYMSGINFO');
				
					$to = $admin->email.','.$admin->firstname.' '.$admin->lastname;
					$elxis->sendmail($subject, $body, '', null, 'plain', $to);
				}
			}
		}

		if ($curlang != $original_language) {
			$eLang->switchLanguage($original_language);
		}
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}

}

?>