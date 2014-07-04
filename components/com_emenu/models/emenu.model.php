<?php 
/**
* @version		$Id: emenu.model.php 1102 2012-05-04 18:35:17Z datahell $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class emenuModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*********************************/
	/* GET ALL AVAILABLE COLLECTIONS */
	/*********************************/
	public function getCollections() {
		$section = 'frontend';
		$modname = 'mod_menu';
		$all_collections = array();

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('position').", ".$this->db->quoteId('params')." FROM #__modules"
		."\n WHERE ".$this->db->quoteId('module')." = :xmodname"
		."\n AND ".$this->db->quoteId('section')." = :xsection";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmodname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$mods = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if ($mods) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			foreach ($mods as $mod) {
				$params = new elxisParameters($mod['params'], '', 'module');
				$collection = trim($params->get('collection', ''));
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
				unset($params);
			}
		}

		$sql = "SELECT ".$this->db->quoteId('collection')." FROM #__menu"
		."\n WHERE ".$this->db->quoteId('section')." = :xsection GROUP BY ".$this->db->quoteId('collection')
		."\n ORDER BY ".$this->db->quoteId('collection')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$collections = $stmt->fetchCol();
		if ($collections) {
			foreach ($collections as $collection) {
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
			}
		}

		if (count($all_collections) == 0) { return array(); }

		$sql = "SELECT COUNT(menu_id) FROM #__menu WHERE ".$this->db->quoteId('collection')." = :xcol"
		."\n AND ".$this->db->quoteId('section')." = :xsection";
		$stmt = $this->db->prepare($sql);

		$rows = array();
		foreach ($all_collections as $collection) {
			$stmt->bindParam(':xcol', $collection, PDO::PARAM_STR);
			$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
			$stmt->execute();
			$num = (int)$stmt->fetchResult();

			$colmods = array();
			foreach ($mods as $mod) {
				if (strpos($mod['params'], 'collection='.$collection) !== false) {
					$colmods[] = $mod;
				}
			}

			$row = new stdClass;
			$row->collection = $collection;
			$row->items = $num;
			$row->modules = $colmods;

			$rows[] = $row;
		}

		return $rows;
	}


	/*****************************/
	/* SAVE NEW COLLECTION IN DB */
	/*****************************/
	public function saveCollection($collection, $modtitle) {
		$eLang = eFactory::getLang();

		$response = array('success' => false, 'message' => 'Unknown error');

		$section = 'frontend';
		$modname = 'mod_menu';
		$parlike = '%collection='.$collection.'%';

		$sql = "SELECT COUNT(menu_id) FROM ".$this->db->quoteId('#__menu')." WHERE ".$this->db->quoteId('collection')." = :xcol"
		."\n AND ".$this->db->quoteId('section')." = :xsection";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcol', $collection, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num > 0) {
			$response['message'] = $eLang->get('EXIST_COLLECT_NAME');
			return $response;
		}

		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__modules')
		."\n WHERE ".$this->db->quoteId('module')." = :xmodname"
		."\n AND ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('params')." LIKE :xcol";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmodname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xcol', $parlike, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num > 0) {
			$response['message'] = $eLang->get('EXIST_COLLECT_NAME');
			return $response;
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/modules.db.php');
		$row = new modulesDbTable();
		$row->title = $modtitle;
		$row->section = $section;
		$row->module = $modname;
		$row->params = 'collection='.$collection;
		if (!$row->store()) {
			$response['message'] = $row->getErrorMsg();
			return $response;
		}

		$wheres = array(
			array('position', '=', $row->position),
			array('section', '=', $row->section)
			
		);
		$row->reorder($wheres, true);
		$modid = (int)$row->id;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/acl.db.php');
		$arow = new aclDbTable();
		$arow->category = 'module';
		$arow->element = $row->module;
		$arow->identity = $modid;
		$arow->action = 'view';
		$arow->minlevel = 0;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		$arow = new aclDbTable();
		$arow->category = 'module';
		$arow->element = $row->module;
		$arow->identity = $modid;
		$arow->action = 'manage';
		$arow->minlevel = 70;
		$arow->gid = 0;
		$arow->uid = 0;
		$arow->aclvalue = 1;
		$arow->insert();
		unset($arow);

		$menuid = 0;
		$sql = "INSERT INTO ".$this->db->quoteId('#__modules_menu')." (".$this->db->quoteId('mmid').", ".$this->db->quoteId('moduleid').", ".$this->db->quoteId('menuid').")"
		."\n VALUES (NULL, :xmod, :xmen)";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmod', $modid, PDO::PARAM_INT);
		$stmt->bindParam(':xmen', $menuid, PDO::PARAM_INT);
		$stmt->execute();

		$response['success'] = true;
		$response['message'] = $eLang->get('ACTION_SUCCESS');

		return $response;
	}


	/*********************/
	/* DELETE COLLECTION */
	/*********************/
	public function deleteCollection($collection) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$response = array('success' => false, 'message' => 'Unknown error');
		if ($elxis->acl()->check('com_emenu', 'menu', 'delete') < 1) {
			$response['message'] = $eLang->get('NOTALLOWACTION');
			return $response;
		}

		$section = 'frontend';
		$modulename = 'mod_menu';
		$parlike = '%collection='.$collection.'%';

		$sql = "DELETE FROM ".$this->db->quoteId('#__menu')." WHERE ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('collection')." = :xcollection";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xcollection', $collection, PDO::PARAM_STR);
		$stmt->execute();

		$sql = "SELECT ".$this->db->quoteId('id')." FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('module')." = :xmod"
		."\n AND ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('params')." LIKE :xcol";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xmod', $modulename, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xcol', $parlike, PDO::PARAM_STR);
		$stmt->execute();
		$moduleid = (int)$stmt->fetchResult();

		if ($moduleid > 0) {
			$sql = "DELETE FROM ".$this->db->quoteId('#__modules')." WHERE ".$this->db->quoteId('id')." = :xid";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xid', $moduleid, PDO::PARAM_INT);
			$stmt->execute();

			$sql = "DELETE FROM ".$this->db->quoteId('#__modules_menu')." WHERE ".$this->db->quoteId('moduleid')." = :xmid";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xmid', $moduleid, PDO::PARAM_INT);
			$stmt->execute();

			$ctg = 'module';
			$sql = "DELETE FROM ".$this->db->quoteId('#__acl')." WHERE ".$this->db->quoteId('category')." = :xcat"
			."\n AND ".$this->db->quoteId('element')." = :xelem"
			."\n AND ".$this->db->quoteId('identity')." = :xid";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
			$stmt->bindParam(':xelem', $modulename, PDO::PARAM_STR);
			$stmt->bindParam(':xid', $moduleid, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = true;
		$response['message'] = 'Success';
		return $response;
	}


	/********************************/
	/* CHECK IF A COLLECTION EXISTS */
	/********************************/
	public function validateCollection($collection) {
		$section = 'frontend';
		$modname = 'mod_menu';
		$parlike = '%collection='.$collection.'%';

		$sql = "SELECT COUNT(menu_id) FROM #__menu WHERE ".$this->db->quoteId('collection')." = :xcol"
		."\n AND ".$this->db->quoteId('section')." = :xsection";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcol', $collection, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num > 0) { return true; }

		$sql = "SELECT COUNT(id) FROM #__modules"
		."\n WHERE ".$this->db->quoteId('module')." = :xmodname"
		."\n AND ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('params')." LIKE :xcol";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xmodname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xcol', $parlike, PDO::PARAM_STR);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		if ($num > 0) { return true; }
		return false;
	}


	/*******************************/
	/* GET COLLECTION'S MENU ITEMS */
	/*******************************/
	public function getMenuItems($collection) {
		$section = 'frontend';
		$sql = "SELECT * FROM #__menu WHERE ".$this->db->quoteId('collection')." = :xcol"
		."\n AND ".$this->db->quoteId('section')." = :xsection"
		."\n ORDER BY ".$this->db->quoteId('parent_id')." ASC, ".$this->db->quoteId('ordering')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcol', $collection, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/********************/
	/* DELETE MENU ITEM */
	/********************/
	public function deleteMenuItem($menu_id) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($menu_id < 1) { return $response; } //just in case
		if (eFactory::getElxis()->acl()->check('com_emenu', 'menu', 'delete') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACTION');
			return $response;
		}

		$items_to_delete = array($menu_id);
		$section = 'frontend';
		$sql = "SELECT ".$this->db->quoteId('menu_id')." FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('section')." = :xsection AND ".$this->db->quoteId('parent_id')." = :xparent";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xparent', $menu_id, PDO::PARAM_INT);
		$stmt->execute();
		$childs = $stmt->fetchCol(0);

		if ($childs) {
			foreach ($childs as $child) {
				$items_to_delete[] = $child;
				$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
				$stmt->bindParam(':xparent', $child, PDO::PARAM_INT);
				$stmt->execute();
				$childs2 = $stmt->fetchCol(0);
				if ($childs2) {
					foreach ($childs2 as $child2) {
						$items_to_delete[] = $child2;
						$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
						$stmt->bindParam(':xparent', $child2, PDO::PARAM_INT);
						$stmt->execute();
						$childs3 = $stmt->fetchCol(0);
						if ($childs3) {
							foreach ($childs3 as $child3) {
								$items_to_delete[] = $child3;
							}
						}
					}
				}
			}
		}

		$sql = "DELETE FROM ".$this->db->quoteId('#__menu')." WHERE ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('menu_id')." = :xmenuid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
			$stmt->bindParam(':xmenuid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$trcategory = 'com_emenu';
		$trelement = 'title';
		$sql = "DELETE FROM ".$this->db->quoteId('#__translations')." WHERE ".$this->db->quoteId('category')." = :xcat"
		."\n AND ".$this->db->quoteId('element')." = :xelement AND ".$this->db->quoteId('elid')." = :xelid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_delete as $item) {
			$stmt->bindParam(':xcat', $trcategory, PDO::PARAM_STR);
			$stmt->bindParam(':xelement', $trelement, PDO::PARAM_STR);
			$stmt->bindParam(':xelid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = true;
		$response['message'] = 'Success';
		return $response;
	}


	/**************************************/
	/* PUBLISH/UNPUBLISH/TOGGLE MENU ITEM */
	/**************************************/
	public function publishItem($menu_id, $publish=-1, $recursive=true) {
		$response = array('success' => false, 'message' => 'Unknown error');
		if ($menu_id < 1) { return $response; } //just in case
		if (eFactory::getElxis()->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			$response['message'] = eFactory::getLang()->get('NOTALLOWACTION');
			return $response;
		}

		if ($publish == -1) { //toggle status
			$sql = "SELECT ".$this->db->quoteId('published')." FROM ".$this->db->quoteId('#__menu')
			."\n WHERE ".$this->db->quoteId('menu_id')." = :xmenuid";
			$stmt = $this->db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xmenuid', $menu_id, PDO::PARAM_INT);
			$stmt->execute();
			$publish = ((int)$stmt->fetchResult() == 1) ? 0 : 1;
		}

		$items_to_publish = array($menu_id);
		if (($publish == 0) && ($recursive === true)) { //apply recursively
			$sql = "SELECT ".$this->db->quoteId('menu_id')." FROM ".$this->db->quoteId('#__menu')." WHERE ".$this->db->quoteId('parent_id')." = :xparent";
			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':xparent', $menu_id, PDO::PARAM_INT);
			$stmt->execute();
			$childs = $stmt->fetchCol(0);
			if ($childs) {
				foreach ($childs as $child) {
					$items_to_publish[] = $child;
					$stmt->bindParam(':xparent', $child, PDO::PARAM_INT);
					$stmt->execute();
					$childs2 = $stmt->fetchCol(0);
					if ($childs2) {
						foreach ($childs2 as $child2) {
							$items_to_publish[] = $child2;
							$stmt->bindParam(':xparent', $child2, PDO::PARAM_INT);
							$stmt->execute();
							$childs3 = $stmt->fetchCol(0);
							if ($childs3) {
								foreach ($childs3 as $child3) {
									$items_to_publish[] = $child3;
								}
							}
						}
					}
				}
			}
		}

		$sql = "UPDATE ".$this->db->quoteId('#__menu')." SET ".$this->db->quoteId('published')." = :xpub"
		."\n WHERE ".$this->db->quoteId('menu_id')." = :xmenuid";
		$stmt = $this->db->prepare($sql);
		foreach ($items_to_publish as $item) {
			$stmt->bindParam(':xpub', $publish, PDO::PARAM_INT);
			$stmt->bindParam(':xmenuid', $item, PDO::PARAM_INT);
			$stmt->execute();
		}

		$response['success'] = true;
		$response['message'] = 'Success';
		return $response;
	}


	/****************************/
	/* GET INSTALLED COMPONENTS */
	/****************************/
	public function getComponents() {
		$sql = "SELECT ".$this->db->quoteId('name').", ".$this->db->quoteId('component')
		."\n FROM ".$this->db->quoteId('#__components')." ORDER BY ".$this->db->quoteId('id')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/************************************/
	/* GET USER GROUPS AND THEIR LEVELS */
	/************************************/
	public function getGroups() {
		$sql = "SELECT * FROM ".$this->db->quoteId('#__groups')." ORDER BY ".$this->db->quoteId('level')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $rows;
	}


	/***************************/
	/* GET CATEGORY'S CHILDREN */
	/***************************/
	public function getCategories($parent_id, $limitstart=0, $order='oa') {
		switch ($order) {
			case 'ta': $orderby = $this->db->quoteId('title').' ASC'; break;
			case 'td': $orderby = $this->db->quoteId('title').' DESC'; break;
			case 'ia': $orderby = $this->db->quoteId('catid').' ASC'; break;
			case 'id': $orderby = $this->db->quoteId('catid').' DESC'; break;
			case 'od': $orderby = $this->db->quoteId('ordering').' DESC, '.$this->db->quoteId('title').' DESC'; break;
			case 'oa': default: $orderby = $this->db->quoteId('ordering').' ASC, '.$this->db->quoteId('title').' ASC'; break;
		}

		$sql = "SELECT ".$this->db->quoteId('catid').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seolink').","
		."\n ".$this->db->quoteId('published').", ".$this->db->quoteId('alevel').""
		."\n FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('parent_id')." = :xparent"
		."\n ORDER BY ".$orderby;
		$stmt = $this->db->prepareLimit($sql, $limitstart, 10);
		$stmt->bindParam(':xparent', $parent_id, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

		if ($rows) {
			$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('catid')." = :xctg";
			$stmt = $this->db->prepare($sql);
			for ($i=0; $i < count($rows); $i++) {
				$catid = $rows[$i]->catid;
				$stmt->bindParam(':xctg', $catid, PDO::PARAM_INT);
				$stmt->execute();
				$rows[$i]->articles = (int)$stmt->fetchResult();
			}
		}

		return $rows;
	}


	/***********************/
	/* GET CATEGORY'S INFO */
	/***********************/
	public function getCategory($catid) {
		$sql = "SELECT ".$this->db->quoteId('parent_id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seolink')
		."\n FROM ".$this->db->quoteId('#__categories')
		."\n WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		return $row;
	}


	/*****************************/
	/* COUNT CATEGORY'S CHILDREN */
	/*****************************/
	public function countCategories($parent_id=0) {
		$sql = "SELECT COUNT(catid) FROM ".$this->db->quoteId('#__categories')." WHERE ".$this->db->quoteId('parent_id')." = :xparent";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xparent', $parent_id, PDO::PARAM_INT);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		return $num;
	}


	/***************************/
	/* GET CATEGORY'S ARTICLES */
	/***************************/
	public function getArticles($catid, $limitstart=0, $order='oa') {
		switch ($order) {
			case 'da': $orderby = $this->db->quoteId('created').' ASC'; break;
			case 'dd': $orderby = $this->db->quoteId('created').' DESC'; break;
			case 'ma': $orderby = $this->db->quoteId('modified').' ASC'; break;
			case 'md': $orderby = $this->db->quoteId('modified').' DESC'; break;
			case 'ta': $orderby = $this->db->quoteId('title').' ASC'; break;
			case 'td': $orderby = $this->db->quoteId('title').' DESC'; break;
			case 'ia': $orderby = $this->db->quoteId('id').' ASC'; break;
			case 'id': $orderby = $this->db->quoteId('id').' DESC'; break;
			case 'od': $orderby = $this->db->quoteId('ordering').' DESC, '.$this->db->quoteId('title').' DESC'; break;
			case 'oa': default: $orderby = $this->db->quoteId('ordering').' ASC, '.$this->db->quoteId('title').' ASC'; break;
		}

		$sql = "SELECT ".$this->db->quoteId('id').", ".$this->db->quoteId('title').", ".$this->db->quoteId('seotitle').","
		."\n ".$this->db->quoteId('published').", ".$this->db->quoteId('alevel').""
		."\n FROM ".$this->db->quoteId('#__content')
		."\n WHERE ".$this->db->quoteId('catid')." = :xcatid"
		."\n ORDER BY ".$orderby;
		$stmt = $this->db->prepareLimit($sql, $limitstart, 10);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_OBJ);
		return $rows;
	}


	/*****************************/
	/* COUNT CATEGORY'S ARTICLES */
	/*****************************/
	public function countArticles($catid=0) {
		$sql = "SELECT COUNT(id) FROM ".$this->db->quoteId('#__content')." WHERE ".$this->db->quoteId('catid')." = :xcatid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcatid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$num = (int)$stmt->fetchResult();
		return $num;
	}


	/********************************/
	/* GET A MENU ITEM ACCESS LEVEL */
	/********************************/
	public function getItemLevel($menu_id) {
		$sql = "SELECT ".$this->db->quoteId('alevel')." FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('menu_id')." = :xmenuid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xmenuid', $menu_id, PDO::PARAM_INT);
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}

}

?>