<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module Articles
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modArticles', false)) {
	class modArticles {

		private $source = 0;
		private $catid = 0;
		private $subcats = 0;
		private $catids = array();
		private $order = 0;
		private $days = 10;
		private $limit = 5;
		private $columns = 1;
		private $featured = 1;
		private $featured_sub = 1;
		private $featured_cat = 0;
		private $featured_date = 0;
		private $featured_text = 0;
		private $featured_more = 0;
		private $featured_img = 1;
		private $featured_imgw = 0;
		private $featured_caption = 0;
		private $links_sub = 0;
		private $links_cat = 0;
		private $links_date = 0;
		private $links_img = 0;
		private $links_imgw = 0;
		private $errormsg = '';
		private $lng = 'en';
		private $translate = false;
		private $img_thumbw = 100;
		private $img_mediumw = 240;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->lng = eFactory::getURI()->getUriLang();
			if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
				if ($this->lng != '') { $this->translate = true; }
			}
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
            $this->source = (int)$params->get('source', 0);
            if ($this->source == 1) {
            	$this->catid = (int)$params->get('catid', 0);
            	$this->subcats = (int)$params->get('subcats', 0);
            	if ($this->catid < 1) { $this->errormsg = 'No category selected for the articles!'; }
           	} else if ($this->source == 2) {
				$catstr = trim($params->get('catids', ''));
				$catids = explode(',', $catstr);
				if ($catids) {
					foreach ($catids as $catid) {
						$catid = (int)$catid;
						if ($catid > 0) { $this->catids[] = $catid; }
					}
				}
				if (count($this->catids) == 0) { $this->errormsg = 'No categories selected for the articles!'; }
      		}

            $this->order = (int)$params->get('order', 0);
            if ($this->order == 2) {
            	$this->days = (int)$params->get('days', 10);
            	if ($this->days < 1) { $this->days = 10; }
           	}

            $this->limit = (int)$params->get('limit', 5);
            if ($this->limit < 1) { $this->limit = 5; }
            $this->columns = (int)$params->get('columns', 1);
            if ($this->columns != 2) { $this->columns = 1; }
            if (ELXIS_MOBILE == 1) { $this->columns = 1; }
            $this->featured = (int)$params->get('featured', 1);
            if ($this->featured > $this->limit) { $this->featured = $this->limit; }
            $this->featured_sub = (int)$params->get('featured_sub', 1);
            $this->featured_cat = (int)$params->get('featured_cat', 0);
            $this->featured_date = (int)$params->get('featured_date', 0);
            $this->featured_text = (int)$params->get('featured_text', 0);
            $this->featured_more = (int)$params->get('featured_more', 0);
            $this->featured_caption = (int)$params->get('featured_caption', 0);
            $this->featured_img = (int)$params->get('featured_img', 1);
            if (($this->featured_img == 6) || ($this->featured_img == 7)) {
            	$this->featured_imgw = (int)$params->get('featured_imgw', 0);
           	}

            $this->links_sub = (int)$params->get('links_sub', 0);
            $this->links_cat = (int)$params->get('links_cat', 0);
            $this->links_date = (int)$params->get('links_date', 0);

            $this->links_img = (int)$params->get('links_img', 0);
            if (($this->links_img == 3) || ($this->links_img == 4)) {
            	$this->links_imgw = (int)$params->get('links_imgw', 0);
           	}

           	if ($this->source == 3) {
           		$this->featured_cat = 0;
           		$this->links_cat = 0;
			}
        }


		/*************************/
		/* DISPLAY ERROR MESSAGE */
		/*************************/
		private function showError($msg) {
			echo '<div class="elx_error">'.$msg."</div>\n";
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/         
        public function run() {
        	if ($this->errormsg != '') {
        		$this->showError($this->errormsg);
        		return;
       		}

			$rows = $this->getArticles();
			if (!$rows) { return; }

			if (($this->featured_img > 0) || ($this->links_img > 0)) {
				$this->getImageWidths();
			}

			$total = count($rows);
			$featured = $this->featured;
			if ($featured > $total) { $featured = $total; }
			$numlinks = $total - $featured;

			$this->openWrapper();
			$this->openColumn(true);
			$skip = $featured;
			if ($featured > 0) {
				$this->showFeatured($rows, $featured);
			} else if ($numlinks > 0) {
				$skip = $numlinks;
				if ($this->columns == 2) {
					$skip = ceil($numlinks / 2);
					$numlinks = $numlinks - $skip;
				}
				$this->showLinks($rows, 0, $skip);
			}
			$this->closeColumn();
			$this->openColumn(false);
			if ($numlinks > 0) {
				$this->showLinks($rows, $skip, $numlinks);
			}
			$this->closeColumn();
			$this->closeWrapper();
		}


		/**************************/
		/* RENDER LINKED ARTICLES */
		/**************************/
		private function showLinks($articles, $skip, $numlinks) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();
			$eFiles = eFactory::getFiles();

			if ($eLang->getinfo('DIR') == 'rtl') {
				$lalign = 'right';
				$ralign = 'left';
				$lmargin = '0 0 5px 5px';
				$rmargin = '0 0 5px 5px';
			} else {
				$lalign = 'left';
				$ralign = 'right';
				$lmargin = '0 5px 5px 0';
				$rmargin = '0 5px 5px 0';
			}

			$img_style = '';
			$img_box_style = '';
			switch ($this->links_img) {
				case 1://thumb left
					$w = $this->img_thumbw + 10;
					$img_style = ' width="'.$this->img_thumbw.'" style="width:'.$this->img_thumbw.'px;"';
					$img_box_style = ' style="margin:'.$lmargin.'; float:'.$lalign.'; width:'.$w.'px;"';
				break;
				case 2://thumb right
					$w = $this->img_thumbw + 10;
					$img_style = ' width="'.$this->img_thumbw.'" style="width:'.$this->img_thumbw.'px;"';
					$img_box_style = ' style="margin:'.$rmargin.'; float:'.$ralign.'; width:'.$w.'px;"';
				break;
				case 3://custom left
					if ($this->links_imgw > 0) {
						$width = $this->links_imgw;
					} else {
						$width = $this->img_thumbw;
					}
					$w = $width + 10;
					$img_style = ' width="'.$width.'" style="width:'.$width.'px;"';
					$img_box_style = ' style="margin:'.$lmargin.'; float:'.$lalign.'; width:'.$w.'px;"';
				break;
				case 4://custom right
					if ($this->links_imgw > 0) {
						$width = $this->links_imgw;
					} else {
						$width = $this->img_thumbw;
					}
					$w = $width + 10;
					$img_style = ' width="'.$width.'" style="width:'.$width.'px;"';
					$img_box_style = ' style="margin:'.$rmargin.'; float:'.$ralign.'; width:'.$w.'px;"';
				break;
				case 0: default: break;
			}

			$i = 0;
			$b = 0;
			foreach ($articles as $id => $article) {
				if ($i < $skip) { $i++; continue; }
				if ($b >= $numlinks) { break; }
				if ($this->source != 3) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$imgbox = '';
				if ($this->links_img > 0) {
					if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						$imgbox .= "</div>\n";
					} else {
						$imgfile = $elxis->secureBase().'/'.$article->image;
						$file_info = $eFiles->getNameExtension($article->image);
						if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
							$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
						} else if (!file_exists(ELXIS_PATH.'/'.$article->image)) {
							$imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; 
						}
						unset($file_info);
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$imgfile.'" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						$imgbox .= "</div>\n";	
					}
				}

				echo '<div class="elx_short_box">'."\n";
				echo $imgbox;
				echo '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
				if ($this->links_date == 1) {
					$txt = $this->friendlyDate($article->created);
					if (($this->links_cat == 1) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->links_cat == 1) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				}

				if ($this->links_sub == 1) {
					if (trim($article->subtitle) != '') { echo '<p class="elx_content_short">'.$article->subtitle."</p>\n"; } //elx_content_subtitle
				}
				echo '<div style="clear:both;"></div>'."\n";
				echo "</div>\n";
				$i++;
				$b++;
			}
		}


		/****************************/
		/* RENDER FEATURED ARTICLES */
		/****************************/
		private function showFeatured($articles, $num) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();
			$eFiles = eFactory::getFiles();
			$eDate = eFactory::getDate();

			if ($eLang->getinfo('DIR') == 'rtl') {
				$lalign = 'right';
				$ralign = 'left';
				$lmargin = '0 0 5px 5px';
				$rmargin = '0 0 5px 5px';
			} else {
				$lalign = 'left';
				$ralign = 'right';
				$lmargin = '0 5px 5px 0';
				$rmargin = '0 5px 5px 0';
			}

			$img_style = '';
			$img_box_style = '';
			$use_medium_img = true;
			switch ($this->featured_img) {
				case 1://thumb left
					$w = $this->img_thumbw + 10;
					$img_style = ' width="'.$this->img_thumbw.'" style="width:'.$this->img_thumbw.'px;"';
					$img_box_style = ' style="margin:'.$lmargin.'; float:'.$lalign.'; width:'.$w.'px;"';
					$use_medium_img = false;
				break;
				case 2://thumb right
					$w = $this->img_thumbw + 10;
					$img_style = ' width="'.$this->img_thumbw.'" style="width:'.$this->img_thumbw.'px;"';
					$img_box_style = ' style="margin:'.$rmargin.'; float:'.$ralign.'; width:'.$w.'px;"';
					$use_medium_img = false;
				break;
				case 3://medium top
					$img_box_style = ' style="text-align:'.$lalign.';"';
				break;
				case 4://medium left
					$w = $this->img_mediumw + 10;
					$img_style = ' width="'.$this->img_mediumw.'" style="width:'.$this->img_mediumw.'px;"';
					$img_box_style = ' style="margin:'.$lmargin.'; float:'.$lalign.'; width:'.$w.'px;"';
				break;
				case 5://medium right
					$w = $this->img_mediumw + 10;
					$img_style = ' width="'.$this->img_mediumw.'" style="width:'.$this->img_mediumw.'px;"';
					$img_box_style = ' style="margin:'.$rmargin.'; float:'.$ralign.'; width:'.$w.'px;"';
				break;
				case 6://custom left
					if ($this->featured_imgw > 0) {
						$width = $this->featured_imgw;
					} else {
						$width = $this->img_mediumw;
					}
					if ($width < 161) { $use_medium_img = false; }
					$w = $width + 10;
					$img_style = ' width="'.$width.'" style="width:'.$width.'px;"';
					$img_box_style = ' style="margin:'.$lmargin.'; float:'.$lalign.'; width:'.$w.'px;"';
				break;
				case 7://custom right
					if ($this->featured_imgw > 0) {
						$width = $this->featured_imgw;
					} else {
						$width = $this->img_mediumw;
					}
					if ($width < 161) { $use_medium_img = false; }
					$w = $width + 10;
					$img_style = ' width="'.$width.'" style="width:'.$width.'px;"';
					$img_box_style = ' style="margin:'.$rmargin.'; float:'.$ralign.'; width:'.$w.'px;"';
				break;
				case 0: default: break;
			}

			$i = 0;
			foreach ($articles as $id => $article) {
				if ($i >= $num) { break; }

				if ($this->source != 3) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$imgbox = '';
				if ($this->featured_img > 0) {
					if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						if (($this->featured_caption == 1) && (trim($article->caption) != '')) {
							$imgbox .= '<div>'.$article->caption."</div>\n";
						}
						$imgbox .= "</div>\n";

					} else {
						$imgfile = $elxis->secureBase().'/'.$article->image;
						$file_info = $eFiles->getNameExtension($article->image);
						if ($use_medium_img) {
							if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
								$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
							} elseif (!file_exists(ELXIS_PATH.'/'.$article->image)) {
								$imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';
							}
						} else {
							if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
								$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
							} elseif (!file_exists(ELXIS_PATH.'/'.$article->image)) {
								$imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';
							}
						}
						unset($file_info);
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$imgfile.'" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						if (($this->featured_caption == 1) && (trim($article->caption) != '')) {
							$imgbox .= '<div>'.$article->caption."</div>\n";
						}
						$imgbox .= "</div>\n";	
					}
				}

				echo '<div class="elx_featured_box">'."\n";
				echo '<h2><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h2>'."\n";
				if ($this->featured_date == 1) {
					$txt = $this->friendlyDate($article->created);
					if (($this->featured_cat == 1) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->featured_cat == 1) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				}

				echo '<div class="elx_category_featured_inner">'."\n";
				echo $imgbox;
				if ($this->featured_sub == 1) {
					if (trim($article->subtitle) != '') { echo '<p class="elx_content_subtitle">'.$article->subtitle."</p>\n"; }
				}

				if ($this->featured_text > 0) {
					$article->introtext = $this->removePlugins($article->introtext);
					if ($this->featured_text == 1000) {
						echo $article->introtext."\n";
						if ($this->featured_more) {
							echo ' <a href="'.$link.'" title="'.$article->title.'">'.$eLang->get('MORE')."</a>\n";
						}
					} else {
						$txt = strip_tags($article->introtext);
						$len = eUTF::strlen($txt);
						if ($len > $this->featured_text) {
							$limit = $this->featured_text - 3;
							$txt = eUTF::substr($txt, 0, $limit).'...';
						}

						if ($this->featured_more) {
							$txt .= ' <a href="'.$link.'" title="'.$article->title.'">'.$eLang->get('MORE')."</a>\n";
						}
						echo '<p class="elx_content_short">'.$txt."</p>\n";
					}
				} else if ($this->featured_more) {
					echo '<a href="'.$link.'" title="'.$article->title.'">'.$eLang->get('MORE')."</a>\n";
				}
				echo '<div style="clear:both;"></div>'."\n";
				echo "</div>\n";
				if ($this->featured_date == 2) {
					$txt = $this->friendlyDate($article->created);
					if (($this->featured_cat == 2) && ($article->catid > 0)) {
						$link2 = $elxis->makeURL($article->seolink);
						$txt .= ' '.$eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					}
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				} elseif (($this->featured_cat == 2) && ($article->catid > 0)) {
					$link2 = $elxis->makeURL($article->seolink);
					$txt = $eLang->get('IN').' <a href="'.$link2.'" title="'.$article->cattitle.'">'.$article->cattitle."</a>\n";
					echo '<div class="elx_dateauthor">'.$txt.'</div>'."\n";
				}
				echo "</div>\n";
				$i++;
			}
		}


		/********************************/
		/* REMOVE ALL PLUGINS FROM TEXT */
		/********************************/
		/* Use this method instead of elxisPlugin::removePlugins() in order not to initiate class in pages were we don't need it */
		private function removePlugins($text) {
			$cregex = '#<code>(.*?)</code>#';
			$regex = '#{[^}]*}(?:.+?{\/[^}]*})?#';
			$eregex = '~href="#elink:(.*?)"~';
			$newtext = preg_replace($cregex, '', $text);
			$newtext = preg_replace($regex, '', $newtext);
			$newtext = preg_replace($eregex, 'href="javascript:void(null);"', $newtext);
			return $newtext;
		}

		/**********************/
		/* USER FRIENDLY DATE */
		/**********************/
		private function friendlyDate($date) {
			$eLang = eFactory::getLang();

			$today = gmdate('Y-m-d');
			if (strpos($date, $today) === 0) {
				$dt = eFactory::getDate()->formatDate($date, '%H:%M');
				return $eLang->get('TODAY').' '.$dt;
			} else {
				$ts = time() - 86400;
				$yesterday = gmdate('Y-m-d', $ts);
				if (strpos($date, $yesterday) === 0) {
					$dt = eFactory::getDate()->formatDate($date, '%H:%M');
					return $eLang->get('YESTERDAY').' '.$dt;
				} else {
					return eFactory::getDate()->formatDate($date, $eLang->get('DATE_FORMAT_4'));
				}
			}
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

			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.introtext, a.image, a.caption, a.created";
			if ($this->source != 3) { $sql .= ", c.title AS cattitle, c.seolink"; }
			$sql .= "\n FROM ".$db->quoteId('#__content')." a";
			if ($this->source != 3) {
				$sql .= "\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
			}
			$sql .= "\n WHERE a.published = 1";	
			if ($this->source != 3) { $sql .= " AND c.published = 1"; }

			if ($this->source == 1) {
				if ($this->subcats == 1) {
					$sql .= "\n AND ((c.catid = :ctg) OR (c.parent_id = :ctg))";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				} else {
					$sql .= "\n AND c.catid = :ctg";
					$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				}
			} else if ($this->source == 2) {
				$sql .= "\n AND a.catid IN (".implode(",", $this->catids).")";
			} else if ($this->source == 3) {
				$sql .= "\n AND a.catid = :ctg";
				$binds[] = array(':ctg', 0, PDO::PARAM_INT);
			}

			$sql .= " AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))";
			$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
			$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);

			if ($this->order == 1) {
				$sql .= "\n ORDER BY a.hits DESC";
			} else if ($this->order == 2) {
				$ts = gmmktime(0, 0, 0, gmdate('m'), gmdate('d') - $this->days, gmdate('Y'));
				$date = gmdate('Y-m-d H:i:s', $ts);
				$sql .= " AND a.created > :crdate";
				$binds[] = array(':crdate', $date, PDO::PARAM_STR);
				$sql .= "\n ORDER BY a.hits DESC";
			} else {
				$sql .= "\n ORDER BY a.created DESC";
			}

			$stmt = $db->prepareLimit($sql, 0, $this->limit);
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

			if (($this->source != 3) && ($catids)) { $catids = array_unique($catids); }

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('element').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle').")"
			."\n OR (".$db->quoteId('element')." = ".$db->quote('introtext').") OR (".$db->quoteId('element')." = ".$db->quote('caption')."))"
			."\n AND ".$db->quoteId('elid')." IN (".implode(", ", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
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
						case 'caption': $rows[$id]->caption = $trans['translation']; break;
						default: break;
					}
				}
			}

			if (($this->source != 3) && ($catids)) {
				if ((($this->featured > 0) && ($this->featured_cat > 0)) || ($this->links_cat > 0)) {
					$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
					."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
					."\n AND ".$db->quoteId('element')." = ".$db->quote('category_title')." AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
					$stmt = $db->prepare($sql);
					$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
					$stmt->execute();
					$translations = $stmt->fetchAllAssoc('elid', PDO::FETCH_ASSOC);
					if ($translations) {
						foreach ($rows as $id => $row) {
							if (($row->catid > 0) && isset($translations[ $row->catid ])) {
								$rows[$id]->cattitle = $translations[ $row->catid ]['translation'];
							}
						}
					}
				}
			}

			return $rows;
		}


		/*****************************/
		/* GET CONTENT IMAGES WIDTHS */
		/*****************************/
		private function getImageWidths() {
			$db = eFactory::getDB();

			$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_content');
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->execute();
			$params_str = (string)$stmt->fetchResult();

			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
			$this->img_thumbw = (int)$params->get('img_thumb_width', 100);
			$this->img_mediumw = (int)$params->get('img_medium_width', 240);
			if ($this->img_thumbw < 10) { $this->img_thumbw = 100; }
			if ($this->img_mediumw < 10) { $this->img_mediumw = 240; }
		}


		/************************/
		/* OPEN COLUMNS WRAPPER */
		/************************/
		private function openWrapper() {
			if ($this->columns > 1) { echo '<div style="margin:0; padding:0;">'."\n"; }
		}


		/*************************/
		/* CLOSE COLUMNS WRAPPER */
		/*************************/
		private function closeWrapper() {
			if ($this->columns > 1) {
				echo '<div style="clear:both;"></div>'."\n";
				echo "</div>\n";
			}
		}


		/***************/
		/* OPEN COLUMN */
		/***************/
		private function openColumn($addspace=true) {
			if ($this->columns  < 2) { return; }
			$dir = eFactory::getLang()->getinfo('DIR');
			if ($dir == 'rtl') {
				$float = 'right';
				$margin = '0 0 0 1%';
			} else {
				$float = 'left';
				$margin = '0 1% 0 0';
			}

			if ($addspace) {
				echo '<div style="margin:'.$margin.'; padding:0; width:49%; float:'.$float.';">'."\n";
			} else {
				echo '<div style="margin:0; padding:0; width:49%; float:'.$float.';">'."\n";
			}
		}


		/****************/
		/* CLOSE COLUMN */
		/****************/
		private function closeColumn() {
			if ($this->columns > 1) { echo "</div>\n"; }
		}

    }
}


$elxmodarts = new modArticles($params);
$elxmodarts->run();
unset($elxmodarts);

?>