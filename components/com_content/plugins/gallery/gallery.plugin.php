<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class galleryPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
    	$regex = "#{gallery\s*(.*?)}(.*?){/gallery}#s";
    	if (!$published) {
    		$row->text = preg_replace($regex, '', $row->text);
    		return true;
    	}
		$matches = array();
		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if (!$matches) { return true; }

		$cfg = array();
		$cfg['width'] = (int)$params->get('width', 80);
		if ($cfg['width'] < 10 || $cfg['width'] > 151) { $cfg['width'] = 80; }
		$cfg['height'] = (int)$params->get('height', 80);
		if ($cfg['height'] < 10 || $cfg['height'] > 151) { $cfg['height'] = 80; }
		$cfg['autoplay'] = (int)$params->get('autoplay', 1);
		$cfg['autoplay'] = ($cfg['autoplay'] == 1) ? 'true' : 'false';
		$cfg['loop'] = (int)$params->get('loop', 0);
		$cfg['loop'] = ($cfg['loop'] == 1) ? 'true' : 'false';
		$cfg['speed'] = (int)$params->get('speed', 4000);
		if ($cfg['speed'] < 3000) { $cfg['speed'] = 4000; }
		$cfg['ordering'] = (int)$params->get('ordering', 0);

		foreach ($matches[0] as $i => $match) {
			$fpath = $matches[2][$i];
			if (($fpath == '') || !file_exists(ELXIS_PATH.'/'.$fpath) || !is_dir(ELXIS_PATH.'/'.$fpath)) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}
			if (!preg_match('#(\/)$#', $fpath)) { $fpath .= '/'; }
			$images = $this->getImages($fpath, $cfg['ordering']);
			if ($images === false) {
			    $row->text = str_replace($match, '', $row->text);
				continue;
			}

			$this->importCSSJS($row->id.$i, $cfg);

			$width = $cfg['width'];
			$height = $cfg['height'];
			$attributes = eFactory::getPlugin()->parseAttributes($matches[1][$i]);
			if (is_array($attributes) && isset($attributes['width']) && isset($attributes['height'])) {
				$w = (int)$attributes['width'];
				$h = (int)$attributes['height'];
				if (($w > 9) && ($h > 9)) { $width = $w; $height = $h; }
			}
			$html = $this->makeHTML($fpath, $images, $row->id.$i, $width, $height);
			$row->text = preg_replace("#".$match."#", $html, $row->text);
		}

		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '{gallery width="80" height="80"}relative/path/to/images/folder/{/gallery}';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		return array($eLang->get('SELECT_FOLDER') , $eLang->get('UPLOAD_IMAGES'), $eLang->get('HELP'));
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$response = array(
            'css' => array(),
			'js' => array(eFactory::getElxis()->secureBase().'/components/com_content/plugins/gallery/includes/gallery.js')
		);
		return $response;	
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->pickFolder($pluginid, $fn); break;
			case 2: $this->uploadForm($pluginid, $fn); break;
			case 3: $this->showHelp(); break;
			default: break;
		}
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$act = (isset($_POST['act'])) ? $_POST['act'] : '';
		switch ($act) {
			case 'list': $this->listImages($pluginid, $fn); break;
			case 'upload': $this->uploadImages($pluginid, $fn); break;
			default: break;
		}

		die('Invalid request');
	}


	/*********************************/
	/* HELPER : SELECT IMAGES FOLDER */
	/*********************************/
	private function pickFolder($pluginid, $fn) {
		$eLang = eFactory::getLang();
        $eFiles = eFactory::getFiles();
        $elxis = eFactory::getElxis();

        $relpath = $this->imagesRoot();
        $options = array();
		$folders = $eFiles->listFolders($relpath, false);
		if ($folders) {
			foreach ($folders as $folder) {
				$options[] = array($folder.'/', $folder.'/');
				$subfolders = $eFiles->listFolders($relpath.$folder.'/', false);
				if ($subfolders) {
					foreach ($subfolders as $subfolder) {
						$options[] = array($folder.'/'.$subfolder.'/', $folder.'/'.$subfolder.'/');
					}
				}
			}
		}

        echo '<div style="margin:0 0 10px 0;">'."\n";
        echo '<select name="egalleryctg" id="egalleryctg" class="selectbox" dir="ltr" onchange="egalFolderImages('.$pluginid.', '.$fn.')">'."\n";
        echo '<option value="" selected="selected">- '.$eLang->get('NONE')." -</option>\n";
        if ($options) {
        	foreach ($options as $option) {
				echo '<option value="'.$option['0'].'">'.$option[1]."</option>\n";
       		}
        }
        echo "</select> \n";
        echo '&#160; <a href="javascript:void(null);" onclick="egaltoFolder()" title="'.$eLang->get('INSERT_LINK_FOL').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/gallery/includes/link.png" alt="'.$eLang->get('INSERT_LINK_FOL').'" border="0" /></a>'."\n";
        echo "</div>\n";
        echo '<div id="relpath" style="display:none;">'.$relpath."</div>\n";
        echo '<div id="egalimages"></div>'."\n";
	}


	/*******************************/
	/* HELPER : UPLOAD IMAGES FORM */
	/*******************************/
	private function uploadForm($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
        $eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			echo '<div class="elx_warning">'.$eLang->get('REQ_ACCESS_UPLOAD')."</div>\n";
			return;
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 1) {
			echo '<div class="elx_warning">Upload media files under the current security policy is not allowed!</div>'."\n";
			return;
		}

        $relpath = $this->imagesRoot();
        $foptions = array();
		$folders = $eFiles->listFolders($relpath, false);
		if ($folders) {
			foreach ($folders as $folder) {
				$foptions[] = $folder.'/';
				$subfolders = $eFiles->listFolders($relpath.$folder.'/', false);
				if ($subfolders) {
					foreach ($subfolders as $subfolder) { $foptions[] = $folder.'/'.$subfolder.'/'; }
				}
			}
		}

		$action = $elxis->makeAURL('content:plugin/?task=handler', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array('name' => 'pluggalform', 'action' => $action, 'idprefix' => 'gal', 'label_width' => 160, 'label_align' => 'left', 'label_top' => 0, 'tip_style' => 2, 'jsonsubmit' => 'plugGallerySubmit()');
		$form = new elxisForm($formOptions);
		$form->addNote($eLang->get('UPLOAD_CREATE_TP'), 'elx_info');
		$form->openRow();
		$options = array();
		$options[] = $form->makeOption('', '- '.$eLang->get('ROOT_FOLDER').' -');
		if ($foptions) {
			foreach ($foptions as $foption) { $options[] = $form->makeOption($foption, $foption); }
		}
		$form->addSelect('folder', $eLang->get('FOLDER'), '', $options, array('dir' => 'ltr'));
		$form->addText('newfolder', '', $eLang->get('NEW_SUBFOLDER'), array('forcedir' => 'ltr', 'size' => 15, 'maxlength' => 40));
		$form->closeRow();
		$form->addFile('ifile1', $eLang->get('IMAGE'));
		$form->addFile('ifile2', $eLang->get('IMAGE'));
		$form->addFile('ifile3', $eLang->get('IMAGE'));
		$form->addButton('upload', $eLang->get('UPLOAD'), 'submit');
		$notice = $eLang->get('GALLERY_LIMIT_1')."<br />\n".$eLang->get('GALLERY_LIMIT_2')."<br />\n".$eLang->get('GALLERY_LIMIT_3');
		$form->addNote($notice, 'elx_warning');
		$form->addHidden('task', 'handler');
		$form->addHidden('act', 'upload');
		$form->addHidden('id', $pluginid);
		$form->addHidden('fn', $fn);
		$form->render();
		unset($form);
	}


	/**************************************/
	/* HANDLER: LIST FOLDER IMAGES (AJAX) */
	/**************************************/
	private function listImages($pluginid, $fn) {
		$eLang = eFactory::getLang();

		$fpath = '';
		if (isset($_POST['fpath'])) {
			$fpath = trim(preg_replace('#[^a-z0-9\-\_\(\)\/]#i', '', $_POST['fpath']));
			if (($fpath == '') || ($fpath != $_POST['fpath'])) {
				$this->ajaxHeaders('text/html');
				echo '<div class="elx_warning">Requested path is invalid!'."</div>\n";
				exit();
			}
		}

		$relpath = $this->imagesRoot();
		if (($fpath == '') || !is_dir(ELXIS_PATH.'/'.$relpath.$fpath)) {
			$this->ajaxHeaders('text/html');
			echo '<div class="elx_warning">Requested path not found!'."</div>\n";
			exit();
		}

		$images = eFactory::getFiles()->listFiles($relpath.$fpath, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
		$this->ajaxHeaders('text/html');
		if (!$images) {
			echo '<div class="elx_warning">'.$eLang->get('NO_IMAGES')."</div>\n";
			exit();
		}

		$total = count($images);
		$txt = sprintf($eLang->get('FOLDER_CONTAIN_IMAGES'), '<strong>'.$total.'</strong>');
		if ($total > 30) { $txt .= sprintf($eLang->get('ONLY_SHOWN'), '<strong>30</strong>'); }
		$baseURL = eFactory::getElxis()->secureBase().'/'.$relpath.$fpath;

		echo '<div class="elx_sminfo">'.$txt."</div>\n";
		$i = 1;
		foreach ($images as $image) {
			echo '<img src="'.$baseURL.$image.'" alt="'.$image.'" border="0" title="'.$image.'" width="80" height="80" style="border:1px solid #ccc; padding:3px;" /> &#160; ';
			if ($i > 29) { break; }
			$i++;
		}
		exit();
	}


	/*********************************************/
	/* HANDLER: UPLOAD IMAGES AND CREATE FOLDERS */
	/*********************************************/
	private function uploadImages($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();
		$eFiles = eFactory::getFiles();

		$redirurl = $elxis->makeAURL('content:plugin/', 'inner.php').'?id='.$pluginid.'&fn='.$fn;
		$sess_token = trim($eSession->get('token_pluggalform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			echo '<div class="elx_error">'.$eLang->get('REQDROPPEDSEC')."</div>\n";
			return;
		}

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) { $elxis->redirect($redirurl); }
		if ($elxis->getConfig('SECURITY_LEVEL') > 1) { $elxis->redirect($redirurl); }

		$relpath = $this->imagesRoot();
		$folder = '';
		$newfolder = '';
		$uploadpath = '';
		if (isset($_POST['folder'])) {
			$folder = trim(preg_replace('#[^a-z0-9\_\-\/]#i', '', $_POST['folder']));
			if ($folder != $_POST['folder']) { $elxis->redirect($redirurl); }
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder)) { $elxis->redirect($redirurl); }
		}

		if (isset($_POST['newfolder'])) {
			$newfolder = trim(preg_replace('#[^a-z0-9\_\-]#i', '', $_POST['newfolder']));
			if ($newfolder != $_POST['newfolder']) { $elxis->redirect($redirurl); }
			$newfolder = strtolower($newfolder);
		}

		if ($folder == '') {
			if ($newfolder == '') { $elxis->redirect($redirurl); }
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$newfolder.'/')) {
				$ok = $eFiles->createFolder($relpath.$newfolder.'/');
				if (!$ok) { $elxis->redirect($redirurl); }
			}
			$uploadpath = $relpath.$newfolder.'/';
		} else {
			if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder)) { $elxis->redirect($redirurl); }
			$level = substr_count($folder, '/');
			if ($newfolder != '') {
				if ($level >= 2) { $elxis->redirect($redirurl); }
				if (!is_dir(ELXIS_PATH.'/'.$relpath.$folder.$newfolder.'/')) {
					$ok = $eFiles->createFolder($relpath.$folder.$newfolder.'/');
					if (!$ok) { $elxis->redirect($redirurl); }
				}
				$uploadpath = $relpath.$folder.$newfolder.'/';
			} else {
				$uploadpath = $relpath.$folder;
			}
		}

		if (!isset($_FILES) || (count($_FILES) == 0)) { $elxis->redirect($redirurl); }
		$valid_exts = array('jpg', 'jpeg', 'png', 'gif');
		for ($i=1; $i < 4; $i++) {
			if (!isset($_FILES['ifile'.$i])) { continue; }
			$upf = $_FILES['ifile'.$i];
		 	if (($upf['name'] != '') && ($upf['error'] == 0) && ($upf['size'] > 0)) {
		 		$filename = strtolower(preg_replace('#[^a-zA-Z0-9\_\-\.]#', '', $upf['name']));
		 		$info = $eFiles->getNameExtension($filename);
		 		if (($info['extension'] == '') || !in_array($info['extension'], $valid_exts)) { continue; }
		 		if ($info['name'] == '') { $filename = 'image_'.rand(1000,9999).'.'.$info['extension']; }
		 		if (file_exists(ELXIS_PATH.'/'.$uploadpath.$filename)) { continue; }
		 		$eFiles->upload($upf['tmp_name'], $uploadpath.$filename);
	 		}
		}

		$elxis->redirect($redirurl);
	}


	/******************************/
	/* IMPORT REQUIRED CSS AND JS */
	/******************************/
	private function importCSSJS($id, $cfg) {
		$eDoc = eFactory::getDocument();

		$eDoc->addStyleLink(eFactory::getElxis()->secureBase().'/components/com_content/plugins/gallery/includes/gallery.css'); 
		$eDoc->loadLightbox();      

		$js = '$(document).ready(function() { '."\n";
		$js .= "\t\t".'$(".plug_gallery'.$id.'").colorbox({rel:\'plug_gallery'.$id.'\', slideshow:true, slideshowAuto:'.$cfg['autoplay'].', slideshowSpeed:'.$cfg['speed'].', loop:'.$cfg['loop'].'});'."\n";
		$js .= "\t\t".'});';
		$eDoc->addScript($js);
	}


	/********************************/
	/* MAKE IMAGE GALLERY HTML CODE */
	/********************************/
	private function makeHTML($fpath, $images, $id, $width, $height) {
		$elxis_url_base = eFactory::getElxis()->secureBase();

		$html = '<div class="plug_gallery_box">'."\n";
		foreach($images as $image) {
			$html .= '<a class="plug_gallery'.$id.'"  href="'.$elxis_url_base.'/'.$fpath.$image.'">';
			$html .= '<img src="'.$elxis_url_base.'/'.$fpath.$image.'" alt="'.$image.'" width="'.$width.'" height="'.$height.'" />';
			$html .= "</a>\n";
		}
		$html .= '<div style="clear:both;"></div>'."\n";
		$html .= "</div>\n";
		return $html;
	}


	/*********************/
	/* GET FOLDER IMAGES */
	/*********************/
	private function getImages($fpath, $ordering) {
		if (!is_dir(ELXIS_PATH.'/'.$fpath)) { return false; }
		if (strpos($fpath, 'media/images/') !== 0) { return false; }
		$images = eFactory::getFiles()->listFiles($fpath, '(.gif)|(.jpeg)|(.jpg)|(.png)$');
		if (!$images) { return false; }

		if ($ordering == 1) {
			usort($images, array('galleryPlugin', 'orderByName'));
			return $images;
		} else if (($ordering == 2) || ($ordering == 3)) {
			$temp = array();
			foreach ($images as $image) {
				$ts = filemtime(ELXIS_PATH.'/'.$fpath.$image);
				$temp[] = array('image' => $image, 'ts' => $ts);
			}
			$method = ($ordering == 2) ? 'orderNewer' : 'orderOlder';
			usort($temp, array('galleryPlugin', $method));
			$final = array();
			foreach ($temp as $tmp) { $final[] = $tmp['image']; }
			return $final;
		} else {
			return $images;
		}
	}


	/**********************************/
	/* ORDER IMAGES BY THEIR FILENAME */
	/**********************************/
	public static function orderByName($a, $b) {
		return strcmp($a, $b);
	}


	/**********************/
	/* NEWER IMAGES FIRST */
	/**********************/
	public static function orderNewer($a, $b) {
		if ($a['ts'] == $b['ts']) { return 0; }
		return ($a['ts'] < $b['ts']) ? 1 : -1;
	}


	/**********************/
	/* OLDER IMAGES FIRST */
	/**********************/
	public static function orderOlder($a, $b) {
		if ($a['ts'] == $b['ts']) { return 0; }
		return ($a['ts'] < $b['ts']) ? -1 : 1;
	}


	/***************/
	/* PLUGIN HELP */
	/***************/
	private function showHelp() {
?>
		<p>The <strong>Gallery</strong> plugin allows you to easily display image galleries inside articles. There are several settings on the Gallery plugin&apos;s 
		edit page. It is also possible to display multiple image galleries on a single article. Images should be located in a sub-folder of the <em>media/images/</em> path.</p>
		<h3>Usage instructions</h3>
		<p>Select the folder that contains your images. Click on the icon link next to the select box to create the ready to use plugin code. That&apos;s it!</p>
        <p>Since Elxis CMS 4.1 version you can set the dimensions of each  thumbnail per gallery set. The attributes of width and height will bypass the relative 
        parameters on the Gallery plugin&apos;s edit page.</p>
		<h3>Upload images</h3>
		<p>Users having upload permissions on component eMedia can upload images and create folders.</p>  
<?php 
	}


	/**********************************/
	/* GET IMAGE UPLOAD RELATIVE PATH */
	/**********************************/
	private function imagesRoot() {
		$relpath = 'media/images/';
		if (defined('ELXIS_MULTISITE')) {
			if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/'; }
		}
		return $relpath;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	private function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}

}

?>