<?php 
/**
* @version		$Id: install.php 1307 2012-09-28 21:02:07Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class installExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/************************************************/
	/* PREPARE TO DISPLAY COMPONENT'S CONTROL PANEL */
	/************************************************/
	public function ipanel() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$sync = array('components' => array(), 'modules' => array(), 'plugins' => array(), 'templates' => array(), 'engines' => array(), 'auths' => array());
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$sync = $this->syncExtensions();
		}

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/extmanager.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/extmanager.js');

		eFactory::getPathway()->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$eDoc->setTitle($eLang->get('EXTENSIONS'));

		$this->view->ipanel($sync);
	}


	/************************************************/
	/* GET EXTENSIONS AVAILABLE FOR SYNCHRONIZATION */
	/************************************************/
	private function syncExtensions() {
		$eFiles = eFactory::getFiles();

		$sync = array('components' => array(), 'modules' => array(), 'plugins' => array(), 'templates' => array(), 'engines' => array(), 'auths' => array());

		$fcomps = array();
		$icomps = array();
		$cdirs = $eFiles->listFolders('components/');
		if ($cdirs) {
			foreach ($cdirs as $cdir) {
				$cname = preg_replace('#^(com_)#', '', $cdir);
				if (file_exists(ELXIS_PATH.'/components/'.$cdir.'/'.$cname.'.php')) { $fcomps[] = $cdir; }
			}
		}

		$options = array('sortname' => 'component', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 500);
		$dbcomps = $this->model->getComponents($options);
		if ($dbcomps) {
			foreach ($dbcomps as $dbcomp) { $icomps[] = $dbcomp->component; }
		}

		if ($fcomps) {
			foreach ($fcomps as $fcomp) {
				if (!in_array($fcomp, $icomps)) { $sync['components'][] = $fcomp; }
			}
		}
		unset($cdirs, $fcomps, $icomps, $dbcomps);

		$fmods = array();
		$imods = array('mod_content');
		$mdirs = $eFiles->listFolders('modules/');
		if ($mdirs) {
			foreach ($mdirs as $mdir) {
				if (file_exists(ELXIS_PATH.'/modules/'.$mdir.'/'.$mdir.'.php')) { $fmods[] = $mdir; }
			}
		}

		$options = array('sortname' => 'module', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 900);
		$dbmods = $this->model->getModules($options);
		if ($dbmods) {
			foreach ($dbmods as $dbmod) { $imods[] = $dbmod['module']; }
			$imods = array_unique($imods);
		}

		if ($fmods) {
			foreach ($fmods as $fmod) {
				if (!in_array($fmod, $imods)) { $sync['modules'][] = $fmod; }
			}
		}
		unset($mdirs, $fmods, $imods, $dbmods);

		$fplgs = array();
		$iplgs = array();
		$pdirs = $eFiles->listFolders('components/com_content/plugins/');
		if ($pdirs) {
			foreach ($pdirs as $pdir) {
				if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$pdir.'/'.$pdir.'.plugin.php')) { $fplgs[] = $pdir; }
			}
		}

		$options = array('sortname' => 'plugin', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 500);
		$dbplgs = $this->model->getPlugins($options);
		if ($dbplgs) {
			foreach ($dbplgs as $dbplg) { $iplgs[] = $dbplg->plugin; }
		}

		if ($fplgs) {
			foreach ($fplgs as $fplg) {
				if (!in_array($fplg, $iplgs)) { $sync['plugins'][] = $fplg; }
			}
		}
		unset($pdirs, $fplgs, $iplgs, $dbplgs);

		$ftpls = array();
		$itpls = array();
		$tdirs = $eFiles->listFolders('templates/');
		if ($tdirs) {
			foreach ($tdirs as $tdir) {
				if ($tdir == 'admin') { continue; }
				if (file_exists(ELXIS_PATH.'/templates/'.$tdir.'/index.php')) { $ftpls[] = $tdir; }
			}
		}

		$options = array('section' => 'frontend', 'sortname' => 'template', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 500);
		$dbtpls = $this->model->getTemplates($options);
		if ($dbtpls) {
			foreach ($dbtpls as $dbtpl) { $itpls[] = $dbtpl->template; }
		}

		if ($ftpls) {
			foreach ($ftpls as $ftpl) {
				if (!in_array($ftpl, $itpls)) { $sync['templates'][] = $ftpl; }
			}
		}
		unset($tdirs, $ftpls, $itpls, $dbtpls);

		$fengs = array();
		$iengs = array();
		$edirs = $eFiles->listFolders('components/com_search/engines/');
		if ($edirs) {
			foreach ($edirs as $edir) {
				if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$edir.'/'.$edir.'.engine.php')) { $fengs[] = $edir; }
			}
		}

		$options = array('sortname' => 'engine', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 500);
		$dbengs = $this->model->getEngines($options);
		if ($dbengs) {
			foreach ($dbengs as $dbeng) { $iengs[] = $dbeng->engine; }
		}

		if ($fengs) {
			foreach ($fengs as $feng) {
				if (!in_array($feng, $iengs)) { $sync['engines'][] = $feng; }
			}
		}
		unset($edirs, $fengs, $iengs, $dbengs);

		$faths = array();
		$iaths = array();
		$adirs = $eFiles->listFolders('components/com_user/auth/');
		if ($adirs) {
			foreach ($adirs as $adir) {
				if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$adir.'/'.$adir.'.auth.php')) { $faths[] = $adir; }
			}
		}

		$options = array('sortname' => 'auth', 'sortorder' => 'ASC', 'limitstart' => 0, 'rp' => 500);
		$dbaths = $this->model->getAuthMethods($options);
		if ($dbaths) {
			foreach ($dbaths as $dbath) { $iaths[] = $dbath->auth; }
		}

		if ($faths) {
			foreach ($faths as $fath) {
				if (!in_array($fath, $iaths)) { $sync['auths'][] = $fath; }
			}
		}
		unset($adirs, $faths, $iaths, $dbaths);

		return $sync;
	}


	/***************************************/
	/* PERFORM SYSTEM DOWNLOAD AND INSTALL */
	/***************************************/
	public function installextensionsys() {
		$this->installextension(true);
	}


	/***********************************/
	/* PERFORM USER UPLOAD AND INSTALL */
	/***********************************/
	public function installextension($system=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		if (!$system) {
			$js = 'function extmaniload() { if (top.installerResponse) { top.installerResponse(); } } window.onload=extmaniload;';
			eFactory::getDocument()->addScript($js);
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError($system, $eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			return;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError($system, $eLang->get('NOTALLOWACTION'));
			return;
		}

		$sess_token = trim($eSession->get('token_extmaninst'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$this->view->installError($system, $eLang->get('REQDROPPEDSEC'));
			return;
		}

		$packok = false;
		if (isset($_FILES) && isset($_FILES['package']) && ($_FILES['package']['name'] != '') && ($_FILES['package']['error'] == 0) && ($_FILES['package']['size'] > 0)) {
			$packok = true;
		}

		if (!$packok) {
			$this->view->installError($system, $eLang->get('NO_PACK_UPLOADED'));
			return;
		}

		$filext = strtolower($eFiles->getExtension($_FILES['package']['name']));
		if ($filext != 'zip') {
			$this->view->installError($system, $eLang->get('ELXIS_PACK_MUST_ZIP'));
			return;
		}

		$upfile = 'package_'.date('YmdHis').'_'.rand(100, 999).'.zip';

		$ok = $eFiles->upload($_FILES['package']['tmp_name'], 'tmp/'.$upfile, true);
		if (!$ok) {
			$this->view->installError($system, $eFiles->getError());
			return;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepare($upfile);
		if (!$ok) {
			$this->view->installError($system, $installer->getError());
			return;
		}

		if (($system == false) && ($installer->isUpdate() === true)) {
			$installer->deletePackage();
			$this->view->confirmUpdate($installer);
			return;
		}

		if (($system == false) && (count($installer->getWarnings()) > 0)) {
			$installer->deletePackage();
			$this->view->confirmInstall($installer);
			return;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError($system, $installer->getError());
			return;
		}

		$this->view->installSuccess($system, $installer);
	}


	/**************************************/
	/* CONTINUE INSTALL (BY PASS WARNINGS) */
	/**************************************/
	public function extcinstall() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError(true, $eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			return;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError(true, $eLang->get('NOTALLOWACTION'));
			return;
		}

		if (!isset($_POST['ufolder'])) {
			$this->view->installError(true, 'Invalid request! ufolder not set.');
			return;
		}

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$ufolder = filter_input(INPUT_POST, 'ufolder', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$ufolder = preg_replace($pat, '', $ufolder);
		$ufolder = trim(str_replace('..', '', $ufolder));
		if (($ufolder == '') || ($ufolder != $_POST['ufolder'])) {
			$this->view->installError(true, 'Temporary folder (ufolder) has an invalid name!');
			return;
		}

		$tmpdir = $eFiles->elxisPath('tmp/', true);
		if (!is_dir($tmpdir.$ufolder.'/')) {
			$this->view->installError(true, 'Temporary folder (ufolder) does not exist!');
			return;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepareFromFolder($ufolder);
		if (!$ok) {
			$this->view->installError(true, $installer->getError());
			return;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError(true, $installer->getError());
			return;
		}

		$this->view->installSuccess(true, $installer);
	}


	/*************************************/
	/* CONTINUE UPDATE (BYPASS WARNINGS) */
	/*************************************/
	public function extcupdate() {
		$this->extcinstall();
	}


	/**************************************************/ 
	/* DOWNLOAD AND INSTALL/UPLOAD EXTENSION FROM EDC */
	/**************************************************/ 
	public function edcinstall() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->view->installError(true, $eLang->get('THIS_IS_SUBSITE').' '.$eLang->get('INST_EXT_MOTHERSITE'));
			return;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError(true, $eLang->get('NOTALLOWACTION'));
			return;
		}

		$options = array();
		$options['task'] = filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['edcauth'] = trim(filter_input(INPUT_POST, 'edcauth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$options['pcode'] = trim(filter_input(INPUT_POST, 'pcode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if (($options['task'] != 'install') && ($options['task'] != 'update')) {
			$this->view->installError(true, 'Invalid request!');
			return;
		}
		if ($options['edcauth'] == '') {
			$this->view->installError(true, 'You are not authorized to access EDC!');
			return;
		}
		if ($options['pcode'] == '') {
			$this->view->installError(true, 'No Elxis package set!');
			return;
		}

		$str = $this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');

		$params = new elxisParameters($str, '', 'component');
		$edc = new elxisDC($params);
		$edc_result = $edc->downloadPackage($options);
		unset($edc, $params, $str);

		if (($edc_result['error'] == 1) || ($edc_result['pack'] == '')) {
			$errormsg = ($edc_result['errormsg'] != '') ? $edc_result['errormsg'] : 'Downloading extension from EDC failed!';
			$this->view->installError(true, $errormsg);
			return;
		}

		$upfile = $edc_result['pack'];
		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->prepare($upfile, true);
		if (!$ok) {
			$this->view->installError(true, $installer->getError());
			return;
		}

		$ok = $installer->install();
		if (!$ok) {
			$this->view->installError(true, $installer->getError());
			return;
		}

		$this->view->installSuccess(true, $installer);
	}


	/*************************/
	/* SYNCHRONIZE EXTENSION */
	/*************************/
	public function syncextension() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		if (!defined('ELXIS_MULTISITE') || (ELXIS_MULTISITE == 1)) {
			$this->view->installError(true, $eLang->get('SYNC_EXT_SUBSITES'));
			return;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
		if ($can_install > 0) {
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) { $can_install = 0; }
		}

		if ($can_install < 1) {
			$this->view->installError(true, $eLang->get('NOTALLOWACTION'));
			return;
		}

		$sess_token = trim($eSession->get('token_extmansync'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$this->view->installError(true, $eLang->get('REQDROPPEDSEC'));
			return;
		}

		$extension = trim(filter_input(INPUT_POST, 'extension', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($extension == '') {
			$this->view->installError(true, $eLang->get('NO_EXT_SELECTED'));
			return;
		}

		if (preg_match('#^(com\_)#', $extension)) {
			$type = 'component';
			$acltype = 'components';
			$cname = preg_replace('#^(com_)#', '', $extension);
			if (!file_exists(ELXIS_PATH.'/components/'.$extension.'/'.$cname.'.php')) {
				$this->view->installError(true, 'Component '.$extension.' does not exist!');
				return;
			}
		} else if (preg_match('#^(mod\_)#', $extension)) {
			$type = 'module';
			$acltype = 'modules';
			if (!file_exists(ELXIS_PATH.'/modules/'.$extension.'/'.$extension.'.php')) {
				$this->view->installError(true, 'Module '.$extension.' does not exist!');
				return;
			}
		} else {
			if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$extension.'/'.$extension.'.engine.php')) {
				$type = 'engine';
				$acltype = 'engines';
			} else if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$extension.'/'.$extension.'.auth.php')) {
				$type = 'auth';
				$acltype = 'auth';
			} else if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/'.$extension.'.plugin.php')) {
				$type = 'plugin';
				$acltype = 'plugins';
			} else {
				$type = 'template';
				$acltype = 'templates';
				if (!file_exists(ELXIS_PATH.'/templates/'.$extension.'/index.php')) {
					$this->view->installError(true, 'Template '.$extension.' does not exist!');
					return;
				}
				if (($extension == 'admin') || ($extension == 'system')) {
					$this->view->installError(true, 'Invalid template!');
					return;
				}
			}
		}

		if ($elxis->acl()->check('com_extmanager', $acltype, 'install') < 1) {
			$this->view->installError(true, $eLang->get('NOTALLOWACTION'));
			return;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->synchronize($type, $extension);
		if (!$ok) {
			$this->view->installError(true, $installer->getError());
			return;
		}

		$this->view->installSuccess(true, $installer);
	}

}

?>