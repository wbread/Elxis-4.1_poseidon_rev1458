<?php 
/**
* @version		$Id: browse.html.php 1370 2012-12-06 21:22:27Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class browseExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************************/
	/* EXTENSIONS BROWSER CENTRAL PAGE */
	/***********************************/
	public function extCentral($edc) {
		$this->ui($edc);
	}


	/**************************/
	/* DISPLAY USER INTERFACE */
	/**************************/
	private function ui($edc, $catid=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
?>

		<div class="ui_global_wrapper">
			<div class="ui_side_wrapper">
				<div class="ui_pad">
					<?php $this->menuCategories($edc, $catid); ?>
					<?php $this->displayElxisId($edc); ?>
				</div>
			</div>
			<div class="ui_main_wrapper">
				<div class="ui_pad">
					<div id="ui_edc_auth"></div>
					<div id="ui_edc_filters"></div>
					<div id="ui_edc_main"></div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="ui_elxis_copyright">
				<!-- created by Ioannis Sannos (datahell) -->
				Extensions provided by <a href="http://www.elxis.org/" target="_blank" title="Elxis CMS" class="ui_nodeclink">elxis.org</a>. 
				Visit <a href="http://forum.elxis.org/" target="_blank" title="Elxis official forum" class="ui_nodeclink">Elxis forums</a> for support. 
				Copyright &#0169; 2006 - <?php echo date('Y'); ?> elxis.org. All rights reserved.
			</div>
		</div>
		<div id="sitebase" style="display:none; visibility:hidden;" dir="ltr"><?php echo $elxis->secureBase(); ?></div>
		<div id="extmanbase" style="display:none; visibility:hidden;" dir="ltr"><?php echo $elxis->makeAURL('extmanager:/', 'inner.php'); ?></div>
		<div id="elxisid" style="display:none; visibility:hidden;" dir="ltr"><?php echo $edc->getElxisId(); ?></div>
		<div id="edcurl" style="display:none; visibility:hidden;" dir="ltr"><?php echo $edc->getEdcUrl(); ?></div>
		<div id="edccatid" style="display:none; visibility:hidden;" dir="ltr">0</div>
		<div id="edcauth" style="display:none; visibility:hidden;" dir="ltr"></div>
		<div id="edclightbox" style="display:none;" class="ui_lightbox">
			<div class="ui_lightbox_top">
				<div class="ui_lightbox_title"><?php echo $eLang->get('ELXISDC'); ?></div>
				<div class="ui_lightbox_close"><a href="javascript:void(null);" onclick="edcClosebox();"><?php echo $eLang->get('CLOSE'); ?></a></div>
				<div class="clear"></div>
			</div>
			<div id="ui_lightbox_message"></div>
			<div id="ui_edc_response" style="display:none;"></div>
		</div>
		<div id="edcfadebox" style="display:none;" class="ui_fadebox"></div>
		<iframe id="edcframe" src="" style="display:none; visibility:hidden;"></iframe>

<?php 
	}


	/********************************/
	/* MENU OF EXTENSION CATEGORIES */
	/********************************/
	private function menuCategories($edc, $curcatid=0) {
		$eLang = eFactory::getLang();

		$categories = $edc->getCategories();
		$total = count($categories);

		echo '<h3 class="ui_menu_h3">'.$eLang->get('CATEGORIES')."</h3>\n";
		echo '<ul class="ui_menu">'."\n";
		$a_class = ($curcatid == 0) ? $a_class = ' class="ui_bold"' : '';
		echo '<li><a href="javascript:void(null);" id="edcctg0" onclick="edcFrontpage()"'.$a_class.' title="'.$eLang->get('HOME').'">'.$eLang->get('HOME').'</a></li>'."\n";
		$i = 1;
		foreach ($categories as $catid => $category) {
			if ($catid == 1) {
				$extra_class = ($i == $total) ? ' class="ui_menu_core ui_menu_last"' : ' class="ui_menu_core"';
			} elseif ($catid == 19) {
				$extra_class = ($i == $total) ? ' class="ui_menu_template ui_menu_last"' : ' class="ui_menu_template"';
			} elseif ($catid == 20) {
				$extra_class = ($i == $total) ? ' class="ui_menu_atemplate ui_menu_last"' : ' class="ui_menu_atemplate"';
			} elseif ($catid == 3) {
				$extra_class = ($i == $total) ? ' class="ui_menu_language ui_menu_last"' : ' class="ui_menu_language"';
			} else {
				$extra_class = ($i == $total) ? ' class="ui_menu_last"' : '';
			}

			$a_class = ($curcatid == $catid) ? $a_class = ' class="ui_bold"' : '';

			echo '<li'.$extra_class.'><a href="javascript:void(null);" id="edcctg'.$catid.'" onclick="edcLoad('.$catid.', 0, 1)"'.$a_class.' title="'.$category.'">'.$category.'</a></li>'."\n";
			$i++;
		}
		echo "</ul>\n";
	}


	/****************/
	/* ELXIS ID BOX */
	/****************/
	private function displayElxisId($edc) {
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) { return; }

		$elxisid = $edc->getElxisId();
		if ($elxisid == '') {
			echo '<div class="ui_elxisid_not">'."\n";
			echo $eLang->get('SITE_NOREG_ELXIS')." \n";
			echo '<a href="javascript:void(null);" onclick="edcRegister()">'.$eLang->get('REG_NOW_EID').'</a>';
			echo "</div>\n";
		} else {
			echo '<div class="ui_elxisid">'."\n";
			echo $eLang->get('SITE_REG_ELXIS')."<br />\n";
			echo 'Elxis ID: <span dir="ltr" class="ui_bold">'.$elxisid.'</span>';
			echo "</div>\n";
		}
	}


	/******************************/
	/* DISPLAY CONNECTION RESULTS */
	/******************************/
	public function connectionResult($response) {
		if ($response['error'] != '') {
			$json = array('error' => 1, 'errormsg' => addslashes($response['error']), 'edcauth' => $response['edcauth']);
		} else {
			$json = array('error' => 0, 'errormsg' => '', 'edcauth' => $response['edcauth']);
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}


	/******************************/
	/* DISPLAY CATEGORY'S FILTERS */
	/******************************/
	public function showFilters($catid, $response) {
		$this->ajaxHeaders();
		if (is_array($response) && (count($response > 0))) {
			$eLang = eFactory::getLang();
			echo '<div class="ui_fbox">'."\n";
			echo '<h4>'.$eLang->get('CATEGORY_FILTERS')."</h4>\n";
			echo '<a href="javascript:void(null);" onclick="edcLoad('.$catid.', 0, 1)" title="'.$eLang->get('ALL_EXTENSIONS').'">'.$eLang->get('ALL_EXTENSIONS')."</a> \n";
			foreach ($response as $fid => $title) {
				echo '<a href="javascript:void(null);" onclick="edcLoad('.$catid.', '.$fid.', 1)" title="'.$title.'">'.$title."</a> \n";
			}
			echo "</div>\n";
		} else {
			echo '';
		}
		exit();
	}


	/*********************************/
	/* DISPLAY CATEGORY'S EXTENSIONS */
	/*********************************/
	public function showCategory($response, $edc) {
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx_warning">'.$response['error']."</div>\n";
			exit();
		}
		
		if (($response['total'] == 0) || (count($response['rows']) == 0)) {
			$this->ajaxHeaders();
			echo '<p class="elx_sminfo">'.$eLang->get('NO_EXTS_FOUND')."</p>\n";
			exit();
		}

		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();

		$this->ajaxHeaders('text/plain');
		if ($response['total'] == 1) {
			echo '<p class="elx_sminfo">'.$eLang->get('EXTENSION_FOUND')."</p>\n";
		} else {
			$txt = sprintf($eLang->get('EXTENSIONS_FOUND'), '<strong>'.$response['total'].'</strong>');
			if ($response['maxpage'] > 1) {
				$txt .= ' '.sprintf($eLang->get('PAGEOF'), '<strong>'.$response['page'].'</strong>', '<strong>'.$response['maxpage'].'</strong>');
			}
			echo '<p class="elx_sminfo">'.$txt."</p>\n";
		}

		echo '<div class="ui_extensions">'."\n";
		foreach ($response['rows'] as $row) {
			$actions = $edc->extActions($row, $perms);
			$this->extensionBox($row, $is_ssl, $response['ordering'], $actions, false, false);
		}
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		if ($response['maxpage'] > 1) {
			$row = $response['rows'][0];
			echo '<div class="ui_pages_box">'."\n";
			echo '<span class="ui_pages_index">'.$eLang->get('PAGE')."</span> \n";
			for ($i=1; $i <= $response['maxpage']; $i++) {
				if ($i == $response['page']) {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PAGE').' '.$i.'" class="ui_pages_link">'.$i."</a> \n";
				} else {
					echo '<a href="javascript:void(null);" onclick="edcLoad('.$row['catid'].', '.$row['fid'].', '.$i.');" class="ui_pages_active" title="'.$eLang->get('PAGE').' '.$i.'">'.$i."</a> \n";
				}
			}
			echo "</div>\n";
			unset($row);
		}

		exit();
	}


	/*******************************/
	/* DISPLAY EXTENSION'S DETAILS */
	/*******************************/
	public function showExtension($response, $edc) {
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx_warning">'.$response['error']."</div>\n";
			exit();
		}

		$row = $response['row'];
		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();
		$actions = $edc->extActions($row, $perms);

		$this->ajaxHeaders('text/plain');

		echo '<div style="margin:0; padding:0;">'."\n";
		echo '<div class="ui_details_main">'."\n";
		$this->extensionBox($row, $is_ssl, 'c', $actions, true, false);
		$this->extensionMore($row, $is_ssl, $edc);
		echo "</div>\n";
		echo '<div class="ui_details_side">'."\n";
		$this->extensionSideBox($row, $edc);
		echo "</div>\n";
		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";
		exit();
	}


	/***************************/
	/* DISPLAY EDC'S FRONTPAGE */
	/***************************/
	public function showFrontpage($response, $edc) {
		$eLang = eFactory::getLang();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx_warning">'.$response['error']."</div>\n";
			exit();
		}
		
		if (count($response['blocks']) == 0) {
			$this->ajaxHeaders();
			echo '<p class="elx_sminfo">Nothing to display!'."</p>\n";
			exit();
		}

		$is_ssl = eFactory::getURI()->detectSSL();
		$perms = $edc->permissions();

		$this->ajaxHeaders();
		foreach ($response['blocks'] as $block) {
			if (in_array($block['type'], array('latest', 'popular', 'featured'))) {
				switch ($block['type']) {
					case 'latest': $block_title = $eLang->get('LATEST_LISTINGS'); break;
					case 'featured': $block_title = $eLang->get('SUGGESTED'); break;
					case 'popular': default: $block_title = $eLang->get('POPULAR_LISTINGS'); break;
				}

				$ids = explode(',',$block['contents']);
				if (!$ids) { continue; }
				echo '<h3 class="ui_extensions_h3">'.$block_title."</h3>\n";
				echo '<div class="ui_extensions">'."\n";
				foreach ($ids as $id) {
					$x = (int)$id;
					if (isset($response['rows'][$x])) {
						$actions = $edc->extActions($response['rows'][$x], $perms);
						$this->extensionBox($response['rows'][$x], $is_ssl, 'c', $actions, false, true);
					}
				}
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
				continue;
			}

			if ($block['type'] == 'alert') {
				echo '<div class="elx_warning">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'advertisement') {
				echo '<div class="ui_adsbox">'."\n";
				echo '<div class="ui_adsbox_border">'."\n";
				echo stripslashes($block['contents']);
				echo '<span class="ui_adsbox_mark">'.$eLang->get('ADVERTISEMENT')."</span>\n";
				echo '</div>'."\n";
				echo "</div>\n";
			}
			if ($block['type'] == 'notice') {
				echo '<div class="elx_notice">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'announcement') {
				echo '<div class="ui_announcement">'.stripslashes($block['contents'])."</div>\n";
			}
			if ($block['type'] == 'message') {
				echo $block['contents']."\n";
			}
		}
		exit();
	}


	/****************************/
	/* SHOW AUTHOR'S EXTENSIONS */
	/****************************/
	public function showAuthorExtensions($response, $edc) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		if ($response['error'] != '') {
			$this->ajaxHeaders();
			echo '<div class="elx_warning">'.$response['error']."</div>\n";
			exit();
		}
		
		if (count($response['author']) == 0) {
			$this->ajaxHeaders();
			echo '<div class="elx_warning">Invalid author!</div>'."\n";
			exit();
		}

		$total = count($response['rows']);
		$is_ssl = eFactory::getURI()->detectSSL();
		$total_downloads = 0;

		if (($total > 0) && $response['rows'][0]['author']['avatar'] != '') {
			if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $response['rows'][0]['author']['avatar'])) {//prevent breaking SSL with no SSL images
				$img = $elxis->secureBase().'/components/com_user/images/noavatar.png';
			} else {
				$img = $response['rows'][0]['author']['avatar'];
			}
		} else {
			$img = $elxis->secureBase().'/components/com_user/images/noavatar.png';
		}

		if ($response['author']['city'] != '') {
			$location = $response['author']['city'];
			if ($response['author']['country'] != '') {
				$location .= ', '.$response['author']['country'];
			}
		} else if ($response['author']['country'] != '') {
			$location = $response['author']['country'];
		} else {
			$location = '';
		}

		if ($total > 0) {
			foreach ($response['rows'] as $row) { $total_downloads += $row['downloads']; }
		}

		$this->ajaxHeaders('text/plain');

		echo '<div class="ui_abox">'."\n";
		echo '<div class="ui_abox_thumb">'."\n";
		if ($response['author']['website'] != '') {
			echo '<a href="'.$response['author']['website'].'" title="'.$eLang->get('VISIT_AUTHOR_SITE').'" target="_blank"><img src="'.$img.'" alt="icon" width="80" height="80" /></a>'."\n";
		} else {
			echo '<img src="'.$img.'" alt="icon" width="80" height="80" />'."\n";
		}
		echo "</div>\n";
		echo '<div class="ui_abox_main">'."\n";
		echo '<div class="ui_xbox_info"><strong>'.$response['author']['name']."</strong></div>\n";
		echo '<div class="ui_xbox_info">'.$eLang->get('LOCATION').': <strong>'.$location."</strong></div>\n";
		echo '<div class="ui_xbox_info">'.$eLang->get('WEBSITE').': ';
		if ($response['author']['website'] != '') {
			echo '<a href="'.$response['author']['website'].'" title="'.$eLang->get('VISIT_AUTHOR_SITE').'" target="_blank">'.$response['author']['website'].'</a>';
		}
		echo "</div>\n";
		echo '<div class="ui_xbox_info">'.$eLang->get('EXTENSIONS').': <strong>'.$total."</strong></div>\n";
		echo '<div class="ui_xbox_info">'.$eLang->get('DOWNLOADS').': <strong>'.$total_downloads."</strong></div>\n";
		echo "</div>\n";
		echo '<div class="clear"></div>'."\n";
		echo '</div>';

		if ($total > 0) {
			echo '<h3 class="ui_extensions_h3">'.$eLang->get('EXTENSIONS')."</h3>\n";
			echo '<p class="ui_para">'.$eLang->get('ALL_EXTENSIONS_BY').' '.$response['author']['name']."</p>\n";

			foreach ($response['rows'] as $row) {
				echo '<div class="ui_abox_extension">'."\n";
				echo '<div class="ui_xbox_title">'."\n";
				echo '<a href="javascript:void(null);" onclick="edcLoadExtension('.$row['id'].', '.$row['catid'].', '.$row['fid'].');" class="ui_xbox_a_'.$row['type'].'" title="'.$row['title'].'">'.$row['title'].'</a> '."\n";
				echo '<span class="ui_version">'.$row['version']."</span>\n";
				echo "</div>\n";
				if ($row['short'] != '') { echo '<div class="ui_xbox_info">'.$row['short']."</div>\n"; }

				echo '<div class="ui_xbox_info">'."\n";
				if (($row['modified'] != '') && ($row['modified'] != $row['created'])) {
					echo $eLang->get('LAST_MODIFIED').' <strong>'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_3'))."</strong> &#0124; \n";
				} else {
					echo $eLang->get('DATE').' <strong>'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_3'))."</strong> &#0124; \n";
				}

				if ($row['license'] != '') {
					if ($row['licenseurl'] != '') {
						echo $eLang->get('LICENSE').' <a href="'.$row['licenseurl'].'" target="_blank" title="'.$row['license'].'" class="ui_external">'.$row['license']."</a> &#0124; \n";
					} else {
						echo $eLang->get('LICENSE').' <strong>'.$row['license']."</strong> &#0124; \n";
					}
				}
				if ($row['price'] != '') { echo $eLang->get('PRICE').' <strong>'.$row['price']."</strong> &#0124; \n"; }
				if ($row['downloads'] > 0) { echo $eLang->get('DOWNLOADS').' <strong>'.$row['downloads']."</strong> &#0124; \n"; }
				echo $eLang->get('RATING').' ';
				echo '<span dir="ltr">'.$this->ratingStars($row['rating']);
				if ($row['rating_num'] > 0) { echo ' ('.$row['rating_num'].')'; }
				echo '</span>';
				echo "\n</div>\n";
				echo "</div>\n";
			}
		}

		exit();
	}


	/*******************************/
	/* GENERATE AN EXTENSION'S BOX */
	/*******************************/
	private function extensionBox($row, $is_ssl, $ordering, $actions, $showdetails=false, $showcategory=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		if ($row['icon'] != '') {
			if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $row['icon'])) {//prevent breaking SSL with no SSL images
				if (file_exists(ELXIS_PATH.'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/'.$row['type'].'.png')) {
					$img = $elxis->secureBase().'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/'.$row['type'].'.png';
				} else {
					$img = $elxis->secureBase().'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/not_found.png';
				}
			} else {
				$img = $row['icon'];
			}
		} else {
			if (file_exists(ELXIS_PATH.'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/'.$row['type'].'.png')) {
				$img = $elxis->secureBase().'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/'.$row['type'].'.png';
			} else {
				$img = $elxis->secureBase().'/includes/icons/'.$elxis->getConfig('ICONS_PACK').'/64x64/not_found.png';
			}
		}

		if (($showdetails === false) && (eUTF::strlen($row['title']) > 24)) {
			$limited_title = substr($row['title'], 0, 21).'...';
		} else {
			$limited_title = $row['title'];
		}

		$css_sfx = ($showdetails) ? '_large' : '';

		echo '<div class="ui_xbox'.$css_sfx.'">'."\n";
		echo '<div class="ui_xbox_thumb">'."\n";
		if ($showdetails) {
			echo '<a href="javascript:void(null);" title="'.$row['title'].'">';
		} else {
			echo '<a href="javascript:void(null);" onclick="edcLoadExtension('.$row['id'].', '.$row['catid'].', '.$row['fid'].');" title="'.$row['title'].'">';
		}
		echo '<img src="'.$img.'" alt="icon" /></a>'."\n";
		echo "</div>\n";

		echo '<div class="ui_xbox_main'.$css_sfx.'">'."\n";
		echo '<div class="ui_xbox_title">'."\n";
		if ($showdetails) {
			echo '<a href="javascript:void(null);" class="ui_xbox_a_'.$row['type'].'" title="'.$row['title'].'">'.$limited_title.'</a> '."\n";
		} else {
			echo '<a href="javascript:void(null);" onclick="edcLoadExtension('.$row['id'].', '.$row['catid'].', '.$row['fid'].');" class="ui_xbox_a_'.$row['type'].'" title="'.$row['title'].'">'.$limited_title.'</a> '."\n";
		}
		echo '<span class="ui_version">'.$row['version']."</span>\n";
		echo "</div>\n";

		if ($showcategory) {
			echo '<div class="ui_xbox_info"><a href="javascript:void(null);" onclick="edcLoad('.$row['catid'].', 0, 1)" title="'.$row['category'].'">'.$row['category']."</a></div>\n";
		} else {
			echo '<div class="ui_xbox_info"><a href="javascript:void(null);" onclick="edcAuthor('.$row['uid'].')" title="'.$eLang->get('ALL_EXTENSIONS_BY').' '.$row['author'].'">'.$row['author']."</a></div>\n";
		}

		if ($row['short'] != '') {
			if ($showdetails) {
				$limited_short = $row['short'];
			} else if (eUTF::strlen($row['short']) > 38) {
				$limited_short = eUTF::substr($row['short'], 0, 35).'...';
			} else {
				$limited_short = $row['short'];
			}
			echo '<div class="ui_xbox_info">'.$limited_short."</div>\n";
		} else {
			echo '<div class="ui_xbox_info ui_italic">'.$eLang->get('NO_AVAIL_DESC')."</div>\n";
		}

		if ($showdetails === true) {
			if (($row['modified'] != '') && ($row['modified'] != $row['created'])) {
				echo '<div class="ui_xbox_info">'.$eLang->get('LAST_MODIFIED').' <strong>'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_5'))."</strong></div>\n";
			} else {
				echo '<div class="ui_xbox_info">'.$eLang->get('DATE').' <strong>'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_5'))."</strong></div>\n";
			}			
		} else {
			if ($ordering == 'm') {
				echo '<div class="ui_xbox_info" title="'.$eLang->get('LAST_MODIFIED').'">'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_5'))."</div>\n";
			} else {
				echo '<div class="ui_xbox_info">'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_5'))."</div>\n";
			}			
		}

		echo '<div class="ui_xbox_info">'."\n";
		if ($showdetails === true) {
			echo $eLang->get('USERS_RATING').' ';
			echo '<span dir="ltr">'.$this->ratingStars($row['rating']);
			if ($row['rating_num'] > 0) { echo ' ('.$row['rating_num'].')'; }
			echo '</span>';
		} else {
			if (!$actions['buy']) {
				echo '<span class="ui_xbox_downloads" dir="ltr">'.$row['downloads'].' '.$eLang->get('DOWNLOADS')."</span> \n";
			}
			echo '<span dir="ltr">'.$this->ratingStars($row['rating']);
			if ($row['rating_num'] > 0) { echo ' ('.$row['rating_num'].')'; }
			echo '</span>';			
		}
		echo "\n</div>\n";
		echo "</div>\n";
		echo '<div class="clear"></div>'."\n";
		echo '<div class="ui_xbox_buttons">'."\n";

		if ($actions['download']) {
			echo '<a class="ui_action" href="javascript:void(null);" onclick="edcDownload(\''.$row['pcode'].'\');">'.$eLang->get('DOWNLOAD').'</a>'." \n";
		}
		if ($actions['is_installed']) {
			if ($actions['update']) {
				$exttitle = addslashes($row['title']);
				echo '<a class="ui_action" href="javascript:void(null);" onclick="edcPrompt(\'update\', \''.$row['pcode'].'\', \''.$exttitle.'\', \''.$row['version'].'\');">'.$eLang->get('UPDATE')."</a> \n";
			} else if ($actions['is_updated']) {
				echo '<a class="ui_noaction" href="javascript:void(null);">'.$eLang->get('UPDATED')."</a> \n";
			} else {
				echo '<a class="ui_noaction" href="javascript:void(null);">'.$eLang->get('INSTALLED')."</a> \n";
			}
		} else {
			if ($actions['install']) {
				$exttitle = addslashes($row['title']);
				echo '<a class="ui_action" href="javascript:void(null);" onclick="edcPrompt(\'install\', \''.$row['pcode'].'\', \''.$exttitle.'\', \''.$row['version'].'\');">'.$eLang->get('INSTALL')."</a> \n";
			}			
		}

		if ($actions['buy']) {
			echo '<a class="ui_buyaction" href="'.$row['buylink'].'" title="'.$eLang->get('BUY').'" target="_blank">'.$row['price']."</a> \n";
		}
		echo "</div>\n";
		echo "</div>\n";
	}


	/***************************************/
	/* EXTENSION'S DETAILS IN THE SIDE BOX */
	/***************************************/
	private function extensionMore($row, $is_ssl, $edc) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		echo '<h3 class="ui_extensions_h3">'.$eLang->get('DESCRIPTION')."</h3>\n";
		if ($row['description'] != '') {
			echo '<div class="ui_details_largeline">'.$row['description']."</div>\n";
		} else {
			echo '<div class="ui_details_largeline">'.$eLang->get('NO_AVAIL_DESC')."</div>\n";
		}

		$screens = array();
		$use_thumbs = true;
		for ($i=1; $i<4; $i++) {
			$idx = 'image'.$i;
			if ($row[$idx] == '') { continue; }
			if (($is_ssl == true) && !preg_match('#^(https\:\/\/)#i', $row[$idx])) { $use_thumbs = false; }
			$screens[] = $row[$idx];
		}
		
		if ($screens) {
			echo '<h3 class="ui_extensions_h3">'.$eLang->get('SCREENSHOTS')."</h3>\n";
			echo '<div class="ui_details_largeline">'."\n";
			foreach ($screens as $screen) {
				echo '<a href="javascript:void(null);" onclick="$.colorbox({href:\''.$screen.'\'});">';
				if ($use_thumbs) {
					echo '<img src="'.$screen.'" alt="thumbnail" border="0" style="width:120px; padding:4px; border:1px solid #ccc;" />';
				} else {
					echo '<img src="'.$elxis->icon('media', 64).'" alt="thumbnail" border="0" style="width:64px; padding:4px; border:1px solid #ccc;" />';
				}
				echo "</a> &#160; \n";
			}
			echo "</div>\n";
		}

		if ($row['link'] != '') {
			echo '<div class="ui_details_largeline"><a href="'.$row['link'].'" target="_blank" class="ui_external">'.$eLang->get('EXTDET_PUBSITE')."</a></div>\n";
		}

		$this->ratingForm($row['id'], $edc);
		$this->reportForm($row['id'], $edc);
	}


	/***************************/
	/* EXTENSION'S RATING FORM */
	/***************************/
	private function ratingForm($id, $edc) {
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) { return; }
		$elxisid = $edc->getElxisId();
		echo '<h3 class="ui_extensions_h3">'.$eLang->get('RATE_EXTENSION')."</h3>\n";
		if ($elxisid == '') {
			echo '<div class="elx_smwarning">'.$eLang->get('REQUIRED_EXLISID')."</div><br />\n";
			return;
		}

		if (isset($_COOKIE['extensions_rated'])) {
			$extids = explode(',',urldecode($_COOKIE['extensions_rated']));
			foreach ($extids as $extid) {
				$rid = (int)$extid;
				if ($rid == $id) {
					echo '<div class="elx_sminfo">'.$eLang->get('ALREADY_RATED')."</div><br />\n";
					return;
				}
			}
		}

		$base = eFactory::getElxis()->secureBase().'/components/com_extmanager/css/';
