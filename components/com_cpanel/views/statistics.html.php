<?php 
/**
* @version		$Id: statistics.html.php 1442 2013-05-14 18:23:06Z datahell $
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class statisticsCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*******************************/
	/* MAKE/SHOW STATISTICS GRAPHS */
	/*******************************/
	public function graphs($data, $yearstats=false) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		if ($yearstats) {
			echo '<h2>'.$eLang->get('STATISTICS').' - '.$data['year']."</h2>\n";
		} else {
			echo '<h2>'.$eLang->get('STATISTICS').' - '.$data['monthname'].' '.$data['year']."</h2>\n";
		}

		if ($elxis->getConfig('STATISTICS') == 0) {
			echo '<div class="elx_warning">'.$eLang->get('STATS_COL_DISABLED')."</div>\n";
		}

		$this->statsNavigation($data, $yearstats);

		if ($yearstats) {
			$this->yearGraph($data, true);
			$this->yearGraph($data, false);
		} else {
			$this->monthGraph($data, true);
			$this->monthGraph($data, false);
		}
		$this->langsGraph($data, $yearstats);
	}


	/*************************/
	/* STATISTICS NAGIGATION */
	/*************************/
	private function statsNavigation($data, $yearstats) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$months = array(
			1 => array($eLang->get('JANUARY'), $eLang->get('JANUARY_SHORT')),
			2 => array($eLang->get('FEBRUARY'), $eLang->get('FEBRUARY_SHORT')),
			3 => array($eLang->get('MARCH'), $eLang->get('MARCH_SHORT')),
			4 => array($eLang->get('APRIL'), $eLang->get('APRIL_SHORT')),
			5 => array($eLang->get('MAY'), $eLang->get('MAY_SHORT')),
			6 => array($eLang->get('JUNE'), $eLang->get('JUNE_SHORT')),
			7 => array($eLang->get('JULY'), $eLang->get('JULY_SHORT')),
			8 => array($eLang->get('AUGUST'), $eLang->get('AUGUST_SHORT')),
			9 => array($eLang->get('SEPTEMBER'), $eLang->get('SEPTEMBER_SHORT')),
			10 => array($eLang->get('OCTOBER'), $eLang->get('OCTOBER_SHORT')),
			11 => array($eLang->get('NOVEMBER'), $eLang->get('NOVEMBER_SHORT')),
			12 => array($eLang->get('DECEMBER'), $eLang->get('DECEMBER_SHORT'))
		);

		$baseurl = $elxis->secureBase().'/components/com_cpanel/css/';
		if ($eLang->getinfo('DIR') == 'rtl') {
			$year_style = 'float:right; margin:0 0 0 20px; padding:0;';
		} else {
			$year_style = 'float:left; margin:0 20px 0 0; padding:0;';
		}
?>
		<div class="cpstats_navwrap">
			<div class="cpstats_nav">
				<div style="<?php echo $year_style; ?>">
					<div style="float:left; width:24px; text-align:center;">
						<a href="javascript:void(null);" onclick="cpstats_setyear(-1);" style="text-decoration:none;" title="<?php echo $eLang->get('PREVIOUS_YEAR'); ?>">
							<img src="<?php echo $baseurl; ?>arrow_left.png" border="0" alt="previous" />
						</a>
					</div>
					<div style="float:left; width:60px; text-align:center;">
						<a href="javascript:void(null);" id="cpstats_year" class="cpstats_navasl" onclick="cpshowstats(0);" title="<?php echo $eLang->get('YEAR_STATS'); ?>"><?php echo $data['year']; ?></a>
					</div>
					<div style="float:left; width:24px; text-align:center;">
						<a href="javascript:void(null);" onclick="cpstats_setyear(1);" style="text-decoration:none;" title="<?php echo $eLang->get('NEXT_YEAR'); ?>">
							<img src="<?php echo $baseurl; ?>arrow_right.png" border="0" alt="next" />
						</a>
					</div>
					<div style="clear:both;"></div>
				</div>
				<div style="margin:0; padding:0;">
<?php 
		foreach ($months as $m => $mnames) {
			$month = sprintf("%02d", $m);
			$class = 'cpstats_nava';
			if (!$yearstats) {
				$class = ($m == $data['month']) ? 'cpstats_navasl' : 'cpstats_nava';
			}
			echo "\t\t\t\t".'<a href="javascript:void(null);" onclick="cpshowstats(\''.$month.'\');" class="'.$class.'" title="'.$eLang->get('MONTH_STATS').' - '.$mnames[0].'">'.$mnames[1].'</a> '."\n";
		}
