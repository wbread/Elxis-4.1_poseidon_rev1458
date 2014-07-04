<?php 
/**
* @version		$Id: el.date.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @subpackage	Date
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisDateel implements elxisLocalDate {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/******************************/
	/* LANGUAGE SPECIFIC STRFTIME */
	/******************************/
	public function local_strftime($format, $ts) {
		if (strpos($format, '%a') !== false) {
			$format = str_replace('%a', $this->greekDayName(gmdate('w', $ts), true), $format);
		}
		if (strpos($format, '%A') !== false) {
			$format = str_replace('%A', $this->greekDayName(gmdate('w', $ts), false), $format);
		}
		if (strpos($format, '%b') !== false) {
			$format = str_replace('%b', $this->greekMonthName(gmdate('n', $ts), true), $format);
		}
		if (strpos($format, '%B') !== false) {
			$format = str_replace('%B', $this->greekMonthName(gmdate('n', $ts), false), $format);
		}
		$date = strftime($format, $ts);
		return $date;
	}


	/*************************************************/
	/* CONVERT LOCAL DATETIME (Y-m-d H:i:s) TO ELXIS */
	/*************************************************/
	public function local_to_elxis($date, $offset) {
		if ($offset == 0) { return $date; }
		$modstr = ($offset > 0) ? '-'.$offset.' seconds' : '+'.abs($offset).' seconds';
		$datetime = new DateTime($date);
		$datetime->modify($modstr);
		return $datetime->format('Y-m-d H:i:s');
	}


	/*************************************************/
	/* CONVERT ELXIS DATETIME (Y-m-d H:i:s) TO LOCAL */
	/*************************************************/
	public function elxis_to_local($date, $offset) {
		if ($offset == 0) { return $date; }
		$modstr = ($offset > 0) ? '+'.$offset.' seconds' : $offset.' seconds';
		$datetime = new DateTime($date);
		$datetime->modify($modstr);
		return $datetime->format('Y-m-d H:i:s');
	}


	/*****************************/
	/* GET FULL/SHORT MONTH NAME */
	/*****************************/
	private function greekMonthName($month, $short=false) {
		$eLang = eFactory::getLang();
		switch (intval($month)) {
			case 1: return $short ? $eLang->get('JANUARY_SHORT') : 'Ιανουαρίου'; break;
			case 2: return $short ? $eLang->get('FEBRUARY_SHORT') : 'Φεβρουαρίου'; break;
			case 3: return $short ? $eLang->get('MARCH_SHORT') : 'Μαρτίου'; break;
			case 4: return $short ? $eLang->get('APRIL_SHORT') : 'Απριλίου'; break;
			case 5: return $short ? $eLang->get('MAY_SHORT') : 'Μαϊου'; break;
			case 6: return $short ? $eLang->get('JUNE_SHORT') : 'Ιουνίου'; break;
			case 7: return $short ? $eLang->get('JULY_SHORT') : 'Ιουλίου'; break;
			case 8: return $short ? $eLang->get('AUGUST_SHORT') : 'Αυγούστου'; break;
			case 9: return $short ? $eLang->get('SEPTEMBER_SHORT') : 'Σεπτεμβρίου'; break;
			case 10: return $short ? $eLang->get('OCTOBER_SHORT') : 'Οκτωβρίου'; break;
			case 11: return $short ? $eLang->get('NOVEMBER_SHORT') : 'Νοεμβρίου'; break;
			case 12: return $short ? $eLang->get('DECEMBER_SHORT') : 'Δεκεμβρίου'; break;
			default: return ''; break;
		}
	}


	/***************************/
	/* GET FULL/SHORT DAY NAME */
	/***************************/
	private function greekDayName($day, $short=false) {
		$eLang = eFactory::getLang();
		switch (intval($day)) {
			case 0: return $short ? $eLang->get('SUNDAY_SHORT') : $eLang->get('SUNDAY'); break;
			case 1: return $short ? $eLang->get('MONDAY_SHORT') : $eLang->get('MONDAY'); break;
			case 2: return $short ? $eLang->get('THUESDAY_SHORT') : $eLang->get('THUESDAY'); break;
			case 3: return $short ? $eLang->get('WEDNESDAY_SHORT') : $eLang->get('WEDNESDAY'); break;
			case 4: return $short ? $eLang->get('THURSDAY_SHORT') : $eLang->get('THURSDAY'); break;
			case 5: return $short ? $eLang->get('FRIDAY_SHORT') : $eLang->get('FRIDAY'); break;
			case 6: return $short ? $eLang->get('SATURDAY_SHORT') : $eLang->get('SATURDAY'); break;
			default: return ''; break;
		}
	}

}

?>