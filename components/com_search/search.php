<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class searchRouter extends elxisRouter {

	private $controller = 'main';
	private $task = 'run';
	private $engine = '';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (defined('ELXIS_ADMIN')) {
			$this->makeAdminRoute();
		} else {
			$this->makeRoute();
		}
		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CSEA-0001', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CSEA-0002', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$controller = new $class($view);
		unset($view);
		if (defined('ELXIS_ADMIN')) {
			$controller->$task();
		} else {
			$controller->$task( $this->engine );
		}
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeRoute() {
		$c = count($this->segments);
		if ($c == 0) { return; }
		if ($c > 1) {
			exitPage::make('404', 'CSEA-0003');
		}

		if ($this->segments[0] == 'osdescription.xml') {
			$this->controller = 'open';
			$this->task = 'osDescription';
			return;
		}

		$eng = strtolower(str_ireplace('.html', '', $this->segments[0]));
		$eng = filter_var($eng, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		if ($eng != '') { $this->engine = $eng; }
	}


	/********************/
	/* MAKE ADMIN ROUTE */
	/********************/
	private function makeAdminRoute() {
		$this->controller = 'asearch';
		$this->task = 'listengines';
	}

}

?>