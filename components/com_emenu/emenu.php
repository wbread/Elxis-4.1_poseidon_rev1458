<?php 
/**
* @version		$Id: emenu.php 857 2012-01-21 17:11:04Z datahell $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_emenu/controllers/base.php');
elxisLoader::loadFile('components/com_emenu/views/base.html.php');


class emenuRouter extends elxisRouter {

	private $controller = 'collection';
	private $task = 'listcollections';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) { exitPage::make('404', 'CEME-0001'); }

		$this->makeAdminRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CEME-0002', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CEME-0003', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new emenuModel();
		$controller = new $class($view, $task, $model);
		unset($view);
		$controller->$task();
	}


	/***********************/
	/* MAKE FRONTEND ROUTE */
	/***********************/
	private function makeAdminRoute() {
		$c = count($this->segments);
		if ($c == 0) {
			$this->task = 'listcollections';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'getcollections.xml') {
				$this->task = 'getcollections';
				return;
			} else if ($this->segments[0] == 'addcol.html') { //hs iframe
				$this->task = 'addcollection';
				return;
			} else if ($this->segments[0] == 'deletecol') { //ajax
				$this->task = 'deletecollection';
				return;
			}
		}

		if (($c == 2) && ($this->segments[0] == 'mitems')) {
			$this->controller = 'menuitem';
			if ($this->segments[1] == 'getitems.xml') { //ajax
				$this->task = 'getitems';
				return;
			} else if ($this->segments[1] == 'move') { //ajax
				$this->task = 'moveitem';
				return;
			} else if ($this->segments[1] == 'delete') { //ajax
				$this->task = 'deleteitem';
				return;
			} else if ($this->segments[1] == 'publish') { //ajax
				$this->task = 'publishitem';
				return;
			} else if ($this->segments[1] == 'add.html') {
				$this->task = 'additem';
				return;
			} else if ($this->segments[1] == 'edit.html') {
				$this->task = 'edititem';
				return;
			} else if ($this->segments[1] == 'save.html') {
				$this->task = 'saveitem';
				return;
			} else if ($this->segments[1] == 'generator.html') { //ajax
				$this->task = 'linkgenerator';
				return;
			} else if ($this->segments[1] == 'browser.html') {
				$this->task = 'browser';
				return;
			} else {
				$this->task = 'listmenuitems';
				return;
			}
		}

		exitPage::make('404', 'CEME-0004');
	}

}

?>