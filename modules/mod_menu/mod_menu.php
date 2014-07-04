<?php 
/**
* @version		$Id: mod_menu.php 1362 2012-11-17 19:38:14Z datahell $
* @package		Elxis
* @subpackage	Module Menu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modMenu', false)) {
	class modMenu {

		private $collection = 'mainmenu';
		private $orientation = 0;
		private $elxis_uri = '';
		private $is_frontpage = false;
		private $lightbox_loaded = false;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$eURI = eFactory::getURI();

			$this->elxis_uri = $eURI->getElxisUri();
			$component = $eURI->getComponent();
			$defroute = eFactory::getElxis()->getConfig('DEFAULT_ROUTE');
			if (($this->elxis_uri == '') || ($this->elxis_uri == '/') || ($this->elxis_uri == $defroute)) {
				$this->elxis_uri = $defroute;
				$this->is_frontpage = true;
			} else {
				if (strpos($this->elxis_uri, ':') === false) { $this->elxis_uri = $component.':'.$this->elxis_uri; }
			}

			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
			$this->collection = $params->get('collection', 'mainmenu');
			if ($this->collection == '') { $this->collection = 'mainmenu'; }
			$this->orientation = (int)$params->get('orientation', 0);
        }


		/********************/
		/* RUN FOREST, RUN! */
		/********************/         
        public function run() {
			$items = eFactory::getMenu()->getItems($this->collection, 'frontend');
			$this->populate(0, $items);
       	}


		/*****************/
		/* POPULATE MENU */
		/*****************/
		private function populate($level, $items) {
			if (!$items) { return; }

			$elxis = eFactory::getElxis();

			$t = str_repeat("\t", $level);
			$t2 = $t."\t";
			$ulclass = '';
			if ($level == 0) {
				$ulclass = ($this->orientation == 0) ? ' class="elx_vmenu"' : ' class="elx_menu"';
			}

			$inc = rand(10,99);
			echo "\n".$t.'<ul'.$ulclass.'>'."\n";
			foreach ($items as $key => $item) {
				if ($this->elxis_uri == $item->link) {
					$liclass = ' class="menu_active"';
				} else if (($item->link == '') && $this->is_frontpage) {
					$liclass = ' class="menu_active"';
				} else {
					$liclass = '';
				}

				$contents = '';
				if ($item->menu_type == 'url') {
					$onclick = '';
					$hrefid = '';
					if ($item->popup == 2) {
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$this->loadLightBox();
						$link = $item->link;
						$item->target = '_blank';
						eFactory::getDocument()->addDocReady('$("#mitem'.$item->menu_id.'_'.$inc.'").colorbox({iframe:true, width:'.$w.', height:'.$h.'});');
						$hrefid = ' id="mitem'.$item->menu_id.'_'.$inc.'"';
						$inc++;
					} else if ($item->popup == 1) {
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$onclick = ' onclick="elxPopup(\''.$item->link.'\', '.$w.', '.$h.');"';
						$link = 'javascript:void(null);';
						$item->target = '_blank';
					} else {
						$link = $item->link;
					}
					$trg = ($item->target != '_self') ? ' target="'.$item->target.'"' : '';
					$contents = '<a href="'.$link.'" title="'.$item->title.'"'.$hrefid.$onclick.$trg.'>'.$item->title."</a>\n";
				} elseif ($item->menu_type == 'separator') {
					$liclass = ' class="menu_separator"';
					$contents = '<a href="javascript:void(null);">'.$item->title."</a>\n";
				} elseif ($item->menu_type == 'wrapper') {
					$ssl = ($item->secure == 1) ? true : false;
					$onclick = '';
					$hrefid = '';
					if ($item->popup == 2) {
						if ($item->file == '') { $item->file = 'inner.php'; }
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$this->loadLightBox();
						$link = $elxis->makeURL('wrapper:'.$item->menu_id.'.html', $item->file, $ssl, false);
						$item->target = '_blank';
						eFactory::getDocument()->addDocReady('$("#mitem'.$item->menu_id.'_'.$inc.'").colorbox({iframe:true, width:'.$w.', height:'.$h.'});');
						$hrefid = ' id="mitem'.$item->menu_id.'_'.$inc.'"';
						$inc++;
					} else if ($item->popup == 1) {
						if ($item->file == '') { $item->file = 'inner.php'; }
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$plink = $elxis->makeURL('wrapper:'.$item->menu_id.'.html', $item->file, $ssl, false);
						$onclick = ' onclick="elxPopup(\''.$plink.'\', '.$w.', '.$h.');"';
						$link = 'javascript:void(null);';
					} else {
						$link = $elxis->makeURL('wrapper:'.$item->menu_id.'.html', $item->file, $ssl);
					}
					$trg = ($item->target != '_self') ? ' target="'.$item->target.'"' : '';
					$contents = '<a href="'.$link.'" title="'.$item->title.'"'.$hrefid.$onclick.$trg.'>'.$item->title."</a>\n";
				} else {
					$ssl = ($item->secure == 1) ? true : false;
					$onclick = '';
					$hrefid = '';
					if ($item->popup == 2) {
						if ($item->file == '') { $item->file = 'inner.php'; }
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$this->loadLightBox();
						$link = $elxis->makeURL($item->link, $item->file, $ssl, false);
						$item->target = '_blank';
						eFactory::getDocument()->addDocReady('$("#mitem'.$item->menu_id.'_'.$inc.'").colorbox({iframe:true, width:'.$w.', height:'.$h.'});');
						$hrefid = ' id="mitem'.$item->menu_id.'_'.$inc.'"';
						$inc++;
					} else if ($item->popup == 1) {
						if ($item->file == '') { $item->file = 'inner.php'; }
						$w = ($item->width > 10) ? $item->width : 970;
						$h = ($item->height > 10) ? $item->height : 450;
						$link = $elxis->makeURL($item->link, $item->file, $ssl, false);
						$onclick = ' onclick="elxPopup(\''.$link.'\', '.$w.', '.$h.');"';
						$link = 'javascript:void(null);';
						$item->target = '_blank';
					} else {
						$link = $elxis->makeURL($item->link, $item->file, $ssl);
					}
					$trg = ($item->target != '_self') ? ' target="'.$item->target.'"' : '';
					$contents = '<a href="'.$link.'" title="'.$item->title.'"'.$hrefid.$onclick.$trg.'>'.$item->title."</a>\n";
				}

				echo $t2.'<li'.$liclass.'>'."\n";
				echo $contents;

				if (count($item->children) > 0) {
					$this->populate($level+1, $item->children);
					echo $t2."</li>\n";
				} else {
					echo "</li>\n";
				}
			}
			echo $t.'</ul>'."\n";
		}


		/********************************/
		/* LOAD ELXIS STANDARD LIGHTBOX */
		/********************************/
		private function loadLightBox() {
			if ($this->lightbox_loaded) { return; }
			$this->lightbox_loaded = true;
			eFactory::getDocument()->loadLightbox();
		}

	}
}


$elxmodmenu = new modMenu($params);
$elxmodmenu->run();
unset($elxmodmenu);

?>