<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: en-GB (English - Great Britain) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ioannis Sannos ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Installation';
$_lang['STEP'] = 'Step';
$_lang['VERSION'] = 'Version';
$_lang['VERSION_CHECK'] = 'Version check';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Revision number';
$_lang['RELEASE_DATE'] = 'Release date';
$_lang['ELXIS_INSTALL'] = 'Elxis installation';
$_lang['LICENSE'] = 'License';
$_lang['VERSION_PROLOGUE'] = 'You are about to install Elxis CMS. The exact version of the Elxis copy 
	you are about to install is shown below. Please make sure this is the latest Elxis version released 
	on <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Before you begin';
$_lang['BEFORE_DESC'] = 'Before you proceed further please read carefully the following.';
$_lang['DATABASE'] = 'Database';
$_lang['DATABASE_DESC'] = 'Create an empty database which will be used by Elxis to store your data in. We 
	strongly recommend to use a <strong>MySQL</strong> database. Although Elxis has backend support for 
	other database types such as PostgreSQL and SQLite 3 it is well tested only with MySQL. To create an 
	empty MySQL database do so from your hosting control panel (CPanel, Plesk, ISP Config, etc) or from 
	phpMyAdmin or other database management tools. Just provide a <strong>name</strong> for your database and create it. 
	After that, create a database <strong>user</strong> and assign him to your newly created database. Make a note of 
	the database name, the username and the password; you will need them later during install.';
$_lang['REPOSITORY'] = 'Repository';
$_lang['REPOSITORY_DESC'] = 'Elxis uses a special folder to store cached pages, log files, sessions, backups and more. By 
	default this folder is named <strong>repository</strong> and it is placed inside the Elxis root folder. This folder 
	<strong>must be writeable</strong>! We strongly reccomend to <strong>rename</strong> this folder and <strong>move</strong> it 
	in a place not reachable from the web. After this move if you have enabled <strong>open basedir</strong> protection in PHP 
	you might also need to include the repository path into the allowed paths.';
$_lang['REPOSITORY_DEFAULT'] = 'Repository is in its default location!';
$_lang['SAMPLE_ELXPATH'] = 'Sample Elxis path';
$_lang['DEF_REPOPATH'] = 'Default repository path';
$_lang['REQ_REPOPATH'] = 'Recommended repository path';
$_lang['CONTINUE'] = 'Continue';
$_lang['I_AGREE_TERMS'] = 'I have read, understood and agree to EPL terms and conditions';
$_lang['LICENSE_NOTES'] = 'Elxis CMS is a free software released under the <strong>Elxis Public License</strong> (EPL). 
	To continue this installation and use Elxis you must agree to the terms and conditions of EPL. Read carefully 
	the Elxis license and if you agree check the checkbox at the bottom of the page and click Continue. If not, 
	stop this installation and delete Elxis files.';
$_lang['SETTINGS'] = 'Settings';
$_lang['SITE_URL'] = 'Site URL';
$_lang['SITE_URL_DESC'] = 'Without trailing slash (eg. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'The absolute path to Elxis repository folder. Leave it empty for the default path and name.';
$_lang['SETTINGS_DESC'] = 'Set required Elxis configuration parameters. Some parameters are required to be set before Elxis 
	installaton. After the installation is complete log in to the administration console and configure remaining parameters. 
	This should be your very first administrator task.';
$_lang['DEF_LANG'] = 'Default language';
$_lang['DEFLANG_DESC'] = 'The content is written in the default language. Content in other languages is translations of the 
	original content in the default language.';
$_lang['ENCRYPT_METHOD'] = 'Encryption method';
$_lang['ENCRYPT_KEY'] = 'Encryption key';
$_lang['AUTOMATIC'] = 'Automatic';
$_lang['GEN_OTHER'] = 'Generate another';
$_lang['SITENAME'] = 'Site name';
$_lang['TYPE'] = 'Type';
$_lang['DBTYPE_DESC'] = 'We strongly reccomend MySQL. Selectable are only the supported drivers by your system and Elxis installer.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Tables prefix';
$_lang['DSN_DESC'] = 'You can instead provide a ready-to-use Data Source Name for connecting to the database.';
$_lang['SCHEME'] = 'Scheme';
$_lang['SCHEME_DESC'] = 'The absolute path to a database file if you use a database such as SQLite.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'The default port for MySQL is 3306. Leave it as 0 for auto selection.';
$_lang['FTPPORT_DESC'] = 'The default port for FTP is 21. Leave it as 0 for auto selection.';
$_lang['USE_FTP'] = 'Use FTP';
$_lang['PATH'] = 'Path';
$_lang['FTP_PATH_INFO'] = 'The relative path from the FTP root folder to Elxis installation folder (example: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Check FTP settings';
$_lang['CHECK_DB_SETS'] = 'Check database settings';
$_lang['DATA_IMPORT'] = 'Data import';
$_lang['SETTINGS_ERRORS'] = 'The settings you gave contain errors!';
$_lang['NO_QUERIES_WARN'] = 'Initial data imported into database but looks like no queries were executed. Make 
	sure data was indeed imported before you proceed further.';
$_lang['RETRY_PREV_STEP'] = 'Retry previous step';
$_lang['INIT_DATA_IMPORTED'] = 'Initial data imported into database.';
$_lang['QUERIES_EXEC'] = "%s SQL queries executed."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Administrator account';
$_lang['CONFIRM_PASS'] = 'Confirm password';
$_lang['AVOID_COMUNAMES'] = 'Avoid common usernames such as admin and administrator.';
$_lang['YOUR_DETAILS'] = 'Your details';
$_lang['PASS_NOMATCH'] = 'Passwords do not match!';
$_lang['REPOPATH_NOEX'] = 'Repository path does not exist!';
$_lang['FINISH'] = 'Finish';
$_lang['FRIENDLY_URLS'] = 'Friendly URLs';
$_lang['FRIENDLY_URLS_DESC'] = 'We strongly recommend to enable it. In order to work, Elxis will try to rename file htaccess.txt into 
	<strong>.htaccess</strong> . If there is already another .htaccess file on the same folder it will be deleted.';
$_lang['GENERAL'] = 'General';
$_lang['ELXIS_INST_SUCC'] = 'Elxis installation completed with success.';
$_lang['ELXIS_INST_WARN'] = 'Elxis installation completed with warnings.';
$_lang['CNOT_CREA_CONFIG'] = 'Could not create <strong>configuration.php</strong> file in Elxis root folder.';
$_lang['CNOT_REN_HTACC'] = 'Could not rename <strong>htaccess.txt</strong> file to <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Configuration file';
$_lang['CONFIG_FILE_MANUAL'] = 'Create manually configuration.php file, copy the following code and paste it inside it.';
$_lang['REN_HTACCESS_MANUAL'] = 'Please rename manually <strong>htaccess.txt</strong> file to <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'What to do next?';
$_lang['RENAME_ADMIN_FOLDER'] = 'To enhance security you can rename the administration folder (<em>estia</em>) to anything you wish. 
	If you do so, you must also update the .htaccess file with the new name.';
$_lang['LOGIN_CONFIG'] = 'Login in administration section and set properly the rest configuration options.';
$_lang['VISIT_NEW_SITE'] = 'Visit your new web site';
$_lang['VISIT_ELXIS_SUP'] = 'Visit Elxis support site';
$_lang['THANKS_USING_ELXIS'] = 'Thanks for using Elxis CMS.';

?>