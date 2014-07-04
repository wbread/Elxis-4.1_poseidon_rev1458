<?php 
/**
* @version		$Id: template.class.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTemplate {

	/*
	template name
	template dir
	template file
	*/
	private $template = '';
	
	private $tpl_index_path = '';
	private $tpl_error_path = '';
	private $tpl_error404_path = '';
	private $tpl_offline_path = '';
	private $tpl_login_path = '';
	private $tpl_mini_path = '';
	
	private $backend = false; //admin template
	private $params; //template parameters
	private $vars = array();


	/*************************************/
	/* BACKEND/FRONTEND DEFAULT TEMPLATE */
	/*************************************/
	public function __construct() {
		$elxis = eFactory::getElxis();
		if (defined('ELXIS_ADMIN')) {
			$this->backend = true;
			$this->template = $elxis->getConfig('ATEMPLATE');
		} else {
			$this->template = $elxis->getConfig('TEMPLATE');
			if (!file_exists(ELXIS_PATH.'/templates/'.$this->template.'/index.php')) {
				trigger_error('Template '.$this->template.' not found!', E_USER_ERROR);
			}
			//setFrontTemplate();
		}
	}


	private function setFrontTemplate() {
		if (defined('ELXIS_ADMIN')) {
			//$this->template = 
		} else {
			switch ($type) {
				
			}
		}

		//FRONT
		//index
		//offline
		//error404
		//error
		//mobile devices (minimal)
		
		//BACK
		//index
		//login
	}

	/* set undefined vars */
	public function __set($index, $value) {
		$this->vars[$index] = $value;
	}


	public function show($view='') {
		if ($view == '') {
			$view = 'index';
		}

		$path = ELXIS_PATH.'/templates/'.$this->template.'/index.php';
		if (file_exists($path) == false) {
			trigger_error('Template '.$this->template.' not found!', E_USER_ERROR);
		}

		// Load variables
		foreach ($this->vars as $key => $value) {
			$$key = $value;
		}
		include ($path);
	}
}

?>