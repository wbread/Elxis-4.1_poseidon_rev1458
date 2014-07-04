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


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_etranslator/controllers/base.php');
	elxisLoader::loadFile('components/com_etranslator/views/base.html.php');
}


class etranslatorRouter extends elxisRouter {

	private $controller = 'single';
	private $task = '';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) { exitPage::make('404', 'CETR-0001'); }

		$this->makeAdminRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CETR-0002', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CETR-0003', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new etranslatorModel();
		$controller = new $class($view, $task, $model);
		unset($view);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeAdminRoute() {
		$c = count($this->segments);
		if ($c == 0) {
			$this->controller = 'single';
			$this->task = 'listtrans';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'single') {
				$this->controller = 'single';
				$this->task = 'listtrans';
				return;
			} else if ($this->segments[0] == 'api') {
				$this->controller = 'api';
				$this->task = 'badrequest';
				return;
			} else {
				exitPage::make('404', 'CEXT-0005');
			}
		}

		if ($c == 2) {
			if ($this->segments[0] == 'single') {
				$this->controller = 'single';
				if ($this->segments[1] == 'gettrans.xml') {
					$this->task = 'gettrans';
					return;
				} else if ($this->segments[1] == 'add.html') {
					$this->task = 'addtrans';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'edittrans';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deletetrans';
					return;
				} else {
					exitPage::make('404', 'CEXT-0006');
				}
			}

			if ($this->segments[0] == 'api') {
				$this->controller = 'api';
				if ($this->segments[1] == 'load') {
					$this->task = 'loadtranslation';
					return;
				} else if ($this->segments[1] == 'save') {
					$this->task = 'savetranslation';
					return;
				} else if ($this->segments[1] == 'tsave') {
					$this->task = 'savettranslation';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deletetranslation';
					return;
				} else {
					$this->task = 'badrequest';
					return;
				}
			}
		}

		exitPage::make('404', 'CETR-0004');
	}

}

?>