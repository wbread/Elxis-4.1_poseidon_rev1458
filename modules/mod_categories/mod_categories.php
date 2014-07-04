<?php 
/**
* @version		$Id: mod_categories.php 1205 2012-06-23 18:58:43Z datahell $
* @package		Elxis
* @subpackage	Module Categories
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modCategories', false)) {
	class modCategories {

		private $home = 0;
		private $mode = 0;
		private $orientation = 0;
		private $catids = array();
		private $order = 0;
		private $apc = false;
		private $moduleId = 0;
		private $lng = 'en';
		private $translate = false;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			$elxis = eFactory::getElxis();

			$this->moduleId = $elxmod->id;
			$this->lng = eFactory::getURI()->getUriLang();
			if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
				if ($this->lng != '') { $this->translate = true; }
			}
			$this->home = (int)$params->get('home', 0);
			$this->mode = (int)$params->get('mode', 0);
			$this->order = (int)$params->get('order', 0);
			$this->orientation = (int)$params->get('orientation', 0);
			if ($this->mode == 2) {
				$catstr = trim($params->get('catids', ''));
				$catids = explode(',', $catstr);
				if ($catids) {
					foreach ($catids as $catid) {
						$catid = (int)$catid;
						if ($catid > 0) { $this->catids[] = $catid; }
					}
				}
			}

			$cache = (int)$params->get('cache', 0);
			if (($elxis->getConfig('CACHE') == 0) || ($cache < 1)) {
				if ($elxis->getConfig('APC') == 1) {
					$this->apc = true;
					if ($elxis->user()->gid <> 7) { $this->apc = false; }
				}
			}
        }


		/******************/
		/* EXECUTE MODULE */
		/******************/
        public function run() {
			$categories = $this->getCategories();
			if (!$categories && ($this->home == 0)) { return; }

			if (!defined('MOD_CATEGORIES_CSS')) {
				$cssfile = eFactory::getElxis()->secureBase().'/modules/mod_categories/css/categories.css';
				eFactory::getDocument()->addStyleLink($cssfile);
				define('MOD_CATEGORIES_CSS', 1);
			}

			if ($this->orientation == 0) {
				$this->popVertical($categories);
			} else {
				$this->popHorizontal($categories);
			}
		}


		/**********************************/
		/* POPULATE CATEGORIES VERTICALLY */
		/**********************************/
		private function popVertical($categories) {
			$eURI = eFactory::getURI();
			$eLang = eFactory::getLang();

			$sfx = $eLang->getinfo('RTLSFX');
			echo '<ul class="modcateg'.$sfx.'">'."\n";
			if ($this->home == 1) {
				$link = $eURI->makeURL();
				$home = $eLang->get('HOME');
				echo '<li><a href="'.$link.'" title="'.$home.'">'.$home.'</a></li>'."\n";
			}
			if ($categories) {
				foreach ($categories as $ctg) {
					$link = $eURI->makeURL('content:'.$ctg['seolink']);
					echo '<li><a href="'.$link.'" title="'.$ctg['title'].'">'.$ctg['title'].'</a></li>'."\n";
					if (isset($ctg['subs']) && (count($ctg['subs']) > 0)) {
						foreach ($ctg['subs'] as $sctg) {
							$link = $eURI->makeURL('content:'.$sctg['seolink']);
							echo '<li class="modcateg_sub"><a href="'.$link.'" title="'.$sctg['title'].'">'.$sctg['title'].'</a></li>'."\n";
						}
					}
				}
			}
			echo "</ul>\n";
		}


		/************************************/
		/* POPULATE CATEGORIES HORIZONTALLY */
		/************************************/
		private function popHorizontal($categories) {
			$eURI = eFactory::getURI();
			$eLang = eFactory::getLang();

			$sfx = $eLang->getinfo('RTLSFX');
			echo '<div class="modcateg_h'.$sfx.'">'."\n";
			if ($this->home == 1) {
				$link = $eURI->makeURL();
				$home = $eLang->get('HOME');
				echo '<a href="'.$link.'" title="'.$home.'">'.$home.'</a>'." \n";
			}
			if ($categories) {
				foreach ($categories as $ctg) {
					$link = $eURI->makeURL('content:'.$ctg['seolink']);
					echo '<a href="'.$link.'" title="'.$ctg['title'].'">'.$ctg['title'].'</a> '."\n";
					if (isset($ctg['subs']) && (count($ctg['subs']) > 0)) {
						foreach ($ctg['subs'] as $sctg) {
							$link = $eURI->makeURL('content:'.$sctg['seolink']);
							echo '<a class="modcateg_hsub" href="'.$link.'" title="'.$sctg['title'].'">'.$sctg['title'].'</a> '."\n";
						}
					}
				}
			}
			echo "</div>\n";
		}


        /****************************/
	    /* FETCH CATEGORIES FROM DB */
	    /****************************/
		private function getCategories() {
			$elxis = eFactory::getElxis();
			$db = eFactory::getDB();

			if ($this->apc == true) {
				$data = elxisAPC::fetch('data'.$this->moduleId.$this->lng, 'modcateg');
				if ($data !== false) { return $data; }
			}

			switch ($this->mode) {
				case 2:
					if (!$this->catids) { return array(); }
					$where = $db->quoteId('catid').' IN ('.implode(', ',$this->catids).')';
				break;
				case 1: case 0: default:
					$where = $db->quoteId('parent_id').' = 0 ';
				break;
			}

            $lowlev = $elxis->acl()->getLowLevel();
            $exactlev = $elxis->acl()->getExactLevel();

            $sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('title').", ".$db->quoteId('seolink')
            ."\n FROM ".$db->quoteId('#__categories')." WHERE ".$db->quoteId('published')."=1 AND ".$where
            ."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))";
            if ($this->order == 0) {
           		$sql .= " ORDER BY ".$db->quoteId('title')." ASC";
           	} else {
           		$sql .= " ORDER BY ".$db->quoteId('ordering')." ASC";
			}
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
            $stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if (!$rows) { return array(); }

			$data = array();
			$catids = array();
			foreach ($rows as $row) {
				$catid = $row['catid'];
				$catids[] = $catid;
				$data[$catid] = array('catid' => $catid, 'title' => $row['title'], 'seolink' => $row['seolink'], 'subs' => array());
			}

			if ($this->mode == 1) {
				$where = $db->quoteId('parent_id').' IN ('.implode(', ', $catids).')';
            	$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('parent_id').", ".$db->quoteId('title').", ".$db->quoteId('seolink')
            	."\n FROM ".$db->quoteId('#__categories')." WHERE ".$db->quoteId('published')."=1 AND ".$where
            	."\n AND ((".$db->quoteId('alevel')." <= :lowlevel) OR (".$db->quoteId('alevel')." = :exactlevel))";
            	if ($this->order == 0) {
           			$sql .= " ORDER BY ".$db->quoteId('title')." ASC";
           		} else {
           			$sql .= " ORDER BY ".$db->quoteId('ordering')." ASC";
				}
            	$stmt = $db->prepare($sql);
            	$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
            	$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
            	$stmt->execute();
            	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  				if ($rows) {
					foreach ($rows as $row) {
						$catid = $row['catid'];
						$parent = $row['parent_id'];
						$catids[] = $catid;
						$data[$parent]['subs'][$catid] = array('catid' => $catid, 'title' => $row['title'], 'seolink' => $row['seolink']);
					}
				}
			}

			if ($this->translate) {
				$translations = $this->getTranslations($catids);
				if ($translations) {
					foreach ($data as $cid => $ctg) {
						if (isset($translations[$cid])) {
							$data[$cid]['title'] = $translations[$cid];
						}
						if (isset($ctg['subs']) && (count($ctg['subs']) > 0)) {
							foreach ($ctg['subs'] as $scid => $sctg) {
								if (isset($translations[$scid])) {
									$data[$cid]['subs'][$scid]['title'] = $translations[$scid];
								}
							}
						}
					}
				}
			}

			if ($this->apc == true) {
				elxisAPC::store('data'.$this->moduleId.$this->lng, 'modcateg', $data, 1800);
			}

            return $data;
		}


		/*******************************/
		/* GET CATEGORIES TRANSLATIONS */
		/*******************************/
		private function getTranslations($catids) {
			$db = eFactory::getDB();

			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('element')."=".$db->quote('category_title')
			."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':lng', $this->lng, PDO::PARAM_STR);
			$stmt->execute();
			return $stmt->fetchPairs();
		}

	}
}


$modCategs = new modCategories($params, $elxmod);
$modCategs->run();
unset($modCategs);

?>