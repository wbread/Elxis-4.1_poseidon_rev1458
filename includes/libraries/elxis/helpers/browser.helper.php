<?php 
/**
* @version		$Id: browser.helper.php 1397 2013-02-25 17:11:31Z datahell $
* @package		Elxis
* @subpackage	Helpers / Browser detection
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
* @based on:
* (1) Browser.php 1.9 by Chris Schuld (http://chrisschuld.com/)
* (2) Full Featured PHP Browser/OS detection by Harald Hope (http://techpatterns.com/)
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisBrowserHelper {

	private $agent = '';
	private $browser = '';
	private $version = '';
	private $platform = '';
	private $os = '';
	private $os_version = '';
	private $os_name = '';
	private $is_aol = false;
	private $is_mobile = false;
	private $is_robot = false;
	private $aol_version = '';


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/********************/
	/* GET BROWSER INFO */
	/********************/
	public function getBrowser($useragent='') {
		$this->reset();
		if ($useragent != '') {
			$this->setUserAgent($useragent);
		} else {
			$this->determine();
		}

		$browser_info = array(
			'agent' => $this->agent,
			'browser' => $this->browser,
			'version' => $this->version,
			'platform' => $this->platform,
			'os' => $this->os,
			'os_version' => $this->os_version,
			'os_name' => $this->os_name,
			'mobile' => $this->is_mobile,
			'robot' => $this->is_robot,
			'aol' => $this->is_aol,
			'aol_version' => $this->aol_version,
		);
		return $browser_info;
	}


	/***************************************************/
	/* INDEPENDENT MOBILE CHECK (FASTER, MORE UPDATED) */
	/* Harald Hope, 5.4.12, http://techpatterns.com    */
	/***************************************************/
	public function isMobile($useragent='') {
		if ($useragent == '') {
			if (!isset($_SERVER['HTTP_USER_AGENT'])) { return false; }
			$useragent = $_SERVER['HTTP_USER_AGENT'];
		}
		$ismobile = false;
		$mob_patterns = array(
			'android', 'blackberry', 'epoc', 'linux armv', 'palmos', 'palmsource', 'windows ce', 'windows phone os', 'symbianos', 'symbian os', 'symbian', 'webos', 
			'benq', 'blackberry', 'danger hiptop', 'ddipocket', ' droid', 'ipad', 'ipod', 'iphone', 'kindle', 'kobo', 'lge-cx', 'lge-lx', 'lge-mx', 'lge vx', 'lge ', 'lge-', 'lg;lx', 'nintendo wii', 'nokia', 'nook', 'palm', 'pdxgw', 'playstation', 'sagem', 'samsung', 'sec-sgh', 'sharp', 'sonyericsson', 'sprint', 'zune', 'j-phone', 'n410', 'mot 24', 'mot-', 'htc-', 'htc_', 'htc ', 'sec-', 'sie-m', 'sie-s', 'spv ', 'vodaphone', 'smartphone', 'armv', 'midp', 'mobilephone',
			'avantgo', 'blazer', 'elaine', 'eudoraweb', 'fennec', 'iemobile',  'minimo', 'mobile safari', 'mobileexplorer', 'opera mobi', 'opera mini', 'netfront', 'opwv', 'polaris', 'semc-browser', 'skyfire', 'up.browser', 'webpro/', 'wms pie', 'xiino', 
			'astel', 'docomo', 'novarra-vision', 'portalmmm', 'reqwirelessweb', 'vodafone', 'mobile;', 'tablet;'
		);
		foreach ($mob_patterns as $mob) {
			if (stristr($useragent, $mob)) {
				if (($mob != 'zune') || stristr($useragent, 'iemobile')) {
					$ismobile = true;
					break;
				}
			}
		}
		return $ismobile;
	}


	/************************/
	/* RESET ALL PROPERTIES */
	/************************/
	private function reset() {
		$this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$this->browser = '';
		$this->version = '';
		$this->platform = '';
		$this->os = '';
		$this->os_version = '';
		$this->os_name = '';
		$this->is_aol = false;
		$this->is_mobile = false;
		$this->is_robot = false;
		$this->aol_version = '';
	}


	/***************************/
	/* SET THE BROWSER VERSION */
	/***************************/
	private function setVersion($version) {
		$this->version = preg_replace('/[^0-9,.,a-z,A-Z-]/','',$version);
	}


	/***********************/
	/* SET THE AOL VERSION */
	/***********************/
	private function setAolVersion($version) {
		$this->aol_version = preg_replace('/[^0-9,.,a-z,A-Z]/','',$version);
	}


	private function setUserAgent($agent_string) {
		$this->reset();
		$this->agent = $agent_string;
		$this->determine();
	}


	private function determine($checkos=true) {
		if ($checkos) {
			$this->checkOS();
			$this->checkPlatform();
		}
		$this->checkBrowsers();
		$this->checkForAol();
	}


	private function checkBrowsers() {
		return (
			// well-known, well-used
			// Special Notes:
			// (1) Opera must be checked before FireFox due to the odd
			//     user agents used in some older versions of Opera
			// (2) WebTV is strapped onto Internet Explorer so we must
			//     check for WebTV before IE
			// (3) (deprecated) Galeon is based on Firefox and needs to be
			//     tested before Firefox is tested
			// (4) OmniWeb is based on Safari so OmniWeb check must occur
			//     before Safari
			// (5) Netscape 9+ is based on Firefox so Netscape checks
			//     before FireFox are necessary
			$this->checkBrowserWebTv() ||
			$this->checkBrowserInternetExplorer() ||
			$this->checkBrowserOpera() ||
			$this->checkBrowserGaleon() ||
			$this->checkBrowserNN9Plus() ||
			$this->checkBrowserFirefox() ||
			$this->checkBrowserChrome() ||
			$this->checkBrowserOmniWeb() ||
			// common mobile
			$this->checkBrowserAndroid() ||
			$this->checkBrowseriPad() ||
			$this->checkBrowseriPod() ||
			$this->checkBrowseriPhone() ||
			$this->checkBrowserBlackBerry() ||
			$this->checkBrowserNokia() ||
			// common bots
			$this->checkBrowserGoogleBot() ||
			$this->checkBrowserMSNBot() ||
			$this->checkBrowserSlurp() ||
			// WebKit base check (post mobile and others)
			$this->checkBrowserSafari() ||
			// everyone else
			$this->checkBrowserNetPositive() ||
			$this->checkBrowserFirebird() ||
			$this->checkBrowserKonqueror() ||
			$this->checkBrowserIcab() ||
			$this->checkBrowserPhoenix() ||
			$this->checkBrowserAmaya() ||
			$this->checkBrowserLynx() ||
			$this->checkBrowserShiretoko() ||
			$this->checkBrowserIceCat() ||
			$this->checkBrowserW3CValidator() ||
			$this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
		);
	}


 	private function checkBrowserBlackBerry() {
		if( stripos($this->agent,'blackberry') !== false ) {
			$aresult = explode("/",stristr($this->agent,"BlackBerry"));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'BlackBerry';
			$this->is_mobile = true;
			return true;
		}
		return false;
	}


	private function checkForAol() {
		$this->is_aol = false;
		$this->setAolVersion('');
		if( stripos($this->agent,'aol') !== false ) {
			$aversion = explode(' ',stristr($this->agent, 'AOL'));
			$this->is_aol = true;
			$this->setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $aversion[1]));
			return true;
		}
		return false;
	}


	private function checkBrowserGoogleBot() {
		if (stripos($this->agent,'googlebot') !== false) {
			$aresult = explode('/',stristr($this->agent,'googlebot'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion(str_replace(';','',$aversion[0]));
			$this->browser = 'GoogleBot';
			$this->is_robot = true;
			return true;
		}
		return false;
	}

	private function checkBrowserMSNBot() {
		if (stripos($this->agent,"msnbot") !== false) {
			$aresult = explode("/",stristr($this->agent,"msnbot"));
			$aversion = explode(" ",$aresult[1]);
			$this->setVersion(str_replace(";","",$aversion[0]));
			$this->browser = 'MSN Bot';
			$this->is_robot = true;
			return true;
		}
		return false;
	}


	private function checkBrowserW3CValidator() {
		if (stripos($this->agent,'W3C-checklink') !== false) {
			$aresult = explode('/',stristr($this->agent,'W3C-checklink'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'W3C Validator';
			return true;
		} else if (stripos($this->agent,'W3C_Validator') !== false) {
			$ua = str_replace("W3C_Validator ", "W3C_Validator/", $this->agent);
			$aresult = explode('/',stristr($ua,'W3C_Validator'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'W3C Validator';
			return true;
		}
		return false;
	}


	private function checkBrowserSlurp() {
		if (stripos($this->agent,'slurp') !== false ) {
			$aresult = explode('/',stristr($this->agent,'Slurp'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'Yahoo! Slurp';
				$this->is_robot = true;
				$this->is_mobile = false;
			return true;
		}
		return false;
	}


	private function checkBrowserInternetExplorer() {
		if (stripos($this->agent,'microsoft internet explorer') !== false) {
			$this->browser = 'Internet Explorer';
			$this->setVersion('1.0');
			$aresult = stristr($this->agent, '/');
			if (preg_match('/308|425|426|474|0b1/i', $aresult)) {
				$this->setVersion('1.5');
			}
			return true;
		} else if (stripos($this->agent,'msie') !== false && stripos($this->agent,'opera') === false) {
			if (stripos($this->agent,'msnb') !== false ) {
				$aresult = explode(' ',stristr(str_replace(';','; ',$this->agent),'MSN'));
				$this->browser = 'MSN Browser';
				$this->setVersion(str_replace(array('(',')',';'),'',$aresult[1]));
				return true;
			}
			$aresult = explode(' ',stristr(str_replace(';','; ',$this->agent),'msie'));
			$this->browser = 'Internet Explorer';
			$this->setVersion(str_replace(array('(',')',';'),'',$aresult[1]));
			return true;
		} else if (stripos($this->agent,'mspie') !== false || stripos($this->agent,'pocket') !== false) {
			$aresult = explode(' ',stristr($this->agent,'mspie'));
			$this->platform = 'Windows CE';
			$this->browser = 'Pocket Internet Explorer';
			$this->is_mobile = true;

			if (stripos($this->agent,'mspie') !== false) {
				$this->setVersion($aresult[1]);
			} else {
				$aversion = explode('/',$this->agent);
				$this->setVersion($aversion[1]);
			}
			return true;
		}
		return false;
	}


	private function checkBrowserOpera() {
		if (stripos($this->agent,'opera mini') !== false) {
			$resultant = stristr($this->agent, 'opera mini');
			if (preg_match('/\//',$resultant)) {
				$aresult = explode('/',$resultant);
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$aversion = explode(' ',stristr($resultant,'opera mini'));
				$this->setVersion($aversion[1]);
			}
			$this->browser = 'Opera Mini';
			$this->is_mobile = true;
			return true;
		} else if (stripos($this->agent,'opera') !== false) {
			$resultant = stristr($this->agent, 'opera');
			if( preg_match('/Version\/(10.*)$/',$resultant,$matches) ) {
				$this->setVersion($matches[1]);
			} else if( preg_match('/\//',$resultant) ) {
				$aresult = explode('/',str_replace("("," ",$resultant));
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$aversion = explode(' ',stristr($resultant,'opera'));
				$this->setVersion(isset($aversion[1])?$aversion[1]:"");
			}
			$this->browser = 'Opera';
			return true;
		}
		return false;
	}


	private function checkBrowserChrome() {
		if (stripos($this->agent,'Chrome') !== false) {
			$aresult = explode('/',stristr($this->agent,'Chrome'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'Chrome';
			return true;
		}
		return false;
	}


	private function checkBrowserWebTv() {
		if (stripos($this->agent,'webtv') !== false) {
			$aresult = explode('/',stristr($this->agent,'webtv'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'WebTV';
			return true;
		}
		return false;
	}


	private function checkBrowserNetPositive() {
		if (stripos($this->agent,'NetPositive') !== false) {
			$aresult = explode('/',stristr($this->agent,'NetPositive'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion(str_replace(array('(',')',';'),'',$aversion[0]));
			$this->browser = 'NetPositive';
			return true;
		}
		return false;
	}


	private function checkBrowserGaleon() {
		if (stripos($this->agent,'galeon') !== false) {
			$aresult = explode(' ',stristr($this->agent,'galeon'));
			$aversion = explode('/',$aresult[0]);
			$this->setVersion($aversion[1]);
			$this->browser = 'Galeon';
			return true;
		}
		return false;
	}


	private function checkBrowserKonqueror() {
		if (stripos($this->agent,'Konqueror') !== false) {
			$aresult = explode(' ',stristr($this->agent,'Konqueror'));
			$aversion = explode('/',$aresult[0]);
			$this->setVersion($aversion[1]);
			$this->browser = 'Konqueror';
			return true;
		}
		return false;
	}


	private function checkBrowserIcab() {
		if (stripos($this->agent,'icab') !== false) {
			$aversion = explode(' ',stristr(str_replace('/',' ',$this->agent),'icab'));
			$this->setVersion($aversion[1]);
			$this->browser = 'iCab';
			return true;
		}
		return false;
	}


	private function checkBrowserOmniWeb() {
		if (stripos($this->agent,'omniweb') !== false) {
			$aresult = explode('/',stristr($this->agent,'omniweb'));
			$aversion = explode(' ',isset($aresult[1])?$aresult[1]:"");
			$this->setVersion($aversion[0]);
			$this->browser = 'OmniWeb';
			return true;
		}
		return false;
	}


	private function checkBrowserPhoenix() {
		if (stripos($this->agent,'Phoenix') !== false) {
			$aversion = explode('/',stristr($this->agent,'Phoenix'));
			$this->setVersion($aversion[1]);
			$this->browser = 'Phoenix';
			return true;
		}
		return false;
	}


	private function checkBrowserFirebird() {
		if (stripos($this->agent,'Firebird') !== false) {
			$aversion = explode('/',stristr($this->agent,'Firebird'));
			$this->setVersion($aversion[1]);
			$this->browser = 'Firebird';
			return true;
		}
		return false;
	}


	private function checkBrowserNN9Plus() {
		if (stripos($this->agent,'Firefox') !== false && preg_match('/Navigator\/([^ ]*)/i',$this->agent,$matches)) {
			$this->setVersion($matches[1]);
			$this->browser = 'Netscape Navigator';
			return true;
		} else if (stripos($this->agent,'Firefox') === false && preg_match('/Netscape6?\/([^ ]*)/i',$this->agent,$matches)) {
			$this->setVersion($matches[1]);
			$this->browser = 'Netscape Navigator';
			return true;
		}
		return false;
	}


	private function checkBrowserShiretoko() {
		if (stripos($this->agent,'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i',$this->agent,$matches)) {
			$this->setVersion($matches[1]);
			$this->browser = 'Shiretoko';
			return true;
		}
		return false;
	}


	private function checkBrowserIceCat() {
		if (stripos($this->agent,'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i',$this->agent,$matches)) {
			$this->setVersion($matches[1]);
			$this->browser = 'IceCat';
			return true;
		}
		return false;
	}


	private function checkBrowserNokia() {
		if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i",$this->agent,$matches)) {
			$this->setVersion($matches[2]);
			if (stripos($this->agent,'Series60') !== false || strpos($this->agent,'S60') !== false ) {
				$this->browser = 'Nokia S60 OSS Browser';
			} else {
				$this->browser = 'Nokia Browser';
			}
			$this->is_mobile = true;
			return true;
		}
		return false;
	}


	private function checkBrowserFirefox() {
		if (stripos($this->agent,'safari') === false) {
			if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i",$this->agent,$matches)) {
				$this->setVersion($matches[1]);
				$this->browser = 'Firefox';
				return true;
			} else if (preg_match("/Firefox$/i",$this->agent,$matches)) {
				$this->setVersion('');
				$this->browser = 'Firefox';
				return true;
			}
		}
		return false;
	}


	private function checkBrowserIceweasel() {
		if (stripos($this->agent,'Iceweasel') !== false) {
			$aresult = explode('/',stristr($this->agent,'Iceweasel'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser = 'Iceweasel';
			return true;
		}
		return false;
	}


	private function checkBrowserMozilla() {
		if (stripos($this->agent,'mozilla') !== false  && preg_match('/rv:[0-9].[0-9][a-b]?/i',$this->agent) && stripos($this->agent,'netscape') === false) {
			$aversion = explode(' ',stristr($this->agent,'rv:'));
			preg_match('/rv:[0-9].[0-9][a-b]?/i',$this->agent,$aversion);
			$this->setVersion(str_replace('rv:','',$aversion[0]));
			$this->browser = 'Mozilla';
			return true;
		} else if (stripos($this->agent,'mozilla') !== false && preg_match('/rv:[0-9]\.[0-9]/i',$this->agent) && stripos($this->agent,'netscape') === false) {
			$aversion = explode('',stristr($this->agent,'rv:'));
			$this->setVersion(str_replace('rv:','',$aversion[0]));
			$this->browser = 'Mozilla';
			return true;
		} else if (stripos($this->agent,'mozilla') !== false  && preg_match('/mozilla\/([^ ]*)/i',$this->agent,$matches) && stripos($this->agent,'netscape') === false) {
			$this->setVersion($matches[1]);
			$this->browser = 'Mozilla';
			return true;
		}
		return false;
	}


	private function checkBrowserLynx() {
		if (stripos($this->agent,'lynx') !== false ) {
			$aresult = explode('/',stristr($this->agent,'Lynx'));
			$aversion = explode(' ',(isset($aresult[1])?$aresult[1]:""));
			$this->setVersion($aversion[0]);
			$this->browser = 'Lynx';
			return true;
		}
		return false;
	}


	private function checkBrowserAmaya() {
		if (stripos($this->agent,'amaya') !== false ) {
			$aresult = explode('/',stristr($this->agent,'Amaya'));
			$aversion = explode(' ',$aresult[1]);
			$this->setVersion($aversion[0]);
			$this->browser =  'Amaya';
			return true;
		}
		return false;
	}


	private function checkBrowserSafari() {
		if (stripos($this->agent,'Safari') !== false && stripos($this->agent,'iPhone') === false && stripos($this->agent,'iPod') === false ) {
			$aresult = explode('/',stristr($this->agent,'Version'));
			if (isset($aresult[1])) {
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion('');
			}
			$this->browser = 'Safari';
			return true;
		}
		return false;
	}


	private function checkBrowseriPhone() {
		if (stripos($this->agent,'iPhone') !== false ) {
			$aresult = explode('/',stristr($this->agent,'Version'));
			if( isset($aresult[1]) ) {
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion('');
			}
			$this->is_mobile = true;
			$this->browser = 'iPhone';
			return true;
		}
		return false;
	}


	private function checkBrowseriPad() {
		if (stripos($this->agent,'iPad') !== false ) {
			$aresult = explode('/',stristr($this->agent,'Version'));
			if( isset($aresult[1]) ) {
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion('');
			}
			$this->is_mobile = true;
			$this->browser = 'iPad';
			return true;
		}
		return false;
	}


	private function checkBrowseriPod() {
		if (stripos($this->agent,'iPod') !== false) {
			$aresult = explode('/',stristr($this->agent,'Version'));
			if (isset($aresult[1])) {
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion('');
			}
			$this->is_mobile = true;
			$this->browser = 'iPod';
			return true;
		}
		return false;
	}

	private function checkBrowserAndroid() {
		if (stripos($this->agent,'Android') !== false) {
			$aresult = explode(' ',stristr($this->agent,'Android'));
			if (isset($aresult[1]) ) {
				$aversion = explode(' ',$aresult[1]);
				$this->setVersion($aversion[0]);
			} else {
				$this->setVersion('');
			}
			$this->is_mobile = true;
			$this->browser = 'Android';
			return true;
		}
		return false;
	}


	/*****************************/
	/* DETERMINE USER'S PLATFORM */
	/*****************************/
	private function checkPlatform() {
		if (stripos($this->agent, 'windows') !== false) {
			$this->platform = 'Windows';
		} else if (stripos($this->agent, 'iPad') !== false) {
			$this->platform = 'iPad';
		} else if (stripos($this->agent, 'iPod') !== false) {
			$this->platform = 'iPod';
		} else if (stripos($this->agent, 'iPhone') !== false) {
			$this->platform = 'iPhone';
		} elseif (stripos($this->agent, 'mac') !== false) {
			$this->platform = 'Apple';
		} elseif (stripos($this->agent, 'android') !== false) {
			$this->platform = 'Android';
		} elseif (stripos($this->agent, 'linux') !== false) {
			$this->platform = 'Linux';
		} else if (stripos($this->agent, 'Nokia') !== false) {
			$this->platform = 'Nokia';
		} else if (stripos($this->agent, 'BlackBerry') !== false) {
			$this->platform = 'BlackBerry';
		} elseif (stripos($this->agent,'FreeBSD') !== false) {
			$this->platform = 'FreeBSD';
		} elseif (stripos($this->agent,'OpenBSD') !== false) {
			$this->platform = 'OpenBSD';
		} elseif (stripos($this->agent,'NetBSD') !== false) {
			$this->platform = 'NetBSD';
		} elseif (stripos($this->agent, 'OpenSolaris') !== false) {
			$this->platform = 'OpenSolaris';
		} elseif (stripos($this->agent, 'SunOS') !== false) {
			$this->platform = 'SunOS';
		} elseif (stripos($this->agent, 'OS\/2') !== false) {
			$this->platform = 'OS/2';
		} elseif (stripos($this->agent, 'BeOS') !== false) {
			$this->platform = 'BeOS';
		} elseif (stripos($this->agent, 'win') !== false) {
			$this->platform = 'Windows';
		}
	}


	/**************************************/
	/* GET OS AND VERSION FROM USER AGENT */
	/**************************************/
	private function checkOS () {
		$lowagent = strtolower($this->agent);
		$a_unix_types = array('dragonfly', 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix');
		$a_linux_distros = array('ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'jolicloud', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux');
		$a_os_types = array('android', 'blackberry', 'iphone', 'palmos', 'palmsource', 'symbian', 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix_types, $a_linux_distros);

		$i_count = count($a_os_types);
		for ($i = 0; $i < $i_count; $i++) {
			$os_working_data = $a_os_types[$i];
			if (!is_array($os_working_data) && strstr($lowagent, $os_working_data) && !strstr($lowagent, "linux")) {
				$os_working_type = $os_working_data;
				switch ($os_working_type) {
					case 'nt':
						$this->os = $os_working_type;
						if (strstr($lowagent, 'nt 6.2')) {
							$this->os_version = 6.2;
							$this->os_name = 'Windows 8';
						} elseif (strstr($lowagent, 'nt 6.1')) {
							$this->os_version = 6.1;
							$this->os_name = 'Windows 7';
						} elseif (strstr($lowagent, 'nt 6.0')) {
							$this->os_version = 6.0;
							$this->os_name = 'Windows Vista';
						} elseif (strstr($lowagent, 'nt 5.2')) {
							$this->os_version = 5.2;
							$this->os_name = 'Windows 2003';
						} elseif (strstr($lowagent, 'nt 5.1') || strstr($lowagent, 'xp')) {
							$this->os_version = 5.1;
							$this->os_name = 'Windows XP';
						} elseif (strstr($lowagent, 'nt 5') || strstr($lowagent, '2000')) {
							$this->os_version = 5.0;
							$this->os_name = 'Windows 2000';
						} elseif (strstr($lowagent, 'nt 4')) {
							$this->os_version = 4.0;
							$this->os_name = 'Windows NT 4';
						} elseif (strstr($lowagent, 'nt 3')) {
							$this->os_version = 3.0;
							$this->os_name = 'Windows NT 3';
						}
					break;
					case 'win':
						if (strstr($lowagent, 'vista')) {
							$this->os_version = 6.0;
							$this->os_name = 'Windows Vista';
							$this->os = 'nt';
						} elseif (strstr($lowagent, 'xp')) {
							$this->os_version = 5.1;
							$this->os_name = 'Windows XP';
							$this->os = 'nt';
						} elseif (strstr($lowagent, '2003')) {
							$this->os_version = 5.2;
							$this->os_name = 'Windows 2003';
							$this->os = 'nt';
						} elseif (strstr($lowagent, 'windows ce')) {
							$this->os_version = 'ce';
							$this->os_name = 'Windows CE';
							$this->os = 'nt';
						} elseif (strstr( $lowagent, '95')) {
							$this->os_version = 95;
							$this->os_name = 'Windows 95';
							$this->os = 'win';
						} elseif ((strstr($lowagent, '9x 4.9')) || (strstr($lowagent, ' me'))) {
							$this->os_version = 'me';
							$this->os_name = 'Windows Millenium';
							$this->os = 'win';
						} elseif (strstr( $lowagent, '98' ) ) {
							$this->os_version = 98;
							$this->os_name = 'Windows 98';
							$this->os = 'win';
						} elseif (strstr( $lowagent, '2000')) {
							$this->os_version = 5.0;
							$this->os_name = 'Windows 2000';
							$this->os = 'nt';
						} else {
							$this->os = 'win';
						}
					break;
					case 'mac':
						$this->os = 'mac';
						$this->os_name = 'MAC OS';
						if (strstr($lowagent, 'os x')) {
							$this->os_name = 'MAC OS X';
							$this->os_version = 10;
						}
					break;
					case 'iphone':
						$this->os = 'iphone';
						$this->os_name = 'iPhone';
						$this->os_version = 10;
					break;
					default:
						$this->os = $os_working_type;
					break;
				}
				break;
			} elseif (is_array($os_working_data) && ($i == ($i_count - 2))) {
				$j_count = count($os_working_data);
				for ($j = 0; $j < $j_count; $j++) {
					if (strstr($lowagent, $os_working_data[$j])) {
						$this->os = 'unix';
						$this->os_version = ( $os_working_data[$j] != 'unix' ) ? $os_working_data[$j] : '';
						$this->os_name = 'Unix';
						break;
					}
				}
			} elseif (is_array($os_working_data) && ($i == ($i_count - 1))) {
				$j_count = count($os_working_data);
				for ($j = 0; $j < $j_count; $j++) {
					if (strstr( $lowagent, $os_working_data[$j])) {
						$this->os = 'lin';
						$this->os_version = ( $os_working_data[$j] != 'linux' ) ? $os_working_data[$j] : '';
						$this->os_name = 'Linux';
						break;
					}
				}
			}
		}
	}

}

?>