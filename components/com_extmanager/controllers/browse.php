<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class browseExtmanagerController extends extmanagerController {

	private $edc = null;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
		$this->initEDC();
	}


	/*************************************/
	/* INITIALIZE ELXIS DOWNLOADS CENTER */
	/*************************************/
	private function initEDC() {
		$elxisid = '';
		$edcurl = '';
		$str = $this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		elxisLoader::loadFile('components/com_extmanager/includes/edc.class.php');
		$params = new elxisParameters($str, '', 'component');
		$this->edc = new elxisDC($params);
	}


	/******************************************************/
	/* PREPARE TO DISPLAY EXTENSIONS BROWSER CENTRAL PAGE */
	/******************************************************/
	public function central() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$eDoc->setContentType('text/html'); //force text/html due to iframe
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_extmanager/css/ui'.$eLang->getinfo('RTLSFX').'.css');
		$js = $this->getJSONLang();
		$eDoc->addScript($js);
		$eDoc->loadLightbox();
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_extmanager/js/edc.js');
		$js = '$(document).ready(function() { edcConnect(); });';
		$eDoc->addScript($js);

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('BROWSE'));
		$eDoc->setTitle($eLang->get('EXTENSIONS').' - '.$eLang->get('BROWSE'));

		$this->view->extCentral($this->edc);
	}


	/*************************************/
	/* GET REQUIRED JSON LANGUAGE STRING */
	/*************************************/
	private function getJSONLang() {
		$eLang = eFactory::getLang();
	
		$strings = array('PLEASE_WAIT', 'CONNECTING_EDC', 'LOADING_EDC', 'INSTALL', 'CANCEL', 'UPDATE', 'ELXISDC', 'SYSTEM_WARNINGS', 
		'AREYOUSURE', 'ACTION_WAIT', 'NAMEMAIL_ELXIS', 'INFO_STAY_PRIVE', 'CONTINUE');

		$js = 'var edcLang = {'."\n";
		foreach ($strings as $string) {
			$js .= "\t".'\''.$string.'\':\''.addslashes($eLang->get($string)).'\','."\n";
		}
		$special1 = sprintf($eLang->get('ABOUT_TO_INSTALL'), 'X1', 'X2');
		$special2 = sprintf($eLang->get('ABOUT_TO_UPDATE_TO'), 'X1', 'X2');
		$special3 = sprintf($eLang->get('EXT_INST_SUCCESS'), 'X1', 'X2');
		$js .= "\t".'\'ABOUT_TO_INSTALL\':\''.addslashes($special1).'\','."\n";
		$js .= "\t".'\'ABOUT_TO_UPDATE_TO\':\''.addslashes($special2).'\','."\n";
		$js .= "\t".'\'EXT_INST_SUCCESS\':\''.addslashes($special3).'\''."\n";
		$js .= '};';

		return $js;
	}


	/**********************/
	/* HANDLE EDC REQUEST */
	/**********************/
	public function requestedc() {
		$options = array();
		$options['task'] = trim(filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if ($options['task'] == '') { $options['task'] = 'nothing'; }
		$options['catid'] = isset($_POST['catid']) ? (int)$_POST['catid'] : 0;
		if ($options['catid'] < 0) { $options['catid'] = 0; }
		$options['fid'] = isset($_POST['fid']) ? (int)$_POST['fid'] : 0;
		if ($options['fid'] < 0) { $options['fid'] = 0; }
		$options['id'] = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($options['id'] < 0) { $options['id'] = 0; }
		$options['page'] = isset($_POST['page']) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['edcauth'] = trim(filter_input(INPUT_POST, 'edcauth', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($options['task'] == 'auth') {
			$response = $this->edc->connect();
			$this->view->connectionResult($response);
			return;
		}

		if ($options['task'] == 'frontpage') {
			$lng = eFactory::getLang()->currentLang();
			$response = $this->edc->getFrontpage($lng, $options['edcauth']);
			$this->view->showFrontpage($response, $this->edc);
			return;
		}

		if ($options['task'] == 'filters') {
			$response = $this->edc->getFilters($options);
			$this->view->showFilters($options['catid'], $response);
			return;
		}

		if ($options['task'] == 'category') {
			$response = $this->edc->getCategory($options);
			$this->view->showCategory($response, $this->edc);
			return;
		}

		if ($options['task'] == 'view') {
			$response = $this->edc->getExtension($options);
			$this->view->showExtension($response, $this->edc);
			return;
		}

		if ($options['task'] == 'author') {
			$options['uid'] = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
			$response = $this->edc->getAuthorExtensions($options);
			$this->view->showAuthorExtensions($response, $this->edc);
			return;
		}

		if ($options['task'] == 'rate') {
			$options['rating'] = isset($_POST['rating']) ? (int)$_POST['rating'] : 3;
			if ($options['rating'] < 1) { $options['rating'] = 1; }
			if ($options['rating'] > 5) { $options['rating'] = 5; }
			$response = $this->edc->rateExtension($options);
			$this->view->ratingResult($response);
			return;
		}

		if ($options['task'] == 'report') {
			$options['rcode'] = isset($_POST['rcode']) ? (int)$_POST['rcode'] : 0;
			$response = $this->edc->reportExtension($options);
			$this->view->reportResult($response);
			return;
		}

		if ($options['task'] == 'register') {
			$comparams = $this->model->componentParams();
			$response = $this->edc->registerSite($options, $comparams);
			if (($response['error'] == '') && ($response['newparams'] != '')) {
				$this->model->saveComponentParams($response['newparams']);
			}
			$this->view->registrationResult($response);
			return;
		}

		$this->ajaxHeaders();
		exit();
	}

}
	
?>