<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Module Who Is Online
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modWhoIsOnline', false)) {
	class modWhoIsOnline {

		private $mode = 0;
		private $avatarw = 40;
		private $ontime = 0;
		private $apc = false;
		private $ts = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$elxis = eFactory::getElxis();

			$this->mode = (int)$params->get('mode', 0);
			$this->avatarw = (int)$params->get('avatarw', 40);
			if ($this->avatarw < 20) { $this->avatarw = 40; }
			$this->ontime = (int)$params->get('ontime', 0);
			$cache = (int)$params->get('cache', 0);
			$this->ts = eFactory::getDate()->getTS();
			if (($elxis->getConfig('CACHE') == 0) || ($cache < 1)) {
				if ($elxis->getConfig('APC') == 1) { $this->apc = true; }
			}
			if (ELXIS_MOBILE == 1) {
				$this->mode = 0;
				$this->ontime = 0;
			}
		}


		/********************/
		/* MODULE EXECUTION */
		/********************/         
		public function run() {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();

			$online = $this->getOnlineUsers();
			if ($online['guests'] <> 1) {
				if ($online['users'] <> 1) {
					$txt = sprintf($eLang->get('GUESTS_USERS_ON'), $online['guests'], $online['guests']);
				} else {
					$txt = sprintf($eLang->get('GUESTS_USER_ON'), $online['guests']);
				}
			} else {
				if ($online['users'] <> 1) {
					$txt = sprintf($eLang->get('GUEST_USERS_ON'), $online['users']);
				} else {
					$txt = $eLang->get('GUEST_USER_ON');
				}
			}

			echo '<div class="whoisonline">'."\n";
			echo '<p>'.$txt."</p>\n";
			if ($this->mode == 1) {
				if (count($online['data']) > 0) {
            		$access = $elxis->acl()->check('com_user', 'profile', 'view');
					$linkbase = $elxis->makeURL('user:members/');
					echo '<div class="whoisonline_thumbs">'."\n";
					foreach ($online['data'] as $user) {
						if ($this->ontime == 1) {
							$title = $user['name'].' - '.$eLang->get('ONLINE_FOR').' '.$this->humanTime($user['time']);
						} else {
							$title = $user['name'];
						}
						if ($access == 2) {
							echo '<a href="'.$linkbase.$user['uid'].'.html" title="'.$title.'">';
							echo '<img src="'.$user['avatar'].'" alt="avatar" width="'.$this->avatarw.'" height="'.$this->avatarw.'" />';
							echo "</a> \n";
						} else {
							echo '<img src="'.$user['avatar'].'" alt="'.$user['name'].'" title="'.$title.'" width="'.$this->avatarw.'" height="'.$this->avatarw.'" /> '."\n";
						}
					}
					echo "</div>\n";
				}
			}
			echo "</div>\n";
		}


		/********************/
		/* GET ONLINE USERS */
		/********************/
		private function getOnlineUsers() {
			if ($this->apc == true) {
				$online = elxisAPC::fetch('list'.$this->mode, 'whosonline');
				if ($online !== false) { return $online; }
			}

			$db = eFactory::getDB();
			$elxis = eFactory::getElxis();

			$online = array('guests' => 0, 'users' => 0, 'data' => array());

			$startts = $this->ts - $elxis->getConfig('SESSION_LIFETIME');

			$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM ".$db->quoteId('#__session')
			."\n WHERE ".$db->quoteId('last_activity')." > :ts AND ".$db->quoteId('uid').' > 0';
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':ts', $startts, PDO::PARAM_INT);
			$stmt->execute();
			$online['users'] = (int)$stmt->fetchResult();

			$sql = "SELECT COUNT(".$db->quoteId('uid').") FROM ".$db->quoteId('#__session')
			."\n WHERE ".$db->quoteId('last_activity')." > :ts AND ".$db->quoteId('uid').' = 0';
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':ts', $startts, PDO::PARAM_INT);
			$stmt->execute();
			$online['guests'] = (int)$stmt->fetchResult();

			if (($this->mode == 1) && ($online['users'] > 0)) {
				$sql = "SELECT s.uid, s.first_activity, u.firstname, u.lastname, u.uname, u.avatar"
				."\n FROM ".$db->quoteId('#__session')." s"
				."\n LEFT JOIN ".$db->quoteId('#__users')." u ON u.uid=s.uid"
				."\n WHERE s.last_activity > :ts AND s.uid > 0"
				."\n ORDER BY s.last_activity DESC";
				$stmt = $db->prepareLimit($sql, 0, 30);
				$stmt->bindParam(':ts', $startts, PDO::PARAM_INT);
				$stmt->execute();
				$dbrows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				
				if ($dbrows) {
					$relpath = 'media/images/avatars/';
					if (defined('ELXIS_MULTISITE')) {
						if (ELXIS_MULTISITE > 1) { $relpath = 'media/images/site'.ELXIS_MULTISITE.'/avatars/'; }
					}
					foreach ($dbrows as $dbrow) {
						$time =  $this->ts - $dbrow['first_activity'];
						if ($elxis->getConfig('REALNAME') == 1) {
							$name = $dbrow['firstname'].' '.$dbrow['lastname'];
						} else {
							$name = $dbrow['uname'];
						}

						if ((trim($dbrow['avatar']) != '') && file_exists(ELXIS_PATH.'/'.$relpath.$dbrow['avatar'])) {
							$avatar = $elxis->secureBase().'/'.$relpath.$dbrow['avatar'];
						} else {
							$avatar = $elxis->secureBase().'/components/com_user/images/noavatar.png';
						}

						$online['data'][] = array('uid' => $dbrow['uid'], 'name' => $name, 'avatar' => $avatar, 'time' => $time);
					}
				}
			}

			if ($this->apc == true) {
				elxisAPC::store('list'.$this->mode, 'whosonline', $online, 120);
			}

			return $online;
		}


		/**********************************/
		/* HUMAN FRIENDLY TIME DIFERRENCE */
		/**********************************/
		private function humanTime($dt) {
			$eLang = eFactory::getLang();

			$h = floor($dt / 3600);
			$rest = $dt - ($h * 3600);
			$m = floor($rest / 60);

			if ($h == 0) {
				if ($m > 1) {
					$out = $m.' '.$eLang->get('MINUTES');
				} else {
					$out = '1 '.$eLang->get('MINUTE');
				}
			} else  if ($h == 1) {
				$out = '1 '.$eLang->get('HOUR');
				if ($m > 1) {
					$out .= ' '.$eLang->get('AND').' '.$m.' '.$eLang->get('MINUTES');
				} else if ($m == 1) {
					$out .= ' '.$eLang->get('AND').' 1 '.$eLang->get('MINUTE');
				}
			} else {
				$out = $h.' '.$eLang->get('HOURS');
				if ($m > 1) {
					$out .= ' '.$eLang->get('AND').' '.$m.' '.$eLang->get('MINUTES');
				} else if ($m == 1) {
					$out .= ' '.$eLang->get('AND').' 1 '.$eLang->get('MINUTE');
				}
			}

			return $out;
		}

    }

}

$modwho = new modWhoIsOnline($params);
$modwho->run();
unset($modwho);

?>