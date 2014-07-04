<?php 
/**
* @version		$Id: cache.class.php 1357 2012-11-11 11:35:03Z datahell $
* @package		Elxis
* @subpackage	Cache
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCache {

	private $time = 0;
	private $cache = 0;
	private $global_cachetime = 1800; //seconds
	private $cache_path = '';
	private $lang = 'en';
	private $uid = 0;
	private $name = '';
	private $item = null;
	private $errormsg = '';


	/*********************/
	/* MAGIC CONTSRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$this->time = time();
		$this->lang = eFactory::getLang()->currentLang();
		$this->cache = $elxis->getConfig('CACHE');
		$this->global_cachetime = $elxis->getConfig('CACHE_TIME');
		$this->uid = (int)$elxis->user()->uid;

		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$this->cache_path = $repo_path.'/cache';
	}


	/***********************/
	/* BEGIN A NEW PROCESS */
	/***********************/
	public function begin($element, $id, $group='', $cachetime=0, $mlang=true, $force=false, $extension='php') {
		$this->errormsg = '';
		$this->item = null;
		if (!$this->cache) {
			if (!$force) {
				$this->errormsg = 'Caching is disabled!';
				return 0;
			}
		}

		$group = trim($group);
		if ($group != '') {
			$group = '/'.$group;
			if (!is_dir($this->cache_path.$group.'/')) {
				if (!@mkdir($this->cache_path.$group.'/')) {
					$this->errormsg = 'Could not create required folder '.$this->cache_path.$group.'/';
					return -1;
				}
			}
		}

		if ($extension == '') { $extension = 'php'; }
		if (!in_array($extension, array('php', 'xml', 'txt', 'html', 'css', 'js', 'csv'))) { $extension = 'txt'; }

		if ($mlang == true) {
			$name = $element.'_'.$id.'_'.$this->lang.'.'.$extension;
		} else {
			$name = $element.'_'.$id.'.'.$extension;
		}
		if ($cachetime == 0) { $cachetime = $this->global_cachetime; }

		$this->item = new stdClass;
		$this->item->path = $this->cache_path.$group.'/'.$name;
		$this->item->cachetime = $cachetime;
		$this->item->state = 2;
		$this->item->extension = $extension;

		if (!file_exists($this->cache_path.$group.'/'.$name)) { return 2; }
		$ts = filemtime($this->cache_path.$group.'/'.$name);
		if ($this->time - $ts > $cachetime) { return 2; }
		$this->item->state = 1;
		return 1;
	}


	/********************/
	/* FETCH FROM CACHE */
	/********************/
	public function fetch() {
		if (!$this->item) { $this->errormsg = 'No cache item set. Please first use begin() method!'; return false; }
		if ($this->item->state !== 1) { $this->errormsg = 'Cache item is not in fetch state!'; return false; }

		include($this->item->path);

		if (($this->item->extension == 'php') && isset($head) && is_array($head) && (count($head) > 0)) {
			$eDoc = eFactory::getDocument();
			foreach ($head as $source) {
				$contents = stripslashes($source['contents']);
				switch ($source['type']) {
					case 'cssfile': $eDoc->addStyleLink($contents); break;
					case 'css': $eDoc->addStyle($contents); break;
					case 'jsfile': $eDoc->addScriptLink($contents); break;
					case 'js': $eDoc->addScript($contents); break;
					case 'jslibrary': 
						$libn = (isset($source['libn'])) ? stripslashes($source['libn']) : '';
						$libv = (isset($source['libv'])) ? stripslashes($source['libv']) : '';
						if ($libn == '') {
							$eDoc->addScriptLink($contents);
						} else {
							$eDoc->addLibrary($libn, $contents, $libv); 
						}
					break;
					case 'docready': $eDoc->addDocReady($contents); break;
					case 'custom': $eDoc->addCustom($contents); break;
					case 'title': $eDoc->setTitle($contents); break;
					case 'description': $eDoc->setDescription($contents); break;
					case 'keywords': $eDoc->setKeywords($contents); break;
					default: break;
				}
			}
		}

		$this->item = null;
		return true;
	}


	/******************************/
	/* FETCH CACHED ITEM CONTENTS */
	/******************************/
	public function fetchContents() {
		if (!$this->item) { $this->errormsg = 'No cache item set. Please first use begin() method!'; return false; }
		if ($this->item->state !== 1) { $this->errormsg = 'Cache item is not in fetch state!'; return false; }
		$c = file_get_contents($this->item->path);
		$this->item = null;
		return $c;
	}


	/***********************/
	/* STORE DATA IN CACHE */
	/***********************/
	public function store($data) {
		if (!$this->item) { $this->errormsg = 'No cache item set. Please first use begin() method!'; return false; }
		if ($this->item->state !== 2) { $this->errormsg = 'Cache item is not in store state!'; return false; }

		$observed = eFactory::getDocument()->getObserved();

		$contents = '';
		if ($this->item->extension == 'php') {
			$contents .= '<?php '."\n";
			$contents .= '//Elxis Cache file generated on '.gmdate('Y-m-d H:i:s').' GMT'."\n\n";
			$contents .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'."\n\n";
			if (count($observed) > 0) {
				$contents .= '$head = array('."\n";
				foreach ($observed as $obitem) {
					$contents .= 'array(\'type\' => \''.$obitem['type'].'\', \'contents\' => \''.addslashes($obitem['contents']).'\', \'libn\' => \''.addslashes($obitem['libn']).'\', \'libv\' => \''.addslashes($obitem['libv']).'\'),'."\n";
				}
				$contents .= ');'."\n";
			} else {
				$contents .= '$head = array();'."\n";
			}
			$contents .= '?>'."\n";
		}
		$contents .= $data;

		$f = @fopen($this->item->path, 'wb');
		if (!$f) {
			$this->item = null;
			$this->errormsg = 'Could not write on '.$this->item->path;
			return false;
		}

		$len = strlen($contents);
		@fwrite($f, $contents, $len);
		fclose($f);

		$this->item = null;
		return true;
	}


	/***************************************/
	/* CLEAR A CACHE FOLDER OR ALL FOLDERS */
	/***************************************/
	public function clear($group='') {
		$eFiles = eFactory::getFiles();

		if (trim($group) == '') {
			$dirs = $eFiles->listFolders('cache/', false, false, true);
			if (!$dirs) { return true; }
			foreach ($dirs as $dir) {
				$ok = $eFiles->deleteFolder('cache/'.$dir.'/', true);
			}
		} else {
			if (!is_dir($this->cache_path.'/'.$group.'/')) { return true; }
			$ok = $eFiles->deleteFolder('cache/'.$group.'/', true);
		}

		return $ok;
	}


	/****************************************/
	/* CLEAR SELECTIVE ITEMS WITHIN A GROUP */
	/****************************************/
	public function clearItems($group, $filter) {
		$eFiles = eFactory::getFiles();

		$filter = trim($filter);
		if ($filter == '')  { return false; }
		$reldir = (trim($group) == '') ? 'cache/' : 'cache/'.$group.'/';
		$files = $eFiles->listFiles($reldir, $filter, false, false, true);
		if (!$files) { return true; }
		$num = 0;
		foreach ($files as $file) {
			if ($file == 'index.html') { continue; }
			$ok = $eFiles->deleteFile($reldir.$file, true);
			if ($ok) { $num++; }
		}

		return $num;
	}


	/**************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getError() {
		return $this->errormsg;
	}

}

?>