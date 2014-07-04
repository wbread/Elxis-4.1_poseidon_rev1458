<?php 
/**
* @version		$Id: files.class.php 1303 2012-09-28 18:56:44Z datahell $
* @package		Elxis
* @subpackage	File Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


elxisLoader::loadFile('includes/libraries/elxis/ftp.class.php');


class elxisFiles {

	private $repo_path = '';
	private $ftp = null;
	private $ftp_status = 'disconnected'; //disabled, disconnected, connected, error
	private $ftp_root = '';
	private $ftp_root_repo = '';
	private $errormsg = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
	public function __construct() {
		$elxis = eFactory::getElxis();
		$this->ftp_root = rtrim($elxis->getConfig('FTP_ROOT'), '/');
		$this->repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($this->repo_path == '') {
			$this->repo_path = ELXIS_PATH.'/repository';
			$this->ftp_root_repo = $this->ftp_root.'/repository';
		} else {
			$site_root = preg_replace('#('.$this->ftp_root.')$#', '', ELXIS_PATH);
			$this->ftp_root_repo = preg_replace('#^('.$site_root.')#', '', $this->repo_path);
		}
		if (!file_exists($this->repo_path.'/tmp/') || !is_dir($this->repo_path.'/tmp/')) {
			trigger_error('Temporary directory does not exist! '.$this->repo_path.'/tmp/', E_USER_ERROR);
		}
	}


	/*************************************************/
	/* CONNECT OR CHECK THE CONNECTION STATUS OF FTP */
	/*************************************************/
	private function useFTP() {
		if ($this->ftp_status == 'disconnected') {
			$elxis = eFactory::getElxis();
			if ($elxis->getConfig('FTP') == 1) {
				$params = array(
					'ftp_host' => $elxis->getConfig('FTP_HOST'),
					'ftp_port' => (int)$elxis->getConfig('FTP_PORT'),
					'ftp_user' => $elxis->getConfig('FTP_USER'),
					'ftp_pass' => $elxis->getConfig('FTP_PASS')
				);
				$this->ftp = new elxisFTP($params);
				$this->ftp_status = $this->ftp->getStatus();
				return ($this->ftp_status == 'connected') ? true : false;				
			} else {
				$this->ftp_status = 'disabled';
			}
		}
		return ($this->ftp_status == 'connected') ? true : false;
	}


	/*************************************************************/
	/* CLOSE FTP CONNECTION (NEVER USE IT, ELXIS AUTO CLOSES IT) */
	/*************************************************************/
	public function closeFTP() {
		if ($this->ftp_status == 'connected') {
			return $this->ftp->disconnect();
		}
		return true;
	}


	/***********************************************************/
	/* CONVERT A RELATIVE PATH TO ELXIS / REPOSITORY FULL PATH */
	/***********************************************************/
	public function elxisPath($path, $inrepo=false) {
		return ($inrepo === true) ? $this->repo_path.'/'.$path : ELXIS_PATH.'/'.$path;
	}


	/*******************************/
	/* GET FILE/FOLDER PERMISSIONS */
	/*******************************/
	public function getPermissions($path, $inrepo=false) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) {
			$this->errormsg = 'Path '.$path.' does not exists!';
			return false;
		}
		clearstatcache();
		return substr(decoct(fileperms($abspath)), -4);
	}


	/*************************/
	/* VALIDATE OCTAL NUMBER */
	/*************************/
	private function isOctal($octal) {
		return ($octal === intval($octal, 8)) ? true : false;
	}


	/*********************/
	/* CHMOD FILE/FOLDER */
	/*********************/
	public function chmod($path, $mode, $inrepo=false) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) {
			$this->errormsg = 'Path '.$path.' does not exists!';
			return false;
		}
		if (is_string($mode)) { $mode = intval($mode, 8); }
		if (!$this->isOctal($mode)) {
    		$this->errormsg = 'You must provide chmod value in Octal';
			return false;
		}
		$ok = @chmod($abspath, $mode);
		if ($ok) { return true; }
		if ($this->useFTP()) {
			$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$path : $this->ftp_root.'/'.$path;
			$ok = $this->ftp->chmod($ftppath, $mode);
			if ($ok === true) { return true; }
			$this->errormsg = $this->ftp->getError();
			return false;
		}
		$this->errormsg = 'Could not set permissions on '.$path;
		return false;
	}


	/***************/
	/* DELETE FILE */
	/***************/
	public function deleteFile($path, $inrepo=false) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) { return true; }
		if (is_dir($abspath)) {
			return $this->deleteFolder($path, $inrepo);
		}
		return $this->removeFile($abspath, $path, $inrepo);
	}


	/*************************/
	/* DELETE ARRAY OF FILES */
	/*************************/
	public function deleteFiles($paths=array(), $inrepo=false) {
		$this->errormsg = '';
		if (!is_array($paths)) {
			$this->errormsg = 'Provided input is not an array of files';
			return false;
		}
		$c = count($paths);
		if ($c == 0) { return true; }
		$deleted = 0;
		foreach ($paths as $path) {
			$deleted += $this->deleteFile($path, $inrepo) ? 1 : 0;
		}
		if ($deleted == 0) { return false; }
		if ($deleted < $c) {
			$this->errormsg = 'Only '.$deleted.' files out of '.$c.' were deleted, rest failed';
			return false;
		}
		return true;
	}


	/**********************/
	/* DIRECT DELETE FILE */
	/**********************/
	private function removeFile($abspath, $path='', $inrepo=false) {
		$ok = @unlink($abspath);
		if ($ok) { return true; }
		if ($this->useFTP()) {
			if ($path == '') { $path = $this->relativePath($abspath, $inrepo); }
			$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$path : $this->ftp_root.'/'.$path;
			$ok = $this->ftp->delete($ftppath);
			if ($ok === true) { return true; }
			$this->errormsg = $this->ftp->getError();
			return false;
		}
		$this->errormsg = ($path == '') ? 'Could not delete file '.$abspath : 'Could not delete file '.$path;
		return false;
	}


	/*************************************/
	/* MAKE RELATIVE PATH FROM FULL PATH */
	/*************************************/
	private function relativePath($abspath, $inrepo=false) {
		if ($inrepo === true) {
			$relative_path = ltrim(preg_replace('#^('.$this->repo_path.')#', '', $abspath), '/');
    	} else {
    		$relative_path = ltrim(preg_replace('#^('.ELXIS_PATH.')#', '', $abspath), '/');
    	}
		return $relative_path; 
	}


	/*******************************************/
	/* DELETE A FOLDER AND ALL OF ITS CONTENTS */
	/*******************************************/
	public function deleteFolder($path, $inrepo=false) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		if (($path == '') || ($path == '/')) {
			$this->errormsg = 'You can not delete everything!';
			return false;
		}
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) { return true; }
		if (!is_dir($abspath)) {
			$this->errormsg = 'Path '.$path.' is not a directory!';
			return false;
		}
		$ok = $this->removeFolderRecurse($abspath, $path, $inrepo);
		return $ok;
	}


	/********************************/
    /* RECURSIVELY DELETE DIRECTORY */
    /********************************/
    private function removeFolderRecurse($dir, $path, $inrepo=false) {
    	$current_dir = opendir($dir);
    	while ($entry = readdir($current_dir)) {
    		if (($entry != '.') && ($entry != '..')) {
    			if (is_dir($dir.$entry)) {
    				$this->removeFolderRecurse($dir.$entry.'/', $path.$entry.'/', $inrepo);
    			} else {
    				$this->removeFile($dir.$entry, $path.$entry, $inrepo);
    			}
    		}
    	}
    	closedir($current_dir);
    	$ok = $this->removeFolder($dir, $path, $inrepo);
    	return $ok;
    }


	/******************************/
	/* DIRECT DELETE EMPTY FOLDER */
	/******************************/
	private function removeFolder($abspath, $path='', $inrepo=false) {
		$ok = @rmdir($abspath);
		if ($ok) { return true; }
		if ($this->useFTP()) {
			if ($path == '') { $path = $this->relativePath($abspath, $inrepo); }
			$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$path : $this->ftp_root.'/'.$path;
			$ok = $this->ftp->rmdir($ftppath);
			if ($ok === true) { return true; }
			$this->errormsg = $this->ftp->getError();
			return false;
		}
		$this->errormsg = ($path == '') ? 'Could not delete folder '.$abspath : 'Could not delete folder '.$path;
		return false;
	}


	/*************/
	/* COPY FILE */
	/*************/
	public function copy($src_path, $dest_path, $inrepo_src=false, $inrepo_dest=false) {
		$this->errormsg = '';
		$src_path = ltrim($src_path, '/');
		$dest_path = ltrim($dest_path, '/');
		$abspath_src = $this->elxisPath($src_path, $inrepo_src);
		$abspath_dest = $this->elxisPath($dest_path, $inrepo_dest);
		if (!file_exists($abspath_src)) {
			$this->errormsg = 'Path '.$src_path.' does not exists!';
			return false;
		}

		if (is_dir($abspath_src)) {
			$this->errormsg = 'Given path '.$src_path.' is a directory!';
			return false;
		}

		if (!is_dir(dirname($abspath_dest))) {
			$dpath2 = dirname($dest_path).'/';
			$ok = $this->createFolder($dpath2, 0755, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not create destination folder '.$dpath2;
				return false;
			}
		} else if (file_exists($abspath_dest)) {
			$ok = $this->removeFile($abspath_dest, $dest_path, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not delete existing file '.$dest_path;
				return false;
			}
		}

		$ok = @copy($abspath_src, $abspath_dest);
		if ($ok) { return true; }
		if ($this->useFTP()) {
			$ftppath = ($inrepo_dest) ? $this->ftp_root_repo.'/'.$dest_path : $this->ftp_root.'/'.$dest_path;
			$ok = $this->ftp->copy($abspath_src, $ftppath);
			if ($ok === true) { return true; }
			$this->errormsg = $this->ftp->getError();
			return false;
		}

		$this->errormsg = 'Could not copy '.$src_path.' to '.$dest_path;
		return false;
	}


	/***************/
	/* COPY FOLDER */
	/***************/
	public function copyFolder($src_path, $dest_path, $inrepo_src=false, $inrepo_dest=false) {
		$this->errormsg = '';
		$src_path = trim($src_path, '/').'/';
		$dest_path = trim($dest_path, '/').'/';
		$abspath_src = $this->elxisPath($src_path, $inrepo_src);
		$abspath_dest = $this->elxisPath($dest_path, $inrepo_dest);
		if (!file_exists($abspath_src)) {
			$this->errormsg = 'Path '.$src_path.' does not exists!';
			return false;
		}
		if (!is_dir($abspath_src)) {
			$this->errormsg = 'Given path '.$src_path.' is not a directory!';
			return false;
		}

		if (!is_dir($abspath_dest)) {
			$ok = $this->createFolder($dest_path, 0755, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not create destination folder '.$dest_path;
				return false;
			}
		}

		$ok = $this->copyFolderRecurse($abspath_src, $src_path, $abspath_dest, $dest_path, $inrepo_src, $inrepo_dest);
		return $ok;
	}


	/******************************/
    /* RECURSIVELY COPY DIRECTORY */
    /******************************/
    private function copyFolderRecurse($src_dir, $src_path, $dest_dir, $dest_path, $inrepo_src=false, $inrepo_dest=false) {
    	$current_dir = opendir($src_dir);
    	while ($entry = readdir($current_dir)) {
    		if (($entry != '.') && ($entry != '..')) {
    			if (is_dir($src_dir.$entry)) {
    				if (!is_dir($dest_dir.$entry)) {
    					$ok = $this->createFolder($dest_path.$entry.'/', 0755, $inrepo_dest);
						if (!$ok) {
							$this->errormsg = 'Could not create destination folder '.$dest_path.$entry.'/';
							return false;
						}
    				}
    				$this->copyFolderRecurse($src_dir.$entry.'/', $src_path.$entry.'/', $dest_dir.$entry.'/', $dest_path.$entry.'/', $inrepo_src, $inrepo_dest);
    			} else {
					$ok = @copy($src_dir.$entry, $dest_dir.$entry);
					if (!$ok) {
						if ($this->useFTP()) {
							$ftppath = ($inrepo_dest) ? $this->ftp_root_repo.'/'.$dest_path.$entry : $this->ftp_root.'/'.$dest_path.$entry;
							$ok = $this->ftp->copy($src_dir.$entry, $ftppath);
							if (!$ok) {
								$this->errormsg = $this->ftp->getError();
								return true;
							}
						}
					}
					if (!$ok) {
						$this->errormsg = 'Could not copy file '.$src_path.$entry;
						return false;
					}
    			}
    		}
    	}
    	closedir($current_dir);
    	return true;
    }


	/*************/
	/* MOVE FILE */
	/*************/
	public function move($src_path, $dest_path, $inrepo_src=false, $inrepo_dest=false) {
		$this->errormsg = '';
		$src_path = ltrim($src_path, '/');
		$dest_path = ltrim($dest_path, '/');
		$abspath_src = $this->elxisPath($src_path, $inrepo_src);
		$abspath_dest = $this->elxisPath($dest_path, $inrepo_dest);
		if (!file_exists($abspath_src)) {
			$this->errormsg = 'Path '.$src_path.' does not exists!';
			return false;
		}

		if (is_dir($abspath_src)) {
			$this->errormsg = 'Given path '.$src_path.' is a directory!';
			return false;
		}

		if (!is_dir(dirname($abspath_dest))) {
			$dpath2 = dirname($dest_path).'/';
			$ok = $this->createFolder($dpath2, 0755, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not create destination folder '.$dpath2;
				return false;
			}
		} else if (file_exists($abspath_dest)) {
			$ok = $this->removeFile($abspath_dest, $dest_path, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not delete existing file '.$dest_path;
				return false;
			}
		}

		$ok = @rename($abspath_src, $abspath_dest);
		if ($ok) { return true; }
		if ($this->useFTP()) {
			$ftppath1 = ($inrepo_src) ? $this->ftp_root_repo.'/'.$src_path : $this->ftp_root.'/'.$src_path;
			$ftppath2 = ($inrepo_dest) ? $this->ftp_root_repo.'/'.$dest_path : $this->ftp_root.'/'.$dest_path;
			$ok = $this->ftp->rename($ftppath1, $ftppath2);
			if ($ok === true) { return true; }
			$this->errormsg = $this->ftp->getError();
			return false;
		}
		$this->errormsg = 'Could not move '.$src_path.' to '.$dest_path;
		return false;
	}


	/***************/
	/* MOVE FOLDER */
	/***************/
	public function moveFolder($src_path, $dest_path, $inrepo_src=false, $inrepo_dest=false) {
		$this->errormsg = '';
		$src_path = trim($src_path, '/').'/';
		$dest_path = trim($dest_path, '/').'/';
		$abspath_src = $this->elxisPath($src_path, $inrepo_src);
		$abspath_dest = $this->elxisPath($dest_path, $inrepo_dest);
		if (!file_exists($abspath_src)) {
			$this->errormsg = 'Path '.$src_path.' does not exists!';
			return false;
		}
		if (!is_dir($abspath_src)) {
			$this->errormsg = 'Given path '.$src_path.' is not a directory!';
			return false;
		}

		if (!is_dir($abspath_dest)) {
			$ok = @rename($abspath_src, $abspath_dest);
			if ($ok) { return true; }

			$ok = $this->createFolder($dest_path, 0755, $inrepo_dest);
			if (!$ok) {
				$this->errormsg = 'Could not create destination folder '.$dest_path;
				return false;
			}
		}

		$ok = $this->copyFolderRecurse($abspath_src, $src_path, $abspath_dest, $dest_path, $inrepo_src, $inrepo_dest);
		if (!$ok) { return false; }
		$ok = $this->removeFolderRecurse($abspath_src, $src_path, $inrepo_src);
		if (!$ok) {
			$this->errormsg = 'Folder successfully copied but the source folder could not be deleted '.$src_path;
		}
		return true;
	}


	/**************************/
	/* LIST FILES INSIDE PATH */
	/**************************/
	public function listFiles($path, $filter = '.', $recurse = false, $fullpath = false, $inrepo=false) {
		$this->errormsg = '';
		$path = trim($path, '/').'/';
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) {
			$this->errormsg = 'Path '.$path.' does not exists!';
			return array();
		}
		if (!is_dir($abspath)) {
			$this->errormsg = 'Path '.$path.' is not a directory!';
			return array();
		}

		$arr = $this->listFilesRecurse($abspath, $filter, $recurse, $fullpath);
		return $arr;
	}


	/**************************************/
	/* RECURSIVELY LIST FILES INSIDE PATH */
	/**************************************/
	private function listFilesRecurse($path, $filter = '.', $recurse = false, $fullpath = false) {
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			if (($entry != '.') && ($entry != '..')) {
				$dir = $path.$entry;
				if (is_dir($dir)) {
					if ($recurse) {
						$arr2 = $this->listFilesRecurse($dir.'/', $filter, $recurse, $fullpath);
						if (isset($arr)) {
							$arr = array_merge($arr, $arr2);
						} else {
							$arr = $arr2;
						}
					}
				} else {
					if (preg_match("/$filter/", $entry)) {
						$arr[] = ($fullpath) ? $path.$entry : $entry;
					}
				}
			}
		}
		closedir($handle);
		if (isset($arr)) {
			asort($arr);
			return $arr;
		} else {
			return array();
		}
	}


	/****************************/
	/* LIST FOLDERS INSIDE PATH */
	/****************************/
	public function listFolders($path, $recurse=false, $fullpath=false, $inrepo=false) {
		$this->errormsg = '';
		$path = trim($path, '/').'/';
		$abspath = $this->elxisPath($path, $inrepo);
		if (!file_exists($abspath)) {
			$this->errormsg = 'Path '.$path.' does not exists!';
			return array();
		}
		if (!is_dir($abspath)) {
			$this->errormsg = 'Path '.$path.' is not a directory!';
			return array();
		}

		$arr = $this->listFoldersRecurse($abspath, $recurse, $fullpath);
		return $arr;
	}


	/****************************************/
	/* RECURSIVELY LIST FOLDERS INSIDE PATH */
	/****************************************/
	private function listFoldersRecurse($path, $recurse=false, $fullpath=false) {
		$handle = opendir($path);
		while ($entry = readdir($handle)) {
			$dir = $path.$entry;
			if (($entry != '.') && ($entry != '..') && is_dir($dir)) {
				$arr[] = ($fullpath) ? $dir.'/' : $entry;
				if ($recurse) {
					$arr2 = $this->listFoldersRecurse($dir.'/', $recurse, $fullpath);
					if (isset($arr)) {
						$arr = array_merge($arr, $arr2);
					} else {
						$arr = $arr2;
					}
				}
			}
		}
		closedir($handle);
		if (isset($arr)) {
			asort($arr);
			return $arr;
		} else {
			return array();
		}
	}


	/**************************************/
	/* CONVERT ABSOLUTE PATHS TO RELATIVE */
	/**************************************/
	public function absToRelativePath($abspaths, $base='', $inrepo=false) {
		$repl = ($inrepo) ? $this->repo_path.'/' : ELXIS_PATH.'/';
		$base = trim($base, '/');
		if ($base != '') { $repl .= $base.'/'; }
		if (is_array($abspaths)) {
			if (count($abspaths) == 0) { return array(); }
			$relpaths = array();
			foreach ($abspaths as $abspath) {
				$relpaths[] = str_replace($repl, '', $abspath);
			}
			return $relpaths;
		}
		
		return str_replace($repl, '', $abspaths);
	}
 
 
	/******************************************/
	/* CREATE A NEW FILE AND WRITE DATA IN IT */
	/******************************************/
	public function createFile($path, $data=null, $inrepo=false, $forcenew=true) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		if (preg_match('#^((http)|(https)|(ftp)\:\/\/)#i', $path)) {
			$this->errormsg = 'You can not create a remote file!';
			return false;
		}

		$abspath = $this->elxisPath($path, $inrepo);
		$flag = ($forcenew) ? 'w' : 'a';
		if ($handle = @fopen($abspath, $flag)) {
            if ($data) { @fwrite($handle, $data); }
            fclose($handle);
            return true;
        }

		if ($this->useFTP()) {
            $keys = preg_split("/[\\\]+/", $path);
            $c = count($keys)-1;
            $keys2 = preg_split("/[\/]+/", $keys[$c]);
            $j = count($keys2)-1;
			$tmpFile = $this->repo_path.'/tmp/'.$keys2[$j];
            if (!file_put_contents($tmpFile, $data)) {
                $this->errormsg = 'Folder repository/tmp/ must be writable!';
				return false;
			}
			$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$path : $this->ftp_root.'/'.$path;
			$ok = $this->ftp->put($ftppath, $tmpFile);
			@unlink($tmpFile);
			if (!$ok) {
				$this->errormsg = 'Could not create file '.$path;
				return false;
			} else {
				return true;
			}
		}

		$this->errormsg = 'Could not create file '.$path;
		return false;
	}

	/***************************************************/
	/* APPEND DATA TO A FILE (CREATE IT IF NOT EXISTS) */
	/***************************************************/
	public function writeFile($path, $data=null, $inrepo=false) {
		$ok = $this->createFile($path, $data, $inrepo, false);
		return $ok;
	}


	/*******************/
	/* CREATE A FOLDER */
	/*******************/
	public function createFolder($path, $mode=0755, $inrepo=false) {
		$this->errormsg = '';
		$path = ltrim($path, '/');
		$base = ($inrepo) ? $this->repo_path : ELXIS_PATH;
		if (is_dir($base.'/'.$path)) { return true; }
		$parts = preg_split('#\/#', $path, -1, PREG_SPLIT_NO_EMPTY);
		if (!$parts) { return true; }

		$dirmode = (intval($mode) > 0) ? $mode : 0755;
		$origmask = @umask(0);
		$cfpath = $base.'/';
		$cpath = '';
		foreach ($parts as $folder) {
			$cfpath .= $folder.'/';
			$cpath .= $folder.'/';
			if (is_dir($cfpath)) { continue; }
			$ok = @mkdir($cfpath, $dirmode);
			if (!$ok) {
				if ($this->useFTP()) {
					$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$cpath : $this->ftp_root.'/'.$cpath;
					$ok = $this->ftp->mkdir($ftppath);
					if (!$ok) {
						$this->errormsg = 'Could not create folder '.$path;
						return false;
					}
				} else {
					$this->errormsg = 'Could not create folder '.$path;
					return false;
				}
			}
		}
		@umask($origmask);
		return true;
	}


	/****************/
	/* UPLOAD FILES */
	/****************/
	public function upload($src_path, $dest_path, $inrepo=false) {
		$this->errormsg = '';
		$dest_path = ltrim($dest_path, '/');
		$abspath_dest = $this->elxisPath($dest_path, $inrepo);
		$baseDir = dirname($abspath_dest);
		if (!file_exists($baseDir)) {
			$relbase = $this->relativePath($baseDir, $inrepo);
			if (!$this->createFolder($relbase, 0755, $inrepo)) {
				$this->errormsg = 'Upload failed because the creation of the container folder failed '.$baseDir;
				return false;
			}
		}

        if (@move_uploaded_file($src_path, $abspath_dest)) { return true; }
		if ($this->useFTP()) {
			$ftppath = ($inrepo) ? $this->ftp_root_repo.'/'.$dest_path : $this->ftp_root.'/'.$dest_path;
			$ok = $this->ftp->put($ftppath, $src_path);
			if (!$ok) {
				$this->errormsg = 'Could not upload file '.$src_path.'!';
				return false;
			} else {
				return true;
			}
		}

		$this->errormsg = 'Could not upload file '.$src_path.'!';
		return false;
	}


	/**************************/
	/* GET A FILE'S EXTENSION */
	/**************************/
	public function getExtension($file) {
		if (trim($file) == '') { return ''; }
		return substr(strrchr($file, '.'), 1); //this is the fastest method
	}


	/*******************************************/
	/* GET A FILE'S NAME WITHOUT THE EXTENSION */
	/*******************************************/
	public function getFilename($file) {
		if (trim($file) == '') { return ''; }
		$parts = preg_split('#\/#', $file, -1, PREG_SPLIT_NO_EMPTY);
		$i = count($parts) - 1;
		return substr($parts[$i], 0, strrpos($parts[$i], '.'));
	}


	/********************************************/
	/* GET A FILE'S NAME AND EXTENSION AS ARRAY */
	/********************************************/
	public function getNameExtension($file) {
		$info = array('name' => '', 'extension' => '');
		if (trim($file) == '') { return $info; }
		$pos = strrpos($file, '.');
		if ($pos === false) {
			$info['name'] = $file;
			return $info;
		}
		$info['name'] = substr($file, 0, $pos);
		$pos = $pos + 1;
		$info['extension'] = substr($file, $pos);
		return $info;
	}


	/**************************/
	/* GET A FILE'S MIME TYPE */
	/**************************/
	public function getMimetype($file) {
		if (function_exists('finfo_file')) { //php 5.3.0+
			if (file_exists($file)) {
				$finfo = @finfo_open(FILEINFO_MIME_TYPE);
				if ($finfo) {
    				$mimetype = finfo_file($finfo, $file);
					finfo_close($finfo);
					return $mimetype;
				}
			}
		}

		$ext = strtolower($this->getExtension($file));
		if ($ext == '') { return ''; }
		$mime = array();
		$mime['acx'] = 'application/internet-property-stream';
		$mime['ai'] = 'application/postscript';
		$mime['aif'] = 'audio/x-aiff';
		$mime['aifc'] = 'audio/x-aiff';
		$mime['aiff'] = 'audio/x-aiff';
		$mime['asc'] = 'text/plain';
		$mime['asf'] = 'video/x-ms-asf';
		$mime['asr'] = 'video/x-ms-asf';
		$mime['asx'] = 'video/x-ms-asf';
		$mime['au'] = 'audio/basic';
		$mime['avi'] = 'video/x-msvideo';
		$mime['axs'] = 'application/olescript';
		$mime['bas'] = 'text/plain';
		$mime['bcpio'] = 'application/x-bcpio';
		$mime['bin'] = 'application/octet-stream';
		$mime['bmp'] = 'image/bmp';
		$mime['c'] = 'text/plain';
		$mime['cat'] = 'application/vnd.ms-pkiseccat';
		$mime['cdf'] = 'application/x-cdf';
		$mime['cer'] = 'application/x-x509-ca-cert';
		$mime['class'] = 'application/octet-stream';
		$mime['clp'] = 'application/x-msclip';
		$mime['cmx'] = 'image/x-cmx';
		$mime['cod'] = 'image/cis-cod';
		$mime['cpio'] = 'application/x-cpio';
		$mime['crd'] = 'application/x-mscardfile';
		$mime['crl'] = 'application/pkix-crl';
		$mime['crt'] = 'application/x-x509-ca-cert';
		$mime['csh'] = 'application/x-csh';
		$mime['css'] = 'text/css';
		$mime['dcr'] = 'application/x-director';
		$mime['der'] = 'application/x-x509-ca-cert';
		$mime['dir'] = 'application/x-director';
		$mime['dll'] = 'application/octet-stream';
		$mime['dms'] = 'application/octet-stream';
		$mime['doc'] = 'application/msword';
		$mime['dot'] = 'application/msword';
		$mime['dvi'] = 'application/x-dvi';
		$mime['dxr'] = 'application/x-director';
		$mime['eps'] = 'application/postscript';
		$mime['etx'] = 'text/x-setext';
		$mime['evy'] = 'application/envoy';
		$mime['exe'] = 'application/octet-stream';
		$mime['fif'] = 'application/fractals';
		$mime['flr'] = 'x-world/x-vrml';
		$mime['gif'] = 'image/gif';
		$mime['gtar'] = 'application/x-gtar';
		$mime['gz'] = 'application/x-gzip';
		$mime['gzip'] = 'application/x-gzip';
		$mime['h'] = 'text/plain';
		$mime['h261'] = 'video/h261';
		$mime['h263'] = 'video/h263';
		$mime['h264'] = 'video/h264';
		$mime['hdf'] = 'application/x-hdf';
		$mime['hlp'] = 'application/winhlp';
		$mime['hqx'] = 'application/mac-binhex40';
		$mime['hta'] = 'application/hta';
		$mime['htc'] = 'text/x-component';
		$mime['htm'] = 'text/html';
		$mime['html'] = 'text/html';
		$mime['htt'] = 'text/webviewhtml';
		$mime['ico'] = 'image/x-icon';
		$mime['ief'] = 'image/ief';
		$mime['iii'] = 'application/x-iphone';
		$mime['ins'] = 'application/x-internet-signup';
		$mime['isp'] = 'application/x-internet-signup';
		$mime['jfif'] = 'image/pipeg';
		$mime['jpe'] = 'image/jpeg';
		$mime['jpeg'] = 'image/jpeg';
		$mime['jpg'] = 'image/jpeg';
		$mime['js'] = 'application/x-javascript';
		$mime['latex'] = 'application/x-latex';
		$mime['lha'] = 'application/octet-stream';
		$mime['lsf'] = 'video/x-la-asf';
		$mime['lsx'] = 'video/x-la-asf';
		$mime['lzh'] = 'application/octet-stream';
		$mime['m13'] = 'application/x-msmediaview';
		$mime['m14'] = 'application/x-msmediaview';
		$mime['m3u'] = 'audio/x-mpegurl';
		$mime['man'] = 'application/x-troff-man';
		$mime['mdb'] = 'application/x-msaccess';
		$mime['me'] = 'application/x-troff-me';
		$mime['mht'] = 'message/rfc822';
		$mime['mhtml'] = 'message/rfc822';
		$mime['mid'] = 'audio/midi';
		$mime['midi'] = 'audio/midi';
		$mime['mny'] = 'application/x-msmoney';
		$mime['mov'] = 'video/quicktime';
		$mime['movie'] = 'video/x-sgi-movie';
		$mime['mp2'] = 'audio/mpeg';
		$mime['mp3'] = 'audio/mpeg';
		$mime['mp4'] = 'video/mp4';
		$mime['mpa'] = 'video/mpeg';
		$mime['mpe'] = 'video/mpeg';
		$mime['mpeg'] = 'video/mpeg';
		$mime['mpg'] = 'video/mpeg';
		$mime['mpp'] = 'application/vnd.ms-project';
		$mime['mpv2'] = 'video/mpeg';
		$mime['ms'] = 'application/x-troff-ms';
		$mime['mvb'] = 'application/x-msmediaview';
		$mime['nws'] = 'message/rfc822';
		$mime['oda'] = 'application/oda';
		$mime['ogv'] = 'video/ogg';
		$mime['ogg'] = 'video/ogg';
		$mime['p10'] = 'application/pkcs10';
		$mime['p12'] = 'application/x-pkcs12';
		$mime['p7b'] = 'application/x-pkcs7-certificates';
		$mime['p7c'] = 'application/x-pkcs7-mime';
		$mime['p7m'] = 'application/x-pkcs7-mime';
		$mime['p7r'] = 'application/x-pkcs7-certreqresp';
		$mime['p7s'] = 'application/x-pkcs7-signature';
		$mime['pbm'] = 'image/x-portable-bitmap';
		$mime['pdf'] = 'application/pdf';
		$mime['pfx'] = 'application/x-pkcs12';
		$mime['pgm'] = 'image/x-portable-graymap';
		$mime['php'] = 'application/x-httpd-php';
		$mime['pko'] = 'application/ynd.ms-pkipko';
		$mime['pma'] = 'application/x-perfmon';
		$mime['pmc'] = 'application/x-perfmon';
		$mime['pml'] = 'application/x-perfmon';
		$mime['pmr'] = 'application/x-perfmon';
		$mime['pmw'] = 'application/x-perfmon';
		$mime['png'] = 'image/png';
		$mime['pnm'] = 'image/x-portable-anymap';
		$mime['pot'] = 'application/vnd.ms-powerpoint';
		$mime['ppm'] = 'image/x-portable-pixmap';
		$mime['pps'] = 'application/vnd.ms-powerpoint';
		$mime['ppt'] = 'application/vnd.ms-powerpoint';
		$mime['prf'] = 'application/pics-rules';
		$mime['ps'] = 'application/postscript';
		$mime['pub'] = 'application/x-mspublisher';
		$mime['qt'] = 'video/quicktime';
		$mime['ra'] = 'audio/x-realaudio';
		$mime['ram'] = 'audio/x-pn-realaudio';
		$mime['ras'] = 'image/x-cmu-raster';
		$mime['rgb'] = 'image/x-rgb';
		$mime['rm'] = 'audio/x-pn-realaudio';
		$mime['rmi'] = 'audio/mid';
		$mime['roff'] = 'application/x-troff';
		$mime['rpm'] = 'audio/x-pn-realaudio-plugin';
		$mime['rtf'] = 'text/rtf';
		$mime['rtx'] = 'text/richtext';
		$mime['scd'] = 'application/x-msschedule';
		$mime['sct'] = 'text/scriptlet';
		$mime['setpay'] = 'application/set-payment-initiation';
		$mime['setreg'] = 'application/set-registration-initiation';
		$mime['sh'] = 'application/x-sh';
		$mime['shar'] = 'application/x-shar';
		$mime['sit'] = 'application/x-stuffit';
		$mime['snd'] = 'audio/basic';
		$mime['spc'] = 'application/x-pkcs7-certificates';
		$mime['spl'] = 'application/x-futuresplash';
		$mime['src'] = 'application/x-wais-source';
		$mime['sst'] = 'application/vnd.ms-pkicertstore';
		$mime['stl'] = 'application/vnd.ms-pkistl';
		$mime['stm'] = 'text/html';
		$mime['sv4cpio'] = 'application/x-sv4cpio';
		$mime['sv4crc'] = 'application/x-sv4crc';
		$mime['svg'] = 'image/svg+xml';
		$mime['swf'] = 'application/x-shockwave-flash';
		$mime['t'] = 'application/x-troff';
		$mime['tar'] = 'application/x-tar';
		$mime['tcl'] = 'application/x-tcl';
		$mime['tex'] = 'application/x-tex';
		$mime['texi'] = 'application/x-texinfo';
		$mime['texinfo'] = 'application/x-texinfo';
		$mime['tgz'] = 'application/x-compressed';
		$mime['tif'] = 'image/tiff';
		$mime['tiff'] = 'image/tiff';
		$mime['tr'] = 'application/x-troff';
		$mime['trm'] = 'application/x-msterminal';
		$mime['tsv'] = 'text/tab-separated-values';
		$mime['txt'] = 'text/plain';
		$mime['uls'] = 'text/iuls';
		$mime['ustar'] = 'application/x-ustar';
		$mime['vcf'] = 'text/x-vcard';
		$mime['vrml'] = 'x-world/x-vrml';
		$mime['wav'] = 'audio/x-wav';
		$mime['wbxml'] = 'application/vnd.wap.wbxml';
		$mime['wcm'] = 'application/vnd.ms-works';
		$mime['wdb'] = 'application/vnd.ms-works';
		$mime['wks'] = 'application/vnd.ms-works';
		$mime['wmf'] = 'application/x-msmetafile';
		$mime['wml'] = 'text/vnd.wap.wml';
		$mime['wmlc'] = 'application/vnd.wap.wmlc';
		$mime['wmls'] = 'text/vnd.wap.wmlscript';
		$mime['wmlsc'] = 'application/vnd.wap.wmlscriptc';
		$mime['wmv'] = 'video/x-ms-wmv';
		$mime['wps'] = 'application/vnd.ms-works';
		$mime['wri'] = 'application/x-mswrite';
		$mime['wrl'] = 'x-world/x-vrml';
		$mime['wrz'] = 'x-world/x-vrml';
		$mime['xaf'] = 'x-world/x-vrml';
		$mime['xbm'] = 'image/x-xbitmap';
		$mime['xhtml'] = 'application/xhtml+xml';
		$mime['xla'] = 'application/vnd.ms-excel';
		$mime['xlc'] = 'application/vnd.ms-excel';
		$mime['xlm'] = 'application/vnd.ms-excel';
		$mime['xls'] = 'application/vnd.ms-excel';
		$mime['xlt'] = 'application/vnd.ms-excel';
		$mime['xlw'] = 'application/vnd.ms-excel';
		$mime['xml'] = 'text/xml';
		$mime['xof'] = 'x-world/x-vrml';
		$mime['xpm'] = 'image/x-xpixmap';
		$mime['xsl'] = 'text/xml';
		$mime['xwd'] = 'image/x-xwindowdump';
		$mime['z'] = 'application/x-compress';
		$mime['zip'] = 'application/zip';
		$mime['323'] = 'text/h323';

		return (isset($mime[$ext])) ? $mime[$ext] : '';
	}


	/*******************************/
	/* RESIZE A JPEG/PNG/GIF IMAGE */
	/*******************************/
	public function resizeImage($src_file, $width, $height, $crop=false, $inrepo=false) {
		$this->errormsg = '';
		$width = (int)$width;
		$height = (int)$height;
		if ($width < 1) { $this->errormsg = 'Invalid new image width!'; return false; }
		if ($height < 1) { $this->errormsg = 'Invalid new image height!'; return false; }

		$src_file = ltrim($src_file, '/');
		$abspath_src = $this->elxisPath($src_file, $inrepo);
		if (($src_file == '') || !file_exists($abspath_src) || !is_file($abspath_src)) {
			$this->errormsg = 'Image not found or it is not a file!';
			return false;
		}

		$imginfo = getimagesize($abspath_src);
    	if (!$imginfo) { $this->errormsg = 'Invalid image file!'; return false; }
		if (!in_array($imginfo[2], array(1, 2, 3))) { $this->errormsg = 'Invalid image file!'; return false; }

		$dst_x = 0;
		$dst_y = 0;
		$dst_w = $width;
		$dst_h = $height;
		if ($crop) {
			if ($imginfo[0] > $imginfo[1]) {
				$dst_w = ($imginfo[0] * $height)/$imginfo[1];
				$dst_x = -(($dst_w - $width)/ 2);
			} elseif ($imginfo[0] <= $imginfo[1]) {
				$dst_h = ($imginfo[1] * $width) / $imginfo[0];
				$dst_y = -(($dst_h - $height)/ 2);
			}
		}

 		if (($imginfo[2] == 2) && function_exists('imagecreatefromjpeg')) { //JPG
			$src_img = imagecreatefromjpeg($abspath_src);
			if (!$src_img){ return false;}
			$dst_img = imagecreatetruecolor($width, $height);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			imagejpeg($dst_img, $abspath_src, 80);
			@imagedestroy($src_img);
			@imagedestroy($dst_img);
		} else if (($imginfo[2] == 3) && function_exists('imagecreatefrompng')) { //PNG
			$src_img = imagecreatefrompng($abspath_src);
			$dst_img = imagecreatetruecolor($width, $height);
			imagealphablending($dst_img, true);
			imagesavealpha($dst_img, true);
			$trans_color = imagecolorallocatealpha($dst_img, 0, 0, 0, 127);
			@imagefill($dst_img, 0, 0, $trans_color);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			imagepng($dst_img, $abspath_src, 6);
			@imagedestroy($src_img);
			@imagedestroy($dst_img);
		} else if (($imginfo[2] == 1) && function_exists('imagecreatefromgif')) { //GIF
			$src_img = imagecreatefromgif($abspath_src);
			$dst_img = imagecreatetruecolor($width, $height);
			imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $imginfo[0], $imginfo[1]);
			imagegif($dst_img, $abspath_src);
			@imagedestroy($src_img);
			@imagedestroy($dst_img);
		} else {
			$this->errormsg = 'Not supported image format!';
			return false;
		}

    	$imginfo = @getimagesize($abspath_src);
    	if ($imginfo == null) {
			$this->errormsg = 'Could not resize image '.$src_file;
			return false;
		} else {
			return true;
		}
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}

}

?>