<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Helpers / Thumb
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisThumbHelper {

	private $default_width = 80;
	private $quality = 80;
	private $gdsupport = true;


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		if (!function_exists('imagecreatetruecolor')) { $this->gdsupport = false; }
	}


	/********************************/
	/* SET THUMBNAILS DEFAULT WIDTH */
	/********************************/
	public function setDefaultWidth($width) {
		$this->default_width = (int)$width;
	}


	/**************************/
	/* SET THUMBNAILS QUALITY */
	/**************************/
	public function setQuality($quality) {
		$this->quality = (int)$quality;
	}


	/********************************************************/
	/*   GENERATE, CACHE(OPTIONAL) AND SHOW THE THUMBNAIL   */
	/* If only make thumbnail use elxisFiles::resizeImage() */
	/********************************************************/
	public function make($image, $width=0, $height=0, $crop=true, $use_cache=true) {
		$eFiles = eFactory::getFiles();
		if (!$this->gdsupport) { $this->fatalError('GD Library Error: function imagecreatetruecolor does not exist'); }
		$image = ltrim($image, '/');
		$abspath_src = $eFiles->elxisPath($image, false);
		if (($image == '') || !is_file($abspath_src)) { $this->makeBlankImage($width, $height); }
		$imginfo = @getimagesize($abspath_src);
    	if (!$imginfo) { $this->makeBlankImage($width, $height); }
		if (!in_array($imginfo[2], array(1, 2, 3))) {
			$this->makeBlankImage($width, $height);
		}

		$lastmodified_src = filemtime($abspath_src);

		$width = (int)$width;
		$height = (int)$height;
		if (($width < 1) && ($height < 1)) {
			$width = $this->default_width;
			$height = $width;
		} else if ($width < 1) {
			$width = intval($height * ($imginfo[0] / $imginfo[1]));
			$crop = false;
		} else if ($height < 1) {
			$height = intval($width * ($imginfo[1] / $imginfo[0]));
			$crop = false;
		}

		$dst_x = 0;
		$dst_y = 0;
		$dst_w = $width;
		$dst_h = $height;

		if ($crop) {
			$original_ratio = $imginfo[0] / $imginfo[1];
			$thumb_ratio = $width / $height;
			if ($original_ratio > $thumb_ratio) {
				$crop = (($original_ratio - $thumb_ratio) < 0.1) ? false : true;
			} else {
				$crop = (($thumb_ratio - $original_ratio) < 0.1) ? false : true;
			}
		}

		if ($crop) {
			if ($imginfo[0] > $imginfo[1]) {
				$dst_w = $original_ratio * $height;
				$dst_x = -(($dst_w - $width)/ 2);
				$dst_x = (int)$dst_x;
			} elseif ($imginfo[0] <= $imginfo[1]) {
				$dst_h = $width / $original_ratio;
				$dst_y = -(($dst_h - $height)/ 2);
				$dst_y = (int)$dst_y;
			}
		}

		$dst_w = round($dst_w);
		$dst_h = round($dst_h);

		switch($imginfo[2]) {
			case 1:
				$image_ext = 'gif';
				$image_mime = 'image/gif';
			break;
			case 2:
				$image_ext = 'jpg';
				$image_mime = 'image/jpeg';
			break;
			case 3: default:
				$image_ext = 'png';
				$image_mime = 'image/png';
			break;
		}

		if ($use_cache) {
			$cache_image = md5($image.$lastmodified_src).'_'.$width.'x'.$height.'.'.$image_ext;
			$this->showCached($cache_image, $image_mime);
			$cache_image_abs = $eFiles->elxisPath('cache/thumbnails/'.$cache_image, true);
		}
 		if (($imginfo[2] == 2) && function_exists('imagecreatefromjpeg')) { //JPG
			$src_img = imagecreatefromjpeg($abspath_src);
			if (!$src_img) { $this->fatalError('Could not create JPEG thumbnail!'); }
			$dst_img = imagecreatetruecolor($width, $height);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			if (!$use_cache) {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				header("Content-Transfer-Encoding: Binary");
				header('Content-Disposition: inline; filename='.basename($image));
				header("Content-length: ".filesize($abspath_src));
				header('Cache-Control: no-cache');
				header('Pragma: no-cache');
				imagejpeg($dst_img, null, $this->quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				exit();
			}
			if (touch($cache_image_abs)) {
				chmod($cache_image_abs, 0666);
				imagejpeg($dst_img, $cache_image_abs, $this->quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				$this->showCached($cache_image, $image_mime);
			} else {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				imagejpeg($dst_img, null, $this->quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			exit();
		} else if (($imginfo[2] == 3) && function_exists('imagecreatefrompng')) { //PNG
			$src_img = imagecreatefrompng($abspath_src);
			if (!$src_img){ $this->fatalError('Could not create PNG thumbnail!'); }
			$dst_img = imagecreatetruecolor($width, $height);
			$quality = ($this->quality - 100) / 11.111111;
			$quality = round(abs($quality));
			imagealphablending($dst_img, true);
			imagesavealpha($dst_img, true);
			$trans_color = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
			imagefill($dst_img, 0, 0, $trans_color);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			if (!$use_cache) {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				header("Content-Transfer-Encoding: Binary");
				header('Content-Disposition: inline; filename='.basename($image));
				header("Content-length: ".filesize($abspath_src));
				header('Cache-Control: no-cache');
				header('Pragma: no-cache');
				imagepng($dst_img, null, $quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				exit();
			}
			if (touch($cache_image_abs)) {
				chmod($cache_image_abs, 0666);
				imagepng($dst_img, $cache_image_abs, $quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				$this->showCached($cache_image, $image_mime);
			} else {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				imagepng($dst_img, null, $quality);
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			exit();
		} else if (($imginfo[2] == 1) && function_exists('imagecreatefromgif')) { //GIF
			$src_img = imagecreatefromgif($abspath_src);
			if (!$src_img){ $this->fatalError('Could not create GIF thumbnail!'); }
			$dst_img = imagecreatetruecolor($width, $height);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			if (!$use_cache) {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				header("Content-Transfer-Encoding: Binary");
				header('Content-Disposition: inline; filename='.basename($image));
				header("Content-length: ".filesize($abspath_src));
				header('Cache-Control: no-cache');
				header('Pragma: no-cache');
				imagegif($dst_img, null);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				exit();
			}
			if (touch($cache_image_abs)) {
				chmod($cache_image_abs, 0666);
				imagegif($dst_img, $cache_image_abs);
				imagedestroy($src_img);
				imagedestroy($dst_img);
				$this->showCached($cache_image, $image_mime);
			} else {
				if (ob_get_length() > 0) { ob_end_clean(); }
				header('Content-type: '.$image_mime);
				imagegif($dst_img, null);
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			exit();
		} else {
			$this->fatalError('Could not create thumbnail!');
		}

		exit();
	}


	/****************************************************/
	/* GENERATE, AND SHOW THE THUMBNAIL WITHOUT CACHING */
	/****************************************************/
	public function show($image, $width=0, $height=0, $crop=true) {
		$this->make($image, $width, $height, $crop, false);
	}


	/*********************************/
	/* CREATE A BLANK IMAGE USING GD */
	/*********************************/
	private function makeBlankImage($width=1, $height=1, $text='') {
		if ($width < 1) { $width = 1; }
		if ($height < 1) { $height = 1; }
		$im = @imagecreate($width, $height) or die('Cannot Initialize new GD image stream');
		$bg_colour = imagecolorallocate($im, 255, 255, 255);
		if ($text != '') {
			$text_colour = imagecolorallocate($im, 0, 0, 0);
			imagestring($im, 2, 5, 5, $text, $text_colour);
		}
		
		if (ob_get_length() > 0) { ob_end_clean(); }
		header('Content-type: image/png');
		header('Content-Disposition: inline; filename=blank.png');
		header('Cache-Control: no-cache');
		header('Pragma: no-cache');
		imagepng($im);
		imagedestroy($im);
		exit();
	}


	/*******************************/
	/* SHOW CACHED IMAGE IF EXISTS */
	/*******************************/
	private function showCached($cache_image, $mime) {
		$eFiles = eFactory::getFiles();
		if ($eFiles->createFolder('cache/thumbnails/', 0777, true) === true) {
    		$cache_file = $eFiles->elxisPath('cache/thumbnails/'.$cache_image, true);
			if (file_exists($cache_file)) {
				$gmdate_mod = gmdate('D, d M Y H:i:s', filemtime($cache_file)).' GMT';
				if (isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])) {
					$if_modified_since = preg_replace("/;.*$/", "", $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
					if ($if_modified_since == $gmdate_mod) {
						header("HTTP/1.1 304 Not Modified");
						exit();
					}
				}

				if (ob_get_length() > 0) { ob_end_clean(); }
        		header('Content-Type: '.$mime);
        		header('Content-Disposition: inline; filename='.$cache_image);
        		header("Accept-Ranges: bytes");
        		header("Last-Modified: ".$gmdate_mod);
        		header("Content-Length: ".filesize($cache_file));
        		header("Cache-Control: max-age=86400, must-revalidate");
        		header("Expires: ".$gmdate_mod);
				readfile($cache_file);
				exit();
			}
		}
	}


	/********************/
	/* SHOW FATAL ERROR */
	/********************/
	private function fatalError($message='') {
		if (ob_get_length() > 0) { ob_end_clean(); }
		header('HTTP/1.1 400 Bad Request');
		die($message);
	}

}

?>