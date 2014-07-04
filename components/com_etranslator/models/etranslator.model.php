<?php 
/**
* @version		$Id: etranslator.model.php 1137 2012-05-17 19:58:22Z datahell $
* @package		Elxis
* @subpackage	Component Translator
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class etranslatorModel {

	private $db;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->db = eFactory::getDB();
	}


	/***************************/
	/* GET INSTALLED LANGUAGES */
	/***************************/
	public function getInstalledLangs() {
		$installed = eFactory::getFiles()->listFolders('language/');
		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		$ilangs = array();
		foreach ($installed as $lng) {
			if (!isset($langdb[$lng])) { continue; }
			$ilangs[$lng] = array_change_key_case($langdb[$lng], CASE_LOWER);
			$ilangs[$lng]['identifier'] = $lng;
		}
		return $ilangs;
	}


	/******************************/
	/* GET TRANSLATION CATEGORIES */
	/******************************/
	public function getTransCategories() {
		$sql = "SELECT ".$this->db->quoteId('category')." FROM ".$this->db->quoteId('#__translations')
		."\n GROUP BY ".$this->db->quoteId('category')." ORDER BY ".$this->db->quoteId('category')." ASC";
		$stmt = $this->db->prepare($sql);
		$stmt->execute();
		$categories = $stmt->fetchCol(0);
		return $categories;
	}


	/**********************/
	/* COUNT TRANSLATIONS */
	/**********************/
	public function countTranslations($options) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['category']) && ($options['category'] != '')) {
			$wheres[] = $this->db->quoteId('category').' = :xcat';
			$pdo_binds[':xcat'] = array($options['category'], PDO::PARAM_STR);
		}
		if (isset($options['element']) && ($options['element'] != '')) {
			$wheres[] = $this->db->quoteId('element').' = :xelem';
			$pdo_binds[':xelem'] = array($options['element'], PDO::PARAM_STR);
		}
		if (isset($options['language']) && ($options['language'] != '')) {
			$wheres[] = $this->db->quoteId('language').' = :xlng';
			$pdo_binds[':xlng'] = array($options['language'], PDO::PARAM_STR);
		}
		if (isset($options['query']) && ($options['query'] > 0)) {
			$wheres[] = $this->db->quoteId('elid').' = :xelid';
			$pdo_binds[':xelid'] = array(intval($options['query']), PDO::PARAM_INT);
		}

		$sql = "SELECT COUNT(trid) FROM ".$this->db->quoteId('#__translations');
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$stmt = $this->db->prepare($sql);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$stmt = $this->db->prepare($sql);
		}
		$stmt->execute();
		return (int)$stmt->fetchResult();
	}


	/**********************************/
	/* GET TRANSLATIONS FROM DATABASE */
	/**********************************/
	public function getTranslations($options, $with_original=false) {
		$wheres = array();
		$pdo_binds = array();
		if (isset($options['category']) && ($options['category'] != '')) {
			$wheres[] = $this->db->quoteId('category').' = :xcat';
			$pdo_binds[':xcat'] = array($options['category'], PDO::PARAM_STR);
		}
		if (isset($options['element']) && ($options['element'] != '')) {
			$wheres[] = $this->db->quoteId('element').' = :xelem';
			$pdo_binds[':xelem'] = array($options['element'], PDO::PARAM_STR);
		}
		if (isset($options['language']) && ($options['language'] != '')) {
			$wheres[] = $this->db->quoteId('language').' = :xlng';
			$pdo_binds[':xlng'] = array($options['language'], PDO::PARAM_STR);
		}
		if (isset($options['query']) && ($options['query'] > 0)) {
			$wheres[] = $this->db->quoteId('elid').' = :xelid';
			$pdo_binds[':xelid'] = array(intval($options['query']), PDO::PARAM_INT);
		}

		$sql = "SELECT * FROM #__translations";
		if (count($wheres) > 0) {
			$sql .= ' WHERE '.implode(' AND ', $wheres);
			$sql .= ' ORDER BY '.$options['sortname'].' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
			if (count($pdo_binds) > 0) {
				foreach ($pdo_binds as $key => $parr) {
					$stmt->bindParam($key, $parr[0], $parr[1]);
				}
			}
		} else {
			$sql .= ' ORDER BY '.$options['sortname'].' '.strtoupper($options['sortorder']);
			$stmt = $this->db->prepareLimit($sql, $options['limitstart'], $options['rp']);
		}
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($with_original) {
			$rows = $this->bindOriginalText($rows);
		}

		return $rows;
	}


	/**********************************************/
	/* BIND TO TRANSLATION ROWS THE ORIGINAL TEXT */
	/**********************************************/
	private function bindOriginalText($rows) {
		if (!$rows) { return $rows; }
		foreach ($rows as $i => $row) {
			$sql = '';
			$original_text = '';
			switch ($row['category']) {
				case 'com_content':
					switch ($row['element']) {
						case 'category_title':
							$sql = "SELECT ".$this->db->quoteId('title')." FROM #__categories WHERE ".$this->db->quoteId('catid').' = :xval';
						break;
						case 'title':
						case 'subtitle':
						case 'caption':
						case 'metakeys':
							$sql = "SELECT ".$this->db->quoteId($row['element'])." FROM #__content WHERE ".$this->db->quoteId('id').' = :xval';
						break;
						default: break;
					}
				break;
				case 'com_emenu':
					if ($row['element'] == 'title') {
						$sql = "SELECT ".$this->db->quoteId('title')." FROM #__menu WHERE ".$this->db->quoteId('menu_id').' = :xval';
					}
				break;
				case 'module':
					if ($row['element'] == 'title') {
						$sql = "SELECT ".$this->db->quoteId('title')." FROM #__modules WHERE ".$this->db->quoteId('id').' = :xval';
					}
				break;
				case 'config':
					$key = strtoupper($row['element']);
					if (in_array($key, array('SITENAME', 'METADESC', 'METAKEYS'))) {
						$original_text = eFactory::getElxis()->getConfig($key);
					}
				break;
				default: break;
			}
			
			if ($sql != '') {
				$stmt = $this->db->prepareLimit($sql, 0, 1);
				$stmt->bindParam(':xval', $row['elid'], PDO::PARAM_INT);
				$stmt->execute();
				$rows[$i]['original_text'] = $stmt->fetchResult();
			} elseif ($original_text != '') {
				$rows[$i]['original_text'] = $original_text;
			} else {
				$rows[$i]['original_text'] = '';
			}
		}

		return $rows;
	}


	/*****************************/
	/* GET ORIGINAL TEXT FROM DB */
	/*****************************/
	public function getOriginal($tbl, $idcol, $textcol, $id) {
		$sql = "SELECT ".$this->db->quoteId($textcol)." FROM ".$this->db->quoteId($tbl)." WHERE ".$this->db->quoteId($idcol)." = :xid";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xid', $id, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchResult();
	}


	/*********************************************************/
	/* GET THE CURRENTLY TRANSLATED LANGUAGES FOR AN ELEMENT */
	/*********************************************************/
	public function getCTLangs($ctg, $elem, $elid) {
		$sql = "SELECT ".$this->db->quoteId('language')." FROM ".$this->db->quoteId('#__translations')
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xelem"
		."\n AND ".$this->db->quoteId('elid')." = :xid";
		$stmt = $this->db->prepare($sql);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $elem, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $elid, PDO::PARAM_INT);
		$stmt->execute();
		return $stmt->fetchCol();
	}


	/**************************************/
	/* GET SINGLE TRANSLATION FOR THE API */
	/**************************************/
	public function getTranslation($ctg, $elem, $elid, $lng) {
		$sql = "SELECT ".$this->db->quoteId('trid').", ".$this->db->quoteId('translation')." FROM #__translations"
		."\n WHERE ".$this->db->quoteId('category')." = :xcat AND ".$this->db->quoteId('element')." = :xelem"
		."\n AND ".$this->db->quoteId('elid')." = :xid AND ".$this->db->quoteId('language')." = :xlng";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcat', $ctg, PDO::PARAM_STR);
		$stmt->bindParam(':xelem', $elem, PDO::PARAM_STR);
		$stmt->bindParam(':xid', $elid, PDO::PARAM_INT);
		$stmt->bindParam(':xlng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}


	/******************************/
	/* GET MICROSOFT BING API KEY */
	/******************************/
	public function getBingAPI() {
		$bingapi = '';
		$component = 'com_etranslator';
		$sql = "SELECT ".$this->db->quoteId('params')." FROM ".$this->db->quoteId('#__components')." WHERE ".$this->db->quoteId('component')." = :xcomp";
		$stmt = $this->db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcomp', $component, PDO::PARAM_STR);
		$stmt->execute();
		$params_str = $stmt->fetchResult();
		if ($params_str) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
			$bingapi = trim($params->get('bingapi', ''));
			unset($params);
		}
		return $bingapi;
	}

}

?>