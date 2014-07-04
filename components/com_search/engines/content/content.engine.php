<?php 
/**
* @version		$Id: content.engine.php 1432 2013-05-04 16:22:57Z datahell $
* @package		Elxis
* @subpackage	Component Search
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class contentEngine implements searchEngine {

	private $options = array('q' => '', 'time' => 0, 'category' => 0, 'author' => 0, 'ordering' => 'r');	
	private $dosearch = false;
	private $total = 0;
	private $limit = 10;
	private $limitstart = 0;
	private $page = 1;
	private $maxpage = 1;
	private $results = array();
	private $columns = 2;
	private $subcategories = 0;
	private $showauthor = 1;
	private $showhits = 1;
	private $highlight = 0;
	private $year = 0; //for internal use only
	private $month = 0; //for internal use only
	private $cache_categories = false; //for internal use only


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->limit = (int)$params->get('limit', 10);
		if ($this->limit < 1) { $this->limit = 10; }
		$this->columns = (int)$params->get('columns', 2);
		if ($this->columns < 1) { $this->columns = 2; }
		if ($this->columns > 2) { $this->columns = 2; }
		$this->subcategories = (int)$params->get('subcategories', 0);
		$this->showauthor = (int)$params->get('showauthor', 1);
		$this->showhits = (int)$params->get('showhits', 1);
		$this->highlight = (int)$params->get('highlight', 0);

		$this->options['ordering'] = $params->get('ordering', 'r');
		if (($this->options['ordering'] == '') || !in_array($this->options['ordering'], array('r', 'ta', 'td', 'da', 'dd', 'hd'))) {
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

		if (isset($_GET['category'])) {
			$this->options['category'] = (int)$_GET['category'];
			if ($this->options['category'] > 0) { $this->dosearch = true; }
		}

		if (isset($_GET['author'])) {
			$this->options['author'] = (int)$_GET['author'];
			if ($this->options['author'] > 0) { $this->dosearch = true; }
		}

		if (isset($_GET['ordering'])) {
			$ordering = trim(filter_input(INPUT_GET, 'ordering', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if (in_array($ordering, array('r', 'ta', 'td', 'dd', 'da', 'hd'))) {
				$this->options['ordering'] = $ordering;
			}
		}
	}


	/**************************/
	/* GET ENGINE'S META INFO */
	/**************************/
	public function engineInfo() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$info = array(
			'title' => $eLang->get('CONTENT'),
			'description' => sprintf($eLang->get('SEARCH_CONTENT_DESC'), $elxis->getConfig('SITENAME')),
			'metakeys' => array(
				$eLang->get('SEARCH'), 
				$eLang->get('CONTENT'), 
				$eLang->get('KEYWORD'),
				$eLang->get('AUTHOR'),
				$eLang->get('CATEGORY'),
				'OpenSearch',
				'elxis content search'
			)
		);

		return $info;
	}


	/********************/
	/* MAKE SEARCH FORM */
	/********************/
	public function searchForm() {
		$eURI = eFactory::getURI();
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$isssl = $eURI->detectSSL();
		$action = $eURI->makeURL('search:content.html', '', $isssl);

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmsearchcontent',
			'method' => 'get',
			'action' => $action,
			'idprefix' => 'scon',
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

		$categories = $this->getCategories();
		if ($categories) {
			$prf = '- ';
			$sfx = '';
			if ($eLang->getinfo('DIR') == 'rtl') { $prf = ''; $sfx = ' -'; }
			$options = array();
			$options[] = $form->makeOption(0, $eLang->get('ALL_CATEGORIES'));
			foreach ($categories as $category) {
				$options[] = $form->makeOption($category['catid'], $category['title']);
				if (count($category['subcategories']) > 0) {
					foreach ($category['subcategories'] as $subcat) {
						$options[] = $form->makeOption($subcat['catid'], $prf.$subcat['title'].$sfx);
					}
				}
			}
			$form->addSelect('category', $eLang->get('CATEGORY'), $this->options['category'], $options, array('dir' => 'rtl'));
			unset($options, $prf, $sfx);
		}
		unset($categories);

		if (ELXIS_MOBILE == 0) {
			$authors = $this->getAuthors();
			if (count($authors) > 1) {
				$options = array();
				$options[] = $form->makeOption(0, $eLang->get('ALL_AUTHORS'));
				foreach ($authors as $author) {
					$txt = ($elxis->getConfig('REALNAME') == 1) ? $author['firstname'].' '.$author['lastname'] : $author['uname'];
					$options[] = $form->makeOption($author['uid'], $txt);
				}
				$form->addSelect('author', $eLang->get('AUTHOR'), $this->options['author'], $options, array('dir' => 'rtl'));
				unset($options);
			}
			unset($authors);
		}

		if ($this->columns == 2) {
			$form->closeRow();
			$form->openRow();
		}

		if (ELXIS_MOBILE == 0) {
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
			$form->addHidden('time', 0);
		}

		$options = array();
		$options[] = $form->makeOption('r', $eLang->get('RELEVANCY'));
		$options[] = $form->makeOption('hd', $eLang->get('MOST_POPULAR_FIRST'));
		$options[] = $form->makeOption('dd', $eLang->get('NEWER_FIRST'));
		$options[] = $form->makeOption('da', $eLang->get('OLDER_FIRST'));
		$options[] = $form->makeOption('ta', $eLang->get('TITLE_ASC'));
		$options[] = $form->makeOption('td', $eLang->get('TITLE_DSC'));
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

		$results = $this->searchContentItems();
		if ($results) {
			$this->total = count($results);
			if (($this->options['ordering'] == 'ta') || ($this->options['ordering'] == 'td')) {
				if (eFactory::getElxis()->getConfig('MULTILINGUISM') == 1) {
					$lng = eFactory::getURI()->getUriLang();
					if ($lng != '') { //needs to re-order results
						usort($results, array($this, 'sortResults'));
					}
				}
			}
			$this->results = $this->limitResults($results, $page);
			return $this->total;
		}
		return 0;
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


	/******************************/
	/* RE-ORDER RESULTS IF NEEDED */
	/******************************/
	private function sortResults($a, $b) {
		if ($this->options['ordering'] == 'ta') {
			$one = ($a['transtitle'] != '') ? $a['transtitle'] : $a['title'];
			$two = ($b['transtitle'] != '') ? $b['transtitle'] : $b['title'];
			return strcasecmp($one, $two);
		} else if ($this->options['ordering'] == 'td') {
			$one = ($a['transtitle'] != '') ? $a['transtitle'] : $a['title'];
			$two = ($b['transtitle'] != '') ? $b['transtitle'] : $b['title'];
			return (strcasecmp($one, $two) * -1);
		} else {
			return 0;
		}
	}


	/*********************/
	/* PERFORM DB SEARCH */
	/*********************/
	private function searchContentItems() {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();

		$lng = '';
		if ($elxis->getConfig('MULTILINGUISM') == 1) { $lng = eFactory::getURI()->getUriLang(); }
		/* the multilingual query is not tested with others than mysql dbs, so better disable 
		multilingual search to avoid SQL errors on other than mysql DBs */
		if ($db->getType() != 'mysql') { $lng = ''; }

		$pdo_binds = array();
		$pdo_binds[':lowlevel'] = array($lowlev, PDO::PARAM_INT);
		$pdo_binds[':exactlevel'] = array($exactlev, PDO::PARAM_INT);

		switch ($this->options['ordering']) {
			case 'hd': $orderby = 'a.hits DESC'; break;
			case 'ta': $orderby = 'a.title ASC'; break;
			case 'td': $orderby = 'a.title DESC'; break;
			case 'da': $orderby = 'a.created ASC'; break;
			case 'dd': $orderby = 'a.created DESC'; break;
			case 'r': default: $orderby = 'relevance DESC'; break;
		}

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

		$query_title = '';
		$query_relevance = '0 AS relevance';
		if ($this->options['q'] != '') {
			$pdo_binds[':qttl'] = array('%'.$this->options['q'].'%', PDO::PARAM_STR);

			$query_title = "\n AND ((a.title LIKE :qttl) OR (a.subtitle LIKE :qttl) OR (a.introtext LIKE :qttl) OR (a.maintext LIKE :qttl)";
			if ($lng != '') {
				$query_title .= " OR (t.translation LIKE :qttl) OR (x.translation LIKE :qttl)";
			}
			$query_title .= ")";
			$query_relevance = '((CASE WHEN a.title LIKE :qttl THEN 1 ELSE 0 END) + (CASE WHEN a.subtitle LIKE :qttl THEN 0.7 ELSE 0 END)';
			if ($lng != '') {
				$query_relevance .= ' + (CASE WHEN t.translation LIKE :qttl THEN 1 ELSE 0 END)';
				$query_relevance .= ' + (CASE WHEN x.translation LIKE :qttl THEN 0.7 ELSE 0 END)';
			}
			$query_relevance .= ') AS relevance';
		}

		$sql = "SELECT a.id, a.catid, a.title, a.seotitle, a.subtitle, a.image, a.created, a.created_by, a.created_by_name, a.hits, c.title AS category, c.seolink, ";
		if ($lng != '') {
			$pdo_binds[':trcat'] = array('com_content', PDO::PARAM_STR);
			$pdo_binds[':trlang'] = array($lng, PDO::PARAM_STR);
			$pdo_binds[':trelemt'] = array('title', PDO::PARAM_STR);
			$pdo_binds[':trelems'] = array('subtitle', PDO::PARAM_STR);
			$pdo_binds[':trelemc'] = array('category_title', PDO::PARAM_STR);
			$sql .= 't.translation AS transtitle, x.translation AS transsubtitle, z.translation as transcategory';
		} else {
			$sql .= 'NULL AS transtitle, NULL AS transsubtitle, NULL as transcategory';
		}
		$sql .= ",\n ".$query_relevance;
		$sql .= "\n FROM #__content a"
		."\n LEFT JOIN #__categories c ON c.catid=a.catid";
		if ($lng != '') {
			$sql .= "\n LEFT JOIN #__translations t ON t.elid=a.id AND t.category=:trcat AND t.language=:trlang AND t.element=:trelemt";
			$sql .= "\n LEFT JOIN #__translations x ON x.elid=a.id AND x.category=:trcat AND x.language=:trlang AND x.element=:trelems";
			$sql .= "\n LEFT JOIN #__translations z ON z.elid=a.id AND z.category=:trcat AND z.language=:trlang AND z.element=:trelemc";
		}
		$sql .= "\n WHERE a.published=1"
		."\n AND ((a.alevel <= :lowlevel) OR (a.alevel = :exactlevel))"
		.$query_time;

		if ($this->options['author'] > 0) {
			$sql .= "\n AND a.created_by = :uid";
			$pdo_binds[':uid'] = array($this->options['author'], PDO::PARAM_INT);
		}

		if ($this->options['category'] > 0) {
			$pdo_binds[':ctg'] = array($this->options['category'], PDO::PARAM_INT);
			if ($this->subcategories == 1) {
				$sql .= "\n AND ((a.catid = :ctg) OR (c.parent_id = :ctg))";
			} else {
				$sql .= "\n AND a.catid = :ctg";
			}
			$sql .= "\n AND c.published=1 AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel))";
		} else {
			$sql .= "\n AND ((c.published=1) OR (a.catid=0))"
			."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel) OR (a.catid=0))";
		}
		$sql .= $query_title
		."\n ORDER BY ".$orderby;
		$stmt = $db->prepareLimit($sql, 0, 1000);
		foreach ($pdo_binds as $key => $parr) {
			$stmt->bindParam($key, $parr[0], $parr[1]);
		}
		$stmt->execute();
		$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$articles) { return array(); }

		return $articles;
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


	/***********************/
	/* SHOW SEARCH RESULTS */
	/***********************/
	public function showResults() {
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		if ($this->dosearch == false) { return; }
		if ($this->total == 0) {
			echo '<div class="elx_warning">'.$eLang->get('SEARCH_NO_RESULTS')."</div>\n";
			return;
		}

		if ($eLang->getinfo('DIR') == 'rtl') {
			$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:110px;"';
		} else {
			$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:110px;"';
		}

		$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');
		$members_link = $elxis->makeURL('user:members/');

		foreach ($this->results as $row) {
			$link = $elxis->makeURL($row['seolink'].$row['seotitle'].'.html');
			if (($this->highlight == 1) && ($this->options['q'] != '')) { $link .= '?q='.$this->options['q']; }
			$title = ($row['transtitle'] != '') ? $row['transtitle'] : $row['title'];
			$imgbox = '';
			$imgurl = '';
			if ((trim($row['image']) != '') && file_exists(ELXIS_PATH.'/'.$row['image'])) {
				$imgfile = $elxis->secureBase().'/'.$row['image'];
				$file_info = $eFiles->getNameExtension($row['image']);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					$imgurl = $imgfile;
				}
				unset($file_info);

				$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
				$imgbox .= '<a href="'.$link.'" title="'.$title.'">';
				$imgbox .= '<img src="'.$imgfile.'" alt="'.$title.'" border="0" width="100" style="width:100px;" />'; 
				$imgbox .= "</a>\n";
				$imgbox .= "</div>\n";
			}

			if (ELXIS_MOBILE == 0) {
				echo '<div class="elx_short_box">'."\n";
				echo $imgbox;
				echo '<h3><a href="'.$link.'" title="'.$title.'">'.$title."</a></h3>\n";
				echo '<div class="elx_dateauthor">';
				echo $eDate->formatDate($row['created'], $eLang->get('DATE_FORMAT_4'));
				if ($row['catid'] > 0) {
					$clink = $elxis->makeURL($row['seolink']);
					$ctitle = ($row['transcategory'] != '') ? $row['transcategory'] : $row['category'];
					echo ' '.$eLang->get('IN').' <a href="'.$clink.'" title="'.$ctitle.'">'.$ctitle.'</a>';
				}
				if (($this->showauthor == 1) || ($this->showhits == 1)) {
					echo "<br />\n";
					$sep = '';
					if ($this->showauthor == 1) {
						$sep = ', ';
						if ($acl_profile == 2) {
							echo $eLang->get('AUTHOR').' <a href="'.$members_link.$row['created_by'].'.html" title="'.$row['created_by_name'].'">'.$row['created_by_name'].'</a>';
						} else {
							echo $eLang->get('AUTHOR').': '.$row['created_by_name'];
						}
					}
					if ($this->showhits == 1) {
						echo $sep.$eLang->get('HITS').' '.$row['hits'];
					}
					echo "\n";
				}
				echo '</div>'."\n";
				if (trim($row['subtitle']) != '') {
					$subtitle = ($row['transsubtitle'] != '') ? $row['transsubtitle'] : $row['subtitle'];
					echo '<p class="elx_content_short">'.$subtitle."</p>\n";
				}
				echo '<div style="clear:both;"></div>'."\n";
				echo "</div>\n";
			} else {
				echo '<article class="elx_short_box">'."\n";
				if ($imgurl != '') {
					echo '<figure><a href="'.$link.'" title="'.$title.'"><img src="'.$imgurl.'" alt="'.$title.'" /></a></figure>'."\n";
				}
				echo '<div class="elx_short_textbox">'."\n";
				echo '<h3><a href="'.$link.'" title="'.$title.'">'.$title.'</a></h3>'."\n";
				if (trim($row['subtitle']) != '') {
					$subtitle = ($row['transsubtitle'] != '') ? $row['transsubtitle'] : $row['subtitle'];
					echo '<p class="elx_content_short">'.$subtitle."</p>\n";
				}
				echo '<div class="elx_dateauthor">';
				echo $eDate->formatDate($row['created'], $eLang->get('DATE_FORMAT_2'));
				if ($row['catid'] > 0) {
					$clink = $elxis->makeURL($row['seolink']);
					$ctitle = ($row['transcategory'] != '') ? $row['transcategory'] : $row['category'];
					echo ' '.$eLang->get('IN').' <a href="'.$clink.'" title="'.$ctitle.'">'.$ctitle.'</a>';
				}
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</article>\n";
			}
		}
	}


	/***********************/
	/* GET CATEGORIES TREE */
	/***********************/
	private function getCategories() {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		$lng = eFactory::getLang()->currentLang();

		$file = md5('categories_list_'.$lowlev.'_'.$exactlev.'_'.$lng).'.php';
		$cache_dir = $eFiles->elxisPath('cache/', true);

		if (file_exists($cache_dir.'com_search/'.$file)) {
			$ts = filemtime($cache_dir.'com_search/'.$file);
			if ((time() - $ts) < 43200) {
				include($cache_dir.'com_search/'.$file);
				if (isset($categories) && is_array($categories)) { return $categories; }
			}
		}

		$categories = $this->getDBCategories();
		if (!$this->cache_categories || !$categories) { return $categories; }

		$buffer = '<?php '."\n";
		$buffer .= 'defined(\'_ELXIS_\') or die ();'."\n\n";
		$buffer .= '$categories = array('."\n";
		foreach ($categories as $idx => $row) {
			$buffer .= "\t".$idx.' => array('."\n";
			$buffer .= "\t\t".'\'catid\' => '.$idx.",\n";
			$buffer .= "\t\t".'\'title\' => \''.addslashes($row['title']).'\','."\n";
			$buffer .= "\t\t".'\'subcategories\' => array('."\n";
			if ($row['subcategories']) {
				foreach ($row['subcategories'] as $idx2 => $sub) {
					$buffer .= "\t\t\t".$idx2.' => array(\'catid\' => '.$idx2.', \'title\' => \''.addslashes($sub['title']).'\'),'."\n";
				}
			}
			$buffer .= "\t\t)\n";
			$buffer .= "\t),\n";
		}
		$buffer .= ");\n\n";
		$buffer .= '?>';

		if (!is_dir($cache_dir.'com_search/')) {
			$eFiles->createFolder('cache/com_search/', 0, true);
		}

		$eFiles->createFile('cache/com_search/'.$file, $buffer, true, true);
		return $categories;
	}


	/*************************************/
	/* GET CATEGORIES TREE FROM DATABASE */
	/*************************************/
	private function getDBCategories() {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();
		$lowlev = $elxis->acl()->getLowLevel();
		$exactlev = $elxis->acl()->getExactLevel();
		$this->cache_categories = false;

		$sql = "SELECT c.catid, c.title"
		."\n FROM #__categories c"
		."\n WHERE c.published = 1 AND c.parent_id = 0"
		."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel))"
		."\n ORDER BY c.ordering ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$cats) { return array(); }

		$rows = array();
		$catids = array();
		foreach ($cats as $cat) {
			$cid = (int)$cat['catid'];
			$catids[] = $cid;
			$row = array();
			$row['catid'] = $cid;
			$row['title'] = $cat['title'];
			$row['subcategories'] = array();
			$rows[$cid] = $row;
			unset($row);
		}
		unset($cats);

		$sql = "SELECT c.catid, c.parent_id, c.title"
		."\n FROM #__categories c WHERE c.published = 1 AND c.parent_id IN (".implode(", ", $catids).")"
		."\n AND ((c.alevel <= :lowlevel) OR (c.alevel = :exactlevel))"
		."\n ORDER BY c.ordering ASC";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lowlevel', $lowlev, PDO::PARAM_INT);
		$stmt->bindParam(':exactlevel', $exactlev, PDO::PARAM_INT);
		$stmt->execute();
		$subcats = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if ($subcats) {
			foreach ($subcats as $subcat) {
				$cid = (int)$subcat['catid'];
				$pid = (int)$subcat['parent_id'];
				$catids[] = $cid;
				$row = array();
				$row['catid'] = $cid;
				$row['title'] = $subcat['title'];
				$rows[$pid]['subcategories'][$cid] = $row;
				unset($row);
			}
		}
		unset($subcats);

		if ($elxis->getConfig('MULTILINGUISM') == 1) { $lng = eFactory::getURI()->getUriLang(); }
		if ($lng == '') { return $rows; }

		if (count($catids) > 20) {
			$this->cache_categories = true;
		}

		$sql = "SELECT ".$db->quoteId('elid').", ".$db->quoteId('translation')." FROM ".$db->quoteId('#__translations')
		."\n WHERE ".$db->quoteId('category')."=".$db->quote('com_content')." AND ".$db->quoteId('element')."=".$db->quote('category_title')
		."\n AND ".$db->quoteId('language')." = :lng AND ".$db->quoteId('elid')." IN (".implode(", ", $catids).")";
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':lng', $lng, PDO::PARAM_STR);
		$stmt->execute();
		$translations = $stmt->fetchPairs();
		if (!$translations) { return $rows; }

		foreach ($rows as $idx => $row) {
			$cid = $row['catid'];
			if (isset($translations[$cid])) {
				$rows[$idx]['title'] = $translations[$cid];
			}
			if (count($rows[$idx]['subcategories']) > 0) {
				foreach ($rows[$idx]['subcategories'] as $sidx => $subcat) {
					if (isset($translations[$sidx])) {
						$rows[$idx]['subcategories'][$sidx]['title'] = $translations[$sidx];
					}
				}
			}
		}

		return $rows;
	}


	/*****************************/
	/* GET AUTHORS FROM DATABASE */
	/*****************************/
	private function getAuthors() {
		$orderby = (eFactory::getElxis()->getConfig('REALNAME') == 1) ? 'u.firstname' : 'u.uname';

		$sql = "SELECT u.uid, u.firstname, u.lastname, u.uname FROM #__users u"
		."\n INNER JOIN #__content c ON c.created_by = u.uid"
		."\n GROUP BY u.uid"
		."\n ORDER BY ".$orderby." ASC";
		$stmt = eFactory::getDB()->prepare($sql);
		$stmt->execute();
		$authors = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $authors;
	}

}

?>