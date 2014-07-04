<?php 
/**
* @version		$Id: zip.helper.php 1259 2012-08-21 18:20:25Z datahell $
* @package		Elxis
* @subpackage	Helpers / Zip
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisZipHelper {

	private $zip_support = false;
	private $zip = null;
	private $errormsg = '';

		
	/***************/
    /* CONSTRUCTOR */
    /***************/
	public function __construct() {
		$this->zip_support = class_exists('ZipArchive', false) ? true : false;
	}


	/******************************************/
	/* GENERATE ERROR MESSAGE FROM ERROR CODE */
	/******************************************/
	private function errorMsgFromCode($res) {
		switch ($res) {
			case ZIPARCHIVE::ER_EXISTS: return 'Given archive already exists'; break;
			case ZIPARCHIVE::ER_INVAL: return 'Invalid argument'; break;
			case ZIPARCHIVE::ER_MEMORY: return 'Memory allocation failure'; break;
			case ZIPARCHIVE::ER_INCONS: return 'Zip archive inconsistent'; break;
			case ZIPARCHIVE::ER_NOENT: return 'No such file'; break;
			case ZIPARCHIVE::ER_NOZIP: return 'Not a zip archive'; break;
			case ZIPARCHIVE::ER_OPEN: return 'Could not open file'; break;
			case ZIPARCHIVE::ER_READ: return 'Read error'; break;
			case ZIPARCHIVE::ER_SEEK: return 'Seek error'; break;
			case ZIPARCHIVE::ER_CRC: return 'CRC error'; break;
			case ZIPARCHIVE::ER_WRITE: return 'Write error'; break;
			case ZIPARCHIVE::ER_MULTIDISK : return 'Multi-disk zip archives not supported'; break;
			case ZIPARCHIVE::ER_RENAME: return 'Renaming temporary file failed'; break;
			case ZIPARCHIVE::ER_CLOSE: return 'Closing zip archive failed'; break;
			case ZIPARCHIVE::ER_ZIPCLOSED: return 'Containing zip archive was closed'; break;
			case ZIPARCHIVE::ER_TMPOPEN: return 'Failure to create temporary file'; break;
			case ZIPARCHIVE::ER_ZLIB: return 'Zlib error'; break;
			case ZIPARCHIVE::ER_CHANGED: return 'Entry has been changed'; break;
			case ZIPARCHIVE::ER_COMPNOTSUPP: return 'Compression method not supported'; break;
			case ZIPARCHIVE::ER_EOF: return 'Premature EOF'; break;
			case ZIPARCHIVE::ER_INTERNAL: return 'Internal error'; break;
			case ZIPARCHIVE::ER_REMOVE: return 'Could not remove file'; break;
			case ZIPARCHIVE::ER_DELETED: return 'Entry has been deleted'; break;
			case ZIPARCHIVE::ER_OK: return ''; break; //No error
			default: return 'Unknown ZIP error'; break;
		}
	}


	/********************************/
	/* EXTRACT ARCHIVE TO DIRECTORY */
	/********************************/
	public function unzip($archive, $dir) {
		if (!$this->zip_support) {
			$this->errormsg = 'ZIP is not supported by your PHP installation!';
			return false;
		}
		if (!file_exists($archive)) {
			$this->errormsg = 'Given archive '.$archive.' does not exists!';
			return false;
		}
		$dir = rtrim(str_replace("\\", "/", $dir), '/').'/';
		if (!file_exists($dir)) {
			$ok = @mkdir($dir, 0755);
			if (!$ok) {
				$this->errormsg = 'Could not create extract directory container '.$dir;
				return false;
			}
		} else if (!is_dir($dir)) {
			$this->errormsg = 'Extract path is not a directory '.$dir;
			return false;
		}

		$archive = realpath($archive);
		$this->zip = new ZipArchive();
		$res = $this->zip->open($archive);
		if ($res !== true) {
			$this->errormsg = $this->errorMsgFromCode($res);
			return false;
		}
		$ok = $this->zip->extractTo($dir);
		$this->zip->close();
		$this->zip = null;
		if (!$ok) {
			$this->errormsg = 'Could not extract zip to '.$dir;
			return false;
		}
		return true;
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/*********************************************************/
	/* ZIP FILE(S) OR FOLDER(S) OR BUFFERED DATA AND SAVE IT */
	/*********************************************************/
	public function zip($archive, $source=null, $data=array()) {
		if (!$this->zip_support) {
			$this->errormsg = 'ZIP is not supported by your PHP installation!';
			return false;
		}

		$topack = array();
		if ($source !== null) {
			if (is_string($source) && (trim($source) != '')) {
				if (!file_exists($source)) {
					$this->errormsg = 'Given source '.$source.' does not exists!';
					return false;
				}
				$localname = basename($source);
				$type = (is_dir($source)) ? 'folder' : 'file';
				$topack[] = array('localname' => $localname, 'type' => $type, 'source' => $source);
			} else if (is_array($source) && (count($source) > 0)) {
				foreach ($source as $item) {
					if (!file_exists($item)) {
						$this->errormsg = 'Given source '.$item.' does not exists!';
						return false;
					}
					$localname = basename($item);
					if (is_dir($item)) {
						$item = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $item), '/').'/';
						$type = 'folder';
					} else {
						$type = 'file';						
					}
					$topack[] = array('localname' => $localname, 'type' => $type, 'source' => $item);
				}
			}
		}
		
		if (is_array($data) && (count($data) > 0)) {
			foreach ($data as $localname => $buffer) {
				$topack[] = array('localname' => $localname, 'type' => 'buffer', 'source' => $buffer);
			}
		}

		if (count($topack) == 0) {
			$this->errormsg = 'Nothing provided to zip!';
			return false;
		}

		$this->zip = new ZipArchive();
		$res = $this->zip->open($archive, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
		if ($res !== true) {
			$this->errormsg = $this->errorMsgFromCode($res);
			return false;
		}

		foreach ($topack as $pack) {
			if ($pack['type'] == 'file') {
				$ok = $this->zip->addFile(realpath($pack['source']), $pack['localname']);
				if (!$ok) {
					$this->zip->close();
					@unlink($archive);
					$this->errormsg = 'Could not add '.$pack['source'].' in zip archive!';
					return false;
				}
			} else if ($pack['type'] == 'folder') {
				$ok = $this->zip->addEmptyDir($pack['localname']);
				if (!$ok) {
					$this->zip->close();
					@unlink($archive);
					$this->errormsg = 'Could not create folder '.$pack['localname'].' in zip archive!';
					return false;
				}
				$ok = $this->addFolderRecursive($pack['source'], $pack['localname'].'/');
				if (!$ok) {
					$this->zip->close();
					@unlink($archive);
					$this->errormsg = 'Could not zip folder '.$pack['source'].'!';
					return false;
				}
			} else if ($pack['type'] == 'buffer') {
				$ok = $this->zip->addFromString($pack['localname'], $pack['source']);
				if (!$ok) {
					$this->zip->close();
					@unlink($archive);
					$this->errormsg = 'Could not create '.$pack['localname'].' in zip archive!';
					return false;
				}
			} else {
				//invalid pack type!
			}
		}

		$this->zip->close();
		$this->zip = null;
		return true;
	}


	/*********************************/
    /* RECURSIVELY ADD FOLDER TO ZIP */
    /*********************************/
    private function addFolderRecursive($dir, $path) {
    	$current_dir = @opendir($dir);
    	while ($entry = readdir($current_dir)) {
    		if (($entry != '.') && ($entry != '..')) {
    			if (is_dir($dir.$entry)) {
					if (!$this->zip->addEmptyDir($path.$entry.'/')) {
						return false;
					}
					$this->addFolderRecursive($dir.$entry.'/', $path.$entry.'/');
    			} else {
    				if (!$this->zip->addFile($dir.$entry, $path.$entry)) {
						return false;
					}
    			}
    		}
    	}
    	@closedir($current_dir);
    	return true;
    }

}

?>