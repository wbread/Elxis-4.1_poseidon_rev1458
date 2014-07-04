<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainSearchController {

	private $view = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view) {
		$this->view = $view;
	}


	/*******************/
	/* PROCESS REQUEST */
	/*******************/
	public function run($engine) {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$eSearch = eFactory::getSearch();

		$engines = $eSearch->getEngines();

		if (count($engines) == 0) {
			exitPage::make('error', 'CSEA-0004', $eLang->get('NO_AVAIL_ENGINES'));
		}

		if ($engine != '') {
			if (!isset($engines[$engine])) {
				exitPage::make('error', 'CSEA-0005', $eLang->get('ENGINE_NOT_AVAIL'));
			}
			$eSearch->setEngine($engine);
		} else {
			$def = $eSearch->getDefaultEngine();
			$eSearch->setEngine($def);
		}

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }

		$eSearch->search($page);
		$page = $eSearch->getPage();

		$info = $eSearch->engineInfo();
		if ($page > 1) {
			$eDoc->setTitle($eLang->get('SEARCH').' : '.$info['title'].' : '.eFactory::getLang()->get('PAGE').' '.$page);
		} else {
			$eDoc->setTitle($eLang->get('SEARCH').' : '.$info['title']);
		}
		$eDoc->setDescription($info['description']);
		$eDoc->setKeywords($info['metakeys']);

		$pathway = eFactory::getPathway();
		$pathway->addNode($eLang->get('SEARCH'), 'search:/');
		if ($page > 1) {
			$pathway->addNode($info['title'], 'search:'.$eSearch->getCurrentEngine().'.html');
			$pathway->addNode(eFactory::getLang()->get('PAGE').' '.$page);
		} else {
			$pathway->addNode($info['title']);
		}

		unset($info, $pathway, $eDoc);

		$params = $this->componentParams();

		$this->view->makePage($params);
	}


	/****************************/
	/* GET COMPONENT PARAMETERS */
	/****************************/
	private function componentParams() {
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_search');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$params_str = (string)$stmt->fetchResult();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($params_str, '', 'component');
		return $params;
	}

}

?>