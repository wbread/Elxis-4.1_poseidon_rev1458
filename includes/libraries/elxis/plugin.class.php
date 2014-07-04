<?php 
/**
* @version		$Id: plugin.class.php 1231 2012-07-08 08:09:42Z datahell $
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisPlugin {

	private $plugins = array();
	private $allplugins = array();
	private $isloaded = false;
	private $runtimes = 0;
	private $plugintimes = 0;
	private $executed = array();
	private $apc = 0;


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
		$this->loadPlugins();
	}


	/****************/
	/* LOAD PLUGINS */
	/****************/
	private function loadPlugins() {
		if ($this->isloaded) { return; }
		$this->isloaded = true;

		$db = eFactory::getDB();
		$elxis = eFactory::getElxis();

		$this->apc = $elxis->getConfig('APC');
		if (($this->apc == 1) && ($elxis->user()->gid == 7)) {
			$plugins = elxisAPC::fetch('published', 'plugins');
			$allplugins = elxisAPC::fetch('all', 'plugins');
			if (($plugins !== false) && ($allplugins !== false)) {
				$this->plugins = $plugins;
				$this->allplugins = $allplugins;
				return;
			}
		}

		$sql = "SELECT ".$db->quoteId('id').", ".$db->quoteId('plugin').", ".$db->quoteId('alevel').", ".$db->quoteId('published').", ".$db->quoteId('params')
		."\n FROM ".$db->quoteId('#__plugins')." ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return; }

		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		foreach ($rows as $row) {
			$plugin = $row['plugin'];
			$alevel = (int)$row['alevel'];
			if (!file_exists(ELXIS_PATH.'/components/com_content/plugins/'.$plugin.'/'.$plugin.'.plugin.php')) { continue; }
			$this->allplugins[] = $plugin;
			if ($row['published'] == 0) {
				$this->plugins[$plugin] = array('published' => false, 'params' => null);
				continue;
			}
			if (($row['alevel'] > $lowlev) && ($row['alevel'] != $exactlev)) {
				$this->plugins[$plugin] = array('published' => false, 'params' => null);
				continue;
			}
			$this->plugins[$plugin] = array('published' => true, 'params' => $row['params']);
		}

		if (($this->apc == 1) && ($elxis->user()->gid == 7)) {
			if ($this->plugins) {
				elxisAPC::store('published', 'plugins', $this->plugins, 900);
				elxisAPC::store('all', 'plugins', $this->allplugins, 900);
			}
		}
	}


	/*************************************************/
	/* RUN ALL PROVIDED/AVAILABLE PLUGINS ON AN ITEM */
	/*************************************************/
	public function process(&$row) {
		if (!$this->allplugins) { return true; }
		$avplugins = $this->usedPlugins($row->text); //= $this->allplugins for global execution (slower)
		if (!$avplugins) { return true; }

		if ($this->runtimes == 0) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			elxisLoader::loadFile('components/com_content/plugins/plugin.interface.php');
		}

		$this->runtimes++;
		$result = array();
		$cregex = '#<code>(.*?)</code>#';
		foreach ($avplugins as $plugin) {
			if (!in_array($plugin, $this->executed)) {
				if (!isset($this->plugins[$plugin])) { continue; }
				$this->plugintimes++;
				$published = $this->plugins[$plugin]['published'];
				if (!$published) {
					$params = null;
				} else {
					$this->loadPluginLang($plugin);
					$params = new elxisParameters($this->plugins[$plugin]['params'], '', 'plugin');
				}
				elxisLoader::loadFile('components/com_content/plugins/'.$plugin.'/'.$plugin.'.plugin.php');
				$this->plugins[$plugin]['params'] = $params; //converted to object, ready for later use
				$this->executed[] = $plugin;
			} else {
				$published = $this->plugins[$plugin]['published'];
				$params = $this->plugins[$plugin]['params'];
			}

			$row->text = preg_replace($cregex, "$1", $row->text); //remove <code> tags that surrounds plugins

			$classname = $plugin.'Plugin';
			$plg = new $classname();
			$result = $plg->process($row, $published, $params);
		}

		return $result;
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


	/********************************/
	/* REMOVE ALL PLUGINS FROM TEXT */
	/********************************/
	public function removePlugins($text) {
		$cregex = '#<code>(.*?)</code>#';
		$regex = '#{[^}]*}(?:.+?{\/[^}]*})?#';
		$eregex = '~href="#elink:(.*?)"~';
		$newtext = preg_replace($cregex, '', $text);
		$newtext = preg_replace($regex, '', $newtext);
    	$newtext = preg_replace($eregex, 'href="javascript:void(null);"', $newtext);

		return $newtext;
	}


	/********************************/
	/* GET ALL USED PLUGINS IN TEXT */
	/********************************/
	public function usedPlugins($text) {
		$regex = '#\{([^\/](.*?))[\}|\s]#';
		preg_match_all($regex, $text, $matches);
		if (isset($matches[1]) && (count($matches[1]) > 0)) {
			$avplugins = array_unique($matches[1]);
		} else {
			$avplugins = array();
		}

		if (strpos($text, '#elink:') !== false) { $avplugins[] = 'elink'; }
		return $avplugins;
	}


	/**********************************************/
	/* CONVERT AN ATTRIBUTES STRING INTO AN ARRAY */
	/**********************************************/
	public function parseAttributes($string) {
		$attrs = array();
		if (trim($string) == '') { return $attrs; }
		$string = html_entity_decode($string);
		$pattern = '/(\\w+)\s*=\\s*("[^"]*"|\'[^\']*\'|[^"\'\\s>]*)/';
		preg_match_all($pattern, $string, $matches, PREG_SET_ORDER);
		if (!$matches) { return $attrs; }
		foreach ($matches as $match) {
    		if (($match[2][0] == '"' || $match[2][0] == "'") && ($match[2][0] == $match[2][strlen($match[2])-1])) {
    			$match[2] = substr($match[2], 1, -1);
			}
    		$name = $match[1];
			$attrs[$name] = $match[2];
		}
		return $attrs;
	}


	/*************************/
	/* GET DEBUG INFORMATION */
	/*************************/
	public function runData() {
		$rundata = array(
			'plugins' => $this->allplugins,
			'runtimes' => $this->runtimes,
			'plugintimes' => $this->plugintimes
		);
		return $rundata;
	}

}

?>