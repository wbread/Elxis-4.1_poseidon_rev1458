<?php 
/**
* @version		$Id: registry.class.php 432 2011-06-24 18:23:39Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2012 elxis.org (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class eRegistry {

	static private $registry = array();


	/**********************/
	/* SET REGISTRY ENTRY */
	/**********************/
	static public function set($obj, $idx='') {
		if ($idx == '') {
			if (!is_object($obj)) { return false; }
			$idx = get_class($obj);
			if (strtolower($idx) == 'stdclass') { return false; }
		}
		if (!isset(self::$registry[$idx])) { self::$registry[$idx] = $obj; }
		return true;
	}


	/**********************/
	/* GET REGISTRY ENTRY */
	/**********************/
	static public function get($idx) {
		return isset(self::$registry[$idx]) ? self::$registry[$idx] : null;
	}


	/*********************************/
	/* GET MULTIPLE REGISTRY ENTRIES */
	/*********************************/
	static public function gets($idxs) {
		$out = array();
		if (!$idxs || !is_array($idxs)) { return $out; }
		foreach ($idxs as $idx) {
			$out[] = self::get($idx);
		}
		return $out;
	}


	/*****************************************/
	/* CHECK IF AN ONJECT HAS BEEN INITIATED */
	/*****************************************/
	static public function isLoaded($idx) {
		return (isset(self::$registry[$idx])) ? true : false;
	}


	/*************************/
	/* REMOVE REGISTRY ENTRY */
	/*************************/
	static public function remove($idx) {
		if (isset(self::$registry[$idx])) { unset(self::$registry[$idx]); }
	}


	/******************************/
	/* GET ALL REGISTERED OBJECTS */
	/******************************/
	static public function getAll() {
		if (self::$registry) { return array_keys(self::$registry); }
		return array();
	}

}

?>