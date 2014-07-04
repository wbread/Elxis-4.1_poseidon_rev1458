<?php 
/**
* @version		$Id: statistics.php 1130 2012-05-13 18:18:35Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class statisticsCPController extends cpanelController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null) {
		parent::__construct($view, $model);
	}


	/***************************/
	/* PREPARE SITE STATISTICS */
	/***************************/
	public function showstats() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();

		if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') < 1) {
			$url = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($url, $eLang->get('NOTALLOWACCPAGE'), true);
		}

		$year = date('Y');
		$month = date('n');
		$yearstats = false;

		if (isset($_GET['dt'])) {
			$dt = trim($_GET['dt']);
			if (is_numeric($dt)) {
				if (strlen($dt) == 6) {
					$y = intval(substr($dt, 0, 4));
					$m = intval(substr($dt, -2));
					if (($m > 0) && ($m < 13)) { $month = $m; }
					if (($y > 2010) && ($y <= $year)) { $year = $y; }
				} elseif (strlen($dt) == 4) {
					$y = (int)$dt;
					if (($y > 2010) && ($y <= $year)) {
						$year = $y;
						$yearstats = true;
					}
				}
			}
		}

		if ($yearstats) {
			$data = $this->collectYearStats($year);
		} else {
			$data = $this->collectMonthStats($year, $month);
		}

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_cpanel/css/cp.css');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_cpanel/js/cpanel.js');

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('STATISTICS'), 'cpanel:stats/');
		if ($yearstats) {
			$pathway->addNode($year);
			$eDoc->setTitle($eLang->get('STATISTICS').' - '.$year);
		} else {
			$pathway->addNode($year, 'cpanel:stats/?dt='.$year);
			$pathway->addNode($data['monthname']);
			$eDoc->setTitle($eLang->get('STATISTICS').' - '.$data['monthname'].' '.$year);
		}

		$this->view->graphs($data, $yearstats);
	}


	/******************************************/
	/* COLLECT STATISTICS FOR THE GIVEN MONTH */
	/******************************************/
	private function collectMonthStats($year, $month) {
		$ts = mktime(12, 0, 0, $month, 15, $year);
		$daysnum = date('t', $ts);
		$mname = eFactory::getDate()->monthName($month);

		$data = array(
			'year' => $year,
			'month' => $month,
			'monthname' => $mname,
			'daysnum' => $daysnum,
			'visits' => array(
				'total' => 0,
				'yaxis' => array(0, 1, 2, 3, 4, 5),
				'ymax' => 5,
				'stats' => array()
			),
			'clicks' => array(
				'total' => 0,
				'yaxis' => array(0, 1, 2, 3, 4, 5),
				'ymax' => 5,
				'stats' => array()
			),
			'langs' => array(
				'total' => 0,
				'stats' => array()
			)
		);

		for ($i=1; $i <= $daysnum; $i++) {
			$data['visits']['stats'][$i] = array('num' => 0, 'pc' => 0);
			$data['clicks']['stats'][$i] = array('num' => 0, 'pc' => 0);
		}

		$rows = $this->model->getStatistics($year, $month);
		if (!$rows) { return $data; }

		$maxv = 0;
		$maxc = 0;
		foreach ($rows as $row) {
			$day = (int)substr($row['statdate'], -2);
			$numv = $row['visits'];
			$numc = $row['clicks'];

			if ($numv > $maxv) { $maxv = $numv; }
			if ($numc > $maxc) { $maxc = $numc; }

			$data['visits']['stats'][$day]['num'] = $numv;
			$data['visits']['total'] += $numv;
			$data['clicks']['stats'][$day]['num'] = $numc;
			$data['clicks']['total'] += $numc;
			
			$alngs = unserialize($row['langs']);
			if (is_array($alngs) && (count($alngs) > 0)) {
				foreach ($alngs as $lng => $clicks) {
					$data['langs']['total'] += $clicks;
					if (!isset($data['langs']['stats'][$lng])) {
						$data['langs']['stats'][$lng] = array('num' => $clicks, 'pc' => 0);
					} else {
						$data['langs']['stats'][$lng]['num'] += (int)$clicks;
					}
				}
				foreach ($data['langs']['stats'] as $lng => $stat) {
					$data['langs']['stats'][$lng]['pc'] = round((($stat['num'] * 100) / $data['langs']['total']), 1);
				}
			}
		}

		$yv = $this->makeYaxis($maxv);
		$yc = $this->makeYaxis($maxc);
		$data['visits']['yaxis'] = $yv['yaxis'];
		$data['visits']['ymax'] = $yv['ymax'];
		$data['clicks']['yaxis'] = $yc['yaxis'];
		$data['clicks']['ymax'] = $yc['ymax'];
		unset($yv, $yc);

		if ($data['visits']['total'] > 0) {
			foreach ($data['visits']['stats'] as $day => $stat) {
				$data['visits']['stats'][$day]['pc'] = floor(($stat['num'] * 100) / $data['visits']['ymax']);
			}
		}

		if ($data['clicks']['total'] > 0) {
			foreach ($data['clicks']['stats'] as $day => $stat) {
				$data['clicks']['stats'][$day]['pc'] = floor(($stat['num'] * 100) / $data['clicks']['ymax']);
			}
		}

		return $data;
	}


	/*****************************************/
	/* COLLECT STATISTICS FOR THE GIVEN YEAR */
	/*****************************************/
	private function collectYearStats($year) {
		$data = array(
			'year' => $year,
			'visits' => array(
				'total' => 0,
				'yaxis' => array(0, 1, 2, 3, 4, 5),
				'ymax' => 5,
				'stats' => array()
			),
			'clicks' => array(
				'total' => 0,
				'yaxis' => array(0, 1, 2, 3, 4, 5),
				'ymax' => 5,
				'stats' => array()
			),
			'langs' => array(
				'total' => 0,
				'stats' => array()
			)
		);

		for ($i=1; $i <= 12; $i++) {
			$data['visits']['stats'][$i] = array('num' => 0, 'pc' => 0);
			$data['clicks']['stats'][$i] = array('num' => 0, 'pc' => 0);
		}

		$rows = $this->model->getStatistics($year, 0);
		if (!$rows) { return $data; }

		foreach ($rows as $row) {
			$month = (int)substr($row['statdate'], 5, 2);
			$numv = $row['visits'];
			$numc = $row['clicks'];
			$data['visits']['stats'][$month]['num'] += $numv;
			$data['visits']['total'] += $numv;
			$data['clicks']['stats'][$month]['num'] += $numc;
			$data['clicks']['total'] += $numc;

			$alngs = unserialize($row['langs']);
			if (is_array($alngs) && (count($alngs) > 0)) {
				foreach ($alngs as $lng => $clicks) {
					$data['langs']['total'] += $clicks;
					if (!isset($data['langs']['stats'][$lng])) {
						$data['langs']['stats'][$lng] = array('num' => $clicks, 'pc' => 0);
					} else {
						$data['langs']['stats'][$lng]['num'] += (int)$clicks;
					}
				}
				foreach ($data['langs']['stats'] as $lng => $stat) {
					$data['langs']['stats'][$lng]['pc'] = round((($stat['num'] * 100) / $data['langs']['total']), 1);
				}
			}
		}

		$maxv = 0;
		$maxc = 0;
		foreach ($data['visits']['stats'] as $month => $stat) {
			if ($stat['num'] > $maxv) { $maxv = $stat['num']; }
		}
		foreach ($data['clicks']['stats'] as $month => $stat) {
			if ($stat['num'] > $maxc) { $maxc = $stat['num']; }
		}

		$yv = $this->makeYaxis($maxv);
		$yc = $this->makeYaxis($maxc);
		$data['visits']['yaxis'] = $yv['yaxis'];
		$data['visits']['ymax'] = $yv['ymax'];
		$data['clicks']['yaxis'] = $yc['yaxis'];
		$data['clicks']['ymax'] = $yc['ymax'];
		unset($yv, $yc);

		if ($data['visits']['total'] > 0) {
			foreach ($data['visits']['stats'] as $month => $stat) {
				$data['visits']['stats'][$month]['pc'] = floor(($stat['num'] * 100) / $data['visits']['ymax']);
			}
		}

		if ($data['clicks']['total'] > 0) {
			foreach ($data['clicks']['stats'] as $month => $stat) {
				$data['clicks']['stats'][$month]['pc'] = floor(($stat['num'] * 100) / $data['clicks']['ymax']);
			}
		}

		return $data;
	}


	/**********************/
	/* MAKE Y-AXIS VALUES */
	/**********************/
	private function makeYaxis($max) {
		if ($max <= 5) {
			$yaxis = array(0, 1, 2, 3, 4, 5);
			$ymax = 5;
		} else if ($max <= 10) {
			$yaxis = array(0, 2, 4, 6, 8, 10);
			$ymax = 10;
		} else if ($max <= 30) {
			$yaxis = array(0, 6, 12, 18, 24, 30);
			$ymax = 30;
		} else if ($max <= 50) {
			$yaxis = array(0, 10, 20, 30, 40, 50);
			$ymax = 50;
		} else if ($max <= 100) {
			$yaxis = array(0, 20, 40, 60, 80, 100);
			$ymax = 100;
		} else if ($max <= 150) {
			$yaxis = array(0, 30, 60, 90, 120, 150);
			$ymax = 150;
		} else if ($max <= 200) {
			$yaxis = array(0, 40, 80, 120, 160, 200);
			$ymax = 200;
		} else if ($max <= 400) {
			$yaxis = array(0, 80, 160, 240, 320, 400);
			$ymax = 400;
		} else if ($max <= 500) {
			$yaxis = array(0, 100, 200, 300, 400, 500);
			$ymax = 500;
		} else if ($max <= 800) {
			$yaxis = array(0, 160, 320, 480, 640, 800);
			$ymax = 800;
		} else if ($max <= 1000) {
			$yaxis = array(0, 200, 400, 600, 800, 1000);
			$ymax = 1000;
		} else if ($max <= 1500) {
			$yaxis = array(0, 300, 600, 900, 1200, 1500);
			$ymax = 1500;
		} else if ($max <= 2000) {
			$yaxis = array(0, 300, 600, 1200, 1600, 2000);
			$ymax = 2000;
		} else if ($max <= 5000) {
			$yaxis = array(0, 1000, 2000, 3000, 4000, 5000);
			$ymax = 5000;
		} else if ($max <= 10000) {
			$yaxis = array(0, '2k', '4k', '6k', '8k', '10k');
			$ymax = 10000;
		} else if ($max <= 15000) {
			$yaxis = array(0, '3k', '6k', '9k', '12k', '15k');
			$ymax = 15000;
		} else if ($max <= 20000) {
			$yaxis = array(0, '4k', '8k', '12k', '16k', '20k');
			$ymax = 20000;
		} else if ($max <= 50000) {
			$yaxis = array(0, '10k', '20k', '30k', '40k', '50k');
			$ymax = 50000;
		} else if ($max <= 100000) {
			$yaxis = array(0, '20k', '40k', '60k', '80k', '100k');
			$ymax = 100000;
		} else if ($max <= 500000) {
			$yaxis = array(0, '100k', '200k', '300k', '400k', '500k');
			$ymax = 500000;
		} else if ($max <= 1000000) {
			$yaxis = array(0, '200k', '400k', '600k', '800k', '1m');
			$ymax = 1000000;
		} else if ($max <= 5000000) {
			$yaxis = array(0, '1m', '2m', '3m', '4m', '5m');
			$ymax = 5000000;
		} else {
			$yaxis = array(0, '2m', '4m', '6m', '8m', '10m');
			$ymax = 10000000;
		}

		$psi = array('yaxis' => $yaxis, 'ymax' => $ymax);
		return $psi;
	}

}

?>