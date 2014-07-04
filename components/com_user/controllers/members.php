<?php 
/**
* @version		$Id: members.php 1423 2013-05-01 09:35:25Z webgift $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class membersUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***********************************/
	/* PREPARE TO DISPLAY MEMBERS LIST */
	/***********************************/
	public function memberslist() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$eDoc->setTitle($eLang->get('MEMBERSLIST').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('MEMBERSLIST'));
		$eDoc->setKeywords(array($eLang->get('MEMBERSLIST'), $eLang->get('USER'), $eLang->get('PROFILE'), $eLang->get('USERNAME')));

		$allowed = $elxis->acl()->check('com_user', 'memberslist', 'view');
		if ($allowed < 1) {
			$eDoc->setTitle($eLang->get('MEMBERSLIST').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$db = eFactory::getDB();
		$sql = "SELECT COUNT(uid) FROM #__users WHERE ".$db->quoteId('block')."=0 AND ".$db->quoteId('expiredate')." > '".eFactory::getDate()->getDate()."'";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$total = (int)$stmt->fetchResult();

		$perpage = 20;
		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }
		$maxpage = ($total == 0) ? 1 : ceil($total/$perpage);
		if ($page > $maxpage) { $page = $maxpage; }
    	$limit = $perpage;
		$limitstart = (($page -1) * $limit);

		$order = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		switch($order) {
			case 'fa': $orderby = 'u.firstname ASC'; break;
			case 'fd': $orderby = 'u.firstname DESC'; break;
			case 'la': $orderby = 'u.lastname ASC'; break;
			case 'ld': $orderby = 'u.lastname DESC'; break;
			case 'ga': $orderby = 'u.groupname ASC'; break;
			case 'gd': $orderby = 'u.groupname DESC'; break;
			case 'pa': $orderby = 'u.preflang ASC'; break;
			case 'pd': $orderby = 'u.preflang DESC'; break;
			case 'ca': $orderby = 'u.country ASC'; break;
			case 'cd': $orderby = 'u.country DESC'; break;
			case 'wa': $orderby = 'u.website ASC'; break;
			case 'wd': $orderby = 'u.website DESC'; break;
			case 'gea': $orderby = 'u.gender ASC'; break;
			case 'ged': $orderby = 'u.gender DESC'; break;
			case 'ra': $orderby = 'u.registerdate ASC'; break;
			case 'rd': $orderby = 'u.registerdate DESC'; break;
			case 'lva': $orderby = 'u.lastvisitdate ASC'; break;
			case 'lvd': $orderby = 'u.lastvisitdate DESC'; break;
			case 'pva': $orderby = 'u.profle_views ASC'; break;
			case 'pvd': $orderby = 'u.profle_views DESC'; break;
			case 'ud': $orderby = 'u.uname DESC'; break;
			case 'ua': default: $order = 'ua'; $orderby = 'u.uname ASC'; break;
		}

		$sql = "SELECT u.*, s.last_activity FROM #__users u"
		."\n LEFT JOIN #__session s ON s.uid = u.uid"
		."\n WHERE u.block = 0 AND u.expiredate > '".eFactory::getDate()->getDate()."'"
		."\n GROUP BY u.uid ORDER BY ".$orderby;
		$stmt = $db->prepareLimit($sql, $limitstart, $limit);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

		if ($rows) {
			$extra_keys = array();
			foreach ($rows as $row) {
				$extra_keys[] = $row->uname;
			}
			$eDoc->setKeywords($extra_keys);
		}

		$txt = ($maxpage > 1) ? $eLang->get('MEMBERSLIST').' - '.$eLang->get('PAGE').' '.$page : $eLang->get('MEMBERSLIST');
		$eDoc->setTitle($txt.' - '.$elxis->getConfig('SITENAME'));
		$txt = $eLang->get('MEMBERSLIST').', '.$eLang->get('PAGE').' '.$page.'. '.sprintf($eLang->get('REGMEMBERSTOTAL'), $total);
		$eDoc->setDescription($txt);

		$params = $this->base_getParams();

		$columns = array();
		if (ELXIS_MOBILE == 0) {
			if ((int)$params->get('members_firstname', 0) == 1) { $columns[] = 'firstname'; }
			if ((int)$params->get('members_lastname', 0) == 1) { $columns[] = 'lastname'; }
			if ((int)$params->get('members_uname', 1) == 1) { $columns[] = 'uname'; }
			if ((int)$params->get('members_groupname', 1) == 1) { $columns[] = 'groupname'; }
			if ((int)$params->get('members_preflang', 1) == 1) { $columns[] = 'preflang'; }
			if ((int)$params->get('members_country', 0) == 1) { $columns[] = 'country'; }
			if ((int)$params->get('members_website', 0) == 1) { $columns[] = 'website'; }
			if ((int)$params->get('members_gender', 0) == 1) { $columns[] = 'gender'; }
			if ((int)$params->get('members_registerdate', 1) == 1) { $columns[] = 'registerdate'; }
			if ((int)$params->get('members_lastvisitdate', 1) == 1) { $columns[] = 'lastvisitdate'; }
			if ((int)$params->get('members_profile_views', 0) == 1) { $columns[] = 'profile_views'; }
		} else {
			$columns[] = 'uname';
			$columns[] = 'registerdate';
		}

		$nav_links = (int)$params->get('nav_links', 1);
		unset($params);
		if (count($columns) == 0) { $columns[] = 'uname'; }

		$this->view->membersList($rows, $columns, $total, $order, $page, $maxpage, $nav_links);
	}


	/***********************************/
	/* PREPARE TO DISPLAY USER PROFILE */
	/***********************************/
	public function profile() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();

		$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('USERPROFILE'));
		$eDoc->setKeywords(array($eLang->get('PROFILE'), $eLang->get('USER')));

		$segments = eFactory::getURI()->getSegments();
		if (count($segments) != 2) {
			exitPage::make('404', 'CUSE-0008'); //just in case
		}

		if ($segments[1] === 'myprofile.html') {
			$uid = $elxis->user()->uid;
		} else {
			$uid = str_ireplace('.html', '', $segments[1]);
			if (!is_numeric($uid)) {
				exitPage::make('404', 'CUSE-0009'); //just in case
			}			
		}

		$uid = (int)$uid;
		if ($uid < 1) {
			exitPage::make('404', 'CUSE-0010');
		}

		$allowed = (int)$elxis->acl()->check('com_user', 'profile', 'view');
		if ($allowed == 2) {
			$proceed = true;
		} else if (($allowed == 1) && ($uid == $elxis->user()->uid)) {
			$proceed = true;
		} else {
			$proceed = false;
		}

		if ($proceed === false) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$stmt = $db->prepareLimit("SELECT * FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uid')." = :userid", 0, 1);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_OBJ);
		if (!$row) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERNFOUND'));
			return;
		}

		$usname = ($elxis->getConfig('REALNAME') == 1) ? $row->firstname.' '.$row->lastname : $row->uname;
		$eDoc->setTitle($eLang->get('USERPROFILE').' '.$usname.' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('PROFILEUSERAT'), $usname, $elxis->getConfig('SITENAME'));
		$eDoc->setDescription($desc);
		$eDoc->setKeywords(array($row->firstname, $row->lastname, $row->uname));
		unset($desc);

		if ($row->block == 1) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' '.$usname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTBLOCKED'));
			return;
		}

		if ($row->expiredate < gmdate('Y-m-d H:i:s')) {
			$eDoc->setTitle($eLang->get('USERPROFILE').' '.$usname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTEXPIRED'));
			return;
		}

		$row->is_online = 0;
		$row->ip_address = null;
		$row->time_online = 0;
		$row->clicks = 0;
		$row->current_page = null;
		$row->browser = null;

		if ($elxis->user()->uid <> $row->uid) {
			$row->profile_views++;
			$this->model->incrementProfileViews($row->uid, $row->profile_views);
		}

		$sql = "SELECT ".$db->quoteId('first_activity').", ".$db->quoteId('last_activity').", ".$db->quoteId('clicks').","
		."\n ".$db->quoteId('current_page').", ".$db->quoteId('ip_address').", ".$db->quoteId('user_agent')
		."\n FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('uid')." = :userid ORDER BY ".$db->quoteId('last_activity')." DESC";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$sess = $stmt->fetch(PDO::FETCH_OBJ);
		if ($sess) {
			if ($sess->last_activity + $elxis->getConfig('SESSION_LIFETIME') >= time()) {
				$row->is_online = 1;
				$row->ip_address = trim($sess->ip_address);
				$row->time_online = intval($sess->last_activity - $sess->first_activity);
				if ($row->time_online < 1) { $row->time_online = 1; }
				$row->clicks = (int)$sess->clicks;
				$row->current_page = trim($sess->current_page);
				$row->user_agent = trim($sess->user_agent);
				
				$browser_info = $elxis->obj('browser')->getBrowser($row->user_agent);
				$row->browser = $browser_info['browser'].' '.$browser_info['version'];
				unset($browser_info);
			}
		}
		unset($sess, $stmt);

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$params = $this->base_getParams();
		$gravatar = (int)$params->get('gravatar', 0);
		$avsize = (int)$params->get('profile_avatar_width', 80);
		$profile_comments = (int)$params->get('profile_comments', 0);
		$row->avatar = $elxis->obj('avatar')->getAvatar($row->avatar, $avsize, $gravatar, $row->email);
		unset($gravatar, $avsize);

		$comments = array();
		if (($uid > 0) && ($profile_comments > 0)) {
			$comments = $this->model->fetchUserComments($uid, $profile_comments);
		}
		unset($profile_comments);

		$twitter = null;
		if (intval($params->get('profile_twitter', 0)) == 1) {
			$twitter_username = trim($userparams->get('twitter', ''));
			if ($twitter_username != '') {
				$tw = $elxis->obj('twitter');
				$twitter = $tw->getInfo($twitter_username);
				if ($twitter !== false) {
					$twitter->tweets = $tw->getTweets($twitter_username, 5);
				}
				unset($tw);
			}
			unset($twitter_username);
		}

		$this->view->userProfile($row, $params, $userparams, $usname, $twitter, $comments);
	}


	/************************/
	/* BLOCK A USER ACCOUNT */
	/************************/
	public function blockaccount() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id < 0) { $id = 0; }
		$this->base_forceSSL('user:members/block.html?id='.$id);

		$response = $this->model->blockUser($id, 1);
		if ($response['success'] === false) {
			eFactory::getDocument()->setTitle($eLang->get('BLOCKUSER').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($response['message']);
			return;
		}

		eFactory::getDocument()->setTitle($eLang->get('BLOCKUSER').' - '.$elxis->getConfig('SITENAME'));
		$url = $elxis->makeURL('user:/');
		$elxis->redirect($url, $response['message']);
	}


	/*************************/
	/* DELETE A USER ACCOUNT */
	/*************************/
	public function deleteaccount() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		if ($id < 0) { $id = 0; }
		$this->base_forceSSL('user:members/delete.html?id='.$id);

		$response = $this->model->deleteUser($id, 'unpublish');
		if ($response['success'] === false) {
			eFactory::getDocument()->setTitle($eLang->get('DELETEACCOUNT').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($response['message']);
			return;
		}

		eFactory::getDocument()->setTitle($eLang->get('DELETEACCOUNT').' - '.$elxis->getConfig('SITENAME'));
		$url = $elxis->makeURL('user:/');
		$elxis->redirect($url, $response['message']);
	}


	/**********************************/
	/* PREPARE TO EDIT USER'S PROFILE */
	/**********************************/
	public function editprofile($row=null, $errormsg='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if (($row == null) || !is_object($row)) {
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			if ($id < 0) { $id = 0; }
			$this->base_forceSSL('user:members/edit.html?id='.$id);

			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$elxis->getConfig('SITENAME'));

			$proceed = false;
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $id))) { $proceed = true; }
			if (($id == 0) || ($proceed === false)) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
				return;
			}

			$row = $this->model->getUser($id, 0);
			if (!$row) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('USERNFOUND'));
				return;
			}
			$userLevel = $this->model->getGroupLevel($row->gid);
			if ($elxis->acl()->getLevel() < $userLevel) {
				$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
				$this->view->base_errorScreen($eLang->get('NALLOW_HIGHER_ACCESS'));
				return;
			}
		}

		$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$elxis->getConfig('SITENAME'));
		$eDoc->setDescription($eLang->get('EDITPROFILE').' '.$row->firstname.' '.$row->lastname);
		$eDoc->setKeywords(array($eLang->get('EDITPROFILE'), $eLang->get('USER'), $row->uname, $row->firstname, $row->lastname));

		$params = $this->base_getParams();
		$gravatar = (int)$params->get('gravatar', 0);
		$avsize = (int)$params->get('profile_avatar_width', 80);
		$row->avatar = $elxis->obj('avatar')->getAvatar($row->avatar, $avsize, $gravatar, $row->email);
		unset($gravatar, $avsize, $params);

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$this->view->editProfile($row, $userparams, $errormsg);
	}


	/*********************/
	/* SAVE USER PROFILE */
	/*********************/
	public function saveprofile() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$id = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		if (($id < 1) || ($elxis->user()->gid == 7) || ($elxis->user()->gid == 6)) {
			$url = $elxis->makeURL('user:/');
			$elxis->redirect($url);
		}

		$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$elxis->getConfig('SITENAME'));

		$eSession = eFactory::getSession();
		$sess_token = trim($eSession->get('token_fmeditprof'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('REQDROPPEDSEC'));
			return;
		}

		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $id))) { $proceed = true; }
		if ($proceed === false) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		$row = new usersDbTable();
		if (!$row->load($id)) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('USERNFOUND'));
			return;
		}

		if (intval($row->block) == 1) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTBLOCKED'));
			return;
		}

		if ($row->expiredate < gmdate('Y-m-d H:i:s')) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($eLang->get('ACCOUNTEXPIRED'));
			return;
		}

		$row->firstname = filter_input(INPUT_POST, 'firstname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->occupation = filter_input(INPUT_POST, 'occupation', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		if (strlen($row->country) > 3) { $row->country = null; }
		$row->city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->postalcode = filter_input(INPUT_POST, 'postalcode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->mobile = filter_input(INPUT_POST, 'mobile', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$row->website = filter_input(INPUT_POST, 'website', FILTER_SANITIZE_URL);
		$row->preflang = filter_input(INPUT_POST, 'preflang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$row->timezone = filter_input(INPUT_POST, 'timezone', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

		$new_activation_by_user = false;
		$new_activation_by_admin = false;

		if ($elxis->getConfig('SECURITY_LEVEL') < 2) {
			$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
			if ($email != $row->email) {
				$row->email = $email;
				if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 2) {
					if ($elxis->user()->gid <> 1) {
						$row->block = 1;
						$new_activation_by_admin = true;
					}
				} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 1) {
					if ($elxis->user()->gid <> 1) {
						$row->block = 1;
						$act = '';
						while (strlen($act) < 40) { $act .= mt_rand(0, mt_getrandmax()); }
						$row->activation = sha1($act);
						$new_activation_by_user = true;
					}
				}
			}
		}

		$pword = filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword2 = filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (trim($pword) != '') {
			$len = eUTF::strlen($pword);
			if (($pword != $pword2) || ($len < 6)) {
				$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
				$row->pword = '';
				$this->editprofile($row, $errormsg);
				return;
			}

			$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
		}

		$sess_captcha = trim($eSession->get('captcha_seccode'));
		$seccode = trim(filter_input(INPUT_POST, 'seccode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($sess_captcha == '') || ($seccode == '') || ($seccode != $sess_captcha)) {
			$row->pword = '';
			$this->editprofile($row, $eLang->get('INVALIDSECCODE'));
			return;
		}

		$birthdate = filter_input(INPUT_POST, 'birthdate', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$row->birthdate = null;
		if (trim($birthdate) != '') {
			$newdate = eFactory::getDate()->convertFormat($birthdate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d');
			if ($newdate === false) {
			 	$errormsg = 'Invalid date '.$eLang->get('BIRTHDATE');
			 	$row->pword = '';
			 	$this->editprofile($row, $errormsg);
			 	return;
			}
			$row->birthdate = $newdate;
		}

		$params = $this->base_getParams();
		$avsize = (int)$params->get('profile_avatar_width', 80);
		unset($params);

		//process params
		$row->params = '';
		$pat = "#([\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\|]|[\{]|[\}]|[\\\])#u";
		foreach ($_POST as $key => $val) {
			if (strpos($key, 'params_') === 0) {
				$param_name = substr($key, 7);
				$param_val = filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
				$param_val = eUTF::trim(preg_replace($pat, '', $param_val));
				$row->params .= $param_name.'='.$param_val."\n";
			}
		}
		if ($row->params == '') { $row->params = null; }

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
        	$newavatar = false;
			$relpath = 'media/images/avatars/';
			if (defined('ELXIS_MULTISITE')) {
				if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
			}
			if (isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
            	$file = $_FILES['avatar'];
				$eFiles = eFactory::getFiles();
            	$avname = eUTF::strtolower($file['name']);
            	$avname = preg_replace("/[\s]/", "_", $avname);
            	$avname = filter_var($avname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            	$lowfilename = $row->uid.'_'.$avname;
            	$ext = $eFiles->getExtension($lowfilename);
            	$valid_exts = array('jpg', 'jpeg', 'png', 'gif');
            	if (in_array($ext, $valid_exts)) {
                	if (file_exists(ELXIS_PATH.'/'.$relpath.$lowfilename)) {
                    	$lowfilename = $row->uid.'_'.time().'.'.$ext;
                	}
                	if ($eFiles->upload($file['tmp_name'], $relpath.$lowfilename)) {
						$newavatar = true;
                    	$isize = getimagesize(ELXIS_PATH.'/'.$relpath.$lowfilename);
                    	if (($isize[0] != $avsize) || ($isize[1] != $avsize)) {
                    		if (!$eFiles->resizeImage($relpath.$lowfilename, $avsize, $avsize)) {
                    			$eFiles->deleteFile($relpath.$lowfilename);
                    			$newavatar = false;
                    		}
                    	}
                	}
            	}
        	}

        	if ($newavatar) {
        		if ((trim($row->avatar) != '') && ($row->avatar != 'noavatar.png') && file_exists(ELXIS_PATH.'/'.$relpath.$row->avatar)) {
        			$eFiles->deleteFile($relpath.$row->avatar);
        		}
            	$row->avatar = $lowfilename;
        	}
        	unset($file, $avname, $lowfilename, $ext, $valid_exts);
		}

		if (!$row->fullCheck()) {
			$errormsg = $row->getErrorMsg();
			$row->pword = '';
			$this->editprofile($row, $errormsg);
			return;
		}

		if (!$row->update()) {
			$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname.' - '.$eLang->get('ERROR'));
			$this->view->base_errorScreen($row->getErrorMsg());
			return;
		}

		$eSession->set('token_fmeditprof');

		if ($new_activation_by_admin === true) {
			$id = $row->uid;
			$db = eFactory::getDB();
			$sql = "DELETE FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('uid')." = :uid";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $id, PDO::PARAM_INT);
			$stmt->execute();

			$this->mailReactivateAccount($row, 'admin');

			$msg = $eLang->get('PROFUPSUC')."<br />\n";
			$msg .= sprintf($eLang->get('USERACCBLOCKED'), $row->uname)."<br />\n";
			$msg .=	$eLang->get('ADMINMREACT');
		} else if ($new_activation_by_user === true) {
			$id = $row->uid;
			$db = eFactory::getDB();
			$sql = "DELETE FROM ".$db->quoteId('#__session')." WHERE ".$db->quoteId('uid')." = :uid";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $id, PDO::PARAM_INT);
			$stmt->execute();

			$this->mailReactivateAccount($row, 'user');
			
			$msg = $eLang->get('PROFUPSUC')."<br />\n";
			if ($row->uid == $elxis->user()->uid) {
				$msg .= sprintf($eLang->get('EMAILATCHANGED'), $elxis->getConfig('SITENAME'))."<br />\n";
				$msg .= $eLang->get('MAILACTLINK');
			} else {
				$msg .= $eLang->get('USERMREACT');
			}
		} else {
			$msg = $eLang->get('PROFUPSUC');
		}

		$eDoc->setTitle($eLang->get('EDITPROFILE').' '.$row->uname);
		$this->view->profileSuccess($row, $msg);
	}

}

?>