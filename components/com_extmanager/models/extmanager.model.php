<?php 
/**
* @version		$Id: extmanager.model.php 1248 2012-08-05 11:23:59Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extmanagerModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/**************************/
	/* GET TEMPLATE POSITIONS */
	/**************************/
	public function getPositions() {
		$sql = "SELECT ".$this->db->quoteId('position')." FROM ".$this->db->quoteId('#__template_positions')." ORDER BY ".$this->db->quoteId('position')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchCol(0);
	}


	/*****************/
	/* COUNT MODULES */
	/*****************/
	public function countModules($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'id':
					$wheres[] = $this->db->quoteId('id').' = :xid';
					$pdo_binds[':xid'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $this->db->quoteId($options['qtype']).' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		if (isset($options['section']) && ($options['section'] != '')) {
			$wheres[] = $this->db->quoteId('section').' = :xsect';
			$pdo_binds[':xsect'] = array($options['section'], PDO::PARAM_STR);
		}

		if (isset($options['position']) && ($options['position'] != '')) {
			$wheres[] = $this->db->quoteId('position').' = :xpos';
			$pdo_binds[':xpos'] = array($options['position'], PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET MODULES FROM DATABASE */
	/*****************************/
	public function getModules($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'id':
					$wheres[] = $this->db->quoteId('id').' = :xid';
					$pdo_binds[':xid'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $this->db->quoteId($options['qtype']).' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		if (isset($options['section']) && ($options['section'] != '')) {
			$wheres[] = $this->db->quoteId('section').' = :xsect';
			$pdo_binds[':xsect'] = array($options['section'], PDO::PARAM_STR);
		}

		if (isset($options['position']) && ($options['position'] != '')) {
			$wheres[] = $this->db->quoteId('position').' = :xpos';
			$pdo_binds[':xpos'] = array($options['position'], PDO::PARAM_STR);
		}

		if ($options['sortname'] == 'position') {
			$orderby = $this->db->quoteId('position').' '.strtoupper($options['sortorder']).', '.$this->db->quoteId('ordering').' '.strtoupper($options['sortorder']);
		} else {
			$orderby = $this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__modules');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		}

		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE MODULE */
	/***********************************/
	public function publishModule($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Module not found!'; return $response; } //just in case
		if (eFactory::getElxis()->acl()->check('com_extmanager', 'modules', 'edit') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACTION');
			return $response;
		}

		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__modules')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__modules')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*********************/
	/* COPY MODULE'S ACL */
	/*********************/
	public function copyModuleACL($module, $source_id, $newid) {
		$newid = (int)$newid;
		if ($newid < 1) { return false; } //just in case

		$ctg = 'module';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('identity')." = :xident";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $module, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $source_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return true; } //nothing to copy

		$sql = "INSERT INTO ".$this->db->quoteId('#__acl')
		."\n (".$this->db->quoteId('id').", ".$this->db->quoteId('category').", ".$this->db->quoteId('element').", ".$this->db->quoteId('identity').", "
		.$this->db->quoteId('action').", ".$this->db->quoteId('minlevel').", ".$this->db->quoteId('gid').", ".$this->db->quoteId('uid').", ".$this->db->quoteId('aclvalue').")"
		."\n VALUES (NULL, :xcol2, :xcol3, :xcol4, :xcol5, :xcol6, :xcol7, :xcol8, :xcol9)";
		$stmt = $this->db->prepare($sql);
		foreach ($rows as $row) {
			$stmt->bindParam(':xcol2', $row->category, PDO::PARAM_STR);
			$stmt->bindParam(':xcol3', $row->element, PDO::PARAM_STR);
			$stmt->bindParam(':xcol4', $newid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol5', $row->action, PDO::PARAM_STR);
			$stmt->bindParam(':xcol6', $row->minlevel, PDO::PARAM_INT);
			$stmt->bindParam(':xcol7', $row->gid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol8', $row->uid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol9', $row->aclvalue, PDO::PARAM_INT);
			$stmt->execute();
		}
		return true;
	}


	/******************************/
	/* COPY MODULE'S TRANSLATIONS */
	/******************************/
	public function copyModuleTranslations($source_id, $newid) {
		$newid = (int)$newid;
		if ($newid < 1) { return false; } //just in case

		$ctg = 'module';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('elid')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $source_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$rows) { return true; } //nothing to copy

		$sql = "INSERT INTO ".$this->db->quoteId('#__translations')
		."\n (".$this->db->quoteId('trid').", ".$this->db->quoteId('category').", ".$this->db->quoteId('element').", ".$this->db->quoteId('language').", "
		.$this->db->quoteId('elid').", ".$this->db->quoteId('translation').")"
		."\n VALUES (NULL, :xcol2, :xcol3, :xcol4, :xcol5, :xcol6)";
		$stmt = $this->db->prepare($sql);
		foreach ($rows as $row) {
			$stmt->bindParam(':xcol2', $row->category, PDO::PARAM_STR);
			$stmt->bindParam(':xcol3', $row->element, PDO::PARAM_STR);
			$stmt->bindParam(':xcol4', $row->language, PDO::PARAM_STR);
			$stmt->bindParam(':xcol5', $newid, PDO::PARAM_INT);
			$stmt->bindParam(':xcol6', $row->translation, PDO::PARAM_LOB);
			$stmt->execute();
		}
		return true;
	}


	/**************************/
	/* COUNT MODULE INSTANCES */
	/**************************/
	public function countModuleInstances($module) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('module')." = :xmod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $module, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*********************************************/
	/* DELETE MODULE (USED FOR MODULE INSTANCES) */
	/*********************************************/
	public function deleteModule($id, $module) {
		$id = (int)$id;
		if ($id < 1) { return false; }

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('id')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__modules_menu')." WHERE ".$this->db->quoteId('moduleid')." = :xid");
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$ctg = 'module';
		$sql = "DELETE FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('identity')." = :xident";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $module, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $id, PDO::PARAM_INT);
		$stmt->execute();

		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelid', $id, PDO::PARAM_INT);
		$stmt->execute();

		return true;
	}


	/****************************************/
	/* GET MODULES ACL PERMISSIONS FOR VIEW */
	/****************************************/
	public function getModulesViewACL($ids) {
		$ctg = 'module';
		$act = 'view';
		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('identity')." IN (".implode(', ', $ids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xact', $act, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		$final = array();
		if ($rows) {
			foreach ($rows as $row) {
				$id = $row['identity'];
				$num = (isset($final[$id])) ? $final[$id]['num'] : 0;
				$num++;
				$final[$id] = array(
					'minlevel' => $row['minlevel'],
					'gid' => $row['gid'],
					'uid' => $row['uid'],
					'aclvalue' => $row['aclvalue'],
					'num' => $num
				);
			}
		}

		unset($rows);
		return $final;
	}


	/************************************/
	/* GET USER GROUPS AND THEIR LEVELS */
	/************************************/
	public function getGroups($orderby='level', $orderdir='ASC') {
		if ($orderby == '') { $orderby == 'level'; }
		if ($orderdir == '') { $orderdir = 'ASC'; }
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId($orderby)." ".$orderdir;
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*****************************************/
	/* GET ACL ROWS THAT MATCH WHERE CLAUSES */
	/*****************************************/
	public function queryACL($wheres) {
		$ands = array();
		$binds = array();
		if ($wheres) {
			$i = 0;
			foreach ($wheres as $col => $val) {
				switch ($col) {
					case 'id': case 'identity': case 'minlevel': case 'gid': case 'uid': case 'aclvalue':
						$i++;
						$ands[] = $this->db->quoteId($col).' = :xcol'.$i;
						$binds[] = array(':xcol'.$i, intval($val), PDO::PARAM_INT);
					break;
					case 'category': case 'element': case 'action': 
						$i++;
						$ands[] = $this->db->quoteId($col).' = :xcol'.$i;
						$binds[] = array(':xcol'.$i, $val, PDO::PARAM_STR);
					break;
					default: break;
				}
			}
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl');
		if ($ands) { $sql .= "\n WHERE ".implode(' AND ', $ands); }
		$sql .= "\n ORDER BY ".$this->db->quoteId('category')." ASC, ".$this->db->quoteId('element')." ASC, ".$this->db->quoteId('action')." ASC";
		$stmt = $this->db->prepare($sql);
		if ($binds) {
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
		}
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***************************/
	/* GET MODULES BY POSITION */
	/***************************/
	public function getModsByPosition($position) {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('position')." = :xpos"
		."\n ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***************/
	/* COUNT USERS */
	/***************/
	public function countUsers() {
		$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/***************************/
	/* GET USERS FROM DATABASE */
	/***************************/
	public function getUsers() {
		$orderby = (eFactory::getElxis()->getConfig('REALNAME') == 1) ? 'firstname' : 'uname';
		$sql = "SELECT ".$this->db->quoteId('uid').", ".$this->db->quoteId('firstname').", ".$this->db->quoteId('lastname').", ".$this->db->quoteId('uname')
		."\n FROM ".$this->db->quoteId('#__users')." ORDER BY ".$this->db->quoteId($orderby)." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/******************/
	/* GET MENU ITEMS */
	/******************/
	public function getMenuItems($section) {
		$xlin = 'link';
		$xwra = 'wrapper';
		$sql = "SELECT ".$this->db->quoteId('menu_id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('collection')
		."\n FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('section')." = :xsec AND (".$this->db->quoteId('menu_type')." = :xlin OR ".$this->db->quoteId('menu_type')." = :xwra)"
		."\n ORDER BY ".$this->db->quoteId('collection')." ASC, ".$this->db->quoteId('parent_id')." ASC, ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xlin', $xlin, PDO::PARAM_STR);
		$stmt->bindParam(':xwra', $xwra, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*************************/
	/* GET MODULE MENU ITEMS */
	/*************************/
	public function getModMenuItems($moduleid) {
		$xlin = 'link';
		$xwra = 'wrapper';
		$sql = "SELECT ".$this->db->quoteId('menuid')." FROM ".$this->db->quoteId('#__modules_menu')
		."\n WHERE ".$this->db->quoteId('moduleid')." = :xmod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchCol();
	}


	/****************************/
	/* DELETE MODULE MENU ITEMS */
	/****************************/
	public function deleteModMenus($moduleid, $mitems) {
		if (!$mitems) { return; }
		$sql = "DELETE FROM ".$this->db->quoteId('#__modules_menu')
		."\n WHERE ".$this->db->quoteId('moduleid')." = :xmod AND ".$this->db->quoteId('menuid')." = :xmen";
		$stmt = $this->db->prepare($sql);
		foreach ($mitems as $menuid) {
			$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/****************************/
	/* INSERT MODULE MENU ITEMS */
	/****************************/
	public function insertModMenus($moduleid, $mitems) {
		if (!$mitems) { return; }
		$sql = "INSERT INTO ".$this->db->quoteId('#__modules_menu')
		."\n (".$this->db->quoteId('mmid').", ".$this->db->quoteId('moduleid').", ".$this->db->quoteId('menuid').")"
		."\n VALUES (NULL, :xmod, :xmen)";
		$stmt = $this->db->prepare($sql);
		foreach ($mitems as $menuid) {
			$stmt->bindParam(':xmod', $moduleid, PDO::PARAM_INT);
			$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
			$stmt->execute();
		}
	}


	/********************/
	/* COUNT COMPONENTS */
	/********************/
	public function countComponents($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			$wheres[] = $this->db->quoteId($options['qtype']).' LIKE '.$this->db->quote('%'.$options['query'].'%');
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__components');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* GET COMPONENTS FROM DATABASE */
	/********************************/
	public function getComponents($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			$wheres[] = $this->db->quoteId($options['qtype']).' LIKE '.$this->db->quote('%'.$options['query'].'%');
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__components');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$sql .= ' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		}

		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*******************/
	/* COUNT TEMPLATES */
	/*******************/
	public function countTemplates($section='frontend') {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__templates').' WHERE '.$this->db->quoteId('section').' = :sect';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':sect', $section, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*******************************/
	/* GET TEMPLATES FROM DATABASE */
	/*******************************/
	public function getTemplates($options) {
		$section = $options['section'];
		$sql = "SELECT * FROM ".$this->db->quoteId('#__templates').' WHERE '.$this->db->quoteId('section').' = :sect';
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->bindParam(':sect', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*******************/
	/* COUNT POSITIONS */
	/*******************/
	public function countPositions() {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__template_positions');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*************************************************************************/
	/* GET TEMPLATE POSITIONS FROM DATABASE (OPTIONALLY COUNT THEIR MODULES) */
	/*************************************************************************/
	public function getFullPositions($options, $count_modules=false) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__template_positions');
		$sql .= ' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		
		if (!$rows || ($count_modules == false)) { return $rows; }

		$section = 'frontend';
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')
		."\n WHERE ".$this->db->quoteId('section')." = :xsec AND ".$this->db->quoteId('position')." = :xpos";
		$stmt = $this->db->prepare($sql);
		for ($i=0; $i < count($rows); $i++) {
			$position = $rows[$i]->position;
			$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
			$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
			$stmt->execute();
			$rows[$i]->modules = (int)$stmt->fetchResult();
		}

		return $rows;
	}


	/**********************************************/
	/* COUNT ROWS HAVING A SPECIFIC POSITION NAME */
	/**********************************************/
	public function countPositionName($position) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__template_positions');
		$sql .= "\n WHERE ".$this->db->quoteId('position')." = :xpos";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpos', $position, PDO::PARAM_STR);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************************************/
	/* UPDATE MODULES POSITIONS (DUE TO POSITION RENAME) */
	/*****************************************************/
	public function updateModulesPositions($oldname, $newposition) {
		$section = 'frontend';
		$sql = "UPDATE ".$this->db->quoteId('#__modules')." SET ".$this->db->quoteId('position')." = :newpos"
		."\n WHERE ".$this->db->quoteId('position')." = :oldpos AND ".$this->db->quoteId('section')." = :xsec";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':newpos', $newposition, PDO::PARAM_STR);
		$stmt->bindParam(':oldpos', $oldname, PDO::PARAM_STR);
		$stmt->bindParam(':xsec', $section, PDO::PARAM_STR);
		$stmt->execute();
	}


	/*****************/
	/* COUNT ENGINES */
	/*****************/
	public function countEngines() {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__engines');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET ENGINES FROM DATABASE */
	/*****************************/
	public function getEngines($options) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__engines').' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE ENGINE */
	/***********************************/
	public function publishEngine($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Engine not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__engines')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*******************************/
	/* GET ALL ENGINES BY ORDERING */
	/*******************************/
	public function getAllEngines() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__engines')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/***********************/
	/* MAKE ENGINE DEFAULT */
	/***********************/
	public function setDefaultEngine($id) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Engine not found!'; return $response; }

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__engines')
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { $response['message'] = 'Engine not found!'; return $response; }

		if ($row['published'] == 0) { $response['message'] = eFactory::getLang()->get('DEF_ENGINE_PUB'); return $response; }

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('defengine')." = 0 WHERE ".$this->db->quoteId('id')." <> :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();

		$sql = "UPDATE ".$this->db->quoteId('#__engines')." SET ".$this->db->quoteId('defengine')." = 1 WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/**********************/
	/* COUNT AUTH METHODS */
	/**********************/
	public function countAuthMethods() {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__authentication');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**********************************/
	/* GET AUTH METHODS FROM DATABASE */
	/**********************************/
	public function getAuthMethods($options) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__authentication').' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/****************************************/
	/* PUBLISH/UNPUBLISH/TOGGLE AUTH METHOD */
	/****************************************/
	public function publishAuth($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Authentication method not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__authentication')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__authentication')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/**********************************************/
	/* GET ALL AUTHENTICATION METHODS BY ORDERING */
	/**********************************************/
	public function getAllAuths() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__authentication')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/*****************/
	/* COUNT PLUGINS */
	/*****************/
	public function countPlugins() {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__plugins');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*****************************/
	/* GET PLUGINS FROM DATABASE */
	/*****************************/
	public function getPlugins($options) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__plugins').' ORDER BY '.$this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/***********************************/
	/* PUBLISH/UNPUBLISH/TOGGLE PLUGIN */
	/***********************************/
	public function publishPlugin($id, $publish=-1) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($id < 1) { $response['message'] = 'Plugin not found!'; return $response; }
		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__plugins')
			."\n WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$sql = "UPDATE ".$this->db->quoteId('#__plugins')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('id')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			$response['message'] = 'Success';
		} else {
			$response['message'] = $stmt->getErrorMsg();
		}
		return $response;
	}


	/*******************************/
	/* GET ALL PLUGINS BY ORDERING */
	/*******************************/
	public function getAllPlugins() {
		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('ordering')
		."\n FROM ".$this->db->quoteId('#__plugins')." ORDER BY ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_OBJ);
	}


	/****************************/
	/* GET COMPONENT PARAMETERS */
	/****************************/
	public function componentParams() {
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__components')
		."\n WHERE ".$this->db->quoteId('component')." = ".$this->db->quote('com_extmanager');
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (string)$stmt->fetchResult();
	}


	/************************************/
	/* SAVE COMPONENT PARAMETERS STRING */
	/************************************/
	public function saveComponentParams($str) {
		$comp = 'com_extmanager';
		$sql = "UPDATE ".$this->db->quoteId('#__components')." SET  ".$this->db->quoteId('params')." = :xpar"
		."\n WHERE ".$this->db->quoteId('component')." = :xcomp";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xpar', $str, PDO::PARAM_STR);
		$stmt->bindParam(':xcomp', $comp, PDO::PARAM_STR);
		$ok = $stmt->execute();
		return $ok;
	}

}

?>