?>
		<table cellspacing="0" cellpadding="0" border="0" dir="ltr">
		<tr>
			<td style="width:140px;"><a href="javascript:void(null);" onclick="edcRate(<?php echo $id; ?>, 5)" title="5/5" class="ui_nodeclink"><?php echo $eLang->get('VERY_GOOD'); ?></a></td>
			<td><?php echo str_repeat('<img src="'.$base.'star.png" alt="star" />', 5); ?></td>
		</tr>
		<tr>
			<td style="width:140px;"><a href="javascript:void(null);" onclick="edcRate(<?php echo $id; ?>, 4)" title="4/5" class="ui_nodeclink"><?php echo $eLang->get('GOOD'); ?></a></td>
			<td>
<?php 
				echo str_repeat('<img src="'.$base.'star.png" alt="star" />', 4);
				echo '<img src="'.$base.'star_no.png" alt="star" />';
?>
			</td>
		</tr>
		<tr>
			<td style="width:140px;"><a href="javascript:void(null);" onclick="edcRate(<?php echo $id; ?>, 3)" title="3/5" class="ui_nodeclink"><?php echo $eLang->get('FAIR'); ?></a></td>
			<td>
<?php 
				echo str_repeat('<img src="'.$base.'star.png" alt="star" />', 3);
				echo str_repeat('<img src="'.$base.'star_no.png" alt="star" />', 2);
