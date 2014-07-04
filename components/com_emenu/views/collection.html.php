<?php 
/**
* @version		$Id: collection.html.php 1040 2012-04-16 08:13:20Z datahell $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class collectionEmenuView extends emenuView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* SHOW COLLECTIONS LIST */
	/*************************/
	public function listcollections() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('MENU_ITEM_COLLECTIONS')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lcolects', $eLang->get('MENU_ITEM_COLLECTIONS'));
		$grid->setOption('url', $elxis->makeAURL('emenu:getcollections.xml', 'inner.php'));
		$grid->setOption('sortname', 'collection');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', 50);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 60, false, 'center');
		$grid->addColumn($eLang->get('COLLECTION'), 'collection', 180, true, 'auto');
		$grid->addColumn($eLang->get('MENU_ITEMS'), 'items', 180, true, 'center');
		$grid->addColumn($eLang->get('MODULES'), 'modules', 450, false, 'auto');
		if ($elxis->acl()->check('com_emenu', 'menu', 'add') > 0) {
			$grid->addButton($eLang->get('NEW'), 'addcol', 'add', 'collectaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'delete') > 0) {
			$grid->addButton($eLang->get('DELETE'), 'deletecol', 'delete', 'collectaction');
			$grid->addSeparator();
		}
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function collectaction(task, grid) {
			if (task == 'addcol') {
				var frlink = '<?php echo $elxis->makeAURL('emenu:addcol.html', 'inner.php'); ?>';
				$.colorbox({iframe:true, top:'160px', width:'500px', height:'340px', href:frlink});
			} else if (task == 'deletecol') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var collection = items[0].id.substr(3);
					if (collection == 'mainmenu') {
						alert('<?php echo $eLang->get('CNOT_DELETE_MAINMENU'); ?>');
						return false;
					}

					if (confirm('<?php echo addslashes($eLang->get('WARN_DELETE_COLLECT')); ?>')) {
						var edata = {'collection': collection};
						var eurl = '<?php echo $elxis->makeAURL('emenu:deletecol', 'inner.php'); ?>';
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok == 1) {
								$("#lcolects").flexReload();
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


	/***********************/
	/* ADD COLLECTION HTML */
	/***********************/
	public function addCollection($collection, $modtitle, $errormsg='', $sucmsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('emenu:addcol.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'epr',
			'label_width' => 180,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1
		);
		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('ADD_NEW_COLLECT'));
		$form->addInfo('', $eLang->get('COLLECT_NAME_INFO'));
		$form->addText('collection', $collection, $eLang->get('COLLECTION'), array('required' => 1, 'dir' => 'ltr', 'size' => 30, 'maxlength' => 30));
		$form->addText('modtitle', $modtitle, $eLang->get('MODULE_TITLE'), array('required' => 1, 'forcedir' => $cinfo['DIR'], 'size' => 30, 'maxlength' => 120));
		$form->addButton('colbtn', $eLang->get('SAVE'));
		$form->closeFieldset();

		if ($errormsg != '') {
			echo '<div class="elx_error">'.$errormsg."</div>\n";
		} elseif ($sucmsg != '') {
			echo '<div class="elx_success">'.$sucmsg."</div>\n";
		}
		$form->render();
		unset($form);
	}

}

?>