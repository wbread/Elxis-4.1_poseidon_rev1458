<?php 
/**
* @version		$Id: pathway.class.php 1186 2012-06-17 19:38:05Z datahell $
* @package		Elxis
* @subpackage	Pathway
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisPathway {

	private $idx = 0;
	private $separator = ' &#187; ';
	private $you_are_here = false;
	private $nodes = array();
	private $homeImage = '';
	private $homeImageHover = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct($you_are_here=false) {
		$eLang = eFactory::getLang();
		$this->you_are_here = $you_are_here;
		if ($eLang->getinfo('DIR') == 'rtl') { $this->separator = '<span dir="rtl">&#160;</span>'; }
		$this->newNode($eLang->get('HOME'), '/', false, true);
	}


	/******************/
	/* ADD A NEW NODE */
	/******************/
	public function addNode($title, $link='', $ssl=false) {
		return $this->newNode($title, $link, $ssl, false);
	}


	/**********************/
	/* SET HOME PAGE NODE */
	/**********************/
	public function setHome($title, $link='/', $ssl=false) {
		if ($link == '') { $link = '/'; }
		if ($title == '') { $title = eFactory::getLang()->get('HOME'); }
		$this->newNode($title, $link, $ssl, true);
	}


	/***************************/
	/* SET HOME APEAR AS IMAGE */
	/***************************/
	public function setHomeImage($image='', $image_hover='') {
		$this->homeImage = trim($image);
		$this->homeImageHover = trim($image_hover);
	}


	/***********************/
	/* SET NODES SEPARATOR */
	/***********************/
	public function setSeparator($separator) {
		$this->separator = $separator;
	}


	/************************************/
	/* DELETE ALL NODES (OPTIONAL HOME) */
	/************************************/
	public function deleteAllNodes($including_home=false) {
		if ($including_home === true) {
			$this->nodes = array();
		} else {
			if (isset($this->nodes[0])) {
				$home_node = $this->nodes[0];
				$this->nodes = array();
				$this->nodes[0] = $home_node;
			} else {
				$this->nodes = array();
			}
		}
		$this->idx = 0;
	}


	/**************************/
	/* DELETE LAST ADDED NODE */
	/**************************/
	public function deleteLastNode() {
		$idx = $this->idx;
		if (($idx > 0) && isset($this->nodes[$idx])) {
			unset($this->nodes[$idx]);
			$this->idx--;
		}
	}


	/*****************/
	/* GET ALL NODES */
	/*****************/
	public function getNodes() {
		return $this->nodes;
	}


	/***********************************/
	/* GENERATE AND GET ALL NODES HTML */
	/***********************************/
	public function getHTMLNodes($auto_make_if_empty=true) {
		$c = count($this->nodes);
		$automake = false;
		if ($auto_make_if_empty) {
			if ($c == 0) {
				$automake = true;
			} else if ($c == 1) {
				$elxuri = eFactory::getURI()->getElxisUri();
				if (($elxuri == '') || ($elxuri == 'content:/') || ($elxuri == 'frontpage:/') || ($elxuri == 'cpanel:/') || ($elxuri == eFactory::getElxis()->getConfig('DEFAULT_ROUTE'))) {
					$automake = false;
				} else {
					$automake = true;
				}
			}
		}

		if ($automake) { $this->autoMakeNodes(); } 
		if (count($this->nodes) == 0) { return ''; }
		unset($c, $automake);

		$elxis = eFactory::getElxis();

		$html = '<div class="elx_pathway">'."\n";
		if ($this->you_are_here === true) {
			$html .= '<span class="elx_pathway_here">'.eFactory::getLang()->get('YOU_ARE_HERE')."</span>\n";
		}
		$x = 0;
		foreach ($this->nodes as $idx => $node) {
			$jstxt = '';
			if (($x == 0) && ($this->homeImage != '')) {
				if ($this->homeImageHover != '') {
					$jstxt = ' onmouseover="this.src=\''.$this->homeImageHover.'\';" onmouseout="this.src=\''.$this->homeImage.'\';"';
				}
				$txt = '<img src="'.$this->homeImage.'" alt="'.$node->title.'" border="0" align="top"'.$jstxt.' />';
			} else {
				$txt = $node->title;
			}
			if ($node->link == '') {
				$html .= "\t".'<span class="pathway_text">'.$txt.'</span>';
			} else {
				$elxuri = ($node->link == '/') ? '' : $node->link;
				if (defined('ELXIS_ADMIN')) {
					$link = $elxis->makeAURL($elxuri, 'index.php', $node->ssl);
				} else {
					$link = $elxis->makeURL($elxuri, 'index.php', $node->ssl);
				}
				$html .= "\t".'<a href="'.$link.'" title="'.$node->title.'" class="pathway">'.$txt.'</a>';
			}
			if ($idx < $this->idx) { $html .= $this->separator; }
			$html .= "\n";
			$x++;
		}
		$html .= "</div>\n";
		return $html;
	}


	/*********************************************/
	/* AUTO GENERATE NODES BASED ON URI SEGMENTS */
	/*********************************************/
	private function autoMakeNodes() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();
		if (!isset($this->nodes[0])) {
			$node = new stdClass;
			$node->title = eFactory::getLang()->get('HOME');
			$node->link = '/';
			$node->ssl = false;
			$this->nodes[0] = $node;
			$this->idx = 0;
		}

		$has_comp = false;
		$component = $eURI->getComponent();
		if (($component != '') && ($component != 'content') && ($component != 'cpanel') && ($component != 'frontpage')) {
			$has_comp = true;
			$node = new stdClass;
			$upt = strtoupper($component);
			$ttl = ($eLang->exist($upt) === true) ? $eLang->get($upt) : ucfirst($component);
			$node->title = ucfirst($component);
			$node->link = $component.':/';
			$node->ssl = false;
			$this->nodes[1] = $node;
			$this->idx = 1;
		}

		$segments = $eURI->getSegments();
		$c = count($segments);
		if ($c > 0) {
			$k = 0;
			$prev_link = '';
			foreach ($segments as $segment) {
				if (($k == 0) && $has_comp) {
					$link = $component.':'.$segment;
				} else {
					$link = $segment;
				}

				$pos = strpos($segment, '.');
				if ($pos === false) {
					$link .= '/';
					$title = $segment;
				} else {
					$title = substr($segment, 0, $pos);
				}

				$title = str_replace('-', ' ', $title);
				$upt = strtoupper($title);
				$ttl = ($eLang->exist($upt) === true) ? $eLang->get($upt) : ucfirst($title);
				if ($prev_link != '') { $link = $prev_link.$link; }
				$node = new stdClass;
				$node->title = $ttl;
				$node->link = $link;
				$node->ssl = false;
				$this->idx++;
				$this->nodes[ $this->idx ] = $node;
				$prev_link = $link;
				$k++;
			}
		}

		$this->nodes[ $this->idx ]->link = ''; //show the last node without link
	}


	/***********************************/
	/* ADD A NEW NODE IN ELXIS PATHWAY */
	/***********************************/
	private function newNode($title, $link='', $ssl=false, $home=false) {
		if (trim($title) == '') { return false; }
		$node = new stdClass;
		$node->title = $title;
		$node->link = $link;
		$node->ssl = (bool)$ssl;
		if ($home === true) {
			$this->nodes[0] = $node;
		} else {
			$this->idx++;
			$this->nodes[ $this->idx ] = $node;
		}
		return true;
	}

}

?>