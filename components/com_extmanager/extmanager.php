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


if (defined('ELXIS_ADMIN')) {
	elxisLoader::loadFile('components/com_extmanager/controllers/base.php');
	elxisLoader::loadFile('components/com_extmanager/views/base.html.php');
}


class extmanagerRouter extends elxisRouter {

	private $controller = 'none';
	private $task = '';


	/**********************************************/
	/* ROUTE THE REQUEST TO THE PROPER CONTROLLER */
	/**********************************************/
	public function route() {
		if (!defined('ELXIS_ADMIN')) { exitPage::make('404', 'CEXT-0001'); }

		$this->makeAdminRoute();

		require(ELXIS_PATH.'/components/com_'.$this->component.'/controllers/'.$this->controller.'.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CEXT-0002', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CEXT-0003', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new extmanagerModel();
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
			$this->controller = 'install';
			$this->task = 'ipanel';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'modules') {
				$this->controller = 'modules';
				$this->task = 'listmods';
				return;
			} elseif ($this->segments[0] == 'components') {
				$this->controller = 'components';
				$this->task = 'listcomps';
				return;
			} elseif ($this->segments[0] == 'templates') {
				$this->controller = 'templates';
				$this->task = 'listtempls';
				return;
			} elseif ($this->segments[0] == 'engines') {
				$this->controller = 'engines';
				$this->task = 'listengs';
				return;
			} elseif ($this->segments[0] == 'auth') {
				$this->controller = 'auth';
				$this->task = 'listauth';
				return;
			} elseif ($this->segments[0] == 'plugins') {
				$this->controller = 'plugins';
				$this->task = 'listplugins';
				return;
			} elseif ($this->segments[0] == 'install') {
				$this->controller = 'install';
				$this->task = 'ipanel';
				return;
			} elseif ($this->segments[0] == 'browse') {
				$this->controller = 'browse';
				$this->task = 'central';
				return;
			}
		}

		if ($c == 2) {
			if ($this->segments[0] == 'modules') {
				$this->controller = 'modules';
				if ($this->segments[1] == 'getmodules.xml') {
					$this->task = 'getmodules';
					return;
				} else if ($this->segments[1] == 'publish') {
					$this->task = 'publishmodule';
					return;
				} else if ($this->segments[1] == 'copy') {
					$this->task = 'copymodule';
					return;		
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deletemodule';
					return;
				} else if ($this->segments[1] == 'move') {
					$this->task = 'movemodule';
					return;
				} else if ($this->segments[1] == 'setorder') {
					$this->task = 'setmoduleorder';
					return;
				} else if ($this->segments[1] == 'add.html') {
					$this->task = 'addmodule';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editmodule';
					return;
				} else if ($this->segments[1] == 'positionorder') {
					$this->task = 'positionorder';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'savemodule';
					return;
				} else {
					exitPage::make('404', 'CEXT-0005');
				}
			}

			if ($this->segments[0] == 'components') {
				$this->controller = 'components';
				if ($this->segments[1] == 'getcomponents.xml') {
					$this->task = 'getcomponents';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editcomponent';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'savecomponent';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deletecomponent';
					return;
				} else {
					exitPage::make('404', 'CEXT-0008');
				}
			}

			if ($this->segments[0] == 'templates') {
				$this->controller = 'templates';
				if ($this->segments[1] == 'gettemplates.xml') {
					$this->task = 'gettemplates';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'edittemplate';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'savetemplate';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deletetemplate';
					return;
				} else if ($this->segments[1] == 'positions.html') {
					$this->task = 'listpositions';
					return;
				} else if ($this->segments[1] == 'getpositions.xml') {
					$this->task = 'getpositions';
					return;
				} else if ($this->segments[1] == 'editposition.html') {
					$this->task = 'editposition';
					return;
				} else if ($this->segments[1] == 'saveposition.html') {
					$this->task = 'saveposition';
					return;
				} else if ($this->segments[1] == 'deleteposition') {
					$this->task = 'deleteposition';
					return;
				} else {
					exitPage::make('404', 'CEXT-0009');
				}
			}

			if ($this->segments[0] == 'engines') {
				$this->controller = 'engines';
				if ($this->segments[1] == 'getengines.xml') {
					$this->task = 'getengines';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editengine';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'saveengine';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deleteengine';
					return;
				} else if ($this->segments[1] == 'publish') {
					$this->task = 'publishengine';
					return;
				} else if ($this->segments[1] == 'makedef') {
					$this->task = 'makedefault';
					return;
				} else if ($this->segments[1] == 'move') {
					$this->task = 'moveengine';
					return;
				} else {
					exitPage::make('404', 'CEXT-0012');
				}
			}

			if ($this->segments[0] == 'auth') {
				$this->controller = 'auth';
				if ($this->segments[1] == 'getauth.xml') {
					$this->task = 'getauth';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editauth';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'saveauth';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deleteauth';
					return;
				} elseif ($this->segments[1] == 'publish') {
					$this->task = 'publishauth';
					return;
				} else if ($this->segments[1] == 'move') {
					$this->task = 'moveauth';
					return;
				} else {
					exitPage::make('404', 'CEXT-0014');
				}
			}

			if ($this->segments[0] == 'plugins') {
				$this->controller = 'plugins';
				if ($this->segments[1] == 'getplugins.xml') {
					$this->task = 'getplugins';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editplugin';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'saveplugin';
					return;
				} else if ($this->segments[1] == 'delete') {
					$this->task = 'deleteplugin';
					return;
				} else if ($this->segments[1] == 'publish') {
					$this->task = 'publishplugin';
					return;
				} else if ($this->segments[1] == 'move') {
					$this->task = 'moveplugin';
					return;
				} else {
					exitPage::make('404', 'CEXT-0016');
				}
			}

			if ($this->segments[0] == 'install') {
				$this->controller = 'install';
				if ($this->segments[1] == 'install.html') {
					$this->task = 'installextension';
					return;
				} elseif ($this->segments[1] == 'sysinstall') {
					$this->task = 'installextensionsys';
					return;
				} elseif ($this->segments[1] == 'cupdate') {
					$this->task = 'extcupdate';
					return;
				} elseif ($this->segments[1] == 'cinstall') {
					$this->task = 'extcinstall';
					return;
				} else if ($this->segments[1] == 'synchro') {
					$this->task = 'syncextension';
					return;
				} else if ($this->segments[1] == 'edc') {
					$this->task = 'edcinstall';
					return;
				} else {
					exitPage::make('404', 'CEXT-0011');
				}
			}

			if ($this->segments[0] == 'browse') {
				$this->controller = 'browse';
				if ($this->segments[1] == 'req') {
					$this->task = 'requestedc';
					return;
				} else {
					exitPage::make('404', 'CEXT-0011');
				}
				exitPage::make('404', 'CEXT-0012');
			}

		}

		exitPage::make('404', 'CEXT-0004');
	}

}

?>