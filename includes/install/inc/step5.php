<?php 
/**
* @version		$Id: step5.php 1276 2012-09-09 14:56:28Z datahell $
* @package		Elxis
* @subpackage	Installer
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_INSTALLER') or die ('Direct access to this location is not allowed');


echo '<h3>'.$this->getLang('FINISH')."</h3>\n";

if (($this->dataValue('final', 'save', false) === false) || ($this->dataValue('final', 'renhtaccess', -1) === 0)) {
	echo '<div class="elx_warning">'.$this->getLang('ELXIS_INST_WARN')."<br />\n";
	if ($this->dataValue('final', 'save', false) === false) {
		echo '- '.$this->getLang('CNOT_CREA_CONFIG')."<br />\n";
	}
	if ($this->dataValue('final', 'renhtaccess', -1) === 0) {
		echo '- '.$this->getLang('CNOT_REN_HTACC')."<br />\n";
	}
	echo "</div>\n";
} else {
	echo '<div class="elx_info">'.$this->getLang('ELXIS_INST_SUCC')."</div>\n";
}
echo "<br /><br />\n";

if ($this->dataValue('final', 'save', false) === false) {
	echo '<h3>'.$this->getLang('CONFIG_FILE')."</h3>\n";
	echo '<div class="elx_textblock" dir="ltr">'.ELXIS_PATH."/configuration.php</div>\n";
	echo '<p>'.$this->getLang('CONFIG_FILE_MANUAL')."</p>\n";
	echo '<textarea rows="10" cols="80" class="ei_license" dir="ltr">'."\n";
	echo htmlspecialchars($this->dataValue('final', 'config', ''))."</textarea>\n";
}

if ($this->dataValue('final', 'renhtaccess', -1) === 0) {
	echo '<h3>.htaccess</h3>'."\n";
	echo '<p>'.$this->getLang('REN_HTACCESS_MANUAL')."</p>\n";
}
?>
<h3><?php echo $this->getLang('WHAT_TODO'); ?></h3>
<ul class="elx_stdul">
	<li><?php echo $this->getLang('RENAME_ADMIN_FOLDER'); ?></li>
	<li><?php echo $this->getLang('LOGIN_CONFIG'); ?></li>
	<li><a href="<?php echo $this->url; ?>/"><?php echo $this->getLang('VISIT_NEW_SITE'); ?></a></li>
	<li><a href="http://forum.elxis.org/" target="_blank"><?php echo $this->getLang('VISIT_ELXIS_SUP'); ?></a></li>
</ul>
<br /><br /><br />

<p><?php echo $this->getLang('THANKS_USING_ELXIS'); ?><br />
Elxis Team
</p>
