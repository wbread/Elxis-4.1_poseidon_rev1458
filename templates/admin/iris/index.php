<?php
/**
* @version		$Id: index.php 1308 2012-09-29 16:35:45Z datahell $
* @package		Elxis
* @subpackage	iris administration template
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

$tplurl = $elxis->secureBase().'/templates/admin/iris';

$eDoc->addJQuery();
$eDoc->addScriptLink($elxis->secureBase().'/includes/js/jquery/livequery.js');
$eDoc->addScriptLink($elxis->secureBase().'/includes/js/jquery/tooltip.js');
$eDoc->loadLightbox();
$eDoc->addScriptLink($tplurl.'/js/iris-tpl.js');
$eDoc->addStyleLink($tplurl.'/css/template'.$eLang->getinfo('RTLSFX').'.css');
eFactory::getPathway(false)->setHomeImage($tplurl.'/images/pathway_home.png', $tplurl.'/images/pathway_home_hover.png');
eFactory::getPathway(false)->setSeparator('<img src="'.$tplurl.'/images/pathway_sep'.$eLang->getinfo('RTLSFX').'.png" alt="sep" border="0" align="top" /> ');

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
</head>
<body>
	<div id="elx_outwrapper">
		<div id="elx_topwrapper">
            <div id="elx_headerwrapper">
                <div id="iris_elx_header">
                    <div id="iris_elx_logo">
						<a href="<?php echo $elxis->getConfig('URL'); ?>" target="_blank"><?php echo $elxis->getConfig('SITENAME'); ?></a>
					</div>
					<div id="iris_elx_version">
						<?php echo strtolower($elxis->fromVersion('CODENAME')).' '.$elxis->getVersion(); ?>
					</div>
                </div>
				<div class="iris_navutools_wrapper">
					<div id="iris_navigation_wrapper">
						<?php $eDoc->module('mod_adminmenu', 'none'); ?>
					</div>
					<div id="iris_utools_wrapper">
						<?php $eDoc->modules('tools', 'none'); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div class="iris_pathtool_wrapper">
					<div id="iris_pathway_wrapper">
						<?php $eDoc->pathway(false); ?>
					</div>
					<div id="iris_toolbar_wrapper">
						<?php $eDoc->toolbar(); ?>
					</div>
					<div style="clear:both;"></div>
				</div>
            </div>
        </div>
        <div style="clear:both;"></div>
		<div id="elx_contentwrapper">
			<div class="elx_inner">
<?php 
			if ($eDoc->countModules('admintop') > 0) {
				echo '<div class="iris_top_mods">'."\n";
				$eDoc->modules('admintop');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}

			$eDoc->component();

			if ($eDoc->countModules('adminbottom') > 0) {
				echo '<div class="iris_bottom_mods">'."\n";
				$eDoc->modules('adminbottom');
				echo "</div>\n";
				echo '<div class="clear;"></div>'."\n";
			} else {
				echo '<div style="clear:both; height:20px;"></div>'."\n";
			}
?>
			</div>
        </div>
		<div id="elx_footerwrapper">
			<div class="elx_copyright">
				Elxis CMS v<?php echo $elxis->getVersion().' '.$elxis->fromVersion('CODENAME'); ?> -  Copyright &#169; 2006-<?php echo date('Y'); ?> 
				<a href="http://www.elxis.org" title="Elxis Open Source CMS" target="_blank">elxis.org</a>
			</div>
		</div>
	</div>
</body>
</html>