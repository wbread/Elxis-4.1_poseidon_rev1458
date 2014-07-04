<?php 
/**
* @version		$Id: mod_iosslider.php 1353 2012-11-10 08:41:44Z datahell $
* @package		Elxis
* @subpackage	Module IOS Slider
* @copyright	Copyright (c) 2008-2012 Is Open Source (http://www.isopensource.com). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Ioannis Sannos ( http://www.isopensource.com )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modIOSslider', false)) {
	class modIOSslider {

		private $source = 0;
		private $cache = 0;
		private $catid = 0;
		private $subcats = 0;
		private $catids = array();
		private $custom = array();
		private $limit = 5;
		private $autoslide = 0;
		private $img_width = 470;
		private $img_height = 310;
		private $thumbspos = 0;
		private $thumbsdims = '';
		private $thumb_width = 100;
		private $thumb_height = 70;
		private $bgcolour = '';
		private $bordercolour = '';
		private $lng = 'en';
		private $translate = false;
		private $apc = false;
		private $errormsg = '';
		private static $idx = 0;
		private $moduleId = 0;


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
			$this->getParams($params);
			if (($elxis->getConfig('CACHE') == 0) || ($this->cache < 1)) {
				if ($elxis->getConfig('APC') == 1) { $this->apc = true; }
			}
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
        	$elxis = eFactory::getElxis();

            $this->cache = (int)$params->get('cache', 0);
            $this->source = (int)$params->get('source', 0);
            if ($this->source == 1) {
            	$this->catid = (int)$params->get('catid', 0);
            	$this->subcats = (int)$params->get('subcats', 0);
            	if ($this->catid < 1) { $this->errormsg = 'No category selected for the slider!'; }
           	} else if ($this->source == 2) {
				$catstr = trim($params->get('catids', ''));
				$catids = explode(',', $catstr);
				if ($catids) {
					foreach ($catids as $catid) {
						$catid = (int)$catid;
						if ($catid > 0) { $this->catids[] = $catid; }
					}
				}
				if (count($this->catids) == 0) { $this->errormsg = 'No categories selected for the slider!'; }
      		}  else if ($this->source == 3) {
      			for ($i=1; $i<7; $i++) {
      				$image = trim($params->get('image'.$i, ''));
      				if (($image == '') || !file_exists(ELXIS_PATH.'/'.$image)) { continue; }
      				$link = trim($params->get('link'.$i, ''));
      				if (($link != '') && (!preg_match('#^(http(s)?\:\/\/)#i', $link))) { $link = $elxis->makeURL($link); }
      				$title = eUTF::trim(strip_tags($params->getML('title'.$i, '')));
      				$image = $elxis->secureBase().'/'.$image;
      				$this->custom[] = array('title' => $title, 'subtitle' => '', 'link' => $link, 'image' => $image, 'thumb' => $image);
				}

				if (count($this->custom) == 0) { $this->errormsg = 'There are no custom images for the slider!'; }
 			}

            $this->limit = (int)$params->get('limit', 5);
            if ($this->limit < 1) { $this->limit = 5; }
            $this->autoslide = (int)$params->get('autoslide', 0);
            $this->img_width = (int)$params->get('img_width', 470);
            if ($this->img_width < 100) { $this->img_width = 470; }
            $this->img_height = (int)$params->get('img_height', 310);
            if ($this->img_height < 60) { $this->img_height = 310; }
            $this->thumbspos = (int)$params->get('thumbspos', 0);
            $this->thumbsdims = trim($params->get('thumbsdims', ''));
            $this->bgcolour = strtoupper(trim($params->get('bgcolour', 'EEEEEE')));
            if ($this->bgcolour == '000000') {
            	$this->bgcolour = '';
			} else if (strlen($this->bgcolour) != 6) {
				$this->bgcolour = '';
           	}
            $this->bordercolour = strtoupper(trim($params->get('bordercolour', 'BBBBBB')));
            if ($this->bordercolour == '000000') {
            	$this->bordercolour = '';
			} else if (strlen($this->bordercolour) != 6) {
				$this->bordercolour = '';
           	}
        }


		/***************************/
		/* ADD REQUIRED JS AND CSS */
		/***************************/
		private function addJSCSS() {
			$eDoc = eFactory::getDocument();

			if (!defined('IOS_SLIDER_LOADED')) {
				$sfx = eFactory::getLang()->getinfo('RTLSFX');
				$baseurl = eFactory::getElxis()->secureBase().'/modules/mod_iosslider';
				$eDoc->addStyleLink($baseurl.'/css/iosslider'.$sfx.'.css');
				$eDoc->addJQuery();
				$eDoc->addLibrary('easing', $baseurl.'/js/jquery.easing.1.3.js', 1.3);
				$eDoc->addScriptLink($baseurl.'/js/iosslider'.$sfx.'.js');
				define('IOS_SLIDER_LOADED', 1);
			}

			$autoslide = ($this->autoslide == 1) ? 'true' : 'false';
			$vertical = ($this->thumbspos == 1) ? 'true' : 'false';
			$js = '$().ready(function() { $(\'#iosslider-'.self::$idx.'\').iosSlider({slideIdx:'.self::$idx.', autoSlide:'.$autoslide.', vertical:'.$vertical.' }); });';
			$eDoc->addScript($js);
			$css = '#iosslider-'.self::$idx.', #iosslider-'.self::$idx.' .iospanel { width:'.$this->img_width.'px; }';
			if (($this->bgcolour != '') || ($this->bordercolour != '')) {
				$css .= '#iosslider_wrap'.self::$idx.' { padding:8px;';
				if ($this->bgcolour != '') { $css .= ' background-color:#'.$this->bgcolour.';'; }
				if ($this->bordercolour != '') { $css .= ' border:1px solid #'.$this->bordercolour.';'; }
				$css .= ' border-radius:8px;';
				$css .= ' }';
			}
			$eDoc->addStyle($css);
		}


		/*************************/
		/* DISPLAY ERROR MESSAGE */
		/*************************/
		private function showError($msg) {
			echo '<div class="elx_warning">'.$msg."</div>\n";
		}


		/**********************/
		/* EXECUTE THE MODULE */
		/**********************/
		public function run() {
        	if ($this->errormsg != '') {
        		$this->showError($this->errormsg);
        		return;
       		}

			self::$idx++;
			$data = $this->getData();
			if (!$data) { return; }

			$total = count($data);

			$this->calcWH($total);
			$this->addJSCSS();

			echo '<div class="iosslider_wrap" id="iosslider_wrap'.self::$idx.'">'."\n";
			echo '<div class="iosslider preload" id="iosslider-'.self::$idx.'">'."\n";
			foreach ($data as $k => $item) {
				if ($item['title'] != '') {
					$title_extra =  ' title="'.$item['title'].'"';
					$alt_text = $item['title'];
				} else {
					$title_extra = '';
					$alt_text = 'image';
				}
				echo '<div class="iospanel">'."\n";
				echo '<div class="iospanel-wrapper">'."\n";
				echo '<p>'."\n";
				if ($item['link'] != '') {
					echo '<a href="'.$item['link'].'"'.$title_extra.'>';
					echo '<img src="'.$item['image'].'" alt="'.$alt_text.'" style="width:'.$this->img_width.'px; height:'.$this->img_height.'px;" /></a>';
				} else {
					echo '<img src="'.$item['image'].'" alt="'.$alt_text.'" style="width:'.$this->img_width.'px; height:'.$this->img_height.'px;" />';
				}
				if ($item['title'] != '') {
					echo '<div class="iosslider_caption">'.$item['title'];
					if ($item['subtitle'] != '') { echo '<br /><span>'.$item['subtitle'].'</span>'; }
					echo "</div>\n";
				}
				echo "</p>\n";
				echo "</div>\n";
				echo "</div>\n";
			}

			echo "</div>\n";
			if ($this->thumbspos == 1) {
				echo '<ul class="iosslider_vnav">'."\n";
				foreach ($data as $k => $item) {
					$x = $k + 1;
					$title_extra = ($item['title'] != '') ? ' title="'.$item['title'].'"' : '';
					echo '<li><a href="javascript:void(null);" class="iosslider_trig" rel="iosslider-'.self::$idx.'" id="iosslider_trig'.self::$idx.'_'.$x.'"'.$title_extra.'>';
					echo '<img src="'.$item['image'].'" alt="thumb" style="width:'.$this->thumb_width.'px; height:'.$this->thumb_height.'px;" /></a></li>'."\n";
				}
				echo "</ul>\n";
			}
			echo '<div style="clear:both;"></div>'."\n";
			if ($this->thumbspos == 0) {
				echo '<ul class="iosslider_nav">'."\n";
				foreach ($data as $k => $item) {
					$x = $k + 1;
					$title_extra = ($item['title'] != '') ? ' title="'.$item['title'].'"' : '';
					echo '<li><a href="javascript:void(null);" class="iosslider_trig" rel="iosslider-'.self::$idx.'" id="iosslider_trig'.self::$idx.'_'.$x.'"'.$title_extra.'>';
					echo '<img src="'.$item['image'].'" alt="thumb" style="width:'.$this->thumb_width.'px; height:'.$this->thumb_height.'px;" /></a></li>'."\n";
				}
				echo "</ul>\n";
			}
			echo "</div>\n";
		}


		/****************************************/
		/* CALCULATE THUMBNAIL WIDTH AND HEIGHT */
		/****************************************/
		private function calcWH($total) {
			if ($this->thumbspos == 2) { return; }

			if ($this->thumbsdims != '') {
				$dims = explode('x', $this->thumbsdims);
				if (is_array($dims) && (count($dims) == 2) && (intval($dims[0]) > 10) && (intval($dims[1]) > 10)) {
					$this->thumb_width = (int)$dims[0];
					$this->thumb_height = (int)$dims[1];
					return;
				}
			}

			$max = 150;
			$min = 40;
			if ($this->thumbspos == 1) {
				$extra_h = 10; //(border: 1 x 2) + (padding: 2 x 2) + (margin: 1 x 4) = 2 + 4 + 10 = 10
				$avail_height = $this->img_height - ($total * $extra_h);
				$h = floor($avail_height / $total);
				$w = intval(($h * $this->img_width) / $this->img_height);
				if ($w > $max) {
					$this->thumb_width = $max;
					$this->thumb_height = intval(($h * $this->thumb_width) / $w);
				} else if ($w < $min) {
					$this->thumb_width = $min;
					$this->thumb_height = intval(($h * $this->thumb_width) / $w);
				} else {
					$this->thumb_width = $w;
					$this->thumb_height = $h;
				}
			} else {
				$this->thumbspos = 0;//make sure no other, invalid value, has been entered
				$extra_w = 10; //(border: 1 x 2) + (padding: 2 x 2) + (margin: 1 x 4) = 2 + 4 + 4 = 10
				$avail_width = $this->img_width - ($total * $extra_w);
				$w = floor($avail_width / $total);
				if ($w > $max) {
					$this->thumb_width = $max;
				} else if ($w < $min) {
					$this->thumb_width = $min;
				} else {
					$this->thumb_width = $w;
				}

				$this->thumb_height = intval(($this->img_height * $this->thumb_width) / $avail_width);
			}
		}


		/*******************/
		/* GET SLIDES DATA */
		/*******************/
		private function getData() {
			if ($this->source == 3) { return $this->custom; }
			if ($this->apc == true) {
				$data = elxisAPC::fetch('data'.$this->moduleId.$this->lng, 'iosslider');
				if ($data !== false) { return $data; }
			}

			$db = eFactory::getDB();
            $elxis = eFactory::getElxis();
            $eFiles = eFactory::getFiles();
			$lowlev = 0;
			$binds = array();
			$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.image, c.seolink"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid"
			."\n WHERE a.published = 1 AND c.published = 1";
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
			}
			$sql .= "\n AND a.alevel = :lowlevel ORDER BY a.created DESC";
			$binds[] = array(':lowlevel', $lowlev, PDO::PARAM_INT);
			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			foreach ($binds as $bind) {
				$stmt->bindParam($bind[0], $bind[1], $bind[2]);
			}
			$stmt->execute();
			$rows = $stmt->fetchAllAssoc('id', PDO::FETCH_OBJ);

			if (!$rows) { return array(); }

			if ($this->translate === true) { $rows = $this->translateArticles($rows); }

			$data = array();
			foreach ($rows as $row) {
				if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) { continue; }
				$image = $elxis->secureBase().'/'.$row->image;
				$file_info = $eFiles->getNameExtension($row->image);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$thumb = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
				} else {
					$thumb = $elxis->secureBase().'/'.$row->image;
				}

				if ($row->catid > 0) {
					$link = $elxis->makeURL('content:'.$row->seolink.$row->seotitle.'.html');
				} else {
					$link = $elxis->makeURL('content:'.$row->seotitle.'.html');
				}
      			$data[] = array('title' => $row->title, 'subtitle' => $row->subtitle, 'link' => $link, 'image' => $image, 'thumb' => $thumb);
			}

			if (($this->apc == true) && $data) {
				elxisAPC::store('data'.$this->moduleId.$this->lng, 'iosslider', $data, 600);
			}

            return $data;
		}


		/**********************/
		/* TRANSLATE ARTICLES */
		/**********************/
		private function translateArticles($rows) {
			$db = eFactory::getDB();

			$ids = array();
			foreach ($rows as $row) { $ids[] = $row->id; }
			$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('element').", ".$db->quoteId('translation')
			."\n FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('language')." = :lng"
			."\n AND ((".$db->quoteId('element')." = ".$db->quote('title').") OR (".$db->quoteId('element')." = ".$db->quote('subtitle')."))"
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
						default: break;
					}
				}
			}
			return $rows;
		}

	}
}


$iosslider = new modIOSslider($params, $elxmod);
$iosslider->run();
unset($iosslider);

?>