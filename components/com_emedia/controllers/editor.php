<?php 
/**
* @version		$Id: editor.php 1027 2012-04-13 19:49:50Z datahell $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class editorMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view, true);
	}


	/***************************************/
	/* PREPARE MEDIA MANAGER UI FOR EDITOR */
	/***************************************/
	public function editorui() {
		$eLang = eFactory::getLang();

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath)) {
			if (!eFactory::getFiles()->createFolder($this->relpath)) {
				$this->view->fatalError('Could not create required folder '.$this->relpath);
				return;
			}
		}

		$this->importCSS();
		$this->importJS();
		eFactory::getDocument()->setTitle($eLang->get('MEDIA_MANAGER'));
		$this->view->editorUI();
	}

}

?>