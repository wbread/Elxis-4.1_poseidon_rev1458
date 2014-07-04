<?php 
/**
* @version		$Id: autolinks.plugin.php 1093 2012-05-01 18:42:24Z datahell $
* @package		Elxis
* @subpackage	Content Plugins / Atuomatic links
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class autolinksPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
    	$regex = "#{autolinks}(.*?){/autolinks}#s";
    	if (!$published) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
    	}

		preg_match($regex, $row->text, $matches);
		if (!$matches) { return true; }
    	$row->text = preg_replace($regex, '', $row->text);

		if (isset($matches[1]) && (trim($matches[1]) != '')) {
			$tags = explode(',', $matches[1]);
		} else {
			if (!isset($row->keywords['tags'])) { return true; }
			if (!is_array($row->keywords['tags']) || (count($row->keywords['tags']) == 0)) { return true; }
			$tags = $row->keywords['tags'];
		}

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$baselink = $elxis->makeURL('tags.html');
		foreach ($tags as $tag) {
			$title = sprintf($eLang->get('ARTICLES_TAGGED'), $tag);
			$html = '<a href="'.$baselink.'?tag='.urlencode($tag).'" title="'.$title.'">'.$tag.'</a>';
			$row->text = preg_replace('#'.$tag.'#i', $html, $row->text);
		}

		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{autolinks}comma,separated,keywords{/autolinks}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('KEYWORDS'), $eLang->get('HELP'));
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->makeCode(); break;
			case 2: $this->showHelp(); break;
			default:break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/autolinks/includes/autolinks.js'),
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


	/**************************/
	/* MAKE / ADD PLUGIN CODE */
	/**************************/
	private function makeCode() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<table border="0" class="plug_table" dir="'.$eLang->getinfo('DIR').'">'."\n";
		echo '<tr><td class="plug_td200">'.$eLang->get('OPT_KEYWORDS')."</td>\n";
		echo '<td><input type="text" name="autolink_keys" id="autolink_keys" class="inputbox" size="40" dir="ltr" value="" />'."\n";
		echo '&#160; <a href="javascript:void(null);" onclick="addAutolinkCode()" title="'.$eLang->get('ADD').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/autolinks/includes/link.png" alt="link" border="0" /></a></td></tr>'."\n";
		echo "</table>\n";
	}


	/*************/
	/* SHOW HELP */
	/*************/
	private function showHelp() {
?>
		<p><strong>Automatic Links</strong> plugin will convert given keywords, or article&apos;s META keywords, into links pointing to the tags search page. 
		This way visitors can easily find similar articles. The plugin will boost your site&apos; SEO value as it improves cross-site linking. 
		To enable autolinks in an article just insert the plugin code <em>{autolinks}{/autolinks}</em> into the article&apos;s text area. Insert the plugin 
		code only once in any editor instance. If you don&apos;t provide keywords then the article&apos;s META keywords will be used as keywords. If you want 
		you can set which keywords to use by separating them with commas like this: <em>{autolinks}italy,spain,greece,russia{/autolinks}</em>.<br />
		<strong>Tip</strong>: Pick keywords that exists as META keywords in other articles!</p>
		<div class="elx_warning"><strong>Caution</strong>: Autolinks will convert ANY matching keyword into an HTML link. If you are not carefull you 
		might break HTML code. For instance the <em>&lt;strong&gt;</em> HTML tag will be converted to a link if you use a keyword such as 
		<strong>strong</strong>, or even worst, HTML attributes such as <em>title</em> may containg one of the matching keywords. So, pick your keywords with 
		caution.</div>
<?php 
	}

}

?>