<?php 
/**
* @version		$Id: plugin.interface.php 1079 2012-04-28 19:58:40Z datahell $
* @package		Elxis
* @subpackage	Component content / Plugins
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


interface contentPlugin {

	public function __construct();
	public function process(&$row, $published, $params);
	public function syntax();
	public function tabs();
	public function helper($pluginid, $tabidx, $fn);
	public function head();
	public function handler($pluginid, $fn);
}

?>