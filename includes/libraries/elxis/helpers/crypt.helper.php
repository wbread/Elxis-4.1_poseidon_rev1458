<?php 
/**
* @version		$Id: crypt.helper.php 1272 2012-09-08 09:08:00Z datahell $
* @package		Elxis
* @subpackage	Helpers / Encryption
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisCryptHelper {
	
	private $method = 'xor';
	private $key = '';
	private $hash_type = 'sha1'; //sha1 or md5


	/***************************************/
	/* DETERMINE ENCRYPTION METHOD AND KEY */
	/***************************************/
	public function __construct($params=null) {
		if ($params && is_array($params) && isset($params['method']) && isset($params['key'])) {
			$method = trim($params['method']);
			$this->key = trim($params['key']);
		} else {
			$elxis = eFactory::getElxis();
			$method = $elxis->getConfig('ENCRYPT_METHOD');
			$this->key = trim($elxis->getConfig('ENCRYPT_KEY'));
		}

		if (($method == '') || ($method == 'auto') || ($method == 'mcrypt')) {
			$this->method = function_exists('mcrypt_encrypt') ? 'mcrypt' : 'xor';
		} else {
			$this->method = 'xor';
		}

		if ($this->key == '') {
			trigger_error('For security reasons an encryption key must be set in configuration.php file (parameter ENCRYPT_KEY).', E_USER_ERROR);
		}
	}


	/********************/
	/* ENCRYPT A STRING */
	/********************/
	public function encrypt($string, $key='') {
		$key = $this->getMD5Key($key);
		$enc = $this->xor_encode($string, $key);
		if ($this->method == 'mcrypt') {
			$enc = $this->mcrypt_encode($enc, $key);
		}
		return base64_encode($enc);
	}


	/********************/
	/* DECRYPT A STRING */
	/********************/
	public function decrypt($string, $key='') {
		$key = $this->getMD5Key($key);
		if (preg_match('/[^a-zA-Z0-9\/\+=]/', $string)) { return false; }
		$dec = base64_decode($string);
		if ($this->method == 'mcrypt') {
			$dec = $this->mcrypt_decode($dec, $key);
			if ($dec === false) { return false; }
		}
		return $this->xor_decode($dec, $key);
	}


	/*****************************************/
	/* GET THE 128 BIT LENGTH ENCRYPTION KEY */
	/*****************************************/
	private function getMD5Key($key='') {
		if (trim($key) == '') { return md5($this->key); }
		return md5($key);
	}


	/******************************/
	/* ENCRYPT A STRING USING XOR */
	/******************************/
	private function xor_encode($string, $key) {
		$rand = '';
		while (strlen($rand) < 32) {
			$rand .= mt_rand(0, mt_getrandmax());
		}
		$rand = $this->hash($rand);
		$enc = '';
		for ($i = 0; $i < strlen($string); $i++) {			
			$enc .= substr($rand, ($i % strlen($rand)), 1).(substr($rand, ($i % strlen($rand)), 1) ^ substr($string, $i, 1));
		}

		return $this->xor_merge($enc, $key);
	}


	/********************************/
	/* DECRYPT A XOR ENCODED STRING */
	/********************************/
	private function xor_decode($string, $key) {
		$string = $this->xor_merge($string, $key);
		$dec = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$dec .= (substr($string, $i++, 1) ^ substr($string, $i, 1));
		}
		return $dec;
	}


	/***********************************************************/
	/* COMPUTE THE DIFFERENCE BETWEEN STRING AND KEY USING XOR */
	/***********************************************************/
	private function xor_merge($string, $key) {
		$hash = $this->hash($key);
		$str = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$str .= substr($string, $i, 1) ^ substr($hash, ($i % strlen($hash)), 1);
		}
		return $str;
	}


	/************************/
	/* HASH ENCODE A STRING */
	/************************/
	private function hash($string) {
		return ($this->hash_type == 'sha1') ? sha1($string) : md5($string);
	}


	/*********************************/
	/* ENCRYPT A STRING USING MCRYPT */
	/*********************************/
	private function mcrypt_encode($data, $key) {
		$init_size = mcrypt_get_iv_size($this->getCipher(), $this->getMode());
		$init_vect = mcrypt_create_iv($init_size, MCRYPT_RAND);
		return $this->addCipherNoise($init_vect.mcrypt_encrypt($this->getCipher(), $key, $data, $this->getMode(), $init_vect), $key);
	}


	/************************/
	/* DECRYPT USING MCRYPT */
	/************************/
	private function mcrypt_decode($data, $key) {
		$data = $this->removeCipherNoise($data, $key);
		$init_size = mcrypt_get_iv_size($this->getCipher(), $this->getMode());
		if ($init_size > strlen($data)) { return false; }
		$init_vect = substr($data, 0, $init_size);
		$data = substr($data, $init_size);
		return rtrim(mcrypt_decrypt($this->getCipher(), $key, $data, $this->getMode(), $init_vect), "\0");
	}


	/*************************/
	/* GET CIPHER FOR MCRYPT */
	/*************************/
	private function getCipher() {
		return MCRYPT_RIJNDAEL_256;
	}


	/*************************/
	/* GET MCRYPT MODE VALUE */
	/*************************/
	private function getMode() {
		return MCRYPT_MODE_ECB;
	}


	/**********************************************/
	/* ADD PERMUTTED NOISE TO IV + ENCRYPTED DATA */
	/**********************************************/
	private function addCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) { $j = 0; }
			$str .= chr((ord($data[$i]) + ord($keyhash[$j])) % 256);
		}
		return $str;
	}


	/***************************************************/
	/* REMOVE PERMUTTED NOISE FROM IV + ENCRYPTED DATA */
	/***************************************************/
	private function removeCipherNoise($data, $key) {
		$keyhash = $this->hash($key);
		$keylen = strlen($keyhash);
		$str = '';
		for ($i = 0, $j = 0, $len = strlen($data); $i < $len; ++$i, ++$j) {
			if ($j >= $keylen) { $j = 0; }
			$temp = ord($data[$i]) - ord($keyhash[$j]);
			if ($temp < 0) { $temp = $temp + 256; }
			$str .= chr($temp);
		}
		return $str;
	}


	/****************************/
	/* GENERATE A PASSWORD SALT */
	/****************************/
	public function getEncryptedPassword($text) {
		$text = trim($text);
		if (strlen($text) < 4) { //not acceptable password, randomize it and make it useless
			$salt = $this->key.rand(10000000, 99999999);
			return $this->hash($salt);
		}
		$kparts = str_split($this->key);
		$tparts = str_split($text);
		$f1 = substr($this->key, -1);
		$f2 = substr($text, -1);
		$f3 = substr($this->key, 0, 1);
		$f4 = substr($text, 0, 1);
		$salt = '';
		if (count($kparts) > count($tparts)) {
			foreach ($kparts as $k => $char) {
				$salt .= $char;
				$salt .= (isset($tparts[$k])) ? $tparts[$k] : '';
			}
		} else {
			foreach ($tparts as $k => $char) {
				$salt .= $char;
				$salt .= (isset($kparts[$k])) ? $kparts[$k] : '';
			}
		}

		$salt = str_ireplace($f1, $f2, $salt);
		$salt = str_ireplace($f4, $f3, $salt);
		return $this->hash($salt);
	}

}

?>