<?php 
/**
* @version		$Id: amembers.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class amembersUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY USERS LIST */
	/*********************************/
	public function listusers() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$avlangs = $this->model->getLanguages();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('MEMBERSLIST'));

		eFactory::getDocument()->setTitle($eLang->get('USERS_MANAGER').' - '.$elxis->getConfig('SITENAME'));
		$this->view->listusers($avlangs);
	}


	/******************************************************/
	/* RETURN LIST OF USERS FOR GRID IN XML FORMAT (AJAX) */
	/******************************************************/
	public function getusers() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();

		$sortcols = array('name', 'uname', 'block', 'groupname', 'email', 'registerdate', 'lastvisitdate');
		$querycols = array('uid', 'firstname', 'lastname', 'uname', 'email', 'city', 'address', 'phone', 'mobile', 'website');
		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'uname', 'sortorder' => 'asc',
			'qtype' => 'lastname', 'query' => '',
			'gender' => '', 'preflang' => '', 'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'uname';
		if ($options['sortname'] == 'name') { $options['sortname'] = 'firstname'; }
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'uname'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'lastname';
		if (($options['qtype'] == '') || !in_array($options['qtype'], $querycols)) { $options['qtype'] = 'lastname'; }
		$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
		$options['gender'] = (isset($_POST['gender'])) ? trim($_POST['gender']) : '';
		if (($options['gender'] != 'male') && ($options['gender'] != 'female')) { $options['gender'] = ''; }
		$options['preflang'] = trim(filter_input(INPUT_POST, 'preflang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (!file_exists(ELXIS_PATH.'/language/'.$options['preflang'].'/'.$options['preflang'].'.php')) { $options['preflang'] = ''; }

		$total = $this->model->countUsers($options);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getUsers($options);
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$active_icon = $elxis->icon('tick', 16);
			$inactive_icon = $elxis->icon('error', 16);
			$manage_groups = $elxis->acl()->check('com_user', 'groups', 'manage');
			$manage_link = $elxis->makeAURL('user:groups/edit.html');
			$canedit = ($elxis->acl()->check('com_user', 'profile', 'edit') > 1) ? true : false;
			$edit_link = $elxis->makeAURL('user:users/edit.html');
			foreach ($rows as $row) {
				$icon = (intval($row['block']) == 1) ? $inactive_icon : $active_icon;
				$emailtxt = (strlen($row['email']) > 20) ? substr($row['email'], 0, 17).'...' : $row['email'];
				if ($row['lastvisitdate'] == '1970-01-01 00:00:00') {
					$lastvisit = $eLang->get('NEVER');
				} else {
					$lastvisit = $eDate->formatDate($row['lastvisitdate'], $eLang->get('DATE_FORMAT_4'));
				}
				echo '<row id="'.$row['uid'].'">'."\n";
				echo '<cell>'.$row['uid']."</cell>\n";
				echo '<cell><![CDATA['.$row['firstname'].' '.$row['lastname']."]]></cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$edit_link.'?uid='.$row['uid'].'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$row['uname']."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$row['uname']."]]></cell>\n";
				}
				echo '<cell><![CDATA[<img src="'.$icon.'" alt="icon" border="0" />]]>'."</cell>\n";
				if ($manage_groups > 0) {
					echo '<cell><![CDATA[<a href="'.$manage_link.'?gid='.$row['gid'].'" title="'.$eLang->get('EDIT_GROUP').'" style="text-decoration:none;">'.$row['groupname']."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$row['groupname']."]]></cell>\n";
				}
				echo '<cell><![CDATA[<a href="mailto:'.$row['email'].'" title="'.$row['email'].'" style="text-decoration:none;">'.$emailtxt."</a>]]></cell>\n";
				echo '<cell><![CDATA['.$eDate->formatDate($row['registerdate'], $eLang->get('DATE_FORMAT_4'))."]]></cell>\n";
				echo '<cell><![CDATA['.$lastvisit."]]></cell>\n";
				echo '<cell>'.$row['articles']."</cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/************************/
	/* TOGGLE USER'S STATUS */
	/************************/
	public function toggleuser() {
		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		$response = $this->model->blockUser($uid, -1); //includes acl checks
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.$response['message'];
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/***************/
	/* DELETE USER */
	/***************/
	public function deleteuser() {
		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		$response = $this->model->deleteUser($uid, 'delete'); //includes acl checks
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.$response['message'];
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/****************************/
	/* PREPARE TO ADD/EDIT USER */
	/****************************/
	public function edituser() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$redirurl = $elxis->makeAURL('user:users/');

		$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : 0;
		if ($uid < 0) { $uid = 0; }
		
		if ($uid > 0) {
			$proceed = false;
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
			if ($proceed === false) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
			}

			$row = $this->model->getUser($uid);
			if (!$row) {
				$elxis->redirect($redirurl, $eLang->get('USERNFOUND'), true);
			}

			$userLevel = $this->model->getGroupLevel($row->gid);
			if ($elxis->acl()->getLevel() < $userLevel) {
				$elxis->redirect($redirurl, $eLang->get('NALLOW_HIGHER_ACCESS'), true);
			}
		} else {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
			if ($allowed !== 2) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
			}
			$row = new usersDbTable();
			$row->uid = 0;
			$row->gid = 5;
			$row->gender = 'male';
			$row->block = 1;
		}

		$info = new stdClass;
		$info->articles = 0;
		$info->comments = 0;
		if ($row->uid > 0) {
			$info->articles = $this->model->counter($row->uid, 'content', false);
			$info->comments = $this->model->counter($row->uid, 'comments', false);
		}

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$userparams = new elxisParameters($row->params, '', 'custom');		

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MEMBERSLIST'), '');
		if ($row->uid > 0) {
			$pathway->addNode($eLang->get('EDITPROFILE').' <strong>'.$row->uname.'</strong>');
		} else {
			$pathway->addNode($eLang->get('NEW_USER'));
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('user:users/'));

		$this->view->editUser($row, $info, $userparams);
	}


	/*********************/
	/* SAVE USER PROFILE */
	/*********************/
	public function saveuser() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eDate = eFactory::getDate();

		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
		if ($uid < 0) { $uid = 0; }

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CUSE-0011', $eLang->get('REQDROPPEDSEC'));
		}

		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if ($uid > 0) {
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
		} else {
			if ($allowed == 2) { $proceed = true; }
		}

		$redirurl = $elxis->makeAURL('user:users/');
		if ($proceed === false) {
			$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$oldpass = '';
		$row = new usersDbTable();
		if ($uid > 0) {
			if (!$row->load($uid)) {
				$elxis->redirect($redirurl, $eLang->get('USERNFOUND'), true);
			}
			$oldpass = $row->pword;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$redirurl = $elxis->makeAURL('user:users/edit.html?uid='.$uid);

		$pword = trim(filter_input(INPUT_POST, 'pword', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		$pword2 = filter_input(INPUT_POST, 'pword2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ($uid > 0) {
			if ($pword != '') {
				$len = eUTF::strlen($pword);
				if (($pword != $pword2) || ($len < 6)) {
					$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
					$elxis->redirect($redirurl, $errormsg, true);
				}
				$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
			} else {
				$row->pword = $oldpass;
			}
		} else {
			$len = eUTF::strlen($pword);
			if (($pword == '') || ($pword != $pword2) || ($len < 6)) {
				$errormsg = ($pword != $pword2) ? $eLang->get('PASSNOMATCH') : $eLang->get('INVALIDPASS');
				$elxis->redirect($redirurl, $errormsg, true);
			}
			$row->pword = $elxis->obj('crypt')->getEncryptedPassword($pword);
		}

		$userLevel = $this->model->getGroupLevel($row->gid);
		if ($elxis->acl()->getLevel() < $userLevel) {
			$elxis->redirect($redirurl, $eLang->get('NALLOW_HIGHER_ACCESS'), true);
		}

		$row->birthdate = trim($row->birthdate);
		if ($row->birthdate != '') {
			$newdate = $eDate->convertFormat($row->birthdate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d');
			if ($newdate !== false) { $row->birthdate = $newdate; } else { $row->birthdate = null; }
		}

		$row->expiredate = trim($row->expiredate);
		if ($row->expiredate != '') {
			$newdate = $eDate->convertFormat($row->expiredate, $eLang->get('DATE_FORMAT_BOX'), 'Y-m-d H:i:s');
			if ($newdate !== false) { $row->expiredate = $newdate; } else { $row->expiredate = '2060-01-01 00:00:00'; }
		} else {
			$row->expiredate = '2060-01-01 00:00:00';
		}

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

		$params = $this->base_getParams();
		$avsize = (int)$params->get('profile_avatar_width', 80);
		unset($params);

		$relpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}
		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
        	$newavatar = false;
			if (isset($_FILES['avatar']) && is_array($_FILES['avatar'])) {
				$tmpuid = 'temp'.rand(1000, 9999);
            	$file = $_FILES['avatar'];
				$eFiles = eFactory::getFiles();
            	$avname = eUTF::strtolower($file['name']);
            	$avname = preg_replace("/[\s]/", "_", $avname);
            	$avname = filter_var($avname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
            	$lowfilename = ($uid > 0) ? $row->uid.'_'.$avname : $tmpuid.'_'.$avname;
            	$ext = $eFiles->getExtension($lowfilename);
            	$valid_exts = array('jpg', 'jpeg', 'png', 'gif');
            	if (in_array($ext, $valid_exts)) {
                	if (file_exists(ELXIS_PATH.'/'.$relpath.$lowfilename)) {
                		$lowfilename = ($uid > 0) ? $row->uid.'_'.time().'.'.$ext : $tmpuid.'_'.time().'.'.$ext;
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
			if ((intval($uid) == 0) && (trim($row->avatar) != '')) {
				$eFiles->deleteFile($relpath.$row->avatar);
			}
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$ok = ($uid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			if ((intval($uid) == 0) && (trim($row->avatar) != '')) {
				$eFiles->deleteFile($relpath.$row->avatar);
			}
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ((intval($uid) == 0) && (trim($row->avatar) != '') && ($row->uid > 0) && isset($tmpuid)) {
			$newname = str_replace($tmpuid, $row->uid, $row->avatar);
			$ok = $eFiles->move($relpath.$row->avatar, $relpath.$newname);
			if ($ok) {
				$row->avatar = $newname;
				$row->update();
			}
		}

		$eSession->set('token_elxisform');

		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('user:users/edit.html?uid='.$row->uid) : $elxis->makeAURL('user:users/');
		$msg = ($uid > 0) ? $eLang->get('PROFUPSUC') : $eLang->get('ACCOUNT_CREATED_SUC');
		$elxis->redirect($redirurl, $msg);
	}

}

?>