<?php 
/**
* @version		$Id: youtube.engine.php 1426 2013-05-02 19:09:47Z datahell $
* @package		Elxis
* @subpackage	Component Search
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class youtubeEngine implements searchEngine {

	private $options = array('q' => '', 'time' => 0, 'ordering' => 'r');		
	private $dosearch = false;
	private $total = 0;
	private $limit = 10;
	private $limitstart = 0;
	private $page = 1;
	private $maxpage = 1;
	private $results = array();
	private $columns = 2;
	private $safe = 'moderate';
	private $author = '';


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->limit = (int)$params->get('limit', 10);
		if ($this->limit < 1) { $this->limit = 10; }
		$this->columns = (int)$params->get('columns', 2);
		if ($this->columns < 1) { $this->columns = 2; }
		if ($this->columns > 2) { $this->columns = 2; }

		$this->safe = trim($params->get('safe', 'moderate'));
		if (($this->safe == '') || !in_array($this->safe, array('none', 'moderate', 'strict'))) {
			$this->safe = 'moderate';
		}

		$author = trim($params->get('author', ''));
		if ($author != '') {
			$pat = "#([\']|[\;]|[\&]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
			$author2 = filter_var($author, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
			$author2 = preg_replace($pat, '', $author2);
			if ($author == $author2) { $this->author = $author; }
		}

		$this->options['ordering'] = $params->get('ordering', 'r');
		if (($this->options['ordering'] == '') || !in_array($this->options['ordering'], array('r', 'dd', 'hd', 'vd'))) {
			$this->options['ordering'] = 'r';
		}

		if (ELXIS_MOBILE == 1) {
			$this->columns = 1;
		}

		$this->setOptions();
	}


	/***********************************/
	/* SET SEARCH OPTIONS FROM THE URL */
	/***********************************/
	private function setOptions() {
		$pat = "#([\']|[\;]|[\.]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
		if (isset($_GET['q'])) {
			$q = urldecode(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			$q = eUTF::trim(preg_replace($pat, '', $q));
			if (eUTF::strlen($q) > 3) { $this->options['q'] = $q; $this->dosearch = true; }			
		}

		if (isset($_GET['time'])) {
			$time = (int)$_GET['time'];
			if (in_array($time, array(0, 1, 7, 30))) {
				$this->options['time'] = $time;
				if ($time > 0) { $this->dosearch = true; }
			}
		}

		if (isset($_GET['ordering'])) {
			$ordering = trim(filter_input(INPUT_GET, 'ordering', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($ordering, array('r', 'dd', 'hd', 'vd'))) {
				$this->options['ordering'] = $ordering;
			}
		}
	}


	/**************************/
	/* GET ENGINE'S META INFO */
	/**************************/
	public function engineInfo() {
		$eLang = eFactory::getLang();
		$info = array(
			'title' => 'YouTube',
			'description' => $eLang->get('SEARCH_YOUTUBE'),
			'metakeys' => array(
				$eLang->get('SEARCH'), 
				'videos', 
				'youtube', 
				'youtube videos',
				$eLang->get('KEYWORD'),
				'OpenSearch',
				'elxis youtube search'
			)
		);
		if ($this->author != '') { $info['metakeys'][] = 'Videos by '.$this->author; }
		return $info;
	}


	/********************/
	/* MAKE SEARCH FORM */
	/********************/
	public function searchForm() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();

		$isssl = $eURI->detectSSL();
		$action = $eURI->makeURL('search:youtube.html', '', $isssl);

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmsearchytube',
			'method' => 'get',
			'action' => $action,
			'idprefix' => 'stub',
			'label_width' => 120,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2,
			'token' => false,
			'elxisbase' => false
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('SEARCH_OPTIONS'));
		$form->addText('q', $this->options['q'], $eLang->get('KEYWORD'), array('required' => 0, 'size' => 30, 'dir' => 'rtl', 'maxlength' => 80));
		if ($this->columns == 2) { $form->openRow(); }
		if (ELXIS_MOBILE == 0) {
			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('ANY_TIME'));
			$options[] = $form->makeOption(1, $eLang->get('TODAY'));
			$options[] = $form->makeOption(7, $eLang->get('THIS_WEEK'));
			$options[] = $form->makeOption(30, $eLang->get('THIS_MONTH'));
			$form->addSelect('time', $eLang->get('DATE'), $this->options['time'], $options, array('dir' => 'rtl'));
			unset($options);
		} else {
			$form->addHidden('time', 0);
		}
		$options = array();
		$options[] = $form->makeOption('r', $eLang->get('RELEVANCY'));
		$options[] = $form->makeOption('dd', $eLang->get('NEWER_FIRST'));
		$options[] = $form->makeOption('hd', $eLang->get('MOST_POPULAR_FIRST'));
		$options[] = $form->makeOption('vd', $eLang->get('RATING'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $this->options['ordering'], $options, array('dir' => 'rtl'));
		unset($options);
		if ($this->columns == 2) { $form->closeRow(); }
		if (ELXIS_MOBILE == 0) {
			$form->addButton('sbm', $eLang->get('SEARCH'), 'submit', array('class' => 'elxbutton-search', 'tip' => $eLang->get('LEAST_ONE_CRITERIA')));
		} else {
			$form->addButton('sbm', $eLang->get('SEARCH'), 'submit', array('class' => 'elxbutton-search'));
		}
		$form->closeFieldset();
		$form->render();
		unset($form);
	}


	/**************************/
	/* PROCESS SEARCH REQUEST */
	/**************************/
	public function search($page=1) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$this->total = 0;
		$this->limitstart = 0;
		$this->page = $page;
		$this->maxpage = 1;
		$this->results = array();
		if ($this->dosearch == false) { return false; }
		$results = $this->request($page);
		if ($results) {
			$this->results = $results;
			return $this->total;
		}
		return 0;
	}


	/**************************************/
	/* QUERY YOUTUBE AND GET BACK RESULTS */
	/**************************************/
	private function request($page) {
		$page = (int)$page;
		if ($page < 1) { $page = 1; }
		$start = (($page - 1) * $this->limit) + 1;

		$params = array();
		$params['v'] = 2;
		$params['alt'] = 'atom';
		$params['max-results'] = $this->limit;
		$params['start-index'] = $start;
		$params['safeSearch'] = $this->safe;
		if ($this->author != '') { $params['author'] = $this->author; }
		switch ($this->options['ordering']) {
			case 'dd': $params['orderby'] = 'published'; break;
			case 'hd': $params['orderby'] = 'viewCount'; break;
			case 'vd': $params['orderby'] = 'rating'; break;
			case 'r': default: $params['orderby'] = 'relevance'; break;
		}
		switch ($this->options['time']) {
			case 1: $params['time'] = 'today'; break;
			case 7: $params['time'] = 'this_week'; break;
			case 30: $params['time'] = 'this_month'; break;
			case 0: default: $params['time'] = 'all_time'; break;
		}
		if ($this->options['q'] != '') { $params['q'] = $this->options['q']; }

		$url = 'http://gdata.youtube.com/feeds/api/videos';
		if (function_exists('curl_init')) {
			$result = $this->curlget($url, $params);
		} else {
			$result = $this->httpget($url, $params);
		}

		if (!$result) { return false; }
		return $this->parsexmldata($result);
	}


	/*******************************/
	/* HTTP GET REQUEST USING CURL */
	/*******************************/
    private function curlget($url, $params=null) {
        $ch = curl_init();
        if ($params) {
        	curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params)); //url encodes the data
        } else {
        	curl_setopt($ch, CURLOPT_URL, $url);
        }
 
		curl_setopt($ch, CURLOPT_HEADER, 0); 
        curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = curl_exec($ch);
        if (0 == curl_errno($ch)) {
            curl_close($ch);
            return $result;
        } else {
            curl_close($ch);
            return false;
        }
    }


	/************************************/
	/* HTTP GET REQUEST USING FSOCKOPEN */
	/************************************/
	private function httpget($url, $params=null) {
		$parseurl = parse_url($url);
		$getstr = '';
		if ($params) {
			$parr = array();
			foreach($params as $key => $val) { $parr[] = $key.'='.urlencode($val); }
			$getstr = implode('&', $parr);
			unset($parr);
		}

		$req = 'GET '.$parseurl['path'].'?'.$getstr." HTTP/1.1\r\n";
		$req .= 'Host: '.$parseurl['host']."\r\n";
		$req .= "Referer: ".eFactory::getElxis()->getConfig('URL')."\r\n";
		$req .= 'User-Agent: Mozilla/5.0 Firefox/3.6.12'."\r\n";
    	$req .= 'Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,*/*;q=0.6'."\r\n";
    	$req .= 'Accept-Language: en-us,en;q=0.5'."\r\n";
    	$req .= 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7'."\r\n";
		$req .= "Connection: Close\r\n\r\n"; 

		if (!isset($parseurl['port'])) {
			$parseurl['port'] = ($parseurl['scheme'] == 'https') ? 443 : 80;
		}

		$fp = fsockopen($parseurl['host'], $parseurl['port'], $errno, $errstr, 20);
		if (!$fp) { return false; }
		stream_set_timeout($fp, 15);
		fputs($fp, $req);
		$raw = '';
		while(!feof($fp)) {
			$raw .= fgets($fp);
			$info = stream_get_meta_data($fp);
			if ($info['timed_out']) {
				fclose($fp);
				return false;
			}
		}
		fclose($fp);
		$result = '';
		$chunked = false;
		if ($raw != '') {
			$expl = preg_split("/(\r\n){2,2}/", $raw, 2);
			$result = $expl[1];
			if (preg_match('/Transfer\\-Encoding:\\s+chunked/i',$expl[0])) { $chunked = true; }
			unset($expl);
		}
		unset($raw);

		if ($chunked) {
			$result = $this->decodeChunked($result);
		}
		return $result;
	}


	/**********************/
	/* PARSE XML RESPONSE */
	/**********************/
    private function parsexmldata($result) {
        $xml = simplexml_load_string($result);
        if (false === $xml) { return false; }

		$ns = $xml->getNamespaces(true);
		if (!isset($ns['gd'])) { $ns['gd'] = 'http://schemas.google.com/g/2005'; }
		if (!isset($ns['openSearch'])) { $ns['openSearch'] = 'http://a9.com/-/spec/opensearch/1.1/'; }
		if (!isset($ns['yt'])) { $ns['yt'] = 'http://gdata.youtube.com/schemas/2007'; }
		if (!isset($ns['media'])) { $ns['media'] = 'http://search.yahoo.com/mrss/'; }
		if (!isset($ns['georss'])) { $ns['georss'] = 'http://www.georss.org/georss'; }
		if (!isset($ns['gml'])) { $ns['gd'] = 'http://www.opengis.net/gml'; }
		if (!isset($ns['gd'])) { $ns['gd'] = 'http://schemas.google.com/g/2005'; }

		$results = array();

		$open = $xml->children($ns['openSearch']);
		$this->total = (int)$open->totalResults;
		$limit = (int)$open->itemsPerPage;
		if (($limit > 0) && ($limit != $this->limit)) { $this->limit = $limit; }

		$maxpage = ($this->total == 0) ? 1 : ceil($this->total/$this->limit);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($this->page > $maxpage) { $this->page = $maxpage; }
		$this->maxpage = $maxpage;
		$this->limitstart = (($this->page - 1) * $this->limit);
		$startIndex = (int)$open->startIndex;
		if ($startIndex > $this->total) { return array(); }

		foreach ($xml->entry as $entry) {
			$date = (string)$entry->published;
			$title = (string)$entry->title;
			$author = (string)$entry->author->name;
			
			$media = $entry->children($ns['media']);

			$description = (string)$media->group->description;

			$attrs = $media->group->player->attributes();
			$url = isset($attrs['url']) ? (string)$attrs['url'] : '';

			$attrs = $media->group->thumbnail[0]->attributes();
			$thumbnail = isset($attrs['url']) ? (string)$attrs['url'] : '';

			$yt = $media->children($ns['yt']);
			$attrs = $yt->duration->attributes();
			$duration = isset($attrs['seconds']) ? (int)$attrs['seconds'] : 0; 
			$videoid = (string)$yt->videoid;

			if ($url == '') { $url = 'http://www.youtube.com/watch?v='.$videoid; }

			$yt = $entry->children($ns['yt']);
			$views = 0;
			if (isset($yt->statistics)) {
				$attrs = $yt->statistics->attributes();
				$views = isset($attrs['viewCount']) ? (int)$attrs['viewCount'] : 0; 
			}

			$gd = $entry->children($ns['gd']);
			if ($gd->rating) {
				$attrs = $gd->rating->attributes();
				$rating = isset($attrs['average']) ? (string)$attrs['average'] : 0;
			} else {
				$rating = 0;
			}
			$rating = sprintf("%02.d", $rating);

			$video = new stdClass;
			$video->videoid = $videoid;
			$video->url = $url;
			$video->thumbnail = $thumbnail;
			$video->title = $title;
			$video->description = $description;
			$video->author = $author;
			$video->duration = $duration;
			$video->time = strtotime($date);
			$video->views = $views;
			$video->rating = $rating;

			$results[] = $video;
		}

		return $results;
    }


	/***********************/
	/* SHOW SEARCH RESULTS */
	/***********************/
	public function showResults() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if ($this->dosearch == false) { return; }

		eFactory::getDocument()->addStyleLink($elxis->secureBase().'/components/com_search/engines/youtube/css/youtube.engine.css');

		if ($this->total == 0) {
			echo '<div class="elx_warning">'.$eLang->get('SEARCH_NO_RESULTS')."</div>\n";
			return;
		}

		if (ELXIS_MOBILE == 0) {
			$eDoc->loadLightbox();
			$eDoc->addDocReady('$(".resultyoutube").colorbox({iframe:true, innerWidth:425, innerHeight:344, close:\''.$eLang->get('CLOSE').'\'});');
		}
		$playimg = $elxis->secureBase().'/components/com_search/engines/youtube/css/play.png';

		$boxclass = (ELXIS_MOBILE == 1) ? 'elx_yeng_mobbox'.$eLang->getinfo('RTLSFX') : 'elx_yeng_box'.$eLang->getinfo('RTLSFX');

		echo '<div class="elx_yeng_container">'."\n";
		foreach ($this->results as $row) {
			$min = floor($row->duration/60);
			$sec = $row->duration - ($min * 60);
			$dur = sprintf("%02d", $min).':'.sprintf("%02d", $sec);
			$title = (eUTF::strlen($row->title) > 38) ? eUTF::substr($row->title, 0, 35).'...' : $row->title;
			$link = 'http://www.youtube.com/v/'.$row->videoid.'&amp;fs=1&amp;rel=0&amp;wmode=transparent'; 
			echo '<div class="'.$boxclass.'">'."\n";
			echo '<div class="elx_yeng_imgbox">'."\n";
			echo '<span class="elx_yeng_duration">'.$dur."</span>\n";
			echo '<img class="elx_yeng_play" src="'.$playimg.'" width="32" height="32" border="0" />'."\n";
			echo '<a href="'.$link.'" title="'.$row->title.'" target="_blank" class="resultyoutube">';
			echo '<img src="'.$row->thumbnail.'" alt="'.$row->title.'" border="0" width="160" height="100" /></a>'."\n";
			echo "</div>\n";
			if (ELXIS_MOBILE == 0) {
				echo '<div class="elx_yeng_notes'.$eLang->getinfo('RTLSFX').'">'."\n";
				echo '<a href="'.$link.'" title="'.$row->title.'" class="elx_yeng_title resultyoutube" target="_blank">'.$title."</a><br />\n";
				echo $eLang->get('USER').' <span>'.$row->author."</span><br />\n";
				echo $eLang->get('DATE').'	<span>'.$eDate->formatDate($row->time, $eLang->get('DATE_FORMAT_2'))."</span><br />\n";
				echo $eLang->get('HITS').' <span>'.$row->views."</span>\n";
				echo "</div>\n";
			}
			echo "</div>\n";
		}
		echo "<div style=\"clear:both;\"></div>\n";
		echo "</div>\n";
	}


	/*******************************/
	/* GET NUMBER OF TOTAL RESULTS */
	/*******************************/
	public function getTotal() {
		return $this->total;
	}


	/********************/
	/* GET SEARCH LIMIT */
	/********************/
	public function getLimit() {
		return $this->limit;
	}


	/**************************/
	/* GET SEARCH LIMIT START */
	/**************************/
	public function getLimitStart() {
		return $this->limitstart;
	}


	/***************************/
	/* GET CURRENT PAGE NUMBER */
	/***************************/
	public function getPage() {
		return $this->page;
	}


	/***************************/
	/* GET MAXIMUM PAGE NUMBER */
	/***************************/
	public function getMaxPage() {
		return $this->maxpage;
	}


	/****************************/
	/* GET SEARCH OPTIONS ARRAY */
	/****************************/
	public function getOptions() {
		return $this->options;
	}


	/******************************************/
	/* GET SEARCH SEARCH FOR THE CURRENT PAGE */
	/******************************************/
	public function getResults() {
		return $this->results;
	}


	/****************************/
	/* DECODE AN CHUNKED STRING */
	/****************************/
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