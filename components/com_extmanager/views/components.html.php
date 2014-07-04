<?php 
/**
* @version		$Id: components.html.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class componentsExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/************************/
	/* SHOW COMPONENTS LIST */
	/************************/
	public function listcomponents() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		echo '<h2>'.$eLang->get('COMPONENTS')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lcoms', $eLang->get('COMPONENTS'));
		$grid->setOption('url', $elxis->makeAURL('extmanager:components/getcomponents.xml', 'inner.php'));
		$grid->setOption('sortname', 'name');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 40, false, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'name', 180, true, 'auto');
		$grid->addColumn($eLang->get('COMPONENT'), 'component', 160, true, 'auto');
		$grid->addColumn($eLang->get('VERSION'), 'version', 100, false, 'center');
		$grid->addColumn($eLang->get('DATE'), 'cdate', 160, false, 'auto');
		$grid->addColumn($eLang->get('AUTHOR'), 'author', 180, false, 'auto');
		$grid->addColumn($eLang->get('ROUTE'), 'route', 120, false, 'auto');

		if ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('NEW'), 'addcomp', 'add', 'compsaction');
				$grid->addSeparator();
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'editcomp', 'edit', 'compsaction');
			$grid->addSeparator();
		}

		if ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('UPDATE'), 'updatecomp', 'download', 'compsaction');
				$grid->addSeparator();
				$grid->addButton($eLang->get('UNINSTALL'), 'deletecomp', 'trash', 'compsaction');
				$grid->addSeparator();
			}
		}

		$grid->addSearch($eLang->get('TITLE'), 'name', true);
		$grid->addSearch($eLang->get('COMPONENT'), 'component', false);
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function compsaction(task, grid) {
			if (task == 'addcomp') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
				location.href = newurl;
			} else if (task == 'editcomp') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('extmanager:components/edit.html'); ?>?id=';
					var items = $('.trSelected',grid);
					var comid = parseInt(items[0].id.substr(3), 10);
					newurl += comid;
					location.href = newurl;
				}
			} else if ((task == 'updatecomp') || (task == 'deletecomp')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var comid = parseInt(items[0].id.substr(3), 10);
					if (task == 'updatecomp') {
						alert('Live update is not yet supported. Update the component by uploading a newer version.');
						var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
						location.href = newurl;
						return false;
					}

					if ((task == 'deletecomp') && confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
						var edata = {'id': comid};
						var eurl = '<?php echo $elxis->makeAURL('extmanager:components/delete', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#lcoms").flexReload();
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
	/* EDIT COMPONENT */
	/******************/
	public function editComponent($row, $aclrows, $groups, $users, $exml) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('extmanager:components/save.html', 'inner.php');
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

		$pgtitle = sprintf($eLang->get('EDIT_COMPONENT_X'), $row->name);
		echo '<h2>'.$pgtitle."</h2>\n";

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('BASIC_SETTINGS'));
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));

		$form->addInfo($eLang->get('ID'), $row->id);
		$form->addInfo($eLang->get('TITLE'), $row->name);
		$form->addInfo($eLang->get('COMPONENT'), $row->component);
		$form->addText('route', $row->route, $eLang->get('ROUTE'), array('dir' => 'ltr', 'size' => 20, 'maxlength' => 60, 'tip' => $eLang->get('ROUTE').'|'.$eLang->get('ROUTING_HELP')));
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

		$buffer = '<div class="elx_tbl_wrapper">'."\n";
		$buffer .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list" id="acllist">'."\n";
		$buffer .= "<tr>\n";
		$buffer .= '<th class="elx_th_sub">'.$eLang->get('CATEGORY')."</th>\n";
		$buffer .= '<th class="elx_th_sub">'.$eLang->get('ELEMENT')."</th>\n";
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
				$acttxt = $aclrow->action;
				$elemtxt = $aclrow->element;
				$upstring = strtoupper($aclrow->action);
				if ($eLang->exist($upstring)) { $acttxt = $eLang->get($upstring); }
				$upstring = strtoupper($aclrow->element);
				if ($eLang->exist($upstring)) { $elemtxt = $eLang->get($upstring); }
				unset($upstring);

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
				$buffer .= '<td>'.$aclrow->category."</td>\n";
				$buffer .= '<td>'.$elemtxt."</td>\n";
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

		$form->openTab($eLang->get('ACCESS'));
		$form->addHTML($buffer);
		unset($buffer);

		if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
			$form->openFieldset($eLang->get('ADD_ACCESS_RULE'));
			
			$buffer = '<select name="aclcategory" id="aclcategory" class="selectbox" title="'.$eLang->get('CATEGORY').'" dir="'.$eLang->getinfo('DIR').'" onchange="switchaclcat(\'component\', \''.$row->component.'\');">
			<option value="component">component</option>
			<option value="'.$row->component.'" selected="selected">'.$row->component.'</option>
			</select> '."\n";
			$buffer .= '<input type="text" name="aclelement" id="aclelement" dir="ltr" value="" title="'.$eLang->get('ELEMENT').'" class="inputbox" size="20" /> '."\n";
			$buffer .= '<input type="text" name="aclaction" id="aclaction" dir="ltr" value="" title="'.$eLang->get('ACTION').'" class="inputbox" size="20" /> '."\n";
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

			$buffer .= '<select name="aclvalue" id="aclvalue" class="selectbox" title="'.$eLang->get('ACL_VALUE').'" dir="'.$eLang->getinfo('DIR').'">';
			$buffer .= '<option value="0">0</option>'."\n";
			$buffer .= '<option value="1" selected="selected">1</option>'."\n";
			$buffer .= '<option value="2">2</option>'."\n";
			$buffer .= '<option value="3">3</option>'."\n";
			$buffer .= '<option value="4">4</option>'."\n";
			$buffer .= '<option value="5">5</option>'."\n";
			$buffer .= "</select> \n";
			$buffer .= '<button type="button" name="acladd" title="'.$eLang->get('ADD').'" class="extman_button" dir="ltr" onclick="addFullACLRule('.$sel_user.');">'.$eLang->get('ADD')."</button>\n";

			$form->addHTML($buffer);
			unset($buffer);
			$form->closeFieldset();
		}

		$form->closeTab();

		$form->openTab($eLang->get('PARAMETERS'));
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$cname = preg_replace('/^(com\_)/', '', $row->component);
		$path = ELXIS_PATH.'/components/'.$row->component.'/'.$cname.'.xml';
		$params = new elxisParameters($row->params, $path, 'component');
		$form->addHTML($params->render(array('width' => 220), false));
		if ($params->getUpload()) {
			$form->setOptions(array('enctype' => 'multipart/form-data'));
		}
		unset($params, $path);
		$form->closeTab();

		$form->addHidden('name', $row->name);
		$form->addHidden('component', $row->component);
		$form->addHidden('iscore', $row->iscore);
		$form->addHidden('id', $row->id);
		$form->addHidden('task', '');
		$form->render();
		unset($form);

		echo '<div id="extmanagerbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('extmanager:/', 'inner.php')."</div>\n";
		echo '<div id="userbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('user:/', 'inner.php')."</div>\n";
	}

}

?>