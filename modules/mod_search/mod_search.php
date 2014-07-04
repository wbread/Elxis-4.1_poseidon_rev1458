<?php 
/**
* @version		$Id: mod_search.php 1395 2013-02-24 10:42:44Z datahell $
* @package		Elxis
* @subpackage	Module search
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('moduleSearch', false)) {
	class moduleSearch {

		private $width = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->width = (int)$params->get('width', 0);
		}


		/**************/
		/* RUN MODULE */
		/**************/
		public function run() {
			if (ELXIS_MOBILE == 1) {
				$this->mobileSearch();
				return;
			}
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();
			$eSearch = eFactory::getSearch();

			$engines = $eSearch->getEngines();
			if (count($engines) == 0) { return; }
			$current = $eSearch->getCurrentEngine();
			eFactory::getDocument()->addScriptLink($eURI->secureBase().'/modules/mod_search/search.js');

			$icon = $eURI->secureBase().'/components/com_search/engines/'.$current.'/'.$current.'.png';
			$isssl = $eURI->detectSSL();
			$baseaction = $eURI->makeURL('search:/', '', $isssl);
			$extra = ($this->width > 0) ? ' style="width:'.$this->width.'px;"' : '';
			echo '<form name="fmmodsearch" id="fmmodsearch" class="elx_modsearchform" action="'.$baseaction.$current.'.html" method="get">'."\n";
			echo '<ul class="elx_modsearch_box">'."\n";
			echo "<li>\n";
			echo '<a href="javascript:void(null);" style="width:20px;" title="'.$eLang->get('SEARCH').'">'."\n";
			echo '<img src="'.$icon.'" id="msearch_icon" width="16" height="16" alt="searchin" />'."\n";
			echo "</a>\n";
			if (count($engines) > 1) {
				echo '<ul class="elx_modsearch_list">'."\n";
				foreach ($engines as $name => $engine) {
					$img = $eURI->secureBase().'/components/com_search/engines/'.$name.'/'.$name.'.png';
					echo '<li><a href="javascript:void(null);" onclick="msearch_pick(\''.$name.'\');" title="'.$eLang->get('SEARCHIN').' '.$engine['title'].'">';
					echo '<img src="'.$img.'" width="16" height="16" alt="'.$name.'" /> ';
					echo $engine['title']."</a></li>\n";
				}
				echo "</ul>\n";
			}
			echo "</li>\n";
			echo "</ul>\n";
			echo '<input type="text" name="q" id="msearchq" size="20" class="elx_modsearch_input"'.$extra.' value="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" onfocus="msearch_clear(1);" onblur="msearch_clear(0);" />'."\n";
			echo "</form>\n";
			echo '<span id="msearch_abase" style="display:none; visibility:hidden;">'.$baseaction."</span>\n";
			echo '<span id="msearch_ubase" style="display:none; visibility:hidden;">'.$eURI->secureBase()."</span>\n";
			echo '<span id="msearch_sear" style="display:none; visibility:hidden;">'.$eLang->get('SEARCH')."...</span>\n";
		}


		/***************************/
		/* MOBILE-FRIENDLY VERSION */
		/***************************/
		public function mobileSearch() {
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();
			$eSearch = eFactory::getSearch();

			$engines = $eSearch->getEngines();
			if (count($engines) == 0) { return; }
			$current = $eSearch->getDefaultEngine();
			$isssl = $eURI->detectSSL();
			$baseaction = $eURI->makeURL('search:/', '', $isssl);

			echo '<form name="fmmodsearch" id="fmmodsearch" class="elx_modsearchform" action="'.$baseaction.$current.'.html" method="get">'."\n";
			echo '<div class="elx_modsearch_mobobox">';
			echo '<button type="submit" name="searchbtn" class="elx_modsearch_mobbtn">Go</button>'."\n";
			echo '<div class="elx_modsearch_mobibox">';
			echo '<input type="text" name="q" id="msearchq" size="20" class="elx_modsearch_mobin" value="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" onfocus="if (this.value == \''.$eLang->get('SEARCH').'...\') { this.value = \'\'; }" />';
			echo "</div>\n";
			echo "</div>\n";
			echo "</form>\n";
		}


	}
}

$modsearch = new moduleSearch($params);
$modsearch->run();
unset($modsearch);

?>