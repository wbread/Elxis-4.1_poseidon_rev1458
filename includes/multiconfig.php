<?php 
/**
* @version		$Id: multiconfig.php 921 2012-02-19 12:29:24Z datahell $
* @package		Elxis
* @subpackage	Multisites
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Multisites configuration loader
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


if (!defined('ELXIS_MULTISITE')) {
	$multisite = 1;
	if (isset($_SERVER['REQUEST_URI']) && isset($multisites) && is_array($multisites)) {
		$p = preg_split('#\/#', $_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY);
		if ($p) {
			$folder = $p[0];
			foreach ($multisites as $id => $site) {
				if ($site['active'] == false) { continue; }
				if ($site['folder'] == $folder) { $multisite = $id; break; } 
			}
		}
	}

	define('ELXIS_MULTISITE', $multisite);
	unset($multisite);
}

if (!class_exists('elxisConfig', false)) {
	require(ELXIS_PATH.'/config'.ELXIS_MULTISITE.'.php'); //generate fatal error if not exist!
}

?>