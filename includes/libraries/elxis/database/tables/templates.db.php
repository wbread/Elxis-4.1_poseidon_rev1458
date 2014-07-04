<?php 
/**
* @version		$Id: templates.db.php 801 2011-12-27 21:21:23Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class templatesDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__templates', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'title' => array('type' => 'string', 'value' => null),
			'template' => array('type' => 'string', 'value' => null),
			'section' => array('type' => 'string', 'value' => 'frontend'),
			'iscore' => array('type' => 'integer', 'value' => 0),
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
		$this->template = trim($this->template);
		if ($this->template == '') {
			$this->errorMsg = 'Template name can not be empty!'; 
			return false;
		}
		$this->iscore = (int)$this->iscore;
		return true;
	}

}

?>