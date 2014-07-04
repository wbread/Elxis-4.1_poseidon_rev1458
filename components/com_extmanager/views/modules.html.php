<?php 
/**
* @version		$Id: modules.html.php 1314 2012-10-03 15:53:04Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class modulesExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*********************/
	/* SHOW MODULES LIST */
	/*********************/
	public function listmodules($positions) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		echo '<h2>'.$eLang->get('MODULES')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lmods', $eLang->get('MODULES'));
		$grid->setOption('url', $elxis->makeAURL('extmanager:modules/getmodules.xml', 'inner.php'));
		$grid->setOption('sortname', 'position');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('ID'), 'id', 40, true, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'title', 140, true, 'auto');
		$grid->addColumn($eLang->get('MODULE'), 'module', 140, true, 'auto');
		$grid->addColumn($eLang->get('VERSION'), 'version', 80, true, 'center');
		$grid->addColumn($eLang->get('PUBLISHED'), 'published', 90, true, 'center');
		$grid->addColumn($eLang->get('POSITION'), 'position', 90, true, 'auto');
		$grid->addColumn($eLang->get('ORDERING'), 'ordering', 100, true, 'center');
		$grid->addColumn($eLang->get('REORDER'), 'reorder', 100, false, 'center');
		$grid->addColumn($eLang->get('ACCESS'), 'access', 110, false, 'auto');
		$grid->addColumn($eLang->get('SECTION'), 'section', 100, false, 'auto');

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) {
			$grid->addButton($eLang->get('NEW'), 'addmodule', 'add', 'modsaction');
			$grid->addSeparator();
		}

		if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'editmodule', 'edit', 'modsaction');
			$grid->addSeparator();
			$grid->addButton($eLang->get('PUBLISH'), 'publishmodule', 'tick', 'modsaction');
			$grid->addSeparator();
		}

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) {
			$grid->addButton($eLang->get('COPY'), 'copymodule', 'copy', 'modsaction');
			$grid->addSeparator();
			if ($is_subsite) {
				$grid->addButton($eLang->get('DELETE'), 'deletemodule', 'trash', 'modsaction');
				$grid->addSeparator();	
			} else {
				$grid->addButton($eLang->get('UPDATE'), 'updatemodule', 'download', 'modsaction');
				$grid->addSeparator();
				$grid->addButton($eLang->get('DELETE').'/'.$eLang->get('UNINSTALL'), 'deletemodule', 'trash', 'modsaction');
				$grid->addSeparator();				
			}
		}

		$filters = array('frontend' => $eLang->get('FRONTEND'), 'backend' => $eLang->get('BACKEND'));
		$grid->addFilter($eLang->get('SECTION'), 'section', $filters, 'frontend');

		$filters = array('' => '- '.$eLang->get('ANY').' -');
		if ($positions) {
			foreach ($positions as $position) { $filters[$position] = $position; }
		}
		$grid->addFilter($eLang->get('POSITION'), 'position', $filters, '');

		$grid->addSearch($eLang->get('ID'), 'id', false);
		$grid->addSearch($eLang->get('TITLE'), 'title', true);
		$grid->addSearch($eLang->get('MODULE'), 'module', false);
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function modsaction(task, grid) {
			if (task == 'addmodule') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:modules/add.html'); ?>';
				location.href = newurl;
			} else if (task == 'editmodule') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('extmanager:modules/edit.html'); ?>?id=';
					var items = $('.trSelected',grid);
					var modid = parseInt(items[0].id.substr(3), 10);
					newurl += modid;
					location.href = newurl;
				}
			} else if ((task == 'publishmodule') || (task == 'copymodule') || (task == 'updatemodule') || (task == 'deletemodule')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var modid = parseInt(items[0].id.substr(3), 10);
					if (task == 'updatemodule') {
						alert('Live update is not yet supported. Update the module by uploading a newer version.');
						var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
						location.href = newurl;
						return false;
					}
					if ((task == 'publishmodule') || (task == 'copymodule') || ((task == 'deletemodule') && confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>'))) {
						var edata = {'id': modid};
						if (task == 'publishmodule') {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:modules/publish', 'inner.php'); ?>';
						} else if (task == 'copymodule') {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:modules/copy', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:modules/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#lmods").flexReload();
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

		function movemodule(mid, moveup) {
			var edata = {'id': mid, 'moveup':moveup };
			var eurl = '<?php echo $elxis->makeAURL('extmanager:modules/move', 'inner.php'); ?>';
			var successfunc = function(xreply) {
				var rdata = new Array();
				rdata = xreply.split('|');
				var rok = parseInt(rdata[0]);
				if (rok == 1) {
					$("#lmods").flexReload();
				} else {
					alert(rdata[1]);
				}
			}
			elxAjax('POST', eurl, edata, null, null, successfunc, null);
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
		echo '<div id="extmanagerbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('extmanager:/', 'inner.php')."</div>\n";
	}


	/*******************/
	/* ADD/EDIT MODULE */
	/*******************/
	public function editModule($row, $positions, $posmods, $aclrows, $groups, $users, $allmenuitems, $modmenuitems, $exml) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('extmanager:modules/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'emo',
			'label_width' => 220,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		if (!$row->id) {
			$pgtitle = $eLang->get('ADD_NEW_MODULE');
		} else if ($row->module == 'mod_content') {
			$pgtitle = $eLang->get('EDIT_TEXT_MODULE');
		} else {
			$pgtitle = sprintf($eLang->get('EDIT_MODULE_X'), $row->module);
		}
		echo '<h2>'.$pgtitle."</h2>\n";

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('BASIC_SETTINGS'));
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));
		if ($row->id) {
			$form->addInfo($eLang->get('ID'), $row->id);
		}
		$form->addInfo($eLang->get('MODULE'), $row->module);

		$trdata = array('category' => 'module', 'element' => 'title', 'elid' => (int)$row->id);
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 1, 'size' => 40, 'maxlength' => 255));

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		$options[] = $form->makeOption(1, $eLang->get('YES'));
		$options[] = $form->makeOption(2, $eLang->get('AUTO_MULTILINGUAL_TITLE'));
		$form->addSelect('showtitle', $eLang->get('SHOW_TITLE'), $row->showtitle, $options, array('dir' => 'rtl', 'tip' => $eLang->get('SHOW_TITLE').'|'.$eLang->get('SHOW_TITLE_DESC')));

		$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);

		$options = array();
		if ($positions) {
			foreach ($positions as $position) {
				$options[] = $form->makeOption($position, $position);
			}
			$form->addSelect('position', $eLang->get('POSITION'), $row->position, $options, array('onchange' => 'loadposorder();', 'tip' => $eLang->get('POSITION').'|'.$eLang->get('POSITION_TPL_MOD')));
		} else {
			$form->addText('position', $row->position, $eLang->get('POSITION'), array('required' => 1, 'size' => 30, 'maxlength' => 60, 'tip' => $eLang->get('POSITION').'|'.$eLang->get('POSITION_TPL_MOD')));
		}

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($posmods) {
			foreach ($posmods as $posmod) {
				$options[] = $form->makeOption($q, $q.' - '.$posmod->title);
				$q++;
			}
		}
		$q = ($q > 1) ? $q : 999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options, array('dir' => 'rtl'));
		$form->closeFieldset();

		$form->openFieldset($eLang->get('VERSION_AUTHOR'));
		$txt = $this->extensionInfo($exml);
		$form->addHTML($txt);
		$form->closeFieldset();

		if ($exml->getErrorMsg() == '') {
			$txt = $exml->getHead()->description;
			if ($txt != '') {
				if (strlen($txt < 30)) { $txt = $eLang->silentGet($txt); }
				$form->openFieldset($eLang->get('DESCRIPTION'));
				$form->addHTML($txt);
				$form->closeFieldset();
			}
			unset($txt);
		}

		$form->openFieldset($eLang->get('COMPAT_DEPENDECIES'));
		$txt = $this->extensionDependencies($exml);
		$form->addHTML($txt);
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab($eLang->get('ACCESS'));
		if (!$row->id) {
			$form->addNote($eLang->get('FIRST_SAVE_ITEM'), 'elx_smwarning');
		} else {
			$buffer = '<div class="elx_tbl_wrapper">'."\n";
			$buffer .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list" id="acllist">'."\n";
			$buffer .= "<tr>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('ACTION')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('ACCESS_LEVEL')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('GROUP')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('USER')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('ACL_VALUE')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('ACTIONS')."</th>\n";
			$buffer .= "</tr>\n";
			if ($aclrows) {
				$k = 1;
				$i = 1;
				$icon_edit = '<img src="'.$elxis->icon('edit', 16).'" alt="edit" border="0" />';
				$icon_delete = '<img src="'.$elxis->icon('delete', 16).'" alt="delete" border="0" />';
				foreach ($aclrows as $aclrow) {
					switch ($aclrow->action) {
						case 'view': $acttxt = $eLang->get('VIEW'); break;
						case 'manage': $acttxt = $eLang->get('MANAGE'); break;
						default: $acttxt = $aclrow->action; break;
					}
					
					$grouptxt = '<span style="color:#666;">'.$eLang->get('NONE').'</span>';
					if ($aclrow->gid > 0) {
						$grouptxt = $eLang->get('GROUP').' '.$aclrow->gid;
						if ($groups) {
							foreach ($groups as $group) {
								if ($group['gid'] == $aclrow->gid) {
									$grouptxt = $group['groupname'].' <span dir="ltr">('.$aclrow->gid.')</span>';
									break;
								}
							}
						}
					}

					$usertxt = '<span style="color:#666;">'.$eLang->get('NOONE').'</span>';
					if ($aclrow->uid > 0) {
						$usertxt = $eLang->get('USER').' '.$aclrow->uid;
						if ($users) {
							foreach ($users as $user) {
								if ($user['uid'] == $aclrow->uid) {
									$usertxt = ($elxis->getConfig('REALNAME') == 1) ? $user['firstname'].' '.$user['lastname'] : $user['uname'];
									$usertxt .= ' <span dir="ltr">('.$aclrow->uid.')</span>';
									break;
								}
							}
						}
					}

					if ($aclrow->minlevel < 0) {
						$leveltxt = '<span style="color:#666;">'.$aclrow->minlevel.'</span>';
					} else {
						$leveltxt = $aclrow->minlevel;
					}

					$buffer .= '<tr class="elx_tr'.$k.'" id="aclrow'.$aclrow->id.'">'."\n";
					$buffer .= '<td>'.$acttxt."</td>\n";
					$buffer .= '<td class="elx_td_center">'.$leveltxt."</td>\n";
					$buffer .= '<td>'.$grouptxt."</td>\n";
					$buffer .= '<td>'.$usertxt."</td>\n";
					$buffer .= '<td class="elx_td_center">'.$aclrow->aclvalue."</td>\n";
					$buffer .= '<td class="elx_td_center">'."\n";
					if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
						$buffer .= '<a href="javascript:void(null);" onclick="editACLRule('.$aclrow->id.')" title="'.$eLang->get('EDIT').'">'.$icon_edit."</a> &#160; \n";
						$buffer .= '<a href="javascript:void(null);" onclick="deleteACLRule('.$aclrow->id.')" title="'.$eLang->get('DELETE').'">'.$icon_delete."</a>\n";
					}
					$buffer .= "</td>\n";
					$buffer .= "</tr>\n";
					$k = 1 - $k;
					$i++;
				}
			}
			$buffer .= "</table>\n";
			$buffer .= "</div>\n";
			$form->addHTML($buffer);
			unset($buffer);

			if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
				$form->openFieldset($eLang->get('ADD_ACCESS_RULE'));
				$buffer = '<select name="aclaction" id="aclaction" class="selectbox" title="'.$eLang->get('ACTION').'" dir="'.$eLang->getinfo('DIR').'">
				<option value="view" selected="selected">'.$eLang->get('VIEW').'</option>
				<option value="manage">'.$eLang->get('MANAGE').'</option>
				</select> '."\n";
				$buffer .= '<select name="acltype" id="acltype" class="selectbox" dir="'.$eLang->getinfo('DIR').'" onchange="switchacltype();">
				<option value="level" selected="selected">'.$eLang->get('ACCESS_LEVEL').'</option>
				<option value="group">'.$eLang->get('GROUP').'</option>
				<option value="user">'.$eLang->get('USER').'</option>
				</select> '."\n";

				$buffer .= '<select name="acllevel" id="acllevel" class="selectbox" title="'.$eLang->get('ACCESS_LEVEL').'" dir="'.$eLang->getinfo('DIR').'">';
				if ($groups) {
					$lastlevel = -1;
					$space = '';
					foreach ($groups as $group) {
						if ($group['level'] != $lastlevel) {
							$space .= ($lastlevel == -1) ? '' : '&#160; ';
							$lastlevel = $group['level'];						
						}
						$sel = ($group['gid'] == 0) ? ' selected="selected"' : '';
						$buffer .= '<option value="'.$group['level'].'"'.$sel.'>'.$space.$group['level'].' - '.$group['groupname']."</option>\n";
					}
				}
				$buffer .= "</select> \n";

				$buffer .= '<select name="aclgroup" id="aclgroup" class="selectbox" title="'.$eLang->get('GROUP').'" dir="'.$eLang->getinfo('DIR').'" style="display:none;">';
				if ($groups) {
					foreach ($groups as $group) {
						$sel = ($group['gid'] == 7) ? ' selected="selected"' : '';
						$buffer .= '<option value="'.$group['gid'].'"'.$sel.'>'.$group['gid'].' - '.$group['groupname']."</option>\n";
					}
				}
				$buffer .= "</select> \n";

				if ($users) {
					$sel_user = true;
					$buffer .= '<select name="acluser" id="acluser" class="selectbox" title="'.$eLang->get('USER').'" dir="'.$eLang->getinfo('DIR').'" style="display:none;">';
					foreach ($users as $user) {
						$sel = ($user['uid'] == 1) ? ' selected="selected"' : '';
						$utxt = ($elxis->getConfig('REALNAME') == 1) ? $user['firstname'] .' '.$user['lastname'] : $user['uname'];
						$buffer .= '<option value="'.$user['uid'].'"'.$sel.'>'.$user['uid'].' - '.$utxt."</option>\n";
					}
					$buffer .= "</select> \n";
				} else {
					$sel_user = false;
					$buffer .= '<input type="text" name="acluser" id="acluser" dir="ltr" value="0" size="6" maxlength="6" class="inputbox" title="'.$eLang->get('USER').'" style="display:none;" /> '."\n";
				}
				$buffer .= '<button type="button" name="acladd" title="'.$eLang->get('ADD').'" class="elxbutton" dir="ltr" onclick="addACLRule(\'module\', \''.$row->module.'\', '.$row->id.', '.$sel_user.');">'.$eLang->get('ADD')."</button>\n";

				$form->addHTML($buffer);
				unset($buffer);
				$form->closeFieldset();
			}
		}
		$form->closeTab();

		if ($row->module == 'mod_content') {
			$form->openTab($eLang->get('MODULE_TEXT'));
			$trdata = array('category' => 'module', 'element' => 'content', 'elid' => (int)$row->id);
			$form->addMLTextarea(
				'content', $trdata, $row->content, $eLang->get('TEXT'), 
				array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang)
			);
			$form->closeTab();
		}

		$form->openTab($eLang->get('PARAMETERS'));
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$path = ELXIS_PATH.'/modules/'.$row->module.'/'.$row->module.'.xml';
		$params = new elxisParameters($row->params, $path, 'module');
		$form->addHTML($params->render(array('width' => 220)));
		if ($params->getUpload()) {
			$form->setOptions(array('enctype' => 'multipart/form-data'));
		}
		unset($params, $path);
		$form->closeTab();

		if ($row->section == 'frontend') {
			$form->openTab($eLang->get('MODULE_ASSIGNMENT'));
			$form->addNote($eLang->get('MODULE_ASSIGNMENT_HELP'), 'elx_sminfo');
			$options = array();
			$size = 1;
			$options[] = $form->makeOption(0, '- '.$eLang->get('ALL_ITEMS').' -');
			if ($allmenuitems) {
				$collection = '';
				$disid = -1;
				foreach ($allmenuitems as $menuitem) {
					if (($collection == '') || ($collection != $menuitem['collection'])) {
						$options[] = $form->makeOption($disid, '- '.$menuitem['collection'].' -', array(), 1);
						$collection = $menuitem['collection'];
						$disid--;
						$size++;
					}
					$options[] = $form->makeOption($menuitem['menu_id'], $menuitem['title']);
					$size++;
				}
				unset($collection, $disid);
			}

			if ($size > 20) { $size = 20; }
			if (!is_array($modmenuitems)) { $modmenuitems = array(); }
			$form->addSelect('pages', $eLang->get('MENU_ITEMS'), $modmenuitems, $options, array('multiple' => 1, 'dir' => 'rtl', 'size' => $size));
			unset($options, $size);
			$form->closeTab();
		}

		if ($row->module != 'mod_content') {
			$form->addHidden('content', '');
		}
		$form->addHidden('section', $row->section);
		$form->addHidden('module', $row->module);
		$form->addHidden('iscore', $row->iscore);
		$form->addHidden('id', $row->id);
		$form->addHidden('task', '');
		$form->render();
		unset($form);

		echo '<div id="extmanagerbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('extmanager:/', 'inner.php')."</div>\n";
		echo '<div id="userbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('user:/', 'inner.php')."</div>\n";
		echo '<div id="acontentbase" style="display:none;">'.$elxis->makeAURL('content:/', 'inner.php')."</div>\n";
	}

}

?>