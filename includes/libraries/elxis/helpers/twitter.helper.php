<?php 
/**
* @version		$Id: twitter.helper.php 233 2011-03-27 10:40:04Z datahell $
* @package		Elxis
* @subpackage	Helpers / Twitter
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisTwitterHelper {

	private $errormsg = '';

		
	/***************/
    /* CONSTRUCTOR */
    /***************/
	public function __construct() {
	}


	/********************************/
	/* GET USER TWEETS FROM TWITTER */
	/********************************/
	public function getTweets($username, $limit=10) {
		$this->errormsg = '';
		$username  = trim($username);
		$limit = (int)$limit;
		if ($limit < 1) { $limit = 5; }
		if ($username == '') {
			$this->errormsg = 'Twitter username can not be empty!';
			return false;
		}

		$xml = $this->getCURL(array('q' => 'from:'.$username, 'rpp' => $limit, 'result_type' => 'recent'));
		if (!$xml || ($xml == '')) {
			$this->errormsg = 'Could not fetch tweets from Twitter!';
			return false;
		}

		if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
		$xmlDoc = simplexml_load_string($xml, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$this->errormsg = 'Could not load data.';
			return false;
		}

		if (!isset($xmlDoc->entry)) { return array(); }

		$ssl = eFactory::getURI()->detectSSL();
		$tweets = array();
		foreach($xmlDoc->entry as $entry) {
			$tweet = new stdClass();
			$tweet->id = (string)$entry->id;
			$tweet->published = isset($entry->published) ? (string)$entry->published : '';
			$tweet->title = (string)$entry->title;
			$tweet->content = (string)$entry->content;
			$tweet->contents = filter_var($tweet->content, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$tweet->updated = isset($entry->updated) ? (string)$entry->updated : '';
			$tweet->permalink = '';
			if (isset($entry->link[0])) { $tweet->permalink = (string)$entry->link[0]->attributes()->href; }
			$tweet->avatar = '';
			if (isset($entry->link[1])) {
				$tweet->avatar = (string)$entry->link[1]->attributes()->href;
				if ($ssl == true) {
					$tweet->avatar = str_ireplace('http://a3.', 'https://si3.', $tweet->avatar);
					$tweet->avatar = str_ireplace('http://a4.', 'https://si4.', $tweet->avatar);
				}
			}
			$tweet->author = '';
			if (isset($entry->author->name)) {
				$tuser = explode(' ', $entry->author->name);
				$tweet->author = (string)$tuser[0];
			}
			$tweet->author_uri = isset($entry->author->uri) ? (string)$entry->author->uri : '';
			$tweets[] = $tweet;
		}
		return $tweets;
	}


	/******************************/
	/* GET USER INFO FROM TWITTER */
	/******************************/
	public function getInfo($username) {
		$this->errormsg = '';
		$username  = trim($username);
		if ($username == '') {
			$this->errormsg = 'Twitter username can not be empty!';
			return false;
		}

		$xml = $this->getCURL(array('screen_name' => $username), 'http://api.twitter.com/1/users/show.xml');
		if (!$xml || ($xml == '')) {
			$this->errormsg = 'Could not fetch user information from Twitter!';
			return false;
		}

		if (version_compare(PHP_VERSION, '5.1.0') >= 0) { libxml_use_internal_errors(true); }
		$xmlDoc = simplexml_load_string($xml, 'SimpleXMLElement');
		if (!$xmlDoc) {
			$this->errormsg = 'Could not load data.';
			return false;
		}

		if (!isset($xmlDoc->name)) { return false; }

		$user = new stdClass;
		$user->name = (string)$xmlDoc->name;
		$user->screen_name = (string)$xmlDoc->screen_name;
		$user->location = (string)$xmlDoc->location;
		$user->description = (string)$xmlDoc->description;
		$user->profile_image_url = (string)$xmlDoc->profile_image_url;
		if (eFactory::getURI()->detectSSL() == true) {
			$user->profile_image_url = str_ireplace('http://a3.', 'https://si3.', $user->profile_image_url);
			$user->profile_image_url = str_ireplace('http://a4.', 'https://si4.', $user->profile_image_url);
		}
		$user->followers_count = (string)$xmlDoc->followers_count;
		$user->friends_count = (string)$xmlDoc->friends_count;
		$user->created_at = (string)$xmlDoc->created_at;
		$user->favourites_count = (string)$xmlDoc->favourites_count;
		$user->statuses_count = (string)$xmlDoc->statuses_count;
		$user->time_zone = (string)$xmlDoc->time_zone;
		$user->status_created_at = '';
		$user->status_text = '';
		if (isset($xmlDoc->status)) {
			$user->status_created_at = (string)$xmlDoc->status->created_at;
			$user->status_text = (string)$xmlDoc->status->text;
		}

		return $user;
	}


	/********************************/
	/* GET THE LAST GENERATED ERROR */
	/********************************/
	public function getError() {
		return $this->errormsg;
	}


	/********************************/
	/* QUERY TWITTER WITH FSOCKOPEN */
	/********************************/
	private function getHTTP($params=null, $url='http://search.twitter.com/search.atom') {
		$parseurl = parse_url($url);
		$getstr = '';
		if ($params) {
			$getstr = '?';
			$c = count($params);
			$i = 1;
			foreach($params as $key => $val) {
				$getstr .= urlencode($key).'='.urlencode($val);
				if ($i < $c) { $getstr .= '&'; }
				$i++;
			}
		}

		$requestheader = 'GET '.$parseurl['path'].$getstr." HTTP/1.1\r\n";
		$requestheader .= 'Host: '.$parseurl['host']."\r\n";
		$requestheader .= 'Referer: '.eFactory::getElxis()->getConfig('URL')."\r\n";
		$requestheader .= "User-Agent: Mozilla/5.0 Firefox/3.6.14\r\n";
		$requestheader .= 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' . "\r\n";
    	$requestheader .= 'Accept-Language: en-us,en;q=0.5' . "\r\n";
    	$requestheader .= 'Accept-Encoding: deflate' . "\r\n";
    	$requestheader .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7' . "\r\n";
    	$requestheader .= "Connection: Close\r\n\r\n";

		$fp = @fsockopen($parseurl['host'], 80, $errno , $errstr , 10);
		if (!$fp) { $this->errormsg = 'Could not connect to twitter!'; return false; }
		stream_set_timeout($fp, 8);
		fwrite($fp, $requestheader);
		$raw = '';
		while (!feof($fp)) {
			$raw .= fgets($fp, 1024);
    	}
		fclose($fp);
		$result = '';
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = isset($expl[1]) ? $expl[1] : '';
		}
		return $result;
	}


	/***************************/
	/* QUERY TWITTER WITH CURL */
	/***************************/
    private function getCURL($params=null, $url='http://search.twitter.com/search.atom') {
		if (!function_exists('curl_init')) {
			$result = $this->getHTTP($params);
			return $result;
		}

		$getstr = '';
		if ($params) {
			$getstr = '?';
			$c = count($params);
			$i = 1;
			foreach($params as $key => $val) {
				$getstr .= urlencode($key).'='.urlencode($val);
				if ($i < $c) { $getstr .= '&'; }
				$i++;
			}
		}

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.$getstr);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        $xml = curl_exec($ch);
        if (curl_errno($ch) == 0) {
            curl_close($ch);
            return $xml;
        } else {
        	$this->errormsg = curl_error($ch);
            curl_close($ch);
            return false;
        }
    }

}

?>