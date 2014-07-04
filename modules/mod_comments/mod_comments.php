<?php 
/**
* @version		$Id: mod_comments.php 1395 2013-02-24 10:42:44Z datahell $
* @package		Elxis
* @subpackage	Module Comments
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modComments', false)) {
	class modComments {

		private $limit = 5;
		private $source = 0;
		private $catid = 0;
		private $subcats = 0;
		private $title_limit = 30;
		private $comment_limit = 100;
		private $name_limit = 16;
		private $display_date = 1;
		private $avatar = 1;
		private $avatar_w = 40;
		private $gravatar = 0;
		private $title_colour = '333333';
		private $bg_colour = 'EEEEEE';
		private $font_colour = '555555';
		private $seolink = '';
		private $lng = 'en';
		private $translate = false;
		private $today = '';
		private $yesterday = '';


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->lng = eFactory::getURI()->getUriLang();
			if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
				if ($this->lng != '') { $this->translate = true; }
			}
			$this->today = gmdate('Y-m-d');
			$ts = time() - 86400;
			$this->yesterday = gmdate('Y-m-d', $ts);
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
			$this->limit = (int)$params->get('limit', 5);
            if ($this->limit < 1) { $this->limit = 5; }
			$this->source = (int)$params->get('source', 0);
            $this->catid = (int)$params->get('catid', 0);
			if ($this->catid == 0) {
				if ($this->source == 1) { $this->source = 0; }
			}
			$this->subcats = (int)$params->get('subcats', 0);
			if ($this->source == 2) {
				$this->autoDetect();
				if ($this->seolink == '') { $this->source = 0; }
			}

            $this->title_limit = (int)$params->get('title_limit', 30);
            if ($this->title_limit < 10) { $this->title_limit = 30; }
            $this->comment_limit = (int)$params->get('comment_limit', 100);
            if ($this->comment_limit < 10) { $this->comment_limit = 100; }
            $this->name_limit = (int)$params->get('name_limit', 14);
            if ($this->name_limit < 8) { $this->name_limit = 14; }
            $this->display_date = (int)$params->get('display_date', 1);
            $this->avatar = (int)$params->get('avatar', 1);
            $this->avatar_w = (int)$params->get('avatar_w', 40);
            if ($this->avatar_w < 30) { $this->avatar_w = 40; }
            $this->gravatar = (int)$params->get('gravatar', 0);
            $this->title_colour = trim($params->get('title_colour', '333333'));
			if ($this->title_colour == '') { $this->title_colour = '333333'; }
            $this->bg_colour = trim($params->get('bg_colour', 'EEEEEE'));
			if ($this->bg_colour == '') { $this->bg_colour = 'EEEEEE'; }
            $this->font_colour = trim($params->get('font_colour', '555555'));
			if ($this->font_colour == '') { $this->font_colour = '555555'; }
			
			if (ELXIS_MOBILE == 1) {
				$this->display_date = 0;
				$this->avatar = 0;
			}
        }


		/********************/
		/* MODULE EXECUTION */
		/********************/         
		public function run() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$rows = $this->getComments();
			if (!$rows) {
				echo '<div class="elx_warning">'.$eLang->get('NOCOMMENTS')."</div>\n";
				return;
			}

			if ($this->avatar == 1) {
				$avatars = $this->collectAvatars($rows);
				$boxw = $this->avatar_w + 10;
				if ($eLang->getinfo('DIR') == 'rtl') {
					$float = 'right';
					$margin = '0 '.$boxw.'px 0 0;';
				} else {
					$float = 'left';
					$margin = '0 0 0 '.$boxw.'px;';
				}
			}

			if ($eLang->getinfo('DIR') == 'rtl') {
				$img_arrow = $elxis->secureBase().'/modules/mod_comments/css/arrow-rtl.png';
				$cssfile = $elxis->secureBase().'/modules/mod_comments/css/comments-rtl.css';
			} else {
				$img_arrow = $elxis->secureBase().'/modules/mod_comments/css/arrow.png';
				$cssfile = $elxis->secureBase().'/modules/mod_comments/css/comments.css';
			}

			eFactory::getDocument()->addStyleLink($cssfile);

			$crop_title = $this->title_limit - 3;
			$crop_comment = $this->comment_limit - 3;
			$crop_name = $this->name_limit - 3;

			echo '<ul class="modcomments_wrap">'."\n";
			foreach ($rows as $row) {
				$n = eUTF::strlen($row->title);
				$title = ($n > $this->title_limit) ? eUTF::substr($row->title, 0, $crop_title).'...' : $row->title;

				$message = strip_tags($row->message);
				$n = eUTF::strlen($message);
				if ($n > $this->comment_limit) { $message = eUTF::substr($message, 0, $crop_comment).'...'; }

				$n = eUTF::strlen($row->author);
				$author = ($n > $this->name_limit) ? eUTF::substr($row->author, 0, $crop_name).'...' : $row->author;

				if (intval($row->catid) > 0) {
					$link = $elxis->makeURL($row->seolink.$row->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($row->seotitle.'.html');
				}

				echo "<li>\n";
				echo '<a href="'.$link.'" title="'.$row->title.'" class="modcomm_title" style="color:#'.$this->title_colour.'">'.$title."</a>\n";
				if ($this->avatar == 1) {
					$uid = (int)$row->uid;
					if ($uid > 0) {
						$img = (isset($avatars[$uid])) ? $avatars[$uid] : '';
					} else {
						$img = '';
					}

					$avatar = $elxis->obj('avatar')->getAvatar($img, $this->avatar_w, $this->gravatar, $row->email);
					echo '<div class="elx_avatar_box" style="width:'.$boxw.'px; float:'.$float.';">'."\n";
					echo '<img src="'.$avatar.'" alt="'.$row->author.'" width="'.$this->avatar_w.'" height="'.$this->avatar_w.'" title="'.$row->author.'" />';
					echo "</div>\n";
					echo '<div style="margin:'.$margin.'">'."\n";
				} else {
					echo '<div>'."\n";
				}

				echo '<div class="modcomm_head" style="background-color:#'.$this->bg_colour.'">'."\n";
				echo '<div class="modcomm_author" style="color:#'.$this->font_colour.'">'.$author."</div>\n";
				if ($this->display_date == 1) {
					echo '<div class="modcomm_date" style="color:#'.$this->font_colour.'">'.$this->friendlyDate($row->created)."</div>\n";
				}
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
				echo '<div class="modcomm_comment">'."\n";
				echo $message."\n";
				echo ' <a href="'.$link.'" title="'.$eLang->get('GO_TO_ARTICLE').'"><img src="'.$img_arrow.'" alt="arrow" /></a>'."\n";
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
        }


		/********************************/
		/* AUTO DETECT CURRENT CATEGORY */
		/********************************/
		private function autoDetect() {
			$eURI = eFactory::getURI();

			if ($eURI->getComponent() != 'content') { $this->source = 0; return; }

            $segments = $eURI->getSegments();
			$n = count($segments);
			if ($n == 0) { $this->source = 0; return; }

			$elxis_uri = $eURI->getElxisUri();
			if (($elxis_uri == '') || ($elxis_uri == '/')) { $this->source = 0; return; }
			if ($eURI->isDir()) { $this->seolink = $elxis_uri; return; }

			$global_pages = array('feeds.html', 'contenttools.html', 'tags.html', 'send-to-friend.html');
			if (in_array($segments[0], $global_pages)) { $this->source = 0; return; }

			$last = $n - 1;
			if (!preg_match('/(\.html)$/i', $segments[$last])) { $this->source = 0; return; }

			$this->seolink = '';
			for ($i=0; $i<$n; $i++) {
				if ($i == $last) { break; }
				$this->seolink .= $segments[$i].'/';
			}
		}


		/**********************************/
		/* GET COMMENTS FROM THE DATABASE */
		/**********************************/
		private function getComments() {
			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();

			$lowlev = $elxis->acl()->getLowLevel();
			$exactlev = $elxis->acl()->getExactLevel();
			$celement = 'com_content';

			$binds = array();
			$binds[] = array(':celem', $celement, PDO::PARAM_STR);

			$sql = "SELECT c.id, c.message, c.created, c.uid, c.author, c.email, a.id AS aid, a.catid, a.title, a.seotitle, g.seolink"
			."\n FROM ".$db->quoteId('#__comments')." c"
			."\n JOIN ".$db->quoteId('#__content')." a ON a.id = c.elid"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." g ON g.catid=a.catid"
			."\n WHERE c.published = 1 AND c.element = :celem AND a.published = 1 AND ((g.published = 1) OR (a.catid = 0))";

			if ($this->source == 1) {
				$binds[] = array(':ctg', $this->catid, PDO::PARAM_INT);
				if ($this->subcats == 1) {
					$sql .= " AND ((g.catid = :ctg) OR (g.parent_id = :ctg))";
				} else {
					$sql .= " AND g.catid = :ctg";
				}

				$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
				$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);
				$sql .= "\n AND ((g.alevel <= :lowlevel) OR (g.alevel = :exactlevel))";
				
			} else if ($this->source == 2) {
				$binds[] = array(':seol', $this->seolink, PDO::PARAM_STR);
				$sql .= " AND g.seolink = :seol";

				$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
				$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);
				$sql .= "\n AND ((g.alevel <= :lowlevel) OR (g.alevel = :exactlevel))";
			} else {
				$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
				$binds[] = array(':exactlevel', $exactlev, PDO::PARAM_INT);
				$sql .= "\n AND ((g.alevel <= :lowlevel) OR (g.alevel = :exactlevel) OR (a.catid = 0))";
			}
			$sql .= "\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))";
			$sql .= "\n ORDER BY c.created DESC";

			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

			if ($rows && ($this->translate === true)) {
				$rows = $this->translateTitles($rows);
			}

			return $rows;            
        }


		/****************************/
		/* TRANSLATE ARTICLES TITLE */
		/****************************/
		private function translateTitles($rows) {
			$db = eFactory::getDB();

			$ids = array();
			foreach ($rows as $row) { $ids[] = $row->aid; }
			$ids = array_unique($ids);

			$ttl = 'title';
			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ".$db->quoteId('element')." = :ttl"
			."\n AND ".$db->quoteId('elid')." IN (".implode(",", $ids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':ttl', $ttl, PDO::PARAM_STR);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
			$stmt->execute();
			$translations = $stmt->fetchPairs();

			if ($translations) {
				foreach ($rows as $k => $row) {
					$aid = $row->aid;
					if (isset($translations[$aid])) {
						$rows[$k]->title = $translations[$aid];
					}
				}
			}

			return $rows;
		}


		/************************/
		/* COLLECT USERS AVATAR */
		/************************/
		private function collectAvatars($rows) {
			$db = eFactory::getDB();

			$uids = array();
			foreach ($rows as $row) {
				if (intval($row->uid) > 0) { $uids[] = $row->uid; }
			}

			if (!$uids) { return array(); }

			$uids = array_unique($uids);
			$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('avatar')." FROM ".$db->quoteId('#__users')." WHERE ".$db->quoteId('uid')." IN (".implode(",", $uids).")";
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$avatars = $stmt->fetchPairs();

			return $avatars;
		}


		/**********************/
		/* USER FRIENDLY DATE */
		/**********************/
		private function friendlyDate($date) {
			$eLang = eFactory::getLang();

			if (strpos($date, $this->today) === 0) {
				$dt = eFactory::getDate()->formatDate($date, '%H:%M');
				return $eLang->get('TODAY').' '.$dt;
			} else {
				if (strpos($date, $this->yesterday) === 0) {
					$dt = eFactory::getDate()->formatDate($date, '%H:%M');
					return $eLang->get('YESTERDAY').' '.$dt;
				} else {
					return eFactory::getDate()->formatDate($date, $eLang->get('DATE_FORMAT_2'));
				}
			}
		}

	}
}


$modcomm = new modComments($params);
$modcomm->run();
unset($modcomm);

?>