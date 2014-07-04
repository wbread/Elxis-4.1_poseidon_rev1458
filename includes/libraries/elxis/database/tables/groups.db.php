<?php 
/**
* @version		$Id: groups.db.php 757 2011-11-18 19:27:34Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class groupsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__groups', 'gid');

		$this->columns = array(
			'gid' => array('type' => 'integer', 'value' => null),
			'level' => array('type' => 'integer', 'value' => 2),
			'groupname' => array('type' => 'string', 'value' => null)
		);

	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->level = (int)$this->level;
		if (($this->level < 0) || ($this->level > 100)) {
			$this->errorMsg = 'Level should have a value from 0 to 100';
			return false;
		}

		$this->groupname = eUTF::trim($this->groupname);
		if ($this->groupname == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('GROUP'));
			return false;
		}

		$gid = (int)$this->gid;
		if (($this->level == 100) && ($gid <> 1)) {
			$this->errorMsg = 'Only the Administrator group can have level 100';
			return false;
		}

		if ($gid === 1) { $this->level = 100; }
		if ($gid === 7) { $this->level = 0; }
		if ($gid === 6) { $this->level = 1; }
		if ($gid === 5) { $this->level = 2; }

		return true;
	}

}

?>