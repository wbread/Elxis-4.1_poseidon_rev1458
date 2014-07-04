<?php 
/**
* @version		$Id: tools.php 1311 2012-09-30 08:01:03Z datahell $
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

if (defined('_ELXIS_')) { die ('You can not include installer tools!'); }
if (defined('ELXIS_PATH')) { die ('You can not include installer tools!'); }


$elxis_root = str_replace('/includes/install/inc', '', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)));
if (file_exists($elxis_root.'/configuration.php')) { die ('Invalid request'); } //can run only when Elxis is not installed

define('ELXIS_PATH', $elxis_root);
define('_ELXIS_', 1);
if (!defined('ELXIS_INSTALLER')) { define('ELXIS_INSTALLER', 1); }


function sendHeaders($type='text/plain') {
	if(ob_get_length() > 0) { ob_end_clean(); }
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
	header('Cache-Control: no-cache, must-revalidate');
	header('Pragma: no-cache');
	header('Content-type:'.$type.'; charset=utf-8');
}


function checkFTP() {
	$host = trim(filter_input(INPUT_POST, 'fho', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$port = intval(filter_input(INPUT_POST, 'fpo', FILTER_SANITIZE_NUMBER_INT));
	if ($port < 1) { $port = 21; }
	$user = trim(filter_input(INPUT_POST, 'fus', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$pass = trim(filter_input(INPUT_POST, 'fpa', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$root = trim(filter_input(INPUT_POST, 'fro', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$root = rtrim($root, '/');
	if ($root == '') { $root = '/'; }

	$out = array('success' => 0, 'message' => 'Invalid request');

	if ($host == '') {
		$out['message'] = 'FTP host can not be empty!';
		sendHeaders('application/json');
		echo json_encode($out);
		exit();
	}
	if ($user == '') {
		$out['message'] = 'FTP user can not be empty!';
		sendHeaders('application/json');
		echo json_encode($out);
		exit();
	}
	if ($pass == '') {
		$out['message'] = 'FTP password can not be empty!';
		sendHeaders('application/json');
		echo json_encode($out);
		exit();
	}
	if ($root == '') {
		$out['message'] = 'FTP path can not be empty!';
		sendHeaders('application/json');
		echo json_encode($out);
		exit();
	}

	include(ELXIS_PATH.'/includes/libraries/elxis/ftp.class.php');
	$params = array('ftp_host' => $host, 'ftp_port' => $port, 'ftp_user' => $user, 'ftp_pass' => $pass);
	$ftp = new elxisFTP($params);
	if ($ftp->getStatus() != 'connected') {
		$msg = $ftp->getError();
		if ($msg == '') { $msg = 'Could not connect to FTP server!'; }

		$out['message'] = $msg;
		sendHeaders('application/json');
		echo json_encode($out);
		exit();
	}

	$rfiles = $ftp->nlist($root);
	$ftp->disconnect();
	if ($rfiles && is_array($rfiles) && (count($rfiles) > 0)) {
		foreach ($rfiles as $rfile) {
			if (strpos($rfile, 'inner.php') !== false) {
				$out['success'] = 1;
				$out['message'] = '';
				sendHeaders('application/json');
				echo json_encode($out);
				exit();
			}
		}
	}

	$out['success'] = 1;
	$out['message'] = 'Connected successfully to FTP host but Elxis filesystem not found. Is the FTP path correct?';
	sendHeaders('application/json');
	echo json_encode($out);
	exit();
}


function checkDB() {
	$dbtype = trim(filter_input(INPUT_POST, 'dty', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$host = trim(filter_input(INPUT_POST, 'dho', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$port = intval(filter_input(INPUT_POST, 'dpo', FILTER_SANITIZE_NUMBER_INT));
	if ($port < 0) { $port = 0; }
	$dbname = trim(filter_input(INPUT_POST, 'dna', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$table_prefix = trim(filter_input(INPUT_POST, 'dpr', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$username = trim(filter_input(INPUT_POST, 'dus', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$password = trim(filter_input(INPUT_POST, 'dpa', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$dsn = trim(filter_input(INPUT_POST, 'dds', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$scheme = trim(filter_input(INPUT_POST, 'dsc', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));

	$pdodrivers = PDO::getAvailableDrivers();
	if (!$pdodrivers) { $pdodrivers = array(); }

	if (($dbtype == '') || !in_array($dbtype, $pdodrivers)) {
		sendHeaders('text/plain');
		echo 'msg:Invalid or not supported database type!';
		exit();
	}
	if ($dbname == '') {
		sendHeaders('text/plain');
		echo 'msg:Invalid database name!';
		exit();
	}
	if ($table_prefix == '') {
		sendHeaders('text/plain');
		echo 'msg:Invalid database prefix!';
		exit();
	}
	if ($scheme != '') {
		$scheme = str_replace('\\', '/', $scheme);
		if (!is_file($scheme)) {
			sendHeaders('text/plain');
			echo 'msg:Database schema file does not exist!';
			exit();
		}
	}
	if ((($dbtype == 'sqlite') || ($dbtype == 'sqlite2')) && ($scheme == '')) {
		sendHeaders('text/plain');
		echo 'msg:A schema file is required for '.$dbtype;
		exit();
	}
	if ($host == '') {
		sendHeaders('text/plain');
		echo 'msg:Invalid host!';
		exit();
	}

	if (($dsn == '') && ($scheme == '')) {
		if ($username == '') {
			sendHeaders('text/plain');
			echo 'msg:Invalid username!';
			exit();
		}
		if ($password == '') {
			sendHeaders('text/plain');
			echo 'msg:Invalid password!';
			exit();
		}
	}

	include(ELXIS_PATH.'/includes/install/inc/miniloader.php');
	include(ELXIS_PATH.'/includes/libraries/elxis/database.class.php');
	$params = array(
		'dbtype' => $dbtype,
		'host' => $host,
		'port' => $port,
		'dbname' => $dbname,
		'username' => $username,
		'password' => $password,
		'persistent' => 0,
		'dsn' => $dsn,
		'scheme' => $scheme,
		'table_prefix' => $table_prefix,
		'debug' => 0
	);

	$db = new elxisDatabase($params, array(), false);
	$okcon = $db->connect($params['dsn'], $params['username'], $params['password'], array(), true); //on fail returns false
	if (!$okcon) {
		$status = 'FAILED';
	} else {
		$db->disconnect();
		$status = 'OK';
	}

	sendHeaders('text/plain');
	echo $status;
	exit();
}


function makeUsername() {
	$curname = trim(filter_input(INPUT_POST, 'curname', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	$curlang = trim(filter_input(INPUT_POST, 'curlang', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
	if ($curlang == '') { $curlang = 'en'; }

	$out = array('success' => 0, 'message' => 'Request failed', 'uname'=> '');

	$greek = array('zeus', 'hermes', 'apollo', 'athena', 'poseidon', 'hades', 'cronus', 'erebos', 'chaos', 
	'uranus', 'tartarus', 'iapetos', 'atlas', 'prometheus', 'gaia', 'talos', 'typhon', 'phobos', 'cerberus',
	'medusa', 'proteus', 'triton', 'pandora', 'electra', 'nestor', 'pythagoras', 'socrates', 'archimedes', 'hector', 
	'ajax', 'theseus', 'orpheus', 'cadmus', 'anaxagoras', 'protagoras', 'hypatia', 'homer');
	$italian = array('flora', 'janus', 'juno', 'mars', 'mercury', 'pluto', 'saturn', 'venus', 'vulcan', 
	'vesta', 'minerva', 'fauna', 'diana', 'aurora', 'luna', 'hercules', 'augustus', 'caligula', 'tiberius', 
	'titus', 'magnus', 'severus', 'claudius', 'cicero', 'seneca');
	$german = array('odin', 'loki', 'thor', 'balder', 'njord', 'buri', 'seth', 'freya', 'freyr', 'midgard', 'valhalla', 
	'hesus', 'fornjot', 'druden', 'donar', 'alfadir', 'picullus');
	$inter = array('helix', 'acropolis', 'analysis', 'genesis', 'eureka', 'abyss', 'enigma', 'amazon', 
	'anax', 'asterisk', 'helios', 'nectar', 'utopia', 'paradox', 'alpha', 'delta', 'epsilon', 'sigma', 'omega', 'olympus', 'pilot', 'harmony', 
	'acrobat', 'astronaut', 'captain', 'cosmos', 'discus', 'hypnosis', 'logic', 'micro', 'neutron', 'electron', 'proton', 
	'photon', 'neuron', 'oasis', 'panic', 'phoenix', 'planet', 'python', 'thesis', 'typhoon', 'android', 'energy', 
	'oxygen', 'bios', 'logos');

	$f = rand(0, 9); //add more random
	$s = $f + 1;
	if ($curlang == 'el') {
		shuffle($greek);
		$proposal = $greek[$f];
		if (($curname != '') && ($curname == $proposal)) {
			$proposal = $greek[$s];
		}
	} else if ($curlang == 'it') {
		shuffle($italian);
		$proposal = $italian[$f];
		if (($curname != '') && ($curname == $proposal)) {
			$proposal = $italian[$s];
		}
	} else if ($curlang == 'de') {
		shuffle($german);
		$proposal = $german[$f];
		if (($curname != '') && ($curname == $proposal)) {
			$proposal = $german[$s];
		}
	} else {
		$arr = array_merge($greek, $inter);
		shuffle($arr);
		$proposal = $arr[$f];
		if (($curname != '') && ($curname == $proposal)) {
			$proposal = $arr[$s];
		}
	}

	$out['success'] = 1;
	$out['uname'] = $proposal;
	$out['message'] = '';

	sendHeaders('application/json');
	echo json_encode($out);
	exit();
}


$action = isset($_POST['action']) ? $_POST['action'] : '';
if ($action == '') { $action = isset($_GET['action']) ? $_GET['action'] : ''; }
if ($action == '') { die('Invalid Request!'); }

if ($action == 'checkftp') {
	checkFTP();
} else if ($action == 'checkdb') {
	checkDB();
} else if ($action == 'makeuname') {
	makeUsername();
}

sendHeaders('text/plain');
echo 'Invalid request!';
exit();

?>