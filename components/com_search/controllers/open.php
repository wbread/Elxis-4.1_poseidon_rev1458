<?php 
/**
* @version		$Id: open.php 1280 2012-09-11 15:50:20Z datahell $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class openSearchController {

	private $view = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view) {
		$this->view = $view;
	}


	/************************************/
	/* OPEN SEARCH DESCRIPTION DOCUMENT */
	/************************************/
	public function osDescription($nothing='') {
		$eFiles = eFactory::getFiles();

		$lng = eFactory::getLang()->currentLang();
		$file = 'osdescription_'.$lng.'.xml';
		$cache_dir = $eFiles->elxisPath('cache/', true);

		if (file_exists($cache_dir.$file)) {
			$ts = filemtime($cache_dir.$file);
			if ((time() - $ts) < 86400) { //1 day
				if (@ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: application/opensearchdescription+xml; charset=utf-8');
				include($cache_dir.$file);
				exit();
			}
		}

		$elxis = eFactory::getElxis();
		$eSearch = eFactory::getSearch();

		$engine = trim($eSearch->getDefaultEngine());
		if ($engine == '') { die('Engine not found!'); }

		$os_title = '';
		$os_suggest = 1;

		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = ".$db->quote('com_search');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		$params_str = (string)$stmt->fetchResult();
		if ($params_str != '') {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
			$os_title = eUTF::trim($params->get('os_title', ''));
			$os_suggest = (int)$params->get('os_suggest', 1);
			unset($params);
		}

		if ($engine == $eSearch->getCurrentEngine()) {
			$info = $eSearch->engineInfo();
			$description = $info['description'];
		} else {
			$engines = $eSearch->getEngines();
			if (!isset($engines[$engine])) { die('Engine not found!'); }
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($engines[$engine]['params'], '', 'engine');
			elxisLoader::loadFile('components/com_search/engines/'.$engine.'/'.$engine.'.engine.php');
			$class = $engine.'Engine';
			$openEngine = new $class($params);
			$info = $openEngine->engineInfo();
			$description = $info['description'];
		}

		$osdfile = $elxis->makeURL('search:osdescription.xml', 'inner.php');

		$row = new stdClass;
		$row->title = ($os_title != '') ? $os_title : $elxis->getConfig('SITENAME');
		$row->description = $description;
		$row->icon = eFactory::getDocument()->getFavicon();
		$row->icontype = $eFiles->getMimetype($row->icon);
		$row->template = $elxis->makeURL('search:'.$engine.'.html').'?q={searchTerms}';
		if (($os_suggest == 1) && (file_exists(ELXIS_PATH.'/components/com_search/engines/'.$engine.'/'.$engine.'.suggest.php'))) {
			$row->suggestions = $elxis->secureBase().'/components/com_search/engines/'.$engine.'/'.$engine.'.suggest.php?q={searchTerms}&amp;lang='.$lng;
		} else {
			$row->suggestions = '';
		}

		/* $buffer = '<?xml version="1.0" encoding="UTF-8"?>'."\n"; */
		$buffer = '<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">'."\n";
		$buffer .= "\t<ShortName>".$row->title."</ShortName>\n";
		$buffer .= "\t<Description>".$row->description."</Description>\n";
		$buffer .= "\t<Tags>".$row->title." elxis cms opensearch</Tags>\n";
		$buffer .= "\t".'<Url type="text/html" template="'.$row->template.'" />'."\n";
		if ($row->suggestions != '') {
			$buffer .= "\t".'<Url type="application/x-suggestions+json" template="'.$row->suggestions.'" rel="suggestions" />'."\n";
		}
		$buffer .= "\t".'<Url type="application/opensearchdescription+xml" template="'.$osdfile.'" rel="self" />'."\n";
		$buffer .= "\t".'<Image height="16" width="16" type="'.$row->icontype.'">'.$row->icon."</Image>\n";
		$buffer .= "\t<Developer>elxis.org Elxis Team - Ioannis Sannos</Developer>\n";
		$buffer .= "\t".'<Attribution>Copyright 2006-'.date('Y').' elxis.org, All Rights Reserved</Attribution>'."\n";
		$buffer .= "\t<SyndicationRight>open</SyndicationRight>\n";
		$buffer .= "\t<AdultContent>false</AdultContent>\n";
		$buffer .= "\t<Language>*</Language>\n";
 		$buffer .= "\t<InputEncoding>UTF-8</InputEncoding>\n";
		$buffer .= "\t<OutputEncoding>UTF-8</OutputEncoding>\n";
 		$buffer .= '</OpenSearchDescription>';

		$eFiles->createFile('cache/'.$file, $buffer, true, true);

		$this->view->showDescription($buffer);
	}

}

?>