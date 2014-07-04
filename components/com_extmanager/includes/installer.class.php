<?php 
/**
* @version		$Id: installer.class.php 1454 2013-07-12 15:30:27Z datahell $
* @package		Elxis
* @subpackage	Extensions manager / Elxis installer
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisInstaller {

	private $efiles = null;
	private $tmpdir = ''; //absolute path to repository/tmp/
	private $package = ''; //package file name as uploaded in repository/tmp/
	private $ufolder = ''; //initial folder name created after unzip and located in repository/tmp/
	private $uffolder = ''; //path relative to repository/tmp/ where extension files have been unzipped (it might be the same as ufolder or a sub-folder of it).
	private $ifolder = ''; //final installation folder relative to elxis root (i.e. modules/mod_test/)
	private $head = null;
	private $dependencies = array();
	private $is_update = false;
	private $is_synchro = false;
	private $cur_version = 0;
	private $cur_created = '';
	private $cur_author = '';
	private $cur_authorurl = '';
	private $warnings = array();
	private $notify_install = 1;
	private $notify_uninstall = 1;
	private $notify_update = 1;
	private $log_install = 1;
	private $log_uninstall = 1;
	private $log_update = 1;
	private $errormsg = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$this->efiles = eFactory::getFiles();
		$this->tmpdir = $this->efiles->elxisPath('tmp/', true);
		$this->loadParams();
	}


	/*************************/
	/* LOAD COMPONENT PARAMS */
	/*************************/
	private function loadParams() {
		$db = eFactory::getDB();

		$component = 'com_extmanager';
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = :xcomp";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcomp', $component, PDO::PARAM_STR);
		$stmt->execute();
		$params_str = $stmt->fetchResult();
		if ($params_str) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
			$this->notify_install = (int)$params->get('notify_install', 1);
			$this->notify_uninstall = (int)$params->get('notify_uninstall', 1);
			$this->notify_update = (int)$params->get('notify_update', 1);
			$this->log_install = (int)$params->get('log_install', 1);
			$this->log_uninstall = (int)$params->get('log_uninstall', 1);
			$this->log_update = (int)$params->get('log_update', 1);
			unset($params);
		}
	}


	/************************************************************/
	/* SET PACKAGE FILENAME (UPLOADED IN REPOSITORY TMP FOLDER) */
	/************************************************************/
	public function setPackage($package) {
		$this->package = trim($package);
	}


	/******************************/
	/* CLEAN FILES/FOLDERS IN TMP */
	/******************************/
	private function cleanUP() {
		if (($this->package != '') && is_file($this->tmpdir.$this->package)) {
			$this->efiles->deleteFile('tmp/'.$this->package, true);
		}
		if (($this->ufolder != '') && is_dir($this->tmpdir.$this->ufolder.'/')) {
			$this->efiles->deleteFolder('tmp/'.$this->ufolder.'/', true);
		}
	}


	/***********************/
	/* DELETE PACKAGE FILE */
	/***********************/
	public function deletePackage() {
		if (($this->package != '') && is_file($this->tmpdir.$this->package)) {
			$this->efiles->deleteFile('tmp/'.$this->package, true);
		}
	}


	/******************************************************/
	/* UNZIP AND CHECK PACKAGE, PREPARE TO INSTALL/UPDATE */
	/******************************************************/
	public function prepare($package='', $system=false) {
		$eLang = eFactory::getLang();

		$this->errormsg = '';
		if ($package != '') { $this->setPackage($package); }
		if ($this->package == '') {
			$this->errormsg = $eLang->exist('NO_PACK_SET') ? $eLang->get('NO_PACK_SET') : 'No package set!';
			return false;
		}

		if (!file_exists($this->tmpdir.$this->package)) {
			$this->errormsg = $eLang->exist('PACK_NOTFOUND_REPO') ? $eLang->get('PACK_NOTFOUND_REPO') : 'Package file not found in Elxis repository (folder tmp/)!';
			return false;
		}

		$folder = $this->efiles->getFilename($this->package);
		if (file_exists($this->tmpdir.$folder.'/') && is_dir($this->tmpdir.$folder.'/')) {
			$folder = 'dir'.microtime();
		}

		$ok = $this->efiles->createFolder('tmp/'.$folder.'/', 0755, true);
		if (!$ok) {
			$this->errormsg = $eLang->exist('CNOT_CREATE_TMPFO_REPO') ? $eLang->get('CNOT_CREATE_TMPFO_REPO') : 'Could not create temporary folder in Elxis repository (folder tmp/)!';
			$this->cleanUP();
			return false;
		}

		$this->ufolder = $folder.'/';

		$zip = eFactory::getElxis()->obj('zip');
		$ok = $zip->unzip($this->tmpdir.$this->package, $this->tmpdir.$this->ufolder);
		if (!$ok) {
			$this->errormsg = $zip->getError();
			$this->cleanUP();
			return false;
		}
		unset($zip);

		return $this->continueFromFolder($system);
	}


	/***************************************/
	/* PREPARE INSTALL/UPDATE FROM UFOLDER */
	/***************************************/
	public function prepareFromFolder($folder='', $system=false) {
		$folder = trim(trim($folder, '/'));
		if ($folder == '') {
			$this->errormsg = 'Temporary folder (ufolder) can not be empty!';
			$this->cleanUP();
			return false;
		}

		if (!file_exists($this->tmpdir.$folder.'/') || !is_dir($this->tmpdir.$folder.'/')) {
			$this->errormsg = 'Temporary folder (ufolder) was not found!';
			$this->cleanUP();
			return false;
		}

		$this->ufolder = $folder.'/';
		if ($this->package == '') {
			$this->package = 'it_does_not_matter.zip';
		}

		return $this->continueFromFolder($system);
	}


	/****************************************/
	/* CONTINUE INSTALL/UPDATE FROM UFOLDER */
	/****************************************/
	private function continueFromFolder($system=false) {
		$eLang = eFactory::getLang();

		$this->uffolder = $this->ufolder;
		$files = $this->efiles->listFiles('tmp/'.$this->ufolder, '.', false, false, true);
		if (!$files) {
			$folders = $this->efiles->listFolders('tmp/'.$this->ufolder, false, false, true);
			if (!$folders || !is_array($folders) || (count($folders) <> 1)) {
				$this->errormsg = $eLang->exist('INV_ELXIS_PACK') ? $eLang->get('INV_ELXIS_PACK') : 'Invalid Elxis package!';
				$this->cleanUP();
				return false;
			}
			$folder = $this->ufolder.$folders[0].'/';
			$files = $this->efiles->listFiles('tmp/'.$folder, '.', false, false, true);
			if (!$files) {
				$this->errormsg = $eLang->exist('INV_ELXIS_PACK') ? $eLang->get('INV_ELXIS_PACK') : 'Invalid Elxis package!';
				$this->cleanUP();
				return false;
			}
			$this->uffolder = $this->ufolder.$folders[0].'/';
			unset($folders, $folder);
		}

		$xmlfiles = array();
		foreach ($files as $file) {
			if (preg_match('/(\.xml)$/', $file)) { $xmlfiles[] = $file; }
		}

		if (count($xmlfiles) == 0) {
			$this->errormsg = $eLang->exist('XML_NFOUND_INV_PACK') ? $eLang->get('XML_NFOUND_INV_PACK') : 'Installation XML file was not found. Invalid Elxis package!';
			$this->cleanUP();
			return false;
		}

		$xmlfile = '';
		if (count($xmlfiles) == 1) {
			$xmlfile = $xmlfiles[0];
		} else { //more than 1 XML files
			foreach ($xmlfiles as $xfile) {
				foreach ($files as $file) {
					if (preg_match('/(\.php)$/', $file)) {
						$fname = $this->efiles->getFilename($file);
						if ($fname.'.xml' == $xfile) {
							$xmlfile = $xfile;
							break(2);
						}
					}
				}
			}

			if ($xmlfile == '') {
				foreach ($xmlfiles as $xfile) {
					if ($xfile == 'templateDetails.xml') {//compatibility for Elxis 2009.x templates
						$xmlfile = $xfile;
						break;
					}
				}
			}

			if ($xmlfile == '') { //nothing found, get the first one without dots in filename
				foreach ($xmlfiles as $xfile) {
					$fname = $this->efiles->getFilename($xfile);
					if (strpos($fname, '.') === false) {
						$xmlfile = $xfile;
						break;
					}
				}
			}

			//nothing found, get the first one...
			if ($xmlfile == '') { $xmlfile = $xmlfiles[0]; }
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$ok = $exml->parse($this->tmpdir.$this->uffolder.$xmlfile, true);
		if (!$ok) {
			$msg = $exml->getErrorMsg();
			if ($msg == '') { $msg = 'Could not parse extension XML file!'; }
			$this->errormsg = $msg;
			$this->cleanUP();
			return false;
		}
		$exml->checkDependencies();
		$this->head = $exml->getHead();
		$this->dependencies = $exml->getDependencies();
		unset($exml);

		$aclcheck = ($system == true) ? false : true;

		return $this->check($aclcheck);
	}


	/********************/
	/* MAKE SOME CHECKS */
	/********************/
	private function check($aclcheck=true) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if (($this->head->type == '') || !in_array($this->head->type, array('component', 'module', 'template', 'engine', 'auth', 'plugin'))) {
			$this->errormsg = $eLang->exist('EXT_TYPE_XML_INVALID') ? $eLang->get('EXT_TYPE_XML_INVALID') : 'The Elxis extension type in XML file is not set ot it is invalid!';
			$this->cleanUP();
			return false;
		}

		switch ($this->head->type) {
			case 'component':
				$acl_element = 'components';
				$this->ifolder = 'components/'.$this->head->name.'/';
				if (!preg_match('/^(com\_)/', $this->head->name)) {
					$this->errormsg = 'A valid Elxis component name should start with com_!';
					$this->cleanUP();
					return false;
				}
			break;
			case 'module':
				$acl_element = 'modules';
				$this->ifolder = 'modules/'.$this->head->name.'/';
				if (!preg_match('/^(mod\_)/', $this->head->name)) {
					$this->errormsg = 'A valid Elxis module name should start with mod_!';
					$this->cleanUP();
					return false;
				}
			break;
			case 'template':
				$acl_element = 'templates';
				if ($this->head->section == 'backend') {
					$this->ifolder = 'templates/admin/'.$this->head->name.'/';
				} else {
					$this->ifolder = 'templates/'.$this->head->name.'/';
				}
			break;
			case 'engine':
				$acl_element = 'engines';
				$this->ifolder = 'components/com_search/engines/'.$this->head->name.'/';
			break;
			case 'auth':
				$acl_element = 'auth';
				$this->ifolder = 'components/com_user/auth/'.$this->head->name.'/';
			break;
			case 'plugin':
				$acl_element = 'plugins';
				$this->ifolder = 'components/com_content/plugins/'.$this->head->name.'/';
			break;
			default: break;
		}

		if ($aclcheck) {
			if ($elxis->acl()->check('component', 'com_extmanager', 'manage') < 1) {
				$this->errormsg = 'You are not allowed to manage extensions!';
				$this->cleanUP();
				return false;
			}

			if ($elxis->acl()->check('com_extmanager', $acl_element, 'install') < 1) {
				if ($eLang->exist('NALLOW_INSTALL_EXT')) {
					$this->errormsg = sprintf($eLang->get('NALLOW_INSTALL_EXT'), $acl_element);
				} else {
					$this->errormsg = 'You are not allowed to install '.$acl_element;
				}
				$this->cleanUP();
				return false;
			}
			if (($elxis->getConfig('SECURITY_LEVEL') > 0) && ($elxis->user()->gid <> 1)) {
				$this->errormsg = $eLang->exist('SECLEV_OADMIN_INSTALL') ? $eLang->get('SECLEV_OADMIN_INSTALL') : 'Under the current security level only administrators can install extensions!';
				$this->cleanUP();
				return false;
			}
		}

		$ipath = ELXIS_PATH.'/'.$this->ifolder;
		if (file_exists($ipath) && is_dir($ipath)) {
			$this->is_update = true;

			$exttype = $this->head->type;
			if (($this->head->type == 'template') && ($this->head->section == 'backend')) { $exttype= 'atemplate'; }

			elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
			$exml = new extensionXML();
			$info = $exml->quickXML($exttype, $this->head->name);
			$this->cur_version = $info['version'];
			$this->cur_created = $info['created'];
			$this->cur_author = $info['author'];
			$this->cur_authorurl = $info['authorurl'];
			unset($exml, $info);

			if ($this->cur_version == 0) {
				$this->warnings[] = 'An unknown version of '.$dpc->type.' '.$dpc->extension.' is already installed!';
			}

			if ($this->cur_version == $this->head->version) {
				if ($eLang->exist('EXT_ALREADY_INSTALL')) {
					$this->errormsg = sprintf($eLang->get('EXT_ALREADY_INSTALL'), $this->head->title.' v'.$this->head->version);
				} else {
					$this->errormsg = 'Extension '.$this->head->title.' v'.$this->head->version.' is already installed!';
				}
				$this->cleanUP();
				return false;
			}

			if ($this->cur_version > $this->head->version) {
				if ($eLang->exist('NEWER_ALREADY_INSTALL')) {
					$this->errormsg = sprintf($eLang->get('NEWER_ALREADY_INSTALL'), $this->cur_version, $this->head->title);
				} else {
					$this->errormsg = 'A newer version ('.$this->cur_version.') of '.$this->head->title.' is already installed!';
				}
				$this->cleanUP();
				return false;
			}
		}

		if ($this->dependencies) {
			foreach ($this->dependencies as $dpc) {
				if (!$dpc->installed) {
					if ($eLang->exist('EXT_REQ_EXT_NOTINSTALL')) {
						$arg2 = $dpc->type.' '.$dpc->extension.' ('.implode(', ',$dpc->versions).')';
						$this->warnings[] = sprintf($eLang->get('EXT_REQ_EXT_NOTINSTALL'), $this->head->title, $arg2);
					} else {
						$this->warnings[] = $this->head->title.' requires '.$dpc->type.' '.$dpc->extension.' ('.implode(', ',$dpc->versions).') which is not installed!';
					}
				} elseif (!$dpc->icompatible) {
					if ($eLang->exist('EXT_REQ_EXT_NOTCOMPAT')) {
						$arg2 = $dpc->type.' '.$dpc->extension.' ('.implode(', ',$dpc->versions).')';
						$this->warnings[] = sprintf($eLang->get('EXT_REQ_EXT_NOTCOMPAT'), $this->head->title, $arg2, $dpc->iversion);
					} else {
						$this->warnings[] = $this->head->title.' requires '.$dpc->type.' '.$dpc->extension.' ('.implode(', ',$dpc->versions).') but the installed version ('.$dpc->iversion.') is not compatible!';
					}
				}
			}
		}

		return true;
	}


	/****************************/
	/* INSTALL/UPDATE EXTENSION */
	/****************************/
	public function install() {
		//just in case
		if ($this->errormsg != '') { return false; }
		if ($this->package == '') { return false; }
		if ($this->ufolder == '') { return false; }
		if ($this->uffolder == '') { return false; }
		if ($this->ifolder == '') { return false; }
		if ($this->head->type == '') { return false; }
		if ($this->head->name == '') { return false; }

		if (!$this->efiles->createFolder($this->ifolder)) {
			$this->errormsg = $this->efiles->getError();
			$this->cleanUP();
			return false;
		}

		$files = $this->efiles->listFiles('tmp/'.$this->uffolder, '.', false, false, true);
		if ($files) {
			foreach ($files as $file) {
				if ($this->is_update) {
					if (file_exists(ELXIS_PATH.'/'.$this->ifolder.$file)) {
						if (strpos($file, 'config') !== false) {
							continue; //dont overwrite "config" files on update!
						}
					}
				}

				if (!$this->efiles->move('tmp/'.$this->uffolder.$file, $this->ifolder.$file, true, false)) {
					$this->errormsg = $this->efiles->getError();
					$this->cleanUP();
					$this->efiles->deleteFolder($this->ifolder);
					return false;
				}
			}
		}
		unset($files);

		$folders = $this->efiles->listFolders('tmp/'.$this->uffolder, false, false, true);
		if ($folders) {
			foreach ($folders as $folder) {
				if (!$this->efiles->moveFolder('tmp/'.$this->uffolder.$folder.'/', $this->ifolder.$folder.'/', true, false)) {
					$this->errormsg = $this->efiles->getError();
					$this->cleanUP();
					$this->efiles->deleteFolder($this->ifolder);
					return false;
				}
			}
		}
		unset($folders);

		$this->cleanUP();

		$func = ($this->is_update) ? 'update_'.$this->head->type : 'install_'.$this->head->type;
		if (method_exists($this, $func)) {
			$ok = $this->$func();
			if (!$ok) { return false; }
		}

		//custom install actions
		if (file_exists(ELXIS_PATH.'/'.$this->ifolder.'install.php')) {
			elxisLoader::loadFile($this->ifolder.'install.php');
			$class = $this->head->name.'_installer';
			if (class_exists($class, false)) {
				$obj = new $class();
				$func = ($this->is_update) ? 'update' : 'install';
				if (method_exists($obj, $func)) {
					$obj->$func();
				}
				unset($obj);
			}
		}

		if ($this->is_update) {
			if ($this->log_update == 1) {
				$this->logger($this->head->name, 'update', $this->head->version);
			}
			if ($this->notify_update == 1) {
				$this->notify('update', $this->head->type, $this->head->name, $this->head->version);
			}
		} else {
			if ($this->log_install == 1) {
				$this->logger($this->head->name, 'install', $this->head->version);
			}
			if ($this->notify_install == 1) {
				$this->notify('install', $this->head->type, $this->head->name, $this->head->version);
			}
		}

		return true;
	}


	/********************/
	/* UPDATE EXTENSION */
	/********************/
	public function update() {
		$ok = $this->install();
		return $ok;
	}


	/***********************/
	/* UNINSTALL EXTENSION */
	/***********************/
	public function uninstall($type, $extension, $id, $version='') {
		$this->errormsg = '';
		$id = (int)$id;

		if ($id < 1) {
			$this->errormsg = 'You must provide the Elxis Installer the exact extension id!';
			return false;
		}

		$fn = $type;
		switch($type) {
			case 'template':
				$idir = 'templates/'.$extension.'/';
			break;
			case 'atemplate':
				$idir = 'templates/admin/'.$extension.'/';
				$fn = 'template';
			break;
			case 'module':
				$idir = 'modules/'.$extension.'/';
			break;
			case 'component':
				$idir = 'components/'.$extension.'/';
			break;
			case 'engine':
				$idir = 'components/com_search/engines/'.$extension.'/';
			break;
			case 'auth':
				$idir = 'components/com_user/auth/'.$extension.'/';
			break;
			case 'plugin':
				$idir = 'components/com_content/plugins/'.$extension.'/';
			break;
			default:
				$this->errormsg = 'Not supported Extension type ('.$type.')';
				return false;
			break;
		}

		$func = 'uninstall_'.$fn;
		if (method_exists($this, $func)) {
			$ok = $this->$func($extension, $id);
			if (!$ok) { return false; }
		}

		if (file_exists(ELXIS_PATH.'/'.$idir)) {
			if (file_exists(ELXIS_PATH.'/'.$idir.'install.php')) {
				include_once(ELXIS_PATH.'/'.$idir.'install.php');
				$class = $extension.'_installer';
				if (class_exists($class, false)) {
					$obj = new $class();
					if (method_exists($obj, 'uninstall')) {
						$obj->uninstall();
					}
					unset($obj);
				}
			}

			if (!$this->efiles->deleteFolder($idir)) {
				$this->errormsg = $this->efiles->getError();
				return false;
			}
		}

		$langs = $this->efiles->listFolders('language/');
		if ($langs) {
			foreach ($langs as $lng) {
				if ($type == 'engine') {
					$lf = ELXIS_PATH.'/language/'.$lng.'/'.$lng.'.engine_'.$extension.'.php';
				} else if ($type == 'auth') {
					$lf = ELXIS_PATH.'/language/'.$lng.'/'.$lng.'.auth_'.$extension.'.php';
				} else if ($type == 'plugin') {
					$lf = ELXIS_PATH.'/language/'.$lng.'/'.$lng.'.plugin_'.$extension.'.php';
				} else {
					$lf = ELXIS_PATH.'/language/'.$lng.'/'.$lng.'.'.$extension.'.php';
				}

				if (file_exists($lf)) { $this->efiles->deleteFile($lf); }
			}
		}

		if ($this->log_uninstall == 1) {
			$this->logger($extension, 'uninstall', $version);
		}

		if ($this->notify_uninstall == 1) {
			$this->notify('uninstall', $type, $extension, $version);
		}

		return true;
	}


	/*************************/
	/* SYNCHRONIZE EXTENSION */
	/*************************/
	public function synchronize($type, $extension) {
		$db = eFactory::getDB();
		$eLang = eFactory::getLang();

		$this->is_synchro = true;
		if ($this->errormsg != '') { return false; }
		if (!defined('ELXIS_MULTISITE') || (ELXIS_MULTISITE == 1)) {
			$this->errormsg = $eLang->get('SYNC_EXT_SUBSITES');
			return false;
		}

		switch ($type) {
			case 'component':
				$ifolder = 'components';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = :xts";
			break;
			case 'module':
				$ifolder = 'modules';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__modules')." WHERE ".$db->quoteId('module')." = :xts";
			break;
			case 'template':
				$ifolder = 'templates';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__templates')." WHERE ".$db->quoteId('template')." = :xts";
			break;
			case 'engine':
				$ifolder = 'components/com_search/engines';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__engines')." WHERE ".$db->quoteId('engine')." = :xts";
			break;
			case 'auth':
				$ifolder = 'components/com_user/auth';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__authentication')." WHERE ".$db->quoteId('auth')." = :xts";
			break;
			case 'plugin':
				$ifolder = 'components/com_content/plugins';
				$sql = "SELECT COUNT(id) FROM ".$db->quoteId('#__plugins')." WHERE ".$db->quoteId('plugin')." = :xts";
			break;
			default:
				$this->errormsg = 'Invalid extension type!';
				return false;
			break;
		}

		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xts', $extension, PDO::PARAM_STR);
		$stmt->execute();
		$n = (int)$stmt->fetchResult();
		if ($n > 0) {
			$this->errormsg = sprintf($eLang->get('EXT_ALREADY_INSTALL'), $extension);
			return false;
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML($type, $extension);
		unset($exml);	

		if (($info['installed'] == false) || ($info['title'] == '')) {
			$this->errormsg = $eLang->get('XML_NFOUND_INV_PACK');
			return false;
		}

		$this->head = new stdClass;
		$this->head->type = $type;
		$this->head->name = $extension;
		$this->head->title = $info['title'];
		$this->head->version = $info['version'];
		$this->head->section = ($info['section'] != '') ? $info['section'] : 'frontend';
		$this->head->created = $info['created'];
		$this->head->author = $info['author'];
		$this->head->authorurl = $info['authorurl'];

		$func = 'install_'.$type;
		if (method_exists($this, $func)) {
			$ok = $this->$func();
			if (!$ok) { return false; }
		}

		//custom install actions
		if (file_exists(ELXIS_PATH.'/'.$ifolder.'/'.$extension.'/install.php')) {
			elxisLoader::loadFile($ifolder.'/'.$extension.'/install.php');
			$class = $extension.'_installer';
			if (class_exists($class, false)) {
				$obj = new $class();
				if (method_exists($obj, 'install')) {
					$obj->install();
				}
				unset($obj);
			}
		}

		return true;
	}


	/***********************************/
	/* MODULE SPECIFIC INSTALL ACTIONS */
	/***********************************/
	private function install_module() {
		$row = new modulesDbTable();
		$row->title = $this->head->title;
		$row->ordering = 99;
		$row->position = 'left';
		$row->published = 0;
		$row->module = $this->head->name;
		$row->showtitle = 2;
		$row->iscore = 0;
		$row->section = $this->head->section;
		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
			
		);
		$row->reorder($wheres, true);
		$modid = (int)$row->id;
		$section = $row->section;
		unset($row);

		$arow = new aclDbTable();
		$arow->category = 'module';
		$arow->element = $this->head->name;
		$arow->identity = $modid;
		$arow->action = 'view';
		$arow->minlevel = 0;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		$arow = new aclDbTable();
		$arow->category = 'module';
		$arow->element = $this->head->name;
		$arow->identity = $modid;
		$arow->action = 'manage';
		$arow->minlevel = 70;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		if ($section == 'frontend') {
			$db = eFactory::getDB();
			$menuid = 0;
			$sql = "INSERT INTO ".$db->quoteId('#__modules_menu')." (".$db->quoteId('mmid').", ".$db->quoteId('moduleid').", ".$db->quoteId('menuid').")"
			."\n VALUES (NULL, :xmod, :xmen)";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xmod', $modid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}

		return true;
	}


	/**************************************/
	/* COMPONENT SPECIFIC INSTALL ACTIONS */
	/**************************************/
	private function install_component() {
		$row = new componentsDbTable();
		$row->name = $this->head->title;
		$row->component = $this->head->name;
		$row->route = null;
		$row->iscore = 0;
		$row->params = null;
		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		$comid = (int)$row->id;
		unset($row);
		
		$arow = new aclDbTable();
		$arow->category = 'component';
		$arow->element = $this->head->name;
		$arow->identity = 0;
		$arow->action = 'view';
		$arow->minlevel = 0;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		$arow = new aclDbTable();
		$arow->category = 'component';
		$arow->element = $this->head->name;
		$arow->identity = 0;
		$arow->action = 'manage';
		$arow->minlevel = 70;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		return true;
	}


	/*************************************/
	/* TEMPLATE SPECIFIC INSTALL ACTIONS */
	/*************************************/
	private function install_template() {
		$row = new templatesDbTable();
		$row->title = $this->head->title;
		$row->template = $this->head->name;
		$row->section = $this->head->section;
		$row->iscore = 0;
		$row->params = null;
		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		return true;
	}


	/******************************************/
	/* SEARCH ENGINE SPECIFIC INSTALL ACTIONS */
	/******************************************/
	private function install_engine() {
		$row = new enginesDbTable();
		$row->title = $this->head->title;
		$row->ordering = 99;
		$row->published = 0;
		$row->engine = $this->head->name;
		$row->alevel = 0;
		$row->defengine = 0;
		$row->iscore = 0;

		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		$row->reorder(array(), true);
		return true;
	}


	/**************************************************/
	/* AUTHENTICATION METHOD SPECIFIC INSTALL ACTIONS */
	/**************************************************/
	private function install_auth() {
		$row = new authenticationDbTable();
		$row->title = $this->head->title;
		$row->ordering = 99;
		$row->published = 0;
		$row->auth = $this->head->name;
		$row->iscore = 0;

		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		$row->reorder(array(), true);
		return true;
	}


	/*******************************************/
	/* CONTENT PLUGIN SPECIFIC INSTALL ACTIONS */
	/*******************************************/
	private function install_plugin() {
		$row = new pluginsDbTable();
		$row->title = $this->head->title;
		$row->plugin = $this->head->name;
		$row->alevel = 0;
		$row->ordering = 99;
		$row->published = 0;
		$row->iscore = 0;
		if (!$row->insert()) {
			$this->errormsg = $row->getErrorMsg();
			if ($this->is_synchro == false) {
				$this->efiles->deleteFolder($this->ifolder);
			}
			return false;
		}

		$row->reorder(array(), true);
		return true;
	}


	/**********************************/
	/* COMPONENT SPECIFIC UPDATE ACTIONS */
	/**********************************/
	private function update_component() {
		return true;
	}


	/**********************************/
	/* MODULE SPECIFIC UPDATE ACTIONS */
	/**********************************/
	private function update_module() {
		return true;
	}


	/**********************************/
	/* TEMPLATE SPECIFIC UPDATE ACTIONS */
	/**********************************/
	private function update_template() {
		return true;
	}


	/**********************************/
	/* ENGINE SPECIFIC UPDATE ACTIONS */
	/**********************************/
	private function update_engine() {
		return true;
	}


	/*************************************************/
	/* AUTHENTICATION METHOD SPECIFIC UPDATE ACTIONS */
	/*************************************************/
	private function update_auth() {
		return true;
	}


	/**********************************/
	/* PLUGIN SPECIFIC UPDATE ACTIONS */
	/**********************************/
	private function update_plugin() {
		return true;
	}


	/****************************************/
	/* COMPONENT SPECIFIC UNINSTALL ACTIONS */
	/****************************************/
	private function uninstall_component($extension, $id=0) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = :xcomp");
		$stmt->bindParam(':xcomp', $extension, PDO::PARAM_STR);
		$stmt->execute();

		$ctg = 'component';
		$sql = "DELETE FROM ".$db->quoteId('#__acl')." WHERE ".$db->quoteId('category')." = :xcat AND ".$db->quoteId('element')." = :xelem";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $extension, PDO::PARAM_STR);
		$stmt->execute();

		$sql = "DELETE FROM ".$db->quoteId('#__acl')." WHERE ".$db->quoteId('category')." = :xelem";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xelem', $extension, PDO::PARAM_STR);
		$stmt->execute();

		$sql = "DELETE FROM ".$db->quoteId('#__translations')." WHERE ".$db->quoteId('category')." = :xelem";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xelem', $extension, PDO::PARAM_STR);
		$stmt->execute();

		return true;
	}


	/*************************************/
	/* MODULE SPECIFIC UNINSTALL ACTIONS */
	/*************************************/
	private function uninstall_module($extension, $id) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__modules')." WHERE ".$db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__modules_menu')." WHERE ".$db->quoteId('moduleid')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$ctg = 'module';
		$sql = "DELETE FROM ".$db->quoteId('#__acl')
		."\n WHERE ".$db->quoteId('category')." = :xcat AND ".$db->quoteId('element')." = :xelem AND ".$db->quoteId('identity')." = :xident";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $extension, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $id, PDO::PARAM_INT);
		$stmt->execute();

		$sql = "DELETE FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = :xcat AND ".$db->quoteId('elid')." = :xelid";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/***************************************/
	/* TEMPLATE SPECIFIC UNINSTALL ACTIONS */
	/***************************************/
	private function uninstall_template($extension, $id) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__templates')." WHERE ".$db->quoteId('id')." = :xid AND ".$db->quoteId('template')." = :xtpl");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->bindParam(':xtpl', $extension, PDO::PARAM_STR);
		$stmt->execute();

		return true;
	}


	/********************************************/
	/* SEARCH ENGINE SPECIFIC UNINSTALL ACTIONS */
	/********************************************/
	private function uninstall_engine($extension, $id) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__engines')." WHERE ".$db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/****************************************************/
	/* AUTHENTICATION METHOD SPECIFIC UNINSTALL ACTIONS */
	/****************************************************/
	private function uninstall_auth($extension, $id) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__authentication')." WHERE ".$db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/*********************************************/
	/* CONTENT PLUGIN SPECIFIC UNINSTALL ACTIONS */
	/*********************************************/
	private function uninstall_plugin($extension, $id) {
		$db = eFactory::getDB();

		$stmt = $db->prepare("DELETE FROM ".$db->quoteId('#__plugins')." WHERE ".$db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/*******************************************/
	/* SEND NOTIFICATION EMAIL TO TECH MANAGER */
	/*******************************************/
	private function notify($action, $type, $extension, $version) {
		$elxis = eFactory::getElxis();

		if (($version == '') || !is_numeric($version)) { $version = 0; }
		if ($elxis->getConfig('MAIL_MANAGER_NAME') == '') { return; }
		if ($elxis->getConfig('MAIL_MANAGER_EMAIL') == '') { return; }

    	$parsed = parse_url($elxis->getConfig('URL')); 
 		$host = preg_replace('#^(www\.)#i', '', $parsed['host']);
		$vtxt = '';
		$vtxt2 = '';
		if ($version > 0) { $vtxt = ' v'.$version; $vtxt2 = ' to v'.$version; }

		if ($action == 'install') {
			switch ($type) {
				case 'module':
					$subject = 'Module '.$extension.$vtxt.' installed on '.$host;
				break;
				case 'component':
					$subject = 'Component '.$extension.$vtxt.' installed on '.$host;
				break;
				case 'template':
				case 'atemplate':
					$subject = 'Template '.$extension.$vtxt.' installed on '.$host;
				break;
				case 'engine':
					$subject = 'Search engine '.$extension.$vtxt.' installed on '.$host;
				break;
				case 'auth':
					$subject = 'Authentication method '.$extension.$vtxt.' installed on '.$host;
				break;
				case 'plugin':
					$subject = 'Content plugin '.$extension.$vtxt.' installed on '.$host;
				break;
				default:
					$subject = 'Extension '.$extension.$vtxt.' installed on '.$host;
				break;
			}
		} elseif ($action == 'update') {
			switch ($type) {
				case 'module':
					$subject = 'Module '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				case 'component':
					$subject = 'Component '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				case 'template':
				case 'atemplate':
					$subject = 'Template '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				case 'engine':
					$subject = 'Search engine '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				case 'auth':
					$subject = 'Authentication method '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				case 'plugin':
					$subject = 'Content plugin '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
				default:
					$subject = 'Extension '.$extension.' updated'.$vtxt2.' on '.$host;
				break;
			}
		} elseif ($action == 'uninstall') {
			switch ($type) {
				case 'module':
					$subject = 'Module '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				case 'component':
					$subject = 'Component '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				case 'template':
				case 'atemplate':
					$subject = 'Template '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				case 'engine':
					$subject = 'Search engine '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				case 'auth':
					$subject = 'Authentication method '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				case 'plugin':
					$subject = 'Content plugin '.$extension.$vtxt.' uninstalled from '.$host;
				break;
				default:
					$subject = 'Extension '.$extension.$vtxt.' uninstalled from '.$host;
				break;
			}
		} else {
			$subject = 'Extension '.$extension.$vtxt.' '.$action.' on '.$host;
		}

    	$to = $elxis->getConfig('MAIL_MANAGER_EMAIL').','.$elxis->getConfig('MAIL_MANAGER_NAME');

		$body = 'This is a notification message from the Elxis extensions manager on '.$elxis->getConfig('URL')."\r\n\r\n";
		$body .= "Elxis installer report\r\n";
		$body .= "----------------------------------------------\r\n";
		$body .= "Action    : \t".$action."\r\n";
		$body .= "Type      : \t".$type."\r\n";
		$body .= "Extension : \t".$extension."\r\n";
		if ($version > 0) { $body .= "Version   : \t".$version."\r\n"; }
		$body .= "User      : \t".$elxis->user()->uname.' (UID '.$elxis->user()->uid.')'."\r\n";
		$body .= "Date      : \t".gmdate('Y-m-d H:i:s')." (UTC)\r\n";
		$body .= "Site URL  : \t".$elxis->getConfig('URL')."\r\n\r\n\r\n";
		$body .= "You have selected to receive Elxis installer notifications on\r\n";
		if ($this->notify_install == 1) { $body .= "\t- New extension install\r\n"; }
		if ($this->notify_uninstall == 1) { $body .= "\t- Extension uninstall\r\n"; }
		if ($this->notify_update == 1) { $body .= "\t- Extension update\r\n"; }
		$body .= "\r\n".'If you dont want to receive such notifications you can ';
		$body .= 'turn off notifications in Extensions Manager component parameters.'."\r\n\r\n\r\n";
		$body .= "----------------------------------------------\r\n";
		$body .= 'Please do not reply to this message as it was generated automatically by the Elxis Installer ';
		$body .= 'and it was sent for informational purposes.'."\r\n";
		$body .= 'Powered by Elxis v'.$elxis->getVersion()." ( http://www.elxis.org )\r\n";

		$elxis->sendmail($subject, $body, '', null, 'plain', $to, null, null, null, 1);
	}


	/*****************************************************/
	/* WRITE INSTALL/UNINSTALL/UPDATE MESSSAGE INTO LOGS */
	/*****************************************************/
	private function logger($extension, $action, $version='') {
		$elxis = eFactory::getElxis();

		if (($version == '') || !is_numeric($version)) { $version = 0; }
		$txt = '['.gmdate('Y-m-d H:i:s').' UTC]'." ".ucfirst($action).' '.$extension.' ';
		if ($version > 0) { $txt .= 'v'.$version.' '; }
		$txt .= 'by '.$elxis->user()->uname.' (UID '.$elxis->user()->uid.')'._LEND._LEND;
		$ok = eFactory::getFiles()->writeFile('logs/installer.log', $txt, true);
		return $ok;
	}


	/************ PUBLIC GETTERS ****************/


	public function getError() {
		return $this->errormsg;
	}

	public function getWarnings() {
		return $this->warnings;
	}

	public function getHead() {
		return $this->head;
	}

	public function getDependencies() {
		return $this->dependencies;
	}

	public function isUpdate() {
		return $this->is_update;
	}

	public function getPackage() {
		return $this->package;
	}

	public function getUfolder() {
		return $this->ufolder;
	}

	public function getUffolder() {
		return $this->uffolder;
	}

	public function getIfolder() {
		return $this->ifolder;
	}

	public function getCurrent() {
		$arr = array(
			'version' => $this->cur_version,
			'created' => $this->cur_created,
			'author' => $this->cur_author,
			'authorurl' => $this->cur_authorurl
		);
		return $arr;
	}

}


interface iExtension {
    public function install();
    public function uninstall();
    public function update();
}

?>