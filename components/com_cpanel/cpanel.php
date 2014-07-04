<?php 
/**
* @version		$Id: cpanel.php 1253 2012-08-07 17:54:11Z datahell $
* @package		Elxis
* @subpackage	Component Control Panel
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_cpanel/controllers/common.php');
	elxisLoader::loadFile('components/com_cpanel/views/common.html.php');
}

class cpanelRouter extends elxisRouter {

	private $controller = 'main';
	private $task = 'dashboard';


	/******************************************/
	/* ROUTE REQUEST TO THE PROPER CONTROLLER */
	/******************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) {
			exitPage::make('404', 'CCPA-0001');
		}

		$this->makeRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');

		$class = $this->controller.'CPController';
		$viewclass = $this->controller.'CPView';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CCPA-0002', 'Class '.$class.' not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CCPA-0003', 'Method '.$task.' not found in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$model = new cpanelModel();
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
			$this->task = 'dashboard';
			return;
		}

		if ($this->segments[0] == 'checkftp') {//ajax
			$this->controller = 'utilities';
			$this->task = 'checkftp';
			return;
		}

		if ($this->segments[0] == 'beat') {//ajax
			$this->controller = 'utilities';
			$this->task = 'heartbeat';
			return;
		}

		if ($this->segments[0] == 'banip') {//ajax
			$this->controller = 'utilities';
			$this->task = 'banip';
			return;
		}

		if ($this->segments[0] == 'forcelogout') {//ajax
			$this->controller = 'utilities';
			$this->task = 'forcelogout';
			return;
		}

		if ($this->segments[0] == 'ajax') {//ajax
			$this->controller = 'utilities';
			$this->task = 'genericajax';
			return;
		}

		if ($this->segments[0] == 'logout.html') {
			$this->controller = 'utilities';
			$this->task = 'logout';
			return;
		}

		if ($this->segments[0] == 'config.html') {
			$this->controller = 'main';
			$this->task = 'configure';
			return;
		}

		if ($this->segments[0] == 'saveconfig.html') {
			$this->controller = 'main';
			$this->task = 'saveconfig';
			return;
		}

		if ($this->segments[0] == 'backup') {
			$this->controller = 'utilities';
			if (!isset($this->segments[1])) {
				$this->task = 'listbackup';
				return;
			} else if ($this->segments[1] == 'getbackups.xml') {
				$this->task = 'getbackups';
				return;
			} else if ($this->segments[1] == 'download') {
				$this->task = 'downbackup';
				return;
			} else if ($this->segments[1] == 'delbackup') {
				$this->task = 'deletebackup';
				return;
			} else if ($this->segments[1] == 'makebackup') {
				$this->task = 'makebackup';
				return;
			} else {
				exitPage::make('404', 'CCPA-0004');
			}
		}

		if ($this->segments[0] == 'routing') {
			$this->controller = 'utilities';
			if (!isset($this->segments[1])) {
				$this->task = 'listroutes';
				return;
			} else if ($this->segments[1] == 'getrouting.xml') {
				$this->task = 'getrouting';
				return;
			} else if ($this->segments[1] == 'edit.html') {
				$this->task = 'editroute';
				return;
			} else if ($this->segments[1] == 'save') {
				$this->task = 'saveroute';
				return;
			} else if ($this->segments[1] == 'delete') {
				$this->task = 'deleteroute';
				return;
			} else {
				exitPage::make('404', 'CCPA-0005');
			}
		}

		if ($this->segments[0] == 'logs') {
			$this->controller = 'utilities';
			if (!isset($this->segments[1])) {
				$this->task = 'listlogs';
				return;
			} else if ($this->segments[1] == 'getlogs.xml') {
				$this->task = 'getlogs';
				return;
			} else if ($this->segments[1] == 'view.html') {
				$this->task = 'viewlog';
				return;		
			} else if ($this->segments[1] == 'clear') {
				$this->task = 'clearlog';
				return;
			} else if ($this->segments[1] == 'delete') {
				$this->task = 'deletelog';
				return;
			} else if ($this->segments[1] == 'download') {
				$this->task = 'downloadlog';
				return;
			} else {
				exitPage::make('404', 'CCPA-0006');
			}
		}

		if ($this->segments[0] == 'cache') {
			$this->controller = 'utilities';
			if (!isset($this->segments[1])) {
				$this->task = 'listcache';
				return;
			} else if ($this->segments[1] == 'getcache.xml') {
				$this->task = 'getcache';
				return;
			} else if ($this->segments[1] == 'delcache') {
				$this->task = 'deletecache';
				return;
			} else {
				exitPage::make('404', 'CCPA-0010');
			}
		}

		if ($this->segments[0] == 'sys') {
			$this->controller = 'system';
			if (!isset($this->segments[1])) {
				$this->task = 'elxisinfo';
				return;
			} else if ($this->segments[1] == 'elxis.html') {
				$this->task = 'elxisinfo';
				return;
			} else if ($this->segments[1] == 'php.html') {
				$this->task = 'phpinformation';
				return;
			} else {
				exitPage::make('404', 'CCPA-0007');
			}
		}

		if ($this->segments[0] == 'multisites') {
			$this->controller = 'multisites';
			if (!isset($this->segments[1])) {
				$this->task = 'listsites';
				return;
			} else if ($this->segments[1] == 'enable.html') {
				$this->task = 'enablemultiple';
				return;
			} else if ($this->segments[1] == 'disable.html') {
				$this->task = 'disablemultiple';
				return;
			} else if ($this->segments[1] == 'edit.html') {
				$this->task = 'editmultisite';
				return;
			} else if ($this->segments[1] == 'save.html') {
				$this->task = 'savemultisite';
				return;
			} else if ($this->segments[1] == 'delete.html') {
				$this->task = 'deletemultisite';
				return;
			} else {
				exitPage::make('404', 'CCPA-0009');
			}
		}

		if ($this->segments[0] == 'stats') {
			$this->controller = 'statistics';
			$this->task = 'showstats';
			return;
		}

		exitPage::make('404', 'CCPA-0008');
	}

}

?>