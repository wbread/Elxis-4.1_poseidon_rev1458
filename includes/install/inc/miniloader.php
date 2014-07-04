<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_INSTALLER') or die ('Direct access to this location is not allowed');


if (!defined('ELXIS_OS')) {
	switch (strtoupper(substr(PHP_OS, 0, 3))) {
		case 'WIN':
			define('ELXIS_OS', 'WIN');
			define('DS', "\\");
			define("_LEND", "\r\n");
		break;
		case 'MAC':
			define('ELXIS_OS', 'MAC');
			define('DS', "/");
			define("_LEND", "\r");
		break;
		default:
			define('ELXIS_OS', 'LIN');
			define('DS', "/");
			define("_LEND", "\n");
		break;
	}
}

if (!class_exists('elxisLoader', false)) {
	class elxisLoader {

		static public function load($str, $class='') {
        	$file = ELXIS_PATH.'/includes/'.str_replace(':', '/', $str).'.php';
        	if (is_file($file)) { require_once($file); }
		}

		static public function loadFile($file) {
			if (($file != '') && is_file(ELXIS_PATH.'/'.$file)) {
				require_once(ELXIS_PATH.'/'.$file);
			}
		}

	}
}


if (!class_exists('eFactory', false)) {
	class eFactory {

		private static $elxis = null;
		private static $db = null;

		static public function getElxis($cfg=null) {
			if (self::$elxis == null) {
				elxisLoader::loadFile('includes/libraries/elxis/framework.class.php');
				$elxis = new elxisFramework($cfg);
				self::$elxis = $elxis;
				return $elxis;
			} else {
				return self::$elxis;
			}
		}


		static public function getDB() {
			if (self::$db == null) {
				self::$db = self::makeDB();
				return self::$db;
			} else {
				return self::$db;
			}
		}


		static private function makeDB() {
			$elxis = self::getElxis();
			elxisLoader::loadFile('includes/libraries/elxis/database.class.php');
			self::$db = new elxisDatabase();
			return self::$db;
		}

	}
}

if (!class_exists('elxisError', false)) {
	class elxisError {
		static public function error($errno, $errstr, $errfile, $errline) {}
		static public function logWarning($message) {}
		static public function logError($message) {}
	}
}


if (!class_exists('elxisFramework', false)) {
	class elxisFramework {

		private $cfg = array();

		public function __construct($cfg=null) {
			if (is_array($cfg) && (count($cfg) > 0)) { $this->cfg = $cfg; }
		}


		public function getConfig($var='') {
			return (isset($this->cfg[$var])) ? $this->cfg[$var] : null;
		}
	}
}

?>