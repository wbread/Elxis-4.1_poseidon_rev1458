<?php 
/**
* @version		$Id: EpiTwitter.php 1404 2013-03-25 10:09:12Z datahell $
* @package		Elxis
* @subpackage	OAuth / Authentication
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class EpiTwitter extends EpiOAuth {

	const EPITWITTER_SIGNATURE_METHOD = 'HMAC-SHA1';
	protected $requestTokenUrl = 'https://api.twitter.com/oauth/request_token';
	protected $accessTokenUrl = 'https://api.twitter.com/oauth/access_token';
	protected $authorizeUrl = 'https://api.twitter.com/oauth/authorize';
	protected $apiUrl = 'https://api.twitter.com';


	public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null) {
		parent::__construct($consumerKey, $consumerSecret, self::EPITWITTER_SIGNATURE_METHOD);
		$this->setToken($oauthToken, $oauthTokenSecret);
	}


	public function __call($name, $params = null) {
		$parts = explode('_', $name);
		$method = strtoupper(array_shift($parts));
		$parts = implode('_', $parts);
		$url = $this->apiUrl . '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
		if (!empty($params)) { $args = array_shift($params); } else { $args = array(); }
		return new EpiTwitterJson(call_user_func(array($this, 'httpRequest'), $method, $url, $args));
	}

}


class EpiTwitterJson {

	private $resp;


	public function __construct($resp) {
		$this->resp = $resp;
	}


	public function __get($name) {
		$this->responseText = $this->resp->data;
		$this->response = (array)json_decode($this->responseText, 1);
		foreach($this->response as $k => $v) { $this->$k = $v; }
		return (isset($this->$name)) ? $this->$name : null;
	}

}

?>