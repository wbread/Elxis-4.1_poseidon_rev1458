<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisFilterHelper {


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}
	

	/*********************************************/
	/* REPLACE & WITH &amp; FOR XHTML COMPLIANCE */
	/*********************************************/
	public function ampReplace($string) {
		$string = str_replace('&amp;', '&', $string);
		$string = str_replace('&', '&amp;', $string);
		return $string;
	}

}

?>