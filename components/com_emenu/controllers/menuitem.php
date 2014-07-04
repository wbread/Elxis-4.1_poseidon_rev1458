<?php 
/**
* @version		$Id: menuitem.php 1332 2012-10-19 16:31:52Z datahell $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class menuitemEmenuController extends emenuController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/**************************************/
	/* PREPARE TO DISPLAY MENU ITEMS LIST */
	/**************************************/
	public function listmenuitems() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();

		$segments = eFactory::getURI()->getSegments();
		$collection = str_ireplace('.html', '', $segments[1]);
		if ($this->model->validateCollection($collection) === false) {
			$link = $elxis->makeAURL('emenu:/');
			$elxis->redirect($link, 'Requested collection does not exist!', true);
		}

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($collection);

		eFactory::getDocument()->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$collection);
		eFactory::getDocument()->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');

		$this->view->listmenuitems($collection);
	}


	/***********************************************************/
	/* RETURN LIST OF MENU ITEMS FOR GRID IN XML FORMAT (AJAX) */
	/***********************************************************/
	public function getitems() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$options = array(
			'rp' => 10, 
			'page' => 1,
			'qtype' => '', 
			'collection' => '', 
			'maxlevel' => 10,
			'query' => '', 
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['maxlevel'] = (isset($_POST['maxlevel'])) ? (int)$_POST['maxlevel'] : 10;
		if ($options['maxlevel'] < 1) { $options['maxlevel'] = 10; }
		if (isset($_POST['collection'])) {
			$options['collection'] = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['collection']));
		}

		$items = $this->model->getMenuItems($options['collection']);

		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'menu_id', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => true));
		$rows = $tree->makeTree($items, $options['maxlevel']);
		unset($items, $tree);

		$total = count($rows);
		if ($total > 1) {
			$maxpage = ceil($total/$options['rp']);
			if ($maxpage < 1) { $maxpage = 1; }
			if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
			$options['limitstart'] = (($options['page'] - 1) * $options['rp']);

			if ($total > $options['rp']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['rp'];
				$k = 0;
				foreach ($rows as $key => $row) {
					if ($k < $options['limitstart']) { $k++; continue; }
					if ($k >= $end) { break; }
					$limitrows[] = $row;
					$k++;
				}
				$rows = $limitrows;
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 1 + $options['limitstart'];
			$numrows = count($rows);
			$secimg = '<img src="'.$elxis->icon('lock', 16).'" alt="secure" title="SSL" border="0" />';
			$unsecimg = '<img src="'.$elxis->icon('unlock', 16).'" alt="not secure" title="'.$eLang->get('NO').'" border="0" />';
			$pubicon = $elxis->icon('tick', 16);
			$unpubicon = $elxis->icon('error', 16);
			$upicon = '<img src="'.$elxis->icon('arrowup', 16).'" alt="up" border="0" />';
			$downicon = '<img src="'.$elxis->icon('arrowdown', 16).'" alt="down" border="0" />';
			$allgroups = $this->model->getGroups();
			$canedit = ($elxis->acl()->check('com_emenu', 'menu', 'edit') > 0) ? true : false;
			$edit_base = $elxis->makeAURL('emenu:mitems/edit.html');
			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pubicon : $unpubicon;
				switch ($row->expand) {
					case 2: $lvl = $eLang->get('FULL'); break;
					case 1: $lvl = $eLang->get('LIMITED'); break;
					case 0: default: $lvl = $eLang->get('NO'); break;
				}

				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				switch ($row->menu_type) {
					case 'link': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('LINK').'</span>'; break;
					case 'separator': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('SEPARATOR').'</span>'; break;
					case 'wrapper': $typetxt = '<span title="'.$row->link.'">'.$eLang->get('WRAPPER').'</span>'; break;
					default: $typetxt = '<span title="'.$row->link.'">'.$row->menu_type.'</span>'; break;
				}

				$ordertxt = '';
				if (($i > 1) || (($i + $options['limitstart']) > 1)) {
					$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_UP').'" onclick="moveitem('.$row->menu_id.', 1)">'.$upicon."</a>";
				}
        		if (($i < $numrows) || (($i + $options['limitstart']) < $total)) {
        			$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_DOWN').'" onclick="moveitem('.$row->menu_id.', 0)">'.$downicon."</a>";
       			}

				echo '<row id="'.$row->menu_id.'">'."\n";
				echo '<cell>'.$i."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$edit_base.'?menu_id='.$row->menu_id.'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$row->treename."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$row->treename."]]></cell>\n";
				}
				echo '<cell><![CDATA[<img src="'.$picon.'" alt="icon" border="0" />]]></cell>'."\n";
				echo '<cell><![CDATA['.$typetxt."]]></cell>\n";
       			echo '<cell><![CDATA['.$ordertxt."]]></cell>\n";
				echo '<cell><![CDATA['.$lvl."]]></cell>\n";
				echo '<cell><![CDATA['.$acctxt."]]></cell>\n";
				echo '<cell>'.$row->menu_id."</cell>\n";
				if ($row->secure == 1) {
					echo '<cell><![CDATA['.$secimg."]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$unsecimg."]]></cell>\n";
				}
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/*******************************/
	/* MOVE A MENU ITEM UP OR DOWN */
	/*******************************/
	public function moveitem() {
		$this->ajaxHeaders('text/plain');
		if (eFactory::getElxis()->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			echo '0|'.eFactory::getLang()->get('NOTALLOWACTION');
			exit();
		}

		$menu_id = (isset($_POST['menu_id'])) ? (int)$_POST['menu_id'] : 0;
		if ($menu_id < 1) {
			echo '0|Invalid request!';
			exit();
		}
		$moveup = (isset($_POST['moveup'])) ? (int)$_POST['moveup'] : 0;
		$inc = ($moveup == 1) ? -1 : 1;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$row->load($menu_id);
		if (!$row->menu_id) {
			echo '0|Menu item not found!';
			exit();
		}

		$wheres = array(
			array('collection', '=', $row->collection),
			array('parent_id', '=', $row->parent_id),
			array('section', '=', $row->section)
		);
		$ok = $row->move($inc, $wheres);
		if (!$ok) {
			echo '0|'.addslashes($row->getErrorMsg());
		} else {
			echo '1|Success!';
		}
		exit();
	}


	/****************************/
	/* DELETE A MENU COLLECTION */
	/****************************/
	public function deleteitem() {
		$menu_id = (isset($_POST['menu_id'])) ? (int)$_POST['menu_id'] : 0;
		$this->ajaxHeaders('text/plain');
		if ($menu_id < 1) {
			echo '0|Invalid menu item!';
			exit();
		}
		$response = $this->model->deleteMenuItem($menu_id); //includes acl check
		if ($response['success'] === false) {
			echo '0|'.addslashes($response['message']);
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/********************************/
	/* TOGGLE ITEM'S PUBLISH STATUS */
	/********************************/
	public function publishitem() {
		$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
		$response = $this->model->publishItem($menu_id, -1); //includes acl checks
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.addslashes($response['message']);
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/**********************/
	/* ADD/EDIT MENU ITEM */
	/**********************/
	public function edititem($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		if (!$row) {
			if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('emenu:/');
				$elxis->redirect($link, $msg, true);
			}
			$menu_id = isset($_GET['menu_id']) ? (int)$_GET['menu_id'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
			$row = new menuDbTable();
			if (!$row->load($menu_id)) {
				$link = $elxis->makeAURL('emenu:/');
				$elxis->redirect($link, 'Menu item not found', true);
			}

            $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}
			$is_new = false;
			if (trim($row->link) == '') { $row->link = '/'; } //needed to save problem on link validation for frontpage
		}

		$leveltip = $this->makeLevelsTip();
		$treeitems = $this->collectionTree($row->collection);
		$components = $this->componentsList();

		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($row->collection, 'emenu:mitems/'.$row->collection.'.html');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$eLang->get('NEW'));
			$pathway->addNode($eLang->get('NEW'));
		} else {
			$eDoc->setTitle($eLang->get('MANAGE_MENU_ITEMS').' - '.$eLang->get('EDIT'));
			$pathway->addNode($eLang->get('EDIT').' '.$row->menu_id);
		}

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html'));

		$component = '';
		if (!$is_new) {
			if ($row->menu_type == 'link') {
				$component = $this->findComponent($row->link);
			}
		}

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');
        $eDoc->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');
        if ($component != '') {
        	$js = '$(document).ready(function(){emenu_pickcomponent(\'com_'.$component.'\');});';
			$eDoc->addScript($js);
		}

		$this->view->editMenuItem($row, $treeitems, $components, $leveltip, $component);
	}


	/*****************/
	/* ADD MENU ITEM */
	/*****************/
	public function additem() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('emenu:/');
			$elxis->redirect($link, $msg, true);
		}
		$collection = trim(filter_input(INPUT_GET, 'collection', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($collection == '') { $collection = 'maimenu'; }
		$type = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($type == '') || !in_array($type, array('link', 'url', 'separator', 'wrapper'))) { $type = 'link'; }

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$row->collection = $collection;
		$row->section = 'frontend';
		$row->target = '';
		$row->file = 'index.php';
		$row->published = 1;
		$row->menu_type = $type;
		if (($type == 'separator') || ($type == 'url')) { $row->file = null; }

		$this->edititem($row);
	}


	/************************************/
	/* FIND COMPONENT FROM AN ELXIS URI */
	/************************************/
	private function findComponent($elxis_uri) {
		$parts = preg_split('#\:#', $elxis_uri, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts) { return 'content'; } //frontpage
		if (strlen($parts[0]) < 3) { array_shift($parts); } //language
		$c = count($parts);
		if ($c > 2) {
			return '';
		} else if ($c == 2) {
			$component = $parts[0];
		} else {
			$component = 'content';
		}

		if (!file_exists(ELXIS_PATH.'/components/com_'.$component.'/'.$component.'.php')) { return ''; }
		return $component;
	}


	/*******************/
	/* MAKE LEVELS TIP */
	/*******************/
	private function makeLevelsTip() {
		$eLang = eFactory::getLang();
		$groups = $this->model->getGroups();
		$elements = array();
		if ($groups) {
			foreach ($groups as $group) {
				switch ($group['gid']) {
					case 7: $name = $eLang->get('GUEST'); break;
					case 6: $name = $eLang->get('EXTERNALUSER'); break;
					case 5: $name = $eLang->get('USER'); break;
					case 1: $name = $eLang->get('ADMINISTRATOR'); break;
					default: $name = $group['groupname']; break;
				}
				$elements[] = (int)$group['level'].': '.$name;
			}
			return implode(', ', $elements);
		}
		return '';
	}


	/*************************************/
	/* MAKE COLLECTION'S MENU ITEMS TREE */
	/*************************************/
	private function collectionTree($collection) {
		$rows = $this->model->getMenuItems($collection);
		$tree = eFactory::getElxis()->obj('tree');
		$tree->setOptions(array('itemid' => 'menu_id', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$items = $tree->makeTree($rows, 10);
		return $items;
	}


	/*************************************/
	/* MAKE COLLECTION'S MENU ITEMS TREE */
	/*************************************/
	private function componentsList() {
		$eLang = eFactory::getLang();
		$rows = array();
		$rows['com_content'] = $eLang->get('CONTENT');
		$comps = $this->model->getComponents();
		if ($comps) {
			foreach ($comps as $comp) {
				if ($comp['component'] == 'com_content') { continue; }
				$cmp = $comp['component'];
				$str = strtoupper(str_replace('com_', '', $comp['component']));
				$name = ($eLang->exist($str)) ? $eLang->get($str) : $comp['name'];
				$rows[$cmp] = $name;
			}
		}
		return $rows;
	}


	/**************************/
	/* PREPARE LINK GENERATOR */
	/**************************/
	public function linkgenerator() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACTION')."</div>\n";
			return;
		}

		$component = trim(filter_input(INPUT_POST, 'component', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$no_public = array('com_cpanel', 'com_languages', 'com_emenu', 'com_emedia', 'com_etranslator', 'com_extmanager');

		if (strpos($component, 'com_') === false) {
			echo '<div class="elx_error">Invalid component!'."</div>\n";
			exit();
		}
		if (in_array($component, $no_public)) {
			echo '<div class="elx_warning">'.$eLang->get('COMP_NO_PUBLIC_IFACE')."</div>\n";
			exit();
		}

		$cname = str_replace('com_', '', $component);
		if (!file_exists(ELXIS_PATH.'/components/'.$component.'/'.$cname.'.php')) {
			echo '<div class="elx_error">Invalid component!'."</div>\n";
			exit();
		}

		$xmlmenus = null;
		if (file_exists(ELXIS_PATH.'/components/'.$component.'/'.$cname.'.menu.xml')) {
			elxisLoader::loadFile('includes/libraries/elxis/menu.xml.php');
			$xmenu = new elxisXMLMenu(null);
			$xmlmenus = $xmenu->getAllMenus($cname, 'frontend', $elxis->getConfig('LANG'));
			unset($xmenu);
		}

		if ($cname == 'search') {
			$items = $this->searchGenerator();	
		} else {
			$items = array();
			$items[] = $this->componentfpLink($cname);
		}

		$this->view->linkGeneratorOutput($items, $xmlmenus, $cname);
		exit();
	}


	/*********************************/
	/* LINK TO COMPONENT'S FRONTPAGE */
	/*********************************/
	private function componentfpLink($cname) {
		$eLang = eFactory::getLang();

		$item = new stdClass;
		$item->name = sprintf($eLang->get('COMP_FRONTPAGE'), ucfirst($cname));
		$item->title = ucfirst($cname);
		$item->link = $cname.':/';
		$item->secure = 0;
		$item->alevel = 0;
		return $item;
	}


	/**********************************************/
	/* CREATE STANDARD LINKS FOR COMPONENT SEARCH */
	/**********************************************/
	private function searchGenerator() {
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$component_title = 'Search';
		if ($eLang->currentLang() == eFactory::getElxis()->getConfig('LANG')) {
			$component_title = $eLang->get('SEARCH');
		}
		$items = array();
		$item = new stdClass;
		$item->name = sprintf($eLang->get('COMP_FRONTPAGE'), $eLang->get('SEARCH'));
		$item->title = $component_title;
		$item->link = 'search:/';
		$item->secure = 0;
		$item->alevel = 0;
		$items[] = $item;

		$engs = $eFiles->listFolders('components/com_search/engines/');
		if ($engs) {
			foreach ($engs as $eng) {
				if (!file_exists(ELXIS_PATH.'/components/com_search/engines/'.$eng.'/'.$eng.'.engine.php')) { continue; }
				$item = new stdClass;
				$item->name = $eLang->get('SEARCH').' '.ucfirst($eng);
				$item->title = $component_title.' '.ucfirst($eng);
				$item->link = 'search:'.$eng.'.html';
				$item->secure = 0;
				$item->alevel = 0;
				$items[] = $item;
			}
		}
		return $items;
	}


	/*******************/
	/* CONTENT BROWSER */
	/*******************/
	public function browser() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACTION')."</div>\n";
			return;
		}

		$options = array(
			'catid' => 0,
			'page' => 1,
			'perpage' => 10,
			'total' => 0,
			'maxpage' => 1,
			'limitstart' => 0,
			'type' => 'c',
			'order' => 'oa',
			'articles' => 0
		);

		$options['catid'] = (isset($_GET['catid'])) ? (int)$_GET['catid'] : 0;
		if ($options['catid'] < 0) { $options['catid'] = 0; }
		$options['page'] = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['type'] = isset($_GET['t']) ? $_GET['t'] : 'c';
		if ($options['type'] != 'a') { $options['type'] = 'c'; }
		$options['order'] = isset($_GET['o']) ? $_GET['o'] : 'oa';
		if ($options['type'] == 'a') {
			if (!in_array($options['order'], array('oa', 'od', 'ta', 'td', 'ia', 'id', 'da', 'dd', 'ma', 'md'))) {
				$options['order'] = 'oa';
			}
		} else {
			if (!in_array($options['order'], array('oa', 'od', 'ta', 'td', 'ia', 'id'))) {
				$options['order'] = 'oa';
			}
		}

		if ($options['type'] == 'a') {
			$options['total'] = $this->model->countArticles($options['catid']);
		} else {
			$options['total'] = $this->model->countCategories($options['catid']);
			$options['articles'] = $this->model->countArticles($options['catid']);
		}

		if ($options['total'] > $options['perpage']) {
			$options['maxpage'] = ceil($options['total']/$options['perpage']);
			if ($options['maxpage'] < 1) { $options['maxpage'] = 1; }
			if ($options['page'] > $options['maxpage']) { $options['page'] = $options['maxpage']; }
			$options['limitstart'] = (($options['page'] - 1) * $options['perpage']);
		} else {
			$options['page'] = 1;
		}

		if ($options['type'] == 'a') {
			$rows = $this->model->getArticles($options['catid'], $options['limitstart'], $options['order']);
		} else {
			$rows = $this->model->getCategories($options['catid'], $options['limitstart'], $options['order']);
		}

		$allgroups = $this->model->getGroups();

		$paths = $this->makePath($options['catid']);

        eFactory::getDocument()->addScriptLink($elxis->secureBase().'/components/com_emenu/js/emenu.js');
        eFactory::getDocument()->addStyleLink($elxis->secureBase().'/components/com_emenu/css/emenu'.$eLang->getinfo('RTLSFX').'.css');

		if ($options['type'] == 'a') {
			$this->view->articlesBrowser($rows, $paths, $options, $allgroups);
		} else {
			$this->view->categoriesBrowser($rows, $paths, $options, $allgroups);
		}
	}


	/***************************/
	/* MAKE CATEGORIES PATHWAY */
	/***************************/
	private function makePath($catid=0) {
		$items = array();
		$p = $catid;
		while($p > 0) {
			$row = $this->model->getCategory($p);
			if (!$row) { $p = 0; break; }
			$item = new stdClass;
			$item->catid = $p;
			$item->title = $row['title'];
			$item->seolink = $row['seolink'];
			$items[] = $item;
			$p = $row['parent_id'];
		}

		$item = new stdClass;
		$item->catid = 0;
		$item->title = eFactory::getLang()->get('ROOT');
		$item->seolink = '';
		$items[] = $item;

		return array_reverse($items);
	}


	/******************/
	/* SAVE MENU ITEM */
	/******************/
	public function saveitem() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$task = isset($_POST['task']) ? $_POST['task'] : 'save';
		if ($task != 'apply') { $task = 'save'; }
		$menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : 0;
		if ($menu_id < 0) { $menu_id = 0; }

		$redirurl = $elxis->makeAURL('emenu:/');
		if ($menu_id > 0) {
			if ($elxis->acl()->check('com_emenu', 'menu', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEME-0006', $eLang->get('REQDROPPEDSEC'));
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/menu.db.php');
		$row = new menuDbTable();
		$old_ordering = -1;
		if ($menu_id > 0) {
			if (!$row->load($menu_id)) { $elxis->redirect($redirurl, 'Menu item was not found!', true); }
			$old_ordering = $row->ordering;
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($menu_id > 0) {
			$redirurledit = $elxis->makeAURL('emenu:mitems/edit.html?menu_id='.$menu_id);
		} else {
			$redirurledit = $elxis->makeAURL('emenu:mitems/add.html?collection='.$row->collection.'&type='.$row->menu_type);
		}

		$row->alevel = (int)$row->alevel;
		if ($row->parent_id > 0) {
			$parent_alevel = $this->model->getItemLevel($row->parent_id);
			if ($parent_alevel > $row->alevel) { $row->alevel = $parent_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
			$elxis->redirect($redirurl, $eLang->get('NOTALLOWACCITEM'), true);
		}

		$ok = ($menu_id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}

		$reorder = ($menu_id == 0) ? true : ($old_ordering <> $row->ordering) ? true : false;
		if ($reorder) {
			$wheres = array(array('section', '=', $row->section), array('collection', '=', $row->collection), array('parent_id', '=', $row->parent_id));
			$row->reorder($wheres, true);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('emenu:mitems/edit.html?menu_id='.$row->menu_id) : $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}
	
?>