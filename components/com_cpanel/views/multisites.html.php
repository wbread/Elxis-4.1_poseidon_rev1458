<?php 
/**
* @version		$Id: multisites.html.php 1445 2013-05-22 18:06:05Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class multisitesCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/******************************/
	/* LIST AND MANAGE MULTISITES */
	/******************************/
	public function listSites($rows) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function enableMultisites() {
			if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
				location.href = '<?php echo $elxis->makeAURL('cpanel:multisites/enable.html', 'inner.php'); ?>';
			}
		}
		function disableMultisites() {
			if (confirm('<?php echo addslashes($eLang->get('DISABLE_MULTISITES_WARN')); ?>')) {
				location.href = '<?php echo $elxis->makeAURL('cpanel:multisites/disable.html', 'inner.php'); ?>';
			}
		}
		function deleteMultisite(mid) {
			if (mid < 2) { return false; }
			if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
				location.href = '<?php echo $elxis->makeAURL('cpanel:multisites/delete.html', 'inner.php'); ?>?id='+mid;
			}
		}
		/* ]]> */
		</script>

<?php 
		echo '<h2>'.$eLang->get('MULTISITES')."</h2>\n";
		echo '<div class="elx_sminfo">'.$eLang->get('MULTISITES_WARN');
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			echo '<br />'.sprintf($eLang->get('MAN_MULTISITES_ONLY'), '<strong>1</strong>');
		}
		echo "</div>\n";

		if ($rows) {
			if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE == 1)) {
				echo '<div style="margin:10px 0; padding:0 20px;"><a href="javascript:void(null);" onclick="disableMultisites()" class="elx_button-22"><span>'.$eLang->get('DISABLE_MULTISITES')."</span></a></div>\n";
			}
		}

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="6">'.$eLang->get('MULTISITES').'</th></tr>'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter" width="60">'.$eLang->get('ID')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('NAME')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('URL_ID')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACTIVE')."</th>\n";
		echo '<th class="elx_th_sub">URL'."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACTIONS')."</th>\n";
		echo "</tr>\n";

		$k = 0;
		$ok_icon = $elxis->icon('tick', 16);
		$no_icon = $elxis->icon('error', 16);
		$add_icon = $elxis->icon('add', 16);
		$edit_icon = $elxis->icon('edit', 16);
		$del_icon = $elxis->icon('delete', 16);
		$edit_url = $elxis->makeAURL('cpanel:multisites/edit.html', 'inner.php');

		if ($rows) {
			$manage_only_txt = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE == 1)) {
				eFactory::getDocument()->addDocReady('$(".newmultisite").colorbox({iframe:true, width:880, height:520});');
				eFactory::getDocument()->addDocReady('$(".editmultisite").colorbox({iframe:true, width:800, height:380});');
			}

			foreach ($rows as $row) {
				if ($row->active == true) {
					$acticon = '<img src="'.$ok_icon.'" alt="'.$eLang->get('YES').'" title="'.$eLang->get('YES').'" border="0" />';
				} else {
					$acticon = '<img src="'.$no_icon.'" alt="'.$eLang->get('YES').'" title="'.$eLang->get('NO').'" border="0" />';
				}
				echo '<tr class="elx_tr'.$k.'">'."\n";
				echo '<td class="elx_td_center">'.$row->id."</td>\n";
				if ($row->current == true) {
					echo '<td><strong>'.$row->name."</strong></td>\n";
				} else {
					echo '<td>'.$row->name."</td>\n";
				}
				echo '<td>'.$row->folder."</td>\n";
				echo '<td class="elx_td_center">'.$acticon."</td>\n";
				if ($row->active == true) {
					echo '<td><a href="'.$row->url.'" target="_blank">'.$row->url."</a></td>\n";
				} else {
					echo '<td>'.$row->url."</td>\n";
				}
				echo '<td class="elx_td_center">'."\n";
				if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE == 1)) {
					echo '<a href="'.$edit_url.'?id=0" title="'.$eLang->get('ADD').'" target="_blank" class="newmultisite"><img src="'.$add_icon.'" alt="add" border="0" /></a> &#160; '."\n";
					echo '<a href="'.$edit_url.'?id='.$row->id.'" title="'.$eLang->get('EDIT').' '.$row->name.'" target="_blank" class="editmultisite"><img src="'.$edit_icon.'" alt="edit" border="0" /></a> &#160; '."\n";
					if ($row->id > 1) {
						echo '<a href="javascript:void(null);" title="'.$eLang->get('DELETE').'" onclick="deleteMultisite('.$row->id.');"><img src="'.$del_icon.'" alt="delete" border="0" /></a>';
					} else {
						echo '<img src="'.$del_icon.'" alt="delete" border="0" title="You can not delete this site" style="opacity:0.5" />'."\n";
					}
				} else {
					echo '<img src="'.$add_icon.'" alt="add" border="0" title="'.$manage_only_txt.'" style="opacity:0.5" /> &#160; '."\n";
					echo '<img src="'.$edit_icon.'" alt="edit" border="0" title="'.$manage_only_txt.'" style="opacity:0.5" /> &#160; '."\n";
					echo '<img src="'.$del_icon.'" alt="delete" border="0" title="'.$manage_only_txt.'" style="opacity:0.5" />'."\n";
				}
				echo "</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
			}
		} else {
			echo '<tr class="elx_trx">'."\n";
			echo '<td colspan="6" class="elx_td_center">'."\n";
			echo $eLang->get('MULTISITES_DISABLED')." &#160; \n";
			echo '<a href="javascript:void(null);" onclick="enableMultisites()">'.$eLang->get('ENABLE')."</a>\n";
			echo "</td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";
		echo "</div>\n";
	}


	/***********************/
	/* EDIT MULTISITE HTML */
	/***********************/
	public function editMultisite($row, $dbtypes, $newid, $importers) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmmsite',
			'action' => $elxis->makeAURL('cpanel:multisites/save.html', 'inner.php'),
			'idprefix' => 'ms',
			'label_width' => 160,
			'label_align' => 'left'
		);

		$form = new elxisForm($formOptions);
		if ($row->id > 0) {
			$form->openFieldset($eLang->get('EDIT'));
			$form->addInfo($eLang->get('ID'), $row->id);
		} else {
			$form->openFieldset($eLang->get('NEW'));
		}
		$form->addText('name', $row->name, $eLang->get('NAME'), array('required' => 1, 'dir' => 'ltr', 'size' => 40));
		if ($row->id == 1) {
			$form->addText('folder', $row->folder, $eLang->get('URL_ID'), array('forcedir' => 'ltr', 'size' => 20, 'readonly' => 1, 'maxlength' => 20));
		} else {
			$form->addText('folder', $row->folder, $eLang->get('URL_ID'), array('forcedir' => 'ltr', 'size' => 20, 'required' => 1, 'maxlength' => 20, 'tip'=> $eLang->get('LOWER_ALPHANUM')));
		}
		if ($row->id > 0) {
			$form->addInfo('URL', $row->url);
		}
		$form->addYesNo('active', $eLang->get('ACTIVE'), $row->active);

		if ($row->id == 0) {
			$form->closeFieldset();
			$form->openFieldset($eLang->get('DATABASE'));
			$options = array();
			foreach ($dbtypes as $dbtype => $dbtypetxt) {
				$options[] = $form->makeOption($dbtype, $dbtypetxt);
			}
			$form->openRow();

			if ($importers) {
				$import_txt = $eLang->get('IMPORT_DATA').' ('.implode(', ', $importers).')';
			} else {
				$import_txt = $eLang->get('IMPORT_DATA');
			}

			$form->addSelect('db_type', $eLang->get('DB_TYPE'), $elxis->getConfig('DB_TYPE'), $options, array('dir' => 'ltr'));
			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('NO'));
			$options[] = $form->makeOption(1, $eLang->get('YES'));
			$options[] = $form->makeOption(2, $eLang->get('YES').' + '.$eLang->get('CONTENT'));
			$form->addSelect('db_import', $import_txt, 1, $options);
			$form->closeRow();
			$form->openRow();
			$form->addText('db_host', $elxis->getConfig('DB_HOST'), $eLang->get('HOST'), array('dir' => 'ltr'));
			$form->addNumber('db_port', $elxis->getConfig('DB_PORT'), $eLang->get('PORT'), array('size' => 5, 'maxlength' => 5));
			$form->closeRow();
			$form->openRow();
			$form->addText('db_name', $elxis->getConfig('DB_NAME'), $eLang->get('DB_NAME'), array('dir' => 'ltr'));
			$form->addText('db_prefix', 'elx'.$newid.'_', $eLang->get('TABLES_PREFIX'), array('required' => 1, 'dir' => 'ltr', 'size' => 10, 'maxlength' => 10));
			$form->closeRow();
			$form->openRow();
			$form->addText('db_user', $elxis->getConfig('DB_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
			$form->addPassword('db_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr'));
			$form->closeRow();
			$form->addText('db_dsn', $elxis->getConfig('DSN'), 'DSN', array('dir' => 'ltr', 'size' => 60));
			$form->addText('db_scheme', $elxis->getConfig('DB_SCHEME'), $eLang->get('SCHEME'), array('dir' => 'ltr', 'size' => 60));
		}

		$form->addButton('mscbtn', $eLang->get('SAVE'));
		$form->closeFieldset();
		$form->addHidden('id', $row->id);
		$form->render();
	}

}

?>