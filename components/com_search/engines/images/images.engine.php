<?php 
/**
* @version		$Id: images.engine.php 1429 2013-05-04 11:16:59Z datahell $
* @package		Elxis
* @subpackage	Component Search
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class imagesEngine implements searchEngine {

	private $options = array('q' => '', 'size' => '', 'time' => 0, 'type' => '', 'ordering' => 'r');		
	private $dosearch = false;
	private $total = 0;
	private $limit = 10;
	private $limitstart = 0;
	private $page = 1;
	private $maxpage = 1;
	private $results = array();
	private $columns = 2;
	private $year = 0;
	private $month = 0;
	private $search_into = 0; //0: content, 1: filesystem, 2: both
	private $search_dirs = array();


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->limit = (int)$params->get('limit', 10);
		if ($this->limit < 1) { $this->limit = 10; }
		$this->columns = (int)$params->get('columns', 2);
		if ($this->columns < 1) { $this->columns = 2; }
		if ($this->columns > 2) { $this->columns = 2; }
		$this->search_into = (int)$params->get('search_into', 0);
		if ($this->search_into < 0) { $this->search_into = 0; }
		if ($this->search_into > 2) { $this->search_into = 0; }
		if ($this->search_into > 0) {
			for ($i = 1; $i < 11; $i++) {
				$dir = trim($params->get('dir'.$i, ''));
				if (($dir != '') && ($dir != '/')) {
					$dir = trim($dir, '/').'/';
					if (file_exists(ELXIS_PATH.'/'.$dir) && is_dir(ELXIS_PATH.'/'.$dir)) {
						$this->search_dirs[] = $dir;
					}
				}
			}
		}

		$this->options['ordering'] = $params->get('ordering', 'r');
		if (($this->options['ordering'] == '') || !in_array($this->options['ordering'], array('r', 'ta', 'td', 'sa', 'sd', 'da', 'dd'))) {
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
		if (isset($_GET['size'])) {
			$size = trim(filter_input(INPUT_GET, 'size', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($size, array('xs', 's', 'm', 'l', 'xl'))) {
				$this->options['size'] = $size;
				$this->dosearch = true;
			}
		}

		if (isset($_GET['time'])) {
			$time = (int)$_GET['time'];
			if ($time > 0) {
				if ($time < 365) {
					$this->options['time'] = $time;
					$this->dosearch = true;
				} else {
					$t = (string)$time;
					if (strlen($t) == 4) {
						if (($time > 1970) && ($time <= date('Y'))) { //valid year
							$this->options['time'] = $time;
							$this->year = $time;
							$this->month = 0;
							$this->dosearch = true;
						}
					} else if (strlen($t) == 6) {
						$y = intval(substr($t, 0, 4));
						$m = intval(substr($t, -2));
						$m2 = sprintf("%02d", $m);
						if (($y > 1970) && ($m > 0) && ($m < 13) && ($y.$m2 <= date('Ym'))) { //valid year & month
							$this->year = $y;
							$this->month = $m2;
							$this->options['time'] = $y.$this->month;
							$this->dosearch = true;
						}
						unset($y, $m, $m2);
					}
				}
			}
		}

		if (isset($_GET['type'])) {
			$type = trim(filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($type, array('jpg', 'png', 'gif', 'bmp', 'svg'))) {
				$this->options['type'] = $type;
				$this->dosearch = true;
			}
		}

		if (isset($_GET['ordering'])) {
			$ordering = trim(filter_input(INPUT_GET, 'ordering', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($ordering, array('r', 'ta', 'td', 'sd', 'sa', 'dd', 'da'))) {
				$this->options['ordering'] = $ordering;
			}
		}
	}


	/********************/
	/* MAKE SEARCH FORM */
	/********************/
	public function searchForm() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();

		$isssl = $eURI->detectSSL();
		$action = $eURI->makeURL('search:images.html', '', $isssl);

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmsearchimages',
			'method' => 'get',
			'action' => $action,
			'idprefix' => 'simg',
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
			$options[] = $form->makeOption('', $eLang->get('ANY'));
			$options[] = $form->makeOption('xs', $eLang->get('EXTRA_SMALL'));
			$options[] = $form->makeOption('s', $eLang->get('SMALL'));
			$options[] = $form->makeOption('m', $eLang->get('MEDIUM'));
			$options[] = $form->makeOption('l', $eLang->get('LARGE'));
			$options[] = $form->makeOption('xl', $eLang->get('EXTRA_LARGE'));
			$form->addSelect('size', $eLang->get('SIZE'), $this->options['size'], $options, array('dir' => 'rtl'));

			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('ANY_TIME'));
			$options[] = $form->makeOption(1, $eLang->get('LAST_24_HOURS'));
			$options[] = $form->makeOption(2, $eLang->get('LAST_2_DAYS'));
			$options[] = $form->makeOption(10, $eLang->get('LAST_10_DAYS'));
			$options[] = $form->makeOption(30, $eLang->get('LAST_30_DAYS'));
			$options[] = $form->makeOption(90, $eLang->get('LAST_3_MONTHS'));
			$options[] = $form->makeOption(date('Y').date('m'), $eLang->get('THIS_MONTH'));
			$years = array();
			$end = (($this->year > 0) && ($this->year < 2010)) ? $this->year : 2010;
			for ($i = date('Y'); $i >= $end; $i--) { $years[] = $i; }
			foreach ($years as $year) {
				$txt = ($year == date('Y')) ? $eLang->get('THIS_YEAR') : $year;
				$options[] = $form->makeOption($year, $txt);
				if (($this->year == $year) && ($this->month > 0)) {
					$monthname = eFactory::getDate()->monthName($this->month);
					$options[] = $form->makeOption($year.$this->month, $monthname.' '.$year);
				}
			}
			$form->addSelect('time', $eLang->get('DATE'), $this->options['time'], $options, array('dir' => 'rtl'));
			unset($years, $end, $options);
		} else {
			$form->addHidden('size', '');
			$form->addHidden('time', 0);
		}

		if ($this->columns == 2) {
			$form->closeRow();
			$form->openRow();
		}

		if (ELXIS_MOBILE == 0) {
			$options = array();
			$options[] = $form->makeOption('', $eLang->get('ANY'));
			$options[] = $form->makeOption('jpg', 'JPG');
			$options[] = $form->makeOption('png', 'PNG');
			$options[] = $form->makeOption('gif', 'GIF');
			$options[] = $form->makeOption('bmp', 'BMP');
			//$options[] = $form->makeOption('svg', 'SVG');
			$form->addSelect('type', $eLang->get('FILETYPE'), $this->options['type'], $options, array('dir' => 'ltr'));
		} else {
			$form->addHidden('type', '');
		}

		$options = array();
		$options[] = $form->makeOption('r', $eLang->get('RELEVANCY'));
		$options[] = $form->makeOption('ta', $eLang->get('TITLE_ASC'));
		$options[] = $form->makeOption('td', $eLang->get('TITLE_DSC'));
		$options[] = $form->makeOption('sd', $eLang->get('BIGGER_FIRST'));
		$options[] = $form->makeOption('sa', $eLang->get('SMALLER_FIRST'));
		$options[] = $form->makeOption('dd', $eLang->get('NEWER_FIRST'));
		$options[] = $form->makeOption('da', $eLang->get('OLDER_FIRST'));
		$form->addSelect('ordering', $eLang->get('ORDERING'), $this->options['ordering'], $options, array('dir' => 'rtl'));
		if ($this->columns == 2) { $form->closeRow(); }
		if (ELXIS_MOBILE == 0) {
			$form->addButton('sbm', $eLang->get('SEARCH'), 'submit', array('class' => 'elxbutton-search', 'tip' => $eLang->get('LEAST_ONE_CRITERIA')));
		} else {
			$form->addButton('sbm', $eLang->get('SEARCH'), 'submit', array('class' => 'elxbutton-search'));
		}
		$form->closeFieldset();
		$form->render();
		unset($form, $options);
	}


	/**************************/
	/* GET ENGINE'S META INFO */
	/**************************/
	public function engineInfo() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$info = array(
			'title' => $eLang->get('IMAGES'),
			'description' => sprintf($eLang->get('SEARCH_IMAGES_DESC'), $elxis->getConfig('SITENAME')),
			'metakeys' => array(
				$eLang->get('SEARCH'), 
				$eLang->get('IMAGES'), 
				'PNG images',
				'JPG images',
				'GIF images',
				'image gallery',
				'OpenSearch',
				$eLang->get('KEYWORD'),
				$eLang->get('FILETYPE'),
				$eLang->get('SIZE'),
				'elxis images search'
			)
		);

		return $info;
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

		if ($this->search_into == 0) {
			$results = $this->searchContentImages();
		} else if ($this->search_into == 1) {
			$results = $this->searchFileImages();
		} else {
			$rowsc = $this->searchContentImages();
			$rowsf = $this->searchFileImages();
			//we may have double images but can be prevented by the user by choosing correct search directories.
			$results = array_merge($rowsc, $rowsf);
			unset($rowsc, $rowsf);
		}

		if ($results) {
			$this->total = count($results);
			usort($results, array($this, 'sortResults'));
			$this->results = $this->limitResults($results, $page);
			return $this->total;
		}
		return 0;
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


	/*****************************/
	/* SEARCH CONTENT FOR IMAGES */
	/*****************************/
	private function searchContentImages() {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();
		$eFiles = eFactory::getFiles();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$lng = '';
		if ($elxis->getConfig('MULTILINGUISM') == 1) { $lng = eFactory::getURI()->getUriLang(); }
		/* the multilingual query is not tested with others than mysql dbs, so better disable 
		multilingual search to avoid SQL errors on other than mysql DBs */
		if ($db->getType() != 'mysql') { $lng = ''; }

		$query_time = '';
		if ($this->options['time'] > 0) {
			if ($this->options['time'] < 365) {
				$tstart = time() - ($this->options['time'] * 24 * 3600);
				$datestart = eFactory::getDate()->formatTS($tstart, '%Y-%m-%d %H:%M:%S', false);
				$query_time = "\n AND a.created >= ".$db->quote($datestart);
			} else if ($this->year > 0) {
				if ($this->month > 0) {
					$query_time = "\n AND a.created LIKE ".$db->quote($this->year.'-'.$this->month.'%');
				} else {
					$query_time = "\n AND a.created LIKE ".$db->quote($this->year.'%');
				}
			}
		}

		if ($this->options['type'] != '') {
			$query_image = "\n AND a.image LIKE '%.".$this->options['type']."'";
		} else {
			$query_image = "\n AND a.image IS NOT NULL";
		}

		$query_title = '';
		if ($this->options['q'] != '') {
			$query_title = "\n AND ((a.title LIKE :qttl) OR (a.caption LIKE :qttl)";
			if ($lng != '') {
				$query_title .= " OR (t.translation LIKE :qttl) OR (x.translation LIKE :qttl)";
			}
			$query_title .= ")";
		}

		if ($this->options['ordering'] == 'ta') {
			$orderby = 'a.title ASC';
		} else if ($this->options['ordering'] == 'td') {
			$orderby = 'a.title DESC';
		} else if ($this->options['ordering'] == 'da') {
			$orderby = 'a.created ASC';
		} else {
			$orderby = 'a.created DESC';
		}

		$sql = "SELECT a.id, a.title, a.caption, a.created, a.image, c.catid, ";
		if ($lng != '') {
			$sql .= 't.translation AS transtitle, x.translation AS transcaption';
		} else {
			$sql .= 'NULL AS transtitle, NULL AS transcaption';
		}
		$sql .= "\n FROM #__content a"
		."\n LEFT JOIN #__categories c ON c.catid=a.catid";
		if ($lng != '') {
			$sql .= "\n LEFT JOIN #__translations t ON t.elid=a.id AND t.category=:trcat AND t.language=:trlang AND t.element=:trelemt";
			$sql .= "\n LEFT JOIN #__translations x ON x.elid=a.id AND x.category=:trcat AND x.language=:trlang AND x.element=:trelemc";
		}
		$sql .= "\n WHERE a.published=1"
		."\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		.$query_time
		.$query_image
		.$query_title
		."\n AND ((c.published=1) OR (a.catid=0))"
		."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel) OR (a.catid=0))"
		."\n ORDER BY ".$orderby;

		$stmt = $db->prepareLimit($sql, 0, 500);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		if ($this->options['q'] != '') {
			$qs = '%'.$this->options['q'].'%';
			$stmt->bindParam(':qttl', $qs, PDO::PARAM_STR);
		}
		if ($lng != '') {
			$trcat = 'com_content';
			$trelemt = 'title';
			$trelemc = 'caption';
			$stmt->bindParam(':trcat', $trcat, PDO::PARAM_STR);
			$stmt->bindParam(':trlang', $lng, PDO::PARAM_STR);
			$stmt->bindParam(':trelemt', $trelemt, PDO::PARAM_STR);
			$stmt->bindParam(':trelemc', $trelemc, PDO::PARAM_STR);
		}
		$stmt->execute();
		$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$articles) { return array(); }

		$rows = array();
		$qtlen = 0;
		if ($this->options['q'] != '') {
			$qtlen = eUTF::strlen($this->options['q']);
		}

		foreach ($articles as $article) {
			$relevancy = 0;
			if (!file_exists(ELXIS_PATH.'/'.$article['image'])) { continue; }
			if (trim($article['transcaption']) != '') {
				$title = $article['transcaption'];
			} else if (trim($article['transtitle']) != '') {
				$title = $article['transtitle'];
			} else if (trim($article['caption']) != '') {
				$title = $article['caption'];
			} else {
				$title = $article['title'];
			}

			if ($this->options['q'] != '') {
				$trel = (100 - (eUTF::strlen($title) - $qtlen));
				if ($trel > 0) { $relevancy += $trel; }
			}

			$file_info = $eFiles->getNameExtension($article['image']);
			if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
				$thumb = $file_info['name'].'_thumb.'.$file_info['extension'];
			} else {
				$thumb = $article['image'];
			}

			$image = $article['image'];
			if ($this->options['size'] != '') {
				$relevancy += 4;
				if (($this->options['size'] == 'xs') || ($this->options['size'] == 's')) {
					if (!file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						continue;
					}
					$image = $file_info['name'].'_thumb.'.$file_info['extension'];
				} else if ($this->options['size'] == 'm') {
					if (!file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
						continue;
					}
					$image = $file_info['name'].'_medium.'.$file_info['extension'];
				} else if ($this->options['size'] == 'l') {
					$image = $article['image'];
				} else { //xl
					$image = $article['image'];
					if (filesize(ELXIS_PATH.'/'.$image) < 122880) { continue; }
				}
			}

			if ($this->options['time'] != 0) {
				$relevancy += 2;
			}

			$fsize = getimagesize(ELXIS_PATH.'/'.$image);

			$img = new stdClass;
			$img->image = $image;
			$img->thumbnail = $thumb;
			$img->title = $title;
			$img->size = filesize(ELXIS_PATH.'/'.$image);
			$img->type = $file_info['extension'];
			$img->width = $fsize[0];
			$img->height = $fsize[1];
			$img->time = strtotime($article['created']);
			$img->relevancy = $relevancy;
			$rows[] = $img;
		}

		return $rows;
	}


	/*****************************************/
	/* SEARCH FILESYSTEM FOR MATHCING IMAGES */
	/*****************************************/
	private function searchFileImages() {
		if ($this->dosearch === false) { return array(); }
		if (count($this->search_dirs) == 0) { return array(); }

		clearstatcache();

		$irelevancy = 0;
		if ($this->options['type'] != '') {
			$pattype = '*.'.$this->options['type'];
			$irelevancy += 5;
		} else {
			$pattype = '*.jpg,*.gif,*.png,*.bmp,*.jpeg';
		}

		$patdirs = implode(',',$this->search_dirs);
		$images = glob("{".$patdirs."}{".$pattype."}", GLOB_BRACE);
		if (!$images) { return array(); }

		if ($this->options['time'] > 0) {
			$tstart = time() - ($this->options['time'] * 24 * 3600);
		} else if ($this->options['time'] == -1 ) {
			$tstart = mktime(0, 0, 0, date('m'), 1, date('Y'));
		} else if ($this->options['time'] == -2 ) {
			$tstart = mktime(0, 0, 0, 1, 1, date('Y'));
		} else {
			$tstart = 0;
		}

		$tstart = 0;
		$tend = 0;
		if ($this->options['time'] > 0) {
			if ($this->options['time'] < 365) {
				$tstart = time() - ($this->options['time'] * 24 * 3600);
				$tend = time();
			} else if ($this->year > 0) {
				if ($this->month > 0) {
					$ts = gmmktime(12, 0, 0, $this->month, 15, $this->year);
					$days = date('t', $ts);
					$tstart = gmmktime(0, 0, 0, $this->month, 1, $this->year);
					$tend = gmmktime(23, 59, 59, $this->month, $days, $this->year);
				} else {
					$tstart = gmmktime(0, 0, 0, 1, 1, $this->year);
					$tend = gmmktime(23, 59, 59, 12, 31, $this->year);
				}
			}
		}

		$rows = array();
		
		$qt = '';
		$qtlen = 0;
		if ($this->options['q'] != '') {
			$qt = eUTF::utf8_to_ascii($this->options['q']);
			$qt = str_replace(' ', '_', $qt);
			$qt = str_replace('-', '_', $qt);
			$qtlen = strlen($qt);
		}

		foreach ($images as $image) {
			$relevancy = $irelevancy;

			$time = filemtime(ELXIS_PATH.'/'.$image);
			if ($tstart > 0) {
				if (($time < $tstart) || ($time > $tend)) { continue; }
				$relevancy += 2;
			}

			$size = filesize(ELXIS_PATH.'/'.$image);
			if ($this->options['size'] != '') {
				$relevancy += 4;
				if ($size < 5120) { //5kb
					$isize = 'xs';
				} else if ($size < 15360) { //15kb
					$isize = 's';
				} else if ($size < 61440) { //60kb
					$isize = 'm';
				} else if ($size < 122880) { //120kb
					$isize = 'l';
				} else {
					$isize = 'xl';
				}

				if ($this->options['size'] != $isize) { continue; }
			}

			$fsize = getimagesize(ELXIS_PATH.'/'.$image);
			$ext = $this->getImageType($fsize[2]);

			$p = strrpos($image, '/') + 1;
			$title = substr($image, $p);
			$title = preg_replace('/(.'.$ext.')$/', '', $title);
			$title = str_replace('-', '_', $title);
			if ($qt != '') {
				if (stripos($title, $qt) === false) { continue; }
				$relevancy += (100 - (strlen($title) - $qtlen));
			}
			$title = str_replace('_', ' ', $title);
			$title = str_replace('.', ' ', $title);

			$img = new stdClass;
			$img->image = $image;
			$img->thumbnail = $image;
			$img->title = $title;
			$img->size = $size;
			$img->type = $ext;
			$img->width = $fsize[0];
			$img->height = $fsize[1];
			$img->time = $time;
			$img->relevancy = $relevancy;
			$rows[] = $img;
		}

		return $rows;
	}


	/************************/
	/* LIMIT SEARCH RESULTS */
	/************************/
	private function limitResults($rows, $page=1) {
		$this->total = count($rows);
		if ($page < 1) { $page = 1; }
		$this->limitstart = 0;
		$maxpage = ($this->total == 0) ? 1 : ceil($this->total/$this->limit);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($page > $maxpage) { $page = $maxpage; }
		$this->limitstart = (($page - 1) * $this->limit);

		$this->page = $page;
		$this->maxpage = $maxpage;
		if ($this->total <= $this->limit) { return $rows; }

		$page_rows = array();
		$end = $this->limitstart + $this->limit;
		foreach ($rows as $key => $row) {
			if ($key < $this->limitstart) { continue; }
			if ($key >= $end) { break; }
			$page_rows[] = $row;
		}
		return $page_rows;
	}


	/***********************/
	/* SHOW SEARCH RESULTS */
	/***********************/
	public function showResults() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_search/engines/images/images.engine.css');
		
		if ($this->dosearch == false) { return; }
		if ($this->total == 0) {
			echo '<div class="elx_warning">'.$eLang->get('SEARCH_NO_RESULTS')."</div>\n";
			return;
		}

		if (ELXIS_MOBILE == 0) {
			$eDoc->loadLightbox();
			$eDoc->addDocReady('$(".resultimage").colorbox({rel:\'resultimage\', current:\''.$eLang->get('IMAGE').' {current} '.$eLang->get('OF').' {total}\', next:\''.$eLang->get('NEXT').'\', previous:\''.$eLang->get('PREVIOUS').'\', close:\''.$eLang->get('CLOSE').'\'});');
		}

		echo '<div class="elx_ieng_container">'."\n";
		foreach ($this->results as $row) {
			if (ELXIS_MOBILE == 1) {
				echo '<figure class="elx_ieng_fig"><a href="'.$elxis->secureBase().'/'.$row->image.'">';
				echo '<img src="'.$elxis->secureBase().'/'.$row->image.'" alt="'.$row->title.'" title="'.$row->title.'" />';
				echo "</a></figure>\n";
				continue;
			}

			$w = ($row->width > 100) ? 100 : $row->width;
			$h = ($row->height > 100) ? 100 : $row->height;
			if ($row->width > $row->height) {
				$h = intval(($w * $row->height) / $row->width);
			} else {
				$w = intval(($h * $row->width) / $row->height);
			}

			echo '<div class="elx_ieng_box'.$eLang->getinfo('RTLSFX').'">'."\n";
			echo '<div class="elx_ieng_imgbox">'."\n";
			echo '<a href="'.$elxis->secureBase().'/'.$row->image.'" class="resultimage">';
			echo '<img src="'.$elxis->secureBase().'/'.$row->image.'" alt="'.$row->title.'" border="0" width="'.$w.'" height="'.$h.'" title="'.$row->title.'" />';
			echo "</a>\n";
			echo "</div>\n";
			echo '<div class="elx_ieng_notes">'."\n";
			echo $row->width.'x'.$row->height.' - '.$this->formatFilesize($row->size)."<br />\n";
			echo $eDate->formatTS($row->time, $eLang->get('DATE_FORMAT_4'));
			echo "</div>\n";
			echo "</div>\n";
		}
		echo "<div style=\"clear:both;\"></div>\n";
		echo "</div>\n";
	}


	/*******************/
	/* FORMAT FILESIZE */
	/*******************/
	private function formatFilesize($size) {
		if ($size < 400000) {
			$r = ($size / 1024);
			return number_format($r, 1, '.', '').'kb';
		} else {
			$r = ($size / (1024 * 1024));
			return number_format($r, 2, '.', '').'mb';
		}
	}


	/*********************/
	/* ORDER IMAGE FILES */
	/*********************/
	private function sortResults($a, $b) {
		if ($this->options['ordering'] == 'da') {
			if ($a->time == $b->time) { return 0; }
			return ($a->time < $b->time) ? -1 : 1;
		} else if ($this->options['ordering'] == 'dd') {
			if ($a->time == $b->time) { return 0; }
			return ($a->time < $b->time) ? 1 : -1;
		} else if ($this->options['ordering'] == 'sa') {
			if ($a->size == $b->size) { return 0; }
			return ($a->size < $b->size) ? -1 : 1;
		} else if ($this->options['ordering'] == 'sd') {
			if ($a->size == $b->size) { return 0; }
			return ($a->size < $b->size) ? 1 : -1;
		} else if ($this->options['ordering'] == 'ta') {
			return strcasecmp($a->title, $b->title);
		} else if ($this->options['ordering'] == 'td') {
			return (strcasecmp($a->title, $b->title) * -1);
		} else if ($this->options['ordering'] == 'r') {
			if ($a->relevancy == $b->relevancy) { return 0; }
			return ($a->relevancy < $b->relevancy) ? 1 : -1;
		} else {
			return 0;
		}
	}


	/******************/
	/* GET IMAGE TYPE */
	/******************/
	private function getImageType($type=0) {
		switch($type) {
			case 1: return 'gif'; break;
			case 2: return 'jpg'; break;
			case 3: return 'png'; break;
			case 4: return 'swf'; break;
			case 5: return 'psd'; break;
			case 6: return 'bmp'; break;
			case 7: return 'tiff'; break; //intel
			case 8: return 'tiff'; break; //motorola
			case 9: return 'jpc'; break;
			case 10: return 'jp2'; break;
			case 11: return 'jpx'; break;
			case 12: return 'jb2'; break;
			case 13: return 'swc'; break;
			case 14: return 'iff'; break;
			case 15: return 'wbmp'; break;
			case 16: return 'xbm'; break;
			default: return 'unknown'; break;
		}
	}

}

?>