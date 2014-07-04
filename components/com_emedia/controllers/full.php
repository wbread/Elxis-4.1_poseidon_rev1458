<?php 
/**
* @version		$Id: full.php 1285 2012-09-14 17:58:07Z datahell $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fullMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view, false);
	}


	/***************************************/
	/* PREPARE FULL SCALE MEDIA MANAGER UI */
	/***************************************/
	public function fullui() {
		$eLang = eFactory::getLang();

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath)) {
			if (!eFactory::getFiles()->createFolder($this->relpath)) {
				$this->view->fatalError('Could not create required folder '.$this->relpath);
				return;
			}
		}

		$this->importCSS();
		$this->importJS();
		eFactory::getPathway()->addNode($eLang->get('MEDIA_MANAGER'));
		eFactory::getDocument()->setTitle($eLang->get('MEDIA_MANAGER').' - '.$eLang->get('ADMINISTRATION'));
		$this->view->fullUI();
	}


	/*****************************************/
	/* CONFIGURE JS MEDIA MANAGER ON-THE-FLY */
	/*****************************************/
	public function configure() {
		$elxis = eFactory::getElxis();

		$curlang = eFactory::getLang()->currentLang();
		if (file_exists(ELXIS_PATH.'/components/com_emedia/js/language/'.$curlang.'.js')) {
			$lng = $curlang;
		} else {
			$lng = 'en';
		}

		$editor = 0;
		if (isset($_GET['editor'])) { $editor = (int)$_GET['editor']; }

		$browse_only = 'true';
		$caps = ($editor == 1) ? "'select'" : "'download'";
		if ($elxis->acl()->check('com_emedia', 'files', 'edit') > 0) {
			$browse_only = 'false';
			if ($editor == 1) {
				$caps .= ", 'rename', 'delete', 'resize'";
			} else {
				$caps .= ", 'rename', 'delete', 'resize', 'compress'";
			}
		}
		if ($elxis->acl()->check('com_emedia', 'files', 'upload') > 0) {
			$browse_only = 'false';
			$caps .= ", 'upload'";
		}

		$this->pageHeaders('text/javascript');

		echo 'var cfg_lang = \''.$lng.'\';'."\n";
		echo 'var cfg_viewmode = \'grid\';'."\n";
		echo 'var cfg_autoload = true;'."\n";
		echo 'var cfg_showfullpath = false;'."\n";
		echo 'var cfg_browseonly = '.$browse_only.';'."\n";
		echo 'var cfg_fileroot = \'\';'."\n";
		echo 'var cfg_urlbase = \''.$elxis->secureBase().'\';'."\n";
		echo 'var cfg_relpath = \''.$this->relpath.'\';'."\n"; //fixes select on multisites
		echo 'var cfg_showthumbs = true;'."\n";
		echo 'var cfg_connector = \''.$elxis->makeAURL('emedia:connect/', 'inner.php').'\';'."\n";
		echo 'var cfg_capabilities = new Array('.$caps.');'."\n";
		echo 'var cfg_editor = '.$editor.';'."\n";
		echo 'var cfg_tree_files = '.$this->tree_show_files.';'."\n";
		exit();
	}

}

?>