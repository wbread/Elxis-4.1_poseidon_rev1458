<?php 
/**
* @version		$Id: translations.db.php 820 2012-01-07 21:17:31Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class translationsDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__translations', 'trid');
		$this->columns = array(
			'trid' => array('type' => 'integer', 'value' => null),
			'category' => array('type' => 'string', 'value' => null),
			'element' => array('type' => 'string', 'value' => null),
			'language' => array('type' => 'string', 'value' => null),
			'elid' => array('type' => 'integer', 'value' => 0),
			'translation' => array('type' => 'text', 'value' => null)
		);
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		$pat = '#[^a-zA-Z0-9\_\-]#';
		$category = preg_replace($pat, '', $this->category);
		$element = preg_replace($pat, '', $this->element);
		$this->elid = (int)$this->elid;
		$language = preg_replace($pat, '', $this->language);
		if (!file_exists(ELXIS_PATH.'/language/'.$language.'/'.$language.'.php')) { $language = ''; }

		if (($category == '') || ($category != $this->category)) {
			$this->errorMsg = 'Translation category is invalid!';
			return false;
		}
		if (($element == '') || ($element != $this->element)) {
			$this->errorMsg = 'Translation element is invalid!';
			return false;
		}
		if (($language == '') || ($language != $this->language)) {
			$this->errorMsg = 'Translation language is invalid!';
			return false;
		}
		if ($this->elid < 1) {
			$this->errorMsg = 'Translations can be added only to saved items!';
			return false;
		}
		if (trim($this->translation) == '') {
			$this->errorMsg = 'No translation given!';
			return false;
		}

		return true;
	}

}

?>