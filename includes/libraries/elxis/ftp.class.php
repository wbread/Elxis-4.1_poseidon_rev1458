<?php 
/**
* @version		$Id: ftp.class.php 98 2011-02-11 11:24:35Z datahell $
* @package		Elxis
* @subpackage	FTP Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisFTP {

    private $ftp_host = 'localhost';
	private $ftp_user = 'root';
	private $ftp_pass = '';
	private $ftp_port = 21;
    private $conn_id = null;
	private $status = 'disconnected'; //disconnected, connected, error
	private $errormsg = '';


	/***************/
	/* CONSTRUCTOR */
	/***************/
    public function __construct($params=array()) {
    	if (is_array($params) && (count($params) > 0)) {
    		$this->setConnectionParams($params);
			if (($this->ftp_host != '') && ($this->ftp_user != '') && ($this->ftp_pass != '')) {
				$this->connect();
			}
    	}
    }


	/*****************************/
	/* SET CONNECTION PARAMETERS */
	/*****************************/
	private function setConnectionParams($params=array()) {
    	if (is_array($params) && (count($params) > 0)) {
    		$this->ftp_host = $params['ftp_host'];
    		$this->ftp_user = $params['ftp_user'];
    		$this->ftp_pass = $params['ftp_pass'];
    		$this->ftp_port = (int)$params['ftp_port'];
		}
	}


	/******************/
	/* CONNECT TO FTP */
	/******************/
    public function connect($params=array()) {
    	if (is_array($params) && (count($params) > 0)) {
    		$this->setConnectionParams($params);
    	}

    	if ($this->status == 'connected') { return true; }
    	if ($this->status == 'error') { return false; }
    	//ftp_ssl_connect?
    	if ($this->ftp_port > 0) {
    		$this->conn_id = @ftp_connect($this->ftp_host, ''.$this->ftp_port.'');
    	} else {
    		$this->conn_id = @ftp_connect($this->ftp_host);
    	}

    	if (!$this->conn_id) {
    		$this->status = 'error';
    		$this->errormsg = 'Could not connect to FTP host '.$this->ftp_host;
    		return false;
    	}

		$this->status = 'connected';
		$ok = @ftp_login($this->conn_id, $this->ftp_user, $this->ftp_pass);
		if (!$ok) {
			$this->disconnect();
			$this->status = 'error';
			$this->errormsg = 'Connection succeed but login failed to FTP host '.$this->ftp_host;
			return false;
		}
		return true;
    }


	/************************/
	/* CLOSE FTP CONNECTION */
	/************************/
    public function disconnect() {
    	if ($this->status != 'connected') { return true; }
		$ok = @ftp_close($this->conn_id);
		if ($ok) { $this->status = 'disconnected'; }
		return $ok;
    }


	/*************************/
	/* GET CONNECTION STATUS */
	/*************************/
	public function getStatus() {
		return $this->status;
	}


	/*********************/
	/* GET ERROR MESSAGE */
	/*********************/
	public function getError() {
		return $this->errormsg;
	}


	/*************************/
	/* VALIDATE OCTAL NUMBER */
	/*************************/
	private function isOctal($octal) {
		return ($octal === intval($octal, 8)) ? true : false;
	}


	/*******************************************************/
	/* GET PROPER FTP MODE DEPENDING ON THE FILE EXTENSION */
	/*******************************************************/
	private function getMode($path) {
		$path_parts = pathinfo($path);
		if (!isset($path_parts['extension'])) { return FTP_BINARY; }
		if (in_array(strtolower($path_parts['extension']), array('am','asp','bat','c','cfm','cgi','conf','cpp','css',
		'dhtml','diz','h','hpp','htm','html','in','inc','js','m4','mak','nfs','nsi','pas','patch','php','php3','php4',
		'php5','phtml','pl','po','py','qmail','sh','shtml','sql','tcl','tpl','txt','vbs','xml','xrc'))) {
        	return FTP_ASCII;
        }
		return FTP_BINARY;
	}


	/*********************/
	/* CHMOD FILE/FOLDER */
	/*********************/
    public function chmod($path, $mode) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		if (is_string($mode)) { $mode = intval($mode, 8); }
		if (!$this->isOctal($mode)) {
    		$this->errormsg = 'You must provide chmod value in Octal';
			return false;
		}
		$ok = (@ftp_chmod($this->conn_id, $mode, $path) !== false) ? true : false;
		if (!$ok) {
			$this->errormsg = 'Could not change mode '.$path.' to '.decoct($mode);
			return false;
		}
		return true;
    }


	/***************/
	/* DELETE FILE */
	/***************/
    public function delete($path) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}

		if (@ftp_delete($this->conn_id, $path)) {
			return true;
		} else {
    		$this->errormsg = 'Could not delete file '.$path;
			return false;
		}
    }


	/*****************/
	/* DELETE FOLDER */
	/*****************/
    public function rmdir($path) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		if (@ftp_rmdir($this->conn_id, $path)) {
			return true;
		} else {
    		$this->errormsg = 'Could not delete folder '.$path;
			return false;
		}
    }


	/*************/
	/* COPY FILE */
	/*************/
	public function copy($src, $dest) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		$mode = $this->getMode($src);
		ftp_pasv($this->conn_id, true);
        if (@ftp_put($this->conn_id, $dest, $src, $mode)) {
        	return true;
        } else {
        	$this->errormsg = 'Could not copy '.$src;
        	return false;
        }
	}


	/**********************/
	/* RENAME FILE/FOLDER */
	/**********************/
    public function rename($src, $dest) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		$mode = $this->getMode($src);
		if (@ftp_rename($this->conn_id, $src, $dest)) {
			return true;
		} else {
			$this->errormsg = 'Could not rename/move '.$src;
			return false;
		}
	}


	/*******************/
	/* PUT FILE/FOLDER */
	/*******************/
    public function put($remote_file, $local_file) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		$mode = $this->getMode($local_file);
		ftp_pasv($this->conn_id, true);
		if (@ftp_put($this->conn_id, $remote_file, $local_file, $mode)) {
			return true;
		} else {
			$this->errormsg = 'Could not put '.$local_file;
			return false;
		}
	}


	/********************/
	/* CREATE DIRECTORY */
	/********************/
    public function mkdir($path) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		if (@ftp_mkdir($this->conn_id, $path)) {
			return true;
		} else {
			$this->errormsg = 'Could not create folder '.$path;
			return false;
		}
	}


	/*****************************************/
	/* LIST FILES/FOLDERS IN GIVEN DIRECTORY */
	/*****************************************/
    public function nlist($path) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}
		$list = @ftp_nlist($this->conn_id, $path);
		return $list;
    }


	/*************************************/
	/* DOWNLOAD FILE AND SAVE IT LOCALLY */
	/*************************************/
    public function get($local_file, $path) {
    	if ($this->status == 'disconnected') { $this->connect(); }
    	if ($this->status == 'error') {
    		$this->errormsg = 'No active FTP connection';
			return false;
		}

		$mode = $this->getMode($path);
		if (@ftp_get($this->conn_id, $local_file, $path, $mode)) {
			return true;
		} else {
			$this->errormsg = 'Could not download file '.$path;
			return false;
		}
    }

}

?>