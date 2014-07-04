<?php 
/**
* @version		$Id: statement.class.php 274 2011-04-03 18:54:47Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisPDOStatement extends PDOStatement {

	private $pdo = null;
	private $elxisdebug = 0;


	protected function __construct($pdo, $elxisdebug=0) {
		$this->pdo = $pdo;
		$this->elxisdebug = $elxisdebug;
		$this->setFetchMode(PDO::FETCH_OBJ);
	}


	/********************************/
	/* EXECUTE A PREPARED STATEMENT */
	/********************************/
	public function execute($params=null, $options=array()) {
		if (isset($options['fetch'])) {
			if (is_string($options['fetch'])) {
				$this->setFetchMode(PDO::FETCH_CLASS, $options['fetch']);
			} else {
				$this->setFetchMode($options['fetch']);
			}
		}

		$this->monitor();
		$result = parent::execute($params);
		return $result;
	}


	/*********************/
	/* MONITOR SQL QUERY */
	/*********************/
	private function monitor() {
		if ($this->elxisdebug > 1) {
			if (($this->elxisdebug == 2) || ($this->elxisdebug == 3)) {
				eRegistry::get('ePerformance')->addQuery();
			} else if (($this->elxisdebug == 4) || ($this->elxisdebug == 5)) {
				eRegistry::get('ePerformance')->addQuery($this->queryString);
			}
		}
	}


	/********************/
	/* GET QUERY STRING */
	/********************/
	public function getQueryString() {
		return $this->queryString;
	}


	/***************************************************************/
	/* GET RESULT SET AS AN ASSOCIATIVE ARRAY KEYED BY GIVEN FIELD */
	/***************************************************************/
	public function fetchAllAssoc($key, $fetchMode=null) {
		if ($fetchMode !== null) {
			if (is_string($fetchMode)) {
				$this->setFetchMode(PDO::FETCH_CLASS, $fetchMode);
			} else {
				$this->setFetchMode($fetchMode);
			}
		}

		$rows = array();
		foreach ($this as $record) {
			$record_key = is_object($record) ? $record->$key : $record[$key];
			$rows[$record_key] = $record;
		}

		return $rows;
	}


	/**************************************************************/
	/* FETCH A 2-COLUMN RESULT SET AS AN ARRAY OF KEY-VALUE PAIRS */
	/**************************************************************/
	public function fetchPairs($key_index=0, $value_index=1) {
		$this->setFetchMode(PDO::FETCH_NUM);
		$rows = array();
		foreach ($this as $record) {
			$rows[$record[$key_index]] = $record[$value_index];
		}
		return $rows;
	}


	/********************************************/
	/* RETURN SINGLE COLUMN AS AN INDEXED ARRAY */
	/********************************************/
	public function fetchCol($index = 0) {
		return $this->fetchAll(PDO::FETCH_COLUMN, $index);
	}


	/********************************************/
	/* RETURN SINGLE COLUMN AS AN INDEXED ARRAY */
	/********************************************/
	public function fetchResult() {
		$row = $this->fetch(PDO::FETCH_NUM);
		return ($row && isset($row[0])) ? $row[0] : null;
	}

}

?>