<?php 
/**
* @version		$Id: minifier.helper.php 1386 2013-02-17 10:47:51Z datahell $
* @package		Elxis
* @subpackage	Helpers / CSS & JS minifier
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisMinifierHelper {

	private $repo_path = '';
	private $hash = '';
	private $url = '';
	private $securl = '';
	private $excluded_links = array();
	public $remove_comments = true;
	public $remove_emptylines = true;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$this->url = $elxis->getConfig('URL').'/';
		$this->securl = str_replace('http:', 'https:', $this->url);
		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') {
			$this->repo_path = ELXIS_PATH.'/repository';
		}
	}


	/**********************/
	/* MINIFY GIVEN LINKS */
	/**********************/
	public function minify($links, $type='css', $only_get_contents=false) {
		$this->hash = '';
		$this->excluded_links = array();
		if (!is_array($links) || (count($links) == 0)) { return false; }
		$names = '';
		$rellinks = array();
		foreach ($links as $link) {
			if (strpos('.php', $link) !== false) {
				$this->excluded_links[] = $link;
			} else if (strpos($link, $this->url) !== false) {
				$rel_link = str_replace($this->url, '', $link);
				$names .= $rel_link;
				$rellinks[] = $rel_link;
			} else if (strpos($link, $this->securl) !== false) {
				$rel_link = str_replace($this->securl, '', $link);
				$names .= $rel_link;
				$rellinks[] = $rel_link;
			} else {
				$this->excluded_links[] = $link;
			}
		}

		if (!$rellinks) { return false; }
		$this->hash = md5($names);
		if (!$only_get_contents) {
			if (file_exists($this->repo_path.'/cache/minify/'.$this->hash.'.'.$type)) { return true; }
		}
		$contents = '';
		foreach ($rellinks as $lnk) {
			$contents .= file_get_contents(ELXIS_PATH.'/'.$lnk);
		}

		if ($this->remove_comments) {
			$contents = $this->removeComments($contents);
		}
		if ($this->remove_emptylines) {
			$contents = $this->removeEmptyLines($contents);
		}

		if ($only_get_contents) {
			return $contents;
		}

		$eFiles = eFactory::getFiles();
		if (!file_exists($this->repo_path.'/cache/minify/')) {
			$ok = $eFiles->createFolder('cache/minify/', 0777, true);
			if (!$ok) { return false; }
		}
		$filename = $this->hash.'.'.$type;
		$ok = $eFiles->createFile('cache/minify/'.$filename, $contents, true, true);

		return $ok;
	}


	/******************/
	/* GET HASH VALUE */
	/******************/
	public function getHash() {
		return $this->hash;
	}


	/**********************/
	/* GET EXCLUDED LINKS */
	/**********************/
	public function getExcluded() {
		return $this->excluded_links;
	}


	/***********************************/
	/* REMOVE COMMENTS FROM GIVEN TEXT */
	/***********************************/
	private function removeComments($txt) {
    	$txt = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $txt);
		return $txt;
	}


	/**************************************/
	/* REMOVE EMPTY LINES FROM GIVEN TEXT */
	/**************************************/
	private function removeEmptyLines($txt) {
		$txt = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $txt);
		return $txt;
	}

}

?>