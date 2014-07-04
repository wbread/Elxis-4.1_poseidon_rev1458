<?php 
/**
* @version		$Id: content.suggest.php 949 2012-03-03 18:58:36Z datahell $
* @package		Elxis
* @subpackage	Search component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

define('_ELXIS_', 1);


if (!isset($_GET['q'])) { die(); }
$pat = "#([\']|[\;]|[\.]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
$q = urldecode(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
$q = preg_replace($pat, '', $q);
if (trim($q) == '') { die(); }
if (strlen($q) < 3) {
	header("Content-type: application/x-suggestions+json; charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");
	echo '["'.$q.'",[],[],[]]';
	exit();
}

$elxis_root = str_replace( '/components/com_search/engines/content', '', str_replace(DIRECTORY_SEPARATOR, '/', dirname(__FILE__)));
if (!file_exists($elxis_root.'/configuration.php')) { die(); }
if (!defined('ELXIS_PATH')) { define('ELXIS_PATH', $elxis_root); }
include(ELXIS_PATH.'/configuration.php');

$cfg = new elxisConfig();
if ($cfg->get('ONLINE') != 1) { die(); }
if ($cfg->get('DB_TYPE') != 'mysql') { die(); }
$dsn = $cfg->get('DB_DSN');
if ($dsn == '') {
	$dsn = 'mysql:host='.$cfg->get('DB_HOST');
	if ($cfg->get('DB_PORT') > 0) { $dsn .= ';port='.$cfg->get('DB_PORT'); }
	$dsn .= ';dbname='.$cfg->get('DB_NAME').';charset=utf8';
}

$driveroptions = array();
$driveroptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
//IMPORTANT: 500ms timeout (or bug?) in firefox, unfortunately makes it impossible to query db on time...
try {
	$pdo = new PDO($dsn, $cfg->get('DB_USER'), $cfg->get('DB_PASS'), $driveroptions);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	die();
}

//query must be simple and fast!!!
$qstr = $q.'%';
$sql = "SELECT title FROM ".$cfg->get('DB_PREFIX')."content"
."\n WHERE title LIKE :qstr AND published=1 AND alevel=0 ORDER BY title ASC LIMIT 0,5";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':qstr', $qstr, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_COLUMN);
$pdo = null;

if (!$results) {
	header("Content-type: application/x-suggestions+json; charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
	header("Pragma: no-cache");
	echo '["'.$q.'",[],[],[]]';
	exit();
}

$suggestions = array();
foreach ($results as $result) {
	$suggestions[] = '"'.addslashes($result).'"';
}

header("Content-type: application/x-suggestions+json; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");
echo '["'.$q.'",['.implode(',',$suggestions).'],[],[]]';
exit();

?>