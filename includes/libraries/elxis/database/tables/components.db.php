<?php 
/**
* @version		$Id: components.db.php 796 2011-12-26 17:41:04Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class componentsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__components', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'name' => array('type' => 'string', 'value' => null),
			'component' => array('type' => 'string', 'value' => null),
			'route' => array('type' => 'string', 'value' => null),
			'iscore' => array('type' => 'integer', 'value' => 0),
			'params' => array('type' => 'text', 'value' => null)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->name = eUTF::trim($this->name);
		if ($this->name == '') {
			$this->errorMsg = 'Name can not be empty!'; 
			return false;
		}
		$this->component = trim($this->component);
		if (($this->component == '') || !preg_match('/^(com\_)/', $this->component)) {
			$this->errorMsg = 'An Elxis component should start with com_!'; 
			return false;
		}
		$this->iscore = (int)$this->iscore;
		return true;
	}

}

?>