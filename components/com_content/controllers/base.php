<?php 
/**
* @version		$Id: base.php 1421 2013-04-30 17:31:26Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contentController {

	protected $view = null;
	protected $model = null;
	protected $lng = '';
	protected $translate = false;
	protected $format = 'html';
	protected $apc = 0;
	protected $apc_ttl = 600;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	protected function __construct($view=null, $model=null, $format='') {
		$elxis = eFactory::getElxis();

		$this->view = $view;
		$this->model = $model;
		$this->apc = $elxis->getConfig('APC');
		if ($format != '') { $this->format = $format; }
		if ($elxis->getConfig('MULTILINGUISM') == 1) {
			$this->lng = eFactory::getURI()->getUriLang();
			if ($this->lng != '') { $this->translate = true; }
		}
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/*******************************************/
	/* LOAD CATEGORY INFORMATION FROM DATABASE */
	/*******************************************/
	protected function loadCategory($seotitle, $onlypublic=false) {
		if ($this->apc == 1) {
			$apc_name = preg_replace('/[^a-z0-9]/i', '', $seotitle);
			$row = elxisAPC::fetch($apc_name, 'category'.$this->lng);
			if ($row !== false) { return $row; }
		}

		$row = $this->model->fetchCategory($seotitle, $onlypublic);
		if (!$this->translate) {
			if ($row && ($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store($apc_name, 'category'.$this->lng, $row, $this->apc_ttl);
			}
			return $row;
		}
		if (!$row) { return false; }

		$translations = $this->model->categoryTranslate($row->catid, $this->lng);
		if (!$translations) {
			if (($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store($apc_name, 'category'.$this->lng, $row, $this->apc_ttl);
			}
			return $row;
		}

		foreach ($translations as $element => $translation) {
			switch($element) {
				case 'category_title':
					$row->title = $translation;
				break;
				case 'category_description':
					$row->description = $translation;
				break;
				default: break;
			}
		}

		if ($this->apc == 1) {
			if ($row->alevel == 0) {
				elxisAPC::store($apc_name, 'category'.$this->lng, $row, $this->apc_ttl);
			}
		}

		return $row;
	}


	/******************************************************************/
	/* MAKE A PATHWAY TREE TO CATEGORY (RETURN FALSE ON ACCESS ERROR) */
	/******************************************************************/
	protected function loadCategoryTree($catid, $curctg=null, $onlypublic=false) {
		if ($this->apc == 1) {
			$tree = elxisAPC::fetch('tree'.$catid, 'categorytree'.$this->lng);
			if ($tree !== false) { return $tree; }
		}

		$tree = $this->model->categoryTree($catid, $curctg, $onlypublic);
		if (!$this->translate) {
			if ($tree && ($this->apc == 1)) {
				elxisAPC::store('tree'.$catid, 'categorytree'.$this->lng, $tree, $this->apc_ttl);
			}
			return $tree;
		}
		if (!$tree) { return $tree; }

		$elids = array();
		foreach ($tree as $ctg) { $elids[] = $ctg->catid; }
		$translations = $this->model->categoriesTranslate($elids, $this->lng);
		if (!$translations) {
			if ($this->apc == 1) {
				elxisAPC::store('tree'.$catid, 'categorytree'.$this->lng, $tree, $this->apc_ttl);
			}
			return $tree;
		}

		foreach ($tree as $idx => $ctg) {
			$cid = $ctg->catid;
			if (isset($translations[$cid])) {
				$tree[$idx]->title = $translations[$cid];
			}
		}

		if ($this->apc == 1) {
			elxisAPC::store('tree'.$catid, 'categorytree'.$this->lng, $tree, $this->apc_ttl);
		}

		return $tree;
	}


	/***********************************/
	/* GET A CATEGORY'S SUB-CATEGORIES */
	/***********************************/
	protected function loadSubcategories($catid) {
		if ($this->apc == 1) {
			$rows = elxisAPC::fetch('subcategories'.$catid, 'category'.$this->lng);
			if ($rows !== false) { return $rows; }
		}

		$rows = $this->model->fetchSubCategories($catid);
		if (!$this->translate) {
			if ($rows && ($this->apc == 1)) {
				elxisAPC::store('subcategories'.$catid, 'category'.$this->lng, $rows, $this->apc_ttl);
			}
			return $rows;
		}
		if (!$rows) { return $rows; }

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row->catid; }
		$translations = $this->model->categoriesTranslate($elids, $this->lng);
		if (!$translations) {
			if ($this->apc == 1) {
				elxisAPC::store('subcategories'.$catid, 'category'.$this->lng, $rows, $this->apc_ttl);
			}
			return $rows;
		}

		foreach ($rows as $idx => $row) {
			$cid = $row->catid;
			if (isset($translations[$cid])) {
				$rows[$idx]->title = $translations[$cid];
			}
		}

		if ($this->apc == 1) {
			elxisAPC::store('subcategories'.$catid, 'category'.$this->lng, $rows, $this->apc_ttl);
		}

		return $rows;
	}


	/*****************************/
	/* GET A CATEGORY'S ARTICLES */
	/*****************************/
	protected function loadArticles($catid, $limitstart, $limit, $order, $onlypublic=false) {
		$rows = $this->model->fetchArticles($catid, $limitstart, $limit, $order, $onlypublic);
		if (!$this->translate) { return $rows; }
		if (!$rows) { return $rows; }

		$elids = array();
		foreach ($rows as $row) { $elids[] = $row->id; }
		$translations = $this->model->articlesTranslate($elids, $this->lng);
		if (!$translations) { return $rows; }

		foreach ($translations as $trans) {
			$id = (int)$trans['elid'];
			$element = $trans['element'];
			switch($element) {
				case 'title': $rows[$id]->title = $trans['translation']; break;
				case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
				case 'introtext': $rows[$id]->introtext = $trans['translation']; break;
				case 'caption': $rows[$id]->caption = $trans['translation']; break;
				default: break;
			}
		}

		return $rows;
	}


	/**********************************/
	/* MAKE PATHWAY FROM A TREE ARRAY */
	/**********************************/
	protected function makeTreePathway($tree) {
		if (!is_array($tree)) { return; }
		$c = count($tree);
		if ($c == 0) { return; }
		$pathway = eFactory::getPathway();
		$k = 1;
		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		foreach ($tree as $element) {
			if ($k == $c) {
				if ($page > 1) {
					$pathway->addNode($element->title, $element->link);
					$pathway->addNode(eFactory::getLang()->get('PAGE').' '.$page);
				} else {
					$pathway->addNode($element->title);
				}
			} else {
				$pathway->addNode($element->title, $element->link);
			}
			$k++;
		}
	}


	/************************************/
	/* CALCULATE CATEGORY META KEYWORDS */
	/************************************/
	protected function categoryKeywords($title, $articles, $tree, $subcategories) {
		$metaKeys = array();
		$keystring = $title;
		$c = count($tree);
		if ($c > 1) {
			$idx = $c - 2;
			$keystring .= ' '.$tree[$idx]->title;
		}

    	$keystring = eUTF::str_replace('\'', '', $keystring);
    	$keystring = eUTF::str_replace('"', '', $keystring);
    	$keystring = eUTF::str_replace('?', '', $keystring);
    	$keystring = eUTF::str_replace(';', '', $keystring);
    	$keystring = eUTF::str_replace('!', '', $keystring);
    	$keystring = eUTF::str_replace(':', '', $keystring);
    	$keystring = eUTF::str_replace(',', '', $keystring);
    	$keystring = eUTF::str_replace('.', '', $keystring);
		$parts = preg_split('/[\s]/', $keystring, -1, PREG_SPLIT_NO_EMPTY);
		foreach($parts as $part) {
			if (eUTF::strlen($part) > 3) { $metaKeys[] = $part; }
		}

		if ($articles) {
			$c = count($articles);
			if ($c > 0) {
				$keys_per_article = ($c > 5) ? 2 : 3;
				foreach ($articles as $article) {
					if (count($metaKeys) > 19) { break; }
					if (trim($article->metakeys) != '') {
						$parts = explode(',', $article->metakeys);
						if ($parts) {
							$i = 0;
							foreach($parts as $part) {
								if ($i >= $keys_per_article) { break; }
								$metaKeys[] = $part;
								$i++;
							}
						}
					}
				}
			}
		}

		if (count($metaKeys) < 6) {
			if ($subcategories && (count($subcategories) > 0)) {
				$keystring = '';
				foreach ($subcategories as $subcategory) {
					$keystring .= ' '.$subcategory->title;
				}

    			$keystring = eUTF::str_replace('\'', '', $keystring);
    			$keystring = eUTF::str_replace('"', '', $keystring);
    			$keystring = eUTF::str_replace('?', '', $keystring);
    			$keystring = eUTF::str_replace(';', '', $keystring);
    			$keystring = eUTF::str_replace('!', '', $keystring);
    			$keystring = eUTF::str_replace(':', '', $keystring);
    			$keystring = eUTF::str_replace(',', '', $keystring);
    			$keystring = eUTF::str_replace('.', '', $keystring);
				$keystring = eUTF::strtolower($keystring);
				$parts = preg_split('/[\s]/', $keystring, -1, PREG_SPLIT_NO_EMPTY);
				foreach($parts as $part) {
					if (count($metaKeys) > 19) { break; }
					if (eUTF::strlen($part) > 3) { $metaKeys[] = $part; }
				}
			}
		}

		$metaKeys = array_unique($metaKeys);
		return $metaKeys;
	}


	/*********************************************/
	/* GET COMBINED CATEGORY PARAMETERS INSTANCE */
	/*********************************************/
	protected function combinedCategoryParams($local_str='') {
		$local_str = (string)$local_str;
		$global_str = (string)$this->model->componentParams();

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');
		//make sure some params are defined
		$params->def('popup_window', 0);
		$params->def('live_bookmarks', 'rss');
		$params->def('feed_items', 10);
		$params->def('feed_cache', 6);
		$params->def('comments', 0); //there is no global setting for the component
		$params->def('comments_src', 0);
		if ((int)$params->def('comments_src', 0) < 0) { $params->set('comments_src', 0); }
		$params->def('disqus_shortname', '');
		if ($params->get('disqus_shortname') == '') { $params->set('comments_src', 0); }
		$params->def('disqus_developer', 0);
		if ((int)$params->def('img_medium_width', 240) < 10) {
			$params->set('img_medium_width', 240);
		}
		if ((int)$params->def('img_thumb_width', 100) < 10) {
			$params->set('img_thumb_width', 100);
		}
		$params->def('ctg_subcategories', 2);
		$params->def('ctg_print', 0);
		$params->def('ctg_mods_pos', 'category');
		$params->def('ctg_img_empty', 1);
		if (ELXIS_MOBILE == 1) {
			$params->set('ctg_print', 0);
			$params->set('ctg_img_empty', 0);
			$params->set('ctg_show', 0);
			$params->set('ctg_layout', 0);
			$params->set('ctg_subcategories_cols', 1);
			$params->set('ctg_nextpages_style', 1);
			//$params->set('ctg_featured_num', 0);
			$params->set('ctg_featured_dateauthor', 1);
			$params->set('ctg_featured_img', 2);
			$params->set('ctg_featured_more', 0);
			//$params->set('ctg_short_num', 10);
			$params->set('ctg_short_cols', 1);
			$params->set('ctg_short_img', 2);
			$params->set('ctg_short_dateauthor', 1);
			$params->set('ctg_short_text', 220);
			$params->set('ctg_short_more', 0);
			//$params->set('ctg_links_num', 0);
			$params->set('ctg_links_cols', 1);
			$params->set('ctg_links_dateauthor', 0);
			$params->set('ctg_pagination', 1);
		}

		if ($local_str == '') { return $params; }
		//combine global and local parameters
		$local = new elxisParameters($local_str, '', 'component');
		$ikeys = array(
		'ctg_show', 'ctg_layout', 'ctg_subcategories', 'ctg_subcategories_cols', 'ctg_print', 'ctg_img_empty', 
		'ctg_nextpages_style', 'ctg_featured_num', 'ctg_featured_img', 'ctg_featured_dateauthor', 'ctg_featured_more', 
		'ctg_short_num', 'ctg_short_cols', 'ctg_short_img', 'ctg_short_dateauthor', 'ctg_short_text', 'ctg_short_more', 
		'ctg_links_num', 'ctg_links_cols', 'ctg_links_header', 'ctg_links_dateauthor', 'ctg_pagination', 'comments'
		);

		foreach ($ikeys as $ikey) {
			$v = (int)$local->get($ikey, -1);
			if ($v  > -1) { $params->set($ikey, $v); }
		}

		if (ELXIS_MOBILE == 1) {
			$params->set('ctg_print', 0);
			$params->set('ctg_img_empty', 0);
			$params->set('ctg_show', 0);
			$params->set('ctg_layout', 0);
			$params->set('ctg_subcategories_cols', 1);
			$params->set('ctg_nextpages_style', 1);
			//$params->set('ctg_featured_num', 0);
			$params->set('ctg_featured_dateauthor', 1);
			$params->set('ctg_featured_img', 2);
			$params->set('ctg_featured_more', 0);
			//$params->set('ctg_short_num', 10);
			$params->set('ctg_short_cols', 1);
			$params->set('ctg_short_img', 2);
			$params->set('ctg_short_dateauthor', 1);
			$params->set('ctg_short_text', 220);
			$params->set('ctg_short_more', 0);
			//$params->set('ctg_links_num', 0);
			$params->set('ctg_links_cols', 1);
			$params->set('ctg_links_dateauthor', 0);
			$params->set('ctg_pagination', 1);
		}

		$v = trim($local->get('ctg_ordering', ''));
		if ($v != '') { $params->set('ctg_ordering', $v); }
		$v = trim($local->get('ctg_mods_pos', ''));
		if (($v != '') && ($v != '_global_')) {
			$params->set('ctg_mods_pos', $v);
		}

		unset($local);
		return $params;
	}


	/***********************************/
	/* LOAD ARTICLE DATA FROM DATABASE */
	/***********************************/
	protected function loadArticle($seotitle='', $id=0) {
		if ($this->apc == 1) {
			$apc_name = ($id > 0) ? 'article'.$id : preg_replace('/[^a-z0-9]/i', '', $seotitle);
			$row = elxisAPC::fetch($apc_name, 'article'.$this->lng);
			if ($row !== false) { return $row; }
		}

		$row = $this->model->fetchArticle($seotitle, $id);
		if (!$this->translate) {
			if ($row && ($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store($apc_name, 'article'.$this->lng, $row, $this->apc_ttl);
			}
			return $row;
		}
		if (!$row) { return false; }

		$translations = $this->model->articleTranslate($row->id, $this->lng);
		if (!$translations) {
			if (($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store($apc_name, 'article'.$this->lng, $row, $this->apc_ttl);
			}
			return $row;
		}

		foreach ($translations as $element => $translation) {
			switch($element) {
				case 'title': $row->title = $translation; break;
				case 'subtitle': $row->subtitle = $translation; break;
				case 'introtext': $row->introtext = $translation; break;
				case 'maintext': $row->maintext = $translation; break;
				case 'caption': $row->caption = $translation; break;
				case 'metakeys': $row->metakeys = $translation; break;
				default: break;
			}
		}

		if (($this->apc == 1) && ($row->alevel == 0)) {
			elxisAPC::store($apc_name, 'article'.$this->lng, $row, $this->apc_ttl);
		}

		return $row;
	}


	/********************************************/
	/* GET COMBINED ARTICLE PARAMETERS INSTANCE */
	/********************************************/
	protected function combinedArticleParams($local_str='', $catid=0) {
		$local_str = (string)$local_str;
		$global_str = (string)$this->model->componentParams();

		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');

		//make sure some params are defined
		$params->def('popup_window', 0);
		$params->def('live_bookmarks', 'rss');
		//$params->def('feed_items', 10); //not need in article page
		//$params->def('feed_cache', 6); //not need in article page
		if ((int)$params->def('img_medium_width', 240) < 10) {
			$params->set('img_medium_width', 240);
		}
		if ((int)$params->def('img_thumb_width', 100) < 10) {
			$params->set('img_thumb_width', 100);
		}
		$params->def('art_img', 2);
		$params->def('ctg_img_empty', 1);
		$params->def('comments', 0);
		$params->def('art_comments', 0);
		$params->def('comments_src', 0);
		if ((int)$params->def('comments_src', 0) < 0) { $params->set('comments_src', 0); }
		$params->def('disqus_shortname', '');
		if ($params->get('disqus_shortname') == '') { $params->set('comments_src', 0); }
		$params->def('disqus_developer', 0);

		if ($catid > 0) {
			$local_ctg_str = $this->model->categoryParams($catid);
			if (trim($local_ctg_str) != '') {
				$ctgparams = new elxisParameters($local_ctg_str, '', 'component');
				$c = (int)$ctgparams->get('comments', 0);
				$v = trim($ctgparams->get('ctg_ordering', ''));
				unset($ctgparams);
				$params->set('art_comments', $c);
				$params->set('comments', $c);				
				if ($v != '') { $params->set('ctg_ordering', $v); }
				unset($c, $v);
			}
		}

		if (ELXIS_MOBILE == 1) {
			$params->set('popup_window', 0);
			$params->set('art_dateauthor', 1);
			$params->set('art_dateauthor_pos', 1);
			$params->set('art_img', 4);
			$params->set('art_hits', 0);
			$params->set('art_print', 0);
			$params->set('art_email', 0);
			$params->set('ctg_img_empty', 0);
			$params->set('art_comments', 0);
			//$params->set('art_chain', 1);
			//$params->set('art_tags', 1);
		}

		if ($local_str == '') { return $params; }

		$local = new elxisParameters($local_str, '', 'component');
		$ikeys = array('art_dateauthor', 'art_dateauthor_pos', 'art_img', 'art_tags', 'art_hits', 'art_chain', 'art_print', 'art_email', 'art_comments');
		foreach ($ikeys as $ikey) {
			$v = (int)$local->get($ikey, -1);
			if ($v  > -1) {
				$params->set($ikey, $v);
				if ($ikey == 'art_comments') { $params->set('comments', $v); }
			}
		}
		if (ELXIS_MOBILE == 1) {
			$params->set('art_dateauthor', 1);
			$params->set('art_dateauthor_pos', 1);
			$params->set('art_img', 4);
			$params->set('art_hits', 0);
			$params->set('art_print', 0);
			$params->set('art_email', 0);
			$params->set('ctg_img_empty', 0);
			$params->set('art_comments', 0);
			//$params->set('art_chain', 1);
			//$params->set('art_tags', 1);
		}

		unset($local);
		return $params;
	}


	/***********************************/
	/* CALCULATE ARTICLE META KEYWORDS */
	/***********************************/
	protected function articleKeywords($row, $tree) {
		$metaKeys = array(
			'tags' => array(),
			'extended' => array(),
			'total' => array()
		);

		if (trim($row->metakeys) != '') {
    		$keystring = eUTF::str_replace('\'', '', $row->metakeys);
    		$keystring = eUTF::str_replace('"', '', $keystring);
    		$keystring = eUTF::str_replace('?', '', $keystring);
    		$keystring = eUTF::str_replace(';', '', $keystring);
    		$keystring = eUTF::str_replace('!', '', $keystring);
    		$keystring = eUTF::str_replace(':', '', $keystring);
    		$keystring = eUTF::str_replace('.', '', $keystring);
    		if ($keystring != '') {
				$parts = explode(',', $keystring);
				if ($parts) {
					foreach ($parts as $part) {
						$p = eUTF::trim($part);
						$metaKeys['tags'][] = $p; $metaKeys['total'][] = $p;
					}
					$metaKeys['tags'] = array_unique($metaKeys['tags']);
				}
			}
		}

		$keystring = '';
		$space = '';
		if ($tree) {
			$c = count($tree);
			if ($c > 0) {
				$idx = $c - 1;
				$keystring .= $tree[$idx]->title;
				$space = ' ';
			}
		}

		$keystring .= $space.$row->title;
    	$keystring = eUTF::str_replace('\'', '', $keystring);
    	$keystring = eUTF::str_replace('"', '', $keystring);
    	$keystring = eUTF::str_replace('?', '', $keystring);
    	$keystring = eUTF::str_replace(';', '', $keystring);
    	$keystring = eUTF::str_replace('!', '', $keystring);
    	$keystring = eUTF::str_replace(':', '', $keystring);
    	$keystring = eUTF::str_replace(',', '', $keystring);
    	$keystring = eUTF::str_replace('.', '', $keystring);
		$keystring = eUTF::strtolower($keystring);
		$parts = preg_split('/[\s]/', $keystring, -1, PREG_SPLIT_NO_EMPTY);
		foreach($parts as $part) {
			if (eUTF::strlen($part) > 3) { $metaKeys['extended'][] = $part; $metaKeys['total'][] = $part; }
		}

		$metaKeys['extended'] = array_unique($metaKeys['extended']);
		$metaKeys['total'] = array_unique($metaKeys['total']);
		return $metaKeys;
	}


	/************************/
	/* GET CHAINED ARTICLES */
	/************************/
	protected function loadChainedArticles($row, $params) {
		if (($row->catid == 0) || ((int)$params->get('art_chain', 0) < 1)) { return null; }
		if ($this->apc == 1) {
			$chained = elxisAPC::fetch('chained'.$row->id, 'article'.$this->lng);
			if ($chained !== false) { return $chained; }
		}
		$order = $params->get('ctg_ordering', 'cd');
		$chained = $this->model->fetchChainedArticles($row, $order);
		if (!$chained) { return $chained; }
		if (!$this->translate) {
			if (($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store('chained'.$row->id, 'article'.$this->lng, $chained, $this->apc_ttl);
			}
			return $chained;
		}
		$elids = array();
		if ($chained['previous'] !== null) { $elids[] = $chained['previous']->id; }
		if ($chained['next'] !== null) { $elids[] = $chained['next']->id; }
		if (!$elids) {
			if (($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store('chained'.$row->id, 'article'.$this->lng, $chained, $this->apc_ttl);
			}
			return $chained;
		}

		$translations = $this->model->articlesTitlesTranslate($elids, $this->lng);
		if (!$translations) {
			if (($this->apc == 1) && ($row->alevel == 0)) {
				elxisAPC::store('chained'.$row->id, 'article'.$this->lng, $chained, $this->apc_ttl);
			}
			return $chained;
		}
		if ($chained['previous'] !== null) {
			$id = $chained['previous']->id;
			if (isset($translations[$id])) { $chained['previous']->title = $translations[$id]; }
		}
		if ($chained['next'] !== null) {
			$id = $chained['next']->id;
			if (isset($translations[$id])) { $chained['next']->title = $translations[$id]; }
		}

		if (($this->apc == 1) && ($row->alevel == 0)) {
			elxisAPC::store('chained'.$row->id, 'article'.$this->lng, $chained, $this->apc_ttl);
		}

		return $chained;
	}


	/***********************/
	/* GET TAGGED ARTICLES */
	/***********************/
	protected function loadTagArticles($tag) {
		$rows = $this->model->fetchTagArticles($tag, $this->translate, $this->lng);
		if (!$this->translate) { return $rows; }
		if (!$rows) { return $rows; }

		$elids = array();
		$cids = array();
		foreach ($rows as $row) {
			$elids[] = $row->id;
			if ($row->catid > 0) { $cids[] = $row->catid; }
		}

		$translations = $this->model->articlesTitleSubTranslate($elids, $this->lng);
		if (!$translations) { return $rows; }
		foreach ($translations as $trans) {
			$id = (int)$trans['elid'];
			$element = $trans['element'];
			switch($element) {
				case 'title': $rows[$id]->title = $trans['translation']; break;
				case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
				default: break;
			}
		}
		unset($translations, $elids);

		if ($cids) {
			$cids = array_unique($cids);
			$translations = $this->model->categoriesTranslate($cids, $this->lng);
			if (!$translations) { return $rows; }
			foreach ($rows as $id => $row) {
				if ($row->catid == 0) { continue; }
				if (isset($translations[ $row->catid ])) {
					$rows[$id]->category = $translations[ $row->catid ];
				}
			}
		}

		return $rows;
	}


	/*********************************/
	/* GET CATEGORIES FOR FEEDS PAGE */
	/*********************************/
	protected function loadFeedCategories() {
		$result = $this->model->fetchFeedCategories();
		if (!$result || !is_array($result)) { return null; }
		$rows = $result[0];
		$elids = $result[1];
		unset($result);
		if (!$this->translate) { return $rows; }

		$translations = $this->model->categoriesTranslate($elids, $this->lng);
		if (!$translations) { return $rows; }

		foreach ($rows as $idx => $ctg) {
			$cid = $ctg->catid;
			if (isset($translations[$cid])) {
				$rows[$idx]->title = $translations[$cid];
			}
			
			if (count($ctg->categories) > 0) {
				foreach ($ctg->categories as $idx2 => $ctg2) {
					$cid2 = $ctg2->catid;
					if (isset($translations[$cid2])) {
						$rows[$idx]->categories[$idx2]->title = $translations[$cid2];
					}
				}
			}
		}

		return $rows;
	}


	/**************************************/
	/* GET RSS/ATOM GENERIC FEED ARTICLES */
	/**************************************/
	protected function loadFeedArticles($limit) {
		$rows = $this->model->fetchFeedArticles($limit);
		if (!$this->translate) { return $rows; }
		if (!$rows) { return $rows; }

		$elids = array();
		$catids = array();
		foreach ($rows as $row) {
			$elids[] = $row->id;
			if ($row->catid > 0) { $catids[] = $row->catid; }
		}
		$translations = $this->model->articlesTranslate($elids, $this->lng);
		if ($translations) {
			foreach ($translations as $trans) {
				$id = (int)$trans['elid'];
				$element = $trans['element'];
				switch($element) {
					case 'title': $rows[$id]->title = $trans['translation']; break;
					case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
					case 'introtext': $rows[$id]->introtext = $trans['translation']; break;
					default: break;
				}
			}
		}

		if ($catids) {
			$catids = array_unique($catids);
			$translations = $this->model->categoriesTranslate($catids, $this->lng);
			if ($translations) {
				foreach ($translations as $trans) {
					$catid = (int)$trans['elid'];
					foreach ($rows as $id => $row) {
						if ($row->catid == $catid) {
							$rows[$id]->category =  $trans['translation'];
						}
					}
				}
			}
		}

		return $rows;
	}


	/*******************/
	/* MAKE LEVELS TIP */
	/*******************/
	protected function makeLevelsTip() {
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


	/*******************************************/
	/* SEND NOTIFICATION FOR PUBLISHED COMMENT */
	/*******************************************/
	protected function notifyPublishComment($author_name, $author_email, $article_title, $article_link) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$subject = $eLang->get('YOUR_COMMENT_PUB');
		$body = $eLang->get('HI').' '.$author_name.",\n";
		$body .= sprintf($eLang->get('YOUR_COMMENT_ARTICLE_PUB'), '"'.$article_title.'"')."\n\n";
		$body .= $article_link."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= $eLang->get('NOREPLYMSGINFO');

		$to = $author_email.','.$author_email;
		$ok = $elxis->sendmail($subject, $body, '', null, 'plain', $to);
		return $ok;
	}


	/************************************************/
	/* DETERMINE UPLOAD FOLDER FOR AN ARTICLE IMAGE */
	/************************************************/
	protected function determineUploadFolder() {
		$base = ELXIS_PATH.'/media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $base = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}
		if (is_dir($base.'articles20/')) {
			$start = 20; $last = 30;
		} elseif (is_dir($base.'articles10/')) {
			$start = 10; $last = 20;
		} else {
			$start = 1; $last = 10;
		}
		$updir = '';
		for ($i=$start; $i < $last; $i++) {
			$dir = ($i == 1) ? 'articles' : 'articles'.$i;
			if (is_dir($base.$dir.'/')) {
				$n = count(scandir($base.$dir.'/'));
				if ($n < 300) { $updir = $dir; break; }
			} else {
				$updir = $dir; break;
			}
		}
		if ($updir == '') { $updir = 'articles'.$last; }
		return $updir;
	}


	/**********************************************************/
	/* DELETE ARTICLE'S TRIPPLE IMAGE (NORMAL, MEDIUM, THUMB) */
	/**********************************************************/
	protected function deleteArticleImage($relfile) {
		$eFiles = eFactory::getFiles();

		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) {
				$base = 'media/images/site'.ELXIS_MULTISITE.'/';
				if (strpos($relfile, $base) !== 0) { return false; }
			}
		}

		$ok = $eFiles->deleteFile($relfile);
		if ($ok) {
			$dir = dirname($relfile);
			if ($dir == '.') {
				$dir = '';
			} else {
				$dir .= '/';
			}
			$img_filename = basename($relfile);
			$clean_filename = $eFiles->getFilename($img_filename);
			$ext = $eFiles->getExtension($img_filename);
			$eFiles->deleteFile($dir.$clean_filename.'_medium.'.$ext);
			$eFiles->deleteFile($dir.$clean_filename.'_thumb.'.$ext);
		}
		return $ok;
	}


	/*********************************************/
	/* MAKE ARTICLE'S MEDIUM IMAGE AND THUMBNAIL */
	/*********************************************/
	protected function makeMediumThumb($updir, $filename, $extension) {
		$eFiles = eFactory::getFiles();

		$global_str = (string)$this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');

		if ((int)$params->def('img_medium_width', 240) < 10) { $params->set('img_medium_width', 240); }
		if ((int)$params->def('img_medium_height', 0) < 0) { $params->set('img_medium_height', 0); }
		if ((int)$params->def('img_thumb_width', 100) < 10) { $params->set('img_thumb_width', 100); }
		if ((int)$params->def('img_thumb_height', 0) < 0) { $params->set('img_thumb_height', 0); }
		$mw = (int)$params->get('img_medium_width', 240);
		$mh = (int)$params->get('img_medium_height', 0);
		$tw = (int)$params->get('img_thumb_width', 100);
		$th = (int)$params->get('img_thumb_height', 0);
		unset($params, $global_str);

		$imginfo = getimagesize(ELXIS_PATH.'/'.$updir.$filename.'.'.$extension);
    	if (!$imginfo) { return false; }
    	$mcrop = true;
    	if ($mh == 0) { $mh = intval($mw * ($imginfo[1] / $imginfo[0])); $mcrop = false; }
    	$tcrop = true;
    	if ($th == 0) { $th = intval($tw * ($imginfo[1] / $imginfo[0])); $tcrop = false; }

		if (!$eFiles->copy($updir.$filename.'.'.$extension, $updir.$filename.'_medium.'.$extension)) { return false; }
		if (!$eFiles->copy($updir.$filename.'.'.$extension, $updir.$filename.'_thumb.'.$extension)) { return false; }
		$eFiles->resizeImage($updir.$filename.'_medium.'.$extension, $mw, $mh, $mcrop);
		$eFiles->resizeImage($updir.$filename.'_thumb.'.$extension, $tw, $th, $tcrop);
		return true;
	}

}

?>