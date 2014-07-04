<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Extensions Manager / XML parser
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extensionXML {

	private $xmlfile = null;
	private $head = null;
	private $dependencies = array();
	private $errormsg = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/********************/
	/* RESET EVERYTHING */
	/********************/
	private function reset() {
		$this->xmlfile = null;
		$this->head = new stdClass;
		$this->dependencies = array();
		$this->errormsg = '';
	}


	/*****************************/
	/* READ EXTENSION'S XML FILE */
	/*****************************/
	public function parse($xmlfile, $dependencies=false) {
		$this->reset();

		$xmlfile = trim($xmlfile);
		if (($xmlfile == '') || !file_exists($xmlfile) || !is_file($xmlfile)) {
			$this->errormsg = 'XML file not found!';
			return false;
		}

		$extension = strtolower(substr(strrchr($xmlfile, '.'), 1));
		if ($extension != 'xml') {
			$this->errormsg = 'Given file is not an XML file!';
			return false;
		}

		libxml_use_internal_errors(true);
		$xmlDoc = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$xmlDoc) {
			foreach (libxml_get_errors() as $error) {
				$this->errormsg = 'Could not parse XML file. Error: '.$error->message.'. Line: '.$error->line;
				break;
			}
			return false;
    	}

		if ($xmlDoc->getName() != 'package') {
			$this->errormsg = 'The XML file is not a valid Elxis extension XML!';
			return false;
		}

		$type = '';
		$section = 'frontend';
		$attrs = $xmlDoc->attributes();
		if ($attrs) {
			if (isset($attrs['type'])) { $type = (string)$attrs['type']; }
			if (isset($attrs['section'])) { $section = (string)$attrs['section']; }
		}
		if (($type == '') || !in_array($type, array('component', 'module', 'template', 'engine', 'auth', 'plugin'))) {
			$this->errormsg = 'The type attribute in the XML file is either missing or incorrect!';
			return false;
		}
		if ($section != 'backend') { $section = 'frontend'; }

		$this->head->type = $type;
		$this->head->section = $section;

		$req_elements = array('name', 'title', 'created', 'author', 'version');
		foreach ($req_elements as $req_element) {
			if (!isset($xmlDoc->$req_element)) {
				$this->errormsg = ucfirst($req_element).' node was not found in the XML file!';
				return false;
			}

			$v = (string)$xmlDoc->$req_element;
			$v = trim($v);
			if ($v == '') {
				$this->errormsg = ucfirst($req_element).' node can not be empty in the XML file!';
				return false;
			}

			if ($req_element == 'name') {
				switch ($this->head->type) {
					case 'component': $prf = 'com_'; break;
					case 'module': $prf = 'mod_'; break;
					case 'template': case 'engine': case 'auth': case 'plugin': default: $prf = ''; break;
				}

				if (($prf != '') && (strpos($v, $prf) !== 0)) {
					$this->errormsg = 'A '.ucfirst($this->head->type).' name should start with '.$prf.'!';
					return false;
				}
			} else if ($req_element == 'created') {
				if (strlen($v) == 10) { $v = $v .= ' 00:00:00'; }
				if (strlen($v) != 19) { $v = gmdate('Y-m-d H:i:s'); }
			} else if ($req_element == 'version') {
				if (!is_numeric($v)) {
					$this->errormsg = ucfirst($req_element).' node in the XML file should have a numeric value!';
					return false;
				}

				if ($v <= 0) {
					$this->errormsg = ucfirst($req_element).' node in the XML file should have a positive float value!';
					return false;
				}
			}

			$this->head->$req_element = $v;
		}

		$other_elements = array('authoremail', 'authorurl',  'link', 'copyright', 'license', 'licenseurl', 'description');
		foreach ($other_elements as $other_element) {
			if (!isset($xmlDoc->$other_element)) {
				$this->head->$other_element = null;
				continue;
			}

			$v = (string)$xmlDoc->$other_element;
			$v = trim($v);
			$this->head->$other_element = $v;
		}

		if ($dependencies) {
			$this->dependencies = $this->parseDependencies($xmlDoc);
		}

		return true;
	}


	/**********************************/
	/* PARSE EXTENSION'S DEPENDENCIES */
	/**********************************/
	private function parseDependencies($xmlDoc) {
		$dependencies = array();
		if (!isset($xmlDoc->dependencies)) { return $dependencies; }
		if (!$xmlDoc->dependencies->children()) { return $dependencies; }
		foreach ($xmlDoc->dependencies->children() as $dependency) {
			$extension = (string)$dependency;
			$extension = trim(strtolower($extension));
			if ($extension == '') { continue; }
			$attrs = $dependency->attributes();
			if (!isset($attrs['type'])) { continue; }
			if (!isset($attrs['version'])) { continue; }
			$type = (string)$attrs['type'];
			$version = (string)$attrs['version'];
			if ($version == '') { continue; }

			$versions = explode(',', $version);

			switch ($type) {
				case 'component':
					$extension = preg_replace('/^(com\_)/', '', $extension);
				break;
				case 'module':
					$extension = preg_replace('/^(mod\_)/', '', $extension);
				break;
				default: break;
			}

			$dpc = new stdClass;
			$dpc->type = strtolower($type);
			$dpc->versions = $versions;
			$dpc->extension = $extension;
			$dpc->installed = false;
			$dpc->iversion = 0;
			$dpc->icompatible = false;
			$dependencies[] = $dpc;
			unset($dpc);
		}
		return $dependencies;
	}


	/********************************************/
	/* CHECK DEPENDENCIES EXISTANCE AND VERSION */
	/********************************************/
	public function checkDependencies() {
		if (!$this->dependencies) { return; }
		foreach ($this->dependencies as $k => $dpc) {
			if ($dpc->type == 'core') {
				$this->dependencies[$k]->installed = true;
				if ($dpc->extension == 'elxis') {
					$this->dependencies[$k]->iversion = eFactory::getElxis()->getVersion();
					$this->dependencies[$k]->icompatible = $this->isCompatible($this->dependencies[$k]->iversion, $dpc->versions);
				}
			} else if (in_array($dpc->type, array('component', 'module', 'template', 'atemplate', 'engine', 'auth', 'plugin'))) {
				$info = $this->quickXML($dpc->type, $dpc->extension);
				$this->dependencies[$k]->installed = $info['installed'];
				$this->dependencies[$k]->iversion = $info['version'];
				$this->dependencies[$k]->icompatible = $this->isCompatible($this->dependencies[$k]->iversion, $dpc->versions);
			}
		}
	}


	/***********************************************************************/
	/* GET QUICKLY AN EXTENSION'S VERSION AND SOME MORE INFO FROM XML FILE */
	/***********************************************************************/
	public function quickXML($type, $name) {
		$info = array('installed' => false, 'version' => 0, 'title' => '', 'created' => '', 'author'=> '', 'authorurl' => '', 'section' => 'frontend');
		$xmlfile = '';
		if ($type == 'component') {
			$name = preg_replace('/^(com\_)/', '', $name);
			if (file_exists(ELXIS_PATH.'/components/com_'.$name.'/'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/components/com_'.$name.'/'.$name.'.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'module') {
			$name = preg_replace('/^(mod\_)/', '', $name);
			if (file_exists(ELXIS_PATH.'/modules/mod_'.$name.'/mod_'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/modules/mod_'.$name.'/mod_'.$name.'.xml';
				$info['installed'] = true;
			} else if (file_exists(ELXIS_PATH.'/modules/mod_'.$name.'/'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/modules/mod_'.$name.'/'.$name.'.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'atemplate') {
			if (file_exists(ELXIS_PATH.'/templates/admin/'.$name.'/'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/templates/admin/'.$name.'/'.$name.'.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'template') {
			if (file_exists(ELXIS_PATH.'/templates/'.$name.'/'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/templates/'.$name.'/'.$name.'.xml';
				$info['installed'] = true;
			} else if (file_exists(ELXIS_PATH.'/templates/admin/'.$name.'/'.$name.'.xml')) {
				$xmlfile = ELXIS_PATH.'/templates/admin/'.$name.'/'.$name.'.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'engine') {
			if (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$name.'/'.$name.'.engine.xml')) {
				$xmlfile = ELXIS_PATH.'/components/com_search/engines/'.$name.'/'.$name.'.engine.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'auth') {
			if (file_exists(ELXIS_PATH.'/components/com_user/auth/'.$name.'/'.$name.'.auth.xml')) {
				$xmlfile = ELXIS_PATH.'/components/com_user/auth/'.$name.'/'.$name.'.auth.xml';
				$info['installed'] = true;
			}
		} else if ($type == 'plugin') {
			if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$name.'/'.$name.'.plugin.xml')) {
				$xmlfile = ELXIS_PATH.'/components/com_content/plugins/'.$name.'/'.$name.'.plugin.xml';
				$info['installed'] = true;
			}
		}

		if ($xmlfile == '') { return $info; }
		libxml_use_internal_errors(true);
		$xmlDoc = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$xmlDoc) { return $info; }
		if ($xmlDoc->getName() != 'package') { return $info; }
		$attrs = $xmlDoc->attributes();
		if ($attrs) {
			if (isset($attrs['section'])) { $info['section'] = (string)$attrs['section']; }
		}
		if (!isset($xmlDoc->version)) { return $info; }
		$v = (string)$xmlDoc->version;
		$info['version'] = trim($v);
		if (isset($xmlDoc->title)) { $info['title'] = (string)$xmlDoc->title; }
		if (isset($xmlDoc->created)) { $info['created'] = (string)$xmlDoc->created; }
		if (isset($xmlDoc->author)) { $info['author'] = (string)$xmlDoc->author; }
		if (isset($xmlDoc->authorurl)) { $info['authorurl'] = (string)$xmlDoc->authorurl; }
		unset($xmlDoc);
		return $info;
	}


	/*********************************************************************/
	/* CHECK VERSION COMPATIBILITY (format: 4.x or 4.x.y where x,y: 0-9) */
	/*********************************************************************/
	public function isCompatible($iversion, $versions) {
		foreach ($versions as $version) {
			if ($iversion == $version) {
				return true;
			} else if (preg_match('/(x)$/', $version)) {
				$av = array();
				for ($i=0; $i<10; $i++) {
					$av[] = str_replace('x', $i, $version);
				}
				if (in_array($iversion, $av)) { return true; }
			} else if (preg_match('/(\+)$/', $version)) {
				$v = str_replace('+', '', $version);
				$parts = explode('.', $v);
				$last = count($parts) - 1;
				if ($last == 0) {
					if (version_compare($iversion, $v, '>=') === true) {
						return true;
					} else {
						break;
					}
				}

				$first = '';
				foreach ($parts as $k => $part) {
					if ($k == $last) { break; }
					$first .= $part.'.';
				}

				$av = array();
				$num = (int)$parts[$last];
				for ($i=$num; $i<10; $i++) { $av[] = $first.$i; }
				if (in_array($iversion, $av)) { return true; }
			} else {
				//do nothing
			}
		}
		return false;
	}


	/*****************/
	/* GET HEAD DATA */
	/*****************/
	public function getHead() {
		return $this->head;
	}


	/********************/
	/* GET DEPENDENCIES */
	/********************/
	public function getDependencies() {
		return $this->dependencies;
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getErrorMsg() {
		return $this->errormsg;
	}
	
}

?>