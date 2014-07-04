<?php 
/**
* @version		$Id: loader.php 1373 2012-12-10 19:29:49Z datahell $
* @package		Elxis
* @subpackage	Elxis loader
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS bootstrap file
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisLoader {

	static private $classes = array();
	static private $files = array(); //generic files loader in order to avoid require_once


	/******************************/
	/* GET CLASS NAME FROM STRING */
	/******************************/
	static private function getClassName($str) {
		$parts = preg_split('/\:/', $str);
		if (count($parts) > 1) {
			$classfile = strtolower(array_pop($parts));
			$parts2 = preg_split('/\./', $classfile, 2);
			$class = 'elxis'.ucfirst($parts2[0]);
		} else {
			$class = 'elxis'.ucfirst(strtolower($parts[0]));
		}
		return $class;
	}


	/*****************/
	/* INCLUDE CLASS */
	/*****************/
	static public function load($str, $class='') {
		if ($class == '') { $class = self::getClassName($str); }
		if (isset(self::$classes[$class])) { return true; }
        $file = ELXIS_PATH.'/includes/'.str_replace(':', '/', $str).'.php';
        if (is_file($file)) {
			require($file);
		} else {
			$trace = debug_backtrace();
			$extra = '';
			if (is_array($trace) && isset($trace[0])) {
				$extra = "\n".'Backtrace report: File: '.$trace[0]['file'].' Line: '.$trace[0]['line'];
			}
			trigger_error('File "'.$file.'" does not exist! Elxis loader could not load class '.$class.$extra, E_USER_ERROR);
			return false;
		}

		self::$classes[$class] = $file;
		return true;
	}


	/*********************/
	/* REQUIRE FILE ONCE */
	/*********************/
	static public function loadFile($file) {
		if (($file == '') || !is_file(ELXIS_PATH.'/'.$file)) {
			$trace = debug_backtrace();
			$extra = '';
			if (is_array($trace) && isset($trace[0])) {
				$extra = "\n".'Backtrace report: File: '.$trace[0]['file'].' Line: '.$trace[0]['line'];
			}
			trigger_error('File "'.$file.'" does not exist!'.$extra, E_USER_ERROR);
			return false;
		} else {
			if (in_array($file, self::$files)) { return true; }
			include(ELXIS_PATH.'/'.$file);
			self::$files[] = $file;
			return true;
		}
	}


	/*********************************/
	/* INCLUDE CLASS AND INITIATE IT */
	/*********************************/
	static public function loadInit($str, $instance, $class='') {
		if (eRegistry::isLoaded($instance)) { return true; }
		if ($class == '') { $class = self::getClassName($str); }
        $file = ELXIS_PATH.'/includes/'.str_replace(':', '/', $str).'.php';
        if (is_file($file)) {
			require($file);
		} else {
			$trace = debug_backtrace();
			$extra = '';
			if (is_array($trace) && isset($trace[0])) {
				$extra = "\n".'Backtrace report: File: '.$trace[0]['file'].' Line: '.$trace[0]['line'];
			}
			trigger_error('FATAR ERROR: File "'.$file.'" does not exist! Elxis loader could not load class '.$class.$extra, E_USER_ERROR);
			return false;
		}

		$obj = new $class();
		eRegistry::set($obj, $instance);
	}


	/*****************************/
	/* LOAD CLASS (AS SINGLETON) */
	/*****************************/
	public static function loadClass($class) {
        if (class_exists($class, false)) { return true; }
        if (array_key_exists(strtolower($class), self::$classes)) {
			include(self::$classes[$class]);
            return true;
        }
        if (strpos($class, 'DbTable') !== false) {
        	$tbl = str_replace('DbTable', '', $class);
        	return self::load('libraries:elxis:database:tables:'.$tbl.'.db', $class);
       	}
        return false;
    }

}


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

if (!file_exists(ELXIS_PATH.'/configuration.php')) {
	require(ELXIS_PATH.'/includes/install/install.php');
	exit();
}

require(ELXIS_PATH.'/includes/libraries/elxis/apc.class.php');
elxisAPC::init();
require(ELXIS_PATH.'/includes/libraries/elxis/exit.class.php');
require(ELXIS_PATH.'/includes/libraries/elxis/defender.class.php');


/**************************************************/
/* TRY TO AUTOLOAD CLASS BEFORE FATAL ERROR OCCUR */
/**************************************************/
function autoloader($class) {
    if (strpos($class, 'Swift') === 0) { return false; }
	if (elxisLoader::loadClass($class)) {
		return true;
	} else {
		trigger_error('Class "'.$class.'" could not be autoloaded by the Elxis loader', E_USER_ERROR);
		return false;
	}
}

spl_autoload_register('autoloader', false);


/*******************/
/* SYSTEM SHUTDOWN */
/*******************/
function elxisSystemShutdown() {
	if (eRegistry::isLoaded('eFiles')) {
		eRegistry::get('eFiles')->closeFTP();
	}
	if (isset($_SESSION)) { session_write_close(); }
}

ini_set('display_errors', 0);
date_default_timezone_set('UTC');

if (version_compare(phpversion(), '5.4', '<')) {
	if (get_magic_quotes_gpc()) {
    	$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    	while (list($key, $val) = each($process)) {
        	foreach ($val as $k => $v) {
            	unset($process[$key][$k]);
            	if (is_array($v)) {
                	$process[$key][stripslashes($k)] = $v;
                	$process[] = &$process[$key][stripslashes($k)];
            	} else {
                	$process[$key][stripslashes($k)] = stripslashes($v);
            	}
        	}
    	}
    	unset($process);
	}
}

elxisLoader::load('libraries:elxis:registry.class', 'eRegistry');
register_shutdown_function('elxisSystemShutdown');
elxisLoader::loadInit('libraries:elxis:performance.class', 'ePerformance');
elxisLoader::load('libraries:elxis:factory.class', 'eFactory');
elxisLoader::loadInit('libraries:elxis:framework.class', 'elxis', 'elxisFramework');
elxisLoader::load('libraries:elxis:error.class');
elxisError::init();
set_error_handler('elxisErrorHandler');
register_shutdown_function('elxisErrorShutdown');
elxisLoader::load('libraries:utf8:utf8.class', 'elxisUTF8');
elxisUTF8::init();
elxisLoader::loadInit('libraries:elxis:uri.class', 'eURI', 'elxisUri');
elxisLoader::loadInit('libraries:elxis:language.class', 'eLang', 'elxisLanguage');
elxisLoader::loadInit('libraries:elxis:date.class', 'eDate', 'elxisDate');
eFactory::getElxis()->initSession(); //after URI!

$isoffline = false;
if (!defined('ELXIS_ADMIN')) {
	if (eFactory::getElxis()->getConfig('ONLINE') === 0) {
		$isoffline = true;
	} else if (eFactory::getElxis()->getConfig('ONLINE') === 2) {
		$isoffline = (eFactory::getElxis()->user()->gid == 1) ? false : true;
	}
}

if ($isoffline) {
	exitPage::make('offline', 'LOAD-0001');
} else {
	elxisLoader::loadInit('libraries:elxis:document.class', 'eDoc', 'elxisDocument');
	eFactory::getDocument()->make();
}

if (isset($_SESSION)) { session_write_close(); }

?>