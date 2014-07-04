<?php 
/**
* @version		$Id: acl.class.php 1120 2012-05-10 19:47:28Z datahell $
* @package		Elxis
* @subpackage	ACL
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisACL {

	private $gid = 7;
	private $uid = 0;
	private $level = 0;
	private $lowlevel = 0;
	private $exactlevel = 0;
	private $groupname = 'Guest';
	private $acllist = array();
	private $errormsg = '';
	private $apc = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->apc = eFactory::getElxis()->getConfig('APC');
	}


	/**************************/
	/* LOAD CURRENT USER INFO */
	/**************************/
	public function load($uid, $gid) {
		$gid = (int)$gid;
		if ($gid < 1) {
			$this->errormsg = 'Invalid group id '.$gid;
			return false;
		} else if ($gid == 7) {
			$this->gid = $gid;
			$this->uid = 0;
			$this->level = 0;
			$this->lowlevel = 0;
			$this->exactlevel = 7;
			$this->groupname = 'Guest';
		} else if ($gid == 6) {
			$this->gid = $gid;
			$this->uid = 0;
			$this->level = 1;
			$this->lowlevel = 1000;
			$this->exactlevel = 1006;
			$this->groupname = 'External user';
		} else if ($gid == 5) {
			$this->gid = $gid;
			$this->uid = (int)$uid;
			$this->level = 2;
			$this->lowlevel = 2000;
			$this->exactlevel = 2005;
			$this->groupname = 'User';
		} else if ($gid == 1) {
			$this->gid = $gid;
			$this->uid = (int)$uid;
			$this->level = 100;
			$this->lowlevel = 100000;
			$this->exactlevel = 100001;
			$this->groupname = 'Administrator';
		} else {
			$db = eFactory::getDB();
			$sql = "SELECT * FROM ".$db->quoteId('#__groups')." WHERE ".$db->quoteId('gid')." = :groupid";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if (!$row) {
				$this->errormsg = 'Invalid group id '.$gid;
				return false;
			}
			$this->gid = $gid;
			$this->uid = (int)$uid;
			$this->level = (int)$row['level'];
			$this->lowlevel = $this->level * 1000;
			$this->exactlevel = ($this->level * 1000) + $this->gid;
			$this->groupname = (string)$row['groupname'];
		}

		$this->loadAccessLists();
		return true;
	}


	/*********************************************/
	/* GET AN ACL CATEGORY OR THE WHOLE ACL LIST */
	/*********************************************/
	public function getCategory($ctg='') {
		if ($ctg == '') { return $this->acllist; }
		if (!isset($this->acllist[$ctg])) { return array(); }
		return $this->acllist[$ctg];
	}


	/*****************************/
	/* LOAD ACCESS LISTS FROM DB */
	/*****************************/
	private function loadAccessLists() {
		if (($this->apc == 1) && ($this->gid == 7)) {
			$acllist = elxisAPC::fetch('acllist', 'system');
			if ($acllist !== false) {
				$this->acllist = $acllist;
				return;
			}
		}

		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__acl')." ORDER BY ".$db->quoteId('category')." ASC, ".$db->quoteId('element')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return; }

		foreach ($rows as $row) {
			$cat = $row['category'];
			$elem = $row['element'];
			$iden = (int)$row['identity'];
			$action = $row['action'];

			$exists = isset($this->acllist[$cat][$elem][$iden][$action]) ? true : false;
			if (!isset($this->acllist[$cat])) { $this->acllist[$cat] = array(); }
			if (!isset($this->acllist[$cat][$elem])) { $this->acllist[$cat][$elem] = array(); }
			if (!isset($this->acllist[$cat][$elem][$iden])) { $this->acllist[$cat][$elem][$iden] = array(); }
			//if (!isset($this->acllist[$cat][$elem][$iden])) { $this->acllist[$cat][$elem][$iden] = array(); }

			if ($row['uid'] > 0) { //priority = 3
				if ($row['uid'] == $this->uid) {
					$aclvalue = (int)$row['aclvalue'];
					$this->acllist[$cat][$elem][$iden][$action] = array($aclvalue, 3);
					continue;
				} else if (!$exists) {
					$this->acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				} else { //keep the old value
					continue;
				}
			}

			if ($exists) {
				if ($this->acllist[$cat][$elem][$iden][$action][1] > 2) { continue; } //dont overwrite higher priority
			}

			if ($row['gid'] > 0) { //priority = 2
				if ($exists) {
					if ($this->acllist[$cat][$elem][$iden][$action][1] > 2) { continue; } //dont overwrite higher priority
				}
				if ($row['gid'] == $this->gid) {
					$aclvalue = (int)$row['aclvalue'];
					$this->acllist[$cat][$elem][$iden][$action] = array($aclvalue, 2);
					continue;
				} else if (!$exists) {
					$this->acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				} else { //keep the old value
					continue;
				}
			}

			if ($exists) {
				if ($this->acllist[$cat][$elem][$iden][$action][1] > 1) { continue; } //dont overwrite higher priority
			}

			//priority 1
			if (($row['minlevel'] > -1) && ($row['minlevel'] <= $this->level)) {
				$aclvalue = (int)$row['aclvalue'];
				if ($exists) {
					$v = ($this->acllist[$cat][$elem][$iden][$action][0] > $aclvalue) ? $this->acllist[$cat][$elem][$iden][$action][0] : $aclvalue;
					$this->acllist[$cat][$elem][$iden][$action] = array($v, 1);
					continue;
				} else {
					$this->acllist[$cat][$elem][$iden][$action] = array($aclvalue, 1);
					continue;
				}
			} else {
				if ($exists) { //keep the old value
					continue;
				} else {
					$this->acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				}
			}
		}

		if (($this->apc == 1) && ($this->gid == 7)) {
			elxisAPC::store('acllist', 'system', $this->acllist, 1800);
		}
	}


	/************************/
	/* GET ACL ACCESS VALUE */
	/************************/
	public function check($category, $element, $action, $identity=0) {
		if (!isset($this->acllist[$category][$element][$identity][$action])) { return -1; }
		return $this->acllist[$category][$element][$identity][$action][0];
	}


	/*********************************************************************/
	/* GET ACCESS LISTS FROM DB FOR A SPECIFIC LEVEL-GID-UID COMBINATION */
	/*********************************************************************/
	public function getSpecificLists($level, $gid, $uid=0) {
		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__acl')." ORDER BY ".$db->quoteId('category')." ASC, ".$db->quoteId('element')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }

		$acllist = array();
		foreach ($rows as $row) {
			$cat = $row['category'];
			$elem = $row['element'];
			$iden = (int)$row['identity'];
			$action = $row['action'];

			$exists = isset($acllist[$cat][$elem][$iden][$action]) ? true : false;
			if (!isset($acllist[$cat])) { $acllist[$cat] = array(); }
			if (!isset($acllist[$cat][$elem])) { $acllist[$cat][$elem] = array(); }
			if (!isset($acllist[$cat][$elem][$iden])) { $acllist[$cat][$elem][$iden] = array(); }

			if ($row['uid'] > 0) { //priority = 3
				if ($row['uid'] == $uid) {
					$aclvalue = (int)$row['aclvalue'];
					$acllist[$cat][$elem][$iden][$action] = array($aclvalue, 3);
					continue;
				} else if (!$exists) {
					$acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				} else { //keep the old value
					continue;
				}
			}

			if ($exists) {
				if ($acllist[$cat][$elem][$iden][$action][1] > 2) { continue; } //dont overwrite higher priority
			}

			if ($row['gid'] > 0) { //priority = 2
				if ($exists) {
					if ($acllist[$cat][$elem][$iden][$action][1] > 2) { continue; } //dont overwrite higher priority
				}
				if ($row['gid'] == $gid) {
					$aclvalue = (int)$row['aclvalue'];
					$acllist[$cat][$elem][$iden][$action] = array($aclvalue, 2);
					continue;
				} else if (!$exists) {
					$acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				} else { //keep the old value
					continue;
				}
			}

			if ($exists) {
				if ($acllist[$cat][$elem][$iden][$action][1] > 1) { continue; } //dont overwrite higher priority
			}

			//priority 1
			if (($row['minlevel'] > -1) && ($row['minlevel'] <= $level)) {
				$aclvalue = (int)$row['aclvalue'];
				if ($exists) {
					$v = ($acllist[$cat][$elem][$iden][$action][0] > $aclvalue) ? $acllist[$cat][$elem][$iden][$action][0] : $aclvalue;
					$acllist[$cat][$elem][$iden][$action] = array($v, 1);
					continue;
				} else {
					$acllist[$cat][$elem][$iden][$action] = array($aclvalue, 1);
					continue;
				}
			} else {
				if ($exists) { //keep the old value
					continue;
				} else {
					$acllist[$cat][$elem][$iden][$action] = array(0, 0);
					continue;
				}
			}
		}

		return $acllist;
	}


	/**************************/
	/* GET THE WHOLE ACL LIST */
	/**************************/
	public function getList() {
		return $this->acllist;
	}


	/***************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getError() {
		return $this->errormsg;
	}


	public function getLevel() {
		return $this->level;
	}


	public function getLowLevel() {
		return $this->lowlevel;
	}


	public function getExactLevel() {
		return $this->exactlevel;
	}


	public function getInfo($translated=false) {
		$info = array(
			'gid' => $this->gid,
			'uid' => $this->uid,
			'level' => $this->level,
			'lowlevel' => $this->lowlevel,
			'exactlevel' => $this->exactlevel,
			'groupname' => $this->getGroupname($translated)
		);
		return $info;
	}


	public function getGroupname($translated=false) {
		if (!$translated) { return $this->groupname; }
		switch ($this->gid) {
			case 0: case 7: return eFactory::getLang()->get('GUEST'); break;
			case 1: return eFactory::getLang()->get('ADMINISTRATOR'); break;
			case 2: return eFactory::getLang()->get('USER'); break;
			case 6: return eFactory::getLang()->get('EXTERNALUSER'); break;
			default: return $this->groupname; break;
		}
	}
	
}

?>