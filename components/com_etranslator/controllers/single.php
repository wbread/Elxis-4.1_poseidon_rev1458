<?php 
/**
* @version		$Id: single.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class singleEtranslatorController extends etranslatorController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***********************************/
	/* PREPARE TO DISPLAY TRANSLATIONS */
	/***********************************/
	public function listtrans() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$ilangs = $this->model->getInstalledLangs();
		$trcats = $this->model->getTransCategories();
		$trelements = array (
			'title' => $eLang->get('TITLE'),
			'subtitle' => $eLang->get('SUBTITLE'),
			'introtext' => $eLang->get('INTROTEXT'),
			'maintext' => $eLang->get('MAINTEXT'),
			'caption' => $eLang->get('CAPTION'),
			'sitename' => $eLang->get('SITENAME'),
			'metadesc' => $eLang->get('METADESC'),
			'metakeys' => $eLang->get('METAKEYS'),
			'category_title' => $eLang->get('CATEGORY_TITLE'),
			'category_description' => $eLang->get('CATEGORY_DESCRIPTION'),			
			'content' => $eLang->get('CONTENT')
		);

		$eDoc->addStyleLink($elxis->secureBase().'/includes/libraries/elxis/language/mlflag.css');
        $eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/etranslator.js');

		$pathway->addNode($eLang->get('TRANSLATOR'), 'etranslator:/');
		$eDoc->setTitle($eLang->get('TRANSLATOR').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->listTrans($trcats, $trelements, $ilangs);
	}


	/*************************************************************/
	/* RETURN LIST OF TRANSLATIONS FOR GRID IN XML FORMAT (AJAX) */
	/*************************************************************/
	public function gettrans() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$sortcols = array('category', 'element', 'elid', 'language');
		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'category', 'sortorder' => 'asc',
			'qtype' => 'elid', 'query' => '', 'limitstart' => 0,
			'category' => '', 'element' => '', 'language' => ''
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'category';
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'category'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['query'] = isset($_POST['query']) ? (int)$_POST['query'] : 0;
		if ($options['query'] < 0) { $options['query'] = 0; }
		$pat = '#[^a-zA-Z0-9\_\-]#';
		$options['category'] = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['category'] = preg_replace($pat, '', $options['category']);
		$options['element'] = filter_input(INPUT_POST, 'element', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['element'] = preg_replace($pat, '', $options['element']);
		$options['language'] = filter_input(INPUT_POST, 'language', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$options['language'] = preg_replace($pat, '', $options['language']);
		if ($options['language'] != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$options['language'].'/'.$options['language'].'.php')) { $options['language'] = ''; }
		}

		$total = $this->model->countTranslations($options);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		$modsacl = array();
		if ($total > 0) {
			$rows = $this->model->getTranslations($options, true);
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 0;
			$flags_dir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			$otextdir = (in_array($elxis->getConfig('LANG'), array('fa', 'he', 'ar'))) ? 'rtl' : 'ltr';

			foreach ($rows as $row) {
				$sn = $options['limitstart'] + $i + 1;
				if (in_array($row['element'], array('introtext', 'maintext', 'category_description', 'content'))) {
					$trans_txt = '<span style="color:#555;">'.$eLang->get('LONG_TEXT').'</span>';
					$orig_txt = '<span style="color:#555;">'.$eLang->get('LONG_TEXT').'</span>';
				} else if ((strpos($row['element'], 'description') === true) || (strpos($row['element'], 'text') === true)) {
					$trans_txt = '<span style="color:#555;">'.$eLang->get('LONG_TEXT').'</span>';
					$orig_txt = '<span style="color:#555;">'.$eLang->get('LONG_TEXT').'</span>';
				} else {
					$textdir = (in_array($row['translation'], array('fa', 'he', 'ar'))) ? 'rtl' : 'ltr';
					$trans_txt = (eUTF::strlen($row['translation']) > 30) ? eUTF::substr($row['translation'], 0, 27).'...' : $row['translation'];
					$trans_txt = '<span dir="'.$textdir.'">'.$trans_txt.'</span>';
					if ($row['original_text'] == '') {
						$orig_txt = '<span style="color:#555;">'.$eLang->get('NOT_AVAILABLE').'</span>';
					} else {
						$orig_txt = (eUTF::strlen($row['original_text']) > 30) ? eUTF::substr($row['original_text'], 0, 27).'...' : $row['original_text'];
						$orig_txt = '<span dir="'.$otextdir.'">'.$orig_txt.'</span>';
					}
				}

				$element_txt = $eLang->silentGet($row['element'], true);

				echo '<row id="'.$row['trid'].'">'."\n";
				echo '<cell>'.$sn."</cell>\n";
				echo '<cell><![CDATA['.$row['category']."]]></cell>\n";
				echo '<cell><![CDATA['.$element_txt."]]></cell>\n";
				echo '<cell>'.$row['elid']."</cell>\n";
				echo '<cell><![CDATA['.$orig_txt."]]></cell>\n";
				echo '<cell><![CDATA[<img src="'.$flags_dir.$row['language'].'.png" alt="'.$row['language'].'" title="'.$row['language'].'" border="0" />'."]]></cell>\n";
				echo '<cell><![CDATA['.$trans_txt."]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}

	/*******************************/
	/* PREPARE TO EDIT TRANSLATION */
	/*******************************/
	public function edittrans($is_new=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$trid = isset($_GET['trid']) ? (int)$_GET['trid'] : 0;
		if ($trid < 1) { $trid = 0; }

		if ($trid == 0) {
			echo '<div class="elx_error">'.$eLang->get('TRANS_NOT_FOUND')."</div>\n";
			return;
		}

		$row = new translationsDbTable();
		if (!$row->load($trid)) {
			echo '<div class="elx_error">'.$eLang->get('TRANS_NOT_FOUND')."</div>\n";
			return;
		}

		if (preg_match('/^(com\_)/i', $row->category)) {
			$component = strtolower($row->category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				echo '<div class="elx_error">You are not allowed to manage component '.$component."</div>\n";
				return;
			}
		}

		if ($row->category == 'module') {
			if (preg_match('/^(mod\_)/i', $row->element)) {
				$module = strtolower($row->element);
				if ($elxis->acl()->check('module', $module, 'manage', $row->elid) < 1) {
					echo '<div class="elx_error">You are not allowed to edit module '.$module.' with instance '.$elid.'!'."</div>\n";
					return;
				}
			}
		}

		if ($row->category == 'config') {
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
				echo '<div class="elx_error">You are not allowed to edit Elxis configuration options!'."</div>\n";
				return;
			}
		}

		$ctlangs = array();
		if ($is_new) {
			$row->forceNew(true);
			$row->translation = '';
			$ctlangs = $this->model->getCTLangs($row->category, $row->element, $row->elid);
		}

		$original = $this->loadOriginal($row->category, $row->element, $row->elid);

		$installed = eFactory::getFiles()->listFolders('language/');
		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		$avlangs = array();
		foreach ($installed as $lng) {
			if (!isset($langdb[$lng])) { continue; }
			$avlangs[$lng] = $langdb[$lng];
		}
		unset($installed, $langdb);

		$bingapi = $this->model->getBingAPI();

        $eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/etranslator.js');

		$this->view->editTranslation($row, $original, $avlangs, $ctlangs, $bingapi);
	}


	/******************************/
	/* PREPARE TO ADD TRANSLATION */
	/******************************/
	public function addtrans() {
		$this->edittrans(true);
	}


	/***********************************************/
	/* LOAD ORIGINAL TEXT FROM TRANSLATION ELEMENT */
	/***********************************************/
	private function loadOriginal($category, $element, $elid) {
		$original = new stdClass;
		$original->table = '';
		$original->id_column = '';
		$original->text_column = '';
		$original->text = '';
		$original->longtext = false;

		if ($category == 'config') {
			$key = strtoupper($element);
			if (in_array($key, array('SITENAME', 'METADESC', 'METAKEYS'))) {
				$original->text = eFactory::getElxis()->getConfig($key);
			}
			return $original;
		} else if ($category == 'com_content') {
			switch ($element) {
				case 'category_title': 
					$original->table = '#__categories';
					$original->id_column = 'catid';
					$original->text_column = 'title';
				break;
				case 'category_description':
					$original->table = '#__categories';
					$original->id_column = 'catid';
					$original->text_column = 'description';
					$original->longtext = true;
				break;
				case 'title':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'title';
				break;
				case 'subtitle':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'subtitle';
				break;
				case 'introtext':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'introtext';
					$original->longtext = true;
				break;
				case 'maintext':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'maintext';
					$original->longtext = true;
				break;
				case 'caption':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'caption';
				break;
				case 'metakeys':
					$original->table = '#__content';
					$original->id_column = 'id';
					$original->text_column = 'metakeys';
				break;
				default: break;
			}
		} else if ($category == 'com_emenu') {
			if ($element == 'title') {
				$original->table = '#__menu';
				$original->id_column = 'menu_id';
				$original->text_column = 'title';
			}
		} else if ($category == 'module') {
			if ($element == 'title') {
				$original->table = '#__modules';
				$original->id_column = 'id';
				$original->text_column = 'title';
			} else 	if ($element == 'content') {
				$original->table = '#__modules';
				$original->id_column = 'id';
				$original->text_column = 'content';
				$original->longtext = true;
			}
		} else {
			//do nothing
		}

		if ($original->text_column == '') { //try to guess if it is long text
			if (strpos($element, 'description') !== false) {
				$original->longtext = true;
			} elseif (strpos($element, 'text') !== false) {
				$original->longtext = true;
			}

			return $original;
		}

		$original->text = $this->model->getOriginal($original->table, $original->id_column, $original->text_column, $elid);

		return $original;
	}


	/********************************************************/
	/* DELETE TRANSLATION (THIS IS DIFFERENT FROM THE API!) */
	/********************************************************/
	public function deletetrans() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$trid = isset($_POST['trid']) ? (int)$_POST['trid'] : 0;
		if ($trid < 0) { $trid = 0; }
		if ($trid == 0) {
			$this->view->jsonResponse(1, $eLang->get('TRANS_DELETED'));
			return;
		}

		$row = new translationsDbTable();
		if (!$row->load($trid)) {
			$this->view->jsonResponse(1, $eLang->get('TRANS_NOT_FOUND'));
			return;
		}

		if (preg_match('/^(com\_)/i', $row->category)) {
			$component = strtolower($row->category);
			if ($elxis->acl()->check('component', $component, 'manage') < 1) {
				$this->view->jsonResponse(1, 'You are not allowed to manage component '.$component);
				return;
			}
		}

		if ($row->category == 'module') {
			if (preg_match('/^(mod\_)/i', $row->element)) {
				$module = strtolower($row->element);
				if ($elxis->acl()->check('module', $module, 'manage', $row->elid) < 1) {
					$this->view->jsonResponse(1, 'You are not allowed to edit '.$module.' with instance '.$row->elid.'!');
					return;
				}
			}
		}

		if ($row->category == 'config') {
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') < 1) {
				$this->view->jsonResponse(1, 'You are not allowed to edit Elxis configuration options!');
				return;
			}
		}

		if (!$row->delete()) {
			$this->view->jsonResponse(1, $eLang->get('ACTION_FAILED'));
			return;
		}

		$this->view->jsonResponse(0, $eLang->get('TRANS_DELETED'));
	}

}
	
?>