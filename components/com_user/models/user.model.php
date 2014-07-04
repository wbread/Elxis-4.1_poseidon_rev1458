<?php 
/**
* @version		$Id: user.model.php 1131 2012-05-15 18:50:58Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class userModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*******************************/
	/* GET ALL AVAILABLE LANGUAGES */
	/*******************************/
	public function getLanguages() {
		$ilangs = eFactory::getFiles()->listFolders('language');
		$langs = eFactory::getLang()->getallinfo($ilangs);
		return $langs;
	}


	/***************/
	/* COUNT USERS */
	/***************/
	public function countUsers($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'uid':
					$wheres[] = 'uid = :xuid';
					$pdo_binds[':xuid'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $options['qtype'].' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		if (isset($options['gender']) && ($options['gender'] != '')) {
			$wheres[] = 'gender = :xgender';
			$pdo_binds[':xgender'] = array($options['gender'], PDO::PARAM_STR);
		}
		if (isset($options['preflang']) && ($options['preflang'] != '')) {
			$wheres[] = 'preflang = :xpreflang';
			$pdo_binds[':xpreflang'] = array($options['preflang'], PDO::PARAM_STR);
		}

		$sql = "SELECT COUNT(uid) FROM #__users";
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


	/**************************************/
	/* GET ALL USER DETAILS FROM DATABASE */
	/**************************************/
	public function getUser($uid=0, $block=-1) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__users').' WHERE uid = :xuid';
		if ($block > -1) { $sql .= ' AND block = :xblock'; }
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		if ($block > -1) { $stmt->bindParam(':xblock', $block, PDO::PARAM_INT); }
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/*******************************/
	/* GET USER GROUP ACCESS LEVEL */
	/*******************************/
	public function getGroupLevel($gid) {
		$sql = "SELECT ".$this->db->quoteId('level')." FROM ".$this->db->quoteId('#__groups').' WHERE gid = :xgid';
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/***************************/
	/* GET USERS FROM DATABASE */
	/***************************/
	public function getUsers($options, $countarticles=true) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'uid':
					$wheres[] = 'uid = :xuid';
					$pdo_binds[':xuid'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $options['qtype'].' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		if (isset($options['gender']) && ($options['gender'] != '')) {
			$wheres[] = 'gender = :xgender';
			$pdo_binds[':xgender'] = array($options['gender'], PDO::PARAM_STR);
		}
		if (isset($options['preflang']) && ($options['preflang'] != '')) {
			$wheres[] = 'preflang = :xpreflang';
			$pdo_binds[':xpreflang'] = array($options['preflang'], PDO::PARAM_STR);
		}

		$sql = "SELECT uid, firstname, lastname, uname, block, gid, groupname, email, registerdate, lastvisitdate FROM #__users";
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$options['sortname'].' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$sql .= ' ORDER BY '.$options['sortname'].' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rows && $countarticles) {
			$sql = "SELECT COUNT(id) FROM #__content WHERE created_by = :cuid";
			$stmt = $this->db->prepare($sql);
			foreach ($rows as $k => $row) {
				$stmt->bindParam(':cuid', $row['uid'], PDO::PARAM_INT);
				$stmt->execute();
				$rows[$k]['articles'] = (int)$stmt->fetchResult();
			}
		}
		return $rows;
	}


	/**********************/
	/* BLOCK/UNBLOCK USER */
	/**********************/
	public function blockUser($uid, $block=1) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = (int)$uid;
		if ($uid < 0) { $uid = 0; }
		$block = (int)$block;
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$response['message'] = 'The block of user accounts is not allowed under the current security level!';
			return $response;
		}
		$allowed = $elxis->acl()->check('com_user', 'profile', 'block');
		if (($uid == 0) || ($allowed != 1)) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			return $response;
		}
		if ($elxis->user()->uid == $uid) {
			$response['message'] = $eLang->get('CNOT_ACTION_SELF');
			return $response;
		}

		$sql = "SELECT u.uid, u.gid, u.uname, u.block, g.level FROM ".$this->db->quoteId('#__users')." u"
		."\n INNER JOIN ".$this->db->quoteId('#__groups')." g ON g.gid=u.gid WHERE u.uid = :uid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			$response['message'] = $eLang->get('USERNFOUND');
			return $response;
		}

		if ((intval($row['gid']) == 1) || ($row['level'] >= $elxis->acl()->getLevel())) {
			$response['message'] = $eLang->get('CNOT_ACTION_USER');
			return $response;
		}
		
		if ($block == -1) { $block = (intval($row['block']) == 0) ? 1 : 0; }

		$stmt = $this->db->prepare("UPDATE ".$this->db->quoteId('#__users')." SET ".$this->db->quoteId('block')." = :xblock WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xblock', $block, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if ($ok) {
			$response['success'] = true;
			if ($block == 1) {
				$response['message'] = sprintf($eLang->get('USERACCBLOCKED'), $row['uname']);
			} else {
				$response['message'] = sprintf($eLang->get('USERACCUNBLOCKED'), $row['uname']);
			}
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__session')." WHERE ".$this->db->quoteId('uid')." = :uid");
			$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		} else {
			$response['message'] = $eLang->get('ACTION_FAILED');
		}

		return $response;
	}


	/***************/
	/* DELETE USER */
	/***************/
	public function deleteUser($uid, $usercontent='unpublish') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = (int)$uid;
		if ($uid < 0) { $uid = 0; }

		$response = array('success' => false, 'message' => 'Unknown error');
		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$response['message'] = 'The deletion of user accounts is not allowed under the current security level!';
			return $response;
		}
		$proceed = false;
		$allowed = $elxis->acl()->check('com_user', 'profile', 'delete');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $uid))) { $proceed = true; }
		if (($uid == 0) || ($proceed === false)) {
			$response['message'] = $eLang->get('NOTALLOWACCPAGE');
			return $response;
		}

		$sql = "SELECT u.uid, u.gid, u.uname, g.level FROM ".$this->db->quoteId('#__users')." u"
		."\n INNER JOIN ".$this->db->quoteId('#__groups')." g ON g.gid=u.gid WHERE u.uid = :uid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) {
			$response['message'] = $eLang->get('USERNFOUND');
			return $response;
		}

		if ((intval($row['gid']) == 1) || ($row['level'] >= $elxis->acl()->getLevel())) {
			$response['message'] = $eLang->get('CNOT_ACTION_USER');
			return $response;
		}

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$ok = $stmt->execute();
		if (!$ok) {
			$response['message'] = $eLang->get('ACTION_FAILED');
			return $response;
		}
		
		$response['success'] = true;
		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__session')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__comments')." WHERE ".$this->db->quoteId('uid')." = :xuid");
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();

		if ($usercontent == 'delete') {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('created_by')." = :xuid");
			$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		} else if ($usercontent == 'unpublish') {
			$pub = 0;
			$stmt = $this->db->prepare("UPDATE ".$this->db->quoteId('#__content')." SET ".$this->db->quoteId('published')." = :xpub WHERE ".$this->db->quoteId('created_by')." = :xuid");
			$stmt->bindParam(':xpub', $pub, PDO::PARAM_INT);
			$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['message'] = sprintf($eLang->get('USERACCDELETED'), $row['uname']);
		return $response;
	}


	/*******************************************/
	/* COUNT USER'S TOTAL ARTICLES OR COMMENTS */
	/*******************************************/
	public function counter($uid, $cmp='content', $only_published=false) {
		if ($cmp == 'comments') {
			$sql = 'SELECT COUNT(id) FROM #__comments WHERE '.$this->db->quoteId('uid').' = :xuid';
		} else {
			$sql = 'SELECT COUNT(id) FROM #__content WHERE '.$this->db->quoteId('created_by').' = :xuid';
		}
		if ($only_published) { $sql .= ' AND published = 1'; }
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/********************************/
	/* INCREMENT USER PROFILE VIEWS */
	/********************************/
	public function incrementProfileViews($uid, $views) {
		$sql = 'UPDATE #__users SET '.$this->db->quoteId('profile_views').' = :xviews WHERE uid = :xuid';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xviews', $views, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->execute();
	}


	/****************/
	/* COUNT GROUPS */
	/****************/
	public function countGroups() {
		$sql = "SELECT COUNT(gid) FROM ".$this->db->quoteId('#__groups');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/****************************/
	/* GET GROUPS FROM DATABASE */
	/****************************/
	public function getGroups($options, $with_members=true) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId($options['sortname'])." ".strtoupper($options['sortorder']);
		$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($rows && $with_members) {
			$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('gid')." = :xgid";
			$stmt = $this->db->prepare($sql);
			foreach ($rows as $k => $row) {
				if ($row['gid'] == 7) {
					$rows[$k]['members'] = 0;
				} else if ($row['gid'] == 6) {
					$rows[$k]['members'] = 0;
				} else {
					$stmt->bindParam(':xgid', $row['gid'], PDO::PARAM_INT);
					$stmt->execute();
					$rows[$k]['members'] = (int)$stmt->fetchResult();
				}
			}
		}

		return $rows;
	}


	/***************************/
	/* GET GROUP FROM DATABASE */
	/***************************/
	public function getGroup($gid) {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." WHERE ".$this->db->quoteId('gid')." = :xgid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { return false; }

		$sql = "SELECT COUNT(uid) FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('gid')." = :xgid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xgid', $row['gid'], PDO::PARAM_INT);
		$stmt->execute();
		$row['members'] = (int)$stmt->fetchResult();

		return $row;
	}


	/*********************/
	/* DELETE USER GROUP */
	/*********************/
	public function deleteGroup($gid) {
		$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__groups')." WHERE ".$this->db->quoteId('gid')." = :xgid");
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		return $stmt->execute();
	}


	/*************/
	/* COUNT ACL */
	/*************/
	public function countACL($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'minlevel': case 'gid': case 'uid':
					$wheres[] = $options['qtype'].' = :xquer';
					$pdo_binds[':xquer'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $options['qtype'].' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) { $stmt->bindParam($key, $parr[0], $parr[1]); }
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/*************************/
	/* GET ACL FROM DATABASE */
	/*************************/
	public function getACL($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['qtype']) && isset($options['query']) && ($options['query'] != '')) {
			switch ($options['qtype']) {
				case 'minlevel': case 'gid': case 'uid':
					$wheres[] = $options['qtype'].' = :xquer';
					$pdo_binds[':xquer'] = array(intval($options['query']), PDO::PARAM_INT);
				break;
				default: $wheres[] = $options['qtype'].' LIKE '.$this->db->quote('%'.$options['query'].'%'); break;
			}
		}

		switch ($options['sortname']) {
			case 'category':
				$orderby = $this->db->quoteId('category').' '.strtoupper($options['sortorder']).', '.$this->db->quoteId('element').' '.strtoupper($options['sortorder']);
			break;
			default:
				$orderby = $this->db->quoteId($options['sortname']).' '.strtoupper($options['sortorder']);
			break;
		}

		$sql = "SELECT * FROM ".$this->db->quoteId('#__acl');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) { $stmt->bindParam($key, $parr[0], $parr[1]); }
			}
		} else {
			$sql .= ' ORDER BY '.$orderby;
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/*************************/
	/* DELETE ACL ELEMENT(S) */
	/*************************/
	public function deleteACL($ids) {
		if (is_array($ids)) {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('id')." IN (".implode(', ', $ids).")");
			return $stmt->execute();
		} else if (is_int($ids)) {
			$stmt = $this->db->prepare("DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('id')." = :xid");
			$stmt->bindParam(':xid', $ids, PDO::PARAM_INT);
			return $stmt->execute();
		} else {
			return false;
		}
	}


	/*****************************************/
	/* CHECK IF THERE IS ALREADY AN ACL RULE */
	/*****************************************/
	public function countMatchRules($id, $category, $element, $identity, $action, $minlevel, $gid, $uid) {
		$id = (int)$id;
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xel"
		."\n AND ".$this->db->quoteId('identity')." = :xident AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('minlevel')." = :xlevel AND ".$this->db->quoteId('gid')." = :xgid AND ".$this->db->quoteId('uid')." = :xuid";
		if ($id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." <> :xid";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $category, PDO::PARAM_STR);
		$stmt->bindParam(':xel', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $identity, PDO::PARAM_INT);
		$stmt->bindParam(':xact', $action, PDO::PARAM_STR);
		$stmt->bindParam(':xlevel', $minlevel, PDO::PARAM_INT);
		$stmt->bindParam(':xgid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		if ($id > 0) {
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$n = (int)$stmt->fetchResult();
		return $n;
	}


	/******************************************************/
	/* CHECK IF THERE IS ALREADY A MINIMUM LEVEL ACL RULE */
	/******************************************************/
	public function countLevelRules($id, $category, $element, $identity, $action) {
		$id = (int)$id;
		$minlevel = -1;
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__acl')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xel"
		."\n AND ".$this->db->quoteId('identity')." = :xident AND ".$this->db->quoteId('action')." = :xact"
		."\n AND ".$this->db->quoteId('minlevel')." > :xlevel";
		if ($id > 0) {
			$sql .= " AND ".$this->db->quoteId('id')." <> :xid";
		}
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $category, PDO::PARAM_STR);
		$stmt->bindParam(':xel', $element, PDO::PARAM_STR);
		$stmt->bindParam(':xident', $identity, PDO::PARAM_INT);
		$stmt->bindParam(':xact', $action, PDO::PARAM_STR);
		$stmt->bindParam(':xlevel', $minlevel, PDO::PARAM_INT);
		if ($id > 0) {
			$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		}
		$stmt->execute();
		$n = (int)$stmt->fetchResult();
		return $n;
	}


	/*************************/
	/* FETCH USER'S COMMENTS */
	/*************************/
	public function fetchUserComments($uid, $num) {
		$elxis = eFactory::getElxis();

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		$element = 'com_content';
		$sql = "SELECT a.message, a.created, c.id, c.catid, c.title, c.seotitle, g.seolink, g.published FROM ".$this->db->quoteId('#__comments')." a"
		."\n LEFT JOIN ".$this->db->quoteId('#__content')." c ON c.id=a.elid"
		."\n LEFT JOIN ".$this->db->quoteId('#__categories')." g ON g.catid=c.catid"
		."\n WHERE a.uid = :xuid AND a.element = :xelem AND a.published=1 AND c.published=1"
		."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel))"
		."\n ORDER BY a.created DESC";
		$stmt = $this->db->prepareLimit($sql, 0, $num);
		$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':xelem', $element, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return array(); }

		$comments = array();
		$elids = array();
		foreach ($rows as $row) {
			$catid = (int)$row['catid'];
			if (($catid > 0) && (intval($row['published']) == 0)) { continue; }
			$elids[] = $row['id'];

			$comment = new stdClass;
			$comment->id = $row['id'];
			$comment->title = $row['title'];
			$comment->catid = $catid;
			$comment->link = (($catid > 0) && (trim($row['seolink']) != '')) ? $row['seolink'].$row['seotitle'].'.html' : $row['seotitle'].'.html';
			$comment->created = $row['created'];
			$comment->message = $row['message'];
			$comments[] = $comment;
		}

		if (!$comments) { return array(); }
		if ($elxis->getConfig('MULTILINGUISM') == 0) { return $comments; }
		if (!$elids) { return $comments; }
		$lng = eFactory::getURI()->getUriLang();
		if ($lng == '') { return $comments; }

		$elids = array_unique($elids);
		$sql = "SELECT ".$this->db->quoteId('elid').", ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')."=".$this->db->quote('com_content')." AND ".$this->db->quoteId('element')."=".$this->db->quote('title')
		."\n AND ".$this->db->quoteId('language')." = :lng AND ".$this->db->quoteId('elid')." IN (".implode(", ", $elids).")";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		$trans = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$trans) { return $comments; }

		foreach ($trans as $tran) {
			$elid = $tran['elid'];
			$title = $tran['translation'];
			foreach ($comments as $i => $comment) {
				if ($comment->id == $elid) { $comments[$i]->title = $title; }
			}
		}

		return $comments;
	}

}

?>