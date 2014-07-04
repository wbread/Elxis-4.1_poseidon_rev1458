<?php 
/**
* @version		$Id: single.html.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class singleEtranslatorView extends etranslatorView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**************************/
	/* SHOW TRANSLATIONS LIST */
	/**************************/
	public function listTrans($trcats, $trelements, $ilangs) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('TRANSLATOR')."</h2>\n";
		if ($elxis->getConfig('MULTILINGUISM') == 0) {
			echo '<div class="elx_warning">'.$eLang->get('MLCONTENT_DISABLED')."</div>\n";
		}

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('ltrans', $eLang->get('TRANS_MANAGEMENT'));
		$grid->setOption('url', $elxis->makeAURL('etranslator:single/gettrans.xml', 'inner.php'));
		$grid->setOption('sortname', 'category');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 50, false, 'center');
		$grid->addColumn($eLang->get('CATEGORY'), 'category', 140, true, 'auto');
		$grid->addColumn($eLang->get('ELEMENT'), 'element', 140, true, 'auto');
		$grid->addColumn($eLang->get('ID'), 'elid', 50, true, 'center');
		if (eFactory::getDocument()->getContentType() == 'application/xhtml+xml') {
			$txt = '<![CDATA['.$eLang->get('ORIGINAL_TEXT').' <img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$elxis->getConfig('LANG').'.png" ';
			$txt .= 'alt="'.$elxis->getConfig('LANG').'" title="'.$elxis->getConfig('LANG').'" align="top" />]]>';
		} else {
			$txt = $eLang->get('ORIGINAL_TEXT').' <img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$elxis->getConfig('LANG').'.png" ';
			$txt .= 'alt="'.$elxis->getConfig('LANG').'" title="'.$elxis->getConfig('LANG').'" align="top" />';
		}
		$grid->addColumn($txt, 'originaltext', 190, false, 'auto');
		unset($txt);
		$grid->addColumn($eLang->get('LANGUAGE'), 'language', 90, true, 'center');
		$grid->addColumn($eLang->get('TRANSLATION'), 'translation', 190, false, 'auto');
		$grid->addButton($eLang->get('NEW'), 'addtrans', 'add', 'transaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('EDIT'), 'edittrans', 'edit', 'transaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'deletetrans', 'delete', 'transaction');
		$grid->addSeparator();
		$filters = array('' => '- '.$eLang->get('ANY').' -');
		if ($trcats) {
			foreach ($trcats as $trcat) { $filters[$trcat] = $trcat; }
		}
		$grid->addFilter($eLang->get('CATEGORY'), 'category', $filters, '');
		$filters = array('' => '- '.$eLang->get('ANY').' -');
		foreach ($trelements as $trelement => $txt) { $filters[$trelement] = $txt; }
		$grid->addFilter($eLang->get('ELEMENT'), 'element', $filters, '');
		$filters = array('' => '- '.$eLang->get('ANY').' -');
		if ($ilangs) {
			foreach ($ilangs as $lng => $ilang) { $filters[$lng] = $lng.' - '.$ilang['name']; }
		}
		$grid->addFilter($eLang->get('LANGUAGE'), 'language', $filters, '');
		$grid->addSearch($eLang->get('ID'), 'elid', true);
		unset($filters);

		eFactory::getDocument()->addStyle('.cboxIframe { overflow:auto; }');
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function transaction(task, grid) {
			if ((task == 'addtrans') || (task == 'edittrans')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var trid = parseInt(items[0].id.substr(3), 10);
					if (task == 'addtrans') {
						var newurl = '<?php echo $elxis->makeAURL('etranslator:single/add.html', 'inner.php'); ?>?trid='+trid;
					} else {
						var newurl = '<?php echo $elxis->makeAURL('etranslator:single/edit.html', 'inner.php'); ?>?trid='+trid;
					}
					$.colorbox({iframe:true, width:940, height:450, href:newurl});
				}
			} else if (task == 'deletetrans') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var trid = parseInt(items[0].id.substr(3), 10);
					if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
						var edata = {'trid': trid};
						var eurl = '<?php echo $elxis->makeAURL('etranslator:single/delete', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var jsonObj = JSON.parse(xreply);
							if (parseInt(jsonObj.error, 10) > 0) {
								alert(jsonObj.errormsg);
							} else {
								$("#ltrans").flexReload();
							}
						}
						elxAjax('POST', eurl, edata, null, null, successfunc, null);
					}
				}
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
	}


	/*******************************************/
	/* PICK LANGUAGE LAYER FOR NEW TRANSLATION */
	/*******************************************/
	private function pickLanguage($avlangs, $ctlangs) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$flags_base = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
		$deflang = $elxis->getConfig('LANG');
		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';

		echo '<div id="etrans_picklang_cover" style="position:absolute; top:0px; left:0px; width:100%; height:100%; margin:0; padding:0; background-color:#ffffff;">'."\n";
		echo '<h3>'.$eLang->get('PICK_LANGUAGE')."</h3>\n";
		echo '<div dtyle="margin:10px 0; padding:0;">'."\n";
		foreach ($avlangs as $lng => $linfo) {
			if ($lng == $deflang) {
				$can_select = false;
			} else if (is_array($ctlangs) && in_array($lng, $ctlangs)) {
				$can_select = false;
			} else {
				$can_select = true;
			}

			echo '<div style="float:'.$float.'; width:150px; margin:0 10px 10px 10px; padding: 4px;">'."\n";
			if ($can_select) {
				echo '<a href="javascript:void(null);" onclick="etrans_picklang(\''.$lng.'\');" style="text-decoration:none;" title="'.$linfo['NAME_ENG'].'">';
			}
			echo '<img src="'.$flags_base.$lng.'.png" alt="'.$lng.'" align="top" style="border:none;" /> '.$lng.' - '.$linfo['NAME'];
			if ($can_select) { echo '</a>'; }
			echo "</div>\n";
		}
		echo '<div style="clear:both;"></div>'."\n";
		echo "</div>\n";
		echo "</div>\n";
	}


	/*****************************/
	/* ADD/EDIT TRANSLATION HTML */
	/*****************************/
	public function editTranslation($row, $original, $avlangs, $ctlangs, $bingapi) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$cinfo = $eLang->getallinfo($row->language);
		$islongtext = ($original->longtext === true) ? 1: 0;

		if (!$row->trid) {
			$pgtitle = $eLang->get('ADD_TRANSLATION');
		} else {
			$pgtitle = $eLang->get('EDIT_TRANSLATION');
		}

		if (!$row->trid) { $this->pickLanguage($avlangs, $ctlangs); }

		echo '<form name="fmedtrans" action="" onsubmit="return false;">'."\n";
		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="2">'.$pgtitle.'</th></tr>'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter">'."\n";
		echo $eLang->get('ORIGINAL_TEXT')."\n";
		echo ' <img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$elxis->getConfig('LANG').'.png" alt="'.$elxis->getConfig('LANG').'" title="'.$elxis->getConfig('LANG').'" align="top" />'."\n";
		echo "</th>\n";
		echo '<th class="elx_th_subcenter" width="50%">'."\n";
		echo $eLang->get('TRANSLATION')."\n";
		if ($row->trid) {
			echo ' <img id="etrans_flag" src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$row->language.'.png" alt="'.$row->language.'" title="'.$row->language.'" align="top" />'."\n";
		} else {
			echo ' <img id="etrans_flag" src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/un.png" alt="un" title="un" align="top" />'."\n";
		}
		echo "</th>\n</tr>\n";
		echo '<tr>'."\n";
		echo "<td>\n";
		echo '<a href="javascript:void(null);" title="'.$eLang->get('COPY').'" onclick="etrans_copy()">'.$eLang->get('COPY')."</a> | \n";
		echo '<a href="javascript:void(null);" title="'.$eLang->get('COPY_HTML').'" onclick="etrans_copyhtml()">'.$eLang->get('COPY_HTML')."</a>\n";
		echo "</td>\n";
		echo "<td>\n";
		echo '<a href="javascript:void(null);" title="'.$eLang->get('SAVE').'" onclick="etrans_sisave('.$islongtext.')" style="text-decoration:none;">'.$eLang->get('SAVE')."</a> | \n";
		echo '<a href="javascript:void(null);" title="'.$eLang->get('SWITCH_WRITE_DIR').'" onclick="etrans_switchdir()" style="text-decoration:none;">LTR/RTL</a>'."\n";
		if ($original->longtext == false) {
			if ($bingapi != '') {
				echo ' | <a href="javascript:void(null);" title="'.$eLang->get('AUTO_TRANS').' (Bing)" onclick="etrans_sibing()" style="text-decoration:none;">'.$eLang->get('AUTO_TRANS')."</a>\n";
			} else {
				echo ' | <span style="color:#666;">'.$eLang->get('AUTO_TRANS')."</span>\n";
			}
		}
		echo "</td>\n</tr>\n";
		echo '<tr>'."\n";
		echo '<td id="etroriginal" dir="'.$eLang->getinfo('DIR').'" style="direction:'.$eLang->getinfo('DIR').'; vertical-align:top;">';
		if ($original->text) {
			echo $original->text;
		} else {
			echo '<em>'.$eLang->get('NOT_AVAILABLE')."</em>\n";
		}
		echo "</td>\n";
		echo '<td style="vertical-align:top;">'."\n";
		if ($original->longtext == true) {
			echo '<textarea id="etrtranslation" name="translation" cols="50" rows="10" dir="'.$cinfo['DIR'].'" style="width:95%" onchange="trans_marksiunsaved(this);">'.$row->translation.'</textarea>';
		} else {
			echo '<input type="text" id="etrtranslation" name="translation" value="'.$row->translation.'" size="50" maxlength="255" dir="'.$cinfo['DIR'].'" style="width:95%" onchange="trans_marksiunsaved(this);" />';
		}
		echo "</td>\n";
		echo "</tr>\n";
		echo "</table>\n";
		echo "</div>\n";
		echo '<div id="etrans_combase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('etranslator:/', 'inner.php')."</div>\n";
		echo '<div id="etrans_elxbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->secureBase()."</div>\n";
		echo '<input type="hidden" name="trid" id="etrans_trid" dir="ltr" value="'.intval($row->trid).'" />'."\n";
		echo '<input type="hidden" name="category" id="etrans_category" dir="ltr" value="'.$row->category.'" />'."\n";
		echo '<input type="hidden" name="element" id="etrans_element" dir="ltr" value="'.$row->element.'" />'."\n";
		echo '<input type="hidden" name="elid" id="etrans_elid" dir="ltr" value="'.$row->elid.'" />'."\n";
		echo '<input type="hidden" name="language" id="etrans_language" dir="ltr" value="'.$row->language.'" />'."\n";
		echo '<input type="hidden" name="bingapi" id="etrans_bingapi" dir="ltr" value="'.$bingapi.'" />'."\n";
		echo '<input type="hidden" name="deflang" id="etrans_deflang" dir="ltr" value="'.$elxis->getConfig('LANG').'" />'."\n";
		echo "</form>\n";
	}


	/*****************/
	/* JSON RESPONSE */
	/*****************/
	public function jsonResponse($error, $message) {
		$response = array (
			'error' => $error,
			'message' => $message
		);

		$this->ajaxHeaders('application/json');
		echo json_encode($response);
		exit();
	}

}

?>