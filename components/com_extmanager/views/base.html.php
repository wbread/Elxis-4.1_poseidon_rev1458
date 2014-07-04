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


class extmanagerView {


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

		echo '<h1>'.$title."</h1>\n";
		echo '<div class="elx_error">'.$message."</div>\n";
		echo '<div class="elx_user_bottom_links" style="text-align: center; margin: 15px auto;">'."\n";
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo "</div>\n";
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


	/***********************************/
	/* DISPLAY EXTENSION'S INFORMATION */
	/***********************************/
	protected function extensionInfo($exml, $with_description=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($exml->getErrorMsg() != '') {
			return '<div class="elx_warning">'.$exml->getErrorMsg()."</div>\n";
		}

		$head = $exml->getHead();
		$rowspan = 1;
		if ($head->author != '') { $rowspan++; }
		if ($head->copyright != '') { $rowspan++; }
		if ($head->license != '') { $rowspan++; }
		if (($with_description == true) && ($head->description != '')) { $rowspan++; }

		$out = '<table cellspacing="0" cellpadding="0" border="0" width="100%" dir="'.$eLang->getinfo('DIR').'">'."\n";
		$out .= '<tr><td rowspan="'.$rowspan.'" width="90" style="text-align:center; vertical-align:top;">';
		if ($head->link != '') {
			$out .= '<a href="'.$head->link.'" title="'.$head->title.'" target="_blank">';
			$out .= '<img src="'.$elxis->icon($head->type, 64).'" alt="'.$head->type.'" border="0" /></a>';
		} else {
			$out .= '<img src="'.$elxis->icon($head->type, 64).'" alt="'.$head->type.'" border="0" />';
		}
		$out .= "</td>\n";
		$out .= '<td style="vertical-align:top;">';
		$exttitle = '';
		if ($head->type == 'module') {
			$trtxt = strtoupper($head->name).'_TITLE';
			if ($eLang->exist($trtxt)) { $exttitle = $eLang->get($trtxt); }
		}
		if ($exttitle == '') { $exttitle = $eLang->silentGet($head->title, true); }

		if ($head->link != '') {
			$ttl = sprintf($eLang->get('MORE_INFO_FOR'), $head->title);
			$out .= '<a href="'.$head->link.'" title="'.$ttl.'" target="_blank" style="font-weight:bold; text-decoration:none;">'.$exttitle.'</a>';
		} else {
			$out .= '<strong>'.$exttitle.'</strong>';
		}
		$out .= ' <span dir="ltr">('.$head->name.')</span> &#160; '.$eLang->get('VERSION').' <strong>'.$head->version.'</strong> &#160; <span dir="ltr">('.eFactory::getDate()->formatDate($head->created, $eLang->get('DATE_FORMAT_12')).")</span></td>\n</tr>\n";

		if ($head->author != '') {
			$out .= "<tr><td>\n";
			if ($head->authorurl != '') {
				$txt = '<a href="'.$head->authorurl.'" title="'.$head->author.'" target="_blank" style="font-weight:bold; text-decoration:none;">'.$head->author.'</a>';
			} else {
				$txt = '<strong>'.$head->author.'</strong>';
			}
			if ($head->authoremail != '') {
				$txt .= ' &#160; <span dir="ltr">(<a href="mailto:'.$head->authoremail.'" title="e-mail">'.$head->authoremail.'</a>)</span>';
			}
			$out .= $eLang->get('AUTHOR').': '.$txt."\n";
			$out .= "</td></tr>\n";
		}

		if ($head->copyright != '') {
			$out .= '<tr><td>'. $eLang->get('COPYRIGHT').': '.$head->copyright."</td></tr>\n";
		}

		if ($head->license != '') {
			if ($head->licenseurl != '') {
				$txt = '<a href="'.$head->licenseurl.'" title="'.$head->license.'" target="_blank">'.$head->license.'</a>';
			} else {
				$txt = $head->license;
			}
			$out .= '<tr><td>'. $eLang->get('LICENSE').': '.$txt."</td></tr>\n";
		}

		if (($with_description == true) && ($head->description != '')) {
			$out .= '<tr><td>'.$head->description."</td></tr>\n";
		}
		$out .= "</table>\n";
		return $out;
	}


	/*****************************************/
	/* DISPLAY EXTENSION'S DEPENDENCIES INFO */
	/*****************************************/
	protected function extensionDependencies($exml) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($exml->getErrorMsg() != '') {
			return '<div class="elx_warning">'.$exml->getErrorMsg()."</div>\n";
		}

		$dependencies = $exml->getDependencies();

		$out = '<div class="elx_tbl_wrapper">'."\n";
		$out .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		$out .= "<tr>\n";
		$out .= '<th class="elx_th_subcenter">#'."</th>\n";
		$out .= '<th class="elx_th_sub">'.$eLang->get('TYPE')."</th>\n";
		$out .= '<th class="elx_th_sub">'.$eLang->get('EXTENSION')."</th>\n";
		$out .= '<th class="elx_th_sub">'.$eLang->get('REQUIRED_VERSION')."</th>\n";
		$out .= '<th class="elx_th_sub">'.$eLang->get('INSTALLED_VERSION')."</th>\n";
		$out .= '<th class="elx_th_sub">'.$eLang->get('NOTES')."</th>\n";
		$out .= "</tr>\n";
		if ($dependencies) {
			$k = 0;
			$i = 1;
			foreach ($dependencies as $dpc) {
				if ($dpc->icompatible === true) {
					$class = 'elx_tr'.$k;
					$comptxt = '<span style="color:#008000;">'.$eLang->get('COMPATIBLE').'</span>';
				} elseif ($dpc->iversion > 0) {
					$class = 'elx_trx';
					$comptxt = '<span style="color:#FF0000;">'.$eLang->get('NOT_COMPATIBLE').'</span>';
				} else if ($dpc->installed === false) {
					$class = 'elx_trx';
					$comptxt = '<span style="color:#FF0000;">'.$eLang->get('NOT_INSTALLED').'</span>';
				}

				$out .= '<tr class="'.$class.'">'."\n";
				$out .= '<td class="elx_td_center">'.$i."</td>\n";
				$out .= '<td>'.$eLang->silentGet($dpc->type, true)."</td>\n";
				$out .= '<td>'.ucfirst($dpc->extension)."</td>\n";
				$out .= '<td>';
				if ($dpc->versions) {
					$final = array();
					foreach ($dpc->versions as $v) {
						$plus = strpos($v, '+');
						if ($plus !== false) {
							$v = str_replace('+', '', $v);
							$final[] = $v.' '.$eLang->get('OR_GREATER');
						} else {
							$final[] = $v;
						}
					}
					$out .= implode(', ', $final);
				}
				$out .= "</td>\n";
				$out .= '<td>'.$dpc->iversion."</td>\n";
				$out .= '<td>'.$comptxt."</td>\n";
				$out .= "</tr>\n";
				$i++;
			}
		} else {
			$out .= '<tr class="elx_tr0"><td colspan="6" class="elx_td_center">'.$eLang->get('NO_DEPENDENCIES')."</td></tr>\n";
		}
		$out .= "</table>\n";
		$out .= "</div>\n";

		return $out;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	protected function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}

}

?>