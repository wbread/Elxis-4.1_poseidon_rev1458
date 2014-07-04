<?php 
/**
* @version		$Id: category.php 1421 2013-04-30 17:31:26Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class categoryContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/***************************************/
	/* PREPARE TO DISPLAY CONTENT CATEGORY */
	/***************************************/
	public function viewcategory() {
		if ($this->format == 'xml') {
			$this->viewXMLcategory();
			return;
		}

		$elxis = eFactory::getElxis();
		$eURI = eFactory::getURI();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		$segs = $eURI->getSegments();
		$n = count($segs);
		$last = $n - 1;
		$seotitle = $segs[$last];
		$row = $this->loadCategory($seotitle);
		if (!$row) {
			exitPage::make('404', 'CCON-0005', $eLang->get('CTG_NFOUND_NACCESS'));
		}

		$tree = $this->loadCategoryTree($row->catid, $row);
		if ($tree === false) {
			exitPage::make('403', 'CCON-0006', $eLang->get('NOTALLOWACCPAGE'));
		}

		define('ELXIS_CATID', $row->catid);
		//the tree is valid, set the category's full link
		$n = count($tree) - 1;
		$row->link = $tree[$n]->link;
		unset($n, $segs, $last, $seotitle);

		$this->makeTreePathway($tree);

		$print = (isset($_GET['print'])) ? (int)$_GET['print'] : 0;
		$params = $this->combinedCategoryParams($row->params);

		$subcategories = null;
		if ((int)$params->get('ctg_subcategories', 1) > 0) {
			if ($print == 0) {
				$subcategories = $this->loadSubcategories($row->catid);
			}
		}

		$total = $this->model->countArticles($row->catid);
		$perpage = (int)$params->def('ctg_featured_num', 0);
		$perpage += (int)$params->def('ctg_short_num', 0);
		$perpage += (int)$params->def('ctg_links_num', 10);
		$showarticles = true;
		if ($perpage < 1) { $showarticles = false; }

		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }
		$maxpage = (($showarticles === false) || ($total == 0)) ? 1 : ceil($total/$perpage);
		if ($page > $maxpage) { $page = $maxpage; }
    	$limit = $perpage;
		$limitstart = (($page -1) * $limit);

		$articles = null;
		if (($showarticles === true) && ($total > 0)) {
			$order = $params->get('ctg_ordering', 'cd');
			$articles = $this->loadArticles($row->catid, $limitstart, $limit, $order);
			if ($articles) {
				if ($page > 1) {
					$run_plugins = (intval($params->def('ctg_nextpages_style', 0)) == 0) ? 1 : 0;
				} else {
					$run_plugins = (int)$params->def('ctg_featured_num', 0);
				}
				if (ELXIS_MOBILE == 1) { $run_plugins = 0; }

				$ePlugin = eFactory::getPlugin();
				foreach ($articles as $k => $article) {
					$article->link = $row->link.$article->seotitle.'.html';
					$articles[$k]->link = $article->link;
					if ($run_plugins > 0) {
						$article->text = $article->introtext;
						$ePlugin->process($article);
						$articles[$k]->introtext = $article->text;
						unset($article->text);
					} else {
						$articles[$k]->introtext = $ePlugin->removePlugins($article->introtext);
						if (ELXIS_MOBILE == 1) { $articles[$k]->introtext = strip_tags($articles[$k]->introtext); }
					}
				}
			}
		}

		$metaKeys = $this->categoryKeywords($row->title, $articles, $tree, $subcategories);

		$title = ($page > 1) ? $row->title.', '.$eLang->get('PAGE').' '.$page : $row->title;
		$eDoc->setTitle($title.' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('ARTICLES_ON_CAT'), $row->title);
		if ($page > 1) { $desc .= ', '.$eLang->get('PAGE').' '.$page; }
		$eDoc->setDescription($desc.'. '.$elxis->getConfig('SITENAME'));
		$eDoc->setKeywords($metaKeys);

		unset($metaKeys, $tree);

		$live = trim($params->get('live_bookmarks'));
		if (($live == 'rss') || ($live == 'atom')) {
			$rsslink = $elxis->makeURL($row->link.$live.'.xml');
			$eDoc->addLink($rsslink, 'application/'.$live.'+xml', 'alternate', 'title="'.$row->title.'"');
			unset($rsslink);
		}
		unset($live);

		$this->view->showCategory($row, $subcategories, $articles, $page, $maxpage, $total, $params, $print);
	}


	/********************************************/
	/* PREPARE TO DISPLAY XML FEED FOR CATEGORY */
	/********************************************/
	private function viewXMLcategory() {
		$elxis = eFactory::getElxis();
		$eURI = eFactory::getURI();
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$segs = $eURI->getSegments();
		$n = count($segs);
		$last = $n - 1;
		$type = ($segs[$last] == 'atom.xml') ? 'atom' : 'rss';
		$last = $n - 2;
		$seotitle = $segs[$last];

		$row = $this->loadCategory($seotitle, true);
		if (!$row) {
			$this->view->feedError($type, $eLang->get('CTG_NFOUND_NACCESS'));
			return;
		}

		$tree = $this->loadCategoryTree($row->catid, $row, true);
		if ($tree === false) {
			$this->view->feedError($type, $eLang->get('NOTALLOWACCPAGE'));
			return;
		}

		//the tree is valid, set the category's full link
		$n = count($tree) - 1;
		$row->link = $tree[$n]->link;
		unset($n, $segs, $last, $seotitle);

		$params = $this->combinedCategoryParams($row->params);

		$cachefile = md5($eURI->getElxisUri(true, false).'-'.$type).'.xml';

		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$feed_cache = (int)$params->get('feed_cache', 6) * 3600;
		if ($feed_cache > 0) {
			if (file_exists($repo_path.'/cache/feeds/'.$cachefile)) {
				$ts = filemtime($repo_path.'/cache/feeds/'.$cachefile);
				if (($ts + $feed_cache) > time()) {
					if (rand(0, 100) < 5) { //5% probability for cache cleanup
						$this->cleanFeedsCache($feed_cache, $repo_path);
					}
					if (@ob_get_length() > 0) { @ob_end_clean(); }
					@header("Content-type:text/xml; charset=utf-8");
					echo file_get_contents($repo_path.'/cache/feeds/'.$cachefile);
					exit();
				}
			}
		}

		$feeditems = (int)$params->get('feed_items', 10);
		if ($feeditems < 1) { $feeditems = 10; }
		$articles = $this->loadArticles($row->catid, 0, $feeditems, 'cd', true);

		elxisLoader::loadFile('includes/libraries/elxis/feed.class.php');
		$feed = new elxisFeed($type);
		if ($feed_cache > 0) {
			if (!file_exists($repo_path.'/cache/feeds/')) {
				$eFiles->createFolder('cache/feeds/', 0755, true);
			}
			$ttl = intval($feed_cache / 60);
			$feed->setTTL($ttl);
		}

		$channel_desc = $row->description;
		if (trim($row->description) == '') {
			$channel_desc = sprintf($eLang->get('ARTICLES_ON_CAT'), $row->title);
		}
		$feed->addChannel($row->title, $elxis->makeURL($row->link), $channel_desc);
		if ($articles) {
			$ePlugin = eFactory::getPlugin();
			foreach ($articles as $article) {
				$enclosure = null;
				$itemdesc = '';
				if (trim($article->subtitle) != '') { $itemdesc = '<p>'.$article->subtitle.'</p>'; }
				$itemdesc .= $ePlugin->removePlugins($article->introtext);
				if (trim($article->image != '')) {
					$enclosure = $article->image;
					$file_info = $eFiles->getNameExtension($article->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$enclosure = $file_info['name'].'_thumb.'.$file_info['extension'];
						$itemdesc = '<img style="margin:5px; float:left;" src="'.$elxis->getConfig('URL').'/'.$enclosure.'" alt="'.$row->title.'" /> '.$itemdesc;
					} else if (!file_exists(ELXIS_PATH.'/'.$article->image)) {
						$enclosure = null;
					}
				}

				$feed->addItem(
					$article->title,
					$itemdesc,
					$elxis->makeURL($row->link.$article->seotitle.'.html'),
					strtotime($article->created),
					$article->created_by_name,
					$enclosure
				);
			}
		}

		$action = ($feed_cache > 0) ? 'saveshow' : 'show';
		$feed->makeFeed($action, 'cache/feeds/'.$cachefile);
	}


	/**********************************/
	/* PERFORM A FEEDS CACHE CLEAN UP */
	/**********************************/
	private function cleanFeedsCache($feed_cache, $repo_path) {
		$eFiles = eFactory::getFiles();
		$files = $eFiles->listFiles('cache/feeds/', '(.xml)$', false, false, true);
		if ($files) {
			foreach ($files as $file) {
				$ts = filemtime($repo_path.'/cache/feeds/'.$file);
				if (($ts + $feed_cache) < time()) {
					$eFiles->deleteFile('cache/feeds/'.$file, true);
				}
			}
		}
	}

}

?>