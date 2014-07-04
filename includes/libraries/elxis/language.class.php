<?php 
/**
* @version		$Id: language.class.php 1355 2012-11-10 21:30:07Z datahell $
* @package		Elxis
* @subpackage	Language
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisLanguage {

	private $uri_lang = '';
	private $deflang = 'en';
	private $lang = 'en';
	private $locale = array('en_GB.utf8', 'en_GB.UTF-8', 'en_GB', 'en', 'english', 'england');
	private $ERROR_REPORT = 0;
	private $feeders = array();
	private $strings = array();
	private $errormsg = '';
	private $apc = 0;
	private $ilangs = array(); //installed languages
	private $sitelangs = array(); //languages available in frontend


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$eURI = eFactory::getURI();
		$elxis = eFactory::getElxis();

		$this->uri_lang = $eURI->getUriLang();
		$this->deflang = $elxis->getConfig('LANG');
		$this->ERROR_REPORT = $elxis->getConfig('ERROR_REPORT');
		$this->apc = $elxis->getConfig('APC');
		$this->setLangs();
		if (($this->uri_lang == '') && file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.php')) {
			$this->lang = $this->deflang;
		} elseif (($this->uri_lang != '') && file_exists(ELXIS_PATH.'/language/'.$this->uri_lang.'/'.$this->uri_lang.'.php')) {
			if (defined('ELXIS_ADMIN')) {
				$this->lang = $this->uri_lang;
			} else {
				if (in_array($this->uri_lang, $this->sitelangs)) {
					$this->lang = $this->uri_lang;
				} else {
					$this->lang = $this->deflang;
					$url = $elxis->getConfig('URL').'/';
					$elxis->redirect($url);
				}
			}
		} else {
			$this->lang = 'en';
		}

		$this->feed(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.php', true);
		$this->setLocale();
	}


	/*********************/
	/* GET LANGUAGE INFO */
	/*********************/
	public function getinfo($k) {
		$lng = $this->lang;
		if (isset($this->ilangs[$lng][$k])) {
			return $this->ilangs[$lng][$k];
		} else {
			if ($this->ERROR_REPORT > 1) {
				$this->errormsg = 'Could not get '.$k.' information for language '.$this->lang;
				trigger_error($this->errormsg, E_USER_WARNING);
			}
			return '';
		}
	}


	/***************************************************************************************/
	/* GET ALL INFO FOR THE CURRENT LANGUAGE, A SPECIFIC LANGUAGE OR AN ARRAY OF LANGUAGES */
	/***************************************************************************************/
	public function getallinfo($lng=null) {
		$info = array();
		if (is_null($lng)) {
			$lng == $this->lang;
			$info = $this->ilangs[$lng];
		} elseif (is_string($lng)) {
			if (($lng == '') || ($lng == $this->lang)) {
				$lng = $this->lang;
				$info = $this->ilangs[$lng];
			} else if (isset($this->ilangs[$lng])) {
				$info = $this->ilangs[$lng];
			} else {
				include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
				if (isset($langdb[$lng])) {
					$info = $langdb[$lng];
					$info['RTLSFX'] = ($info['DIR'] == 'rtl') ? '-rtl' : '';
				} else {
					if ($this->ERROR_REPORT > 2) {
						$this->errormsg = 'Language '.$lng.' does not exist in Elxis languages database.';
						trigger_error($this->errormsg, E_USER_NOTICE);
					}
				}
			}
		} elseif (is_array($lng)) {
			$allexist = true;
			foreach ($lng as $glossa) {
				if (isset($this->ilangs[$glossa])) {
					$info[$glossa] = $this->ilangs[$glossa];
				} else {
					$allexist = false;
				}
			}

			if ($allexist) { return $info; }

			include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
			foreach ($lng as $glossa) {
				if (isset($langdb[$glossa])) {
					$info[$glossa] = $langdb[$glossa];
					$info[$glossa]['RTLSFX'] = ($info[$glossa]['DIR'] == 'rtl') ? '-rtl' : '';
				} else {
					if ($this->ERROR_REPORT > 2) {
						$this->errormsg = 'Language '.$glossa.' does not exist in Elxis languages database.';
						trigger_error($this->errormsg, E_USER_NOTICE);
					}
				}
			}
		} else {
			return array();
		}

		return $info;
	}


	/***********************************/
	/* GET CURRENT LANGUAGE IDENTIFIER */
	/***********************************/
	public function currentLang() {
		return $this->lang;
	}


	/****************************************************************/
	/* GET URI LANGUAGE IDENTIFIER (EMPTY FOR THE DEFAULT LANGUAGE) */
	/****************************************************************/
	public function uriLang() {
		return $this->uri_lang;
	}


	/*************************************/
	/* CHECK IF A LANGUAGE STRING EXISTS */
	/*************************************/
	public function exist($k) {
		return isset($this->strings[$k]);
	}


	/***********************/
	/* SET TRANSLITERATION */
	/***********************/
	public function set($k, $value) {
		$k = trim($k);
		if ($k != '') {
			$this->strings[$k] = $value;
			return true;
		}
		return false;
	}


	/***********************/
	/* GET TRANSLITERATION */
	/***********************/
	public function get($k) {
		if (isset($this->strings[$k])) {
			return $this->strings[$k];
		} else {
			if ($this->ERROR_REPORT > 2) {
				$this->errormsg = 'Language string '.$k.' not found for language '.$this->lang;
				trigger_error($this->errormsg, E_USER_NOTICE);
			}
			return preg_replace('/\_/', ' ', ucfirst(strtolower($k)));
		}
	}


	/********************************************************/
	/* GET TRANSLITERATION WITHOUT NOTICES (MOSTLY FOR XML) */
	/********************************************************/
	public function silentGet($k, $uppercase=false) {
		$v = ($uppercase === true) ? trim(strtoupper($k)) : trim($k);
		if (($v != '') && isset($this->strings[$v])) {
			return $this->strings[$v];
		} else {
			return $k;
		}
	}


	/*****************************/
	/* MAGIC GET TRANSLITERATION */
	/*****************************/
	public function __get($name) {
		return $this->get($name);
	}


	/*************************************/
	/* GET ALL EXISTING TRANSLITERATIONS */
	/*************************************/
	public function getall() {
		ksort($this->strings);
		return $this->strings;
	}


	/*****************************************/
	/* GET NUMBER OF LOADED LANGUAGE STRINGS */
	/*****************************************/
	public function countStrings() {
		return count($this->strings);
	}


	/**********************/
	/* GET LOADED FEEDERS */
	/**********************/
	public function getFeeders() {
		return $this->feeders;
	}


	/**************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getError() {
		return $this->errormsg;
	}


	/****************************/
	/* RESET LAST ERROR MESSAGE */
	/****************************/
	public function resetError() {
		$this->errormsg = '';
	}


	/**************************************/
	/* FEED LANGUAGE FROM ELXIS EXTENSION */
	/**************************************/
	public function load($extension, $type='') {
		if ($extension == '') { return false; }
		if ($extension == 'exit') {
			$ok = $this->loadExitLanguage();
			return $ok;
		}
		if ($type == '') { $type = $this->extensionType($extension); }
		if ($type == '') {
			$this->errormsg = 'Could not determine extension type for '.$extension;
			return false;
		}

		switch ($type) {
			case 'component':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/'.$extension.'/language/'.$this->lang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/'.$extension.'/language/'.$this->lang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/'.$extension.'/language/'.$this->deflang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/'.$extension.'/language/'.$this->deflang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/'.$extension.'/language/en.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/'.$extension.'/language/en.'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for component '.$extension;
					return false;
				}
			break;
			case 'module':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/modules/'.$extension.'/language/'.$this->lang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/modules/'.$extension.'/language/'.$this->lang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/modules/'.$extension.'/language/'.$this->deflang.'.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/modules/'.$extension.'/language/'.$this->deflang.'.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/modules/'.$extension.'/language/en.'.$extension.'.php')) {
					$file = ELXIS_PATH.'/modules/'.$extension.'/language/en.'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for module '.$extension;
					return false;
				}
			break;
			case 'plugin':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.plugin_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/'.$this->lang.'.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/'.$this->lang.'.plugin_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.plugin_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/'.$this->deflang.'.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/'.$this->deflang.'.plugin_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.plugin_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/en.plugin_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_content/plugins/'.$extension.'/language/en.plugin_'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for plugin '.$extension;
					return false;
				}
			break;
			case 'engine':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.engine_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/'.$this->lang.'.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/'.$this->lang.'.engine_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.engine_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/'.$this->deflang.'.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/'.$this->deflang.'.engine_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.engine_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/en.engine_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_search/engines/'.$extension.'/language/en.engine_'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for search engine '.$extension;
					return false;
				}
			break;
			case 'auth':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.auth_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/'.$this->lang.'.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/'.$this->lang.'.auth_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.auth_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/'.$this->deflang.'.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/'.$this->deflang.'.auth_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.auth_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/en.auth_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/components/com_user/auth/'.$extension.'/language/en.auth_'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for authentication method '.$extension;
					return false;
				}
			break;
			case 'template':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/'.$extension.'/language/'.$this->lang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/'.$extension.'/language/'.$this->lang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/'.$extension.'/language/'.$this->deflang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/'.$extension.'/language/'.$this->deflang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/'.$extension.'/language/en.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/'.$extension.'/language/en.tpl_'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for template '.$extension;
					return false;
				}
			break;
			case 'atemplate':
				if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/admin/'.$extension.'/language/'.$this->lang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/admin/'.$extension.'/language/'.$this->lang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/admin/'.$extension.'/language/'.$this->deflang.'.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/admin/'.$extension.'/language/'.$this->deflang.'.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/language/en/en.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/language/en/en.tpl_'.$extension.'.php';
				} elseif (file_exists(ELXIS_PATH.'/templates/admin/'.$extension.'/language/en.tpl_'.$extension.'.php')) {
					$file = ELXIS_PATH.'/templates/admin/'.$extension.'/language/en.tpl_'.$extension.'.php';
				} else {
					$this->errormsg = 'Could not find a language file to load for administration template '.$extension;
					return false;
				}
			break;
			default:
				$this->errormsg = 'Could not determine extension type for '.$extension.' and therefor load a language file for it.';
				return false;
			break;
		}

		$this->feed($file);
		return true;
	}


	/****************************/
	/* LOAD EXIT PAGES LANGUAGE */
	/****************************/
	private function loadExitLanguage() {
		if (file_exists(ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.exit.php')) {
			$file = ELXIS_PATH.'/language/'.$this->lang.'/'.$this->lang.'.exit.php';
		} elseif (file_exists(ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.exit.php')) {
			$file = ELXIS_PATH.'/language/'.$this->deflang.'/'.$this->deflang.'.exit.php';
		} elseif (file_exists(ELXIS_PATH.'/language/en/en.exit.php')) {
			$file = ELXIS_PATH.'/language/en/en.exit.php';
		} else {
			return false;
		}
		$this->feed($file);
		return true;
	}


	/**********************************/
	/* FEED LANGUAGE FROM CUSTOM FILE */
	/**********************************/
	public function loadFile($file) {
		if ($file == '') { return false; }
		if (!file_exists($file)) { $this->errormsg = 'File '.$file.' does not exist!'; return false; }
		$this->feed($file);
		return true;
	}


	/***********************************************/
	/* LOCATE THE SOURCE FILE OF A LANGUAGE STRING */
	/***********************************************/
	public function locate($string='') {
		if ($string == '') { return ''; }
		$file = '';
		if ($this->feeders) {
			foreach ($this->feeders as $feeder) {
				include($feeder);
				if (isset($_lang)) {
					if (isset($_lang[$string])) {
						$file = $feeder;
						break;
					}
					unset($_lang);
				}				
			}
		}
		if ($file == '') { $this->errormsg = 'Could not locate '.$string.' source file.'; }
		return $file;
	}


	/*******************************************************/
	/* COMPARE 2 LANGUAGES FILES (USEFUL FOR TRANSLATIONS) */
	/*******************************************************/
	public function compare($file1, $file2) {
		if (!file_exists($file1)) { $this->errormsg = 'File '.$file1.' does not exist!'; return array(); }
		if (!file_exists($file2)) { $this->errormsg = 'File '.$file2.' does not exist!'; return array(); }

		$out = array();
		include($file1);
		if (isset($_lang)) {
			if (is_array($_lang) && (count($_lang) > 0)) {
				foreach ($_lang as $k => $v) { $out[$k] = array($v, ''); }
			}
			unset($_lang);
		}

		include($file2);
		if (isset($_lang)) {
			if (is_array($_lang) && (count($_lang) > 0)) {
				foreach ($_lang as $k => $v) {
					if (isset($out[$k])) {
						$out[$k][1] = $v;
					} else {
						$out[$k] = array('', $v);
					}
				}
			}
			unset($_lang);
		}
		return $out;
	}


	/*******************************************************************/
	/* COMPARE EXTENSION OR SYSTEM LANGUAGES (USEFUL FOR TRANSLATIONS) */
	/*******************************************************************/
	public function compareExtensions($lang1, $lang2, $extension='', $type='') {
		if (($lang1 == '') || (!file_exists(ELXIS_PATH.'/language/'.$lang1.'/'.$lang1.'.php'))) {
			$this->errormsg = 'Invalid language '.$lang1.'!'; return array();
		}
		if (($lang2 == '') || (!file_exists(ELXIS_PATH.'/language/'.$lang2.'/'.$lang2.'.php'))) {
			$this->errormsg = 'Invalid language '.$lang2.'!'; return array();
		}
		if ($extension == '') {
			$out = $this->compare(ELXIS_PATH.'/language/'.$lang1.'/'.$lang1.'.php', ELXIS_PATH.'/language/'.$lang2.'/'.$lang2.'.php');
			return $out;
		}
		if ($type == '') { $type = $this->extensionType($extension); }
		if ($type == '') {
			$this->errormsg = 'Could not determine the extension type!'; return array();
		}
		if (!in_array($type, array('component', 'module'))) {
			$this->errormsg = 'Only language files for components and modules can be compared!'; return array();
		}
		
		$folder = $type.'s';

		if (file_exists(ELXIS_PATH.'/language/'.$lang1.'/'.$lang1.'.'.$extension.'.php')) {
			$file1 = ELXIS_PATH.'/language/'.$lang1.'/'.$lang1.'.'.$extension.'.php';
		} elseif (file_exists(ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang1.'.'.$extension.'.php')) {
			$file1 = ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang1.'.'.$extension.'.php';
		} elseif (file_exists(ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang1.'.php')) {
			$file1 = ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang1.'.php';
		} else {
			$this->errormsg = 'Could not detect language file for language '.$lang1.'!'; return array();
		}
			
		if (file_exists(ELXIS_PATH.'/language/'.$lang2.'/'.$lang2.'.'.$extension.'.php')) {
			$file2 = ELXIS_PATH.'/language/'.$lang2.'/'.$lang2.'.'.$extension.'.php';
		} elseif (file_exists(ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang2.'.'.$extension.'.php')) {
			$file2 = ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang2.'.'.$extension.'.php';
		} elseif (file_exists(ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang2.'.php')) {
			$file2 = ELXIS_PATH.'/'.$folder.'/'.$extension.'/language/'.$lang2.'.php';
		} else {
			$this->errormsg = 'Could not detect language file for language '.$lang2.'!'; return array();
		}

		$out = $this->compare($file1, $file2);
		return $out;
	}


	/******************************/
	/* SWITCH LANGUAGE ON RUNTIME */
	/******************************/
	public function switchLanguage($newlang) {
		if (trim($newlang) == '') { return false; }
		if ($newlang == $this->lang) { return true; }
		if (count($this->feeders) == 0) { return true; }
		if (!defined('ELXIS_ADMIN') && !in_array($newlang, $this->sitelangs)) { return false; }
		if (!file_exists(ELXIS_PATH.'/language/'.$newlang.'/'.$newlang.'.php')) { return false; }
		foreach ($this->feeders as $key => $feedfile) {
			$newfile = str_replace('/'.$this->lang.'/', '/'.$newlang.'/', $feedfile);
			$newfile = str_replace('/'.$this->lang.'.', '/'.$newlang.'.', $newfile);
			if (file_exists($newfile)) {
				$this->feed($newfile);
				unset($this->feeders[$key]);
			}
		}
		$this->lang = $newlang;
		return true;
	}


	/********************************/
	/* FEED ME WITH A LANGUAGE FILE */
	/********************************/
	private function feed($file, $uploc=false) {
		if (!in_array($file, $this->feeders)) {
			include($file);
			if (isset($_lang) && is_array($_lang)) {
				foreach ($_lang as $k => $v) { $this->strings[$k] = $v; } //overwrite is allowed!
			} else {
				$this->errormsg = 'Feed file '.$file.' does not contain language strings information.';
			}
			if ($uploc) {
				if (isset($locale) && is_array($locale) && !empty($locale)) {
					$this->locale = $locale;
				} else {
					$this->errormsg = 'No locale information found on '.$file;
				}
			}
			$this->feeders[] = $file;
		}
	}


	/**********************************************/
	/* GET EXTENSION'S TYPE FROM EXTENSION'S NAME */
	/**********************************************/
	private function extensionType($extension) {
		$parts = preg_split('/\_/', $extension, 2, PREG_SPLIT_NO_EMPTY);
		switch ($parts[0]) {
			case 'com': $type = 'component'; break;
			case 'mod': $type = 'module'; break;
			//case 'plugin': $type = 'plugin'; break;
			//case 'tpl': $type = 'template'; break;
			//case 'auth': $type = 'auth'; break;
			//case 'engine': $type = 'engine'; break;
			default: $type = ''; break;
		}
		return $type;
	}


	/***********************************/
	/* FILTER LANGUAGE NAME (NOT USED) */
	/***********************************/
	private function filter($string) {
		$s = preg_replace('/[^a-zA-Z\-\_]/', '', $string);
		return $s;
	}


	/********************************************/
	/* GATHER INSTALLED AND SITE LANGUAGES INFO */
	/********************************************/
	private function setLangs() {
		if ($this->apc) {
			$ilangs = elxisAPC::fetch('ilangs', 'language');
			$slangs = elxisAPC::fetch('sitelangs', 'language');
			if ($ilangs && $slangs) {
				$this->ilangs = $ilangs;
				$this->sitelangs = $slangs;
				return;
			}
		}

		$lngs = eFactory::getFiles()->listFolders('language/');
		if (!$lngs) { trigger_error('No languages available!', E_USER_ERROR); }

		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		foreach ($lngs as $lng) {
			if (isset($langdb[$lng])) {
				$this->ilangs[$lng] = $langdb[$lng];
				$this->ilangs[$lng]['RTLSFX'] = ($langdb[$lng]['DIR'] == 'rtl') ? '-rtl' : '';
			}
		}

		if (!$this->ilangs) { trigger_error('No valid installed languages found!', E_USER_ERROR); }

		$slangs = eFactory::getElxis()->getConfig('SITELANGS');
		if ($slangs == '') {
			$this->sitelangs = array_keys($this->ilangs);
		} else {
			$this->sitelangs = explode(',',$slangs);
		}
		if (!in_array($this->deflang, $this->sitelangs)) { $this->sitelangs[] = $this->deflang; }

		if ($this->apc) {
			elxisAPC::store('ilangs', 'language', $this->ilangs, 14400);
			elxisAPC::store('sitelangs', 'language', $this->sitelangs, 14400);
		}
	}


	/*******************************/
	/* GET ALL INSTALLED LANGUAGES */
	/*******************************/
	public function getAllLangs($withinfo=false) {
		if (!$withinfo) {
			return array_keys($this->ilangs);
		}
		return $this->ilangs;
	}


	/*********************************/
	/* GET SITE (FRONTEND) LANGUAGES */
	/*********************************/
	public function getSiteLangs($withinfo=false) {
		if (!$withinfo) {
			return $this->sitelangs;
		}

		$arr = array();
		foreach ($this->sitelangs as $lng) {
			if (isset($this->ilangs[$lng])) { $arr[$lng] = $this->ilangs[$lng]; }
		}
		return $arr;
	}


	/*************************/
	/* SET ENVIROMENT LOCALE */
	/*************************/
	private function setLocale() {
		if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
			$loc = array ('en_GB.utf8', 'en_GB.utf-8', 'eng', 'english');
        	setlocale(LC_COLLATE, $loc);
        	setlocale(LC_CTYPE, $loc);
        	setlocale(LC_TIME, $loc);
        	return;
		}

		setlocale(LC_COLLATE, $this->locale);
		setlocale(LC_CTYPE, $this->locale);
        setlocale(LC_TIME, $this->locale);	
    }

}

?>