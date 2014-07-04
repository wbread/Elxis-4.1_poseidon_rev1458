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


define('ELXIS_INSTALLER', 1);

require(ELXIS_PATH.'/includes/install/install.class.php');
$ielxis = new elxisInstaller();
$ielxis->process();

header('content-type:text/html; charset=utf-8');
header('Expires:Mon, 1 Jan 2001 00:00:00 GMT', true);
header('Last-Modified:'.gmdate("D, d M Y H:i:s").' GMT', true);
header('Cache-Control:no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
header('Pragma:no-cache');
?>
<!DOCTYPE html>
<html lang="<?php echo $ielxis->langInfo('LANGUAGE'); ?>" dir="<?php echo $ielxis->langInfo('DIR'); ?>">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="<?php echo $ielxis->langInfo('LANGUAGE'); ?>" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="author" content="Elxis Team" />
	<meta name="copyright" content="Copyright (C) 2006-<?php echo date('Y'); ?> elxis.org" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, follow" />
	<title>Elxis <?php echo $ielxis->verInfo('RELEASE').'.'.$ielxis->verInfo('LEVEL').' '.$ielxis->verInfo('CODENAME').' - '.$ielxis->getLang('INSTALLATION'); ?></title>
	<meta name="description" content="Elxis CMS installer" />
	<link rel="shortcut icon" href="<?php echo $ielxis->url; ?>/media/images/favicon.ico" />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/templates/system/css/standard<?php echo $ielxis->langInfo('RTLSFX'); ?>.css" type="text/css" media="all"  />
	<link rel="stylesheet" href="<?php echo $ielxis->url; ?>/includes/install/css/install<?php echo $ielxis->langInfo('RTLSFX'); ?>.css" type="text/css" media="all"  />
	<script type="text/javascript" src="<?php echo $ielxis->url; ?>/includes/js/elxis.js"></script>
	<script type="text/javascript" src="<?php echo $ielxis->url; ?>/includes/install/inc/install.js"></script>
 </head>
<body>
<div class="ei_global">
	<div class="ei_top">
		<div class="ei_top_middle">
			<a href="http://www.elxis.org" title="Elxis open source CMS" target="_blank">
				<img src="<?php echo $ielxis->url; ?>/includes/install/css/elxislogo.png" alt="elxis cms" border="0" style="border:none;" />
			</a>
			<div class="ei_version_box">Elxis  <?php echo $ielxis->verInfo('RELEASE').'.'.$ielxis->verInfo('LEVEL').' '.$ielxis->verInfo('CODENAME').' rev'.$ielxis->verInfo('REVISION'); ?></div>
			<span class="ei_step_desc"><?php echo $ielxis->stepTitle(); ?></span>
		</div>
	</div>
	<div class="ei_bar">
		<div class="ei_bar_middle">
			<div class="ei_bar_title">
				<h2><?php echo $ielxis->stepTitle(); ?></h2>
			</div>
			<div class="ei_bar_steps">
				<ul>
<?php 
				$total = $ielxis->countSteps();
				$step = $ielxis->getStep();
				for ($i=1; $i <= $total; $i++) {
					$class = ($i == $step) ? ' class="ei_step_active"' : '';
					echo '<li'.$class.'>'.$i."</li>\n";
				}
?>
				</ul>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
	<div class="ei_langbar">
		<div class="ei_langbox">
			<?php $ielxis->langSelect(); ?>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="ei_wrapper">
		<?php $ielxis->makehtml(); ?>
	</div>
	<div id="elxisbaseurlx" style="display:none; visibility:hidden;" dir="ltr"><?php echo $ielxis->url; ?></div>
	<div class="ei_footer_wrap">
		<div class="ei_footer">
			<div class="ei_footer_left">
			<a href="http://www.elxis.org/" title="Elxis open source cms" target="_blank">
				<img src="<?php echo $ielxis->url; ?>/includes/install/css/elxis_footer_off.png" id="elxisfooterlogo" alt="elxis" onmouseover="eiFocusElxis(1)" onmouseout="eiFocusElxis(0)" />
			</a>
			</div>
			<div class="ei_footer_right">
				<br />Powered by <a href="http://www.elxis.org/" target="_blank" title="Elxis CMS">Elxis open source CMS</a>. 
				Copyright (c) 2006-<?php echo date('Y'); ?> Elxis Team.
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>
</div>
</body>
</html>