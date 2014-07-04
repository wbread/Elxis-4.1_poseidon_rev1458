<?php 
/**
* @version		$Id: index.php 818 2012-01-05 13:20:54Z webgift $
* @package		Elxis CMS
* @subpackage	Templates / Aiolos
* @author		Stavros Stratakis ( http://www.webgiftgr.com )
* @copyright	(c) 2009-2013 Webgift web services (http://www.webgiftgr.com). All rights reserved.
* @license		Creative Commons 3.0 Attribution-ShareAlike Unported ( http://creativecommons.org/licenses/by-sa/3.0/ )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
**************************************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

elxisloader::loadFile('templates/aiolos/includes/aiolos.class.php');
$aiolos = new tplaiolos();

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $elxis->secureBase(); ?>/templates/aiolos/css/template<?php echo $eLang->getinfo('RTLSFX'); ?>.css" type="text/css" />
    <?php $aiolos->loadHeader(); ?>
</head>
<body>
	<div class="wrapper">
        <div class="pre-header">
        <div class="header-bottom">
		<div class="header-wrapper">
			<div class="logo_area">
				<h3>
                <a href="<?php echo $elxis->makeURL(); ?>" title="<?php echo $elxis->getConfig('SITENAME'); ?>">
                    <?php $aiolos->changeLogo(); ?>
                </a>
                </h3>
			</div>
			<div class="toparea" >
				<?php $eDoc->modules('language', 'none'); ?>
			</div>
            <?php $eDoc->modules('menu', 'none'); ?>
			<div class="toparea" >
				<?php $eDoc->modules('search', 'none'); ?>
			</div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        </div>
        </div>
        <div class="total-wrapper">
		<div class="content-wrapper">
            <?php $eDoc->pathway(true); ?>
            <div class="main-body">
				<div class="maincontent">
            	<?php 
                if ($eDoc->countModules('top') > 0) {
				echo '<div class="aiolos_top">'."\n";
				$eDoc->modules('top');
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				}?>
			         <?php $eDoc->component(); ?>
		      	</div>
              <div class="leftcolumn">
		          <?php $eDoc->modules('left'); ?>
<?php 
				if ($elxis->getConfig('MOBILE') == 1) {
					echo '<div class="aiolos_mobilever"><a href="'.$elxis->makeURL().'?elxmobile=1" title="'.$eLang->get('SWITCH_MOBILE_VERSION').'">'.$eLang->get('MOBILE_VERSION')."</a></div>\n";
				}
?>
		      </div>
            </div>
            <div class="rightcolumn">
                <?php $eDoc->modules('right'); ?>
                <div class="clear"></div>
            </div>
        </div>
        <div class="clear"></div>
		<?php if ($eDoc->countModules('bottom') > 0) {
			echo '<div class="aiolos_bottom">'."\n";
			$eDoc->modules('bottom');
			echo "</div>\n";
			echo '<div class="clear"></div>'."\n";
		}?>   
        </div>
	</div>
	<div class="footer-wrapper">
        <div class="fixed_width">
            <div class= "content_user1">
                <?php $eDoc->modules('user1', 'none'); ?>
            </div>
            <div class="content_user2">
                <?php $eDoc->modules('user2', 'none'); ?>
            </div>
            <div class="content_user3">
                <?php $eDoc->modules('user3', 'none'); ?>
            </div>
            <div class="content_user4">
                <?php $eDoc->modules('user4', 'none'); ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="bottom-area">
        <div class="fixed_width">
        <?php $eDoc->modules('footer'); ?>
            <div class="footer">
            <div class="gototop"><a href="javascript:void(null);" title="<?php echo $eLang->get('GOTOTOP'); ?>" onclick="gotop();"><img  id="topgohover" src="<?php echo $elxis->secureBase(); ?>/templates/aiolos/images/tophover.png" alt="<?php echo $eLang->get('GOTOTOP'); ?>" onmouseover="tophover(1);" onmouseout="tophover(0);" /></a></div>
                <?php echo $aiolos->hiddendiv(); ?>
                <div class="footertxt">
                <?php $aiolos->footer(); ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>