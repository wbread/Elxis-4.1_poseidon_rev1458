<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class etranslatorController {

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

}

?>