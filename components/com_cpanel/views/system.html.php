<?php 
/**
* @version		$Id: system.html.php 1215 2012-06-28 20:39:48Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class systemCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*****************************/
	/* DISPLAY ELXIS INFORMATION */
	/*****************************/
	public function elxisInformation() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$warn_icon = $elxis->icon('warning', 16);
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		echo '<h2>'.$eLang->get('ELXIS_INFO')."</h2>\n";

		echo '<div style="margin:10px 0 20px 0; font-size:150%; font-weight:bold; color:#444;">'.$eLang->get('VERSION').' 
		<span style="color:#5180BA;" dir="ltr">'.$elxis->getVersion().' '.$elxis->fromVersion('CODENAME')."</span></div>\n";

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="ltr" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="2">'.$eLang->get('ELXIS_INFO').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td width="220">'.$eLang->get('PLATFORM').'</td><td>Elxis</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('VERSION').'</td><td>'.$elxis->fromVersion('RELEASE').'.'.$elxis->fromVersion('LEVEL').'</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('REVISION_NUMBER').'</td><td>'.$elxis->fromVersion('REVISION').'</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('CODENAME').'</td><td>'.$elxis->fromVersion('CODENAME').'</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('STATUS').'</td><td>'.$elxis->fromVersion('STATUS').'</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('RELEASE_DATE').'</td><td>'.$eDate->formatDate($elxis->fromVersion('RELDATE'), $eLang->get('DATE_FORMAT_10')).'</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('AUTHOR').'</td><td>Elxis Team (Chief developer Ioannis Sannos)</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('COPYRIGHT').'</td><td>'.$elxis->fromVersion('COPYRIGHTURL').'</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('POWERED_BY').'</td><td>'.$elxis->fromVersion('POWEREDBY').'</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('HEADQUARTERS').'</td><td>Athens, Hellas</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('LICENSE').'</td><td><a href="http://www.elxis.org/elxis-public-license.html" target="_blank">Elxis Public License</a></td></tr>'."\n";
		echo "</table>\n</div>\n<br /><br />\n";

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="ltr" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="4">'.$eLang->get('CREDITS').'</th></tr>'."\n";
		echo '<tr><th class="elx_th_sub">'.$eLang->get('NAME').'</th><th class="elx_th_sub">'.$eLang->get('CONTRIBUTION').'</th><th class="elx_th_sub">'.$eLang->get('LOCATION').'</th><th class="elx_th_sub">'.$eLang->get('WEBSITE').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td>Ioannis Sannos (datahell)</td><td>Elxis architect and core developer</td><td>Athens, Greece</td><td><a href="http://www.isopensource.com/" target="blank">isopensource.com</a></td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>Stavros Stratakis (webgift)</td><td>Template design and developer</td><td>Herakleion, Greece</td><td><a href="http://www.webgift.gr/" target="blank">webgift.gr</a></td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>Ivan Trebjesanin (jazzman)</td><td>EDC responsible, translator</td><td>Novi Sad, Serbia</td><td><a href="http://www.trebjesanin.com/" target="blank">trebjesanin.com</a></td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>Kostas Stathopoulos (ks-net)</td><td>Initial backend template design, generic support</td><td>Nafpaktos, Greece</td><td><a href="http://www.ks-net.gr/" target="blank">ks-net.gr</a></td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>Nikos Vlachtsis (nikos)</td><td>Exit pages, languages database author</td><td>Athens, Greece</td><td><a href="http://www.osw.gr/" target="blank">osw.gr</a></td></tr>'."\n";
		echo '<tr class="elx_tr0"><td colspan="4">Special thanks to the following people for their support and contributions on Elxis project:<br /><br />
		<strong>Jim KanaDOS</strong> (Evros - Greece), 
		<strong>Farhad Sakhaei</strong> (Tehran - Iran), 
		<strong>Nikos Sirigos</strong> (Corfu - Greece), 
		<strong>Dejan Viduka</strong> (Novi Sad - Serbia), 
		<strong>Spiros Panagiotakopoulos</strong> (Thessaloniki, Greece), 
		<strong>Harris Kontos</strong> (Greece), 
		<strong>Giannis Mitropoulos</strong> (Greece), 
		<strong>Vaggelis Karabinis</strong> (Psachna - Greece), 
		<strong>Peter Bournias</strong> (Athens - Greece), 
		<strong>Coursar</strong> (Russia), 
		<strong>Yiannis Kottaras</strong> (Athens, Greece), 
		<strong>Francesco Venuti</strong> (Lamezia Teme, Italy), 
		<strong>Wbread</strong> (Russia)<br /><br />
		Finally, many thanks to all translators!
		</td></tr>'."\n";
		echo "</table>\n</div>\n<br /><br />\n";

		$linfo = $eLang->getallinfo($elxis->getConfig('LANG'));
		$text = '<strong>'.$elxis->getConfig('LANG').'</strong> '.$linfo['LANGUAGE'].'-'.$linfo['REGION'].' <em>'.$linfo['NAME'].'</em> '.$linfo['NAME_ENG'];
		$current_daytime = $eDate->worldDate('now', $elxis->getConfig('TIMEZONE'), $eLang->get('DATE_FORMAT_10'));

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="ltr" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="2">'.$eLang->get('ELXIS_ENVIROMENT').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td width="220">'.$eLang->get('INSTALL_PATH').'</td><td><em>'.ELXIS_PATH.'/</em></td></tr>'."\n";

		if (!file_exists($repo_path.'/') || !is_dir($repo_path.'/')) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span style="color:#cc0000;">Does not exist!</span></td></tr>'."\n";
		} elseif (!is_writeable($repo_path.'/')) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span style="color:#cc0000;">Not writeable!</span></td></tr>'."\n";
		} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em> <span style="color:#cc0000;">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr1"><td>'.$eLang->get('REPO_PATH').'</td><td><em>'.$repo_path.'/</em>'.'</td></tr>'."\n";
		}
		echo '<tr><th class="elx_th_sub" colspan="2">'.$eLang->get('LOCALE').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td width="220">'.$eLang->get('LANGUAGE').'</td><td>'.$text.'</td></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('TIMEZONE').'</td><td>'.$elxis->getConfig('TIMEZONE').' <span dir="ltr" style="font-style:italic;">('.$current_daytime.')</span></td></tr>'."\n";
		echo '<tr><th class="elx_th_sub" colspan="2">'.$eLang->get('DATABASE').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('DB_TYPE').'</td><td>'.$elxis->getConfig('DB_TYPE').'</td></tr>'."\n";
		echo '<tr class="elx_tr1"><td>'.$eLang->get('HOST').'</td><td>'.$elxis->getConfig('DB_HOST').'</td></tr>'."\n";
		if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
			echo '<tr class="elx_tr0"><td>DSN</td><td>'.$elxis->getConfig('DB_DSN').'</td></tr>'."\n";
		}

		switch ($elxis->getConfig('SESSION_HANDLER')) {
			case 'files': $text = $eLang->get('FILES'); break;
			case 'database': $text = $eLang->get('DATABASE'); break;
			case 'none': default: $text = $eLang->get('NONE'); break;
		}

		echo '<tr><th class="elx_th_sub" colspan="2">'.$eLang->get('SESSION').'</th></tr>'."\n";
		echo '<tr class="elx_tr0"><td>'.$eLang->get('HANDLER').'</td><td>'.$text.'</td></tr>'."\n";
		$text = intval($elxis->getConfig('SESSION_LIFETIME') / 60).' min';
		echo '<tr class="elx_tr1"><td>'.$eLang->get('LIFETIME').'</td><td>'.$text.'</td></tr>'."\n";
		if ($elxis->getConfig('SESSION_HANDLER') == 'files') {
			if (!file_exists($repo_path.'/sessions/') || !is_dir($repo_path.'/sessions/')) {
				$text = sprintf($eLang->get('FOLDER_NOT_EXIST'), 'sessions/');
				echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span style="color:#cc0000;">'.$text.'</span></td></tr>'."\n";
			} elseif (!is_writeable($repo_path.'/sessions/')) {
				echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span style="color:#cc0000;">Not writeable!</span></td></tr>'."\n";
			} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
				echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em> <span style="color:#cc0000;">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
			} else {
				echo '<tr class="elx_tr0"><td>'.$eLang->get('PATH').'</td><td><em>'.$repo_path.'/sessions/</em>'.'</td></tr>'."\n";
			}
		}

		echo '<tr><th class="elx_th_sub" colspan="2">'.$eLang->get('SECURITY').'</th></tr>'."\n";
		switch ($elxis->getConfig('SECURITY_LEVEL')) {
			case 2: $text = $eLang->get('INSANE'); break;
			case 1: $text = $eLang->get('HIGH'); break;
			case 0: default: $text = $eLang->get('NORMAL'); break;
		}
		echo '<tr class="elx_tr0"><td>'.$eLang->get('SECURITY_LEVEL').'</td><td>'.$text.'</td></tr>'."\n";
		if ($elxis->getConfig('DEFENDER') == '') {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('ELXIS_DEFENDER').'</td><td>'.$eLang->get('OFF').'</td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr1"><td>'.$eLang->get('ELXIS_DEFENDER').'</td><td>'.$elxis->getConfig('DEFENDER').'</td></tr>'."\n";
		}

		if (!file_exists($repo_path.'/logs/') || !is_dir($repo_path.'/logs/')) {
			$text = sprintf($eLang->get('FOLDER_NOT_EXIST'), 'logs/');
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span style="color:#cc0000;">'.$text.'</span></td></tr>'."\n";
		} elseif (!is_writeable($repo_path.'/logs/')) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span style="color:#cc0000;">Not writeable!</span></td></tr>'."\n";
		} elseif (strpos($repo_path, ELXIS_PATH) !== false) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em> <span style="color:#cc0000;">'.$eLang->get('IS_PUBLIC').'</span></td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr0"><td>'.$eLang->get('DEFENDER_LOGS').'</td><td><em>'.$repo_path.'/logs/defender_ban.php</em>'.'</td></tr>'."\n";
		}

		switch ($elxis->getConfig('SSL')) {
			case 1: $text = $eLang->get('ADMINISTRATION'); break;
			case 2: $text = $eLang->get('PUBLIC_AREA').' + '.$eLang->get('ADMINISTRATION'); break;
			case 0: default: $text = $eLang->get('OFF'); break;
		}
		echo '<tr class="elx_tr1"><td>'.$eLang->get('SSL_SWITCH').'</td><td>'.$text.'</td></tr>'."\n";
		if (ELXIS_ADIR == 'estia') {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('ADMIN_FOLDER').'</td><td><em>'.ELXIS_ADIR.'</em> <span style="color:#cc0000;">'.$eLang->get('DEF_NAME_RENAME').'</span></td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr0"><td>'.$eLang->get('ADMIN_FOLDER').'</td><td><em>'.ELXIS_ADIR.'</em></td></tr>'."\n";
		}
		echo '<tr><th class="elx_th_sub" colspan="2">'.$eLang->get('ERRORS').'</th></tr>'."\n";
		if ($elxis->getConfig('ERROR_REPORT') > 0) {
			switch ($elxis->getConfig('SECURITY_LEVEL')) {
				case 1: $text = $eLang->get('ERRORS'); break;
				case 2: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS'); break;
				case 3: default: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS').' + '.$eLang->get('NOTICES'); break;
			}
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('REPORT').'</td><td>'.$text.'</td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr0"><td>'.$eLang->get('REPORT').'</td><td>'.$eLang->get('OFF').'</td></tr>'."\n";
		}
		switch ($elxis->getConfig('ERROR_LOG')) {
			case 1: $text = $eLang->get('ERRORS'); break;
			case 2: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS'); break;
			case 3: $text = $eLang->get('ERRORS').' + '.$eLang->get('WARNINGS').' + '.$eLang->get('NOTICES'); break;
			case 0: default: $text = $eLang->get('OFF'); break;
		}
		echo '<tr class="elx_tr1"><td>'.$eLang->get('LOG').'</td><td>'.$text.'</td></tr>'."\n";

		if ($elxis->getConfig('LOG_ROTATE') == 0) {
			echo '<tr class="elx_trx"><td><img src="'.$warn_icon.'" alt="warning" align="top" /> '.$eLang->get('ROTATE').'</td><td>'.$eLang->get('NO').'</td></tr>'."\n";
		} else {
			echo '<tr class="elx_tr0"><td>'.$eLang->get('ROTATE').'</td><td>'.$eLang->get('YES').'</td></tr>'."\n";
		}
		echo "</table>\n</div>\n<br />\n";
	}


	/***************************/
	/* DISPLAY PHP INFORMATION */
	/***************************/
	public function phpInformation($phpinfo) {
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('PHP_INFO')."</h2>\n";
		if (!$phpinfo) {
			echo '<div class="elx_error">Could not get PHP information! Most probably <strong>phpinfo</strong> function is disabled.</div>'."\n";
			return;
		}
		echo '<div style="margin:10px 0 20px 0; font-size:150%; font-weight:bold; color:#444;">'.$eLang->get('PHP_VERSION').' <span style="color:#5180BA;">'.phpversion()."</span></div>\n";
		foreach ($phpinfo as $ctg => $items) {
			$columns = $items['tblcolumns'];
			if (count($items) > 1) {
				echo '<div class="elx_tbl_wrapper">'."\n";
				echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="ltr" class="elx_tbl_list">'."\n";
				echo '<tr><th colspan="3">'.$ctg.'</th></tr>'."\n";
				if ($columns == 3) {
					echo '<tr><th class="elx_th_sub" width="220"></th><th class="elx_th_sub">Local value</th><th class="elx_th_sub">Master value</th></tr>'."\n";
				}
				$k = 0;
				foreach ($items as $key => $item) {
					if ($key == 'tblcolumns') { continue; }
					if (is_array($item)) {
						echo '<tr class="elx_tr'.$k.'"><td width="220">'.$key.'</td><td>'.$item['local'].'</td><td>'.$item['master'].'</td></tr>'."\n";
					} else {
						$text = $this->breaklong($key, $item);
						echo '<tr class="elx_tr'.$k.'"><td width="220">'.$key.'</td><td colspan="2">'.$text.'</td></tr>'."\n";
					}
					$k = 1 - $k;
				}
				echo "</table>\n</div>\n<br />\n";
			}
		}
	}


	/**********************/
	/* BREAK LONG STRINGS */
	/**********************/
	private function breaklong($key, $text, $max=100) {
		$key = strtoupper(trim($key));
		if ($key == 'HTTP_COOKIE') {
			$chunks = chunk_split($text, $max, " \n");
		} else if ($key == 'PATH') {
			$chunks = chunk_split($text, $max, " \n");
		} else if ($key == 'COOKIE') {
			$chunks = chunk_split($text, $max, " \n");
		} else {
			return $text;
		}
		return $chunks;
	}

}

?>