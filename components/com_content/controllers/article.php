<?php 
/**
* @version		$Id: article.php 1432 2013-05-04 16:22:57Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class articleContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/**************************************/
	/* PREPARE TO DISPLAY CONTENT ARTICLE */
	/**************************************/ 
	public function viewarticle() {
		$elxis = eFactory::getElxis();
		$eURI = eFactory::getURI();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		$segs = $eURI->getSegments();
		$n = count($segs);
		$last = $n - 1;
		$seotitle = preg_replace('/(\.html)$/i', '', $segs[$last]);
		$row = $this->loadArticle($seotitle);
		if (!$row) {
			exitPage::make('404', 'CCON-0007', $eLang->get('ARTICLE_NOT_FOUND'));
		}

		$category_link = '';
		if ($row->catid > 0) {
			$tree = $this->loadCategoryTree($row->catid);
			if (!$tree) {
				exitPage::make('403', 'CCON-0008', $eLang->get('NOTALLOWACCPAGE'));
			}
			$n = count($tree) - 1;
			$category_title = $tree[$n]->title;
			$category_link = $tree[$n]->link;
			$row->link = $category_link.$row->seotitle.'.html';
		} else {
			$tree = null; //autonomous page
			$row->link = $row->seotitle.'.html';
			$category_title = '';
		}

		unset($n, $last, $segs, $seotitle);

		define('ELXIS_ARTID', $row->id);

		$atree = ($tree === null) ? array() : $tree;
		$art = new stdClass;
		$art->id = $row->id;
		$art->title = $row->title;
		$art->seotitle = $row->seotitle;
		$art->link = $row->link;
		$atree[] = $art;

		$this->makeTreePathway($atree);
		unset($atree, $art);

		$print = (isset($_GET['print'])) ? (int)$_GET['print'] : 0;
		$params = $this->combinedArticleParams($row->params, $row->catid);

		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }
		$page_extra = ($page > 1) ? ' - '.$eLang->get('PAGE').' '.$page : '';

		if ($page == 1) {
			$this->model->updateHits($row->id, $row->hits);
			$row->hits++;
		}

		$row->text = $row->introtext.$row->maintext;
		$row->keywords = $this->articleKeywords($row, $tree);

		$ePlugin = eFactory::getPlugin();
		$ePlugin->process($row);

		if ($print == 1) {
			$chained = null;
			$params->set('art_tags', 0);
		} else {
			$chained = $this->loadChainedArticles($row, $params);
		}
		if ($chained) {
			if ($chained['previous'] !== null) {
				$chained['previous']->link = $category_link.$chained['previous']->seotitle.'.html';
			}
			if ($chained['next'] !== null) {
				$chained['next']->link = $category_link.$chained['next']->seotitle.'.html';
			}
		}

		$comments = null;
		if ((int)$params->get('comments') == 1) {
			if ($params->get('comments_src') == 2) {
				if ($eURI->detectSSL() === true) { //disqus does not support SSL
					$params->set('comments') == 0;
				} else {
					$js = 'var disqus_shortname = \''.$params->get('disqus_shortname').'\';'."\n".'var disqus_identifier = \'article'.$row->id.'\';'."\n".'var disqus_developer='.intval($params->get('disqus_developer')).';';
					$eDoc->addScript($js);
					$js = '<script type="text/javascript" src="http://'.$params->get('disqus_shortname').'.disqus.com/embed.js" async="true"></script>';
					$eDoc->addCustom($js);
				}
			} else {
				$onlypublished = ((int)$elxis->acl()->check('com_content', 'comments', 'publish') == 2) ? false : true;
				$comments = $this->model->fetchComments($row->id, $onlypublished);
				unset($onlypublished);
			}
		}

		if ($category_title != '') {
			$eDoc->setTitle($row->title.' - '.$category_title.$page_extra);
		} else {
			$eDoc->setTitle($row->title.$page_extra.' - '.$elxis->getConfig('SITENAME'));
		}

		if (trim($row->subtitle) != '') {
			$desc = $row->subtitle;
		} else if (trim($row->introtext) != '') {
			$desc = filter_var($row->introtext, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$desc = eUTF::substr($desc, 0, 120).'...';
		} else {
			$desc = filter_var($row->maintext, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$desc = eUTF::substr($desc, 0, 120).'...';
		}

		if ($category_title != '') {
			$eDoc->setDescription($desc.' '.$category_title.$page_extra);
		} else {
			$eDoc->setDescription($desc.$page_extra.' '.$elxis->getConfig('SITENAME'));
		}

		$eDoc->setKeywords($row->keywords['total']);

		if ($category_link != '') {
			$live = trim($params->get('live_bookmarks'));
			if (($live == 'rss') || ($live == 'atom')) {
				$rsslink = $elxis->makeURL($category_link.$live.'.xml');
				$eDoc->addLink($rsslink, 'application/'.$live.'+xml', 'alternate', 'title="'.$category_title.'"');
				unset($rsslink);
			}
			unset($live);
		}
		unset($tree, $category_link, $category_title);

		if (isset($_GET['q'])) {
			$q = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\?]|[\@]|[\!]|[\;]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
			$q = eUTF::trim(preg_replace($pat, '', $q));
			if (eUTF::strlen($q) > 2) {
				$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/content.js');
				$js = 'var arthighlight; document.addEventListener(\'DOMContentLoaded\', function() { arthighlight = new textHighlight(\'elx_article_page_'.$row->id.'\'); arthighlight.apply(\''.$q.'\'); }, false);';
				$eDoc->addScript($js);
			}
		}

		$this->view->showArticle($row, $params, $chained, $comments, $print);
	}
	
}

?>