<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Debug
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elxisPerformance {

	private $blocks = array();
	private $global_time = 0;
	private $block_time = 0;
	private $current_block = '';
	private $sys_errors = 0;
	private $sys_queries = 0;
	private $sys_sql = array();
	private $blocks_sql = array();


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		if (defined('ELXIS_DEFENDER_DT') && (ELXIS_DEFENDER_DT > 0)) {
			$this->global_time = microtime(true) - ELXIS_DEFENDER_DT;
			$dt = ELXIS_DEFENDER_DT * 1000;
			$dt = number_format($dt, 2, '.', '');
			$this->blocks['Elxis_Defender'] = array(
				'file' => 'includes/libraries/elxis/defender.class.php',
				'exec_time' => $dt,
				'db_queries' => 0,
				'errors' => 0
			);
		} else {
			$this->global_time = microtime(true);
		}
	}


	/**************************/
	/* START BLOCK MONITORING */
	/**************************/
	public function startBlock($name, $file='') {
		$this->block_time = microtime(true);
		if ($name == '') { $name = 'Block_'.$this->block_time; }
		$this->current_block = $name;
		$this->blocks[$name] = array(
			'file' => $file,
			'exec_time' => 0,
			'db_queries' => 0,
			'errors' => 0
		);
	}


	/*************************/
	/* STOP BLOCK MONITORING */
	/*************************/
	public function stopBlock() {
		if ($this->current_block == '') { return; }
		$name = $this->current_block;
		$this->blocks[$name]['exec_time'] = $this->stopBenchmark($this->block_time, microtime(true));
		$this->current_block = '';
	}


	/****************************************/
	/* INCREASE CURRENT BLOCK ERRORS BY ONE */
	/****************************************/
	public function addError() {
		if ($this->current_block == '') {
			$this->sys_errors++;
			return;
		}
		$name = $this->current_block;
		$this->blocks[$name]['errors']++;
	}


	/********************************************/
	/* INCREASE CURRENT BLOCK DB QUERIES BY ONE */
	/********************************************/
	public function addQuery($sql='') {
		if ($this->current_block == '') {
			$this->sys_queries++;
			if ($sql != '') { $this->sys_sql[] = $sql; }
			return;
		}
		$name = $this->current_block;
		$this->blocks[$name]['db_queries']++;
		if ($sql != '') {
			if (!isset($this->blocks_sql[$name])) {
				$this->blocks_sql[$name] = array();
			}
			$this->blocks_sql[$name][] = $sql;
		}
	}


	/*******************************/
	/* GENERATE PERFORMANCE REPORT */
	/*******************************/
	private function getReport() {
		$report = array();
		$report['global'] = array(
			'file' => ELXIS_SELF,
			'exec_time' => $this->stopBenchmark($this->global_time, microtime(true)),
			'db_queries' => $this->sys_queries,
			'errors' => $this->sys_errors
		);

		$report['system'] = array(
			'file' => 'includes/loader.php',
			'exec_time' => $report['global']['exec_time'],
			'db_queries' => $this->sys_queries,
			'errors' => $this->sys_errors
		);

		if ($this->blocks) {
			foreach ($this->blocks as $name => $block) {
				$report['global']['db_queries'] += $block['db_queries'];
				$report['global']['errors'] += $block['errors'];
				$report['system']['exec_time'] -= $block['exec_time'];
				$report[$name] = $block;
			}
		}
		return $report;
	}


	/********************************************/
	/* GENERATE PERFORMANCE MONITOR HTML REPORT */
	/********************************************/
	public function makeReport($debuglevel=0) {
		$eLang = eFactory::getLang();
		$report = $this->getReport();
		$str = "\n\n".'<div style="margin: 20px 4px;">'."\n";
		$str .= '<div class="elx_tbl_wrapper">'."\n";
		$str .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		$str .= '<tr><th colspan="5">'.$eLang->get('ELX_PERF_MONITOR').'</th></tr>'."\n";
		$str .= "<tr>\n";
		$str .= "\t".'<th class="elx_th_sub">'.$eLang->get('ITEM').'</th>'."\n";
		$str .= "\t".'<th class="elx_th_sub">'.$eLang->get('INIT_FILE').'</th>'."\n";
		$str .= "\t".'<th class="elx_th_sub">'.$eLang->get('EXEC_TIME').'</th>'."\n";
		$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('DB_QUERIES').'</th>'."\n";
		$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('ERRORS').'</th>'."\n";
		$str .= "</tr>\n";
		$k = 0;
		foreach ($report as $name => $data) {
			$rowclass = ($name === 'global') ? 'elx_trx' : 'elx_tr'.$k;
			if (strlen($data['file']) > 20) {
				$file_txt = '<span title="'.$data['file'].'" style="cursor: pointer;">...'.substr($data['file'], -17).'</span>';
			} else {
				$file_txt = $data['file'];
			}

			$name = ucfirst(str_replace('_', ' ', $name));
			$timestr = ($data['exec_time'] > 400) ? number_format(($data['exec_time']/1000), 2, '.', '').' sec' : $data['exec_time'].' ms';
			$str .= '<tr class="'.$rowclass.'">'."\n";
			$str .= "\t<td>".$name."</td>\n";
			$str .= "\t<td dir=\"ltr\">".$file_txt."</td>\n";
			$str .= "\t<td>".$timestr."</td>\n";
			$str .= "\t".'<td class="elx_td_center">'.$data['db_queries']."</td>\n";
			$str .= "\t".'<td class="elx_td_center">'.$data['errors']."</td>\n";
			$str .= "</tr>\n";
			$k = 1 - $k;
		}
		$str .= "</table>\n";
		$str .= "</div>\n</div>\n";
		if ($debuglevel > 1) {
			$str .= $this->makeAPCReport();
		}
		if ($debuglevel > 3) {
			$str .= $this->makePluginsReport();
		}
		$str .= $this->makeSQLReport();
		return $str;
	}


	/****************************/
	/* MAKE SQL QUERRIES REPORT */
	/****************************/
	private function makeSQLReport() {
		if (!$this->sys_sql && !$this->blocks_sql) { return ''; }
		$eLang = eFactory::getLang();
		$str = "\n\n".'<div style="margin: 20px 4px;">'."\n";
		$str .= '<div class="elx_tbl_wrapper">'."\n";
		$str .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		$str .= '<tr><th>'.$eLang->get('DB_QUERIES').'</th></tr>'."\n";
		if ($this->sys_sql) {
			$str .= '<tr><th class="elx_th_sub">System</th></tr>'."\n";
			$k = 0;
			foreach ($this->sys_sql as $sql) {
				$str .= '<tr class="elx_tr'.$k.'"><td dir="ltr">'.htmlspecialchars($sql)."</td></tr>\n";
				$k = 1 - $k;
			}
		}
		if ($this->blocks_sql) {
			foreach ($this->blocks_sql as $name => $sqls) {
				if ($sqls) {
					$str .= '<tr><th class="elx_th_sub">'.ucfirst($name)."</th></tr>\n";
					$k = 0;
					foreach ($sqls as $sql) {
						$str .= '<tr class="elx_tr'.$k.'"><td dir="ltr">'.htmlspecialchars($sql)."</td></tr>\n";
						$k = 1 - $k;					
					}
				}
			}
		}
		$str .= "</table>\n";
		$str .= "</div>\n</div>\n";
		return $str;
	}


	/*******************************/
	/* MAKE CONTENT PLUGINS REPORT */
	/*******************************/
	private function makePluginsReport() {
		if (defined('ELXIS_ADMIN')) { return; }

		$eLang = eFactory::getLang();
		$data = eFactory::getPlugin()->runData();

		$str = "\n\n".'<div style="margin: 20px 4px;">'."\n";
		$str .= '<div class="elx_tbl_wrapper">'."\n";
		$str .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		$str .= '<tr><th colspan="2">'.$eLang->get('CONTENT_PLUGINS').'</th></tr>'."\n";
		$str .= '<tr class="elx_tr0"><td>Available plugins</td><td dir="ltr">'.implode(', ', $data['plugins'])."</td></tr>\n";
		$str .= '<tr class="elx_tr1"><td>Articles processed</td><td dir="ltr">'.$data['runtimes']."</td></tr>\n";
		$str .= '<tr class="elx_tr0"><td>Plugin executions</td><td dir="ltr">'.$data['plugintimes']."</td></tr>\n";
		$str .= "</table>\n";
		$str .= "</div>\n</div>\n";
		return $str;
	}


	/*************************/
	/* MAKE APC USAGE REPORT */
	/*************************/
	private function makeAPCReport() {
		if (defined('ELXIS_ADMIN')) { return; }
		$stats = elxisAPC::getInfo();
		if (!$stats || !is_array($stats)) { return; }
		$eLang = eFactory::getLang();

		$str = "\n\n".'<div style="margin: 20px 4px;">'."\n";
		$str .= '<div class="elx_tbl_wrapper">'."\n";
		$str .= '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="ltr" class="elx_tbl_list">'."\n";
		$str .= '<tr><th colspan="6">APC</th></tr>'."\n";
		$str .= "<tr>\n";
		$str .= "\t".'<th class="elx_th_sub" colspan="3">&#160;</th>'."\n";
		$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('ENTRIES')."</th>\n";
		$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('SIZE')."</th>\n";
		$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('HITS')."</th>\n";
		$str .= "</tr>\n";
		$str .= '<tr class="elx_tr0"><td colspan="3"><strong>Server</strong></td>';
		$str .= '<td class="elx_td_center">'.$stats['server']['num_entries'].'</td><td class="elx_td_center">'.$this->getSize($stats['server']['mem_size']).'</td>';
		$str .= '<td class="elx_td_center">'.$stats['server']['num_hits'].'</td></tr>'."\n";
		$str .= '<tr class="elx_trx"><td colspan="3"><strong>Elxis</strong></td>';
		$str .= '<td class="elx_td_center">'.$stats['elxis']['num_entries'].'</td><td class="elx_td_center">'.$this->getSize($stats['elxis']['mem_size']).'</td>';
		$str .= '<td class="elx_td_center">'.$stats['elxis']['num_hits'].'</td></tr>'."\n";
		if (count($stats['items']) > 0) {
			$str .= "<tr>\n";
			$str .= "\t".'<th class="elx_th_sub">'.$eLang->get('GROUP')."</th>\n";
			$str .= "\t".'<th class="elx_th_sub">'.$eLang->get('ITEM')."</th>\n";
			$str .= "\t".'<th class="elx_th_subcenter">DT</th>'."\n";
			$str .= "\t".'<th class="elx_th_subcenter">TTL</th>'."\n";
			$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('SIZE')."</th>\n";
			$str .= "\t".'<th class="elx_th_subcenter">'.$eLang->get('HITS')."</th>\n";
			$str .= "</tr>\n";
			$k = 0;
			foreach ($stats['items'] as $item) {
				$str .= '<tr class="elx_tr'.$k.'">'."\n";
				$str .= "\t<td>".$item['group']."</td>\n";
				$str .= "\t<td>".$item['name']."</td>\n";
				$str .= "\t".'<td class="elx_td_center">'.$item['dt']."</td>\n";
				$str .= "\t".'<td class="elx_td_center">'.$item['ttl']."</td>\n";
				$str .= "\t".'<td class="elx_td_center">'.$this->getSize($item['mem_size'])."</td>\n";
				$str .= "\t".'<td class="elx_td_center">'.$item['num_hits']."</td>\n";
				$str .= "</tr>\n";
				$k = 1 - $k;
			}	
		}
		$str .= "</table>\n";
		$str .= "</div>\n</div>\n";

		return $str;
	}


	/******************************/
	/* HUMAN FRIENDLY SIZE FORMAT */
	/******************************/
	private function getSize($bytes) {
		if ($bytes < 1024) { return $bytes.' bytes'; }
		if ($bytes < 1048576) { return round($bytes/1024, 2).' KB'; }
		return round($bytes/1048576, 2).' MB';
	}


	/******************/
	/* STOP BENCHMARK */
	/******************/
	private function stopBenchmark($start, $end) {
		$total_time = $end - $start;
		$total_time = $total_time * 1000;
		return number_format($total_time, 2, '.', '');
	}


}

?>