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


class apiEtranslatorView extends etranslatorView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/******************/
	/* SHOW API ERROR */
	/******************/
	public function apiError($errormsg) {
		if ($errormsg == '') { $errormsg = 'Action failed! Unknown error.'; }
		$this->ajaxHeaders('application/json');
		$response = array('error' => 1, 'errormsg' => addslashes($errormsg));
		echo json_encode($response);
		exit();
	}


	/**************************/
	/* API LOAD JSON RESPONSE */
	/**************************/
	public function loadResponse($options, $row) {
		if (!$row) {
			$trid = 0;
			$translation = '';
		} else {
			$trid = (int)$row['trid'];
			$translation = $row['translation'];// addslashes ?
		}

		$response = array (
			'error' => 0,
			'errormsg' => '',
			'trid' => $trid,
			'translation' => $translation
		);

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit();
	}


	/**************************/
	/* API SAVE JSON RESPONSE */
	/**************************/
	public function saveResponse($trid, $message) {
		$response = array (
			'error' => 0,
			'errormsg' => '',
			'trid' => $trid,
			'successmsg' => $message
		);

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit();
	}


	/****************************/
	/* API DELETE JSON RESPONSE */
	/****************************/
	public function deleteResponse($message) {
		$response = array (
			'error' => 0,
			'errormsg' => '',
			'trid' => 0,
			'successmsg' => $message
		);

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit();
	}

}

?>