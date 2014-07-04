<?php 
/**
* @version		$Id: base.html.php 1163 2012-06-04 10:22:14Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class userView {


	protected function __construct() {
	}


	/***************************/
	/* DISPLAY AN ERROR SCREEN */
	/***************************/
	public function base_errorScreen($message, $title='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($title == '') { $title = $eLang->get('ERROR'); }
		$link = $elxis->makeURL('user:/');
		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';

		echo '<h1>'.$title."</h1>\n";
		echo '<div class="elx_error">'.$message."</div>\n";
		echo '<'.$navtag.' class="elx_user_bottom_links" style="text-align:center; margin:15px auto;">'."\n";
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/*********************************/
	/* GET THE TRANSLATED GROUP NAME */
	/*********************************/
	protected function base_translateGroup($groupname, $gid) {
		switch((int)$gid) {
			case 1: $out = eFactory::getLang()->get('ADMINISTRATOR'); break;
			case 5: $out = eFactory::getLang()->get('USER'); break;
			case 6: $out = eFactory::getLang()->get('EXTERNALUSER'); break;
			case 7: $out = eFactory::getLang()->get('GUEST'); break;
			default: $out = $groupname; break;
		}
		return $out;
	}


	/********************/
	/* CREATE SORT LINK */
	/********************/
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
			case 'profile_views': $norder = ($corder == 'pva') ? 'pvd': 'pva'; break;
			case 'uname': default: $norder = ($corder == 'ua') ? 'ud': 'ua'; break;
		}
		return ($page > 0) ? 'page='.$page.'&amp;order='.$norder : 'order='.$norder;
	}

}

?>