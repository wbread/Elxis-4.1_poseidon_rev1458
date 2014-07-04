<?php 
/**
* @version		$Id: mod_mobilefront.php 1428 2013-05-03 18:55:07Z datahell $
* @package		Elxis
* @subpackage	Module Mobile Frontpage
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modMobFront', false)) {
	class modMobFront {

		private $style = 2;
		private $subcategories = 1;
		private $pcatid = 0;
		private $subtitle = 1;
		private $introtext = 1;
		private $introlimit = 200;
		private $image = 1;
		private $date = 1;
		private $artspercat = 5;
		private $maxarticles = 10;
		private $translate = false;
		private $lang = '';
		private $apc = false;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$elxis = eFactory::getElxis();

			if ($elxis->getConfig('MULTILINGUISM') == 1) {
				$this->lang = eFactory::getURI()->getUriLang();
				if ($this->lang != '') { $this->translate = true; }
			}
			if ($elxis->getConfig('APC') == 1) { $this->apc = true; }
			$this->getConfig($params);
		}


		/********************************/
		/* GET CONFIGURATION PARAMETERS */
		/********************************/		
		private function getConfig($params) {
			$this->style = (int)$params->get('style', 2);
			$this->subcategories = (int)$params->get('subcategories', 1);
			$this->pcatid = (int)$params->get('pcatid', 0);
			$this->subtitle = (int)$params->get('subtitle', 1);
			$this->introtext = (int)$params->get('introtext', 1);
			$this->introlimit = (int)$params->get('introlimit', 200);
			if ($this->introlimit < 20) { $this->introlimit = 200; }
			$this->image = (int)$params->get('image', 1);
			$this->date = (int)$params->get('date', 1);
			$this->artspercat = (int)$params->get('artspercat', 5);
			if ($this->artspercat < 1) { $this->artspercat = 5; }
			$this->maxarticles = (int)$params->get('maxarticles', 10);
			if ($this->maxarticles < 1) { $this->maxarticles = 10; }
		}


		/***************************************/
		/* RUN TO THE HILLS, RUN FOR YOUR LIFE */
		/***************************************/
		public function run() {
			if ($this->style === 0) {
				$rows = $this->getCategories();
				if (!$rows) { return; }
				$this->addJSCSS();
				$this->showCategories($rows);
			} else if ($this->style === 1) {
				$rows = $this->getArticles();
				if (!$rows) { return; }
				$this->addJSCSS();
				$this->showArticles($rows);
			} else {
				$this->style = 2;
				$rows = $this->getBoth();
				if (!$rows) { return; }
				$this->addJSCSS();
				$this->showBoth($rows);
			}
		}


		/************************************/
		/* GET CATEGORIES FROM THE DATABASE */
		/************************************/
		private function getCategories() {
			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();

			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();

			$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('title').", ".$db->quoteId('seolink')." FROM ".$db->quoteId('#__categories');
			if ($this->pcatid > 0) {
				$sql .= "\n WHERE ".$db->quoteId('catid')." = :pctg";
			} else {
				$sql .= "\n WHERE ".$db->quoteId('parent_id')." = :pctg";
			}
			$sql .= "\n AND ".$db->quoteId('published')." = 1 AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
			."\n ORDER BY ".$db->quoteId('ordering')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':pctg', $this->pcatid, PDO::PARAM_INT);
			$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
			$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
			$stmt->execute();
			$rootcats = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rootcats) { return array(); }

			$catids = array();
			$categories = array();
			foreach ($rootcats as $ctg) {
				$catid = $ctg['catid'];
				$catids[] = $catid;

				$row = new stdClass;
				$row->catid = $ctg['catid'];
				$row->category = $ctg['title'];
				$row->seolink = $ctg['seolink'];
				$row->subcategories = array();
				$categories[$catid] = $row;
				unset($row);
			}
			unset($rootcats);

			$subcats = array();
			if ($this->subcategories) {
				$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('parent_id').", ".$db->quoteId('title').", ".$db->quoteId('seolink')." FROM ".$db->quoteId('#__categories')
				."\n WHERE ".$db->quoteId('parent_id')." IN (".implode(', ', $catids).") AND ".$db->quoteId('published')." = 1 AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
				."\n ORDER BY ".$db->quoteId('ordering')." ASC";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
				$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
				$stmt->execute();
				$subcats = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($subcats) {
					foreach ($subcats as $ctg) { $catids[] = $ctg['catid']; }
				}
			}

			$translations = array();
			if ($this->translate) {
				$translations = $this->getCategoryTrans($catids);
				if ($translations) {
					foreach ($categories as $catid => $ctg) {
						if (isset($translations[$catid])) { $categories[$catid]->category = $translations[$catid]; }
					}
				} else {
					$translations = array();
				}
			}

			if ($subcats) {
				foreach ($subcats as $ctg) {
					$catid = $ctg['catid'];
					$pid = $ctg['parent_id'];
					if (!isset($categories[$pid])) { continue; }

					$row = new stdClass;
					$row->catid = $ctg['catid'];
					$row->category = (isset($translations[$catid])) ? $translations[$catid] : $ctg['title'];
					$row->seolink = $ctg['seolink'];
					$categories[$pid]->subcategories[$catid] = $row;
					unset($row);
				}
			}

			$this->catids = $catids;
			return $categories;
		}


		/***********************************/
		/* GET TRANSLATIONS FOR CATEGORIES */
		/***********************************/
		private function getCategoryTrans($catids) {
			$db = eFactory::getDB();

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ".$db->quoteId('element')." = ".$db->quote('category_title')." AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lang, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchPairs();
			return $translations;
		}


		/**********************************/
		/* GET ARTICLES FROM THE DATABASE */
		/**********************************/
		private function getArticles() {
			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();

			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();
			$binds = array();
			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.introtext, a.image, a.created, c.title AS category, c.seolink"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid"
			."\n WHERE a.published = 1";
			if ($this->pcatid > 0) {
				if ($this->subcategories == 1) {
					$binds[] = array(':xctg', $this->pcatid, PDO::PARAM_INT);
					$sql .= "\n AND ((c.catid = :xctg) OR (c.parent_id = :xctg)) AND c.published = 1";
				} else {
					$binds[] = array(':xctg', $this->pcatid, PDO::PARAM_INT);
					$sql .= "\n AND a.catid = :xctg AND c.published = 1";
				}
			} else {
				$sql .= "\n AND ((c.published = 1) OR (a.catid=0))";
			}
			$sql .= "\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))";
			$sql .= "\n ORDER BY a.created DESC";
			$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
			$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);
			$stmt = $db->prepareLimit($sql, 0, $this->maxarticles);
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
			$stmt->execute();
			$rows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);

			if ($rows && ($this->translate === true)) {
				$rows = $this->translateArticles($rows);
			}

            return $rows;            
        }


		/**********************/
		/* TRANSLATE ARTICLES */
		/**********************/
		private function translateArticles($rows) {
			$db = eFactory::getDB();

			$ids = array();
			$catids = array();
			foreach ($rows as $row) {
				$ids[] = $row->id;
				if ($row->catid > 0) { $catids[] = $row->catid; }
			}

			if ($catids) { $catids = array_unique($catids); }

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('element').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle').")"
			."\n OR (".$db->quoteId('element')." = ".$db->quote('introtext')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lang, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					$element = $trans['element'];
					if (!isset($rows[$id])) { continue; }
					switch($element) {
						case 'title': $rows[$id]->title = $trans['translation']; break;
						case 'subtitle': $rows[$id]->subtitle = $trans['translation']; break;
						case 'introtext': $rows[$id]->introtext = $trans['translation']; break;
						default: break;
					}
				}
			}

			if ($catids) {
				$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
				."\n WHERE ".$db->quoteId('category')." = ".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
				."\n AND ".$db->quoteId('element')." = ".$db->quote('category_title')." AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':lng', $this->lang, PDO::PARAM_STR);
				$stmt->execute();
				$translations = $stmt->fetchPairs();
				if ($translations) {
					foreach ($rows as $id => $row) {
						if (($row->catid > 0) && isset($translations[ $row->catid ])) {
							$rows[$id]->category = $translations[ $row->catid ];
						}
					}
				}
			}

			return $rows;
		}


		/*************************************************/
		/* GET CATEGORIES AND ARTICLES FROM THE DATABASE */
		/*************************************************/
		private function getBoth() {
			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();

			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();

			$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('title').", ".$db->quoteId('seolink')." FROM ".$db->quoteId('#__categories');
			if ($this->pcatid > 0) {
				$sql .= "\n WHERE ".$db->quoteId('catid')." = :pctg";
			} else {
				$sql .= "\n WHERE ".$db->quoteId('parent_id')." = :pctg";
			}
			$sql .= "\n AND ".$db->quoteId('published')." = 1 AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))"
			."\n ORDER BY ".$db->quoteId('ordering')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':pctg', $this->pcatid, PDO::PARAM_INT);
			$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
			$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
			$stmt->execute();
			$rootcats = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rootcats) { return array(); }

			$catids = array();
			$rows = array();
			foreach ($rootcats as $ctg) {
				$catid = $ctg['catid'];
				$catids[] = $catid;

				$row = new stdClass;
				$row->catid = $ctg['catid'];
				$row->category = $ctg['title'];
				$row->seolink = $ctg['seolink'];
				$row->articles = array();
				$rows[$catid] = $row;
				unset($row);
			}
			unset($rootcats);

			$aids = array();

			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.introtext, a.image, a.created, c.title AS category, c.seolink"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid"
			."\n WHERE a.published = 1";
			if ($this->subcategories == 1) {
				$sql .= "\n AND ((c.catid = :xctg) OR (c.parent_id = :xctg)) AND c.published = 1";
			} else {
				$sql .= "\n AND c.catid = :xctg AND c.published = 1";
			}
			$sql .= "\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))";
			$sql .= "\n ORDER BY a.created DESC";
			$stmt = $db->prepareLimit($sql, 0, $this->artspercat);
			foreach ($rows as $catid => $row) {
				$stmt->bindParam(':xctg', $catid, PDO::PARAM_INT);
				$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
				$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
				$stmt->execute();
				$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
				if ($articles) {
					foreach ($articles as $article) { $aids[] = $article['id']; $catids[] = $article['catid']; }
					$rows[$catid]->articles = $articles;
				}
				unset($articles);
			}

			if (!$this->translate) { return $rows; }

			$catids = array_unique($catids);
			$ctg_trans = $this->getCategoryTrans($catids);
			if ($ctg_trans) {
				foreach ($rows as $catid => $row) {
					if (isset($ctg_trans[$catid])) { $rows[$catid]->category = $ctg_trans[$catid]; }
					if ($row->articles) {
						foreach ($row->articles as $k => $article) { //required as it might belong to a child category and not to catid
							$c = $article['catid'];
							if (isset($ctg_trans[$c])) { $rows[$catid]->articles[$k]['category'] = $ctg_trans[$c]; }
						}
					}
				}
			}
			unset($ctg_trans, $catids);

			if ($aids) {
				$art_trans = $this->getArtTrans($aids);
				if ($art_trans) {
					foreach ($rows as $catid => $row) {
						if ($row->articles) {
							foreach ($row->articles as $k => $article) {
								$id = $article['id'];
								if (!isset($art_trans[$id])) { continue; }
								if (isset($art_trans[$id]['title'])) { $rows[$catid]->articles[$k]['title'] = $art_trans[$id]['title']; }
								if (isset($art_trans[$id]['subtitle'])) { $rows[$catid]->articles[$k]['subtitle'] = $art_trans[$id]['subtitle']; }
								if (isset($art_trans[$id]['introtext'])) { $rows[$catid]->articles[$k]['introtext'] = $art_trans[$id]['introtext']; }
							}
						}
					}
				}
			}

			return $rows;
		}


		/*****************************/
		/* GET ARTICLES TRANSLATIONS */
		/*****************************/
		private function getArtTrans($aids) {
			$db = eFactory::getDB();

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('element').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle').")"
			."\n OR (".$db->quoteId('element')." = ".$db->quote('introtext')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $aids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lang, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$translations) { return array(); }

			$art_trans = array();
			if ($translations) {
				foreach ($translations as $trans) {
					$id = (int)$trans['elid'];
					if (!isset($art_trans[$id])) { $art_trans[$id] = array(); }
					switch ($trans['element']) {
						case 'title': $art_trans[$id]['title'] = $trans['translation']; break;
						case 'subtitle': $art_trans[$id]['subtitle'] = $trans['translation']; break;
						case 'introtext': $art_trans[$id]['introtext'] = $trans['translation']; break;
						default: break;
					}
				}
			}

			return $art_trans;
		}


		/***************************/
		/* ADD REQUIRED JS AND CSS */
		/***************************/
		private function addJSCSS() {
			if (defined('MODMOBFRONT_LOADED')) { return; }
			$eDoc = eFactory::getDocument();
			$baseurl = eFactory::getElxis()->secureBase().'/modules/mod_mobilefront';
			$eDoc->addStyleLink($baseurl.'/css/mobfront'.eFactory::getLang()->getinfo('RTLSFX').'.css');
			$eDoc->addJQuery();
			define('MODMOBFRONT_LOADED', 1);
		}


		/***************************/
		/* DISPLAY CATEGORIES LIST */
		/***************************/
		private function showCategories($rows) {
			$elxis = eFactory::getElxis();

			echo '<nav class="mobfrontwrap">'."\n";
			echo '<ul>'."\n";
			foreach ($rows as $row) {
				$link = $elxis->makeURL('content:'.$row->seolink);
				if ($row->subcategories) {
					echo '<li><h3><a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a></h3><span class="mobfrtoggle" onclick="$(\'#mobfrsub'.$row->catid.'\').slideToggle();">&#x00B1;</span></li>'."\n";
					echo '<ul class="mobfrsub" id="mobfrsub'.$row->catid.'">';
					foreach ($row->subcategories as $sub) {
						$sublink = $elxis->makeURL('content:'.$sub->seolink);
						echo '<li><h4><a href="'.$sublink.'" title="'.$sub->category.'">'.$sub->category.'</a></h4></li>'."\n";
					}
					echo "</ul>\n";
				} else {
					echo '<li><h3><a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a></h3></li>'."\n";
				}
			}
			echo "</ul>\n";
			echo "</nav>\n";
		}


		/*************************/
		/* DISPLAY ARTICLES LIST */
		/*************************/
		private function showArticles($rows) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eFiles = eFactory::getFiles();

			echo '<div class="mobfrontwrap">'."\n";
			foreach ($rows as $row) {
				$img = '';
				if (($this->image == 1) && (trim($row->image) != '') && file_exists(ELXIS_PATH.'/'.$row->image)) {
					$file_info = $eFiles->getNameExtension($row->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
						$img = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
					} else {
						$img = $elxis->secureBase().'/'.$row->image; 
					}
				}
				$link = $elxis->makeURL('content:'.$row->seolink.$row->seotitle.'.html');

				echo "<article>\n";
				if ($img != '') {
					echo '<figure><a href="'.$link.'" title="'.$row->title.'"><img src="'.$img.'" alt="image" /></a></figure>';
					echo '<div class="mobfrart">'."\n";
				} else {
					echo '<div class="mobfrartf">'."\n";
				}

				echo '<div class="mobfrartin">'."\n";
				echo '<h3><a href="'.$link.'" title="'.$row->title.'">'.$row->title."</a></h3>\n";
				if (($this->subtitle == 1) && (trim($row->subtitle) != '')) {
					echo '<p class="mobfrp mobfrstrong">'.$row->subtitle."</p>\n";
				}
				$introtxt = '';
				if (($this->introtext > 0) && (trim($row->introtext) != '')) {
					if ($this->introtext == 1) {
						$introtxt = $this->cleanHTML($row->introtext);
					} else if (($this->introtext == 2) && (trim($row->subtitle) == '')) {
						$introtxt = $this->cleanHTML($row->introtext);
					}
				}
				if ($introtxt != '') {
					if (eUTF::strlen($introtxt) > $this->introlimit) { $introtxt = eUTF::substr($introtxt, 0, $this->introlimit).'...'; }
					echo '<p class="mobfrintro">'.$introtxt."</p>\n";
				}
				$dtcat_txt = '';
				if ($this->date == 1) {
					$dtcat_txt .= '<time>'.$this->friendlyDate($row->created).'</time> ';
				}
				if ($row->catid > 0) {
					$catlink = $elxis->makeURL('content:'.$row->seolink);
					$dtcat_txt .= $eLang->get('IN').' <a href="'.$catlink.'" title="'.$row->category.'">'.$row->category.'</a>';
				}
				if ($dtcat_txt != '') {
					echo '<p class="mobfrdtcat">'.$dtcat_txt."</p>\n";
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</article>\n";
			}
			echo "</div>\n";
		}


		/****************************************/
		/* DISPLAY CATEGORIES AND ARTICLES LIST */
		/****************************************/
		private function showBoth($rows) {
			$elxis = eFactory::getElxis();
			$eFiles = eFactory::getFiles();
			$eLang = eFactory::getLang();

			echo '<div class="mobfrontwrap">'."\n";
			foreach ($rows as $row) {
				$link = $elxis->makeURL('content:'.$row->seolink);
				if ($row->articles) {
					echo '<div class="mobfrhead"><h3><a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a></h3><span class="mobfrtoggle" onclick="$(\'#mobfrarts'.$row->catid.'\').slideToggle();">&#x00B1;</span></div>'."\n";
					echo '<div id="mobfrarts'.$row->catid.'">'."\n";
					foreach ($row->articles as $article) {
						$img = '';
						if (($this->image == 1) && (trim($article['image']) != '') && file_exists(ELXIS_PATH.'/'.$article['image'])) {
							$file_info = $eFiles->getNameExtension($article['image']);
							if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
								$img = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
							} else {
								$img = $elxis->secureBase().'/'.$article['image'];
							}
						}
						$link = $elxis->makeURL('content:'.$article['seolink'].$article['seotitle'].'.html');
						
						echo "<article>\n";
						if ($img != '') {
							echo '<figure><a href="'.$link.'" title="'.$article['title'].'"><img src="'.$img.'" alt="image" /></a></figure>';
							echo '<div class="mobfrart">'."\n";
						} else {
							echo '<div class="mobfrartf">'."\n";
						}

						echo '<div class="mobfrartin">'."\n";
						echo '<h3><a href="'.$link.'" title="'.$article['title'].'">'.$article['title']."</a></h3>\n";
						if (($this->subtitle == 1) && (trim($article['subtitle']) != '')) {
							echo '<p class="mobfrp mobfrstrong">'.$article['subtitle']."</p>\n";
						}
						$introtxt = '';
						if (($this->introtext > 0) && (trim($article['introtext']) != '')) {
							if ($this->introtext == 1) {
								$introtxt = $this->cleanHTML($article['introtext']);
							} else if (($this->introtext == 2) && (trim($article['subtitle']) == '')) {
								$introtxt = $this->cleanHTML($article['introtext']);
							}
						}
						if ($introtxt != '') {
							if (eUTF::strlen($introtxt) > $this->introlimit) { $introtxt = eUTF::substr($introtxt, 0, $this->introlimit).'...'; }
							echo '<p class="mobfrintro">'.$introtxt."</p>\n";
						}
						$dtcat_txt = '';
						if ($this->date == 1) {
							$dtcat_txt .= '<time>'.$this->friendlyDate($article['created']).'</time> ';
						}
						if ($article['catid'] > 0) {
							$catlink = $elxis->makeURL('content:'.$article['seolink']);
							$dtcat_txt .= $eLang->get('IN').' <a href="'.$catlink.'" title="'.$article['category'].'">'.$article['category'].'</a>';
						}
						if ($dtcat_txt != '') {
							echo '<p class="mobfrdtcat">'.$dtcat_txt."</p>\n";
						}
						echo "</div>\n";
						echo "</div>\n";
						echo '<div class="clear"></div>'."\n";
						echo "</article>\n";
					}
					echo "</div>\n";
				} else {
					echo '<div class="mobfrhead"><h3><a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a></h3></div>'."\n";
				}
			}
			echo "</div>\n";
		}


		/*************************************/
		/* REMOVE PLUGINS AND HTML FROM TEXT */
		/*************************************/
		private function cleanHTML($text) {
			$cregex = '#<code>(.*?)</code>#';
			$regex = '#{[^}]*}(?:.+?{\/[^}]*})?#';
			$eregex = '~href="#elink:(.*?)"~';
			$newtext = preg_replace($cregex, '', $text);
			$newtext = preg_replace($regex, '', $newtext);
			$newtext = preg_replace($eregex, 'href="javascript:void(null);"', $newtext);
			$newtext = eUTF::trim(strip_tags($newtext));
			return $newtext;
		}


		/**********************/
		/* USER FRIENDLY DATE */
		/**********************/
		private function friendlyDate($date) {
			$eLang = eFactory::getLang();

			$today = gmdate('Y-m-d');
			$yts = time() - 86400;
			$yesterday = gmdate('Y-m-d', $yts);
			if (strpos($date, $today) === 0) {
				$dt = eFactory::getDate()->formatDate($date, '%H:%M');
				return $eLang->get('TODAY').' '.$dt;
			} else if (strpos($date, $yesterday) === 0) {
				$dt = eFactory::getDate()->formatDate($date, '%H:%M');
				return $eLang->get('YESTERDAY').' '.$dt;
			} else {
				return eFactory::getDate()->formatDate($date, $eLang->get('DATE_FORMAT_3'));
			}
		}

	}
}

if (ELXIS_MOBILE == 1) {
	$mobFront = new modMobFront($params);
	$mobFront->run();
	unset($mobFront);
}

?>