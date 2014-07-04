<?php 
/**
* @version		$Id: aaccess.html.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aaccessUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*****************/
	/* SHOW ACL LIST */
	/*****************/
	public function listacl() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('ACL')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lacl', $eLang->get('ACL'));
		$grid->setOption('url', $elxis->makeAURL('user:acl/getacl.xml', 'inner.php'));
		$grid->setOption('sortname', 'category');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', false);
		$grid->addColumn($eLang->get('CATEGORY'), 'category', 120, true, 'left');
		$grid->addColumn($eLang->get('ELEMENT'), 'element', 160, true, 'left');
		$grid->addColumn($eLang->get('IDENTITY'), 'identity', 100, false, 'center');
		$grid->addColumn($eLang->get('ACTION'), 'action', 120, true, 'auto');
		$grid->addColumn($eLang->get('MINLEVEL'), 'minlevel', 130, true, 'center');
		$grid->addColumn($eLang->get('GROUP_ID'), 'gid', 100, true, 'center');
		$grid->addColumn($eLang->get('USER_ID'), 'uid', 100, true, 'center');
		$grid->addColumn($eLang->get('ACCESS_VALUE'), 'aclvalue', 120, true, 'center');
		$grid->addButton($eLang->get('NEW'), 'addacl', 'add', 'aclaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('EDIT'), 'editacl', 'edit', 'aclaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'deleteacl', 'delete', 'aclaction');
		$grid->addSeparator();
		$grid->addSearch($eLang->get('CATEGORY'), 'category', true);
		$grid->addSearch($eLang->get('ELEMENT'), 'element', false);
		$grid->addSearch($eLang->get('ACTION'), 'action', false);
		$grid->addSearch($eLang->get('MINLEVEL'), 'minlevel', false);
		$grid->addSearch($eLang->get('GROUP_ID'), 'gid', false);
		$grid->addSearch($eLang->get('USER_ID'), 'uid', false);
		$filters = array(0 => $eLang->get('NO'), 1 => $eLang->get('YES'));
		$grid->addFilter($eLang->get('USE_TRANSLATIONS'), 'translations', $filters, 1);
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function aclaction(task, grid) {
			if (task == 'addacl') {
				var frlink = '<?php echo $elxis->makeAURL('user:acl/edit.html', 'inner.php'); ?>?id=0';
				$.colorbox({iframe:true, width:740, height:550, href:frlink});
			} else if (task == 'editacl') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var eid = parseInt(items[0].id.substr(3), 10);
					var frlink = '<?php echo $elxis->makeAURL('user:acl/edit.html', 'inner.php'); ?>?id='+eid;
					$.colorbox({iframe:true, width:740, height:550, href:frlink});
				}
			} else if (task == 'deleteacl') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
						var items = $('.trSelected',grid);
						var itemlist = '';
						for(i=0;i<items.length;i++){
							itemlist+= parseInt(items[i].id.substr(3), 10)+",";
						}
						var edata = {'ids': itemlist};
						var eurl = '<?php echo $elxis->makeAURL('user:acl/deleteacl', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok == 1) {
								$("#lacl").flexReload();
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


	/*********************/
	/* ADD/EDIT ACL HTML */
	/*********************/
	public function editACL($row, $data, $lck=0, $errormsg='', $sucmsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$action = $elxis->makeAURL('user:acl/edit.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'aclform',
			'action' => $action,
			'idprefix' => 'eac',
			'label_width' => 150,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1
		);
		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('ELEMENT_IDENTIF'));
		if ($row->id && ($lck == 1)) {
			$form->addInfo($eLang->get('CATEGORY'), $row->category);
			$form->addHidden('category', $row->category);
			$form->addHidden('category2', '');
		} else {
			$form->openRow();
			$s1 = '';
			$options = array();
			$options[] = $form->makeOption('', '- '.$eLang->get('OTHER').' -');
			foreach ($data['categories'] as $ctg) {
				if ($row->category == $ctg) { $s1 = $row->category; }
				$options[] = $form->makeOption($ctg, $ctg);
			}
			$form->addSelect('category', $eLang->get('CATEGORY'), $s1, $options);
			$s2 = ($s1 == '') ? $row->category : '';
			$form->addText('category2', $s2, $eLang->get('OTHER_CATEGORY'), array('required' => 0, 'dir' => 'ltr', 'size' => 20, 'maxlength' => 80));
			$form->closeRow();
		}

		if ($row->id && ($lck == 1)) {
			$form->addInfo($eLang->get('ELEMENT'), $row->element);
			$form->addInfo($eLang->get('ACTION'), $row->action);
			$form->addInfo($eLang->get('IDENTITY'), $row->identity);
			$form->addHidden('element', $row->element);
			$form->addHidden('element2', '');
			$form->addHidden('action', $row->action);
			$form->addHidden('action2', '');
			$form->addHidden('identity', $row->identity);
		} else {
			$form->openRow();
			$s1 = '';
			$options = array();
			$options[] = $form->makeOption('', '- '.$eLang->get('OTHER').' -');
			foreach ($data['elements'] as $elem) {
				if ($row->element == $elem) { $s1 = $row->element; }
				$options[] = $form->makeOption($elem, $elem);
			}
			$form->addSelect('element', $eLang->get('ELEMENT'), $s1, $options);
			$s2 = ($s1 == '') ? $row->element : '';
			$form->addText('element2', $s2, $eLang->get('OTHER_ELEMENT'), array('required' => 0, 'dir' => 'ltr', 'size' => 20, 'maxlength' => 80));
			$form->closeRow();

			$form->openRow();
			$s1 = '';
			$options = array();
			$options[] = $form->makeOption('', '- '.$eLang->get('OTHER').' -');
			foreach ($data['actions'] as $act) {
				if ($row->action == $act) { $s1 = $row->action; }
				$options[] = $form->makeOption($act, $act);
			}
			$form->addSelect('action', $eLang->get('ACTION'), $s1, $options);
			$s2 = ($s1 == '') ? $row->action : '';
			$form->addText('action2', $s2, $eLang->get('OTHER_ACTION'), array('required' => 0, 'dir' => 'ltr', 'size' => 20, 'maxlength' => 80));
			unset($s1, $s2);
			$form->closeRow();

			$form->addNumber('identity', $row->identity, $eLang->get('IDENTITY'), array('required' => 1, 'size' => 6, 'maxlength' => 10, 'tip' => $eLang->get('IDENTITY_HELP')));
		}
		$form->closeFieldset();

		$form->openFieldset($eLang->get('USERS_IDENTIF'));
		$form->addSlider('minlevel', $row->minlevel, $eLang->get('MINLEVEL'), array('width' => 102, 'min' => -1, 'max' => 100, 'required' => 1));
		$form->openRow();
		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('NONE').' -');
		if ($data['groups']) {
			foreach ($data['groups'] as $grp) {
				$lvl = sprintf("%03d", $grp['level']);
				$options[] = $form->makeOption($grp['gid'], $grp['gid'].' - '.$lvl.' - '.$grp['groupname']);
			}
		}
		$form->addSelect('gid', $eLang->get('GROUP'), $row->gid, $options);

		if ($data['users']) {
			$options = array();
			$options[] = $form->makeOption(0, '- '.$eLang->get('NOONE').' -');
			foreach ($data['users'] as $usr) {
				$userid = sprintf("%03d", $usr['uid']);
				$txt = ($elxis->getConfig('REALNAME') == 1) ? $usr['firstname'].' '.$usr['lastname'] : $usr['uname'];
				$options[] = $form->makeOption($usr['uid'], $userid.' - '.$txt);
			}
			$form->addSelect('uid', $eLang->get('USER'), $row->uid, $options, array('style' => 'width:160px'));
		} else {
			$form->addText('uid', $row->uid, $eLang->get('USER_ID'), array('required' => 1, 'size' => 6, 'maxlength' => 10));
		}

		$form->closeRow();
		$form->closeFieldset();

		$form->openFieldset($eLang->get('GRANT_ACCESS'));
		$options = array();
		$options[] = $form->makeOption(0, 0);
		$options[] = $form->makeOption(1, 1);
		$options[] = $form->makeOption(2, 2);
		$options[] = $form->makeOption(3, 3);
		$options[] = $form->makeOption(4, 4);
		$options[] = $form->makeOption(5, 5);
		$form->addSelect('aclvalue', $eLang->get('ACCESS_VALUE'), $row->aclvalue, $options, array('tip' => $eLang->get('ACLVALUE_HELP')));
		$form->closeFieldset();

		$form->addHidden('id', $row->id);
		$form->addHidden('lck', $lck);
		$form->addButton('aclbtn', $eLang->get('SAVE'));

		if ($errormsg != '') {
			echo '<div class="elx_smerror">'.$errormsg."</div>\n";
		} elseif ($sucmsg != '') {
			echo '<div class="elx_smnotice">'.$sucmsg."</div>\n";
		} else {
			echo '<div class="elx_smwarning">'.$eLang->get('WARN_WRONG_ACL')."</div>\n";
		}
		$form->render();
		unset($form);

	}
}

?>