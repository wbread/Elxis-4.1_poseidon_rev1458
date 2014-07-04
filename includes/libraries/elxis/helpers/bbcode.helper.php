<?php 
/**
* @version		$Id: bbcode.helper.php 739 2011-11-13 16:46:07Z datahell $
* @package		Elxis
* @subpackage	Helpers / BBCode
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisBbcodeHelper {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
	}


	/**************************/
	/* CONVERT BBCODE TO HTML */
	/**************************/
	public function toHTML($text, $convert_smileys=true) {
		$bbcode_replacements = array(
			//"#\[/\]#" => '',
			"#\<#" => '&lt;',
			"#\>#" => '&gt;',
			"#\[b\](.+)\[/b\]#isU" => "<strong>$1</strong>",
			"#\[u\](.+)\[/u\]#isU" => "<u>$1</u>",
			"#\[i\](.+)\[/i\]#isU" => "<em>$1</em>",
			"#\[s\](.+)\[/s\]#isU" => "<s>$1</s>",
    		"#\[list\](.+)\[/list\]#isU" => "<ul class=\"elx_stdul\">$1</ul>",
    		"#\[list(.+)\](.+)\[/list\]#isU" => "<ol class=\"elx_stdol\">$2</ol>",
			"#\[li\](.+)\[/li\]#isU" => "<li>$1</li>",
    		//"#\[\*\](.+)\[/\*\]#isU" => "<li>$1</li>",
    		"#\[\*\](.+)\n#isU" => "<li>$1</li>",
    		"#\[/\*\]#" => '',
			"#\[img\](.+)\[/img\]#isU" => "<img src=\"$1\" border=\"0\" alt=\"image\" />",
			"#\[img=(.+)\]#isU" => "<img src=\"$1\" border=\"0\" alt=\"image\" />",
			"#\[color=(.+)\](.+)\[/color\]#isU" => "<span style=\"color:$1\">$2</span>",
			"#\[size=([0-9]+)\](.+)\[/size\]#isU" => "<span style=\"font-size:$1%\">$2</span>",
    		"#\[url\](.+)\[/url\]#isU" => "<a href=\"$1\">$1</a>",
    		"#\[url=(.+)\](.+)\[/url\]#isU" => "<a href=\"$1\">$2</a>",
    		"#\[email\](.+)\[/email\]#isU" => "<a href=\"mailto:$1\">$1</a>",
    		"#\[email=(.+)\](.+)\[/email\]#isU" => "<a href=\"mailto:$1\">$2</a>",
			"#\[quote\](.+)\[/quote\]#isU" => "<blockquote>$1</blockquote>",
			"#\[quote(.+)\](.+)\[/quote\]#isU" => "<blockquote>$1: %2</blockquote>",
			"#\[code\](.+)\[/code\]#isU" => "<pre>$1</pre>",
			"#\[code(.+)\](.+)\[/code\]#isU" => "<pre>$2</pre>",
			"#\[warning\](.+)\[/warning\]#isU" => "<div class=\"elx_warning\">$1</div>",
			"#\[info\](.+)\[/info\]#isU" => "<div class=\"elx_info\">$1</div>",
			"#\[youtube\](.+)\[/youtube\]#isU" => "<embed src=\"http://www.youtube.com/v/$1\" type=\"application/x-shockwave-flash\" width=\"400\" height=\"325\"></embed>",
			"#\[gvideo\](.+)\[/gvideo\]#isU" => "<embed src=\"http://video.google.com/googleplayer.swf?docId=$1\" type=\"application/x-shockwave-flash\" style=\"width:400px; height:325px;\"></embed>"
		);

    	foreach ($bbcode_replacements as $key => $val) {
    		$text = preg_replace($key, $val, $text);
    	}
    	
    	if ($convert_smileys) {
    		$text = $this->smileys2Img($text);
   		}

    	$text = nl2br($text);
   		$text = $this->improveHTML($text);

    	return $text;
	}


	/********************************************/
	/* PERFORM SOME FINAL FIXES FOR BETTER HTML */
	/********************************************/
	private function improveHTML($text) {
		$replacements = array(
			'<br>' => '<br />',
			'<ul class="elx_stdul"><br />' => '<ul class="elx_stdul">',
			'<ol class="elx_stdol"><br />' => '<ol class="elx_stdol">',
			'<li><br />' => "<li>\n",
			'</li><br />' => "</li>\n",
			'</ul><br />' => "</ul>\n",
			'</ol><br />' => "</ol>\n",
			'<br /><br /><br />' => '<br /><br />',
		);

		foreach ($replacements as $needle => $replacement) {
			$text = str_replace($needle, $replacement, $text);
		}
		return $text;
	}


	/*****************************/
	/* CONVERT SMILEYS TO IMAGES */
	/*****************************/
	private function smileys2Img($text) {
		$baseurl = eFactory::getElxis()->secureBase().'/includes/js/ckeditor/plugins/smiley/images/';
		$smileys = array(
			':)' => 'regular_smile',
			':(' => 'sad_smile',
			';)' => 'wink_smile',
			':D' => 'teeth_smile',
			':P' => 'tounge_smile',
			':*)' => 'embaressed_smile',
			':-o' => 'omg_smile',
			':|' => 'confused_smile',
			'>:(' => 'angry_smile',
			'&gt;:(' => 'angry_smile',
			'o:)' => 'angel_smile',
			'8-)' => 'shades_smile',
			'>:-)' => 'devil_smile',
			'&gt;:-)' => 'devil_smile',
			';(' => 'cry_smile',
			':-*' => 'kiss'
		);
		
		foreach ($smileys as $smile => $ico) {
			$img = '<img src="'.$baseurl.$ico.'.gif" alt="'.$ico.'" border="0" />'."\n";
			$text = str_replace($smile, $img, $text);
		}
		return $text;
	}


	/********************************/
	/* CONVERT BBCODE TO CLEAR TEXT */
	/********************************/
	public function toText($text) {
		$bbcode_replacements = array(
			"#\[/\]#" => '',
			"#\<#" => '&lt;',
			"#\>#" => '&gt;',
			"#\[b\](.+)\[/b\]#isU" => "$1",
			"#\[i\](.+)\[/i\]#isU" => "$1",
			"#\[u\](.+)\[/u\]#isU" => "$1",
			"#\[s\](.+)\[/s\]#isU" => "$1",
			"#\[color=(.+)\](.+)\[/color\]#isU" => "$2",
			"#\[size=([0-9]+)\](.+)\[/size\]#isU" => "$2",
			"#\[img\](.+)\[/img\]#isU" => '',
			"#\[img=(.+)\]#isU" => '',
			"#\[quote\](.+)\[/quote\]#isU" => 'Quote: ('."$1".')'."\n",
			"#\[quote(.+)\](.+)\[/quote\]#isU" => 'Quote: ('."$2".')'."\n",
			"#\[youtube\](.+)\[/youtube\]#isU" => "\n",
			"#\[gvideo\](.+)\[/gvideo\]#isU" => "\n",
			"#\[code\](.+)\[/code\]#isU" => '',
			"#\[code(.+)\](.+)\[/code\]#isU" => '',
			"#\[warning\](.+)\[/warning\]#isU" => "$1\n",
			"#\[info\](.+)\[/info\]#isU" => "$1\n",
    		"#\[email\](.+)\[/email\]#isU" => "$1",
    		"#\[email=(.+)\](.+)\[/email\]#isU" => "$1 ($2)",
    		"#\[url\](.+)\[/url\]#isU" => "$1",
    		"#\[url=(.+)\](.+)\[/url\]#isU" => "$2 ($1)",
    		"#\[list\](.+)\[/list\]#isU" => "$1\n",
    		"#\[list(.+)\](.+)\[/list\]#isU" => "$2\n",
			"#\[li\](.+)\[/li\]#isU" => " - $1\n",
    		//"#\[\*\](.+)\[/\*\]#isU" => "$1\n",
    		"#\[\*\](.+)\n#isU" => " - $1\n",
    		"#\[/\*\]#" => ''
		);
    	foreach ($bbcode_replacements as $key => $val) {
    		$text = preg_replace($key, $val, $text);
    	}
    	return $text;
	}

}

?>