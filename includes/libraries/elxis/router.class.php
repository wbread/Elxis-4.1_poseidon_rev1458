<?php 
/**
* @version		$Id: router.class.php 893 2012-02-05 18:26:23Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


abstract class elxisRouter {

	protected $query_string = '';
	protected $component = '';
	//private $uri_lang = ''; //requested language (empty for default)
	//private $route = ''; //URI first segment (can be empty, file, or directory)
	//private $component = 'content'; //component routed from the first segment of the URI
	protected $segments = array(); //Parsed URI segments without the route/component and the query string


	public function __construct() {
		$eURI = eFactory::getURI();
		$this->component = $eURI->getComponent();
		$this->segments = $eURI->getSegments();
	}

	abstract protected function route();

}

?>