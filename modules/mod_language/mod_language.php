<?php 
/**
* @version		$Id: mod_language.php 1400 2013-03-07 17:20:47Z datahell $
* @package		Elxis
* @subpackage	Module Language
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modLanguage', false)) {
	class modLanguage {

		private $style = 0;
		private $langnames = 0;
		private $colour = 0;
		private $elxis_uri = '';
		private $ssl = false;
		private $lang = 'en';
		private $infolangs = array();


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();

			$this->style = (int)$params->get('style', 0);
			if (ELXIS_MOBILE == 1) { $this->style = 1; }
			$this->langnames = (int)$params->get('langnames', 0);
			$this->colour = (int)$params->get('colour', 0);
			$segs = $eURI->getSegments();
			$this->elxis_uri = $eURI->getComponent();
			//if (($this->elxis_uri == $elxis->getConfig('DEFAULT_ROUTE')) || ($this->elxis_uri.':/' == $elxis->getConfig('DEFAULT_ROUTE'))) {
			if ($this->elxis_uri == 'content') {
				$this->elxis_uri = '';
			}

			if ($segs) {
				$this->elxis_uri .= ($this->elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
				$n = count($segs) - 1;
				if (!preg_match('#\.#', $segs[$n])) { $this->elxis_uri .= '/'; }
			} else {
				$this->elxis_uri .= ($this->elxis_uri != '') ? '/' : '';
			}

			$this->lang = $eLang->currentLang();
			$this->ssl = $eURI->detectSSL();
			$this->infolangs = $eLang->getSiteLangs(true);
		}


		/**********************/
		/* EXECUTE THE MODULE */
		/**********************/
		public function run() {
			if (!$this->infolangs) { return; }
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';

			if ($this->style == 1) {
				foreach ($this->infolangs as $lng => $info) {
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$info['NAME'].' - '.$info['NAME_ENG'].'">';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /></a> '."\n";
				}
			} else if ($this->style == 2) {
				foreach ($this->infolangs as $lng => $info) {
					$name = $this->langName($lng, $info);
					$style = 'text-decoration:none;';
					if ($lng == $this->lang) { $style .= ' font-weight:bold;'; }
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$info['NAME'].'" style="'.$style.'">';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" style="border:none; vertical-align:middle;" /> '.$name.'</a> '."\n";
				}
			} else if ($this->style == 3) {
				foreach ($this->infolangs as $lng => $info) {
					$name = $this->langName($lng, $info);
					$styletxt = '';
					if ($lng == $this->lang) { $styletxt = ' style="font-weight:bold;"'; }
					echo '<a href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$name.'"'.$styletxt.'>'.$name.'</a> '."\n";
				}
			} else if ($this->style == 4) {
				$lng = $this->lang;
				$info = $this->infolangs[$lng];
				$name = $this->langName($lng, $info);
				echo '<a href="'.$elxis->makeURL('user:/', '', $this->ssl).'" title="'.$name.' - '.$eLang->get('SELECT_LANG').'" style="text-decoration:none;">'."\n";
				echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" style="border:none; vertical-align:middle;" /> '.$name."</a>\n";
			} else {
				$sfx = $eLang->getinfo('RTLSFX');
				switch ($this->colour) {
					case 1: $clr = 'black'; break;
					case 2: $clr = 'blue'; break;
					case 3: $clr = 'purple'; break;
					case 0: default: $clr = 'gray'; break;
				}
				$cssfile = $elxis->secureBase().'/modules/mod_language/css/modlang_'.$clr.'.css';
				eFactory::getDocument()->addStyleLink($cssfile);

				$info = $this->infolangs[ $this->lang ];
				$name = $this->langName($this->lang, $info);

				echo '<div class="modlang_box">'."\n";
				echo '<div class="modlang_wrappper'.$sfx.'">'."\n";
				echo '<a href="javascript:void(null);" class="modlang_active'.$sfx.'" title="'.$name.'"><img src="'.$flagsdir.$this->lang.'.png" alt="'.$this->infolangs[$this->lang]['NAME_ENG'].'" /> '.$name."</a>\n";
				if (count($this->infolangs) > 1) {
					echo '<ul>'."\n";
					foreach ($this->infolangs as $lng => $info) {
						if ($lng == $this->lang) { continue; }
						$name = $this->langName($lng, $info);
						echo '<li><a hreflang="'.$lng.'" href="'.$elxis->makeURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$name.'">';
						echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /> '.$name.'</a>'."</li>\n";
					}
					echo "</ul>\n";
				}
				echo "</div>\n";
				echo "</div>\n";
			}
		}


		/**********************************/
		/* GET LANGUAGE NAME AS ON PARAMS */
		/**********************************/
		private function langName($lng, $info) {
			switch ($this->langnames) {
				case 1: return $info['NAME_ENG']; break;
				case 2: return $lng; break;
				case 3: return $info['LANGUAGE'].'-'.$info['REGION']; break;
				case 0: default: return $info['NAME']; break;
			}
		}

	}
}


$modlang = new modLanguage($params);
$modlang->run();
unset($modlang);

?>