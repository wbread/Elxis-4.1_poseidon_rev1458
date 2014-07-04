<?php 
/**
* @version		$Id: categories.db.php 745 2011-11-14 18:29:15Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class categoriesDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__categories', 'catid');

		$this->columns = array(
			'catid' => array('type' => 'integer', 'value' => null),
			'parent_id' => array('type' => 'integer', 'value' => 0),
			'title' => array('type' => 'string', 'value' => null),
			'seotitle' => array('type' => 'string', 'value' => null),
			'seolink' => array('type' => 'string', 'value' => null),
			'published' => array('type' => 'bit', 'value' => 0),
			'ordering' => array('type' => 'integer', 'value' => 0),
			'image' => array('type' => 'string', 'value' => null),
			'description' => array('type' => 'text', 'value' => null),
			'params' => array('type' => 'text', 'value' => null),
			'alevel' => array('type' => 'integer', 'value' => 0)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->published = (int)$this->published;
		if ($this->published !== 1) { $this->published = 0; }
		$this->parent_id = (int)$this->parent_id;
		if ($this->parent_id < 0) { $this->parent_id = 0; }
		$this->ordering = (int)$this->ordering;
		if ($this->ordering < 1) { $this->ordering = 1; }
		$this->alevel = (int)$this->alevel;
		if ($this->alevel < 0) { $this->alevel = 0; }
		if ($this->alevel > 100000) { $this->alevel = 100000; }

		if (trim($this->title) == '') {
			$this->errorMsg = 'Category title can not be empty!';
			return false;
		}
		if (trim($this->seotitle) == '') {
			$this->errorMsg = 'Category seotitle can not be empty!';
			return false;
		}
        $ascii = preg_replace("/[^a-z0-9\-\_]/", '', $this->seotitle);
        if ($ascii != $this->seotitle) {
			$this->errorMsg = 'The SEO Title is invalid!';
			return false;
       	}
        if (strlen($this->seotitle) < 3) {
			$this->errorMsg = 'The SEO Title is too short!';
			return false;
       	}
		if ($this->parent_id == 0) {
			$this->seolink = $this->seotitle.'/';
		}
		if (trim($this->seolink) == '') {
			$this->errorMsg = 'Category seolink can not be empty!';
			return false;
		}

		return true;
	}
}

?>