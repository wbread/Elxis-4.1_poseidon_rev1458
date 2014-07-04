<?php 
/**
* @version		$Id: user.php 1131 2012-05-15 18:50:58Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_user/controllers/base.php');
elxisLoader::loadFile('components/com_user/views/base.html.php');


class userRouter extends elxisRouter {

	private $controller = '';
	private $task = '';
	private $json = false;
	private $auth = '';


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
		require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CUSE-0001', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CUSE-0002', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}

		$view = new $viewclass();
		$model = new userModel();
		if ($this->controller == 'aaccess') {
			$controller = new $class($view, $task, $model, $this->json);
		} else {
			$controller = new $class($view, $task, $model);
		}
		unset($view);

		if (($this->controller == 'account') && ($this->task == 'login')) {
			$controller->$task($this->auth);
		} else {
			$controller->$task();
		}
	}


	/***********************/
	/* MAKE FRONTEND ROUTE */
	/***********************/
	private function makeRoute() {
		$c = count($this->segments);
		if ($c == 0) {
			$this->controller = 'account';
			$this->task = 'userscentral';
			return;
		}

		if ($c > 2) {
			exitPage::make('404', 'CUSE-0003');
		}

		if ($c == 1) {
			$this->controller = 'account';
			switch($this->segments[0]) {
				case 'login': $this->task = 'login'; break;
				case 'logout.html': $this->task = 'logout'; break;
				case 'register.html': $this->task = 'register'; break;
				case 'activate.html': $this->task = 'activate'; break;
				case 'recover-pwd.html': $this->task = 'recoverpass'; break;
				case 'changetz.html': $this->task = 'changetimezone'; break;
				case 'userscentral': break;//don't allowed access to userscentral this way
				case 'members':
					$this->controller = 'members';
					$this->task = 'memberslist';
				break;
				default: break;
			}
			if ($this->task == '') {
				exitPage::make('404', 'CUSE-0004');
			}
			return;	
		}

		if ($this->segments[0] == 'members') {
			$this->controller = 'members';
			switch ($this->segments[1]) {
				case 'edit.html': $this->task = 'editprofile'; break;
				case 'save.html': $this->task = 'saveprofile'; break;
				case 'block.html': $this->task = 'blockaccount'; break;
				case 'delete.html': $this->task = 'deleteaccount'; break;
				case 'myprofile.html': $this->task = 'profile'; break; //shortcut for my own profile
				default:
					$n = str_ireplace('.html', '', $this->segments[1]);
					if (!is_numeric($n) || (intval($n) < 1)) {
						exitPage::make('404', 'CUSE-0005');
					}
					$this->task = 'profile';
				break;
			}
		}

		if ($this->segments[0] == 'login') {
			$this->controller = 'account';
			if (!preg_match('/(\.html)$/', $this->segments[1])) {
				exitPage::make('404', 'CUSE-0005');
			}
			$auth = str_replace('.html', '', $this->segments[1]);
			$auth = trim(preg_replace('/[^a-z\_\-0-9]/', '', $auth));
			if (($auth == '') || !file_exists(ELXIS_PATH.'/components/com_user/auth/'.$auth.'/'.$auth.'.auth.php')) {
				exitPage::make('404', 'CUSE-0015');
			}
			$this->auth = $auth;
			$this->task = 'login';
		}

		if ($this->task == '') {
			exitPage::make('404', 'CUSE-0006');
		}
	}


	/********************/
	/* MAKE ADMIN ROUTE */
	/********************/
	private function makeAdminRoute() {
		$this->controller = 'amembers';

		$c = count($this->segments);
		if ($c == 0) { //alias of user/amembers/
			$this->task = 'listusers';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'users') {
				$this->task = 'listusers';
				return;
			} elseif ($this->segments[0] == 'groups') {
				$this->controller = 'agroups';
				$this->task = 'listgroups';
				return;
			} elseif ($this->segments[0] == 'acl') {
				$this->controller = 'aaccess';
				$this->task = 'listacl';
				return;
			}
		}

		if ($c == 2) {
			if ($this->segments[0] == 'users') {
				$this->controller = 'amembers';
				if ($this->segments[1] == 'getusers.xml') {
					$this->task = 'getusers';
					return;
				} elseif ($this->segments[1] == 'toggleuser') { //ajax
					$this->task = 'toggleuser';
					return;
				} elseif ($this->segments[1] == 'deleteuser') { //ajax
					$this->task = 'deleteuser';
					return;
				} elseif ($this->segments[1] == 'edit.html') {
					$this->task = 'edituser';
					return;
				} elseif ($this->segments[1] == 'save.html') { //inner.php
					$this->task = 'saveuser';
					return;
				}
			}

			if ($this->segments[0] == 'groups') {
				$this->controller = 'agroups';
				if ($this->segments[1] == 'getgroups.xml') {
					$this->task = 'getgroups';
					return;
				} elseif ($this->segments[1] == 'edit.html') {
					$this->task = 'editgroup';
					return;
				} elseif ($this->segments[1] == 'deletegroup') { //ajax
					$this->task = 'deletegroup';
					return;
				} elseif ($this->segments[1] == 'save.html') { //inner.php
					$this->task = 'savegroup';
					return;
				}
			}

			if ($this->segments[0] == 'acl') {
				$this->controller = 'aaccess';
				if ($this->segments[1] == 'getacl.xml') {
					$this->task = 'getacl';
					return;
				} elseif ($this->segments[1] == 'deleteacl') { //ajax
					$this->task = 'deleteacl';
					return;
				} elseif ($this->segments[1] == 'edit.html') { //inner.php
					$this->task = 'editacl';
					return;
				} elseif ($this->segments[1] == 'savejson') { //ajax/json
					$this->json = true;
					$this->task = 'savejson';
					return;
				}
			}

		}

		exitPage::make('404', 'CUSE-0007');
	}

}

?>