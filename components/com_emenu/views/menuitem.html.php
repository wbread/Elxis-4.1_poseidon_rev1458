<?php 
/**
* @version		$Id: menuitem.html.php 1331 2012-10-19 16:24:36Z datahell $
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class menuitemEmenuView extends emenuView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/************************/
	/* SHOW MENU ITEMS LIST */
	/************************/
	public function listmenuitems($collection) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('MANAGE_MENU_ITEMS')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lmtimes', $eLang->get('COLLECTION').' '.$collection);
		$grid->setOption('url', $elxis->makeAURL('emenu:mitems/getitems.xml', 'inner.php'));
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('SN'), 'sn', 40, false, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'treename', 300, false, 'auto');
		$grid->addColumn($eLang->get('PUBLISHED'), 'published', 90, false, 'center');
		$grid->addColumn($eLang->get('TYPE'), 'menu_type', 90, false, 'auto');
		$grid->addColumn($eLang->get('ORDERING'), 'ordering', 80, false, 'center');
		$grid->addColumn($eLang->get('EXPAND'), 'expand', 90, false, 'center');
		$grid->addColumn($eLang->get('ACCESS'), 'alevel', 120, false, 'auto');
		$grid->addColumn($eLang->get('ID'), 'menu_id', 40, false, 'center');
		$grid->addColumn('SSL', 'secure', 50, false, 'center');

		if ($elxis->acl()->check('com_emenu', 'menu', 'add') > 0) {
			$grid->addButton($eLang->get('NEW'), 'additem', 'add', 'mitemaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'edititem', 'edit', 'mitemaction');
			$grid->addSeparator();
			$grid->addButton($eLang->get('PUBLISH'), 'publishitem', 'toggle', 'mitemaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_emenu', 'menu', 'delete') > 0) {
			$grid->addButton($eLang->get('DELETE'), 'deleteitem', 'delete', 'mitemaction');
			$grid->addSeparator();
		}

		$filters = array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5, 10=>10, 20=>20);
		$grid->addFilter($eLang->get('MAX_LEVEL'), 'maxlevel', $filters, 10);
		$grid->addHidden('collection', $collection);
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function mitemaction(task, grid) {
			if (task == 'additem') {
				$.colorbox({inline:true, href:'#pickmenutype'});
			} else if (task == 'edititem') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var menu_id = parseInt(items[0].id.substr(3), 10);
					location.href = '<?php echo $elxis->makeAURL('emenu:mitems/edit.html'); ?>?menu_id='+menu_id;
				}
			} else if ((task == 'publishitem') || (task == 'deleteitem')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var menu_id = parseInt(items[0].id.substr(3), 10);
					if ((task == 'publishitem') || ((task == 'deleteitem') && confirm('<?php echo addslashes($eLang->get('WARN_DELETE_MENUITEM')); ?>'))) {
						var edata = {'menu_id': menu_id};
						if (task == 'publishitem') {
							var eurl = '<?php echo $elxis->makeAURL('emenu:mitems/publish', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('emenu:mitems/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok == 1) {
								$("#lmtimes").flexReload();
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

		function moveitem(menu_id, moveup) {
			var edata = {'menu_id': menu_id, 'moveup':moveup };
			var eurl = '<?php echo $elxis->makeAURL('emenu:mitems/move', 'inner.php'); ?>';
			var successfunc = function(xreply) {
				var rdata = new Array();
				rdata = xreply.split('|');
				var rok = parseInt(rdata[0]);
				if (rok == 1) {
					$("#lmtimes").flexReload();
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

		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right': 'left';
		$link = $elxis->makeAURL('emenu:mitems/add.html?collection='.$collection);
?>
		<div style="display:none;">
    		<div id="pickmenutype" style="background-color:#fff; width:600px;">
				<?php echo $eLang->get('SEL_MENUITEM_TYPE'); ?><br /><br />
				<div class="emenu_type_wrap">
					<a href="<?php echo $link; ?>&amp;type=link" class="emenu_type_icon">
						<img src="<?php echo $elxis->icon('link', 32); ?>" alt="link" border="0" />
					</a>
					<div style="float:<?php echo $float; ?>;">
						<a href="<?php echo $link; ?>&amp;type=link" class="emenu_type_link"><?php echo $eLang->get('LINK'); ?></a><br />
						<?php echo $eLang->get('LINK_LINK_DESC'); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="emenu_type_wrap">
					<a href="<?php echo $link; ?>&amp;type=url" class="emenu_type_icon">
						<img src="<?php echo $elxis->icon('world', 32); ?>" alt="url" border="0" />
					</a>
					<div style="float:<?php echo $float; ?>;">
						<a href="<?php echo $link; ?>&amp;type=url" class="emenu_type_link">URL</a><br />
						<?php echo $eLang->get('LINK_URL_DESC'); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="emenu_type_wrap">
					<a href="<?php echo $link; ?>&amp;type=separator" class="emenu_type_icon">
						<img src="<?php echo $elxis->icon('text', 32); ?>" alt="separator" border="0" />
					</a>
					<div style="float:<?php echo $float; ?>;">
						<a href="<?php echo $link; ?>&amp;type=separator" class="emenu_type_link"><?php echo $eLang->get('SEPARATOR'); ?></a><br />
						<?php echo $eLang->get('LINK_SEPARATOR_DESC'); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="emenu_type_wrap">
					<a href="<?php echo $link; ?>&amp;type=wrapper" class="emenu_type_icon">
						<img src="<?php echo $elxis->icon('wrapper', 32); ?>" alt="wrapper" border="0" />
					</a>
					<div style="float:<?php echo $float; ?>;">
						<a href="<?php echo $link; ?>&amp;type=wrapper" class="emenu_type_link"><?php echo $eLang->get('WRAPPER'); ?></a><br />
						<?php echo $eLang->get('LINK_WRAPPER_DESC'); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
<?php 
	}


	/**********************/
	/* ADD/EDIT MENU ITEM */
	/**********************/
	public function editMenuItem($row, $treeitems, $components=null, $leveltip, $component='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('emenu:mitems/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'epr',
			'label_width' => 180,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		switch ($row->menu_type) {
			case 'link':
				if ($row->menu_id) {
					$typetxt = $this->linkTitle($component, $row->link);
				} else {
					$typetxt = $eLang->get('LINK');
				}
			break;
			case 'url': $typetxt = 'URL'; break;
			case 'wrapper': $typetxt = $eLang->get('WRAPPER'); break;
			case 'separator': $typetxt = $eLang->get('SEPARATOR'); break;
			default: $typetxt = $row->menu_type; break;
		}

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('BASIC_SETTINGS'));
		$form->addInfo($eLang->get('TYPE'), $typetxt);
		$trdata = array('category' => 'com_emenu', 'element' => 'title', 'elid' => intval($row->menu_id));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 1, 'size' => 30, 'maxlength' => 255));
		if (($row->menu_type == 'url') || ($row->menu_type == 'wrapper')) {
			$form->addUrl('link', $row->link, $eLang->get('LINK'), array('required' => 1, 'dir' => 'ltr', 'size' => 40, 'maxlength' => 255));
		} else {
			if ($row->menu_type == 'separator') {
				$req = 0;
				$label = $eLang->get('LINK');
			} else {
				$req = 1;
				$label = $eLang->get('ELXIS_LINK');
			}
			$form->addText('link', $row->link, $label, array('required' => $req, 'dir' => 'ltr', 'size' => 40, 'maxlength' => 160));
		}

		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		if ($treeitems) {
			foreach ($treeitems as $treeitem) {
				$disabled = 0;
				if ($row->menu_id) {
					if ($row->menu_id == $treeitem->menu_id) { $disabled = 1; }
				}
				$options[] = $form->makeOption($treeitem->menu_id, $treeitem->treename, array(), $disabled);
			}
		}
		$form->addSelect('parent_id', $eLang->get('PARENT_ITEM'), $row->parent_id, $options, array('dir' => 'rtl', 'tip' => $eLang->get('PARENT_ITEM').'|'.$eLang->get('PARENT_ITEM_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		$q = 1;
		if ($row->menu_id) {
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
		$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		$form->closeFieldset();
		$form->openFieldset($eLang->get('OTHER_OPTIONS'));
		if ($row->menu_type == 'link') {
			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('NO'));
			$options[] = $form->makeOption(1, $eLang->get('LIMITED'));
			$options[] = $form->makeOption(2, $eLang->get('FULL'));
			$form->addSelect('expand', $eLang->get('EXPAND'), $row->expand, $options, array('dir' => 'rtl', 'tip' => $eLang->get('EXPAND').'|'.$eLang->get('EXPAND_DESC')));
		}
		if (($row->menu_type == 'link') || ($row->menu_type == 'wrapper')) {
			$options = array();
			$options[] = $form->makeOption('index.php', $eLang->get('FULL_PAGE'));
			$options[] = $form->makeOption('inner.php', $eLang->get('ONLY_COMPONENT'));
			$form->addSelect('file', $eLang->get('ELXIS_INTERFACE'), $row->file, $options, array('dir' => 'rtl', 'tip' => $eLang->get('ELXIS_INTERFACE').'|'.$eLang->get('ELXIS_INTERFACE_DESC')));
		}

		if ($row->menu_type != 'separator') {
			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('NO'));
			$options[] = $form->makeOption(1, $eLang->get('TYPICAL_POPUP'));
			$options[] = $form->makeOption(2, $eLang->get('LIGHTBOX_WINDOW'));
			$form->addSelect('popup', $eLang->get('POPUP_WINDOW'), $row->popup, $options, array('dir' => 'rtl'));
			$form->addNumber('width', $row->width, $eLang->get('WIDTH'), array('dir' => 'ltr', 'size' => 4, 'maxlength' => 4, 'tip' => $eLang->get('POPUP_WIDTH_DESC')));
			$form->addNumber('height', $row->height, $eLang->get('HEIGHT'), array('dir' => 'ltr', 'size' => 4, 'maxlength' => 4, 'tip' => $eLang->get('POPUP_HEIGHT_DESC')));
			$options = array();
			$options[] = $form->makeOption('', $eLang->get('NONE'));
			$options[] = $form->makeOption('_self', $eLang->get('SELF_WINDOW'));
			$options[] = $form->makeOption('_blank', $eLang->get('NEW_WINDOW'));
			$options[] = $form->makeOption('_parent', $eLang->get('PARENT_WINDOW'));
			$options[] = $form->makeOption('_top', $eLang->get('TOP_WINDOW'));
			$form->addSelect('target', $eLang->get('LINK_TARGET'), $row->target, $options, array('dir' => 'ltr'));
		}

		if (($row->menu_type == 'link') || ($row->menu_type == 'wrapper')) {
			$form->addYesNo('secure', $eLang->get('SECURE_CONNECT'), $row->secure, array('tip' => 'warning:SSL|'.$eLang->get('SECURE_CONNECT_DESC')));
		}
		$form->closeFieldset();

		$form->addHidden('section', $row->section);
		$form->addHidden('collection', $row->collection);
		$form->addHidden('menu_type', $row->menu_type);
		$form->addHidden('menu_id', $row->menu_id);
		$form->addHidden('emenuurl', $elxis->makeAURl('emenu:/', 'inner.php'));
		$form->addHidden('loadingtxt', $eLang->get('LOADING'));
		$form->addHidden('task', '');
		if (($row->menu_type != 'link') && ($row->menu_type != 'wrapper')) {
			$form->addHidden('file', '');
		}
		if ($row->menu_type != 'link') { $form->addHidden('expand', 0); }
		if ($row->menu_type == 'separator') {
			$form->addHidden('popup', 0);
			$form->addHidden('width', 0);
			$form->addHidden('height', 0);
			$form->addHidden('target', '');
		}
		if (($row->menu_type != 'link') && ($row->menu_type != 'wrapper')) { $form->addHidden('secure', 0); }
?>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" dir="<?php echo $eLang->getinfo('DIR'); ?>">
		<tr>
		<td width="650" align="top" style="vertical-align:top">
		<?php 
		$form->render();
		unset($form);
		?>
		</td>
		<td align="top" style="vertical-align:top">
		<?php $this->initMenuHelper($row->menu_type, $components, $component); ?>
		</td>
		</tr>
		</table>
<?php 
	}


	/*************************************************************************/
	/* GET A TITLE FOR THE CURRENT LINK (ONLY FOR EDIT AND MENU TYPE = LINK) */
	/*************************************************************************/
	private function linkTitle($component, $link) {
		$eLang = eFactory::getLang();
		if ($component == '') { return $eLang->get('LINK'); }
		if ($component == 'content') {
			if (($link == '') || ($link == '/') || ($link == 'content:/')) {
				return $eLang->get('FRONTPAGE');
			} else if (preg_match('#(\/)$#', $link)) {
				return $eLang->get('LINK_TO_CAT');
			} else if ($link == 'feeds.html') {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			} else if (strpos('tags.html', $link) === 0) {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			} else if (preg_match('#(\.html)$#', $link)) {
				if (strpos($link, '/') !== false) {
					return $eLang->get('LINK_TO_CAT_ARTICLE');
				} else {
					return $eLang->get('LINK_TO_AUT_PAGE');
				}
			} else if (preg_match('#(rss\.xml)$#', $link)) {
				return $eLang->get('LINK_TO_CAT_RSS');
			} else if (preg_match('#(atom\.xml)$#', $link)) {
				return $eLang->get('LINK_TO_CAT_ATOM');
			} else {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.$eLang->get('CONTENT').')</span>';
			}
		} else {
			if ($link == $component.':/') {
				return sprintf($eLang->get('COMP_FRONTPAGE'), ucfirst($component));
			} else {
				return $eLang->get('SPECIAL_LINK').' <span dir="ltr">('.ucfirst($component).')</span>';
			}
		}
	}


	/************************************/
	/* INITIALIZE MENU GENERATOR/HELPER */
	/************************************/
	private function initMenuHelper($menu_type, $components, $component='') {
		$eLang = eFactory::getLang();
		echo '<div style="margin:0 10px; padding:0px;">'."\n";
		if ($menu_type == 'link') {
			echo '<h3>'.$eLang->get('LINK_GENERATOR')."</h3>\n";
			echo $eLang->get('SEL_COMPONENT').' <select name="pickcomponent" id="pickcomponent" onchange="emenu_pickcomponent()">'."\n";
			if ($component == '') {
				echo '<option value="" selected="selected">- '.$eLang->get('SELECT')." -</option>\n";
			}
			if ($components) {
				foreach ($components as $key => $val) {
					$sel = ($key == 'com_'.$component) ? ' selected="selected"' : '';
					echo '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'."\n";
				}
			}
			echo "</select><br />\n";
			echo '<div id="emenu_generator" style="margin:10px 0; overflow-x:none; overflow-y:auto; width:100%; height:350px;"></div>'."\n";
		} else if ($menu_type == 'url') {
			echo "<h3>URL</h3>\n";
			echo '<div style="margin:10px 0;">'.$eLang->get('URL_HELPER')."</div>\n";
		} else if ($menu_type == 'wrapper') {
			echo '<h3>'.$eLang->get('WRAPPER')."</h3>\n";
			echo '<div style="margin:10px 0;">'.$eLang->get('WRAPPER_HELPER')."</div>\n";
			echo '<div class="elx_notice">'.$eLang->get('TIP_INTERFACE')."</div>\n";
		} else if ($menu_type == 'separator') {
			echo '<h3>'.$eLang->get('SEPARATOR')."</h3>\n";
			echo '<div style="margin:10px 0;">'.$eLang->get('SEPARATOR_HELPER')."</div>\n";
		} else {
		}
		echo "</div>\n";
	}


	/********************/
	/* GENERATOR OUTPUT */
	/********************/
	public function linkGeneratorOutput($items, $xmlmenus, $cname) {
		$eLang = eFactory::getLang();

		if (is_array($items) && (count($items) > 0)) {
			echo '<ul class="emenu_gblock">'."\n";
			echo '<li class="emenu_gtitle">'.$eLang->get('STANDARD_LINKS')."</li>\n";
			foreach ($items as $item) {
				$this->showAddLink($item, 0);
			}
			if ($cname == 'content') {
				$pop = eFactory::getElxis()->makeAURL('emenu:mitems/browser.html', 'inner.php');
				echo '<li class="emenu_glevel0">'."\n";
				echo "\t".'<a href="javascript:void(null);" onclick="elxPopup(\''.$pop.'\', 800, 440, \'browser\')">'.$eLang->get('LINK_TO_CAT_OR_ARTICLE')."</a>\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
		}
		if (is_array($xmlmenus) && (count($xmlmenus) > 0)) {
			foreach ($xmlmenus as $xmlmenu) {
				if (!is_array($xmlmenu->items) || (count($xmlmenu->items) == 0)) { continue; }
				echo '<ul class="emenu_gblock">'."\n";
				echo '<li class="emenu_gtitle">'.$eLang->get('MENU').': '.$xmlmenu->title."</li>\n";
				foreach ($xmlmenu->items as $item) {
					$this->showAddLink($item, 0, true);
					if (count($item->children) > 0) {
						foreach ($item->children as $level1) {
							$this->showAddLink($level1, 1, true);
							if (count($level1->children) > 0) {
								foreach ($level1->children as $level2) {
									$this->showAddLink($level2, 2, true);
									if (count($level2->children) > 0) {
										foreach ($level2->children as $level3) {
											$this->showAddLink($level3, 3, true);
											if (count($level3->children) > 0) {
												foreach ($level3->children as $level4) {
													$this->showAddLink($level4, 4, true);
												}
											}
										}
									}
								}
							}
						}
					}
				}
				echo "</ul>\n";
			}
		}
	}


	/********************************/
	/* SHOW GENERATOR ADDITION LINK */
	/********************************/
	private function showAddLink($item, $level=0, $multiplier=false) {
		$title = addslashes($item->title);
		$alevel = ($multiplier === true) ? $item->alevel * 1000 : $item->alevel;
		echo '<li class="emenu_glevel'.$level.'">'."\n";
		echo "\t".'<a href="javascript:void(null);" onclick="emenu_setlink(\''.$title.'\', \''.$item->link.'\', '.$item->secure.', '.$alevel.')">'.$item->name."</a>\n";
		echo "</li>\n";
	}


	/***************************/
	/* CATEGORIES BROWSER HTML */
	/***************************/
	public function categoriesBrowser($rows, $paths, $options, $allgroups) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$n = count($paths) - 1;

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="6">'."\n";
		foreach ($paths as $i => $path) {
			$cattitle = $path->title;
			$len = eUTF::strlen($path->title);
			$title = ($len > 20) ? eUTF::substr($path->title, 0, 17).'...' : $path->title;
			echo '<a href="'.$browser_link.'?catid='.$path->catid.'&amp;t=c&amp;o='.$options['order'].'" title="'.$path->title.'">'.$title.'</a>';
			if ($i < $n) { echo " &#187; \n"; }
		}
		echo "</th></tr>\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter" width="40">'.$eLang->get('ID')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('CATEGORY')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('PUBLISHED')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ARTICLES')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACCESS')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACTIONS')."</th>\n";
		echo "</tr>\n";
		$k = 0;
		$folder_icon = $elxis->icon('folder', 16);
		if ($rows) {
			$link_icon = $elxis->icon('link', 16);
			$rss_icon = $elxis->icon('rss', 16);
			$atom_icon = $elxis->icon('atom', 16);
			$pub_icon = $elxis->icon('tick', 16);
			$unpub_icon = $elxis->icon('error', 16);

			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pub_icon : $unpub_icon;
				$title = addslashes($row->title);
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				echo '<tr class="elx_tr'.$k.'">'."\n";
				echo '<td class="elx_td_center">'.$row->catid."</td>\n";
				echo '<td><a href="'.$browser_link.'?catid='.$row->catid.'&amp;t=c&amp;o='.$options['order'].'">'.$row->title."</a></td>\n";
				echo '<td class="elx_td_center"><img src="'.$picon.'" alt="publish status" border="0" />'."</td>\n";
				echo '<td class="elx_td_center">'.$row->articles."</td>\n";
				echo '<td class="elx_td_center">'.$acctxt."</td>\n";
				echo '<td class="elx_td_center">'."\n";
				echo '<a href="'.$browser_link.'?catid='.$row->catid.'&amp;t=a&amp;o='.$options['order'].'" title="'.$eLang->get('BROWSE_ARTICLES').'"><img src="'.$folder_icon.'" alt="browse" border="0" /></a> &#160; '."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_ITEM').'" onclick="emenu_osetlink(\''.$title.'\', \'content:'.$row->seolink.'\', 0, '.$row->alevel.')"><img src="'.$link_icon.'" alt="add" border="0" /></a> &#160; '."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_CAT_RSS').'" onclick="emenu_osetlink(\''.$title.' RSS\', \'content:'.$row->seolink.'rss.xml\', 0, 0)"><img src="'.$rss_icon.'" alt="RSS" border="0" /></a> &#160; '."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_CAT_ATOM').'" onclick="emenu_osetlink(\''.$title.' ATOM\', \'content:'.$row->seolink.'atom.xml\', 0, 0)"><img src="'.$atom_icon.'" alt="ATOM" border="0" /></a>'."\n";
				echo "</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
			}
		} else {
			echo '<tr class="elx_trx">'."\n";
			echo '<td class="elx_td_center" colspan="6">'.$eLang->get('NO_ITEMS_DISPLAY')."</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";

		echo '<div style="margin:5px 10px;">'."\n";
		if ($options['catid'] == 0) {
			$txt = sprintf($eLang->get('ART_WITHOUT_CAT'), '<strong>'.$options['articles'].'</strong>');
		} else {
			$txt = sprintf($eLang->get('CAT_CONT_ART'), '<strong>'.$cattitle.'</strong>', '<strong>'.$options['articles'].'</strong>');
		}

		echo '<a href="'.$browser_link.'?catid='.$options['catid'].'&amp;t=a&amp;o='.$options['order'].'" title="'.$eLang->get('BROWSE_ARTICLES').'" class="emenu_catarts">';
		echo '<img src="'.$folder_icon.'" alt="browse" border="0" /> '.$txt.'</a><br />'."\n";

		if ($options['maxpage'] > 1) {
			$linkbase = $browser_link.'?catid='.$options['catid'].'&amp;t='.$options['type'].'&amp;o='.$options['order'];
			$navigation = $elxis->obj('navigation')->navLinks($linkbase, $options['page'], $options['maxpage']);
			echo $navigation;
		}
		$this->sortingOptions($options);
		echo "</div>\n";
	}


	/*************************/
	/* ARTICLES BROWSER HTML */
	/*************************/
	public function articlesBrowser($rows, $paths, $options, $allgroups) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$ctg_seolink = '';
		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="5">'."\n";
		foreach ($paths as $i => $path) {
			$len = eUTF::strlen($path->title);
			$title = ($len > 20) ? eUTF::substr($path->title, 0, 17).'...' : $path->title;
			$ctg_seolink = $path->seolink;
			echo '<a href="'.$browser_link.'?catid='.$path->catid.'&amp;t=c&amp;o='.$options['order'].'" title="'.$path->title.'">'.$title.'</a>';
			echo " &#187; \n";
		}
		echo $eLang->get('ARTICLES')."\n";
		echo "</th></tr>\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter" width="40">'.$eLang->get('ID')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('ARTICLE')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('PUBLISHED')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACCESS')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('ACTIONS')."</th>\n";
		echo "</tr>\n";
		$k = 0;
		if ($rows) {
			$link_icon = $elxis->icon('link', 16);
			$folder_icon = $elxis->icon('folder', 16);
			$pub_icon = $elxis->icon('tick', 16);
			$unpub_icon = $elxis->icon('error', 16);
			$browser_link = $elxis->makeAURL('emenu:mitems/browser.html', 'inner.php');

			foreach ($rows as $row) {
				$picon = ($row->published == 1) ? $pub_icon : $unpub_icon;
				$title = addslashes($row->title);
				$acctxt = $elxis->alevelToGroup($row->alevel, $allgroups);

				echo '<tr class="elx_tr'.$k.'">'."\n";
				echo '<td class="elx_td_center">'.$row->id."</td>\n";
				echo '<td>'.$row->title."</td>\n";
				echo '<td class="elx_td_center"><img src="'.$picon.'" alt="publish status" border="0" />'."</td>\n";
				echo '<td class="elx_td_center">'.$acctxt."</td>\n";
				echo '<td class="elx_td_center">'."\n";
				echo '<a href="javascript:void(null);" title="'.$eLang->get('LINK_TO_ITEM').'" onclick="emenu_osetlink(\''.$title.'\', \'content:'.$ctg_seolink.$row->seotitle.'.html\', 0, '.$row->alevel.')"><img src="'.$link_icon.'" alt="add" border="0" /></a>'."\n";
				echo "</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
			}
		} else {
			echo '<tr class="elx_trx">'."\n";
			echo '<td class="elx_td_center" colspan="5">'.$eLang->get('NO_ITEMS_DISPLAY')."</td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";

		echo '<div style="margin:5px 10px;">'."\n";
		if ($options['maxpage'] > 1) {
			$linkbase = $browser_link.'?catid='.$options['catid'].'&amp;t='.$options['type'].'&amp;o='.$options['order'];
			$navigation = $elxis->obj('navigation')->navLinks($linkbase, $options['page'], $options['maxpage']);
			echo $navigation;
		}
		$this->sortingOptions($options);
		echo "</div>\n";
	}


	/***************************************/
	/* DISPLAY SORTING OPTIONS FOR BROWSER */
	/***************************************/
	private function sortingOptions($options) {
		$eLang = eFactory::getLang();

		$browser_link = eFactory::getElxis()->makeAURL('emenu:mitems/browser.html', 'inner.php');
		$sorts = array(
			'oa' => $eLang->get('ORDERING').' '.$eLang->get('ASCENDING'),
			'od' => $eLang->get('ORDERING').' '.$eLang->get('DESCENDING'),
			'ta' => $eLang->get('TITLE').' '.$eLang->get('ASCENDING'),
			'td' => $eLang->get('TITLE').' '.$eLang->get('DESCENDING'),
			'ia' => $eLang->get('ID').' '.$eLang->get('ASCENDING'),
			'id' => $eLang->get('ID').' '.$eLang->get('DESCENDING')
		);
		if ($options['type'] == 'a') {
			$sorts['da'] = $eLang->get('DATE').' '.$eLang->get('ASCENDING');
			$sorts['dd'] = $eLang->get('DATE').' '.$eLang->get('DESCENDING');
			$sorts['ma'] = $eLang->get('LAST_MODIFIED').' '.$eLang->get('ASCENDING');
			$sorts['md'] = $eLang->get('LAST_MODIFIED').' '.$eLang->get('DESCENDING');
		}

		echo '<form name="emchangesort" id="emchangesort" method="get" action="'.$browser_link.'" class="elx_form" style="margin:10px 0 0 0;">'."\n";
		echo $eLang->get('ORDERING').' <select name="o" class="selectbox" onchange="this.form.submit();">'."\n";
		foreach ($sorts as $key => $name) {
			$sel = ($key == $options['order']) ? ' selected="selected"' : '';
			echo '<option value="'.$key.'"'.$sel.'>'.$name."</option>\n";
		}
		echo "</select>\n";
		echo '<input type="hidden" name="catid" value="'.$options['catid'].'" />'."\n";
		echo '<input type="hidden" name="t" value="'.$options['type'].'" />'."\n";
		echo '<input type="hidden" name="page" value="'.$options['page'].'" />'."\n";
		echo "</form>\n";
	}

}

?>