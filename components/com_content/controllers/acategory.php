<?php 
/**
* @version		$Id: acategory.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class acategoryContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/**************************************/
	/* PREPARE TO DISPLAY CATEGORIES TREE */
	/**************************************/
	public function listcategories() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('CONTENT_CATEGORIES'));

		eFactory::getDocument()->setTitle($eLang->get('CONTENT_CATEGORIES'));
		$this->view->listcategories();
	}


	/***********************************************************/
	/* RETURN LIST OF CATEGORIES FOR GRID IN XML FORMAT (AJAX) */
	/***********************************************************/
	public function getcategories() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$options = array(
			'rp' => 10,
			'page' => 1,
			'sortname' => 'nothing',
			'sortorder' => 'asc',
			'maxlevel' => 10,
			'qtype' => 'title',
			'query' => '',
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'nothing';
		if (!in_array($options['sortname'], array('catid', 'treename'))) { $options['sortname'] = 'nothing'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['maxlevel'] = (isset($_POST['maxlevel'])) ? (int)$_POST['maxlevel'] : 10;
		if ($options['maxlevel'] < 1) { $options['maxlevel'] = 10; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'title';
		if (($options['qtype'] == '') || !in_array($options['qtype'], array('catid', 'title', 'seotitle'))) {
			$options['qtype'] = 'title';
		}

		if ($options['qtype'] == 'catid') {
			$options['query'] = (isset($_POST['query'])) ? (int)$_POST['query'] : 0;
		} else {
			$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
			$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
			if (eUTF::strlen($options['query']) < 3) { $options['query'] = ''; }
		}

		$treeview = true;
		if ($options['sortname'] != 'nothing') {
			$treeview = false;
		} elseif ($options['qtype'] == 'catid') {
			$options['sortname'] = 'treename';
			$treeview = false;
			if ($options['query'] < 1) {
				$this->ajaxHeaders('text/xml');
				echo "<rows>\n";
				echo "<page>1</page>\n";
				echo "<total>0</total>\n";
				echo "</rows>\n";
				exit();
			}
		} elseif ($options['query'] != '') {
			$treeview = false;
			$options['sortname'] = 'treename';
		}

		if ($treeview) {
			$cats = $this->model->getAllCategories();
			$tree = $elxis->obj('tree');
			$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => true));
			$rows = $tree->makeTree($cats, $options['maxlevel']);
			unset($cats, $tree);

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

			if ($rows) {
				$category_ids = array();
				foreach ($rows as $row) { $category_ids[] = $row->catid; }
				$result = $this->model->countCtgArticles($category_ids);
				foreach ($rows as $key => $row) {
					$cid = $row->catid;
					$rows[$key]->acticles = (isset($result[$cid])) ? $result[$cid] : 0;
				}
				unset($result, $category_ids);
			}
		} else {
			$total = $this->model->countAllCategories($options['qtype'], $options['query']);
			$maxpage = ceil($total/$options['rp']);
			if ($maxpage < 1) { $maxpage = 1; }
			if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
			$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
			if ($total > 0) {
				$rows = $this->model->getAllCategories($options);
				if ($rows) {
					$category_ids = array();
					foreach ($rows as $row) { $category_ids[] = $row->catid; }
					$result = $this->model->countCtgArticles($category_ids);
					for ($i=0; $i<count($rows); $i++) {
						$cid = $rows[$i]->catid;
						$rows[$i]->acticles = (isset($result[$cid])) ? $result[$cid] : 0;
					}
					unset($result, $category_ids);
				}
			} else {
				$rows = array();
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 1 + $options['limitstart'];
			$numrows = count($rows);
			$pubicon = $elxis->icon('tick', 16);
			$unpubicon = $elxis->icon('error', 16);
			$upicon = '<img src="'.$elxis->icon('arrowup', 16).'" alt="up" border="0" />';
			$downicon = '<img src="'.$elxis->icon('arrowdown', 16).'" alt="down" border="0" />';
			$articles_link = $elxis->makeAURL('content:articles/');
			$allgroups = $this->model->getGroups();
			$editcat = $elxis->acl()->check('com_content', 'category', 'edit');
			$edit_link = $elxis->makeAURL('content:categories/edit.html');
			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pubicon : $unpubicon;
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);
				$ordertxt = '';
				if (($i > 1) || (($i + $options['limitstart']) > 1)) {
					$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_UP').'" onclick="movecategory('.$row->catid.', 1)">'.$upicon."</a>";
				}
        		if (($i < $numrows) || (($i + $options['limitstart']) < $total)) {
        			$ordertxt .= '<a href="javascript:void(null);" title="'.$eLang->get('MOVE_DOWN').'" onclick="movecategory('.$row->catid.', 0)">'.$downicon."</a>";
       			}
				$title = ($treeview) ? $row->treename : $row->title;

				echo '<row id="'.$row->catid.'">'."\n";
				echo '<cell>'.$row->catid."</cell>\n";
				if ($editcat > 0) {
					echo '<cell><![CDATA[<a href="'.$edit_link.'?catid='.$row->catid.'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$title."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$title."]]></cell>\n";
				}
				echo '<cell><![CDATA[<img src="'.$picon.'" alt="icon" border="0" />]]></cell>'."\n";
       			echo '<cell><![CDATA['.$ordertxt."]]></cell>\n";
				echo '<cell><![CDATA['.$acctxt."]]></cell>\n";
				echo '<cell><![CDATA[<a href="'.$articles_link.'?catid='.$row->catid.'" title="'.$eLang->get('VIEW_ARTICLES').'" style="text-decoration:none;">'.$row->acticles."</a>]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/******************************/
	/* MOVE A CATEGORY UP OR DOWN */
	/******************************/
	public function movecategory() {
		$this->ajaxHeaders('text/plain');
		if (eFactory::getElxis()->acl()->check('com_content', 'category', 'edit') < 1) {
			echo '0|'.eFactory::getLang()->get('NOTALLOWACTION');
			exit();
		}

		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : 0;
		if ($catid < 1) {
			echo '0|Invalid request!';
			exit();
		}
		$moveup = (isset($_POST['moveup'])) ? (int)$_POST['moveup'] : 0;
		$inc = ($moveup == 1) ? -1 : 1;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$row->load($catid);
		if (!$row->catid) {
			echo '0|Category not found!';
			exit();
		}

		$wheres = array(
			array('parent_id', '=', $row->parent_id)
		);
		$ok = $row->move($inc, $wheres);
		if (!$ok) {
			echo '0|'.addslashes($row->getErrorMsg());
		} else {
			echo '1|Success!';
		}
		exit();
	}


	/************************************/
	/* TOGGLE CATEGORY'S PUBLISH STATUS */
	/************************************/
	public function publishcategory() {
		$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : 0;
		$response = $this->model->publishCategory($catid, -1); //includes acl checks
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.addslashes($response['message']);
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/*********************/
	/* DELETE A CATEGORY */
	/*********************/
	public function deletecategory() {
		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : 0;
		$this->ajaxHeaders('text/plain');
		if ($catid < 1) {
			echo '0|Invalid category!';
			exit();
		}
		$response = $this->model->deleteCategory($catid, true); //includes acl check
		if ($response['success'] === false) {
			echo '0|'.addslashes($response['message']);
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}


	/****************/
	/* ADD CATEGORY */
	/****************/
	public function addcategory() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_content', 'category', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('content:/');
			$elxis->redirect($link, $msg, true);
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$row->published = 1;
		$this->editcategory($row);
	}


	/*********************/
	/* ADD/EDIT CATEGORY */
	/*********************/
	public function editcategory($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		if (!$row) {
			if ($elxis->acl()->check('com_content', 'category', 'edit') < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, $msg, true);
			}
			$catid = isset($_GET['catid']) ? (int)$_GET['catid'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$row = new categoriesDbTable();
			if (!$row->load($catid)) {
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, 'Category not found', true);
			}

            $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('content:categories/');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}
			$is_new = false;
		}

		$allctgs = $this->model->getAllCategories();
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$treeitems = $tree->makeTree($allctgs, 10);
		unset($allctgs, $tree);

		$leveltip = $this->makeLevelsTip();

		$pathway->addNode($eLang->get('CONTENT_CATEGORIES'), 'content:categories/');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('NEW_CATEGORY'));
			$pathway->addNode($eLang->get('NEW_CATEGORY'));
		} else {
			$eDoc->setTitle($eLang->get('EDIT_CATEGORY'));
			$pathway->addNode($eLang->get('EDIT_CATEGORY').' '.$row->catid);
		}

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('content:categories/'));

		$this->view->editCategory($row, $treeitems, $leveltip);
	}


	/********************************/
	/* SUGGEST CATEGORY'S SEO TITLE */
	/********************************/
	public function suggestcategory() {
		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : 0;
		$title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$title = eUTF::trim($title);
		if ($title == '') {
			$eLang = eFactory::getLang();
			$msg = addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')));
			$this->ajaxHeaders('text/plain');
			echo '0|'.$msg;
			exit();
		}

		$title = preg_replace('/[!@#;\'\"\.$%^&*(){}\[\]]/u', '', $title);
        $ascii = strtolower(eUTF::utf8_to_ascii($title, ''));
        $ascii = preg_replace("/[^a-z0-9-_\s]/", '', $ascii);
        if (strlen($ascii) < 3) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes(eFactory::getLang()->get('TITLE_FEW_ALPHANUM'));
			exit();
       	}

        $parts = preg_split('/[\s]/', $ascii);
        $nparts = array();
        $length = 0;
        foreach ($parts as $part) {
        	if ($length > 30) { break; }
        	$plength = strlen($part);
            if ($plength > 2) {
            	$nparts[] = $part;
            	$length += $plength;
			}
        }

        $seotitle = implode('-', $nparts);
        unset($parts, $nparts, $length, $ascii);

		$result = $this->validateCatSEO($seotitle, $catid);
		if ($result['success'] === false) {
			for($i=2; $i<6; $i++) {
				if ($i < 5) {
					$newseo = $seotitle.$i;
				} else {
					$newseo = ($catid > 0) ? $seotitle.$catid : $seotitle.$i;
				}
				$res = $this->validateCatSEO($newseo, $catid);
				if ($res['success'] === true) {
					$seotitle = $newseo;
					break;
				}
			}
			if ($res['success'] === false) {
				$seotitle = $seotitle.'-'.rand(1000, 9999);
			}
		}

		$this->ajaxHeaders('text/plain');
		echo '1|'.$seotitle;
		exit();
	}


	/**********************/
	/* VALIDATE SEO TITLE */
	/**********************/
	public function validatecategory() {
		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : 0;
		$seotitle = (isset($_POST['seotitle'])) ? $_POST['seotitle'] : '';
		$res = $this->validateCatSEO($seotitle, $catid);
		$this->ajaxHeaders('text/plain');
		if ($res['success'] === true) {
			echo '1|'.addslashes($res['message']);
		} else {
			echo '0|'.addslashes($res['message']);
		}
		exit();
	}


	/*********************************/
	/* VALIDATE CATEGORY'S SEO TITLE */
	/*********************************/
	private function validateCatSEO($seotitle, $catid) {
		$eLang = eFactory::getLang();

		$result = array('success' => false, 'message' => 'The SEO Title is invalid!');
        if (trim($seotitle) == '') {
			$result['message'] = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('SEOTITLE'));
			return $result;
       	}
        $ascii = preg_replace("/[^a-z0-9\-\_]/", '', $seotitle);
        if ($ascii != $seotitle) {
        	$result['message'] = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('SEOTITLE'));
			return $result;
        }
		if (strlen($seotitle) < 3) {
			$result['message'] = $eLang->get('SEOTITLE_FEW_ALPHANUM');
			return $result;
		}
		if (is_dir(ELXIS_PATH.'/'.$seotitle.'/')) {
			$result['message'] = sprintf($eLang->get('FOLDER_NAMED'), $seotitle);
			return $result;
		}
		if (is_dir(ELXIS_PATH.'/components/com_'.$seotitle)) {
			$result['message'] = sprintf($eLang->get('COMPONENT_NAMED'), $seotitle);
			return $result;
		}
		$c =  $this->model->countComponentsByRoute($seotitle);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('COMPONENT_ROUTED'), $seotitle);
			return $result;
		}
		$c = $this->model->countCategoriesBySEO($seotitle, $catid);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('OTHER_CATEGORY_SEO'), $seotitle);
			return $result;
		}

		$msg = $eLang->get('VALID');
		$result = array('success' => true, 'message' => $msg);
		return $result;
	}


	/*****************/
	/* SAVE CATEGORY */
	/*****************/
	public function savecategory() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : 0;
		if ($catid < 0) { $catid = 0; }

		$redirurl = $elxis->makeAURL('content:categories/');
		if ($catid > 0) {
			if ($elxis->acl()->check('com_content', 'category', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_content', 'category', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCON-0006', $eLang->get('REQDROPPEDSEC'));
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new categoriesDbTable();
		$old_ordering = -1;
		$old_published = 0;
		$old_seolink = '';
		$old_image = '';
		if ($catid > 0) {
			if (!$row->load($catid)) { $elxis->redirect($redirurl, 'Category was not found!', true); }
			$old_ordering = $row->ordering;
			$old_published = $row->published;
			$old_seolink = $row->seolink;
			$old_image = trim($row->image);
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if (isset($_POST['transl_description']) && isset($_POST['transorig_description'])) {
			if ($_POST['transl_description'] != $elxis->getConfig('LANG')) {
				$row->description = $_POST['transorig_description'];
			}
		}

		if ($catid > 0) {
			$redirurledit = $elxis->makeAURL('content:categories/edit.html?catid='.$catid);
		} else {
			$redirurledit = $elxis->makeAURL('content:categories/add.html');
		}

		if ($elxis->acl()->check('com_content', 'category', 'publish') < 1) {
			$row->published = $old_published;
		}

		$seoresult = $this->validateCatSEO($row->seotitle, $catid);
		if ($seoresult['success'] === false) {
			$elxis->redirect($redirurledit, $seoresult['message'], true);
		}
		
		if ($row->parent_id > 0) {
			$parent_seolink = $this->model->categorySEOLink($row->parent_id);
			if (!$parent_seolink) {
				$elxis->redirect($redirurl, 'Could not determine the SEO Link of the parent category!', true);
			}
			$row->seolink = $parent_seolink.$row->seotitle.'/';
			unset($parent_seolink);
		} else {
			$row->seolink = $row->seotitle.'/';
		}

		$row->alevel = (int)$row->alevel;
		if ($row->parent_id > 0) {
			$parent_alevel = $this->model->getCategoryLevel($row->parent_id);
			if ($parent_alevel > $row->alevel) { $row->alevel = $parent_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('content:categories/');
			$elxis->redirect($redirurl, 'You can not manage a category with higher access level than yours!', true);
		}

		$pint = array('ctg_img_empty', 'ctg_layout', 'ctg_show', 'ctg_subcategories', 'ctg_subcategories_cols', 
		'ctg_print', 'ctg_featured_num', 'ctg_featured_img', 'ctg_featured_dateauthor', 'ctg_short_num', 'ctg_short_cols', 
		'ctg_short_img', 'ctg_short_dateauthor', 'ctg_short_text', 'ctg_links_num', 'ctg_links_cols', 'ctg_links_header', 
		'ctg_links_dateauthor', 'ctg_pagination', 'ctg_pagination', 'ctg_nextpages_style', 'comments');
		$pstr = array('ctg_ordering', 'ctg_mods_pos');
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new  elxisParameters('', '', 'component');
		$row->params = $params->toString($_POST['params'], $pint, $pstr);
		unset($params, $pint, $pstr);

		$img_rel_path = 'media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $img_rel_path = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}

		if (isset($_POST['delimage'])) {
			$delimage = (is_array($_POST['delimage'])) ? (int)$_POST['delimage'][0] : (int)$_POST['delimage'];
			if ($delimage === 1) {
				if ($old_image != '') {
					$ok = $eFiles->deleteFile($old_image);
					if ($ok) {
						$old_image = '';
						$row->image = null;
					}
				}
			}
		}

		if (isset($_FILES) && isset($_FILES['image']) && ($_FILES['image']['name'] != '') && ($_FILES['image']['error'] == 0) && ($_FILES['image']['size'] > 0)) {
			$type = $_FILES['image']['type'];
			if (in_array($type, array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/jp2'))) {
				$extension = $eFiles->getExtension($_FILES['image']['name']);
				$filename = $eFiles->getFilename($_FILES['image']['name']);
				$filename = preg_replace("/[^a-zA-Z0-9\-\_]/", '', $filename);
				if ($filename == '') { $filename = ($row->catid > 0) ? 'category'.$row->catid : 'category'.rand(1000, 2000); }
				if (file_exists(ELXIS_PATH.'/'.$img_rel_path.'categories/'.$filename.'.'.$extension)) {
					$filename = ($row->catid > 0) ? 'category'.$row->catid : 'category'.rand(1000, 2000);
				}
				$relpath = $img_rel_path.'categories/'.$filename.'.'.$extension;
				$ok = $eFiles->upload($_FILES['image']['tmp_name'], $relpath);
				if ($ok) {
					$row->image = $relpath;
					if ($old_image != '') { $eFiles->deleteFile($old_image); }
				}
			}
		}

		$ok = ($catid > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}
		
		if ($catid > 0) {
			if ($old_seolink != $row->seolink) {
				$this->model->rebuildSEOLinks($catid, $row->seolink);
			}
			if (($old_image != '') && ($old_image != $row->image)) {
				eFactory::getFiles()->deleteFile($old_image);
			}
		}

		$reorder = ($catid == 0) ? true : ($old_ordering <> $row->ordering) ? true : false;
		if ($reorder) {
			$wheres = array(array('parent_id', '=', $row->parent_id));
			$row->reorder($wheres, true);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('content:categories/edit.html?catid='.$row->catid) : $elxis->makeAURL('content:categories/');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}

}

?>