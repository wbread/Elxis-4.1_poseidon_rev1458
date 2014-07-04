<?php 
/**
* @version		$Id: date.class.php 119 2011-02-22 20:32:10Z datahell $
* @package		Elxis
* @subpackage	Date
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisDate {

	private $timezone = 'UTC'; //local timezone (system's timezone is always UTC)
	private $offset = 0; //offset in seconds from local timezone to UTC
	private $lang = 'en'; //current language identifier
	private $localInterface = null; //null or language specific date helper interface instance
	private $datetime = null; //system's (UTC) datetime instance
	private $system_ts = 0; //system's timestamp (UTC)
	private $system_date = ''; //system's date (UTC)


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$eLang = eFactory::getLang();
		$this->lang = $eLang->currentLang();

		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/date/'.$this->lang.'.date.php')) {
			include(ELXIS_PATH.'/includes/libraries/elxis/date/date.interface.php');
			include(ELXIS_PATH.'/includes/libraries/elxis/date/'.$this->lang.'.date.php');
			$class = 'elxisDate'.$this->lang;
			if (class_exists($class)) { $this->localInterface = new $class(); }
		}

		$this->timezone = eFactory::getElxis()->getConfig('TIMEZONE');
		$this->datetime = new DateTime(NULL, new DateTimeZone($this->timezone)); //init datetime with local timezone
		$this->offset = $this->datetime->getOffset(); //offset from UTC in seconds
		$this->datetime->setTimezone(new DateTimeZone('UTC')); //switch timezone to UTC
		date_default_timezone_set('UTC');
		$this->system_ts = time();
		$this->system_date = $this->datetime->format('Y-m-d H:i:s');
	}


	/***********************/
	/* SET CUSTOM TIMEZONE */
	/***********************/
	public function setTimezone($tz) {
		$tz = trim($tz);
		if ($tz == '') { return false; }
		if ($tz == $this->timezone) { return true; }
		if (!in_array($tz, DateTimeZone::listIdentifiers())) { return false; }
		$this->timezone = $tz;
		$this->datetime = new DateTime(NULL, new DateTimeZone($this->timezone));
		$this->offset = $this->datetime->getOffset();
		$this->datetime->setTimezone(new DateTimeZone('UTC'));
		return true;
	}


    /********************************************/
    /* FORMAT TIMESTAMP BASED ON CURRENT LOCALE */
    /********************************************/
    public function formatTS($ts, $format='', $local=true) {
    	if (intval($ts) == 0) { $ts = $this->system_ts; }
    	if ($local == true) { $ts = $ts + $this->offset; }
		if ($format == '') { $format = '%Y-%m-%d %H:%M:%S'; }

    	if (ELXIS_OS == 'WIN') {
    		$format = str_replace('%e', '%d', $format);
    	} elseif (ELXIS_OS == 'MAC') {
    		$format = str_replace('%P', '%p', $format);
    	}

		if (is_object($this->localInterface)) {
			return $this->localInterface->local_strftime($format, $ts);
		} else {
			return ($this->lang == 'en') ? strftime($format, $ts) : $this->i18n_strftime($format, $ts);
		}
	}


    /***************************************************/
    /* FORMAT DATE Y-m-d H:i:s BASED ON CURRENT LOCALE */
    /***************************************************/
	public function formatDate($date, $format='%d %B %Y %H:%M', $local=true) {
		if (($date == '') || ($date == 'now')) {
			$ts = $this->system_ts;
		} else {
			$ts = strtotime($date);
		}
		return $this->formatTS($ts, $format, $local);
	}


	/********************************************/
	/* SHOW DATETIME FOR ANY PLACE IN THE WORLD */
	/********************************************/
	public function worldDate($dateUTC, $timezone='UTC', $format='%A %d %B %Y %H:%M:%S') {
		if (($dateUTC == '') || ($dateUTC == 'now')) {
			$ts = $this->system_ts;
		} else {
			$ts = strtotime($dateUTC);
		}

		$dt = new DateTime(null, new DateTimeZone($timezone));
		$ts = $ts + $dt->getOffset();
		unset($dt);

		return $this->formatTS($ts, $format, false);
	}


	/*************************************************/
	/* CONVERT LOCAL DATETIME (Y-m-d H:i:s) TO ELXIS */
	/*************************************************/
	public function localToElxis($date='', $system=false) {
		if (($date == '') || ($date == 'now')) { return $this->system_date; }
		if (($system === false) && is_object($this->localInterface)) {
			return $this->localInterface->local_to_elxis($date, $this->offset);
		}

		if ($this->offset == 0) { return $date; }
		//support for php 5.2+, Y2038 bug bypass ( http://en.wikipedia.org/wiki/Year_2038_problem )
		$modstr = ($this->offset > 0) ? '-'.$this->offset.' seconds' : '+'.abs($this->offset).' seconds';
		$datetime = new DateTime($date);
		$datetime->modify($modstr);
		return $datetime->format('Y-m-d H:i:s');
	}


	/*************************************************/
	/* CONVERT ELXIS DATETIME (Y-m-d H:i:s) TO LOCAL */
	/*************************************************/
	public function elxisToLocal($date='', $system=false) {
		if (($date == '') || ($date == 'now')) { $date = $this->system_date; }
		if (($system === false) && is_object($this->localInterface)) {
			return $this->localInterface->elxis_to_local($date, $this->offset);
		}
		if ($this->offset == 0) { return $date; }
		//support for php 5.2+, Y2038 bug bypass ( http://en.wikipedia.org/wiki/Year_2038_problem )
		$modstr = ($this->offset > 0) ? '+'.$this->offset.' seconds' : $this->offset.' seconds';
		$datetime = new DateTime($date);
		$datetime->modify($modstr);
		return $datetime->format('Y-m-d H:i:s');
	}


	/*****************/
	/* I18N STRFTIME */
	/*****************/
	private function i18n_strftime($format, $ts) {
		if (strpos($format, '%a') !== false) {
			$format = str_replace('%a', $this->dayName(gmdate('w', $ts), true), $format);
		}
		if (strpos($format, '%A') !== false) {
			$format = str_replace('%A', $this->dayName(gmdate('w', $ts), false), $format);
		}
		if (strpos($format, '%b') !== false) {
			$format = str_replace('%b', $this->monthName(gmdate('n', $ts), true), $format);
		}
		if (strpos($format, '%B') !== false) {
			$format = str_replace('%B', $this->monthName(gmdate('n', $ts), false), $format);
		}
		$date = strftime($format, $ts);
		return $date;
	}


	/*****************************/
	/* GET FULL/SHORT MONTH NAME */
	/*****************************/
	public function monthName($month, $short=false) {
		$eLang = eFactory::getLang();
		switch (intval($month)) {
			case 1: return $short ? $eLang->get('JANUARY_SHORT') : $eLang->get('JANUARY'); break;
			case 2: return $short ? $eLang->get('FEBRUARY_SHORT') : $eLang->get('FEBRUARY'); break;
			case 3: return $short ? $eLang->get('MARCH_SHORT') : $eLang->get('MARCH'); break;
			case 4: return $short ? $eLang->get('APRIL_SHORT') : $eLang->get('APRIL'); break;
			case 5: return $short ? $eLang->get('MAY_SHORT') : $eLang->get('MAY'); break;
			case 6: return $short ? $eLang->get('JUNE_SHORT') : $eLang->get('JUNE'); break;
			case 7: return $short ? $eLang->get('JULY_SHORT') : $eLang->get('JULY'); break;
			case 8: return $short ? $eLang->get('AUGUST_SHORT') : $eLang->get('AUGUST'); break;
			case 9: return $short ? $eLang->get('SEPTEMBER_SHORT') : $eLang->get('SEPTEMBER'); break;
			case 10: return $short ? $eLang->get('OCTOBER_SHORT') : $eLang->get('OCTOBER'); break;
			case 11: return $short ? $eLang->get('NOVEMBER_SHORT') : $eLang->get('NOVEMBER'); break;
			case 12: return $short ? $eLang->get('DECEMBER_SHORT') : $eLang->get('DECEMBER'); break;
			default: return ''; break;
		}
	}


	/***************************/
	/* GET FULL/SHORT DAY NAME */
	/***************************/
	public function dayName($day, $short=false) {
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


	/**************************************************/
	/* GET CURRENT PUBLIC TIMEZONE (SITE'S OR USER'S) */
	/**************************************************/
	public function getTimezone() {
		return $this->timezone;
	}


	/*****************************************************/
	/* GET CURRENT PUBLIC TIMEZONE'S OFFSET (IN SECONDS) */
	/*****************************************************/
	public function getOffset() {
		return $this->offset;
	}


	/**********************************/
	/* GET CURRENT SYSTEM'S TIMESTAMP */
	/**********************************/
	public function getTS() {
		return $this->system_ts;
	}


	/*********************************/
	/* GET CURRENT SYSTEM'S DATETIME */
	/*********************************/
	public function getDate() {
		return $this->system_date;
	}


	/*****************************************************/
	/* CONVERT A DATE'S FORMAT WITHOUT CHANGING THE DATE */
	/*****************************************************/
	public function convertFormat($date, $informat, $outformat='Y-m-d H:i:s') {
		$date = trim($date);
		if ($date == '') { return false; }
		if ($informat == $outformat) { return $date; }
		$date = str_replace('/', '-', $date);
		$informat = str_replace('/', '-', $informat);

		$parts = preg_split('#[\s-]+#', $date);
		if (!$parts || (count($parts) < 3) || (count($parts) > 4)) { return false; }
		switch ($informat) {
			case 'Y-m-d': $yi = 0; $mi = 1; $di = 2; break;
			case 'm-d-Y': $yi = 2; $mi = 0; $di = 1; break;
			case 'd-m-Y': $yi = 2; $mi = 1; $di = 0; break;
			case 'Y-m-d H:i:s': $yi = 0; $mi = 1; $di = 2; break;
			case 'm-d-Y H:i:s': $yi = 2; $mi = 0; $di = 1; break;
			case 'd-m-Y H:i:s': $yi = 2; $mi = 1; $di = 0; break;
			default: return false; break; //not supported format
		}

		$year = (int)$parts[$yi];
		$month = (int)$parts[$mi];
		$day = (int)$parts[$di];
		if (!checkdate($month, $day, $year)) { return false; }
		$sep = (strpos($outformat, '-') !== false) ? '-' : '/';
		$year = sprintf("%02d", $year);
		$month = sprintf("%02d", $month);
		$day = sprintf("%02d", $day);
		switch ($outformat) {
			case 'Y-m-d': case 'Y/m/d': $out = $year.$sep.$month.$sep.$day; break;
			case 'm-d-Y': case 'm/d/Y': $out = $month.$sep.$day.$sep.$year; break;
			case 'd-m-Y': case 'd/m/Y': $out = $day.$sep.$month.$sep.$year; break;
			case 'Y-m-d H:i:s': case 'Y/m/d H:i:s':
				$out = $year.$sep.$month.$sep.$day;
				$out .= (!isset($parts[3]) || (strlen($parts[3]) != 8)) ? ' 00:00:00' : ' '.$parts[3];
			break;
			case 'm-d-Y H:i:s': case 'm/d/Y H:i:s':
				$out = $month.$sep.$day.$sep.$year;
				$out .= (!isset($parts[3]) || (strlen($parts[3]) != 8)) ? ' 00:00:00' : ' '.$parts[3];
			break;
			case 'd-m-Y H:i:s': case 'd/m/Y H:i:s':
				$out = $day.$sep.$month.$sep.$year;
				$out .= (!isset($parts[3]) || (strlen($parts[3]) != 8)) ? ' 00:00:00' : ' '.$parts[3];
			break;
			default: return false; break; //not supported format
		}

		return $out;
	}

}

?>