?>
			</td>
		</tr>
		<tr>
			<td style="width:140px;"><a href="javascript:void(null);" onclick="edcRate(<?php echo $id; ?>, 2)" title="2/5" class="ui_nodeclink"><?php echo $eLang->get('POOR'); ?></a></td>
			<td>
<?php 
				echo str_repeat('<img src="'.$base.'star.png" alt="star" />', 2);
				echo str_repeat('<img src="'.$base.'star_no.png" alt="star" />', 3);
?>
			</td>
		</tr>
		<tr>
			<td style="width:140px;"><a href="javascript:void(null);" onclick="edcRate(<?php echo $id; ?>, 1)" title="1/5" class="ui_nodeclink"><?php echo $eLang->get('VERY_POOR'); ?></a></td>
			<td>
<?php 
				echo '<img src="'.$base.'star.png" alt="star" />';
				echo str_repeat('<img src="'.$base.'star_no.png" alt="star" />', 4);
?>
			</td>
		</tr>
		</table><br />
<?php 
	}


	/***********************/
	/* REPORT AN EXTENSION */
	/***********************/
	private function reportForm($id, $edc) {
		$eLang = eFactory::getLang();

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) { return; }
		$elxisid = $edc->getElxisId();
		echo '<h3 class="ui_extensions_h3">'.$eLang->get('REPORT')."</h3>\n";
		echo '<p class="ui_para">'.$eLang->get('REPORT_ELXISTEAM')."</p>\n";
		if ($elxisid == '') {
			echo '<div class="elx_smwarning">'.$eLang->get('REQUIRED_EXLISID')."</div>\n";
			return;
		}
