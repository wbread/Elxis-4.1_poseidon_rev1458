<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Grid
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisGrid {

	private $element = 'elxisgrid';
	private $title = 'Elxis grid';
	private $url = '';
	private $dataType = 'xml';
	private $sortname = '';
	private $sortorder = 'asc';
	private $usepager = true;
	private $useRp = true;
	private $rp = 10;
	private $width = '100%';
	private $height = 'auto';
	private $resizable = false;
	private $showTableToggleBtn = false;
	private $showToggleBtn = false;
	private $singleSelect = false;
	private $dir = 'ltr';
	private $i18n = array();
	private $columns = array();
	private $buttons = array();
	private $searchitems = array();
	private $filteritems = array();
	private $hiddenitems = array();
	private $errors = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($element='', $title='') {
		$eLang = eFactory::getLang();
	
		if (trim($element) != '') { $this->element = $element; }
		if (trim($title) != '') { $this->title = $title; }
		$this->dir = $eLang->getinfo('DIR');
		$this->i18n['pagetext'] = $eLang->get('PAGE');
		$this->i18n['outof'] = $eLang->get('OF');
		$this->i18n['pagestat'] = sprintf($eLang->get('DISPLAY_FROM_TO_TOTAL'), '{from}', '{to}', '{total}');
		$this->i18n['findtext'] = $eLang->get('SEARCH');
		$this->i18n['procmsg'] = $eLang->get('PLEASE_WAIT');
		$this->i18n['nomsg'] = $eLang->get('NO_RESULTS');
		$this->i18n['errormsg'] = $eLang->get('CONNECT_ERROR');
		$this->i18n['submittext'] = $eLang->get('SUBMIT');
	}


	/*************************/
	/* SET A GRID'S PROPERTY */
	/*************************/
	public function setOption($option, $value) {
		switch (strtolower($option)) {
			case 'element': $this->element = $value; break;
			case 'title': if (trim($value) != '') { $this->title = $value; } break;
			case 'url': $this->url = $value; break;
			case 'datatype':
				$value = strtolower($value);
				$this->dataType = ($value == 'json') ? 'json' : 'xml';
			break;
			case 'sortname': $this->sortname = $value; break;
			case 'sortorder':
				$value = strtolower($value);
				$this->sortorder = ($value == 'desc') ? 'desc' : 'asc';
			break;
			case 'usepager':
			case 'userp':
			case 'resizable':
			case 'showtabletogglebtn':
			case 'showtogglebtn':
			case 'singleselect':
				$this->$option = (bool)$value;
			break;
			case 'rp':
				$value = (int)$value;
				$this->rp = ($value > 0) ? $value : 10;
			break;
			case 'width':
			case 'height':
				if (is_int($value)) {
					$this->$option = ($value > 0) ? $value : ($option == 'height') ? 'auto' : '100%';
				} else if ($value == 'auto') {
					$this->$option = 'auto';
				} else {
					if (preg_match('/(\%)$/', $value)) { $this->$option = $value; }
				}
			break;
			case 'dir': $this->dir = ($value == 'rtl') ? 'rtl' : 'ltr'; break;
			default: break;
		}
	}


	/****************************/
	/* ADD A COLUMN TO THE GRID */
	/****************************/
	public function addColumn($title, $name='', $width=140, $sortable=true, $align='auto', $filteroptions=array(), $initvalue='') {
		if (trim($name) == '') { $n = count($this->columns) + 1; $name = 'col'.$n; }
		$width = (int)$width;
		if ($width < 1) { $width = 140; }
		$sortable = (bool)$sortable;
		switch ($align) {
			case 'left': case 'right': case 'center'; break;
			case 'auto': default: $align = ($this->dir == 'rtl') ? 'right' : 'left'; break;
		}

		$this->columns[$name] = array(
			'title' => $title,
			'width' => $width,
			'sortable' => $sortable,
			'align' => $align
		);
		
		if (is_array($filteroptions) && (count($filteroptions) > 0)) {
			$this->addFilter($title, $name, $filteroptions, $initvalue);
		}
	}


	/****************************/
	/* ADD A BUTTON TO THE GRID */
	/****************************/
	public function addButton($title, $task='', $class='', $onpress='') {
		$this->buttons[] = array(
			'type' => 'button',
			'title' => $title,
			'task' => $task,
			'class' => $class,
			'onpress' => $onpress,
			'sepbool' => false
		);
	}


	/***************************/
	/* ADD A SEPARATOR BUTTTON */
	/***************************/
	public function addSeparator($bool=true) {
		$this->buttons[] = array(
			'type' => 'separator',
			'title' => '',
			'task' => '',
			'class' => '',
			'onpress' => '',
			'sepbool' => (bool)$bool
		);
	}


	/*************************/
	/* ADD A SEARCHABLE ITEM */
	/*************************/
	public function addSearch($title, $name, $isdefault=false) {
		if (trim($name) == '') {
			$this->errors[] = 'A Search item must have a name!';
			return false;
		}
		$this->searchitems[$name] = array(
			'title' => $title,
			'isdefault' => (bool)$isdefault
		);
	}


	/*********************/
	/* ADD A FILTER ITEM */
	/*********************/
	public function addFilter($title, $name, $options=array(), $value='') {
		$name = trim($name);
		if ($name == '') {
			$this->errors[] = 'A Filter item must have a name!';
			return false;
		}
		if (in_array(strtolower($name), array('page', 'rp', 'sortname', 'sortorder', 'query', 'qtype'))) {
			$this->errors[] = 'A Filter item can not be named '.$name.'!';
			return false;
		}
		if (!is_array($options) || (count($options) == 0)) { return false; }
		$this->filteritems[$name] = array(
			'title' => $title,
			'options' => $options,
			'value' => $value
		);
	}


	/***********************/
	/* ADD A HIDDEN COLUMN */
	/***********************/
	public function addHidden($name, $value) {
		$name = trim($name);
		if ($name == '') {
			$this->errors[] = 'A hidden item must have a name!';
			return false;
		}
		if (in_array(strtolower($name), array('page', 'rp', 'sortname', 'sortorder', 'query', 'qtype'))) {
			$this->errors[] = 'A hidden item can not be named '.$name.'!';
			return false;
		}

		$this->hiddenitems[$name] = $value;
	}


	/*******************/
	/* SHOW GRID ERROR */
	/*******************/
	private function showError($msg) {
		echo '<div class="elx_error"><strong>'.eFactory::getLang()->get('ERROR').'</strong> '.$msg.'</div>'."\n";
	}


	/*****************/
	/* GENERATE GRID */
	/*****************/
	public function render() {
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		if ($this->errors) {
			$errortxt = implode('<br />', $this->errors);
			$this->showError($errortxt);
			return;
		}

		if (!$this->columns) {
			$this->showError('The grid has no columns!');
			return;
		}

		$sfx = ($this->dir == 'rtl') ? '-rtl' : '';
		$eDoc->addJQuery();
		$eDoc->addScriptLink($elxis->secureBase().'/includes/js/jquery/flexigrid/flexigrid.js');
		$eDoc->addStyleLink($elxis->secureBase().'/includes/js/jquery/flexigrid/css/flexigrid'.$sfx.'.css');

		if ($this->sortname == '') {
			foreach ($this->columns as $name => $col) {
				$this->sortname = $name;
				break;
			}
		}

		echo '<table id="'.$this->element.'" style="display: none"></table>'."\n";
		echo '<script type="text/javascript">'."\n";
		echo '$(\'#'.$this->element.'\').flexigrid({'."\n";
		echo 'url: \''.$this->url.'\','."\n";
		echo 'dataType: \''.$this->dataType.'\','."\n";
		echo 'colModel: ['."\n";
		$n = count($this->columns);
		$i = 1;
		foreach ($this->columns as $name => $col) {
			$stxt = ($col['sortable'] === true) ? 'true' : 'false';
			echo "\t".'{ display:\''.addslashes($col['title']).'\', name:\''.$name.'\', width:'.$col['width'].', sortable:'.$stxt.', align:\''.$col['align'].'\'}';
			echo ($i < $n) ? ",\n" : "\n";
			$i++;
		}
		echo "],\n";

		$n = count($this->buttons);
		if ($n > 0) {
			$i = 1;
			echo 'buttons: ['."\n";
			foreach ($this->buttons as $btn) {
				if ($btn['type'] == 'separator') {
					$stxt = ($btn['sepbool'] === true) ? 'true' : 'false';
					echo "\t{separator: ".$stxt.'}';
				} else {
					echo "\t{name: '".$btn['title'].'\'';
					if ($btn['task'] != '') { echo ', task: \''.$btn['task'].'\''; }
					if ($btn['class'] != '') { echo ', bclass: \''.$btn['class'].'\''; }
					if ($btn['onpress'] != '') { echo ', onpress: '.$btn['onpress']; }
					echo '}';
				}
				echo ($i < $n) ? ",\n" : "\n";
				$i++;
			}
			echo "],\n";
		}

		$n = count($this->searchitems);
		if ($n > 0) {
			$i = 1;
			echo 'searchitems: ['."\n";
			foreach ($this->searchitems as $name => $item) {
				$stxt = ($item['isdefault'] === true) ? 'true' : 'false';
				echo "\t".'{display:\''.addslashes($item['title']).'\', name:\''.$name.'\', isdefault:'.$stxt.'}';
				echo ($i < $n) ? ",\n" : "\n";
				$i++;
			}
			echo "],\n";
		}

		$n = count($this->filteritems);
		if ($n > 0) {
			$i = 1;
			echo 'filteritems: ['."\n";
			foreach ($this->filteritems as $name => $item) {
				$optstr = '';
				$t = count($item['options']);
				$x = 1;
				foreach ($item['options'] as $k => $v) {
					$optstr .= '{oval:\''.$k.'\', otxt:\''.addslashes($v).'\'}';
					$optstr .= ($x < $t) ? ',' : '';
					$x++;
				}
				echo "\t".'{display:\''.addslashes($item['title']).'\', name:\''.$name.'\', opts:['.$optstr.'], value:\''.addslashes($item['value']).'\'}';
				echo ($i < $n) ? ",\n" : "\n";
				$i++;
			}
			echo "],\n";
		}

		$n = count($this->hiddenitems);
		if ($n > 0) {
			$i = 1;
			echo 'hiddenitems: ['."\n";
			foreach ($this->hiddenitems as $name => $value) {
				echo "\t".'{name:\''.$name.'\', value:\''.$value.'\'}';
				echo ($i < $n) ? ",\n" : "\n";
				$i++;
			}
			echo "],\n";
		}

		echo 'sortname: \''.$this->sortname."',\n";
		echo 'sortorder: \''.$this->sortorder."',\n";
		$stxt = ($this->usepager === true) ? 'true' : 'false';
		echo 'usepager: '.$stxt.",\n";
		$stxt = ($this->useRp === true) ? 'true' : 'false';
		echo 'useRp: '.$stxt.",\n";
		echo 'rp: '.$this->rp.",\n";
		$stxt = ($this->showTableToggleBtn === true) ? 'true' : 'false';
		echo 'showTableToggleBtn: '.$stxt.",\n";
		$stxt = ($this->showToggleBtn === true) ? 'true' : 'false';
		echo 'showToggleBtn: '.$stxt.",\n";
		$stxt = ($this->resizable === true) ? 'true' : 'false';
		echo 'resizable: '.$stxt.",\n";
		$stxt = ($this->singleSelect === true) ? 'true' : 'false';
		echo 'singleSelect: '.$stxt.",\n";
		if (is_int($this->width)) {
			echo 'width: '.$this->width.",\n";
		} else {
			echo 'width: \''.$this->width."',\n";
		}
		if (is_int($this->height)) {
			echo 'height: '.$this->height.",\n";
		} else {
			echo 'height: \''.$this->height."',\n";
		}

		echo 'title: \''.addslashes($this->title)."',\n";
		foreach ($this->i18n as $k => $v) {
			echo $k.': \''.addslashes($v)."',\n";
		}
		echo 'dir: \''.$this->dir."'\n";
		echo '});'."\n";
		echo "</script>\n";
	}

}

?>