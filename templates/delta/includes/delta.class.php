<?php 
/**
* @version		$Id: delta.class.php 1353 2012-11-10 08:41:44Z datahell $
* @package		Elxis CMS
* @subpackage	Templates / Delta
* @author		Ioannis Sannos ( http://www.isopensource.com )
* @copyright	Copyright (c) 2008-2012 Is Open Source (http://www.isopensource.com). All rights reserved.
* @license		Creative Commons 3.0 Attribution-ShareAlike Unported ( http://creativecommons.org/licenses/by-sa/3.0/ )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
************************************************************************************************/


defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class templateDelta {

	private $media = 'pc';
	private $tplparams = array();
	private $baseurl = '/templates/delta';
	private $apc = false;
	private $sidecolumn = true;
	private $pathway = true;
	private $footer = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($media='pc') { //TODO: mobile
		$elxis = eFactory::getElxis();

		if ($media == 'mobile') { $this->media = 'mobile'; }
        $this->baseurl = $elxis->secureBase().'/templates/delta';
		if ($elxis->getConfig('APC') == 1) { $this->apc = true; }
        $this->prepare();
    }


	/***************************/
	/* GET TEMPLATE PARAMETERS */
	/***************************/    
	private function prepare() {
		$process = true;
		if ($this->apc == true) {
			$data = elxisAPC::fetch('params', 'tpldelta');
			if ($data !== false) { $this->tplparams = $data; $process = false; }
		}

		if ($process) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$xmlpath = ELXIS_PATH.'/templates/delta/delta.xml';
			$tplparams = $this->getDBParams();
			$params = new elxisParameters($tplparams, $xmlpath, 'template');

        	$this->tplparams['logo'] = trim($params->get('logo', 'templates/delta/images/logo.png'));
       		$this->tplparams['sitename'] = (int)$params->get('sitename', 0);
        	if ($this->tplparams['sitename'] == 0) {
        		if (($this->tplparams['logo'] == '') || !file_exists(ELXIS_PATH.'/'.$this->tplparams['logo'])) { $this->tplparams['sitename'] = 1; }
       		}
			$this->tplparams['slogan'] = trim(strip_tags($params->getML('slogan', '')));
			$this->tplparams['head_colour'] = trim($params->get('head_colour', '54A3F4'));
			if (($this->tplparams['head_colour'] == '') || (strlen($this->tplparams['head_colour']) != 6)) { $this->tplparams['head_colour'] = '54A3F4'; }
			$this->tplparams['colwidth'] = (int)$params->get('colwidth', 200);
			if ($this->tplparams['colwidth'] < 140) { $this->tplparams['colwidth'] = 200; }
			$this->tplparams['hidecol_all'] = (int)$params->get('hidecol_all', 0);
			$this->tplparams['hidecol_front'] = (int)$params->get('hidecol_front', 0);
			$this->tplparams['hidecol_path1'] = trim($params->get('hidecol_path1', ''));
			$this->tplparams['hidecol_path2'] = trim($params->get('hidecol_path2', ''));
			$this->tplparams['hidecol_path3'] = trim($params->get('hidecol_path3', ''));
			$this->tplparams['bgcolor'] = trim($params->get('bgcolor', 'EEEEEE'));
			if (($this->tplparams['bgcolor'] == '') || (strlen($this->tplparams['bgcolor']) != 6)) { $this->tplparams['bgcolor'] = 'EEEEEE'; }
			$this->tplparams['pathway'] = (int)$params->get('pathway', 2);
			$this->tplparams['pathway_here'] = (int)$params->get('pathway_here', 1);
			$this->tplparams['pathway_colour'] = trim($params->get('pathway_colour', '3D4653'));
			if (($this->tplparams['pathway_colour'] == '') || (strlen($this->tplparams['pathway_colour']) != 6)) { $this->tplparams['pathway_colour'] = '3D4653'; }
			$this->tplparams['footer_colour'] = trim($params->get('footer_colour', '3D4653'));
			if (($this->tplparams['footer_colour'] == '') || (strlen($this->tplparams['footer_colour']) != 6)) { $this->tplparams['footer_colour'] = '3D4653'; }
			$this->tplparams['footer1_width'] = (int)$params->get('footer1_width', 565);
			if ($this->tplparams['footer1_width'] < 140) { $this->tplparams['footer1_width'] = 565; }
			if ($this->tplparams['footer1_width'] > 925) { $this->tplparams['footer1_width'] = 925; }
		}

		$this->sidecolumn = $this->determineColumn();
		$this->pathway = $this->determinePathway();
		$this->footer = $this->determineFooter();

		if (!$process) { return; }

		if ($this->apc == true) {
			elxisAPC::store('params', 'tpldelta', $this->tplparams, 7200);
		}
    }


	/***********************************/
	/* GET TEMPLATE PARAMETERS FROM DB */
	/***********************************/    
	private function getDBParams() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('params')." FROM ".$db->quoteId('#__templates')
		."\n WHERE ".$db->quoteId('template').' = '.$db->quote('delta').' AND '.$db->quoteId('section').' = '.$db->quote('frontend');
		$stmt = $db->prepareLimit($sql, 0, 1);
		$stmt->execute();
		return (string)$stmt->fetchResult();        
    }


	/***********************************************/
	/* DETERMINE IF WE SHOULD SHOW THE SIDE COLUMN */
	/***********************************************/
	private function determineColumn() {
		if ($this->tplparams['hidecol_all'] == 1) { return false; }

		$eURI = eFactory::getURI();
		$elxuri = $eURI->getElxisUri();
		$uri_str = $eURI->getUriString();

		if ($this->tplparams['hidecol_front'] == 1) {
			$elxis = eFactory::getElxis();
			if (($elxuri == '') || ($elxuri == 'content:/') || ($elxuri == '/') || ($elxuri == 'content') || ($elxuri == $elxis->getConfig('DEFAULT_ROUTE'))) {
				return false;
			}
		}
		if (($elxuri == '') || ($uri_str == '')) { return true; }

		$parts = explode('/', $uri_str);
		if (!$parts) { return true; }
		if (strlen($parts[0]) < 3) {
			array_shift($parts);
			if (!$parts) { return true; }
			$uri_str = implode('/', $parts);
		}

		if ($uri_str == '') { return true; }
		if ($this->tplparams['hidecol_path1'] != '') {
			if (strpos($uri_str, $this->tplparams['hidecol_path1']) === 0) { return false; }
		}
		if ($this->tplparams['hidecol_path2'] != '') {
			if (strpos($uri_str, $this->tplparams['hidecol_path2']) === 0) { return false; }
		}
		if ($this->tplparams['hidecol_path3'] != '') {
			if (strpos($uri_str, $this->tplparams['hidecol_path3']) === 0) { return false; }
		}
		return true;	
	}


	/*******************************************/
	/* DETERMINE IF WE SHOULD SHOW THE PATHWAY */
	/*******************************************/
	private function determinePathway() {
		if ($this->tplparams['pathway'] == 2) { return true; }
		if ($this->tplparams['pathway'] == 0) { return false; }
		if ($this->tplparams['pathway'] == 1) {
			$elxuri = eFactory::getURI()->getElxisUri();
			if (($elxuri == '') || ($elxuri == 'content:/') || ($elxuri == '/') || ($elxuri == 'content') || ($elxuri == eFactory::getElxis()->getConfig('DEFAULT_ROUTE'))) {
				return false;
			}
		}

		return true;
	}


	/********************************************/
	/* DETERMINE THE ELEMENTS TO LOAD ON FOOTER */
	/********************************************/
	private function determineFooter() {
		$eDoc = eFactory::getDocument();

		$footer = array();
		$footer['mods1'] = $eDoc->countModules('user1');
		$footer['mods2'] = $eDoc->countModules('user2');
		$footer['width1'] = $this->tplparams['footer1_width'];
		$footer['width2'] = 925 - $footer['width1'];
		if ($footer['width2'] == 0) { $footer['mods2'] = 0; }
		return $footer;
	}


	/*********************/
	/* COLOUR CALCULATOR */
	/*********************/
	private function colourCalc($hex, $factor=0, $darken=false) {
		$new_hex = '';
		$base = array();
		$base['R'] = hexdec(substr($hex, 0, 2));
		$base['G'] = hexdec(substr($hex, 2, 2));
		$base['B'] = hexdec(substr($hex, 4, 2));
		foreach ($base as $k => $v) {
			if ($darken) {
				$amount = $v / 100;
				$amount = round($amount * $factor);		
				$new_decimal = $v - $amount;	
			} else {
				$amount = 255 - $v;
				$amount = $amount / 100;
				$amount = round($amount * $factor);
				$new_decimal = $v + $amount;
			}

			$hex_seg = dechex($new_decimal);
			if (strlen($hex_seg) < 2) { $hex_seg = '0'.$hex_seg; }
			$new_hex .= $hex_seg;
		}

		return $new_hex;        
	}


	/**********************************************/
	/* CHECK IF A COLOUR IS A DARK OR A LIGHT ONE */
	/**********************************************/
	private function isDark($hexcolor) {
		$r = hexdec(substr($hexcolor,0,2));
		$g = hexdec(substr($hexcolor,2,2));
		$b = hexdec(substr($hexcolor,4,2));
		$yiq = (($r*299)+($g*587)+($b*114))/1000;
		return ($yiq >= 128) ? false : true;
	}


	/*************************/
	/* SHOW THE SIDE COLUMN? */
	/*************************/
	public function showColumn() {
		return $this->sidecolumn;
	}


	/*********************/
	/* SHOW THE PATHWAY? */
	/*********************/
	public function showPathway() {
		return $this->pathway;
	}


	/*********************************/
	/* SHOW THE YOU ARE HERE PREFIX? */
	/*********************************/
	public function youAreHere() {
		return ($this->tplparams['pathway_here'] == 1) ? true : false;
	}


	/*************************/
	/* SHOW LOGO OR SITENAME */
	/*************************/
	public function showLogo() {
		$elxis = eFactory::getElxis();

		$fplink = $elxis->makeURL();
    	if ($this->tplparams['sitename'] == 1) {
    		echo '<div class="delta_pad5">'."\n";
    		echo '<h2><a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a></h2>\n";
    		if ($this->tplparams['slogan'] != '') {
    			echo '<div class="delta_slogan">'.$this->tplparams['slogan']."</div>\n";
   			}
   			echo "</div>\n";
   			return;
   		}

		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'"><img src="'.$elxis->secureBase().'/'.$this->tplparams['logo'].'" alt="'.$elxis->getConfig('SITENAME').'" border="0" /></a>'."\n";
    }


	/**************************/
	/* ECHO FOOTER BOXES HTML */
	/**************************/
	public function footerBoxes() {
		$eDoc = eFactory::getDocument();

		if (($this->footer['mods1'] == 0) && ($this->footer['mods2'] == 0)) { return; }
		if (($this->footer['mods1'] > 0) && ($this->footer['mods2'] > 0)) {
			echo '<div class="delta_footer1">'."\n";
			$eDoc->modules('user1');
			echo "</div>\n";
			echo '<div class="delta_footer2">'."\n";
			$eDoc->modules('user2');
			echo "</div>\n";
		} else if ($this->footer['mods1'] > 0) {
			echo '<div class="delta_footerall">'."\n";
			$eDoc->modules('user1');
			echo "</div>\n";
		} else if ($this->footer['mods2'] > 0) {
			echo '<div class="delta_footerall">'."\n";
			$eDoc->modules('user2');
			echo "</div>\n";
		}
	}


	/***********************************/
	/* ADD DATA TO PAGE'S HEAD SECTION */
	/***********************************/     
	public function addHead() {
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();

		$base = $elxis->secureBase().'/templates/delta';

    	$eDoc->addJQuery();
    	$eDoc->addScriptLink($base.'/includes/hoverIntent.js');
    	$eDoc->addScriptLink($base.'/includes/superfish.js');
    	$eDoc->addScript('jQuery(function(){ jQuery(\'.delta_menu ul.elx_menu\').superfish(); });');

		$css = array();
		if ($this->tplparams['bgcolor'] != 'EEEEEE') {
			$css[] = 'body { background-color:#'.$this->tplparams['bgcolor'].'; }';
		}

		if ($this->tplparams['head_colour'] != '54A3F4') {
			$is_dark = $this->isDark($this->tplparams['head_colour']);
			$darken = !$is_dark;
			$light = $this->colourCalc($this->tplparams['head_colour'], 90, false);
			$border = $this->colourCalc($this->tplparams['head_colour'], 60, false);
			$contrast = $this->colourCalc($this->tplparams['head_colour'], 80, $darken);
			$contrast_full = ($is_dark == true) ? 'FFFFFF' : '333333';

			$head_css = '.delta_head { background-image:none; background-color:#'.$this->tplparams['head_colour'].';';
			$head_css .= ' background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#'.$this->tplparams['head_colour'].'), to(#'.$light.'));';
			$head_css .= ' background:-webkit-linear-gradient(top, #'.$this->tplparams['head_colour'].', #'.$light.');';
			$head_css .= ' background:-moz-linear-gradient(top, #'.$this->tplparams['head_colour'].', #'.$light.');';
			$head_css .= ' background:-ms-linear-gradient(top, #'.$this->tplparams['head_colour'].', #'.$light.');';
			$head_css .= ' background:-o-linear-gradient(top, #'.$this->tplparams['head_colour'].', #'.$light.');';
			$head_css .= ' background:linear-gradient(top, #'.$this->tplparams['head_colour'].', #'.$light.'); }'."\n";
			$head_css .= '.delta_head_logo h2, .delta_head_logo h2 a { color:#'.$contrast_full.'; }'."\n";
			$head_css .= '.delta_slogan { color:#'.$contrast.'; }'."\n";
			$head_css .= '.delta_head_position, .delta_head_position a { color:#'.$contrast_full.'; }'."\n";
			$head_css .= '.elx_modsearchform { background-color:#'.$border.'; }'."\n";

			$menu_center = $this->colourCalc($this->tplparams['head_colour'], 10, true);
			$menu_top = $this->colourCalc($menu_center, 30, false);
			$menu_bottom = $this->colourCalc($menu_center, 30, true);
			$menu_on = $this->colourCalc($menu_center, 50, false);
			$menu_on_top = $this->colourCalc($menu_on, 30, false);
			$menu_on_bottom = $this->colourCalc($menu_on, 30, true);

			$head_css .= '.delta_menu, .delta_menu .elx_menu li { background-image:none; background-color:#'.$menu_center.';';
			$head_css .= ' background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#'.$menu_top.'), to(#'.$menu_bottom.'));';
			$head_css .= ' background:-webkit-linear-gradient(top, #'.$menu_top.', #'.$menu_bottom.');';
			$head_css .= ' background:-moz-linear-gradient(top, #'.$menu_top.', #'.$menu_bottom.');';
			$head_css .= ' background:-ms-linear-gradient(top, #'.$menu_top.', #'.$menu_bottom.');';
			$head_css .= ' background:-o-linear-gradient(top, #'.$menu_top.', #'.$menu_bottom.');';
			$head_css .= ' background:linear-gradient(top, #'.$menu_top.', #'.$menu_bottom.'); }'."\n";
			$head_css .= '.delta_menu .elx_menu li:hover, .delta_menu .elx_menu li.deltahover, .delta_menu .elx_menu li a:focus, .delta_menu .elx_menu li a:hover, .delta_menu .elx_menu li a:active { ';
			$head_css .= ' background-image:none; background-color:#'.$menu_on.';';
			$head_css .= ' background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#'.$menu_on_top.'), to(#'.$menu_on_bottom.'));';
			$head_css .= ' background:-webkit-linear-gradient(top, #'.$menu_on_top.', #'.$menu_on_bottom.');';
			$head_css .= ' background:-moz-linear-gradient(top, #'.$menu_on_top.', #'.$menu_on_bottom.');';
			$head_css .= ' background:-ms-linear-gradient(top, #'.$menu_on_top.', #'.$menu_on_bottom.');';
			$head_css .= ' background:-o-linear-gradient(top, #'.$menu_on_top.', #'.$menu_on_bottom.');';
			$head_css .= ' background:linear-gradient(top, #'.$menu_on_top.', #'.$menu_on_bottom.');}'."\n";
			$head_css .= '.delta_menu .elx_menu li li, .delta_menu .elx_menu li li li, .delta_menu .elx_menu li li li li { ';
			$head_css .= ' background-color:#'.$menu_on.'; background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#'.$menu_on.'), to(#'.$menu_on.'));';
			$head_css .= ' background:-webkit-linear-gradient(top, #'.$menu_on.', #'.$menu_on.');';
			$head_css .= ' background:-moz-linear-gradient(top, #'.$menu_on.', #'.$menu_on.');';
			$head_css .= ' background:-ms-linear-gradient(top, #'.$menu_on.', #'.$menu_on.');';
			$head_css .= ' background:-o-linear-gradient(top, #'.$menu_on.', #'.$menu_on.');';
			$head_css .= ' background:linear-gradient(top, #'.$menu_on.', #'.$menu_on.'); }'."\n";
			$head_css .= '.delta_menu .elx_menu li ul li:hover, .delta_menu .elx_menu li ul li.deltahover, .delta_menu .elx_menu li ul li a:focus, .delta_menu .elx_menu li ul li a:hover, .delta_menu .elx_menu li ul li a:active { ';
			$head_css .= ' background-color:#'.$menu_top.'; background:-webkit-gradient(linear, 0% 0%, 0% 100%, from(#'.$menu_top.'), to(#'.$menu_top.'));';
			$head_css .= ' background:-webkit-linear-gradient(top, #'.$menu_top.', #'.$menu_top.');';
			$head_css .= ' background:-moz-linear-gradient(top, #'.$menu_top.', #'.$menu_top.');';
			$head_css .= ' background:-ms-linear-gradient(top, #'.$menu_top.', #'.$menu_top.');';
			$head_css .= ' background:-o-linear-gradient(top, #'.$menu_top.', #'.$menu_top.');';
			$head_css .= ' background:linear-gradient(top, #'.$menu_top.', #'.$menu_top.');}';

			$css[] = $head_css;
			unset($head_css, $is_dark, $darken, $light, $border, $contrast, $contrast_full, $menu_center, $menu_top, $menu_bottom, $menu_on, $menu_on_top, $menu_on_bottom);
		}

		if ($this->sidecolumn == true) {
			if ($this->tplparams['colwidth'] <> 200) {
				$mw = 935 - $this->tplparams['colwidth'];
				$w = $this->tplparams['colwidth'] - 12;
				$css[] = '.delta_maincol { width:'.$mw.'px; }';
				$css[] = '.delta_sidecol { width:'.$this->tplparams['colwidth'].'px; }';
				$css[] = '.elx_vmenu li { width:'.$w.'px; }';
				$css[] = '.elx_vmenu li ul { width:'.$this->tplparams['colwidth'].'px; }';
				$css[] = '.elx_vmenu li:hover > ul { left:-'.$this->tplparams['colwidth'].'px; }';
			}
		}
		if ($this->tplparams['pathway_colour'] != '3D4653') {
			$is_dark = $this->isDark($this->tplparams['pathway_colour']);
			$darken = !$is_dark;
			$path_text = ($is_dark == true) ? 'FFFFFF' : '333333';
			$path_here = $this->colourCalc($this->tplparams['pathway_colour'], 40, $darken);
			$path_link = $this->colourCalc($this->tplparams['pathway_colour'], 70, $darken);
			$path_pathtext = $this->colourCalc($this->tplparams['pathway_colour'], 100, $darken);

			$css[] = '.delta_pathway { background-color:#'.$this->tplparams['pathway_colour'].'; color:#'.$path_text.'; }';
			$css[] = 'span.elx_pathway_here { color:#'.$path_here.'; }';
			$css[] = 'span.pathway_text { color:#'.$path_pathtext.'; }';
			$css[] = 'a.pathway { color:#'.$path_link.'; }';
			$css[] = 'a.pathway:hover { color:#'.$path_pathtext.'; }';
		}

		if ($this->tplparams['footer_colour'] != '3D4653') {
			$is_dark = $this->isDark($this->tplparams['footer_colour']);
			$darken = !$is_dark;
			$footer_h3_text = ($is_dark == true) ? 'FFFFFF' : '333333';
			$footer_h3_bg = $this->colourCalc($this->tplparams['footer_colour'], 10, $darken);
			$footer_border = $this->colourCalc($this->tplparams['footer_colour'], 20, $darken);
			$footer_link = $this->colourCalc($this->tplparams['footer_colour'], 50, $darken);
			$footer_text = $this->colourCalc($this->tplparams['footer_colour'], 70, $darken);
			$footer_gen_text = $this->colourCalc($this->tplparams['footer_colour'], 90, $darken);

			$css[] = '.delta_footer { background-color:#'.$this->tplparams['footer_colour'].'; color:#'.$footer_gen_text.'; }';
			$css[] = '.delta_footer1 { border-color:#'.$footer_border.'; }';
			$css[] = '.delta_footer_copy { color:#'.$footer_gen_text.'; }';
			$css[] = '.delta_footer_copy a { color:#'.$footer_link.'; }';
			$css[] = '.delta_footer div.module { color:#'.$footer_text.'; }';
			$css[] = '.delta_footer div.module a { color:#'.$footer_link.'; }';
			$css[] = '.delta_footer div.module h3 { background-color:#'.$footer_h3_bg.'; color:#'.$footer_h3_text.'; }';
			$css[] = '.delta_footer_menu .elx_menu li { border-right:1px solid #'.$footer_border.'; }';
			$css[] = '.delta_footer_menu .elx_menu a, .delta_footer_menu .elx_menu a:visited, .delta_footer_menu .elx_menu a:focus, .delta_footer_menu .elx_menu a:hover { color:#'.$footer_link.'; } ';
		}

		if (($this->footer['mods1'] > 0) && ($this->footer['mods2'] > 0)) {
			if ($this->footer['width1'] <> 565) {
				$css[] = '.delta_footer1 { width:'.$this->footer['width1'].'px; }';
				$css[] = '.delta_footer2 { width:'.$this->footer['width2'].'px; }';
			}
		}

		if ($css) {
			echo '<style type="text/css">'."\n";
			if ($elxis->getConfig('DOCTYPE') != 'html5') { echo "\t<![CDATA[\n"; }
			foreach ($css as $rule) { echo "\t".$rule."\n"; }
			if ($elxis->getConfig('DOCTYPE') != 'html5') { echo "\t]]>\n"; }
			echo "</style>\n";
		}
	}

}

?>