<?php 
/**
* @version		$Id: plugins.db.php 1047 2012-04-18 17:58:37Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class pluginsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__plugins', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'title' => array('type' => 'string', 'value' => null),
			'plugin' => array('type' => 'string', 'value' => null),
			'alevel' => array('type' => 'integer', 'value' => 0),
			'ordering' => array('type' => 'integer', 'value' => 0),
			'published' => array('type' => 'integer', 'value' => 0),
			'iscore' => array('type' => 'bit', 'value' => 0),
			'params' => array('type' => 'text', 'value' => null)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->title = eUTF::trim($this->title);
		if ($this->title == '') {
			$this->errorMsg = 'Title can not be empty!'; 
			return false;
		}
		if (trim($this->plugin) == '') {
			$this->errorMsg = 'Plugin can not be empty!'; 
			return false;
		}
		$this->alevel = (int)$this->alevel;
		if ($this->alevel < 0) { $this->alevel = 0; }
		$this->ordering = (int)$this->ordering;
		if ($this->ordering < 0) { $this->ordering = 0; }
		$this->published = (int)$this->published;
		$this->iscore = (int)$this->iscore;
		return true;
	}

}

?>