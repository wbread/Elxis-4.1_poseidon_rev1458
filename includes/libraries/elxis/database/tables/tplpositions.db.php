<?php 
/**
* @version		$Id: tplpositions.db.php 813 2012-01-04 19:18:21Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class tplpositionsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__template_positions', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'position' => array('type' => 'string', 'value' => null),
			'description' => array('type' => 'string', 'value' => null)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->position = trim($this->position);
		$position = preg_replace("/[^a-z0-9]/", '', $this->position);
		if (($this->position == '') || ($position != $this->position)) {
			$this->errorMsg = 'Position name should not be empty and contain alphanumeric lowercase latin characters!'; 
			return false;
		}
		$this->description = strip_tags($this->description);
		return true;
	}

}

?>