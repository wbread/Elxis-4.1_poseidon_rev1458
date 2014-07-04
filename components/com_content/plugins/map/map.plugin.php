<?php 
/**
* @version		$Id: map.plugin.php 1116 2012-05-09 13:05:59Z webgift $
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class mapPlugin implements contentPlugin {


	private static $imap = 0;


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{map\s*(.*?)}(.*?){/map}#s";
    	$regexno = "#{map\s*.*?}.*?{/map}#s";
    	if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$ePlugin = eFactory::getPlugin();

		$cfg = array();
		$cfg['width'] = (int)$params->get('width', 550);
		if (($cfg['width'] < 100) || ($cfg['width'] > 900)) { $cfg['width'] = 550; }
		$cfg['height'] = (int)$params->get('height', 400);
		if (($cfg['height'] < 100) || ($cfg['height'] > 800)) { $cfg['height'] = 400; }
		$cfg['mtype'] = trim($params->get('mtype', 'ROADMAP'));
		if (($cfg['mtype'] == '') || !in_array($cfg['mtype'], array('ROADMAP', 'SATELLITE', 'HYBRID', 'TERRAIN'))) { $cfg['mtype'] = 'ROADMAP'; }
		$cfg['mtypecontrol'] = (intval($params->get('mtypecontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mtypecontrolopts'] = trim($params->get('mtypecontrolopts', 'DEFAULT'));
		if (($cfg['mtypecontrolopts'] == '') || !in_array($cfg['mtypecontrolopts'], array('DEFAULT', 'HORIZONTAL_BAR', 'DROPDOWN_MENU'))) { $cfg['mtypecontrolopts'] = 'DEFAULT'; }
		$cfg['mzoom'] = (int)$params->get('mzoom', 13);
		if (($cfg['mzoom'] < 1) || ($cfg['mzoom'] > 20)) { $cfg['mzoom'] = 13; }
		$cfg['mzoomcontrol'] = (intval($params->get('mzoomcontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mzoomcontrolopts'] = trim($params->get('mzoomcontrolopts', 'DEFAULT'));
		if (($cfg['mzoomcontrolopts'] == '') || !in_array($cfg['mzoomcontrolopts'], array('DEFAULT', 'SMALL', 'LARGE'))) { $cfg['mzoomcontrolopts'] = 'DEFAULT'; }
		$cfg['mnavcontrol'] = (intval($params->get('mnavcontrol', 1)) == 1) ? 'true' : 'false';
		$cfg['mnavcontrolopts'] = trim($params->get('mnavcontrolopts', 'DEFAULT'));
		if (($cfg['mnavcontrolopts'] == '') || !in_array($cfg['mnavcontrolopts'], array('DEFAULT', 'SMALL', 'ANDROID', 'ZOOM_PAN'))) { $cfg['mnavcontrolopts'] = 'DEFAULT'; }
		$cfg['mscale'] = (intval($params->get('mscale', 1)) == 1) ? 'true' : 'false';
		$cfg['key'] = trim($params->get('key', ''));

		if (ELXIS_MOBILE == 1) {
			$cfg['mtypecontrol'] = 'false';
			$cfg['mnavcontrolopts'] = 'SMALL';
		}

		foreach ($matches[0] as $i => $match) {
			$address = trim($matches[2][$i]);
			if ($address == '') {
				$row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}
			if (preg_match('#([^0-9\-\,\.])#', $address)) {
				$repl = '<div class="elx_warning">Invalid map coordinates!</div>'."\n";
				$row->text = preg_replace("#".$match."#", $repl, $row->text);
				continue;
			}

			self::$imap++;
			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			$html = $this->makeMap($cfg, $attributes, $address);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{map info="optional info" width="opt width" height="opt height"}latitude,longitude{/map}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('COORDINATES'), $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->setArea(); break;
			case 2: $this->showHelp(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/map/includes/map.js'),
			'css' => array()
		);
		return $response;
	}
    

	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$elxis->redirect($url);
	}


	/**********************************/
	/* GENERATE GOOGLE MAPS HTML CODE */
	/**********************************/
	private function makeMap($cfg, $attributes, $address) {
		$info = '';
		$width = $cfg['width'];
		$height = $cfg['height'];
		if (isset($attributes['info']) && ($attributes['info'] != '')) {
			$pat = '@([\']|[\"]|[\$]|[\#]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])@';
			$info = strip_tags($attributes['info']);
			$info = preg_replace($pat, '', $info);
		}

		if (isset($attributes['width']) && isset($attributes['height']) && (ELXIS_MOBILE == 0)) {
			$w = (int)$attributes['width'];
			$h = (int)$attributes['height'];
			if (($w > 100) && ($height > 80)) {
				$width = $w;
				$height = $h;
			}
		}

		$this->importJS($cfg, $info, $address);

		if (ELXIS_MOBILE == 1) {
			$html = '<div id="googlemap'.self::$imap.'" style="width:100%; height:300px;"></div>';
		} else {
			$html = '<div id="googlemap'.self::$imap.'" style="width:'.$width.'px; height:'.$height.'px;"></div>';
		}

		return $html;
	}


	/******************************/
	/* IMPORT REQUIRED JAVASCRIPT */
	/******************************/	
	private function importJS($cfg, $info, $address) {
		$eDoc = eFactory::getDocument();

		$latlng = explode(',',$address);
		if (!is_array($latlng) || (count($latlng) != 2)) { return; }

		if (!defined('PLUG_MAP_LOADED')) {
			$eDoc->setContentType('text/html'); //google maps do not work with application/xhtml+xml due to document.write
			if ($cfg['key'] != '') {
				$eDoc->addScriptLink('https://maps.googleapis.com/maps/api/js?key='.$cfg['key'].'&sensor=false');
			} else {
				$eDoc->addScriptLink('https://maps.googleapis.com/maps/api/js?sensor=false');
			}

			$js = 'var mapcfg = { mzoom:'.$cfg['mzoom'].', mtypecontrol:'.$cfg['mtypecontrol'].', mzoomcontrol:'.$cfg['mzoomcontrol'].', mnavcontrol:'.$cfg['mnavcontrol'];
			$js .= ', mscale:'.$cfg['mscale'].', mtype:\''.$cfg['mtype'].'\'';
			if ($cfg['mtypecontrol'] === 'true') { $js.= ', mtypecontrolopts: \''.$cfg['mtypecontrolopts'].'\''; }
			if ($cfg['mzoomcontrol'] === 'true') { $js.= ', mzoomcontrolopts: \''.$cfg['mzoomcontrolopts'].'\''; }
			if ($cfg['mnavcontrol'] === 'true') { $js.= ', mnavcontrolopts: \''.$cfg['mnavcontrolopts'].'\''; }
			$js .= ', address: [], lat: [], lng: [], info: []'."};\n";
			$js .= 'window.onload = function() { initGoogleMaps(); }';
			$eDoc->addScript($js);

			$link = eFactory::getElxis()->secureBase().'/components/com_content/plugins/map/includes/map.js';
			$eDoc->addScriptLink($link);
			define('PLUG_MAP_LOADED', 1);
		}

		$js = 'mapcfg.lat['.self::$imap.'] = '.$latlng[0].'; mapcfg.lng['.self::$imap.'] = '.$latlng[1].'; mapcfg.info['.self::$imap.'] = \''.$info.'\';';
		$eDoc->addScript($js);
	}


	/***************/
	/* SET AN AREA */
	/***************/
	private function setArea() {
		$eLang = eFactory::getLang();

		echo '<table border="0" class="plug_table" dir="'.$eLang->getinfo('DIR').'">'."\n";
		echo '<tr><td class="plug_td200">'.$eLang->get('COORDINATES')."</td>\n";
		echo '<td><input type="text" name="map_area" id="map_area" class="inputbox" size="40" dir="ltr" value="" />'."\n";
		echo '&#160; <a href="javascript:void(null);" onclick="addMapAreaCode()" title="'.$eLang->get('ADD').'">'."\n";
		echo '<img src="'.eFactory::getElxis()->secureBase().'/components/com_content/plugins/map/includes/link.png" width="16" height="16" alt="link" border="0" /></a></td></tr>'."\n";
		echo '<tr><td class="plug_td200">'.$eLang->get('MAP_WIDTHL')."</td>\n";
		echo '<td><input type="text" name="map_width" id="map_width" class="inputbox" size="10" maxlength="4" dir="ltr" value="" /> <span dir="ltr">('.$eLang->get('OPTIONAL').')</span></td></tr>'."\n";
		echo '<tr><td class="plug_td200">'.$eLang->get('MAP_HEIGHTL')."</td>\n";
		echo '<td><input type="text" name="map_height" id="map_height" class="inputbox" size="10" maxlength="4" dir="ltr" value="" /> <span dir="ltr">('.$eLang->get('OPTIONAL').')</span></td></tr>'."\n";
		echo "</table>\n";		
	}
	
	
	/***************/
	/* PLUGIN HELP */
	/***************/
	private function showHelp() {
		$imglink = eFactory::getElxis()->secureBase().'/components/com_content/plugins/map/includes/coordinates.png';
?>		
		<p><strong>Map</strong> plugin allows you to display Google maps inside Elxis articles. You can display any location in the world and info for each location. 
		The map appearance is fully customizable (marker style, map size, map type can be normal, satellite and hybrid, zoom, controls, map scale etc). 
		You can change these parameters on Map plugin&apos;s edit page.</p>
		<h3>Usage</h3>
		<p>The generic syntax of the plugin is:<br />
		<pre>{map info=&quot;optional info&quot; width=&quot;optional width&quot; height=&quot;optional height&quot;}latitude,longitude{/map}</pre>
		To get a location coordinates, go to <a href="http://maps.google.com/" target="_blank">Google maps</a> find the spot you are interested in and central 
		the map on this spot. After, click on the link icon Google provides you to copy the URL of the page. On the short URL there is an attribute named 
		<strong>ll</strong> or <strong>sll</strong>. These are the coordinates (Latitude,Longitude) we are interested in. 
		Copy this in the plugin&apos;s Coordinates text box.<br />
		<img src="<?php echo $imglink; ?>" alt="coordinates" style="border:1px solid #ccc; padding:3px;" /></p>	
		<h3>Limitations</h3>
		<p>You can use the Google Maps API version 3 for free. However if you make massive usage it is recommended to take a look at the 
		<a href="https://developers.google.com/maps/documentation/javascript/usage" title="Google maps usage limits" target="_blank">usage limits</a> 
		of this API. You must load the maps API using an API key in order to purchase additional quota.</p>

<?php 
	}

}

?>