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


class connectorMediaView extends emediaView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************/
	/* ERROR JSON RESPONSE */
	/***********************/
	public function errorResponse($errormsg) {
    	$properties = array('date_created' => null, 'date_modified' => null, 'height' => null, 'width' => null, 'size' => null);
    	$response = array('error' => $errormsg, 'code' => '-1', 'properties' => $properties);

		$this->pageHeaders('application/json');
		echo json_encode($response);
		exit();
	}


	/*************************/
	/* SUCCESS JSON RESPONSE */
	/*************************/
	public function jsonResponse($response) {
		$this->pageHeaders('application/json');
		echo json_encode($response);
		exit();
	}

}

?>