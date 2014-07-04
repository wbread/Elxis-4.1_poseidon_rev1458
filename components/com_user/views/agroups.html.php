<?php 
/**
* @version		$Id: agroups.html.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class agroupsUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************/
	/* SHOW GROUPS LIST */
	/********************/
	public function listgroups() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('USER_GROUPS')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lgroups', $eLang->get('USER_GROUPS'));
		$grid->setOption('url', $elxis->makeAURL('user:groups/getgroups.xml', 'inner.php'));
		$grid->setOption('sortname', 'level');
		$grid->setOption('sortorder', 'desc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('ID'), 'gid', 60, true, 'center');
		$grid->addColumn($eLang->get('ACCESS_LEVEL'), 'level', 160, true, 'center');
		$grid->addColumn($eLang->get('GROUP'), 'groupname', 200, false, 'auto');
		$grid->addColumn($eLang->get('MEMBERS'), 'members', 120, false, 'center');
		$grid->addButton($eLang->get('NEW'), 'addgroup', 'add', 'groupsaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('EDIT'), 'editgroup', 'edit', 'groupsaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'deletegroup', 'delete', 'groupsaction');
		$grid->addSeparator();
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function groupsaction(task, grid) {
			if (task == 'addgroup') {
				var newurl = '<?php echo $elxis->makeAURL('user:groups/edit.html'); ?>?gid=0';
				location.href = newurl;
			} else if (task == 'editgroup') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('user:groups/edit.html'); ?>?gid=';
					var items = $('.trSelected',grid);
					var groupid = parseInt(items[0].id.substr(3));
					newurl += groupid;
					location.href = newurl;
				}
			} else if (task == 'deletegroup') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
					var items = $('.trSelected',grid);
					var groupid = parseInt(items[0].id.substr(3));
					var edata = {'gid': groupid};
					var eurl = '<?php echo $elxis->makeAURL('user:groups/deletegroup', 'inner.php'); ?>';
					var successfunc = function(xreply) {
						var rdata = new Array();
						rdata = xreply.split('|');
						var rok = parseInt(rdata[0]);
						if (rok == 1) {
							$("#lgroups").flexReload();
						} else {
							alert(rdata[1]);
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


	/******************/
	/* EDIT GROUP HTML */
	/******************/
	public function editGroup($row, $tree, $acllist, $readonly) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$action = $elxis->makeAURL('user:groups/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'egr',
			'label_width' => 220,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('GROUP_DETAILS'));
		$form->openFieldset($eLang->get('GROUP_DETAILS'));
		if ($readonly) {
			$form->addNote($eLang->get('CNOT_MOD_GROUP'), 'elx_smwarning');
			$form->addInfo($eLang->get('ID'), $row['gid']);
			$form->addInfo($eLang->get('GROUP'), $row['groupname']);
			$form->addInfo($eLang->get('ACCESS_LEVEL'), $row['level']);
		} else {
			$form->addInfo($eLang->get('ID'), $row['gid']);
			$form->addText('groupname', $row['groupname'], $eLang->get('GROUP'), array('required' => 1, 'dir' => 'ltr', 'maxlength' => 60));
			$max = $elxis->acl()->getLevel() - 1;
			if ($max > 99) { $max = 99; }
			$form->addSlider('level', $row['level'], $eLang->get('ACCESS_LEVEL'), array('required' => 1, 'width' => 100, 'min' => 2, 'max' => $max));
		}

		$form->addInfo($eLang->get('MEMBERS'), $row['members']);
		$form->addHidden('gid', $row['gid']);
		$form->addHidden('task', '');
		$form->closeFieldset();

		if ($tree) {
			$txt = '';
			foreach ($tree as $item) { $txt .= $item->treename."<br />\n"; }
			$form->openFieldset($eLang->get('GROUPS_HIERARCHY_TREE'));
			$form->addInfo('', $txt);
			unset($txt);
			$form->closeFieldset();
		}

		$form->addNote('<strong>'.$eLang->get('HELP').'</strong><br />'.$eLang->get('GROUPS_GENERIC_INFO'), 'elx_info');
		$form->closeTab();

		$form->openTab($eLang->get('ACCESS'));
		if ($elxis->acl()->check('com_user', 'acl' , 'manage') < 1) {
			$form->addNote($eLang->get('NOTALLOWACCITEM'), 'elx_smwarning');
		} else if ($row['gid'] == 0) {
			$form->addNote($eLang->get('FIRST_SAVE_GROUP'), 'elx_smwarning');
		} else {
			$buffer = '<div class="elx_tbl_wrapper">'."\n";
			$buffer .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
			$buffer .= '<tr><th colspan="5">'.sprintf($eLang->get('GROUP_ACCESS_SITE'), $row['groupname']).'</th></tr>'."\n";
			$buffer .= "<tr>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('CATEGORY')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('ELEMENT')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('IDENTITY')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('ACTION')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('ACCESS')."</th>\n";
			$buffer .= "</tr>\n";			
			if ($acllist) {
				$k = 0;
				foreach ($acllist as $cat => $s1) {
					foreach ($s1 as $elem => $s2) {
						foreach ($s2 as $iden => $s3) {
							foreach ($s3 as $action => $s4) {
								if ($s4[0] == 0) {
									$allowed_txt = '<span style="color:#FF0000;">'.$eLang->get('DENIED').'</span>';
								} elseif ($s4[0] == 1) {
									$allowed_txt = '<span style="color:#008000;">'.$eLang->get('ALLOWED').'</span>';
								} else if ($s4[0] == 2) {
									$allowed_txt = '<span style="color:#008000;">'.$eLang->get('ALLOWED_TO_ALL').'</span>';
								} else {
									$allowed_txt = $s4[0];
								}

								$elem_txt = $eLang->silentGet($elem, true);
								$action_txt = $eLang->silentGet($action, true);
								$buffer .= '<tr class="elx_tr'.$k.'">'."\n";
								$buffer .= '<td>'.$cat.'</td><td>'.$elem_txt.'</td><td class="elx_td_center">'.$iden.'</td><td>'.$action_txt.'</td><td>'.$allowed_txt."</td>\n";
								$buffer .= "</tr>\n";
								$k = 1 - $k;
							}
						}
					}
				}
			} else {
				$buffer .= '<tr class="elx_tr0"><td colspan="5">No items found!</td></tr>'."\n";
			}
			$buffer .= "</table>\n";
			$buffer .= "</div>\n";
			$form->addHTML($buffer);
			unset($buffer);
		}
		$form->closeTab();

		if (intval($row['gid']) > 0) {
			echo '<h1>'.$eLang->get('EDIT_GROUP').' '.$row['groupname']."</h1>\n";
		} else {
			echo '<h1>'.$eLang->get('NEW_GROUP')."</h1>\n";
		}

		$form->render();
		unset($form);
	}

}

?>