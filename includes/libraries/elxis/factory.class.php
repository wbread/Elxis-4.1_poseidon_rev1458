<?php 
/**
* @version		$Id: factory.class.php 1106 2012-05-05 18:22:48Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2012 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class eFactory {


	/*************************/
	/* GET DATABASE INSTANCE */
	/*************************/
	static public function getDB() {
		if (!eRegistry::isLoaded('database')) {
			$database = self::makeDB();
			return $database;
		} else {
			return eRegistry::get('database');
		}
	}


	/**************************/
	/* MAKE DATABASE INSTANCE */
	/**************************/
	static private function makeDB() {
		if (!eRegistry::isLoaded('elxis')) {
			elxisLoader::loadFile('includes/libraries/elxis/framework.class.php');
			$elxis = new elxisFramework();
			eRegistry::set($elxis, 'elxis');
		} else {
			$elxis = eRegistry::get('elxis');
		}

		elxisLoader::loadFile('includes/libraries/elxis/database.class.php');
		$database = new elxisDatabase();
		eRegistry::set($database, 'database');
		return $database;
	}


	/***************************/
	/* GET ELXIS CORE INSTANCE */
	/***************************/
	static public function getElxis() {
		if (!eRegistry::isLoaded('elxis')) {
			elxisLoader::loadFile('includes/libraries/elxis/framework.class.php');
			$elxis = new elxisFramework();
			eRegistry::set($elxis, 'elxis');
			return $elxis;
		} else {
			return eRegistry::get('elxis');
		}
	}


	/*****************************/
	/* GET/INITIATE URI INSTANCE */
	/*****************************/
	static public function getURI() {
		if (!eRegistry::isLoaded('eURI')) {
			elxisLoader::loadFile('includes/libraries/elxis/uri.class.php');
			$eURI = new elxisUri();
			eRegistry::set($eURI, 'eURI');
			return $eURI;
		} else {
			return eRegistry::get('eURI');
		}
	}


	/**********************************/
	/* GET/INITIATE LANGUAGE INSTANCE */
	/**********************************/
	static public function getLang() {
		if (!eRegistry::isLoaded('eLang')) {
			elxisLoader::loadFile('includes/libraries/elxis/language.class.php');
			$eLang = new elxisLanguage();
			eRegistry::set($eLang, 'eLang');
			return $eLang;
		} else {
			return eRegistry::get('eLang');
		}
	}


	/**********************************/
	/* GET/INITIATE DOCUMENT INSTANCE */
	/**********************************/
	static public function getDocument() {
		if (!eRegistry::isLoaded('eDoc')) {
			elxisLoader::loadFile('includes/libraries/elxis/document.class.php');
			$eDoc = new elxisDocument();
			eRegistry::set($eDoc, 'eDoc');
			return $eDoc;
		} else {
			return eRegistry::get('eDoc');
		}
	}


	/******************************/
	/* GET/INITIATE DATE INSTANCE */
	/******************************/
	static public function getDate() {
		if (!eRegistry::isLoaded('eDate')) {
			elxisLoader::loadFile('includes/libraries/elxis/date.class.php');
			$eDate = new elxisDate();
			eRegistry::set($eDate, 'eDate');
			return $eDate;
		} else {
			return eRegistry::get('eDate');
		}
	}


	/*******************************/
	/* GET/INITIATE FILES INSTANCE */
	/*******************************/
	static public function getFiles() {
		if (!eRegistry::isLoaded('eFiles')) {
			elxisLoader::loadFile('includes/libraries/elxis/files.class.php');
			$eFiles = new elxisFiles();
			eRegistry::set($eFiles, 'eFiles');
			return $eFiles;
		} else {
			return eRegistry::get('eFiles');
		}
	}


	/******************************/
	/* GET/INITIATE MENU INSTANCE */
	/******************************/
	static public function getMenu() {
		if (!eRegistry::isLoaded('eMenu')) {
			elxisLoader::loadFile('includes/libraries/elxis/menu.class.php');
			$eMenu = new elxisMenu();
			eRegistry::set($eMenu, 'eMenu');
			return $eMenu;
		} else {
			return eRegistry::get('eMenu');
		}
	}


	/*********************************/
	/* GET/INITIATE SESSION INSTANCE */
	/*********************************/
	static public function getSession($options=array()) {
		if (!eRegistry::isLoaded('eSession')) {
			elxisLoader::loadFile('includes/libraries/elxis/session.class.php');
			$eSession = new elxisSession($options);
			if ($eSession->getState() == 'expired') {
				$eSession->restart();
			}
			eRegistry::set($eSession, 'eSession');
			return $eSession;
		} else {
			return eRegistry::get('eSession');
		}
	}


	/********************************/
	/* GET/INITIATE MODULE INSTANCE */
	/********************************/
	static public function getModule() {
		if (!eRegistry::isLoaded('eModule')) {
			elxisLoader::loadFile('includes/libraries/elxis/module.class.php');
			$eModule = new elxisModule();
			eRegistry::set($eModule, 'eModule');
			return $eModule;
		} else {
			return eRegistry::get('eModule');
		}
	}


	/******************************/
	/* GET ELXIS PATHWAY INSTANCE */
	/******************************/
	static public function getPathway($pathway_here=false) {
		if (!eRegistry::isLoaded('ePathway')) {
			elxisLoader::loadFile('includes/libraries/elxis/pathway.class.php');
			$ePathway = new elxisPathway($pathway_here);
			eRegistry::set($ePathway, 'ePathway');
			return $ePathway;
		} else {
			return eRegistry::get('ePathway');
		}
	}


	/******************************/
	/* GET ELXIS SEARCH INSTANCE */
	/******************************/
	static public function getSearch() {
		if (!eRegistry::isLoaded('eSearch')) {
			elxisLoader::loadFile('includes/libraries/elxis/search.class.php');
			$eSearch = new elxisSearch();
			eRegistry::set($eSearch, 'eSearch');
			return $eSearch;
		} else {
			return eRegistry::get('eSearch');
		}
	}


	/*****************************/
	/* GET ELXIS PLUGIN INSTANCE */
	/*****************************/
	static public function getPlugin() {
		if (!eRegistry::isLoaded('ePlugin')) {
			elxisLoader::loadFile('includes/libraries/elxis/plugin.class.php');
			$ePlugin = new elxisPlugin();
			eRegistry::set($ePlugin, 'ePlugin');
			return $ePlugin;
		} else {
			return eRegistry::get('ePlugin');
		}
	}


	/****************************/
	/* GET ELXIS CACHE INSTANCE */
	/****************************/
	static public function getCache() {
		if (!eRegistry::isLoaded('eCache')) {
			elxisLoader::loadFile('includes/libraries/elxis/cache.class.php');
			$eCache = new elxisCache();
			eRegistry::set($eCache, 'eCache');
			return $eCache;
		} else {
			return eRegistry::get('eCache');
		}
	}

}

?>