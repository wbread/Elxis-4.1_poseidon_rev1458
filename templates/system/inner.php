<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Templates / System
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


$eLang = eFactory::getLang();
$eDoc = eFactory::getDocument();
$elxis = eFactory::getElxis();

if (defined('ELXIS_ADMIN')) {
	$cssbase = $elxis->secureBase().'/templates/admin/'.$elxis->getConfig('ATEMPLATE');
} else {
	$cssbase = $elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE');
}

echo $eDoc->getDocType()."\n";
?>
<html<?php echo $eDoc->htmlAttributes(); ?>>
<head>
	<?php $eDoc->showHead(); ?>
	<link rel="stylesheet" href="<?php echo $cssbase; ?>/css/template<?php echo $eLang->getinfo('RTLSFX'); ?>.css" type="text/css" />
</head>
<body class="innerpage" id="innerpage">
	<?php $eDoc->component(); ?>
</body>
</html>