<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contentModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/***********************/
	/* GET PLUGINS FROM DB */
	/***********************/
	public function getPlugins() {
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('plugin')." FROM ".$this->db->quoteId('#__plugins')
		."\n WHERE ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))"
		."\n AND ".$this->db->quoteId('published').'=1 ORDER BY '.$this->db->quoteId('title')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


	/**********************/
	/* GET PLUGIN FROM DB */
	/**********************/
	public function getPlugin($id) {
		$elxis = eFactory::getElxis();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$sql = "SELECT * FROM ".$this->db->quoteId('#__plugins')
		."\n WHERE ".$this->db->quoteId('id')." = :xid AND ((".$this->db->quoteId('alevel')." <= :lowlevel) OR (".$this->db->quoteId('alevel')." = :exactlevel))"
		."\n AND ".$this->db->quoteId('published').'=1';
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}

}

?>