?>
		<ul class="ui_ul">
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 1);" class="ui_textlink"><?php echo $eLang->get('EXT_CNOT_DOWNLOAD'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 2);" class="ui_textlink"><?php echo $eLang->get('EXT_CNOT_INSTALL'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 3);" class="ui_textlink"><?php echo $eLang->get('SECURITY_ISSUES'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 4);" class="ui_textlink"><?php echo $eLang->get('BAD_EXTENSION'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 5);" class="ui_textlink"><?php echo $eLang->get('COPYRIGHT_VIOLATION'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 6);" class="ui_textlink"><?php echo $eLang->get('LICENSE_VIOLATION'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 7);" class="ui_textlink"><?php echo $eLang->get('PERSONAL_VIOLATION'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 8);" class="ui_textlink"><?php echo $eLang->get('INACC_MISSING_DETAILS'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 9);" class="ui_textlink"><?php echo $eLang->get('AUTHOR_FRAUD'); ?></a></li>
			<li><a href="javascript:void(null);" onclick="edcReport(<?php echo $id; ?>, 10);" class="ui_textlink"><?php echo $eLang->get('OTHER_REASON'); ?></a></li>
		</ul>
<?php 
	}


	/***************************************/
	/* EXTENSION'S DETAILS IN THE SIDE BOX */
	/***************************************/
	private function extensionSideBox($row, $edc) {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$categories = $edc->getCategories();
		$catid = $row['catid'];
		$fid = $row['fid'];

		if ($row['verified'] == 1) { echo '<div class="ui_details_verified">'.$eLang->get('VERIFIED_EXTENSION')."</div>\n"; }

		echo '<div class="ui_details_line">'.$eLang->get('TYPE').'<br /><strong>'.$edc->getTypeName($row['type'])."</strong></div>\n";

		if (($row['modified'] != '') && ($row['modified'] != $row['created'])) {
			echo '<div class="ui_details_line">'.$eLang->get('LAST_MODIFIED').'<br /><strong>'.$eDate->formatTS($row['modified'], $eLang->get('DATE_FORMAT_3'))."</strong></div>\n";
		} else {
			echo '<div class="ui_details_line">'.$eLang->get('DATE').'<br /><strong>'.$eDate->formatTS($row['created'], $eLang->get('DATE_FORMAT_3'))."</strong></div>\n";
		}

		if ($row['authorlink'] != '') {
			echo '<div class="ui_details_line">'.$eLang->get('AUTHOR').' <a href="'.$row['authorlink'].'" target="_blank" class="ui_external" title="'.$eLang->get('VISIT_AUTHOR_SITE').'">'.$row['author']."</a></div>\n";
		} else {
			echo '<div class="ui_details_line">'.$eLang->get('AUTHOR').' <strong>'.$row['author']."</strong></div>\n";
		}

		echo '<div class="ui_details_line">'.$eLang->get('VERSION').' <strong>'.$row['version']."</strong></div>\n";

		if ($row['compatibility'] != '') {
			echo '<div class="ui_details_line">'.$eLang->get('COMPATIBILITY').' <strong>Elxis '.$row['compatibility']."</strong></div>\n";
		}

		if (isset($categories[$catid])) {
			$altcatid = $row['altcatid'];
			$altfid = $row['altfid'];
			echo '<div class="ui_details_line">'.$eLang->get('CATEGORY')."<br />\n";
			echo '<a href="javascript:void(null);" onclick="edcLoad('.$catid.', '.$fid.', 1)" title="'.$categories[$catid].'">'.$categories[$catid].'</a>';
			if (($altcatid > 0) && isset($categories[$altcatid])) {
				echo "<br />\n".$eLang->get('ALSO_LISTED_UNDER')."<br />\n";
				echo '<a href="javascript:void(null);" onclick="edcLoad('.$altcatid.', '.$altfid.', 1)" title="'.$categories[$altcatid].'">'.$categories[$altcatid]."</a>\n";
			}
			echo "</div>\n";
		}
		unset($categories);

		if ($row['downloads'] > 0) { echo '<div class="ui_details_line">'.$eLang->get('DOWNLOADS').' <strong>'.$row['downloads']."</strong></div>\n"; }
		if ($row['size'] > 0) { echo '<div class="ui_details_line">'.$eLang->get('SIZE').' <strong>'.$row['size']." kb</strong></div>\n"; }

		if ($row['license'] != '') {
			if ($row['licenseurl'] != '') {
				echo '<div class="ui_details_line">'.$eLang->get('LICENSE').' <a href="'.$row['licenseurl'].'" target="_blank" title="'.$row['license'].'" class="ui_external">'.$row['license']."</a></div>\n";
			} else {
				echo '<div class="ui_details_line">'.$eLang->get('LICENSE').' <strong>'.$row['license']."</strong></div>\n";
			}
		}

		if ($row['demolink'] != '') {
			echo '<div class="ui_details_line"><a href="'.$row['demolink'].'" target="_blank" title="'.$eLang->get('DEMO').'" class="ui_external">'.$eLang->get('DEMO')."</a></div>\n";
		}
		if ($row['doclink'] != '') {
			echo '<div class="ui_details_line"><a href="'.$row['doclink'].'" target="_blank" title="'.$eLang->get('DOCUMENTATION').'" class="ui_external">'.$eLang->get('DOCUMENTATION')."</a></div>\n";
		}

		if ($row['price'] != '') {
			echo '<div class="ui_details_line">'.$eLang->get('PRICE').' <strong>'.$row['price'].'</strong>';
			if ($row['buylink'] != '') {
			  echo ' <a href="'.$row['buylink'].'" target="_blank" title="'.$eLang->get('BUY').'" class="ui_external">'.$eLang->get('BUY').'</a>';
			}
			echo "</div>\n";
		}
		echo "<br />\n";
	}


	/**************************/
	/* MAKE RATING STARS HTML */
	/**************************/
	private function ratingStars($rating) {
		$full = floor($rating);
		$half = 0;
		if ($rating - $full >= 0.5) { $half = 1; }
		$no = 5 - $full - $half;
		$base = eFactory::getElxis()->secureBase().'/components/com_extmanager/css/';

		$out = '';
		if ($full > 0) {
			$out .= str_repeat('<img src="'.$base.'star.png" alt="star" title="'.$rating.'" />', $full);
		}
		if ($half == 1) {
			$out .= '<img src="'.$base.'star_half.png" alt="half" title="'.$rating.'" />';
		}
		if ($no > 0) {
			$out .= str_repeat('<img src="'.$base.'star_no.png" alt="no" title="'.$rating.'" />', $no);
		}
		return $out;
	}


	/***********************************/
	/* DISPLAY EXTENSION RATING RESULT */
	/***********************************/
	public function ratingResult($response) {
		if ($response['error'] != '') {
			$json = array('error' => 1, 'msg' => addslashes($response['error']));
		} else {
			$msg = eFactory::getLang()->get('RATING_SUCCESS');
			$json = array('error' => 0, 'msg' => addslashes($msg));
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}


	/***********************************/
	/* DISPLAY EXTENSION REPORT RESULT */
	/***********************************/
	public function reportResult($response) {
		if ($response['error'] != '') {
			$json = array('error' => 1, 'msg' => addslashes($response['error']));
		} else {
			$msg = eFactory::getLang()->get('EXT_REPORTED');
			$json = array('error' => 0, 'msg' => addslashes($msg));
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}


	/***********************************/
	/* DISPLAY EXTENSION REPORT RESULT */
	/***********************************/
	public function registrationResult($response) {
		if ($response['error'] != '') {
			$json = array('error' => 1, 'msg' => addslashes($response['error']));
		} else if ($response['elxisid'] == '') {
			$json = array('error' => 1, 'msg' => 'Could not get a valid Elxis ID!');
		} else {
			$json = array('error' => 0, 'msg' => $response['elxisid']);
		}

		$this->ajaxHeaders('application/json');
		echo json_encode($json);
		exit();
	}

}

?>