<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contentView {


	protected function __construct() {
	}


	protected function wrapperStart($type, $id) {
		if ($type == 'category') {
			echo '<div class="elx_category_page" id="elx_category_page_'.$id.'">'."\n";
		} elseif ($type == 'article') {
			if (ELXIS_MOBILE == 1) {
				echo '<article class="elx_article_page" id="elx_article_page_'.$id.'">'."\n";
			} else {
				echo '<div class="elx_article_page" id="elx_article_page_'.$id.'">'."\n";
			}
		} elseif ($type == 'tags') {
			echo '<div class="elx_tags_page">'."\n";
		} elseif ($type == 'feeds') {
			echo '<div class="elx_feeds_page">'."\n";
		}
	}


	protected function wrapperEnd($type) {
		if (($type == 'article') && (ELXIS_MOBILE == 1)) {
			echo "</article>\n";
		} else {
			echo "</div>\n";
		}
	}


	/***************************/
	/* DISPLAY AN ERROR SCREEN */
	/***************************/
	public function base_errorScreen($message, $title='', $close=false, $back=false, $frontpage=false) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		if ($title == '') { $title = $eLang->get('ERROR'); }

		echo '<h3>'.$title."</h3>\n";
		echo '<div class="elx_error">'.$message."</div>\n";
		echo '<div style="margin: 15px auto;">'."\n";
		if ($close == true) {
			echo '<a href="javascript:void(null);" onclick="javascript:window.close();" title="'.$eLang->get('CLOSE_WINDOW').'">'.$eLang->get('CLOSE')."</a><br />\n";
		}
		if ($back == true) {
			echo '<a href="javascript:void(null);" onclick="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a><br />\n";
		}
		if ($frontpage == true) {
			$link = $elxis->makeURL();
			echo '<a href="'.$link.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a><br />\n";
		}
		echo "</div>\n";
	}


	/********************/
	/* CREATE SORT LINK */
	/********************/
	/*
	protected function base_sortLink($col, $corder='ua', $page=0) {
		
		switch ($col) {
			case 'firstname': $norder = ($corder == 'fa') ? 'fd': 'fa'; break;
			case 'lastname': $norder = ($corder == 'la') ? 'ld': 'la'; break;
			case 'groupname': $norder = ($corder == 'ga') ? 'gd': 'ga'; break;
			case 'preflang': $norder = ($corder == 'pa') ? 'pd': 'pa'; break;
			case 'country': $norder = ($corder == 'ca') ? 'cd': 'ca'; break;
			case 'website': $norder = ($corder == 'wa') ? 'wd': 'wa'; break;
			case 'gender': $norder = ($corder == 'gea') ? 'ged': 'gea'; break;
			case 'registerdate': $norder = ($corder == 'ra') ? 'rd': 'ra'; break;
			case 'lastvisitdate': $norder = ($corder == 'lva') ? 'lvd': 'lva'; break;
			case 'uname': default: $norder = ($corder == 'ua') ? 'ud': 'ua'; break;
		}
		return ($page >0) ? 'page='.$page.'&amp;order='.$norder : 'order='.$norder;
	
	}	
	*/
	

}

?>