<?php 
/**
* @version		$Id: step2.php 1273 2012-09-09 11:46:58Z datahell $
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
<h3><?php echo $this->getLang('LICENSE'); ?></h3>
<p><?php echo $this->getLang('LICENSE_NOTES'); ?></p>

<h3 dir="ltr">Elxis Public License (EPL)</h3>

<?php 
if (file_exists(ELXIS_PATH.'/includes/install/inc/license.txt')) {
	echo '<textarea rows="10" cols="80" class="ei_license" dir="ltr">'."\n";
	include(ELXIS_PATH.'/includes/install/inc/license.txt');
	echo "</textarea>\n";
} else if (file_exists(ELXIS_PATH.'/license.txt')) {
	echo '<textarea rows="10" cols="80" class="ei_license" dir="ltr">'."\n";
	include(ELXIS_PATH.'/license.txt');
	echo "</textarea>\n";
} else {
	echo '<iframe name="licframe" id="licframe" src="http://www.elxis.org/elxis-public-license.html" frameborder="0" border="0" cellspacing="0" style="border:none; width:99%; height:300px; overflow-y:scroll; overflow-x: hidden;"></iframe>'."\n";
}
?>

<div class="ei_button_wrap">
	<input type="checkbox" name="licagree" id="licagree" value="1" onclick="eiAgreeTerms();" /> <?php echo $this->getLang('I_AGREE_TERMS'); ?><br /><br />
	<a href="javascript:void(null);" id="gotostep3a" class="ei_abutton_off" title="<?php echo $this->getLang('STEP'); ?> 2"><?php echo $this->getLang('CONTINUE'); ?></a>
	<div id="gotostep3u" style="display:none;" dir="ltr"><?php echo $this->url.'/?step=3&amp;lang='.$this->currentLang(); ?></div>
</div>
