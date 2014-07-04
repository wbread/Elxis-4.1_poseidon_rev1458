<?php 
/**
* @version		$Id: multisites.php 1445 2013-05-22 18:06:05Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class multisitesCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/**************************/
	/* LIST / EDIT MULTISITES */
	/**************************/
	public function listsites() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$curl = $elxis->getConfig('URL');
		$cpath = '';
		$p1 = parse_url($curl);
		if (isset($p1['path'])) {
			$fullpath = trim($p1['path'], '/');
			$p2 = explode('/', $fullpath);
			$n = count($p2) - 1;
			$cpath = $p2[$n];
			unset($n, $p2);
		}
		unset($p1);

		$rows = array();
		if (defined('ELXIS_MULTISITE')) {
			include(ELXIS_PATH.'/configuration.php');
			foreach ($multisites as $id => $multisite) {
				$row = new stdClass;
				$row->id = $id;
				$row->name = $multisite['name'];
				$row->folder = $multisite['folder'];
				$row->active = (bool)$multisite['active'];
				if (ELXIS_MULTISITE == $id) {
					$row->current = true;
					$row->url = $curl;
				} else {
					$row->current = false;
					$row->url = '';
				}
				$rows[] = $row;
			}

			foreach ($rows as $i => $row) {
				if ($row->url == '') {
					$rows[$i]->url = $this->calculateURL($row->folder, $cpath, $curl);
				}
			}
		}

		eFactory::getPathway()->addNode($eLang->get('MULTISITES'));
		$eDoc->setTitle($eLang->get('MULTISITES').' - '.$eLang->get('ADMINISTRATION'));

		$this->view->listSites($rows);
	}


	/*****************************/
	/* CALCULATE MULTISITE'S URL */
	/*****************************/
	private function calculateURL($folder, $cpath, $curl) {
		if ($cpath == '') {
			return rtrim($curl.'/'.$folder, '/');
		}
		$url = preg_replace('#('.$cpath.')$#', $folder, $curl);
		return rtrim($url, '/');
	}



	/*********************/
	/* ENABLE MULTISITES */
	/*********************/
	public function enablemultiple() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$redirurl = $elxis->makeAURL('cpanel:multisites/');

		if (defined('ELXIS_MULTISITE')) { $elxis->redirect($redirurl); }

		$contents = @file_get_contents(ELXIS_PATH.'/configuration.php'); 
		if ($contents === false) {
			$elxis->redirect($redirurl, 'Could not read configuration file', true);
		}

		$ok = $eFiles->createFile('config1.php', $contents);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not create config1.php file', true);
		}

		$name = $elxis->getConfig('SITENAME');
		$name = filter_var($name, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$name = eUTF::trim(preg_replace($pat, '', $name));

		$sites = array(
			1 => array('folder' => '', 'name' => $name, 'active' => true)
		);

		$multiconfig = $this->makeMultiConfig($sites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not update configuration.php file', true);
		}

		$elxis->redirect($redirurl);
	}


	/****************************/
	/* CREATE MULTISITE ENTRIES */
	/****************************/
	private function makeMultiConfig($sites) {
		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC)'._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= '$multisites = array('._LEND;
		$total = count($sites);
		$i = 1;
		foreach ($sites as $id => $site) {
			$acttxt = ($site['active'] === true) ? 'true' : 'false';
			$comma = ($i == $total) ? '' : ',';
			$out .= "\t".''.$id.' => array(\'folder\' => \''.$site['folder'].'\', \'name\' => \''.$site['name'].'\', \'active\' => '.$acttxt.')'.$comma._LEND;
			$i++;
		}
		$out .= ');'._LEND._LEND;
		$out .= 'include(ELXIS_PATH.\'/includes/multiconfig.php\');'._LEND._LEND;
		$out .= '?>';
		return $out;
	}


	/**********************/
	/* DISABLE MULTISITES */
	/**********************/
	public function disablemultiple() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$redirurl = $elxis->makeAURL('cpanel:multisites/');

		if (!defined('ELXIS_MULTISITE')) { $elxis->redirect($redirurl); }

		if (ELXIS_MULTISITE != 1) {
			$msg = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$elxis->redirect($url, $msg, true);
		}

		if (!file_exists(ELXIS_PATH.'/config1.php')) {
			$elxis->redirect($redirurl, 'Configuration file 1 does not exist!', true);
		}

		$contents = @file_get_contents(ELXIS_PATH.'/config1.php'); 
		if ($contents === false) {
			$elxis->redirect($redirurl, 'Could not read configuration file 1', true);
		}

		$ok = $eFiles->createFile('configuration.php', $contents);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not create configuration file', true);
		}

		$eFiles->deleteFile('config1.php');
		for ($i=2; $i<21; $i++) {
			if (file_exists(ELXIS_PATH.'/config'.$i.'.php')) {
				$eFiles->deleteFile('config'.$i.'.php');
			}
		}

		$elxis->redirect($redirurl);
	}


	/***********************************/
	/* PREPARE TO ADD / EDIT MULTISITE */
	/***********************************/
	public function editmultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACCPAGE')."</div>\n";
			return;
		}

		if (!defined('ELXIS_MULTISITE')) {
			echo '<div class="elx_error">'.$eLang->get('MULTISITES_DISABLED')."</div>\n";
			return;
		}

		if (ELXIS_MULTISITE != 1) {
			$msg = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '<strong>1</strong>');
			echo '<div class="elx_error">'.$msg."</div>\n";
			return;
		}

		$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
		$curl = $elxis->getConfig('URL');
		$cpath = '';
		$p1 = parse_url($curl);
		if (isset($p1['path'])) {
			$fullpath = trim($p1['path'], '/');
			$p2 = explode('/', $fullpath);
			$n = count($p2) - 1;
			$cpath = $p2[$n];
			unset($n, $p2);
		}
		unset($p1);

		$importers = array();
		if ($id == 0) {
			$files = eFactory::getFiles()->listFiles('includes/libraries/elxis/database/importers/', 'php$');
			if ($files) {
				foreach ($files as $file) {
					$n = strpos($file, '.importer.php');
					if ($n !== false) { $importers[] = substr($file, 0, $n); }
				}
			}

			$row = new stdClass;
			$row->id = 0;
			$row->name = '';
			$row->folder = '';
			$row->active = 0;
			$row->url = '';
		} else {
			include(ELXIS_PATH.'/configuration.php');
			foreach ($multisites as $mid => $multisite) {
				if ($mid == $id) {
					$row = new stdClass;
					$row->id = $id;
					$row->name = $multisite['name'];
					$row->folder = $multisite['folder'];
					$row->active = ($multisite['active'] == true) ? 1 : 0;
					if (ELXIS_MULTISITE == $mid) {
						$row->url = $curl;
					} else {
						$row->url = $this->calculateURL($row->folder, $cpath, $curl);
					}
					break;
				}
			}
		}

		if (!isset($row)) {
			echo '<div class="elx_error">Site not found!'."</div>\n";
			return;
		}

		$dbtypes = array(
			'4D' => '4D',
			'cubrid' => 'Cubrid',
			'dblib' => 'dbLib',
			'firebird' => 'Firebird',
			'freetds' => 'FreeTDS',
			'ibm' => 'IBM',
			'informix' => 'Informix',
			'mssql' => 'msSQL',
			'mysql' => 'MySQL',
			'oci' => 'OCI (Oracle)',
			'odbc' => 'ODBC',
			'odbc_db2' => 'ODBC db2',
			'odbc_access' => 'ODBC MS Access',
			'odbc_mssql' => 'ODBC msSQL',
			'pgsql' => 'PostgreSQL',
			'sqlite' => 'SQLite 3',
			'sqlite2' => 'SQLite 2',
			'sybase' => 'SyBase'
		);

		$newid = ($id == 0) ? $this->makeSiteId() : 0;

		$this->view->editMultisite($row, $dbtypes, $newid, $importers);
	}


	/******************/
	/* SAVE MULTISITE */
	/******************/
	public function savemultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACCPAGE')."</div>\n";
			return;
		}

		if (!defined('ELXIS_MULTISITE')) {
			echo '<div class="elx_error">'.$eLang->get('MULTISITES_DISABLED')."</div>\n";
			return;
		}

		if (ELXIS_MULTISITE != 1) {
			$msg = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '<strong>1</strong>');
			echo '<div class="elx_error">'.$msg."</div>\n";
			return;
		}

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$name = eUTF::trim(preg_replace($pat, '', $name));
		if ($name == '') {
			$msg = sprintf($eLang->get('FIELDNOEMPTY'), '<strong>'.$eLang->get('NAME').'</strong>');
			echo '<div class="elx_error">'.$msg."</div>\n";
			return;
		}

		if ($id == 1) {
			$folder = '';
		} else {
			$folder = trim(preg_replace('/[^a-z0-9]/', '', $_POST['folder']));
			if ($folder != $_POST['folder']) {
				$msg = sprintf($eLang->get('FIELDNOACCCHAR'), '<strong>'.$eLang->get('URL_ID').'</strong>');
				echo '<div class="elx_error">'.$msg."</div>\n";
				return;
			}
			if ($folder == '') {
				$msg = sprintf($eLang->get('FIELDNOEMPTY'), '<strong>'.$eLang->get('URL_ID').'</strong>');
				echo '<div class="elx_error">'.$msg."</div>\n";
				return;
			}
			if (is_dir(ELXIS_PATH.'/'.$folder.'/')) {
				echo '<div class="elx_error">There is a folder with the same name as the URL identifier! Please choose an other URL identifier.'."</div>\n";
				return;
			}
			if (is_dir(ELXIS_PATH.'/components/com_'.$folder.'/')) {
				echo '<div class="elx_error">There is a component with the same name as the URL identifier! Please choose an other URL identifier.'."</div>\n";
				return;
			}
			if (strlen($folder) < 3) {
				echo '<div class="elx_error">URL identifier is too short! Please choose an other URL identifier.'."</div>\n";
				return;
			}
		}

		$active = (intval($_POST['active']) == 1) ? true : false;
		if ($id == ELXIS_MULTISITE) { $active = true; }

		$newmultisites = array();
		$unique = true;
		include(ELXIS_PATH.'/configuration.php');
		foreach ($multisites as $mid => $multisite) {
			if ($mid == $id) {
				$newmultisites[$mid] = array('folder' => $folder, 'name' => $name, 'active' => $active);
			} else {
				if ($multisite['folder'] == $folder) { $unique = false; }
				$newmultisites[$mid] = $multisite;
			}
		}

		if (!$unique) {
			echo '<div class="elx_error">URL identifier is not unique! Please choose an other URL identifier.'."</div>\n";
			return;
		}

		$import_msg = '';
		if ($id == 0) {
			$newid = $this->makeSiteId();
			$newmultisites[$newid] = array('folder' => $folder, 'name' => $name, 'active' => $active);

			$dbdata = array();
			$strings = array('db_type', 'db_host', 'db_name', 'db_prefix', 'db_user', 'db_pass', 'db_dsn', 'db_scheme');
			$gpc = get_magic_quotes_gpc();
			foreach ($strings as $str) {
				$up = strtoupper($str);
				$dbdata[$up] = trim(filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
				if ($gpc == 0) { $dbdata[$up] = addslashes($dbdata[$up]); }
			}
			$dbdata['DB_PORT'] = (int)$_POST['db_port'];

			$db_import = (int)$_POST['db_import'];

			$siteconfig = $this->makeConfig($folder, $name, $dbdata);
			$configfile = 'config'.$newid.'.php';
			$ok = $eFiles->createFile($configfile, $siteconfig);
			if (!$ok) {
				$msg = sprintf($eLang->get('CNOT_CREATE_CFG_NEW'), $siteconfig);
				echo '<div class="elx_error">'.$msg."</div>\n";
				return;
			}

			if ($db_import > 0) {
				$result = $this->importData($dbdata, $db_import);
				if ($result['success'] == false) {
					$import_msg = '<br />'.$eLang->get('DATA_IMPORT_FAILED').'<br />'.$result['message'];
				} else {
					$import_msg = '<br />'.$eLang->get('DATA_IMPORT_SUC');
				}
			}
		}

		$multiconfig = $this->makeMultiConfig($newmultisites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if ($ok) {
			echo '<div class="elx_success">'.$eLang->get('SETS_SAVED_SUCC').$import_msg."<br />\n";
			echo '<strong>'.$eLang->get('CREATE_REPOSITORY_NOTE')."</strong>\n";
			if (($id == 0) && ($folder != '')) {
				echo '<br />'.$eLang->get('ADD_RULES_HTACCESS').'<br />';
				echo '<span style="color:#FF00FF;">RewriteRule ^'.$folder.'/'.ELXIS_ADIR.'/inner.php '.ELXIS_ADIR.'/inner.php [L]<br />';
				echo 'RewriteRule ^'.$folder.'/inner.php(.*) inner.php [L]<br />';
				echo 'RewriteRule ^'.$folder.'/(.*) $1</span>';
			}
			echo "</div>\n";
		} else {
			echo '<div class="elx_error">Could not save settings!'.$import_msg."</div>\n";
		}
	}


	/******************************************/
	/* BACKUP AND IMPORT DATA TO THE NEW SITE */
	/******************************************/
	private function importData($upperdbdata, $db_import=0) {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$dbdata = array();
		foreach ($upperdbdata as $key => $val) {
			$lowkey = strtolower($key);
			$dbdata[$lowkey] = $val;
		}

		$result = array('success' => false, 'message' => 'Import failed!');

		if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/database/importers/'.$dbdata['db_type'].'.importer.php')) {
			$result['message'] = $eLang->get('NOT_SUP_DBTYPE');
			return $result;
		}

		if ($dbdata['db_type'] != $elxis->getConfig('DB_TYPE')) {
			$result['message'] = $eLang->get('DBTYPES_MUST_SAME');
			return $result;
		}

		$repo_path = $elxis->getConfig('REPO_PATH');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		$prefix = $elxis->getConfig('DB_PREFIX');
		$noinsert = array();
		if ($db_import < 2) {
			$noinsert[] = $prefix.'categories';
			$noinsert[] = $prefix.'comments';
			$noinsert[] = $prefix.'content';
			$noinsert[] = $prefix.'menu';
			$noinsert[] = $prefix.'translations';
		}
		$noinsert[] = $prefix.'session';
		$userparams = array('no_insert_tables' => $noinsert);
		unset($prefix, $noinsert);

	 	$sql = eFactory::getDB()->backup($userparams);
	 	unset($userparams);

	 	if ($sql === 0) {
	 		return $result;
		} else if ($sql === -1) {
		 	$result['message'] = $eLang->get('NOT_SUP_DBTYPE');
	 		return $result;
	 	} else if ($sql === -2) {
		 	$result['message'] = 'Invalid or insufficient backup parameters!';
	 		return $result;
 		} else if ($sql === -3) {
		 	$result['message'] = $elxis->getConfig('DB_TYPE').' database adapter faced an unrecoverable error!';
 			return $result;
 		}

		$rnd = rand(1000, 9999);
		$sqlfile = $repo_path.'/backup/elxis'.$rnd.'.sql';

		$ok = $eFiles->createFile('backup/elxis'.$rnd.'.sql', $sql, true);
		if (!$ok) {
		 	$result['message'] = 'Could not create backup of this site!';
			return $result;
		}
		unset($sql, $ok);

		$classname = 'elxis'.ucfirst($dbdata['db_type']).'Importer';
		elxisLoader::loadFile('includes/libraries/elxis/database/importers/'.$dbdata['db_type'].'.importer.php');

		$dbdata['db_prefix_old'] = $elxis->getConfig('DB_PREFIX');
		$dbdata['file'] = $sqlfile;

		$importer = new $classname($dbdata);
		if (!$importer->import()) {
			$result['message'] = $importer->getError();
			$importer->disconnect();
			$eFiles->deleteFile('backup/elxis'.$rnd.'.sql', true);
			return $result;
		}

		$eFiles->deleteFile('backup/elxis'.$rnd.'.sql', true);

		$sql = 'DELETE FROM '.$dbdata['db_prefix'].'users WHERE gid <> 1';
		$importer->query($sql);

		$importer->disconnect();
		$result['success'] = true;
		$result['message'] = $eLang->get('DATA_IMPORT_SUC');

		return $result;
		
	}


	/*****************************/
	/* MAKE AN ID FOR A NEW SITE */
	/*****************************/
	private function makeSiteId() {
		for ($i=2; $i < 101; $i++) {
			if (!file_exists(ELXIS_PATH.'/config'.$i.'.php')) { return $i; }
		}
		return rand(101, 1000);
	}


	/********************************************/
	/* CREATE CONFIGURATION FILE FOR A NEW SITE */
	/********************************************/
	private function makeConfig($folder, $name, $dbdata) {
		$elxis = eFactory::getElxis();

		$out = '<?php '._LEND;
		$out .= '/**'._LEND;
		$out .= 'Elxis CMS - Copyright 2006-'.date('Y').' elxis.org. All rights reserved.'._LEND;
		$out .= 'Last saved on '.gmdate('Y-m-d H:i:s').' (UTC) by '.$elxis->user()->uname._LEND;
		$out .= '******************************************/'._LEND._LEND;
		$out .= 'defined(\'_ELXIS_\') or die (\'Direct access to this location is not allowed\');'._LEND._LEND._LEND;
		$out .= 'class elxisConfig {'._LEND._LEND;
		$out .= "\t".'private $URL = \''.$elxis->getConfig('URL').'/'.$folder.'\';'._LEND;
		$out .= "\t".'private $MAIL_EMAIL = \''.$elxis->getConfig('MAIL_EMAIL').'\';'._LEND;
		$out .= "\t".'private $MAIL_FROM_EMAIL = \''.$elxis->getConfig('MAIL_FROM_EMAIL').'\';'._LEND;
		$out .= "\t".'private $MAIL_MANAGER_EMAIL = \''.$elxis->getConfig('MAIL_MANAGER_EMAIL').'\';'._LEND;
		$out .= "\t".'private $REPO_PATH = \''.$elxis->getConfig('REPO_PATH').'\';'._LEND;
		$out .= "\t".'private $DEFAULT_ROUTE = \''.$elxis->getConfig('DEFAULT_ROUTE').'\';'._LEND;
		$out .= "\t".'private $TEMPLATE = \''.$elxis->getConfig('TEMPLATE').'\';'._LEND;
		$out .= "\t".'private $ATEMPLATE = \''.$elxis->getConfig('ATEMPLATE').'\';'._LEND;
		$out .= "\t".'private $ICONS_PACK = \''.$elxis->getConfig('ICONS_PACK').'\';'._LEND;
		$out .= "\t".'private $LANG = \''.$elxis->getConfig('LANG').'\';'._LEND;
		$out .= "\t".'private $TIMEZONE = \''.$elxis->getConfig('TIMEZONE').'\';'._LEND;
		$out .= "\t".'private $REGISTRATION_EMAIL_DOMAIN = \''.$elxis->getConfig('REGISTRATION_EMAIL_DOMAIN').'\';'._LEND;
		$out .= "\t".'private $REGISTRATION_EXCLUDE_EMAIL_DOMAINS = \''.$elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS').'\';'._LEND;
		$out .= "\t".'private $MAIL_METHOD = \''.$elxis->getConfig('MAIL_METHOD').'\';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_HOST = \''.$elxis->getConfig('MAIL_SMTP_HOST').'\';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_USER = \''.$elxis->getConfig('MAIL_SMTP_USER').'\';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_PASS = \''.$elxis->getConfig('MAIL_SMTP_PASS').'\';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_SECURE = \''.$elxis->getConfig('MAIL_SMTP_SECURE').'\';'._LEND;
		$out .= "\t".'private $DB_TYPE = \''.$dbdata['DB_TYPE'].'\';'._LEND;
		$out .= "\t".'private $DB_HOST = \''.$dbdata['DB_HOST'].'\';'._LEND;
		$out .= "\t".'private $DB_NAME = \''.$dbdata['DB_NAME'].'\';'._LEND;
		$out .= "\t".'private $DB_PREFIX = \''.$dbdata['DB_PREFIX'].'\';'._LEND;
		$out .= "\t".'private $DB_USER = \''.$dbdata['DB_USER'].'\';'._LEND;
		$out .= "\t".'private $DB_PASS = \''.$dbdata['DB_PASS'].'\';'._LEND;
		$out .= "\t".'private $DB_DSN = \''.$dbdata['DB_DSN'].'\';'._LEND;
		$out .= "\t".'private $DB_SCHEME = \''.$dbdata['DB_SCHEME'].'\';'._LEND;
		$out .= "\t".'private $DB_PORT = '.intval($dbdata['DB_PORT']).';'._LEND;
		$out .= "\t".'private $DB_PERSISTENT = '.$elxis->getConfig('DB_PERSISTENT').';'._LEND;
		$out .= "\t".'private $FTP_HOST = \''.$elxis->getConfig('FTP_HOST').'\';'._LEND;
		$out .= "\t".'private $FTP_USER = \''.$elxis->getConfig('FTP_USER').'\';'._LEND;
		$out .= "\t".'private $FTP_PASS = \''.$elxis->getConfig('FTP_PASS').'\';'._LEND;
		$out .= "\t".'private $FTP_ROOT = \''.$elxis->getConfig('FTP_ROOT').'\';'._LEND;
		$out .= "\t".'private $SESSION_HANDLER = \''.$elxis->getConfig('SESSION_HANDLER').'\';'._LEND;
		$out .= "\t".'private $ENCRYPT_METHOD = \''.$elxis->getConfig('ENCRYPT_METHOD').'\';'._LEND;
		$out .= "\t".'private $ENCRYPT_KEY = \''.$elxis->getConfig('ENCRYPT_KEY').'\';'._LEND;
		$out .= "\t".'private $SITENAME = \''.$name.'\';'._LEND;
		$out .= "\t".'private $OFFLINE_MESSAGE = \''.$elxis->getConfig('OFFLINE_MESSAGE').'\';'._LEND;
		$out .= "\t".'private $METADESC = \''.$elxis->getConfig('METADESC').'\';'._LEND;
		$out .= "\t".'private $METAKEYS = \''.$elxis->getConfig('METAKEYS').'\';'._LEND;
		$out .= "\t".'private $MAIL_NAME = \''.$elxis->getConfig('MAIL_NAME').'\';'._LEND;
		$out .= "\t".'private $MAIL_FROM_NAME = \''.$elxis->getConfig('MAIL_FROM_NAME').'\';'._LEND;
		$out .= "\t".'private $MAIL_MANAGER_NAME = \''.$elxis->getConfig('MAIL_MANAGER_NAME').'\';'._LEND;
		$out .= "\t".'private $ONLINE = 0;'._LEND;
		$out .= "\t".'private $SEF = '.$elxis->getConfig('SEF').';'._LEND;
		$out .= "\t".'private $STATISTICS = '.$elxis->getConfig('STATISTICS').';'._LEND;
		$out .= "\t".'private $GZIP = '.$elxis->getConfig('GZIP').';'._LEND;
		$out .= "\t".'private $MULTILINGUISM = 0;'._LEND;
		$out .= "\t".'private $CACHE = '.$elxis->getConfig('CACHE').';'._LEND;
		$out .= "\t".'private $CACHE_TIME = '.$elxis->getConfig('CACHE_TIME').';'._LEND;
		$out .= "\t".'private $APC = '.$elxis->getConfig('APC').';'._LEND;
		$out .= "\t".'private $APCID = '.rand(1000,9999).';'._LEND;
		$out .= "\t".'private $REALNAME = '.$elxis->getConfig('REALNAME').';'._LEND;
		$out .= "\t".'private $REGISTRATION = '.$elxis->getConfig('REGISTRATION').';'._LEND;
		$out .= "\t".'private $REGISTRATION_ACTIVATION = '.$elxis->getConfig('REGISTRATION_ACTIVATION').';'._LEND;
		$out .= "\t".'private $PASS_RECOVER = '.$elxis->getConfig('PASS_RECOVER').';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_PORT = '.$elxis->getConfig('MAIL_SMTP_PORT').';'._LEND;
		$out .= "\t".'private $MAIL_SMTP_AUTH = '.$elxis->getConfig('MAIL_SMTP_AUTH').';'._LEND;
		$out .= "\t".'private $FTP = '.$elxis->getConfig('FTP').';'._LEND;
		$out .= "\t".'private $FTP_PORT = '.$elxis->getConfig('FTP_PORT').';'._LEND;
		$out .= "\t".'private $SESSION_LIFETIME = '.$elxis->getConfig('SESSION_LIFETIME').';'._LEND;
		$out .= "\t".'private $SESSION_MATCHIP = '.$elxis->getConfig('SESSION_MATCHIP').';'._LEND;
		$out .= "\t".'private $SESSION_MATCHBROWSER = '.$elxis->getConfig('SESSION_MATCHBROWSER').';'._LEND;
		$out .= "\t".'private $SESSION_MATCHREFERER = '.$elxis->getConfig('SESSION_MATCHREFERER').';'._LEND;
		$out .= "\t".'private $SESSION_ENCRYPT = '.$elxis->getConfig('SESSION_ENCRYPT').';'._LEND;
		$out .= "\t".'private $SECURITY_LEVEL = '.$elxis->getConfig('SECURITY_LEVEL').';'._LEND;
		$out .= "\t".'private $SSL = '.$elxis->getConfig('SSL').';'._LEND;
		$out .= "\t".'private $ERROR_REPORT = '.$elxis->getConfig('ERROR_REPORT').';'._LEND;
		$out .= "\t".'private $ERROR_LOG = '.$elxis->getConfig('ERROR_LOG').';'._LEND;
		$out .= "\t".'private $ERROR_ALERT = '.$elxis->getConfig('ERROR_ALERT').';'._LEND;
		$out .= "\t".'private $LOG_ROTATE = '.$elxis->getConfig('LOG_ROTATE').';'._LEND;
		$out .= "\t".'private $DEBUG = '.$elxis->getConfig('DEBUG').';'._LEND;
		$out .= "\t".'private $DEFENDER = \''.$elxis->getConfig('DEFENDER').'\';'._LEND;
		$out .= "\t".'private $DOCTYPE = \''.$elxis->getConfig('DOCTYPE').'\';'._LEND;
		$out .= "\t".'private $MINICSS = '.$elxis->getConfig('MINICSS').';'._LEND;
		$out .= "\t".'private $MINIJS = '.$elxis->getConfig('MINIJS').';'._LEND;
		$out .= "\t".'private $MOBILE = '.$elxis->getConfig('MOBILE').';'._LEND;
		$out .= _LEND;
		$out .= "\t".'public function __construct() {'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function get($var=\'\') {'._LEND;
		$out .= "\t\t".'if (($var != \'\') && isset($this->$var)) { return $this->$var; }'._LEND;
		$out .= "\t\t".'return \'\';'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= "\t".'public function set($var, $value) {'._LEND;
		$out .= "\t\t".'if (($var == \'\') || (!is_string($var))) { return false; }'._LEND;
		$out .= "\t\t".'if (isset($this->$var)) {'._LEND;
		$out .= "\t\t\t".'if (!in_array($var, array(\'SITENAME\', \'METADESC\', \'METAKEYS\'))) { return false; }'._LEND;
		$out .= "\t\t".'}'._LEND;
		$out .= "\t\t".'$this->$var = $value;'._LEND;
		$out .= "\t\t".'return true;'._LEND;
		$out .= "\t".'}'._LEND._LEND;
		$out .= '}'._LEND._LEND;
		$out .= '?>';

		return $out;
	}


	/***********************/
	/* DELETE A MULTI-SITE */
	/***********************/
	public function deletemultisite() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$redirurl = $elxis->makeAURL('cpanel:multisites/');
		if (!defined('ELXIS_MULTISITE')) {
			$elxis->redirect($redirurl, $eLang->get('MULTISITES_DISABLED'), true);
		}

		if (ELXIS_MULTISITE != 1) {
			$msg = sprintf($eLang->get('MAN_MULTISITES_ONLY'), '1');
			$elxis->redirect($redirurl, $msg, true);
		}

		$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
		if ($id < 2) {
			$elxis->redirect($redirurl);
		}

		$newmultisites = array();
		$found = false;
		include(ELXIS_PATH.'/configuration.php');
		foreach ($multisites as $mid => $multisite) {
			if ($mid == $id) { $found = true; continue; }
			$newmultisites[$mid] = $multisite;
		}

		if (!$found) {
			$elxis->redirect($redirurl, 'Site not found!', true);
		}

		$multiconfig = $this->makeMultiConfig($newmultisites);
		$ok = $eFiles->createFile('configuration.php', $multiconfig);
		if (!$ok) {
			$elxis->redirect($redirurl, 'Could not save settings!', true);
		}
		$eFiles->deleteFile('config'.$id.'.php');

		$elxis->redirect($redirurl);
	}

}

?>