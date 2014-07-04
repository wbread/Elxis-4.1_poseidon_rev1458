<?php 
/**
* @version		$Id: parser.class.php 1368 2012-12-05 19:40:42Z datahell $
* @package		Elxis
* @subpackage	RSS/ATOM feed parser
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisParser {

	private $ttl = 7200;
	private $type = ''; //rss or atom
	private $feed = null;
	private $errormsg = '';


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/    
	public function __construct() {
	} 


	/********************************/
	/* SET TIME TO LIVE CACHED FEED */
	/********************************/
	public function setTTL($seconds=7200) {
		$seconds = (int)$seconds;
		if ($seconds > 0) { $this->ttl = $seconds; }
	}


	/*******************/
	/* GET REMOTE FEED */
	/*******************/
	public function getFeed($url, $cache=true, $get_xml_string=false) {
		$this->errormsg = '';
		$id = md5($url);

		if ($cache) {
			$eCache = eFactory::getCache();
			$status = $eCache->begin('feed', $id, 'feeds', $this->ttl, false, true, 'xml');
			if ($status == 1) {
				$xmldata = $eCache->fetchContents();
				if ($get_xml_string === true) { return $xmldata; }
				$ok = $this->parseXML($xmldata);
				if (!$ok) { //wrong saved feed, try to fetch it again
					$this->errormsg = '';
					$eCache->clearItems('feeds', $id);
					$cache = false;
				} else {
					return $this->feed;
				}
			}
		}

		if (function_exists('curl_init')) {
			$xmldata = $this->curlget($url);
		} else {
			$xmldata = $this->httpget($url);
		}

		if (($xmldata === false) || (trim($xmldata) == '')) {
			if ($this->errormsg == '') { $this->errormsg = 'Could not fetch the feed from '.$url; }
			return false;
		}

		if (!$get_xml_string) {
			$ok = $this->parseXML($xmldata);
			if (!$ok) {
				if ($this->errormsg == '') { $this->errormsg = 'Could not parse XML feed!'; }
				return false;
			}
		}

		if ($cache) {
			if ($status == 2) { $eCache->store($xmldata); }
		}

		return $get_xml_string ? $xmldata : $this->feed;
    }


	/*******************************/
	/* PARSE AN XML STRING OR FILE */
	/*******************************/
    public function parseXML($xmldata='', $xmlfile='') {
    	$this->errormsg = '';
    	$this->type = '';
    	$this->feed = null;
		if (($xmldata == '') && ($xmlfile == '')) {
			$this->errormsg = 'You must provide an XML string or the absolute path to an XML file!';
			return false;
		}

		libxml_use_internal_errors(true);
		if ($xmldata != '') {
			$xml = simplexml_load_string($xmldata, 'SimpleXMLElement');
		} else {
			if (!file_exists($xmlfile)) {
				$this->errormsg = 'Provided XML file does not exist!';
				return false;
			}
			$xml = simplexml_load_file($xmlfile, 'SimpleXMLElement');
		}

		if (!$xml) {
			foreach (libxml_get_errors() as $error) {
				$this->errormsg = 'Could not parse XML file. Error: '.$error->message.'. Line: '.$error->line;
				break;
			}
			return false;
    	}

		$type = $xml->getName();
		if ($type == 'feed') { $type = 'atom'; }
		if (!in_array($type, array('rss', 'atom'))) {
			$this->errormsg = 'Not supported feed type '.$type;
			return false;
		}

		$this->feed = new stdClass();
		$this->feed->type = $type;

		if ($this->feed->type == 'rss') {
			if (!isset($xml->channel)) {
				$this->feed = null;
				$this->errormsg = 'Invalid RSS feed!';
				return false;
			}

			$this->feed->title = (string)$xml->channel->title;
			$this->feed->description = (string)$xml->channel->description;
			$this->feed->link = (string)$xml->channel->link;
			$this->feed->date = (isset($xml->channel->lastBuildDate)) ? (string)$xml->channel->lastBuildDate : '';
			if ($this->feed->date == '') {
				$this->feed->date = (isset($xml->channel->pubDate)) ? (string)$xml->channel->pubDate : '';
			}
			$this->feed->copyright = (isset($xml->channel->copyright)) ? (string)$xml->channel->copyright : '';
			$this->feed->generator = (isset($xml->channel->generator)) ? (string)$xml->channel->generator : '';
			$this->feed->image = '';
			if (isset($xml->channel->image)) {
				$this->feed->image = (isset($xml->channel->image->url)) ? (string)$xml->channel->image->url : '';
			}
			$this->feed->items = array();

			if (isset($xml->channel->item)) {
				foreach ($xml->channel->item as $item) {
					$iObj = new stdClass;
					$iObj->title = (string)$item->title;
					$iObj->link = (string)$item->link;
					$iObj->date = (isset($item->pubDate)) ? (string)$item->pubDate : '';
					$iObj->description = (isset($item->description)) ? (string)$item->description : '';
					$this->feed->items[] = $iObj;
					unset($iObj);
				}
			}

			return true;
		}

		if ($this->feed->type == 'atom') {
			$this->feed->title = (string)$xml->title;
			$this->feed->description = (isset($xml->subtitle)) ? (string)$xml->subtitle : '';
			//$this->feed->id = (string)$xml->id;
			$this->feed->link = $this->getAtomLink($xml);
			$this->feed->date = (isset($xml->updated)) ? (string)$xml->updated : '';
			$this->feed->copyright = (isset($xml->rights)) ? (string)$xml->rights : '';
			$this->feed->generator = (isset($xml->generator)) ? (string)$xml->generator : '';
			$this->feed->image = (isset($xml->logo)) ? (string)$xml->logo : '';
			if ($this->feed->image == '') {
				$this->feed->image = (isset($xml->icon)) ? (string)$xml->icon : '';
			}
			$this->feed->items = array();

			if (isset($xml->entry)) {
				foreach ($xml->entry as $item) {
					$iObj = new stdClass;
					$iObj->title = (string)$item->title;
					$iObj->link = $this->getAtomLink($item);
					$iObj->date = (isset($item->published)) ? (string)$item->published : '';
					if ($iObj->date == '') {
						$iObj->date = (isset($item->updated)) ? (string)$item->updated : '';
					}
					$iObj->description = (isset($item->content)) ? (string)$item->content : '';
					if ($iObj->description == '') {
						$iObj->description = (isset($item->summary)) ? (string)$item->summary : '';
					}
					$this->feed->items[] = $iObj;
					unset($iObj);
				}
			}

			return true;
		}

		$this->feed = null;
		$this->errormsg = 'Not supported feed type!';
		return false;
    }


	/*****************************/
	/* GET PARSED FEED AS OBJECT */
	/*****************************/
	public function getParsed() {
		return $this->feed;
	}


	/*******************/
	/* ERROR GENERATOR */
	/*******************/    
	public function getError() {
		return $this->errormsg;
	}


	/***************************************/
	/* GET THE HTML LINK FROM AN ATOM ITEM */
	/***************************************/
	private function getAtomLink($xmlitem) {
		$htmllink = '';
		if (!isset($xmlitem->link)) { return ''; }
		if (is_array($xmlitem->link)) {
			foreach($xmlitem->link as $v) {
				$attr = $v->attributes();
				if (!$attr) { continue; }
				if (!isset($attr['href'])) { continue; }
				if (isset($attr['rel']) && ($attr['rel'] == 'self')) { continue; }
				if (isset($attr['type']) && ($attr['type'] == 'text/html')) {
					$htmllink = trim($attr['href']);
					break;
				}
				if ($htmllink == '') {
					$htmllink = trim($attr['href']); //add this and continue for a better one
				}
			}

			return $htmllink;
		}

		$attr = $xmlitem->link->attributes();
		if (!$attr) { return ''; }
		if (!isset($attr['href'])) { return ''; }
		$rel = (isset($attr['rel'])) ? (string)$attr['rel'] : '';
		if ($rel != 'self') { $htmllink = trim($attr['href']); }

		return $htmllink;
	}


	/****************/
	/* GET FEED TYPE */
	/****************/    
	public function getType() {
		return ($this->feed) ? $this->feed->type : null;
	}


	/*******************************/
	/* HTTP GET REQUEST USING CURL */
	/*******************************/
    private function curlget($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$xmldata = curl_exec($ch);
		if (curl_errno($ch) == 0) {
			curl_close($ch);
			return $xmldata;
		} else {
			$this->errormsg = curl_error($ch);
			curl_close($ch);
			return false;
		}
	}


	/************************************/
	/* HTTP GET REQUEST USING FSOCKOPEN */
	/************************************/
	private function httpget($url) {
		$parseurl = parse_url($url);
		if (!$parseurl) {
			$this->errormsg = 'Given feed URL is invalid!';
			return false;
		}

		$getstr = '';
		if (isset($parseurl['query']) && ($parseurl['query'] != '')) {
			$getstr = '?'.$parseurl['query']; //urlencode?
		}

		$req = 'GET '.$parseurl['path'].$getstr." HTTP/1.1\r\n";
		$req .= 'Host: '.$parseurl['host']."\r\n";
		$req .= "Referer: ".eFactory::getElxis()->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12'."\r\n";
    	$req .= 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.6'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Encoding: deflate'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n"; 

		if (!isset($parseurl['port'])) {
			$parseurl['port'] = ($parseurl['scheme'] == 'https') ? 443 : 80;
		}

		$fp = fsockopen($parseurl['host'], $parseurl['port'], $errno, $errstr, 15);
		if (!$fp) {
			$this->errormsg = 'Could not access feed '.$url;
			return false;
		}
		stream_set_timeout($fp, 10);
		fputs($fp, $req);
		$raw = '';
		while(!feof($fp)) {
			$raw .= fgets($fp);
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				fclose($fp);
				$this->errormsg = 'Connection time out!';
				return false;
			}
		}
		fclose($fp);
		$result = '';
		$chunked = false;
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = isset($expl[1]) ? $expl[1] : '';
			if (preg_match('/Transfer\\-Encoding:\\s+chunked/i',$expl[0])) { $chunked = true; }
			unset($expl);
		}
		unset($raw);

		if ($chunked) {
			$result = $this->decodeChunked($result);
		}
		return $result;
	}
    
    
	/***************************/
	/* DECODE A CHUNKED STRING */
	/***************************/
	private function decodeChunked($chunk) {
		if (function_exists('http_chunked_decode')) {
			return http_chunked_decode($chunk);
		}

		$pos = 0;
		$len = strlen($chunk);
		$dechunk = null;
		while(($pos < $len) && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos))) {
            if (!$this->is_hex($chunkLenHex)) { return $chunk; }
			$pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
        }

		return $dechunk;
	}   
    
    
	/****************************/
	/* IS STRING A HEX NUMBER ? */
	/****************************/
	private function is_hex($hex) {
		$hex = strtolower(trim(ltrim($hex,"0")));
		if (empty($hex)) { $hex = 0; };
		$dec = hexdec($hex);
		return ($hex == dechex($dec));
	}    

}
    
?>