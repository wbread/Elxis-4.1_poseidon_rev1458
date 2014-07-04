<?php 
/**
* @version		$Id: aarticle.php 1407 2013-04-10 19:03:03Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aarticleContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/************************************/
	/* PREPARE TO DISPLAY ARTICLES LIST */
	/************************************/
	public function listarticles() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$categories = $this->model->getAllCategories();

		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$categories_tree = $tree->makeTree($categories, 10);
		unset($allctgs, $tree);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('ARTICLES'));

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');
        $eDoc->addStyle('.flexigrid div.sDiv { display:block; }');
		$eDoc->setTitle($eLang->get('ARTICLES'));

		$this->view->listarticles($categories, $categories_tree);
	}


	/*********************************************************/
	/* RETURN LIST OF ARTICLES FOR GRID IN XML FORMAT (AJAX) */
	/*********************************************************/
	public function getarticles() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$options = array(
			'rp' => 10,
			'page' => 1,
			'sortname' => 'created',
			'sortorder' => 'desc',
			'catid' => -1,
			'qtype' => 'title',
			'query' => '',
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'created';
		if (!in_array($options['sortname'], array('id', 'catid', 'title', 'published', 'ordering', 'created', 'hits', 'created_by_name'))) { $options['sortname'] = 'created'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'desc';
		if ($options['sortorder'] != 'asc') { $options['sortorder'] = 'desc'; }
		$options['catid'] = (isset($_POST['catid'])) ? (int)$_POST['catid'] : -1;
		if ($options['catid'] < -1) { $options['catid'] = -1; }
		$options['qtype'] = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : 'title';
		if (($options['qtype'] == '') || !in_array($options['qtype'], array('id', 'title', 'seotitle', 'created_by_name', 'modified_by_name'))) {
			$options['qtype'] = 'title';
		}

		if ($options['qtype'] == 'id') {
			$options['query'] = (isset($_POST['query'])) ? (int)$_POST['query'] : 0;
		} else {
			$options['query'] = filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
			$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
			if (eUTF::strlen($options['query']) < 3) { $options['query'] = ''; }
		}

		if (isset($_GET['q'])) { //search from module
			$options['qtype'] = 'title';
			$options['query'] = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
			$options['query'] = eUTF::trim(preg_replace($pat, '', $options['query']));
			if (eUTF::strlen($options['query']) < 3) { $options['query'] = ''; }
		}

		$total = $this->model->countAllArticles($options);
		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getAllArticles($options);
			$category_ids = array();
			foreach ($rows as $row) {
				if ($row->catid > 0) { $category_ids[] = $row->catid; }
			}
			if ($category_ids) {
				$category_ids = array_unique($category_ids);
				$categories = $this->model->getArrayCategories($category_ids);
			} else {
				$categories = array();
			}
			unset($category_ids);
		} else {
			$rows = array();
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
			$saveicon = $elxis->icon('save', 16);
			$canedit = ($elxis->acl()->check('com_content', 'article', 'edit') > 0) ? true : false;
			$canedit_cat = ($elxis->acl()->check('com_content', 'category', 'edit') > 0) ? true : false;
			$editart_base = $elxis->makeAURL('content:articles/edit.html');
			$editcat_base = $elxis->makeAURL('content:categories/edit.html');
			$allgroups = $this->model->getGroups();
			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pubicon : $unpubicon;
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);
				$title = (eUTF::strlen($row->title) > 30) ? eUTF::substr($row->title, 0, 27).'...' : $row->title;
       			$cdate = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4'));
				$author = (eUTF::strlen($row->created_by_name) > 15) ? '<span title="'.$row->created_by_name.'">'.eUTF::substr($row->created_by_name, 0, 12).'...</span>' : $row->created_by_name;

				if ($row->catid == 0) {
					$category = $eLang->get('NONE');
				} else {
					$category = (isset($categories[ $row->catid ])) ? $categories[ $row->catid ] : $eLang->get('CATEGORY').' '.$row->catid;
					$ctg_text = (eUTF::strlen($category) > 20) ? eUTF::substr($category, 0, 17).'...' : $category;
					if ($canedit_cat) {
						$category = '<a href="'.$editcat_base.'?catid='.$row->catid.'" title="'.$eLang->get('EDIT').' '.$category.'" style="text-decoration:none;">'.$ctg_text.'</a>';
					} else {
						$category = '<span title="'.$category.'">'.$ctg_text.'</span>';
					}
				}

				echo '<row id="'.$row->id.'">'."\n";
				echo '<cell>'.$row->id."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$editart_base.'?id='.$row->id.'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$title."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$title."]]></cell>\n";
				}
				echo '<cell><![CDATA[<img src="'.$picon.'" alt="icon" border="0" />]]></cell>'."\n";
				echo '<cell><![CDATA['.$category."]]></cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<input type="text" name="orderbox'.$row->id.'" id="orderbox'.$row->id.'" size="4" maxlength="7" value="'.$row->ordering.'" dir="ltr" onchange="markOrderUnsaved('.$row->id.');" /> ';
					echo '<a href="javascript:void(null);" onclick="setArticleOrder('.$row->id.');" title="'.$eLang->get('SAVE_ORDERING').'"><img src="'.$saveicon.'" alt="save" border="0" align="bottom" /></a>]]></cell>'."\n";
				} else {
					echo '<cell>'.$row->ordering."</cell>\n";
				}
				echo '<cell><![CDATA['.$acctxt."]]></cell>\n";
				echo '<cell><![CDATA['.$cdate."]]></cell>\n";
				echo '<cell><![CDATA['.$author."]]></cell>\n";
				echo '<cell>'.$row->hits."</cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/**************************/
	/* SET ARTICLE'S ORDERING */
	/**************************/
	public function setorder() {
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');
		$myaccess = $elxis->acl()->check('com_content', 'article', 'edit');
		if ($myaccess < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$ordering = (isset($_POST['ordering'])) ? (int)$_POST['ordering'] : 0;

		if (($id < 1) || ($ordering < 1)) {
			echo '0|Invalid request!';
			exit();
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		$row->load($id);
		if (!$row->id) {
			echo '0|'.addslashes(eFactory::getLang()->get('ARTICLE_NOT_FOUND'));
			exit();
		}

		if (($myaccess === 1) && ($row->created_by != $elxis->user()->uid)) {
			echo '0|'.addslashes(eFactory::getLang()->get('ACTION_ONLY_OWN_ARTS'));
			exit();
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACCITEM'));
			exit();
		}

		if ($row->ordering == $ordering) {
			echo '1|Success!';
			exit();
		}
		$row->ordering = $ordering;
		if (!$row->update()) {
			echo '0|'.addslashes($row->getErrorMsg());
			exit();
		}

		$wheres = array(array('catid', '=', $row->catid));
		$row->reorder($wheres, false);
		echo '1|Success!';
		exit();
	}


	/*********************************/
	/* (UN)PUBLISH MULTIPLE ARTICLES */
	/*********************************/
	public function publisharticles() {
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');
		$myaccess = $elxis->acl()->check('com_content', 'article', 'publish');
		if ($myaccess < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$str = trim(filter_input(INPUT_POST, 'ids', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($str == '') {
			echo '0|Invalid request!';
			exit();
		}

		$arrs = explode(',',$str);
		$ids = array();
		foreach ($arrs as $arr) {
			$id = (int)$arr;
			if ($id > 0) { $ids[] = $id; }
		}

		if (count($ids) == 0) {
			echo '0|Invalid request!';
			exit();
		}

		$rows = $this->model->getArticlesById($ids);
		if (!$rows) {
			echo '0|Requested article(s) not found!';
			exit();
		}

		$toggle_items = array();
		foreach ($rows as $row) {
			if (($myaccess === 1) && ($row['created_by'] != $elxis->user()->uid)) { continue; }
			$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) { continue; }
			$id = (int)$row['id'];
			$published = (intval($row['published']) == 1) ? 0 : 1;
			$toggle_items[$id] = $published;
		}
				
		if (count($toggle_items) == 0) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACCITEM'));
			exit();
		}

		$this->model->setArticlesStatus($toggle_items);

		echo '1|Success!';
		exit();
	}


	/*****************************/
	/* DELETE MULTIPLE ARTICLES */
	/****************************/
	public function deletearticles() {
		$elxis = eFactory::getElxis();

		$this->ajaxHeaders('text/plain');

		$myaccess = $elxis->acl()->check('com_content', 'article', 'delete');
		if ($myaccess < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$str = trim(filter_input(INPUT_POST, 'ids', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($str == '') {
			echo '0|Invalid request!';
			exit();
		}

		$arrs = explode(',',$str);
		$ids = array();
		foreach ($arrs as $arr) {
			$id = (int)$arr;
			if ($id > 0) { $ids[] = $id; }
		}

		if (count($ids) == 0) {
			echo '0|Invalid request!';
			exit();
		}

		$rows = $this->model->getArticlesById($ids);
		if (!$rows) {
			echo '0|Requested article(s) not found!';
			exit();
		}

		$delete_items = array();
		$delete_images = array();
		foreach ($rows as $row) {
			if (($myaccess === 1) && ($row['created_by'] != $elxis->user()->uid)) { continue; }
			$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) { continue; }
			$id = (int)$row['id'];
			$delete_items[] = $id;
			if (trim($row['image']) != '') { $delete_images[] = $row['image']; }
		}

		if (count($delete_items) == 0) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACCITEM'));
			exit();
		}

		$this->model->deleteArticles($delete_items);
		if ($delete_images) {
			foreach ($delete_images as $delete_image) {
				$this->deleteArticleImage($delete_image);
			}
		}

		echo '1|Success!';
		exit();
	}


	/***************/
	/* ADD ARTICLE */
	/***************/
	public function addarticle() {
		$elxis = eFactory::getElxis();
		if ($elxis->acl()->check('com_content', 'article', 'add') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('content:articles/');
			$elxis->redirect($link, $msg, true);
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
		$row = new contentDbTable();
		$row->published = 1;
		$this->editarticle($row);
	}


	/********************/
	/* ADD/EDIT ARTICLE */
	/********************/
	public function editarticle($row=null) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$is_new = true;
		$ordering = array();
		$ordering['total'] = 0;
		$ordering['start'] = -1;
		$ordering['end'] = 9999;
		$ordering['articles'] = array();
		$comments = array();
		if (!$row) {
			$myaccess = $elxis->acl()->check('com_content', 'article', 'edit');
			if ($myaccess < 1) {
				$msg = $eLang->get('NOTALLOWACTION');
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $msg, true);
			}
			$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$row = new contentDbTable();
			if (!$row->load($id)) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, 'Article not found', true);
			}

			if (($myaccess === 1) && ($row->created_by != $elxis->user()->uid)) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $eLang->get('ACTION_ONLY_OWN_ARTS'), true);
			}

			$allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
			if (!$allowed) {
				$link = $elxis->makeAURL('content:articles/');
				$elxis->redirect($link, $eLang->get('NOTALLOWACCITEM'), true);
			}

			$ordering = array();
			$ordering['total'] = $this->model->countCtgArticles($row->catid);
			if ($ordering['total'] > 0) {
				if ($ordering['total'] > 50) {
					$ordering['start'] = $row->ordering - 25;
					if ($ordering['start'] < 2) { $ordering['start'] = 0; }
					if (($ordering['start'] + 50) > $ordering['total']) {
						$ordering['start'] = $ordering['total'] - 50;
					}
					$ordering['end'] = $ordering['start'] + 51;
				} else {
					$ordering['start'] = 0;
					$ordering['end'] = $ordering['total'] + 1;
				}
				$ordering['articles'] = $this->model->getOrderingArticles($row->catid, $ordering['start'], 0, 50);	
			}

			$comments = $this->model->fetchComments($row->id, false);
			$is_new = false;
		}

		$allctgs = $this->model->getAllCategories();
		$tree = $elxis->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$treeitems = $tree->makeTree($allctgs, 10);
		unset($allctgs, $tree);

		$leveltip = $this->makeLevelsTip();

		$pathway->addNode($eLang->get('ARTICLES'), 'content:articles/');
		if ($is_new) {
			$eDoc->setTitle($eLang->get('NEW_ARTICLE'));
			$pathway->addNode($eLang->get('NEW_ARTICLE'));
		} else {
			$eDoc->setTitle($eLang->get('EDIT_ARTICLE'));
			$pathway->addNode($eLang->get('EDIT_ARTICLE').' '.$row->id);
		}

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/acontent.js');

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('content:articles/'));

		$this->view->editArticle($row, $treeitems, $leveltip, $ordering, $comments);
	}


	/***************************/
	/* DELETE A COMMENT (AJAX) */
	/***************************/
	public function deletecomment() {
		$elxis = eFactory::getElxis();

		$myaccess = (int)$elxis->acl()->check('com_content', 'comments', 'delete');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		$this->ajaxHeaders('text/plain');
		if ($id < 1) { echo '0|Invalid request'; exit(); }
		if ($myaccess < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) { echo '0|The requested comment was not found!'; exit(); }
		$allowed = false;
		if (($myaccess == 2) || (($myaccess == 1) && ($comment->uid == $elxis->user()->uid))) { $allowed = true; }
		if (!$allowed) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$artid = (int)$comment->elid;
		$row = $this->model->getArticlesById($artid);
		if (!$row) {
			echo '0|'.addslashes(eFactory::getLang()->get('ARTICLE_NOT_FOUND'));
			exit();
		}

		$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACCITEM'));
			exit();
		}

		$ok = $this->model->deleteComment($id);
		if ($ok) {
			echo '1|Success!';
		} else {
			echo '0|Could not delete comment!';
		}
		exit();
	}


	/****************************/
	/* PUBLISH A COMMENT (AJAX) */
	/****************************/
	public function publishcomment() {
		$elxis = eFactory::getElxis();

		$myaccess = (int)$elxis->acl()->check('com_content', 'comments', 'publish');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		$this->ajaxHeaders('text/plain');
		if ($id < 1) { echo '0|Invalid request'; exit(); }
		if ($myaccess < 1) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$comment = $this->model->fetchComment($id);
		if (!$comment) { echo '0|The requested comment was not found!'; exit(); }
		if ($comment->published == 1) { echo '1|Success!'; exit(); }
		$allowed = false;
		if (($myaccess == 2) || (($myaccess == 1) && ($comment->uid == $elxis->user()->uid))) { $allowed = true; }
		if (!$allowed) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACTION'));
			exit();
		}

		$artid = (int)$comment->elid;
		$row = $this->model->getArticlesById($artid);
		if (!$row) {
			echo '0|'.addslashes(eFactory::getLang()->get('ARTICLE_NOT_FOUND'));
			exit();
		}

		$allowed = (($row['alevel'] <= $elxis->acl()->getLowLevel()) || ($row['alevel'] == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			echo '0|'.addslashes(eFactory::getLang()->get('NOTALLOWACCITEM'));
			exit();
		}

		$ok = $this->model->publishComment($id);
		if ($ok) {
			$seolink = '';
			if ($row['catid'] > 0) { $seolink = (string)$this->model->categorySEOLink($row['catid']); }
			$link = $elxis->makeURL('content:'.$seolink.$row['seotitle'].'.html');
			$this->notifyPublishComment($comment->author, $comment->email, $row['title'], $link);
			echo '1|Success!';
		} else {
			echo '0|Could not publish comment!';
		}
		exit();
	}


	/*******************************/
	/* SUGGEST ARTICLE'S SEO TITLE */
	/*******************************/
	public function suggestarticle() {
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
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

		$result = $this->validateArtSEO($seotitle, $id);
		if ($result['success'] === false) {
			for($i=2; $i<6; $i++) {
				if ($i < 5) {
					$newseo = $seotitle.$i;
				} else {
					$newseo = ($id > 0) ? $seotitle.$id : $seotitle.$i;
				}
				$res = $this->validateArtSEO($newseo, $id);
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
	public function validatearticle() {
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$seotitle = (isset($_POST['seotitle'])) ? $_POST['seotitle'] : '';
		$res = $this->validateArtSEO($seotitle, $id);
		$this->ajaxHeaders('text/plain');
		if ($res['success'] === true) {
			echo '1|'.addslashes($res['message']);
		} else {
			echo '0|'.addslashes($res['message']);
		}
		exit();
	}


	/*********************************/
	/* VALIDATE ARTICLE'S SEO TITLE */
	/*********************************/
	private function validateArtSEO($seotitle, $id) {
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

		if (is_file(ELXIS_PATH.'/'.$seotitle.'.html')) {
			$result['message'] = sprintf($eLang->get('FILE_NAMED'), $seotitle.'.html');
			return $result;
		}

		$reserved_names = array('index', 'feeds', 'contenttools',  'tags', 'send-to-friend');
		if (in_array($seotitle, $reserved_names)) {
			$result['message'] = sprintf($eLang->get('SEOTITLE_RESERVED'), $seotitle);
			return $result;
		}

		$c = $this->model->countArticlesBySEO($seotitle, $id);
		if ($c > 0) {
			$result['message'] = sprintf($eLang->get('OTHER_ARTICLE_SEO'), $seotitle);
			return $result;
		}

		$msg = $eLang->get('VALID');
		$result = array('success' => true, 'message' => $msg);
		return $result;
	}


	/****************/
	/* SAVE ARTICLE */
	/****************/
	public function savearticle() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		$redirurl = $elxis->makeAURL('content:articles/');
		if ($id > 0) {
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		} else {
			if ($elxis->acl()->check('com_content', 'article', 'add') < 1) {
				$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
			}
		}

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CCON-0013', $eLang->get('REQDROPPEDSEC'));
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		$row = new contentDbTable();
		$old_image = '';
		if ($id > 0) {
			if (!$row->load($id)) { $elxis->redirect($redirurl, $eLang->get('ARTICLE_NOT_FOUND'), true); }
			$oldrow = $row;
			$old_image = trim($row->image);
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 2) {
				if ($row->created_by != $elxis->user()->uid) {
					$elxis->redirect($redirurl, $eLang->get('NOTALLOWACTION'), true);
				}
			}
		}

		if (!$row->bind($_POST)) {
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		if ($id > 0) {
			$redirurledit = $elxis->makeAURL('content:articles/edit.html?id='.$id);
			$row->hits = $oldrow->hits;
			$row->created = $oldrow->created;
			$row->created_by = $oldrow->created_by;
			$row->created_by_name = $oldrow->created_by_name;
		} else {
			$redirurledit = $elxis->makeAURL('content:articles/add.html');
			$row->hits = 0;
			$row->created = $eDate->getDate();
			$row->created_by = $elxis->user()->uid;
			$row->created_by_name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;
		}

		if ($elxis->acl()->check('com_content', 'article', 'publish') < 1) {
			if ($id > 0) {
				$row->published = $oldrow->published;
			} else {
				$row->published = 0;
			}
		}

		$seoresult = $this->validateArtSEO($row->seotitle, $id);
		if ($seoresult['success'] === false) {
			$elxis->redirect($redirurledit, $seoresult['message'], true);
		}

		$row->catid = (int)$row->catid;
		$row->alevel = (int)$row->alevel;
		if ($row->catid > 0) {
			$category_alevel = $this->model->getCategoryLevel($row->catid);
			if ($category_alevel > $row->alevel) { $row->alevel = $category_alevel; }
		}

        $allowed = (($row->alevel <= $elxis->acl()->getLowLevel()) || ($row->alevel == $elxis->acl()->getExactLevel())) ? true : false;
		if (!$allowed) {
			$redirurl = $elxis->makeAURL('content:articles/');
			$elxis->redirect($redirurl, 'You can not manage an article with higher access level than yours or place it in a category with higher access level than yours!', true);
		}

		if (isset($_POST['transl_introtext']) && isset($_POST['transorig_introtext'])) {
			if ($_POST['transl_introtext'] != $elxis->getConfig('LANG')) {
				$row->introtext = $_POST['transorig_introtext'];
			}
		}
		if (isset($_POST['transl_maintext']) && isset($_POST['transorig_maintext'])) {
			if ($_POST['transl_maintext'] != $elxis->getConfig('LANG')) {
				$row->maintext = $_POST['transorig_maintext'];
			}
		}

		if (trim($row->metakeys) == '') {
			$metakeys = $elxis->obj('keywords');
			$keywords = $metakeys->getKeywords($row->title.' '.$row->introtext.' '.$row->maintext, 15, 4, $elxis->getConfig('LANG'));
			if ($keywords) {
				$row->metakeys = implode(',', $keywords);
			}
			unset($metakeys, $keywords);
		} else {
			$keywords = filter_var($row->metakeys, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
        	$keywords = str_replace(array('–', '(', ')', '+', ':', '.', '?', '!', '_', '*', '-', '"', '\'', '@', '#', '$', '%', '&', '[',']', '{', '}', '<', '>', ';'), '', $keywords); 
			$keywords = eUTF::strtolower(eUTF::trim($keywords));
			$arr = explode(',', $keywords);
			$final = array();
			if ($arr) {
				foreach ($arr as $str) {
					if (eUTF::strlen($str) > 2) { $final[] = $str; }
				}
			}
			$row->metakeys = ($final) ? implode(',', $final) : null;
			unset($final, $arr, $keywords);	
		}

		$pint = array('art_dateauthor', 'art_dateauthor_pos', 'art_img', 'art_print', 'art_email', 'art_hits', 'art_comments', 'art_tags', 'art_chain');
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new  elxisParameters('', '', 'component');
		$row->params = $params->toString($_POST['params'], $pint, array());
		unset($params, $pint);

		if (isset($_POST['delimage'])) {
			$delimage = (is_array($_POST['delimage'])) ? (int)$_POST['delimage'][0] : (int)$_POST['delimage'];
			if ($delimage === 1) {
				if ($old_image != '') {
					$ok = $this->deleteArticleImage($old_image);
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
				$base = 'media/images/';
				if (defined('ELXIS_MULTISITE')) {
					if (ELXIS_MULTISITE > 1) {
						$base = 'media/images/site'.ELXIS_MULTISITE.'/';
					}
				}
				$extension = $eFiles->getExtension($_FILES['image']['name']);
				$filename = $eFiles->getFilename($_FILES['image']['name']);
				$filename = preg_replace("/[^a-zA-Z0-9\-\_]/", '', $filename);
				if ($filename == '') { $filename = $row->seotitle; }
				$updir = $this->determineUploadFolder();
				if (file_exists(ELXIS_PATH.'/'.$base.$updir.'/'.$filename.'.'.$extension)) {
					$filename = ($row->id > 0) ? 'article'.$row->id : 'article'.rand(1000, 2000);
				}
				$relpath = $base.$updir.'/'.$filename.'.'.$extension;
				$ok = $eFiles->upload($_FILES['image']['tmp_name'], $relpath);
				if ($ok) {
					$this->makeMediumThumb($base.$updir.'/', $filename, $extension);
					$row->image = $base.$updir.'/'.$filename.'.'.$extension;
					if ($old_image != '') {
						$this->deleteArticleImage($old_image);
					}
				}
			}
		}

		$ok = ($id > 0) ? $row->update() : $row->insert();
		if (!$ok) {
			$elxis->redirect($redirurledit, $row->getErrorMsg(), true);
		}
		
		if ($id > 0) {
			if (($old_image != '') && ($old_image != $row->image)) {
				$this->deleteArticleImage($old_image);
			}
		}

		$reorder = false;
		if ($id == 0) {
			$reorder = true;
		} else {
			if (($oldrow->catid <> $row->catid) || ($oldrow->ordering <> $row->ordering)) {
				$reorder = true;
			}
		}
		if ($reorder) {
			$wheres = array(array('catid', '=', $row->catid));
			$row->reorder($wheres, true);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('content:articles/edit.html?id='.$row->id) : $elxis->makeAURL('content:articles/?catid='.$row->catid);
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/************************/
	/* COPY ARTICLES (AJAX) */
	/************************/
	public function copyarticles() {
		$this->copymovearticles(0);
	}


	/************************/
	/* MOVE ARTICLES (AJAX) */
	/************************/
	public function movearticles() {
		$this->copymovearticles(1);
	}


	/********************************/
	/* COPY OR MOVE ARTICLES (AJAX) */
	/********************************/
	public function copymovearticles($move=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($move == 0) {
			if ($elxis->acl()->check('com_content', 'article', 'edit') < 2) {
				$this->ajaxHeaders('text/plain');
				echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
				exit();
			}
		} else {
			if ($elxis->acl()->check('com_content', 'article', 'add') < 2) {
				$this->ajaxHeaders('text/plain');
				echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
				exit();
			}
		}

		$aids_str = isset($_POST['aids']) ? trim($_POST['aids']) : '';
		$catid = isset($_POST['catid']) ? (int)$_POST['catid'] : -1;
		if (($catid < 0) || ($aids_str == '')) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('ACTION_FAILED'));
			exit();
		}

		$aids = array();
		$aids_arr = explode(',', $aids_str);
		if ($aids_arr) {
			foreach ($aids_arr as $aid) { if (intval($aid) > 0) { $aids[] = $aid; } }
		}

		if (!$aids) {
			$this->ajaxHeaders('text/plain');
			echo '0|No articles selected';
			exit();
		}

		if ($catid > 0) {//check category exists
			elxisLoader::loadFile('includes/libraries/elxis/database/tables/categories.db.php');
			$cat = new categoriesDbTable();
			if (!$cat->load($catid)) {
				$this->ajaxHeaders('text/plain');
				echo '0|Category not found!';
				exit();
			}
			unset($cat);
		}

		$success_actions = 0;
		$now = eFactory::getDate()->getDate();
		$uid = $elxis->user()->uid;
		$author = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname;

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/content.db.php');
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/translations.db.php');
		foreach ($aids as $id) {
			$row = new contentDbTable();
			if (!$row->load($id)) { continue; }
			if ($move == 1) {
				$row->catid = $catid;
				$row->modified = $now;
				$row->modified_by = $uid;
				$row->modified_by_name = $author;
				if ($row->update()) { $success_actions++; }
				unset($row);
			} else {
				$row->forceNew(true);
				if ($catid == $row->catid) { $row->title = $row->title.' 2'; }
				$row->catid = $catid;
				$row->seotitle = $row->seotitle.'2';
				$row->created = $now;
				$row->created_by = $uid;
				$row->created_by_name = $author;
				$row->modified = '1970-01-01 00:00:00';
				$row->modified_by = 0;
				$row->modified_by_name = '';
				$row->hits = 0;
				if ($row->insert()) {
					$success_actions++;
					//copy translations
					$trans = $this->model->allArticleTrans($id);
					if ($trans) {
						foreach ($trans as $tran) {
							$trow = new translationsDbTable();
							$trow->category = 'com_content';
							$trow->element = $tran['element'];
							$trow->language = $tran['language'];
							$trow->elid = $row->id;
							$trow->translation = $tran['translation'];
							$trow->insert();
							unset($trow);
						}
					}
					unset($trans);
				}
				unset($row);
			}
		}

		$this->ajaxHeaders('text/plain');
		if ($success_actions > 0) {
			echo '1|'.addslashes($eLang->get('ACTION_SUCCESS'));
		} else {
			echo '0|'.addslashes($eLang->get('ACTION_FAILED'));
		}
		exit();
	}

}

?>