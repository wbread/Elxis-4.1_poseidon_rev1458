<?php 
/**
* @version		$Id: aplugin.php 1130 2012-05-13 18:18:35Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class apluginContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/****************************/
	/* INITIATE JS-PHP IMPORTER */
	/****************************/
	public function import() {
		if (isset($_GET['task'])) {
			$task = trim(filter_input(INPUT_GET, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_POST['task'])) {
			$task = trim(filter_input(INPUT_POST, 'task', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$task = 'ui';
		}

		switch ($task) {
			case 'ui': $this->iface(); break;
			case 'load': $this->loadPlugin(); break;
			case 'handler': $this->handlerPlugin(); break;
			case 'head': $this->headData(); break;
      		default:
				$this->view->errorResponse('Invalid request!');
			break;
		}
	}


	/**********************************************/
	/* PREPARE TO DISPLAY IMPORTER USER INTERFACE */
	/**********************************************/
	private function iface() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		$fn = (isset($_GET['fn'])) ? (int)$_GET['fn'] : 0;
		$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;

		$plugins = $this->model->getPlugins();

		$iPlugin = array();
		if ($id > 0) {
			$iPlugin['row'] = $this->model->getPlugin($id);
			if (!$iPlugin['row']) {
				$id = 0;
			} else {
				if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$iPlugin['row']->plugin.'/'.$iPlugin['row']->plugin.'.plugin.php')) {
					$id = 0;
				} else {
					elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
					$exml = new extensionXML();
					$iPlugin['info'] = $exml->quickXML('plugin', $iPlugin['row']->plugin);
					unset($exml);

					elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
					elxisLoader::loadFile('components/com_content/plugins/'.$iPlugin['row']->plugin.'/'.$iPlugin['row']->plugin.'.plugin.php');
					$class = $iPlugin['row']->plugin.'Plugin';
					if (!class_exists($class, false)) {
						$id = 0;
					} else {
						$this->loadPluginLang($iPlugin['row']->plugin);
						$iPlugin['plugObj'] = new $class();
						
						$headdata = $iPlugin['plugObj']->head();
						if ($headdata) {
							if (isset($headdata['css']) && is_array($headdata['css']) && (count($headdata['css']) > 0)) {
								foreach ($headdata['css'] as $cssfile) { $eDoc->addStyleLink($cssfile); }
							}
							if (isset($headdata['js']) && is_array($headdata['js']) && (count($headdata['js']) > 0)) {
								foreach ($headdata['js'] as $jsfile) { $eDoc->addScriptLink($jsfile); }
							}
						}
					}
				}
			}
		}

		if ($id == 0) { $iPlugin = array(); }

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_content/css/plugins.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/plugins.js');
		$eDoc->addJQuery();
		$eDoc->addScriptLink($elxis->secureBase().'/includes/js/jquery/tabs.js');

		$eDoc->setTitle($eLang->get('IMPORT_ELXIS_PLUGIN'));

		$this->view->interfaceHTML($id, $fn, $plugins, $iPlugin);
	}


	/*************************/
	/* LOAD REQUESTED PLUGIN */
	/*************************/
	private function loadPlugin() {
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$fn = (isset($_POST['fn'])) ? (int)$_POST['fn'] : 0;
		if ($id < 1) { $this->view->errorResponse('No plugin was selected!'); }
		$row = $this->model->getPlugin($id);
		if (!$row) {
			$this->view->errorResponse('The requested plugin was not found!');
		}

		if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php')) {
			$this->view->errorResponse('A required file for plugin <strong>'.$row->plugin.'</strong> was not found!');
		}

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML('plugin', $row->plugin);
		unset($exml);

		elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
		elxisLoader::loadFile('components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php');
		$class = $row->plugin.'Plugin';
		if (!class_exists($class, false)) {
			$this->view->errorResponse('Class <strong>'.$class.'</strong> was not found. <strong>'.$row->plugin.'</strong> is not a valid Elxis plugin!');
		}

		$this->loadPluginLang($row->plugin);
		$plugObj = new $class();

		$this->ajaxHeaders('text/html');
		$this->view->pluginHTML($row, $info, $plugObj, $fn);
		exit();
	}


	/************************************/
	/* LOAD PLUGIN'S HEAD DATA (CSS/JS) */
	/************************************/
	private function headData() {
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$row = $this->model->getPlugin($id);
		if (!$row) {
			$json = array('error' => 1, 'css' => array(), 'js' => array());
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit();
		}
		if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php')) {
			$json = array('error' => 1, 'css' => array(), 'js' => array());
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit();
		}

		elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
		elxisLoader::loadFile('components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php');
		$class = $row->plugin.'Plugin';
		if (!class_exists($class, false)) {
			$json = array('error' => 1, 'css' => array(), 'js' => array());
			$this->ajaxHeaders('application/json');
			echo json_encode($json);
			exit();
		}

		$plugObj = new $class();
		$response = $plugObj->head();
		unset($plugObj);

		$json = array('error' => 1, 'css' => array(), 'js' => array());
		if (is_array($response)) {
			if (isset($response['css']) && is_array($response['css']) && (count($response['css']) > 0)) {
				$json['error'] = 0;
				foreach ($response['css'] as $css) { $json['css'][] = $css; }
			}
			if (isset($response['js']) && is_array($response['js']) && (count($response['js']) > 0)) {
				$json['error'] = 0;
				foreach ($response['js'] as $js) { $json['js'][] = $js; }
			}
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	private function handlerPlugin() {
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$fn = (isset($_POST['fn'])) ? (int)$_POST['fn'] : 0;
		if ($id < 1) { echo '<div class="elx_error">No plugin selected!</div>'; return; }
		$row = $this->model->getPlugin($id);
		if (!$row) {
			echo '<div class="elx_error">The requested plugin was not found!</div>';
			return;
		}
		if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php')) {
			echo '<div class="elx_error">A required file for plugin <strong>'.$row->plugin.'</strong> was not found!</div>';
			return;
		}

		elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
		elxisLoader::loadFile('components/com_content/plugins/'.$row->plugin.'/'.$row->plugin.'.plugin.php');
		$class = $row->plugin.'Plugin';
		if (!class_exists($class, false)) {
			echo '<div class="elx_error">Class <strong>'.$class.'</strong> was not found. <strong>'.$row->plugin.'</strong> is not a valid Elxis plugin!</div>';
			return;
		}

		$this->loadPluginLang($row->plugin);
		$plugObj = new $class();
		$plugObj->handler($id, $fn);
	}


	/************************/
	/* LOAD PLUGIN LANGUAGE */
	/************************/
	private function loadPluginLang($plugin) {
		$eLang = eFactory::getLang();

		$clang = $eLang->currentLang();
		if (file_exists(ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.plugin_'.$plugin.'.php')) {
			$langfile = ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.plugin_'.$plugin.'.php';
		} else if (file_exists(ELXIS_PATH.'/language/en/en.plugin_'.$plugin.'.php')) {
			$langfile = ELXIS_PATH.'/language/en/en.plugin_'.$plugin.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/language/'.$clang.'.plugin_'.$plugin.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/language/'.$clang.'.plugin_'.$plugin.'.php';
		} else if (file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/language/en.plugin_'.$plugin.'.php')) {
			$langfile = ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/language/en.plugin_'.$plugin.'.php';
		} else {
			$langfile = '';
		}

		if ($langfile != '') { $eLang->loadFile($langfile); }
	}

}

?>