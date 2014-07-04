<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class systemCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/*************************************/
	/* PREPARE TO SHOW ELXIS INFORMATION */
	/*************************************/
	public function elxisinfo() {
		$pathway = eFactory::getPathway();
		$eLang = eFactory::getLang();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('SYSTEM'), 'cpanel:sys/');
		$pathway->addNode($eLang->get('ELXIS_INFO'));

		eFactory::getDocument()->setTitle($eLang->get('ELXIS_INFO').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->elxisInformation();
	}


	/***********************************/
	/* PREPARE TO SHOW PHP INFORMATION */
	/***********************************/
	public function phpinformation() {
		$pathway = eFactory::getPathway();
		$eLang = eFactory::getLang();

		$phpinfo = $this->getPHPInfo();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('SYSTEM'), 'cpanel:sys/');
		$pathway->addNode($eLang->get('PHP_INFO'));

		eFactory::getDocument()->setTitle($eLang->get('PHP_INFO').' - '.$eLang->get('ADMINISTRATION'));
		eFactory::getDocument()->setContentType('text/html'); //avoid XHTML invalid entities

		$this->view->phpInformation($phpinfo);
	}


	/********************************/
	/* GET PHP INFORMATION AS ARRAY */
	/********************************/
	private function getPHPInfo() {
		if (!function_exists('phpinfo') || !is_callable('phpinfo')) {return array(); }
		ob_start();
		phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
		$info = array();
		$info_lines = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
		$cat = 'General';
		$old_cat = 'General';
		$info[$cat]['tblcolumns'] = 2;
		foreach($info_lines as $line) {
        	preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
        	if ($cat != $old_cat) {
        		$info[$cat]['tblcolumns'] = 2;
        		$old_cat = $cat;
       		}
        	if (preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
            	$info[$cat][$val[1]] = $val[2];
			} elseif(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val)) {
				$info[$cat][$val[1]] = array('local' => $val[2], 'master' => $val[3]);
				$info[$cat]['tblcolumns'] = 3;
			}
		}
		return $info;
	}

}

?>