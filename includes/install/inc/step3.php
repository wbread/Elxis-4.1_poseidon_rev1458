<?php 
/**
* @version		$Id: step3.php 1310 2012-09-30 07:20:49Z datahell $
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_INSTALLER') or die ('Direct access to this location is not allowed');

?>
<h3><?php echo $this->getLang('SETTINGS'); ?></h3>
<p><?php echo $this->getLang('SETTINGS_DESC'); ?></p>
<?php 
$errormsg = $this->dataValue('cfg', 'errormsg', '');
if ($errormsg != '') {
	echo '<div class="elx_error">'.$errormsg."</div><br />\n";
}
?>
<form name="fmconfig" action="<?php echo $this->url; ?>/index.php" method="post" onsubmit="return eiValidateConfig()">
	<fieldset>
		<legend><?php echo $this->getLang('GENERAL'); ?></legend>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_sitename"><?php echo $this->getLang('SITENAME'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="cfg_sitename" id="cfg_sitename" value="<?php echo $this->dataValue('cfg', 'cfg_sitename', 'Elxis '.$this->verInfo('CODENAME')); ?>" class="ei_inputbox" />
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_url"><?php echo $this->getLang('SITE_URL'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="cfg_url" id="cfg_url" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_url', $this->url); ?>" class="ei_inputbox" /><br />
			<span class="ei_formhelp"><?php echo $this->getLang('SITE_URL_DESC'); ?></span>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_repo_path"><?php echo $this->getLang('REPOSITORY'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="cfg_repo_path" id="cfg_repo_path" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_repo_path', ''); ?>" class="ei_inputboxlong" autocomplete="off" /><br />
			<span class="ei_formhelp"><?php echo $this->getLang('REPOPATH_DESC'); ?></span>
		</div>
		<div style="clear:both;"></div>
	</div>

	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_lang"><?php echo $this->getLang('DEF_LANG'); ?></label>
		</div>
		<div class="ei_formcellfs">
<?php 
		$elangs = $this->elxisLanguages();
		$sellang = $this->currentLang();
		if (!isset($elangs[$sellang])) { $sellang = 'en'; }
		echo '<select name="cfg_lang" id="cfg_lang" dir="ltr" class="ei_selectbox">'."\n";
		if ($elangs) {
			foreach ($elangs as $elng => $elnginfo) {
				$sel = ($elng == $sellang) ? ' selected="selected"' : '';
				echo '<option value="'.$elng.'"'.$sel.'>'.$elnginfo['NAME'].' - '.$elnginfo['NAME_ENG']."</option>\n";
			}
		}
		echo "</select><br />\n";
		unset($elangs, $sellang);
?>
			<span class="ei_formhelp"><?php echo $this->getLang('DEFLANG_DESC'); ?></span>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_encrypt_method"><?php echo $this->getLang('ENCRYPT_METHOD'); ?></label>
		</div>
		<div class="ei_formcellfs">
<?php 
		echo '<select name="cfg_encrypt_method" id="cfg_encrypt_method" dir="ltr" class="ei_selectbox">'."\n";
		if (function_exists('mcrypt_encrypt')) {
			echo '<option value="">'.$this->getLang('AUTOMATIC')."</option>\n";
			echo '<option value="mcrypt" selected="selected">mCrypt</option>'."\n";
			echo '<option value="xor">XOR</option>'."\n";
		} else {
			echo '<option value="" selected="selected">'.$this->getLang('AUTOMATIC')."</option>\n";
			echo '<option value="mcrypt" disabled="disabled">mCrypt</option>'."\n";
			echo '<option value="xor">XOR</option>'."\n";
		}
		echo "</select><br />\n";
?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="cfg_encrypt_key"><?php echo $this->getLang('ENCRYPT_KEY'); ?></label>
		</div>
		<div class="ei_formcellfs">
<?php 
			$chars = array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9));
			shuffle($chars);
			$enckey = '';
			for ($i=0; $i<16; $i++) { $enckey .= $chars[$i]; }
?>
			<input type="text" name="cfg_encrypt_key" id="cfg_encrypt_key" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_encrypt_key', $enckey); ?>" class="ei_inputbox" maxlength="16" /><br />
			<a href="javascript:void(null);" onclick="eiMakeKey();" class="ei_refresh"><?php echo $this->getLang('GEN_OTHER'); ?></a>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label><?php echo $this->getLang('FRIENDLY_URLS'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<span dir="ltr"><input type="radio" name="cfg_sef" id="cfg_sef1" value="1" /> <?php echo $this->getLang('YES'); ?></span> 
			<span dir="ltr"><input type="radio" name="cfg_sef" id="cfg_sef0" value="0" checked="checked" /> <?php echo $this->getLang('NO'); ?></span><br />
			<span class="ei_formhelp"><?php echo $this->getLang('FRIENDLY_URLS_DESC'); ?></span>
		</div>
		<div style="clear:both;"></div>
	</div>
	</fieldset>

	<fieldset>
		<legend><?php echo $this->getLang('DATABASE'); ?></legend>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_type"><?php echo $this->getLang('TYPE'); ?></label>
			</div>
			<div class="ei_formcellfs">
<?php 
			$pdodrivers = PDO::getAvailableDrivers();
			if (!$pdodrivers) { $pdodrivers = array(); }

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
			echo '<select name="cfg_db_type" id="cfg_db_type" dir="ltr" class="ei_selectbox">'."\n";
			$found = false;
			foreach ($dbtypes as $dbtype => $dbtext) {
				if (file_exists(ELXIS_PATH.'/includes/install/data/'.$dbtype.'.sql')) {
					$supported = (in_array($dbtype, $pdodrivers)) ? true : false;
				} else {
					$supported = false;
				}

				if ($dbtype == 'mysql') {
					if ($supported) {
						$found = true;
						$extra = ' selected="selected"';
					} else {
						$extra = ' disabled="disabled"';
					}
				} else {
					if ($supported) {
						$found = true;
						$extra = '';
					} else {
						$extra = ' disabled="disabled"';
					}
				}
				echo '<option value="'.$dbtype.'"'.$extra.'>'.$dbtext."</option>\n";
			}
			if (!$found) {
				echo '<option value="" selected="selected">No PDO driver is available!'."</option>\n";
			}
			echo "</select><br />\n";
			unset($dbtypes);
?>
				<span class="ei_formhelp"><?php echo $this->getLang('DBTYPE_DESC'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_host"><?php echo $this->getLang('HOST'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_host" id="cfg_db_host" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_host', 'localhost'); ?>" class="ei_inputbox" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_port"><?php echo $this->getLang('PORT'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_port" id="cfg_db_port" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_port', '0'); ?>" class="ei_inputbox" maxlength="6" /><br />
				<span class="ei_formhelp"><?php echo $this->getLang('PORT_DESC'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_name"><?php echo $this->getLang('NAME'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_name" id="cfg_db_name" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_name', ''); ?>" class="ei_inputbox" autocomplete="off" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_prefix"><?php echo $this->getLang('TABLES_PREFIX'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_prefix" id="cfg_db_prefix" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_prefix', 'elx_'); ?>" class="ei_inputbox" maxlength="10" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_user"><?php echo $this->getLang('USERNAME'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_user" id="cfg_db_user" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_user', ''); ?>" class="ei_inputbox" autocomplete="off" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_pass"><?php echo $this->getLang('PASSWORD'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="password" name="cfg_db_pass" id="cfg_db_pass" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_pass', ''); ?>" class="ei_inputbox" autocomplete="off" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_dsn">DSN</label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_dsn" id="cfg_db_dsn" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_dsn', ''); ?>" class="ei_inputboxlong" autocomplete="off" /><br />
				<span class="ei_formhelp"><?php echo $this->getLang('DSN_DESC'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_db_scheme"><?php echo $this->getLang('SCHEME'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_db_scheme" id="cfg_db_scheme" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_db_scheme', ''); ?>" class="ei_inputboxlong" autocomplete="off" /><br />
				<span class="ei_formhelp"><?php echo $this->getLang('SCHEME_DESC'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">&#160;</div>
			<div class="ei_formcellfs">
				<a href="javascript:void(null);" onclick="eiCheckDB();"><?php echo $this->getLang('CHECK_DB_SETS'); ?></a><br />
				<div id="dbresponse" class="elx_sminfo" style="display:none;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</fieldset>
	<fieldset>
		<legend>FTP</legend>
<?php 
		$parts = parse_url($this->url);
		$host = $parts['host'];
		$ftppath = isset($parts['path']) ? rtrim($parts['path'], '/') : '';
		if ($ftppath == '') { $ftppath = '/'; }

		if ($host == 'localhost') {
			$hoststr = $host;
		} else if (preg_match('@(\.loc)$@', $host)) {
			$hoststr = 'localhost';
		} else if (preg_match('/^[0-9\.]+$/', $host)) {
			$hoststr = $host;
		} else if (substr_count($host, '.') > 1) {
			$hoststr = $host;
		} else {
			$hoststr = 'ftp.'.$host;
		}
		unset($parts, $host);
?>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label><?php echo $this->getLang('USE_FTP'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<span dir="ltr"><input type="radio" name="cfg_ftp" id="cfg_ftp1" value="1" onclick="eiToggleFTP(1)" /> <?php echo $this->getLang('YES'); ?></span> 
				<span dir="ltr"><input type="radio" name="cfg_ftp" id="cfg_ftp0" value="0" checked="checked" onclick="eiToggleFTP(0)" /> <?php echo $this->getLang('NO'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div id="ftp_details" style="display:none; visibility:hidden;">
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_ftp_host"><?php echo $this->getLang('HOST'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_ftp_host" id="cfg_ftp_host" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_host', $hoststr); ?>" class="ei_inputbox" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_ftp_port"><?php echo $this->getLang('PORT'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_ftp_port" id="cfg_ftp_port" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_port', '0'); ?>" class="ei_inputbox" maxlength="6" /><br />
				<span class="ei_formhelp"><?php echo $this->getLang('FTPPORT_DESC'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_ftp_root"><?php echo $this->getLang('PATH'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_ftp_root" id="cfg_ftp_root" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_root', $ftppath); ?>" class="ei_inputbox" /><br />
				<span class="ei_formhelp"><?php echo $this->getLang('FTP_PATH_INFO'); ?></span>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_ftp_user"><?php echo $this->getLang('USERNAME'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="text" name="cfg_ftp_user" id="cfg_ftp_user" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_user', ''); ?>" class="ei_inputbox" autocomplete="off" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">
				<label for="cfg_ftp_pass"><?php echo $this->getLang('PASSWORD'); ?></label>
			</div>
			<div class="ei_formcellfs">
				<input type="password" name="cfg_ftp_pass" id="cfg_ftp_pass" dir="ltr" value="<?php echo $this->dataValue('cfg', 'cfg_ftp_pass', ''); ?>" class="ei_inputbox" autocomplete="off" />
			</div>
			<div style="clear:both;"></div>
		</div>
		<div class="ei_formrow">
			<div class="ei_formlabfs">&#160;</div>
			<div class="ei_formcellfs">
				<a href="javascript:void(null);" onclick="eiCheckFTP();"><?php echo $this->getLang('CHECK_FTP_SETS'); ?></a><br />
				<div id="ftpresponse" class="elx_sminfo" style="display:none;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
		</div>
	</fieldset>

	<div id="ei_baseurl" style="display:none;" dir="ltr"><?php echo $this->url; ?></div>
	<input type="hidden" name="step" value="4" />
	<input type="hidden" name="lang" value="<?php echo $this->currentLang(); ?>" />
	<div class="ei_button_wrap">
		<button type="submit" name="cfgsubmit" id="cfgsubmit" value="1" class="submit"><span><span><?php echo $this->getLang('SUBMIT'); ?></span></span></button>
		<div style="clear:both;"></div>
	</div>
</form>