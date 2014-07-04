<?php 
/**
* @version		$Id: defender.class.php 1408 2013-04-11 14:33:29Z datahell $
* @package		Elxis
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
* Part of this script is inpired by ZBblock (Zaphod Breeblebrox - http://www.spambotsecurity.com)
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$defstart = microtime(true);

if (isset($_SERVER['QUERY_STRING'])) {
	if (strpos(urldecode($_SERVER['QUERY_STRING']), chr(0)) !== false) {
		exitPage::make('security', 'DEF-0001');
	}
    if (preg_match("#((\%0d)|(\%0a)|(\\\r)|(\\\n))#", $_SERVER['QUERY_STRING'])) {
    	exitPage::make('security', 'DEF-0002', 'Possible CRLF injection/HTTP response split.');
    }
}

if (isset($_COOKIE)) {
    if (preg_match("#((\%0d)|(\%0a)|(\\\r)|(\\\n))#", serialize($_COOKIE))) {
    	exitPage::make('security', 'DEF-0003', 'Possible CRLF injection/HTTP response split.');
    }
}


class elxisDefender {

	private $cfg = NULL;
	private $types = array();
	private $repo_path = '';
	private $query = '';
	private $querydec = '';
	private $querydecsws = '';
	private $requesturi = '';
	private $lcrequesturi = '';
	private $useragent = '';
	private $useragentsws = '';
	private $address = '';
	private $fromhost = '';
	private $rawpost = '';
	private $rawpostsws = '';
	private $lcpost = '';
	private $bantimes = 3;
	private $banmessage = '';


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		elxisLoader::loadFile('configuration.php');
		$this->cfg = new elxisConfig();

		$this->setTypes();
		if (count($this->types) == 0) { return; }

		$this->repo_path = rtrim($this->cfg->get('REPO_PATH'), '/');
		if ($this->repo_path == '') { $this->repo_path = ELXIS_PATH.'/repository'; }
		if (!file_exists($this->repo_path.'/logs/') || !is_dir($this->repo_path.'/logs/')) {
			die('Elxis repository folder logs does not exist!');
		}

		if (isset($_SERVER['QUERY_STRING'])) {
			$this->query = strtolower($_SERVER['QUERY_STRING']);
			$this->querydec = strtolower(urldecode($_SERVER['QUERY_STRING']));
			$this->querydecsws = preg_replace('/\s+/','',$this->querydec);
			$this->querydecsws = preg_replace("/[^\x9\xA\xD\x20-\x7F]/",'',$this->querydecsws);
		}

		$this->requesturi = $this->getURI();
		$this->lcrequesturi = strtolower($this->requesturi);

		$this->address = $this->getIP();
		if (function_exists('gethostbyaddr') && is_callable('gethostbyaddr') && ($this->address != '')) {
			$this->hoster = strtolower(gethostbyaddr($this->address)); 
		}

		if (isset($_SERVER['HTTP_REFERER'])) {
			$this->fromhost = strtolower($_SERVER['HTTP_REFERER']);
		}

		if (isset($_SERVER['HTTP_USER_AGENT'])) {
			$this->useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
			$this->useragentsws = preg_replace('/\s+/', '', $this->useragent);
			$this->useragentsws = preg_replace("/[^\x9\xA\xD\x20-\x7F]/", '', $this->useragentsws);
		}

		if (isset($_POST) && (count($_POST) > 0)) {
			$this->rawpost = file_get_contents('php://input');
			$this->rawpost = substr($this->rawpost, 0, 4194304);
			$this->rawpostsws = preg_replace('/\s+/','',$this->rawpost);
			$this->rawpostsws = preg_replace("/[^\x9\xA\xD\x20-\x7F]/",'',$this->rawpostsws);

			$randstr = 'xxx'.rand(0,1000000).'yyy';
			$this->lcpost = '&'.strtolower($this->rawpost);
			while(substr_count($this->lcpost,"%25") > 0) {
				$this->lcpost = str_replace("%25","%",$this->lcpost);
			}
			$this->lcpost = str_replace('%26',$randstr,$this->lcpost);
			$this->lcpost = urldecode($this->lcpost);
			$this->lcpost = str_replace($randstr,"%26",$this->lcpost);
		}
	}


	/***************************/
	/* PERFORM SECURITY CHECKS */
	/***************************/
	public function check() {
		if (count($this->types) == 0) { return; }
		$this->checkBanned();
		foreach ($this->types as $type) {
			switch($type) {
				case 'G': $this->checkSignatures('general'); break;
				case 'C': $this->checkSignatures('custom'); break;
				case 'P': $this->checkSignatures('post'); break;
				case 'H': $this->checkSignatures('hosts'); break;
				case 'I': $this->checkSignatures('ips'); break;
				case 'A': $this->checkSignatures('agents'); break;
				case 'F': $this->checkFS(); break;
				default: break;
			}
		}
	}


	/*******************/
	/* SET CHECK TYPES */
	/*******************/
	private function setTypes() {
		if (trim($this->cfg->get('DEFENDER')) != '') {
			$this->types = str_split($this->cfg->get('DEFENDER'));
			if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
		}
		switch ($this->cfg->get('SECURITY_LEVEL')) {
			case 1:
				if (!in_array('G', $this->types)) { $this->types[] = 'G'; }
				if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
				if (!in_array('F', $this->types)) { $this->types[] = 'F'; }
			break;
			case 2:
				if (!in_array('G', $this->types)) { $this->types[] = 'G'; }
				if (!in_array('C', $this->types)) { $this->types[] = 'C'; }
				if (!in_array('P', $this->types)) { $this->types[] = 'P'; }
				if (!in_array('H', $this->types)) { $this->types[] = 'H'; }
				if (!in_array('I', $this->types)) { $this->types[] = 'I'; }
				if (!in_array('A', $this->types)) { $this->types[] = 'A'; }
				if (!in_array('F', $this->types)) { $this->types[] = 'F'; }
			break;
			case 0: default: break;
		}
	}


	/******************************/
	/* FILESYSTEM INTEGRITY CHECK */
	/******************************/
	private function checkFS() {
		$files = $this->getLockFiles();
		$hashfile = $this->repo_path.'/other/elxis_hashes_'.md5($this->cfg->get('ENCRYPT_KEY')).'.php';
		if (!file_exists($hashfile)) {
			$buffer = '<?php '._LEND._LEND;
			$buffer .= '//Elxis Defender - Filesystem hash fingerprint generated on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND._LEND;
			$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
			$buffer .= '$hashes = array('._LEND;
			foreach ($files as $file) {
				$m = md5_file(ELXIS_PATH.'/'.$file);
				$buffer.= "\t".'array(\''.$file.'\', \''.$m.'\'),'._LEND;
			}
			$buffer .= ');'._LEND._LEND;
			$buffer .= '?>';

			$ok = false;
			if ($handler = @fopen($hashfile, 'w')) {
				$ok = @fwrite($handler, $buffer);
            	fclose($handler);
        	}
			if (!$ok) {
				$msg = 'Elxis Defender could not save fingerprint (hashes) file! Please make sure Elxis repository is writable.';
				exitPage::make('fatal', 'DEFF-0001', $msg);
			}
        	return true;
		}

		include($hashfile);
		if (!isset($hashes) || !is_array($hashes) || (count($hashes) == 0)) {
			$msg = 'Elxis Defender detected an empty or invalid fingerprint (hashes) file! If you dont 
			know what to do consider Elxis documentation or visit Elxis forums for support.';
			exitPage::make('fatal', 'DEFF-0002', $msg);
		}

		$i = 1;
		foreach ($hashes as $hash) {
			$f = $hash[0];
			if (!in_array($f, $files)) {
				$n = sprintf("%04d", $i);
				$mailmsg = 'A protected file has been deleted!'."\r\n";
				$mailmsg .= 'The deleted file is: '.$f."\r\n";
				$mailmsg .= 'Actions to perform (pick one):'."\r\n";
				$mailmsg .= '1. If the deletion made by an unauthorized person, or accidently by you, restore the original protected file.'."\r\n";
				$mailmsg .= '2. If you accept this deletion then you must delete the Elxis hashes file in order for the Elxis Defender to regenarate it without the deleted file.'."\r\n";
				$mailmsg .= "Elxis Defender hashes file: ".$hashfile."\r\n";
				$mailmsg .= "The site wont come back online until you perform one of the above actions.\r\n"; 
				$this->sendAlert('SEC-DEFF-'.$n, $mailmsg, 0);

				$msg = 'A protected file has been deleted! If you dont know what 
				to do consider Elxis documentation or visit Elxis forums for support.';
				exitPage::make('security', 'DEFF-'.$n, $msg);
			}
			$m = md5_file(ELXIS_PATH.'/'.$f);
			if ($m != $hash[1]) {
				$n = sprintf("%04d", $i);
				$mailmsg = 'A protected file has been modified!'."\r\n";
				$mailmsg .= 'The modified file is: '.$f."\r\n";
				$mailmsg .= 'Actions to perform (pick one):'."\r\n";
				$mailmsg .= '1. If the modification made by an unauthorized person, or accidently by you, restore the original protected file.'."\r\n";
				$mailmsg .= '2. If you accept this modification then you must delete the Elxis hashes file in order for the Elxis Defender to regenarate it without the modified file.'."\r\n";
				$mailmsg .= "Elxis Defender hashes file: ".$hashfile."\r\n";
				$mailmsg .= "The site wont come back online until you perform one of the above actions.\r\n"; 
				$this->sendAlert('SEC-DEFF-'.$n, $mailmsg, 0);

				$msg = 'A protected file has been modified! If you dont know what 
				to do consider Elxis documentation or visit Elxis forums for support.';
				exitPage::make('security', 'DEFF-'.$n, $msg);
			}
			$i++;
		}
	}


	/*******************************************/
	/* CHECK IF IP IS BANNED BY ELXIS DEFENDER */
	/*******************************************/
	private function checkBanned() {
		if ($this->address == '') { return; }
		$file = $this->repo_path.'/logs/defender_ban.php';
		if (!file_exists($file)) { return; }
		include($file);
		if (!isset($ban) || !is_array($ban) || (count($ban) == 0)) { return; }
		$ip = str_replace('.', 'x', $this->address);
		$ip = str_replace(':', 'y', $ip);
		if (isset($ban[$ip])) {
			if ($ban[$ip]['times'] >= $this->bantimes) {
				$msg = 'You have been banned! If you think this is wrong contact the site administrator.';
				exitPage::make('security', 'DEFB-0001', $msg);
			}
		}
	}


	/********************/
	/* CHECK SIGNATURES */
	/********************/
	private function checkSignatures($type) {
		if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/defender/'.$type.'.php')) { return; }
		include(ELXIS_PATH.'/includes/libraries/elxis/defender/'.$type.'.php');
		if (!isset($signatures) || !is_array($signatures) || (count($signatures) == 0)) { return; }
		$i = 1;
		foreach ($signatures as $sign) {
			$func = $sign[0];
			$haystack = $sign[1];
			if (isset($this->$haystack) && ($this->$haystack != '') && method_exists($this, $func)) {
				if ($this->$func($this->$haystack, $sign[2], $sign[3])) {
					$char = strtoupper(substr($type, 0, 1));
					$last = count($sign) - 1;
					$n = sprintf("%04d", $i);

					if (in_array($type, array('general', 'custom', 'post', 'agents'))) {
						$this->banIP('DEF'.$char.'-'.$n);
					}

					$mailmsg = "Signatures: \t".$type."\r\n";
					$mailmsg .= "Match method: \t".$func."\r\n";
					$mailmsg .= "Haystack: \t".$haystack."\r\n";
					$mailmsg .= "Pattern match: \t".addslashes($sign[2])."\r\n";
					$mailmsg .= "Reason: \t".$sign[$last]."\r\n";
					if ($this->banmessage != '') { $mailmsg .= $this->banmessage."\r\n"; }
					$this->sendAlert('SEC-DEF'.$char.'-'.$n, $mailmsg, 1);

					exitPage::make('security', 'DEF'.$char.'-'.$n, $sign[$last]);
				}
			}
			$i++;
		}
	}


	/***********************************/
	/* EXACT MATCH PATTERN WITH STRING */
	/***********************************/
	private function match($haystack, $pattern, $x='') {
		if ($haystack == $pattern) { return true; }
		return false;
	}


	/************************************/
	/* MATCH PATTERN ANYWHERE IN STRING */
	/************************************/
	private function inmatch($haystack, $pattern, $x='') {
		if (substr_count($haystack,$pattern)) { return true; }
		return false;
	}


	/*****************************************/
	/* MATCH PATTERN IN RIGHT SIDE OF STRING */
	/*****************************************/
	private function rmatch($haystack, $pattern, $x='') {
		$length = strlen($pattern);
		if (substr($haystack,-$length) == $pattern) { return true; }
		return false;
	}


	/*****************************************/
	/* MATCH PATTERN IN RIGHT SIDE OF STRING */
	/*****************************************/
	private function lmatch($haystack, $pattern, $x='') {
		$length = strlen($pattern);
		if (substr($haystack, 0, $length) == $pattern) { return true; }
		return false;
	}


	/*********************************************/
	/* MATCH PATTERN MORE THAN N TIMES IN STRING */
	/*********************************************/
	private function minmatch($haystack, $pattern, $allowed) {
		if (substr_count($haystack,$pattern) > $allowed){ return true; }
		return false;
	}


	/***********************************/
	/* REGULAR EXPRESSION MATCH STRING */
	/***********************************/
	private function regexmatch($haystack, $pattern, $x='') {
		if (preg_match('%'.$pattern.'%i', $haystack)) { return true; }
		return false;
	}


	/******************/
	/* CHECK IP RANGE */
	/******************/
	private function iprange($thisip, $lowip, $highip) {
		$longthisip = sprintf('%u', ip2long($thisip));
		$longlowip = sprintf('%u', ip2long($lowip));
		$longhighip = sprintf('%u', ip2long($highip));
		if (($longlowip <= $longthisip) === ($longthisip <= $longhighip)){ return true; }
		return false;
	}


	/***********************/
	/* CHECK CIDR IP BLOCK */
	/***********************/
	private function cidrblock($thisip, $cidr, $x='') {
		list($lowip, $range) = explode("/", $cidr);
		$range = 32 - $range;
		$range = pow(2,$range);
		$longthisip = sprintf('%u', ip2long($thisip));
		$longlowip = sprintf('%u', ip2long($lowip));
		$longhighip = $longlowip + $range;
		if (($longlowip <= $longthisip) === ($longthisip <= $longhighip)){ return true; }
		return false;
	}


	/*******************/
	/* SEND MAIL ALERT */
	/*******************/
	private function sendAlert($code, $msg='', $attack=1) {
		$file = $this->repo_path.'/logs/defender_notify.txt';
		@clearstatcache();
		if (!file_exists($file)) {
			@touch($file);
			$proceed = true;
		} else {
			$last = filemtime($file);
			if ((time() - filemtime($file)) > 300) {
				@touch($file);
				$proceed = true;
			} else {
				$proceed = false;
			}
		}

		if (!$proceed) { return; }

		$uri = @addslashes(htmlspecialchars(urldecode($this->requesturi), ENT_NOQUOTES, 'UTF-8'));
    	$parsed = parse_url($this->cfg->get('URL')); 
 		$host = preg_replace('#^(www\.)#i', '', $parsed['host']);
		$subject = 'Message from Elxis Defender on '.$host;

		if ($attack == 1) {
			$text = "Elxis Defender blocked an attack to your site!\r\n";
		} else {
			$text = "Elxis Defender detected a change in site filesystem!\r\n";
		}
		$text .= 'Reference code: '.$code."\r\n";
		if ($msg != '') { $text .= "\r\nElxis Defender report\r\n".$msg."\r\n"; }
		$text .= "\r\n";
		$text .= "Requested URI: \t".$uri."\r\n";
		if ($this->address != '') { $text .= "IP address: \t".$this->address."\r\n"; }
		if ($this->hoster != '') { $text .= "Hostname: \t".$this->hoster."\r\n"; }
		if ($this->fromhost != '') { $text .= "HTTP Referrer: \t".$this->fromhost."\r\n"; }
		if (isset($_SERVER['HTTP_USER_AGENT'])) { $text .= "User agent: \t".$_SERVER['HTTP_USER_AGENT']."\r\n"; }
		$text .= "Date (UTC): \t".gmdate('Y-m-d H:i:s')."\r\n";
		$text .= "Site URL: \t".$this->cfg->get('URL')."\r\n\r\n\r\n\r\n";
		$text .= "----------------------------------------------------------\r\n";
		$text .= "Elxis Defender by Elxis Team\r\n";
		$text .= 'Please do not reply to this message as it was generated automatically by the Elxis Defender ';
		$text .= 'and it was sent for informational purposes. Elxis Defender will not send you an other notification for ';
		$text .= 'the next 5 minutes even if more attacks occur.'."\r\n";
		$text .= "----------------------------------------------------------\r\n";

		require_once(ELXIS_PATH.'/includes/libraries/swift/swift_required.php');

		$message = Swift_Message::newInstance();
		$message->setCharset('UTF-8');
		$message->setPriority(2);
		$message->setSubject($subject);
		$message->setBody($text, 'text/plain');
		$message->addTo($this->cfg->get('MAIL_MANAGER_EMAIL'), $this->cfg->get('MAIL_MANAGER_NAME'));
		$message->setFrom(array('defender@'.$host => 'Elxis Defender'));
		$headers = $message->getHeaders();
		$headers->addTextHeader('X-Mailer', 'Elxis');

		switch ($this->cfg->get('MAIL_METHOD')) {
			case 'smtp':
				$transport = Swift_SmtpTransport::newInstance(
					$this->cfg->get('MAIL_SMTP_HOST'), 
					$this->cfg->get('MAIL_SMTP_PORT'),
					$this->cfg->get('MAIL_SMTP_SECURE')
				);
				if ($this->cfg->get('MAIL_SMTP_AUTH') == 1) {
					$transport->setUsername($this->cfg->get('MAIL_SMTP_USER'));
					$transport->setPassword($this->cfg->get('MAIL_SMTP_PASS'));
				}
			break;
			case 'sendmail':
				$transport = Swift_SendmailTransport::newInstance();
			break;
			case 'mail': default:
				$transport = Swift_MailTransport::newInstance();
			break;
		}

		$mailer = Swift_Mailer::newInstance($transport);
		$mailer->send($message);
	}


	/*********************/
	/* BAN AN IP ADDRESS */
	/*********************/
	private function banIP($refcode) {
		if ($this->address == '') { return; }
		$file = $this->repo_path.'/logs/defender_ban.php';
		$ip = str_replace('.', 'x', $this->address);
		$ip = str_replace(':', 'y', $ip);

		$nowtimes = 1;
		$buffer = '<?php '._LEND._LEND;
		$buffer .= '//Elxis Defender - Banned IPs - Last updated on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND._LEND;
		$buffer .= 'defined(\'_ELXIS_\') or die (\'Protected by Elxis Defender\');'._LEND._LEND;
		$buffer .= '$ban = array('._LEND;
		if (!file_exists($file)) {
			$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
		} else {
			include($file);
			$found = false;
			if (isset($ban) && is_array($ban) && (count($ban) > 0)) {
				foreach ($ban as $key => $row) {
					if ($key == $ip) {
						$found = true;
						$nowtimes = $row['times'] + 1;
						$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
					} else {
						$buffer .= '\''.$key.'\' => array(\'times\' => '.$row['times'].', \'refcode\' => \''.$row['refcode'].'\', \'date\' => \''.$row['date'].'\'),'._LEND;
					}
				}
			}
			unset($ban);

			if (!$found) {
				$buffer .= '\''.$ip.'\' => array(\'times\' => '.$nowtimes.', \'refcode\' => \'SEC-'.$refcode.'\', \'date\' => \''.gmdate('Y-m-d H:i:s').'\'),'._LEND;
			}
		}

		$buffer .= ');'._LEND._LEND;
		$buffer .= '?>';

		if ($nowtimes >= $this->bantimes) {
			$this->banmessage .= 'The guest has been BANNED as he was blocked by Elxis Defender '.$nowtimes.' times!'."\r\n";
		}

		if ($handler = @fopen($file, 'w')) {
			$ok = @fwrite($handler, $buffer);
			fclose($handler);
			if ($ok) {
				@clearstatcache();
				$fsize = intval(filesize($file) / 1024);
				if ($fsize > 300) {
					$this->banmessage .= 'Bans log file is '.$fsize.'KB! Please remove old bans to make it load faster.'."\r\n";
					$this->banmessage .= 'Bans log file: '.$file;
				}
			}
		}
	}


	/*********************/
	/* GET REQUESTED URI */
	/*********************/
	private function getURI() {
		if (isset($_SERVER['REQUEST_URI'])) { return $_SERVER['REQUEST_URI']; }
		if (isset($_SERVER['QUERY_STRING'])) {
			$query_str = $_SERVER['QUERY_STRING'];
		} else if (@getenv('QUERY_STRING')) {
			$query_str = getenv('QUERY_STRING');
		} else {
			$query_str = '';
		}
		if ($query_str != '') { $query_str = '?'.$query_str; }

		if (isset($_SERVER['PATH_INFO'])) {
			return $_SERVER['PATH_INFO'].$query_str;
		} elseif (@getenv('PATH_INFO')) {
			return getenv('PATH_INFO').$query_str;
		}

		if (isset($_SERVER['PHP_SELF'])) {
			return $_SERVER['PHP_SELF'].$query_str;
		} elseif (@getenv('PHP_SELF')) {
			return getenv('PHP_SELF').$query_str;
		} else {
			return $query_str;
		}
	}


	/*******************/
	/* GET CLIENT'S IP */
	/*******************/
	private function getIP() {
    	if (isset($_SERVER['HTTP_CLIENT_IP']) && ($_SERVER['HTTP_CLIENT_IP'] != '')) {
    		$ip = $_SERVER['HTTP_CLIENT_IP'];
    	} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && ($_SERVER['HTTP_X_FORWARDED_FOR'] != '')) {
    		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    	} elseif (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '')) {
    		$ip = $_SERVER['REMOTE_ADDR'];
    	} else {
    		$ip = '';
    	}
    	return $ip;
	}


	/***********************************/
	/* GET ELXIS FILESYSTEM LOCK FILES */
	/***********************************/
	private function getLockFiles() {
		$files = array('index.php', 'inner.php', 'includes/loader.php');
		$libs = $this->listFiles('includes/libraries/elxis/');
		if ($libs) {
			foreach ($libs as $lib) { $files[] = $lib; }
		}
		unset($libs);
		$tpls = $this->listFolders('templates/');
		if ($tpls) {
			foreach ($tpls as $tpl) {
				if (file_exists(ELXIS_PATH.'/'.$tpl.'index.php')) {
					$files[] = $tpl.'index.php';
				}
				if (file_exists(ELXIS_PATH.'/'.$tpl.'inner.php')) {
					$files[] = $tpl.'inner.php';
				}
			}
		}
		unset($tpls);
		return $files;
	}


	/*****************************/
	/* LIST FILES IN A DIRECTORY */
	/*****************************/
	private function listFiles($dir, $onlyphp=true) {
		$path = ELXIS_PATH.'/'.$dir;
		if (!is_dir($path)) { return array(); }
		$arr = array();
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..')) {
				if ($onlyphp) {
					if (preg_match('#(\.php)$#i', $entry)) { $arr[] = $dir.$entry; }
				} else {
					$arr[] = $dir.$entry;
				}
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}


	/*******************************/
	/* LIST FOLDERS IN A DIRECTORY */
	/*******************************/
	private function listFolders($dir) {
		$path = ELXIS_PATH.'/'.$dir;
		if (!is_dir($path)) { return array(); }
		$arr = array();
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..') && is_dir($path.$entry)) {
				$arr[] = $dir.$entry.'/';
			}
		}
		closedir($handle);
		asort($arr);
		return $arr;
	}

}


$defender = new elxisDefender();
$defender->check();
unset($defender);

$dt = microtime(true) - $defstart;
define('ELXIS_DEFENDER_DT', $dt);

?>