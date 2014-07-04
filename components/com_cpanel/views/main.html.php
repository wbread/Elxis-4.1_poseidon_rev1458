<?php 
/**
* @version		$Id: main.html.php 1431 2013-05-04 12:09:24Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mainCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************************/
	/* CONTROL PANEL DASHBOARD HTML */
	/********************************/
	public function dashboardHTML($boarditems) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$configlink = $elxis->makeAURL('cpanel:config.html');	
?>
		<h2><?php echo $eLang->get('CONTROL_PANEL'); ?></h2>
		<div class="elx_panel-wrapper">
			<div class="elx_rpanel">
				<?php $eDoc->modules('cpanel', 'none'); ?>
			</div>
			<div class="elx_lpanel">
				<div class="elx_dashboard">
<?php 
				if ($boarditems) {
					foreach ($boarditems as $item) {
						echo '<div class="elx_boarditem">'."\n";
						echo '<a href="'.$item->link.'" class="elx_tooltip" title="'.$item->title.'|'.$item->description.'">';
						echo '<img src="'.$item->icon.'" alt="'.$item->title.'" border="0" /> '.$item->title."</a>\n";
						echo "</div>\n";
					}
				}
?>
					<div style="clear:both;"></div>
				</div>
				<div style="margin:40px 0 30px 0; padding:0;">
					<?php $eDoc->modules('cpanelbottom', 'none'); ?>
				</div>
			</div>
		</div>

