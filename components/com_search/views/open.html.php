<?php 
/**
* @version		$Id: main.html.php 296 2011-04-21 11:21:43Z datahell $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class openSearchView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/***********************************/
	/* SHOW OPENSEARCH DESCRIPTION XML */
	/***********************************/
	public function showDescription($buffer) {
		if (@ob_get_length() > 0) { ob_end_clean(); }
		header('Content-type: application/opensearchdescription+xml; charset=utf-8');
		echo $buffer;
 		exit();
	}
		
}

?>