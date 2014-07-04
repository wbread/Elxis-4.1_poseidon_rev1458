<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class categoryContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**************************/
	/* SHOW AN ERROR XML FEED */
	/**************************/
	public function feedError($type, $msg='') {
		$elxis = eFactory::getElxis();
		elxisLoader::loadFile('includes/libraries/elxis/feed.class.php');
		$feed = new elxisFeed($type);
		$feed->setTTL(1440);
		if ($msg == '') { $msg = 'The feed you try to view does not exist or you are not allowed to access it.'; }
		$feed->addChannel($elxis->getConfig('SITENAME'), $elxis->makeURL(''), $msg);
		$feed->makeFeed('show');
	}

}

?>