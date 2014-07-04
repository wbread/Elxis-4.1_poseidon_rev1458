<?php 
/**
* @version		$Id: modules.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class modulesExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/******************************/
	/* PREPARE TO DISPLAY MODULES */
	/******************************/
	public function listmods() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$positions = $this->model->getPositions();

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('MODULES'), 'extmanager:modules/');
		$eDoc->setTitle($eLang->get('MODULES'));

		$this->view->listmodules($positions);
	}


	/********************************************************/
	/* RETURN LIST OF MODULES FOR GRID IN XML FORMAT (AJAX) */
	/********************************************************/
	public function getmodules() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$sortcols = array('id', 'title', 'published', 'position', 'ordering', 'module');
		$querycols = array('id', 'title', 'module');
		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'position', 'sortorder' => 'asc',
			'qtype' => 'title', 'query' => '',
			'section' => 'frontend', 'position' => '', 'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'title';
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'title'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'title';
		if (($options['qtype'] == '') || !in_array($options['qtype'], $querycols)) { $options['qtype'] = 'title'; }
		$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		if ($options['qtype'] == 'id') {
			$options['query'] = (int)$options['query'];
		} else {
			$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
			if (eUTF::strlen($options['query']) < 3) { $options['query'] = ''; }			
		}
		$options['section'] = (isset($_POST['section'])) ? $_POST['section'] : 'frontend';
		if ($options['section'] != 'backend') { $options['section'] = 'frontend'; }
		$options['position'] = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW || FILTER_FLAG_STRIP_HIGH);
		$options['position'] = eUTF::trim(preg_replace($pat, '', $options['position']));

		$total = $this->model->countModules($options);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		$modsacl = array();
		if ($total > 0) {
			$rows = $this->model->getModules($options);
			if ($rows) {
				$modids = array();
				foreach ($rows as $row) { $modids[] = $row['id']; }
				$modsacl = $this->model->getModulesViewACL($modids);
			}
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 0;
			$numrows = count($rows);
			$pubicon = $elxis->icon('tick', 16);
			$unpubicon = $elxis->icon('error', 16);
			$saveicon = $elxis->icon('save', 16);
			$upicon = '<img src="'.$elxis->icon('arrowup', 16).'" alt="up" border="0" />';
			$downicon = '<img src="'.$elxis->icon('arrowdown', 16).'" alt="down" border="0" />';
			$canedit = ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) ? true : false;
			$allgroups = $this->model->getGroups();
			$edit_link = $elxis->makeAURL('extmanager:modules/edit.html');

			elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
			$exml = new extensionXML();
			foreach ($rows as $row) {
				$info = $exml->quickXML('module', $row['module']);
				$versiontxt = ($info['version'] == 0) ? '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>' : $info['version'];
				unset($info);
					
				$picon = ($row['published'] == 1) ? $pubicon : $unpubicon;
				if (!isset($rows[$i-1])) {
					$condition1 = false;
				} else {
					$condition1 = ($row['position'] == $rows[$i-1]['position']) ? true : false;
				}
				
				if (!isset($rows[$i+1])) {
					$condition2 = false;
				} else {
					$condition2 = ($row['position'] == $rows[$i+1]['position']) ? true : false;
				}

				$ordertxt = '';
				if ($canedit) {
					if ((($i > 0) || (($i + $options['limitstart']) > 0)) && $condition1) {
						$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_UP').'" onclick="movemodule('.$row['id'].', 1)">'.$upicon."</a>";
					}
        			if ((($i < $numrows - 1) || (($i + $options['limitstart']) < $total - 1)) && $condition2) {
        				$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_DOWN').'" onclick="movemodule('.$row['id'].', 0)">'.$downicon."</a>";
       				}
       			}

				$id = $row['id'];
				$acctxt = $eLang->get('NOONE');
				if (isset($modsacl[$id])) {
					if ($modsacl[$id]['aclvalue'] == 1) {
						if ($modsacl[$id]['uid'] > 0) {
							$acctxt = '<span style="color:#ff0000; font-style:italic;">'.$eLang->get('USER').' '.$modsacl[$id]['uid'].'</span>';
						} else if ($modsacl[$id]['gid'] > 0) {
							$grpname = $this->getGroupName($modsacl[$id]['gid'], $allgroups);
							if ($grpname == '') { $grpname = $eLang->get('GROUP').' '.$modsacl[$id]['gid']; }
							$acctxt = '<span style="color:#ff0000; font-style:italic;">'.$grpname.'</span>';
						} else {
							$lvl = $modsacl[$id]['minlevel'] * 1000;
							$acctxt = $elxis->alevelToGroup($lvl, $allgroups);
						}

						if ($modsacl[$id]['num'] > 1) { $acctxt .= ' +'; }
					}
				}

       			$sectiontxt = ($options['section'] == 'frontend') ? $eLang->get('FRONTEND') : $eLang->get('BACKEND');

				echo '<row id="'.$row['id'].'">'."\n";
				echo '<cell>'.$row['id']."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$edit_link.'?id='.$row['id'].'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$row['title']."</a>]]></cell>\n";
				} else {
					echo '<cell>'.$row['title']."</cell>\n";
				}
				echo '<cell>'.$row['module']."</cell>\n";
				echo '<cell><![CDATA['.$versiontxt."]]></cell>\n";
				echo '<cell><![CDATA[<img src="'.$picon.'" alt="icon" border="0" />]]></cell>'."\n";
				echo '<cell>'.$row['position']."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<input type="text" name="orderbox'.$row['id'].'" id="orderbox'.$row['id'].'" size="4" maxlength="7" value="'.$row['ordering'].'" dir="ltr" onchange="markOrderUnsaved('.$row['id'].');" /> ';
					echo '<a href="javascript:void(null);" onclick="setModuleOrder('.$row['id'].');" title="'.$eLang->get('SAVE_ORDERING').'"><img src="'.$saveicon.'" alt="save" border="0" align="bottom" /></a>]]></cell>'."\n";
				} else {
					echo '<cell>'.$row['ordering']."</cell>\n";
				}
       			echo '<cell><![CDATA['.$ordertxt."]]></cell>\n";
				echo '<cell><![CDATA['.$acctxt."]]></cell>\n";
				echo '<cell><![CDATA['.$sectiontxt."]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/**********************************/
	/* TOGGLE MODULE'S PUBLISH STATUS */
	/**********************************/
	public function publishmodule() {
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$response = $this->model->publishModule($id, -1); //includes acl checks
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.addslashes($response['message']);
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/*****************/
	/* COPY A MODULE */
	/*****************/
	public function copymodule() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit();
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|Module not found!';
			exit();
		}

		$row = new modulesDbTable();
		if (!$row->load($id)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$row->getErrorMsg();
			exit();
		}

		if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWMANITEM'));
			exit();
		}

		$row->title = $row->title.' ('.$eLang->get('COPY').')';
		$row->published = 0;
		$row->ordering += 1;

		$row->forceNew(true);

		if (!$row->insert()) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$row->getErrorMsg();
			exit();
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
			
		);
		$row->reorder($wheres, true);

		$this->model->copyModuleACL($row->module, $id, $row->id);
		$this->model->copyModuleTranslations($id, $row->id);

		if ($row->section == 'frontend') {
			$db = eFactory::getDB();
			$modid = $row->id;
			$menuid = 0;
			$sql = "INSERT INTO ".$db->quoteId('#__modules_menu')." (".$db->quoteId('mmid').", ".$db->quoteId('moduleid').", ".$db->quoteId('menuid').")"
			."\n VALUES (NULL, :xmod, :xmen)";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xmod', $modid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}

		$this->ajaxHeaders('text/plain');
		echo '1|Success!';
		exit();
	}


	/*******************/
	/* DELETE A MODULE */
	/*******************/
	public function deletemodule() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit();
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|Module not found!';
			exit();
		}

		$row = new modulesDbTable();
		if (!$row->load($id)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$row->getErrorMsg();
			exit();
		}

		if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWMANITEM'));
			exit();
		}

		$instances = $this->model->countModuleInstances($row->module);
		if ($instances < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|Module not found!';
			exit();
		}

		$uninstall = false;
		if ($instances == 1) { $uninstall = true; }
		if ($row->module == 'mod_content') { $uninstall = false; }
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $uninstall = false; }
		if (($uninstall == true) && ($elxis->getConfig('SECURITY_LEVEL') > 0)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('UNINST_NALLOW_SECLEVEL'));
			return $response;
		}
		if (($row->iscore == 1) && ($uninstall == true)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('CNOT_UNINST_CORE_EXTS'));
			exit();
		}

		if (!$uninstall) {
			$ok = $this->model->deleteModule($row->id, $row->module);
			$this->ajaxHeaders('text/plain');
			if (!$ok) {
				echo '0|'.addslashes($eLang->get('ACTION_FAILED'));
			} else {
				echo '1|Success!';
			}
			exit();
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML('module', $row->module);
		$mod_version = $info['version'];
		unset($exl);

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->uninstall('module', $row->module, $row->id, $mod_version);
		if (!$ok) {
			$msg = $installer->getError();
			if ($msg == '') { $msg = $eLang->get('ACTION_FAILED'); }
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($msg);
			exit();
		}

		$this->ajaxHeaders('text/plain');
		echo '1|Success';
		exit();
	}


	/****************************/
	/* MOVE A MODULE UP OR DOWN */
	/****************************/
	public function movemodule() {
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');
		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
			echo '0|'.eFactory::getLang()->get('NOTALLOWACTION');
			exit();
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			echo '0|Invalid request!';
			exit();
		}
		$moveup = (isset($_POST['moveup'])) ? (int)$_POST['moveup'] : 0;
		$inc = ($moveup == 1) ? -1 : 1;

		$row = new modulesDbTable();
		if (!$row->load($id)) {
			echo '0|Module not found!';
			exit();
		}

		if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
			echo '0|'.addslashes($eLang->get('NOTALLOWMANITEM'));
			exit();
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
		);
		$ok = $row->move($inc, $wheres);
		if (!$ok) {
			echo '0|'.addslashes($row->getErrorMsg());
		} else {
			echo '1|Success!';
		}
		exit();
	}


	/*************************/
	/* SET MODULE'S ORDERING */
	/*************************/
	public function setmoduleorder() {
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$ordering = (isset($_POST['ordering'])) ? (int)$_POST['ordering'] : 0;
		if (($id < 1) || ($ordering < 1)) {
			echo '0|Invalid request!';
			exit();
		}

		$row = new modulesDbTable();
		if (!$row->load($id)) {
			echo '0|Module not found!';
			exit();
		}

		if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
			echo '0|'.addslashes($eLang->get('NOTALLOWMANITEM'));
			exit();
		}

		if ($row->ordering == $ordering) {
			echo '1|Success!';
			exit();
		}
		$row->ordering = $ordering;
		if (!$row->update()) {
			echo '0|'.addslashes($row->getErrorMsg());
			exit();
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
		);
		$row->reorder($wheres, true);
		echo '1|Success!';
		exit();
	}


	/**************/
	/* ADD MODULE */
	/**************/
	public function addmodule() {
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new modulesDbTable();
		$row->position = 'left';
		$row->published = 1;
		$row->ordering = 0;
		$row->module = 'mod_content';
		$row->iscore = 1;
		$row->section = 'frontend';

		$this->editmodule($row);
	}


	/*******************/
	/* ADD/EDIT MODULE */
	/*******************/
	public function editmodule($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		$aclrows = array();
		$groups = array();
		$users = array();
		if (!$row) {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('extmanager:/');
				$elxis->redirect($link, $msg, true);
			}
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			$row = new modulesDbTable();
			if (!$row->load($id)) {
				$link = $elxis->makeAURL('extmanager:modules/');
				$elxis->redirect($link, 'Module not found', true);
			}

			if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
				$link = $elxis->makeAURL('extmanager:modules/');
				$elxis->redirect($link, $eLang->get('NOTALLOWMANITEM'), true);
			}

			$is_new = false;
			$wheres = array('category' => 'module', 'element' => $row->module, 'identity' => $row->id);
			$aclrows = $this->model->queryACL($wheres);
			unset($wheres);

			$groups = $this->model->getGroups('level', 'DESC');
			$groups = $this->translateGroupNames($groups);
			$totalusers = $this->model->countUsers();
			if ($totalusers < 50) {
				$users = $this->model->getUsers();
			}
		}

		$positions = $this->model->getPositions();
		$posmods = $this->model->getModsByPosition($row->position);
		if ($is_new) {
			$modmenuitems = array(0);
			if ($row->section == 'frontend') {
				$allmenuitems = $this->model->getMenuItems($row->section);
			} else {
				$allmenuitems = array();
			}
		} else {
			if ($row->section == 'frontend') {
				$allmenuitems = $this->model->getMenuItems($row->section);
				$modmenuitems = $this->model->getModMenuItems($row->id);
			} else {
				$modmenuitems = array(0);
				$allmenuitems = array();
			}
		}

		$eLang->load($row->module, 'module');
		$path = ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.xml';
		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$exml->parse($path, true);
		$exml->checkDependencies();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('MODULES'), 'extmanager:modules/');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('ADD_NEW_MODULE'));
			$pathway->addNode($eLang->get('ADD_NEW_MODULE'));
		} else {
			if ($row->module == 'mod_content') {
				$eDoc->setTitle($eLang->get('EDIT_TEXT_MODULE'));
			} else {
				$pgtitle = sprintf($eLang->get('EDIT_MODULE_X'), $row->module);
				$eDoc->setTitle($pgtitle);
			}
			$pathway->addNode($eLang->get('EDIT').' '.$row->id);
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('extmanager:modules/'));

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		$this->view->editModule($row, $positions, $posmods, $aclrows, $groups, $users, $allmenuitems, $modmenuitems, $exml);
	}


	/*****************************************/
	/* GET A POSITION'S MODULES FOR ORDERING */
	/*****************************************/
	public function positionorder() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$json = array();
		$json['error'] = 0;
		$json['errormsg'] = '';
		$json['modules'] = array();

		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
			$json['error'] = 1;
			$json['errormsg'] = addslashes($eLang->get('NOTALLOWACTION'));
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit();
		}

		$position = filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$position = trim(preg_replace('/[^A-Za-z\_\-0-9]/', '', $position));

		if ($position == '') {
			$json['error'] = 1;
			$json['errormsg'] = 'You must select a position!';
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit();
		}

		$posmods = $this->model->getModsByPosition($position);

		$json['modules'][] = array('0' => '- '.$eLang->get('FIRST'));
		if ($posmods) {
			$q = 1;
			foreach ($posmods as $posmod) {
				$ttl = addslashes($posmod->title);
				$json['modules'][] = array($q => $q.' - '.$ttl);
				$q++;
			}
		}

		$q = ($q > 1) ? $q : 999;
		$json['modules'][] = array($q => '- '.$eLang->get('LAST'));

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}


	/***************/
	/* SAVE MODULE */
	/***************/
	public function savemodule() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0006', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? $_POST['task'] : 'save';
		if ($task != 'apply') { $task = 'save'; }
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		$redirurl = $elxis->makeAURL('extmanager:/');
		if ($id > 0) {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_extmanager', 'modules', 'install') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/modules.db.php');
		$row = new modulesDbTable();
		$old_ordering = -1;
		$iscore = 1;
		$module = 'mod_content';
		$section = 'frontend';
		if ($id > 0) {
			if (!$row->load($id)) { $elxis->redirect($redirurl, 'Module not found!', true); }
			if ($elxis->acl()->check('module', $row->module, 'manage', $id) < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWMANITEM'), true);
			}
			$old_ordering = $row->ordering;
			$iscore = $row->iscore;
			$module = $row->module;
			$section = $row->section;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$row->iscore = (int)$iscore;
		$row->module = $module;
		$row->section = $section;

		if ($row->module == 'mod_content') {
			if (isset($_POST['transl_content']) && isset($_POST['transorig_content'])) {
				if ($_POST['transl_content'] != $elxis->getConfig('LANG')) {
					$row->content = $_POST['transorig_content'];
				}
			}
		}

		$addacl = false;
		if ($id > 0) {
			$redirurledit = $elxis->makeAURL('extmanager:modules/edit.html?id='.$id);
		} else {
			$addacl = true;
			$redirurledit = $elxis->makeAURL('extmanager:modules/add.html');
		}

		$modxml = ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.xml';
		if (file_exists($modxml)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $modxml, 'module');
			$row->params = $params->toString($_POST['params']);
			unset($params);
		} else {
			$row->params = null;
		}

		$ok = ($id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}

		$reorder = ($id == 0) ? true : ($old_ordering <> $row->ordering) ? true : false;
		if ($reorder) {
			$wheres = array(array('position', '=', $row->position));
			$row->reorder($wheres, true);
		}

		if ($addacl == true) {
			$arow = new aclDbTable();
			$arow->category = 'module';
			$arow->element = $row->module;
			$arow->identity = (int)$row->id;
			$arow->action = 'view';
			$arow->minlevel = 0;
			$arow->gid = 0;
			$arow->uid = 0;
			$arow->aclvalue = 1;
			$arow->insert();
			unset($arow);
			$arow = new aclDbTable();
			$arow->category = 'module';
			$arow->element = $row->module;
			$arow->identity = (int)$row->id;
			$arow->action = 'manage';
			$arow->minlevel = 70;
			$arow->gid = 0;
			$arow->uid = 0;
			$arow->aclvalue = 1;
			$arow->insert();
			unset($arow);
		}

		$pages = isset($_POST['pages']) ? $_POST['pages'] : array();
		if (!is_array($pages)) { $pages = array(); }
		$to_add = array();
		$to_delete = array();
		if ($row->section == 'frontend') {
			if ($id > 0) {
				$modmenuitems = $this->model->getModMenuItems($id);
				if (!is_array($modmenuitems)) { $modmenuitems = array(); }
			} else {
				$modmenuitems = array();
			}
		} else {
			$modmenuitems = array();
		}

		if ($pages) {
			foreach ($pages as $page) {
				$page = (int)$page;
				if ($page < 0) { continue; }
				if (!in_array($page, $modmenuitems)) {
					if ($page == 0) {
						$to_add = array(0);
					} else {
						$to_add[] = $page;
					}
				}
				if ($page == 0) { $pages = array(0); break; }
			}
		}

		if ($modmenuitems) {
			foreach ($modmenuitems as $mmitem) {
				if (!in_array($mmitem, $pages)) { $to_delete[] = $mmitem; }
			}
		}

		if (count($to_delete) > 0) {
			$this->model->deleteModMenus($row->id, $to_delete);
		}
		if (count($to_add) > 0) {
			$this->model->insertModMenus($row->id, $to_add);
		}

		if ($id > 0) {
			eFactory::getCache()->clearItems('modules', '^'.$row->module.'_'.$id);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('extmanager:modules/edit.html?id='.$row->id) : $elxis->makeAURL('extmanager:modules/');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}
	
?>