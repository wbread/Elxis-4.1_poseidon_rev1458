<?php 
/**
* @version		$Id: menu.class.php 1458 2013-09-16 16:18:38Z datahell $
* @package		Elxis
* @subpackage	Menu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisMenu {

	private $section = 'frontend';
	private $collection = 'mainmenu';
	private $translate = false;
	private $lng = '';
	private $last_index = 10000;
	private $default_route = 'content:/';
	private $lowlevel = 0;
	private $exactlevel = 7;
	private $menuid = 0; //the id of the menu item that was clicked or that is closer to the one clicked.
	private $apc = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();
		$this->lowlevel = $elxis->acl()->getLowLevel();
		$this->exactlevel = $elxis->acl()->getExactLevel();
		$this->apc = $elxis->getConfig('APC');
		$this->default_route = $elxis->getConfig('DEFAULT_ROUTE');
		if (($this->default_route == '') || ($this->default_route == '/') || ($this->default_route == 'content')) {
			$this->default_route = 'content:/';
		}
		if ($elxis->getConfig('MULTILINGUISM') == 1) {
			$eURI = eFactory::getURI();
			$this->lng = $eURI->getUriLang();
			if ($this->lng != '') {
				$this->translate = true;
			}
		}
	}


	/******************/
	/* GET MENU ITEMS */
	/******************/
	public function getItems($collection='mainmenu', $section='frontend') {
		$this->collection = $collection;
		$this->section = ($section == 'backend') ? 'backend' : 'frontend';

		if (($this->apc) && ($this->exactlevel == 0)) {
			$items = elxisAPC::fetch($collection.$this->lng, 'menu'.$section);
			if ($items !== false) { return $items; }
		}

		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__menu')
		."\n WHERE ".$db->quoteId('section')." = :section AND ".$db->quoteId('collection')." = :collection AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':section', $this->section, PDO::PARAM_STR);
		$stmt->bindParam(':collection', $this->collection, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return array(); }
		unset($stmt, $sql);

		$items = array();
		$elids = array();
		foreach ($rows as $row) {
			$id = $row['menu_id'];
			$elids[] = $id;
			$target = (trim($row['target']) == '') ? '_self' : $row['target'];
			$params = array(
				'file' => $row['file'],
				'type' => $row['menu_type'],
				'popup' => $row['popup'],
				'secure' => $row['secure'],
				'target' => $target,
				'parent' => (int)$row['parent_id'],
				'ordering' => $row['ordering'],
				'expand' => $row['expand'],
				'width' => (int)$row['width'],
				'height' => (int)$row['height']
			);
			$items[$id] = $this->makeItem($row['title'], $row['link'], $id, $params);
			unset($id, $target, $params);
		}
		unset($rows);

		$items = $this->translateMenuItems($elids, $items);
		unset($elids);

		$tounset = array();
		foreach($items as $k => $v) {
			$id = $v->menu_id;
			$parent = $v->parent_id;
			if (isset($items[$parent])) {
				$items[$parent]->children[$id] = $items[$k];
				$tounset[] = $k;
			} else if ($parent > 0) { //remove elements with missing parents (i.e due to alevel restrictions)
				$tounset[] = $k;
			}
			unset($id, $parent);
		}
		
		if ($tounset) {
			foreach($tounset as $t) { unset($items[$t]); }
		}

		foreach ($items as $k => $v) {
			if (($v->menu_type == 'link') && ($v->expand > 0)) {
				$clean_link = '';
				if ($v->link == '') {
					 if (($this->section == 'frontend') && ($this->default_route == 'content:/')) {
						$component = 'content';
					} else {
						continue;
					}
				} else if (strpos($v->link, ':') === false) {
					$component = ($this->section == 'frontend') ? 'content' : 'cpanel';
					$clean_link = $v->link;
				} else {
					$parts = preg_split('/\:/', $v->link, -1, PREG_SPLIT_NO_EMPTY);
					$component = '';
					if ($parts) {
						if (isset($parts[2])) {
							$clean_link = $parts[2];
							$component = $parts[1];
						} else if (isset($parts[1])) {
							if (strlen($parts[0]) > 2) {
								$component = $parts[0];
								$clean_link = $parts[1];
							} else {
								$clean_link = $parts[1];
								$component = ($this->section == 'frontend') ? 'content' : 'cpanel';
							}
						} else {
							$clean_link = $parts[0];
							$component = ($this->section == 'frontend') ? 'content' : 'cpanel';
						}
					}
					unset($parts);
				}

				if ($component == 'content') {
					if ($this->section == 'frontend') {
						$moreitems = $this->getContentChildren($v->link, $clean_link, $v->menu_id, $v->expand);
						if ($moreitems) {
							if (count($items[$k]->children) > 0) {
								$items[$k]->children = array_merge($moreitems, $items[$k]->children);
							} else {
								$items[$k]->children = $moreitems;
							}
						}
						unset($moreitems);
					}
				} else if ($component != '') {
					$file = ELXIS_PATH.'/components/com_'.$component.'/'.$component.'.menu.xml';
					if (file_exists($file)) {
						elxisLoader::loadFile('includes/libraries/elxis/menu.xml.php');
						$xmlMenu = new elxisXMLMenu($this);
						$moreitems = $xmlMenu->getMenu($items[$k]->menu_id, $component, $v->link, $v->expand, $section, 'default', '');
						if ($moreitems) {
							if (count($items[$k]->children) > 0) {
								$items[$k]->children = array_merge($moreitems, $items[$k]->children);
							} else {
								$items[$k]->children = $moreitems;
							}
						}
						unset($xmlMenu, $moreitems);
					}
				}
			}
		}

		if (($this->apc) && ($this->exactlevel == 0)) {
			elxisAPC::store($collection.$this->lng, 'menu'.$section, $items, 600);
		}

		return $items;
	}


	/*******************************************/
	/* MAKE MENU ITEMS FROM CONTENT CATEGORIES */
	/*******************************************/
	private function getContentChildren($link, $clean_link, $parent_id=0, $expand=0) {
		if (($expand < 1) || ($expand > 2)) { return false; }
		if (($link == '') || ($link == '/')) {
			if ($expand == 1) {
				return $this->getContentRoot($parent_id);
			} else {
				return $this->getContentAll($parent_id);
			}
		} elseif (!preg_match('#(\/)$#', $link)) {
			return false;
		} else {
			$cats = preg_split('#\/#', $clean_link, -1, PREG_SPLIT_NO_EMPTY);
			$n = count($cats) - 1;
			$seocat = $cats[$n];
			if ($expand == 1) {
				return $this->getCategoryParents($link, $seocat, $parent_id);
			} else {
				return $this->getCategoryAllParents($link, $seocat, $parent_id);
			}
		}
	}


	/************************************************/
	/* RETURN MENU ITEMS OF CONTENT ROOT CATEGORIES */
	/************************************************/
	private function getContentRoot($parent_id) {
		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__categories')." WHERE ".$db->quoteId('parent_id')."=0"
		."\n AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }
		unset($stmt, $sql);

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row['catid']; }
		$trans = $this->catsTranslations($elids);
		unset($elids);

		$items = array();
		foreach ($rows as $row) {
			$this->last_index++;
			$catid = $row['catid'];
			$title = (isset($trans[$catid])) ? $trans[$catid] : $row['title'];
			$xlink = 'content:'.$row['seotitle'].'/';
			$params = array(
				'parent' => $parent_id,
				'ordering' => $row['ordering']
			);
			$items[ $this->last_index ] = $this->makeItem($title, $xlink, $this->last_index, $params);
			unset($catid, $title, $xlink, $params);
		}
		return $items;
	}


	/************************************************/
	/* RETURN MENU ITEMS OF ALL CONTENT CATEGORIES */
	/************************************************/
	private function getContentAll($parent_id) {
		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__categories')." WHERE ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }
		unset($stmt, $sql);

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row['catid']; }
		$trans = $this->catsTranslations($elids);
		unset($elids);

		$items = array();
		foreach ($rows as $row) {
			$catid = $row['catid'];
			$title = (isset($trans[$catid])) ? $trans[$catid] : $row['title'];
			$xlink = $row['seotitle'].'/'; //without {content:} !
			$params = array(
				'parent' => $parent_id,
				'ordering' => $row['ordering']
			);

			$item = $this->makeItem($title, $xlink, 0, $params);
			$item->catid = $catid;
			$item->parent_cat = $row['parent_id'];
			$items[$catid] = $item;
			unset($catid, $title, $xlink, $params, $item);
		}
		unset($rows);

		$tounset = array();
		foreach($items as $k => $v) {
			$id = $v->catid;
			$parent = $v->parent_cat;
			unset($items[$k]->catid, $items[$k]->parent_cat);
			if (isset($items[$parent])) {
				$items[$k]->parent_id = $items[$parent]->menu_id;
				$items[$parent]->children[$id] = $items[$k];
				$tounset[] = $k;
			} else if ($parent > 0) { //remove elements with missing parents (i.e due to alevel restrictions)
				$tounset[] = $k;
			}
			unset($id, $parent, $items[$k]->parent_cat);
		}

		if ($tounset) {
			foreach($tounset as $t) { unset($items[$t]); }
		}

		//make paths (up to 5 levels, 6 including root)
		foreach ($items as $k => $v) {
			$link0 = $v->link;
			$items[$k]->link = 'content:'.$link0;
			if ($v->children) { //1st level
				foreach ($v->children as $k1 => $v1) {
					$link1 = $v1->link;
					$items[$k]->children[$k1]->link = 'content:'.$link0.$link1;
					if ($v1->children) { //2nd level
						foreach ($v1->children as $k2 => $v2) {
							$link2 = $v2->link;
							$items[$k]->children[$k1]->children[$k2]->link = 'content:'.$link0.$link1.$link2;
							if ($v2->children) { //3rd level
								foreach ($v2->children as $k3 => $v3) {
									$link3 = $v3->link;
									$items[$k]->children[$k1]->children[$k2]->children[$k3]->link = 'content:'.$link0.$link1.$link2.$link3;
									if ($v3->children) { //4th level
										foreach ($v3->children as $k4 => $v4) {
											$link4 = $v4->link;
											$items[$k]->children[$k1]->children[$k2]->children[$k3]->children[$k4]->link = 'content:'.$link0.$link1.$link2.$link3.$link4;
											if ($v4->children) { //5th level
												foreach ($v4->children as $k5 => $v5) {
													$link5 = $v5->link;
													$items[$k]->children[$k1]->children[$k2]->children[$k3]->children[$k4]->children[$k5]->link = 'content:'.$link0.$link1.$link2.$link3.$link4.$link5;
												}
											}
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


	/*****************************************************/
	/* RETURN MENU ITEMS OF CONTENT CATEGORY (ONLY ROOT) */
	/*****************************************************/
	private function getCategoryParents($link, $seocat, $parent_id) {
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('catid')." FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('seotitle')." = :seocat AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':seocat', $seocat, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$catid = (int)$stmt->fetchColumn();
		if (!$catid) { return false; }
		unset($stmt);

		$sql = "SELECT * FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('parent_id')." = :ctgid AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':ctgid', $catid, PDO::PARAM_INT);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }
		unset($stmt, $sql);

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row['catid']; }
		$trans = $this->catsTranslations($elids);
		unset($elids);

		$items = array();
		foreach ($rows as $row) {
			$this->last_index++;
			$catid = $row['catid'];
			$title = (isset($trans[$catid])) ? $trans[$catid] : $row['title'];
			$xlink = $link.$row['seotitle'].'/';
			$params = array(
				'parent' => $parent_id,
				'ordering' => $row['ordering']
			);
			$items[ $this->last_index ] = $this->makeItem($title, $xlink, $this->last_index, $params);
			unset($catid, $title, $xlink, $params);
		}
		return $items;
	}


	/*******************************************************************/
	/* RETURN MENU ITEMS OF CONTENT CATEGORY (ALL CHILDREN CATEGORIES) */
	/*******************************************************************/
	private function getCategoryAllParents($link, $seocat, $parent_id) {
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('catid')." FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('seotitle')." = :seocat AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':seocat', $seocat, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$catid = (int)$stmt->fetchColumn();
		if (!$catid) { return false; }
		unset($stmt);

		$sql = "SELECT * FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('parent_id')." > 0 AND ".$db->quoteId('published')."=1"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return false; }
		unset($stmt, $sql);

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row['catid']; }
		$trans = $this->catsTranslations($elids);
		unset($elids);

		$items = array();
		//first loop - collect 1st level
		foreach ($rows as $k => $row) {
			if ($row['catid'] == $catid) {
				unset($rows[$k]);
				continue;
			}
			if ($row['parent_id'] == $catid) {
				$curcatid = $row['catid'];
				$title = (isset($trans[$curcatid])) ? $trans[$curcatid] : $row['title'];
				$xlink = $link.$row['seotitle'].'/';
				$params = array(
					'parent' => $parent_id,
					'ordering' => $row['ordering']
				);
				$items[$curcatid] = $this->makeItem($title, $xlink, 0, $params);
				unset($curcatid, $title, $xlink, $params, $rows[$k]);
			}
		}

		if (!$items) { return false; }
		if (count($rows) == 0) { return $items; }

		//second loop - collect 2nd level
		$found_2nd_level = false;
		foreach ($rows as $k => $row) {
			$p = $row['parent_id'];
			if (isset($items[$p])) {
				$found_2nd_level = true;
				$curcatid = $row['catid'];
				$title = (isset($trans[$curcatid])) ? $trans[$curcatid] : $row['title'];
				$xlink = $items[$p]->link.$row['seotitle'].'/';
				$params = array(
					'parent' => $items[$p]->menu_id,
					'ordering' => $row['ordering']
				);
				$items[$p]->children[$curcatid] = $this->makeItem($title, $xlink, 0, $params);
				unset($curcatid, $title, $xlink, $params, $rows[$k]);
			}
		}

		if (!$found_2nd_level || (count($rows) == 0)) { return $items; }

		//third loop - collect 3rd level
		foreach ($rows as $k => $row) {
			$p = $row['parent_id'];
			foreach ($items as $k1 => $rootitems) {
				if (isset($rootitems->children[$p])) {
					$curcatid = $row['catid'];
					$title = (isset($trans[$curcatid])) ? $trans[$curcatid] : $row['title'];
					$xlink = $rootitems->children[$p]->link.$row['seotitle'].'/';
					$params = array(
						'parent' => $rootitems->children[$p]->menu_id,
						'ordering' => $row['ordering']
					);
					$items[$k1]->children[$p]->children[$curcatid] = $this->makeItem($title, $xlink, 0, $params);
					unset($curcatid, $title, $xlink, $params, $rows[$k]);
					break;
				}
			}
		}

		//that's enough loops...
		return $items;
	}


	/************************/
	/* TRANSLATE MENU ITEMS */
	/************************/
	private function translateMenuItems($elids, $rows) {
		if (!$this->translate) { return $rows; }
		$db = eFactory::getDB();
		$query = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = ".$db->quote('com_emenu')." AND ".$db->quoteId('element')." = ".$db->quote('title')
		."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(', ', $elids).")";
		$stmt = $db->prepare($query);
		$stmt->execute(array(':lng' => $this->lng));
		$trans = $stmt->fetchPairs();
		if (!$trans) { return $rows; }
		unset($stmt);
		foreach ($rows as $i => $row) {
			$idx = $row->menu_id;
			if (isset($trans[$idx])) {
				$rows[$i]->title = $trans[$idx];
			}
		}
		return $rows;
	}


	/***********************************/
	/* GET TRANSLATIONS FOR CATEGORIES */
	/***********************************/
	private function catsTranslations($elids) {
		if (!$this->translate) { return array(); }
		$db = eFactory::getDB();
		$query = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = ".$db->quote('com_content')." AND ".$db->quoteId('element')." = ".$db->quote('category_title')
		."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(', ', $elids).")";
		$sth = $db->prepare($query);
		$sth->execute(array(':lng' => $this->lng));
		$trans = $sth->fetchPairs();
		if (!$trans) { return array(); }
		return $trans;
	}


	/*****************************/
	/* MAKE A NEW MENU ITEM NODE */
	/*****************************/
	public function makeItem($title, $link, $id=0, $params=array(), $children=array()) {
		if ($id == 0) {
			$this->last_index++;
			$id = $this->last_index;
		}

		$item = new stdClass;
		$item->menu_id = $id;
		$item->title = $title;
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
		$item->children = $children;
		return $item;
	}


	/*******************/
	/* GET A NEW INDEX */
	/*******************/
	public function newIndex() {
		$this->last_index++;
		return $this->last_index;
	}


	/******************************/
	/* GET FRONTPAGE MENU ITEM ID */
	/******************************/
	public function getFrontpageMenuId() {
		$db = eFactory::getDB();
		$elxis = eFactory::getElxis();
		if (defined('ELXIS_ADMIN')) {
			$section = 'backend';
			$sql = "SELECT ".$db->quoteId('menu_id')." FROM ".$db->quoteId('#__menu')." WHERE ".$db->quoteId('section')." = :sec"
			."\n AND ".$db->quoteId('menu_type')." = ".$db->quote('link')." AND ".$db->quoteId('published')."=1"
			."\n AND ((".$db->quoteId('link')." IS NULL) OR (".$db->quoteId('link')." = ''))"
			."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
			."\n ORDER BY ".$db->quoteId('ordering')." ASC";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':sec', $section, PDO::PARAM_STR);
		} else {
			$section = 'frontend';
			$defroute = $elxis->getConfig('DEFAULT_ROUTE');
			$sql = "SELECT ".$db->quoteId('menu_id')." FROM ".$db->quoteId('#__menu')." WHERE ".$db->quoteId('section')." = :sec"
			."\n AND ".$db->quoteId('menu_type')." = ".$db->quote('link')." AND ".$db->quoteId('published')."=1"
			."\n AND ((".$db->quoteId('link')." IS NULL) OR (".$db->quoteId('link')." = '') OR (".$db->quoteId('link')." = :defroute))"
			."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
			."\n ORDER BY ".$db->quoteId('ordering')." ASC";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':sec', $section, PDO::PARAM_STR);
			$stmt->bindParam(':defroute', $defroute, PDO::PARAM_STR);
		}
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$menuid = (int)$stmt->fetchResult();
		return $menuid;
	}


	/*********************************************************************/
	/* GET/CALCULATE CLICKED MENU ID (OR THE CLOSEST TO THE ONE CLICKED) */
	/*********************************************************************/
	public function getMenuId() {
		if ($this->menuid > 0) { return $this->menuid; }
		$eURI = eFactory::getURI();
		$elxis = eFactory::getElxis();
		$elxis_uri = trim($eURI->getElxisUri());
		if (($elxis_uri == '') || (!defined('ELXIS_ADMIN') && ($elxis_uri == $elxis->getConfig('DEFAULT_ROUTE')))) {
			$this->menuid = $this->getFrontpageMenuId();
			return $this->menuid;
		}

		$db = eFactory::getDB();
		$section = (defined('ELXIS_ADMIN')) ? 'backend' : 'frontend';
		$component = $eURI->getComponent();
		if (strpos($elxis_uri, ':') === false) { $elxis_uri = $component.':'.$elxis_uri; }

		$wrapper_menuid = 0;
		if (($section == 'frontend') && ($component == 'wrapper')) {
			$segments = $eURI->getSegments();
			$n = count($segments);
			if ($n > 0) {
				$seg = $segments[$n - 1];
				if (preg_match('@(\.html)$@i', $seg)) {
					$wrapper_menuid = intval(substr($seg, 0, -5));
				}
			}
		}

		if ($wrapper_menuid > 0) {
			$sql = "SELECT ".$db->quoteId('menu_id')." FROM ".$db->quoteId('#__menu')." WHERE ".$db->quoteId('section')." = :sec"
			."\n AND ".$db->quoteId('menu_type')." = ".$db->quote('wrapper')." AND ".$db->quoteId('published')."=1"
			."\n AND ".$db->quoteId('menu_id')." = :xid"
			."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
			."\n ORDER BY ".$db->quoteId('ordering')." ASC";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':sec', $section, PDO::PARAM_STR);
			$stmt->bindParam(':xid', $wrapper_menuid, PDO::PARAM_INT);
			$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
			$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
			$stmt->execute();
			$this->menuid = (int)$stmt->fetchResult();
			if ($this->menuid > 0) { return $this->menuid; }
			$this->menuid = $this->getFrontpageMenuId();
			return $this->menuid;
		}

		$sql = "SELECT ".$db->quoteId('menu_id')." FROM ".$db->quoteId('#__menu')." WHERE ".$db->quoteId('section')." = :sec"
		."\n AND ".$db->quoteId('menu_type')." = ".$db->quote('link')." AND ".$db->quoteId('published')."=1"
		."\n AND ".$db->quoteId('link')." = :elxisuri"
		."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
		."\n ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':sec', $section, PDO::PARAM_STR);
		$stmt->bindParam(':elxisuri', $elxis_uri, PDO::PARAM_STR);
		$stmt->bindParam(':lowlevel', $this->lowlevel, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $this->exactlevel, PDO::PARAM_INT);
		$stmt->execute();
		$this->menuid = (int)$stmt->fetchResult();
		if ($this->menuid > 0) { return $this->menuid; }

		$segments = $eURI->getSegments();
		$c = count($segments);

		if ($c == 5) {
			$elxis_uri2 = $component.':'.$segments[0].'/'.$segments[1].'/'.$segments[2].'/'.$segments[3].'/';
			$stmt->bindParam(':elxisuri', $elxis_uri2, PDO::PARAM_STR);
			$stmt->execute();
			$this->menuid = (int)$stmt->fetchResult();
			if ($this->menuid > 0) { return $this->menuid; }
			$c = 4;
		}

		if ($c == 4) {
			$elxis_uri2 = $component.':'.$segments[0].'/'.$segments[1].'/'.$segments[2].'/';
			$stmt->bindParam(':elxisuri', $elxis_uri2, PDO::PARAM_STR);
			$stmt->execute();
			$this->menuid = (int)$stmt->fetchResult();
			if ($this->menuid > 0) { return $this->menuid; }
			$c = 3;
		}

		if ($c == 3) {
			$elxis_uri2 = $component.':'.$segments[0].'/'.$segments[1].'/';
			$stmt->bindParam(':elxisuri', $elxis_uri2, PDO::PARAM_STR);
			$stmt->execute();
			$this->menuid = (int)$stmt->fetchResult();
			if ($this->menuid > 0) { return $this->menuid; }
			$c = 2;
		}

		if ($c == 2) {
			$elxis_uri2 = $component.':'.$segments[0].'/';
			$stmt->bindParam(':elxisuri', $elxis_uri2, PDO::PARAM_STR);
			$stmt->execute();
			$this->menuid = (int)$stmt->fetchResult();
			if ($this->menuid > 0) { return $this->menuid; }
			$c = 1;
		}

		if ($c == 1) {
			$defcomp = (defined('ELXIS_ADMIN')) ? 'cpanel' : 'content';
			if ($component != $defcomp) {
				$elxis_uri2 = $component.':/';
				$stmt->bindParam(':elxisuri', $elxis_uri2, PDO::PARAM_STR);
				$stmt->execute();
				$this->menuid = (int)$stmt->fetchResult();
				if ($this->menuid > 0) { return $this->menuid; }
			}
		}

		$this->menuid = $this->getFrontpageMenuId();
		return $this->menuid;
	}

}

?>