<?php 
/**
* @version		$Id: menu.xml.php 861 2012-01-22 13:16:15Z datahell $
* @package		Elxis
* @subpackage	Menu/XML
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisXMLMenu {

	private $elxis_menu = null;
	private $lang = 'en';
	private $errormsg = '';
	private $component = '';
	private $rootLink = '';
	private $menuLang = 'en';
	private $menuTitle = 'Unknown';
	private $menu = null;
	private $mygid = 7;
	private $mylevel = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($elxis_menu) {
		$this->elxis_menu = $elxis_menu;
		$this->lang = eFactory::getLang()->currentLang();
		$this->mygid = (int)eFactory::getElxis()->user()->gid;
		$this->mylevel = (int)eFactory::getElxis()->acl()->getLevel();
	}


	/******************************************/
	/* GENERATE ELXIS MENU FROM XML MENU FILE */
	/******************************************/
	public function getMenu($parent_id, $component, $root_link, $expand=0, $section='frontend', $menu_name='default', $lang='') {
		$expand = (int)$expand;
		if (($expand < 1) || ($expand > 2)) { return false; }
		$this->component = $component;
		if ($lang == '') { $lang = $this->lang; }
		$this->menuLang = $lang;
		$this->rootLink = $root_link;

		$this->menu = $this->parseMenu($section, $menu_name);
		if (!$this->menu) { return false; }

		if (($this->rootLink == '') || ($this->rootLink == '/') || ($this->rootLink == $this->component.':/')) {
			if ($expand === 1) {
				return $this->parseRootNodes($menu_name, $parent_id);
			} else {
				return $this->parseRootAll($menu_name, $parent_id);
			}
		} else {
			if ($expand === 1) {
				return $this->parseNodeRootNodes($menu_name, $parent_id, $root_link);
			} else {
				return $this->parseNodeAllNodes($menu_name, $parent_id, $root_link);
			}
		}
	}


	/****************************/
	/* PARSE ROOT ELEMENT NODES */
	/****************************/
	private function parseRootNodes($menu_name, $parent_id) {
		if (!isset($this->menu->items)) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$xml_items = $this->menu->items->children();
		if (!$xml_items) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$items = array();
		$n = 1;
		foreach ($xml_items as $xml_item) {
			$attrs = $xml_item->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
			if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
			$title = $this->getTitleXML($xml_item, $this->menuLang);
			$link = (string)$attrs['link'];
			$params = $this->makeParams($attrs, $parent_id, $n, 0);
			$idx = $this->elxis_menu->newIndex();
			$n++;

			$items[$idx] = $this->elxis_menu->makeItem($title, $link, $idx, $params);
			unset($link, $title, $idx, $params, $attrs);
		}
		return $items;
	}


	/*********************************/
	/* PARSE ALL ROOT ELEMENTS NODES */
	/*********************************/
	private function parseRootAll($menu_name, $parent_id) {
		if (!isset($this->menu->items)) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$xml_items = $this->menu->items->children();
		if (!$xml_items) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$items = array();
		$n = 1;
		foreach ($xml_items as $xml_item) {
			$attrs = $xml_item->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
			if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
			$title = $this->getTitleXML($xml_item, $this->menuLang);
			$link = (string)$attrs['link'];
			$params = $this->makeParams($attrs, $parent_id, $n, 0);
			$idx = $this->elxis_menu->newIndex();
			$n++;

			$items[$idx] = $this->elxis_menu->makeItem($title, $link, $idx, $params);
			unset($link, $title, $params, $attrs);

			if (isset($xml_item->subitems) && (count($xml_item->subitems->children()) > 0)) { //1st level
				$m = 1;
				foreach ($xml_item->subitems->children() as $lev1node) {
					$attrs = $lev1node->attributes();
					if (!$attrs) { continue; }
					if (!isset($attrs['link'])) { continue; }
					if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
					if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
					$title = $this->getTitleXML($lev1node, $this->menuLang);
					$link = (string)$attrs['link'];
					$params = $this->makeParams($attrs, $idx, $m, 0);
					$idx2 = $this->elxis_menu->newIndex();
					$items[$idx]->children[$idx2] = $this->elxis_menu->makeItem($title, $link, $idx2, $params);
					unset($link, $title, $params, $attrs);					
					$m++;

					if (isset($lev1node->subitems) && (count($lev1node->subitems->children()) > 0)) { //2nd level
						$g = 1;
						foreach ($lev1node->subitems->children() as $lev2node) {
							$attrs = $lev2node->attributes();
							if (!$attrs) { continue; }
							if (!isset($attrs['link'])) { continue; }
							if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
							if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
							$title = $this->getTitleXML($lev2node, $this->menuLang);
							$link = (string)$attrs['link'];
							$params = $this->makeParams($attrs, $idx2, $g, 0);
							$idx3 = $this->elxis_menu->newIndex();
							$items[$idx]->children[$idx2]->children[$idx3] = $this->elxis_menu->makeItem($title, $link, $idx3, $params);
							unset($link, $title, $params, $attrs);					
							$g++;

							if (isset($lev2node->subitems) && (count($lev2node->subitems->children()) > 0)) { //3rd level
								$z = 1;
								foreach ($lev2node->subitems->children() as $lev3node) {
									$attrs = $lev3node->attributes();
									if (!$attrs) { continue; }
									if (!isset($attrs['link'])) { continue; }
									if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
									if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
									$title = $this->getTitleXML($lev3node, $this->menuLang);
									$link = (string)$attrs['link'];
									$params = $this->makeParams($attrs, $idx3, $z, 0);
									$idx4 = $this->elxis_menu->newIndex();
									$items[$idx]->children[$idx2]->children[$idx3]->children[$idx4] = $this->elxis_menu->makeItem($title, $link, $idx4, $params);
									unset($link, $title, $params, $attrs);					
									$z++;

									if (isset($lev3node->subitems) && (count($lev3node->subitems->children()) > 0)) { //4th level
										$v = 1;
										foreach ($lev3node->subitems->children() as $lev4node) {
											$attrs = $lev4node->attributes();
											if (!$attrs) { continue; }
											if (!isset($attrs['link'])) { continue; }
											if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
											if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
											$title = $this->getTitleXML($lev4node, $this->menuLang);
											$link = (string)$attrs['link'];
											$params = $this->makeParams($attrs, $idx4, $v, 0);
											$idx5 = $this->elxis_menu->newIndex();
											$items[$idx]->children[$idx2]->children[$idx3]->children[$idx4]->children[$idx5] = $this->elxis_menu->makeItem($title, $link, $idx5, $params);
											unset($link, $title, $params, $attrs);					
											$v++;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $items;
	}


	/*************************/
	/* PARSE NODE ROOT NODES */
	/*************************/
	private function parseNodeRootNodes($menu_name, $parent_id, $root_link) {
		if (!isset($this->menu->items)) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$xml_items = $this->menu->items->children();
		if (!$xml_items) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$node = $this->searchNode($xml_items, $root_link);
		if ($node === null) {
			$this->errormsg = 'Link '.$root_link.' not found in XML menu tree!';
			return false;
		}
		
		if (!isset($node->subitems) || (count($node->subitems->children()) == 0)) {
			return false;
		}

		$items = array();
		$n = 1;
		foreach ($node->subitems->children() as $subnode) {
			$attrs = $subnode->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
			if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
			$title = $this->getTitleXML($subnode, $this->menuLang);
			$link = (string)$attrs['link'];
			$params = $this->makeParams($attrs, $parent_id, $n, 0);
			$idx = $this->elxis_menu->newIndex();
			$n++;

			$items[$idx] = $this->elxis_menu->makeItem($title, $link, $idx, $params);
			unset($link, $title, $idx, $params, $attrs);
		}

		return $items;
	}


	/*************************/
	/* PARSE NODE ALL NODES */
	/*************************/
	private function parseNodeAllNodes($menu_name, $parent_id, $root_link) {
		if (!isset($this->menu->items)) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$xml_items = $this->menu->items->children();
		if (!$xml_items) {
			$this->errormsg = 'Requested menu '.$menu_name.' has no menu items!';
			return false;
		}

		$node = $this->searchNode($xml_items, $root_link);
		if ($node === null) {
			$this->errormsg = 'Link '.$root_link.' not found in XML menu tree!';
			return false;
		}

		if (!isset($node->subitems) || (count($node->subitems->children()) == 0)) {
			return false;
		}

		$items = array();
		$n = 1;
		foreach ($node->subitems->children() as $subnode) { //root level
			$attrs = $subnode->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
			if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
			$title = $this->getTitleXML($subnode, $this->menuLang);
			$link = (string)$attrs['link'];
			$params = $this->makeParams($attrs, $parent_id, $n, 0);
			$idx = $this->elxis_menu->newIndex();
			$items[$idx] = $this->elxis_menu->makeItem($title, $link, $idx, $params);
			unset($link, $title, $params, $attrs);
			$n++;

			if (isset($subnode->subitems) && (count($subnode->subitems->children()) > 0)) { //1st level
				$m = 1;
				foreach ($subnode->subitems->children() as $lev1node) {
					$attrs = $lev1node->attributes();
					if (!$attrs) { continue; }
					if (!isset($attrs['link'])) { continue; }
					if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
					if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
					$title = $this->getTitleXML($lev1node, $this->menuLang);
					$link = (string)$attrs['link'];
					$params = $this->makeParams($attrs, $idx, $m, 0);
					$idx2 = $this->elxis_menu->newIndex();
					$items[$idx]->children[$idx2] = $this->elxis_menu->makeItem($title, $link, $idx2, $params);
					unset($link, $title, $params, $attrs);					
					$m++;

					if (isset($lev1node->subitems) && (count($lev1node->subitems->children()) > 0)) { //2nd level
						$g = 1;
						foreach ($lev1node->subitems->children() as $lev2node) {
							$attrs = $lev2node->attributes();
							if (!$attrs) { continue; }
							if (!isset($attrs['link'])) { continue; }
							if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
							if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
							$title = $this->getTitleXML($lev2node, $this->menuLang);
							$link = (string)$attrs['link'];
							$params = $this->makeParams($attrs, $idx2, $g, 0);
							$idx3 = $this->elxis_menu->newIndex();
							$items[$idx]->children[$idx2]->children[$idx3] = $this->elxis_menu->makeItem($title, $link, $idx3, $params);
							unset($link, $title, $params, $attrs);					
							$g++;

							if (isset($lev2node->subitems) && (count($lev2node->subitems->children()) > 0)) { //3rd level
								$z = 1;
								foreach ($lev2node->subitems->children() as $lev3node) {
									$attrs = $lev3node->attributes();
									if (!$attrs) { continue; }
									if (!isset($attrs['link'])) { continue; }
									if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
									if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
									$title = $this->getTitleXML($lev3node, $this->menuLang);
									$link = (string)$attrs['link'];
									$params = $this->makeParams($attrs, $idx3, $z, 0);
									$idx4 = $this->elxis_menu->newIndex();
									$items[$idx]->children[$idx2]->children[$idx3]->children[$idx4] = $this->elxis_menu->makeItem($title, $link, $idx4, $params);
									unset($link, $title, $params, $attrs);					
									$z++;
								}
							}
						}
					}
				}
			}
		}

		return $items;
	}


	/*************************************/
	/* INITIAL PARSE OF AN XML MENU FILE */
	/*************************************/
	private function parseMenu($section, $menu_name) {
		$xmlfile = ELXIS_PATH.'/components/com_'.$this->component.'/'.$this->component.'.menu.xml';
		if (!file_exists($xmlfile)) {
			$this->errormsg = 'File '.$this->component.'.menu.xml not found!';
			return false;
		}
		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$xml) {
			foreach (libxml_get_errors() as $error) {
				$this->errormsg = 'Parse XML error on file '.$this->component.'.menu.xml. Error: '.$error->message.'. Line: '.$error->line;
    			break;
    		}
    		return false;
		}

		if ($xml->getName() != 'elxismenu') {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml is not a valid Elxis menu file!';
			return false;
		}
		$attrs = $xml->attributes();
		if (!$attrs) {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml is not a valid Elxis menu file!';
			return false;
		}
		if (!isset($attrs['component']) || ($attrs['component'] != $this->component)) {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml is not a valid Elxis menu file for component '.$this->component.'!';
			return false;
		}
		if (!isset($xml->$section)) {
			$this->errormsg = 'Component '.$this->component.' does not have menu information for '.$section.'!';
			return false;
		}
	
		if (!isset($xml->$section->menu)) {
			$this->errormsg = 'Component '.$this->component.' does not have menu information for '.$section.'!';
			return false;
		}

		$menu = null;
		foreach ($xml->$section->menu as $mchild) {
			$attrs = $mchild->attributes();
			if ($attrs && isset($attrs['name']) && ($attrs['name'] == $menu_name)) {
				$menu = $mchild;
				break;
			}
		}

		if ($menu === null) {
			$this->errormsg = 'Requested menu '.$menu_name.' is not available for '.$section.'!';
			return false;
		}

		$this->menuTitle = $this->getTitleXML($menu, $this->menuLang, ucfirst($this->component));
		return $menu;
	}


	/*****************************************************/
	/* SEARCH XML FOR GIVEN LINK AND RETURN A NODES TREE */
	/*****************************************************/
	private function searchNode($nodes, $link) {
		foreach ($nodes as $node) { //root level
			$attrs = $node->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
			if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
			$nodelink = (string)$attrs['link'];
			if ($nodelink == $link) { return $node; }
			if (isset($node->subitems) && (count($node->subitems->children()) > 0)) { //1st level
				foreach ($node->subitems->children() as $lev1node) {
					$attrs = $lev1node->attributes();
					if (!$attrs) { continue; }
					if (!isset($attrs['link'])) { continue; }
					if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
					if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
					$nodelink = (string)$attrs['link'];
					if ($nodelink == $link) { return $lev1node; }
					if (isset($lev1node->subitems) && (count($lev1node->subitems->children()) > 0)) { //2nd level
						foreach ($lev1node->subitems->children() as $lev2node) {
							$attrs = $lev2node->attributes();
							if (!$attrs) { continue; }
							if (!isset($attrs['link'])) { continue; }
							if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
							if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
							$nodelink = (string)$attrs['link'];
							if ($nodelink == $link) { return $lev2node; }
							if (isset($lev2node->subitems) && (count($lev2node->subitems->children()) > 0)) { //3rd level
								foreach ($lev2node->subitems->children() as $lev3node) {
									$attrs = $lev3node->attributes();
									if (!$attrs) { continue; }
									if (!isset($attrs['link'])) { continue; }
									if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
									if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
									$nodelink = (string)$attrs['link'];
									if ($nodelink == $link) { return $lev3node; }
									if (isset($lev3node->subitems) && (count($lev3node->subitems->children()) > 0)) { //4th level
										foreach ($lev3node->subitems->children() as $lev4node) {
											$attrs = $lev4node->attributes();
											if (!$attrs) { continue; }
											if (!isset($attrs['link'])) { continue; }
											if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
											if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
											$nodelink = (string)$attrs['link'];
											if ($nodelink == $link) { return $lev4node; }
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return null;
	}


	/**********************************************************/
	/* GET THE TITLE FROM AN XML MENU ITEM OR THE MENU ITSELF */
	/**********************************************************/
	private function getTitleXML($element, $lang, $default='Unknown') {
		$en_title = '';
		if (isset($element->title)) {
			foreach ($element->title as $tchild) {
				$attrs = $tchild->attributes();
				if ($attrs && isset($attrs['lang'])) {
					if ($attrs['lang'] == $lang) {
						return (string)$tchild;
						break;
					} else if ($attrs['lang'] == 'en') {
						$en_title = (string)$tchild;
					}
				}
			}
		}

		return ($en_title != '') ? $en_title : $default;
	}


	/***************************************/
	/* MAKE PARAMETERS FROM XML ATTRIBUTES */
	/***************************************/
	private function makeParams($attrs, $parent, $ordering, $expand) {
		$params = array();
		$params['type'] = (isset($attrs['type'])) ? (string)$attrs['type'] : 'link';
		$params['file'] = (isset($attrs['file'])) ? (string)$attrs['file'] : 'index.php';
		$params['popup'] = (isset($attrs['popup'])) ? (int)$attrs['popup'] : 0;
		$params['secure'] = (isset($attrs['secure'])) ? (int)$attrs['secure'] : 0;
		$params['target'] = (isset($attrs['target'])) ? (string)$attrs['target'] : '_self';
		$params['parent'] = $parent;
		$params['ordering'] = $ordering;
		$params['expand'] = $expand;
		$params['width'] = (isset($attrs['width'])) ? (int)$attrs['width'] : 0;
		$params['height'] = (isset($attrs['height'])) ? (int)$attrs['height'] : 0;
		$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';
		return $params;
	}


	/**********************/
	/* GET THE MENU TITLE */
	/**********************/
	public function getMenuTitle() {
		return $this->menuTitle;
	}


	/**************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getErrorMsg() {
		return $this->errormsg;
	}


/************ LINK GENERATOR FOR ADMIN AREA *************************/


	/**************************************/
	/* RETURN ALL MENU'S (LINK GENERATOR) */
	/**************************************/
	public function getAllMenus($component, $section='frontend', $lang='') {
		$this->component = $component;
		if ($lang == '') { $lang = $this->lang; }
		$xmenus = $this->parseAllMenus($section, $lang);
		if (!$xmenus) { return array(); }

		$menus = array();
		foreach ($xmenus as $xmenu) {
			$menu = new stdClass;
			$menu->name = $xmenu['name'];
			$menu->title = $xmenu['title'];
			$menu->items = $this->parseEverything($xmenu['children'], $lang);
			$menus[] = $menu;
		}

		return $menus;
	}


	/******************************************************/
	/* INITIAL PARSE OF AN XML MENU FILE (LINK GENERATOR) */
	/******************************************************/
	private function parseAllMenus($section, $lang) {
		$xmlfile = ELXIS_PATH.'/components/com_'.$this->component.'/'.$this->component.'.menu.xml';
		if (!file_exists($xmlfile)) {
			$this->errormsg = 'File '.$this->component.'.menu.xml not found!';
			return false;
		}
		libxml_use_internal_errors(true);
		$xml = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		if (!$xml) {
			foreach (libxml_get_errors() as $error) {
				$this->errormsg = 'Parse XML error on file '.$this->component.'.menu.xml. Error: '.$error->message.'. Line: '.$error->line;
    			break;
    		}
    		return false;
		}

		if ($xml->getName() != 'elxismenu') {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml is not a valid Elxis menu file!';
			return false;
		}
		$attrs = $xml->attributes();
		if (!$attrs) {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml is not a valid Elxis menu file!';
			return false;
		}
		if (!isset($attrs['component']) || ($attrs['component'] != $this->component)) {
			$this->errormsg = 'XML file '.$this->component.'.menu.xml in not a valid Elxis menu file for component '.$this->component.'!';
			return false;
		}
		if (!isset($xml->$section)) {
			$this->errormsg = 'Component '.$this->component.' does not have menu information for '.$section.'!';
			return false;
		}
	
		if (!isset($xml->$section->menu)) {
			$this->errormsg = 'Component '.$this->component.' does not have menu information for '.$section.'!';
			return false;
		}

		$menus = array();
		foreach ($xml->$section->menu as $mchild) {
			$attrs = $mchild->attributes();
			if ($attrs && isset($attrs['name'])) {
				$menu_name = (string)$attrs['name'];
			} else {
				break;
			}

			$menus[] = array(
				'name' => $menu_name,
				'title' => $this->getTitleXML($mchild, $this->lang, ucfirst($this->component)),
				'children' => $mchild
			);
		}

		return $menus;
	}


	/*************************************/
	/* PARSE EVERYTHING (LINK GENERATOR) */
	/*************************************/
	private function parseEverything($xmenu, $lang) {
		if (!isset($xmenu->items)) { return array(); }
		$xml_items = $xmenu->items->children();
		if (!$xml_items) { return array(); }

		$items = array();
		$n = 1;
		$idx = 0;
		foreach ($xml_items as $xml_item) {
			$attrs = $xml_item->attributes();
			if (!$attrs) { continue; }
			if (!isset($attrs['link'])) { continue; }
			$name = $this->getTitleXML($xml_item, $this->lang);
			$title = $this->getTitleXML($xml_item, $lang);
			$link = (string)$attrs['link'];
			$params = $this->makeParams($attrs, 0, $n, 0);
			$params['alevel'] = (isset($attrs['minlevel'])) ? (int)$attrs['minlevel'] : 0;
			$params['gid'] = (isset($attrs['gid'])) ? (int)$attrs['gid'] : 0;
			$params['uid'] = (isset($attrs['uid'])) ? (int)$attrs['uid'] : 0;
			$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';

			$idx++;
			$n++;
			$items[$idx] = $this->makeGenItem($title, $name, $link, $idx, $params);
			unset($link, $title, $params, $attrs);

			if (isset($xml_item->subitems) && (count($xml_item->subitems->children()) > 0)) { //1st level
				$m = 1;
				$idx2 = 0;
				foreach ($xml_item->subitems->children() as $lev1node) {
					$attrs = $lev1node->attributes();
					if (!$attrs) { continue; }
					if (!isset($attrs['link'])) { continue; }
					$name = $this->getTitleXML($lev1node, $this->lang);
					$title = $this->getTitleXML($lev1node, $lang);
					$link = (string)$attrs['link'];
					$params = $this->makeParams($attrs, $idx, $m, 0);
					$params['alevel'] = (isset($attrs['minlevel'])) ? (int)$attrs['minlevel'] : 0;
					$params['gid'] = (isset($attrs['gid'])) ? (int)$attrs['gid'] : 0;
					$params['uid'] = (isset($attrs['uid'])) ? (int)$attrs['uid'] : 0;
					$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';
					$idx2++;
					$items[$idx]->children[$idx2] = $this->makeGenItem($title, $name, $link, $idx2, $params);
					unset($link, $title, $params, $attrs);					
					$m++;

					if (isset($lev1node->subitems) && (count($lev1node->subitems->children()) > 0)) { //2nd level
						$g = 1;
						$idx3 = 0;
						foreach ($lev1node->subitems->children() as $lev2node) {
							$attrs = $lev2node->attributes();
							if (!$attrs) { continue; }
							if (!isset($attrs['link'])) { continue; }
							if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
							if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
							$name = $this->getTitleXML($lev2node, $this->lang);
							$title = $this->getTitleXML($lev2node, $lang);
							$link = (string)$attrs['link'];
							$params = $this->makeParams($attrs, $idx2, $g, 0);
							$params['alevel'] = (isset($attrs['minlevel'])) ? (int)$attrs['minlevel'] : 0;
							$params['gid'] = (isset($attrs['gid'])) ? (int)$attrs['gid'] : 0;
							$params['uid'] = (isset($attrs['uid'])) ? (int)$attrs['uid'] : 0;
							$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';
							$idx3++;
							$items[$idx]->children[$idx2]->children[$idx3] = $this->makeGenItem($title, $name, $link, $idx3, $params);
							unset($link, $title, $params, $attrs);					
							$g++;

							if (isset($lev2node->subitems) && (count($lev2node->subitems->children()) > 0)) { //3rd level
								$z = 1;
								$idx4 = 0;
								foreach ($lev2node->subitems->children() as $lev3node) {
									$attrs = $lev3node->attributes();
									if (!$attrs) { continue; }
									if (!isset($attrs['link'])) { continue; }
									$name = $this->getTitleXML($lev3node, $this->lang);
									$title = $this->getTitleXML($lev3node, $lang);
									$link = (string)$attrs['link'];
									$params = $this->makeParams($attrs, $idx3, $z, 0);
									$params['alevel'] = (isset($attrs['minlevel'])) ? (int)$attrs['minlevel'] : 0;
									$params['gid'] = (isset($attrs['gid'])) ? (int)$attrs['gid'] : 0;
									$params['uid'] = (isset($attrs['uid'])) ? (int)$attrs['uid'] : 0;
									$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';
									$idx4++;
									$items[$idx]->children[$idx2]->children[$idx3]->children[$idx4] = $this->makeGenItem($title, $name, $link, $idx4, $params);
									unset($link, $title, $params, $attrs);					
									$z++;

									if (isset($lev3node->subitems) && (count($lev3node->subitems->children()) > 0)) { //4th level
										$v = 1;
										$idx5 = 0;
										foreach ($lev3node->subitems->children() as $lev4node) {
											$attrs = $lev4node->attributes();
											if (!$attrs) { continue; }
											if (!isset($attrs['link'])) { continue; }
											if (isset($attrs['gid']) && (intval($attrs['gid']) > 0) && ($attrs['gid'] <> $this->mygid)) { continue; }
											if (isset($attrs['minlevel']) && (intval($attrs['minlevel']) > $this->mylevel)) { continue; }
											$name = $this->getTitleXML($lev4node, $this->lang);
											$title = $this->getTitleXML($lev4node, $lang);
											$link = (string)$attrs['link'];
											$params = $this->makeParams($attrs, $idx4, $v, 0);
											$params['alevel'] = (isset($attrs['minlevel'])) ? (int)$attrs['minlevel'] : 0;
											$params['gid'] = (isset($attrs['gid'])) ? (int)$attrs['gid'] : 0;
											$params['uid'] = (isset($attrs['uid'])) ? (int)$attrs['uid'] : 0;
											$params['icon'] = (isset($attrs['icon'])) ? (string)$attrs['icon'] : '';
											$idx5++;
											$items[$idx]->children[$idx2]->children[$idx3]->children[$idx4]->children[$idx5] = $this->makeGenItem($title, $name, $link, $idx5, $params);
											unset($link, $title, $params, $attrs);					
											$v++;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return $items;
	}


	/******************************************************/
	/* MAKE A NEW MENU ITEM (LINK GENERATOR & ADMIN MENU) */
	/******************************************************/
	public function makeGenItem($title, $name, $link, $id, $params=array(), $children=array()) {
		$item = new stdClass;
		$item->menu_id = $id;
		$item->name = $name; //title in current lang
		$item->title = $title; //title in default lang
		$item->menu_type = (isset($params['type']) && (trim($params['type']) != '')) ? $params['type'] : 'link';
		$item->link = $link;
		$item->file = (isset($params['file']) && (trim($params['file']) != '')) ? (string)$params['file'] : 'index.php';
		$item->popup = isset($params['popup']) ? (int)$params['popup'] : 0;
		$item->secure = isset($params['secure']) ? (int)$params['secure'] : 0;
		$item->parent_id = isset($params['parent']) ? $params['parent'] : 0;
		$item->ordering = isset($params['ordering']) ? (int)$params['ordering'] : 1;
		$item->expand = isset($params['expand']) ? (int)$params['expand'] : 0;
		$item->target = isset($params['target']) ? (string)$params['target'] : '_self';
		$item->width = isset($params['width']) ? (int)$params['width'] : 0;
		$item->height = isset($params['height']) ? (int)$params['height'] : 0;
		$item->alevel = isset($params['alevel']) ? (int)$params['alevel'] : 0;
		$item->gid = isset($params['gid']) ? (int)$params['gid'] : 0;
		$item->uid = isset($params['uid']) ? (int)$params['uid'] : 0;
		$item->icon = isset($params['icon']) ? (string)$params['icon'] : '';
		$item->children = $children;
		return $item;
	}

}

?>