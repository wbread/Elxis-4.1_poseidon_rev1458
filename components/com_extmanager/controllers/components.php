<?php 
/**
* @version		$Id: components.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class componentsExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY COMPONENTS */
	/*********************************/
	public function listcomps() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('COMPONENTS'), 'extmanager:components/');
		$eDoc->setTitle($eLang->get('COMPONENTS'));

		$this->view->listcomponents();
	}


	/***********************************************************/
	/* RETURN LIST OF COMPONENTS FOR GRID IN XML FORMAT (AJAX) */
	/***********************************************************/
	public function getcomponents() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$sortcols = array('name', 'component');
		$querycols = array('name', 'component');
		$options = array(
			'rp' => 20, 'page' => 1,
			'sortname' => 'name', 'sortorder' => 'asc',
			'qtype' => '', 'query' => '',
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'name';
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'name'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'name';
		if (($options['qtype'] == '') || !in_array($options['qtype'], $querycols)) { $options['qtype'] = 'name'; }
		$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
		$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
		if (eUTF::strlen($options['query']) < 3) { $options['query'] = ''; }			

		$total = $this->model->countComponents($options);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getComponents($options);
			if ($rows) {
				elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
				$exml = new extensionXML();
				foreach ($rows as $k => $row) {
					$cname = preg_replace('#^(com\_)#', '', $row->component);
					$info = $exml->quickXML('component', $cname);
					$rows[$k]->version = $info['version'];
					$rows[$k]->created = $info['created'];
					$rows[$k]->author = $info['author'];
					$rows[$k]->authorurl = $info['authorurl'];
					unset($info);
				}
				unset($exml);
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
			$canedit = ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) ? true : false;
			$edit_link = $elxis->makeAURL('extmanager:components/edit.html');
			foreach ($rows as $row) {
				$sn = $options['limitstart'] + $i + 1;
				$authortxt = '';
				if (trim($row->author) != '') {
					if (trim($row->authorurl) != '') {
						$authortxt = '<a href="'.$row->authorurl.'" target="_blank" style="text-decoration:none;">'.$row->author.'</a>';
					} else {
						$authortxt = $row->author;
					}
				}
				if ($authortxt == '') { $authortxt = '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>'; }
				$datetxt = '';
				if (trim($row->created) != '') {
					$datetxt = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_3'));
				}
				if ($datetxt == '') { $datetxt = '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>'; }
				$versiontxt = ($row->version == 0) ? '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>' : $row->version;

				echo '<row id="'.$row->id.'">'."\n";
				echo '<cell>'.$sn."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$edit_link.'?id='.$row->id.'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$row->name."</a>]]></cell>\n";					
				} else {
					echo '<cell><![CDATA['.$row->name."]]></cell>\n";
				}
				echo '<cell>'.$row->component."</cell>\n";
				echo '<cell><![CDATA['.$versiontxt."]]></cell>\n";
				echo '<cell><![CDATA['.$datetxt."]]></cell>\n";
       			echo '<cell><![CDATA['.$authortxt."]]></cell>\n";
       			echo '<cell><![CDATA['.$row->route."]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/******************/
	/* EDIT COMPONENT */
	/******************/
	public function editcomponent() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		if ($elxis->acl()->check('com_extmanager', 'components', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$row = new componentsDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, 'Component not found', true);
		}

		if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, $eLang->get('NOTALLOWMANITEM'), true);
		}

		$aclrows = array();
		$wheres = array('category' => 'component', 'element' => $row->component);
		$aclrows1 = $this->model->queryACL($wheres);
		$wheres = array('category' => $row->component);
		$aclrows2 = $this->model->queryACL($wheres);
		if ($aclrows1) {
			$aclrows = $aclrows1;
			if ($aclrows2) {
				foreach($aclrows2 as $aclrow2) {
					$aclrows[] = $aclrow2;
				}
			}
		} else if ($aclrows2) {
			$aclrows = $aclrows2;
		}
		unset($aclrows1, $aclrows2, $wheres);

		$users = array();
		$groups = $this->model->getGroups('level', 'DESC');
		$groups = $this->translateGroupNames($groups);
		$totalusers = $this->model->countUsers();
		if ($totalusers < 50) {
			$users = $this->model->getUsers();
		}

		$eLang->load($row->component, 'component');
		
		$cname = preg_replace('/^(com\_)/', '', $row->component);
		$path = ELXIS_PATH.'/components/'.$row->component.'/'.$cname.'.xml';
		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$exml->parse($path, true);
		$exml->checkDependencies();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('COMPONENTS'), 'extmanager:components/');
		
		$pgtitle = sprintf($eLang->get('EDIT_COMPONENT_X'), $row->name);
		$eDoc->setTitle($pgtitle);
		$pathway->addNode($eLang->get('EDIT').' '.$row->name);

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('extmanager:components/'));

        $eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager.css');
        $eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		$this->view->editComponent($row, $aclrows, $groups, $users, $exml);
	}


	/******************/
	/* SAVE COMPONENT */
	/******************/
	public function savecomponent() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0007', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? $_POST['task'] : 'save';
		if ($task != 'apply') { $task = 'save'; }
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		if ($elxis->acl()->check('com_extmanager', 'components', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new componentsDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, 'Component not found!', true);
		}

		if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
			$link = $elxis->makeAURL('extmanager:components/');
			$elxis->redirect($link, $eLang->get('NOTALLOWMANITEM'), true);
		}

		$route = strtolower(trim(filter_input(INPUT_POST, 'route', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
		$route_clean = preg_replace('/[^a-z0-9\_\-]/', '', $route);
		if ($route != $route_clean) {
			$link = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
			$elxis->redirect($link, 'Route is invalid!', true);
		}
		
		if ($route != '') {
			if (file_exists(ELXIS_PATH.'/'.$route.'/')) {
				$link = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
				$elxis->redirect($link, 'You can not route a component to an existing folder!', true);
			}
		}		

		$cname = preg_replace('/^(com\_)/', '', $row->component);
		$comxml = ELXIS_PATH.'/components/'.$row->component.'/'.$cname.'.xml';
		if (file_exists($comxml)) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $comxml, 'component');
			$row->params = $params->toString($_POST['params']);
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:components/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($route != trim($row->route)) {
			elxisLoader::loadFile('components/com_cpanel/models/cpanel.model.php');
			$cpmodel = new cpanelModel();
			$cpmodel->setComponentRoute($row->component, $route);
			unset($cpmodel);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('extmanager:components/edit.html?id='.$row->id) : $elxis->makeAURL('extmanager:components/');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/***********************/
	/* UNINSTALL COMPONENT */
	/***********************/
	public function deletecomponent() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_extmanager', 'components', 'install') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit();
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('UNINST_EXT_MOTHERSITE'));
			exit();
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|Component not found!';
			exit();
		}

		$row = new componentsDbTable();
		if (!$row->load($id)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$row->getErrorMsg();
			exit();
		}

		if ($elxis->acl()->check('component', $row->component, 'manage') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWMANITEM'));
			exit();
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('UNINST_NALLOW_SECLEVEL'));
			return $response;
		}

		if ($row->iscore == 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('CNOT_UNINST_CORE_EXTS'));
			exit();
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML('component', $row->component);
		$com_version = $info['version'];
		unset($exl);

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->uninstall('component', $row->component, $row->id, $com_version);
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

}
	
?>