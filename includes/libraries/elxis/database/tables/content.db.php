<?php 
/**
* @version		$Id: content.db.php 745 2011-11-14 18:29:15Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class contentDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__content', 'id');

		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'catid' => array('type' => 'integer', 'value' => 0),
			'title' => array('type' => 'string', 'value' => null),
			'seotitle' => array('type' => 'string', 'value' => null),
			'subtitle' => array('type' => 'string', 'value' => null),
			'introtext' => array('type' => 'text', 'value' => null),
			'maintext' => array('type' => 'text', 'value' => null),
			'image' => array('type' => 'string', 'value' => null),
			'caption' => array('type' => 'string', 'value' => null),
			'published' => array('type' => 'bit', 'value' => 0),
			'metakeys' => array('type' => 'string', 'value' => null),
			'created' => array('type' => 'string', 'value' => null),
			'created_by' => array('type' => 'integer', 'value' => 0),
			'created_by_name' => array('type' => 'string', 'value' => null),
			'modified' => array('type' => 'string', 'value' => '1970-01-01 00:00:00'),
			'modified_by' => array('type' => 'integer', 'value' => 0),
			'modified_by_name' => array('type' => 'string', 'value' => null),
			'ordering' => array('type' => 'integer', 'value' => 0),
			'hits' => array('type' => 'integer', 'value' => 0),
			'alevel' => array('type' => 'integer', 'value' => 0),
			'params' => array('type' => 'text', 'value' => null)
		);

		$elxis = eFactory::getElxis();
		$this->setValue('created', eFactory::getDate()->getDate());
		$this->setValue('created_by', $elxis->user()->uid);
		$created_by_name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;
		$this->setValue('created_by_name', $created_by_name);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$elxis = eFactory::getElxis();

		$now = eFactory::getDate()->getDate();
		$this->catid = (int)$this->catid;
		if ($this->catid < 0) { $this->catid = 0; }
		if (trim($this->title) == '') {
			$this->errorMsg = 'Article title can not be empty!';
			return false;
		}
		if (trim($this->seotitle) == '') {
			$this->errorMsg = 'Article seotitle can not be empty!';
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
		$this->published = (int)$this->published;
		if ($this->published !== 1) { $this->published = 0; }

		if (trim($this->created) == '') { $this->created = $now; }
		if ($this->created_by == 0) {
			$this->created_by = $elxis->user()->uid;
		}
		if (trim($this->created_by_name) == '') {
			$this->created_by_name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;
		}

		if (intval($this->id) > 0) {
			$this->modified = $now;
			$this->modified_by = $elxis->user()->uid;
			$this->modified_by_name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;
		} else {
			$this->modified = '1970-01-01 00:00:00';
			$this->modified_by = 0;
			$this->modified_by_name = null;
		}

		$this->ordering = (int)$this->ordering;
		if ($this->ordering < 1) { $this->ordering = 1; }
		$this->hits = (int)$this->hits;
		if ($this->hits < 0) { $this->hits = 0; }
		$this->alevel = (int)$this->alevel;
		if ($this->alevel < 0) { $this->alevel = 0; }
		if ($this->alevel > 100000) { $this->alevel = 100000; }

		return true;
	}

}

?>