<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	CPanel component
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class utilitiesCPView extends cpanelView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/**************************/
	/* LIST BACKUP FILES HTML */
	/**************************/
	public function listbackup() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) { $is_subsite = true; }
		$align = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		if ($elxis->getConfig('REPO_PATH') == '') {
			$backupdir = ELXIS_PATH.'/repository/backup/';
		} else {
			$backupdir = rtrim($elxis->getConfig('REPO_PATH'), '/').'/backup/';
		}

		echo '<h2>'.$eLang->get('BACKUP')."</h2>\n";
		if (!file_exists($backupdir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_EXIST'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx_warning">'.$txt."</div>\n";
		} elseif (!is_writable($backupdir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_WRITE'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx_warning">'.$txt."</div>\n";
		} else {
			$txt = sprintf($eLang->get('BACKUP_SAVED_INTO'), '<strong>'.$backupdir.'</strong>');
			echo '<div class="elx_info elx_close">'.$txt."</div>\n";
		}

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('backups', $eLang->get('BACKUP_FLIST'));
		$grid->setOption('url', $elxis->makeAURL('cpanel:backup/getbackups.xml', 'inner.php'));
		$grid->setOption('sortname', 'bkdate');
		$grid->setOption('sortorder', 'desc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->addColumn($eLang->get('TYPE'), 'bktype', 140, true, 'auto');
		$grid->addColumn($eLang->get('DATE'), 'bkdate', 180, true, 'auto');
		$grid->addColumn($eLang->get('FILENAME'), 'bkname', 400, true, 'auto');
		$grid->addColumn($eLang->get('SIZE'), 'bksize', 100, true, 'auto');
		$grid->addButton($eLang->get('NEW_DB_BACKUP'), 'dbbackup', 'add', 'backupaction');
		$grid->addSeparator();
		if (!$is_subsite) {
			$grid->addButton($eLang->get('NEW_FS_BACKUP'), 'fsbackup', 'add', 'backupaction');
			$grid->addSeparator();
		}
		$grid->addButton($eLang->get('TOGGLE_SELECTED'), 'togglerows', 'toggle', 'backupaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'delbackup', 'delete', 'backupaction');
		$grid->addSearch($eLang->get('FILENAME'), 'bkname', true);
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function backupaction(task, grid) {
			if ((task == 'fsbackup') || (task == 'dbbackup')) {
				if (confirm('<?php echo $eLang->get('TAKE_NEW_BACKUP'); ?>')) {
					var bktype = 'fs';
					if (task == 'dbbackup') { bktype = 'db'; }
					$('.pPageStat', this.pDiv).html('<?php echo $eLang->get('PLEASE_WAIT'); ?>');
					$('.pReload', this.pDiv).addClass('loading');
					$.ajax({
						type: "POST",
						dataType: "html",
						url: '<?php echo $elxis->makeAURL('cpanel:backup/makebackup', 'inner.php'); ?>',
						data: 'type='+bktype,
						success: function(xreply){
							$('.pReload', this.pDiv).removeClass('loading');
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok > 0) {
								$("#backups").flexReload();
							} else {
								if (typeof rdata[1] === "undefined") {
									$('.pPageStat', this.pDiv).html('Action failed!');
								} else {
									$('.pPageStat', this.pDiv).html(rdata[1]);
								}
							}
						}
					});
				}
			} else if (task == 'delbackup') {
				if ($('.trSelected', grid).length > 0) {
					if (confirm('<?php echo $eLang->get('DELETE_SEL_ITEMS'); ?>')) {
						var items = $('.trSelected',grid);
						var itemlist = '';
						for(i=0;i<items.length;i++){
							itemlist+= items[i].id.substr(3)+",";
						}
						$.ajax({
							type: "POST",
							dataType: "html",
							url: '<?php echo $elxis->makeAURL('cpanel:backup/delbackup', 'inner.php'); ?>',
							data: "items="+itemlist,
							success: function(xreply){
								var rdata = new Array();
								rdata = xreply.split('|');
								var rok = parseInt(rdata[0]);
								if (rok > 0) {
									$("#backups").flexReload();
								} else {
									if (typeof rdata[1] === "undefined") {
										alert('Action failed!');
									} else {
										alert(rdata[1]);
									}
								}
							}
						});
					}
				} else {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				}
			} else if (task == 'togglerows') {
				$('tr',grid).toggleClass('trSelected');
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
	}


	/****************************/
	/* LIST SYSTEM ROUTING HTML */
	/****************************/
	public function listroutes() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('routes', $eLang->get('ROUTING'));
		$grid->setOption('url', $elxis->makeAURL('cpanel:routing/getrouting.xml', 'inner.php'));
		$grid->setOption('sortname', 'rbase');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$filters = array(
			'' => '- '.$eLang->get('ANY').' -',
			'frontpage' => $eLang->get('HOME'),
			'component' => 'Component',
			'dir' => $eLang->get('DIRECTORY'),
			'page' => $eLang->get('PAGE')
		);
		$grid->addColumn($eLang->get('TYPE'), 'rtype', 140, true, 'auto', $filters);
		$grid->addColumn($eLang->get('SOURCE'), 'rbase', 180, true, 'auto');
		$grid->addColumn($eLang->get('ROUTE_TO'), 'rroute', 400, true, 'auto');
		$grid->addColumn($eLang->get('ACTIONS'), 'ractions', 140, false, 'center');
		$grid->addButton($eLang->get('NEW'), 'new', 'add', 'addroute');
		$grid->addSearch($eLang->get('ROUTE_TO'), 'rroute', true);

		echo '<h2>'.$eLang->get('ELXIS_ROUTER')."</h2>\n";
?>
		<div id="routebaseurl" style="display:none; visibility:hidden;"><?php echo $elxis->makeAURL('cpanel:routing/', 'inner.php'); ?></div>

<?php 
		$grid->render();
		unset($grid);
	}


	/*************************/
	/* LIST SYSTEM LOGS HTML */
	/*************************/
	public function listlogs() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('syslogs', $eLang->get('SYSLOG_FILES'));
		$grid->setOption('url', $elxis->makeAURL('cpanel:logs/getlogs.xml', 'inner.php'));
		$grid->setOption('sortname', 'lastmodified');
		$grid->setOption('sortorder', 'desc');
		$grid->setOption('showToggleBtn', false);
		$grid->setOption('singleSelect', true);
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$filters = array(
			'' => '- '.$eLang->get('ANY').' -',
			'notice' => $eLang->get('NOTICE'),
			'warning' => $eLang->get('WARNING'),
			'error' => $eLang->get('ERROR'),
			'other' => $eLang->get('OTHER')
		);

		$grid->addColumn($eLang->get('TYPE'), 'type', 200, true, 'auto', $filters);
		$grid->addColumn($eLang->get('FILENAME'), 'filename', 180, true, 'auto');
		$grid->addColumn($eLang->get('PERIOD'), 'yearmonth', 120, true, 'auto');
		$grid->addColumn($eLang->get('LAST_MODIFIED'), 'lastmodified', 180, true, 'auto');
		$grid->addColumn($eLang->get('SIZE'), 'size', 120, true, 'auto');
		$grid->addButton($eLang->get('VIEW'), 'viewlog', 'view', 'logaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DOWNLOAD'), 'downloadlog', 'download', 'logaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('CLEAR_FILE'), 'clearlog', 'clear', 'logaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'deletelog', 'delete', 'logaction');
		$grid->addSeparator();

		$messages = array();
		switch ($elxis->getConfig('ERROR_LOG')) {
			case 0: $messages[] = '<div class="elx_smwarning">'.$eLang->get('ERROR_LOG_DISABLED').'</div>'; break;
			case 1: $messages[] = '<div class="elx_sminfo">'.$eLang->get('LOG_ENABLE_ERR').'</div>'; break;
			case 2: $messages[] = '<div class="elx_sminfo">'.$eLang->get('LOG_ENABLE_ERRWARN').'</div>'; break;
			case 3: $messages[] = '<div class="elx_sminfo">'.$eLang->get('LOG_ENABLE_ERRWARNNTC').'</div>'; break;
			default: break;
		}
		if ($elxis->getConfig('LOG_ROTATE') == 1) {
			$messages[] = '<div class="elx_sminfo">'.$eLang->get('LOGROT_ENABLED').'</div>';
		} else {
			$messages[] = '<div class="elx_smwarning">'.$eLang->get('LOGROT_DISABLED').'</div>';
		}

		eFactory::getDocument()->addStyle('.cboxIframe { overflow:auto; }');
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function logaction(task, grid) {
			if (task == 'viewlog') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo addslashes($eLang->get('NO_ITEMS_SELECTED')); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var fname = items[0].id.substr(3);
					var frlink = '<?php echo $elxis->makeAURL('cpanel:logs/view.html', 'inner.php'); ?>?fname='+fname;
					$.colorbox({iframe:true, top:'160px', width:'800px', height:'450px', href:frlink});
				}
			} else if (task == 'downloadlog') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var fname = items[0].id.substr(3);
					var winurl = '<?php echo $elxis->makeAURL('cpanel:logs/download', 'inner.php'); ?>?fname='+fname;
					elxPopup(winurl, 400, 200, 'download');
				}
			} else if ((task == 'clearlog') || (task == 'deletelog')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var items = $('.trSelected',grid);
					var fname = items[0].id.substr(3);
					if (((task == 'clearlog') && confirm('<?php echo addslashes($eLang->get('CLEAR_FILE_WARN')); ?>')) || ((task == 'deletelog') && confirm('<?php echo addslashes($eLang->get('AREYOUSURE')); ?>'))) {
						var edata = {'fname': fname};
						if (task == 'clearlog') {
							var eurl = '<?php echo $elxis->makeAURL('cpanel:logs/clear', 'inner.php'); ?>';
						} else {
							var eurl = '<?php echo $elxis->makeAURL('cpanel:logs/delete', 'inner.php'); ?>';
						}
						var successfunc = function(xreply) {
							var rdata = new Array();
							rdata = xreply.split('|');
							var rok = parseInt(rdata[0]);
							if (rok == 1) {
								$("#syslogs").flexReload();
							} else {
								alert(rdata[1]);
							}
						}
						elxAjax('POST', eurl, edata, null, null, successfunc, null);
					}
				}
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>
<?php 
		echo '<h2>'.$eLang->get('LOGS')."</h2>\n";

		$grid->render();
		unset($grid);

		foreach ($messages as $message) {
			echo $message."\n";
		}
	}


	/***************************/
	/* HTML LIST DEFENDER BANS */
	/***************************/
	public function listBanned($ban) {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo '<tr><th colspan="5">'.$eLang->get('DEFENDER_BANS').'</th></tr>'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter">#'."</th>\n";
		echo '<th class="elx_th_sub">IP'."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('TIMES_BLOCKED')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('REFER_CODE')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('DATE')."</th>\n";
		echo "</tr>\n";
		$k = 0;
		if ($ban) {
			$i = 1;
			foreach ($ban as $ip => $row) {
				$ip = str_replace('x', '.', $ip);
				$ip = str_replace('y', ':', $ip);
				$times_txt = ($row['times'] >= 3) ? '<span style="font-weight:bold; color: #ff0000;">'.$row['times'].'</span>' : $row['times'];
				$date_txt = $eDate->formatDate($row['date'], $eLang->get('DATE_FORMAT_12'));
				echo '<tr class="elx_tr'.$k.'">'."\n";
				echo '<td class="elx_td_center">'.$i."</td>\n";
				echo '<td>'.$ip."</td>\n";
				echo '<td class="elx_td_center">'.$times_txt."</td>\n";
				echo '<td>'.$row['refcode']."</td>\n";
				echo '<td>'.$date_txt."</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
				$i++;
			}
		} else {
			echo '<tr class="elx_tr'.$k.'"><td colspan="5">'.$eLang->get('NO_RESULTS')."</td></tr>\n";
		}
		echo "</table>\n";
		echo "</div>\n";
	}


	/**************************/
	/* LIST CACHED ITEMS HTML */
	/**************************/
	public function listcache() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$align = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		if ($elxis->getConfig('REPO_PATH') == '') {
			$cachedir = ELXIS_PATH.'/repository/cache/';
		} else {
			$cachedir = rtrim($elxis->getConfig('REPO_PATH'), '/').'/cache/';
		}

		echo '<h2>'.$eLang->get('CACHE')."</h2>\n";
		if (!file_exists($cachedir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_EXIST'), '<strong>'.$cachedir.'</strong>');
			echo '<div class="elx_warning">'.$txt."</div>\n";
		} elseif (!is_writable($cachedir)) {
			$txt = sprintf($eLang->get('FOLDER_NOT_WRITE'), '<strong>'.$cachedir.'</strong>');
			echo '<div class="elx_warning">'.$txt."</div>\n";
		} else {
			$txt = sprintf($eLang->get('CACHE_SAVED_INTO'), '<strong>'.$cachedir.'</strong>');
			echo '<div class="elx_info elx_close">'.$txt."</div>\n";
		}

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('cacheditems', $eLang->get('CACHED_ITEMS'));
		$grid->setOption('url', $elxis->makeAURL('cpanel:cache/getcache.xml', 'inner.php'));
		$grid->setOption('singleSelect', false);
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('sortname', 'item');
		$grid->setOption('sortorder', 'asc');
		$grid->addColumn($eLang->get('ITEM'), 'item', 540, true, 'auto');
		$grid->addColumn($eLang->get('TYPE'), 'type', 90, false, 'auto');
		$grid->addColumn($eLang->get('UPDATED_BEFORE'), 'dt', 240, true, 'auto');
		$grid->addColumn($eLang->get('SIZE'), 'size', 100, true, 'auto');
		$grid->addButton($eLang->get('TOGGLE_SELECTED'), 'togglerows', 'toggle', 'cacheaction');
		$grid->addSeparator();
		$grid->addButton($eLang->get('DELETE'), 'delcache', 'delete', 'cacheaction');
		$filters = array(0 => $eLang->get('FILE'), 1 => 'APC');
		$grid->addFilter($eLang->get('TYPE'), 'ctype', $filters, 0);
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		function cacheaction(task, grid) {
			if (task == 'delcache') {
				if ($('.trSelected', grid).length > 0) {
					if (confirm('<?php echo $eLang->get('DELETE_SEL_ITEMS'); ?>')) {
						var items = $('.trSelected',grid);
						var itemlist = '';
						for(i=0;i<items.length;i++){
							itemlist+= items[i].id.substr(3)+",";
						}
						$.ajax({
							type: "POST",
							dataType: "html",
							url: '<?php echo $elxis->makeAURL('cpanel:cache/delcache', 'inner.php'); ?>',
							data: "items="+itemlist,
							success: function(xreply){
								var rdata = new Array();
								rdata = xreply.split('|');
								var rok = parseInt(rdata[0]);
								if (rok > 0) {
									$("#cacheditems").flexReload();
								} else {
									if (typeof rdata[1] === "undefined") {
										alert('Action failed!');
									} else {
										alert(rdata[1]);
									}
								}
							}
						});
					}
				} else {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				}
			} else if (task == 'togglerows') {
				$('tr',grid).toggleClass('trSelected');
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
	}

}

?>