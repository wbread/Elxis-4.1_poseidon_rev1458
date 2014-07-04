<?php 
/**
* @version		$Id: firebird.adapter.php 547 2011-07-30 08:12:50Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisFirebirdAdapter extends elxisDbAdapter {


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
		if (($limit < 1) && ($offset < 1)) { return $sql; }
		$str = 'SELECT ';
		if ($limit >= 0) { $str .= 'FIRST '.$limit.' '; }
		if ($offset > 0) { $str .= 'SKIP '.$offset.' '; }
		$sql = preg_replace('/^[ \t]*select/i', $str, $sql);
		return $sql;
	}


    /****************************/
	/* LIST ALL DATABASE TABLES */
	/****************************/
    public function listTables() {
    	$sql = 'select rdb$relation_name from rdb$relations where rdb$relation_name not like \'RDB$%\'';
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