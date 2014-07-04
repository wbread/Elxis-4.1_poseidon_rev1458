<?php 
/**
* @version		$Id$
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
<h3><?php echo $this->getLang('VERSION_CHECK'); ?></h3>
<p><?php echo $this->getLang('VERSION_PROLOGUE'); ?></p>
<div class="version_note">
<?php 
	echo '<strong>Elxis '.$this->verInfo('RELEASE').'.'.$this->verInfo('LEVEL').' '.$this->verInfo('CODENAME')."</strong><br />\n";
	echo $this->getLang('STATUS').': <strong>'.$this->verInfo('STATUS')."</strong><br />\n";
	echo $this->getLang('REVISION_NUMBER').': <strong>'.$this->verInfo('REVISION')."</strong><br />\n";
	echo $this->getLang('RELEASE_DATE').': <strong>'.$this->verInfo('RELDATE')." GMT</strong><br />\n";
?>
	Copyright (c) 2006-<?php echo date('Y'); ?> Elxis Team (<a href="http://www.elxis.org" title="Elxis CMS" target="_blank">elxis.org</a>). All rights reserved.<br />
</div>

<h3><?php echo $this->getLang('BEFORE_BEGIN'); ?></h3>
<p><?php echo $this->getLang('BEFORE_DESC'); ?></p>

<div class="ei_block">
	<h4><?php echo $this->getLang('DATABASE'); ?></h4>
	<p><?php echo $this->getLang('DATABASE_DESC'); ?></p>
</div>

<div class="ei_block">
	<h4><?php echo $this->getLang('REPOSITORY'); ?></h4>
	<p><?php echo $this->getLang('REPOSITORY_DESC'); ?></p>
</div>
<div class="ei_left_space">
	<table cellspacing="0" cellpadding="0" width="100%" border="0">
	<tr>
		<td><span dir="ltr">/sample/path/to/site/public_html/</span></td>
		<td><?php echo $this->getLang('SAMPLE_ELXPATH'); ?></td>
	</tr>
	<tr>
		<td style="color:#666666;"><span dir="ltr">/sample/path/to/site/public_html/<strong>repository/</strong></span></td>
		<td><?php echo $this->getLang('DEF_REPOPATH'); ?></td>
	</tr>
	<tr>
		<td style="color:#008000;"><span dir="ltr">/sample/path/to/site/<strong>secret_repo/</strong></span></td>
		<td><?php echo $this->getLang('REQ_REPOPATH'); ?></td>
	</tr>
	</table>
<?php 
	if (file_exists(ELXIS_PATH.'/repository/')) {
		echo '<p class="elx_warning">'.$this->getLang('REPOSITORY_DEFAULT')."<br />\n";
		echo '<span style="font-style:italic;" dir="ltr">'.ELXIS_PATH."/repository/</span></p>\n";
	}
?>
</div>
<div class="ei_button_wrap">
	<a href="<?php echo $this->url.'/?step=2&amp;lang='.$this->currentLang(); ?>" class="ei_abutton" title="<?php echo $this->getLang('STEP'); ?> 2"><?php echo $this->getLang('CONTINUE'); ?></a>
</div>
