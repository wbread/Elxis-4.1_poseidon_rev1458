<?php 
/**
* @version		$Id: asearch.php 949 2012-03-03 18:58:36Z datahell $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class asearchSearchController {

	private $view = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view) {
		$this->view = $view;
	}


	/***********************/
	/* LIST SEARCH ENGINES */
	/***********************/
	public function listengines() {
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') < 1) {
			$eLang = eFactory::getLang();

			eFactory::getPathway()->addNode($eLang->get('SEARCH'), 'search:/');
			eFactory::getDocument()->setTitle($eLang->get('SEARCH').' - '.$eLang->get('ADMINISTRATION'));

			$title = $eLang->get('SEARCH');
			$msg = $eLang->get('NALLOW_EDIT_ENG');
			$this->view->accessError($title, $msg);
			return;
		}

		$redirurl = $elxis->makeAURL('extmanager:engines/');
		$elxis->redirect($redirurl);
	}

}

?>