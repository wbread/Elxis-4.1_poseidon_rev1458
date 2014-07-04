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


$elxis = eFactory::getElxis();
$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();

//$eDoc->addScriptLink('http://protofluid.com/javascript/protoFluid3.02.js'); //for testing

$eDoc->setContentType('text/html');
$eDoc->setNamespace('');
$eDoc->setDocType('<!DOCTYPE html>');
		
$touch_icon = $elxis->icon('elxis', 64);
$eDoc->setMetaTag('viewport', 'width=device-width, initial-scale=1.0');
$eDoc->addLink($touch_icon, '', 'apple-touch-icon');
$eDoc->addJQuery();
unset($touch_icon);

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase(); ?>/templates/system/css/mobile<?php echo $eLang->getinfo('RTLSFX'); ?>.css" type="text/css" />
</head>
<body>
	<div class="mobi_wrap">
    	<div class="mobi_header">
			<div class="mobi_options">
				<ul>
      				<li id="mobioptl" class="mobioptempty" title="<?php echo $eLang->get('LANGUAGE'); ?>">Language</li>
      				<li id="mobiopts" class="mobioptempty" title="<?php echo $eLang->get('SEARCH'); ?>">Search</li>
      				<li id="mobioptm" class="mobioptempty" title="<?php echo $eLang->get('MENU'); ?>">Menu</li>
      			</ul>
      		</div>
    		<div class="mobi_logo">
				<a href="<?php echo $elxis->makeURL(); ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>"><?php echo $elxis->getConfig('SITENAME'); ?></a>
    		</div>
    		<div class="clear"></div>
		</div>
		<div class="mobi_topmods">
			<div class="mobi_language">
				<?php $eDoc->modules('language', 'none'); ?>
				<div class="clear"></div>
			</div>
			<div class="mobi_search">
			<?php $eDoc->modules('search', 'none'); ?>
				<div class="clear"></div>
			</div>
			<div class="mobi_menu">
				<?php $eDoc->modules('menu', 'none'); ?>
				<div class="clear"></div>
			</div>
		</div>
		<div class="mobi_main">
<?php 
			//$eDoc->pathway(); //Uncomment if you want to display the pathway

			if ($eDoc->countModules('mobiletop') > 0) {
				echo '<div class="mobi_top">'."\n";
				$eDoc->modules('mobiletop');
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
			}
			$eDoc->component();
			if ($eDoc->countModules('mobilebottom') > 0) {
				echo '<div class="mobi_bottom">'."\n";
				$eDoc->modules('mobilebottom');
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
			}
?>
		</div>
		<div class="mobi_footer">
<?php 
		if ($elxis->user()->gid == 7) {
			$ulink = $elxis->makeURL('user:login/', '', true);
			$utitle = $eLang->get('LOGIN');
		} else {
			$ulink = $elxis->makeURL('user:logout.html', '', true);
			$utitle = $eLang->get('LOGOUT');
		}
?>
			<a href="<?php echo $ulink; ?>" title="<?php echo $utitle; ?>"><?php echo $utitle; ?></a> 
			<a href="<?php echo $elxis->makeURL(); ?>?elxmobile=0" title="Switch to desktop version">Desktop</a>
		</div>
	</div>
	<script type="text/javascript">
	$('#mobioptl').click(function(e){ if ($('.mobi_language').css('display') == 'none') { $('#mobioptl').toggleClass('mobioptact', true); $('#mobioptm').toggleClass('mobioptact', false); $('#mobiopts').toggleClass('mobioptact', false); $('.mobi_language').slideDown(); $('.mobi_menu').slideUp(); $('.mobi_search').slideUp(); } else { $('#mobiopts').toggleClass('mobioptact', false); $('#mobioptm').toggleClass('mobioptact', false); $('#mobioptl').toggleClass('mobioptact', false); $('.mobi_language').slideUp(); $('.mobi_search').slideUp(); $('.mobi_menu').slideUp(); } });
	$('#mobiopts').click(function(e){ if ($('.mobi_search').css('display') == 'none') { $('#mobiopts').toggleClass('mobioptact', true); $('#mobioptm').toggleClass('mobioptact', false); $('#mobioptl').toggleClass('mobioptact', false); $('.mobi_language').slideUp(); $('.mobi_menu').slideUp(); $('.mobi_search').slideDown(); } else { $('#mobiopts').toggleClass('mobioptact', false); $('#mobioptm').toggleClass('mobioptact', false); $('#mobioptl').toggleClass('mobioptact', false); $('.mobi_language').slideUp(); $('.mobi_search').slideUp(); $('.mobi_menu').slideUp(); } });
	$('#mobioptm').click(function(e){ if ($('.mobi_menu').css('display') == 'none') { $('#mobiopts').toggleClass('mobioptact', false); $('#mobioptm').toggleClass('mobioptact', true); $('#mobioptl').toggleClass('mobioptact', false); $('.mobi_language').slideUp(); $('.mobi_search').slideUp(); $('.mobi_menu').slideDown(); } else { $('#mobiopts').toggleClass('mobioptact', false); $('#mobioptm').toggleClass('mobioptact', false); $('#mobioptl').toggleClass('mobioptact', false); $('.mobi_language').slideUp(); $('.mobi_menu').slideUp(); $('.mobi_search').slideUp(); } });
	</script>
	<!-- Powered by Elxis CMS( http://www.elxis.org ) - Mobile version -->
</body>
</html>