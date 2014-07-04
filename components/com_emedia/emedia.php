<?php 
/**
* @version		$Id: emedia.php 1382 2013-01-30 20:08:17Z datahell $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_emedia/controllers/common.php');
	elxisLoader::loadFile('components/com_emedia/views/common.html.php');
}

class emediaRouter extends elxisRouter{
    
	private $controller = 'full';
	private $task = 'manage';


	/******************************************/
	/* ROUTE REQUEST TO THE PROPER CONTROLLER */
	/******************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) {
			exitPage::make('404', 'CMED-0001');
		}

		$this->makeRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		$class = $this->controller.'MediaControl';
		$viewclass = $this->controller.'MediaView';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CMED-0002', 'Class '.$class.' not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CMED-0003', 'Method '.$task.' not found in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$controller = new $class($view);
		unset($view);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeRoute() {
		$n = count($this->segments);
		if ($n == 0) {
			if (ELXIS_INNER == 1) {
				$this->controller = 'editor';
				$this->task = 'editorui';
			} else {
				$this->controller = 'full';
				$this->task = 'fullui';				
			}
			return;
		}

		if ($this->segments[0] == 'config') {
			$this->controller = 'full';
			$this->task = 'configure';
			return;
		}

		if ($this->segments[0] == 'editor') {
			$this->controller = 'editor';
			if (!isset($this->segments[1])) {
				$this->task = 'editorui';
				return;
			} else {
				exitPage::make('404', 'CMED-0004');
			}
		}

		if ($this->segments[0] == 'connect') {
			$this->controller = 'connector';
			$this->task = 'connect';
			return;
		}

		exitPage::make('404', 'CMED-0005');
	}

}

?>