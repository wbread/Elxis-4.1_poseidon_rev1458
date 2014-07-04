<?php 
/**
* @version		$Id: languages.model.php 584 2011-09-04 16:17:02Z datahell $
* @package		Elxis
* @subpackage	Component Languages
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class languagesModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*******************************/
	/* GET INSTALLED LANGUAGE INFO */
	/*******************************/
	public function getInstalledLangs($forcompare=false) {
		$eFiles = eFactory::getFiles();

		$installed = $eFiles->listFolders('language/');
		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		$rows = array();
		if (!$forcompare) {
			$stmt = $this->db->prepare('SELECT COUNT(trid) FROM #__translations WHERE language = :lng');
		}
		foreach ($installed as $lng) {
			if (!isset($langdb[$lng])) { continue; }
			$rows[$lng] = array_change_key_case($langdb[$lng], CASE_LOWER);
			$lfs = $eFiles->listFiles('language/'.$lng.'/', '.php');
			if ($forcompare) {
				$rows[$lng]['files'] = $lfs;
			} else {
				$rows[$lng]['files'] = ($lfs) ? count($lfs) : 0;
				$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
				$stmt->execute();
				$rows[$lng]['translations'] = (int)$stmt->fetchResult();
			}
			$rows[$lng]['identifier'] = $lng;
		}
		return $rows;
	}

}

?>