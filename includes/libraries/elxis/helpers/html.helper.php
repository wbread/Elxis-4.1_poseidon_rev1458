<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisHTMLHelper {

	private $elxis;


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$this->elxis = eFactory::getElxis();
	}


	/**************/
	/* ADD OPTION */
	/**************/
	public function makeOption($value, $text='', $disabled=false) {
		$obj = new stdClass;
		$obj->value = $value;
		$obj->text = (trim($text) != '') ? $text : $value;
		$obj->disabled = (bool)$disabled;
		return $obj;
	}


	/*************************************************/
	/* MAKE A SINGLE SELECTION DROP DOWN SELECT LIST */
	/*************************************************/
	public function selectList($arr, $name, $attribs='', $key='value', $text='text', $selected=NULL, $id=false) {
        if ($id === false) {
        	$idx = str_replace('[', '', $name);
        	$idx = str_replace(']', '', $idx);
            $html = "\n".'<select name="'.$name.'" id="'.$idx.'" '.$attribs.'>';
        } else {
            $html = "\n".'<select name="'.$name.'" id="'.$id.'" '.$attribs.'>';
        }
		if (is_array($arr)) {
			reset($arr);
		} else {
			$html .= "\n</select>";
			return $html;
		}

		foreach ($arr as $i => $option) {
			$k = $arr[$i]->$key;
			$extra = '';
			if (is_array($selected)) {
				foreach ($selected as $obj) {
					if ($k == $obj->$key) {
						$extra .= ' selected="selected"';
						break;
					}
				}
			} else {
				$extra .= ($k == $selected) ? ' selected="selected"' : '';
			}

			if ($arr[$i]->disabled == true) {
				$extra .= ' disabled="disabled"';
			}

			$k = $this->elxis->obj('filter')->ampReplace($k);
			$t = $this->elxis->obj('filter')->ampReplace($arr[$i]->$text);
			$html .= "\n\t".'<option value="'.$k.'"'.$extra.'>'.$t.'</option>';
		}

		$html .= "\n</select>\n";
		return $html;
	}


	/***************************/
	/* MAKE AN HTML RADIO LIST */
	/***************************/
	public function radioList($arr, $name, $attribs='', $key='value', $text='text', $selected=NULL, $id=false) {
		if (is_array($arr)) {
			reset($arr);
		} else {
			return '';
		}

		$attribs = trim($attribs);
		if ($attribs != '') { $attribs = ' '.$attribs; }

        if ($id === false) {
        	$idx = str_replace('[', '', $name);
        	$idx = str_replace(']', '', $idx);
        } else {
            $idx = $id;
        }

		$html = '';
		foreach ($arr as $i => $option) {
			$k = $arr[$i]->$key;
			$extra = '';
			if (is_array($selected)) {
				foreach ($selected as $obj) {
					$k2 = is_object($obj) ? $obj->$key : $obj;
					if ($k == $k2) {
						$extra .= ' checked="checked"';
						break;
					}
				}
			} else {
				$extra .= ($k == $selected) ? ' checked="checked"' : '';
			}

			$html .= "\n\t".'<input type="radio" name="'.$name.'" id="'.$idx.$i.'" value="'.$k.'"'.$extra.$attribs.' />';
			$html .= "\n\t".'<label for="'.$idx.$i.'">'.$arr[$i]->$text.'</label>';
		}
		$html .= "\n";
		return $html;
	}


}

?>