?>
				</div>
				<div style="clear:both;"></div>
			</div>
		</div>
		<div id="cpstats_link" style="margin:0;padding:0;display:none;visibility:hidden;"><?php echo $elxis->makeAURL('cpanel:stats/'); ?></div>

<?php 
	}


	/**********************/
	/* CREATE MONTH GRAPH */
	/**********************/
	private function monthGraph($data, $visits=true) {
		$eLang = eFactory::getLang();

		if ($visits == true) {
			$idx = 'visits';
			$title = $eLang->get('UNIQUE_VISITS');
			$stats_desc = sprintf($eLang->get('VISITS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
			$stats_desc .= '<br />'.$eLang->get('TOTAL_VISITS').' <strong>'.$data['visits']['total'].'</strong>';
		} else {
			$idx = 'clicks';
			$title = $eLang->get('PAGE_VIEWS');
			$stats_desc = sprintf($eLang->get('CLICKS_PER_DAY'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
			$stats_desc .= '<br />'.$eLang->get('TOTAL_PAGE_VIEWS').' <strong>'.$data['clicks']['total'].'</strong>';
		}
?>

		<div class="gbox">
			<div class="gbox_inner">
				<div class="gbox_head">
					<h3><?php echo $title; ?></h3>
				</div>
				<div class="gbox_contents">
					<p style="margin:0 0 5px 0; padding:0;"><?php echo $stats_desc; ?></p>
					<div class="cpstats_wrap" dir="ltr">
						<div class="cpstatsgrid<?php echo $data['daysnum']; ?>" dir="ltr">
							<span class="cpstats_y100"><?php echo $data[$idx]['yaxis'][5]; ?></span>
							<span class="cpstats_y80"><?php echo $data[$idx]['yaxis'][4]; ?></span>
							<span class="cpstats_y60"><?php echo $data[$idx]['yaxis'][3]; ?></span>
							<span class="cpstats_y40"><?php echo $data[$idx]['yaxis'][2]; ?></span>
							<span class="cpstats_y20"><?php echo $data[$idx]['yaxis'][1]; ?></span>
							<span class="cpstats_y0"><?php echo $data[$idx]['yaxis'][0]; ?></span>
							<ul class="cpstats_x" dir="ltr">
<?php 
							$k = 0;
							foreach ($data[$idx]['stats'] as $day => $stat) {
								$class = ($k == 0) ? 'cpstats_day'.$day : 'cpstats_altday'.$day;
								echo "\t\t\t\t\t\t\t\t".'<li class="'.$class.' cpstats_pc'.$stat['pc'].'" title="'.$stat['num'].'">'.$this->smartLabel($stat['num'], false)."</li>\n";
								$k = 1 - $k;
							}
?>
							</ul>
						</div>
					</div>
					<div class="gbox_footer">generated by <a href="http://www.elxis.org" target="_blank" title="Elxis Open Source CMS">Elxis</a></div>
				</div>
			</div>
		</div>
<?php 
	}


	/*********************/
	/* CREATE YEAR GRAPH */
	/*********************/
	private function yearGraph($data, $visits=true) {
		$eLang = eFactory::getLang();

		if ($visits == true) {
			$idx = 'visits';
			$title = $eLang->get('UNIQUE_VISITS');
			$stats_desc = sprintf($eLang->get('VISITS_PER_MONTH'), '<strong>'.$data['year'].'</strong>');
			$stats_desc .= '<br />'.$eLang->get('TOTAL_VISITS').' <strong>'.$data['visits']['total'].'</strong>';
		} else {
			$idx = 'clicks';
			$title = $eLang->get('PAGE_VIEWS');
			$stats_desc = sprintf($eLang->get('CLICKS_PER_MONTH'), '<strong>'.$data['year'].'</strong>');
			$stats_desc .= '<br />'.$eLang->get('TOTAL_PAGE_VIEWS').' <strong>'.$data['clicks']['total'].'</strong>';
		}

		$clang = $eLang->currentLang();
		if (file_exists(ELXIS_PATH.'/components/com_cpanel/css/grid_months_'.$clang.'.png')) {
			$grid_class = 'cpstatsgrid12_'.$clang;
		} else {
			$grid_class = 'cpstatsgrid12';
		}
?>

		<div class="gbox">
			<div class="gbox_inner">
				<div class="gbox_head">
					<h3><?php echo $title; ?></h3>
				</div>
				<div class="gbox_contents">
					<p style="margin:0 0 5px 0; padding:0;"><?php echo $stats_desc; ?></p>
					<div class="cpstats_wrap" dir="ltr">
						<div class="<?php echo $grid_class; ?>" dir="ltr">
							<span class="cpstats_y100"><?php echo $data[$idx]['yaxis'][5]; ?></span>
							<span class="cpstats_y80"><?php echo $data[$idx]['yaxis'][4]; ?></span>
							<span class="cpstats_y60"><?php echo $data[$idx]['yaxis'][3]; ?></span>
							<span class="cpstats_y40"><?php echo $data[$idx]['yaxis'][2]; ?></span>
							<span class="cpstats_y20"><?php echo $data[$idx]['yaxis'][1]; ?></span>
							<span class="cpstats_y0"><?php echo $data[$idx]['yaxis'][0]; ?></span>
							<ul class="cpstats_yx" dir="ltr">
<?php 
							$k = 0;
							foreach ($data[$idx]['stats'] as $month => $stat) {
								$class = ($k == 0) ? 'cpstats_month'.$month : 'cpstats_altmonth'.$month;
								echo "\t\t\t\t\t\t\t\t".'<li class="'.$class.' cpstats_pc'.$stat['pc'].'" title="'.$stat['num'].'">'.$this->smartLabel($stat['num'], true)."</li>\n";
								$k = 1 - $k;
							}
?>
							</ul>
						</div>
					</div>
					<div class="gbox_footer">generated by <a href="http://www.elxis.org" target="_blank" title="Elxis Open Source CMS">Elxis</a></div>
				</div>
			</div>
		</div>
<?php 
	}


	/************************************/
	/* MAKE SMART LABEL FOR GRID COLUMN */
	/************************************/
	private function smartLabel($num, $yearstats=false) {
		$snum = (string)$num;
		$len = strlen($snum);
		$max = ($yearstats) ? 7 : 5;

		if ($len > $max) {
			if ($num > 1000000) {
				return round(($num / 1000000), 2).'m';
			} else if ($num > 100000) {
				return round(($num / 1000), 1).'k';
			} else {
				return round(($num / 1000), 2).'k';
			}
		}

		return $num;
	}


	/**************************/
	/* CREATE LANGUAGES GRAPH */
	/**************************/
	private function langsGraph($data, $yearstats=false) {
		$eLang = eFactory::getLang();

		if ($yearstats == true) {
			$stats_desc = sprintf($eLang->get('LANGS_USAGE_FOR'), '<strong>'.$data['year'].'</strong>');
		} else {
			$stats_desc = sprintf($eLang->get('LANGS_USAGE_FOR'), '<strong>'.$data['monthname'].' '. $data['year'].'</strong>');
		}

		$pie = $this->calculatePie($data['langs']);
?>
		<div class="gbox">
			<div class="gbox_inner">
				<div class="gbox_head">
					<h3><?php echo $eLang->get('LANGS_USAGE'); ?></h3>
				</div>
				<div class="gbox_contents">
					<p style="margin:0 0 5px 0; padding:0;"><?php echo $stats_desc; ?></p>
					<div class="cpstats_wrap" style="padding:20px;" dir="ltr">
						<div class="cpstats_piewrap">
							<div class="cpstats_piebg"></div>
<?php 
			if ($pie !== false) {
				$css = '';
				foreach ($pie as $lng => $pdata) {
					if ($pdata['clicks'] == 0) { continue; }
					if ($pdata['degrees'] > 180) {
						echo '<div id="cpstats_pie_'.$pdata['color'].'" class="cpstats_hold cpstats_gt50"><div class="cpstats_pie"></div><div class="cpstats_pie cpstats_fill"></div></div>'."\n";
					} else {
						echo '<div id="cpstats_pie_'.$pdata['color'].'" class="cpstats_hold"><div class="cpstats_pie"></div></div>'."\n";
					}
				
					if ($pdata['start'] > 0) {
						$css .= '#cpstats_pie_'.$pdata['color'].' { -ms-transform:rotate('.$pdata['start'].'deg); -webkit-transform:rotate('.$pdata['start'].'deg); -moz-transform:rotate('.$pdata['start'].'deg); -o-transform:rotate('.$pdata['start'].'deg); transform:rotate('.$pdata['start'].'deg); }'."\n";
					}
					$css .= '#cpstats_pie_'.$pdata['color'].' .cpstats_pie { -ms-transform:rotate('.$pdata['degrees'].'deg); -webkit-transform:rotate('.$pdata['degrees'].'deg); -moz-transform:rotate('.$pdata['degrees'].'deg); -o-transform:rotate('.$pdata['degrees'].'deg); transform:rotate('.$pdata['degrees'].'deg); }'."\n";				
				}
				eFactory::getDocument()->addStyle($css);
			}
?>
						</div>
						<div class="cpstats_appendix">
							<?php $this->pieLegend($pie); ?>
						</div>
						<div style="clear:both;"></div>
					</div>
					<div class="gbox_footer">generated by <a href="http://www.elxis.org" target="_blank" title="Elxis Open Source CMS">Elxis</a></div>
				</div>
			</div>
		</div>		

<?php 	
	}


	/*******************/
	/* MAKE PIE LEGEND */
	/*******************/
	private function pieLegend($pie) {
		$eLang = eFactory::getLang();

		echo '<h3>'.$eLang->get('LEGEND')."</h3>\n";
		if ($pie === false) {
			echo '<p>'.$eLang->get('NO_DATA_AVAIL')."</p>\n";
			return;
		}
?>
		<table class="cpstats_tbl" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th width="20"></th>
			<th colspan="2"><?php echo $eLang->get('LANGUAGE'); ?></th>
			<th><?php echo $eLang->get('USAGE'); ?></th>
			<th><?php echo $eLang->get('VIEWS'); ?></th>
		</tr>
<?php 
		foreach ($pie as $lng => $pdata) {
			if ($pdata['clicks'] == 0) { continue; }
			echo '<tr><td width="20" style="background-color:#'.$pdata['hex'].';">&#160;</td>'."\n";
			echo '<td width="24"><img src="'.$pdata['flag'].'" alt="'.$lng.'" />'."</td>\n";
			echo '<td>'.$pdata['name']."</td>\n";
			echo '<td>'.$pdata['pc']."%</td>\n";
			echo '<td>'.$pdata['clicks']."</td>\n";
			echo "</tr>\n";
		}
?>
		</table>
<?php 
	}


	/************************/
	/* CALCULATE PIE SLICES */
	/************************/
	private function calculatePie($langs) {
		$n = count($langs['stats']);
		if ($n == 0) { return false; }

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$flags_base = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';

		$colours = $this->getColours();

		$pie = array();
		$pie['un'] = array(
			'name' => $eLang->get('OTHER'),
			'flag' => $flags_base.'un.png',
			'clicks' => 0,
			'pc' => 0,
			'color' => $colours[14][0],
			'hex' => $colours[14][1],
			'start' => 0,
			'degrees' => 0
		);

		$c = 0;
		$start = 0;
		$total_dgs = 0;
		$last = '';
		foreach ($langs['stats'] as $lng => $stats) {
			if ($stats['pc'] < 1) {
				$pie['un']['clicks'] += $stats['num'];
				$pie['un']['pc'] += $stats['pc'];
				continue;
			}

			if ($c > 13) {
				$pie['un']['clicks'] += $stats['num'];
				$pie['un']['pc'] += $stats['pc'];
				continue;
			}

			$dg = round(((360 * $stats['pc']) / 100), 1);
			$pie[$lng] = array(
				'name' => strtoupper($lng),
				'flag' => $flags_base.$lng.'.png',
				'clicks' => $stats['num'],
				'pc' => $stats['pc'],
				'color' => $colours[$c][0],
				'hex' => $colours[$c][1],
				'start' => $start,
				'degrees' => $dg
			);
			$last = $lng;
			$total_dgs += $dg;
			$start += $dg;
			$c++;
		}

		if ($pie['un']['clicks'] > 0) {
			$dg = round(((360 * $pie['un']['pc']) / 100), 1);
			$total_dgs += $dg;
			$pie['un']['start'] = $start;
			$pie['un']['degrees'] = $dg;
			$last = 'un';
		}

		if ($total_dgs < 360) {
			$diff = 360 - $total_dgs;
			$pie[$last]['degrees'] += $diff;
		} else if ($total_dgs > 360) {
			$diff = $total_dgs - 360;
			$pie[$last]['degrees'] -= $diff;
		}

		return $pie;
	}


	/************************/
	/* GET SHUFFLED COLOURS */
	/************************/
	private function getColours() {
		$idxs = array('blue', 'navy', 'yellow', 'gold', 'orange', 'bluelight', 'olive', 'greenlight', 'fuchsia', 'red', 'bluegray', 'teal', 'green', 'pink', 'purple');
		$colours = array(
			'blue' => '52AFFD', 'navy' => '0C76D0', 'yellow' => 'F6F282', 'gold' => 'DAD309', 'orange' => 'FF6600',
			'bluelight' => '87C2F5', 'olive' => '466B12', 'greenlight' => '94E028', 'fuchsia' => 'FF00FF', 'red' => 'FF0000',
			'bluegray' => 'A7BDCF', 'teal' => '008080', 'green' => '69A416', 'pink' => 'EF84F8', 'purple' => '7E5FCE'
		);

		shuffle($idxs);
		$shuffled = array();
		foreach ($idxs as $colour) { $shuffled[] = array($colour, $colours[$colour]); }
		return $shuffled;
	}

}

?>