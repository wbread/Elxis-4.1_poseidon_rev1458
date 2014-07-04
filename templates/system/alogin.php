<?php 
/**
* @version		$Id: alogin.php 1434 2013-05-05 17:06:35Z datahell $
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');
defined('ELXIS_ADMIN') or die('You can not access this file from this location!');


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
	<base href="<?php echo $elxis->getConfig('URL').'/'.ELXIS_ADIR; ?>/" />
	<?php echo $page->extrahead; ?>
	<meta http-equiv="content-type" content="<?php echo $page->contenttype; ?>; charset=utf-8" />
	<meta name="generator" content="Elxis - Open Source CMS" />
	<meta name="distribution" content="global" />
	<meta name="robots" content="noindex, nofollow" />
	<title><?php echo $page->title.' - '.$elxis->getConfig('SITENAME'); ?></title>
	<meta name="description" content="<?php echo $page->msgtitle; ?>" />
	<link <?php echo $favrel; ?> href="<?php echo $page->favicon; ?>" />
<?php 
	if (ELXIS_MOBILE == 1) {
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
		echo '<meta name="HandheldFriendly" content="true" />'."\n";
		echo '<link rel="apple-touch-icon" href="'.$elxis->secureBase().'/includes/icons/nautilus/64x64/elxis.png" />'."\n";
	}
?>
	<link rel="stylesheet" href="<?php echo $csslink; ?>" type="text/css" media="all" />
	<script type="text/javascript">
	<?php echo ($page->cdata) ? '/* <![CDATA[ */' : ''; ?>
	function autocoff() {
		if (!document.getElementById) { return false; }
<?php 
		if ($page->confirmform == 0) {
?>
		document.getElementById('uname').value = '';
		document.getElementById('uname').setAttribute("autocomplete", "off");
<?php 
		}
?>
		document.getElementById('pword').value = '';
		document.getElementById('pword').setAttribute("autocomplete", "off");
	}
	<?php echo ($page->cdata) ? '/* ]]> */' : ''; ?>
	</script>
</head>
<body onload="autocoff();">
	<div class="exit_wrapper">
		<div id="exit_content">
			<h2><?php echo $page->title; ?></h2>
			<h3><?php echo $page->msgtitle; ?></h3>
			<p class="msg" id="detailsmsg"><?php echo $page->message; ?></p>
<?php
			if ($page->loginerror != '') {
				echo '<div class="loginerr">'.$page->loginerror."</div>\n";
			}
?>
			<div class="clear"></div>
			<form name="fmelxisalogin" method="post" action="<?php echo $page->loginaction; ?>" class="exit_form">
<?php 
			if ($page->confirmform == 0) {
				echo '<div class="dspace">'."\n";
				echo '<label for="uname">'.$eLang->get('USERNAME')."</label>\n";
				echo '<input type="text" name="uname" id="uname" value="" dir="ltr" class="inputbox" />'."\n";
				echo "</div>\n";
				echo '<div class="dspace">'."\n";						
				echo '<label for="pword">'.$eLang->get('PASSWORD')."</label>\n";
				echo '<input type="password" name="pword" id="pword" value="" dir="ltr" class="inputbox" />'."\n";
				echo "</div>\n";
			} else {
				echo '<div class="dspace">'."\n";
				echo '<label for="pword">'.$eLang->get('PASSWORD')."</label>\n";
				echo '<input type="password" name="pword2" id="pword" value="" dir="ltr" class="inputbox" />'."\n";
				echo "</div>\n";
			}
?>
				<input type="hidden" name="remember" dir="ltr" value="0" />
				<input type="hidden" name="auth_method" dir="ltr" value="elxis" />
				<input type="hidden" name="return" dir="ltr" value="<?php echo $page->return; ?>" />
				<div class="dspace">
					<label for="submitbtn">&#160;</label>
					<button type="submit" id="submitbtn"><?php echo $page->buttontext; ?></button>
				</div>
			</form>
			<div class="clear"></div>
			<div class="exit_langbox">
<?php 
				echo '<div class="picklang">'.$eLang->get('CHOOSE_LANG')."</div>\n";
				$curlng = $eLang->currentLang();
				foreach ($page->infolangs as $lng => $info) {
					$title = $info['NAME'].' - '.$info['NAME_ENG'];
					$classtxt = ($lng == $curlng) ? ' class="currentlang"' : '';
					echo '<a href="'.$elxis->makeAURL($lng).'" title="'.$title.'"'.$classtxt.'>';
					echo '<img src="'.$page->flagsdir.$lng.'.png" alt="'.$info['NAME_ENG'].'" /></a>'."\n";
				}
?>
			</div>
			<div class="exit_sitename">
				<a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a>
			</div>
		</div>
		<div class="exit_copyright">Powered by <a href="http://www.elxis.org" title="Elxis Open Source CMS">Elxis CMS</a> &#169; 2006-<?php echo date('Y'); ?></div>
	</div>
	<!-- Hail, Poseidon, Holder of the Earth, dark-haired lord! -->
</body>
</html>