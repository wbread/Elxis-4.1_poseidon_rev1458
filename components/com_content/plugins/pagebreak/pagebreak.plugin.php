<?php 
/**
* @version		$Id: pagebreak.plugin.php 1137 2012-05-17 19:58:22Z datahell $
* @package		Elxis
* @subpackage	Content Plugins / PageBreak
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class pagebreakPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{pagebreak}{/pagebreak}#s";
		if (!$published) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
		}

		$matches = array();
		preg_match($regex, $row->text, $matches);
		if (!$matches) { return true; }

		if (isset($_GET['print']) && ($_GET['print'] == 1)) {
			$row->text = preg_replace($regex, '', $row->text);
			return true;
		}

		$eURI = eFactory::getURI();
		$component = $eURI->getComponent();
		$segments = $eURI->getSegments();
		$last = count($segments) - 1;
		if (($component != 'content') || !preg_match('/(\.html)$/i', $segments[$last])) {
			$row->text = preg_replace($regex, '', $row->text);
			return true;
		}

		$text = preg_split($regex, $row->text);
		$total_pages = count($text);

		if ($total_pages < 2) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
		}

		$elxis = eFactory::getElxis();

		$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;
		if ($page < 1) { $page = 1; }
		if ($page > $total_pages) { $page = $total_pages; }
		$page_idx = $page - 1;

		if ($page > 1) { $row->image = ''; }

		$page_text = $text[$page_idx];
		$trimmed_text = trim($page_text);
		if (preg_match('#^\<\/p\>#', $trimmed_text)) {
			$page_text = '<p style="margin:0; padding:0;">'.$page_text;
		} elseif (preg_match('#^\<\/div\>#', $trimmed_text)) {
			$page_text = '<div style="margin:0; padding:0;">'.$page_text;
		}

		if (preg_match('#\<p\>$#', $trimmed_text)) {
			$page_text .= '</p>';
		} elseif (preg_match('#\<div\>$#', $trimmed_text)) {
			$page_text .= '</div>';
		}

		$linkbase = $elxis->makeURL('content:'.$row->link);
		$toc = $elxis->obj('navigation')->navLinks($linkbase, $page, $total_pages);

		$row->text = $page_text.$toc;
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{pagebreak}{/pagebreak}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('INSERT_CODE') , $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->insertCode(); break;
			case 2: $this->help(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		return array();
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/******************/
	/* GET A VIDEO ID */
	/******************/
	private function insertCode() {
		$eLang = eFactory::getLang();

		echo '<p><a href="javascript:void(null);" onclick="addPluginCode(\'{pagebreak}{/pagebreak}\')">'.$eLang->get('CLICK_INSERT_PAGEBREAK')."</a></p>\n";
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function help() {
?>
		<p>With the <strong>Page Break</strong> plugin you can split an article in multiple pages. Page Break will also display a table of contents (TOC) 
		in the bottom of the article for navigation through the pages.</p>
<?php 
	}

}

?>