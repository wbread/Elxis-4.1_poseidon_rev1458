<?php 
/**
* @version		$Id: default.html.php 683 2011-10-21 18:38:02Z datahell $
* @package		Elxis
* @subpackage	Component Wrapper
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class wrapperView {


	public function __construct() {
	}


	/************************************/
	/* SHOW IFRAME WITH WRAPPED CONTENT */
	/************************************/
	public function showWrapper($row, $options) {
		$margin = ($row->popup > 0) ? '0' : '0 0 10px 0';
		echo '<div style="margin:'.$margin.'; padding:0;">'."\n";
		echo '<iframe id="'.$options['frameid'].'" name="elxiswrap" src="'.$row->link.'" scrolling="'.$options['scrolling'].'" marginwidth="0" marginheight="0" frameborder="0" width="100%" height="'.$options['height'].'" style="margin:0; padding:0; overflow:visible; width:100%;">'."\n";
		echo 'Your browser does not support iframes!'."\n";
		echo "</iframe>\n";
		echo "</div>\n";
	}

}

?>