<?php 
/**
* @version		$Id: ip.helper.php 506 2011-07-12 17:31:33Z datahell $
* @package		Elxis
* @subpackage	Helpers/IP
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisIPHelper {


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/********************************/
 	/* GET CLIENT'S REAL IP ADDRESS */
 	/********************************/
	public function clientIP($forcev6=true, $tolong=false) {
    	if (isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'] != '')) {
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	} elseif (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '')) {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	} else {
    		return null;
    	}

		if (($pos = strpos($ip, ',')) > 0) {
			$ip = substr($ip, 0, ($pos - 1));
		}

		if ($forcev6) {
			$ip = $this->toV6($ip);
			return $tolong ? $this->ipv6tolong($ip) : $ip;
		} else {
			if ($tolong == true) {
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
					return $this->ipv6tolong($ip);
				} else {
					return ip2long($ip);
				}
			} else {
				return $ip;
			}
		}
	}


	/******************************/
	/* IP2LONG FOR IPV6 ADDRESSES */
	/******************************/
	public function ipv6tolong($ip) {
		$ip = $this->expandV6($ip);
		$parts = explode(':', $ip);
		$iparr = array('', '');
		for ($i = 0; $i < 4; $i++) {
			$iparr[0] .= str_pad(base_convert($parts[$i], 16, 2), 16, 0, STR_PAD_LEFT);
		}
		for ($i = 4; $i < 8; $i++) {
			$iparr[1] .= str_pad(base_convert($parts[$i], 16, 2), 16, 0, STR_PAD_LEFT);
		}

		return base_convert($iparr[0], 2, 10) + base_convert($iparr[1], 2, 10);
	}


	/*****************************************************/
	/* EXPAND IP V6 NOTATION BY REPLACING '::' WITH ':0' */
	/*****************************************************/
	public function expandV6($ip) {
		if (strpos($ip, '::') !== false) {
			$ip = str_replace('::', str_repeat(':0', 8 - substr_count($ip, ':')).':', $ip);
		}
		if (strpos($ip, ':') === 0) { $ip = '0'.$ip; }
		return $ip;
	}


	/*****************************************/
	/* CONVERT A GIVEN IP V4/V6 TO V6 FORMAT */
	/*****************************************/
	public function toV6($ip) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			if (strpos($ip, '.') > 0) {
				$ip = substr($ip, strrpos($ip, ':') +1);
			} else {
				return $ip;
			}
		}
		$is_v4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
		if ($is_v4 === false) { return null; }
		$iparr = array_pad(explode('.', $ip), 4, 0);
		$Part7 = base_convert(($iparr[0] * 256) + $iparr[1], 10, 16);
		$Part8 = base_convert(($iparr[2] * 256) + $iparr[3], 10, 16);
		return '::ffff:'.$Part7.':'.$Part8;
	}


	/*******************************************************/
	/* CONVERT A PREVIOUSLY V4->V6 CONVERTED IP BACK TO V4 */
	/*******************************************************/
	public function ipv6tov4($ip) {
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			if (preg_match('#^(\:\:ffff\:)#i', $ip)) {
				$ip = preg_replace('#^(\:\:ffff\:)#i', '', $ip);
				$parts = preg_split('/\:/', $ip, 2, PREG_SPLIT_NO_EMPTY);
				$ab = sprintf('%04x', hexdec($parts[0]));
				if (isset($parts[1])) {
					$cd = sprintf('%04x', hexdec($parts[1]));
				} else {
					$cd = '0000';
				}
				$a = substr($ab, 0, 2);
				$b = substr($ab, 2, 2);
				$c = substr($cd, 0, 2);
				$d = substr($cd, 2, 2);
				return hexdec($a).'.'.hexdec($b).'.'.hexdec($c).'.'.hexdec($d);
			}
		}
		return $ip;
	}

}

?>