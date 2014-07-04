<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / META keywords
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisKeywordsHelper {

	private $maxkeywords = 15;
	private $minlength = 4;
	private $exclude = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/*****************************************/
	/* ANALYZE TEXT AND RETURN META KEYWORDS */
	/*****************************************/
	public function getKeywords($text, $maxkeywords=15, $minlength=4, $lang='', $return_with_density=false) {
		$this->maxkeywords = (int)$maxkeywords;
		if ($this->maxkeywords < 1) { $this->maxkeywords = 15; }
		$this->minlength = (int)$minlength;
		if ($this->minlength < 3) { $this->minlength = 4; }
		$this->setExcludeWords($lang);

    	$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    	$text = strip_tags($text);
        $text = preg_replace('/\s\s+/', ' ', $text);
		$text = filter_var($text, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        $text = str_replace (array('–', '(', ')', '+', ':', '.', '?', '!', '_', '*', '-', '"', '\'', '@', '#', '$', '%', '&', '[',']', '{', '}', '<', '>', ';'), '', $text); 
		$text = trim($text); 
        $text = eUTF::strtolower($text);

        $keywords = $this->calculate($text);
		if (!$keywords) { return array(); }

        if ($return_with_density) {
        	return $keywords;
       	} else {
       		$final = array();
       		foreach ($keywords as $key => $val) {
       			$final[] = $key;
			}
			return $final;
		}
	}


	/****************************/
	/* SET WORDS TO BE EXCLUDED */
	/****************************/
	private function setExcludeWords($lang) {
		if (trim($lang) == '') { $lang = eFactory::getElxis()->currentLang(); }
		$global_exclude = array(
			'add', 'after', 'all', 'also', 'and', 'any', 'are', 'best', 'big', 'box', 'but', 'bug', 
			'close', 'did', 'does', 'else', 'end', 'each', 'enter', 'for', 'from', 'good', 'has', 
			'how', 'not', 'now', 'off', 'the', 'then', 'img', 'quot', 'copy', 'table', 'strong', 'yes'
		);

		if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/helpers/keywords/'.$lang.'.php')) { $lang = 'en'; }
		include(ELXIS_PATH.'/includes/libraries/elxis/helpers/keywords/'.$lang.'.php');
		if (isset($exclude) && is_array($exclude)) {
			$this->exclude = array_merge($global_exclude, $exclude);
		} else {
			$this->exclude = $global_exclude;
		}
	}


	/***************************/
	/* CALCULATE META KEYWORDS */
	/***************************/
	private function calculate($text) {
        $arr = explode(' ', $text);
        if (!$arr) { return array(); }
        unset($text);
		$keywords = array();
        foreach ($arr as $str) {
        	$str = trim($str);
			if ((eUTF::strlen($str) >= $this->minlength) && !in_array($str, $this->exclude)) {
            	if (isset($keywords[$str])) {
                	$keywords[$str]++;
            	} else {
                	$keywords[$str] = 1;
            	}
			}
       	}

		if (!$keywords) { return array(); }
		arsort($keywords, SORT_NUMERIC);
		return array_slice($keywords, 0, $this->maxkeywords, true);
    }

}

?>