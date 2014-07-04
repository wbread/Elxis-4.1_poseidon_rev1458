<?php 
/**
* @version		$Id: ibm.adapter.php 547 2011-07-30 08:12:50Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisIbmAdapter extends elxisDbAdapter {


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
		if ($limit < 0) { return $sql; }
		if ($offset < 0) { $offset = 0; }

        if ($offset == 0 && $limit > 0) {
            $sql .= ' FETCH FIRST '.$limit.' ROWS ONLY';
            return $sql;
        }

        $limit_sql = "SELECT z2.*
			FROM (
            	SELECT ROW_NUMBER() OVER() AS \"ELXIS_DB_ROWNUM\", z1.*
                FROM (".$sql.") z1
            ) z2
			WHERE z2.elxis_db_rownum BETWEEN ".($offset+1)." AND ".($offset+$limit);
        return $limit_sql;
	}


    /****************************/
	/* LIST ALL DATABASE TABLES */
	/****************************/
    public function listTables() {
    	$stmt = $this->pdo->prepare('SELECT tabname FROM SYSCAT.TABLES');
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