<?php 
/**
* @version		$Id: importer.class.php 1230 2012-07-06 17:23:49Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


abstract class elxisDbImporter {

	protected $params = array();
	protected $error = false;
	protected $errormsg = '';
	protected $queries = 0;


	/*********************/
	/* FINAL CONSTRUCTOR */
	/*********************/
	public final function __construct($params=array()) {
		$this->params = $params;
		if ($this->validate() === true) {
			$this->connect();
		}
	}


	/********************/
	/* FINAL DESTRUCTOR */
	/********************/
	public final function __destruct() {
		$this->disconnect();
	}


	/********************/
	/* ABSTRACT METHODS */
	/********************/
	abstract protected function connect();
	abstract public function disconnect();
	abstract protected function validate();
	abstract public function import();
	abstract public function query($sql);


	/*******************/
	/* SET A PARAMETER */
	/*******************/
	public function setParam($key, $val) {
		$this->params[$key] = $val;
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/************************/
	/* GET QUERIES EXECUTED */
	/************************/
	public function getQueries() {
		return $this->queries;
	}

}

?>