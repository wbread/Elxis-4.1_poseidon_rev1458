<?php 
/**
* @version		$Id: agroups.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class agroupsUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		if (eFactory::getElxis()->acl()->check('com_user', 'groups', 'manage') < 1) {
			exitPage::make('403', 'CUSE-0012');
		}
		parent::__construct($view, $task, $model);
	}


	/***************************************/
	/* PREPARE TO DISPLAY USER GROUPS LIST */
	/***************************************/
	public function listgroups() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('USER_GROUPS'));

		eFactory::getDocument()->setTitle($eLang->get('USER_GROUPS').' - '.$elxis->getConfig('SITENAME'));
		$this->view->listgroups();
	}


	/*******************************************************/
	/* RETURN LIST OF GROUPS FOR GRID IN XML FORMAT (AJAX) */
	/*******************************************************/
	public function getgroups() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();

		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'level', 'sortorder' => 'desc',
			'qtype' => '', 'query' => '',
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'level';
		if (($options['sortname'] == '') || !in_array($options['sortname'], array('gid', 'level'))) { $options['sortname'] = 'level'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'desc';
		if ($options['sortorder'] != 'asc') { $options['sortorder'] = 'desc'; }

		$total = $this->model->countGroups();

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getGroups($options);
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$edit_link = $elxis->makeAURL('user:groups/edit.html');
			foreach ($rows as $row) {
				switch ($row['gid']) {
					case 1: $groupname = $eLang->get('ADMINISTRATOR'); break;
					case 5: $groupname = $eLang->get('USER'); break;
					case 6: $groupname = $eLang->get('EXTERNALUSER'); break;
					case 7: $groupname = $eLang->get('GUEST'); break;
					default: $groupname = $row['groupname']; break;
				}
				echo '<row id="'.$row['gid'].'">'."\n";
				echo '<cell>'.$row['gid']."</cell>\n";
				echo '<cell>'.$row['level']."</cell>\n";
				echo '<cell><![CDATA[<a href="'.$edit_link.'?gid='.$row['gid'].'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$groupname."</a>]]></cell>\n";
				echo '<cell>'.$row['members']."</cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/****************/
	/* DELETE GROUP */
	/****************/
	public function deletegroup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
		if ($gid < 0) { $gid = 0; }

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$this->ajaxHeaders('text/plain');
			echo '0|The deletion of user groups is not allowed under the current security level!';
			exit();
		}

		if (in_array($gid, array(1, 5, 6, 7))) {
			$this->ajaxHeaders('text/plain');
			$response = addslashes($eLang->get('CNOT_DEL_GROUP'));
			echo '0|'.$response;
			exit();
		}

		$group = $this->model->getGroup($gid);
		if (!$group) {
			$this->ajaxHeaders('text/plain');
			$response = addslashes($eLang->get('GROUPNFOUND'));
			echo '0|'.$response;
			exit();
		}

		if ($group['level'] >= $elxis->acl()->getLevel()) {
			$this->ajaxHeaders('text/plain');
			$response = addslashes($eLang->get('NOTALLOWACTION'));
			echo '0|'.$response;
			exit();
		}

		if ($group['members'] > 0) {
			$this->ajaxHeaders('text/plain');
			$response = addslashes($eLang->get('CNOT_DEL_GROUP_MEMBERS'));
			echo '0|'.$response;
			exit();
		}

		$ok = $this->model->deleteGroup($gid);
		$this->ajaxHeaders('text/plain');
		if ($ok) {
			echo '1|Success';
		} else {
			echo '0|Action failed!';
		}
		exit();
	}


	/**********************************/
	/* PREPARE TO ADD/EDIT USER GROUP */
	/**********************************/
	public function editgroup() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$redirurl = $elxis->makeAURL('user:groups/');

		$gid = isset($_GET['gid']) ? (int)$_GET['gid'] : 0;
		if ($gid < 0) { $gid = 0; }

		$readonly = false;
		if (in_array($gid, array(1, 5, 6, 7))) { $readonly = true; }

		$acllist = false;
		if ($gid > 0) {
			$row = $this->model->getGroup($gid);
			if (!$row) {
				$elxis->redirect($redirurl, $eLang->get('GROUPNFOUND'), true);
			}

			if ($row['level'] == $elxis->acl()->getLevel()) { $readonly = true; }
			if ($row['level'] > $elxis->acl()->getLevel()) {
				$elxis->redirect($redirurl, $eLang->get('NEED_HIGHER_ACCESS'), true);
			}
			if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
				$acllist = $elxis->acl()->getSpecificLists($row['level'], $row['gid'], 0);
			}
		} else {
			$row = array();
			$row['gid'] = 0;
			$row['level'] = 2;
			$row['groupname'] = '';
			$row['members'] = 0;
		}

		$options = array('limitstart' => 0, 'rp' => 1000, 'sortname' => 'level', 'sortorder' => 'DESC');
		$groups = $this->model->getGroups($options);

		$tree = array();
		if ($groups) {
			$lastlevel = -1;
			$space = '';
			foreach ($groups as $group) {
				if ($group['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
				} else if ($group['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($group['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($group['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
				} else {
					$groupname = $group['groupname'];
				}

				if ($group['level'] != $lastlevel) {
					$space .= ($lastlevel == -1) ? '' : '&#160; &#160; ';
					$lastlevel = $group['level'];
				}
				$item = new stdClass;
				$item->gid = $group['gid'];
				$item->level = $group['level'];
				$item->groupname = $groupname;
				if ($row['gid'] == $group['gid']) {
					$item->treename = $space.$group['level'].' - <strong>'.$groupname.'</strong>';
				} else {
					$item->treename = $space.$group['level'].' - '.$groupname;
				}
				$tree[] = $item;
				unset($item);
			}
		}
		unset($groups, $options);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USER_GROUPS'), '');
		if ($gid > 0) {
			$pathway->addNode($eLang->get('EDIT_GROUP').' <strong>'.$row['groupname'].'</strong>');
			eFactory::getDocument()->setTitle($eLang->get('EDIT_GROUP'));
		} else {
			$pathway->addNode($eLang->get('NEW_GROUP'));
			eFactory::getDocument()->setTitle($eLang->get('NEW_GROUP'));
		}

		$toolbar = $elxis->obj('toolbar');
		if (!$readonly) {
			$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
			$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		}
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('user:groups/'));

		$this->view->editGroup($row, $tree, $acllist, $readonly);
	}


	/*******************/
	/* SAVE USER GROUP */
	/*******************/
	public function savegroup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : 0;
		if ($gid < 0) { $gid = 0; }

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CUSE-0013', $eLang->get('REQDROPPEDSEC'));
		}

		$redirurl = $elxis->makeAURL('user:groups/');

		if (in_array($gid, array(1, 5, 6, 7))) {
			$elxis->redirect($redirurl, $eLang->get('CNOT_MOD_GROUP'), true);
		}

		$row = new groupsDbTable();
		if ($gid > 0) {
			if (!$row->load($gid)) {
				$elxis->redirect($redirurl, $eLang->get('GROUPNFOUND'), true);
			}
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->groupname = eUTF::trim(filter_input(INPUT_POST, 'groupname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if ($row->groupname == '') {
			$msg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('GROUP'));
			$elxis->redirect($redirurl, $msg, true);
		}

		$row->level = (isset($_POST['level'])) ? (int)$_POST['level'] : 0;
		if (($row->level < 2) || ($row->level > 99)) {
			$elxis->redirect($redirurl, 'Custom groups should have access from 2 to 99', true);
		}

		$mylevel = $elxis->acl()->getLevel();
		if ($row->level >= $mylevel) {
			$elxis->redirect($redirurl, 'You can manage groups up to level of '.$mylevel.'!', true);
		}

		$redirurl = $elxis->makeAURL('user:groups/edit.html?gid='.$gid);

		$ok = ($gid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($row->gid > 999) {
			$this->model->deleteGroup($row->gid);
			$redirurl = $elxis->makeAURL('user:groups/');
			$elxis->redirect($redirurl, 'A user group can not have an id greater than 999! Contact Elxis Team for support.', true);
		}

		$eSession->set('token_fmelxisform');

		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('user:groups/edit.html?gid='.$row->gid) : $elxis->makeAURL('user:groups/');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}

?>