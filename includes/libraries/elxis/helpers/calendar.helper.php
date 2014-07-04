<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / Calendar
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCalendarHelper {

	private $options = array();
	private $idx = 1;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/***********************/
	/* SET CALENDAR OPTION */
	/***********************/
	public function setOption($option, $value) {
		switch ($option) {
			case 'reverseWheel': $this->options[$option] = (bool)$value; break; //default: false
			case 'animation': $this->options[$option] = (bool)$value; break; //default: true
			case 'cont': $this->options[$option] = trim($value); break; //value: container element id (for inline display)
			case 'bottomBar': $this->options[$option] = (bool)$value; break; //default: true
			case 'fdow': $this->options[$option] = (int)$value; break; //first day of the week, values: 0, 1, ..., 6
			case 'min': $this->options[$option] = trim($value); break; //value: YYMMDD
			case 'max': $this->options[$option] = trim($value); break; //value: YYMMDD
			case 'weekNumbers': $this->options[$option] = (bool)$value; break; //default: false
			case 'checkRange': $this->options[$option] = (bool)$value; break; //default: false
			case 'align': $this->options[$option] = trim($value); break; //default: empty
			case 'opacity': $this->options[$option] = (int)$value; break; //default: 1, values: 0, 1, 2, 3
			case 'titleFormat': $this->options[$option] = trim($value); break; //default: %b %Y
			case 'showTime': //default: false, values: false, 12, 24 (boolean or integer)
				$this->options[$option] = is_bool($value) ? (bool)$value : (int)$value; 
			break;
			case 'timePos': $this->options[$option] = trim($value); break; //default: right, values: left, right
			case 'time': $this->options[$option] = (int)$value; break; //default: auto, values: HHMM (24h clock)
			case 'minuteStep': $this->options[$option] = (int)$value; break; //default: 5
			case 'fixed': $this->options[$option] = (bool)$value; break; //default: false
			case 'noScroll': $this->options[$option] = (bool)$value; break; //default: false
			case 'inputField': $this->options[$option] = trim($value); break; //value: input element id
			case 'trigger': $this->options[$option] = trim($value); break; //value: trigger element id
			case 'dateFormat': $this->options[$option] = trim($value); break; //value: input date format
			case 'date': $this->options[$option] = (int)$value; break; //value: YYMMDD
			case 'selectionType': $this->options[$option] = trim($value); break; //value: Calendar.SEL_SINGLE or Calendar.SEL_MULTIPLE
			case 'selection': $this->options[$option] = trim($value); break; //value: js array of YYMMDD
			case 'onSelect':
			case 'onChange':
			case 'onTimeChange':
			case 'disabled':
			case 'dateInfo':
			case 'onFocus':
			case 'onBlur':
				$this->options[$option] = trim($value);
			break; //value: js callback
			default: break;
		}
	}


	/************************/
	/* SET CALENDAR OPTIONS */
	/************************/
	public function setOptions($options) {
		if (is_array($options) && (count($options) > 0)) {
			foreach ($options as $option => $value) {
				$this->setOption($option, $value);
			}
		}
	}


	/***********************************************/
	/* ADD REQUIRED JAVASCRIPT AND CSS TO DOCUMENT */
	/***********************************************/
	private function prepareCalendar() {
		if (defined('ELXIS_CALENDAR')) { return; }
		$eDoc = eFactory::getDocument();
		$caldir = eFactory::getElxis()->secureBase().'/includes/js/calendar';
		$eDoc->addLibrary('calendar', $caldir.'/calendar.js', '1.9');
		$lang = eFactory::getLang()->currentLang();
		if (!file_exists(ELXIS_PATH.'/includes/js/calendar/lang/'.$lang.'.js')) { $lang = 'en'; }
		$eDoc->addScriptLink($caldir.'/lang/'.$lang.'.js');
		$eDoc->addStyleLink($caldir.'/css/calendar.css', 'text/css', 'all');
		$eDoc->addStyleLink($caldir.'/css/border-radius.css', 'text/css', 'all');
		$eDoc->addStyleLink($caldir.'/css/steel/steel.css', 'text/css', 'all');
		define('ELXIS_CALENDAR', 1);
	}


	/**************************************************************/
	/* CONVERT A PHP FORMATTED DATE TO CALENDAR COMPATIBLE FORMAT */
	/**************************************************************/
	private function convertFormat($format) {
		$format = str_replace('i', 'M', $format);
		$format = str_replace('s', 'S', $format);
		$format = str_replace('Y-m-d', '%Y-%m-%d', $format);
		$format = str_replace('Y/m/d', '%Y/%m/%d', $format);
		$format = str_replace('d-m-Y', '%d-%m-%Y', $format);
		$format = str_replace('d/m/Y', '%d/%m/%Y', $format);
		$format = str_replace('m-d-Y', '%m-%d-%Y', $format);
		$format = str_replace('m/d/Y', '%m/%d/%Y', $format);
		$format = str_replace('H:M:S', '%H:%M:%S', $format);
		$format = str_replace('H:M', '%H:%M', $format);
		return $format;
	}


	/**************************/
	/* MAKE CALENDAR INSTANCE */
	/**************************/
	public function makeCalendar($elementid, $triggerid, $format='', $idx=0, $options=array(), $with_input=false, $element_name='', $initial_date='') {
		if (trim($format) == '') {
			$format = (strlen($initial_date) == 19) ? eFactory::getLang()->get('DATE_FORMAT_BOX_LONG') : eFactory::getLang()->get('DATE_FORMAT_BOX');
		}
		$boxtitle = $format.' ('.eFactory::getDate()->getTimezone().')';
		$format = $this->convertFormat($format);

		if (intval($idx) > 0) { $this->idx = (int)$idx; } else { $this->idx = rand(1, 100); }
		if (is_array($options) && (count($options) > 0)) { $this->setOptions($options); }

		$this->prepareCalendar();

		$boxsize = 10;
		if (!isset($this->options['onSelect'])) { $this->setOption('onSelect', 'function(cal'.$this->idx.') { this.hide() }'); }
		if (strpos($format, 'H') !== false) { $this->setOption('showTime', 24); $boxsize = 19; }

		$n = count($this->options);

		$js = '';
		if ($with_input === true) {
			if ($element_name == '') { $element_name = $elementid; }
			$img = eFactory::getElxis()->icon('calendar', 16);
			$js .= '<input type="text" name="'.$element_name.'" id="'.$elementid.'" value="'.$initial_date.'" size="'.$boxsize.'" class="inputbox" title="'.$boxtitle.'" />'."\n";
			$js .= '<img src="'.$img.'" id="'.$triggerid.'" alt="calendar" border="0" align="top" style="margin:2px 2px 0 2px;" />'."\n";
		}

		$js .= '<script type="text/javascript">'."\n";
		$js .= '/* <![CDATA[ */'."\n";
		$js .= 'var cal'.$this->idx.' = Calendar.setup({'."\n";
		if ($n > 0) {
			$i = 1;
			foreach ($this->options as $option => $value) {
				$js .= "\t".$option.': ';
				if (is_int($value)) {
					$js .= $value;
				} elseif (is_bool($value)) {
					$js .= ($value === true) ? 'true' : 'false';
				} else { //string
					if (in_array($option, array('selectionType', 'selection'))) {
						$js .= $value;
					} elseif (in_array($option, array('selectionType', 'selection', 'onSelect', 'onChange', 'onTimeChange', 'disabled', 'dateInfo', 'onFocus', 'onBlur'))) {
						$js .= $value;
					} else {
						$js .= '\''.$value.'\'';
					}
				}
				$js .= ($i >= $n) ? "\n" : ",\n";
				$i++;
			}
		}
		$js .= "});\n";
		$js .= 'cal'.$this->idx.'.manageFields(\''.$triggerid.'\', \''.$elementid.'\', \''.$format.'\');'."\n";
		$js .= '/* ]]> */'."\n";
		$js .= "</script>\n";

		$this->resetOptions();

		return $js;
	}


	/**************************/
	/* RESET CALENDAR OPTIONS */
	/**************************/
	private function resetOptions() {
		$this->options = array();
	}

}

?>