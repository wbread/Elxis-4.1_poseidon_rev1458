<?php 
/**
* @version		$Id: mod_adminlang.php 1356 2012-11-10 21:35:31Z datahell $
* @package		Elxis
* @subpackage	Module Administration language
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminLang', false)) {
	class modadminLang {

		private $localnames = true;
		private $lock = false;
		private $elxis_uri = '';
		private $infolangs = array();
		private $ssl = false;
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();

			if (!defined('ELXIS_ADMIN')) { return; }

			$this->localnames = (int)$params->get('localnames', 1);
			$this->elxis_uri = $eURI->getComponent();
			if (($this->elxis_uri == 'cpanel') || ($this->elxis_uri.':/' == 'cpanel:/')) {
				$this->elxis_uri = '';
			}

			$segs = $eURI->getSegments();
			$n = count($segs);

			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html'))) { $this->lock = true; }

				$this->elxis_uri .= ($this->elxis_uri == '') ? implode('/', $segs) : ':'.implode('/', $segs);
				if (!preg_match('#\.#', $last_segment)) { $this->elxis_uri .= '/'; }
			} else {
				$this->elxis_uri .= ($this->elxis_uri != '') ? '/' : '';
			}

			$this->infolangs = $eLang->getAllLangs(true);
			$this->ssl = $eURI->detectSSL();
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx_warning">This module is available only in Elxis administratrion area!</div>'."\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }
			if (!$this->infolangs) { return; }

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			$curlang = $eLang->currentLang();
			$cssfile = $elxis->secureBase().'/modules/mod_adminlang/css/adminlang'.$eLang->getinfo('RTLSFX').'.css';
			eFactory::getDocument()->addStyleLink($cssfile);
			$title = ($this->localnames == 1) ? $this->infolangs[$curlang]['NAME'] : $this->infolangs[$curlang]['NAME_ENG'];

			echo '<ul class="admlangul">'."\n";
			echo "<li>\n";
			echo '<a href="javascript:void(null);"><img src="'.$flagsdir.$curlang.'.png" alt="'.$this->infolangs[$curlang]['NAME_ENG'].'" /> '.$title."</a>\n";
			if ($this->lock === false) {
				echo '<ul>'."\n";
				$k = 1;
				foreach ($this->infolangs as $lng => $info) {
					$title = ($this->localnames == 1) ? $info['NAME'] : $info['NAME_ENG'];
					$classtxt = ($k == 1) ? ' class="admlangsep"' : '';
					$classtxt2 = ($lng == $curlang) ? ' class="admlangcur"' : '';
					echo '<li'.$classtxt.'>';
					echo '<a href="'.$elxis->makeAURL($lng.':'.$this->elxis_uri, '', $this->ssl).'" title="'.$lng.' - '.$info['NAME_ENG'].'"'.$classtxt2.'>';
					echo '<img src="'.$flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /> '.$title.'</a>';						
					echo "</li>\n";
					$k = 1 - $k;
				}
				echo "</ul>\n";
			}
			echo "</li>\n";
			echo "</ul>\n";
		}

	}
}


$admlang = new modadminLang($params);
$admlang->run();
unset($admlang);

?>