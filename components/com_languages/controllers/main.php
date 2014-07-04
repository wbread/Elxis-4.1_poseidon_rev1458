<?php 
/**
* @version		$Id: main.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component Languages
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainLngController extends languagesController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/***************************************/
	/* PREPARE TO LIST INSTALLED LANGUAGES */
	/***************************************/
	public function listlangs() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		eFactory::getPathway()->addNode($eLang->get('LANGUAGES_MANAGER'));
		eFactory::getDocument()->setTitle($eLang->get('LANGUAGES_MANAGER').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->listLanguages();
	}


	/*******************************************************/
	/* RETURN LIST OF ROUTES FOR GRID IN XML FORMAT (AJAX) */
	/*******************************************************/
	public function getlangs() {
		$elxis = eFactory::getElxis();

		$xcols = array('identifier', 'name', 'name_eng', 'language', 'region', 'dir', 'files', 'translations');

		$rp = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($rp < 1) { $rp = 10; }
		$elxis->updateCookie('rp', $rp);
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$sortname = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'identifier';
		if (($sortname == '') || !in_array($sortname, $xcols)) { $sortname = 'identifier'; }
		$sortorder = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if (($sortorder == '') || !in_array($sortorder, array('asc', 'desc'))) { $sortorder = 'asc'; }
		$dir = (isset($_POST['dir'])) ? trim($_POST['dir']) : '';
		if ($dir != '') { if (!in_array($dir, array('ltr', 'rtl'))) { $dir = ''; } }

		$temp_rows = $this->model->getInstalledLangs();

		$rows = array();
		$total = 0;
		if ($temp_rows) {
			foreach ($temp_rows as $temp_row) {
				if ($dir != '') {
					if ($dir != $temp_row['dir']) { continue; }
				}
				$total++;
				$rows[] = $temp_row;
			}

			if (count($rows) > 1) {
				$rows = $this->sortLanguages($rows, $sortname, $sortorder);
				$limitstart = 0;
				$maxpage = ceil($total/$rp);
				if ($maxpage < 1) { $maxpage = 1; }
				if ($page > $maxpage) { $page = $maxpage; }
				$limitstart = (($page - 1) * $rp);
				if ($total > $rp) {
					$limitrows = array();
					$end = $limitstart + $rp;
					foreach ($rows as $key => $row) {
						if ($key < $limitstart) { continue; }
						if ($key >= $end) { break; }
						$limitrows[] = $row;
					}
					$rows = $limitrows;
				}
			}
		}

		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: text/xml; charset=utf-8');

		echo "<rows>\n";
		echo '<page>'.$page."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			$flagsdir = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			foreach ($rows as $row) {
				$idtxt = ($row['identifier'] == $elxis->getConfig('LANG')) ? '<strong>'.$row['identifier'].'</strong>' : $row['identifier'];
				$flag = $flagsdir.$row['identifier'].'.png';
				echo '<row id="lng'.$row['identifier'].'">'."\n";
				echo '<cell><![CDATA[<img src="'.$flag.'" alt="'.$row['identifier'].'" border="0" />]]></cell>'."\n";
				echo '<cell><![CDATA['.$idtxt."]]></cell>\n";
				echo '<cell><![CDATA['.$row['name']."]]></cell>\n";
				echo '<cell><![CDATA['.$row['name_eng']."]]></cell>\n";
				echo '<cell>'.$row['language']."</cell>\n";
				echo '<cell>'.$row['region']."</cell>\n";
				echo '<cell>'.$row['dir']."</cell>\n";
				echo '<cell>'.$row['files']."</cell>\n";
				echo '<cell>'.$row['translations']."</cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/*******************/
	/* ORDER LANGUAGES */
	/*******************/
	private function sortLanguages($rows, $sortname, $sortorder) {
		switch ($sortname) {
			case 'files': case 'translations':
				$code = 'if ($a["'.$sortname.'"] != $b["'.$sortname.'"]) {';
				if ($sortorder == 'asc') {
					$code .= 'return ($a["'.$sortname.'"] < $b["'.$sortname.'"] ? -1 : 1); }';
				} else {
					$code .= 'return ($a["'.$sortname.'"] < $b["'.$sortname.'"] ? 1 : -1); }';
				}
				$code .= 'return 0;';
			break;
			default:
				$code = 'if ($a["'.$sortname.'"] != $b["'.$sortname.'"]) {';
				if ($sortorder == 'asc') {
					$code .= 'return strcasecmp($a["'.$sortname.'"], $b["'.$sortname.'"]); }';
				} else {
					$code .= 'return (strcasecmp($a["'.$sortname.'"], $b["'.$sortname.'"]) * -1); }';
				}
				$code .= 'return 0;';
			break;
		}

        $compare = create_function('$a,$b', $code);
        usort($rows, $compare);
        return $rows;
    }


	/*************************************/
	/* PREPARE TO COMPARE LANGUAGE FILES */
	/*************************************/
	public function compare() {
		$eLang = eFactory::getLang();

		$langs = array();
		$files = array();
		$langs_str = trim(filter_input(INPUT_GET, 'langs', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$ilangs = $this->model->getInstalledLangs(true);
		if ($langs_str != '') {
			$langs_arr = explode(',', $langs_str);
			if ($langs_arr && (count($langs_arr) > 0)) {
				foreach ($langs_arr as $lng) {
					if (trim($lng) == '') { continue; }
					if (!isset($ilangs[$lng])) { continue; }
					$ilangs[$lng]['files'] = $this->removeLangPrefix($lng, $ilangs[$lng]['files']);
					$langs[$lng] = $ilangs[$lng];
					if ($ilangs[$lng]['files'] && (count($ilangs[$lng]['files']) > 0)) {
						foreach ($ilangs[$lng]['files'] as $fl) {
							if (!in_array($fl, $files)) { $files[] = $fl; }
						}
					}
				}
			}
		}
		unset($ilangs);

		eFactory::getPathway()->addNode($eLang->get('LANGUAGES_MANAGER'), 'languages:/');
		eFactory::getPathway()->addNode($eLang->get('COMPARE_FILES'));
		eFactory::getDocument()->setTitle($eLang->get('COMPARE_FILES').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->compareLangFiles($files, $langs);
	}


	/*************************/
	/* PREVIEW LANGUAGE FILE */
	/*************************/
	public function viewfile() {
		$eLang = eFactory::getLang();

		$l = filter_input(INPUT_GET, 'l', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$f = base64_decode(filter_input(INPUT_GET, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\/]|[\}]|[\\\])#";
		$l = trim(preg_replace($pat, '', $l));
		$f = trim(preg_replace($pat, '', $f));

		if (($l == '') || !file_exists(ELXIS_PATH.'/language/'.$l.'/') || !is_dir(ELXIS_PATH.'/language/'.$l.'/')) {
			echo '<div class="elx_error">'.$eLang->get('INVALID_LANG')."</div>\n";
			return;
		}
		
		$file = ELXIS_PATH.'/language/'.$l.'/'.$l.'.'.$f;
		if (($f == '') || !file_exists($file)) {
			echo '<div class="elx_error">'.$eLang->get('INVALID_LANG_FILE')."</div>\n";
			return;
		}

		$fs = filesize($file) / 1024;
		$fs = number_format($fs, 2, '.', '');
		$ts = filemtime($file);

		$txt = $eLang->get('FILE').': <strong>language/'.$l.'/'.$l.'.'.$f."</strong><br />\n";
		$txt .= $eLang->get('LAST_MOD').': <strong>'.eFactory::getdate()->formatTS($ts, $eLang->get('DATE_FORMAT_4'), true)."</strong><br />\n";
		$txt .= $eLang->get('FILE_SIZE').': <strong>'.$fs ." KB</strong>\n";

		echo '<div class="elx_info">'.$txt."</div>\n";
		echo "<pre>\n";
		echo htmlspecialchars(file_get_contents($file));
		echo "</pre>\n";
	}


	/*********************************/
	/* PREPARE TO CHECK TRANSLATIONS */
	/*********************************/
	public function check() {
		$eLang = eFactory::getLang();

		$lang1 = filter_input(INPUT_POST, 'lang1', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$lang2 = filter_input(INPUT_POST, 'lang2', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$file = filter_input(INPUT_POST, 'file', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\/]|[\}]|[\\\])#";
		$lang1 = trim(preg_replace($pat, '', $lang1));
		$lang2 = trim(preg_replace($pat, '', $lang2));
		$file = trim(preg_replace($pat, '', $file));

		$rows = array();
		$iLangs = array();
		$errormsg = '';
		$mt1 = 0;
		$mt2 = 0;
		if (($lang1 == '') || ($lang2 == '') || ($lang1 == $lang2)) {
			$errormsg = $eLang->get('SEL_LANGS_COMPARE');
		} elseif (!file_exists(ELXIS_PATH.'/language/'.$lang1.'/')) {
			$errormsg = $eLang->get('INVALID_LANG');
			$lang1 = '';
		} elseif (!file_exists(ELXIS_PATH.'/language/'.$lang2.'/')) {
			$errormsg = $eLang->get('INVALID_LANG');
			$lang2 = '';
		} elseif ($file == '') {
			$errormsg = $eLang->get('SEL_LANG_FILE');
		} else {
			$f1 = ELXIS_PATH.'/language/'.$lang1.'/'.$lang1.'.'.$file;
			$f2 = ELXIS_PATH.'/language/'.$lang2.'/'.$lang2.'.'.$file;
			if (!file_exists($f1)) {
				$errormsg = sprintf($eLang->get('FILE_NEXIST'), '<strong>'.$lang1.'.'.$file.'</strong>');
				$file = 'php';
			} else if (!file_exists($f2)) {
				$errormsg = sprintf($eLang->get('FILE_NEXIST'), '<strong>'.$lang2.'.'.$file.'</strong>');
				$file = 'php';
			} else {
				$mt1 = filemtime($f1);
				$mt2 = filemtime($f2);
				$iLangs = $this->model->getInstalledLangs(true);
				$rows = $eLang->compare($f1, $f2);
				//ksort($rows);				
			}
		}

		eFactory::getPathway()->addNode($eLang->get('LANGUAGES_MANAGER'), 'languages:/');
		eFactory::getPathway()->addNode($eLang->get('CHECK_TRANS'));
		eFactory::getDocument()->setTitle($eLang->get('CHECK_TRANS').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->checkTranslations($rows, $lang1, $lang2, $file, $errormsg, $iLangs, $mt1, $mt2);
	}

}

?>