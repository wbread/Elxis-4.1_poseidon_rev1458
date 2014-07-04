<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module search
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modOpenSearch', false)) {
	class modOpenSearch {

		private $style = 0;
		private $custom_image = '';


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->style = (int)$params->get('style', 0);
			$this->custom_image = trim($params->get('custom_image', ''));
		}


		/**************/
		/* RUN MODULE */
		/**************/
		public function run() {
			$eLang = eFactory::getLang();
			$eURI = eFactory::getURI();

			$engines = eFactory::getSearch()->getEngines();
			if (count($engines) == 0) { return; }

			if ($this->style == 3) {
				$imageok = false;
				if (($this->custom_image != '') && is_file(ELXIS_PATH.'/'.$this->custom_image)) {
					$extension = strtolower(substr(strrchr($this->custom_image, '.'), 1));
					if (in_array($extension, array('png', 'jpg', 'jpeg', 'gif'))) { $imageok = true; }
				}
				if (!$imageok) { $this->style = 1; }
			}

			$osd = $eURI->makeURL('search:osdescription.xml', 'inner.php');
			eFactory::getDocument()->addScriptLink($eURI->secureBase().'/components/com_search/extra/addengine.js');

			echo '<div style="text-align:center;">'."\n";
			switch ($this->style) {
				case 1:
					echo '<a href="javascript:void(null);" onclick="installSearchEngine(\''.$osd.'\');" title="'.$eLang->get('ADD_ENGINE_BROWSER').'">';
					echo '<img src="'.$eURI->secureBase().'/components/com_search/extra/browsers.png" border="0" alt="open search" />'."</a>\n";
				break;
				case 2:
					echo '<a href="javascript:void(null);" onclick="installSearchEngine(\''.$osd.'\');" title="'.$eLang->get('ADD_TO_BROWSER').'">';
					echo $eLang->get('ADD_ENGINE_BROWSER')."</a>\n";
				break;
				case 3:
					echo '<a href="javascript:void(null);" onclick="installSearchEngine(\''.$osd.'\');" title="'.$eLang->get('ADD_ENGINE_BROWSER').'">';
					echo '<img src="'.$eURI->secureBase().'/'.$this->custom_image.'" border="0" alt="open search" />'."</a>\n";
				break;
				case 0: default:
					echo '<a href="javascript:void(null);" onclick="installSearchEngine(\''.$osd.'\');" title="'.$eLang->get('ADD_ENGINE_BROWSER').'" style="text-decoration:none;">';
					echo '<img src="'.$eURI->secureBase().'/components/com_search/extra/browsers.png" border="0" alt="open search" /><br />'."\n";
					echo $eLang->get('ADD_TO_BROWSER')."</a>\n";
				break;
			}
			echo "</div>\n";
		}

	}
}


$modosearch = new modOpenSearch($params);
$modosearch->run();
unset($modosearch);

?>