<?php 
/**
* @version		$Id: editor.helper.php 1449 2013-06-11 18:26:32Z datahell $
* @package		Elxis
* @subpackage	Helpers / Editor
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisEditorHelper {

	private $editor_id = 'editor1';
	private $type = 'html';
	private $customConfig = '';
	private $contentsLang = 'en';
	private $contentsDir = 'ltr';
	private $options = array();


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
	}
	

	/************************/
	/* SET AN EDITOR OPTION */
	/************************/
	public function setOption($option, $value) {
		$this->options[$option] = $value;
	}


	/***************************************/
	/* SET EDITOR MULTIPLE OPTIONS AT ONCE */
	/***************************************/
	public function setOptions($options) {
		if (is_array($options) && (count($options) > 0)) {
			foreach ($options as $option => $value) {
				$this->setOption($option, $value);
			}
		}
	}


	/*****************************/
	/* PREPARE EDITOR ENVIROMENT */
	/*****************************/
	public function prepare($editor_id, $type='html', $clang='', $custom_options=array()) {
		$elxis = eFactory::getElxis();

		$this->editor_id = $editor_id;
		if ($type == '') { $type = 'html'; }
		if (!in_array($type, array('html', 'bbcode', 'text'))) { $type = 'html'; }
		$this->type = $type;

		$alevel = $elxis->acl()->getLevel();
		switch ($type) {
			case 'text': break;
			case 'bbcode':
				if ($alevel < 3) {
					$this->customConfig = 'config_bbcode_mini.js';
				} else if ($alevel < 70) {
					$this->customConfig = 'config_bbcode_normal.js';
				} else {
					$this->customConfig = 'config_bbcode_full.js';
				}
			break;
			case 'html': default:
				eFactory::getDocument()->setContentType('text/html'); //ckeditor doesn't work with application/xhtml+xml
				if ($alevel < 3) {
					$this->customConfig = 'config_mini.js';
				} else if ($alevel < 70) {
					$this->customConfig = 'config_normal.js';
				} else {
					$this->customConfig = 'config_full.js';
				}
			break;
		}

		$this->options = array();

		$clang = trim($clang);
		if (($clang == '') || !file_exists(ELXIS_PATH.'/language/'.$clang.'/'.$clang.'.php')) { $clang = $elxis->getConfig('LANG'); }
		$this->contentsLang = $clang;
		$this->contentsDir = 'ltr';
		$ilangs = eFactory::getLang()->getAllLangs(true);
		if (isset($ilangs[$clang])) { $this->contentsDir = $ilangs[$clang]['DIR']; }
		unset($ilangs);
		$this->options['contentsLangDirection'] = $this->contentsDir;

		if (file_exists(ELXIS_PATH.'/includes/js/ckeditor/lang/'.$this->contentsLang.'.js')) {
			$this->options['contentsLanguage'] = $this->contentsLang;
		}

		if (($this->type == 'html') && defined('ELXIS_ADMIN')) {
			if ($elxis->acl()->check('component', 'com_emedia', 'manage') > 0) {
				$this->options['filebrowserBrowseUrl'] = $elxis->makeAURL('emedia:editor/', 'inner.php');
        		$this->options['filebrowserImageWindowWidth'] = 960;
        		$this->options['filebrowserImageWindowHeight'] = 480;
        	}
		}
		if ($this->type == 'html') {
			$this->options['contentsCss'] = '[\''.$elxis->secureBase().'/templates/system/css/standard.css\', \''.$elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE').'/css/template.css\', \''.$elxis->secureBase().'/includes/js/ckeditor/contents.css\']';
		}
		if ($this->type != 'text') {
			//basePath todo
			//baseHref todo
			//timestamp
			$splang = $this->spellCheckLang($this->contentsLang);
			if ($splang != 'en_US') { $this->options['scayt_sLang'] = $splang; }

			$this->options['entities_greek'] = false;
			$this->options['entities_latin'] = false;
			$this->options['skin'] = 'office2003';
		}

		$this->setOptions($custom_options);

		if ($this->type == 'text') { return; }

		$link = $elxis->secureBase().'/includes/js/ckeditor/ckeditor.js';
		eFactory::getDocument()->addLibrary('ckeditor', $link, '3.6.2');
	}


	/****************************/
	/* GET EDITOR INSTANCE HTML */
	/****************************/
	public function editor($name, $value='', $attributes=array()) {
		if (!is_array($attributes)) { $attributes = array(); }
		$attributes['dir'] = $this->contentDir;
		if (!isset($attributes['class'])) { $attributes['class'] = 'textbox'; }
		if (!isset($attributes['cols'])) { $attributes['cols'] = 80; } else { $attributes['cols'] = (int)$attributes['cols']; }
		if (!isset($attributes['rows'])) { $attributes['rows'] = 8; } else { $attributes['rows'] = (int)$attributes['rows']; }

		$attr = '';
		foreach ($attributes as $key => $val) { $attr .= ' '.$key.' = "'.$val.'"'; }

		$out = '<textarea name="'.$name.'" id="'.$this->editor_id.'"'.$attr.'>'.htmlspecialchars($value)."</textarea>\n";
		$out .= $this->getJS();

		return $out;
	}


	/***************************************/
	/* MAKE AND RETURN REQUIRED JAVASCRIPT */
	/***************************************/
	public function getJS() {
		if ($this->type == 'text') { return ''; }
		$js = '<script type="text/javascript">'."\n";
		$js .= '/* <![CDATA[ */'."\n";
		$js .= 'CKEDITOR.replace(\''.$this->editor_id.'\', {';
		if ($this->customConfig != '') {
			$js .= 'customConfig: \''.$this->customConfig."',\n";
		}

		if (count($this->options) > 0) {
			foreach ($this->options as $option => $value) {
				if (($value == 'true') || ($value == 'false')) {
					$v = $value;
				} else if (is_numeric($value)) {
					$v = $value;
				} else if (is_bool($value)) {
					$v = ($value === true) ? 'true' : 'false';
				} else if (strpos($value, '[') === 0) {
					$v = $value;
				} else {
					$v = '\''.$value.'\'';
				}
				$js .= $option.': '.$v.",\n";
			}
		}

		$js .= '});'."\n";
		$js .= '/* ]]> */'."\n";
		$js .= "</script>\n";
		return $js;
	}


	/********************************/
	/* GET SPELL CHECKER'S LANGUAGE */
	/********************************/
	private function spellCheckLang($lng) {
		switch ($lng) {
			case 'en': $sclang = 'en_GB'; break;
			case 'us': $sclang = 'en_US'; break;
			case 'da': $sclang = 'da_DK'; break;
			case 'nl': $sclang = 'nl_NL'; break;
			case 'fi': $sclang = 'fi_FI'; break;
			case 'fr': $sclang = 'fr_FR'; break;
			case 'de': $sclang = 'de_DE'; break;
			case 'el': $sclang = 'el_GR'; break;
			case 'it': $sclang = 'it_IT'; break;
			case 'nb': $sclang = 'nb_NO'; break;
			case 'pt': $sclang = 'pt_PT'; break;
			case 'es': $sclang = 'es_ES'; break;
			case 'sv': $sclang = 'sv_SE'; break;
			default: $sclang = 'en_US'; break;
		}
		return $sclang;
	}


	/*********************/
	/* JSON ENCODE VALUE */
	/*********************/
	private function jsEncode($val) {
		if (is_null($val)) { return 'null'; }
		if (is_bool($val)) {
			return $val ? 'true' : 'false';
		}
		if (is_int($val)) { return $val; }
		if (is_float($val)) { return str_replace(',', '.', $val); }
		if (is_array($val) || is_object($val)) {
			if (is_array($val) && (array_keys($val) === range(0,count($val)-1))) {
				return '[' . implode(',', array_map(array($this, 'jsEncode'), $val)) . ']';
			}
			$temp = array();
			foreach ($val as $k => $v){
				$temp[] = $this->jsEncode("{$k}") . ':' . $this->jsEncode($v);
			}
			return '{' . implode(',', $temp) . '}';
		}
		if (strpos($val, '@@') === 0) { return substr($val, 2); }
		if (strtoupper(substr($val, 0, 9)) == 'CKEDITOR.') { return $val; }

		return '"'.str_replace(array("\\", "/", "\n", "\t", "\r", "\x08", "\x0c", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'), $val).'"';
	}


	private function defaultOptions() {
		$this->defaultOptions = array(
			'autoGrow_bottomSpace' => 0, //default: 0, Extra height in pixel to leave between the bottom boundary of content with document size when auto resizing.
			'autoGrow_maxHeight' => 0, //default: 0, The maximum height that the editor can reach using the AutoGrow feature.
			'autoGrow_minHeight' => 200, //default: 200, The minimum height that the editor can reach using the AutoGrow feature.
			'autoGrow_onStartup' => false, //default: false, Whether to have the auto grow happen on editor creation. 
			'autoParagraph' => true, //automatically create wrapping blocks around inline contents inside document body
			'autoUpdateElement' => true, //Whether the replaced <textarea> is to be updated automatically when posting the form containing the editor.
			'baseFloatZIndex' => 10000, //The base Z-index for floating dialog windows and popups.
			'baseHref' => '', //The base href URL used to resolve relative and absolute URLs in the editor content.
			'basicEntities' => true, //escape basic HTML entities in the document including: nbsp, gt, lt, amp, change on non-HTML data format like BBCode
			'blockedKeystrokes' => array(), //[], A list of keystrokes to be blocked if not defined in the CKEDITOR.config.keystrokes setting
			'bodyClass' => '', //default: empty, class attribute to be used on the body element of the editing area, class-specific CSS rules will be enabled
			'bodyId' => '', //default: empty, id attribute to be used on the body element of the editing area, class-specific id rules will be enabled
			'browserContextMenuOnCtrl' => true,//Whether to show the browser native context menu when the Ctrl or Meta (Mac) key is pressed on opening the context menu 
			//'colorButton_backStyle' => '',//Stores the style definition that applies the text background color. 
			'colorButton_colors' => '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF',
			'colorButton_enableMore' => true, //Whether to enable the More Colors button in the color selectors
			//'colorButton_foreStyle' => '', Stores the style definition that applies the text foreground color. 
			'contentsCss' => 'CKEDITOR.basePath+\'contents.css\'', //todo, The CSS file(s) to be used to apply style to editor contents.
					//config.contentsCss = '/css/mysitestyles.css';
					//config.contentsCss = ['/css/mysitestyles.css', '/css/anotherfile.css'];
			'contentsLangDirection' => 'ui', //values: ui, ltr, rtl
			'contentsLanguage' => '', //@default Same value as editor UI language.
			'corePlugins' => '', //default: empty, comma separated list of plugins that are not related to editor instances
			'coreStyles_bold' => '{ element: \'strong\', overrides: \'b\' }',
			'coreStyles_italic' => '{ element: \'em\', overrides: \'i\' }',
			'coreStyles_strike' => '{ element: \'strike\' }',
			'coreStyles_subscript' => '{ element: \'sub\' }',
			'coreStyles_superscript' => '{ element: \'sup\' }',
			'coreStyles_underline' => '{ element: \'u\' }',
			'customConfig' => '<CKEditor folder>/config.js', //The URL path for the custom configuration file to be loaded, TODO
			'defaultLanguage' => 'en',
			'docType' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">', //default einai ayto, den xreiazetai overwrite
			'emailProtection' => '', //TODO, EXAMPLE: config.emailProtection = 'mt(NAME,DOMAIN,SUBJECT,BODY)';
			'enterMode' => CKEDITOR.ENTER_P,//DEFAULT: CKEDITOR.ENTER_P, OPTIONS: CKEDITOR.ENTER_P, CKEDITOR.ENTER_BR, CKEDITOR.ENTER_DIV
			'entities' => true, //Whether to use HTML entities in the output. 
			'entities_greek' => true, //todo: false
			'entities_latin' => true,//todo: false
			'entities_processNumerical' => false,//values: true, false, 'force', convert all remaining characters not included in the ASCII character table to their relative decimal numeric representation of HTML entity
			'extraPlugins' => '', //additional plugins to be loaded, extraPlugins = 'myplugin,anotherplugin';
			//http://docs.cksource.com/CKEditor_3.x/Developers_Guide/File_Browser_%28Uploader%29
			'filebrowserBrowseUrl' => '',
			'filebrowserFlashBrowseUrl' => '',
			'filebrowserFlashUploadUrl' => '',
			'filebrowserImageBrowseLinkUrl' => '',
			'filebrowserImageBrowseUrl' => '',
			'filebrowserImageUploadUrl' => '',
			'filebrowserUploadUrl' => '',
			'filebrowserWindowFeatures' => 'location=no,menubar=no,toolbar=no,dependent=yes,minimizable=no,modal=yes,alwaysRaised=yes,resizable=yes,scrollbars=yes',
			'filebrowserWindowHeight' => '70%',
			'filebrowserWindowWidth' => '80%',
			'font_defaultLabel' => 'Arial',//todo
			'font_names' => 'Arial;Times New Roman;Verdana',//todo
			//'forceEnterMode' => false,
			'forceSimpleAmpersand' => false,
			'fullPage' => false,
			'height' => 200, //editor height, default: 200
			'htmlEncodeOutput' => false,
			'ignoreEmptyParagraph' => true,
			'language' => '', //@default: empty, UI language, empty autodetect, if not supported defaultLanguage will be used
			'pasteFromWordPromptCleanup' => 'undefined', //todo: convert to "true"
			'pasteFromWordRemoveFontStyles' => true,
			'pasteFromWordRemoveStyles' => true,
			'plugins' => 'about,a11yhelpbasicstyles,bidi,blockquote,button,clipboard,colorbutton,colordialog,contextmenu,dialogadvtabdiv,elementspath,enterkey,entities,
					filebrowser,find,flash,font,format,forms,horizontalrule,htmldataprocessor,iframe,image,indent,justify,keystrokes,link,list,liststyle,maximize,
					newpage,pagebreak,pastefromword,pastetext,popup,preview,print,removeformat,resize,save,scayt,smiley,showblocks,showborders,sourcearea,
					stylescombo,table,tabletools,specialchar,tab,templates,toolbar,undo,wysiwygarea,wsc',//dont have to set it, let all plugins initially loaded, use removePlugins
			'protectedSource' => array(), //@default <code>[]</code> (empty array),
			'readOnly' => false,
			'removePlugins' => '', //plugins that must not be loaded, removePlugins = 'elementspath,save,font'; TODO
			'resize_enabled' => true,
			'resize_maxHeight' => 3000,
			'resize_maxWidth' => 3000,
			'resize_minHeight' => 250,
			'resize_minWidth' => 750,
			'scayt_autoStartup' => false,
			'scayt_sLang' => 'en_US', //values are: en_US, en_GB, pt_BR, da_DK, nl_NL, en_CA, fi_FI, fr_FR, fr_CA, de_DE, el_GR, it_IT, nb_NO, pt_PT, es_ES, sv_SE.
			//'shiftEnterMode' => CKEDITOR.ENTER_BR,//DEFAULT: CKEDITOR.ENTER_BR, OPTIONS: CKEDITOR.ENTER_P, CKEDITOR.ENTER_BR, CKEDITOR.ENTER_DIV
			'skin' => 'default',//default: 'default', options: default, v2, kama, office2003
			'startupMode' => 'wysiwyg', //wysiwyg, source
			'stylesSet' => 'default', //TODO: stylesSet = 'mystyles:http://www.example.com/editorstyles/styles.js';
			'theme' => 'default', //default: 'default'
			'toolbar' => 'Basic', //default: Full, options: Basic, Full, todo
			'toolbar_Basic' => array(), //TODO
/*
config.toolbar_Basic =
[
    [ 'Source', '-', 'Bold', 'Italic' ]
];
*/
			'toolbar_Full' => array(), //TODO
/*
config.toolbar_Full =
[
    { name: 'document',    items : [ 'Source','-','Save','NewPage','DocProps','Preview','Print','-','Templates' ] },
    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
    { name: 'forms',       items : [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },
    '/',
    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
    { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
    { name: 'insert',      items : [ 'Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
    '/',
    { name: 'styles',      items : [ 'Styles','Format','Font','FontSize' ] },
    { name: 'colors',      items : [ 'TextColor','BGColor' ] },
    { name: 'tools',       items : [ 'Maximize', 'ShowBlocks','-','About' ] }
];
*/
			'uiColor' => '', //The base user interface color to be used by the editor. Not all skins are compatible with this setting. 
			'useComputedState' => true, //default : true
			'width' => '' //editor width, % or absolute pixels, default: empty string
		);
	}


}

?>