<?php 
/**
* @version		$Id: templates.html.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class templatesExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************/
	/* SHOW TEMPLATES LIST */
	/***********************/
	public function listtemplates() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		echo '<h2>'.$eLang->get('TEMPLATES')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('ltpls', $eLang->get('TEMPLATES'));
		$grid->setOption('url', $elxis->makeAURL('extmanager:templates/gettemplates.xml', 'inner.php'));
		$grid->setOption('sortname', 'title');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);

		$grid->addColumn($eLang->get('SN'), 'sn', 40, false, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'title', 160, true, 'auto');
		$grid->addColumn($eLang->get('TEMPLATE'), 'template', 140, true, 'auto');
		$grid->addColumn($eLang->get('VERSION'), 'version', 100, false, 'center');
		$grid->addColumn($eLang->get('DEFAULT'), 'default', 120, false, 'center');
		$grid->addColumn($eLang->get('DATE'), 'cdate', 150, false, 'auto');
		$grid->addColumn($eLang->get('AUTHOR'), 'author', 150, false, 'auto');
		$grid->addColumn($eLang->get('SECTION'), 'section', 120, false, 'auto');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('NEW'), 'addtpl', 'add', 'tempsaction');
				$grid->addSeparator();
			}
		}
		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'edittpl', 'edit', 'tempsaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('UPDATE'), 'updatetpl', 'download', 'tempsaction');
				$grid->addSeparator();
				$grid->addButton($eLang->get('UNINSTALL'), 'deletetpl', 'trash', 'tempsaction');
				$grid->addSeparator();
			}
		}
		$grid->addButton($eLang->get('MODULE_POSITIONS'), 'modpos', 'module', 'tempsaction');
		$grid->addSeparator();

		$filters = array('frontend' => $eLang->get('FRONTEND'), 'backend' => $eLang->get('BACKEND'));
		$grid->addFilter($eLang->get('SECTION'), 'section', $filters, 'frontend');
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function tempsaction(task, grid) {
			if (task == 'modpos') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:templates/positions.html'); ?>';
				location.href = newurl;
			} else if (task == 'addtpl') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
				location.href = newurl;
			} else if (task == 'edittpl') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('extmanager:templates/edit.html'); ?>?id=';
					var items = $('.trSelected',grid);
					var tplid = parseInt(items[0].id.substr(3), 10);
					newurl += tplid;
					location.href = newurl;
				}
			} else if ((task == 'updatetpl') || (task == 'deletetpl')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var tplid = parseInt(items[0].id.substr(3), 10);
					if (task == 'updatetpl') {
						alert('Live update is not yet supported. Update the template by uploading a newer version.');
						var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
						location.href = newurl;
						return false;
					}
					if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
						var edata = {'id': tplid};
						var eurl = '<?php echo $elxis->makeAURL('extmanager:templates/delete', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#ltpls").flexReload();
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
	/* EDIT TEMPLATE */
	/******************/
	public function editTemplate($row, $exml, $xmlfile, $tplthumb) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);
		$cur_template = ($row->section == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');

		$action = $elxis->makeAURL('extmanager:templates/save.html', 'inner.php');
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

		$pgtitle = sprintf($eLang->get('EDIT_TEMPLATE_X'), $row->title);
		echo '<h2>'.$pgtitle."</h2>\n";

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('BASIC_SETTINGS'));
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));

		$form->addInfo($eLang->get('ID'), $row->id);
		
		$sectiontxt = ($row->section == 'backend') ? $eLang->get('BACKEND') : $eLang->get('FRONTEND');
		$form->addInfo($eLang->get('SECTION'), $sectiontxt);
		$form->addInfo($eLang->get('TITLE'), $row->title);
		$form->addInfo($eLang->get('TEMPLATE'), $row->template);
		if ($row->template == $cur_template) {
			$txt = '<span style="color:#008000; font-weight:bold;">'.$eLang->get('YES').'</span>';
		} else {
			$txt = '<span style="color:#FF0000; font-weight:bold;">'.$eLang->get('NO').'</span>';
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
				$txt .= ' &#160; &#160; '.$eLang->get('SET_DEFAULT_IN_CONFIG');
			}
		}
		$form->addInfo($eLang->get('DEFAULT'), $txt);

		if ($tplthumb != '') {
			$txt = '<a href="'.$tplthumb.'" class="elx_litebox">';
			$txt .= '<img src="'.$tplthumb.'" alt="thumbnail" border="0" style="width:120px; padding:4px; border:1px solid #ccc;" /></a>';
		} else {
			$txt = '<em>'.$eLang->get('NOT_AVAILABLE').'</em>';
		}
		$form->addInfo($eLang->get('PREVIEW'), $txt);
		unset($txt);
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

		$form->openTab($eLang->get('PARAMETERS'));
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($row->params, $xmlfile, 'template');
		$form->addHTML($params->render(array('width' => 220), false));
		if ($params->getUpload()) {
			$form->setOptions(array('enctype' => 'multipart/form-data'));
		}
		unset($params, $path);
		$form->closeTab();

		$form->addHidden('title', $row->title);
		$form->addHidden('template', $row->template);
		$form->addHidden('iscore', $row->iscore);
		$form->addHidden('section', $row->section);
		$form->addHidden('id', $row->id);
		$form->addHidden('task', '');
		$form->render();
		unset($form);
	}


	/******************************/
	/* LIST MODULE POSITIONS HTML */
	/******************************/
	public function listpositions() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		eFactory::getDocument()->setContentType('text/html');//colorbox wont work correctly with xhtml

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lpos', $eLang->get('MODULE_POSITIONS'));
		$grid->setOption('url', $elxis->makeAURL('extmanager:templates/getpositions.xml', 'inner.php'));
		$grid->setOption('sortname', 'position');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 60, false, 'auto');
		$grid->addColumn($eLang->get('POSITION'), 'position', 200, true, 'auto');
		$grid->addColumn($eLang->get('MODULES'), 'modules', 100, false, 'center');
		$grid->addColumn($eLang->get('DESCRIPTION'), 'description', 350, false, 'auto');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) {
			$grid->addButton($eLang->get('NEW'), 'addpos', 'add', 'modposactions');
			$grid->addSeparator();
			$grid->addButton($eLang->get('EDIT'), 'editpos', 'edit', 'modposactions');
			$grid->addSeparator();
			$grid->addButton($eLang->get('DELETE'), 'deletepos', 'delete', 'modposactions');
			$grid->addSeparator();
		}

		echo '<h2>'.$eLang->get('MODULE_POSITIONS')."</h2>\n";
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function modposactions(task, grid) {
			if (task == 'addpos') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:templates/editposition.html', 'inner.php'); ?>?id=0';
				$.colorbox({top:'160px', width:600, height:300, href:newurl});
			} else if (task == 'editpos') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('extmanager:templates/editposition.html', 'inner.php'); ?>?id=';
					var items = $('.trSelected',grid);
					var posid = parseInt(items[0].id.substr(3), 10);
					newurl += posid;
					$.colorbox({top:'160px', width:600, height:300, href:newurl});
				}
			} else if (task == 'deletepos') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var posid = parseInt(items[0].id.substr(3), 10);
					if (confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>')) {
						var edata = {'id': posid};
						var eurl = '<?php echo $elxis->makeAURL('extmanager:templates/deleteposition', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#lpos").flexReload();
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

}

?>