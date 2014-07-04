<?php 
/**
* @version		$Id: mod_adminusers.php 994 2012-03-30 17:35:45Z datahell $
* @package		Elxis
* @subpackage	Module Administration Online users
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminUsers', false)) {
	class modadminUsers {

		private $userstype = 0;
		private $limit = 10;
		private $order = 0;
		private $moduleId = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			if (isset($_GET['adustp'])) {
				$this->userstype = (int)$_GET['adustp'];
				if ($this->userstype < 0) { $this->userstype = 0; }
				if ($this->userstype > 2) { $this->userstype = 0; }				
			}
			$this->getParams($params);
			$this->moduleId = $elxmod->id;
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
            $this->limit = (int)$params->get('limit', 0);
            if ($this->limit < 5) { $this->limit = 10; }
            $this->order = (int)$params->get('order', 0);
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

			eFactory::getDocument()->addScriptLink($elxis->secureBase().'/modules/mod_adminusers/includes/adminusers.js');

			$online = $this->getOnlineUsers();

			if (($online->users == 1) && ($online->guests == 1)) {
					$total_desc = sprintf($eLang->get('U_G_ONLINE'), '<strong>1</strong>', '<strong>1</strong>');
			} else if ($online->users == 1) {
				$total_desc = sprintf($eLang->get('U_NG_ONLINE'), '<strong>1</strong>', '<strong>'.$online->guests.'</strong>');
			} else if ($online->guests == 1) {
				$total_desc = sprintf($eLang->get('NU_G_ONLINE'), '<strong>'.$online->users.'</strong>', '<strong>1</strong>');
			} else {
				$total_desc = sprintf($eLang->get('NU_NG_ONLINE'), '<strong>'.$online->users.'</strong>', '<strong>'.$online->guests.'</strong>');
			}
?>
			<div class="gbox">
				<div class="gbox_inner">
					<div class="gbox_head">
						<ul class="gbox_tabs">
							<li>
								<a href="<?php echo $elxis->makeAURL('cpanel:/'); ?>?adustp=0"<?php if ($this->userstype == 0) { echo ' class="gbox_selected"'; } ?>>
									<?php echo $eLang->get('ALL_USERS'); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo $elxis->makeAURL('cpanel:/'); ?>?adustp=1"<?php if ($this->userstype == 1) { echo ' class="gbox_selected"'; } ?>>
									<?php echo $eLang->get('USERS'); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo $elxis->makeAURL('cpanel:/'); ?>?adustp=2"<?php if ($this->userstype == 2) { echo ' class="gbox_selected"'; } ?>>
									<?php echo $eLang->get('GUESTS'); ?>
								</a>
							</li>
						</ul>
						<h3><?php echo $eLang->get('ONLINE_USERS'); ?></h3>
					</div>
					<div class="gbox_contents">
						<p style="margin:0 0 5px 0; padding:0;"><?php echo $total_desc; ?></p>
<?php 
						$this->populateUsers($online);

						if ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0) {
							if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
								if ($elxis->acl()->check('module', 'mod_adminusers', 'manage', $this->moduleId) > 0) {
									$editLink = $elxis->makeAURL('extmanager:modules/edit.html').'?id='.$this->moduleId;
									echo '<div class="gbox_footer">'."\n";
									echo '<a href="'.$editLink.'" class="gbox_edit_link" title="'.$eLang->get('CHANGE_MOD_PARAMS').'">'.$eLang->get('EDIT')."</a>\n";
									echo "</div>\n";
								}
							}
						}
?>
					</div>
				</div>
			</div>
			<div id="aduserselxisaurl" style="display:none; visibility:hidden;" dir="ltr"><?php echo $elxis->makeAURL(); ?></div>
<?php 
		}


		/******************************/
		/* DISPLAY ONLINE USERS TABLE */
		/******************************/
		private function populateUsers($online) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();

			if (($online->rows) && (count($online->rows) > 0)) {
				$myip = eFactory::getSession()->getIP();
				echo '<div class="elx_tbl_wrapper">'."\n";
				echo '<table id="adusersonlinetbl" cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
				echo "<tr>\n".'<th class="elx_th_sub">'.$eLang->get('USER')."</th>\n";
				echo '<th class="elx_th_sub">'.$eLang->get('GROUP')."</th>\n";
				echo '<th class="elx_th_subcenter">'.$eLang->get('ONLINE_TIME')."</th>\n";
				echo '<th class="elx_th_subcenter">'.$eLang->get('ACTIONS')."</th>\n</tr>\n";
				$k = 1;
				$r = 1;
				foreach ($online->rows as $row) {
					$txt = $row->uname.'|'.$eLang->get('IDLE_TIME').': '.$row->time_idle.'<br />
					'.$eLang->get('CLICKS').': '.$row->clicks.'<br />
					'.$eLang->get('BROWSER').': '.$row->browser.'<br />
					'.$eLang->get('OS').': '.$row->platform.' / '.$row->os_name.'<br />
					'.$eLang->get('PAGE').': '.$row->current_page.'<br />
					IP: '.$row->ip_address;
					if ($row->gid <> 7) {
						$txt .= '<br />'.$eLang->get('AUTHENTICATION').': '.$row->login_method;
					}
					$txt = htmlspecialchars($txt);

					echo '<tr id="adusersrow'.$r.'" class="elx_tr'.$k.'">'."\n";
					$colorstyle = ($myip == $row->ip_address) ? ' color:#cc0000;' : '';
					echo "<td>\n";
					echo '<a href="javascript:void(null);" class="elx_tooltip" title="'.$txt.'" style="text-decoration:none;'.$colorstyle.'">'.$row->uname."</a>\n";
					echo "</td>\n";
					echo '<td>'.$row->groupname.'</td><td style="text-align:center;">'.$row->time_online."</td>\n";
					echo '<td style="text-align:center;">';
					if (($elxis->user()->gid == 1) && ($row->gid <> 7) && ($row->uid <> $elxis->user()->uid)) {
						echo '<a href="javascript:void(null);" onclick="adusersLogout('.$row->uid.', '.$row->gid.', \''.$row->login_method.'\', \''.base64_encode($row->ip_address).'\', \''.$row->first_activity.'\', '.$r.');" title="'.$eLang->get('FORCE_LOGOUT').'"><img src="'.$elxis->icon('logout', 16).'" alt="logout" border="0" /></a>';
					} else {
						echo '<img src="'.$elxis->icon('logout_off', 16).'" alt="logout" title="'.$eLang->get('FORCE_LOGOUT').'" />';
					}
					if (($elxis->user()->gid == 1) && ($row->uid <> $elxis->user()->uid)) {
						echo ' <a href="javascript:void(null);" onclick="adusersBanIP(\''.base64_encode($row->ip_address).'\', '.$r.');" title="'.$eLang->get('BAN_IP').'"><img src="'.$elxis->icon('user_remove', 16).'" alt="ban" border="0" /></a>'."\n";
					} else {
						echo ' <img src="'.$elxis->icon('user_remove_off', 16).'" alt="ban" title="'.$eLang->get('BAN_IP').'" />'."\n";
					}
					echo "</td>\n</tr>\n";
					$k = 1 - $k;
					$r++;
				}
				echo '</table>'."\n";
				echo "</div>\n";
			} else {
				echo '<div class="elx_warning">'.$eLang->get('NO_RESULTS')."</div>\n";
			}
		}


		/********************/
		/* GET ONLINE USERS */
		/********************/
		private function getOnlineUsers() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$online = $this->getDbUsers();

			if ($online->rows) {
				$browser = $elxis->obj('browser');
				$nowts = eFactory::getDate()->getTS();
				for($i=0; $i<count($online->rows); $i++) {
					$row = $online->rows[$i];

					$dt = $nowts - $row->first_activity;
					$ldt = $nowts - $row->last_activity;

					$min = floor($dt/60);
					$sec = $dt - ($min * 60);
					$online->rows[$i]->time_online = $min.':'.sprintf("%02d", $sec);

					$min = floor($ldt/60);
					$sec = $ldt - ($min * 60);
					$online->rows[$i]->time_idle = $min.':'.sprintf("%02d", $sec);

					$browser_info = $browser->getBrowser($row->user_agent);
					$online->rows[$i]->browser = $browser_info['browser'].' '.$browser_info['version'];
					$online->rows[$i]->platform = $browser_info['platform'];
					$online->rows[$i]->os_name = $browser_info['os_name'];

					switch ($row->gid) {
						case 1:
						$online->rows[$i]->groupname = $eLang->get('ADMINISTRATOR');
						break;
						case 6:
							$online->rows[$i]->groupname = $eLang->get('EXTERNALUSER');
							$online->rows[$i]->uname = sprintf($eLang->get('EXT_UNAME'), ucfirst($row->login_method));
						break;
						case 7: case 0:
							$online->rows[$i]->groupname = $eLang->get('GUEST');
							if ($browser_info['robot']) {
								$online->rows[$i]->uname = $browser_info['browser'];
							} else {
								$online->rows[$i]->uname = $eLang->get('GUEST');
							}
						break;
						default:break;
					}

					if ($row->uname == '') { $online->rows[$i]->uname = $eLang->get('GUEST'); }
					if ($row->groupname == '') { $online->rows[$i]->groupname = $eLang->get('GUEST'); }
				}
			}

			return $online;
		}


		/*************************************************/
		/* GET ONLINE USERS AND GUESTS FROM THE DATABASE */
		/*************************************************/
		private function getDbUsers() {
			$db = eFactory::getDB();

			$online = new stdClass;
			$online->users = 0;
			$online->visitors = 0;
			$online->total = 0;
			$online->rows = array();

			$ts = eFactory::getDate()->getTS() - eFactory::getElxis()->getConfig('SESSION_LIFETIME');

			$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM #__session"
			."\n WHERE last_activity > :ts AND gid <> 7";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
			$stmt->execute();
			$online->users = (int)$stmt->fetchResult();

			$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM #__session"
			."\n WHERE last_activity > :ts AND gid = 7";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
			$stmt->execute();
			$online->guests = (int)$stmt->fetchResult();

			$online->total = $online->users + $online->guests;

			switch($this->userstype) {
				case 1: $andwhere = ' AND s.gid <> 7'; break;
				case 2: $andwhere = ' AND s.gid = 7'; break;
				default: $andwhere = ''; break;
			}

			switch($this->order) {
				case 1: $orderby = 's.first_activity ASC'; break;
				case 2: $orderby = 's.clicks DESC'; break;
				default: $orderby = 's.last_activity DESC'; break;
			}

			$sql = "SELECT s.uid, s.gid, s.login_method, s.first_activity, s.last_activity, s.clicks, s.current_page,"
			."\n s.ip_address, s.user_agent, u.uname, u.groupname"
			."\n FROM #__session s"
			."\n LEFT JOIN #__users u ON u.uid=s.uid"
			."\n WHERE s.last_activity > :ts".$andwhere
			."\n ORDER BY ".$orderby;
			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			$stmt->bindParam(':ts', $ts, PDO::PARAM_INT);
			$stmt->execute();
			$online->rows = $stmt->fetchAll(PDO::FETCH_OBJ);

			return $online;		
		}

	}
}


$admusers = new modadminUsers($params, $elxmod);
$admusers->run();
unset($admusers);

?>