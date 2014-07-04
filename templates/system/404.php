<?php 
/**
* @version		$Id$
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
	<meta name="robots" content="noindex, follow" />
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
	function qclear(ison) {
		var textel = document.getElementById('qtxt');
		var sear = document.getElementById('qsear').innerHTML;
		if (ison == 1) {
			if (textel.value == sear) { textel.value = ''; }
		} else {
			if (textel.value == '') { textel.value = sear; }
		}
	}
	<?php echo ($page->cdata) ? '/* ]]> */' : ''; ?>
	</script>
</head>
<body>
	<div class="exit_wrapper">
		<div id="exit_content">
			<h2><?php echo $page->title; ?></h2>
			<h3><?php echo $page->msgtitle; ?></h3>
			<p class="msg" id="detailsmsg"><?php echo $page->message; ?></p>
			<div class="errurl"><?php echo $page->url; ?></div>
			<div class="refcode"><?php echo $eLang->get('REFERENCE_CODE').': <span>'.$page->refcode.'</span>'; ?></div>
			<form name="fmelxissearch" method="get" action="<?php echo $page->searchaction; ?>" class="exit_form">
				<input type="text" name="q" id="qtxt" value="<?php echo $eLang->get('SEARCH'); ?>..." dir="<?php echo $eLang->getinfo('DIR'); ?>" class="inputbox" onfocus="qclear(1);" onblur="qclear(0);" />
				<button type="submit" id="submitbtn" class="sidebutton"><?php echo $eLang->get('SEARCH'); ?></button>
			</form>
			<div id="menu">
				<p><?php echo $eLang->get('VISIT_ONE_OF_OUR_PAGES'); ?></p>
				<ul class="exit_menu">
<?php 
				if ($page->menu && (count($page->menu) > 0)) {
					$i = 0;
					foreach ($page->menu as $item) {
						if ($item->menu_type != 'link') { continue; }
						if ($i > 9) { break; }
						$ssl = ($item->secure == 1) ? true : false;
						if ($item->popup == 1) {
							$link = $elxis->makeURL($item->link, $item->file, $ssl, false);
							$onclick = ' onclick="elxPopup(\''.$link.'\');"';
							$link = 'javascript:void(null);';
						} else {
							$onclick = '';
							$link = $elxis->makeURL($item->link, $item->file, $ssl);
						}
						$trg = ($item->target != '_self') ? ' target="'.$item->target.'"' : '';
						echo "\t\t\t\t".'<li><a href="'.$link.'" title="'.$item->title.'"'.$onclick.$trg.'>'.$item->title."</a></li>\n";
						$i++;
					}
				} else {
					echo "\t\t\t\t".'<li><a href="'.$page->sitelink.'" title="'.$eLang->get('HOME').'">'.$eLang->get('HOME')."</a></li>\n";
				}
				echo "\t\t\t\t".'<li><a href="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a></li>\n";
?>
				</ul>
				<div class="clear"></div>
			</div>
			<div class="exit_sitename">
				<a href="<?php echo $page->sitelink; ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a>
			</div>
		</div>
		<div class="exit_copyright">Powered by <a href="http://www.elxis.org" title="Elxis Open Source CMS">Elxis CMS</a> &#169; 2006-<?php echo date('Y'); ?></div>
	</div>
	<span id="qsear" style="display:none; visibility:hidden;"><?php echo $eLang->get('SEARCH'); ?>...</span>
	<!-- Hail, Poseidon, Holder of the Earth, dark-haired lord! -->
</body>
</html>