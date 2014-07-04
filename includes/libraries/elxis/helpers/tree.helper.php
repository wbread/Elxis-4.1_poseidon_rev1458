<?php 
/**
* @version		$Id: tree.helper.php 830 2012-01-10 20:18:41Z datahell $
* @package		Elxis
* @subpackage	Helpers / Tree
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTreeHelper {

	private $subsymbol = 'L';
	private $itemid = 'id';
	private $parentid = 'parent_id';
	private $itemname = 'title';
	private $html = true;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->subsymbol = (eFactory::getLang()->getinfo('DIR') == 'rtl') ? '&#8747;' : 'L';
	}


	/**************/
	/* SET OPTION */
	/**************/
	public function setOption($option, $value) {
		switch ($option) {
			case 'itemid': if ($value != '') { $this->itemid = $value; } break;
			case 'parentid': if ($value != '') { $this->parentid = $value; } break;
			case 'itemname': if ($value != '') { $this->itemname = $value; } break;
			case 'html': $this->html = (bool)$value; break;
			default: break;
		}
	}


	/***************/
	/* SET OPTIONS */
	/***************/
	public function setOptions($options) {
		if (is_array($options) && (count($options) > 0)) {
			foreach ($options as $option => $value) {
				$this->setOption($option, $value);
			}
		}
	}


	/*********************************************/
	/* MAKE AND RETURN A RECURSIVE TREE OF ITEMS */
	/*********************************************/
	public function makeTree($rows, $maxlevel=10, $filterids=array()) {
		if (!$rows) { return array(); }
		$itemid = $this->itemid;
		$parentid = $this->parentid;

		$children = array();
		foreach ($rows as $row) {
			$pt = $row->$parentid;
			$nlist = (isset($children[$pt])) ? $children[$pt] : array();
			$nlist[] = $row;
			$children[$pt] = $nlist;
			unset($pt, $nlist);
		}

		$toplevel = max(0, $maxlevel - 1);
		$list = $this->treeRecurse(0, '', array(), $children, $toplevel);

		if (count($filterids) > 0) {
			$newlist = array();
			foreach($filterids as $fid) {
				foreach($list as $item) {
					if ($item->$itemid == $fid) {
						$newlist[] = $item;
					}
				}
			}
			$list = $newlist;
		}

		$this->resetOptions();
		return $list;
	}


	/**********************************/
	/* MAKE A RECURSIVE LIST OF ITEMS */
	/**********************************/
	private function treeRecurse($id, $indent, $list, &$children, $maxlevel=9999, $level=0) {
		if (isset($children[$id]) && ($level <= $maxlevel)) {
			$itemid = $this->itemid;
			$parentid = $this->parentid;
			$itemname = $this->itemname;

			foreach ($children[$id] as $v) {
				$id = $v->$itemid;
				if ($this->html === true) {
					$pre = '<sup>'.$this->subsymbol.'</sup>&#160;';//&nbsp; is XHTML invalid, so use &#160;
					$spacer = '.&#160;&#160;&#160;&#160;&#160;';
				} else {
					$pre = '- ';
					$spacer = '&#160;&#160;';
				}

				$txt = ($v->$parentid == 0) ? $v->$itemname : $pre.$v->$itemname;

				$pt = $v->$parentid;
				$list[$id] = $v;
				$list[$id]->treename = $indent.$txt;
				$list[$id]->children = isset($children[$id]) ? count($children[$id]) : 0;
				$list = $this->treeRecurse($id, $indent.$spacer, $list, $children, $maxlevel, $level+1);
			}
		}

		return $list;
	}


	/******************************************/
	/* RESET OPTIONS TO THEIR STANDARD VALUES */
	/******************************************/
	private function resetOptions() {
		$this->itemid = 'id';
		$this->parentid = 'parent_id';
		$this->itemname = 'title';
		$this->html = true;
	}

}

?>