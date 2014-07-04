<?php 
/**
* @version		$Id: connector.php 1045 2012-04-16 16:52:27Z datahell $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class connectorMediaControl extends emediaController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null) {
		parent::__construct($view);
	}


	/*****************************/
	/* INITIATE JS-PHP CONNECTOR */
	/*****************************/
	public function connect() {
		if (isset($_GET['mode'])) {
			$mode = trim(filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else if (isset($_POST['mode'])) {
			$mode = trim(filter_input(INPUT_POST, 'mode', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		} else {
			$mode = '';
		}

		if ($mode == '') {
			$msg = eFactory::getLang()->get('INVALID_REQUEST');
			$this->view->errorResponse($msg);
		}

		switch ($mode) {
			case 'getinfo': $this->getinfo(); break;
			case 'getfolder': $this->getfolder(); break;
        	case 'rename': $this->rename(); break;
        	case 'delete': $this->delete(); break;
			case 'addfolder': $this->addfolder(); break;
			case 'download': $this->download(); break;
			case 'preview': $this->preview(); break;
			case 'add': $this->uploadFile(); break;
			case 'resize': $this->resizeImage(); break;
			case 'compress': $this->compressFolder(); break;
      		default:
				$msg = eFactory::getLang()->get('INVALID_REQUEST');
				$this->view->errorResponse($msg);
			break;
		}
	}


	/*************************************/
	/* GET INFORMATION FOR A FILE/FOLDER */
	/*************************************/
	private function getinfo() {
		$path = $this->getPath();
		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$item = $this->get_file_info($path, false);
		$response = array(
			'path'=> $path,
			'filename' => $item['filename'],
			'filetype'=> $item['filetype'],
			'preview' => $item['preview'],
			'properties' => $item['properties'],
			'error' => '',
			'code' => 0
		);

		$this->view->jsonResponse($response);
	}


	/**********************************/
	/* GET RELATIVE PATH FROM REQUEST */
	/**********************************/
	private function getPath() {
		$path = rawurldecode(filter_input(INPUT_GET, 'path', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$path = ltrim($path, '/');
		$path = str_replace('..', '', $path);
		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) { return false; }
		return $path;
	}


	/******************************/
	/* GET INFORMATION FOR A FILE */
	/******************************/
	private function get_file_info($path, $folder_view=false) {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();

		$item = array();
		$item['properties'] = array(
			'date_created' => null,
			'date_modified' => null,
			'height' => 0,
			'width' => 0,
			'size' => 0
		);
		$item['capabilities'] = array('select', 'delete', 'rename');

		$tmp = explode('/', $path);
		$item['filename'] = $tmp[(count($tmp)-1)];

    	$tmp = explode('.', $item['filename']);
		$item['filetype'] = $tmp[(count($tmp)-1)];
    	$item['filemtime'] = filemtime(ELXIS_PATH.'/'.$this->relpath.$path);
    	$item['filectime'] = filectime(ELXIS_PATH.'/'.$this->relpath.$path);

		$item['preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/default.png';
		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$item['preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/folder_open.png';
			$item['capabilities'][] = 'compress';
		} else if (in_array(strtolower($item['filetype']), array('jpeg', 'jpg', 'gif', 'png'))) {
			$item['capabilities'][] = 'resize';
			$item['capabilities'][] = 'download';
			$item['properties']['size'] = filesize(ELXIS_PATH.'/'.$this->relpath.$path);
			if (($folder_view == true) && ($item['properties']['size'] > 30000)) {
				$item['preview'] = $elxis->makeAURL('emedia:connect/', 'inner.php').'?mode=preview&amp;path='.rawurlencode($path);
			} else {
				$item['preview'] = $elxis->secureBase().'/'.$this->relpath.$path;
			}
      		$info = getimagesize(ELXIS_PATH.'/'.$this->relpath.$path);
      		$item['properties']['width'] = (int)$info[0];
      		$item['properties']['height'] = (int)$info[1];
		} else if(file_exists(ELXIS_PATH.'/components/com_emedia/images/fileicons/'.strtolower($item['filetype']).'.png')) {
			$item['capabilities'][] = 'download';
			$item['preview'] = $elxis->secureBase().'/components/com_emedia/images/fileicons/'.strtolower($item['filetype']).'.png';
			$item['properties']['size'] = (int)filesize(ELXIS_PATH.'/'.$this->relpath.$path);
		} else {
			$item['capabilities'][] = 'download';
			$item['properties']['size'] = (int)filesize(ELXIS_PATH.'/'.$this->relpath.$path);
		}

		$item['properties']['date_modified'] = $eDate->formatTS($item['filemtime'], $eLang->get('DATE_FORMAT_4'));
		return $item;
	}


	/************************/
	/* LOAD FOLDER CONTENTS */
	/************************/
	public function getfolder() {
		$eFiles = eFactory::getFiles();
		$elxis = eFactory::getElxis();

		$response = array();
		$path = $this->getPath();

		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		$current_path = ELXIS_PATH.'/'.$this->relpath.$path;

		$loadfiles = true;
		if (isset($_GET['tree']) && ($_GET['tree'] == 1)) {
			if ($this->tree_show_files == 0) { $loadfiles = false; }
		}
		$filesDir = ($loadfiles) ? $eFiles->listFiles($this->relpath.$path) : array();
		$foldersDir = $eFiles->listFolders($this->relpath.$path);

		if ($foldersDir) {
			sort($foldersDir);
			foreach($foldersDir as $folder) {
				$relpath = $path.$folder.'/';
            	$response[$relpath] = array(
					'path' => $relpath,
					'filename' => $folder,
					'filetype' => 'dir',
					'preview' => $elxis->secureBase().'/components/com_emedia/images/fileicons/folder_open.png',
					'properties' => array(
						'date_created' => null,
						'date_modified' => null,
						'height' => 0,
						'width' => 0,
						'size' => 0
					),
					'capabilities' => array('select', 'delete', 'rename', 'compress'),
					'error' => '',
					'code' => 0
				);
			}
		}

		if ($filesDir) {
			sort($filesDir);
			foreach($filesDir as $file) {
				$relpath = $path.$file;
				$item = $this->get_file_info($relpath, true);
				$response[$relpath] = array(
					'path' => $relpath,
					'filename' => $item['filename'],
					'filetype' => $item['filetype'],
					'preview' => $item['preview'],
					'properties' => $item['properties'],
					'capabilities' => $item['capabilities'],
					'error' => '',
					'code' => 0
				);
			}
		}

		$this->view->jsonResponse($response);
	}


	/************************/
	/* RENAME FILE / FOLDER */
	/************************/
	private function rename() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$old = rawurldecode(filter_input(INPUT_GET, 'old', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$new = rawurldecode(filter_input(INPUT_GET, 'new', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$old = str_replace('..', '', $old);
		$old = str_replace('//', '/', $old);
		$new = str_replace('..', '', $new);
		$new = preg_replace('/[^a-zA-Z0-9\_\-\.]/', '', $new);
		if (($old == '') || ($old == '/')) {
			$msg = eFactory::getLang()->get('NO_FILE_SELECTED');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$old)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$suffix = '';
		$is_folder = false;
		if (substr($old, -1, 1) == '/') { //folder
			$old = substr($old, 0, (strlen($old)-1));
			$suffix = '/';
			$is_folder = true;
		} else {
			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($old));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
				$this->view->errorResponse($msg);
			}
			unset($exts);
		}

    	$tmp = explode('/', $old);
		$filename = $tmp[(count($tmp)-1)];
		$path = str_replace('/'.$filename, '', $old);

		if ($is_folder) {
			if (($new == '') || ($new != $_GET['new'])) {
				$msg = eFactory::getLang()->get('INVALID_FOLDER_NAME');
				$this->view->errorResponse($msg);
			}

			if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path.'/'.$new.'/')) {
				$msg = eFactory::getLang()->get('FOLDER_NAME_EXISTS');
				$this->view->errorResponse($msg);
			}			
		} else {
			if (($new == '') || ($new != $_GET['new'])) {
				$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
				$this->view->errorResponse($msg);
			}

			if (file_exists(ELXIS_PATH.'/'.$this->relpath.$path.'/'.$new)) {
				$msg = eFactory::getLang()->get('FILE_NAME_EXISTS');
				$this->view->errorResponse($msg);
			}

			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($new));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
				$this->view->errorResponse($msg);
			}
			unset($exts);
		}

		if($is_folder) {
			$ok = $eFiles->moveFolder($this->relpath.$old.'/', $this->relpath.$path.'/'.$new.'/');
		} else {
			$ok = $eFiles->move($this->relpath.$old, $this->relpath.$path.'/'.$new);
		}

		if (!$ok) {
			$msg = eFactory::getLang()->get('RENAME_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('error' => '', 'code' => 0, 'old_path' => $old, 'old_name' => $filename, 'new_path' => urlencode($path.'/'.$new.$suffix), 'new_name' => $new);
		$this->view->jsonResponse($response);
	}


	/************************/
	/* DELETE FILE / FOLDER */
	/************************/
	private function delete() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if (($path === false) || ($path == '') || ($path == '/')) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$ok = $eFiles->deleteFolder($this->relpath.$path);
		} else {
			$exts = $this->allowedExtensions();
			$ext = strtolower($eFiles->getExtension($path));
			if (($ext == '') || !in_array($ext, $exts)) {
				$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
				$this->view->errorResponse($msg);
			}
			$ok = $eFiles->deleteFile($this->relpath.$path);
		}

		if (!$ok) {
			$msg = eFactory::getLang()->get('DELETE_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('error' => '', 'code' => 0, 'path' => $path);
		$this->view->jsonResponse($response);
	}


	/***********************/
	/* CREATE A NEW FOLDER */
	/***********************/
	private function addfolder() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if ($path === false) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		if ($path == '') {
			//do nothing
		} else if ($path == '/') {
			$path = '';
		} else if (!preg_match('#\/$#', $path)) {
			$path .= '/';
		}

		$name = rawurldecode(filter_input(INPUT_GET, 'name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$name = str_replace('..', '', $name);
		$name = trim($name, '/');
		$name = preg_replace('/[^a-zA-Z0-9\_\-]/', '', $name);
		if (($name == '') || ($name != $_GET['name']) || (strlen($name) < 3)) {
			$msg = eFactory::getLang()->get('INVALID_FOLDER_NAME');
			$this->view->errorResponse($msg);
		}

		if (is_dir(ELXIS_PATH.'/'.$this->relpath.$path.$name.'/')) {
			$msg = eFactory::getLang()->get('FOLDER_NAME_EXISTS');
			$this->view->errorResponse($msg);
		}

		$ok = $eFiles->createFolder($this->relpath.$path.$name.'/');
		if (!$ok) {
			$msg = sprintf(eFactory::getLang()->get('CNOT_CREATE_FOLDER'), $name);
			$this->view->errorResponse($msg);
		}

		$response = array('parent' => $path, 'name' => $name, 'error' => '', 'code' => 0);
		$this->view->jsonResponse($response);
	}


	/*****************/
	/* DOWNLOAD FILE */
	/*****************/
	private function download() {
		$path = $this->getPath();
		if ($path === false) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path) || !is_file(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$this->pageHeaders('application/force-download');
		header('Content-Disposition: inline; filename="'.basename($path).'"');
		header("Content-Transfer-Encoding: Binary");
		header("Content-length: ".filesize(ELXIS_PATH.'/'.$this->relpath.$path));
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($path).'"');
		readfile(ELXIS_PATH.'/'.$this->relpath.$path);
		exit();
	}


	/*****************/
	/* PREVIEW IMAGE */
	/*****************/
	private function preview() {
		$path = $this->getPath();
		$final = $this->relpath.$path;
		if (($path == '') || ($path === false)) {
			$final = 'components/com_emedia/images/image_not_found.png';
		} else {
			if (!file_exists(ELXIS_PATH.'/'.$this->relpath.$path) || !is_file(ELXIS_PATH.'/'.$this->relpath.$path)) {
				$final = 'components/com_emedia/images/image_not_found.png';
			} else {
				$ext = strtolower(pathinfo(ELXIS_PATH.'/'.$this->relpath.$path, PATHINFO_EXTENSION));
				if (($ext == '') || !in_array($ext, array('png', 'gif', 'jpeg', 'jpg'))) {
					$final = 'components/com_emedia/images/preview_not_available.png';
				}				
			}
		}

		$thumb = eFactory::getElxis()->obj('thumb');
		$thumb->show($final, 100, 100, true);
	}


	/*********************/
	/* UPLOAD A NEW FILE */
	/*********************/
	private function uploadFile() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'upload') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		if (!isset($_FILES['newfile']) || !is_uploaded_file($_FILES['newfile']['tmp_name'])) {
			$msg = eFactory::getLang()->get('NO_FILE_UPLOADED');
			$this->view->errorResponse($msg);	
		}

		if ($_FILES['newfile']['size'] > $this->max_upload_size) {
			$max = round(($this->max_upload_size / 1048576), 1);
			$msg = sprintf(eFactory::getLang()->get('MAX_ALLOWED_FSIZE'), $max.' mb');
			$this->view->errorResponse($msg);
		}

		$exts = $this->allowedExtensions();
		$ext = strtolower($eFiles->getExtension($_FILES['newfile']['name']));
		if (($ext == '') || !in_array($ext, $exts)) {
			$msg = eFactory::getLang()->get('FORBIDDEN_FILE_TYPE');
			$this->view->errorResponse($msg);
		}
		unset($exts);

		$currentpath = filter_input(INPUT_POST, 'currentpath', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		$currentpath = ltrim($currentpath, '/');
		$currentpath = str_replace('..', '', $currentpath);
		if ($currentpath == '') {
			//do nothing
		} else if ($currentpath == '/') {
			$currentpath = '';
		} else if (!preg_match('#\/$#', $currentpath)) {
			$currentpath .= '/';
		}

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath.$currentpath)) {
			$msg = eFactory::getLang()->get('PATH_NOT_EXIST');
			$this->view->errorResponse($msg);
		}

		$filename = preg_replace('/[^a-zA-Z0-9\_\-\.\(\)]/', '', $_FILES['newfile']['name']);

		$info = $eFiles->getNameExtension($filename);
		if ($info['extension'] == '') {
			$msg = eFactory::getLang()->get('INVALID_FILE_NAME');
			$this->view->errorResponse($msg);
		}

		if ($info['name'] == '') { $filename = 'file'.rand(1000, 9999).'.'.$info['extension']; }

		if (file_exists(ELXIS_PATH.'/'.$this->relpath.$currentpath.$filename)) {
			$msg = eFactory::getLang()->get('FILE_NAME_EXISTS').' ('.$filename.')';
			$this->view->errorResponse($msg);
		}

		$ok = $eFiles->upload($_FILES['newfile']['tmp_name'], $this->relpath.$currentpath.$filename);
		if (!$ok) {
			$msg = eFactory::getLang()->get('FILE_UPLOAD_FAILED');
			$this->view->errorResponse($msg);
		}

		$response = array('path' => $currentpath, 'name' => $filename, 'error' => '', 'code' => 0);
		$this->view->jsonResponse($response);
	}


	/*******************/
	/* RESIZE AN IMAGE */
	/*******************/
	private function resizeImage() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if (($path == false) || !file_exists(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$msg = eFactory::getLang()->get('FILE_NOT_FOUND');
			$this->view->errorResponse($msg);
		}

		$ext = strtolower($eFiles->getExtension($path));
		if (($ext == '') || !in_array($ext, array('png', 'jpeg', 'jpg', 'gif'))) {
			$msg = eFactory::getLang()->get('ONLY_RESIZE_IMAGES');
			$this->view->errorResponse($msg);
		}

		$rwidth = isset($_GET['rwidth']) ? (int)$_GET['rwidth'] : 0;
		if ($rwidth < 1) {
			$msg = eFactory::getLang()->get('WIDTH_INVALID');
			$this->view->errorResponse($msg);
		}
		if ($rwidth < 10) {
			$msg = eFactory::getLang()->get('WIDTH_TOO_SMALL');
			$this->view->errorResponse($msg);
		}

		$imginfo = getimagesize(ELXIS_PATH.'/'.$this->relpath.$path);
    	if (!$imginfo) {
			$this->view->errorResponse('Could not determine original image size!');
    	}
		if (!in_array($imginfo[2], array(1, 2, 3))) {
			$msg = eFactory::getLang()->get('ONLY_RESIZE_IMAGES');
			$this->view->errorResponse($msg);
		}

		if ($rwidth == $imginfo[0]) {
			$msg = eFactory::getLang()->get('IMAGE_ALREADY_DIMS');
			$this->view->errorResponse($msg);
		}

		$rheight = intval(($imginfo[1] * $rwidth) / $imginfo[0]);
		$ok = $eFiles->resizeImage($this->relpath.$path, $rwidth, $rheight, false);

		if (!$ok) {
			$msg = eFactory::getLang()->get('RESIZE_FAILED');
			$this->view->errorResponse($msg);
		}

		if (strpos($path, '/') !== false) { $dirname = dirname($path); } else { $dirname = ''; }
		$response = array('error' => '', 'code' => 0, 'new_width' => $rwidth, 'new_height' => $rheight, 'dirname' => $dirname);
		$this->view->jsonResponse($response);
	}


	/*************************/
	/* ZIP COMPRESS A FOLDER */
	/*************************/
	private function compressFolder() {
		$elxis = eFactory::getElxis();

		if ($elxis->acl()->check('com_emedia', 'files', 'edit') < 1) {
			$msg = eFactory::getLang()->get('NOTALLOWACTION');
			$this->view->errorResponse($msg);
		}

		$path = $this->getPath();
		if (($path === false) || ($path == '') || ($path == '/')) {
			$msg = eFactory::getLang()->get('SELECT_FOLDER_COMPRESS');
			$this->view->errorResponse($msg);
		}

		if (!is_dir(ELXIS_PATH.'/'.$this->relpath.$path)) {
			$this->view->errorResponse('Yopu can compress only folders!');
		}

		$parts = preg_split('#\/#', $path, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts || (count($parts) == 0)) {
			$msg = eFactory::getLang()->get('SELECT_FOLDER_COMPRESS');
			$this->view->errorResponse($msg);
		}

		$n = count($parts);
		$lastseg = $n - 1;
		$last = $parts[$lastseg];

		$upload_dir = '';
		if ($n > 1) {
			for ($i=0; $i < $n; $i++) {
				if ($i == $lastseg) { break; }
				$upload_dir .= $parts[$i].'/';
			}
		}

		$archive_name = $last.'.zip';
		if (file_exists(ELXIS_PATH.'/'.$this->relpath.$upload_dir.$archive_name)) {
			$archive_name = $last.'_'.rand(1000, 9999).'.zip';
		}

		$archive = ELXIS_PATH.'/'.$this->relpath.$upload_dir.$archive_name;
		$source = ELXIS_PATH.'/'.$this->relpath.$path;

		$zip = $elxis->obj('zip');
		$ok = $zip->zip($archive, $source);
		if (!$ok) {
			$msg = $zip->getError();
			if ($msg == '') { $msg = 'Compressing folder '.$last.' failed!'; }
		}
		unset($zip);

		$response = array('error' => '', 'code' => 0, 'path' => $upload_dir, 'archive' => $archive_name);
		$this->view->jsonResponse($response);
	}

}

?>