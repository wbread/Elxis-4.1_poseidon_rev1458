<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: Elxis is an open source content management system provided for free.
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
*****************************************************************************/

class elxisConfig {

	private $ONLINE = 1;
	private $OFFLINE_MESSAGE = '';
	private $SITENAME = 'Elxis Nautilus';
	private $METADESC = 'This site is powered by Elxis CMS!';
	private $METAKEYS = 'Elxis, Nautilus, open source, cms, elxis.org, xhtml5, php cms, free';
	private $REGISTRATION = 1;
	private $REGISTRATION_EMAIL_DOMAIN = '';
	private $REGISTRATION_EXCLUDE_EMAIL_DOMAINS = '';
	private $REGISTRATION_ACTIVATION = 1;
	private $PASS_RECOVER = 1;
	private $REALNAME = 1;
	private $STATISTICS = 1;
	private $SEF = 0;
	private $GZIP = 0;
	private $TEMPLATE = 'delta';
	private $ATEMPLATE = 'iris';
	private $URL = 'http://localhost';
	private $REPO_PATH = '';
	private $ICONS_PACK = 'nautilus';
	private $DEFAULT_ROUTE = 'content:/';  
	private $DB_TYPE = 'mysql';
	private $DB_HOST = 'localhost';
	private $DB_PORT = 0;
	private $DB_PERSISTENT = 0;
	private $DB_USER = 'root'; 
	private $DB_PASS = '';
	private $DB_NAME = 'elxis4';
	private $DB_PREFIX = 'elx_';
	private $DB_DSN = '';
	private $DB_SCHEME = '';
	private $FTP = 0; 
	private $FTP_HOST = 'localhost';
	private $FTP_PORT = 21;
	private $FTP_USER = '';
	private $FTP_PASS = '';
	private $FTP_ROOT = '';
	private $ERROR_REPORT = 0;
	private $ERROR_LOG = 1;
	private $ERROR_ALERT = 0;
	private $LOG_ROTATE = 1;
	private $DEBUG = 0;
	private $MAIL_METHOD = 'mail';
	private $MAIL_NAME = 'Nicolas Surname';
	private $MAIL_EMAIL = 'info@example.com';
	private $MAIL_MANAGER_NAME = 'John Tech';
	private $MAIL_MANAGER_EMAIL = 'tech@example.com';
	private $MAIL_FROM_NAME = 'Elxis Nautilus';
	private $MAIL_FROM_EMAIL = 'info@example.com';
	private $MAIL_SMTP_HOST = 'mail.example.com';
	private $MAIL_SMTP_PORT = 25;
	private $MAIL_SMTP_AUTH = 1;
	private $MAIL_SMTP_SECURE = '';
	private $MAIL_SMTP_USER = '';
	private $MAIL_SMTP_PASS = '';
	private $ENCRYPT_METHOD = 'auto';
	private $ENCRYPT_KEY = 'JqEls8xxLxNOf7nH';
	private $SSL = 0;
	private $DEFENDER = 'GC';
	private $SECURITY_LEVEL = 0;
	private $CACHE = 0;
	private $CACHE_TIME = 1800;
	private $APC = 0;
	private $APCID = 7841;
	private $LANG = 'en';
	private $SITELANGS = '';
	private $TIMEZONE = 'Europe/Athens';
	private $MULTILINGUISM = 1;
	private $SESSION_HANDLER = 'database';
	private $SESSION_LIFETIME = 900;
	private $SESSION_MATCHIP = 0;
	private $SESSION_MATCHBROWSER = 1;
	private $SESSION_MATCHREFERER = 0;
	private $SESSION_ENCRYPT = 0;
	private $DOCTYPE = 'html5';
	private $MINICSS = 0;
	private $MINIJS = 0;
	private $MOBILE = 1;


	public function __construct() {
	}


	public function get($var='') {
		if (($var != '') && isset($this->$var)) { return $this->$var; }
		return '';
	}


	public function set($var, $value) {
		if (($var == '') || (!is_string($var))) { return false; }
		if (isset($this->$var)) {
			if (!in_array($var, array('SITENAME', 'METADESC', 'METAKEYS'))) { return false; }
		}
		$this->$var = $value;
		return true;
	}

}

?>