<?php 
/**
* @version		$Id: wrapper.model.php 683 2011-10-21 18:38:02Z datahell $
* @package		Elxis
* @subpackage	Component Wrapper
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class wrapperModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/*****************************/
	/* GET MENU ITEM INFORMATION */
	/*****************************/
	public function getMenuItem($menu_id) {
		$section = 'frontend';
		$type = 'wrapper';
		$published = 1;

		$sql = "SELECT * FROM ".$this->db->quoteId('#__menu')
		."\n WHERE ".$this->db->quoteId('menu_id')." = :xmenuid AND ".$this->db->quoteId('section')." = :xsection"
		."\n AND ".$this->db->quoteId('menu_type')." = :xmtype AND ".$this->db->quoteId('published')." = :xpub";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xmenuid', $menu_id, PDO::PARAM_INT);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->bindParam(':xmtype', $type, PDO::PARAM_STR);
		$stmt->bindParam(':xpub', $published, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ);
	}


	/*****************************/
	/* GET MENU ITEM TRANSLATION */
	/*****************************/
	public function menuTranslate($mid, $lng) {
		$xcat = 'com_emenu';
		$xelem = 'title';
		$sql = "SELECT ".$this->db->quoteId('translation')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('language')." = :xlang"
		."\n AND ".$this->db->quoteId('element')." = :xelem AND ".$this->db->quoteId('elid')." = :xmid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcat', $xcat, PDO::PARAM_STR);
		$stmt->bindParam(':xlang', $lng, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $xelem, PDO::PARAM_STR);
		$stmt->bindParam(':xmid', $mid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchResult();
	}

}

?>