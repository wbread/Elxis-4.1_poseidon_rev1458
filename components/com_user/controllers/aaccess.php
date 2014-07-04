<?php 
/**
* @version		$Id: aaccess.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aaccessUserController extends userController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null, $json=false) {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_user', 'acl', 'manage') < 1) {
			if ($json) {
				$response = array();
				$response['error'] = 1;
				$response['errormsg'] = 'You are not allowed to manage ACL!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit();
			} else {
				exitPage::make('403', 'CUSE-0014');
			}
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			if ($elxis->user()->gid <> 1) {
				$msg = eFactory::getLang()->get('SECLEVEL_ACC_ADMIN');
				if ($json) {
					$response = array();
					$response['error'] = 1;
					$response['errormsg'] = $msg;
					$this->ajaxHeaders('application/json');
					echo json_encode($response);
					exit();
				} else {
					$redirurl = $elxis->makeAURL('cpanel:/');
					$elxis->redirect($redirurl, $msg, true);					
				}
			}
		}

		parent::__construct($view, $task, $model);
	}


	/*******************************/
	/* PREPARE TO DISPLAY ACL LIST */
	/*******************************/
	public function listacl() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('USERS_MANAGER'), 'user:users/');
		$pathway->addNode($eLang->get('ACL'));

		eFactory::getDocument()->setTitle($eLang->get('ACL').' - '.$elxis->getConfig('SITENAME'));
		$this->view->listacl();
	}


	/****************************************************/
	/* RETURN LIST OF ACL FOR GRID IN XML FORMAT (AJAX) */
	/****************************************************/
	public function getacl() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$sortcols = array('category', 'element', 'action', 'minlevel', 'gid', 'uid', 'aclvalue');
		$querycols = array('category', 'element', 'action', 'minlevel', 'gid', 'uid');

		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'uname', 'sortorder' => 'asc',
			'qtype' => 'category', 'query' => '',
			'translations' => 1, 'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'category';
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'category'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'lastname';
		if (($options['qtype'] == '') || !in_array($options['qtype'], $querycols)) { $options['qtype'] = 'lastname'; }
		$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
		$options['translations'] = (isset($_POST['translations'])) ? (int)$_POST['translations'] : 1;

		$total = $this->model->countACL($options);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getACL($options);
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');
		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			foreach ($rows as $row) {
				$elem_txt = $row['element'];
				$action_txt = $row['action'];
				$allowed_txt = $row['aclvalue'];
				if ($options['translations'] == 1) {
					$elem_txt = $eLang->silentGet($row['element'], true);
					$action_txt = $eLang->silentGet($row['action'], true);
					if ($row['aclvalue'] == 0) {
						$allowed_txt = '<span style="color:#FF0000;">'.$eLang->get('DENIED').'</span>';
					} elseif ($row['aclvalue'] == 1) {
						$allowed_txt = '<span style="color:#008000;">'.$eLang->get('ALLOWED').'</span>';
					} else if ($row['aclvalue'] == 2) {
						$allowed_txt = '<span style="color:#008000;">'.$eLang->get('ALLOWED_TO_ALL').'</span>';
					} else {
						$allowed_txt = $row['aclvalue'];
					}
				}

				echo '<row id="'.$row['id'].'">'."\n";
				echo '<cell><![CDATA['.$row['category']."]]></cell>\n";
				echo '<cell><![CDATA['.$elem_txt."]]></cell>\n";
				echo '<cell>'.$row['identity']."</cell>\n";
				echo '<cell><![CDATA['.$action_txt."]]></cell>\n";
				echo '<cell>'.$row['minlevel']."</cell>\n";
				echo '<cell>'.$row['gid']."</cell>\n";
				echo '<cell>'.$row['uid']."</cell>\n";
				echo '<cell><![CDATA['.$allowed_txt."]]></cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/*************************/
	/* DELETE ACL ELEMENT(S) */
	/*************************/
	public function deleteacl() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$this->ajaxHeaders('text/plain');
			echo '0|The deletion of ACL elements is not allowed under the current security level!';
			exit();
		}

		$ids = trim(filter_input(INPUT_POST, 'ids', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fids = explode(',', $ids);
		$ids_delete = array();
		if ($fids) {
			foreach ($fids as $fid) {
				$v = (int)$fid;
				if ($v > 0) { $ids_delete[] = $v; }
			}
		}

		$ids_delete = array_unique($ids_delete);
		if (count($ids_delete) == 0) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NO_ITEMS_SELECTED'));
			exit();
		}

		$ok = $this->model->deleteACL($ids_delete);
		$this->ajaxHeaders('text/plain');
		if ($ok) {
			echo '1|Success';
		} else {
			echo '0|Action failed!';
		}
		exit();
	}


	/********************************/
	/* PREPARE TO ADD/EDIT/SAVE ACL */
	/********************************/
	public function editacl() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$errormsg = '';
		$sucmsg = '';
		$id = 0;
		if (isset($_POST['aclbtn'])) {
			$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
			$lck = isset($_POST['lck']) ? (int)$_POST['lck'] : 0;
		} else {
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			$lck = isset($_GET['lck']) ? (int)$_GET['lck'] : 0;
		}
		if ($id < 0) { $id = 0; }

		$row = new aclDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				echo '<div class="elx_error">Could not load ACL element!</div>'."\n";
				return;
			}
		}

		$data = array();
		$data['categories'] = array('administration', 'component', 'module');
		$data['elements'] = array('acl', 'article', 'backup', 'category', 'comments', 'groups', 'interface', 'memberslist', 'menu', 'profile', 'routes', 'settings');
		$comps = $eFiles->listFolders('components/');
		if ($comps) {
			$data['categories'] = array_merge($data['categories'], $comps);
			$data['elements'] = array_merge($data['elements'], $comps);
		}
		$mods = $eFiles->listFolders('modules/');
		if ($mods) {
			$data['categories'] = array_merge($data['categories'], $mods);
			$data['elements'] = array_merge($data['elements'], $mods);
		}
		unset($comps, $mods);
		sort($data['categories']);
		sort($data['elements']);

		$data['actions'] = array('view', 'manage', 'add', 'edit', 'delete', 'publish', 'block', 'login', 'post', 'uploadavatar', 
		'viewaddress','viewage', 'viewemail', 'viewgender', 'viewmobile', 'viewphone', 'viewwebsite');

		$options = array('limitstart' => 0, 'rp' => 999, 'sortname' => 'level', 'sortorder' => 'DESC');
		$data['groups'] = $this->model->getGroups($options);
		unset($options);

		$n = $this->model->countUsers(array());
		$data['users'] = array();
		if ($n < 50) {
			$sortname = ($elxis->getConfig('REALNAME') == 1) ? 'firstname' : 'uname';
			$options = array('limitstart' => 0, 'rp' => 999, 'sortname' => $sortname, 'sortorder' => 'ASC');
			$data['users'] = $this->model->getUsers($options, false);
			unset($options, $sortname);
		}

		if (isset($_POST['aclbtn'])) {
			$sess_token = trim(eFactory::getSession()->get('token_aclform'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				echo '<div class="elx_error">'.$eLang->get('REQDROPPEDSEC')."</div>\n";
				return;
			}

			if (!$row->bind($_POST)) {
				echo '<div class="elx_error">Could not bind POST data into ACL element!</div>'."\n";
				return;
			}

			if ($row->uid > 0) {
				$row->gid = 0;
				$row->minlevel = -1;
			} elseif ($row->gid > 0) {
				$row->uid = 0;
				$row->minlevel = -1;
			} else {
				$row->gid = 0;
				$row->uid = 0;
				if ($row->minlevel < 0) { $row->minlevel = 0; }
			}

			$category2 = trim($_POST['category2']);
			if ($category2 != '') { $row->category = $category2; }
			$row->category = strtolower($row->category);
			$element2 = trim($_POST['element2']);
			if ($element2 != '') { $row->element = $element2; }
			$row->element = strtolower($row->element);
			$action2 = trim($_POST['action2']);
			if ($action2 != '') { $row->action = $action2; }
			$row->action = strtolower($row->action);

			$category_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->category);
			$element_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->element);
			$action_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->action);

			if ($row->category == '') {
				$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CATEGORY'));
			} else if ($row->element == '') {
				$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ELEMENT'));
			} else if ($row->action == '') {
				$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ACTION'));
			} else if ($row->category != $category_sanitized) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('CATEGORY'));
			} else if ($row->element != $element_sanitized) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ELEMENT'));
			} else if ($row->action != $action_sanitized) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ACTION'));
			} else if (($row->category == 'module') && ($row->identity == 0)) {
				$errormsg = $eLang->get('MODID_NOTSET');
			} else {
				$continue = true;
				$count_mr = $this->model->countMatchRules($id, $row->category, $row->element, $row->identity, $row->action, $row->minlevel, $row->gid, $row->uid);
				if ($count_mr > 0) {
					$continue = false;
					$errormsg = $eLang->get('ACCRULE_EXISTS');
				}
				if ($row->minlevel > -1) {
					$count_lr = $this->model->countLevelRules($id, $row->category, $row->element, $row->identity, $row->action);
					if ($count_lr > 0) {
						$continue = false;
						$errormsg = $eLang->get('ACCRULE_MINLEVEL_EXISTS');
					}
				}
				if ($continue) {
					$ok = ($id > 0) ? $row->update() : $row->insert();
					if (!$ok) {
						$errormsg = $row->getErrorMsg();
					} else {
						eFactory::getSession()->set('token_aclform');
						$sucmsg = $eLang->get('ITEM_SAVED');
					}
				}
			}
		}

		$this->view->editACL($row, $data, $lck, $errormsg, $sucmsg);
	}


	/***************************************************************************/
	/* SAVE ACL FROM A POST REQUEST AND REPLY AS JSON (used by com_extmanager) */
	/***************************************************************************/
	public function savejson() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		$response = array();
		$response['error'] = 0;
		$response['errormsg'] = '';

		$row = new aclDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				$response['error'] = 1;
				$response['errormsg'] = 'Could not load ACL element!';
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit();
			}
		}

		$minlevel = (isset($_POST['minlevel'])) ? (int)$_POST['minlevel'] : 0;
		$gid = (isset($_POST['gid'])) ? (int)$_POST['gid'] : 0;
		$uid = (isset($_POST['uid'])) ? (int)$_POST['uid'] : 0;
		$aclvalue = (isset($_POST['aclvalue'])) ? (int)$_POST['aclvalue'] : 1;
		$identity = (isset($_POST['identity'])) ? (int)$_POST['identity'] : 0;
		if ($aclvalue < 0) { $aclvalue = 0; }
		if ($identity < 0) { $identity = 0; }
		if ($uid > 0) {
			$user = $this->model->getUser($uid);
			if (!$user) {
				$response['error'] = 1;
				$response['errormsg'] = $eLang->get('USERNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit();
			}
			$gid = 0;
			$minlevel = -1;			
		} elseif ($gid > 0) {
			$group = $this->model->getGroup($gid);
			if (!$group) {
				$response['error'] = 1;
				$response['errormsg'] = $eLang->get('GROUPNFOUND');
				$this->ajaxHeaders('application/json');
				echo json_encode($response);
				exit();
			}
			$uid = 0;
			$minlevel = -1;
		} else {
			$gid = 0;
			$uid = 0;
			if ($minlevel < 0) { $minlevel = 0; }
		}

		$row->minlevel = $minlevel;
		$row->gid = $gid;
		$row->uid = $uid;
		$row->aclvalue = $aclvalue;
	
		if ($id == 0) {
			$row->identity = $identity;
			$row->category = strtolower(trim($_POST['category']));
			$row->element = strtolower(trim($_POST['element']));
			$row->action = strtolower(trim($_POST['action']));
			$category_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->category);
			$element_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->element);
			$action_sanitized = preg_replace('/[^A-Z\-\_0-9]/i', '', $row->action);
		}

		if ($row->category == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CATEGORY'));
		} else if ($row->element == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ELEMENT'));
		} else if ($row->action == '') {
			$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('ACTION'));
		} else if ($row->category != $category_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('CATEGORY'));
		} else if ($row->element != $element_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ELEMENT'));
		} else if ($row->action != $action_sanitized) {
			$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('ACTION'));
		} else if (($row->category == 'module') && ($row->identity == 0)) {
			$errormsg = $eLang->get('MODID_NOTSET');
		} else {
			$continue = true;
			$count_mr = $this->model->countMatchRules($id, $row->category, $row->element, $row->identity, $row->action, $row->minlevel, $row->gid, $row->uid);
			if ($count_mr > 0) {
				$continue = false;
				$errormsg = $eLang->get('ACCRULE_EXISTS');
			}
			if ($row->minlevel > -1) {
				$count_lr = $this->model->countLevelRules($id, $row->category, $row->element, $row->identity, $row->action);
				if ($count_lr > 0) {
					$continue = false;
					$errormsg = $eLang->get('ACCRULE_MINLEVEL_EXISTS');
				}
			}
			if ($continue) {
				$ok = ($id > 0) ? $row->update() : $row->insert();
				if (!$ok) {
					$errormsg = $row->getErrorMsg();
				}
			}
		}

		if ($errormsg != '') {
			$response['error'] = 1;
			$response['errormsg'] = $errormsg;
			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit();
		} else {
			switch ($row->gid) {
				case 0: $gidtext = $eLang->get('NONE'); break;
				case 1: $gidtext = $eLang->get('ADMINISTRATOR').' (1)'; break;
				case 5: $gidtext = $eLang->get('USER').' (5)'; break;
				case 6: $gidtext = $eLang->get('EXTERNALUSER').' (6)'; break;
				case 7: $gidtext = $eLang->get('GUEST').' (7)'; break;
				default: $gidtext = $group['groupname'].' ('.$row->gid.')'; break;
			}

			if ($row->uid > 0) {
				$uidtext = ($elxis->getConfig('REALNAME') == 1) ? $user->firstname.' '.$user->lastname : $user->uname;
				$uidtext .= ' ('.$row->uid.')';
			} else {
				$uidtext = $eLang->get('NOONE');
			}

			$response['id'] = $row->id;
			$response['category'] = $row->category;
			$response['element'] = $row->element;
			$response['elementtext'] = $eLang->silentGet($row->element, true);
			$response['action'] = $row->action;
			$response['actiontext'] = $eLang->silentGet($row->action, true);
			$response['minlevel'] = $row->minlevel;
			$response['minleveltext'] = $row->minlevel;
			$response['gid'] = $row->gid;
			$response['gidtext'] = $gidtext;
			$response['uid'] = $row->uid;
			$response['uidtext'] =  $uidtext;
			$response['aclvalue'] = $row->aclvalue;
			$response['editicon'] = $elxis->icon('edit', 16);
			$response['deleteicon'] = $elxis->icon('delete', 16);

			$this->ajaxHeaders('application/json');
			echo json_encode($response);
			exit();
		}
	}

}

?>