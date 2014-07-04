<?php 
/**
* @version		$Id: offline.php 1434 2013-05-05 17:06:35Z datahell $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$csslink = $elxis->secureBase().'/templates/system/css/';
$csslink .= ($eLang->getinfo('DIR') == 'rtl') ? 'exit-rtl.css' : 'exit.css';
if (preg_match('#(\.png)$#i', $page->favicon)) {
	$favrel = 'rel="icon" type="image/png"';
} else {
	$favrel = 'rel="shortcut icon"';
}

echo $page->doctype."\n";
?>
<html<?php echo $page->htmlattributes; ?>>
<head>
	<base href="<?php echo $elxis->getConfig('URL'); ?>/" />
	<?php echo $page->extrahead; ?>
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="index, follow" />
	<title><?php echo $page->title.' - '.$elxis->getConfig('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $eLang->get('WEBSITE_OFFLINE'); ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
<?php 
	if (ELXIS_MOBILE == 1) {
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
		echo '<meta name="HandheldFriendly" content="true" />'."\n";
		echo '<link rel="apple-touch-icon" href="'.$elxis->secureBase().'/includes/icons/nautilus/64x64/elxis.png" />'."\n";
	}
?>
	<link rel="stylesheet" href="<?php echo $csslink; ?>" type="text/css" media="all" />
</head>
<body>
	<div class="exit_wrapper">
		<div id="exit_content">
			<h2><?php echo $page->title; ?></h2>
			<h3><?php echo $page->msgtitle; ?></h3>
			<p class="msg" id="detailsmsg"><?php echo $page->message; ?></p>
<?php if ($elxis->getConfig('ONLINE') == 2) { ?>
			<p class="msg" id="explainmsg"><?php echo $eLang->get('OWN_ADMIN_LOGIN'); ?></p>
<?php
			if ($page->loginerror != '') {
				echo '<div class="loginerr">'.$page->loginerror."</div>\n";
			}
?>
			<div class="clear"></div>
			<form name="fmelxislogin" method="post" action="<?php echo $page->loginaction; ?>" class="exit_form">
				<div class="dspace">
					<label for="uname"><?php echo $eLang->get('USERNAME'); ?></label>
					<input type="text" name="uname" id="uname" value="" dir="ltr" class="inputbox" />
				</div>
				<div class="dspace">
					<label for="pword"><?php echo $eLang->get('PASSWORD'); ?></label>
					<input type="password" name="pword" id="pword" value="" dir="ltr" class="inputbox" />
				</div>
				<input type="hidden" name="remember" dir="ltr" value="1" />
				<input type="hidden" name="auth_method" dir="ltr" value="elxis" />
				<div class="dspace">
					<label for="submitbtn">&#160;</label>
					<button type="submit" id="submitbtn"><?php echo $eLang->get('LOGIN'); ?></button>
				</div>
			</form>
			<div class="clear"></div>
<?php 
	}
?>
			<div class="exit_sitename">
				<a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a>
			</div>
		</div>
		<div class="exit_copyright">Powered by <a href="http://www.elxis.org" title="Elxis Open Source CMS">Elxis CMS</a> &#169; 2006-<?php echo date('Y'); ?></div>
	</div>
	<!-- Hail, Poseidon, Holder of the Earth, dark-haired lord! -->
</body>
</html>