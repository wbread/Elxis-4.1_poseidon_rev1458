<?php 
/**
* @version		$Id: acl.db.php 759 2011-11-19 21:05:05Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class aclDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__acl', 'id');

		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'category' => array('type' => 'string', 'value' => null),
			'element' => array('type' => 'string', 'value' => null),
			'identity' => array('type' => 'integer', 'value' => 0),
			'action' => array('type' => 'string', 'value' => null),
			'minlevel' => array('type' => 'integer', 'value' => 0),
			'gid' => array('type' => 'integer', 'value' => 0),
			'uid' => array('type' => 'integer', 'value' => 0),
			'aclvalue' => array('type' => 'integer', 'value' => 1)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->category = trim($this->category);
		if ($this->category == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('CATEGORY'));
			return false;
		}
		$category = strtolower(preg_replace('/[^A-Z\-\_0-9]/i', '', $this->category));
		if ($category != $this->category) {
			$this->errorMsg = 'Invalid name for Category!';
			return false;
		}

		$this->element = trim($this->element);
		if ($this->element == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), 'Element');
			return false;
		}
		$element = strtolower(preg_replace('/[^A-Z\-\_0-9]/i', '', $this->element));
		if ($element != $this->element) {
			$this->errorMsg = 'Invalid name for Element!';
			return false;
		}

		$this->action = trim($this->action);
		if ($this->action == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), 'Action');
			return false;
		}
		$action = strtolower(preg_replace('/[^A-Z\-\_0-9]/i', '', $this->action));
		if ($action != $this->action) {
			$this->errorMsg = 'Invalid name for Action!';
			return false;
		}

		$this->identity = (int)$this->identity;
		if ($this->identity < 0) { $this->identity = 0; }
		$this->minlevel = (int)$this->minlevel;
		if ($this->minlevel < -1) { $this->minlevel = -1; }
		if ($this->minlevel > 100) { $this->minlevel = 100; }
		$this->gid = (int)$this->gid;
		if ($this->gid < 0) { $this->gid = 0; }
		$this->uid = (int)$this->uid;
		if ($this->uid < 0) { $this->uid = 0; }
		$this->aclvalue = (int)$this->aclvalue;
		if ($this->aclvalue < 0) { $this->aclvalue = 0; }

		if ($this->minlevel < 0) {
			if (($this->gid == 0) && ($this->uid == 0)) {
				$this->errorMsg = 'Such a generic rule can not be accepted!';
				return false;
			}
		}

		return true;
	}

}

?>