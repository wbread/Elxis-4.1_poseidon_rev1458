<?php 
/**
* @version		$Id: index.php 1439 2013-05-07 10:37:40Z datahell $
* @package		Elxis CMS
* @subpackage	Templates / Delta
* @author		Ioannis Sannos ( http://www.isopensource.com )
* @copyright	Copyright (c) 2008-2012 Is Open Source (http://www.isopensource.com). All rights reserved.
* @license		Creative Commons 3.0 Attribution-ShareAlike Unported ( http://creativecommons.org/licenses/by-sa/3.0/ )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

elxisloader::loadFile('templates/delta/includes/delta.class.php');
$delta = new templateDelta();

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase(); ?>/templates/delta/css/template<?php echo $eLang->getinfo('RTLSFX'); ?>.css" type="text/css" />
    <?php $delta->addHead(); ?>
</head>
<body>
	<div class="delta_wrapper">
		<div class="delta_page">
			<div class="delta_head">
				<div class="delta_head_logo">
					<?php $delta->showLogo(); ?>
				</div>
				<div class="delta_head_position">
					<div class="delta_pad5">
						<?php $eDoc->modules('language', 'none'); ?>
					</div>
				</div>
				<div class="delta_head_position">
					<div class="delta_pad5">
						<?php $eDoc->modules('search', 'none'); ?>
					</div>
				</div>
				<div class="clear"></div>
				<div class="delta_menu">
					<?php $eDoc->modules('menu', 'none'); ?>
				</div>
<?php 
			if ($delta->showPathway() == true) {
				$yahere = $delta->youAreHere();
				echo '<div class="delta_pathway">'."\n";
				$eDoc->pathway($yahere);
				echo "</div>\n";
				unset($yahere);
			}
?>
				<div class="clear"></div>
			</div>
			<div class="delta_main">
<?php 
			if ($eDoc->countModules('top') > 0) {
				echo '<div class="delta_top">'."\n";
				$eDoc->modules('top');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}

			if ($delta->showColumn() == true) {
				echo '<div class="delta_maincol">'."\n";
				$eDoc->component();
				echo "</div>\n";
				echo '<div class="delta_sidecol">'."\n";
				$eDoc->modules('left');
				$eDoc->modules('right');
				if ($elxis->getConfig('MOBILE') == 1) {
					echo '<div class="delta_mobilever"><a href="'.$elxis->makeURL().'?elxmobile=1" title="'.$eLang->get('SWITCH_MOBILE_VERSION').'">'.$eLang->get('MOBILE_VERSION')."</a></div>\n";
				}
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			} else {
				$eDoc->component();
				echo '<div class="clear"></div>'."\n";
			}

			if ($eDoc->countModules('bottom') > 0) {
				echo '<div class="delta_bottom">'."\n";
				$eDoc->modules('bottom');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
			}
?>
			</div>
			<div class="delta_footer">
<?php 
				$delta->footerBoxes();
				echo '<div class="clear"></div>'."\n";
				if ($eDoc->countModules('footer') > 0) {
					echo '<div class="delta_footer_menu">'."\n";
					$eDoc->modules('footer', 'none');
					echo "</div>\n";
					echo '<div class="clear"></div>'."\n";
				}
				//You are allowed to remove/edit the copyright note bellow and add yours.
?>
				<div class="delta_footer_copy">
					Powered by <a href="http://www.elxis.org/" title="Elxis CMS">Elxis open source CMS</a> - Designed by 
					<a href="http://www.isopensource.com/" title="Is Open Source">Ioannis Sannos</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>