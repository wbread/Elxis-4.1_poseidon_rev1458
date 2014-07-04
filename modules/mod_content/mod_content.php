<?php 
/**
* @version		$Id: mod_content.php 1326 2012-10-13 20:12:28Z datahell $
* @package		Elxis
* @subpackage	Module Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$run_plugins = (int)$params->get('plugins', 0);
if ($run_plugins == 1) {
	$row = new stdClass;
	$row->id = $elxmod->id;
	$row->title = $elxmod->title;
	$row->module = $elxmod->module;
	$row->text = $elxmod->content;

	$ePlugin = eFactory::getPlugin();
	$ePlugin->process($row);
	echo $row->text;
} else {
	echo $elxmod->content;
}

?>