<?php 
/**
* @version		$Id: controller.class.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


abstract class elxisController {

	protected $registry;

	public function __construct($registry) {
        $this->registry = $registry;
	}


	/* all controllers must contain an index method */
	abstract function index();
}

?>