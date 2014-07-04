<?php 
/**
* @version		$Id: avatar.helper.php 1200 2012-06-21 19:09:54Z datahell $
* @package		Elxis
* @subpackage	Helpers / User Avatar
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisAvatarHelper {
	
	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}


	/******************************/
	/* GET USER'S AVATAR FULL URL */
	/******************************/
	public function getAvatar($avatar='', $size=80, $use_gravatar=0, $email='') {
		$relpath = 'media/images/avatars/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
		}

		if ((trim($avatar) != '') && file_exists(ELXIS_PATH.'/'.$relpath.$avatar)) {
			$out = eFactory::getElxis()->secureBase().'/'.$relpath.$avatar;
		} elseif ((trim($avatar) != '') && preg_match('#^(http(s)?\:\/\/)#', $avatar)) {
			$out = $avatar;
		} elseif ($use_gravatar && (trim($email) != '')) {
			$size = (int)$size;
			if ($size < 10) { $size = 80; }			
			if (eFactory::getURI()->detectSSL() === true) {
				$out = 'https://secure.gravatar.com/avatar/'.md5(strtolower($email)).'?s='.$size;
			} else {
				$out = 'http://www.gravatar.com/avatar/'.md5(strtolower($email)).'?s='.$size;
			}
		} else {
			$out = eFactory::getElxis()->secureBase().'/components/com_user/images/noavatar.png';
		}
		return $out;
	}

}

?>