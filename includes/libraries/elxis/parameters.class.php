<?php 
/**
* @version		$Id: parameters.class.php 1409 2013-04-19 19:41:59Z datahell $
* @package		Elxis
* @subpackage	XML
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisParameters {

	private $raw = null; //raw parameters string or array
	private $path = '';
	private $type = 'component';
	private $params = null; //instance of stdClass of parsed raw parameters
	private $xml = null; //simple xml instance
	private $hasParams = false;
	private $errormsg = ''; //last error message
	private $lang = 'en'; //current language indentifier
	private $deflang = 'en'; //site default language
	private $dir = 'ltr'; //current language direction
	private $langFile = ''; //language file to include (if any) for multilingual labels/descriptions
	private $params_group = 1;
	private $uri_lang = '';
	private $translate = false;
	private $uploadFields = array();
	private $groupsVisibility = array();
	private $multilinguism = 0;
	private $mlitems = array();//multilingual text fields
	private $ml = null; //multilingual information


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($raw, $path='', $type='component') {
	    $eLang = eFactory::getLang();
	    $elxis = eFactory::getElxis();

		$this->raw = $raw;
	    $this->path = $path;
	    $this->type = $type;
	    $this->lang = $eLang->currentLang();
	    $this->deflang = $elxis->getConfig('LANG');
	    $this->dir = $eLang->getinfo('DIR');
		$this->uri_lang = eFactory::getURI()->getUriLang();
		$this->multilinguism = $elxis->getConfig('MULTILINGUISM');
		if ($this->multilinguism == 1) {
			if ($this->uri_lang != '') { $this->translate = true; }
		}
	    $this->params = $this->parse($raw);
	}


	/**************************/
	/* SET VALUE TO PARAMETER */
	/**************************/
	public function set($key, $value='') {
		$this->params->$key = $value;
		return $value;
	}


	/******************************************/
	/* SET DEFAULT VALUE IF NO VALUE ASSIGNED */
	/******************************************/
	public function def($key, $value='') {
	    return $this->set($key, $this->get($key, $value));
	}


	/*************************/
	/* GET PARAMETER'S VALUE */
	/*************************/
	public function get($key, $default='') {
	    if (isset($this->params->$key)) {
	        return ($this->params->$key === '') ? $default : $this->params->$key;
		} else {
		    return $default;
		}
	}


	/**************************************/
	/* GET MULTILINGUAL PARAMETER'S VALUE */
	/**************************************/
	public function getML($key, $default='') {
		if ($this->multilinguism == 0) { return $this->get($key, $default); }
		$mlkey = $key.'_ml'.$this->lang;
		if (isset($this->params->$mlkey)) {
			if ($this->params->$mlkey != '') { return $this->params->$mlkey; }
		}
		return $this->get($key, $default);
	}


	/*********************************************************/
	/* GET ALL PARAMS FROM AN XML FILE WITH THEIR ATTRIBUTES */
	/*********************************************************/
	public function allParams($xmlpath='') {
		if ($xmlpath == '') { $xmlpath = $this->path; }
		if ((trim($xmlpath) == '') || !is_file($xmlpath)) { return array(); }
		libxml_use_internal_errors(true);
		$xmlDoc = simplexml_load_file($xmlpath, 'SimpleXMLElement');
		if (!$xmlDoc) { return array(); }
		if (($xmlDoc->getName() != 'package') && ($xmlDoc->getName() != 'elxisparameters')) { return array(); }
		if (!isset($xmlDoc->params)) { return array(); }
		if (count($xmlDoc->params->children()) == 0) { return array(); }

		$all_params = array();
		foreach ($xmlDoc->params as $params) {
			if (!isset($params->param)) { continue; }
			foreach ($params->children() as $param) {
				$attrs = $param->attributes();
				if ($attrs && isset($attrs['name'])) {
					$name = (string)$attrs['name'];
					$all_params[$name] = array();
					foreach ($attrs as $k => $v) {
						if ($k == 'name') { continue; }
						$v = (string)$v;
						$all_params[$name][$k] = trim($v);
					}
				}
			}
		}

		return $all_params;
	}


	/**************************************************/
	/* GET UPLOAD FIELDS FOUND IN RENDERED PARAMETERS */
	/**************************************************/
	public function getUpload() {
		return $this->uploadFields;
	}


	/******************************/
	/* PARSE RAW STRING OR ARRAY */
	/******************************/
	private function parse($raw) {
	    if (is_string($raw)) {
			$lines = explode("\n", $raw);
		} else if (is_array($raw)) {
		    $lines = $raw;
		} else {
		    $lines = array();
		}

		$obj = new stdClass();
	    if (!$lines) { return $obj; }
	    foreach ($lines as $line) {
	        $line = eUTF::trim($line);
	        if ($line == '') { continue; }
	        if ($pos = strpos($line, '=')) {
	        	$property = trim(substr($line, 0, $pos));
	        	$value = eUTF::trim(eUTF::substr($line, $pos + 1, 1000));
	            if ($value == 'false') { $value = false; }
	            if ($value == 'true') {	$value = true; }
	            if ((eUTF::substr($value, 0, 1) == '"') && (eUTF::substr($value, -1, 1) == '"')) {
	                $value = stripcslashes(eUTF::substr($value, 1, eUTF::strlen($value) - 2));
	            }
				$obj->$property = $value;
	        }
	    }

	    return $obj;
	}


	/***********************/
	/* PERFORM FILE UPLOAD */
	/***********************/
	private function uploadFile($name, $attrs) {
		if (!defined('ELXIS_ADMIN')) { return false; }
		if (!isset($_FILES[$name]) || !is_array($_FILES[$name])) { return false; }
		if (eFactory::getElxis()->getConfig('SECURITY_LEVEL') > 0) { return false; }

		$eFiles = eFactory::getFiles();
		$file = $_FILES[$name];
		if (!isset($file['tmp_name']) || ($file['tmp_name'] == '') || !is_uploaded_file($file['tmp_name'])) { return false; }
		$fname = eUTF::strtolower($file['name']);
		$ext = $eFiles->getExtension($fname);
		if ($ext == '') { return false; }

		$allowed_exts = array(
			'png', 'jpg', 'jpeg', 'gif', 'ico', 'svg', 'psd', 'bmp', 'tiff', 'tif', 
			'mp3', 'ogg', 'ogv', 'avi', 'mpg', 'mpeg', 'wma', 'wmv', 'mkv', 'aac', 'mp4', 'mp3', 'webm', 
			'mpa', '3gp', 'asf', 'asx', 'mov', 'rm', 'ra', 'm4a', 'mid', 'wav', 'flv', 'swf', 
			'doc', 'docx', 'pps', 'ppt', 'smil', 'xlsx', 'xls', 'csv', 'odt', 'odp', 'odf', 'ods', 'rtf', 'pdf', 'txt', 'srt', 'vtt', 
			'xsl', 'xslt', 'css', 'xml', 
			'zip', 'rar', 'tar', 'gz', 'bzip2', 'gzip'
		);
  		if (!in_array($ext, $allowed_exts)) { return false; }
		if (!isset($attrs['path']) || ($attrs['path'] == '') || ($attrs['path'] == '/')) { return false; }
		if (isset($attrs['filetype']) && ($attrs['filetype'] != '')) {
			$valid_exts = explode(',', $attrs['filetype']);
			if (!in_array($ext, $valid_exts)) { return false; }
		}

		if (isset($attrs['maxsize']) && (intval($attrs['maxsize']) > 0)) {
			if ($file['size'] > (int)$attrs['maxsize']) { return false; }
		}

		$lowfilename = $name.'.'.$ext;
		$resizewidth = (isset($attrs['resizewidth'])) ? (int)$attrs['resizewidth'] : 0;
		$resizeheight = (isset($attrs['resizeheight'])) ? (int)$attrs['resizeheight'] : 0;

		$attrs['path'] = $this->msReplacer($attrs['path']);

		if ($eFiles->upload($file['tmp_name'], $attrs['path'].$lowfilename)) {
			if (($resizewidth > 0) && ($resizeheight > 0) && in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) {
				$eFiles->resizeImage($attrs['path'].$lowfilename, $resizewidth, $resizeheight);
			}
			return $attrs['path'].$lowfilename;
		}

		return false;
	}


	/***************************/
	/* MULTISITE PATH REPLACER */
	/***************************/
	private function msReplacer($string) {
		if (strpos($string, 'multisite') === false) { return $string; }
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) {
				$ms_replace = 'site'.ELXIS_MULTISITE; 
				$string = str_replace('{multisite}', $ms_replace, $string);
				$string = str_replace('{multisite/}', $ms_replace.'/', $string);
				$string = str_replace('{/multisite}', '/'.$ms_replace, $string);
				$string = str_replace('{/multisite/}', '/'.$ms_replace.'/', $string);	
				return $string;
			}
		}

		$string = str_replace('{multisite}', '', $string);
		$string = str_replace('{multisite/}', '', $string);
		$string = str_replace('{/multisite}', '', $string);
		$string = str_replace('{/multisite/}', '', $string);
		return $string;
	}


	/*******************************************************/
	/* CONVERT AN ARRAY OF PARAMS (POST REQUEST) TO STRING */
	/*******************************************************/
	public function toString($params, $integers=array(), $strings=array()) {
		$all_params = array();
		$mlparams = array();
		if ($this->path != '') {
			$all_params = $this->allParams();
			if (count($all_params) == 0) { return ''; }
			if ($this->multilinguism == 1) {
				foreach ($all_params as $k => $v) {
					if (($v['type'] == 'text') && isset($v['multilingual']) && ($v['multilingual'] == 1)) { $mlparams[] = $k; }
				}
			}

			if ($params) {
				if ($mlparams) {
					foreach ($mlparams as $mlparam) {
						$kdef = $mlparam.'_ml'.$this->deflang;
						if (isset($params[$mlparam]) && isset($params[$kdef])) {
							$params[$mlparam] = $params[$kdef]; //set element value to default translation value
						}
					}
				}

				foreach ($params as $k => $v) {
					$isml = false;
					if ($mlparams) {
						foreach ($mlparams as $mlparam) {
							if (strpos($k, $mlparam.'_ml') === 0) { $isml = true; break; }
						}
					}
					if (!$isml) {
						if (!isset($all_params[$k])) { unset($params[$k]); }
					}
				}
			}
			foreach ($all_params as $k => $v) {
				if (!isset($params[$k])) { $params[$k] = ''; }
				if ($v['type'] == 'file') {
					$newvalue = $this->uploadFile($k, $v);
					if ($newvalue !== false) {
						$params[$k] = $newvalue;
					}
				}
			}
		}

		if (!is_array($params) || (count($params) == 0)) { return ''; }
		$arr = array();
		foreach ($params as $k => $v) {
			if (!preg_match("/^([a-z0-9\-\_])+$/i", $k)) { continue; }
			if ($all_params) {
				$isml = false;
				if ($mlparams) {
					foreach ($mlparams as $mlparam) {
						if (strpos($k, $mlparam.'_ml') === 0) { $isml = true; break; }
					}
				}
				if (!$isml) {
					if (!isset($all_params[$k])) { continue; }
				}
			}

			if (in_array($k, $integers)) {
				$v = (int)$v;
			} else if (in_array($k, $strings)) {
				$v = filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			}
			$arr[] = $k.'='.$v;
		}

		for ($i=0; $i < count($arr); $i++) {
			if (strstr($arr[$i], "\n")) {
				$arr[$i] = eUTF::str_replace("\n", '<br />', $arr[$i]);
			}
		}

		$str = implode("\n", $arr);
		return $str;
	}

		
	/*************************************************/
	/* RETURN TEXTAREA WITH RAW TEXT ON RENDER ERROR */
	/*************************************************/
	private function displayRaw($raw) {
		return '<textarea name="params" cols="40" dir="ltr" rows="10" class="text_area">'.$raw."</textarea>\n";
	}


	/**************************/
	/* RENDER PARAMETERS HTML */
	/**************************/
	public function render($style=array(), $show_description=false) {
		$this->errormsg = '';
		$this->langFile = '';
	    if ($this->path != '') {
	        if (!is_object($this->xml)) {
				libxml_use_internal_errors(true);
	        	$xmlDoc = simplexml_load_file($this->path, 'SimpleXMLElement');
	        	if (!$xmlDoc) {
					foreach (libxml_get_errors() as $error) {
						$this->errormsg = 'Could not parse XML file. Error: '.$error->message.'. Line: '.$error->line;
						break;
					}
					return $this->displayRaw($this->raw);
	        	}

				if (($xmlDoc->getName() != 'package') && ($xmlDoc->getName() != 'elxisparameters')) {
					$this->errormsg = 'The XML file is not a valid Elxis extension XML!';
					return $this->displayRaw($this->raw);
				}

				$ok = true;
				$attrs = $xmlDoc->attributes();
				if ($attrs) {
					if (!isset($attrs['type']) || ((string)$attrs['type'] != $this->type)) { $ok = false; }
				} else {
					$ok = false;
				}
				
				if (!$ok) {
					$this->errormsg = 'The XML file is not a valid Elxis extension XML for '.$this->type.'!';
					return $this->displayRaw($this->raw);
				}

				if (isset($xmlDoc->language)) {
					$lng = $this->lang;
					$found = false;
					if (isset($xmlDoc->language->$lng)) {
						$langfile = ELXIS_PATH.'/language/'.$lng.'/';
						$langfile .= (string)$xmlDoc->language->$lng;
						if (file_exists($langfile)) {
							$this->langFile = $langfile;
							$found = true;
						}
						unset($langfile);
					}
					if (!$found && isset($xmlDoc->language->en)) {
						$langfile = ELXIS_PATH.'/language/en/';
						$langfile .= (string)$xmlDoc->language->en;
						if (file_exists($langfile)) {
							$this->langFile = $langfile;
						}
						unset($langfile);
					}
					unset($lng, $found);
				}

				if (isset($xmlDoc->params)) {
					if (count($xmlDoc->params->children()) > 0) {
						$this->hasParams = true;
					} else {
						$this->hasParams = false;
					}
				} else {
					$this->hasParams = false;
				}

				$this->xml = $xmlDoc;
	        	unset($xmlDoc);
			}
		}

	    if (!is_object($this->xml)) {
	    	return $this->displayRaw($this->raw);
	    }

		$eLang = eFactory::getLang();
		if ($this->langFile != '') {
			$eLang->loadFile($this->langFile);
		}

		$css_sfx = '';
		$col_style = '';
		if (is_array($style) && (count($style) > 0)) {
			if (isset($style['css_sfx']) && ($style['css_sfx'] != '')) {
				$css_sfx = $style['css_sfx'];
			}
			if (isset($style['width']) && (intval($style['width']) > 0)) {
				$col_style = ' style="width: '.intval($style['width']).'px;"';
			}
		}

		$html = '';
		if (($show_description == true) && isset($this->xml->description)) {
			$description = (string)$this->xml->description;
			if ($description != '') {
				$html .= '<div class="elx_info'.$css_sfx.'">'."\n";
			    $html .= $eLang->silentGet($description)."\n";
			    $html .= "</div>\n";
			}
		}
		
		if (!$this->hasParams) {
			$html .= '<div class="elx_warning'.$css_sfx.'">'.$eLang->get('NO_PARAMS')."</div>\n";
			return $html;
		}

		$data = array();
		foreach ($this->xml->params as $params) { //first pass: render params
			if (!isset($params->param)) { continue; }
			$attrs = $params->attributes();
			$tbldata = array('collapsed' => 0, 'groupname' => '', 'elements' => array());
			$groupid = $this->params_group;
			if ($attrs) {
				$group = '';
				if (isset($attrs['group'])) { $group = (string)$attrs['group']; }
				if (trim($group) != '') { $tbldata['groupname'] = $eLang->silentGet($group); }
				if (isset($attrs['groupid']) && (intval($attrs['groupid']) > 999)) { $groupid = (int)$attrs['groupid']; }
				$tbldata['collapsed'] = (isset($attrs['collapsed'])) ? (int)$attrs['collapsed'] : 0;
				unset($group);
			}

			foreach ($params->children() as $param) {
				$tbldata['elements'][] = $this->renderParam($param);
			}
			$this->params_group++;
			$data[$groupid] = $tbldata;
			unset($tbldata, $attrs, $groupid);
		}

		if (!$data) {
			$html .= '<div class="elx_warning'.$css_sfx.'">'.$eLang->get('NO_PARAMS')."</div>\n";
			return $html;
		}

		foreach ($data as $groupid => $groupdata) { //second pass: make HTML
			$collapsed = $groupdata['collapsed'];
			$groupname = $groupdata['groupname'];
			if ($groupid > 999) {
				if (isset($this->groupsVisibility[$groupid])) {
					$collapsed = ($this->groupsVisibility[$groupid] == 1) ? 0 : 1;
				}
			}

			if ($collapsed == 1) {
				$tbl_style = 'display:none; visibility:hidden;';
				$a_css = 'elx_params_group_collapsed';
			} else {
				$tbl_style = 'display:table; visibility:visible;';
				$a_css = 'elx_params_group';
			}

			if ($groupname != '') {
				$html .= '<a href="javascript:void(null);" class="'.$a_css.'" title="'.$eLang->get('SHOW').'/'.$eLang->get('HIDE').'" ';
				$html .= 'id="params_toggler_'.$groupid.'" onclick="elxToggleParamsGroup('.$groupid.');">'.$groupname."</a>\n";
			}

			$hidden_params = '';
			$html .= '<table width="100%" class="elx_tbl_params'.$css_sfx.'" dir="'.$this->dir.'" id="params_group_'.$groupid.'" style="'.$tbl_style.'">'."\n";
			foreach ($groupdata['elements'] as $result) {
				if ($result[3] == 'hidden') {
					$hidden_params .= $result[1]."\n";
				} elseif ($result[3] == 'comment') {
					$html .= '<tr><td colspan="2"><span class="elx_param_desc'.$css_sfx.'">'.$result[1]."</span></td></tr>\n";
				} else {
					$html .= "<tr>\n";
					$html .= '<td class="elx_param_title'.$css_sfx.'"'.$col_style.'>'.$result[0]."</td>\n";
					if ($result[2] != '') {
						$html .= "<td>\n";
						$html .= "\t".$result[1]."<br />\n";
						$html .= "\t".'<span class="elx_param_desc'.$css_sfx.'">'.$result[2]."</span>\n";
						$html .= "</td>\n";
					} else {
						$html .= '<td>'.$result[1]."</td>\n";
					}
					$html .= "</tr>\n";	
				}
			}
			$html .= "</table>\n";
			$html .= $hidden_params;
		}

		if ($this->multilinguism == 1) {
			$html .= '<div id="mlparamscontainer" style="display:none; visibility:hidden;">'."\n";
			if ($this->mlitems) {
				if ($this->params) {
					$parr = get_object_vars($this->params);
					if ($parr) {
						foreach ($parr as $k => $v) {
							foreach ($this->mlitems as $mlitem) {
								if (strpos($k, $mlitem.'_ml') === 0) {
									$html .= '<input type="hidden" name="params['.$k.']" id="params'.$k.'" value="'.$v.'" dir="ltr" />'."\n";
									break;
								}
							}
						}
					}
				}
			}
			$html .= "</div>\n";
		}

		return $html;
	}


	/**********************************/
	/* SET AND GET MULTILINGUISM INFO */
	/**********************************/
	private function setgetML() {
		if ($this->multilinguism == 0) { return false; }
		if (!empty($this->ml)) { return $this->ml; }

		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$deflang = $this->deflang;
		$slangs = eFactory::getLang()->getSiteLangs(true);
		if (!isset($slangs[$deflang])) {
			$this->errormsg = 'No information found in languages database for the default language '.$deflang.'!';
			return false;
		}

		$ml = new stdClass;
		$ml->lang = $deflang;
		$ml->dir = $slangs[$deflang]['DIR'];
		$ml->langs = array();
		$ml->langs[$deflang] = $slangs[$deflang];
		foreach ($slangs as $lng => $info) {
			if ($lng == $deflang) { continue; }
			$ml->langs[$lng] = $info;
		}
		$this->ml = $ml;

		$eDoc->addStyleLink($elxis->secureBase().'/includes/libraries/elxis/language/mlflag.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_etranslator/includes/mlparams.js');

		return $ml;
	}


	/********************/
	/* RENDER PARAMETER */
	/********************/
	public function renderParam($param) {
		$eLang = eFactory::getLang();
		
	    $result = array();
		$attrs = $param->attributes();
		$name = isset($attrs['name']) ? (string)$attrs['name'] : '';
		$label = isset($attrs['label']) ? (string)$attrs['label'] : '';
		if (trim($label) != '') { $label = $eLang->silentGet($label); }

		$def_value = (string)$attrs['default'];
		$value = $this->get($name, $def_value);
		$description = isset($attrs['description']) ? $eLang->silentGet((string)$attrs['description']) : '';
		$type = (string)$attrs['type'];
		$result[0] = ($label != '') ? $label : $name;
		$method = 'form_'.$type;
		if (method_exists($this, $method)) {
			$result[1] = $this->$method($name, $value, $param);
		} else {
			$result[1] = $this->form_text($name, $value, $param);
		}
		$result[2] = $description;
		$result[3] = $type;
		return $result;
	}


	/**************************/
	/* GET LAST ERROR MESSAGE */
	/**************************/
	public function getErrorMsg() {
		return $this->errormsg;
	}


	/*******************/
	/* MAKE TEXT FIELD */
	/*******************/
	private function form_text($name, $value, $node) {
		$attrs = $node->attributes();
		$size = isset($attrs['size']) ? (int)$attrs['size'] : 25;
		$multilingual = 0;
		if ($this->multilinguism == 1) {
			$multilingual = isset($attrs['multilingual']) ? (int)$attrs['multilingual'] : 0;
		}
		if ($multilingual == 1) {
			$ml = $this->setgetML();
			if (!$ml) { $this->multilinguism = 0; $multilingual = 0; }
		}
		if ($multilingual == 1) { $this->mlitems[] = $name; }

		$isrtl = (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) ? 1 : 0;
		$dir = 'ltr';
		if (($this->dir == 'rtl') && ($isrtl == 1)) { $dir = 'rtl'; }
		$extra = '';
		if (isset($attrs['maxlength']) && (intval($attrs['maxlength']) > 0)) { $extra .= ' maxlength="'.intval($attrs['maxlength']).'"'; }

		if ($multilingual == 1) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();
			$icon = $elxis->icon('save', 16);

			$html = '<input type="text" name="params['.$name.']" id="params'.$name.'" value="'.$value.'" onchange="param_markunsaved(this);" class="inputbox  mlflag'.$ml->lang.'" size="'.$size.'" dir="'.$dir.'"'.$extra.' />'."\n";
			$html .= '<select name="translp_'.$name.'" id="translp_'.$name.'" class="selectbox mlflag'.$ml->lang.'" dir="ltr" onchange="paramlang_switch(\''.$name.'\', '.$isrtl.');">'."\n";
			foreach ($ml->langs as $lng => $data) {
				$sel = ($lng == $ml->lang) ? ' selected="selected"' : '';
				$html .= "\t".'<option value="'.$lng.'" class="mlflag'.$lng.'"'.$sel.'>'.$lng."</option>\n";
			}
			$html .= "</select> \n";
			$html .= '<a href="javascript:void(null);" title="'.$eLang->get('SAVE').'" onclick="paramlang_save(\''.$name.'\')" style="margin:0 4px;"><img src="'.$icon.'" alt="save" border="0" /></a>';
		} else {
			$html = '<input type="text" name="params['.$name.']" value="'.$value.'" class="inputbox" size="'.$size.'" dir="'.$dir.'"'.$extra.' />';
		}

		return $html;
	}


	/******************************/
	/* MAKE DROP DOWN SELECT LIST */
	/******************************/
	private function form_list($name, $value, $node) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}

		$options = array();
		$ashow = array();
		$ahide = array();
		$attribs = '';
		$children = $node->children();
		if ($children) {
			$index = 0;
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$show = isset($attr2['show']) ? (string)$attr2['show'] : '';
				$show = trim($show);
				$hide = isset($attr2['hide']) ? (string)$attr2['hide'] : '';
				$hide = trim($hide);

				if ($show != '') {
					$ashow[] = $index.':'.$show;
					if ($val == $value) {
						$grids = explode(',',$show);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 1; }
						}
					}
				}

				if ($hide != '') {
					$ahide[] = $index.':'.$hide;
					if ($val == $value) {
						$grids = explode(',',$hide);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 0; }
						}
					}
				}

				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) {
					$text = $eLang->silentGet($text);
				}
				$disabled = false;
				if (isset($attr2['disabled']) && (((string)$attr2['disabled'] == 'disabled') || ((int)$attr2['disabled'] == 1))) {
					$disabled = true;
				}
				$options[] = $elxis->obj('HTML')->makeOption($val, $text, $disabled);
				$index++;
			}
		}

		if (count($ashow) > 0) {
			$attribs .= 'elxShowParams(this, \''.implode(';',$ashow).'\', 1);';
		}
		if (count($ahide) > 0) {
			if ($attribs != '') { $attribs .= ' '; }
			$attribs .= 'elxHideParams(this, \''.implode(';',$ahide).'\', 1);';
		}
		if ($attribs != '') {
			$attribs = ' onchange="'.$attribs.'"';
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$dir.'"'.$attribs, 'value', 'text', $value);
	}


	/*****************************************/
	/* MAKE A SELECT LIST WITH IMAGE PREVIEW */
	/*****************************************/
	private function form_previewlist($name, $value, $node) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}

		$position = 'bottom';
		if (isset($attrs['position']) && (trim($attrs['position']) != '') && in_array($attrs['position'], array('top', 'left', 'right', 'bottom'))) {
			$position = $attrs['position'];
			if ($dir == 'rtl') {
				if ($position == 'left') {
					$position = 'right';
				} else if ($position == 'right') {
					$position = 'left';
				}
			}
		}
		$width = isset($attrs['width']) ? (int)$attrs['width'] : 120;
		if ($width < 5) { $width = 120; }
		$height = isset($attrs['height']) ? (int)$attrs['height'] : 80;
		if ($height < 5) { $height = 80; }
		$initial_image = 'templates/system/images/nopicture.png';

		$images = array();
		$children = $node->children();
		if ($children) {
			$index = 0;
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$image = isset($attr2['image']) ? trim((string)$attr2['image']) : '';
				$image = ltrim($image, '/');
				if (($image != '') && file_exists(ELXIS_PATH.'/'.$image)) {
					$images[$index] = $image;
					if ($val == $value) { $initial_image = $image; }
				}

				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) {
					$text = $eLang->silentGet($text);
				}
				$disabled = false;
				if (isset($attr2['disabled']) && (((string)$attr2['disabled'] == 'disabled') || ((int)$attr2['disabled'] == 1))) {
					$disabled = true;
				}
				$options[] = $elxis->obj('HTML')->makeOption($val, $text, $disabled);
				$index++;
			}
		}

		$image_id = 'params'.$name.'_image';
		$imghtml = '<img src="'.$elxis->secureBase().'/'.$initial_image.'" id="'.$image_id.'" alt="preview" style="vertical-align:bottom; width: '.$width.'px; height: '.$height.'px;" />';

		$attribs = '';
		if (count($images) > 0) {
        	$func_name = 'elxChangeImage_'.$name;
			$js = 'function '.$func_name.'(obj) {'."\n";
			$js .= "\t".'var selIndex = parseInt(obj.selectedIndex);'."\n";
			$js .= "\t".'var newsrc = \'templates/system/images/nopicture.png\';'."\n";
			$js .= "\t".'switch(selIndex) {'."\n";
			foreach ($images as $idx => $image) {
				$js .= "\t\t".'case '.$idx.': newsrc = \''.$image.'\'; break;'."\n";
			}
			$js .= "\t\t".'default: break;'."\n";
			$js .= "\t".'}'."\n";
			$js .= "\t".'document.getElementById(\''.$image_id.'\').src = \''.$elxis->secureBase().'/\'+newsrc;'."\n";
			$js .= "}\n";
		
			eFactory::getDocument()->addScript($js);
			$attribs = ' onchange="'.$func_name.'(this);"';
		}

		$selectbox = $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$dir.'"'.$attribs, 'value', 'text', $value);
		if ($position == 'top') {
			return $imghtml."<br />\n".$selectbox;
		} else if ($position == 'left') {
			return $imghtml." \n".$selectbox;
		} else if ($position == 'right') {
			return $selectbox." \n".$imghtml;
		} else {
			return $selectbox."<br />\n".$imghtml;
		}
	}


	/***************************/
	/* MAKE AN UPLOAD FILE BOX */
	/***************************/
	private function form_file($name, $value, $node) {
		$this->uploadFields[] = $name;
		$filehtml = '';
		if ((trim($value) != '') && is_file(ELXIS_PATH.'/'.$value)) {
			$filesize = round((filesize(ELXIS_PATH.'/'.$value) / 1024), 2);
			$link = eFactory::getElxis()->secureBase().'/'.$value;

			$parts = preg_split('#\/#', $value, -1, PREG_SPLIT_NO_EMPTY);
			$i = count($parts) -1;
			$filename = '<a href="'.$link.'" target="_blank" title="'.eFactory::getLang()->get('VIEW').'">'.$parts[$i].'</a>';
			$extension = strtolower(substr(strrchr($parts[$i], '.'), 1));
			unset($parts, $i);

			if (in_array($extension, array('png', 'jpg', 'jpeg', 'gif'))) {
				$info = getimagesize(ELXIS_PATH.'/'.$value);
				$filename .= ' ('.$info[0].'x'.$info[1].', '.$filesize.' KB)';
				$filehtml .= '<img src="'.$link.'" alt="preview" class="elx_thumb" style="float:left; width:50px; height:50px; margin:4px;" /> ';
				$filehtml .= '<span style="font-size:11px;">'.$filename."</span><br />\n";
			} else {
				$filename .= ' ('.$filesize.' KB)';
				$filehtml .= '<span style="font-size:11px;" dir="ltr">'.$filename."</span><br />\n";
			}
		} else {
			$filehtml .= '<span style="font-size:11px;">'.eFactory::getLang()->get('NO_FILE_UPLOADED')."</span><br />\n";
		}

		if (defined('ELXIS_ADMIN') && (eFactory::getElxis()->getConfig('SECURITY_LEVEL') == 0)) {
			$filehtml .= '<input type="file" name="'.$name.'" value="" class="filebox" dir="ltr" />'."\n";
		} else {
			$filehtml .= eFactory::getLang()->get('NOTALLOWACTION');
		}
		$filehtml .= '<input type="hidden" name="params['.$name.']" value="'.$value.'" dir="ltr" />'."\n";
		return $filehtml;
	}


	/***************************/
	/* MAKE FOLDER SELECT LIST */
	/***************************/
	private function form_folderlist($name, $value, $node) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$options = array();
		$options[] = $elxis->obj('HTML')->makeOption('', '- '.$eLang->get('SELECT').' -');
		if (isset($attrs['directory'])) {
			$dir = str_replace(DIRECTORY_SEPARATOR, '/', (string)$attrs['directory']);
			$dir = $this->msReplacer($dir);
			$dir = preg_replace('/^(\/)/', '', $dir);
			$dir = preg_replace('/(\/)$/', '', $dir);
			if ($dir != '') {
				$path = ELXIS_PATH.'/'.$dir.'/';
				if (file_exists($path) && is_dir($path)) {
					$folders = eFactory::getFiles()->listFolders($dir);
					if ($folders && (count($folders) > 0)) {
						foreach ($folders as $folder) {
							$options[] = $elxis->obj('HTML')->makeOption($folder, $folder);
						}
					}
				}
			}
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"', 'value', 'text', $value);
	}


	/***************************/
	/* MAKE IMAGES SELECT LIST */
	/***************************/
	private function form_imagelist($name, $value, $node) {
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();

		$preview = false;
		if (isset($attrs['position'])) {
			$preview = true;
			$position = 'bottom';
			if ((trim($attrs['position']) != '') && in_array($attrs['position'], array('top', 'left', 'right', 'bottom'))) {
				$position = $attrs['position'];
				if ($this->dir == 'rtl') {
					if ($position == 'left') {
						$position = 'right';
					} else if ($position == 'right') {
						$position = 'left';
					}
				}
			}
			$width = isset($attrs['width']) ? (int)$attrs['width'] : 120;
			if ($width < 5) { $width = 120; }
			$height = isset($attrs['height']) ? (int)$attrs['height'] : 80;
			if ($height < 5) { $height = 80; }
		}

		$initial_image = 'templates/system/images/nopicture.png';
		$images = array();

		$options = array();
		$options[] = $elxis->obj('HTML')->makeOption('', '- '.eFactory::getLang()->get('NONE').' -');
		if (isset($attrs['directory'])) {
			$dir = str_replace(DIRECTORY_SEPARATOR, '/', (string)$attrs['directory']);
			$dir = $this->msReplacer($dir);
			$dir = preg_replace('/^(\/)/', '', $dir);
			$dir = preg_replace('/(\/)$/', '', $dir);
			if ($dir != '') {
				$path = ELXIS_PATH.'/'.$dir.'/';
				if (file_exists($path) && is_dir($path)) {
					$files = eFactory::getFiles()->listFiles($dir, '(\.png)$|(\.gif)$|(\.jpg)$|(\.jpeg)$|(\.bmp)$|(\.ico)$');
					if ($files && (count($files) > 0)) {
						$index = 1;
						foreach ($files as $file) {
							$images[$index] = $dir.'/'.$file;
							if ($file == $value) { $initial_image = $dir.'/'.$file; }
							$options[] = $elxis->obj('HTML')->makeOption($file, $file);
							$index++;
						}
					}
				}
			}
		}

		if (!$preview) {
			return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"', 'value', 'text', $value);
		}

		$image_id = 'params'.$name.'_image';
		$imghtml = '<img src="'.$elxis->secureBase().'/'.$initial_image.'" id="'.$image_id.'" alt="preview" style="vertical-align:bottom; width: '.$width.'px; height: '.$height.'px;" />';

		$attribs = '';
		if (count($images) > 0) {
        	$func_name = 'elxChangeImage_'.$name;
			$js = 'function '.$func_name.'(obj) {'."\n";
			$js .= "\t".'var selIndex = parseInt(obj.selectedIndex);'."\n";
			$js .= "\t".'var newsrc = \'templates/system/images/nopicture.png\';'."\n";
			$js .= "\t".'switch(selIndex) {'."\n";
			foreach ($images as $idx => $image) {
				$js .= "\t\t".'case '.$idx.': newsrc = \''.$image.'\'; break;'."\n";
			}
			$js .= "\t\t".'default: break;'."\n";
			$js .= "\t".'}'."\n";
			$js .= "\t".'document.getElementById(\''.$image_id.'\').src = \''.$elxis->secureBase().'/\'+newsrc;'."\n";
			$js .= "}\n";

			eFactory::getDocument()->addScript($js);
			$attribs = ' onchange="'.$func_name.'(this);"';
		}

		$selectbox = $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"'.$attribs, 'value', 'text', $value);
		if ($position == 'top') {
			return $imghtml."<br />\n".$selectbox;
		} else if ($position == 'left') {
			return $imghtml." \n".$selectbox;
		} else if ($position == 'right') {
			return $selectbox." \n".$imghtml;
		} else {
			return $selectbox."<br />\n".$imghtml;
		}
	}


	/*************************/
	/* MAKE RADIO BOX FIELDS */
	/*************************/
	private function form_radio($name, $value, $node) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$options = array();
		$children = $node->children();

		$ashow = array();
		$ahide = array();
		$attribs = '';
		if ($children) {
			$index = 0;
			foreach ($children as $child) {
				$attr2 = $child->attributes();
				$val = isset($attr2['value']) ? (string)$attr2['value'] : '';
				$show = isset($attr2['show']) ? (string)$attr2['show'] : '';
				$show = trim($show);
				$hide = isset($attr2['hide']) ? (string)$attr2['hide'] : '';
				$hide = trim($hide);
				if ($show != '') {
					$ashow[] = $index.':'.$show;
					if ($val == $value) {
						$grids = explode(',',$show);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 1; }
						}
					}
				}

				if ($hide != '') {
					$ahide[] = $index.':'.$hide;
					if ($val == $value) {
						$grids = explode(',',$hide);
						foreach ($grids as $grid) {
							$grid = (int)$grid;
							if ($grid > 999) { $this->groupsVisibility[$grid] = 0; }
						}
					}
				}

				$text = (string)$child[0];
				if (($text != '') && !is_numeric($text)) {
					$text = $eLang->silentGet($text);
				}
				$options[] = $elxis->obj('HTML')->makeOption($val, $text);
				$index++;
			}
		}

		if (count($ashow) > 0) {
			$attribs .= 'elxShowParams(this, \''.implode(';',$ashow).'\', 2);';
		}
		if (count($ahide) > 0) {
			if ($attribs != '') { $attribs .= ' '; }
			$attribs .= 'elxHideParams(this, \''.implode(';',$ahide).'\', 2);';
		}
		if ($attribs != '') {
			$attribs = 'onclick="'.$attribs.'"';
		}

		return $elxis->obj('HTML')->radioList($options, 'params['.$name.']',$attribs, 'value', 'text', $value);
	}


	/***********************/
	/* MAKE TEXTAREA FIELD */
	/***********************/
	private function form_textarea($name, $value, $node) {
		$attrs = $node->attributes();
		$rows = isset($attrs['rows']) ? (int)$attrs['rows'] : 6;
		$cols = isset($attrs['cols']) ? (int)$attrs['cols'] : 40;
 		$value = eUTF::str_replace('<br />', "\n", $value);
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}

 		return '<textarea name="params['.$name.']" id="params'.$name.'" cols="'.$cols.'" rows="'.$rows.'" class="text_area" dir="'.$dir.'">'.htmlspecialchars($value).'</textarea>';
	}


	/*****************************/
	/* MAKE CATEGORY SELECT LIST */
	/*****************************/
	private function form_category($name, $value, $node) {
		$db = eFactory::getDB();
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$attrs = $node->attributes();
		$dir = 'ltr';
		$onlyroot = 0;
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}
		if (isset($attrs['onlyroot'])) { $onlyroot = (int)$attrs['onlyroot']; }

        $query= "SELECT ".$db->quoteId('catid').", ".$db->quoteId('parent_id').", ".$db->quoteId('title')." FROM ".$db->quoteId('#__categories')
		."\n WHERE ".$db->quoteId('published')."=1 ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$sth = $db->prepare($query);
		$sth->execute();
		$rows = $sth->fetchAll(PDO::FETCH_ASSOC);
		unset($sth);

		$elids = array();
		$categories = array();
		if ($rows) {
			foreach ($rows as $k => $row) {
				if ($row['parent_id'] == 0) {
					$catid = $row['catid'];
					$elids[] = $catid;
					$categories[$catid] = array(
						'catid' => $catid,
						'title' => $row['title'],
						'children' => array()
					);
					unset($rows[$k]);
				}
			}
			if ($rows && ($onlyroot == 0)) {
				foreach ($rows as $k => $row) {
					$p = $row['parent_id'];
					if (isset($categories[$p])) {
						$c = $row['catid'];
						$elids[] = $c;
						$categories[$p]['children'][$c] = $row['title'];
					}
				}
			}
		}
		unset($rows);

		if ($this->translate && $elids) {
			$query = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
			."\n WHERE ".$db->quoteId('category')." = ".$db->quote('com_content')." AND ".$db->quoteId('element')." = ".$db->quote('category_title')
			."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(', ', $elids).")";
			$sth = $db->prepare($query);
			$sth->execute(array(':lng' => $this->uri_lang));
			$trans = $sth->fetchPairs();
			if ($trans) {
				foreach ($categories as $c => $cat) {
					if (isset($trans[$c])) {
						$categories[$c]['title'] = $trans[$c];
					}
					if ($categories[$c]['children']) {
						foreach ($categories[$c]['children'] as $sc => $stitle) {
							if (isset($trans[$sc])) {
								$categories[$c]['children'][$sc] = $trans[$sc];
							}
						}
					}
				}
			}
			unset($trans);
		}
		unset($elids);

		$options = array();
		$options[] = $elxis->obj('HTML')->makeOption(0, '- '.$eLang->get('SELECT').' -');
		if ($categories) {
			foreach ($categories as $category) {
				$options[] = $elxis->obj('HTML')->makeOption($category['catid'], $category['title']);
				if ($category['children']) {
					foreach ($category['children'] as $sc => $stitle) {
						$options[] = $elxis->obj('HTML')->makeOption($sc, '--- '.$stitle);
					}
				}
			}
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$dir.'"', 'value', 'text', $value);
	}


	/****************************/
	/* MAKE COUNTRY SELECT LIST */
	/****************************/
	private function form_country($name, $value, $node) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$options = array();
		$lng = $eLang->getinfo('LANGUAGE');
		if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php')) {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.'.$lng.'.php');
		} else {
			include(ELXIS_PATH.'/includes/libraries/elxis/form/countries.en.php');
		}
		if (isset($countries)) {
			foreach ($countries as $key => $cname) {
				$options[] = $elxis->obj('HTML')->makeOption($key, $cname);
			}
		}
		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$this->dir.'"', 'value', 'text', $value);
	}


	/**************************************/
	/* MAKE AN INTEGERS RANGE SELECT LIST */
	/**************************************/
	private function form_range($name, $value, $node) {
		$value = (int)$value;
		$elxis = eFactory::getElxis();
		$attrs = $node->attributes();
		$first = isset($attrs['first']) ? (int)$attrs['first'] : 1;
		$last = isset($attrs['last']) ? (int)$attrs['last'] : 1;
		$step = isset($attrs['step']) ? (int)$attrs['step'] : 1;
		if ($step < 1) { $step = 1; }
		if ($first == $last) { $last++; }
		if ($first < $last) {
			$values = range($first, $last, $step);
		} else {
			$values = range($last, $first, $step);
			$values = array_reverse($values);
		}
		$options = array();
		foreach ($values as $num) {
			$options[] = $elxis->obj('HTML')->makeOption($num, $num);
		}
		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"', 'value', 'text', $value);
	}


	/****************************/
	/* MAKE A MONTH SELECT LIST */
	/****************************/
	private function form_month($name, $value, $node) {
		$value = (int)$value;
		if ($value < 1) { $value = (int)date('m'); }
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$attrs = $node->attributes();
		$short = (isset($attrs['short']) && (intval($attrs['short']) == 1)) ? true : false;
		$options = array();
		for ($i=1; $i<13; $i++) {
			$monthname = $eDate->monthName($i, $short);
			$options[] = $elxis->obj('HTML')->makeOption($i, $monthname);
		}
		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$this->dir.'"', 'value', 'text', $value);
	}


	/*********************************/
	/* MAKE A USER GROUP SELECT LIST */
	/*********************************/
	private function form_usergroup($name, $value, $node) {
		$value = (int)$value;
		if ($value < 0) { $value = 0; }
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();
		$sql = "SELECT * FROM ".$db->quoteId('#__groups')." ORDER BY ".$db->quoteId('level')." DESC";
		$stmt = $db->prepare($sql);
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
				$options[] = $elxis->obj('HTML')->makeOption($row['gid'], $lev.' - '.$groupname);
			}
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$this->dir.'"', 'value', 'text', $value);
	}


	/*********************************/
	/* MAKE A USER NAME SELECT LIST */
	/*********************************/
	private function form_username($name, $value, $node) {
		$value = (int)$value;
		if ($value < 0) { $value = 0; }
		$attrs = $node->attributes();
		$realname = isset($attrs['realname']) ? (int)$attrs['realname'] : 0;
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('uid').", ".$db->quoteId('firstname').", ".$db->quoteId('lastname').", ".$db->quoteId('uname')." FROM ".$db->quoteId('#__users')
		."\n WHERE ".$db->quoteId('block')."=0 AND ".$db->quoteId('expiredate')." > '".eFactory::getDate()->getDate()."'";
		if ($realname == 1) {
			$sql .= "\n ORDER BY ".$db->quoteId('firstname')." ASC";
		} else {
			$sql .= "\n ORDER BY ".$db->quoteId('uname')." ASC";
		}
		$stmt = $db->prepareLimit($sql, 0, 200);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$options = array();
		$options[] = $elxis->obj('HTML')->makeOption(0, '- '.$eLang->get('SELECT').' -');
		if ($rows) {
			foreach ($rows as $row) {
				$txt = ($realname == 1) ? $row['firstname'].' '.$row['lastname'] : $row['uname'];
				$options[] = $elxis->obj('HTML')->makeOption($row['uid'], $txt);
			}
		}

		$dir = ($realname == 1) ? $this->dir : 'ltr';
		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="'.$dir.'"', 'value', 'text', $value);
	}


	/*********************/
	/* MAKE HIDDEN FIELD */
	/*********************/
	private function form_hidden($name, $value, $node) {
		$attrs = $node->attributes();
		$final = $value;
		if (isset($attrs['autovalue'])) {
			switch($attrs['autovalue']) {
				case '{UID}': $final = eFactory::getElxis()->user()->uid; break;
				case '{GID}': $final = eFactory::getElxis()->user()->gid; break;
				case '{DATETIME}': $final = eFactory::getDate()->getDate(); break;
				case '{TIMESTAMP}': $final = eFactory::getDate()->getTS(); break;
				case '{LANGUAGE}': $final = $this->lang; break;
				default: $final = $value; break;
			}
		}
		
		$dir = 'ltr';
		if ($this->dir == 'rtl') {
			if (isset($attrs['dir']) && (strtolower((string)$attrs['dir']) == 'rtl')) { $dir = 'rtl'; }
		}
		return '<input type="hidden" name="params['.$name.']" value="'.$final.'" dir="'.$dir.'" />';
	}


	/****************/
	/* MAKE COMMENT */
	/****************/
	private function form_comment($name, $value, $node) {
		$text = '';
		$attrs = $node->attributes();
		$val = isset($attrs['default']) ? (string)$attrs['default'] : '';
		if ($val != '') {
			$text = eFactory::getLang()->silentGet($val);
		} else {
			$text = (string)$node[0];
			if ($text != '') {
				$text = eFactory::getLang()->silentGet($text);
			}
		}
		return $text;
	}


	/*********************************/
	/* MAKE COLOUR SELECT TEXT FIELD */
	/*********************************/
	private function form_color($name, $value, $node) {
		$url = eFactory::getElxis()->secureBase().'/includes/js/jscolor/jscolor.js';
		eFactory::getDocument()->addLibrary('jscolor', $url, '1.3.1');
		return '<input type="text" name="params['.$name.']" value="'.$value.'" class="elxcolorpicker" size="10" maxlength="6" dir="ltr" />';
	}


	/****************************************/
	/* MAKE A TEMPLATE POSITION SELECT LIST */
	/****************************************/
	private function form_position($name, $value, $node) {
		$value = trim($value);
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$db = eFactory::getDB();
		$sql = "SELECT ".$db->quoteId('position')." FROM ".$db->quoteId('#__template_positions')." ORDER BY ".$db->quoteId('id')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$rows = $stmt->fetchCol();

		$options = array();
		$attrs = $node->attributes();
		if (isset($attrs['global']) && (intval($attrs['global']) == 1)) {
			$options[] = $elxis->obj('HTML')->makeOption('_global_', $eLang->silentGet('GLOBAL_SETTING'));
		}
		if (isset($attrs['none']) && (intval($attrs['none']) == 1)) {
			$options[] = $elxis->obj('HTML')->makeOption('', $eLang->silentGet('NONE'));
		}
		if ($rows) {
			foreach ($rows as $position) {
				$options[] = $elxis->obj('HTML')->makeOption($position, $position);
			}
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"', 'value', 'text', $value);
	}


	/************************************/
	/* MAKE MENU COLLECTION SELECT LIST */
	/************************************/
	private function form_collection($name, $value, $node) {
		$db = eFactory::getDB();
		$elxis = eFactory::getElxis();

		$section = 'frontend';
		$modname = 'mod_menu';
		$all_collections = array();

		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__modules')
		."\n WHERE ".$db->quoteId('module')." = :xmodname AND ".$db->quoteId('section')." = :xsection";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xmodname', $modname, PDO::PARAM_STR);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$modparams = $stmt->fetchCol();
		if ($modparams) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			foreach ($modparams as $modparam) {
				$params = new elxisParameters($modparam, '', 'module');
				$collection = trim($params->get('collection', ''));
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
				unset($params);
			}
		}

		$sql = "SELECT ".$db->quoteId('collection')." FROM ".$db->quoteId('#__menu')
		."\n WHERE ".$db->quoteId('section')." = :xsection GROUP BY ".$db->quoteId('collection');
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
		$stmt->execute();
		$collections = $stmt->fetchCol();
		if ($collections) {
			foreach ($collections as $collection) {
				if (($collection != '') && !in_array($collection, $all_collections)) {
					$all_collections[] = $collection;
				}
			}
		}

		$options = array();
		if ($all_collections) {
			asort($all_collections);
			foreach ($all_collections as $col) {
				$options[] = $elxis->obj('HTML')->makeOption($col, $col);
			}
		}

		return $elxis->obj('HTML')->selectList($options, 'params['.$name.']', 'class="selectbox" dir="ltr"', 'value', 'text', $value);
	}

}

?>