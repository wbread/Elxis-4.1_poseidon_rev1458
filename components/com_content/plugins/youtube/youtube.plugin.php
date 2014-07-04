<?php 
/**
* @version		$Id: youtube.plugin.php 1096 2012-05-02 13:05:59Z webgift $
* @package		Elxis
* @subpackage	Content Plugins / YouTube
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class youtubePlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = "#{youtube\s*(.*?)}(.*?){/youtube}#s";
		$regexno = "#{youtube\s*.*?}.*?{/youtube}#s";
		if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
		}

		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$ePlugin = eFactory::getPlugin();
		foreach ($matches[0] as $i => $match) {
			$videoid = trim($matches[2][$i]);
			if ($videoid == '') {
				$row->text = preg_replace("#".$match."#", '', $row->text);
				continue;
			}

			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			$html = $this->makeYoutubeHTML($videoid, $attributes);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}

		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{youtube width="425" height="350"}YouTube video ID{/youtube}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('VIDEOID') , $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->getVideoId(); break;
			case 2: $this->Help(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/youtube/includes/youtube.js'),
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


	/********************************/
	/* MAKE YOUTUBE VIDEO HTML CODE */
	/********************************/
	private function makeYoutubeHTML($videoid, $attributes) {
		$width = 425;
		$height = 350;
		if ($attributes) {
			$width = (isset($attributes['width']) && ($attributes['width'] > 100)) ? (int)$attributes['width'] : 425;
			$height = (isset($attributes['height']) && ($attributes['height'] > 100)) ? (int)$attributes['height'] : 350;
		}

		$out = "<object width=\"".$width."\" height=\"".$height."\">\n";
		$out .= "<param name=\"movie\" value=\"http://www.youtube.com/v/".$videoid."\"></param>\n";
		$out .= "<param name=\"wmode\" value=\"transparent\"></param>\n";
		$out .= "<embed src=\"http://www.youtube.com/v/".$videoid."\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"".$width."\" height=\"".$height."\"></embed>\n";
		$out .= "</object>\n";

		return $out;
	}


	/******************/
	/* GET A VIDEO ID */
	/******************/
	private function getVideoId() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<table border="0" class="plug_table" dir="'.$eLang->getinfo('DIR').'">'."\n";
		echo '<tr><td class="plug_td200">'.$eLang->get('VIDEOID')."</td>\n";
		echo '<td><input type="text" name="youtube_videoid" id="youtube_videoid" class="inputbox" size="40" dir="ltr" value="" />'."\n";
		echo '&#160; <a href="javascript:void(null);" onclick="addYTVideoID()" title="'.$eLang->get('ADD').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/youtube/includes/link.png" alt="link" border="0" /></a></td></tr>'."\n";
		echo "</table>\n";
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function Help() {
?>
		<p><strong>Youtube Video</strong> plugin allows you to place a Youtube Video inside article . </p>
		<p><strong><em>How will i get a Youtube Video id?</em></strong><br />
		Each Video on Youtube have a specific URL structure. For example: An elxis video regarding the Elxis Download Center is placed on the url : 
		<em>http://www.youtube.com/watch?v=EZHR569uoew</em>. The video id for this URL is: <strong>EZHR569uoew</strong></p>
<?php 
	}

}

?>