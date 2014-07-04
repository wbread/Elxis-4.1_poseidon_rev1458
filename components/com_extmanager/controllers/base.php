<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class extmanagerController {

	protected $view = null;
	protected $model = null;


	protected function __construct($view=null, $task='', $model=null) {
		$this->view = $view;
		$this->model = $model;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/*************************/
	/* TRANSLATE GROUP NAMES */
	/*************************/
	protected function translateGroupNames($rows) {
		if (!$rows) { return $rows; }
		$eLang = eFactory::getLang();
		foreach ($rows as $i => $row) {
			switch ($row['gid']) {
				case 1: $rows[$i]['groupname'] = $eLang->get('ADMINISTRATOR'); break;
				case 5: $rows[$i]['groupname'] = $eLang->get('USER'); break;
				case 6: $rows[$i]['groupname'] = $eLang->get('EXTERNALUSER'); break;
				case 7: $rows[$i]['groupname'] = $eLang->get('GUEST'); break;
				default: break;
			}
		}
		return $rows;
	}


	/******************/
	/* GET GROUP NAME */
	/******************/
	protected function getGroupName($gid, $allgroups=array()) {
		if ($gid == 1) {
			return eFactory::getLang()->get('ADMINISTRATOR');
		} else if ($gid == 5) {
			return eFactory::getLang()->get('USER');
		} else if ($gid == 6) {
			return eFactory::getLang()->get('EXTERNALUSER');
		} else if ($gid == 7) {
			return eFactory::getLang()->get('GUEST');
		} else {
			if ($allgroups) {
				foreach ($allgroups as $grp) {
					if ($grp['gid'] == $gid) { return $grp['groupname']; }
				}
			}
		}

		return '';
	}

}

?>