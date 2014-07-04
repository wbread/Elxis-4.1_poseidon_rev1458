<?php 
/**
* @version		$Id: mod_adminstats.php 1060 2012-04-22 09:16:11Z datahell $
* @package		Elxis
* @subpackage	Module Administration Statistics
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminStats', false)) {
	class modadminStats {

		private $statstype = 0;
		private $year = 2012;
		private $month = 1;
		private $daysnum = 31;
		private $moduleId = 0;
		private $yaxis = array(0, 1, 2, 3, 4, 5);
		private $ymax = 5;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			$cyear = date('Y');
			$cmonth = date('m');

			$this->year = $cyear;
			$this->month = $cmonth;
			if (isset($_GET['staty']) && isset($_GET['statm'])) {
				$staty = (int)$_GET['staty'];
				$statm = (int)$_GET['statm'];
				if (($staty > 2010) && ($staty <= $cyear)) {
					if (($statm > 0) && ($statm < 13)) {
						$this->year = $staty;
						$this->month = $statm;
					}
				}
			}

			$ts = mktime(12, 0, 0, $this->month, 15, $this->year);
			$this->daysnum = date('t', $ts);

			$this->getParams($params);
			if (isset($_GET['statt'])) {
				$this->statstype = (int)$_GET['statt'];
				if ($this->statstype != 1) { $this->statstype = 0; }
			}

			$this->moduleId = $elxmod->id;
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
            $this->statstype = (int)$params->get('statstype', 0);
			if ($this->statstype != 1) { $this->statstype = 0; }
        }


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			eFactory::getDocument()->addStyleLink($elxis->secureBase().'/modules/mod_adminstats/css/adminstats.css');

			$stats = $this->collectStatistics();
			$cplink = $elxis->makeAURL('cpanel:/');

			if ($this->month == 1) {
				$pm = 12;
				$py = $this->year - 1;
				$nm = 2;
				$ny = $this->year;
			} elseif ($this->month == 12) {
				$pm = 11;
				$py = $this->year;
				$nm = 1;
				$ny = $this->year + 1;
			} else {
				$pm = $this->month - 1;
				$py = $this->year ;
				$nm = $this->month + 1;
				$ny = $this->year;
			}

			if ($this->statstype == 0) {
				$ctyp = $eLang->get('CLICKS');
				$ctyplink = $cplink.'?statt=1&amp;staty='.$this->year.'&amp;statm='.$this->month;
				$ctypttl = sprintf($eLang->get('SWITCH_TO'), $eLang->get('CLICKS'));
				$sd = '<strong>'.eFactory::getDate()->monthName($this->month).' '.$this->year.'</strong>';
				$stats_desc = sprintf($eLang->get('VISITS_PER_DAY'), $sd);
			} else {
				$ctyp = $eLang->get('VISITS');
				$ctyplink = $cplink.'?statt=0&amp;staty='.$this->year.'&amp;statm='.$this->month;
				$ctypttl = sprintf($eLang->get('SWITCH_TO'), $eLang->get('VISITS'));
				$sd = '<strong>'.eFactory::getDate()->monthName($this->month).' '.$this->year.'</strong>';
				$stats_desc = sprintf($eLang->get('CLICKS_PER_DAY'), $sd);
			}
?>
			<div class="gbox">
				<div class="gbox_inner">
					<div class="gbox_head">
						<ul class="gbox_tabs">
							<li>
								<a href="<?php echo $cplink.'?statt='.$this->statstype.'&amp;staty='.$py.'&amp;statm='.$pm; ?>" title="<?php echo $eLang->get('PREV_MONTH'); ?>">&#171;</a>
							</li>
							<li>
								<a href="<?php echo $ctyplink; ?>" title="<?php echo $ctypttl; ?>"><?php echo $ctyp; ?></a>
							</li>
							<li>
								<a href="<?php echo $cplink.'?statt='.$this->statstype.'&amp;staty='.$ny.'&amp;statm='.$nm; ?>" title="<?php echo $eLang->get('NEXT_MONTH'); ?>">&#187;</a>
							</li>
						</ul>
						<h3><?php echo $eLang->get('STATISTICS'); ?></h3>
					</div>
					<div class="gbox_contents">
						<p style="margin:0 0 5px 0; padding:0;"><?php echo $stats_desc; ?></p>
						<div class="stats_wrap" dir="ltr">
							<div class="statsgrid<?php echo $this->daysnum; ?>" dir="ltr">
								<span class="stats_y100"><?php echo $this->yaxis[5]; ?></span>
								<span class="stats_y80"><?php echo $this->yaxis[4]; ?></span>
								<span class="stats_y60"><?php echo $this->yaxis[3]; ?></span>
								<span class="stats_y40"><?php echo $this->yaxis[2]; ?></span>
								<span class="stats_y20"><?php echo $this->yaxis[1]; ?></span>
								<span class="stats_y0"><?php echo $this->yaxis[0]; ?></span>
								<ul class="stats_x" dir="ltr">
<?php 
						$k = 0;
						foreach ($stats as $day => $stat) {
							$class = ($k == 0) ? 'stats_day'.$day : 'stats_altday'.$day;
							echo '<li class="'.$class.' stats_pc'.$stat['pc'].'" title="'.$stat['num'].'">'.$this->smartLabel($stat['num'])."</li>\n";
							$k = 1 - $k;
						}
?>
								</ul>
							</div>
						</div>

<?php 
						if (($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) || ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0)) {
							echo '<div class="gbox_footer">'."\n";
							if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
								if ($elxis->acl()->check('module', 'mod_adminstats', 'manage', $this->moduleId) > 0) {
									$link = $elxis->makeAURL('extmanager:modules/edit.html').'?id='.$this->moduleId;
									echo '<a href="'.$link.'" class="gbox_edit_link" title="'.$eLang->get('CHANGE_MOD_PARAMS').'">'.$eLang->get('EDIT')."</a>\n";
								}
							}
							if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) {
								$link = $elxis->makeAURL('cpanel:stats/');
								echo '<a href="'.$link.'" class="gbox_more_link" title="'.$eLang->get('ANALYTIC_STATS').'">'.$eLang->get('MORE')."</a>\n";
							}
							echo "</div>\n";
						}
?>
					</div>
				</div>
			</div>
<?php 
		}


		/******************************************/
		/* COLLECT STATISTICS FOR THE GIVEN MONTH */
		/******************************************/
		private function collectStatistics() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$stats = array();
			for ($i=1; $i <= $this->daysnum; $i++) {
				$stats[$i] = array('num' => 0, 'pc' => 0);
			}

			$max = 0;
			$rows = $this->getDbStats();
			if (!$rows) { return $stats; }

			foreach ($rows as $row) {
				$day = (int)substr($row['statdate'], -2);
				if ($this->statstype == 0) {
					$num = $row['visits'];
				} else {
					$num = $row['clicks'];
				}
				if ($num > $max) { $max = $num; }
				$stats[$day]['num'] = $num;
			}

			if ($max == 0) { return $stats; }

			$this->makeYaxis($max);

			foreach ($stats as $day => $stat) {
				$stats[$day]['pc'] = floor(($stat['num'] * 100) / $this->ymax);
			}
			return $stats;
		}


		/**********************/
		/* MAKE Y-AXIS VALUES */
		/**********************/
		private function makeYaxis($max) {
			if ($max <= 5) {
				$this->yaxis = array(0, 1, 2, 3, 4, 5);
				$this->ymax = 5;
			} else if ($max <= 10) {
				$this->yaxis = array(0, 2, 4, 6, 8, 10);
				$this->ymax = 10;
			} else if ($max <= 30) {
				$this->yaxis = array(0, 6, 12, 18, 24, 30);
				$this->ymax = 30;
			} else if ($max <= 50) {
				$this->yaxis = array(0, 10, 20, 30, 40, 50);
				$this->ymax = 50;
			} else if ($max <= 100) {
				$this->yaxis = array(0, 20, 40, 60, 80, 100);
				$this->ymax = 100;
			} else if ($max <= 150) {
				$this->yaxis = array(0, 30, 60, 90, 120, 150);
				$this->ymax = 150;
			} else if ($max <= 200) {
				$this->yaxis = array(0, 40, 80, 120, 160, 200);
				$this->ymax = 200;
			} else if ($max <= 400) {
				$this->yaxis = array(0, 80, 160, 240, 320, 400);
				$this->ymax = 400;
			} else if ($max <= 500) {
				$this->yaxis = array(0, 100, 200, 300, 400, 500);
				$this->ymax = 500;
			} else if ($max <= 800) {
				$this->yaxis = array(0, 160, 320, 480, 640, 800);
				$this->ymax = 800;
			} else if ($max <= 1000) {
				$this->yaxis = array(0, 200, 400, 600, 800, '1k');
				$this->ymax = 1000;
			} else if ($max <= 1500) {
				$this->yaxis = array(0, 300, 600, 900, '1.2k', '1.5k');
				$this->ymax = 1500;
			} else if ($max <= 2000) {
				$this->yaxis = array(0, 300, 600, '1.2k', '1.6k', '2k');
				$this->ymax = 2000;
			} else if ($max <= 5000) {
				$this->yaxis = array(0, '1k', '2k', '3k', '4k', '5k');
				$this->ymax = 5000;
			} else if ($max <= 10000) {
				$this->yaxis = array(0, '2k', '4k', '6k', '8k', '10k');
				$this->ymax = 10000;
			} else if ($max <= 15000) {
				$this->yaxis = array(0, '3k', '6k', '9k', '12k', '15k');
				$this->ymax = 15000;
			} else if ($max <= 20000) {
				$this->yaxis = array(0, '4k', '8k', '12k', '16k', '20k');
				$this->ymax = 20000;
			} else if ($max <= 50000) {
				$this->yaxis = array(0, '10k', '20k', '30k', '40k', '50k');
				$this->ymax = 50000;
			} else if ($max <= 100000) {
				$this->yaxis = array(0, '20k', '40k', '60k', '80k', '100k');
				$this->ymax = 100000;
			} else if ($max <= 500000) {
				$this->yaxis = array(0, '100k', '200k', '300k', '400k', '500k');
				$this->ymax = 500000;
			} else {
				$this->yaxis = array(0, '200k', '400k', '600k', '800k', '1m');
				$this->ymax = 1000000;
			}
		}


		/************************************/
		/* MAKE SMART LABEL FOR GRID COLUMN */
		/************************************/
		private function smartLabel($num) {
			if ($num < 1000) {
				return $num;
			} else if ($num < 10000) {
				return round(($num / 1000), 1).'k';
			} else if ($num < 100000) {
				return round(($num / 1000), 0).'k';
			} else if ($num < 1000000) {
				return round(($num / 1000000), 1).'m';
			} else if ($num < 10000000) {
				return round(($num / 1000000), 1).'m';
			} else {
				return round(($num / 1000000), 0).'m';
			}
		}


		/************************************/
		/* GET STATISTICS FROM THE DATABASE */
		/************************************/
		private function getDbStats() {
			$db = eFactory::getDB();

			$dt = $this->year.'-'.sprintf("%02d", $this->month).'%';
			$sql = "SELECT ".$db->quoteId('statdate').", ".$db->quoteId('clicks').", ".$db->quoteId('visits')
			."\n FROM ".$db->quoteId('#__statistics')
			."\n WHERE ".$db->quoteId('statdate')." LIKE :sdt ORDER BY ".$db->quoteId('statdate')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':sdt', $dt, PDO::PARAM_STR);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

			return $rows;		
		}

	}
}


$admstats = new modadminStats($params, $elxmod);
$admstats->run();
unset($admstats);

?>