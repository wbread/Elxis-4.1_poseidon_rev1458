<?php 
/**
* @version		$Id: utilities.php 1377 2012-12-15 19:39:18Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class utilitiesCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/*****************************/
	/* CHECK FTP SETTINGS (AJAX) */
	/*****************************/
	public function checkftp() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->ajaxHeaders('text/plain');

		$host = trim(filter_input(INPUT_POST, 'fho', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$port = intval(filter_input(INPUT_POST, 'fpo', FILTER_SANITIZE_NUMBER_INT));
		$user = trim(filter_input(INPUT_POST, 'fus', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$pass = trim(filter_input(INPUT_POST, 'fpa', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$root = trim(filter_input(INPUT_POST, 'fro', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($host == '') {
			printf($eLang->get('FIELDNOEMPTY'), $eLang->get('HOST'));
			exit();
		}
		if ($port < 1) {
			echo $eLang->get('PORT').': '.$eLang->get('INVALID_NUMBER');
			exit();
		}
		if ($user == '') {
			printf($eLang->get('FIELDNOEMPTY'), $eLang->get('USER'));
			exit();
		}
		if ($pass == '') {
			$pass = $elxis->getConfig('FTP_PASS');
			if ($pass == '') {
				printf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
				exit();
			}
		}
		if ($root == '') {
			printf($eLang->get('FIELDNOEMPTY'), $eLang->get('PATH'));
			exit();
		}

		elxisLoader::loadFile('includes/libraries/elxis/ftp.class.php');
		$params = array('ftp_host' => $host, 'ftp_port' => $port, 'ftp_user' => $user, 'ftp_pass' => $pass);
		$ftp = new elxisFTP($params);
		if ($ftp->getStatus() != 'connected') {
			$msg = $ftp->getError();
			if ($msg == '') { $msg = 'Could not connect to FTP server!'; }
			echo $msg;
			exit();
		}

		$rfiles = $ftp->nlist($root);
		$ftp->disconnect();
		if ($rfiles && is_array($rfiles) && (count($rfiles) > 0)) {
			$ok = 0;
			foreach ($rfiles as $rfile) {
				if (strpos($rfile, 'inner.php') !== false) { $ok++; }
				if (strpos($rfile, 'configuration.php') !== false) { $ok++; }
			}
			if ($ok == 2) {
				echo '|1|'.$eLang->get('FTP_CON_SUCCESS').' '.$eLang->get('ELXIS_FOUND_FTP');
				exit;
			}
		}

		echo $eLang->get('FTP_CON_SUCCESS').' '.$eLang->get('ELXIS_NOT_FOUND_FTP');
		exit();
	}


	/******************************************/
	/* SHOW SYSTEM TIME - NULL REQUEST (AJAX) */
	/******************************************/
	public function heartbeat() {
		$this->ajaxHeaders('text/plain');
		echo eFactory::getDate()->getTS();
		exit;
	}


	/*******************************/
	/* GENERIC AJAX REQUEST (AJAX) */
	/*******************************/
	public function genericajax() {
		$f = '';
		if (isset($_POST['f'])) {
			$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
			$f = trim(filter_input(INPUT_POST, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$f = preg_replace('@^(\/)@', '', $f);

			$f2 = trim(strip_tags(preg_replace($pat, '', $f)));
			$f2 = str_replace('..', '', $f2);
			$f2 = str_replace('\/\/', '', $f2);
		
			if ($f2 != $f) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}

			if (strpos($f, 'modules/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_content/plugins/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_user/auth/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_search/engines/') === 0) {
				$ok = true;
			} else {
				$ok = false;
			}

			if (!$ok) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!preg_match('@(\.php)$@', $f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!is_file(ELXIS_PATH.'/'.$f) || !file_exists(ELXIS_PATH.'/'.$f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
		}

		$this->ajaxHeaders('text/plain');
		if ($f == '') {
			echo 'BAD';
		} else {
			include(ELXIS_PATH.'/'.$f);
		}

		exit;
	}


	/*************************/
	/* BAN IP ADDRESS (AJAX) */
	/*************************/
	public function banip() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$ip = base64_decode(trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

		$this->ajaxHeaders('text/plain');
		if ($elxis->user()->gid <> 1) {
			echo '0|'.$eLang->get('ONLY_ADMINS_ACTION');
			exit();
		}

		if ($elxis->getConfig('DEFENDER') == '') {
			echo '0|'.$eLang->get('BAN_IP_REQ_DEF');
			exit();
		}

		$myip = eFactory::getSession()->getIP();
		if ($myip == $ip) {
			echo '0|'.$eLang->get('BAN_YOURSELF');
			exit();
		}

		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
			$ipv6 = $ip;
			$ip = $elxis->obj('IP')->ipv6tov4($ip);
		} else if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
			//do nothing
		} else {
			echo '0|Invalid IP address!';
			exit();
		}

		$ipstr = str_replace('.', 'x', $ip);
		$ipstr = str_replace(':', 'y', $ipstr);

		$repo_path = $eFiles->elxisPath('', true);
		$file = $repo_path.'logs/defender_ban.php';

		$buffer = '<?php '._LEND._LEND;
		$buffer .= '//Elxis Defender - Banned IPs - Last updated on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$elxis->user()->uname.''._LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
		$buffer .= '$ban = array('._LEND;
		if (!file_exists($file)) {
			$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0001\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
		} else {
			include($file);
			$found = false;
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				foreach ($ban as $key => $row) {
					if ($key == $ipstr) {
						if ($row['times'] >= 3) {
							echo '0|'.$eLang->get('IP_AL_BANNED');
							exit();
						}
						$found = true;
						$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0002\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
					} else {
						$buffer .= '\''.$key.'\' => array(\'times\' => '.$row['times'].', \'refcode\' => \''.$row['refcode'].'\', \'date\' => \''.$row['date'].'\'),'._LEND;
					}
				}
			}
			unset($ban);

			if (!$found) {
				$buffer .= '\''.$ipstr.'\' => array(\'times\' => 10, \'refcode\' => \'SEC-CPBAN-0003\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
			}
		}

		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		$ok = $eFiles->createFile('logs/defender_ban.php', $buffer, true, true);
		if ($ok) {
			$this->model->removeSessionIP($ip);
			if (isset($ipv6)) {
				$this->model->removeSessionIP($ipv6);
			}
			echo '1|'.sprintf($eLang->get('IP_BANNED'), $ip);
			exit();
		} else {
			echo '0|'.$eLang->get('BAN_FAILED_NOWRITE');
		}
		exit();
	}


	/***********************/
	/* FORCE LOGOUT (AJAX) */
	/***********************/
	public function forcelogout() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : -1;
		$gid = isset($_POST['gid']) ? (int)$_POST['gid'] : -1;
		$lmethod = trim(filter_input(INPUT_POST, 'lmethod', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$ip = base64_decode(trim(filter_input(INPUT_POST, 'ip', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

		$this->ajaxHeaders('text/plain');

		if (($uid < 0) || ($gid < 1) || ($lmethod == '') || ($ip == '')) {
			echo '0|Invalid request!';
			exit();
		}

		if ($uid > 0) {
			if ($lmethod != 'elxis') {
				echo '0|Invalid request!';
				exit();
			}
			if (($gid == 1) && ($elxis->user()->gid <> 1)) {
				echo '0|'.$eLang->get('CNOT_LOGOUT_ADMIN');
				exit();
			}
			$this->model->removeSessionUser($uid);
			echo '1|'.$eLang->get('USER_LOGGED_OUT');
			exit();
		} else if ($gid == 6) {
			if ($lmethod == 'elxis') {
				echo '0|Invalid request!';
				exit();
			}
			$fact = isset($_POST['fact']) ? (int)$_POST['fact'] : 0;
			if ($fact < 1) {
				echo '0|Invalid request!';
				exit();
			}
			$ok = $this->model->removeSessionXUser($lmethod, $ip, $fact);
			if ($ok) {
				echo '1|'.$eLang->get('USER_LOGGED_OUT');
			} else {
				echo '0|Action failed!';
			}
			exit();
		} else {
			echo '0|Invalid request!';
			exit();
		}
	}


	/***************/
	/* LOGOUT USER */
	/***************/
	public function logout() {
		$elxis = eFactory::getElxis();

		$elxis->logout();
		$return = $elxis->makeURL();
		$elxis->redirect($return);
	}


	/***************************/
	/* PREPARE TO LIST BACKUPS */
	/***************************/
	public function listbackup() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$sfx = ($eLang->getinfo('DIR') == 'rtl') ? '-rtl' : '';
		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp.css');
		$eDoc->setTitle($eLang->get('BACKUP').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->listbackup();
	}


	/********************************************************/
	/* RETURN LIST OF BACKUPS FOR GRID IN XML FORMAT (AJAX) */
	/********************************************************/
	public function getbackups() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$this->ajaxHeaders('text/xml');
			echo '<rows><page>0</page><total>0</total></rows>';
			exit();
		}

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }

		$columns = array('bktype', 'bkdate', 'bkname', 'bksize');

		$rp = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($rp < 1) { $rp = 10; }
		$elxis->updateCookie('rp', $rp);
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$sortname = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'bkdate';
		if (($sortname == '') || !in_array($sortname, $columns)) { $sortname = 'bkdate'; }
		$sortorder = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'desc';
		if (($sortorder == '') || !in_array($sortorder, array('asc', 'desc'))) { $sortorder = 'desc'; }
		$qtype = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : '';
		if ($qtype != 'bkname') { $qtype = ''; } //search only by filename
		$query = trim(filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$files = eFactory::getFiles()->listFiles('backup/', '(\.zip)$', false, true, true);

		$rows = array();
		$total = 0;
		if ($files) {
			foreach ($files as $file) {
				$filename = basename($file);
				if ($query != '') {
					if (stripos($filename, $query) === false) { continue; }
				}
				$type = (preg_match('/^(db)/i', $filename)) ? 'db' : 'fs';
				if (($type == 'fs') && ($is_subsite == true)) { continue; }
				$total++;
				$row = array(
					'bktype' => $type,
					'bkdate' => filemtime($file),
					'bkname' => $filename,
					'bksize' => filesize($file)
				);
				$rows[] = $row;
			}

			if (count($rows) > 1) {
				$rows = $this->sortRows($rows, $sortname, $sortorder, false);
				$limitstart = 0;
				$maxpage = ceil($total/$rp);
				if ($maxpage < 1) { $maxpage = 1; }
				if ($page > $maxpage) { $page = $maxpage; }
				$limitstart = (($page - 1) * $rp);
				if ($total > $rp) {
					$page_rows = array();
					$end = $limitstart + $rp;
					foreach ($rows as $key => $row) {
						if ($key < $limitstart) { continue; }
						if ($key >= $end) { break; }
						$page_rows[] = $row;
					}
					$rows = $page_rows;
				}
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$page."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			$dlbase = $elxis->makeAURL('cpanel:backup/download', 'inner.php');
			foreach ($rows as $row) {
				$type = ($row['bktype'] == 'db') ? $eLang->get('DATABASE') : $eLang->get('FILESYSTEM');
				if ($row['bksize'] < 400000) {
					$size = number_format(($row['bksize'] / 1024), 2, '.', '').' KB';
				} else {
					$size = number_format(($row['bksize'] / (1024 * 1024)), 2, '.', '').' MB';
				}

				$link = $dlbase.'?f='.base64_encode($row['bkname']);
				echo '<row id="'.base64_encode($row['bkname']).'">
					<cell><![CDATA['.$type.']]></cell>
					<cell><![CDATA['.$eDate->formatTS($row['bkdate'], $eLang->get('DATE_FORMAT_5')).']]></cell>
					<cell><![CDATA[<a href="'.$link.'" target="_blank" title="'.$eLang->get('DOWNLOAD').'" class="cpdownload">'.$row['bkname'].'</a>]]></cell>
					<cell><![CDATA['.$size.']]></cell>
				</row>'."\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/**************************/
	/* DOWNLOAD A BACKUP FILE */
	/**************************/
	public function downbackup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			echo $eLang->get('NOTALLOWACCPAGE');
			exit();
		}

		$f = (isset($_GET['f'])) ? strip_tags(base64_decode($_GET['f'])) : '';
		$f = str_replace('/', '', $f);
		$f = str_replace('..', '', $f);
		if (($f == '') || !preg_match('/(\.zip)$/i', $f)) {
			echo 'Empty or invalid backup file!';
			exit();
		}

		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$filepath = $repo_path.'/backup/'.$f;
		if (!file_exists($filepath)) {
			echo $eLang->get('FILE_NOT_FOUND');
			exit();
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header('Content-Length: '.filesize($filepath));
		header('Content-Disposition: attachment; filename='.$f);
		$handle = @fopen($filepath, 'rb');
		if ($handle !== false) {
			while (!feof($handle)) {
				echo fread($handle, 1048576);
				ob_flush();
				flush();
			}
			fclose($handle);
		}
		exit();
	}


	/********************************/
	/* DELETE BACKUP FILE(S) (AJAX) */
	/********************************/
	public function deletebackup() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			echo '0|'.eFactory::getLang()->get('NOTALLOWACCPAGE');
			exit;
		}

		if (!isset($_POST['items'])) { echo '0|No items set!'; exit(); }
		if (trim($_POST['items']) == '') { echo '0|No items set!'; exit(); }
		$parts = explode(',',$_POST['items']);
		$done = 0;
		foreach ($parts as $part) {
			$f = trim(strip_tags(base64_decode($part)));
			$f = str_replace('/', '', $f);
			$f = str_replace('..', '', $f);
			if (($f != '') && preg_match('/(\.zip)$/i', $f)) {
				$ok = $eFiles->deleteFile('backup/'.$f, true);
				if ($ok === true) { $done++; }
			}
		}

		echo $done;
		exit();
	}


	/****************************/
	/* TAKE A NEW BACKUP (AJAX) */
	/****************************/
	public function makebackup() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.eFactory::getLang()->get('NOTALLOWACCPAGE');
			exit;
		}

		$type = isset($_POST['type']) ? $_POST['type'] : 'fs';
		if ($type != 'db') { $type = 'fs'; }

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1) && ($type == 'fs')) {
			$this->ajaxHeaders('text/plain');
			echo '0|You are not allowed to take filesystem backups from a sub-site!';
			exit();
		}

		if ($type == 'fs') {
			$result = $this->fsBackup();
		} else {
			$result = $this->dbBackup();
		}

		$this->ajaxHeaders('text/plain');
		echo ($result['success'] === true) ? '1|'.$result['message'] : '0|'.$result['message'];
		exit();
	}


	/************************************/
	/* GENERATE A NEW FILESYSTEM BACKUP */
	/************************************/
	private function fsBackup() {
		$elxis = eFactory::getElxis();
		$source = array(
			ELXIS_PATH.'/components/',
			ELXIS_PATH.'/includes/',
			ELXIS_PATH.'/language/',
			ELXIS_PATH.'/media/',
			ELXIS_PATH.'/modules/',
			ELXIS_PATH.'/templates/',
			ELXIS_PATH.'/'.ELXIS_ADIR.'/',
			ELXIS_PATH.'/index.php',
			ELXIS_PATH.'/inner.php',
			ELXIS_PATH.'/configuration.php'
		);

		if (defined('ELXIS_MULTISITE')) {
			for ($i=1; $i<21; $i++) {
				if (file_exists(ELXIS_PATH.'/config'.$i.'.php')) {
					$source[] = ELXIS_PATH.'/config'.$i.'.php';
				}
			}
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$parsed = parse_url($elxis->getConfig('URL'));
		$fname = $parsed['host'];
		if (isset($parsed['path']) && ($parsed['path'] != '') && ($parsed['path'] != '/')) {
			$fname .= $parsed['path'];
		}

		$fname = str_replace('/', '', $fname);
		$fname = str_replace('-', '', $fname);
		$fname = strtolower(str_replace('.', '', $fname));
		$fname = 'fs_'.$fname.'_'.date('YmdHis').'.zip';

		$archive = $repo_path.'/backup/'.$fname;	
		$result = array('success' => false, 'message' => 'Backup failed');

		$zip = $elxis->obj('zip');
		$result['success'] = $zip->zip($archive, $source, array());
		if ($result['success'] === true) {
			$size = filesize($repo_path.'/backup/'.$fname);
			$size = round($size / 1048576, 2).' MB';
			$result['message'] = 'Elxis filesystem backup success! File generated '.$fname.', Size: '.$size;
		} else {
			$result['message'] = $zip->getError();
		}
		return $result;
	}


	/**********************************/
	/* GENERATE A NEW DATABASE BACKUP */
	/**********************************/
	private function dbBackup() {
		$elxis = eFactory::getElxis();

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$fname1 = ($elxis->getConfig('DB_NAME') != '') ? $elxis->getConfig('DB_NAME') : 'elxis';
		$fname1 = str_replace('/', '', $fname1);
		$fname1 = str_replace('-', '', $fname1);
		$fname1 = strtolower(str_replace('.', '', $fname1));
		$fname = 'db_'.$fname1.'_'.date('YmdHis').'.zip';

		$archive = $repo_path.'/backup/'.$fname;
		$result = array('success' => false, 'message' => 'Backup failed!');
	 	$sql = eFactory::getDB()->backup();

	 	if ($sql === 0) {
	 		return $result;
		} else if ($sql === -1) {
		 	$result['message'] = 'Not supported database type!';
	 		return $result;
	 	} else if ($sql === -2) {
		 	$result['message'] = 'Invalid or insufficient backup parameters!';
	 		return $result;
 		} else if ($sql === -3) {
		 	$result['message'] = $elxis->getConfig('DB_TYPE').' database adapter faced an unrecoverable error!';
 			return $result;
 		} else {
			$result['success'] = true;
		}

		$sqlname = $fname1.'.sql';
		$data = array($sqlname => $sql);
		$zip = $elxis->obj('zip');
		$result['success'] = $zip->zip($archive, null, $data);
		if ($result['success'] === true) {
			$size = filesize($repo_path.'/backup/'.$fname);
			$size = round($size / 1048576, 2).' MB';
			$result['message'] = 'Elxis database backup success! File generated '.$fname.', Size: '.$size;
		} else {
			$result['message'] = $zip->getError();
		}
		return $result;
	}


	/**********************************/
	/* PREPARE TO LIST SYSTEM ROUTING */
	/**********************************/
	public function listroutes() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		eFactory::getPathway()->addNode($eLang->get('ROUTING'));
		$eDoc->setTitle($eLang->get('ELXIS_ROUTER').' - '.$eLang->get('ADMINISTRATION'));
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');

		$this->view->listroutes();
	}


	/*******************************************************/
	/* RETURN LIST OF ROUTES FOR GRID IN XML FORMAT (AJAX) */
	/*******************************************************/
	public function getrouting() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			$this->ajaxHeaders('text/xml');
			echo "<rows><page>1</page><total>0</total></rows>\n";
			exit();
		}

		$rtype = (isset($_POST['rtype'])) ? trim($_POST['rtype']) : '';
		if ($rtype != '') {
			if (!in_array($rtype, array('frontpage', 'component', 'dir', 'page'))) { $rtype = ''; }
		}

		$temp_rows = array();
		if (($rtype == '') || ($rtype == 'frontpage')) {
			$row = new stdClass;
			$row->type = 'frontpage';
			$row->typetext = $eLang->get('HOME');
			$row->base = '/';
			$row->route = $elxis->getConfig('DEFAULT_ROUTE');
			$temp_rows[] = $row;
		}

		if (($rtype == '') || ($rtype == 'component')) {
			$components = $this->model->getComponents();
			if ($components) {
				foreach ($components as $cmp) {
					$row = new stdClass;
					$row->type = 'component';
					$row->typetext = 'Component';
					$row->base = $cmp['component'];
					$cname = str_replace('com_', '', $cmp['component']);
					$row->route = (trim($cmp['route']) == '') ? '<span style="color:#666;">'.$cname.'</span>' : $cmp['route'];
					$temp_rows[] = $row;
				}
			}
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		if (file_exists($repo_path.'/other/routes.php')) {
			include($repo_path.'/other/routes.php');
			if (($rtype == '') || ($rtype == 'dir')) {
				if (isset($routes) && is_array($routes) && (count($routes) > 0)) {
					foreach ($routes as $k => $v) {
						$row = new stdClass;
						$row->type = 'dir';
						$row->typetext = $eLang->get('DIRECTORY');
						$row->base = $k;
						$row->route = trim($v);
						$temp_rows[] = $row;
					}
				}
			}
			if (($rtype == '') || ($rtype == 'page')) {
				if (isset($page_routes) && is_array($page_routes) && (count($page_routes) > 0)) {
					foreach ($page_routes as $k => $v) {
						$row = new stdClass;
						$row->type = 'page';
						$row->typetext = $eLang->get('PAGE');
						$row->base = $k;
						$row->route = trim($v);
						$temp_rows[] = $row;
					}
				}
			}
		}

		$rp = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($rp < 1) { $rp = 10; }
		$elxis->updateCookie('rp', $rp);
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$sortname = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'rbase';
		if (($sortname == '') || !in_array($sortname, array('rtype', 'rbase', 'rroute'))) { $sortname = 'rbase'; }
		$sortorder = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($sortorder != 'desc') { $sortorder = 'asc'; }
		$qtype = (isset($_POST['qtype'])) ? trim($_POST['qtype']) : '';
		if ($qtype != 'rroute') { $qtype = ''; }
		$query = trim(filter_input(INPUT_POST, 'query', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$rows = array();
		$total = 0;
		if ($temp_rows) {
			foreach($temp_rows as $temp_row) {
				if ($query != '') {
					if (stripos($temp_row->route, $query) === false) { continue; }
				}
				$total++;
				$rows[] = $temp_row;
			}

			if ($total > 1) {
				$rows = $this->sortRows($rows, $sortname, $sortorder);
				$limitstart = 0;
				$maxpage = ceil($total/$rp);
				if ($maxpage < 1) { $maxpage = 1; }
				if ($page > $maxpage) { $page = $maxpage; }
				$limitstart = (($page - 1) * $rp);
				if ($total > $rp) {
					$page_rows = array();
					$end = $limitstart + $rp;
					foreach ($rows as $key => $row) {
						if ($key < $limitstart) { continue; }
						if ($key >= $end) { break; }
						$page_rows[] = $row;
					}
					$rows = $page_rows;
				}
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$page."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			$infoicon = $elxis->icon('info', 16);
			$editicon = $elxis->icon('edit', 16);
			$delicon = $elxis->icon('delete', 16);
			$delofficon = $elxis->icon('delete_off', 16);
			foreach ($rows as $row) {
				$rowid = base64_encode($row->base);
				echo '<row id="'.$rowid.'">'."\n";
				echo '<cell><![CDATA['.$row->typetext."]]></cell>\n";
				echo '<cell><![CDATA['.$row->base."]]></cell>\n";
				echo '<cell><![CDATA['.$row->route."]]></cell>\n";
				echo '<cell><![CDATA[';
				if ($row->type == 'frontpage') {
					echo '<img src="'.$infoicon.'" class="elx_tooltip" title="'.$eLang->get('HOME').'|'.$eLang->get('SET_FRONT_CONF').'" alt="info" />';
				} else {
					echo '<a href="javascript:void(null);" onclick="editroute(\''.$row->type.'\', \''.$rowid.'\');" title="'.$eLang->get('EDIT').'"><img src="'.$editicon.'" alt="edit" border="0" /></a>';
				}
				$can_delete = false;
				if ($row->type == 'dir') {
					$can_delete = true;
				} elseif ($row->type == 'page') {
					$can_delete = ($row->base == 'tags.html') ? false : true;
				}
				if ($can_delete) {
					echo ' &#160; <a href="javascript:void(null);" onclick="deleteroute(\''.$row->type.'\', \''.$rowid.'\');" title="'.$eLang->get('DELETE').'"><img src="'.$delicon.'" alt="delete" border="0" /></a>';
				} else {
					echo ' &#160; <img src="'.$delofficon.'" title="'.$eLang->get('DELETE').'" alt="delete" border="0" />';
				}
				echo "]]></cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/****************************************/
	/* SORT ROWS (ROUTES, BACKUPS AND LOGS) */
	/****************************************/
	private function sortRows($rows, $sortname, $sortorder, $is_object=true) {
		$is_numeric = false;
		switch ($sortname) {
			case 'rbase': $colname = 'base'; break;
			case 'rroute': $colname = 'route'; break;
			case 'rtype': $colname = 'type'; break;
			default:
				$colname = $sortname;
				if (in_array($sortname, array('bkdate', 'bksize', 'yearmonth', 'lastmodified', 'size', 'dt'))) {
					$is_numeric = true;
				}
			break;
		}

		if ($is_object) {
			$f1 = '$a->'.$colname;
			$f2 = '$b->'.$colname;
		} else {
			$f1 = '$a["'.$colname.'"]';
			$f2 = '$b["'.$colname.'"]';
		}

		if ($is_numeric) {
			$code = 'if ('.$f1.' != '.$f2.') {';
			if ($sortorder == 'asc') {
				$code .= 'return ('.$f1.' < '.$f2.' ? -1 : 1); }';
			} else {
				$code .= 'return ('.$f1.' < '.$f2.' ? 1 : -1); }';
			}
			$code .= 'return 0;';
		} else {
			$code = 'if ('.$f1.' != '.$f2.') {';
			if ($sortorder == 'asc') {
				$code .= 'return strcasecmp('.$f1.', '.$f2.'); }';
			} else {
				$code .= 'return (strcasecmp('.$f1.', '.$f2.') * -1); }';
			}
			$code .= 'return 0;';
		}

        $compare = create_function('$a,$b', $code);
        usort($rows, $compare);
        return $rows;
    }


	/******************/
	/* ADD/EDIT ROUTE */
	/******************/
	public function editroute() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			echo '<div class="elx_warning">'.$eLang->get('NOTALLOWACCPAGE').'</div>';
			return;
		}

		$rtype = filter_input(INPUT_GET, 'rtype', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$rbase = base64_decode(filter_input(INPUT_GET, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		if ($rtype == 'frontpage') {
			echo '<div class="elx_warning">'.$eLang->get('SET_FRONT_CONF').'</div>';
			return;
		}

		eFactory::getDocument()->addJQuery();

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmroute',
			'action' => $elxis->makeAURL('cpanel:routing/save', 'inner.php'),
			'idprefix' => 'rt',
			'label_width' => 140,
			'label_align' => 'left',
			'tip_style' => 1,
			'attributes' => 'id="jfmroute"'
		);

		$form = new elxisForm($formOptions);

		if ($rtype == 'new') {
			$notetxt = $eLang->get('ADD_NEW_ROUTE');
		} else {
			$notetxt = sprintf($eLang->get('REROUTE'), '<strong>'.$rbase.'</strong>');
		}

		$form->openFieldset($eLang->get('ELXIS_ROUTER'));
		$form->addNote($notetxt, 'elx_sminfo');
		if ($rtype == 'new') {
			$options = array();
			$options[] = $form->makeOption('page', $eLang->get('PAGE'));
			$options[] = $form->makeOption('dir', $eLang->get('DIRECTORY'));
			$form->addSelect('rtype', $eLang->get('TYPE'), 'page', $options, array('dir' => 'rtl'));
			$form->addText('rbase', '', $eLang->get('SOURCE'), array('dir' => 'rtl', 'size' => 30, 'required' => 1));
			$comps = $this->model->getComponents(false);
			$options = array();
			foreach ($comps as $comp) {
				$v = str_replace('com_', '', $comp);
				$options[] = $form->makeOption($v, $v);
			}	
			$form->addSelect('rroute', $eLang->get('ROUTE_TO'), $comps[0], $options, array('dir' => 'ltr'));
			$form->addHidden('isnew', 1, array('dir' => 'ltr'));
		} else {
			$route = $this->model->getRoute($rtype, $rbase);
			if ($rtype == 'component') {
				$form->addText('rroute', $route, $eLang->get('ROUTE_TO'), array('dir' => 'rtl', 'size' => 30, 'required' => 0));
			} else {
				$comps = $this->model->getComponents(false);
				$options = array();
				foreach ($comps as $comp) {
					$v = str_replace('com_', '', $comp);
					$options[] = $form->makeOption($v, $v);
				}	
				$form->addSelect('rroute', $eLang->get('ROUTE_TO'), $route, $options, array('dir' => 'ltr'));
			}
			$form->addHidden('rbase', base64_encode($rbase), array('dir' => 'ltr'));
			$form->addHidden('rtype', $rtype, array('dir' => 'ltr'));
		}

		$form->addButton('rsave');
		$form->closeFieldset();
		$form->render();
?>
<script type="text/javascript">
/* <![CDATA[ */
$('#jfmroute').submit(function(event) {
   	event.preventDefault();
	var $form = $( this ),
	rtrtype = $('#rtrtype').val(),
	rtrbase = $('#rtrbase').val(),
	rtrroute = $('#rtrroute').val(),
	url = $form.attr('action');
	$.post(url, 
<?php 
	if ($rtype == 'new') {
		echo '{rtype: rtrtype, rbase:rtrbase, rroute:rtrroute, isnew:1},'."\n";
	} else {
		echo '{rtype: rtrtype, rbase:rtrbase, rroute:rtrroute},'."\n";
	}
?>
		function(data) {
			var update = new Array();
			update = data.split('|');
			if (update[0] == '0') {
				alert(update[1]);
			} else {
				parent.$("#routes").flexReload();
				parent.$.fn.colorbox.close();
			}
		}
	);
});
/* ]]> */
</script>
<?php 
	}


	/*********************/
	/* SAVE ROUTE (AJAX) */
	/*********************/
	public function saveroute() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$isnew = (isset($_POST['isnew'])) ? (int)$_POST['isnew'] : 0;
		$rtype = filter_input(INPUT_POST, 'rtype', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$rroute = trim(filter_input(INPUT_POST, 'rroute', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			echo '0|'.$eLang->get('NOTALLOWACCPAGE');
			exit();
		}

		if ($rtype == 'frontpage') {
			echo '0|'.$eLang->get('SET_FRONT_CONF');
			exit();
		}

		if ($isnew == 1) {
			$action = 'add';
			$rbase = trim(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$rbase = trim($rbase, '/');
			if ($rbase == '') {
				echo '0|Source can not be empty!';
				exit();
			}
			if (($rtype == '') || (($rtype != 'page') && ($rtype != 'dir'))) {
				echo '0|Type is invalid!';
				exit();
			}
		} else {
			$action = 'edit';
			$rbase = base64_decode(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		}

		if (($rtype == 'dir') || ($rtype == 'page')) {
			$ok = $this->updateRoutesFile($rtype, $rbase, $rroute, $action);
			if ($ok) {
				echo '1|Success';
			} else {
				echo '0|Could not update other/routes.php file in Elxis Repository!';
			}
			exit();
		} else if ($rtype == 'component') {
			$ok = $this->model->setComponentRoute($rbase, $rroute);
			if ($ok) {
				echo '1|Success';
			} else {
				echo '0|Could not update database! Make sure component exists and routes are unique.';
			}
			exit();
		}

		echo '0|Invalid request';
		exit();
	}


	/***********************/
	/* DELETE ROUTE (AJAX) */
	/***********************/
	public function deleteroute() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$rtype = filter_input(INPUT_POST, 'rtype', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$rbase = base64_decode(filter_input(INPUT_POST, 'rbase', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$rbase = trim($rbase, '/');

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') < 1) {
			echo '0|'.$eLang->get('NOTALLOWACCPAGE');
			exit();
		}

		if ($rbase == '') {
			echo '0|Source can not be empty!';
			exit();
		}
		if (($rtype == '') || (($rtype != 'page') && ($rtype != 'dir'))) {
			echo '0|Type is invalid!';
			exit();
		}

		$ok = $this->updateRoutesFile($rtype, $rbase, '', 'delete');
		if ($ok) {
			echo '1|Success';
		} else {
			echo '0|Could not update other/routes.php file in Elxis Repository!';
		}
		exit();
	}


	/**********************/
	/* UPDATE ROUTES FILE */
	/**********************/
	private function updateRoutesFile($type, $base, $route, $action) {
		$elxis = eFactory::getElxis();
		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$buffer = '<?php '._LEND;
		$buffer .= '/*'._LEND;
		$buffer .= 'Elxis Routes - Copyright (c) 2006-'.date('Y').' elxis.org'._LEND;
		$buffer .= 'Last update on '.date('Y-m-d H:i:s')._LEND;
		$buffer .= '*/'._LEND;
		$buffer .= _LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND;

		if (file_exists($repo_path.'/other/routes.php')) { include($repo_path.'/other/routes.php'); }

		if (!isset($routes) || !is_array($routes)) { $routes = array(); }
		if ($type == 'dir') {
			if ($action == 'delete') {
				if (isset($routes[$base])) { unset($routes[$base]); }
			} else {
				$routes[$base] = $route;
			}
		}

		$n = count($routes);
		$buffer .= '$routes = array('._LEND;
		if ($n > 0) {
			$i = 1;
			foreach ($routes as $k => $v) {
				$buffer .= ($i < $n) ? "\t'".$k."' => '".$v."',"._LEND : "\t'".$k."' => '".$v."'"._LEND;
				$i++;
			}
		}
		$buffer .= ');'._LEND._LEND;

		if (!isset($page_routes) || !is_array($page_routes)) { $page_routes = array(); }
		if ($type == 'page') {
			if ($action == 'delete') {
				if (isset($page_routes[$base])) { unset($page_routes[$base]); }
			} else {
				$page_routes[$base] = $route;
			}
		}

		$n = count($page_routes);
		$buffer .= '$page_routes = array('._LEND;
		if ($n > 0) {
			$i = 1;
			foreach ($page_routes as $k => $v) {
				$buffer .= ($i < $n) ? "\t'".$k."' => '".$v."',"._LEND : "\t'".$k."' => '".$v."'"._LEND;
				$i++;
			}
		}
		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		$ok = eFactory::getFiles()->createFile('other/routes.php', $buffer, true, true);
		return $ok;
	}


	/*******************************/
	/* PREPARE TO LIST SYSTEM LOGS */
	/*******************************/
	public function listlogs() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		eFactory::getPathway()->addNode($eLang->get('LOGS'));
		eFactory::getDocument()->setTitle($eLang->get('LOGS').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->listlogs();
	}


	/*****************************************************/
	/* RETURN LIST OF LOGS FOR GRID IN XML FORMAT (AJAX) */
	/*****************************************************/
	public function getlogs() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			$this->ajaxHeaders('text/xml');
			echo "<rows><page>1</page><total>0</total></rows>\n";
			exit();
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$temp_rows = array();
		if (is_dir($repo_path.'/logs/')) {
			$logfiles = $eFiles->listFiles('logs/', '', false, false, true);
			if ($logfiles) {
				foreach ($logfiles as $logfile) {
					$finfo = $eFiles->getNameExtension($logfile);
					if (($finfo['extension'] == '') || ($finfo['extension'] == 'html')) { continue; }

					$row = new stdClass;
					$row->filename = $logfile;
					$row->type = 'unknown';
					$row->typetext = 'Unknown';
					$row->yearmonth = '';
					$row->logdate = '';
					$row->lastmodified = filemtime($repo_path.'/logs/'.$logfile);
					$row->size = filesize($repo_path.'/logs/'.$logfile);

					if ($finfo['extension'] == 'log') {
						$parts = preg_split('#\_#', $finfo['name']);
						if (in_array($parts[0], array('error', 'notice', 'warning'))) {
							$row->type = $parts[0];
							$uptype = strtoupper($parts[0]);
							$row->typetext = $eLang->get($uptype);
						} else { //custom log file
							$row->type = 'other';
							$row->typetext = ucfirst($parts[0]);
						}

						if (isset($parts[1])) {
							if (strlen($parts[1]) == 6) {
								$year = substr($parts[1], 0, 4);
								$month = substr($parts[1], 4, 2);
							} else {
								$year = date('Y');
								$month = date('m');
							}
						} else {
							$year = date('Y');
							$month = date('m');
						}

						$row->yearmonth =  $year.$month;
						$month = (int)$month;
						$row->logdate =  $eDate->monthName($month).' '.$year;
					} else if ($logfile == 'defender_ban.php') {
						$row->typetext = $eLang->get('DEFENDER_BANS');
						$row->type = 'other';
						$year = date('Y');
						$month = date('m');
						$row->yearmonth =  $year.$month;
						$month = (int)$month;
						$row->logdate =  $eDate->monthName($month).' '.$year;
					} else if ($logfile == 'lastnotify.txt') {
						$row->typetext = $eLang->get('LAST_ERROR_NOTIF');
						$row->type = 'other';
						$year = date('Y');
						$month = date('m');
						$row->yearmonth =  $year.$month;
						$month = (int)$month;
						$row->logdate =  $eDate->monthName($month).' '.$year;
					} else if ($logfile == 'defender_notify.txt') {
						$row->typetext = $eLang->get('LAST_DEFEND_NOTIF');
						$row->type = 'other';
						$year = date('Y');
						$month = date('m');
						$row->yearmonth =  $year.$month;
						$month = (int)$month;
						$row->logdate =  $eDate->monthName($month).' '.$year;
					} else {
						continue;
					}
					$temp_rows[] = $row;
				}
			}
			unset($logfiles);
		}

		$rp = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($rp < 1) { $rp = 10; }
		$elxis->updateCookie('rp', $rp);
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$sortname = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'lastmodified';
		if (($sortname == '') || !in_array($sortname, array('filename', 'type', 'yearmonth', 'lastmodified', 'size'))) { $sortname = 'lastmodified'; }
		$sortorder = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'desc';
		if ($sortorder != 'asc') { $sortorder = 'desc'; }
		$qtype = '';
		$query = '';
		$type = trim(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (!in_array($type, array('notice', 'warning', 'error', 'other'))) { $type = ''; }

		$rows = array();
		$total = 0;
		if ($temp_rows) {
			foreach($temp_rows as $temp_row) {
				if ($type != '') {
					if ($type != $temp_row->type) { continue; }
				}
				$total++;
				$rows[] = $temp_row;
			}

			if ($total > 1) {
				$rows = $this->sortRows($rows, $sortname, $sortorder);
				$limitstart = 0;
				$maxpage = ceil($total/$rp);
				if ($maxpage < 1) { $maxpage = 1; }
				if ($page > $maxpage) { $page = $maxpage; }
				$limitstart = (($page - 1) * $rp);
				if ($total > $rp) {
					$page_rows = array();
					$end = $limitstart + $rp;
					foreach ($rows as $key => $row) {
						if ($key < $limitstart) { continue; }
						if ($key >= $end) { break; }
						$page_rows[] = $row;
					}
					$rows = $page_rows;
				}
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$page."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$rowid = base64_encode($row->filename);
				$moddate = $eDate->formatTS($row->lastmodified, $eLang->get('DATE_FORMAT_4'));
				if ($row->size > 700000) {
					$fsize = $row->size / 1048576;
					$fsize = number_format($fsize, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' MB';
				} else {
					$fsize = $row->size / 1024;
					$fsize = number_format($fsize, 2, $eLang->get('DECIMALS_SEP'), $eLang->get('THOUSANDS_SEP')).' KB';
				}

				echo '<row id="'.$rowid.'">'."\n";
				echo '<cell><![CDATA['.$row->typetext."]]></cell>\n";
				echo '<cell><![CDATA['.$row->filename."]]></cell>\n";
				echo '<cell><![CDATA['.$row->logdate."]]></cell>\n";
				echo '<cell><![CDATA['.$moddate."]]></cell>\n";
				echo '<cell><![CDATA['.$fsize."]]></cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/****************************/
	/* PREPARE TO VIEW LOG FILE */
	/****************************/
	public function viewlog() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACCPAGE')."</div>\n";
			return;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$fname = trim(filter_input(INPUT_GET, 'fname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fname = base64_decode($fname);
		if (($fname == '') || !file_exists($repo_path.'/logs/'.$fname)) {
			echo '<div class="elx_error">'.$eLang->get('FILE_NOT_FOUND')."</div>\n";
			return;
		}

		$extension = $eFiles->getExtension($fname);
		$ts = filemtime($repo_path.'/logs/'.$fname);
		$moddate = eFactory::getDate()->formatTS($ts, $eLang->get('DATE_FORMAT_5'));

		echo '<div class="elx_info">'."\n";
		echo $eLang->get('FILENAME').': <strong>'.$fname."</strong><br />\n";
		echo $eLang->get('LAST_MODIFIED').': <strong>'.$moddate."</strong>\n";
		echo "</div>\n";

		if ($extension == 'log') {
			echo '<pre dir="ltr">'."\n";
			echo file_get_contents($repo_path.'/logs/'.$fname);
			echo "</pre>\n";
		} else if (($fname == 'defender_notify.txt') || ($fname == 'lastnotify.txt')) {
			echo '<p><em>The contents of this file is of no importance</em></p>'."\n";
		} else if ($fname == 'defender_ban.php') {
			include($repo_path.'/logs/'.$fname);
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				$this->view->listBanned($ban);
			} else {
				$this->view->listBanned(array());
			}
		} else {
			echo '<div class="elx_error">The preview of this file is not allowed.'."</div>\n";
		}
	}


	/********************/
	/* CLEAR A LOG FILE */
	/********************/
	public function clearlog($delete=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWACCPAGE'));
			exit();
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$fname = trim(filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fname = base64_decode($fname);
		if (($fname == '') || !file_exists($repo_path.'/logs/'.$fname)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('FILE_NOT_FOUND'));
			exit();
		}

		$extension = $eFiles->getExtension($fname);
		if ($delete == true) {
			$this->ajaxHeaders('text/plain');
			if ($extension != 'log') {
				echo '0|'.addslashes($eLang->get('FILE_CNOT_DELETE'));
				exit();
			}

			$ok = $eFiles->deleteFile('logs/'.$fname, true);
			if (!$ok) {
				echo '0|'.$eFiles->getError();
				exit();
			} else {
				echo '1|Success';
				exit();
			}
		}

		if ($fname == 'defender_ban.php') {
			$proceed = true;
			$data = '<?php '."\n";
			$data .= '//Elxis Defender - Banned IPs - Created on '.gmdate('Y-m-d H:i:s')." (UTC)\n\n";
			$data .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'."\n\n";
			$data .= '$ban = array();'."\n\n";
			$data .= '?>';
			$ok = $eFiles->createFile('logs/'.$fname, $data, true, true);
		} else if ($extension == 'log') {
			$ok = $eFiles->createFile('logs/'.$fname, null, true, true);
		} else {
			$ok = true;
		}

		$this->ajaxHeaders('text/plain');
		if (!$ok) {
			echo '0|'.$eFiles->getError();
		} else {
			echo '1|Success';
		}
		exit();
	}

 
	/*********************/
	/* DELETE A LOG FILE */
	/*********************/
	public function deletelog() {
		$this->clearlog(true);
	}


	/*********************/
	/* DOWNLOAD LOG FILE */
	/*********************/
	public function downloadlog() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACCPAGE')."</div>\n";
			return;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }
		$fname = trim(filter_input(INPUT_GET, 'fname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$fname = base64_decode($fname);
		if (($fname == '') || !file_exists($repo_path.'/logs/'.$fname)) {
			echo '<div class="elx_error">'.$eLang->get('FILE_NOT_FOUND')."</div>\n";
			return;
		}

		$extension = $eFiles->getExtension($fname);
		if ($extension != 'log') {
			echo '<div class="elx_error">'.$eLang->get('ONLY_LOG_DOWNLOAD')."</div>\n";
			return;
		}

		$filepath = $repo_path.'/logs/'.$fname;
		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Content-Description: File Transfer');
		header('Content-Type: application/force-download');
		header('Content-Length: '.filesize($filepath));
		header('Content-Disposition: attachment; filename='.$fname);
		$handle = @fopen($filepath, 'rb');
		if ($handle !== false) {
			while (!feof($handle)) {
				echo fread($handle, 1048576);
				ob_flush();
				flush();
			}
			fclose($handle);
		}
		exit();
	}


	/********************************/
	/* PREPARE TO LIST CACHED ITEMS */
	/********************************/
	public function listcache() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		eFactory::getPathway()->addNode($eLang->get('CACHE'));
		$eDoc->setTitle($eLang->get('CACHE').' - '.$eLang->get('ADMINISTRATION'));
		$this->view->listcache();
	}


	/*************************************************************/
	/* RETURN LIST OF CACHED ITEMS FOR GRID IN XML FORMAT (AJAX) */
	/*************************************************************/
	public function getcache() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') < 1) {
			$this->ajaxHeaders('text/xml');
			echo '<rows><page>0</page><total>0</total></rows>';
			exit();
		}

		$columns = array('item', 'dt', 'size');
		$rp = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($rp < 1) { $rp = 10; }
		$elxis->updateCookie('rp', $rp);
		$page = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($page < 1) { $page = 1; }
		$sortname = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'item';
		if (($sortname == '') || !in_array($sortname, $columns)) { $sortname = 'item'; }
		$sortorder = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if (($sortorder == '') || !in_array($sortorder, array('asc', 'desc'))) { $sortorder = 'asc'; }
		$ctype = (isset($_POST['ctype'])) ? (int)$_POST['ctype'] : 0;

		$rows = array();
		if ($ctype == 1) {
			if ($elxis->getConfig('APC') == 0) {
				$this->ajaxHeaders('text/xml');
				echo '<rows><page>0</page><total>0</total></rows>';
				exit();
			}

			$stats = elxisAPC::getInfo();
			if (!$stats || !is_array($stats) || !isset($stats['items']) || (count($stats['items']) == 0)) {
				$this->ajaxHeaders('text/xml');
				echo '<rows><page>0</page><total>0</total></rows>';
				exit();
			}

			$now = eFactory::getDate()->getTS();
			foreach ($stats['items'] as $item) {
				$name = ($item['group'] != '') ? $item['group'].'_'.$item['name'] : $item['name'];
				$dt = $now - $item['mtime'];
				$rows[] = array('item' => $name, 'dt' => $dt, 'size' => $item['mem_size']);
			}
		} else {
			$files = eFactory::getFiles()->listFiles('cache/', '', true, true, true);
			if (!$files) {
				$this->ajaxHeaders('text/xml');
				echo '<rows><page>0</page><total>0</total></rows>';
				exit();
			}

			$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
			if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

			$now = eFactory::getDate()->getTS();
			foreach ($files as $file) {
				$filename = basename($file);
				if ($filename == 'index.html') { continue; }
				if (strpos($filename, '.') === 0) { continue; }

				$rel = str_replace($repo_path.'/cache/', '', $file);
				$dt = $now - filemtime($file);
				$size = filesize($file);
				$rows[] = array('item' => $rel, 'dt' => $dt, 'size' => $size);
			}
		}

		if (!$rows) {
			$this->ajaxHeaders('text/xml');
			echo '<rows><page>0</page><total>0</total></rows>';
			exit();
		}

		$total = count($rows);

		if (count($rows) > 1) {
			$rows = $this->sortRows($rows, $sortname, $sortorder, false);
			$limitstart = 0;
			$maxpage = ceil($total/$rp);
			if ($maxpage < 1) { $maxpage = 1; }
			if ($page > $maxpage) { $page = $maxpage; }
			$limitstart = (($page - 1) * $rp);
			if ($total > $rp) {
				$page_rows = array();
				$end = $limitstart + $rp;
				foreach ($rows as $key => $row) {
					if ($key < $limitstart) { continue; }
					if ($key >= $end) { break; }
					$page_rows[] = $row;
				}
				$rows = $page_rows;
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$page."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			foreach ($rows as $row) {
				$type = ($ctype == 1) ? 'APC' : $eLang->get('FILE');
				if ($row['size'] < 400000) {
					$size = number_format(($row['size'] / 1024), 2, '.', '').' KB';
				} else {
					$size = number_format(($row['size'] / (1024 * 1024)), 2, '.', '').' MB';
				}

				echo '<row id="'.base64_encode($ctype.':'.$row['item']).'">
					<cell><![CDATA['.$row['item'].']]></cell>
					<cell><![CDATA['.$type.']]></cell>
					<cell><![CDATA['.$this->humanTime($row['dt']).']]></cell>
					<cell><![CDATA['.$size.']]></cell>
				</row>'."\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/*********************************/
	/* HUMAN FRINDLY TIME DIFFERENCE */
	/*********************************/
	private function humanTime($dt) {
		$eLang = eFactory::getLang();
		if ($dt < 60) { return $dt.' '.$eLang->get('ABR_SECONDS'); }
		if ($dt < 3600) {
			$m = floor($dt / 60);
			$s = $dt - ($m * 60);
			return $m.' '.$eLang->get('ABR_MINUTES').', '.$s.' '.$eLang->get('ABR_SECONDS');
		}

		$d = floor($dt / 86400);
		$rem = $dt - ($d * 86400);
		$h = floor($rem / 3600);
		$rem = $rem - ($h * 3600);
		$m = floor($rem / 60);

		$parts = array();
		if ($d == 1) {
			$parts[] = '1 '.$eLang->get('DAY');
		} else if ($d > 1) {
			$parts[] = $d.' '.$eLang->get('DAYS');
		}

		if ($h == 1) {
			$parts[] = '1 '.$eLang->get('HOUR');
		} else if ($h > 1) {
			$parts[] = $h.' '.$eLang->get('HOURS');
		}

		if ($m > 0) { $parts[] = $m.' '.$eLang->get('ABR_MINUTES'); }
		return implode(', ', $parts);
	}


	/******************************/
	/* DELETE CACHED ITEMS (AJAX) */
	/******************************/
	public function deletecache() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.eFactory::getLang()->get('NOTALLOWACCPAGE');
			exit;
		}
		if (!isset($_POST['items']) || (trim($_POST['items']) == '')) {
			$this->ajaxHeaders('text/plain');
			echo '0|No items set!';
			exit();
		}

		$ctype = -1;
		$items = explode(',', $_POST['items']);
		$todelete = array();
		foreach ($items as $item) {
			$f = trim(strip_tags(base64_decode($item)));
			$parts = preg_split('/\:/', $f, 2);
			if (!isset($parts[1])) { continue; }
			if ($ctype == -1) {
				if ($parts[0] == '1') {
					$ctype = 1;
				} else if ($parts[0] == '0') {
					$ctype = 0;
				} else {
					$this->ajaxHeaders('text/plain');
					echo '0|Invalid request!';
					exit();
				}
			}

			$todelete[] = $parts[1];
		}

		if (!$todelete) {
			$this->ajaxHeaders('text/plain');
			echo '0|No items set!';
			exit();
		}

		$done = 0;
		if ($ctype == 1) {
			if ($elxis->getConfig('APC') == 0) {
				$this->ajaxHeaders('text/plain');
				echo '0|APC is disabled!';
				exit();
			}

			$apcid = (int)$elxis->getConfig('APCID');
			foreach ($todelete as $df) {
				$name = $apcid.'_'.$df;
				$ok = apc_delete($name);
				if ($ok === true) { $done++; }
			}
		} else {
			foreach ($todelete as $df) {
				$ok = $eFiles->deleteFile('cache/'.$df, true);
				if ($ok === true) { $done++; }
			}
		}

		$this->ajaxHeaders('text/plain');
		echo $done;
		exit();
	}

}

?>