<?php 
/**
* @version		$Id: apc.class.php 1208 2012-06-24 12:34:50Z datahell $
* @package		Elxis
* @subpackage	Opcode cache - APC
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisAPC {

	private static $enabled = false;
	private static $id = 0;
	private static $maxttl = 7200;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public static function init() {
		if (!function_exists('apc_fetch')) { self::$enabled = false; return; }
		if (!class_exists('elxisFramework', false)) {
			elxisLoader::loadFile('configuration.php');
			$cfg = new elxisConfig();
			self::$enabled = ($cfg->get('APC') == 1) ? true : false;
			self::$id = (int)$cfg->get('APCID');
			unset($cfg);		
		} else {
			$elxis = eFactory::getElxis();
			self::$enabled = ($elxis->getConfig('APC') == 1) ? true : false;
			self::$id = (int)$elxis->getConfig('APCID');			
		}
	}


	/***********************/
	/* FETCH CACHED OPCODE */
	/***********************/
	public static function fetch($name, $group='') {
		if (!self::$enabled) { return false; }
		$ref = self::getRefName($name, $group);
		if ($ref === false) { return false; }
		return apc_fetch($ref);
	}


	/********************************************/
	/* SAVE IN CACHE (OVERWRITE IS NOT ALLOWED) */
	/********************************************/
	public static function add($name, $group, $data, $ttl=0) {
		if (!self::$enabled) { return false; }
		$ref = self::getRefName($name, $group);
		if ($ref === false) { return false; }
		if (!$ttl) { $ttl = self::$maxttl; }
		return apc_add($ref, $data, $ttl);
	}


	/****************************************/
	/* SAVE IN CACHE (OVERWRITE IS ALLOWED) */
	/****************************************/
	public static function store($name, $group, $data, $ttl=0) {
		if (!self::$enabled) { return false; }
		$ref = self::getRefName($name, $group);
		if ($ref === false) { return false; }
		if (!$ttl) { $ttl = self::$maxttl; }
		return apc_store($ref, $data, $ttl);
	}


	/***************************/
	/* DELETE A CACHED ELEMENT */
	/***************************/
	public static function delete($name, $group='') {
		if (!self::$enabled) { return false; }
		$ref = self::getRefName($name, $group);
		if ($ref === false) { return false; }
		return apc_delete($ref);
	}


	/********************************/
	/* DELETE ALL OR GROUP ELEMENTS */
	/********************************/
	public static function deleteAll($group='') {
		if (!self::$enabled) { return false; }
		$info = apc_cache_info('user');
		if (!$info || !is_array($info)) { return true; }		
		if ($info['cache_list']) {
			foreach ($info['cache_list'] as $cached) {
				$prefix = ($group != '') ? self::$id.'_'.$group : self::$id.'_';
				if (strpos($cached['info'], $prefix) === false) { continue; }
				apc_delete($cached['info']);
			}
		}
		return true;
	}


	/************************************/
	/* GET CACHE ELEMENT REFERENCE NAME */
	/************************************/
	private static function getRefName($name, $group) {
		$name2 = trim(preg_replace('/[^a-z0-9]/i', '', $name));
		$group2 = trim(preg_replace('/[^a-z0-9]/i', '', $group));
		if ($name2 == '') { return false; }
		if ($name != $name2) { return false; }
		if ($group != $group2) { return false; }
		$ref = ($group != '') ? self::$id.'_'.$group.'_'.$name : self::$id.'_'.$name;
		return $ref;
	}


	/*************************/
	/* GET USAGE INFORMATION */
	/*************************/
	public static function getInfo() {
		if (!self::$enabled) { return false; }
		$info = apc_cache_info('user');
		if (!$info || !is_array($info)) { return false; }

		$stats = array();
		$stats['server'] = array(
			'num_hits' => $info['num_hits'],
			'mem_size' => $info['mem_size'],
			'num_entries' => $info['num_entries']
		);
		$stats['elxis'] = array(
			'num_hits' => 0,
			'mem_size' => 0,
			'num_entries' => 0
		);
		$stats['items'] = array();
		if ($info['cache_list']) {
			$now = time();
			foreach ($info['cache_list'] as $cached) {
				if (strpos($cached['info'], self::$id.'_') === false) { continue; }
				$parts = explode('_', $cached['info']);
				if (!isset($parts[1])) { continue; }
				if (isset($parts[2])) {
					$group = $parts[1];
					$name = $parts[2];
				} else {
					$group = '';
					$name = $parts[1];
				}
				$stats['elxis']['num_entries']++;
				$stats['elxis']['num_hits'] += $cached['num_hits'];
				$stats['elxis']['mem_size'] += $cached['mem_size'];
				$dt = ($cached['access_time'] > 0)  ? $cached['access_time'] - $cached['mtime'] : $now - $cached['mtime'];
				$stats['items'][] = array('name' => $name, 'group' => $group, 'dt' => $dt, 'ttl' => $cached['ttl'], 'mtime' => $cached['mtime'], 'num_hits' => $cached['num_hits'], 'mem_size' => $cached['mem_size']);
			}
		}

		return $stats;
	}

}

?>