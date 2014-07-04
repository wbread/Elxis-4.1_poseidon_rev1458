<?php 
/**
* @version		$Id: category.html.php 1421 2013-04-30 17:31:26Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class categoryContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************/
	/* DISPLAY CATEGORY */
	/********************/
	public function showCategory($row, $subcategories, $articles, $page, $maxpage, $total, $params, $print=0) {
		$num_articles = count($articles);
		$ctg_featured_num = (int)$params->def('ctg_featured_num', 0);
		$ctg_short_num = (int)$params->def('ctg_short_num', 0);
		$ctg_links_num = (int)$params->def('ctg_links_num', 0);
		$ctg_layout = (int)$params->def('ctg_layout', 0);

		if ($page > 1) {
			$ctg_layout = 0;
			$params->set('ctg_short_cols', 0);
			$params->set('ctg_links_cols', 0);
			switch ((int)$params->get('ctg_nextpages_style', 0)) {
				case 0:
					$ctg_featured_num += $ctg_short_num;
					$ctg_featured_num += $ctg_links_num;
					$ctg_short_num = 0;
					$ctg_links_num = 0;
				break;
				case 2:
					$ctg_links_num += $ctg_featured_num;
					$ctg_links_num += $ctg_short_num;
					$ctg_featured_num = 0;
					$ctg_short_num = 0;
				break;
				case 1: default:
					$ctg_short_num += $ctg_featured_num;
					$ctg_short_num += $ctg_links_num;
					$ctg_featured_num = 0;
					$ctg_links_num = 0;
				break;
			}
		} else {
			$rest = $num_articles - $ctg_featured_num;
			if ($rest <= 0) {
				$rest = 0;
				$ctg_featured_num = $num_articles;
				$ctg_short_num = 0;
				$ctg_links_num = 0;
			}
			
			if ($rest > 0) {
				$rest = $rest - $ctg_short_num;
				if ($rest <= 0) {
					$rest = 0;
					$ctg_short_num = $num_articles - $ctg_featured_num;
					$ctg_links_num = 0;
				}
			}

			$ctg_links_num = $rest;

			if ($ctg_featured_num == 0) {
				if ($ctg_layout == 1) { $ctg_layout = 0; }
			}
			if ($ctg_short_num == 0) { $ctg_layout = 0; }
			if ($ctg_links_num == 0) {
				if ($ctg_layout == 2) { $ctg_layout = 0; }
			}
		}

		$this->wrapperStart('category', $row->catid);
		$this->renderCategorySummary($row, $params, $print);
		if ((int)$params->def('ctg_subcategories', 2) === 1) {
			$this->renderSubcategories($row, $subcategories, $params);
		}

		$ctg_mods_pos = $params->get('ctg_mods_pos', '');
		switch ($ctg_layout) {
			case 1:
				if (eFactory::getLang()->getinfo('DIR') == 'rtl') {
					$style1 = ' style="float:right;"';
					$style2 = ' style="float:left;"';
					$style3 = ' style="margin:0; padding:0 0 0 5px;"';
					$style4 = ' style="margin:0; padding:0 5px 0 0;"';
				} else {
					$style1 = '';
					$style2 = '';
					$style3 = ' style="margin:0; padding:0 5px 0 0;"';
					$style4 = ' style="margin:0; padding:0 0 0 5px;"';
				}
				echo '<div class="elx_cols_wrapper">'."\n";
				echo '<div class="elx_2columns"'.$style1.'>'."\n";
				echo '<div'.$style3.'>'."\n";
				if ($ctg_featured_num > 0) {
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="elx_2columns"'.$style2.'>'."\n";
				echo '<div'.$style4.'>'."\n";
				if ($ctg_short_num > 0) {
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="clear">'."</div>\n";
				echo "</div>\n";
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				if ($ctg_links_num > 0) {
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
				}
			break;
			case 2:
				if (eFactory::getLang()->getinfo('DIR') == 'rtl') {
					$style1 = ' style="float:right;"';
					$style2 = ' style="float:left;"';
					$style3 = ' style="margin:0; padding:0 0 0 5px;"';
					$style4 = ' style="margin:0; padding:0 5px 0 0;"';
				} else {
					$style1 = '';
					$style2 = '';
					$style3 = ' style="margin:0; padding:0 5px 0 0;"';
					$style4 = ' style="margin:0; padding:0 0 0 5px;"';
				}
				if ($ctg_featured_num > 0) {
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
				}
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				echo '<div class="elx_cols_wrapper">'."\n";
				echo '<div class="elx_2columns"'.$style1.'>'."\n";
				echo '<div'.$style3.'>'."\n";
				if ($ctg_short_num > 0) {
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="elx_2columns"'.$style2.'>'."\n";
				echo '<div'.$style4.'>'."\n";
				if ($ctg_links_num > 0) {
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="clear">'."</div>\n";
				echo "</div>\n";
			break;
			case 0: default:
				if ($ctg_featured_num > 0) {
					$this->renderFeaturedArticles($row, $articles, $ctg_featured_num, $params);
				}
				if ($ctg_mods_pos != '') {
					eFactory::getDocument()->modules($ctg_mods_pos);
				}
				if ($ctg_short_num > 0) {
					$this->renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params);
				}
				if ($ctg_links_num > 0) {
					$this->renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params);
				}
			break;
		}

		if (($maxpage > 1) && ($print == 0)) {
			if ((int)$params->get('ctg_pagination') == 1) {
				$linkbase = eFactory::getElxis()->makeURL($row->link);
				echo eFactory::getElxis()->obj('Navigation')->navLinks($linkbase, $page, $maxpage);
			}
		}

		if ((int)$params->get('ctg_subcategories', 2) === 2) {
			$this->renderSubcategories($row, $subcategories, $params);
		}

		$this->wrapperEnd('category');
	}


	/***************************/
	/* RENDER CATEGORY SUMMARY */
	/***************************/
	private function renderCategorySummary($row, $params, $print=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$ctg_show = (int)$params->get('ctg_show', 2);
		$ctg_print = (int)$params->get('ctg_print', 0);
		if ($ctg_show < 1) {
			if (($print == 1) || ($ctg_print == 1)) {
				echo '<div class="elx_content_icons">'."\n";
				if (file_exists(ELXIS_PATH.'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png')) {
					$icon_print = $elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png';
				} else {
					$icon_print = $elxis->secureBase().'/templates/system/images/print.png';
				}
				echo '<div class="elx_content_icon">'."\n";
				if ($print == 1) {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();">';
				} else {
					$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 600, 400);">';
				}
				echo '<img src="'.$icon_print.'" border="0" alt="print" /></a>'."\n";
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
			}
			return;
		}


		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		echo '<div>';
		echo '<h1 style="float:'.$float.';">'.$row->title."</h1>\n";
		if ($ctg_print == 1) {
			echo '<div class="elx_content_icons">'."\n";
			if (file_exists(ELXIS_PATH.'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png')) {
				$icon_print = $elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png';
			} else {
				$icon_print = $elxis->secureBase().'/templates/system/images/print.png';
			}

			echo '<div class="elx_content_icon">'."\n";
			if ($print == 1) {
				echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();">';
			} else {
				$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
				echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 600, 400);">';
			}
			echo '<img src="'.$icon_print.'" border="0" alt="print" /></a>'."\n";
			echo "</div>\n";
			echo "</div>\n";
		}

		echo '<div class="clear"></div>'."\n";
		echo "</div>\n";

		if ($ctg_show <> 2) { return; }
		$html = '';
		$clear = '';
		if ((trim($row->image) != '') && file_exists(ELXIS_PATH.'/'.$row->image)) {
			$img = $elxis->secureBase().'/'.$row->image;
			$html = '<img src="'.$img.'" alt="'.$row->title.'" class="elx_category_image" />'."\n";
			$clear = '<div class="clear"></div>'."\n";
		}
		if (trim($row->description) != '') { $html.= $row->description."\n"; }
		if ($html != '') {
			echo '<div class="elx_category_summary">'."\n".$html.$clear."</div>\n";
		}
	}


	/*************************/
	/* RENDER SUB-CATEGORIES */
	/*************************/
	private function renderSubcategories($row, $subcategories, $params) {
		if (!$subcategories) { return; }
		$elxis = eFactory::getElxis();
		$cols = (int)$params->get('ctg_subcategories_cols', 2);
		echo '<h3 class="elx_subcategories_title">'.eFactory::getLang()->get('SUBCATEGORIES')."</h3>\n";
		if ($cols  > 1) {
			$cols_idx = array();
			for ($i=0; $i<$cols; $i++) { $cols_idx[$i] = 0; }
			$curcol = 0;
			for ($k=0; $k < count($subcategories); $k++) {
				$cols_idx[$curcol]++;
				$curcol++;
				if ($curcol == $cols) { $curcol = 0; }
			}

			$start = 0;
			echo '<div class="elx_cols_wrapper">'."\n";
			for($col=0; $col < $cols; $col++) {
				$end = $start + $cols_idx[$col];
				echo '<div class="elx_'.$cols.'columns">'."\n";
				echo "\t".'<ul class="elx_subcategories">'."\n";
				for ($i=$start; $i < $end; $i++) {
					if (isset($subcategories[$i])) {
						$subcategory = $subcategories[$i];
						echo "\t\t".'<li><a href="'.$elxis->makeURL($row->link.$subcategory->seotitle.'/').'" title="'.$subcategory->title.'">'.$subcategory->title."</a></li>\n";
					}
				}
				$start += $cols_idx[$col];
				echo "\t</ul>\n";
				echo "</div>\n";
			}
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		} else {
			echo '<ul class="elx_subcategories">'."\n";
			foreach ($subcategories as $subcategory) {
				echo "\t".'<li><a href="'.$elxis->makeURL($row->link.$subcategory->seotitle.'/').'" title="'.$subcategory->title.'">'.$subcategory->title."</a></li>\n";
			}
			echo "</ul>\n";
		}
	}


	/****************************/
	/* RENDER FEATURED ARTICLES */
	/****************************/
	private function renderFeaturedArticles($row, $articles, $ctg_featured_num, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$ctg_featured_dateauthor = (int)$params->get('ctg_featured_dateauthor', 6);
		$ctg_featured_img = (int)$params->get('ctg_featured_img', 2);
		$ctg_featured_more = (int)$params->get('ctg_featured_more', 0);
		if ($eLang->getinfo('DIR') == 'rtl') {
			if ($ctg_featured_img == 2) {
				$ctg_featured_img = 3;
			} elseif ($ctg_featured_img == 3) {
				$ctg_featured_img = 2;
			}
		}
		
		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);
		$img_medium_width = (int)$params->get('img_medium_width', 240);
		
		$img_style = '';
		$img_box_style = '';
		switch ($ctg_featured_img) {
			case 1:
				$img_style = ' width="'.$img_medium_width.'" style="width:'.$img_medium_width.'px;"';
				$img_box_style = ($eLang->getinfo('DIR') == 'rtl') ? ' style="text-align:right;"' : ' style="text-align:left;"';
			break;
			case 2:
				$w = $img_medium_width + 10;
				$img_style = ' width="'.$img_medium_width.'" style="width:'.$img_medium_width.'px;"';
				$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:'.$w.'px;"';
			break;
			case 3:
				$w = $img_medium_width + 10;
				$img_style = ' width="'.$img_medium_width.'" style="width:'.$img_medium_width.'px;"';
				$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:'.$w.'px;"';
			break;
			case 4:
				$img_box_style = ($eLang->getinfo('DIR') == 'rtl') ? ' style="text-align:right;"' : ' style="text-align:left;"'; 
			break;
			case 0: default: break;
		}

		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		foreach ($articles as $id => $article) {
			if ($i >= $ctg_featured_num) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$imgbox = '';
			$imgurl = '';
			if ($ctg_featured_img > 0) {
				if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
					if ($ctg_img_empty == 1) {
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						if (trim($article->caption) != '') {
							$imgbox .= '<div>'.$article->caption."</div>\n";
						}
						$imgbox .= "</div>\n";
					}
				} else {
					$imgfile = $elxis->secureBase().'/'.$article->image;
					if (in_array($ctg_featured_img, array(1, 2, 3,))) {
						$file_info = $eFiles->getNameExtension($article->image);
						if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
							$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
							$imgurl = $imgfile;
						}
						unset($file_info);
					}

					$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
					$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
					$imgbox .= '<img src="'.$imgfile.'" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
					$imgbox .= "</a>\n";
					if (trim($article->caption) != '') {
						$imgbox .= '<div>'.$article->caption."</div>\n";
					}
					$imgbox .= "</div>\n";	
				}
			}

			$dateauthor = $this->getDateAuthor($article, $ctg_featured_dateauthor, $allowed_any_profile, 'DATE_FORMAT_5', 'DATE_FORMAT_4');

			if (ELXIS_MOBILE == 0) {
				echo '<div class="elx_featured_box">'."\n";
				echo '<h2><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h2>'."\n";
				if ($dateauthor != '') { echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
				echo '<div class="elx_category_featured_inner">'."\n";
				echo $imgbox;
				if (trim($article->subtitle) != '') { echo '<p class="elx_content_subtitle">'.$article->subtitle."</p>\n"; }
				echo $article->introtext."\n";
				if ($ctg_featured_more == 1) {
					echo ' <a href="'.$link.'" title="'.$article->title.'" class="elx_more">'.$eLang->get('MORE').'</a>'."\n";
				}
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
				echo "</div>\n";
			} else {
				echo '<article class="elx_featured_box">'."\n";
				if ($imgurl != '') {
					echo "<figure>\n";
					echo '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgurl.'" alt="'.$article->title.'" /></a>'."\n";
					if (trim($article->caption) != '') {
						echo '<figcaption>'.$article->caption."</figcaption>\n";
					}
					echo "</figure>\n";
					echo '<div class="elx_featured_limbox">'."\n";
				} else {
					echo '<div class="elx_featured_fullbox">'."\n";
				}
				echo '<div class="elx_category_featured_inner">'."\n";
				echo '<h2><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h2>'."\n";
				if (trim($article->subtitle) != '') { echo '<p class="elx_content_subtitle">'.$article->subtitle."</p>\n"; }
				if (trim($article->introtext) != '') { echo '<div class="elx_content_intro">'.$article->introtext."</div>\n"; }
				if ($dateauthor != '') { echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</article>\n";
			}
			$i++;
		}
	}


	/*************************/
	/* RENDER SHORT ARTICLES */
	/*************************/
	private function renderShortArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $params) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$cols = (int)$params->get('ctg_short_cols', 1);
		$ctg_short_dateauthor = (int)$params->get('ctg_short_dateauthor', 6);
		$ctg_short_img = (int)$params->get('ctg_short_img', 2);
		$ctg_short_text = (int)$params->get('ctg_short_text', 180);
		$ctg_short_more = (int)$params->get('ctg_short_more', 0);
		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);
		$img_thumb_width = (int)$params->get('img_thumb_width', 100);

		$img_style = ' width="'.$img_thumb_width.'" style="width:'.$img_thumb_width.'px;"';
		$img_box_style = '';
		if ($ctg_short_img == 1) {
			$img_box_style = ($eLang->getinfo('DIR') == 'rtl') ? ' style="text-align:right;"' : ' style="text-align:left;"';
		} elseif ($ctg_short_img == 2) {
			$w = $img_thumb_width + 10;
			if ($eLang->getinfo('DIR') == 'rtl') {
				$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:'.$w.'px;"';
			} else {
				$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:'.$w.'px;"';
			}
		}

		if ($cols > 1) {
			$date_format_long = 'DATE_FORMAT_3';
			$date_format_short = 'DATE_FORMAT_2';
		} else {
			$date_format_long = 'DATE_FORMAT_5';
			$date_format_short = 'DATE_FORMAT_4';
		}

		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		$b = 0;
		$buffer = array();
		foreach ($articles as $id => $article) {
			if ($i < $ctg_featured_num) { $i++; continue; }
			if ($i >= ($ctg_featured_num + $ctg_short_num)) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$imgbox = '';
			$imgurl = '';
			if ($ctg_short_img > 0) {
				if ((trim($article->image) == '') || !file_exists(ELXIS_PATH.'/'.$article->image)) {
					if ($ctg_img_empty == 1) {
						$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
						$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
						$imgbox .= '<img src="'.$elxis->secureBase().'/templates/system/images/nopicture_article.jpg" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
						$imgbox .= "</a>\n";
						$imgbox .= "</div>\n";
					}
				} else {
					$imgfile = $elxis->secureBase().'/'.$article->image;
					$file_info = $eFiles->getNameExtension($article->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
						$imgurl = $imgfile;
					}
					unset($file_info);

					$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
					$imgbox .= '<a href="'.$link.'" title="'.$article->title.'">';
					$imgbox .= '<img src="'.$imgfile.'" alt="'.$article->title.'" border="0"'.$img_style.' />'; 
					$imgbox .= "</a>\n";
					$imgbox .= "</div>\n";	
				}
			}

			$dateauthor = $this->getDateAuthor($article, $ctg_short_dateauthor, $allowed_any_profile, $date_format_long, $date_format_short);

			if (ELXIS_MOBILE == 0) {
				$buffer[$b] = ($cols > 1) ? '<div class="elx_short_box" style="padding:0 4px;">'."\n" : '<div class="elx_short_box">'."\n";
				if ($ctg_short_img == 1) {
					$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
					if ($dateauthor != '') { $buffer[$b] .= '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
					$buffer[$b] .= $imgbox;
				} else {
					$buffer[$b] .= $imgbox;
					$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
					if ($dateauthor != '') { $buffer[$b] .= '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
				}
				if ($ctg_short_text > 0) {
					$moretext = '';
					if ($ctg_short_more == 1) {
						$moretext = ' <a href="'.$link.'" title="'.$article->title.'" class="elx_more">'.$eLang->get('MORE').'</a>'."\n";
					}

					if ($ctg_short_text == 1) {
						if (trim($article->subtitle) != '') {
							$buffer[$b] .= '<p class="elx_content_short">'.$article->subtitle."</p>\n";
						}
					} else if ($ctg_short_text == 1000) {
						if (trim($article->subtitle) != '') { $buffer[$b] .= '<p class="elx_content_subtitle">'.$article->subtitle."</p>\n"; }
						$buffer[$b] .= $article->introtext.$moretext."\n";
					} else {
						$txt = strip_tags($article->introtext);
						if (trim($article->subtitle) != '') { $txt = $article->subtitle.' '.$txt; }
						$len = eUTF::strlen($txt);
						if ($len > $ctg_short_text) {
							$limit = $ctg_short_text - 3;
							$txt = eUTF::substr($txt, 0, $limit).'...';
						}
						$buffer[$b] .= '<p class="elx_content_short">'.$txt.$moretext."</p>\n";
					}
				}
				$buffer[$b] .= '<div class="clear"></div>'."\n";
				$buffer[$b] .= "</div>\n";
			} else {
				$buffer[$b] = '<article class="elx_short_box">'."\n";
				if ($imgurl != '') {
					$buffer[$b] .= "<figure>\n";
					$buffer[$b] .= '<a href="'.$link.'" title="'.$article->title.'"><img src="'.$imgurl.'" alt="'.$article->title.'" /></a>'."\n";
					$buffer[$b] .= "</figure>\n";
				}
				$buffer[$b] .= '<div class="elx_short_textbox">'."\n";
				$buffer[$b] .= '<h3><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a></h3>'."\n";
				if (trim($article->subtitle) != '') {
					$txt = $article->subtitle;
				} else {
					$txt = strip_tags($article->introtext);
					$len = eUTF::strlen($txt);
					if ($len > 220) {
						$txt = eUTF::substr($txt, 0, 217).'...';
					}
				}
				$buffer[$b] .= '<p class="elx_content_short">'.$txt."</p>\n";
				if ($dateauthor != '') { $buffer[$b] .= '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
				$buffer[$b] .= "</div>\n";
				$buffer[$b] .= '<div class="clear"></div>'."\n";
				$buffer[$b] .= "</article>\n";
			}
			$i++;
			$b++;
		}

		if ($cols  > 1) {
			$cols_idx = array();
			for ($i=0; $i<$cols; $i++) { $cols_idx[$i] = 0; }
			$curcol = 0;
			for ($k=0; $k < $ctg_short_num; $k++) {
				$cols_idx[$curcol]++;
				$curcol++;
				if ($curcol == $cols) { $curcol = 0; }
			}

			$start = 0;
			echo '<div class="elx_cols_wrapper">'."\n";
			for($col=0; $col < $cols; $col++) {
				$end = $start + $cols_idx[$col];
				echo '<div class="elx_'.$cols.'columns">'."\n";
				for ($i=$start; $i < $end; $i++) {
					if (isset($buffer[$i])) { echo $buffer[$i]; }
				}
				$start += $cols_idx[$col];
				echo "</div>\n";
			}
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		} else {
			foreach ($buffer as $txt) {
				echo $txt;
			}
		}
	}


	/************************/
	/* RENDER LINK ARTICLES */
	/************************/
	private function renderLinkArticles($row, $articles, $ctg_featured_num, $ctg_short_num, $ctg_links_num, $params) {
		$elxis = eFactory::getElxis();

		$cols = (int)$params->get('ctg_links_cols', 1);
		$ctg_links_dateauthor = (int)$params->get('ctg_links_dateauthor', 0);
		if ($cols > 1) {
			$date_format_long = 'DATE_FORMAT_3';
			$date_format_short = 'DATE_FORMAT_2';
		} else {
			$date_format_long = 'DATE_FORMAT_5';
			$date_format_short = 'DATE_FORMAT_4';
		}
		$allowed_any_profile = ((int)$elxis->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
		$i = 0;
		$b = 0;
		$buffer = array();
		foreach ($articles as $id => $article) {
			if ($i < ($ctg_featured_num + $ctg_short_num)) { $i++; continue; }
			if ($i >= ($ctg_featured_num + $ctg_short_num + $ctg_links_num)) { break; }
			$link = $elxis->makeURL($row->link.$article->seotitle.'.html');
			$dateauthor = $this->getDateAuthor($article, $ctg_links_dateauthor, $allowed_any_profile, $date_format_long, $date_format_short);
			$buffer[$b] = '<li><a href="'.$link.'" title="'.$article->title.'">'.$article->title.'</a>';
			if ($dateauthor != '') { $buffer[$b] .= '<div class="elx_dateauthor">'.$dateauthor.'</div>'; }
			$buffer[$b] .= "</li>\n";
			$i++;
			$b++;
		}

		switch (intval($params->get('ctg_links_header', 0))) {
			case 1: echo '<h3 class="elx_links_box_title">'.eFactory::getLang()->get('READ_ALSO')."</h3>\n"; break;
			case 2: echo '<h3 class="elx_links_box_title">'.eFactory::getLang()->get('OTHER_ARTICLES')."</h3>\n"; break;
			case 0: default: break;
		}

		if ($cols  > 1) {
			$cols_idx = array();
			for ($i=0; $i<$cols; $i++) { $cols_idx[$i] = 0; }
			$curcol = 0;
			for ($k=0; $k < $ctg_links_num; $k++) {
				$cols_idx[$curcol]++;
				$curcol++;
				if ($curcol == $cols) { $curcol = 0; }
			}

			$start = 0;
			echo '<div class="elx_cols_wrapper">'."\n";
			for($col=0; $col < $cols; $col++) {
				$end = $start + $cols_idx[$col];
				echo '<div class="elx_'.$cols.'columns">'."\n";
				echo '<ul class="elx_links_box" style="padding:0 4px;">'."\n";
				for ($i=$start; $i < $end; $i++) {
					if (isset($buffer[$i])) { echo $buffer[$i]; }
				}
				echo "</ul>\n";
				$start += $cols_idx[$col];
				echo "</div>\n";
			}
			echo '<div class="clear">'."</div>\n";
			echo "</div>\n";
		} else {
			echo '<ul class="elx_links_box">'."\n";
			foreach ($buffer as $txt) {
				echo $txt;
			}
			echo "</ul>\n";
		}
	}


	/********************************************/
	/* GET/FORMAT DATE AN AUTHOR FOR AN ARTICLE */
	/********************************************/
	private function getDateAuthor($article, $type, $allowed=false, $date_format_long='DATE_FORMAT_5', $date_format_short='DATE_FORMAT_4') {
		$eLang = eFactory::getLang();
		$dateauthor = '';
		switch($type) {
			case 1:	$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get($date_format_long)); break;
			case 2:
				$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get($date_format_long));
				if ($allowed) {
					$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
					$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
				} else {
					$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
				}
			break;
			case 3:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get($date_format_short));
				}
			break;
			case 4:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get($date_format_short));
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				}
			break;
			case 5:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get($date_format_short));
				} else {
					$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get($date_format_long));
				}
			break;
			case 6:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get($date_format_short));
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				} else {
					$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get($date_format_long));
					if ($allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
					}
				}
			break;
			default: break;
		}

		return $dateauthor;
	}
		
}

?>