<?php 
/**
* @version		$Id: main.html.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component Languages
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainLngView extends languagesView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/****************************/
	/* LIST INSTALLED LANGUAGES */
	/****************************/
	public function listLanguages() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('LANGUAGES_MANAGER')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('ilangs', $eLang->get('INSTALLED_LANGS'));
		$grid->setOption('url', $elxis->makeAURL('languages:getlangs.xml', 'inner.php'));
		$grid->setOption('sortname', 'identifier');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->addColumn($eLang->get('FLAG'), 'flag', 60, false, 'center');
		$grid->addColumn($eLang->get('IDENTIFIER'), 'identifier', 100, true, 'auto');
		$grid->addColumn($eLang->get('NAME_LOCAL'), 'name', 140, true, 'auto');
		$grid->addColumn($eLang->get('NAME_ENGLISH'), 'name_eng', 140, true, 'auto');
		$grid->addColumn($eLang->get('LANGUAGE'), 'language', 100, true, 'auto');
		$grid->addColumn($eLang->get('REGION'), 'region', 100, true, 'auto');
		$filters = array('' => '- '.$eLang->get('ANY').' -', 'ltr' => 'LTR', 'rtl' => 'RTL');
		$grid->addColumn($eLang->get('DIRECTION'), 'dir', 100, true, 'auto', $filters);
		$grid->addColumn($eLang->get('FILES'), 'files', 100, true, 'center');
		$grid->addColumn($eLang->get('DB_TRANSLATIONS'), 'translations', 120, true, 'center');
		$grid->addButton($eLang->get('COMPARE_FILES'), 'compare', '', 'langsaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('TOGGLE_SELECTED'), 'togglerows', 'toggle', 'langsaction');
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function langsaction(task, grid) {
			if (task == 'compare') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else if (nsel < 2) {
					alert('<?php echo addslashes($eLang->get('SELECT_LEAST2')); ?>');
					return false;
				} else if (nsel > 6) {
					alert('<?php echo addslashes($eLang->get('COMPARE_UPTO6')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('languages:compare.html'); ?>?langs=';
					var items = $('.trSelected',grid);
					for(i=0; i < nsel; i++) { newurl += items[i].id.substr(6)+",";}
					location.href = newurl;
				}
			} else if (task == 'togglerows') {
				$('tr',grid).toggleClass('trSelected');
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
		echo "<br /><br />\n";
		$this->checkForm();
	}


	/***************************/
	/* CHECK TRANSLATIONS FORM */
	/***************************/
	private function checkForm($lang1='', $lang2='', $file='php') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$langs = $eFiles->listFolders('language/');
		$lfiles = $eFiles->listFiles('language/en/', '.php');

		if ($lang1 == '') { $lang1 = 'en'; }
		if ($lang2 == '') { $lang2 = $langs[0]; }
		if ($file == '') { $file = 'php'; }
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmclngs',
			'action' => $elxis->makeAURL('languages:check.html'),
			'idprefix' => 'clng',
			'label_width' => 130,
			'label_align' => 'left'
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('CHECK_TRANS'));
		$form->addNote($eLang->get('CHECK_TRANS_DESC'), 'elx_sminfo');
		$form->openRow();
		$options = array();
		foreach ($langs as $lng) { $options[] = $form->makeOption($lng, $lng); }
		$form->addSelect('lang1', $eLang->get('LANGUAGE').' 1', $lang1, $options, array('dir' => 'ltr'));
		$form->addSelect('lang2', $eLang->get('LANGUAGE').' 2', $lang2, $options, array('dir' => 'ltr'));
		$options = array();
		foreach ($lfiles as $lfile) {
			$lf = preg_replace('/^(en.)/', '', $lfile);
			if ($lf == 'php') {
				$options[] = $form->makeOption($lf, $eLang->get('MAIN_LANG_FILE'));
			} else {
				$options[] = $form->makeOption($lf, $lf);
			}
		}
		$form->addSelect('file', $eLang->get('FILE'), $file, $options, array('dir' => 'ltr', 'style' => 'width:100px; overflow:hidden;'));
		$form->addButton('lngcbtn', $eLang->get('CHECK'));
		$form->closeRow();
		$form->closeFieldset();
		$form->render();
		unset($form);
	}


	/********************************************/
	/* DISPLAY LANGUAGE FILES COMPARISSON TABLE */
	/********************************************/
	public function compareLangFiles($files, $langs) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		echo '<h2>'.$eLang->get('COMPARE_FILES')."</h2>\n";

		if (!$langs) {
			echo '<div class="elx_error">'.$eLang->get('NO_LANGS_COMPARE')."</div>\n";
			return;
		}
		if (!$files) {
			echo '<div class="elx_error">'.$eLang->get('NO_FILES_COMPARE')."</div>\n";
			return;
		}

		$c = count($langs) + 2;

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="'.$c.'">'.$eLang->get('LANG_FILES_COMP').'</th></tr>'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter" width="40">#'."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('FILE')."</th>\n";
		foreach ($langs as $lng) {
			echo '<th class="elx_th_subcenter" dir="ltr">'.$lng['identifier'].' - '.$lng['name']."</th>\n";
		}
		echo "</tr>\n";

		$k = 0;
		$i = 1;
		$found_icon = $elxis->icon('tick', 16);
		$not_found_icon = $elxis->icon('error', 16);
		$hurl = $elxis->makeAURL('languages:view.html', 'inner.php');
		eFactory::getDocument()->addDocReady('$(".lightlangfile").colorbox({iframe:true, width:800, height:400});');
		eFactory::getDocument()->addStyle('.cboxIframe { overflow:auto; }');
		foreach ($files as $file) {
			echo '<tr class="elx_tr'.$k.'">'."\n";
			echo '<td class="elx_td_center">'.$i."</td>\n";
			$txt = ($file == 'php') ? '<strong>'.$eLang->get('MAIN_LANG_FILE').'</strong>' : $file;
			echo '<td>'.$txt."</td>\n";
			foreach ($langs as $lng) {
				if (in_array($file, $lng['files'])) {
					$txt = '<a href="'.$hurl.'?l='.$lng['identifier'].'&amp;f='.base64_encode($file).'" title="view" target="_blank" class="lightlangfile"><img src="'.$found_icon.'" alt="ok" border="0" /></a>';
				} else {
					$txt = '<img src="'.$not_found_icon.'" alt="not found" border="0" />';
				}
				echo '<td class="elx_td_center">'.$txt."</td>\n";
			}
			echo "</tr>\n";
			$k = 1 - $k;
			$i++;
		}
		echo "</table>\n";
		echo "</div>\n";
		echo "<br /><br />\n";
		$this->checkForm();
	}


	/***************************/
	/* HTML CHECK TRANSLATIONS */
	/***************************/
	public function checkTranslations($rows, $lang1, $lang2, $file, $errormsg, $iLangs, $mt1, $mt2) {
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('CHECK_TRANS')."</h2>\n";
		$this->checkForm($lang1, $lang2, $file);
		if ($errormsg != '') {
			echo '<div class="elx_error">'.$errormsg."</div>\n";
			return;
		} elseif (!$rows) {
			echo '<div class="elx_error">'.$eLang->get('NO_RESULTS')."</div>\n";
			return;
		}

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="3">'.$eLang->get('CHECK_TRANS').'</th></tr>'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('INFORMATION')."</th>\n";
		echo '<th class="elx_th_sub">'.$lang1.' - '.$iLangs[$lang1]['name']."</th>\n";
		echo '<th class="elx_th_sub">'.$lang2.' - '.$iLangs[$lang2]['name']."</th>\n";
		echo "</tr>\n";
		echo '<tr class="elx_tr0">'."\n";
		echo '<td>'.$eLang->get('LAST_MOD')."</td>\n";
		echo '<td>'.eFactory::getdate()->formatTS($mt1, $eLang->get('DATE_FORMAT_4'), true)."</td>\n";
		echo '<td>'.eFactory::getdate()->formatTS($mt2, $eLang->get('DATE_FORMAT_4'), true)."</td>\n";
		echo "</tr>\n";
		echo "<tr>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('LANG_STRING').' <span dir="ltr">('.count($rows).")</span></th>\n";
		echo '<th class="elx_th_sub">'.$lang1.' - '.$iLangs[$lang1]['name']."</th>\n";
		echo '<th class="elx_th_sub">'.$lang2.' - '.$iLangs[$lang2]['name']."</th>\n";
		echo "</tr>\n";

		$k = 0;
		foreach ($rows as $file => $row) {
			$css = (($row[0] == '') || ($row[1] == '')) ? 'elx_trx' : 'elx_tr'.$k;
			echo '<tr class="'.$css.'">'."\n";
			echo '<td>'.$file."</td>\n";
			echo '<td>'.$row[0]."</td>\n";
			echo '<td>'.$row[1]."</td>\n";
			echo "</tr>\n";
			$k = 1 - $k;
		}
		echo "</table>\n";
		echo "</div>\n";
	}

}

?>