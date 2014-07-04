<?php 
/**
* @version		$Id: wrapper.php 683 2011-10-21 18:38:02Z datahell $
* @package		Elxis
* @subpackage	Component Wrapper
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class wrapperRouter extends elxisRouter {

	private $controller = 'default';
	private $task = 'wrap';
	private $mid = 0;


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (defined('ELXIS_ADMIN')) {
			exitPage::make('404', 'CWRA-0005', 'Component Wrapper has no administration interface!');
		}
		$this->makeRoute();
		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');

		$view = new wrapperView();
		$model = new wrapperModel();
		$controller = new wrapperController($view, $model, $this->mid);
		unset($view, $model);
		$controller->wrap();
	}


	/********************/
	/* MAKE/CHECK ROUTE */
	/********************/
	private function makeRoute() {
		if (count($this->segments) <> 1) {
			exitPage::make('404', 'CWRA-0001');
		}
		if (!preg_match('#(\.html)$#', $this->segments[0])) {
			exitPage::make('404', 'CWRA-0002');
		}
		$mid = preg_replace('#(\.html)$#', '', $this->segments[0]);
		if (!is_numeric($mid)) {
			exitPage::make('404', 'CWRA-0003');
		}
		$this->mid = (int)$mid;
		if (($this->mid < 1) || ($mid != $this->mid)) {
			exitPage::make('404', 'CWRA-0004');
		}
	}

}

?>