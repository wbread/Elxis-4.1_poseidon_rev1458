<?php 
/**
* @version		$Id: mbstring.php 731 2011-11-10 21:25:23Z datahell $
* @package		Elxis
* @subpackage	Unicode support
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class eUTF extends elxisUTF8 {

	static public function strlen($str) {
    	return mb_strlen($str, 'UTF-8');
	}


	static public function strpos($str, $search, $offset = 0) {
    	if ($offset === 0) {
        	return mb_strpos($str, $search);
    	} else {
        	return mb_strpos($str, $search, $offset, 'UTF-8');
    	}
	}


	static public function strrpos($str, $search, $offset = 0) {
    	if ($offset === 0) {
        	if (empty($str)) { return false; }
        	return mb_strrpos($str, $search);
    	} else {
        	if (!is_int($offset)) {
            	trigger_error('strrpos expects parameter 3 to be long',E_USER_WARNING);
            	return false;
        	}
        	$str = mb_substr($str, $offset);
        	if (false !== ($pos = mb_strrpos($str, $search))) {
            	return $pos + $offset;
        	}
        	return false;
    	}
	}


	static public function substr($str, $offset, $length = 0) {
		if ($length === 0) {
			return mb_substr($str, $offset);
		} else {
			return mb_substr($str, $offset, $length, 'UTF-8');
		}
	}


	static public function strtolower($str){
		return mb_strtolower($str, 'UTF-8');
	}


	static public function strtoupper($str){
		return mb_strtoupper($str, 'UTF-8');
	}


	static public function ltrim($str, $charlist='') {
		if (trim($charlist) != '') {
			return ltrim($str, $charlist);
		} else {
			return preg_replace("#(^\s+)#us", "", $str);
		}
	}


	static public function rtrim($str, $charlist='') {
		if (trim($charlist) != '') {
			return rtrim($str, $charlist);
		} else {
			return preg_replace("#(\s+$)#us", "", $str);
		}
	}


	static public function trim($str, $charlist='') {
		if (trim($charlist) != '') {
			return trim($str, $charlist);
		} else {
			return preg_replace("#(^\s+)|(\s+$)#us", "", $str);
		}
	}


	static public function str_pad($input, $pad_length, $pad_string=' ', $pad_style=STR_PAD_RIGHT) {
		return str_pad($input, strlen($input)-mb_strlen($input,'UTF-8')+$pad_length, $pad_string, $pad_style);
	}


    static public function str_split($string, $split_length = 1) {
        mb_regex_encoding('UTF-8');
        $split_length = ($split_length <= 0) ? 1 : $split_length;
        $mb_strlen = mb_strlen($string, 'UTF-8');
        $array = array();
        for($i = 0; $i < $mb_strlen; $i + $split_length) {
            $array[] = mb_substr($string, $i, $split_length, 'UTF-8');
        }
        return $array;
    }


	static public function stristr($haystack, $needle, $part = false) {
		return mb_stristr($haystack, $needle, $part, 'UTF-8');
	}


	static public function ucfirst($str) {
		switch (mb_strlen($str, 'UTF-8')) {
			case 0: return ''; break;
			case 1:
            	return mb_strtoupper($str, 'UTF-8');
        	break;
        	default:
            	preg_match('/^(.{1})(.*)$/us', $str, $matches);
            	return mb_strtoupper($matches[1], 'UTF-8').$matches[2];
        	break;
		}
	}


	static public function strrev($str) {
		preg_match_all('/./us', $str, $ar);
		return join('',array_reverse($ar[0]));
	}


	static public function substr_replace($str, $repl, $start, $length = NULL) {
		preg_match_all('/./us', $str, $ar);
		preg_match_all('/./us', $repl, $rar);
		if ($length === NULL) { $length = self::strlen($str); }
		array_splice( $ar[0], $start, $length, $rar[0] );
		return join('',$ar[0]);
    } 


    static public function str_replace($search, $replace, $subject, $count = NULL) {
        if (is_array($subject)) {
            $ret = array();
            foreach($subject as $key => $val) {
                $ret[$key] = self::str_replace($search, $replace, $val);
            }
            return $ret;
        }
        foreach((array) $search as $key => $s) {
            if ($s == '') { continue; }
            $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
            $pos = mb_strpos($subject, $s);
            while($pos !== false) {
                $subject = mb_substr($subject, 0, $pos) . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'));
                $pos = mb_strpos($subject, $s, $pos + mb_strlen($r), 'UTF-8');
            }
        }
        return $subject;
    }
    
}

?>