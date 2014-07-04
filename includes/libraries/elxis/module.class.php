<?php 
/**
* @version		$Id: module.class.php 1390 2013-02-22 20:01:35Z datahell $
* @package		Elxis
* @subpackage	Module
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisModule {


	private $isloaded = false;
	private $modules = array();
	private $positions = array();
	private $rendered_modules = array();
	private $lng = '';
	private $translate = false;
	private $gid = 7;
	private $apc = 0;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$elxis = eFactory::getElxis();

		$this->gid = $elxis->user()->gid;
		$this->apc = $elxis->getConfig('APC');
		if ($elxis->getConfig('MULTILINGUISM') == 1) {
			$this->lng = eFactory::getURI()->getUriLang();
			if ($this->lng != '') {
				$this->translate = true;
			}
		}
	}


	/****************/
	/* LOAD MODULES */
	/****************/
	public function load() {
		if ($this->isloaded) { return; }
		$this->isloaded = true;

		$section = (defined('ELXIS_ADMIN')) ? 'backend' : 'frontend';

		if (($this->apc == 1) && ($this->gid == 7)) {
			$modules = elxisAPC::fetch('mods'.$this->lng, 'modules'.$section);
			$positions = elxisAPC::fetch('positions'.$this->lng, 'modules'.$section);
			if (($modules !== false) && ($positions !== false)) {
				$this->modules = $modules;
				$this->positions = $positions;
				return;
			}
		}

		$acllist = eFactory::getElxis()->acl()->getCategory('module');
		if (count($acllist) == 0) { return; }

		$allowed = array();
		foreach ($acllist as $element => $list) {
			foreach ($list as $identity => $list2) {
				foreach ($list2 as $action => $aclvalue) {
					if ($action != 'view') { continue; }
					if ($aclvalue[0] < 1) { break; }
					$allowed[$identity] = $element;
					break;
				}
			}
		}
		unset($acllist);
		if (count($allowed) == 0) { return; }

		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('id').", ".$db->quoteId('title').", ".$db->quoteId('module').", ".$db->quoteId('showtitle').","
		."\n ".$db->quoteId('position').", ".$db->quoteId('content').", ".$db->quoteId('params')." FROM ".$db->quoteId('#__modules')
		."\n WHERE ".$db->quoteId('published')." = 1 AND ".$db->quoteId('section')." = :sec ORDER BY ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':sec', $section, PDO::PARAM_STR);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$rows) { return; }

		if ($section == 'frontend') {
			$Itemid = eFactory::getMenu()->getMenuId();
			$sql = "SELECT ".$db->quoteId('moduleid')." FROM ".$db->quoteId('#__modules_menu')
			."\n WHERE ((".$db->quoteId('menuid')." = 0) OR (".$db->quoteId('menuid')." = :itemid))";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':itemid', $Itemid, PDO::PARAM_INT);
			$stmt->execute();
			$showids = $stmt->fetchCol();
			if (!$showids) { return; }
		}

		$i = 1;
		$translate_mods = array();
		foreach ($rows as $row) {
			$m = (int)$row['id'];
			if (!isset($allowed[$m])) { continue; }
			if ($section == 'frontend') {
				if (!in_array($m, $showids)) { continue; }
			}

			if (!file_exists(ELXIS_PATH.'/modules/'.$row['module'].'/'.$row['module'].'.php')) { continue; }
			$position = $row['position'];

			if (!isset($this->positions[$position])) { $this->positions[$position] = array(); }
			$this->positions[$position][] = $i;

			$mod = new stdClass;
			$mod->id = $m;
			$mod->title = $row['title'];
			$mod->module = $row['module'];
			$mod->showtitle	= $row['showtitle'];
			$mod->position = $row['position'];
			$mod->content = $row['content'];
			$mod->params = $row['params'];
			$this->modules[$i] = $mod;

			if ($mod->showtitle == 1) {
				$translate_mods[$m] = $i;
			} else if ($mod->module == 'mod_content') {
				$translate_mods[$m] = $i;
			}

			$i++;
		}
		unset($mod, $i, $rows);

		$this->translateModules($translate_mods);

		if (($this->apc) && ($this->gid == 7)) {
			if ($this->modules) {
				elxisAPC::store('positions'.$this->lng, 'modules'.$section, $this->positions, 900);
				elxisAPC::store('mods'.$this->lng, 'modules'.$section, $this->modules, 900);
			}
		}
	}


	/******************************/
	/* RENDER MODULES BY POSITION */
	/******************************/
	public function renderPosition($position, $elxstyle, $elxdebug=0) {
		$this->load();
		if (trim($position) == '') { return ''; }
		if (!isset($this->positions[$position])) { return ''; }
		$c = count($this->positions[$position]);
		if (in_array($elxdebug, array(1, 3, 5))) {
	    	$buffer = '<div class="elx_moduledebug" dir="ltr">'."\n";
			$buffer .= '<strong>'.$position.'</strong> : '.$c." modules\n";
			if ($c > 0) {
				$buffer .= "<br />\n";
				foreach ($this->positions[$position] as $modid) {
					$buffer .= $this->modules[$modid]->title.' <span>('.$this->modules[$modid]->module.")</span><br />\n";
				}
			}
			$buffer .= "</div>\n";
			return $buffer;
		}

		if ($c == 0) { return ''; }
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$buffer = '';
		foreach ($this->positions[$position] as $modid) {
			$elxmod = $this->modules[$modid];
			if (!file_exists(ELXIS_PATH.'/modules/'.$elxmod->module.'/'.$elxmod->module.'.php')) { continue; }
			$buffer .= $this->render($elxmod, $elxstyle, $elxdebug);
		}
		return $buffer;
	}


	/*************************/
	/* RENDER MODULE BY NAME */
	/*************************/
	public function renderModule($modname, $elxstyle, $elxdebug=0) {
		$this->load();
		if (!$this->modules) { return; }
		if (trim($modname) == '') { return ''; }
		if (!file_exists(ELXIS_PATH.'/modules/'.$modname.'/'.$modname.'.php')) { return ''; }
		$idx = -1;
		foreach ($this->modules as $key => $elxmod) {
			if ($elxmod->module == $modname) { $idx = $key; break; }
		}

		if ($idx < 0) { return ''; }
		if (in_array($elxdebug, array(1, 3, 5))) {
	    	$buffer = '<div class="elx_moduledebug" dir="ltr">'."\n";
			$buffer .= $this->modules[$idx]->title.' <span>('.$this->modules[$idx]->module.")</span>\n";
			$buffer .= "</div>\n";
			return $buffer;
		}
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$buffer = $this->render($this->modules[$idx], $elxstyle, $elxdebug);
		return $buffer;
	}


	/******************************/
	/* ACTUALLY RENDER THE MODULE */
	/******************************/
	private function render($elxmod, $elxstyle, $elxdebug=0) {
		if ($elxdebug > 1) {
			$ePerformance = eRegistry::get('ePerformance');
			$ePerformance->startBlock($elxmod->module, 'modules/'.$elxmod->module.'/'.$elxmod->module.'.php');
		}

		$params = new elxisParameters($elxmod->params, '', 'module');
		$cache = (int)$params->get('cache', 0);
		$mobsfx = (ELXIS_MOBILE == 1) ? '_mob' : '';
		$cache_state = 0;
		if (($cache == 2) || (($cache == 1) && ($this->gid == 7))) {
			$cachetime = (int)$params->get('cachetime', 0);
			$eCache = eFactory::getCache();
			$cache_state = $eCache->begin($elxmod->module.$mobsfx, $elxmod->id, 'modules', $cachetime);
			if ($cache_state == 1) {
				ob_start();
				$eCache->fetch();
				$html = ob_get_contents();
				ob_end_clean();
				if ($elxdebug > 1) { $ePerformance->stopBlock(); }
				return $html;
			} else if ($cache_state == 2) {
				eFactory::getDocument()->beginObserver();
			}
		}

		$eLang = eFactory::getLang();
		if (!in_array($elxmod->module, $this->rendered_modules)) {
			$eLang->load($elxmod->module, 'module');
			$this->rendered_modules[] = $elxmod->module;
		}

		$title = $elxmod->title;
		if ($elxmod->showtitle == 2) {
			$str = strtoupper($elxmod->module).'_TITLE';
			if ($eLang->exist($str)) { $title = $eLang->get($str); }
			if ($elxmod->module == 'mod_login') {
				if (eFactory::getElxis()->user()->gid != 7) {
					if ($eLang->exist('MOD_LOGOUT_TITLE')) { $title = $eLang->get('MOD_LOGOUT_TITLE'); }
				}
			}
		}

		$css_sfx = $params->get('css_sfx');
		ob_start();
        if ($elxstyle == 'round') {
			echo '<div class="moduleround'.$css_sfx.'">'."\n";
			echo "\t<div>\n";
			echo "\t\t<div>\n";
			echo "\t\t\t<div>\n";
			if ($elxmod->showtitle > 0) {
				echo "\t\t\t\t<h3>".$title."</h3>\n";
			}
			include(ELXIS_PATH.'/modules/'.$elxmod->module.'/'.$elxmod->module.'.php');
			echo "\t\t\t</div>\n";
			echo "\t\t</div>\n";
			echo "\t</div>\n";
			echo "</div>\n";
		} elseif ($elxstyle == 'none') {
			if ($elxmod->showtitle > 0) {
				echo "<h3>".$title."</h3>\n";
			}
			include(ELXIS_PATH.'/modules/'.$elxmod->module.'/'.$elxmod->module.'.php');
		} else {
			echo '<div class="module'.$css_sfx.'">'."\n";
			if ($elxmod->showtitle > 0) {
				echo "\t<h3>".$title."</h3>\n";
			}
			include(ELXIS_PATH.'/modules/'.$elxmod->module.'/'.$elxmod->module.'.php');
			echo "</div>\n";
		}
		$html = ob_get_contents();
		ob_end_clean();

		if ($cache_state == 2) {
			$eCache->store($html);
			eFactory::getDocument()->endObserver();
		}

		if ($elxdebug > 1) {
			$ePerformance->stopBlock();
		}

		return $html;
	}


	/*********************/
	/* TRANSLATE MODULES */
	/*********************/
	private function translateModules($translate_elids) {
		if (!$this->translate) { return; }
		if (count($translate_elids) == 0) { return; }
		$elids = array_keys($translate_elids);
		$db = eFactory::getDB();
		$query = "SELECT ".$db->quoteId('element').", ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')." = ".$db->quote('module')
		."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(', ', $elids).")";
		$stmt = $db->prepare($query);
		$stmt->execute(array(':lng' => $this->lng));
		$trans = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (!$trans) { return; }
		unset($stmt);
		foreach ($trans as $tran) {
			$mid = $tran['elid'];
			$element = $tran['element'];
			$idx = $translate_elids[$mid];
			$this->modules[$idx]->$element = $tran['translation'];
		}
	}


	/***********************************/
	/* COUNT THE MODULES IN A POSITION */
	/***********************************/
	public function countModules($position) {
		return (isset($this->positions[$position])) ? count($this->positions[$position]) : 0;
	}

}

?>