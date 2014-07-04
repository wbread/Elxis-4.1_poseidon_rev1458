<?php 
/**
* @version		$Id: mod_ads.php 1414 2013-04-25 17:41:52Z datahell $
* @package		Elxis
* @subpackage	Module Advertisements
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modAdverts', false)) {
	class modAdverts {

		private $source = 0;
		private $ads = array();
		private $links = array();
		private $width = 100;
		private $height = 100;
		private $target = '_blank';
		private $border = 1;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
        	$elxis = eFactory::getElxis();

			$this->source = (int)$params->get('source', 0);
			switch ($this->source) {
				case 3: 
					$adsense = trim($params->get('adsense', ''));
					if ($adsense != '') { $this->ads[] = $adsense; }
				break;
				case 2:
					$this->width = (int)$params->get('width', 100);
					$this->height = (int)$params->get('height', 100);
					for ($i=1; $i<5; $i++) {
						$param = 'flash'.$i;
						$swf = trim($params->get($param, ''));
						if ($swf != '') {
							if (preg_match('#(\.swf)$#i', $swf)) {
								if (preg_match('#^(http(s)?\:\/\/)#i', $swf)) {
									$this->ads[] = $swf;
								} else if (file_exists(ELXIS_PATH.'/'.$swf)) {
									$this->ads[] = $elxis->secureBase().'/'.$swf;
								}
							}
						}
					}
				break;
				case 1:
					$q = 0;
					$reldir = 'media/images/';
					if (defined('ELXIS_MULTISITE')) {
						if (ELXIS_MULTISITE > 1) { $reldir .= 'site'.ELXIS_MULTISITE.'/'; }
					}
					$reldir .= 'ads/';
					for ($i=1; $i<5; $i++) {
						$param = 'image'.$i;
						$img = trim($params->get($param, ''));
						if ($img != '') {
							if (file_exists(ELXIS_PATH.'/'.$reldir.$img)) {
								$this->ads[$q] = $elxis->secureBase().'/'.$reldir.$img;
								$paraml = 'link'.$i;
								$link = trim($params->getML($paraml, ''));
								$link = preg_replace('#^(\/)#', '', $link);
								if ($link != '') {
									if (preg_match('#^(http(s)?\:\/\/)#i', $link)) {
										$this->links[$q] = $link;
									} else {
										$this->links[$q] = $elxis->makeURL($link);
									}
								}
								$q++;
							}
						}
					}
				break;
				case 0: default:
					$this->source = 0; //just in case of an invalid value
					$q = 0;
					for ($i=1; $i<5; $i++) {
						$param = 'text'.$i;
						$txt = eUTF::trim($params->getML($param, ''));
						if ($txt != '') {
							$this->ads[$q] = $txt;
							$paraml = 'link'.$i;
							$link = trim($params->getML($paraml, ''));
							$link = preg_replace('#^(\/)#', '', $link);
							if ($link != '') {
								if (preg_match('#^(http(s)?\:\/\/)#i', $link)) {
									$this->links[$q] = $link;
								} else {
									$this->links[$q] = $elxis->makeURL($link);
								}
							}
							$q++;
						}
					}

					if (!$this->ads) { $this->sampleAds(); }
				break;
			}

			$this->target = (intval($params->get('target', 0) == 1)) ? '_self' : '_blank';
			$this->border = (int)$params->get('border', 1);
        }


		/***************************/
		/* DISPLAY SAMPLE TEXT ADS */
		/***************************/
		private function sampleAds() {
			$this->ads = array(
				'You can have multiple web sites under one Elxis installation. Try Elxis CMS, it is free!',
				'You need a modern, powerful, fast and reliable CMS to build your web site? Try Elxis, it is free!',
				'Warning about security? Elxis Defender is here to protect your Elxis powered web site against web attacks.',
				'Elxis allows you to have content in multiple languages. Try Elxis, the pure multilingual CMS!'
			);
			$this->links = array(
				'http://www.elxis.org/',
				'http://www.elxis.org/',
				'http://www.elxis.org/',
				'http://www.elxis.org/'
			);
		}


		/*****************************************/
		/* PICK A RANDOM AD AND RETURN ITS INDEX */
		/*****************************************/
		private function pickAd() {
			$c = count($this->ads);
			if ($c == 1) { return 0; }
			$max = $c - 1;
			return rand(0, $max);
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/         
        public function run() {
        	if (!$this->ads) { return; }
        	$idx = $this->pickAd();
			switch($this->source) {
				case 3: $this->htmlAd($idx); break;
				case 2: $this->flashAd($idx); break;
				case 1: $this->imageAd($idx); break;
				case 0: $this->textAd($idx); break;
			}
		}


		/*******************/
		/* DISPLAY HTML AD */
		/*******************/
		private function htmlAd($idx) {
			echo stripslashes($this->ads[$idx]);
		}


		/**********************************/
		/* DISPLAY FLASH AD (XHTML VALID) */
		/**********************************/
		private function flashAd($idx) {
			$swf = $this->ads[$idx];
			echo '<object type="application/x-shockwave-flash" data="'.$swf.'" width="'.$this->width.'" height="'.$this->height.'">'."\n";
			echo '<param name="movie" value="'.$swf.'" />'."\n";
			echo '<param name="quality" value="high" />'."\n";
			echo "</object>\n";
		}


		/********************/
		/* DISPLAY IMAGE AD */
		/********************/
		private function imageAd($idx) {
			$css = eFactory::getElxis()->secureBase().'/modules/mod_ads/css/ads.css';
			eFactory::getDocument()->addStyleLink($css);

			$img = $this->ads[$idx];
			$link = (isset($this->links[$idx])) ? $this->links[$idx] : '';

			echo '<div class="modads_box">'."\n";
			if ($link != '') { echo '<a href="'.$link.'" target="'.$this->target.'" class="modads_link">'; }
			echo '<img src="'.$img.'" alt="advertisement" border="0" />';
			if ($link != '') { echo '</a>'; }
			echo "\n";
			echo "</div>\n";
		}


		/*******************/
		/* DISPLAY TEXT AD */
		/*******************/
		private function textAd($idx) {
			$eLang = eFactory::getLang();

			$css = eFactory::getElxis()->secureBase().'/modules/mod_ads/css/ads.css';
			eFactory::getDocument()->addStyleLink($css);

			$text = $this->ads[$idx];
			$link = (isset($this->links[$idx])) ? $this->links[$idx] : '';
			echo '<div class="modads_box">'."\n";
			if ($this->border == 1) { echo '<div class="modads_box_border">'."\n"; }
			if ($link != '') { echo '<a href="'.$link.'" target="'.$this->target.'" class="modads_link">'; }
			echo $text;
			if ($link != '') { echo '</a>'; }
			echo "\n";
			if ($this->border == 1) {
				echo '<span class="modads_box_mark'.$eLang->getinfo('RTLSFX').'">'.$eLang->get('ADVERTISEMENT')."</span>\n";
				echo '</div>'."\n";
			}
			echo "</div>\n";
		}

    }
}


$elxmodads = new modAdverts($params);
$elxmodads->run();
unset($elxmodads);

?>