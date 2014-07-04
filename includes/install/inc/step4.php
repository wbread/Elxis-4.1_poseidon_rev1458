<?php 
/**
* @version		$Id: step4.php 1301 2012-09-27 18:11:01Z datahell $
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
<h3><?php echo $this->getLang('DATA_IMPORT'); ?></h3>

<?php 
if ($this->dataValue('cfg', 'errormsg', '') != '') {
	echo '<div class="elx_error"><strong>'.$this->getLang('SETTINGS_ERRORS')."</strong><br />\n".$this->dataValue('cfg', 'errormsg', '')."</div>\n";
	echo '<div class="ei_button_wrap">'."\n";
	echo '<a href="'.$this->url.'/?step=3&amp;lang='.$this->currentLang().'" class="ei_abutton" title="'.$this->getLang('STEP').' 3">'.$this->getLang('BACK')."</a>\n";
	echo "</div>\n";

	return;
}

if ($this->dataValue('import_error', '', '') != '') {
	echo '<div class="elx_error"><strong>'.$this->getLang('ERROR')."</strong><br />\n".$this->dataValue('import_error', '', '')."</div>\n";
	echo '<div class="ei_button_wrap">'."\n";
	echo '<a href="'.$this->url.'/?step=3&amp;lang='.$this->currentLang().'" class="ei_abutton" title="'.$this->getLang('STEP').' 3">'.$this->getLang('BACK')."</a>\n";
	echo "</div>\n";

	return;
} else if ($this->dataValue('usr', 'errormsg', '') != '') {
	//hide rest messages in case we come back to this page
} else if ($this->dataValue('queries', '', 0) == 0) {
	echo '<div class="elx_warning"><strong>'.$this->getLang('WARNING')."</strong><br />\n".$this->getLang('NO_QUERIES_WARN')."<br />\n";
	echo '<a href="'.$this->url.'/?step=3&amp;lang='.$this->currentLang().'" title="'.$this->getLang('STEP').' 3">'.$this->getLang('RETRY_PREV_STEP')."</a>\n";
	echo "</div>\n";
} else {
	echo '<div class="elx_sminfo">'.$this->getLang('INIT_DATA_IMPORTED').' ';
	printf($this->getLang('QUERIES_EXEC'), '<strong>'.$this->dataValue('queries', '', 0).'</strong>');
	echo "</div><br />\n";
}
?>

<h3><?php echo $this->getLang('ADMIN_ACCOUNT'); ?></h3>
<?php 
$errormsg = $this->dataValue('usr', 'errormsg', '');
if ($errormsg != '') {
	echo '<div class="elx_error">'.$errormsg."</div><br />\n";
}
?>

<form name="fmconfig" action="<?php echo $this->url; ?>/index.php" method="post" onsubmit="return eiValidateUser()">
	<fieldset>
		<legend><?php echo $this->getLang('YOUR_DETAILS'); ?></legend>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_firstname"><?php echo $this->getLang('FIRSTNAME'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="u_firstname" id="u_firstname" value="<?php echo $this->dataValue('usr', 'u_firstname', ''); ?>" class="ei_inputbox" autocomplete="off" />
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_lastname"><?php echo $this->getLang('LASTNAME'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="u_lastname" id="u_lastname" value="<?php echo $this->dataValue('usr', 'u_lastname', ''); ?>" class="ei_inputbox" autocomplete="off" />
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_email"><?php echo $this->getLang('EMAIL'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="u_email" id="u_email" dir="ltr" value="<?php echo $this->dataValue('usr', 'u_email', ''); ?>" class="ei_inputbox" autocomplete="off" />
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_uname"><?php echo $this->getLang('USERNAME'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="text" name="u_uname" id="u_uname" dir="ltr" value="<?php echo $this->dataValue('usr', 'u_uname', $this->makeUname()); ?>" class="ei_inputbox" autocomplete="off" /> 
			<a href="javascript:void(null);" onclick="eiMakeUname();" class="ei_refresh"><?php echo $this->getLang('GEN_OTHER'); ?></a><br />
			<span class="ei_formhelp"><?php echo $this->getLang('AVOID_COMUNAMES'); ?></span>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_pword"><?php echo $this->getLang('PASSWORD'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="password" name="u_pword" id="u_pword" dir="ltr" value="<?php echo $this->dataValue('usr', 'u_pword', ''); ?>" class="ei_inputbox" onkeyup="elxPasswordMeter('fmconfig', 'u_pword', 'u_uname');" autocomplete="off" /> 
			<img src="<?php echo $this->url; ?>/includes/libraries/elxis/form/level0.png" id="u_pwordmeter" alt="strength" title="empty password" border="0" />
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_formrow">
		<div class="ei_formlabfs">
			<label for="u_pword2"><?php echo $this->getLang('CONFIRM_PASS'); ?></label>
		</div>
		<div class="ei_formcellfs">
			<input type="password" name="u_pword2" id="u_pword2" dir="ltr" value="" class="ei_inputbox" autocomplete="off" />
		</div>
		<div style="clear:both;"></div>
	</div>
	</fieldset>
	<div id="ei_baseurl" style="display:none;" dir="ltr"><?php echo $this->url; ?></div>
	<input type="hidden" name="step" value="5" />
	<input type="hidden" name="lang" value="<?php echo $this->currentLang(); ?>" />
	<input type="hidden" id="langfamily" name="langfamily" dir="ltr"  value="<?php echo $this->langInfo('LANGUAGE'); ?>" />
	<input type="hidden" id="elxisbasefmconfig" name="elxisbasefmconfig" dir="ltr" value="<?php echo $this->url; ?>" />

<?php 
	foreach ($this->dataValue('cfg', '', array()) as $k => $v) {
		if (strpos($k, 'cfg_') !== 0) { continue; }
		echo '<input type="hidden" name="'.$k.'" value="'.$v.'" />'."\n";
	}
?>
	<div class="ei_button_wrap">
		<button type="submit" name="usubmit" id="usubmit" value="1" class="submit"><span><span><?php echo $this->getLang('SUBMIT'); ?></span></span></button>
		<div style="clear:both;"></div>
	</div>
</form>