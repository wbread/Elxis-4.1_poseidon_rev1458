<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class cpanelModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*******************************************/
	/* DELETE A SESSION DB ENTRY BY IP ADDRESS */
	/*******************************************/
	public function removeSessionIP($ip) {
		$sql = "DELETE FROM #__session WHERE ip_address = :banip";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':banip', $ip, PDO::PARAM_STR);
		$stmt->execute();
	}


	/***********************************************/
	/* DELETE A SESSION DB ENTRY FOR AN ELXIS USER */
	/***********************************************/
	public function removeSessionUser($uid) {
		$lmethod = 'elxis';
		$sql = "DELETE FROM #__session WHERE uid = :userid AND login_method = :lmethod";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->execute();
	}


	/**************************************************/
	/* DELETE A SESSION DB ENTRY FOR AN EXTERNAL USER */
	/**************************************************/
	public function removeSessionXUser($lmethod, $ip, $fact) {
		$uid = 0;
		$gid = 6;		
		$sql = "SELECT COUNT(".$this->db->quoteId('uid').") FROM #__session"
		."\n WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num < 1) { return false; }
		if ($num == 1) {
			$sql = "DELETE FROM #__session WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
			$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
			$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
			$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
			$stmt->execute();
			return true;
		}

		$sql = "DELETE FROM #__session WHERE uid = :userid AND gid = :groupid AND login_method = :lmethod AND ip_address = :ipaddr AND first_activity = :fact";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':userid', $uid, PDO::PARAM_INT);
		$stmt->bindParam(':groupid', $gid, PDO::PARAM_INT);
		$stmt->bindParam(':lmethod', $lmethod, PDO::PARAM_STR);
		$stmt->bindParam(':ipaddr', $ip, PDO::PARAM_STR);
		$stmt->bindParam(':fact', $fact, PDO::PARAM_INT);
		$stmt->execute();
		return true;
	}


	/*********************************************/
	/* GET INSTALLED COMPONENTS AND THEIR ROUTES */
	/*********************************************/
	public function getComponents($with_routes=true) {
		$stmt = $this->db->prepare("SELECT component, route FROM #__components ORDER BY id ASC");
		$stmt->execute();
		if (!$with_routes) {
			$rows = $stmt->fetchCol(0);
		} else {
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $rows;
	}


	/************************/
	/* GET AN ELEMENT ROUTE */
	/************************/
	public function getRoute($type, $base) {
		$result = '';
		switch($type) {
			case 'component':
				$stmt = $this->db->prepare("SELECT route FROM #__components WHERE component = :cmp");
				$stmt->bindParam(':cmp', $base, PDO::PARAM_STR);
				$stmt->execute();
				$result = trim($stmt->fetchResult());
			break;
			case 'dir':
			case 'page':
				$repo_path = eFactory::getElxis()->getConfig('REPO_PATH');
				if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
				if (file_exists($repo_path.'/other/routes.php')) {
					include($repo_path.'/other/routes.php');
					if ($type == 'dir') {
						if (isset($routes) && is_array($routes) && (count($routes) > 0)) {
							if (isset($routes[$base])) { $result = $routes[$base]; }
						}
					} else {
						if (isset($page_routes) && is_array($page_routes) && (count($page_routes) > 0)) {
							if (isset($page_routes[$base])) { $result = $page_routes[$base]; }
						}
					}
				}
			break;
			case 'frontpage':
				$result = eFactory::getElxis()->getConfig('DEFAULT_ROUTE');
			break;
			default: break;
		}

		return $result;
	}


	/*************************/
	/* SET COMPONENT'S ROUTE */
	/*************************/
	public function setComponentRoute($rbase, $rroute) {
		if ($rroute != '') { //2 components can not have the same route
			$stmt = $this->db->prepare("SELECT COUNT(component) FROM #__components WHERE route = :rt AND component != :cmp");
			$stmt->bindParam(':rt', $rroute, PDO::PARAM_STR);
			$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
			$stmt->execute();
			$n = (int)$stmt->fetchResult();
			if ($n > 0) { return false; }
		}

		$stmt = $this->db->prepare("SELECT component, route FROM #__components WHERE component = :cmp");
		$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		if (!$row) { return false; }

		if (trim($row['route']) == $rroute) { return true; }

		$stmt = $this->db->prepare("UPDATE #__components SET route = :rt WHERE component = :cmp");
		$stmt->bindParam(':rt', $rroute, PDO::PARAM_STR);
		$stmt->bindParam(':cmp', $rbase, PDO::PARAM_STR);
		$stmt->execute();
		return true;
	}


	/*********************************************/
	/* GET INSTALLED TEMPLATES AND THEIR SECTION */
	/*********************************************/
	public function getTemplates() {
		$sql = "SELECT ".$this->db->quoteId('title').", ".$this->db->quoteId('template').", ".$this->db->quoteId('section')." FROM ".$this->db->quoteId('#__templates');
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/************************************/
	/* GET STATISTICS FROM THE DATABASE */
	/************************************/
	public function getStatistics($year, $month=0) {
		$dt = ($month > 0) ? $year.'-'.sprintf("%02d", $month).'%' : $year.'%';
		$sql = "SELECT ".$this->db->quoteId('statdate').", ".$this->db->quoteId('clicks').", ".$this->db->quoteId('visits').", ".$this->db->quoteId('langs')
		."\n FROM ".$this->db->quoteId('#__statistics')
		."\n WHERE ".$this->db->quoteId('statdate')." LIKE :sdt ORDER BY ".$this->db->quoteId('statdate')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':sdt', $dt, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $rows;		
	}
		
}

?>