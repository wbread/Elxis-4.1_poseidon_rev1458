<?php 
/**
* @version		$Id: php6.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @subpackage	Unicode support
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class eUTF extends elxisUTF8 {

	public function __construct() {
		parent::init();
	}

	static public function strlen($str) {
    	return strlen($str);
	}

	static public function strpos($str, $needle, $offset = 0) {
		return strpos($str, $needle, $offset);
	}

	static public function strrpos($str, $needle, $offset = 0) {
		return strrpos($str, $needle, $offset);
	}

	static public function substr($str, $offset, $length = 0) {
		return substr($str, $offset, $length);
	}

	static public function strtolower($str){
		return strtolower($str);
	}

	static public function strtoupper($str){
		return strtoupper($str);
	}

	static public function trim($str, $charlist = '') {
		return trim($str, $charlist);
	}

	static public function ltrim($str, $charlist = '') {
		return ltrim($str, $charlist);
	}

	static public function rtrim($str, $charlist = '') {
		return rtrim($str, $charlist);
	}

	static public function str_pad($input, $pad_length, $pad_string=' ', $pad_style=STR_PAD_RIGHT) {
		return str_pad($input, $pad_length, $pad_string, $pad_style);
	}

	static public function str_split($str, $split_len = 1) {
		return str_split($str, $split_len = 1);
	}

	static public function stristr($haystack, $needle, $part = false) {
		return stristr($haystack, $needle, $part);
	}

	static public function ucfirst($str) {
		return ucfirst($str);
	}

	static public function strrev($str) {
		return strrev($str);
	}

	static public function substr_replace($str, $repl, $start, $length = NULL) {
		substr_replace($str, $repl, $start, $length);
	}

    static public function str_replace($search, $replace, $subject, $count = NULL) {
    	return str_replace($search, $replace, $subject, $count);
    }

}

?>