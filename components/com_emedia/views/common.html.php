<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class emediaView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/     
	protected function __construct() {
	}


	/****************************/
	/* SHOW FATAL ERROR MESSAGE */
	/****************************/
	protected function fatalError($message) {
		echo '<h3>'.eFactory::getLang()->get('ERROR')."</h3>\n";
		echo '<div class="elx_error">'.$message."</div>";
	}


	/******************************************/
	/* ECHO PAGE HEADERS FOR SPECIAL REQUESTS */
	/******************************************/
	protected function pageHeaders($type='text/html') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}

}

?>