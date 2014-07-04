<?php 
/**
* @version		$Id: navigation.helper.php 1162 2012-06-04 08:13:51Z datahell $
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisNavigationHelper {


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/*************************/
	/* MAKE NAVIGATION LINKS */
	/*************************/
	public function navLinks($linkbase, $page, $maxpage, $indicator=true) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$maxpage = (int)$maxpage;
		if ($maxpage < 1) { $maxpage = 1; }
		if ($maxpage < 2) { return ''; }

		$show_start = false;
		$show_end = false;
		if ($maxpage < 13) {
			$first = 1;
			$last = $maxpage;
		} else {
			$first = $page - 5;
			$last = $page + 5;
			if ($first < 1) {
				while ($first < 1) {
					$first++;
					$last++;
				}
			}
			if ($last > $maxpage) {
				while ($last > $maxpage) {
					$first--;
					$last--;
				}
			}

			$show_start = ($first > 1) ? true : false;
			$show_end = ($maxpage > $last) ? true : false;
		}

		$symb = (strpos($linkbase, '?') === false) ? '?' : '&amp;';
		$eLang = eFactory::getLang();
		$navtag = (in_array(eFactory::getElxis()->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';

		$navout = '<'.$navtag.' class="elx_navigation">'."\n";
		if ($indicator === true) { $navout .= '<span class="elx_nav_page">'.$eLang->get('PAGE').'</span> '; }
		if ($show_start) {
			$css = ($page == 1) ? 'elx_nav_link_active' : 'elx_nav_link';
			$navout .= '<a href="'.$linkbase.'" title="'.$eLang->get('PAGE').' 1" class="'.$css.'">1</a> ';
			if ($first > 2) { $navout .= '<span class="elx_nav_space">...</span> '; }
		}
		for ($i=$first; $i < ($last + 1); $i++) {
			$link = ($i == 1) ? $linkbase : $linkbase.$symb.'page='.$i;
			$css = ($page == $i) ? 'elx_nav_link_active' : 'elx_nav_link';
			$navout .= '<a href="'.$link.'" title="'.$eLang->get('PAGE').' '.$i.'" class="'.$css.'">'.$i.'</a> ';
		}
		if ($show_end) {
			if ($last < ($maxpage - 1)) { $navout .= '<span class="elx_nav_space">...</span> '; }
			$css = ($page == $maxpage) ? 'elx_nav_link_active' : 'elx_nav_link';
			$navout .= '<a href="'.$linkbase.$symb.'page='.$maxpage.'" title="'.$eLang->get('PAGE').' '.$maxpage.'" class="'.$css.'">'.$maxpage.'</a>';
		}
		$navout .= '</'.$navtag.">\n";

		return $navout;
	}

}

?>