<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/


class elxisPgsqlImporter extends elxisDbImporter {

	private $replacePrefixes = false;
	private $db2 = null;


	/***********************/
	/* VALIDATE PARAMETERS */
	/***********************/
	protected function validate() {
		$req_params = array('db_host', 'db_user', 'db_pass', 'db_name', 'db_port');
		foreach ($req_params as $req_param) {
			if (!isset($this->params[$req_param])) {
				$this->error = true;
				$this->errormsg = 'Required parameter '.$req_param.' is not set!';
				return false;
			}
			if ($req_param == 'db_port') { $this->params['db_port'] = (int)$this->params['db_port']; continue; }
			if (trim($this->params[$req_param]) == '') {
				$this->error = true;
				$this->errormsg = 'Required parameter '.$req_param.' is empty!';
				return false;
			}		
		}
		return true;
	}


	/***********************/
	/* CONNECT TO DATABASE */
	/***********************/
	protected function connect() {
		if (!class_exists('elxisDatabase', false)) {
			require_once(ELXIS_PATH.'/includes/libraries/elxis/database.class.php');
		}
		$params = array(
			'dbtype' => 'pgsql',
			'host' => $this->params['db_host'],
			'port' => $this->params['db_port'],
			'dbname' => $this->params['db_name'],
			'username' => $this->params['db_user'],
			'password' => $this->params['db_pass'],
			'persistent' => 0,
			'dsn' => '',
			'scheme' => '',
			'table_prefix' => $this->params['db_prefix'],
			'debug' => 0
		);

		$this->db2 = new elxisDatabase($params, array(), false);
		$okcon = $this->db2->connect('', $this->params['db_user'], $this->params['db_pass'], array(), true);
		if (!$okcon) {
			$this->error = true;
			$this->errormsg = $this->db2->getErrorMsg();
			if ($this->errormsg == '') { $this->errormsg = 'Could not connect to database!'; }
			return false;
		}

		return true;
	}


	/****************************/
	/* DISCONNECT FROM DATABASE */
	/****************************/
	public function disconnect() {
		if ($this->db2) {
			$this->db2->disconnect();
		}
	}


	/**********************/
	/* IMPORT AN SQL FILE */
	/**********************/
	public function import() {
		if ($this->error) { return false; }
		if (!isset($this->params['file'])) {
			$this->errormsg = 'Parameter file was not set!';
			return false;
		}
		if (trim($this->params['file']) == '') {
			$this->errormsg = 'Parameter file is empty!';
			return false;
		}
		if (!file_exists($this->params['file'])) {
			$this->errormsg = 'Import SQL file does not exist!';
			return false;
		}
		if (strtolower(substr(strrchr($this->params['file'], '.'), 1)) != 'sql') {
			$this->errormsg = 'Import file is not an SQL file!';
			return false;
		}

		if (isset($this->params['db_prefix']) && isset($this->params['db_prefix_old']) && ($this->params['db_prefix']) != $this->params['db_prefix_old']) { 
			$this->replacePrefixes = true;
		}

		$templine = '';
		$lines = file($this->params['file']);
		if ($lines) {
			foreach ($lines as $line) {
				$trimmed_line = trim($line);
				if (($trimmed_line == '') || (substr($trimmed_line, 0, 2) == '--')) { continue; }
				$templine .= $line;
				if (substr($trimmed_line, -1, 1) == ';') {
					if ($this->replacePrefixes) {
						$templine = $this->replaceTblPrefix($templine, $this->params['db_prefix_old'], $this->params['db_prefix']);
					}
					if (!$this->db2->exec($templine, '')) {
						$this->error = true;
						$this->errormsg = $this->db2->getErrorMsg();
						if ($this->errormsg == '') {
							$this->errormsg = 'An SQL query failed to be executed!';
						}
						$this->disconnect();
						return false;
					}
					$this->queries++;
					$templine = '';
				}
			}
		}
		return true;
	}


	/**************************/
	/* REPLACE TABLE PREFIXES */
	/**************************/
	private function replaceTblPrefix($sql, $oldprf, $newprf) {
		if ($oldprf == $newprf) { return $sql; }
		$sql = str_replace('DROP TABLE "'.$oldprf, 'DROP TABLE "'.$newprf, $sql);
		$sql = str_replace('DROP TABLE '.$oldprf, 'DROP TABLE '.$newprf, $sql);
		$sql = str_replace('DROP TABLE IF EXISTS "'.$oldprf, 'DROP TABLE IF EXISTS "'.$newprf, $sql);
		$sql = str_replace('DROP TABLE IF EXISTS '.$oldprf, 'DROP TABLE IF EXISTS '.$newprf, $sql);
		$sql = str_replace('CREATE TABLE "'.$oldprf, 'CREATE TABLE "'.$newprf, $sql);
		$sql = str_replace('CREATE TABLE '.$oldprf, 'CREATE TABLE '.$newprf, $sql);
		$sql = str_replace('CREATE INDEX "'.$oldprf, 'CREATE INDEX "'.$newprf, $sql);
		$sql = str_replace('CREATE INDEX '.$oldprf, 'CREATE INDEX '.$newprf, $sql);
		$sql = str_replace(' ON "'.$oldprf, ' ON "'.$newprf, $sql);
		$sql = str_replace('INSERT INTO "'.$oldprf, 'INSERT INTO "'.$newprf, $sql);
		$sql = str_replace('INSERT INTO '.$oldprf, 'INSERT INTO '.$newprf, $sql);
		$sql = str_replace(' FROM "'.$oldprf, ' FROM "'.$newprf, $sql);
		if (strpos($sql, 'SELECT setval') !== false) {
			$sql = str_replace($oldprf, $newprf, $sql);
		}
		return $sql;
	}


	/************************/
	/* EXECUTE AN SQL QUERY */
	/************************/
	public function query($sql) {
		if ($this->error) { return false; }
		if (!$this->db2) {
			$this->errormsg = 'Database connection is closed!';
			return false;
		}
		if (trim($sql) == '') {
			$this->errormsg = 'The SQL query is empty!';
			return false;
		}

		if (!$this->db2->exec($sql, '')) {
			$this->errormsg = $this->db2->getErrorMsg();
			if ($this->errormsg == '') {
				$this->errormsg = 'SQL query failed to be executed!';
			}
			return false;
		}

		return true;
	}

}

?>