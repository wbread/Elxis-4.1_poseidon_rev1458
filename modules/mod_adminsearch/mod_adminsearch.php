<?php 
/**
* @version		$Id: mod_adminsearch.php 879 2012-01-28 18:37:34Z datahell $
* @package		Elxis
* @subpackage	Module Administration search
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminSearch', false)) {
	class modadminSearch {

		private $disabled = false;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$segs = eFactory::getURI()->getSegments();
			$n = count($segs);
			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html'))) { $this->disabled = true; }
			}
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

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$cssfile = $elxis->secureBase().'/modules/mod_adminsearch/css/adminsearch'.$eLang->getinfo('RTLSFX').'.css';
			eFactory::getDocument()->addStyleLink($cssfile);

			$action = $elxis->makeAURL('content:articles/');
			echo '<form class="asearch_fm" action="'.$action.'" method="get" name="asearchfm">'."\n";
			echo "<fieldset>\n";
			if ($this->disabled === true) {
				echo '<input type="text" name="q" value="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" class="asearch_input_dis" disabled="disabled" />'."\n";
			} else {
				echo '<input type="text" name="q" value="'.$eLang->get('SEARCH').'..." dir="'.$eLang->getinfo('DIR').'" class="asearch_input" onclick="javascript:this.value=\'\';" />'."\n";
			}
			echo '<input type="submit" name="s" value="submit" style="display:none;" />'."\n";
            echo "</fieldset>\n";
	   		echo "</form>\n";
		}

	}
}


$asearch = new modadminSearch($params);
$asearch->run();
unset($asearch);

?>