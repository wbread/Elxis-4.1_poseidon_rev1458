<?php 
/**
* @version		$Id: form.class.php 1399 2013-03-05 17:59:27Z datahell $
* @package		Elxis
* @subpackage	Form builder
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisForm {

	private $dir = 'ltr';
	private $name = 'elxisform';
	private $enctype = 'application/x-www-form-urlencoded'; //or multipart/form-data
	private $action = 'index.php';
	private $method = 'post';
	private $token = true;
	private $elxisbase = true;
	private $date_format = 'Y-m-d';
	private $datetime_format = 'Y-m-d H:i:s';
	private $attributes = '';
	private $cssclass = 'elx_form';
	private $idprefix = ''; //a prefix to add in all automatically generated id attributes
	private $idx = 1;
	private $label_width = 180;
	private $label_align = 'left'; //left, right -> flip for rtl
	private $label_top = 0; //0: left (flip for rtl), 1: top
	private $tip_style = 0; //0: next to the element, 1: js (only admin) tooltip next to element, 2: bellow the element
	private $jsonsubmit = ''; //js function to execute on submit after input validation
	private $autocomplete_off = false; //if true turn autocomplete off with javascript
	private $elements = array();
	private $fieldsets = array();
	private $fieldset_active = false;
	private $fieldset_idx = 0;
	private $tabs = array();
	private $tab_active = false;
	private $tab_idx = 0;
	private $row_active = false;
	private $row_idx = 0;
	private $row_columns = array();
	private $fields_required = array();
	private $fields_email = array();
	private $fields_date = array();
	private $fields_datetime = array();
	private $fields_slider = array();
	private $fields_number = array();
	private $fields_url = array();
	private $fields_match = array();
	private $errormsg = '';
	private $php64bit = -1;
	private $ml = null; //multilingual elements helper
	private $mlapibase = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($options=array()) {
		$eLang = eFactory::getLang();
		$this->dir = $eLang->getinfo('DIR');
		$this->date_format = $eLang->get('DATE_FORMAT_BOX');
		$this->datetime_format = $eLang->get('DATE_FORMAT_BOX_LONG');
		$this->label_align = ($this->dir == 'rtl') ? 'right' : 'left';
		$this->setOptions($options);
		if (ELXIS_MOBILE == 1) {
			$this->label_top = 1;
			$this->tip_style = 2;
		}
	}


	/********************/
	/* SET FORM OPTIONS */
	/********************/
	public function setOptions($options=array()) {
		if (is_array($options) && (count($options) > 0)) {
			foreach ($options as $key => $val) {
				switch($key) {
					case 'name':
						$name = trim(preg_replace("/\W/", "_", $val));
						if ($name == '') {
							$this->errormsg = 'Form name is invalid!';
							return false;
						}
						$this->name = $name;
					break;
					case 'token': $this->token = (bool)$val; break;
					case 'elxisbase': $this->elxisbase = (bool)$val; break;
					case 'enctype': $this->enctype = $val; break;
					case 'action': $this->action = $val; break;
					case 'method': $this->method = $val; break;
					case 'attributes': $this->attributes = trim($val); break;
					case 'cssclass': $this->cssclass = $val; break;
					case 'idprefix': $this->idprefix = trim($val); break;
					case 'idx': $this->idx = (intval($val) > 0) ? (int)$val : 1; break;
					case 'label_width':
						$val = (int)$val;
						if ($val > 0) {	$this->label_width = $val; }
					break;
					case 'label_align':
						$val = strtolower($val);
						if ($val == 'left') {
							$this->label_align = ($this->dir == 'rtl') ? 'right' : 'left';
						} else if ($val == 'right') {
							$this->label_align = ($this->dir == 'rtl') ? 'left' : 'right';
						}
					break;
					case 'label_top': $this->label_top = (int)$val; break;
					case 'tip_style':
						$this->tip_style = (int)$val;
						if (($this->tip_style === 1) && !defined('ELXIS_ADMIN')) { $this->tip_style = 0; }
					break;
					case 'jsonsubmit': $this->jsonsubmit = trim($val); break;
					case 'autocomplete_off': $this->autocomplete_off = (bool) $val; break;
					case 'mlapibase':
						$val = trim($val);
						if (($val != '') && (stripos($val, 'http') === 0)) { $this->mlapibase = $val; }
					break;
					default: break;
				}
			}
			if (ELXIS_MOBILE == 1) {
				$this->label_top = 1;
				$this->tip_style = 2;
			}
		}
	}


	/*******************/
	/* SHOW FORM ERROR */
	/*******************/
	private function showError() {
		echo '<div class="elx_error">'.$this->errormsg."</div>\n";
	}


	/****************************************/
	/* MARK FIELD FOR JAVASCRIPT VALIDATION */
	/****************************************/
	private function markField($id, $marktype, $sec=null) {
		switch ($marktype) {
			case 'required': $this->fields_required[] = $id; break;
			case 'email': $this->fields_email[] = $id; break;
			case 'date': $this->fields_date[] = $id; break;
			case 'datetime': $this->fields_datetime[] = $id; break;
			case 'slider': $this->fields_slider[$id] = $sec; break;
			case 'number': $this->fields_number[] = $id; break;
			case 'url': $this->fields_url[] = $id; break;
			case 'match': $this->fields_match[$id] = $sec; break;
			default: break;
		}
	}


	/************************/
	/* START A NEW FIELDSET */
	/************************/
	public function openFieldset($legend='') {
		if ($this->fieldset_active === true) {
			$this->errormsg = 'A fieldset is already open! Close the previous one to open a new one.';
			return false;
		}
		$this->fieldset_active = true;
		$this->fieldset_idx++;
		$fidx = $this->fieldset_idx;
		$this->fieldsets[$fidx] = (string)$legend;
		return true;
	}


	/***********************/
	/* CLOSE OPEN FIELDSET */
	/***********************/
	public function closeFieldset() {
		$this->fieldset_active = false;
	}


	/*******************/
	/* START A NEW ROW */
	/*******************/
	public function openRow() {
		if ($this->row_active === true) {
			$this->errormsg = 'A row is already open! Close the previous one to open a new one.';
			return false;
		}
		$this->row_active = true;
		$this->row_idx++;
		$ridx = $this->row_idx;
		$this->row_columns[$ridx] = 0;
	}


	/******************/
	/* CLOSE OPEN ROW */
	/******************/
	public function closeRow() {
		$this->row_active = false;
	}


	/*******************/
	/* START A NEW TAB */
	/*******************/
	public function openTab($legend='', $importJS=true) {
		if ($this->tab_active === true) {
			$this->errormsg = 'A tab is already open! Close the previous one to open a new one.';
			return false;
		}

		if (!defined('ELXIS_TABS_LOADED')) {
			$eDoc = eFactory::getDocument();

			$jsFile = eFactory::getElxis()->secureBase().'/includes/js/jquery/tabs.js';
			$eDoc->addJQuery();
			$eDoc->addScriptLink($jsFile);
			define('ELXIS_TABS_LOADED', 1);
		}

		$this->tab_active = true;
		$this->tab_idx++;
		$tidx = $this->tab_idx;
		$this->tabs[$tidx] = (string)$legend;
		return true;
	}


	/******************/
	/* CLOSE OPEN TAB */
	/******************/
	public function closeTab() {
		$this->tab_active = false;
	}


	/*****************************/
	/* CREATE A NEW BASE ELEMENT */
	/*****************************/
	private function baseElement($type, $name, $label) {
		$name = trim($name);
		if ($name == '') {
			$this->errormsg = 'The element name can not be empty!';
			return null;
		}
		$idx = $this->idx;
		$element = new stdClass();
		$element->type = $type;
		$element->idx = $idx;
		$element->name = $name;
		$element->tip = '';
		$element->label = $label;
		$element->required = 0;
		$element->id = ($this->idprefix != '') ? $this->idprefix.$name : $name;
		$element->fieldset = ($this->fieldset_active === true) ? $this->fieldset_idx : 0;
		$element->tab = ($this->tab_active === true) ? $this->tab_idx : 0;
		if ($this->row_active === true) {
			$ridx = $this->row_idx;
			$element->row = $this->row_idx;
			$this->row_columns[$ridx] += 1;
		} else {
			$element->row = 0;
		}
		$element->extra = array();
		return $element;
	}


	/********************************/
	/* ADD ATTRIBUTES TO AN ELEMENT */
	/********************************/
	private function addAttributes($attributes=array(), &$element) {
		if (is_array($attributes) && (count($attributes) > 0)) {
			foreach ($attributes as $key => $val) {
				switch($key) {
					case 'id': $element->id = trim($val); break;
					case 'title': $element->title = $val; break;
					case 'tip': $element->tip = $val; break;
					case 'required': $element->required = (int)$val; break;
					case 'readonly': $element->readonly = (int)$val; break;
					case 'size': $element->size = (int)$val; break;
					case 'maxlength': $element->maxlength = (int)$val; break;
					case 'dir': $element->dir = ($this->dir == 'rtl') ? $val : 'ltr'; break;
					case 'forcedir': $element->dir = ($val == 'rtl') ? 'rtl' : 'ltr'; break;
					case 'disabled': $element->disabled = (int)$val; break;
					case 'multiple': $element->multiple = (int)$val; break;
					case 'vertical_options': $element->vertical_options = (int)$val; break;
					case 'cols': $element->cols = (int)$val; break;
					case 'rows': $element->rows = (int)$val; break;
					case 'accept': $element->accept = (string)$val; break;
					case 'password_meter': $element->password_meter = (int)$val; break;
					case 'match': $element->match = (string)$val; break;
					case 'class': $element->class = (string)$val; break;
					case 'width': $element->width = (int)$val; break; //slider
					case 'min': $element->min = (int)$val; break; //slider
					case 'max': $element->max = (int)$val; break; //slider
					case 'editor': $element->editor = trim($val); break; //textarea
					case 'editoroptions': $element->editoroptions = (is_array($val)) ? $val : array(); break; //textarea
					case 'contentslang': $element->contentslang = (string)$val; break; //textarea
					case 'type': case 'idx': case 'name': case 'label': break;
					default: $element->extra[$key] = $val; break;
				}
			}
		}
		return $element;
	}


	/************************/
	/* ADD INPUT TEXT FIELD */
	/************************/
	public function addText($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('text', $name, $label);
		if ($element === null) { return false; }
		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->dir = 'ltr';
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/*******************************/
	/* ADD MULTILINGUAL TEXT FIELD */
	/*******************************/
	public function addMLText($name, $trdata, $value='', $label='', $attributes=array()) {
		$allowed = (eFactory::getElxis()->acl()->check('component', 'com_etranslator', 'manage') < 1) ? false : true;
		if (!$allowed && !defined('ELXIS_ADMIN') && ($this->mlapibase != '')) {
			$allowed = (eFactory::getElxis()->acl()->check('component', 'com_etranslator', 'api') < 1) ? false : true;
		}
		$trels = array('category', 'element', 'elid');
		if (!is_array($trdata)) {
			$this->errormsg = 'You must provide the Elxis translator the required data for field '.$name.'!';
			return false;
		}

		foreach ($trels as $trel) {
			if (!isset($trdata[$trel]) || ($trdata[$trel] === '')) {
				$this->errormsg = 'No '.$trel.' information provided to the Elxis translator for the field '.$name.'!';
				return false;
			}
		}

		$idx = $this->idx;
		$element = $this->baseElement('mltext', $name, $label);
		if ($element === null) { return false; }

		$this->prepareMultiLinguism($allowed);

		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->dir = 'ltr';
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);

		$trItem = new stdClass;
		$trItem->itemid = $element->id;
		$trItem->ctg = $trdata['category'];
		$trItem->elem = $trdata['element'];
		$trItem->elid = (int)$trdata['elid'];
		$this->ml->items[$idx] = $trItem;
		unset($trItem);

		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/****************************************/
	/* PREPARE MULTILINGUAL CONTENT SUPPORT */
	/****************************************/
	private function prepareMultiLinguism($allowed=true) {
		$mldatainst = 0;
		if (isset($GLOBALS['mldatainst'])) {
			$mldatainst = (int)$GLOBALS['mldatainst'];
			if ($mldatainst < 1) { $mldatainst = 0; }
		}
		$mldatainst++;
		$GLOBALS['mldatainst'] = $mldatainst;

		if (!empty($this->ml)) { return; }

		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$deflang = $elxis->getConfig('LANG');
		$slangs = eFactory::getLang()->getSiteLangs(true);
		if (!isset($slangs[$deflang])) {
			$this->errormsg = 'No information found in languages database for the default language '.$deflang.'!';
			return false;
		}

		$ml = new stdClass;
		$ml->instance = $mldatainst;
		$ml->lang = $deflang;
		$ml->dir = $slangs[$deflang]['DIR'];
		$ml->langs = array();
		$ml->langs[$deflang] = $slangs[$deflang];
		foreach ($slangs as $lng => $info) {
			if ($lng == $deflang) { continue; }
			$ml->langs[$lng] = $info;
		}
		$ml->items = array();

		$options = $this->loadTransParams();

		$ml->isave = $elxis->icon('save', 16);
		$ml->idelete = $elxis->icon('delete', 16);
		$ml->ibing = $elxis->icon('bing', 16);
		$ml->bingapi = $options['bingapi'];
		$ml->allowed = $allowed;
		$this->ml = $ml;

		$eDoc->addStyleLink($elxis->secureBase().'/includes/libraries/elxis/language/mlflag.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/mlapi.js');
	}


	/***************************/
	/* LOAD ETRANSLATOR PARAMS */
	/***************************/
	private function loadTransParams() {
		$db = eFactory::getDB();
		$options = array('bingapi' => '');
		$component = 'com_etranslator';
		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__components')." WHERE ".$db->quoteId('component')." = :xcomp";
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->bindParam(':xcomp', $component, PDO::PARAM_STR);
		$stmt->execute();
		$params_str = $stmt->fetchResult();
		if ($params_str) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters($params_str, '', 'component');
			$options['bingapi'] = trim($params->get('bingapi', ''));
			unset($params);
		}
		return $options;
	}


	/************************************/
	/* RENDER MULTILINGUAL TEXT ELEMENT */
	/************************************/
	private function renderMltext($element) {
		if (!is_object($element)) { return ''; }
		$eLang = eFactory::getLang();
		$field = "\t\t\t".'<input type="text" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.' ('.$this->ml->langs[ $this->ml->lang ]['NAME'].')"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.' mlflag'.$this->ml->lang.'" dir="'.$this->ml->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		$field .= "\t\t\t".'<input type="text" name="trans_'.$element->name.'" id="trans_'.$element->id.'" value=""';
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		$field .= ' class="inputbox mlflagun" dir="ltr" style="display:none;" onchange="trans_markunsaved(this);"';
		$field .= " /> \n";

		$idx = $element->idx;
		$distxt = ($this->ml->items[$idx]->elid < 1) ? ' disabled="disabled"' : '';
		if ($this->ml->allowed == false) { $distxt = ' disabled="disabled"'; }

		$field .= "\t\t\t".'<select name="transl_'.$element->name.'" id="transl_'.$element->id.'" class="selectbox mlflag'.$this->ml->lang.'" dir="ltr"'.$distxt.' onchange="translang_switch('.$this->ml->instance.', \''.$element->id.'\');">'."\n";
		foreach ($this->ml->langs as $lng => $linfo) {
			$sel = ($lng == $this->ml->lang) ? ' selected="selected"' : '';
			$field .= "\t\t\t".'<option value="'.$lng.'" class="mlflag'.$lng.'"'.$sel.'>'.$lng."</option>\n";
		}
		$field .= "</select> \n";

		if ($element->tip != '') {
			$old_style = $this->tip_style;
			$this->tip_style = 1;
			$field .= "\t\t\t".$this->makeTip($element->tip)." \n";
			$this->tip_style = $old_style;
		}
		$element->tip = '';

		$field .= '<span id="transb_'.$element->id.'" style="display:none;">'."\n";
		$field .= '<a href="javascript:void(null);" title="'.$eLang->get('SAVE').'" onclick="translang_save('.$this->ml->instance.', \''.$element->id.'\')" style="margin:0 4px;">';
		$field .= '<img src="'.$this->ml->isave.'" alt="save" border="0" align="bottom" /></a> ';
		$field .= '<a href="javascript:void(null);" title="'.$eLang->get('DELETE').'" onclick="translang_delete('.$this->ml->instance.', \''.$element->id.'\')" style="margin:0 4px;">';
		$field .= '<img src="'.$this->ml->idelete.'" alt="delete" border="0" align="bottom" /></a> ';
		if ($this->ml->bingapi != '') { //microsft sucks! google too!
		//	$field .= '<a href="javascript:void(null);" title="'.$eLang->get('AUTO_TRANS').' (Bing)" onclick="translang_bing('.$this->ml->instance.', \''.$element->id.'\')" style="margin:0 4px;">';
		} else {
		//	$field .= '<a href="javascript:void(null);" title="'.$eLang->get('AUTO_TRANS').' (Bing)" onclick="alert(\'First enabled Bing in eTranslator!\')" style="margin:0 4px;">';
		}
		//$field .= '<img src="'.$this->ml->ibing.'" alt="bing" border="0" align="bottom" /></a>';
		$field .= "</span>\n";

		if ($this->label_top == 1) {
			$extra_style = '';
		} else if ($this->dir == 'rtl') {
			$w = $this->label_width + 5;
			$extra_style = ' padding:0 '.$w.'px 0 0;';
		} else {
			$w = $this->label_width + 5;
			$extra_style = ' padding:0 0 0 '.$w.'px;';
		}
		$field .= '<div id="transmsg_'.$element->id.'" class="ml_message" style="display:none;'.$extra_style.'"></div>'."\n";
		$field .= '<input type="hidden" name="transid_'.$element->name.'" id="transid_'.$element->id.'" value="0" />';

		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/****************************/
	/* ADD SLIDER NUMERIC FIELD */
	/****************************/
	public function addSlider($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('slider', $name, $label);
		if ($element === null) { return false; }
		$element->value = (int)$value;
		$element->title = $label;
		$element->readonly = 0;
		$element->width = 150;
		$element->min = 0;
		$element->max = 100;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$n = (string)$element->max;
		$element->size = strlen($n);
		$element->maxlength = strlen($n);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'number');
		$this->markField($element->id, 'slider', array($element->width, $element->min, $element->max));
		$this->idx++;
		return true;
	}


	/************************/
	/* ADD DATE TEXT FIELD */
	/************************/
	public function addDate($name, $value='', $label='', $attributes=array()) {
		return $this->addHmeromhnia($name, $value, $label, $attributes, false);
	}


	/***************************/
	/* ADD DATETIME TEXT FIELD */
	/***************************/
	public function addDatetime($name, $value='', $label='', $attributes=array()) {
		return $this->addHmeromhnia($name, $value, $label, $attributes, true);
	}


	/************************************/
	/* ADD DATE OR DATETIME TEXT FIELDS */
	/************************************/
	private function addHmeromhnia($name, $value='', $label='', $attributes=array(), $datetime=false) {
		$idx = $this->idx;
		if ($datetime === true) {
			$element_info = array(
				'type' => 'datetime',
				'format' => $this->datetime_format,
				'size' => 19,
				'title' => $this->datetime_format.', '.eFactory::getDate()->getTimezone()
			);
		} else {
			$element_info = array(
				'type' => 'date',
				'format' => $this->date_format,
				'size' => 10,
				'title' => $this->date_format.', '.eFactory::getDate()->getTimezone()
			);
		}

		$element = $this->baseElement($element_info['type'], $name, $label);
		if ($element === null) { return false; }
		$element->value = '';
		if ($value != '') {
			if (preg_match('#\/#', $value)) {
				$parts = preg_split('#\/#', $value, -1, PREG_SPLIT_NO_EMPTY);
			} else {
				$parts = preg_split('#\-#', $value, -1, PREG_SPLIT_NO_EMPTY);
			}

			if (!$parts || (count($parts) != 3)) {
				$value = '';
			} else {
				$h = 12; $i = 0; $s = 0;
				switch ($element_info['format']) {
					case 'Y-m-d': case 'Y/m/d':
						$d = (int)$parts[2]; $m = (int)$parts[1]; $y = (int)$parts[0];
					break;
					case 'd-m-Y': case 'd/m/Y':
						$d = (int)$parts[0]; $m = (int)$parts[1]; $y = (int)$parts[2];
					break;
					case 'm-d-Y': case 'm/d/Y':
						$d = (int)$parts[1]; $m = (int)$parts[0]; $y = (int)$parts[2];
					break;
					case 'Y-m-d H:i:s': case 'Y/m/d H:i:s':
						$y = (int)$parts[0]; $m = (int)$parts[1]; $savedvalue = $value; $value = '';
						$parts2 = preg_split('#[\s]#', $parts[2], -1, PREG_SPLIT_NO_EMPTY);
						if ($parts2 && (count($parts2) == 2)) {
							$d = (int)$parts2[0];
							$parts3 = preg_split('#\:#', $parts2[1], -1, PREG_SPLIT_NO_EMPTY);
							if ($parts3 && (count($parts3) == 3)) {
								$h = (int)$parts3[0]; $i = (int)$parts3[1]; $s = (int)$parts3[2];
								if (($h > -1) && ($h < 24) && ($i > -1) && ($i < 60) && ($s > -1) && ($s < 60)) {
									$value = $savedvalue;
								}
							}
						}
					break;
					case 'd-m-Y H:i:s': case 'd/m/Y H:i:s':
						$d = (int)$parts[0]; $m = (int)$parts[1]; $savedvalue = $value; $value = '';
						$parts2 = preg_split('#[\s]#', $parts[2], -1, PREG_SPLIT_NO_EMPTY);
						if ($parts2 && (count($parts2) == 2)) {
							$y = (int)$parts2[0];
							$parts3 = preg_split('#\:#', $parts2[1], -1, PREG_SPLIT_NO_EMPTY);
							if ($parts3 && (count($parts3) == 3)) {
								$h = (int)$parts3[0]; $i = (int)$parts3[1]; $s = (int)$parts3[2];
								if (($h > -1) && ($h < 24) && ($i > -1) && ($i < 60) && ($s > -1) && ($s < 60)) {
									$value = $savedvalue;
								}
							}
						}
					break;
					case 'm-d-Y H:i:s': case 'm/d/Y H:i:s':
						$d = (int)$parts[1]; $m = (int)$parts[0]; $savedvalue = $value; $value = '';
						$parts2 = preg_split('#[\s]#', $parts[2], -1, PREG_SPLIT_NO_EMPTY);
						if ($parts2 && (count($parts2) == 2)) {
							$y = (int)$parts2[0];
							$parts3 = preg_split('#\:#', $parts2[1], -1, PREG_SPLIT_NO_EMPTY);
							if ($parts3 && (count($parts3) == 3)) {
								$h = (int)$parts3[0]; $i = (int)$parts3[1]; $s = (int)$parts3[2];
								if (($h > -1) && ($h < 24) && ($i > -1) && ($i < 60) && ($s > -1) && ($s < 60)) {
									$value = $savedvalue;
								}
							}
						}
					break;
					default:
						$value = '';
					break;
				}
				if ($value != '') {
					if (checkdate($m, $d, $y)) {
						$element->value = $this->safe64bit_gmdate($y, $m, $d, $h, $i, $s, $datetime);
					}
				}
			}
		}
		$element->title = $element_info['title'];
		$element->readonly = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$element->size = $element_info['size'];
		$element->maxlength = $element_info['size'];
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, $element_info['type']);
		$this->idx++;
		return true;
	}


	/*************************/
	/* ADD NUMBER TEXT FIELD */
	/*************************/
	public function addNumber($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('text', $name, $label);
		if ($element === null) { return false; }
		$element->value = is_numeric($value) ? $value : '';
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'number');
		$this->idx++;
		return true;
	}


	/*********************/
	/* ADD CAPTCHA FIELD */
	/*********************/
	public function addCaptcha($name, $label='', $attributes=array()) {
		$idx = $this->idx;
		if (trim($label == '')) { $label = eFactory::getLang()->get('SECURITY_CODE'); }
		$element = $this->baseElement('captcha', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->size = 5;
		$element->maxlength = 5;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->required = 1;

		$element->dir = 'ltr';
		$v1 = rand(4, 30);
		$v2 = rand(3, 29);
		if ($v1 % 2) {
			$element->operator = '+';
			$element->number1 = $v1;
			$element->number2 = $v2;
			$sum = $element->number1 + $element->number2;
		} else {
			$element->operator = '-';
			if ($v1 == $v2) {
				$element->number1 = $v1 + rand(6, 21);
				$element->number2 = $v2;
			} else if ($v1 > $v2) {
				if (($v1 - $v2) < 6) { $v1 = $v1 + rand(5, 20); }
				$element->number1 = $v1;
				$element->number2 = $v2;
			} else {
				$element->number1 = $v1 + rand(5, 20);
				$element->number2 = $v1;
			}
			$sum = $element->number1 - $element->number2;
		}
		eFactory::getSession()->set('captcha_'.$name, $sum);
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'number');
		$this->idx++;
		return true;
	}


	/********************/
	/* ADD BUTTON FIELD */
	/********************/
	public function addButton($name, $value='', $button_type='submit', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('button', $name, '');
		if ($element === null) { return false; }
		$button_type = strtolower($button_type);
		if (trim($value == '')) {
			$value = ($button_type == 'reset') ? eFactory::getLang()->get('RESET') : eFactory::getLang()->get('SUBMIT');
		}
		$element->value = $value;
		$element->title = $value;
		$element->disabled = 0;
		$element->dir = 'ltr';
		$element->class = 'elxbutton';
		$this->addAttributes($attributes, $element);
		if (($button_type == '') || !in_array($button_type, array('submit', 'reset', 'button'))) { $button_type = 'submit'; }
		$element->button_type = $button_type;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/******************/
	/* ADD FILE FIELD */
	/******************/
	public function addFile($name, $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('file', $name, $label);
		if ($element === null) { return false; }
		$this->enctype = 'multipart/form-data';
		$element->value = '';
		$element->title = $label;
		$element->readonly = 0;
		$element->class = 'filebox';
		$element->accept = '';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/********************************************/
	/* ADD IMAGE FIELD (FILE WITH IMAGE PREVIEW)*/
	/********************************************/
	public function addImage($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('image', $name, $label);
		if ($element === null) { return false; }
		$this->enctype = 'multipart/form-data';
		$element->value = trim($value);
		$element->title = $label;
		$element->readonly = 0;
		$element->class = 'filebox';
		$element->accept = '';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/**********************/
	/* ADD PASSWORD FIELD */
	/**********************/
	public function addPassword($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('password', $name, $label);
		if ($element === null) { return false; }
		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->password_meter = 0;
		$element->match = '';
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		if ($element->match != '') { $this->markField($element->id, 'match', $element->match); }
		$this->idx++;
		return true;
	}


	/************************/
	/* ADD EMAIL TEXT FIELD */
	/************************/
	public function addEmail($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('text', $name, $label);
		if ($element === null) { return false; }
		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'email');
		$this->idx++;
		return true;
	}


	/**********************/
	/* ADD URL TEXT FIELD */
	/**********************/
	public function addUrl($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('text', $name, $label);
		if ($element === null) { return false; }
		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->dir = 'ltr';
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'url');
		$this->idx++;
		return true;
	}


	/************************/
	/* ADD PRICE TEXT FIELD */
	/************************/
	public function addPrice($name, $value='0.00', $label='', $decimals=2, $currency='EUR', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('text', $name, $label);
		if ($element === null) { return false; }
		$decimals = (int)$decimals;
		if ($decimals < 1) { $decimals = 2; }
		if ($currency == '') { $currency = 'EUR'; }
		$value = filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		if (!is_numeric($value)) { $value = '0.00'; }
		$value = number_format($value, $decimals, '.', '');
		$element->value = $value;
		$element->title = $label;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->dir = 'ltr';
		$element->class = 'inputbox';
		$element->currency = $currency;
		$element->readonly = 0;
		$this->addAttributes($attributes, $element);
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->markField($element->id, 'number');
		$this->idx++;
		return true;
	}


	/**********************/
	/* ADD TEXTAREA FIELD */
	/**********************/
	public function addTextarea($name, $value='', $label='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('textarea', $name, $label);
		if ($element === null) { return false; }
		$element->value = $value;
		$element->title = $label;
		$element->disabled = 0;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->cols = 40;
		$element->rows = 4;
		$element->dir = 'ltr';
		$element->class = 'textbox';
		$element->editor = '';
		$element->editoroptions = array();
		$element->contentslang = '';
		$this->addAttributes($attributes, $element);
		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/***********************************/
	/* ADD MULTILINGUAL TEXTAREA FIELD */
	/***********************************/
	public function addMLTextarea($name, $trdata, $value='', $label='', $attributes=array()) {
		$allowed = (eFactory::getElxis()->acl()->check('component', 'com_etranslator', 'manage') < 1) ? false : true;
		if (!$allowed && !defined('ELXIS_ADMIN') && ($this->mlapibase != '')) {
			$allowed = (eFactory::getElxis()->acl()->check('component', 'com_etranslator', 'api') < 1) ? false : true;
		}
		$trels = array('category', 'element', 'elid');
		if (!is_array($trdata)) {
			$this->errormsg = 'You must provide the Elxis translator the required data for field '.$name.'!';
			return false;
		}

		foreach ($trels as $trel) {
			if (!isset($trdata[$trel]) || ($trdata[$trel] === '')) {
				$this->errormsg = 'No '.$trel.' information provided to the Elxis translator for the field '.$name.'!';
				return false;
			}
		}

		$idx = $this->idx;
		$element = $this->baseElement('textarea', $name, $label);
		if ($element === null) { return false; }

		$this->prepareMultiLinguism($allowed);
		
		$element->value = $value;
		$element->title = $label;
		$element->disabled = 0;
		$element->readonly = 0;
		$element->size = 0;
		$element->maxlength = 0;
		$element->cols = 40;
		$element->rows = 4;
		$element->dir = 'ltr';
		$element->class = 'textbox';
		$element->editor = '';
		$element->editoroptions = array();
		$element->contentslang = '';
		$this->addAttributes($attributes, $element);

		$element->dir = $this->ml->dir;
		$element->contentslang = $this->ml->lang;

		$trItem = new stdClass;
		$trItem->itemid = $element->id;
		$trItem->ctg = $trdata['category'];
		$trItem->elem = $trdata['element'];
		$trItem->elid = (int)$trdata['elid'];
		$this->ml->items[$idx] = $trItem;
		unset($trItem);

		$this->elements[$idx] = $element;
		if ($element->required == 1) { $this->markField($element->id, 'required'); }
		$this->idx++;
		return true;
	}


	/************************/
	/* ADD HIDDEN TEXT FIELD */
	/************************/
	public function addHidden($name, $value='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('hidden', $name, '');
		if ($element === null) { return false; }
		$element->value = $value;
		$element->dir = 'ltr';
		$this->addAttributes($attributes, $element);
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/******************************/
	/* ADD DROP DOWN SELECT FIELD */
	/******************************/
	public function addSelect($name, $label='', $selected=null, $options=array(), $attributes=array()) {
		if (!is_array($options) || (count($options) == 0)) {
			$this->errormsg = 'A select field must have at least one option!';
			return false;
		}
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->multiple = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->options = $options;

		if ($element->multiple == 0) {
			$element->selected = (is_array($selected)) ? (string)$selected[0] : (string)$selected;
		} else {
			$element->selected = (is_array($selected)) ? $selected : array((string)$selected);
		}

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/****************************/
	/* ADD COUNTRY SELECT FIELD */
	/****************************/
	public function addCountry($name, $label='', $selected='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->multiple = 0;
		$element->selected = (is_array($selected)) ? (string)$selected[0] : (string)$selected;

		$lng = eFactory::getLang()->getinfo('LANGUAGE');
		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
		} else {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
		}
		if (!isset($countries)) {
			$this->errormsg = 'Countries language file not found in includes/libraries/elxis/form/';
			return false;
		}

		$options = array();
		foreach ($countries as $key => $name) {
			$options[] = $this->makeOption($key, $name);
		}
		$element->options = $options;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/*****************************/
	/* ADD LANGUAGE SELECT FIELD */
	/*****************************/
	public function addLanguage($name, $label='', $selected='', $attributes=array(), $ltype=2, $nativeNames=true) {
		$ltype = (int)$ltype; //for compatibility reasons (up to Elxis 4.0 rev1354)
		if ($ltype > 2) { $ltype = 1; }
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->multiple = 0;
		$element->selected = (is_array($selected)) ? (string)$selected[0] : (string)$selected;

		switch ($ltype) {
			case 0://all languages even not installed
				if (!file_exists(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php')) {
					$this->errormsg = 'Languages database file langdb.php not found!';
					return false;
				}
				include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
				if (!isset($langdb) || !is_array($langdb)) {
					$this->errormsg = 'Languages database file langdb.php does not contain languages information!';
					return false;
				}
				$xlangs = $langdb;
				unset($langdb);
			case 2://site enabled
				$xlangs = eFactory::getLang()->getSiteLangs(true);
			break;
			case 1: default://all installed
				$xlangs = eFactory::getLang()->getAllLangs(true);
			break;
		}

		$options = array();
		foreach ($xlangs as $lng => $info) {
			$val = ($nativeNames === true) ? $info['NAME'] : $info['NAME_ENG'];
			$options[] = $this->makeOption($lng, $val);
		}
		$element->options = $options;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/*****************************/
	/* ADD TIMEZONE SELECT FIELD */
	/*****************************/
	public function addTimezone($name, $label='', $selected='', $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->multiple = 0;
		$element->selected = (is_array($selected)) ? (string)$selected[0] : (string)$selected;
		$zones = timezone_identifiers_list();
		$options = array();
		foreach ($zones as $zone) {
			$options[] = $this->makeOption($zone, $zone);
		}
		$element->options = $options;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/******************************/
	/* ADD USERGROUP SELECT FIELD */
	/******************************/
	public function addUsergroup($name, $label='', $selected='', $lowerlevel=0, $upperlevel=100, $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$lowerlevel = (int)$lowerlevel;
		$upperlevel = (int)$upperlevel;
		$element->title = $label;
		$element->disabled = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->multiple = 0;
		$element->selected = (is_array($selected)) ? (int)$selected[0] : (int)$selected;

		$eLang = eFactory::getLang();
		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__groups');
		$sql .= ' WHERE '.$db->quoteId('level').' >= :llev AND '.$db->quoteId('level').' <= :ulev';
		$sql .= ' ORDER BY '.$db->quoteId('level').' DESC';
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':llev', $lowerlevel, PDO::PARAM_INT);
		$stmt->bindParam(':ulev', $upperlevel, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		if ($rows) {
			foreach ($rows as $row) {
				if ($row['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
				} else if ($row['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($row['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($row['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
				} else {
					$groupname = $row['groupname'];
				}

				$lev = sprintf("%03d", $row['level']);
				$options[] = $this->makeOption($row['gid'], $row['gid'].' - '.$lev.' - '.$groupname);
			}
		}

		$element->options = $options;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/*********************************/
	/* ADD ACCESS LEVEL SELECT FIELD */
	/*********************************/
	function addAccesslevel($name, $label='', $selected=0, $userlevel=0, $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->size = 0;
		$element->dir = 'ltr';
		$element->class = 'selectbox';
		$this->addAttributes($attributes, $element);
		$element->multiple = 0;
		$element->selected = (is_array($selected)) ? (int)$selected[0] : (int)$selected;
		$userlevel = (int)$userlevel;

		$eLang = eFactory::getLang();
		$db = eFactory::getDB();
		$stmt = $db->prepare("SELECT * FROM ".$db->quoteId('#__groups').' ORDER BY '.$db->quoteId('level').' DESC');
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		if ($rows) {
			$levels = array();
			$element->size = count($rows);

			foreach ($rows as $row) {
				$level = $row['level'];
				if (!isset($levels[$level])) { $levels[$level] = 0; }
				$levels[$level]++;
			}

			$lastlevel = -1;
			$space = '';
			foreach ($rows as $row) {
				$level = $row['level'];
				$lowlevel = $row['level'] * 1000;
				$exactlevel = ($row['level'] * 1000) + $row['gid'];

				if ($row['gid'] == 1) {
					$groupname = $eLang->get('ADMINISTRATOR');
					$exactlevel = 100000;
				} else if ($row['gid'] == 5) {
					$groupname = $eLang->get('USER');
				} else if ($row['gid'] == 6) {
					$groupname = $eLang->get('EXTERNALUSER');
				} elseif ($row['gid'] == 7) {
					$groupname = $eLang->get('GUEST');
					$exactlevel = 0;
				} else {
					$groupname = $row['groupname'];
				}

				$disabled = ($userlevel < $level) ? 1 : 0;
				if ($level != $lastlevel) {
					$space .= ($lastlevel == -1) ? '' : '. &#160;';
					$lastlevel = $level;
					if ($levels[$level] > 1) {
						$element->size++;
						$leveltext = sprintf($eLang->get('ALL_GROUPS_LEVEL'), $level);
						$options[] = $this->makeOption($lowlevel, $space.$leveltext, array(), $disabled);
					}
				}

				switch ($level) {
					case 0: case 1: case 2: case 100: $optionvalue = $lowlevel; break;
					default:
						$optionvalue = ($levels[$level] > 1) ? $exactlevel : $lowlevel;
					break;
				}

				$options[] = $this->makeOption($optionvalue, $space.$level.' - '.$groupname, array(), $disabled);
			}
		}

		$element->options = $options;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/************************************************/
	/* ADD RANGE OF INTEGERS DROP DOWN SELECT FIELD */
	/************************************************/
	public function addRange($name, $label='', $first=0, $last=1, $selected=-1, $step=1, $attributes=array()) {
		$first = (int)$first;
		$last = (int)$last;
		$step = (int)$step;
		if (($first == $last) || ($step < 1)) {
			$this->errormsg = 'The first option is equal to the last option or step is zero than 1 in range select '.$name;
			return false;
		}
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->class = 'selectbox';
		$element->size = 0;
		$this->addAttributes($attributes, $element);
		$element->selected = (int)$selected;
		$element->multiple = 0;
		$element->dir = 'ltr';

		if ($first < $last) {
			$values = range($first, $last, $step);
		} else {
			$values = range($last, $first, $step);
			$values = array_reverse($values);
		}
		$options = array();
		foreach ($values as $value) {
			$options[] = $this->makeOption($value, $value);
		}
		$element->options = $options;

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/************************************/
	/* ADD MONTH DROP DOWN SELECT FIELD */
	/************************************/
	public function addMonth($name, $label='', $selected=1, $short=false, $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('select', $name, $label);
		if ($element === null) { return false; }
		$element->title = $label;
		$element->disabled = 0;
		$element->class = 'selectbox';
		$element->size = 0;
		$this->addAttributes($attributes, $element);
		$element->selected = (int)$selected;
		$element->multiple = 0;
		$element->dir = ($this->dir == 'rtl') ? 'rtl' : 'ltr';

		$eDate = eFactory::getDate();
		$short = (bool)$short;
		$options = array();
		for ($i=1; $i<13; $i++) {
			$name = $eDate->monthName($i, $short);
			$options[] = $this->makeOption($i, $name);
		}
		$element->options = $options;

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/***********************/
	/* ADD RADIO BOX FIELD */
	/***********************/
	public function addRadio($name, $label='', $checked='', $options=array(), $attributes=array()) {
		if (!is_array($options) || (count($options) == 0)) {
			$this->errormsg = 'A radio box field must have at least one option!';
			return false;
		}
		$idx = $this->idx;
		$element = $this->baseElement('radio', $name, $label);
		if ($element === null) { return false; }
		$element->checked = (string)$checked;
		$element->vertical_options = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);
		$element->options = $options;

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/******************************/
	/* ADD YES/NO RADIO BOX FIELD */
	/******************************/
	public function addYesNo($name, $label='', $checked=0, $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('radio', $name, $label);
		if ($element === null) { return false; }
		$element->checked = (int)$checked;
		$element->vertical_options = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);

		$eLang = eFactory::getLang();
		$options = array();
		$options[] = $this->makeOption(1, $eLang->get('YES'));
		$options[] = $this->makeOption(0, $eLang->get('NO'));
		$element->options = $options;

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/*********************************/
	/* ADD SHOW/HIDE RADIO BOX FIELD */
	/*********************************/
	public function addShowHide($name, $label='', $checked=0, $attributes=array()) {
		$idx = $this->idx;
		$element = $this->baseElement('radio', $name, $label);
		if ($element === null) { return false; }
		$element->checked = (int)$checked;
		$element->vertical_options = 0;
		$element->class = 'inputbox';
		$this->addAttributes($attributes, $element);

		$eLang = eFactory::getLang();
		$options = array();
		$options[] = $this->makeOption(1, $eLang->get('SHOW'));
		$options[] = $this->makeOption(0, $eLang->get('HIDE'));
		$element->options = $options;

		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/***********************/
	/* ADD CHECK BOX FIELD */
	/***********************/
	public function addCheckbox($name, $label='', $checked=null, $options=array(), $attributes=array()) {
		if (!is_array($options) || (count($options) == 0)) {
			$this->errormsg = 'A check box field must have at least one option!';
			return false;
		}
		$idx = $this->idx;
		$element = $this->baseElement('checkbox', $name, $label);
		$element->vertical_options = 0;
		if ($element === null) { return false; }
		$this->addAttributes($attributes, $element);
		$element->options = $options;
		$element->checked = (is_array($checked)) ? $checked : array((string)$checked);
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/***************/
	/* MAKE OPTION */
	/***************/
	public function makeOption($value, $label, $attributes=array(), $disabled=0, $optgroup='') {
		$disabled = (int)$disabled;
		$attributes = (is_array($attributes)) ? $attributes : array();
		return array(
			'value' => $value,
			'label' => $label, 
			'attributes' => $attributes,
			'disabled' => $disabled,
			'optgroup' => $optgroup
		);
	}


	/****************/
	/* ADD RAW HTML */
	/****************/
	public function addHTML($html) {
		$idx = $this->idx;
		$element = $this->baseElement('html', 'html_'.$idx, '');
		if ($element === null) { return false; }
		$element->extra['html'] = $html;
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/************/
	/* ADD NOTE */
	/************/
	public function addNote($text='', $class='elx_info') {
		$idx = $this->idx;
		$element = $this->baseElement('note', 'note_'.$idx, '');
		if ($element === null) { return false; }
		if ($class == '') { $class = 'elx_info'; }
		$element->extra['html'] = '<div class="'.$class.'">'.$text."</div>\n";
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/********************************************/
	/* ADD INFORMATIONAL LINE (NOTE WITH LABEL) */
	/********************************************/
	public function addInfo($label='', $text='') {
		$idx = $this->idx;
		$element = $this->baseElement('info', 'info_'.$idx, $label);
		if ($element === null) { return false; }
		$float = ($this->dir == 'rtl') ? 'right' : 'left';
		$element->extra['html'] = '<div style="float:'.$float.'; padding:0; margin:0;">'.$text."</div>\n".'<div style="clear:both;"></div>'."\n";
		$this->elements[$idx] = $element;
		$this->idx++;
		return true;
	}


	/**********************************/
	/* ADD SEO SUGGEST/VALIDATE LINKS */
	/**********************************/
	public function addSEO($name, $seoname, $sugfunc='', $valfunc='', $sargs=array(), $vargs=array()) {
		$eLang = eFactory::getLang();
		if ($sugfunc == '') { $sugfunc = 'suggestSEO'; }
		if ($valfunc == '') { $valfunc = 'validateSEO'; }
		$updateid = 'valseo'.$this->idx;
		if ($name == '') { $name = 'title'; }
		if ($seoname == '') { $seoname = 'seotitle'; }
		$titleid = $this->idprefix.$name;
		$seotitleid = $this->idprefix.$seoname;

		$onsuggest = $sugfunc.'(\''.$titleid.'\', \''.$seotitleid.'\', \''.$updateid.'\'';
		if (is_array($sargs) && (count($sargs) > 0)) { $onsuggest .= ', \''.implode('\', \'', $sargs).'\''; }
		$onsuggest .= ')';

		$onvalidate = $valfunc.'(\''.$seotitleid.'\', \''.$updateid.'\'';
		if (is_array($vargs) && (count($vargs) > 0)) { $onvalidate .= ', \''.implode('\', \'', $vargs).'\''; }
		$onvalidate .= ')';

		$txt = '<a href="javascript:void(null);" onclick="'.$onsuggest.'" class="elx_suggest">'.$eLang->get('SUGGESTED')."</a> \n";
		$txt .= '<a href="javascript:void(null);" onclick="'.$onvalidate.'" class="elx_validate">'.$eLang->get('VALIDATE')."</a><br />\n";
		$txt .= '<div id="'.$updateid.'" style="display:none;"></div>'."\n";
		$this->addInfo('', $txt);
	}


	/**********************************/
	/* GENERATE VALIDATION JAVASCRIPT */
	/**********************************/
	private function makejavascript() {
		$js = 'function elxformval'.$this->name."() {\n";
		$js .= 'if (1 == 2) { alert(\'What a strange world!\'); }'."\n";
		if (count($this->fields_required) > 0) {
			foreach ($this->fields_required as $fld) {
				$js .= 'else if (document.getElementById(\''.$fld.'\').value == \'\') {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('REQFIELDEMPTY')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_email) > 0) {
			foreach ($this->fields_email as $fld) {
				$js .= 'else if (!elxValidateEmailBox(\''.$fld.'\', true)) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('INVALIDEMAIL')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_date) > 0) {
			foreach ($this->fields_date as $fld) {
				$js .= 'else if (!elxValidateDateBox(\''.$fld.'\', \''.$this->date_format.'\', true)) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('INVALID_DATE')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_datetime) > 0) {
			foreach ($this->fields_datetime as $fld) {
				$js .= 'else if (!elxValidateDateBox(\''.$fld.'\', \''.$this->datetime_format.'\', true)) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('INVALID_DATE')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_number) > 0) {
			foreach ($this->fields_number as $fld) {
				$js .= 'else if (!elxValidateNumericBox(\''.$fld.'\', true)) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('INVALID_NUMBER')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_url) > 0) {
			foreach ($this->fields_url as $fld) {
				$js .= 'else if (!elxValidateURLBox(\''.$fld.'\', true)) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('INVALID_URL')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if (count($this->fields_match) > 0) {
			foreach ($this->fields_match as $fld => $initial) {
				$js .= 'else if (document.getElementById(\''.$fld.'\').value != document.getElementById(\''.$initial.'\').value) {'."\n";
				$js .= "\t".'alert(\''.addslashes(eFactory::getLang()->get('PASSNOMATCH')).'\'); elxFocus(\''.$fld.'\'); return false;'."\n";
				$js .= "}\n";
			}
		}
		if ($this->jsonsubmit != '') {
			if (strpos($this->jsonsubmit, '(') !== false) {
				$js .= 'else { '.$this->jsonsubmit.'; return false; }'."\n";
			} else {
				$js .= 'else { '.$this->jsonsubmit.'(); return false; }'."\n";
			}
		} else {
			$js .= 'else { return true; }'."\n";
		}
		$js .= "}\n";

		if (is_object($this->ml) && (count($this->ml->items) > 0)) {
			$rtl_langs = array();
			foreach ($this->ml->langs as $lng => $linfo) {
				if ($linfo['DIR'] == 'rtl') { $rtl_langs[] = $lng; }
			}
			if ($this->mlapibase != '') { //custom ML API
				$js .= "\n".'var mlapibase = \''.$this->mlapibase.'\';'."\n";
			} else if (!defined('ELXIS_ADMIN')) { //currently enabled only for backend!
				$js .= "\n".'var mlapibase = \''.eFactory::getElxis()->makeURL('etranslator:api/', 'inner.php').'\';'."\n";
			} else {
				$js .= "\n".'var mlapibase = \''.eFactory::getElxis()->makeAURL('etranslator:api/', 'inner.php').'\';'."\n";
			}
			$js .= 'var mldata'.$this->ml->instance.' = {'."\n";
			$js .= "\t".'formname: \''.$this->name.'\', lang: \''.$this->ml->lang.'\', dir: \''.$this->ml->dir.'\', waitmsg: \''.addslashes(eFactory::getLang()->get('PLEASE_WAIT')).'\','."\n";
			$js .= "\t".'prtransmsg: \''.addslashes(eFactory::getLang()->get('PROVIDE_TRANS')).'\', bingapi: \''.$this->ml->bingapi.'\', ';
			if (count($rtl_langs) > 0) {
				$js .= 'rtllangs: [\''.implode('\',\'', $rtl_langs).'\'],'."\n";
			} else {
				$js .= 'rtllangs: [],'."\n";
			}
			$js .= "\t".'items: ['."\n";;
			$n = count($this->ml->items);
			$i = 1;
			foreach ($this->ml->items as $item) {
				$js .= "\t\t".'{item: \''.$item->itemid.'\', ctg: \''.$item->ctg.'\', elem: \''.$item->elem.'\', elid: \''.$item->elid.'\'}';
				if ($i < $n) { $js .= ','; }
				$js .= "\n";
				$i++;
			}
			$js .= "\t]\n";
			$js .= "};\n";

		}
		return $js;
	}


/*************************** RENDERERS ***************************/


	/*****************************/
	/* RENDER AN ELEMENT'S LABEL */
	/*****************************/
	private function makeLabel($element) {
		if (trim($element->label) == '') {
			return ($this->label_top == 1) ? '' : '<div class="elx_form_nolabel" style="width:'.$this->label_width.'px;">&#160;</div>';
		}
		$css_class = ($this->label_top == 1) ? 'elx_form_label_top' : 'elx_form_label';
		$for_str = ($element->type == 'radio') ? '' : ' for="'.$element->id.'"';
		$html = '<label'.$for_str.' class="'.$css_class.'" style="width:'.$this->label_width.'px; text-align: '.$this->label_align.';">';
		$html .= $element->label;
		if ($element->required == 1) { $html .= '*'; }
		$html .= '</label>';
		return $html;
	}


	/******************************/
	/* COMPOSE FINAL ELEMENT HTML */
	/******************************/
	private function compose($label, $field, $tip) {
		$html = '';
		if ($this->label_top == 1) {
			if ($label != '') { $html .= "\t\t\t".$label."<br />\n"; }
			if (trim($tip) != '') {
				$html .= $field;
				if ($this->tip_style == 2) { $html .= "<br />\n"; }
				$html .= "\t\t\t".$this->makeTip($tip)."\n";
			} else {
				$html .= $field;
			}
		} else {
			$html .= "\t\t\t".$label."\n";
			if (trim($tip) != '') {
				switch ($this->tip_style) {
					case 2: //bellow
						$dir = ($this->dir == 'rtl') ? 'right' : 'left';
						$html .= "\t\t\t".'<div class="elx_form_field_box" style="margin-'.$dir.':'.$this->label_width.'px;">'."\n";
						$html .= $field."<br />\n";
						$html .= "\t\t\t".$this->makeTip($tip)."\n";
						$html .= "\t\t\t</div>\n";
						$html .= "\t\t\t".'<div style="clear:both;"></div>'."\n";
					break;
					case 1: //js tooltip right
					case 0: default: //right
						$html .= $field;
						$html .= "\t\t\t".$this->makeTip($tip)."\n";
					break;
				}
			} else {
				$html .= $field;
			}
		}
		return $html;
	}


	/*********************************************/
	/* COMPOSE FINAL RADIO/CHECKBOX ELEMENT HTML */
	/*********************************************/
	private function composeRadioCheckBox($label, $fieldwithtip) {
		$html = '';
		if ($this->label_top == 1) {
			if ($label != '') { $html .= "\t\t\t".$label."<br />\n"; }
			$html .= $fieldwithtip;
		} else {
			$dir = ($this->dir == 'rtl') ? 'right' : 'left';
			$html .= "\t\t\t".$label."\n";
			$html .= "\t\t\t".'<div class="elx_form_field_box" style="margin-'.$dir.':'.$this->label_width.'px;">'."\n";
			$html .= $fieldwithtip;
			$html .= "\t\t\t</div>\n";
			$html .= "\t\t\t".'<div style="clear: both;"></div>'."\n";
		}
		return $html;
	}


	/*****************/
	/* MAKE TIP HTML */
	/*****************/
	private function makeTip($tip) {
		if ($tip == '') { return ''; }
		if ($this->tip_style == 1) { //only for admin area
			$parts = preg_split('/\:/', $tip, 2, PREG_SPLIT_NO_EMPTY);
			if (isset($parts[1])) {
				$img = eFactory::getElxis()->icon($parts[0], 16);
				return ' <img src="'.$img.'" alt="tip" class="elx_tooltip" title="'.$parts[1].'" />';
			} else {
				$img = eFactory::getElxis()->icon('info', 16);
				return ' <img src="'.$img.'" alt="tip" class="elx_tooltip" title="'.$parts[0].'" />';
			}
		} else {
			$parts = preg_split('/\|/', $tip, 2, PREG_SPLIT_NO_EMPTY);
			if (count($parts) == 2) {
				return '<span class="elx_form_tip"><strong>'.$parts[0].'</strong> '.$parts[1].'</span>';
			} else {
				return '<span class="elx_form_tip">'.$tip.'</span>';
			}
		}
	}


	/***********************/
	/* RENDER TEXT ELEMENT */
	/***********************/
	private function renderText($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<input type="text" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		if (isset($element->currency)) { $field .= $element->currency."\n"; } //render price
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/*************************/
	/* RENDER SLIDER ELEMENT */
	/*************************/
	private function renderSlider($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<span id="slider_'.$element->id.'"></span> ';
		$field .= '<input type="text" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " /> \n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***********************************/
	/* RENDER DATE & DATETIME ELEMENTS */
	/***********************************/
	private function renderDate($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<input type="text" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " /> \n";
		$img = eFactory::getElxis()->icon('calendar', 16);
		$field .= '<img src="'.$img.'" id="trig'.$element->id.'" alt="calendar" border="0" align="top" style="margin:2px 2px 0 2px;" />'."\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***************************/
	/* RENDER DATETIME ELEMENT */
	/***************************/
	private function renderDatetime($element) {
		return $this->renderDate($element);
	}


	/**************************/
	/* RENDER CAPTCHA ELEMENT */
	/**************************/
	private function renderCaptcha($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<span dir="ltr">'.$element->number1.' '.$element->operator.' '.$element->number2." =</span>\n";
		$field .= "\t\t\t".'<input type="text" name="'.$element->name.'" id="'.$element->id.'" value=""';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***********************/
	/* RENDER FILE ELEMENT */
	/***********************/
	private function renderFile($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<input type="file" name="'.$element->name.'" id="'.$element->id.'" value=""';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->accept != '') { $field .= ' accept="'.$element->accept.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/************************/
	/* RENDER IMAGE ELEMENT */
	/************************/
	private function renderImage($element) {
		if (!is_object($element)) { return ''; }
		$align = ($this->dir == 'rtl') ? 'right' : 'left';
		if ($element->value != '') {
			$imgname = '';
			$parts = preg_split('#\/#', $element->value, -1, PREG_SPLIT_NO_EMPTY);
			if (is_array($parts) && (count($parts) > 0)) {
				$i = count($parts) -1;
				$imgname = ' '.$parts[$i];
			}
			if (strpos($element->value, 'http') === 0) {
				$imghtml = '<img src="'.$element->value.'" alt="preview" align="'.$align.'" class="elx_thumb" style="width:50px; height:50px; margin:4px;" />';
			} else {
				if (($imgname != '') && file_exists(ELXIS_PATH.'/'.$element->value)) {
					$info = getimagesize(ELXIS_PATH.'/'.$element->value);
					$imgname .= ' ('.$info[0].'x'.$info[1].', '.round((filesize(ELXIS_PATH.'/'.$element->value) / 1024), 2).' KB)';
				}
				$imghtml = '<img src="'.eFactory::getElxis()->secureBase().'/'.$element->value.'" alt="preview" align="'.$align.'" class="elx_thumb" style="width:50px; height:50px; margin:4px;" />';
			}
			$imghtml .= $imgname."<br />\n";
		} else {
			$imghtml = '<img src="'.eFactory::getElxis()->secureBase().'/templates/system/images/nopicture.png" alt="preview" align="'.$align.'" class="elx_thumb" style="width:50px; height:50px; margin:4px;" />';
			$imghtml .= ' '.eFactory::getLang()->get('NO_IMAGE_UPLOADED')."<br />\n";
		}
		$field = $imghtml;

		$field .= "\t\t\t".'<input type="file" name="'.$element->name.'" id="'.$element->id.'" value=""';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->accept != '') { $field .= ' accept="'.$element->accept.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/*************************/
	/* RENDER BUTTON ELEMENT */
	/*************************/
	private function renderButton($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<button type="'.$element->button_type.'" name="'.$element->name.'" id="'.$element->id.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->disabled == 1) { $field .= ' disabled="disabled"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= '>'.$element->value."</button>\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***************************/
	/* RENDER PASSWORD ELEMENT */
	/***************************/
	private function renderPassword($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<input type="password" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		if ($element->maxlength > 0) { $field .= ' maxlength="'.$element->maxlength.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		$addpmeter = ($element->password_meter == 1) ? true : false;
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
				if ($addpmeter) {
					if (preg_match('#elxPasswordMeter#i', $val)) { $addpmeter = false; }
				}
			}
		}
		if ($addpmeter) {
			$field .= ' onkeyup="elxPasswordMeter(\''.$this->name.'\', \''.$element->id.'\', \'\');"';
		}
		$field .= " />\n";
		if ($element->password_meter == 1) {
			$field .= "\t\t\t".'<img src="'.eFactory::getElxis()->secureBase().'/includes/libraries/elxis/form/level0.png" id="'.$element->id.'meter" alt="strength" title="empty password" border="0" />'."\n";
		}
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***************************/
	/* RENDER TEXTAREA ELEMENT */
	/***************************/
	private function renderTextarea($element) {
		$eLang = eFactory::getLang();

		if (!is_object($element)) { return ''; }
		$js = '';
		$is_editor = 0;
		if (($element->editor == 'html') || ($element->editor == 'bbcode')) {
			$is_editor = 1;
			$element->tip = ''; //disable tips for rich text editor
			eFactory::getDocument()->setContentType('text/html'); //editor doesn't work with application/xhtml+xml
			$elxis = eFactory::getElxis();
			$editor = $elxis->obj('editor');
			$editor->prepare($element->id, $element->editor, $element->contentslang, $element->editoroptions);
			$js = $editor->getJS();
			unset($editor);
			$element->value = htmlspecialchars($element->value);
		}

		$is_multilang = false;
		$idx = $element->idx;

		$field = '';
		if (is_object($this->ml) && isset($this->ml->items[$idx])) {
			$is_multilang = true;
			$distxt = ($this->ml->items[$idx]->elid < 1) ? ' disabled="disabled"' : '';
			if ($this->ml->allowed == false) { $distxt = ' disabled="disabled"'; }
			$field .= "\t\t\t".'<select name="transl_'.$element->name.'" id="transl_'.$element->id.'" class="selectbox mlflag'.$this->ml->lang.'" dir="ltr"'.$distxt.' onchange="translang_edswitch('.$this->ml->instance.', \''.$element->id.'\', '.$is_editor.');">'."\n";
			foreach ($this->ml->langs as $lng => $linfo) {
				$sel = ($lng == $this->ml->lang) ? ' selected="selected"' : '';
				$field .= "\t\t\t".'<option value="'.$lng.'" class="mlflag'.$lng.'"'.$sel.'>'.$lng.' - '.$linfo['NAME']."</option>\n";
			}
			$field .= "</select> \n";

			$field .= '<span id="transb_'.$element->id.'" style="display:none;">'."\n";
			$field .= '<a href="javascript:void(null);" title="'.$eLang->get('SAVE').'" onclick="translang_edsave('.$this->ml->instance.', \''.$element->id.'\', '.$is_editor.')" style="margin:0 4px;">';
			$field .= '<img src="'.$this->ml->isave.'" alt="save" border="0" align="bottom" /></a> ';
			$field .= '<a href="javascript:void(null);" title="'.$eLang->get('DELETE').'" onclick="translang_eddelete('.$this->ml->instance.', \''.$element->id.'\', '.$is_editor.')" style="margin:0 4px;">';
			$field .= '<img src="'.$this->ml->idelete.'" alt="delete" border="0" align="bottom" /></a>';
			$field .= "</span><br />\n";

			if ($this->label_top == 1) {
				$extra_style = '';
			} else if ($this->dir == 'rtl') {
				$w = $this->label_width + 5;
				$extra_style = ' margin: 5px 0; padding:0 '.$w.'px 0 0;';
			} else {
				$w = $this->label_width + 5;
				$extra_style = ' margin: 5px 0; padding:0 0 0 '.$w.'px;';
			}

			$field .= '<div id="transmsg_'.$element->id.'" class="ml_message" style="display:none;'.$extra_style.'"></div>'."\n";
		}

		if ($is_multilang) {
			$field .= '<div style="'.$extra_style.'">'."\n";
		}
		$field .= "\t\t\t".'<textarea name="'.$element->name.'" id="'.$element->id.'"';
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->rows > 0) { $field .= ' rows="'.$element->rows.'"'; }
		if ($element->cols > 0) { $field .= ' cols="'.$element->cols.'"'; }
		if ($element->readonly == 1) { $field .= ' readonly="readonly"'; }
		if ($element->disabled == 1) { $field .= ' disabled="disabled"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}

		if ($is_multilang) {
			if ($is_editor == 0) {
		 		$field .= ' onchange="trans_marktareaunsaved(\''.$element->id.'\');"';
			}
		}

		$field .= '>'.$element->value."</textarea>\n";

		if ($is_multilang) {
			$field .= '<textarea id="transorig_'.$element->id.'" name="transorig_'.$element->name.'" style="display:none;" dir="ltr"></textarea>'."\n";
			$field .= '<input type="hidden" name="transid_'.$element->name.'" id="transid_'.$element->id.'" value="0" />'."\n";
			$field .= '<input type="hidden" name="transbef_'.$element->name.'" id="transbef_'.$element->id.'" dir="ltr" value="'.$this->ml->lang.'" />'."\n";
			$field .= "</div>\n";
		}

		$field .= $js;

		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/***********************/
	/* RENDER TEXT ELEMENT */
	/***********************/
	private function renderHidden($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".'<input type="hidden" name="'.$element->name.'" id="'.$element->id.'" value="'.$element->value.'"';
		$field .= ' dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= " />\n";
		$final = $this->compose('', $field, '');
		return $final;
	}


	/****************************/
	/* RENDER RADIO BOX ELEMENT */
	/****************************/
	private function renderRadio($element) {
		if (!is_object($element)) { return ''; }
		$field = '';
		$label = $this->makeLabel($element);
		$tip = $this->makeTip($element->tip);
		$i = 1;
		$vspace = ($element->vertical_options == 1) ? '<br />' : '';
		$tip_placed = 0;
		foreach ($element->options as $option) {
			$chk = ($option['value'] == $element->checked) ? ' checked="checked"' : '';
			$attr = '';
			if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
				foreach ($option['attributes'] as $key => $val) {
					$attr .= $key.'="'.$val.'"';
				}
			}
			$ttl_str = '';
			$field .= "\t\t\t".'<input type="radio" name="'.$element->name.'" id="'.$element->id.'_'.$i.'" value="'.$option['value'].'"'.$chk.''.$attr.''.$ttl_str.' />'."\n";
			if (trim($option['label']) != '') {
				$ttl_str = ' title="'.$option['label'].'"';
				$field .= "\t\t\t".'<label for="'.$element->id.'_'.$i.'" class="elx_form_label_option">'.$option['label']."</label>\n";
			}

			if (($i == 1) && ($this->tip_style != 2) && ($element->vertical_options == 1)) {
				$tip_placed = 1;
				if ($tip != '') { $field .= "\t\t\t".$tip."\n"; }
			}
			$field .= $vspace;
			$i++;
		}

		if (($tip_placed == 0)  && ($tip != '')) {
			if (($element->vertical_options == 0) && ($this->tip_style == 2)) { $field .= "<br />\n"; }
			$field .= "\t\t\t".$tip."\n";
		}

		$final = $this->composeRadioCheckBox($label, $field);
		return $final;
	}


	/****************************/
	/* RENDER CHECK BOX ELEMENT */
	/****************************/
	private function renderCheckbox($element) {
		if (!is_object($element)) { return ''; }
		$field = '';
		$label = $this->makeLabel($element);
		$tip = $this->makeTip($element->tip);
		$i = 1;
		$vspace = ($element->vertical_options == 1) ? '<br />' : '';
		$tip_placed = 0;
		foreach ($element->options as $option) {
			$chk = (in_array($option['value'], $element->checked)) ? ' checked="checked"' : '';
			$attr = '';
			if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
				foreach ($option['attributes'] as $key => $val) {
					$attr .= $key.'="'.$val.'"';
				}
			}
			$ttl_str = '';
			$field .= "\t\t\t".'<input type="checkbox" name="'.$element->name.'[]" id="'.$element->id.'_'.$i.'" value="'.$option['value'].'"'.$chk.''.$attr.''.$ttl_str.' />'."\n";
			if (trim($option['label']) != '') {
				$ttl_str = ' title="'.$option['label'].'"';
				$field .= "\t\t\t".'<label for="'.$element->id.'_'.$i.'" class="elx_form_label_option">'.$option['label']."</label>\n";
			}

			if (($i == 1) && ($this->tip_style != 2) && ($element->vertical_options == 1)) {
				$tip_placed = 1;
				if ($tip != '') { $field .= "\t\t\t".$tip."\n"; }
			}
			$field .= $vspace;
			$i++;
		}

		if (($tip_placed == 0)  && ($tip != '')) {
			if (($element->vertical_options == 0) && ($this->tip_style == 2)) { $field .= "<br />\n"; }
			$field .= "\t\t\t".$tip."\n";
		}

		$final = $this->composeRadioCheckBox($label, $field);
		return $final;
	}


	/***********************/
	/* RENDER SELECT ELEMENT */
	/***********************/
	private function renderSelect($element) {
		if (!is_object($element)) { return ''; }
		if ($element->multiple == 1) {
			$field = "\t\t\t".'<select name="'.$element->name.'[]" id="'.$element->id.'"';
		} else {
			$field = "\t\t\t".'<select name="'.$element->name.'" id="'.$element->id.'"';
		}
		if ($element->title != '') { $field .= ' title="'.$element->title.'"'; }
		if ($element->disabled == 1) { $field .= ' disabled="disabled"'; }
		if ($element->multiple == 1) { $field .= ' multiple="multiple"'; }
		if ($element->size > 0) { $field .= ' size="'.$element->size.'"'; }
		$field .= ' class="'.$element->class.'" dir="'.$element->dir.'"';
		if (is_array($element->extra) && (count($element->extra) > 0)) {
			foreach ($element->extra as $key => $val) {
				$field .= ' '.$key.'="'.$val.'"';
			}
		}
		$field .= ">\n";
		if (is_array($element->options) && (count($element->options) > 0)) {
			$optgroup = '';
			foreach ($element->options as $option) {
				if ($option['optgroup'] != '') {
					if ($optgroup == '') {
						$field .= "\t\t\t".'<optgroup label="'.$option['optgroup'].'">'."\n";
					} else {
						if ($option['optgroup'] != $optgroup) {
							$field .= "\t\t\t</optgroup>\n";
							$field .= "\t\t\t".'<optgroup label="'.$option['optgroup'].'">'."\n";
						}
					}
					$optgroup = $option['optgroup'];
				} else {
					if ($optgroup != '') { $field .= "\t\t\t</optgroup>\n"; }
					$optgroup = '';
				}

				$dis = ($option['disabled'] == 1) ? ' disabled="disabled"' : '';
				if ($element->multiple == 1) {
					$sel = in_array($option['value'], $element->selected) ? ' selected="selected"' : '';
				} else {
					$sel = ($option['value'] == $element->selected) ? ' selected="selected"' : '';
				}
				
				$attr = '';
				if (is_array($option['attributes']) && (count($option['attributes']) > 0)) {
					foreach ($option['attributes'] as $key => $val) {
						$attr .= $key.'="'.$val.'"';
					}
				}
				
				$field .= "\t\t\t".'<option value="'.$option['value'].'"'.$dis.''.$sel.''.$attr.'>'.$option['label']."</option>\n";
			}

			if ($optgroup != '') { $field .= "\t\t\t</optgroup>\n"; }
		}
		$field .= "\t\t\t</select>\n";

		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, $element->tip);
		return $final;
	}


	/************************/
	/* RENDER RAW HTML CODE */
	/************************/
	private function renderHtml($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".$element->extra['html']."\n";
		$final = $this->compose('', $field, '');
		return $final;
	}


	/***************/
	/* RENDER NOTE */
	/***************/
	private function renderNote($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".$element->extra['html']."\n";
		$final = $this->compose('', $field, '');
		return $final;
	}


	/**********************/
	/* RENDER INFORMATION */
	/**********************/
	private function renderInfo($element) {
		if (!is_object($element)) { return ''; }
		$field = "\t\t\t".$element->extra['html']."\n";
		$label = $this->makeLabel($element);
		$final = $this->compose($label, $field, '');
		return $final;
	}


	/***************/
	/* RENDER FORM */
	/***************/
	public function render() {
		if ($this->errormsg != '') {
			$this->showError();
			return;
		}

		if ($this->token === true) {
			$token = md5(uniqid(rand(), true));
			eFactory::getSession()->set('token_'.$this->name, $token);
			$token_injected = false;
		} else {
			$token_injected = true;
		}

		$js = $this->makejavascript();
		eFactory::getDocument()->addScript($js);
		unset($js);

		if ($this->attributes != '') {
			$extra_attr = ' '.$this->attributes.' onsubmit="return elxformval'.$this->name.'();"';
		} else {
			$extra_attr = ' onsubmit="return elxformval'.$this->name.'();"';
		}

		echo "\n".'<form name="'.$this->name.'" class="'.$this->cssclass.'" method="'.$this->method.'" action="'.$this->action.'" enctype="'.$this->enctype.'"'.$extra_attr.">\n";
		$fset = 0;
		$tab = 0;
		$actrow = 0;

		if ($this->tabs) {
			echo '<ul class="tabs">'."\n";
			foreach ($this->tabs as $tidx => $tabtitle) {
				echo "\t".'<li><a href="#tab_'.$this->name.'_'.$tidx.'">'.$tabtitle."</a></li>\n";
			}
			echo "</ul>\n";
			echo '<div class="tab_container">'."\n";
		}

		foreach ($this->elements as $idx => $element) {
			$tidx = (int)$element->tab;
			$fidx = (int)$element->fieldset;
			$ridx = (int)$element->row;
			$closed_row = 0;
			if (($element->type == 'button') && !$token_injected) {
				$token_injected = true;
				echo "\t\t\t".'<input type="hidden" name="token" id="'.$this->idprefix.'token" value="'.$token.'" />'."\n";
			}

			$open_tab = false;
			$close_tab = false;
			$open_fset = false;
			$close_fset = false;
			$open_row = false;
			$close_row = false;
			if ($tidx > 0 && ($tidx != $tab)) {
				$open_tab = true;
				if ($tab > 0) { $close_tab = true; }
			}
			if ($tidx == 0 && ($tab > 0)) { $close_tab = true; }
			if ($fidx > 0 && ($fidx != $fset)) {
				$open_fset = true;
				if ($fset > 0) { $close_fset = true; }
			}
			if ($fidx == 0 && ($fset > 0)) { $close_fset = true; }
			if ($ridx > 0 && ($ridx != $actrow)) {
				$open_row = true;
				if ($actrow > 0) { $close_row = true; }
			}
			if ($ridx == 0 && ($actrow > 0)) { $close_row = true; }
			$tab = $tidx;
			$fset = $fidx;
			$actrow = $ridx;

			if ($close_row) {
				echo "\t\t".'<div style="clear:both;"></div>'."\n";
				echo "\t</div>\n";
			}
			if ($close_fset) {
				echo "</fieldset>\n";
			}
			if ($close_tab) {
				echo "</div>\n";
			}
			if ($open_tab) {
				echo '<div id="tab_'.$this->name.'_'.$tidx.'" class="tab_content">'."\n";
			}
			if ($open_fset) {
				echo '<fieldset class="elx_form_fieldset">'."\n";
				echo (trim($this->fieldsets[$fidx]) != '') ? "\t".'<legend class="elx_form_legend">'.$this->fieldsets[$fidx]."</legend>\n" : '';
			}
			if ($open_row) {
				echo "\t".'<div class="elx_form_row">'."\n";
			}

			if ($ridx > 0) {
				$w = intval(100 / $this->row_columns[$ridx]) - 1;
				$float = ($this->dir == 'rtl') ? 'right' : 'left';
				echo "\t\t".'<div class="elx_form_cell" style="float:'.$float.'; width:'.$w.'%;">'."\n";
				$func = 'render'.ucfirst($element->type);
				echo $this->$func($element);
				echo "\t\t</div>\n";
			} else {
				if ($element->type != 'hidden') { echo "\t".'<div class="elx_form_row">'."\n"; }
				$func = 'render'.ucfirst($element->type);
				echo $this->$func($element);
				if ($element->type != 'hidden') { echo "\t</div>\n"; }
				$actrow = 0;
			}
		}

		if ($actrow > 0) {
			echo "\t\t".'<div style="clear:both;"></div>'."\n";
			echo "</div>\n";
		}
		if ($fset > 0) { echo "</fieldset>\n"; }

		if ($this->tabs) {
			if ($tab > 0) {
				echo "</div>\n";
			}
			echo "</div>\n";
			echo '<div style="clear:both;"></div>'."\n";
		}

		if (!$token_injected) {
			$token_injected = true;
			echo "\t\t\t".'<input type="hidden" name="token" id="'.$this->idprefix.'token" value="'.$token.'" />'."\n";
		}
		if ($this->elxisbase === true) {
			echo "\t\t\t".'<input type="hidden" name="elxisbase" id="elxisbase'.$this->name.'" value="'.eFactory::getElxis()->secureBase().'" />'."\n";
		}
		echo "</form>\n";

		if ($this->autocomplete_off === true) {
			foreach ($this->elements as $element) {
				if (($element->type == 'text') || ($element->type == 'password')) {
					$js = 'elxAutocompOff(\''.$element->id.'\');';
					eFactory::getDocument()->addScript($js);
				}
			}
		}
		
		$nd = count($this->fields_date);
		$ndt = count($this->fields_datetime);
		if (($nd >0) || ($ndt > 0)) {
			$calendar = eFactory::getElxis()->obj('calendar');
			$idx = 0;
			if ($nd > 0) {
				foreach ($this->fields_date as $fld) {
					echo $calendar->makeCalendar($fld, 'trig'.$fld, $this->date_format, $idx);
					$idx++;
				}
			}
			if ($ndt > 0) {
				foreach ($this->fields_datetime as $fld) {
					echo $calendar->makeCalendar($fld, 'trig'.$fld, $this->datetime_format, $idx);
					$idx++;
				}
			}
		}

		if (count($this->fields_slider) > 0) {
			$elxisbase = eFactory::getElxis()->secureBase();
			eFactory::getDocument()->addScriptLink($elxisbase.'/includes/libraries/elxis/form/slider.js');
			echo '<script type="text/javascript">'."\n";
			foreach ($this->fields_slider as $fld => $arr) {
				echo 'elxis_form_slider(\'slider_'.$fld.'\', document.getElementById(\''.$fld.'\'),'.$arr[0].','.$arr[1].','.$arr[2].',null,\''.$elxisbase.'\');'."\n";
			}
			echo "</script>\n";
		}
	}


	/*******************************************/
	/* 64BIT SAFE FORMAT GMDATE FOR DATE FIELD */
	/*******************************************/
	private function safe64bit_gmdate($y, $m, $d, $h=12, $i=0, $s=0, $datetime=false) {
		$format = ($datetime === true) ? $this->datetime_format : $this->date_format;
		if ($y < 2038) {
			return gmdate($format, gmmktime($h, $i, $s, $m, $d, $y));
		}
		if ($this->php64bit === -1) {
			$int = "9223372036854775807";
			$int = intval($int);
			$this->php64bit = ($int == 9223372036854775807) ? 1 : 0;
		}

		if ($this->php64bit === 1) {
			return gmdate($format, gmmktime($h, $i, $s, $m, $d, $y));
		}

		$d = sprintf("%02d", $d);
		$m = sprintf("%02d", $m);
		$h = sprintf("%02d", $h);
		$i = sprintf("%02d", $i);
		$s = sprintf("%02d", $s);
		switch ($format) {
			case 'm/d/Y': return $m.'/'.$d.'/'.$y; break;
			case 'm-d-Y': return $m.'-'.$d.'-'.$y; break;
			case 'Y/m/d': return $y.'/'.$m.'/'.$d; break;
			case 'Y-m-d': return $y.'-'.$m.'-'.$d; break;
			case 'd/m/Y': return $d.'/'.$m.'/'.$y; break;
			case 'd-m-Y': return $d.'-'.$m.'-'.$y; break;
			case 'm/d/Y H:i:s': return $m.'/'.$d.'/'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'm-d-Y H:i:s': return $m.'-'.$d.'-'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'Y/m/d H:i:s': return $y.'/'.$m.'/'.$d.' '.$h.':'.$i.':'.$s; break;
			case 'Y-m-d H:i:s': return $y.'-'.$m.'-'.$d.' '.$h.':'.$i.':'.$s; break;
			case 'd/m/Y H:i:s': return $d.'/'.$m.'/'.$y.' '.$h.':'.$i.':'.$s; break;
			case 'd-m-Y H:i:s': return $d.'-'.$m.'-'.$y.' '.$h.':'.$i.':'.$s; break;
			default: return $y.'-'.$m.'-'.$d.' '.$h.':'.$i.':'.$s; break;
		}
	}
					
}

?>