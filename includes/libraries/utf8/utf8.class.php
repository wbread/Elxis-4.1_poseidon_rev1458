<?php 
/**
* @version		$Id: utf8.class.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @subpackage	Unicode support
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class elxisUTF8 {

	private static $method = 'native';


	/*****************************/
	/* SET METHOD AND INITIALIZE */
	/*****************************/
	static public function init() {
		if (version_compare(phpversion(), '6.0.0', ">=")) {
			self::$method = 'php6';
		} else if (extension_loaded('mbstring')) {
    		if (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING) {
				trigger_error('String functions are overloaded by mbstring',E_USER_ERROR);
			}
			mb_internal_encoding('UTF-8');
			self::$method = 'mbstring';
		} else {
			self::$method = 'native';
		}

		require_once(ELXIS_PATH.'/includes/libraries/utf8/'.self::$method.'.php');
	}


	/**************************************/
	/* GET METHOD USED FOR UTF-8 HANDLING */
	/**************************************/
	static public function getMethod() {
		return self::$method;
	}


	/********************************************/
	/* CHECK IF STRING CONTAINS 7bit ASCII ONLY */
	/********************************************/
	static public function isASCII($str) {
		for ($i=0; $i<strlen($str); $i++) {
			if (ord($str{$i}) >127) { return false; }
		}
		return true;
	}


	/****************************/
	/* CONVERT UTF-8 TO UNICODE */
	/****************************/
	static public function utf8_to_unicode($str) {
    	$mState = 0; // cached expected number of octets after the current octet until the beginning of the next UTF8 character sequence
    	$mUcs4  = 0; // cached Unicode character
    	$mBytes = 1; // cached expected number of octets in the current sequence
    	$out = array();
    	$len = strlen($str);
    	for($i = 0; $i < $len; $i++) {
        	$in = ord($str{$i});
        	if ($mState == 0) { //US-ASCII character or a multi-octet sequence.
            	if (0 == (0x80 & ($in))) { // US-ASCII, pass straight through.
                	$out[] = $in;
                	$mBytes = 1;
            	} else if (0xC0 == (0xE0 & ($in))) { //First octet of 2 octet sequence
                	$mUcs4 = ($in);
                	$mUcs4 = ($mUcs4 & 0x1F) << 6;
                	$mState = 1;
                	$mBytes = 2;
            	} else if (0xE0 == (0xF0 & ($in))) { //First octet of 3 octet sequence
                	$mUcs4 = ($in);
                	$mUcs4 = ($mUcs4 & 0x0F) << 12;
                	$mState = 2;
                	$mBytes = 3;
            	} else if (0xF0 == (0xF8 & ($in))) { //First octet of 4 octet sequence
                	$mUcs4 = ($in);
                	$mUcs4 = ($mUcs4 & 0x07) << 18;
                	$mState = 3;
                	$mBytes = 4;
				} else if (0xF8 == (0xFC & ($in))) { //First octet of 5 octet sequence.
                	$mUcs4 = ($in);
                	$mUcs4 = ($mUcs4 & 0x03) << 24;
                	$mState = 4;
                	$mBytes = 5;
				} else if (0xFC == (0xFE & ($in))) { //First octet of 6 octet sequence.
					$mUcs4 = ($in);
                	$mUcs4 = ($mUcs4 & 1) << 30;
                	$mState = 5;
                	$mBytes = 6;
				} else { //Current octet is neither in the US-ASCII range nor a legal first octet of a multi-octet sequence.
					trigger_error('utf8_to_unicode: Illegal sequence identifier in UTF-8 at byte '.$i, E_USER_WARNING);
					return false;
				}
			} else {
				if (0x80 == (0xC0 & ($in))) { //When mState is non-zero, we expect a continuation of the multi-octet sequence
                	//Legal continuation.
                	$shift = ($mState - 1) * 6;
                	$tmp = $in;
                	$tmp = ($tmp & 0x0000003F) << $shift;
                	$mUcs4 |= $tmp;
                	//End of the multi-octet sequence. mUcs4 now contains the final Unicode codepoint to be output
					if (0 == --$mState) {
                    	//Check for illegal sequences and codepoints.
                    	// From Unicode 3.1, non-shortest form is illegal
                    	if (((2 == $mBytes) && ($mUcs4 < 0x0080)) || ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                        	((4 == $mBytes) && ($mUcs4 < 0x10000)) || (4 < $mBytes) ||
                        	// From Unicode 3.2, surrogate characters are illegal
							(($mUcs4 & 0xFFFFF800) == 0xD800) ||
							// Codepoints outside the Unicode range are illegal
							($mUcs4 > 0x10FFFF)) {
							trigger_error('utf8_to_unicode: Illegal sequence or codepoint in UTF-8 at byte '.$i,E_USER_WARNING);
							return false;
						}
						if (0xFEFF != $mUcs4) { // BOM is legal but we don't want to output it
							$out[] = $mUcs4;
						}
						//initialize UTF8 cache
						$mState = 0;
                    	$mUcs4  = 0;
                    	$mBytes = 1;
                	}
				} else {
                	//((0xC0 & (*in) != 0x80) && (mState != 0)) Incomplete multi-octet sequence.
                	trigger_error('utf8_to_unicode: Incomplete multi-octet sequence in UTF-8 at byte '.$i, E_USER_WARNING);
                	return false;
				}
			}
		}
		return $out;
	}


	/****************************/
	/* CONVERT UNICODE TO UTF-8 */
	/****************************/
	static public function utf8_from_unicode($arr) {
		if (!is_array($arr)) { return ''; }
		$out = '';
		foreach (array_keys($arr) as $k) {
			if (($arr[$k] >= 0) && ($arr[$k] <= 0x007f)) { // ASCII range (including control chars)
            	$out .= chr($arr[$k]);
			} else if ($arr[$k] <= 0x07ff) { // 2 byte sequence
            	$out .= chr(0xc0 | ($arr[$k] >> 6));
            	$out .= chr(0x80 | ($arr[$k] & 0x003f));
        	} else if($arr[$k] == 0xFEFF) {// Byte order mark (skip)
			} else if ($arr[$k] >= 0xD800 && $arr[$k] <= 0xDFFF) { // Test for illegal surrogates
            	// found a surrogate
            	trigger_error('utf8_from_unicode: Illegal surrogate at index: '.$k.', value: '.$arr[$k], E_USER_WARNING);
            	return false;
        	} else if ($arr[$k] <= 0xffff) { // 3 byte sequence
            	$out .= chr(0xe0 | ($arr[$k] >> 12));
            	$out .= chr(0x80 | (($arr[$k] >> 6) & 0x003f));
            	$out .= chr(0x80 | ($arr[$k] & 0x003f));
        	} else if ($arr[$k] <= 0x10ffff) { // 4 byte sequence
            	$out .= chr(0xf0 | ($arr[$k] >> 18));
            	$out .= chr(0x80 | (($arr[$k] >> 12) & 0x3f));
            	$out .= chr(0x80 | (($arr[$k] >> 6) & 0x3f));
            	$out .= chr(0x80 | ($arr[$k] & 0x3f));
        	} else { // out of range
            	trigger_error('utf8_from_unicode: Codepoint out of Unicode range at index: '.$k.', value: '.$arr[$k],E_USER_WARNING);
				return false;
			}
		}
		return $out;
	}


	/***************************************/
	/* ASCII TRANSLITERATION OF UTF-8 TEXT */
	/***************************************/
	static public function utf8_to_ascii($string, $unknown = '?') {
		$string = preg_replace('/[\x00-\x08\x0b\x0c\x0e-\x1f]/', $unknown, $string);
		if (!preg_match('/[\x80-\xff]/', $string)) { return $string; }
		static $tailBytes;
		if (!isset($tailBytes)) {
			$tailBytes = array();
			for ($n = 0; $n < 256; $n++) {
				if ($n < 0xc0) {
					$remaining = 0; 
				} elseif ($n < 0xe0) {
					$remaining = 1;
				} elseif ($n < 0xf0) {
					$remaining = 2;
				} elseif ($n < 0xf8) {
					$remaining = 3;
				} elseif ($n < 0xfc) {
					$remaining = 4;
				} elseif ($n < 0xfe) {
					$remaining = 5;
				} else {
					$remaining = 0;
				}
				$tailBytes[chr($n)] = $remaining;
			}
		}
		preg_match_all('/[\x00-\x7f]+|[\x80-\xff][\x00-\x40\x5b-\x5f\x7b-\xff]*/', $string, $matches);
		$result = '';
		foreach ($matches[0] as $str) {
			if ($str{0} < "\x80") { $result .= $str; continue; }
			$head = '';
			$chunk = strlen($str);
			$len = $chunk + 1;
			for ($i = -1; --$len;) {
				$c = $str{++$i};
				if ($remaining = $tailBytes[$c]) {
					$sequence = $head = $c;
					do {
						if (--$len && ($c = $str{++$i}) >= "\x80" && $c < "\xc0") {
							$sequence .= $c;
						} else {
							if ($len == 0) {
								$result .= $unknown;
								break 2;
							} else {
								$result .= $unknown;
								--$i;
								++$len;
								continue 2;
							}
						}
					} while (--$remaining);

					$n = ord($head);
        			if ($n <= 0xdf) {
						$ord = ($n - 192) * 64 + (ord($sequence{1}) - 128);
					} else if ($n <= 0xef) {
						$ord = ($n - 224) * 4096 + (ord($sequence{1}) - 128) * 64 + (ord($sequence{2}) - 128);
					} else if ($n <= 0xf7) {
						$ord = ($n - 240) * 262144 + (ord($sequence{1}) - 128) * 4096 + (ord($sequence{2}) - 128) * 64 + (ord($sequence{3}) - 128);
					} else if ($n <= 0xfb) {
						$ord = ($n - 248) * 16777216 + (ord($sequence{1}) - 128) * 262144 + (ord($sequence{2}) - 128) * 4096 + (ord($sequence{3}) - 128) * 64 + (ord($sequence{4}) - 128);
					} else if ($n <= 0xfd) {
						$ord = ($n - 252) * 1073741824 + (ord($sequence{1}) - 128) * 16777216 + (ord($sequence{2}) - 128) * 262144 + (ord($sequence{3}) - 128) * 4096 + (ord($sequence{4}) - 128) * 64 + (ord($sequence{5}) - 128);
					}
					$result .= self::translit_replace($ord, $unknown);
					$head = '';
				} elseif ($c < "\x80") { // ASCII byte.
					$result .= $c;
					$head = '';
				} elseif ($c < "\xc0") { // Illegal tail bytes.
					if ($head == '') { $result .= $unknown; }
				} else {
					$result .= $unknown;
					$head = '';
				}
			}
		}
		return $result;
	}


	/************************************/
	/* LOOKUP AND REPLACE CHARS FROM DB */
	/************************************/
	static private function translit_replace($ord, $unknown = '?') {
		static $map = array(), $template = array();
		$bank = $ord >> 8;
		if (!isset($template[$bank])) {
			$file = ELXIS_PATH.'/includes/libraries/utf8/db/'.sprintf('x%02x', $bank).'.php';
			if (file_exists($file)) {
				$template[$bank] = include ($file);
			} else {
				$template[$bank] = array('en' => array());
			}
		}
		if (!isset($map[$bank]['en'])) {
			$map[$bank]['en'] = $template[$bank]['en'];
		}
		$ord = $ord & 255;
		return isset($map[$bank]['en'][$ord]) ? $map[$bank]['en'][$ord] : $unknown;
	}


}

?>