<?php 
	}


	/*******************************/
	/* ELXIS GENERAL SETTINGS HTML */
	/*******************************/
	public function configHTML($data) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmconfig',
			'action' => $elxis->makeAURL('cpanel:saveconfig.html', 'inner.php'),
			'idprefix' => 'cfg',
			'label_width' => 240,
			'label_align' => 'left',
			'tip_style' => 1,
			'jsonsubmit' => 'document.fmconfig.submit()'
		);

		$form = new elxisForm($formOptions);

		$form->openTab($eLang->get('GENERAL'));
		$trdata = array('category' => 'config', 'element' => 'sitename', 'elid' => 1);
		$form->addMLText('sitename', $trdata, $elxis->getConfig('SITENAME'), $eLang->get('SITENAME'), array('required' => 1, 'dir' => 'rtl', 'size' => 40));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('OFFLINE'));
		$options[] = $form->makeOption(1, $eLang->get('ONLINE'));
		$options[] = $form->makeOption(2, $eLang->get('ONLINE_ADMINS'));
		$form->addSelect('online', $eLang->get('WEBSITE_STATUS'), $elxis->getConfig('ONLINE'), $options, array('dir' => 'rtl'));
		$form->addText('offline_message', stripslashes($elxis->getConfig('OFFLINE_MESSAGE')), $eLang->get('OFFLINE_MSG'), array('dir' => 'rtl', 'size' => 60, 'tip' => $eLang->get('OFFLINE_MSG').'|'.$eLang->get('OFFLINE_MSG_INFO')));
		$form->addText('url', $elxis->getConfig('URL'), $eLang->get('URL_ADDRESS'), array('required' => 1, 'dir' => 'ltr', 'size' => 40));
		$form->addText('repo_path', $elxis->getConfig('REPO_PATH'), $eLang->get('REPO_PATH'), array('dir' => 'ltr', 'size' => 60, 'tip' => $eLang->get('REPO_PATH').'|'.$eLang->get('REPO_PATH_INFO')));
		$options = array();
		$options[] = $form->makeOption('xhtml_strict', 'XHTML 1.0 strict');
		$options[] = $form->makeOption('xhtml_trans', 'XHTML 1.0 transitional');
		$options[] = $form->makeOption('xhtml5', 'XHTML 5');
		$options[] = $form->makeOption('html5', 'HTML 5');
		$form->addSelect('doctype', $eLang->get('DOCTYPE'), $elxis->getConfig('DOCTYPE'), $options, array('dir' => 'ltr', 'tip' => 'DOCTYPE|'.$eLang->get('DOCTYPE_INFO')));
		$form->addYesNo('mobile', $eLang->get('MOBILE_VERSION'), $elxis->getConfig('MOBILE'), array('tip' => $eLang->get('MOBILE_VERSION_DESC')));
		$form->addYesNo('sef', $eLang->get('FRIENDLY_URLS'), $elxis->getConfig('SEF'), array('tip' => 'warning:SEF|'.$eLang->get('SEF_INFO')));
		$form->addYesNo('statistics', $eLang->get('STATISTICS'), $elxis->getConfig('STATISTICS'), array('tip' => 'info:'.$eLang->get('STATISTICS').'|'.$eLang->get('STATISTICS_INFO')));
		$form->addText('default_route', $elxis->getConfig('DEFAULT_ROUTE'), $eLang->get('DEFAULT_ROUTE'), array('required' => 1, 'dir' => 'ltr', 'size' => 40, 'tip' => $eLang->get('DEFAULT_ROUTE').'|'.$eLang->get('DEFAULT_ROUTE_INFO')));
		$form->openFieldset($eLang->get('META_DATA'));
		$trdata = array('category' => 'config', 'element' => 'metadesc', 'elid' => 1);
		$form->addMLText('metadesc', $trdata, stripslashes($elxis->getConfig('METADESC')), $eLang->get('DESCRIPTION'), array('required' => 1, 'dir' => 'rtl', 'size' => 60, 'tip' => $eLang->get('META_DATA_INFO')));
		$trdata = array('category' => 'config', 'element' => 'metakeys', 'elid' => 1);
		$form->addMLText('metakeys', $trdata, stripslashes($elxis->getConfig('METAKEYS')), $eLang->get('KEYWORDS'), array('required' => 1, 'dir' => 'rtl', 'size' => 60, 'tip' => $eLang->get('KEYWORDS_INFO')));
		$form->closeFieldset();
		$form->openFieldset($eLang->get('STYLE_LAYOUT'));
		$options = array();
		if ($data['templates']) {
			foreach ($data['templates'] as $tpl => $ttl) {
				$options[] = $form->makeOption($tpl, $ttl);
			}
		}
		$form->addSelect('template', $eLang->get('SITE_TEMPLATE'), $elxis->getConfig('TEMPLATE'), $options, array('dir' => 'ltr'));
		$options = array();
		if ($data['atemplates']) {
			foreach ($data['atemplates'] as $tpl => $ttl) {
				$options[] = $form->makeOption($tpl, $ttl);
			}
		}
		$form->addSelect('atemplate', $eLang->get('ADMIN_TEMPLATE'), $elxis->getConfig('ATEMPLATE'), $options, array('dir' => 'ltr'));
		$options = array();
		if ($data['icons']) {
			foreach ($data['icons'] as $val) {
				$options[] = $form->makeOption($val, ucfirst($val));
			}
		}
		$form->addSelect('icons_pack', $eLang->get('ICONS_PACK'), $elxis->getConfig('ICONS_PACK'), $options, array('dir' => 'ltr'));
		$form->closeFieldset();
		$form->openFieldset($eLang->get('LOCALE'));
		$form->addLanguage('lang', $eLang->get('LANGUAGE'), $elxis->getConfig('LANG'), array('tip' => 'warning:'.$eLang->get('CHANGE_LANG').'|'.$eLang->get('LANG_CHANGE_WARN')), 1, true);
		$form->addNote($eLang->get('SITE_LANGS_DESC'), 'elx_sminfo');
		$ilangs = $eLang->getAllLangs(true);
		$slangs = array();
		if ($elxis->getConfig('SITELANGS') != '') { $slangs = explode(',',$elxis->getConfig('SITELANGS')); }
		$options = array();
		foreach ($ilangs as $lng => $info) {
			$options[] = $form->makeOption($lng, $lng.' - '.$info['NAME']);
		}
		$form->addCheckbox('sitelangs', $eLang->get('SITE_LANGS'), $slangs, $options);
		$form->addYesNo('multilinguism', $eLang->get('MULTILINGUISM'), $elxis->getConfig('MULTILINGUISM'), array('tip' => $eLang->get('MULTILINGUISM').'|'.$eLang->get('MULTILINGUISM_INFO')));
		$current_daytime = eFactory::getDate()->worldDate('now', $elxis->getConfig('TIMEZONE'), $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $elxis->getConfig('TIMEZONE'), array('tip' => 'info:'.$current_daytime));
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab($eLang->get('PERFORMANCE'));
		$form->addYesNo('gzip', $eLang->get('GZIP_COMPRESSION'), $elxis->getConfig('GZIP'), array('tip' => 'gzip|'.$eLang->get('GZIP_COMPRESSION_DESC')));
		$form->openFieldset($eLang->get('CACHE'));
		$form->addYesNo('cache', $eLang->get('CACHE'), $elxis->getConfig('CACHE'), array('tip' => 'Cache|'.$eLang->get('CACHE_INFO')));
		$options = array();
		$options[] = $form->makeOption(600, '10 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(900, '15 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1200, '20 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1800, '30 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(2700, '45 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(3600, '60 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(7200, '2 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(10800, '3 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(21600, '6 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(43200, '12 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(86400, '24 '.$eLang->get('HOURS'));
		$options[] = $form->makeOption(172800, '48 '.$eLang->get('HOURS'));
		$form->addSelect('cache_time', $eLang->get('LIFETIME'), $elxis->getConfig('CACHE_TIME'), $options, array('tip' => $eLang->get('LIFETIME').'|'.$eLang->get('CACHE_TIME_INFO')));
		$form->addYesNo('apc', 'APC', $elxis->getConfig('APC'), array('tip' => 'APC|'.$eLang->get('APC_INFO')));
		$form->addNumber('apcid', $elxis->getConfig('APCID'), 'APC Id', array('required' => 1, 'dir' => 'ltr', 'size' => 5, 'maxlength' => 5, 'tip' => 'APC Id|'.$eLang->get('APC_ID_INFO')));
		$form->closeFieldset();
		$form->openFieldset($eLang->get('MINIFIER_CSSJS'));
		$form->addNote($eLang->get('MINIFIER_INFO'), 'elx_info');
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		$options[] = $form->makeOption(1, $eLang->get('YES'));
		$options[] = $form->makeOption(2, $eLang->get('YES').' + '.$eLang->get('GZIP_COMPRESSION'));
		$form->addSelect('minicss', 'CSS', $elxis->getConfig('MINICSS'), $options);
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NO'));
		$options[] = $form->makeOption(1, $eLang->get('YES'));
		$options[] = $form->makeOption(2, $eLang->get('YES').' + '.$eLang->get('GZIP_COMPRESSION'));
		$form->addSelect('minijs', 'Javascript', $elxis->getConfig('MINIJS'), $options);
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab($eLang->get('USERS_AND_REGISTRATION'));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('USERNAME'));
		$options[] = $form->makeOption(1, $eLang->get('FIRSTNAME').' '.$eLang->get('LASTNAME'));
		$form->addSelect('realname', $eLang->get('DISPUSERS_AS'), $elxis->getConfig('REALNAME'), $options);
		$form->addYesNo('registration', $eLang->get('USERS_REGISTRATION'), $elxis->getConfig('REGISTRATION'));
		$form->addText('registration_email_domain', $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN'), $eLang->get('ALLOWED_DOMAIN'), array('dir' => 'ltr', 'size' => 40, 'tip' => $eLang->get('ALLOWED_DOMAIN').'|'.$eLang->get('ALLOWED_DOMAIN_INFO')));
		$form->addText('registration_exclude_email_domains', $elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS'), $eLang->get('EXCLUDED_DOMAINS'), array('dir' => 'ltr', 'size' => 60, 'tip' => $eLang->get('EXCLUDED_DOMAINS').'|'.$eLang->get('EXCLUDED_DOMAINS_INFO')));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('DIRECT'));
		$options[] = $form->makeOption(1, $eLang->get('EMAIL'));
		$options[] = $form->makeOption(2, $eLang->get('MANUAL_BY_ADMIN'));
		$form->addSelect('registration_activation', $eLang->get('ACCOUNT_ACTIVATION'), $elxis->getConfig('REGISTRATION_ACTIVATION'), $options);
		$form->addYesNo('pass_recover', $eLang->get('PASS_RECOVERY'), $elxis->getConfig('PASS_RECOVER'));
		$form->closeTab();

		$form->openTab($eLang->get('EMAIL'));
		$options = array();
		$options[] = $form->makeOption('mail', 'PHP mail');
		$options[] = $form->makeOption('smtp', 'SMTP');
		$options[] = $form->makeOption('sendmail', 'Sendmail');
		$form->addSelect('mail_method', $eLang->get('SEND_METHOD'), $elxis->getConfig('MAIL_METHOD'), $options, array('dir' => 'ltr'));
		$form->addText('mail_name', $elxis->getConfig('MAIL_NAME'), $eLang->get('RCPT_NAME'), array('dir' => 'rtl', 'required' => 1, 'size' => 40));
		$form->addEmail('mail_email', $elxis->getConfig('MAIL_EMAIL'), $eLang->get('RCPT_EMAIL'), array('required' => 1, 'size' => 40));
		$form->addText('mail_from_name', $elxis->getConfig('MAIL_FROM_NAME'), $eLang->get('SENDER_NAME'), array('dir' => 'rtl', 'required' => 1, 'size' => 40));
		$form->addEmail('mail_from_email', $elxis->getConfig('MAIL_FROM_EMAIL'), $eLang->get('SENDER_EMAIL'), array('required' => 1, 'size' => 40));
		$form->openFieldset($eLang->get('TECHNICAL_MANAGER'));
		$form->addNote($eLang->get('TECHNICAL_MANAGER_INFO'), 'elx_sminfo');
		$form->addText('mail_manager_name', $elxis->getConfig('MAIL_MANAGER_NAME'), $eLang->get('RCPT_NAME'), array('dir' => 'rtl', 'required' => 1, 'size' => 40));
		$form->addEmail('mail_manager_email', $elxis->getConfig('MAIL_MANAGER_EMAIL'), $eLang->get('RCPT_EMAIL'), array('required' => 1, 'size' => 40));
		$form->closeFieldset();
		$form->openFieldset($eLang->get('SMTP_OPTIONS'));
		$form->addText('mail_smtp_host', $elxis->getConfig('MAIL_SMTP_HOST'), $eLang->get('HOST'), array('dir' => 'ltr', 'size' => 40));
		$form->addNumber('mail_smtp_port', $elxis->getConfig('MAIL_SMTP_PORT'), $eLang->get('PORT'), array('size' => 5, 'maxlength' => 5));
		$form->addYesNo('mail_smtp_auth', $eLang->get('AUTH_REQ'), $elxis->getConfig('MAIL_SMTP_AUTH'));
		$options = array();
		$options[] = $form->makeOption('', $eLang->get('NO'));
		$options[] = $form->makeOption('ssl', 'SSL');
		$options[] = $form->makeOption('tls', 'TLS');
		$form->addSelect('mail_smtp_secure', $eLang->get('SECURE_CON'), $elxis->getConfig('MAIL_SMTP_SECURE'), $options);
		$form->addText('mail_smtp_user', $elxis->getConfig('MAIL_SMTP_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr', 'size' => 30));
		$tip = ($elxis->getConfig('MAIL_SMTP_PASS') == '') ? '' : 'security:'.$eLang->get('PRIVACY_PROTECTION').'|'.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('mail_smtp_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'size' => 30, 'tip' => $tip));
		$form->closeFieldset();
		$form->closeTab();

		$form->openTab($eLang->get('DATABASE'));
		$options = array();
		if ($data['dbtypes']) {
			foreach ($data['dbtypes'] as $dbtype => $dbtypetxt) {
				$options[] = $form->makeOption($dbtype, $dbtypetxt);
			}
		}
		$form->addSelect('db_type', $eLang->get('DB_TYPE'), $elxis->getConfig('DB_TYPE'), $options, array('dir' => 'ltr', 'tip' => 'warning:'.$eLang->get('WARNING').'|'.$eLang->get('ALERT_CON_LOST')));
		$form->addText('db_host', $elxis->getConfig('DB_HOST'), $eLang->get('HOST'), array('dir' => 'ltr', 'tip' => 'warning:'.$eLang->get('WARNING').'|'.$eLang->get('ALERT_CON_LOST')));
		$form->addNumber('db_port', $elxis->getConfig('DB_PORT'), $eLang->get('PORT'), array('size' => 5, 'maxlength' => 5));
		$form->addYesNo('db_persistent', $eLang->get('PERSISTENT_CON'), $elxis->getConfig('DB_PERSISTENT'));
		$form->addText('db_name', $elxis->getConfig('DB_NAME'), $eLang->get('DB_NAME'), array('dir' => 'ltr'));
		$form->addText('db_prefix', $elxis->getConfig('DB_PREFIX'), $eLang->get('TABLES_PREFIX'), array('required' => 1, 'dir' => 'ltr', 'size' => 10, 'maxlength' => 10));
		$form->addText('db_user', $elxis->getConfig('DB_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$tip = ($elxis->getConfig('DB_PASS') == '') ? '' : 'security:'.$eLang->get('PRIVACY_PROTECTION').'|'.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('db_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'tip' => $tip));
		$form->addText('db_dsn', $elxis->getConfig('DB_DSN'), 'DSN', array('dir' => 'ltr', 'size' => 60, 'tip' => 'DSN|'.$eLang->get('DSN_INFO')));
		$form->addText('db_scheme', $elxis->getConfig('DB_SCHEME'), $eLang->get('SCHEME'), array('dir' => 'ltr', 'size' => 60, 'tip' => $eLang->get('SCHEME').'|'.$eLang->get('SCHEME_INFO')));
		$form->closeTab();

		$form->openTab('FTP');
		$form->addYesNo('ftp', $eLang->get('USE_FTP'), $elxis->getConfig('FTP'));
		$form->addText('ftp_host', $elxis->getConfig('FTP_HOST'), $eLang->get('HOST'), array('dir' => 'ltr'));
		$form->addNumber('ftp_port', $elxis->getConfig('FTP_PORT'), $eLang->get('PORT'), array('size' => 5, 'maxlength' => 5));
		$form->addText('ftp_user', $elxis->getConfig('FTP_USER'), $eLang->get('USERNAME'), array('dir' => 'ltr'));
		$tip = ($elxis->getConfig('FTP_PASS') == '') ? '' : 'security:'.$eLang->get('PRIVACY_PROTECTION').'|'.$eLang->get('PASSWORD_NOT_SHOWN');
		$form->addPassword('ftp_pass', '', $eLang->get('PASSWORD'), array('dir' => 'ltr', 'tip' => $tip));
		$form->addText('ftp_root', $elxis->getConfig('FTP_ROOT'), $eLang->get('PATH'), array('dir' => 'ltr', 'size' => 40, 'tip' => 'info:FTP|'.$eLang->get('FTP_PATH_INFO')));
		$txt = '<a href="javascript:void(null);" onclick="cpCheckFTP();">'.$eLang->get('CHECK_FTP_SETS').'</a>';
		$form->addNote($txt, 'elx_sminfo');
		$form->addHTML('<div id="ftpresponse" class="elx_info" style="display:none;"></div>');
		$form->closeTab();

		$form->openTab($eLang->get('SESSION'));
		$options = array();
		$options[] = $form->makeOption('none', $eLang->get('NONE'));
		$options[] = $form->makeOption('files', $eLang->get('FILES'));
		$options[] = $form->makeOption('database', $eLang->get('DATABASE'));
		$form->addSelect('session_handler', $eLang->get('HANDLER'), $elxis->getConfig('SESSION_HANDLER'), $options, array('tip' => 'Session|'.$eLang->get('HANDLER_INFO')));
		$options = array();
		$options[] = $form->makeOption(600, '10 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(900, '15 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1200, '20 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(1800, '30 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(2700, '45 '.$eLang->get('MINUTES'));
		$options[] = $form->makeOption(3600, '60 '.$eLang->get('MINUTES'));
		$form->addSelect('session_lifetime', $eLang->get('LIFETIME'), $elxis->getConfig('SESSION_LIFETIME'), $options, array('tip' => $eLang->get('LIFETIME').'|'.$eLang->get('SESS_LIFETIME_INFO')));
		$form->addYesNo('session_matchip', $eLang->get('MATCH_IP'), $elxis->getConfig('SESSION_MATCHIP'), array('tip' => 'IP|'.$eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_matchbrowser', $eLang->get('MATCH_BROWSER'), $elxis->getConfig('SESSION_MATCHBROWSER'), array('tip' => 'Browser|'.$eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_matchreferer', $eLang->get('MATCH_REFERER'), $elxis->getConfig('SESSION_MATCHREFERER'), array('tip' => 'HTTP Referer|'.$eLang->get('MATCH_SESS_INFO')));
		$form->addYesNo('session_encrypt', $eLang->get('ENCRYPTION'), $elxis->getConfig('SESSION_ENCRYPT'), array('tip' => $eLang->get('ENCRYPTION').'|'.$eLang->get('ENCRYPT_SESS_INFO')));
		$form->closeTab();

		$form->openTab($eLang->get('SECURITY'));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('NORMAL'));
		$options[] = $form->makeOption(1, $eLang->get('HIGH'));
		$options[] = $form->makeOption(2, $eLang->get('INSANE'));
		$form->addSelect('security_level', $eLang->get('SECURITY_LEVEL'), $elxis->getConfig('SECURITY_LEVEL'), $options, array('tip' => 'warning:'.$eLang->get('SECURITY_LEVEL').'|'.$eLang->get('SECURITY_LEVEL_INFO')));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('OFF'));
		$options[] = $form->makeOption(1, $eLang->get('ADMINISTRATION'));
		$options[] = $form->makeOption(2, $eLang->get('PUBLIC_AREA').' + '.$eLang->get('ADMINISTRATION'));
		$form->addSelect('ssl',  $eLang->get('SSL_SWITCH'), $elxis->getConfig('SSL'), $options, array('tip' => 'warning:SSL/TLS|'.$eLang->get('SSL_SWITCH_INFO')));
		$options = array();
		$options[] = $form->makeOption('auto', $eLang->get('AUTOMATIC'));
		$options[] = $form->makeOption('mcrypt', 'Mcrypt');
		$options[] = $form->makeOption('xor', 'XOR');
		$form->addSelect('encrypt_method', $eLang->get('ENCRYPT_METHOD'), $elxis->getConfig('ENCRYPT_METHOD'), $options, array('disabled' => 1, 'tip' => $eLang->get('CAN_NOT_CHANGE')));
		$form->addText('encrypt_key', '', $eLang->get('ENCRYPTION_KEY'), array('dir' => 'ltr', 'size' => 32, 'readonly' => 1, 'class' => 'elx_input-disabled', 'tip' => $eLang->get('CAN_NOT_CHANGE')));
		$options = array();
		$options[] = $form->makeOption('G', 'G - '.$eLang->get('GENERAL_FILTERS'));
		$options[] = $form->makeOption('C', 'C - '.$eLang->get('CUSTOM_FILTERS'));
		$options[] = $form->makeOption('F', 'F - '.$eLang->get('FSYS_PROTECTION'));
		$options[] = $form->makeOption('P', 'P - POST');
		$options[] = $form->makeOption('H', 'H - '.$eLang->get('HOST'));
		$options[] = $form->makeOption('I', 'I - IP');
		$options[] = $form->makeOption('A', 'A - User Agent');
		$vals = ($elxis->getConfig('DEFENDER') == '') ? array() : str_split($elxis->getConfig('DEFENDER'));
		$form->addSelect('defender', $eLang->get('ELXIS_DEFENDER'), $vals, $options, array('dir' => 'ltr', 'multiple' => 1, 'size' => 7));
		$form->addNote($eLang->get('ELXIS_DEFENDER_INFO'), 'elx_info elx_close');
		$form->closeTab();

		$form->openTab($eLang->get('ERRORS'));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('OFF'));
		$options[] = $form->makeOption(1, $eLang->get('ERRORS'));
		$options[] = $form->makeOption(2, $eLang->get('WARNINGS'));
		$options[] = $form->makeOption(3, $eLang->get('NOTICES'));
		$form->addSelect('error_report', $eLang->get('REPORT'), $elxis->getConfig('ERROR_REPORT'), $options, array('tip' => $eLang->get('REPORT').'|'.$eLang->get('REPORT_INFO')));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('OFF'));
		$options[] = $form->makeOption(1, $eLang->get('ERRORS'));
		$options[] = $form->makeOption(2, $eLang->get('WARNINGS'));
		$options[] = $form->makeOption(3, $eLang->get('NOTICES'));
		$form->addSelect('error_log', $eLang->get('LOG'), $elxis->getConfig('ERROR_LOG'), $options, array('tip' => $eLang->get('LOG').'|'.$eLang->get('LOG_INFO')));
		$form->addYesNo('error_alert', $eLang->get('ALERT'), $elxis->getConfig('ERROR_ALERT'), array('tip' => $eLang->get('ALERT').'|'.$eLang->get('ALERT_INFO')));
		$form->addYesNo('log_rotate', $eLang->get('ROTATE'), $elxis->getConfig('LOG_ROTATE'), array('tip' => 'Log rotate|'.$eLang->get('ROTATE_INFO')));
		$options = array();
		$options[] = $form->makeOption(0, $eLang->get('OFF'));
		$options[] = $form->makeOption(1, $eLang->get('MODULE_POS'));
		$options[] = $form->makeOption(2, $eLang->get('MINIMAL'));
		$options[] = $form->makeOption(3, $eLang->get('MINIMAL').' + '.$eLang->get('MODULE_POS'));
		$options[] = $form->makeOption(4, $eLang->get('FULL'));
		$options[] = $form->makeOption(5, $eLang->get('FULL').' + '.$eLang->get('MODULE_POS'));
		$form->addSelect('debug', $eLang->get('DEBUG'), $elxis->getConfig('DEBUG'), $options);
		$form->addHidden('ajwait', $eLang->get('PLEASE_WAIT'));
		$form->addHidden('ajurl', $elxis->makeAURL('cpanel:checkftp', 'inner.php'), array('dir' => 'ltr'));
		$form->addHidden('task', '');
		$form->closeTab();

		echo '<div class="elx_panel">'."\n";
		$form->render();
		echo "</div>\n";
	}

}

?>