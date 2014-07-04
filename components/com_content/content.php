<?php 
/**
* @version		$Id: content.php 1407 2013-04-10 19:03:03Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('components/com_content/controllers/base.php');
elxisLoader::loadFile('components/com_content/views/base.html.php');


class contentRouter extends elxisRouter {

	private $controller = 'fpage';
	private $task = 'frontpage';
	private $format = 'html';


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
		if (($this->format != 'html') && file_exists(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.'.$this->format.'.php')) {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.'.$this->format.'.php');
		} else {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/views/'.$this->controller.'.html.php');
		}
		if ($this->controller == 'aplugin') {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/models/plugins.model.php');
		} else {
			require(ELXIS_PATH.'/components/com_'.$this->component.'/models/'.$this->component.'.model.php');
		}
		$class = $this->controller.ucfirst($this->component).'Controller';
		$viewclass = $this->controller.ucfirst($this->component).'View';
		$task = $this->task;
		if (!class_exists($class, false)) {
			exitPage::make('error', 'CCON-0001', 'Class '.$class.' was not found in file '.$this->controller.'.php');
		}
		if (!method_exists($class, $task)) {
			exitPage::make('error', 'CCON-0002', 'Task '.$task.' was not found in class '.$class.' in file '.$this->controller.'.php');
		}
		$view = new $viewclass();
		$model = new contentModel();
		$controller = new $class($view, $model, $this->format);
		unset($view, $model);
		$controller->$task();
	}


	/**************/
	/* MAKE ROUTE */
	/**************/
	private function makeRoute() {
		$n = count($this->segments);
		if ($n == 0) {
			$this->controller = 'fpage';
			$this->task = 'frontpage';
			return;
		}

		$eURI = eFactory::getURI();
		$fp = ($eURI->getUriString() == eFactory::getElxis()->getConfig('DEFAULT_ROUTE')) ? true : false;
		if ($eURI->isDir()) { //we are watching a category!
			$this->controller = 'category';
			$this->task = 'viewcategory';
			return;
		} else if ($fp && preg_match('#\/$#', $eURI->getUriString())) { //a category as frontpage
			$this->controller = 'category';
			$this->task = 'viewcategory';
			return;
		} else {
			if ($this->segments[0] == 'minify') {
				if (isset($this->segments[1])) {
					$this->controller = 'generic';
					$this->task = 'minify';
					return;
				}
			}
			if ($this->segments[0] == 'feeds.html') {
				$this->controller = 'generic';
				$this->task = 'feeds';
				return;
			}
			if ($this->segments[0] == 'rss.xml') {
				$this->controller = 'generic';
				$this->task = 'rssfeed';
				$this->format = 'xml';
				return;
			}
			if ($this->segments[0] == 'atom.xml') {
				$this->controller = 'generic';
				$this->task = 'atomfeed';
				$this->format = 'xml';
				return;
			}
			if ($this->segments[0] == 'contenttools.html') {//AJAX
				$this->controller = 'generic';
				$this->task = 'contenttools';
				return;
			}
			if ($this->segments[0] == 'ajax') {//ajax
				$this->controller = 'generic';
				$this->task = 'genericajax';
				return;
			}
			if ($this->segments[0] == 'tags.html') {
				$this->controller = 'generic';
				$this->task = 'tags';
				return;
			}
			if ($this->segments[0] == 'send-to-friend.html') {
				$this->controller = 'generic';
				$this->task = 'sendtofriend';
				return;
			}

			$last = $n - 1;
			if (($n > 1) && (preg_match('/(\.xml)$/i', $this->segments[$last]))) { //category rss/atom feed
				if (!in_array($this->segments[$last], array('rss.xml', 'atom.xml'))) {
					exitPage::make('404', 'CCON-0003');
				}
				$this->controller = 'category';
				$this->task = 'viewcategory';
				$this->format = 'xml';
				return;
			}

			if (!preg_match('/(\.html)$/i', $this->segments[$last])) { //format=mobile?
				exitPage::make('404', 'CCON-0004');
			}
			$this->controller = 'article';
			$this->task = 'viewarticle';
		}
	}


	/********************/
	/* MAKE ADMIN ROUTE */
	/********************/
	private function makeAdminRoute() {
		$this->controller = 'acategory';

		$c = count($this->segments);
		if ($c == 0) { //alias of content/categories/
			$this->task = 'listcategories';
			return;
		}

		if ($c == 1) {
			if ($this->segments[0] == 'categories') {
				$this->task = 'listcategories';
				return;
			} elseif ($this->segments[0] == 'articles') {
				$this->controller = 'aarticle';
				$this->task = 'listarticles';
				return;
			} elseif ($this->segments[0] == 'fpage') {
				$this->controller = 'afpage';
				$this->task = 'design';
				return;
			} elseif ($this->segments[0] == 'plugin') {
				$this->controller = 'aplugin';
				$this->task = 'import';
				return;
			}
		}

		if ($c == 2) {
			if ($this->segments[0] == 'categories') {
				if ($this->segments[1] == 'getcategories.xml') { //ajax
					$this->task = 'getcategories';
					return;
				} else if ($this->segments[1] == 'move') { //ajax
					$this->task = 'movecategory';
					return;
				} else if ($this->segments[1] == 'publish') { //ajax
					$this->task = 'publishcategory';
					return;
				} else if ($this->segments[1] == 'delete') { //ajax
					$this->task = 'deletecategory';
					return;
				} else if ($this->segments[1] == 'add.html') {
					$this->task = 'addcategory';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editcategory';
					return;
				} else if ($this->segments[1] == 'suggest') { //ajax
					$this->task = 'suggestcategory';
					return;
				} else if ($this->segments[1] == 'validate') { //ajax
					$this->task = 'validatecategory';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'savecategory';
					return;
				}
			}

			if ($this->segments[0] == 'articles') {
				$this->controller = 'aarticle';
				if ($this->segments[1] == 'getarticles.xml') { //ajax
					$this->task = 'getarticles';
					return;
				} else if ($this->segments[1] == 'setorder') { //ajax
					$this->task = 'setorder';
					return;
				} else if ($this->segments[1] == 'publish') { //ajax
					$this->task = 'publisharticles';
					return;
				} else if ($this->segments[1] == 'delete') { //ajax
					$this->task = 'deletearticles';
					return;
				} else if ($this->segments[1] == 'add.html') {
					$this->task = 'addarticle';
					return;
				} else if ($this->segments[1] == 'edit.html') {
					$this->task = 'editarticle';
					return;
				} else if ($this->segments[1] == 'deletecomment') { //ajax
					$this->task = 'deletecomment';
					return;
				} else if ($this->segments[1] == 'publishcomment') { //ajax
					$this->task = 'publishcomment';
					return;
				} else if ($this->segments[1] == 'suggest') { //ajax
					$this->task = 'suggestarticle';
					return;
				} else if ($this->segments[1] == 'validate') { //ajax
					$this->task = 'validatearticle';
					return;
				} else if ($this->segments[1] == 'save.html') {
					$this->task = 'savearticle';
					return;
				} else if ($this->segments[1] == 'copy') { //ajax
					$this->task = 'copyarticles';
					return;
				} else if ($this->segments[1] == 'move') { //ajax
					$this->task = 'movearticles';
					return;
				}
			}

			if ($this->segments[0] == 'fpage') {
				$this->controller = 'afpage';
				if ($this->segments[1] == 'save') { //ajax
					$this->task = 'savelayout';
					return;
				}
			}
		}

		exitPage::make('404', 'CCON-0005');
	}

}

?>