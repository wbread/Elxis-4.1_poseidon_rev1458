<?php 
/**
* @version		$Id: acategory.html.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class acategoryContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************/
	/* DISPLAY CATEGORIES LIST */
	/***************************/
	public function listcategories() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('CONTENT_CATEGORIES')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('listctgs', $eLang->get('CATEGORIES_LIST'));
		$grid->setOption('url', $elxis->makeAURL('content:categories/getcategories.xml', 'inner.php'));
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('singleSelect', true);
		$grid->setOption('sortname', 'nothing');
		$grid->setOption('sortorder', 'asc');
		$grid->addColumn($eLang->get('ID'), 'catid', 50, true, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'treename', 350, true, 'auto');
		$grid->addColumn($eLang->get('PUBLISHED'), 'published', 100, false, 'center');
		$grid->addColumn($eLang->get('ORDERING'), 'ordering', 80, false, 'center');
		$grid->addColumn($eLang->get('ACCESS'), 'alevel', 120, false, 'auto');
		$grid->addColumn($eLang->get('ARTICLES'), 'articles', 80, false, 'center');

		if ($elxis->acl()->check('com_content', 'category', 'add') > 0) {
			$grid->addButton($eLang->get('NEW'), 'addctg', 'add', 'ctgaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'category', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'editctg', 'edit', 'ctgaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'category', 'publish') > 0) {
			$grid->addButton($eLang->get('PUBLISH'), 'publishctg', 'toggle', 'ctgaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'category', 'delete') > 0) {
			$grid->addButton($eLang->get('DELETE'), 'deletectg', 'delete', 'ctgaction');
			$grid->addSeparator();
		}
		$grid->addButton($eLang->get('TREE_VIEW'), 'treeview', 'tree', 'ctgaction');
		$grid->addSeparator();

		$filters = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 10=>10, 20=>20);
		$grid->addFilter($eLang->get('MAX_LEVEL'), 'maxlevel', $filters, 10);
		$grid->addSearch($eLang->get('ID'), 'catid', false);
		$grid->addSearch($eLang->get('TITLE'), 'title', true);
		$grid->addSearch($eLang->get('SEOTITLE'), 'seotitle', false);
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function ctgaction(task, grid) {
			if (task == 'treeview') {
				$("input[name=q]").val('');
				$("select[name=qtype]").val('title');
				$('#listctgs').flexOptions({ qtype: 'title', query: '', sortname: 'nothing', sortorder: 'asc' }).flexReload();
			} else if (task == 'addctg') {
				location.href = '<?php echo $elxis->makeAURL('content:categories/add.html'); ?>';
			} else if (task == 'editctg') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var catid = parseInt(items[0].id.substr(3), 10);
					location.href = '<?php echo $elxis->makeAURL('content:categories/edit.html'); ?>?catid='+catid;
				}
			} else if ((task == 'publishctg') || (task == 'deletectg')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var catid = parseInt(items[0].id.substr(3), 10);
					if ((task == 'publishctg') || ((task == 'deletectg') && confirm('<?php echo addslashes($eLang->get('WARN_DELETE_CATEGORY')); ?>'))) {
						var edata = {'catid': catid};
						if (task == 'publishctg') {
							var eurl = '<?php echo $elxis->makeAURL('content:categories/publish', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('content:categories/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok == 1) {
								$("#listctgs").flexReload();
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

		function movecategory(catid, moveup) {
			var edata = {'catid': catid, 'moveup':moveup };
			var eurl = '<?php echo $elxis->makeAURL('content:categories/move', 'inner.php'); ?>';
			var successfunc = function(xreply) {
				var rdata = new Array();
				rdata = xreply.split('|');
				var rok = parseInt(rdata[0]);
				if (rok == 1) {
					$("#listctgs").flexReload();
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
	}


	/**************************/
	/* ADD/EDIT CATEGORY HTML */
	/**************************/
	public function editCategory($row, $treeitems, $leveltip) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('content:categories/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'ect',
			'label_width' => 180,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);


		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('DETAILS'));
		if ($row->catid) {
			$form->addInfo($eLang->get('ID'), $row->catid);
		}

		$trdata = array('category' => 'com_content', 'element' => 'category_title', 'elid' => intval($row->catid));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 1, 'size' => 50, 'maxlength' => 255));
		$form->addText('seotitle', $row->seotitle, $eLang->get('SEOTITLE'), array('required' => 1, 'dir' => 'ltr', 'size' => 50, 'maxlength' => 160, 'tip' => $eLang->get('SEOTITLE').'|'.$eLang->get('SEOTITLE_DESC')));

		$args = array();
		$args[] = $elxis->makeAURL('content:/', 'inner.php');
		$args[] = 'category';
		$form->addSEO('title', 'seotitle', 'suggestContentSEO', 'validateContentSEO', $args, $args);

		$form->addText('seolink', $row->seolink, $eLang->get('SEO_LINK'), array('required' => 0, 'dir' => 'ltr', 'size' => 70, 'readonly' => 1, 'class' => 'inputbox readonly', 'tip' => $eLang->get('SEO_LINK').'|'.$eLang->get('SEO_LINK_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		if ($treeitems) {
			$sameroot = array();
			foreach ($treeitems as $treeitem) {
				$disabled = 0;
				if ($row->catid) {
					if ($row->catid == $treeitem->catid) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					} elseif ($treeitem->parent_id == $row->catid) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					} else if (in_array($treeitem->parent_id, $sameroot)) {
						$disabled = 1;
						$sameroot[] = $treeitem->catid;
					}
				}
				$options[] = $form->makeOption($treeitem->catid, $treeitem->treename, array(), $disabled);
			}
			unset($sameroot);
		}
		$form->addSelect('parent_id', $eLang->get('PARENT_CTG'), $row->parent_id, $options, array('dir' => 'rtl', 'tip' => $eLang->get('PARENT_CTG').'|'.$eLang->get('PARENT_CTG_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($row->catid) {
			if ($treeitems) {
				foreach ($treeitems as $item) {
					if ($item->parent_id == $row->parent_id) {
						$options[] = $form->makeOption($q, $q.' - '.$item->title);
						$q++;
					}
				}
			}
		}
		$q = ($q > 1) ? $q : 999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options, array('dir' => 'rtl'));
		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr', 'tip' => 'info:'.$eLang->get('ACCESS_LEVEL').'|'.$leveltip));
		if ($elxis->acl()->check('com_content', 'category', 'publish') > 0) {
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		} else {
			if (!$row->catid) { $row->published = 0; }
			$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
			$form->addInfo($eLang->get('PUBLISHED'), $txt);
			$form->addHidden('published', $row->published);
		}
		$form->addImage('image', $row->image, $eLang->get('IMAGE'));
		if (trim($row->image) != '') {
			$options = array();
			$options[] = $form->makeOption(1, $eLang->get('DEL_CUR_IMAGE'));
			$form->addCheckbox('delimage', '', null, $options, array('dir' => 'rtl'));
		}
		$form->closeTab();

		$form->openTab($eLang->get('DESCRIPTION'));
		$trdata = array('category' => 'com_content', 'element' => 'category_description', 'elid' => (int)$row->catid);
		$form->addMLTextarea(
			'description', $trdata, $row->description, $eLang->get('DESCRIPTION'), 
			array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang)
		);
		$form->closeTab();

		$form->openTab($eLang->get('PARAMETERS'));
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$path = ELXIS_PATH.'/components/com_content/content.category.xml';
		$params = new elxisParameters($row->params, $path, 'component');
		$form->addHTML($params->render(array('width' => 260)));
		unset($params);
		$form->closeTab();

		$form->addHidden('task', '');
		$form->addHidden('catid', $row->catid);
		$form->render();
		unset($form);

		echo '<div id="lng_titleempty" style="display:none;">'.addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')))."</div>\n";
		echo '<div id="lng_wait" style="display:none;">'.addslashes($eLang->get('PLEASE_WAIT'))."</div>\n";
	}

}

?>