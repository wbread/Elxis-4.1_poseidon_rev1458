<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers/Simple stats
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisStatsHelper {

	private $statdate = '0000-00-00';
	private $hash = '';
	private $newdate = false;
	private $lang = 'en';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$dt = eFactory::getDate()->getDate();
		$this->statdate = substr($dt, 0, 10);
		$this->lang = eFactory::getLang()->currentLang();
	}


	/*****************/
	/* TRACK VISITOR */
	/*****************/
	public function track() {
		if (defined('ELXIS_ADMIN')) { return false; }
		if ('ELXIS_INNER' == 1) { return false; }
		if (defined('ELXIS_STATS_TRACKED')) { return false; }
		$this->hash = $this->makeUniqueHash();
		if ($this->hash == '') { return false; }
		$row = $this->getDateDate();
		if ($this->newdate == true) {
			$this->emptyTracker();
			$this->trackVisit();
			$lngarr = array();
			$lngarr[ $this->lang ] = 1;
			$row->clicks++;
			$row->visits++;
			$row->langs = serialize($lngarr);
			$tracked = $this->insertStats($row);
		} else {
			$row->clicks++;
			$row->langs = $this->trackCompressLangs($row->langs);
			if ($this->firstVisit() == true) {
				$this->trackVisit();
				$row->visits++;
			}

			$tracked = $this->updateStats($row);
		}

		define('ELXIS_STATS_TRACKED', 1);

		return $tracked;
	}


	/********************/
	/* INSERT A NEW ROW */
	/********************/
	private function insertStats($row) {
		$db = eFactory::getDB();

        $sql = "INSERT INTO ".$db->quoteId('#__statistics')
		."\n (".$db->quoteId('id').", ".$db->quoteId('statdate').", ".$db->quoteId('clicks').", ".$db->quoteId('visits').", ".$db->quoteId('langs').")"
		."\n VALUES (NULL, :xdat, :xcli, :xvis, :xlan)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':xdat', $this->statdate, PDO::PARAM_STR);
        $stmt->bindParam(':xcli', $row->clicks, PDO::PARAM_INT);
        $stmt->bindParam(':xvis', $row->visits, PDO::PARAM_INT);
        $stmt->bindParam(':xlan', $row->langs, PDO::PARAM_STR);
        try {
        	$stmt->execute();
		} catch (PDOException $e) {
			return false;
		}
		return true;
	}


	/**************************/
	/* UPDATE AN EXISTING ROW */
	/**************************/
	private function updateStats($row) {
		$db = eFactory::getDB();

        $sql = "UPDATE ".$db->quoteId('#__statistics')
		."\n SET ".$db->quoteId('clicks')." = :xcli, ".$db->quoteId('visits')." = :xvis, ".$db->quoteId('langs')." = :xlan"
		."\n WHERE  ".$db->quoteId('id')." = :xid";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':xcli', $row->clicks, PDO::PARAM_INT);
        $stmt->bindParam(':xvis', $row->visits, PDO::PARAM_INT);
        $stmt->bindParam(':xlan', $row->langs, PDO::PARAM_STR);
        $stmt->bindParam(':xid', $row->id, PDO::PARAM_INT);
        try {
        	$stmt->execute();
		} catch (PDOException $e) {
			return false;
		}
		return true;
	}


	/*****************************/
	/* UPDATE AND COMPRESS LANGS */
	/*****************************/
	private function trackCompressLangs($serlangs) {
		$clang = $this->lang;
		if ($serlangs == '') {
			$lngarr = array();
			$lngarr[$clang] = 1;
			return serialize($lngarr);
		}

		$lngarr = unserialize($serlangs);
		if (!isset($lngarr[$clang])) {
			$lngarr[$clang] = 1;
		} else {
			$lngarr[$clang]++;
		}
		return serialize($lngarr);
	}


	/************************/
	/* EMPTY UNIQUE MARKERS */
	/************************/
	private function emptyTracker() {
		$db = eFactory::getDB();

		$sql = "DELETE FROM ".$db->quoteId('#__statistics_temp');
		$stmt = $db->prepare($sql);
		$stmt->execute();
	}


	/*********************************/
	/* FIRST TIME VISITOR FOR TODAY? */
	/*********************************/
	private function firstVisit() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('id')." FROM ".$db->quoteId('#__statistics_temp')." WHERE ".$db->quoteId('uniqueid')." = :xhash";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xhash', $this->hash, PDO::PARAM_STR);
		$stmt->execute();
		$id = (int)$stmt->fetchResult();
		return ($id > 0) ? false : true;
	}


	/***********************/
	/* MARK UNIQUE VISITOR */
	/***********************/
	private function trackVisit() {
		$db = eFactory::getDB();
	
		$sql = "INSERT INTO ".$db->quoteId('#__statistics_temp')." VALUES (NULL, :xhash)";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xhash', $this->hash, PDO::PARAM_STR);
		$stmt->execute();
	}


	/********************************************/
	/* GET CURRENT'S DATE DATA OR INITIATE THEM */
	/********************************************/
	private function getDateDate() {
		$db = eFactory::getDB();

		$sql = "SELECT * FROM ".$db->quoteId('#__statistics')." WHERE ".$db->quoteId('statdate')." = :statd";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':statd', $this->statdate, PDO::PARAM_STR);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_OBJ);
		if (!$row) {
			$this->newdate = true;
			return $this->createNewDate();
		}
		return $row;
	}


	/**************************/
	/* CREATE A NEW DATE ITEM */
	/**************************/
	private function createNewDate() {
		$row = new stdClass;
		$row->id = null;
		$row->statdate = $this->statdate;
		$row->clicks = 0;
		$row->visits = 0;
		$row->langs = '';
		
		return $row;
	}


	/****************************/
	/* UNIQUE DETERMINE VISITOR */
	/****************************/
	private function makeUniqueHash() {
    	if (isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'] != '')) {
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	} elseif (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '')) {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	} else {
    		$ip = '';
    	}

		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

		$hash = $ip.$agent;
		if ($hash == '') { return ''; }
		return md5($hash);
	}

}

?>