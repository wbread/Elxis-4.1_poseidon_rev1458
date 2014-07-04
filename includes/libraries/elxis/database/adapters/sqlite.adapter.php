<?php 
/**
* @version		$Id: sqlite.adapter.php 547 2011-07-30 08:12:50Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisSqliteAdapter extends elxisDbAdapter {
	

	/*************************************/
	/* CALL THE PARENT CLASS CONSTRUCTOR */
	/*************************************/
	public function __construct($pdo=null) {
		parent::__construct($pdo);
	}


	/*************************************/
	/* ADD LIMIT/OFFSET TO SQL STATEMENT */
	/*************************************/
	public function addLimit($sql, $offset=-1, $limit=-1) {
		if ($offset >= 0) {
			if ($limit > 0) {
				return $sql.' LIMIT '.$limit.' OFFSET '.$offset;
			} else {
				return $sql.' LIMIT 999999999 OFFSET '.$offset;
			}
		} else if ($limit > 0) {
			return $sql.' LIMIT '.$limit;
		} else {
			return $sql;
		}
	}


    /****************************/
	/* LIST ALL DATABASE TABLES */
	/****************************/
    public function listTables() {
		$sql = "SELECT name FROM sqlite_master WHERE type='table'"
		."\n UNION ALL SELECT name FROM sqlite_temp_master"
		."\n WHERE type='table' ORDER BY name";
    	$stmt = $this->pdo->prepare($sql);
    	$stmt->execute();
    	return $stmt->fetchCol();
    }


	/***********************************/
	/* BACKUP DATABASE (NOT SUPPORTED) */
	/***********************************/
	public function backup($params) {
		return -1;
	}

}

?>