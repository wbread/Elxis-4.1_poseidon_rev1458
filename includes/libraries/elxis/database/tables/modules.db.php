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

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class modulesDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__modules', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'title' => array('type' => 'string', 'value' => null),
			'content' => array('type' => 'text', 'value' => null),
			'ordering' => array('type' => 'integer', 'value' => 0),
			'position' => array('type' => 'string', 'value' => 'left'),
			'published' => array('type' => 'integer', 'value' => 0),
			'module' => array('type' => 'string', 'value' => null),
			'showtitle' => array('type' => 'integer', 'value' => 2),
			'params' => array('type' => 'text', 'value' => null),
			'iscore' => array('type' => 'integer', 'value' => 0),
			'section' => array('type' => 'string', 'value' => 'frontend')
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
		$this->published = (int)$this->published;
		$this->ordering = (int)$this->ordering;
		$this->showtitle = (int)$this->showtitle;
		$this->iscore = (int)$this->iscore;
		if ($this->section != 'backend') { $this->section = 'frontend'; }
		if (trim($this->position) == '') { $this->position = 'left'; }
		return true;
	}

}

?>