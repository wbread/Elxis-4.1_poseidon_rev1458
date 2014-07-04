<?php 
/**
* @version		$Id: aarticle.html.php 1407 2013-04-10 19:03:03Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class aarticleContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* DISPLAY ARTICLES LIST */
	/*************************/
	public function listarticles($categories, $categories_tree) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$init_catid = (isset($_GET['catid'])) ? (int)$_GET['catid'] : -1;

		$init_query = '';
		if (isset($_GET['q'])) {
			$q = filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\}]|[\\\])#u";
			$q = eUTF::trim(preg_replace($pat, '', $q));
			if (eUTF::strlen($q) > 2) {
				if (strpos($elxis->getConfig('DOCTYPE'), 'xhtml') !== false) {
					$init_query = '&amp;q='.$q;
				} else {
					$init_query = '&q='.$q;
				}
			}
		}

		echo '<h2>'.$eLang->get('ARTICLES')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('listctgs', $eLang->get('ARTICLES_LIST'));
		$grid->setOption('url', $elxis->makeAURL('content:articles/getarticles.xml', 'inner.php').'?catid='.$init_catid.$init_query);
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('singleSelect', false);
		$grid->setOption('sortname', 'created');
		$grid->setOption('sortorder', 'desc');

		$grid->addColumn($eLang->get('ID'), 'id', 50, true, 'center');
		$grid->addColumn($eLang->get('TITLE'), 'title', 200, true, 'auto');
		$grid->addColumn($eLang->get('PUBLISHED'), 'published', 90, true, 'center');
		$grid->addColumn($eLang->get('CATEGORY'), 'catid', 160, true, 'auto');
		$grid->addColumn($eLang->get('ORDERING'), 'ordering', 80, true, 'auto');
		$grid->addColumn($eLang->get('ACCESS'), 'alevel', 100, false, 'auto');
		$grid->addColumn($eLang->get('DATE'), 'created', 120, true, 'auto');
		$grid->addColumn($eLang->get('AUTHOR'), 'created_by_name', 100, true, 'auto');
		$grid->addColumn($eLang->get('HITS'), 'hits', 70, true, 'center');

		if ($elxis->acl()->check('com_content', 'article', 'add') > 0) {
			$grid->addButton($eLang->get('NEW'), 'addart', 'add', 'artaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'article', 'edit') > 0) {
			$grid->addButton($eLang->get('EDIT'), 'editart', 'edit', 'artaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'article', 'publish') > 0) {
			$grid->addButton($eLang->get('PUBLISH'), 'publishart', 'toggle', 'artaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'article', 'delete') > 0) {
			$grid->addButton($eLang->get('DELETE'), 'deleteart', 'delete', 'artaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'article', 'add') > 1) {
			$grid->addButton($eLang->get('COPY'), 'copyart', 'copy', 'artaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_content', 'article', 'edit') > 1) {
			$grid->addButton($eLang->get('MOVE'), 'moveart', 'move', 'artaction');
			$grid->addSeparator();
		}
		$grid->addButton($eLang->get('TOGGLE_SELECTED'), 'togglerows', 'toggle', 'artaction');
		$grid->addSeparator();

		$filters = array();
		$filters[-1] = '- '.$eLang->get('ALL_CATEGORIES').' -';
		$filters[0] = '- '.$eLang->get('NONE').' -';
		if ($categories) {
			foreach ($categories as $category) {
				$catid = sprintf("%03d", $category->catid);
				$filters[ $category->catid ] = sprintf("%03d", $category->catid).' - '.addslashes($category->title);
			}
		}
		$grid->addFilter($eLang->get('CATEGORY'), 'catid', $filters, $init_catid);

		$grid->addSearch($eLang->get('ID'), 'id', false);
		$grid->addSearch($eLang->get('TITLE'), 'title', true);
		$grid->addSearch($eLang->get('SEOTITLE'), 'seotitle', false);
		$grid->addSearch($eLang->get('WRITTEN_BY'), 'created_by_name', false);
		$grid->addSearch($eLang->get('MODIFIED_BY'), 'modified_by_name', false);
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function artaction(task, grid) {
			if (task == 'addart') {
				location.href = '<?php echo $elxis->makeAURL('content:articles/add.html'); ?>';
			} else if (task == 'editart') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var id = parseInt(items[0].id.substr(3), 10);
					location.href = '<?php echo $elxis->makeAURL('content:articles/edit.html'); ?>?id='+id;
				}
			} else if ((task == 'publishart') || (task == 'deleteart')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var aids = '';
					for (i=0; i<nsel; i++) {
						if (i == 0) {
							aids += items[i].id.substr(3);
						} else {
							aids += ','+items[i].id.substr(3);
						}
					}
					if ((task == 'publishart') || ((task == 'deleteart') && confirm('<?php echo addslashes($eLang->get('WARN_DELETE_ARTICLES')); ?>'))) {
						var edata = {'ids': aids};
						if (task == 'publishart') {
							var eurl = '<?php echo $elxis->makeAURL('content:articles/publish', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('content:articles/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0], 10);
							if (rok == 1) {
								$("#listctgs").flexReload();
							} else {
								alert(rdata[1]);
							}
						}
						elxAjax('POST', eurl, edata, null, null, successfunc, null);
					}
				}
			} else if ((task == 'copyart') || (task == 'moveart')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var aids = '';
					for (i=0; i<nsel; i++) {
						if (i == 0) {
							aids += items[i].id.substr(3);
						} else {
							aids += ','+items[i].id.substr(3);
						}
					}
					document.getElementById('cpmverror').innerHTML = '';
					document.getElementById('cpmvaids').value = aids;
					if (task == 'copyart') {
						document.getElementById('cpmvbtn').textContent = '<?php echo $eLang->get('COPY'); ?>';
						document.getElementById('cpmvact').value = 'copy';
					} else {
						document.getElementById('cpmvbtn').textContent = '<?php echo $eLang->get('MOVE'); ?>';
						document.getElementById('cpmvact').value = 'move';
					}
					$.colorbox({inline:true, href:'#pickcategory'});
				}
			} else if (task == 'togglerows') {
				$('tr',grid).toggleClass('trSelected');
			} else {
				alert('Invalid request!');
			}
		}

		function cpmvarticles() {
			var aids = document.getElementById('cpmvaids').value;
			if (aids == '') { return; }
			var act = document.getElementById('cpmvact').value;
			var catobj = document.getElementById('cpmvcategory');	
    		var catid = parseInt(catobj.options[catobj.selectedIndex].value, 10);
			if (act == 'copy') {
				var eurl = '<?php echo $elxis->makeAURL('content:articles/copy', 'inner.php'); ?>';
			} else {
				var eurl = '<?php echo $elxis->makeAURL('content:articles/move', 'inner.php'); ?>';
			}
			var edata = {'aids': aids, 'catid':catid };
			document.getElementById('cpmverror').innerHTML = '<?php echo $eLang->get('PLEASE_WAIT'); ?>';
			var successfunc = function(xreply) {
				var rdata = new Array();
				rdata = xreply.split('|');
				var rok = parseInt(rdata[0], 10);
				if (rok == 1) {
					document.getElementById('cpmverror').innerHTML = '';
					$.colorbox.close();
					$("#listctgs").flexReload();
				} else {
					if (rdata[1]) {
						document.getElementById('cpmverror').innerHTML = rdata[1];
					} else {
						document.getElementById('cpmverror').innerHTML = 'Action failed!';
					}
				}
			}
			elxAjax('POST', eurl, edata, null, null, successfunc, null);
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		echo '<div id="acontentbase" style="display:none; visibility:hidden;" dir="ltr">'.$elxis->makeAURL('content:/', 'inner.php')."</div>\n";
?>
		<div style="display:none;">
    		<div id="pickcategory" style="background-color:#fff; width:600px; padding:10px;">
    		<form name="cpomvartsfm" method="post">
    			<input type="hidden" name="cpmvaids" id="cpmvaids" value="" dir="ltr" />
    			<input type="hidden" name="cpmvact" id="cpmvact" value="copy" dir="ltr" />
<?php 
				echo $eLang->get('CATEGORY').' : ';
				echo '<select name="cpmvcategory" id="cpmvcategory" class="selectbox" dir="ltr">'."\n";
				echo '<option value="0" selected="selected">- '.$eLang->get('NONE').' -</option>'."\n";
				if ($categories_tree) {
					foreach ($categories_tree as $citem) {
						echo '<option value="'.$citem->catid.'">'.$citem->treename."</option>\n";
					}
				}
				echo "</select> \n";
				echo '<button type="button" name="cpmvbtn" id="cpmvbtn" value="cpmvarts" class="elxbutton" onclick="cpmvarticles();">'.$eLang->get('COPY').'/'.$eLang->get('MOVE')."</button>\n";
				echo '<div id="cpmverror" style="margin:5px 0; height:32px;"></div>'."\n";
?>
			</form>
			</div>
		</div>
<?php 
	}


	/*************************/
	/* ADD/EDIT ARTICLE HTML */
	/*************************/
	public function editArticle($row, $treeitems, $leveltip, $ordering, $comments) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$clang = $elxis->getConfig('LANG');
		$cinfo = $eLang->getallinfo($clang);

		$action = $elxis->makeAURL('content:articles/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'ear',
			'label_width' => 200,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('DETAILS'));
		if ($row->id) {
			$form->addInfo($eLang->get('ID'), $row->id);
		}

		$trdata = array('category' => 'com_content', 'element' => 'title', 'elid' => intval($row->id));
		$form->addMLText('title', $trdata, $row->title, $eLang->get('TITLE'), array('required' => 1, 'size' => 50, 'maxlength' => 255));
		$form->addText('seotitle', $row->seotitle, $eLang->get('SEOTITLE'), array('required' => 1, 'dir' => 'ltr', 'size' => 50, 'maxlength' => 160, 'tip' => $eLang->get('SEOTITLE').'|'.$eLang->get('SEOTITLE_DESC')));

		$args = array();
		$args[] = $elxis->makeAURL('content:/', 'inner.php');
		$args[] = 'article';
		$form->addSEO('title', 'seotitle', 'suggestContentSEO', 'validateContentSEO', $args, $args);

		$catseolink = '';
		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('NONE').' -');
		if ($treeitems) {
			foreach ($treeitems as $treeitem) {
				if ($row->catid == $treeitem->catid) { $catseolink = $treeitem->seolink; }
                $disabled = (($treeitem->alevel <= $elxis->acl()->getLowLevel()) || ($treeitem->alevel == $elxis->acl()->getExactLevel())) ? 0 : 1;
				$options[] = $form->makeOption($treeitem->catid, $treeitem->treename, array(), $disabled);
			}
		}

		if (trim($row->id) > 0) {
			$form->addInfo($eLang->get('SEO_LINK'), $catseolink.$row->seotitle.'.html');
		} else {
			$form->addInfo($eLang->get('SEO_LINK'), $eLang->get('SEO_LINK_DESC'));
		}
		unset($catseolink);
		$form->addSelect('catid', $eLang->get('CATEGORY'), $row->catid, $options, array('dir' => 'rtl'));

		$trdata = array('category' => 'com_content', 'element' => 'subtitle', 'elid' => intval($row->id));
		$form->addMLText('subtitle', $trdata, $row->subtitle, $eLang->get('SUBTITLE'), array('required' => 1, 'size' => 60, 'maxlength' => 255, 'tip' => $eLang->get('SUBTITLE').'|'.$eLang->get('SUBTITLE_DESC')));

		$trdata = array('category' => 'com_content', 'element' => 'metakeys', 'elid' => intval($row->id));
		$form->addMLText('metakeys', $trdata, $row->metakeys, $eLang->get('METAKEYS'), array('size' => 60, 'maxlength' => 255, 'tip' => $eLang->get('METAKEYS').'|'.$eLang->get('METAKEYS_DESC')));

		$options = array();
		$options[] = $form->makeOption(0, '- '.$eLang->get('FIRST'));
		if ($row->id) {
			if ($ordering['total'] > 0) {
				if (is_array($ordering['articles']) && (count($ordering['articles']) > 0)) {
					if ($ordering['start'] > 0) {
						$options[] = $form->makeOption(1, '1 - '.$eLang->get('FIRST_ARTICLE'));
						$options[] = $form->makeOption(-1, '...', array(), 1);
					}
					$found = false;
					foreach ($ordering['articles'] as $article) {
						if ($article['id'] == $row->id) { $found = true; }
						$options[] = $form->makeOption($article['ordering'], $article['ordering'].' - '.$article['title']); 
					}
					if (!$found) {
						$options[] = $form->makeOption($row->ordering, $row->ordering.' - '.$row->title);
					}
					if ($ordering['end'] < $ordering['total']) {
						$options[] = $form->makeOption(-2, '...', array(), 1);
						$options[] = $form->makeOption($ordering['total'], $ordering['total'].' - '.$eLang->get('LAST_ARTICLE'));
					}
				}
			}
		}
		$q = ($row->id) ? $ordering['total'] + 1 : 9999;
		$options[] = $form->makeOption($q, '- '.$eLang->get('LAST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $row->ordering, $options, array('dir' => 'rtl'));

		$form->addAccesslevel('alevel', $eLang->get('ACCESS_LEVEL'), $row->alevel, $elxis->acl()->getLevel(), array('dir' => 'ltr', 'tip' => 'info:'.$eLang->get('ACCESS_LEVEL').'|'.$leveltip));

		$pubaccess = $elxis->acl()->check('com_content', 'article', 'publish');
		if (!$row->id) { $row->published = 0; }
		if ($pubaccess > 1) {
			$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
		} else if ($pubaccess == 1) {
			if ($row->created_by == $elxis->user()->uid) {
				$form->addYesNo('published', $eLang->get('PUBLISHED'), $row->published);
			} else {
				$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
				$form->addInfo($eLang->get('PUBLISHED'), $txt);
				$form->addHidden('published', $row->published);
			}
		} else {
			$txt = (intval($row->published) == 1) ? $eLang->get('YES') : $eLang->get('NO');
			$form->addInfo($eLang->get('PUBLISHED'), $txt);
			$form->addHidden('published', $row->published);
		}
		unset($pubaccess);

		$form->addImage('image', $row->image, $eLang->get('IMAGE'));
		if (trim($row->image) != '') {
			$options = array();
			$options[] = $form->makeOption(1, $eLang->get('DEL_CUR_IMAGE'));
			$form->addCheckbox('delimage', '', null, $options, array('dir' => 'rtl'));
		}

		$trdata = array('category' => 'com_content', 'element' => 'caption', 'elid' => intval($row->id));
		$form->addMLText('caption', $trdata, $row->caption, $eLang->get('CAPTION'), array('size' => 60, 'maxlength' => 255, 'tip' => $eLang->get('CAPTION').'|'.$eLang->get('CAPTION_DESC')));

		$created_user_text = $row->created_by_name;
		if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
			$access = $elxis->acl()->check('com_user', 'profile', 'edit');
			if (($access == 2) || (($access == 1) && ($elxis->user()->uid == $row->created_by))) {
				$link = $elxis->makeAURL('user:users/edit.html').'?uid='.$row->created_by;
				$created_user_text = '<a href="'.$link.'" title="'.$eLang->get('EDIT').' '.$row->created_by_name.'">'.$row->created_by_name.'</a>';
			}
			unset($access);
		}

		$form->addInfo($eLang->get('DATE'), $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_11')));
		$form->addInfo($eLang->get('AUTHOR'), $created_user_text);

		if ($row->id) {
			if ($row->modified != '1970-01-01 00:00:00') {
				$mod_date = $eDate->formatDate($row->modified, $eLang->get('DATE_FORMAT_11'));
			} else {
				$mod_date = $eLang->get('NEVER');
			}
			$form->addInfo($eLang->get('MODIFIED_DATE'), $mod_date);

			if (($row->modified_by > 0) && ($elxis->acl()->check('component', 'com_user', 'manage') > 0)) {
				$access = $elxis->acl()->check('com_user', 'profile', 'edit');
				if (($access == 2) || (($access == 1) && ($elxis->user()->uid == $row->modified_by))) {
					$link = $elxis->makeAURL('user:edit.html').'?uid='.$row->modified_by;
					$modified_user_text = '<a href="'.$link.'" title="'.$eLang->get('EDIT').' '.$row->modified_by_name.'">'.$row->modified_by_name.'</a>';
					$form->addInfo($eLang->get('AUTHOR'), $modified_user_text);
					unset($modified_user_text);
				}
				unset($access);
			}
		}

		$form->addInfo($eLang->get('HITS'), $row->hits);
		$form->closeTab();

		$form->openTab($eLang->get('ARTICLE_BODY'));
		$trdata = array('category' => 'com_content', 'element' => 'introtext', 'elid' => (int)$row->id);
		$form->addMLTextarea('introtext', $trdata, $row->introtext, $eLang->get('INTRO_TEXT'), array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang));
		$trdata = array('category' => 'com_content', 'element' => 'maintext', 'elid' => (int)$row->id);
		$form->addMLTextarea('maintext', $trdata, $row->maintext, $eLang->get('MAIN_TEXT'), array('cols' => 80, 'rows' => 8, 'forcedir' => $cinfo['DIR'], 'editor' => 'html', 'contentslang' => $clang));
		$form->closeTab();

		$form->openTab($eLang->get('PARAMETERS'));
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$path = ELXIS_PATH.'/components/com_content/content.article.xml';
		$params = new elxisParameters($row->params, $path, 'component');
		$form->addHTML($params->render(array('width' => 260)));
		unset($params);
		$form->closeTab();

		$pubicon = $elxis->icon('tick', 16);
		if (is_array($comments) && (count($comments) > 0)) {
			$unpubicon = $elxis->icon('error', 16);
			$delicon = $elxis->icon('delete', 16);
			$pubaccess = $elxis->acl()->check('com_content', 'comments', 'publish');
			$delaccess = $elxis->acl()->check('com_content', 'comments', 'delete');
			$profaccess = $elxis->acl()->check('com_user', 'profile', 'edit');
			$proflink = $elxis->makeAURL('user:edit.html');

			$buffer = '<div class="elx_tbl_wrapper">'."\n";
			$buffer .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list" id="comments_table">'."\n";
			$buffer .= "<tr>\n";
			$buffer .= '<th class="elx_th_subcenter" width="40">#'."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('DATE')."</th>\n";
			$buffer .= '<th class="elx_th_sub">'.$eLang->get('AUTHOR')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('PUBLISHED')."</th>\n";
			$buffer .= '<th class="elx_th_subcenter">'.$eLang->get('DELETE')."</th>\n";
			$buffer .= '<th class="elx_th_sub">Message'."</th>\n";
			$buffer .= "</tr>\n";
			$k = 0;
			$i = 1;
			foreach ($comments as $comment) {
				$cdate = $eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_4'));
				$picon = ($comment->published == 1) ? $pubicon : $unpubicon;
				$authortxt = $comment->author;
				if ($comment->uid > 0) {
					if (($profaccess == 2) || (($profaccess == 1) && ($comment->uid == $elxis->user()->uid))) {
						$authortxt = '<a href="'.$proflink.'?uid='.$comment->uid.'" title="'.$eLang->get('EDIT').'">'.$comment->author.'</a>';
					}
				}

				if (($delaccess == 2) || (($delaccess == 1) && ($comment->uid == $elxis->user()->uid))) {
					$deletetxt = '<a href="javascript:void(null);" onclick="deleteComment('.$comment->id.')" title="'.$eLang->get('DELETE').'"><img src="'.$delicon.'" alt="delete" border="0" id="delicon'.$comment->id.'" /></a>';
				} else {
					$deletetxt = '<img src="'.$delicon.'" alt="delete" border="0" />';
				}

				$publishtxt = '<img src="'.$picon.'" alt="icon" border="0" />';
				if ($comment->published == 0) {
					if (($pubaccess == 2) || (($pubaccess == 1) && ($comment->uid == $elxis->user()->uid))) {
						$publishtxt = '<a href="javascript:void(null);" onclick="publishComment('.$comment->id.')" title="'.$eLang->get('PUBLISH').'"><img src="'.$picon.'" alt="publish" border="0" id="pubicon'.$comment->id.'" /></a>';
					}
				}

				$buffer .= '<tr class="elx_tr'.$k.'" id="comment_row'.$comment->id.'">'."\n";
				$buffer .= '<td class="elx_td_center">'.$i."</td>\n";
				$buffer .= '<td nowrap="nowrap">'.$cdate."</td>\n";
				$buffer .= '<td nowrap="nowrap">'.$authortxt.'<br /><a href="mailto:'.$comment->email.'">'.$comment->email."</a></td>\n";
				$buffer .= '<td class="elx_td_center">'.$publishtxt."</td>\n";
				$buffer .= '<td class="elx_td_center">'.$deletetxt."</td>\n";
				$buffer .= '<td>'.nl2br($comment->message)."</td>\n";
				$buffer .= "</tr>\n";
				$k = 1 - $k;
				$i++;
			}

			$buffer .= "</table>\n";
			$buffer .= "</div>\n";
			$form->openTab($eLang->get('COMMENTS').' ('.count($comments).')');
			$form->addHTML($buffer);
			$form->closeTab();

			unset($buffer, $i, $k, $unpubicon, $delicon, $pubaccess, $delaccess, $profaccess, $proflink);
		}

		$form->addHidden('task', '');
		$form->addHidden('id', $row->id);
		$form->render();
		unset($form);

		echo '<div id="lng_titleempty" style="display:none;">'.addslashes(sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('TITLE')))."</div>\n";
		echo '<div id="lng_wait" style="display:none;">'.addslashes($eLang->get('PLEASE_WAIT'))."</div>\n";
		echo '<div id="acontentbase" style="display:none;">'.$elxis->makeAURL('content:/', 'inner.php')."</div>\n";
		echo '<div id="acontentpubicon" style="display:none;">'.$pubicon."</div>\n";
		echo '<div id="acontentloadicon" style="display:none;">'.$elxis->icon('loading', 16, '', 'gif')."</div>\n";
		echo '<div id="acontentwarnicon" style="display:none;">'.$elxis->icon('warning', 16)."</div>\n";
	}

}

?>