<?php 
/**
* @version		$Id: comments.db.php 738 2011-11-13 12:56:05Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class commentsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__comments', 'id');
		$this->columns = array(
			'id' => array('type' => 'integer', 'value' => null),
			'element' => array('type' => 'string', 'value' => null),
			'elid' => array('type' => 'integer', 'value' => 0),
			'message' => array('type' => 'text', 'value' => null),
			'created' => array('type' => 'string', 'value' => null),
			'uid' => array('type' => 'integer', 'value' => 0),
			'author' => array('type' => 'string', 'value' => 'Anonymus'),
			'email' => array('type' => 'string', 'value' => null),
			'published' => array('type' => 'integer', 'value' => 0)
		);

		$eDate = eFactory::getDate();
		$this->setValue('created', $eDate->getDate());
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$this->element = trim($this->element);
		if ($this->element == '') {
			$this->errorMsg = 'Element can not be empty!'; 
			return false;
		}
		$this->elid = (int)$this->elid;
		if ($this->elid < 1) {
			$this->errorMsg = 'Element id can not be empty!';
			return false;
		}
		$this->email = trim($this->email);
		if ($this->email == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('EMAIL'));
			return false;
		}
		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->errorMsg = eFactory::getLang()->get('INVALIDEMAIL');
			return false;
		}

		$this->uid = (int)$this->uid;
		$this->published = (int)$this->published;

		$pat = "#([\"]|[\']|[\$]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		$this->message = eUTF::trim(preg_replace($pat, '', $this->message));
		if ($this->message == '') {
			$this->errorMsg = 'You must write a comment!';
			return false;
		}

		$this->author = filter_var($this->author, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->author = eUTF::trim(preg_replace($pat, '', $this->author));
		if ($this->author == '') {
			$this->errorMsg = 'You must write a comment!';
			return false;
		}

		return true;
	}

}

?>