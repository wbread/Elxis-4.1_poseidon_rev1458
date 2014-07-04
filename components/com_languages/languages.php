<?php 
/**
* @version		$Id: languages.php 584 2011-09-04 16:17:02Z datahell $
* @package		Elxis
* @subpackage	Component Languages
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_languages/controllers/common.php');
	elxisLoader::loadFile('components/com_languages/views/common.html.php');
}

class languagesRouter extends elxisRouter {

	private $controller = 'main';
	private $task = 'listlangs';


	/******************************************/
	/* ROUTE REQUEST TO THE PROPER CONTROLLER */
	/******************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) {
			exitPage::make('404', 'CLNG-0001');
		}
		$this->makeRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');

		$class = $this->controller.'LngController';
		$viewclass = $this->controller.'LngView';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CLNG-0002', 'Class '.$class.' not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CLNG-0003', 'Method '.$task.' not found in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$model = new languagesModel();
		$controller = new $class($view, $model);
		unset($view);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeRoute() {
		$n = count($this->segments);
		if ($n == 0) {
			$this->controller = 'main';
			$this->task = 'listlangs';
			return;
		}
		if ($this->segments[0] == 'compare.html') {
			$this->controller = 'main';
			$this->task = 'compare';
			return;
		}
		if ($this->segments[0] == 'check.html') {
			$this->controller = 'main';
			$this->task = 'check';
			return;
		}
		if ($this->segments[0] == 'view.html') { //highslide popup
			$this->controller = 'main';
			$this->task = 'viewfile';
			return;
		}
		if ($this->segments[0] == 'getlangs.xml') {//ajax
			$this->controller = 'main';
			$this->task = 'getlangs';
			return;
		}

		exitPage::make('404', 'CLNG-0004');
	}

}

?>