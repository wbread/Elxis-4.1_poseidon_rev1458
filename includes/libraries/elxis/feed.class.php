<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	RSS/ATOM Feed generator
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisFeed {

	private $type = 'rss';
	private $ttl = 60;
	private $copyright = 'Elxis Team';
	private $date_now_rss = '';
	private $date_now_atom = '';
	private $idx = -1; //current channel id
	private $channels = array();
	private $items = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($type='rss') {
		if ($type == 'atom') { $this->type = 'atom'; }
		$this->date_now_rss = date(DATE_RSS, time());
		$this->date_now_atom = date(DATE_ATOM, time());
		$this->copyright = 'Copyright '.date('Y').', '.eFactory::getElxis()->getConfig('SITENAME');
	}


	/*********************************/
	/* SET TIME-TO-LIVE (IN MINUTES) */
	/*********************************/
	public function setTTL($ttl=60) {
		$ttl = (int)$ttl;
		if ($ttl > 0) { $this->ttl = $ttl; }
	}


	/**********************/
	/* SET FEED COPYRIGHT */
	/**********************/
	public function setCopyright($copyright='') {
		if (trim($copyright) != '') {
			$this->copyright = $copyright;
		}
	}


	/***********************/
	/* ADD RSS 2.0 CHANNEL */
	/***********************/
	public function addChannel($title='', $link='', $description='', $image_url='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($title == '') { $title = $elxis->getConfig('SITENAME'); }
		if ($link == '') { $link = $elxis->makeURL(); }
		if ($description == '') { $description = $elxis->getConfig('METADESC'); }
		if ($image_url == '') {
			if (file_exists(ELXIS_PATH.'/media/images/logo_rss.png')) {
				$image_url = $elxis->secureBase().'/media/images/logo_rss.png';
			} else if (file_exists(ELXIS_PATH.'/templates/system/images/logo_rss.png')) {
				$image_url = $elxis->secureBase().'/templates/system/images/logo_rss.png';
			}
		}

		$this->idx++;
		$channel = new stdClass;
		$channel->title = $title;
		$channel->link = $link;
		$channel->description = $description;
		$channel->image = $image_url;
		$this->channels[ $this->idx ] = $channel;
	}


	/***********************/
	/* ADD ITEM TO CHANNEL */
	/***********************/
	public function addItem($title, $description='', $link='', $ts='', $author='', $enclosure=null) {
		$item = new stdClass;
		$item->title = $title;
		$item->description = $description;
		$item->link = $link;
		$item->author = $author;
		$item->encosure = null;
		if (is_array($enclosure) && (count($enclosure) == 3)) { //attributes: url, length, type (mime type)
			$item->encosure = $enclosure;
		} elseif (trim($enclosure) != '') {
			$enclosure = trim($enclosure, '/');
			if (file_exists(ELXIS_PATH.'/'.$enclosure)) {
				$length = filesize(ELXIS_PATH.'/'.$enclosure);
				$mime = eFactory::getFiles()->getMimetype(ELXIS_PATH.'/'.$enclosure);			
				$item->encosure = array(
					'url' => eFactory::getElxis()->secureBase().'/'.$enclosure,
					'length' => $length,
					'type' => $mime
				);
			}
		}
		$item->guid = $link;
		if ($ts == '') {
			$item->pubDate = ($this->type == 'atom') ? $this->date_now_atom : $this->date_now_rss;
		} else {
			$item->pubDate = ($this->type == 'atom') ? date(DATE_ATOM, $ts) : date(DATE_RSS, $ts);
		}
		
		$idx = $this->idx;
		if (!isset($this->items[$idx])) { $this->items[$idx] = array(); }
		$this->items[$idx][] = $item;
	}


	/*************/
	/* MAKE FEED */
	/*************/
	public function makeFeed($action='', $relpath='') {
		$xmlfeed = $this->makeHead();
		$xmlfeed .= $this->makeContent();
		$xmlfeed .= $this->makeBottom();		

		if ($action == 'get') {
			return $xmlfeed;
		} else if ($action == 'show') {
			if (@ob_get_length() > 0) { @ob_end_clean(); }
			header("Content-type: text/xml; charset=utf-8");
			echo $xmlfeed;
			exit();
		} else if ($action == 'save') {
			if ($relpath == '') { return false; }
			$eFiles = eFactory::getFiles();
			$ok = $eFiles->createFile($relpath, $xmlfeed, true, true);
			return $ok;
		} else if ($action == 'saveshow') {
			if ($relpath != '') {
				$eFiles = eFactory::getFiles();
				$eFiles->createFile($relpath, $xmlfeed, true, true);
			}
			if (@ob_get_length() > 0) { @ob_end_clean(); }
			header("Content-type: text/xml; charset=utf-8");
			echo $xmlfeed;
			exit();
		} else {
			if (@ob_get_length() > 0) { @ob_end_clean(); }
			header("Content-type: text/xml; charset=utf-8");
			echo $xmlfeed;
			exit();
		}
	}


	/******************/
	/* MAKE FEED HEAD */
	/******************/
	private function makeHead() {
		$out  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		if ($this->type == 'atom') {
			$out .= '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";
		} else {
			$out .= '<rss version="2.0">'."\n";
		}
		return $out;
	}


	/********************/
	/* MAKE FEED BOTTOM */
	/********************/
	private function makeBottom() {
		return ($this->type == 'atom') ? '</feed>' : '</rss>';
	}


	/***********************************/
	/* MAKE XML FOR CHANNELS AND ITEMS */
	/***********************************/
	private function makeContent() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if (!$this->channels) { return ''; }
		$itemtag = ($this->type == 'atom') ? 'entry' : 'item';
		$out = '';
		foreach ($this->channels as $idx => $channel) {
			if ($this->type == 'rss') { $out .= "<channel>\n"; }
			$out .= "\t".'<title><![CDATA['.$channel->title."]]></title>\n";
			if ($this->type == 'atom') {
				$out .= "\t".'<link type="text/html" href="'.htmlentities($channel->link).'" />'."\n";
				$out .= "\t".'<subtitle type="html"><![CDATA['.$channel->description."]]></subtitle>\n";
				$out .= "\t".'<rights><![CDATA['.$this->copyright."]]></rights>\n";
				$out .= "\t".'<updated>'.$this->date_now_atom."</updated>\n";
				$out .= "\t".'<id>'.$this->uuid($channel->link)."</id>\n";
				$out .= "\t".'<generator uri="http://www.elxis.org/" version="'.$elxis->getVersion().'">Elxis</generator>'."\n";
				$out .= "\t<author>\n";
				$out .= "\t\t".'<name><![CDATA['.$elxis->getConfig('MAIL_NAME')."]]></name>\n";
				$out .= "\t</author>\n";
				if (file_exists(ELXIS_PATH.'/media/images/favicon.ico')) {
					$out .= "\t<icon>".$elxis->secureBase()."/media/images/favicon.ico</icon>\n";
				} elseif (file_exists(ELXIS_PATH.'/media/images/favicon.png')) {
					$out .= "\t<icon>".$elxis->secureBase()."/media/images/favicon.png</icon>\n";
				}
				if ($channel->image != '') { $out .= "\t<logo>".$channel->image."</logo>\n"; }
			} else {
				$out .= "\t".'<link>'.htmlentities($channel->link)."</link>\n";
				$out .= "\t".'<language>'.strtolower($eLang->getinfo('LANGUAGE').'-'.$eLang->getinfo('REGION'))."</language>\n";
				$out .= "\t".'<description><![CDATA['.strip_tags($channel->description)."]]></description>\n";
				$out .= "\t".'<copyright><![CDATA['.$this->copyright."]]></copyright>\n";
				$out .= "\t".'<pubDate>'.$this->date_now_rss."</pubDate>\n";
				$out .= "\t".'<lastBuildDate>'.$this->date_now_rss."</lastBuildDate>\n";
				$out .= "\t".'<generator>Elxis '.$elxis->getVersion()."</generator>\n";
				$out .= "\t".'<ttl>'.$this->ttl."</ttl>\n";
				if ($channel->image != '') {
					$out .= "\t<image>\n";
					$out .= "\t\t<url>".$channel->image."</url>\n";
					$out .= "\t\t<title><![CDATA[".$channel->title."]]></title>\n";
					$out .= "\t\t<link>".htmlentities($channel->link)."</link>\n";
					$out .= "\t</image>\n";
				}
			}
	
			if (isset($this->items[$idx]) && (count($this->items[$idx]) > 0)) {
				foreach ($this->items[$idx] as $item) {
					$out .="\t".'<'.$itemtag.">\n";
					$out .= "\t\t".'<title><![CDATA['.$item->title."]]></title>\n";
					if ($this->type == 'atom') {
						$out .= "\t\t".'<link type="text/html" href="'.htmlentities($item->link).'" />'."\n";
						$out .= "\t\t".'<id>'.$this->uuid($item->link)."</id>\n";
               			$out .= "\t\t".'<updated>'.$item->pubDate."</updated>\n";
						$out .= "\t\t".'<summary type="html"><![CDATA['.$item->description."]]></summary>\n";
						if (trim($item->author) != '') {
							$out .= "\t\t<author>\n";
							$out .= "\t\t\t<name>".$item->author."</name>\n";
							$out .= "\t\t</author>\n";
						}
               			if (is_array($item->encosure) && isset($item->encosure['url'])) {
               				$out .= "\t\t".'<link rel="enclosure" href="'.htmlentities($item->encosure['url']).'"';
               				if (isset($item->encosure['length'])) { $out .= ' length="'.intval($item->encosure['length']).'"'; }
               				if (isset($item->encosure['type'])) { $out .= ' type="'.$item->encosure['type'].'"';}
               				$out .= ' />'."\n";
               			}
					} else {
						$out .= "\t\t".'<description><![CDATA['.$item->description."]]></description>\n";
						$out .= "\t\t".'<link>'.htmlentities($item->link)."</link>\n";
						$out .= "\t\t".'<guid isPermaLink="true">'.$item->link."</guid>\n";
               			$out .= "\t\t".'<pubDate>'.$item->pubDate."</pubDate>\n";
               			if (is_array($item->encosure) && isset($item->encosure['url'])) {
               				$out .= "\t\t".'<enclosure url="'.htmlentities($item->encosure['url']).'"';
               				if (isset($item->encosure['length'])) { $out .= ' length="'.intval($item->encosure['length']).'"'; }
               				if (isset($item->encosure['type'])) { $out .= ' type="'.$item->encosure['type'].'"';}
               				$out .= ' />'."\n";
               			}
					}
					$out .="\t".'</'.$itemtag.">\n";
				}
			}

			if ($this->type == 'rss') { $out .= "</channel>\n"; }
		}

		return $out;
	}


	/********************/
	/* GENERATE AN UUID */
	/********************/
	private function uuid($key = null) {
		$key = ($key == null) ? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);
		return 'urn:uuid:'.$uuid;
	}

}

?>