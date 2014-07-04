<?php 
/**
* @version		$Id: fa.date.php 19 2011-01-18 19:13:58Z datahell $
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


class elxisDatefa implements elxisLocalDate {

	private $j_leap_year_rem = array(1, 5, 9, 13, 17, 22, 26, 30);
	private $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	private $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
	private $j_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
	private $am_pm = array("am" => "ق ظ", "pm" => "ب ظ", "AM" => " ق ظ", "PM" => "ب ظ");


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/******************************/
	/* LANGUAGE SPECIFIC STRFTIME */
	/******************************/
	public function local_strftime($format, $ts) {
		return $this->jalaly_strftime($format, $ts);
	}


	/*************************************************/
	/* CONVERT LOCAL DATETIME (Y-m-d H:i:s) TO ELXIS */
	/*************************************************/
	public function local_to_elxis($date, $offset) {
		$date = $this->latinnum($date);
		$parts = preg_split('/[\s]/', $date, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts || (count($parts) !== 2)) { return ''; }
		$jal_ymd = preg_split('/\-/', $parts[0], -1, PREG_SPLIT_NO_EMPTY);
		if (!$jal_ymd || (count($jal_ymd) !== 3)) { return ''; }
		$gre_ymd = $this->jalali_to_gregorian($jal_ymd[0], $jal_ymd[1], $jal_ymd[2]);
		$date = $gre_ymd[0].'-'.$gre_ymd[1].'-'.$gre_ymd[2].' '.$parts[1];
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
		if ($offset <> 0) {
			$modstr = ($offset > 0) ? '+'.$offset.' seconds' : $offset.' seconds';
			$datetime = new DateTime($date);
			$datetime->modify($modstr);
			$date = $datetime->format('Y-m-d H:i:s');
			unset($datetime);
		}
		$parts = preg_split('/[\s]/', $date, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts || (count($parts) !== 2)) { return ''; }
		$gre_ymd = preg_split('/\-/', $parts[0], -1, PREG_SPLIT_NO_EMPTY);
		if (!$gre_ymd || (count($gre_ymd) !== 3)) { return ''; }
		$jal_ymd = $this->gregorian_to_jalali($gre_ymd[0], $gre_ymd[1], $gre_ymd[2]);
		$date = $this->farsinum($jal_ymd[0].'-'.sprintf("%02d", $jal_ymd[1]).'-'.sprintf("%02d", $jal_ymd[2]).' '.$parts[1]);
		return $date;
	}


	/*******************/
	/* JALALI STRFTIME */
	/*******************/
	private function jalaly_strftime($format, $timestamp) {
		$eDate = eFactory::getDate();
		$format = str_replace('%', '', $format);

		$year = date('Y', $timestamp);
		$month = date('m', $timestamp);
		$day = date('d', $timestamp);
		$hour = date('H', $timestamp);
		$minute = date('i', $timestamp);
		$second = date('s', $timestamp);

		list($jyear, $jmonth, $jday) = $this->gregorian_to_jalali($year, $month, $day);

		$day_of_the_year = $this->getDayofYear($jmonth, $jday);

		$replacements = array();
		$replacements['a'] = $eDate->dayName(date("D", $timestamp), true);
		$replacements['A'] = $eDate->dayName(date("l", $timestamp), false);
		$replacements['b'] = $eDate->monthName($jmonth, true);
		$replacements['B'] = $eDate->monthName($jmonth, false);
		$replacements['c'] = date("c", $timestamp);
		$replacements['C'] = (int) $jyear / 100;
		$replacements['d'] = str_pad($jday, 2, "0", STR_PAD_LEFT);
		$replacements['e'] = str_pad($jday, 2, " ", STR_PAD_LEFT);
		$replacements['h'] = $replacements['b'];
		$replacements['H'] = date("H", $timestamp);
		$replacements['I'] = date("h", $timestamp);
		$replacements['j'] = str_pad($day_of_the_year, 3, "0");
		$replacements['k'] = date("G", $timestamp);
		$replacements['l'] = str_pad(date("g", $timestamp), 2, " ");
		$replacements['m'] = str_pad($jmonth, 2, "0", STR_PAD_LEFT);
		$replacements['M'] = date("i", $timestamp);
		$replacements['n'] = "\n";
		$replacements['p'] = $this->am_pm[date("a", $timestamp)];
		$replacements['S'] = date("s", $timestamp);
		$replacements['t'] = "\t";
		$replacements['T'] = date("%Y-%m-%d %I:%M %p", time());
		$replacements['u'] = date("N", $timestamp);
		$replacements['U'] = ((int)$day_of_the_year / 7) + 1;
		$replacements['V'] = str_pad(((int)$day_of_the_year / 7) + 1, 2, "0");
		$replacements['w'] = date("w", $timestamp);
		$replacements['W'] = $replacements['V'];
		$replacements['y'] = str_pad(substr($jyear, -2), 2, "0");
		$replacements['Y'] = $jyear;
		$replacements['Z'] = date("Z", $timestamp);

		$final = '';
		$characters = preg_split('//', $format);
		if ($characters) {
			foreach ($characters as $c) {
				$final .= isset($replacements[$c]) ? $replacements[$c] : $c;
			}
		}
		return $this->farsinum($final);
	}


	/************************************************/
	/* CONVERT GREGORIAN YEAR, MONTH, DAY TO JALALI */
	/************************************************/
	private function gregorian_to_jalali($g_y, $g_m, $g_d) {
		$gy = $g_y - 1600;
		$gm = $g_m - 1;
		$gd = $g_d - 1;
		$g_day_no = (365 * $gy) + $this->div($gy + 3, 4) - $this->div($gy + 99, 100) + $this->div($gy + 399, 400);

		for ($i=0; $i < $gm; ++$i) {
			$g_day_no += $this->g_days_in_month[$i];
		}
		if (($gm > 1) && (($gy%4 == 0 && $gy%100 != 0) || ($gy%400 == 0))) { //leap and after Feb
			++$g_day_no;
	    }

		$g_day_no += $gd;
		$j_day_no = $g_day_no - 79;

		$j_np = $this->div($j_day_no, 12053); //12053 = 365*33 + 32/4
		$j_day_no %= 12053;

		$jy = 979 + (33 * $j_np) + (4 * $this->div($j_day_no, 1461)); //1461 = 365*4 + 4/4
		$j_day_no %= 1461;

		if ($j_day_no >= 366) {
			$jy += $this->div($j_day_no - 1, 365);
			$j_day_no = ($j_day_no - 1)%365;
		}

		for ($i = 0; $i < 11 && $j_day_no >= $this->j_days_in_month[$i]; ++$i) {
			$j_day_no -= $this->j_days_in_month[$i];
		}
		$jm = $i + 1;
		$jd = $j_day_no + 1;

		return array($jy, $jm, $jd);
	}


	/************************************************/
	/* CONVERT JALALI YEAR, MONTH, DAY TO GREGORIAN */
	/************************************************/
    private function jalali_to_gregorian($j_y, $j_m, $j_d) {
        $j_d = (int) $j_d;
        $j_m = (int) $j_m;
        $j_y = (int) $j_y;

        if ($j_m > 12) {
        	$j_y = $j_y + (floor($j_m/12));
        	$j_m = $j_m % 12;
		}

        if ($j_d < 1) {
			$j_d = 1;
        } elseif ($j_d > $this->j_days_in_month[ $j_m - 1 ]) {
			$j_d = $this->j_days_in_month[ $j_m - 1 ];
		}

        $jy = $j_y - 979; 
        $jm = $j_m - 1;
        $jd = 0;
        $jd = $j_d - 1; 

        $j_day_no = (365 * $jy) + ($this->div($jy, 33) * 8) + $this->div($jy%33 + 3, 4); 
		for ($i=0; $i < $jm; ++$i) {
			$j_day_no += $this->j_days_in_month[$i]; 
		}
        $j_day_no += $jd; 

        $g_day_no = $j_day_no + 79; 

        $gy = 1600 + (400 * $this->div($g_day_no, 146097)); //146097 = 365*400 + 400/4 - 400/100 + 400/400
        $g_day_no = $g_day_no % 146097; 
        
        $leap = true; 
        if ($g_day_no >= 36525) { //36525 = 365*100 + 100/4
			$g_day_no--;
			$gy += 100 * $this->div($g_day_no,  36524); //36524 = 365*100 + 100/4 - 100/100
			$g_day_no = $g_day_no % 36524;
			if ($g_day_no >= 365) {
				$g_day_no++;
			} else {
				$leap = false;
			} 
        }

        $gy += 4 * $this->div($g_day_no, 1461); //1461 = 365*4 + 4/4
        $g_day_no %= 1461; 

		if ($g_day_no >= 366) {
			$leap = false;
			$g_day_no--;
			$gy += $this->div($g_day_no, 365);
			$g_day_no = $g_day_no % 365;
		} 
        
		for ($i = 0; $g_day_no >= $this->g_days_in_month[$i] + ($i == 1 && $leap); $i++) {
			$g_day_no -= $this->g_days_in_month[$i] + ($i == 1 && $leap);
		}
		$gm = $i + 1;
		$gd = $g_day_no + 1;
		return array($gy, $gm, $gd);
	}


	/********************/
	/* GET FARSI NUMBER */
	/********************/
	private function farsinum($str) {
		foreach ($this->j_numbers as $g => $j) {
			$str = eUTF::str_replace($g, $j, $str);
		}
		return $str;
	}


	/********************/
	/* GET LATIN NUMBER */
	/********************/
	private function latinnum($str) {
		foreach ($this->j_numbers as $g => $j) {
			$str = eUTF::str_replace($j, $g, $str);
		}
		return $str;
	}


	private function div($a, $b) {
	   return (int) ($a / $b);
	}


	private function getDayofYear($month, $day) {
		$d = 0;
		for ($i = 0; $i < ($month - 1); $i++) {
			$d += $this->j_days_in_month[$i];
		}
		return $d + $day - 1;
	}

}

?>