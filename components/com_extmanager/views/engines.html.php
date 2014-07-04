<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class enginesExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/****************************/
	/* SHOW SEARCH ENGINES LIST */
	/****************************/
	public function listengines() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		echo '<h2>'.$eLang->get('SEARCH_ENGINES')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lengs', $eLang->get('SEARCH_ENGINES'));
		$grid->setOption('url', $elxis->makeAURL('extmanager:engines/getengines.xml', 'inner.php'));
		$grid->setOption('sortname', 'ordering');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 40, false, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'title', 180, true, 'auto');
		$grid->addColumn($eLang->get('SEARCH_ENGINE'), 'engine', 150, true, 'auto');
		$grid->addColumn($eLang->get('PUBLISHED'), 'published', 90, false, 'center');
		$grid->addColumn($eLang->get('DEFAULT'), 'default', 120, false, 'center');
		$grid->addColumn($eLang->get('REORDER'), 'ordering', 110, true, 'center');
		$grid->addColumn($eLang->get('ACCESS'), 'access', 130, false, 'auto');
		$grid->addColumn($eLang->get('VERSION'), 'version', 80, false, 'center');
		$grid->addColumn($eLang->get('DATE'), 'cdate', 130, false, 'auto');

		if ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('NEW'), 'addeng', 'add', 'engsaction');
				$grid->addSeparator();
			}
		}
		if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'editeng', 'edit', 'engsaction');
			$grid->addSeparator();
			$grid->addButton($eLang->get('PUBLISH'), 'publisheng', 'toggle', 'engsaction');
			$grid->addSeparator();
			$grid->addButton($eLang->get('DEFAULT'), 'defeng', 'tick', 'engsaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) {
			if (!$is_subsite) {
				$grid->addButton($eLang->get('UPDATE'), 'updateeng', 'download', 'engsaction');
				$grid->addSeparator();
				$grid->addButton($eLang->get('UNINSTALL'), 'deleteeng', 'trash', 'engsaction');
				$grid->addSeparator();
			}
		}
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function engsaction(task, grid) {
			if (task == 'addeng') {
				var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
				location.href = newurl;
			} else if (task == 'editeng') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('extmanager:engines/edit.html'); ?>?id=';
					var items = $('.trSelected',grid);
					var engid = parseInt(items[0].id.substr(3), 10);
					newurl += engid;
					location.href = newurl;
				}
			} else if ((task == 'publisheng') || (task == 'defeng') || (task == 'updateeng') || (task == 'deleteeng')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var engid = parseInt(items[0].id.substr(3), 10);
					if (task == 'updateeng') {
						alert('Live update is not yet supported. Update the search engine by uploading a newer version.');
						var newurl = '<?php echo $elxis->makeAURL('extmanager:/'); ?>';
						location.href = newurl;
						return false;
					}

					if ((task == 'publisheng') || (task == 'defeng') || ((task == 'deleteeng') && confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>'))) {
						var edata = {'id': engid};
						if (task == 'publisheng') {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:engines/publish', 'inner.php'); ?>';
						} else if (task == 'defeng') {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:engines/makedef', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('extmanager:engines/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#lengs").flexReload();
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

		function moveengine(eid, moveup) {
			var edata = {'id': eid, 'moveup':moveup };
			var eurl = '<?php echo $elxis->makeAURL('extmanager:engines/move', 'inner.php'); ?>';
			var successfunc = function(xreply) {
				var rdata = new Array();
				rdata = xreply.split('|');
				var rok = parseInt(rdata[0]);
				if (rok == 1) {
					$("#lengs").flexReload();
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
	}


	/***************/
	/* EDIT ENGINE */
	/***************/
	public function editEngine($row, $allengines, $exml, $xmlfile, $engicon) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$action = $elxis->makeAURL('extmanager:engines/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'een',
			'label_width' => 220,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		echo '<h2>'.$eLang->get('EDIT').' '.$row->title."</h2>\n";

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('BASIC_SETTINGS'));
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));
		$form->addInfo($eLang->get('ID'), $row->id);

		if ($engicon != '') {
			$engtxt = '<img src="'.$engicon.'" alt="'.$row->engine.'" style="vertical-align:middle;" /> '.$row->engine;
		} else {
			$engtxt = $row->engine;
		}
		$form->addInfo($eLang->get('SEARCH_ENGINE'), $engtxt);

		$form->addText('title', $row->title, $eLang->get('TITLE'), array('required' => 1, 'size' => 40, 'maxlength' => 255));
		$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		$form->addYesNo('defengine', $eLang->get('DEFAULT'), $row->defengine, array('tip' => $eLang->get('DEF_ENGINE_PUB')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($allengines) {
			foreach ($allengines as $eng) {
				$options[] = $form->makeOption($q, $q.' - '.$eng->title);
				$q++;
			}
		}
		$q = ($q > 1) ? $q : 999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options, array('dir' => 'rtl'));
		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr'));
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
		$params = new elxisParameters($row->params, $xmlfile, 'engine');
		$form->addHTML($params->render(array('width' => 220), false));
		if ($params->getUpload()) {
			$form->setOptions(array('enctype' => 'multipart/form-data'));
		}
		unset($params, $path);
		$form->closeTab();

		$form->addHidden('engine', $row->engine);
		$form->addHidden('iscore', $row->iscore);
		$form->addHidden('id', $row->id);
		$form->addHidden('task', '');
		$form->render();
		unset($form);
	}

}

?>