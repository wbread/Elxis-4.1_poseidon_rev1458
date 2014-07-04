<?php 
/**
* @version		$Id: video.plugin.php 1127 2012-05-13 09:36:16Z datahell $
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class videoPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
    	$regex = "#{video\s*(.*?)}(.*?){/video}#s";
    	$regexno = "#{video\s*.*?}.*?{/video}#s";
    	if (!$published) {
    		$row->text = preg_replace($regexno, '', $row->text);
    		return true;
    	}

		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$valid_exts = array('ogg', 'ogv', 'mp4', 'webm');
		$elxis_url_base = eFactory::getElxis()->secureBase();
		$ePlugin = eFactory::getPlugin();

		$cfg = array();
		$cfg['width'] = (int)$params->get('width', 480);
		if ($cfg['width'] < 10) { $cfg['width'] = 480; }
		$cfg['height'] = (int)$params->get('height', 270);
		if ($cfg['height'] < 10) { $cfg['height'] = 270; }
		$cfg['controls'] = (int)$params->get('controls', 1);
		$cfg['autoplay'] = (int)$params->get('autoplay', 0);
		$cfg['loop'] = (int)$params->get('loop', 0);
		$cfg['subtitles'] = (int)$params->get('subtitles', 0);
		$cfg['theme'] = trim($params->get('theme', 'default'));
		if (($cfg['theme'] == '') || !in_array($cfg['theme'], array('default', 'monoblue', 'monochrome', 'monochrome.fixed', 'vim'))) { $cfg['theme'] = 'default'; }

		foreach ($matches[0] as $i => $match) {
			$videopath = $matches[2][$i];
			if (($videopath == '') || !file_exists(ELXIS_PATH.'/'.$videopath)) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}
			
			$info = $this->mediaInfo($videopath);

		 	if (($info['extension'] == '') || !in_array($info['extension'], $valid_exts)) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
		 	}

			$attributes = $ePlugin->parseAttributes($matches[1][$i]);
			$idx = $row->id.'_'.$i;
			$video = $this->videoData($info, $cfg, $attributes, $elxis_url_base, $idx);
			$this->importCSSJS($cfg['theme']);
			$html = $this->makeVideoHTML($video);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{video width="480" height="270"}relative/path/to/video/sample.webm{/video}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		$tabs = array($eLang->get('SELECT_VIDEO'), $eLang->get('UPLOAD_MEDIA'), $eLang->get('HELP'));
		return $tabs;
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->listVideos(); break;
			case 2: $this->uploadForm($pluginid, $fn); break;
			case 3: $this->showHelp(); break;
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
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		$sess_token = trim($eSession->get('token_plugform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			echo '<div class="elx_error">'.$eLang->get('REQDROPPEDSEC')."</div>\n";
			return;
		}

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
			$elxis->redirect($url);
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
			$elxis->redirect($url);
		}

		if (!isset($_FILES) || (count($_FILES) == 0)) {
			$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
			$elxis->redirect($url);
		}

		$valid_exts = array('ogg', 'ogv', 'mp4', 'webm', 'jpg', 'jpeg', 'png', 'vtt', 'srt');
		$relpath = $this->videoRoot();
		$uploaded = array();
		for ($i=1; $i < 4; $i++) {
			if (!isset($_FILES['vfile'.$i])) { continue; }
			$upf = $_FILES['vfile'.$i];
		 	if (($upf['name'] != '') && ($upf['error'] == 0) && ($upf['size'] > 0)) {
		 		$filename = strtolower(preg_replace('/[^a-zA-Z0-9\_\-\.]/', '', $upf['name']));
		 		$info = $eFiles->getNameExtension($filename);
		 		if (($info['extension'] == '') || !in_array($info['extension'], $valid_exts)) { continue; }
		 		if (file_exists(ELXIS_PATH.'/'.$relpath.$filename)) { continue; }
		 		$ok = $eFiles->upload($upf['tmp_name'], $relpath.$filename);
		 		if ($ok) { $uploaded[] = $filename; }
	 		}
		}

		$msg = '';
		$url = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		if ($uploaded) { $msg = 'Uploaded '.implode(', ', $uploaded); }
		
		$elxis->redirect($url, $msg);
	}


	/******************************/
	/* IMPORT REQUIRED CSS AND JS */
	/******************************/
	private function importCSSJS($theme) {
		if (defined('PLUGIN_VIDEO_LOADED')) { return; }
		
		$eDoc = eFactory::getDocument();
		$player_path = eFactory::getElxis()->secureBase().'/components/com_content/plugins/video/';
		$eDoc->addStyleLink($player_path.'css/leanbackPlayer.'.$theme.'.css');
		$eDoc->addScriptLink($player_path.'js/leanbackPlayer.pack.js');
		$eDoc->addScriptLink($player_path.'js/leanbackPlayer.en.js');
		$lng = eFactory::getLang()->currentLang();
		if (file_exists(ELXIS_PATH.'/components/com_content/plugins/video/js/leanbackPlayer.'.$lng.'.js')) {
			$eDoc->addScriptLink($player_path.'js/leanbackPlayer.'.$lng.'.js');
		}
		define('PLUGIN_VIDEO_LOADED', 1);
	}


	/******************************/
	/* GET MEDIA FILE INFORMATION */
	/******************************/
	private function mediaInfo($path) {
		$parts = preg_split('#\/#', $path, -1, PREG_SPLIT_NO_EMPTY);
		$n = count($parts);
		$last = $n - 1;
		$pos = strrpos($parts[$last], '.');
		$info = array();
		$info['filename'] = substr($parts[$last], 0, $pos);
		$info['extension'] = substr($parts[$last], $pos + 1);
		$info['dir'] = '';
		if ($n > 1) {
			for($i=0; $i<$last; $i++) { $info['dir'] .= $parts[$i].'/'; }
		}
		return $info;
	}


	/************************/
	/* MAKE VIDEO HTML CODE */
	/************************/
	private function makeVideoHTML($video) {
		$out = '<div id="'.$video->id.'" class="leanback-player-video">'."\n";
		$out .= '<video width="'.$video->width.'" height="'.$video->height.'" preload="auto"';
		if ($video->controls == 1) { $out .= ' controls="controls"'; }
		if ($video->autoplay == 1) { $out .= ' autoplay="autoplay"'; }
		if ($video->loop == 1) { $out .= ' loop="loop"'; }
		if ($video->poster != '') { $out .= ' poster="'.$video->poster.'"'; }
		$out .= ">\n";
		foreach ($video->sources as $source) {
			$out .= '<source type="'.$source['type'].'" src="'.$source['src'].'" />'."\n";
		}
		if ($video->tracks) {
			foreach ($video->tracks as $track) {
    			$out .= '<track enabled="true" kind="'.$track['kind'].'" label="'.$track['label'].'" srclang="'.$track['srclang'].'" type="'.$track['type'].'" src="'.$track['src'].'"></track>'."\n";
			}
		}
		$out .= '<div class="leanback-player-html-fallback">'."\n";
		if ($video->poster != '') {
			$out .= '<img src="'.$video->poster.'" width="'.$video->width.'" height="'.$video->height.'" alt="video" title="Your browser does not support HTML5 video!" />'."\n";
		}  else {
			$out .= '<div>Your browser does not support HTML5 video!</div>'."\n";
		}
		$out .= "</div>\n";
    	$out .= "</video>\n";
		$out .= "</div>\n";
		return $out;
	}


	/***********************/
	/* GET FULL VIDEO DATA */
	/***********************/
	private function videoData($info, $cfg, $attributes, $elxis_url_base, $idx) {
		$video = new stdClass;
		$video->id = 'elx_html5_video_'.$idx;
		$video->width = $cfg['width'];
		$video->height = $cfg['height'];
		$video->controls = $cfg['controls'];
		$video->autoplay = $cfg['autoplay'];
		$video->loop = $cfg['loop'];
		$video->sources = array();
		$video->tracks = array();
		$video->poster = '';		
		if ($attributes) {
			if (isset($attributes['width']) && ($attributes['width'] > 10)) { $video->width = (int)$attributes['width']; }
			if (isset($attributes['height']) && ($attributes['height'] > 10)) { $video->height = (int)$attributes['height']; }
		}

		if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.webm')) {
			$video->sources[] = array('type' => 'video/webm', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'.webm');
		}
		if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.mp4')) {
			$video->sources[] = array('type' => 'video/mp4', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'.mp4');
		}
		if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.ogg')) {
			$video->sources[] = array('type' => 'video/ogg', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'.ogg');
		}
		if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.ogv')) {
			$video->sources[] = array('type' => 'video/ogg', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'.ogv');
		}

		if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.jpg')) {
			$video->poster = $elxis_url_base.'/'.$info['dir'].$info['filename'].'.jpg';
		} else if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.jpeg')) {
			$video->poster = $elxis_url_base.'/'.$info['dir'].$info['filename'].'.jpeg';
		} else if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'.png')) {
			$video->poster = $elxis_url_base.'/'.$info['dir'].$info['filename'].'.png';
		}

		if ($cfg['subtitles'] == 1) {
			$langs = array(
				'ar' => 'Arabic', 'bg' => 'Bulgarian', 'cs' => 'Czech', 'da' => 'Danish', 'de' => 'German', 'el' => 'Hellenic',
				'en' => 'English',  'es' => 'Spanish', 'fa' => 'Farsi', 'fi' => 'Finnish', 'fr' => 'French', 'he' => 'Hebrew',
				'hr' => 'Croatian', 'hu' => 'Hungarian', 'it' => 'Italian', 'ja' => 'Japanese', 'lv' => 'Latvian', 'nl' => 'Dutch',
				'no' => 'Norwegian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'ro' => 'Romanian', 'rs' => 'Serbian', 'ru' => 'Russian',
				'sk' => 'Slovak', 'sl' => 'Slovenian', 'sv' => 'Swedish', 'th' => 'Thai', 'tr' => 'Turkish', 'uk' => 'Ukrainian', 'zh' => 'Chinese'
			);

			foreach ($langs as $iso => $label) {
				if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'_'.$iso.'.vtt')) {
					$video->tracks[] = array('kind' => 'subtitles', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'_'.$iso.'.vtt', 'srclang' => $iso, 'label' => $label, 'type' => 'text/vtt');
				} else if (file_exists(ELXIS_PATH.'/'.$info['dir'].$info['filename'].'_'.$iso.'.srt')) {
					$video->tracks[] = array('kind' => 'subtitles', 'src' => $elxis_url_base.'/'.$info['dir'].$info['filename'].'_'.$iso.'.srt', 'srclang' => $iso, 'label' => $label, 'type' => 'text/plain');
				}
			}
		}

		return $video;
	}


	/********************/
	/* LIST VIDEO FILES */
	/********************/
	private function listVideos() {
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$relpath = $this->videoRoot();
		$files = $eFiles->listFiles($relpath);
		if (!$files) {
			echo '<p>'.$eLang->get('NO_VIDEO_FILES')."</p>\n";
			return;
		}

		$mfiles = array();
		$exts = array('ogg', 'ogv', 'webm', 'mp4');
		foreach ($files as $file) {
			$ext = $eFiles->getExtension($file);
			if (($ext != '') && (in_array($ext, $exts))) { $mfiles[] = $file; }
		}
		unset($files);
		
		if (!$mfiles) {
			echo '<p>'.$eLang->get('NO_VIDEO_FILES')."</p>\n";
			return;
		}
		
		sort($mfiles);
		$k = 1;
		echo '<table class="plug_table" border="0">'."\n";
		foreach ($mfiles as $mfile) {
			if ($k == 1) { echo "<tr>\n"; }
			echo '<td class="plug_td200"><a href="javascript:void(null);" class="plug_link" title="'.$eLang->get('SELECT').'" onclick="addPluginCode(\'{video}'.$relpath.$mfile.'{/video}\')">'.$mfile."</a></td>\n";
			if ($k == 4) { echo "</tr>\n"; $k = 1; } else { $k++; }
		}

		if ($k > 1) {
			$rest = 5 - $k;
			echo str_repeat('<td>&#160;</td>', $rest);
			echo "</tr>\n";
		}
		echo "</table>\n";
	}


	/***************************/
	/* UPLOAD MEDIA FILES FORM */
	/***************************/
	private function uploadForm($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			echo '<div class="elx_warning">'.$eLang->get('REQ_ACCESS_UPLOAD')."</div>\n";
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			echo '<div class="elx_warning">Upload media files under the current security policy is not allowed!</div>'."\n";
			return;
		}

		$action = $elxis->makeAURL('content:plugin/?task=handler', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array('name' => 'plugform', 'action' => $action, 'idprefix' => 'plg', 'label_width' => 180, 'label_align' => 'left', 'label_top' => 0, 'tip_style' => 1);

		$form = new elxisForm($formOptions);
		$form->addNote($eLang->get('VIDEO_COMPOSER_INFO'), 'elx_info');
		$form->addFile('vfile1', $eLang->get('FILE'));
		$form->addFile('vfile2', $eLang->get('FILE'));
		$form->addFile('vfile3', $eLang->get('FILE'));
		$form->addButton('upload', $eLang->get('UPLOAD'), 'submit');
		$form->addHidden('task', 'handler');
		$form->addHidden('id', $pluginid);
		$form->addHidden('fn', $fn);
		$form->render();
		unset($form);
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function showHelp() {
?>
		<p>An HTML5 video can consist of several video files of different extensions and subtitles in 
		different languages. Poster images are also supported.</p>
		<p>The <strong>video files</strong> that belong to the same video should have the same name.<br />
		Example: <em>sample.ogv</em>, <em>sample.mp4</em>, <em>sample.webm</em><br /><br />
		<strong>Poster image</strong> (optional) should also have the same name as the video and an jpg, jpeg or png extension. Example: <em>sample.jpg</em><br /><br />
		You can optionally upload <strong>vtt or srt subtitles</strong>. The subtitles naming format is: {video_name}_{language}.vtt/srt<br />
		Examples: <em>sample_en.vtt</em>, <em>sample_el.vtt</em>, <em>sample_it.srt</em><br /><br />
		Web servers have an <strong>upload limit</strong> (usually 2mb to 8mb), so if you try to upload larger files from here the action will fail. 
		Upload <strong>large files</strong> via FTP. After uploading your files, pick just one of the videos files you uploaded to generate the plugin code. 
		The Video plugin will automatically load all related files during playback. 
		Don&apos;t forget to also set the plugin&apos;s general configuration <strong>parameters</strong>.</p>
		<p>If your server does not have <strong>MIME support</strong> for HTML5 videos then the videos wont play. In this case add in your site&apos;s htaccess file the following:<br />
		<em>AddType video/ogg .ogm<br />
		AddType video/ogg .ogv<br />
		AddType video/ogg .ogg<br />
		AddType video/webm .webm<br />
		AddType audio/webm .weba<br />
		AddType video/mp4 .mp4<br />
		AddType video/x-m4v .m4v</em></p>
<?php 
	}


	/**********************************/
	/* GET VIDEO UPLOAD RELATIVE PATH */
	/**********************************/
	private function videoRoot() {
		$relpath = 'media/videos/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/videos/'; }
		}
		return $relpath;
	}

}

?>