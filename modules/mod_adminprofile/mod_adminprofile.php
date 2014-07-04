<?php 
/**
* @version		$Id: mod_adminprofile.php 879 2012-01-28 18:37:34Z datahell $
* @package		Elxis
* @subpackage	Module Administration profile
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminProf', false)) {
	class modadminProf {

		private $show_articles = 3;
		private $show_date = 0;
		private $lock = false;
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->getParams($params);
			$segs = eFactory::getURI()->getSegments();
			$n = count($segs);
			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html', 'configuration.html', 'settings.html'))) { $this->lock = true; }
			}
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
            $this->show_articles = (int)$params->get('show_articles', 3);
            $this->show_date = (int)$params->get('show_date', 0);
        }


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx_warning">This module is available only in Elxis administration area!'."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eDate = eFactory::getDate();

			eFactory::getDocument()->addStyleLink($elxis->secureBase().'/modules/mod_adminprofile/css/adminprofile'.$eLang->getinfo('RTLSFX').'.css');

			if ($this->lock) {
				echo '<ul class="aprofileul">'."\n";
				echo '<li class="aprofileli"><a href="javascript:void(null);" class="aproffirst">'.$elxis->user()->uname."</a></li>\n</ul>\n";
				return;
			}

			$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 40, 0, '');

			$dt = $eDate->getTS() - $elxis->session()->first_activity;
			$min = floor($dt/60);
			$sec = $dt - ($min * 60);
			echo '<ul class="aprofileul">'."\n";
			echo '<li class="aprofileli"><a href="javascript:void(null);" class="aproffirst">'.$elxis->user()->uname."</a>\n";
			echo "<ul>\n<li>\n";

			$txt = $elxis->user()->firstname.' '.$elxis->user()->lastname;
			if (eUTF::strlen($txt) > 25) { $txt = eUTF::substr($txt, 0, 22).'...'; }
			echo '<div class="aprof_head">'.$txt.'</div>';
			echo '<img src="'.$avatar.'" alt="'.$elxis->user()->uname.'" class="aprofavatar" /> ';
			echo ($elxis->user()->gid == 1) ? $eLang->get('ADMINISTRATOR')."\n" : $elxis->user()->groupname."\n";
			if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
				if ($elxis->acl()->check('com_user', 'profile', 'edit') > 0) {
					$link = $elxis->makeAURL('user:users/edit.html').'?uid='.$elxis->user()->uid;
					echo '<br /><a href="'.$link.'" title="'.$eLang->get('EDIT_PROFILE').'" class="aproflink">'.$eLang->get('MY_PROFILE').'</a>'."\n";
				}
			}

			$link = $elxis->makeAURL('cpanel:logout.html', 'inner.php');
			echo '<br /><a href="'.$link.'" class="aproflogout">'.$eLang->get('LOGOUT').'</a> <span dir="ltr" style="color:#666;">('.$min.':'.sprintf("%02d", $sec).")</span>\n";
			echo "</li>\n";

			if ($this->show_date == 1) {
				$offset = round(($eDate->getOffset() / 3600), 1);
				if ($offset > 0) {
					$offset = 'GMT +'.$offset;
				} else {
					$offset = 'GMT '.$offset;
				}
				echo '<li class="aprofseparator">&#160;</li>'."\n";
				echo "<li>\n";
				echo '<div class="aprof_head">'.$eLang->get('DATE')."</div>\n";
				echo $eDate->formatDate('now', $eLang->get('DATE_FORMAT_4'))."<br />\n";
				echo '<span style="color:#666; font-size:11px; line-height:14px;">'.$eDate->getTimezone().' ('.$offset.')</span>'."\n";
				echo "</li>\n";
			}

			if ($this->show_articles > 0) {
				echo '<li class="aprofseparator">&#160;</li>'."\n";
				echo "<li>\n";				
				echo '<div class="aprof_head">'.$eLang->get('LATEST_ARTICLES')."</div>\n";
				$articles = $this->getLatestArticles();
				if ($articles) {
					$link = $elxis->makeAURL('content:articles/edit.html');
					foreach ($articles as $article) {
						if (eUTF::strlen($article['title']) > 30) {
							$title = eUTF::substr($article['title'], 0, 27).'...';
						} else {
							$title = $article['title'];
						}
						echo '<a class="aprof_article" href="'.$link.'?id='.$article['id'].'">'.$title."</a>\n";
					}
				} else {
					echo $eLang->get('HAVENOT_WRITEART');
				}
				echo "</li>\n";
			}

			echo "</ul>\n";
			echo "</li>\n";
			echo "</ul>\n";
		}


		/******************************/
		/* GET LATEST USER'S ARTICLES */
		/******************************/
		private function getLatestArticles() {
			$db = eFactory::getDB();

			$uid = (int)eFactory::getElxis()->user()->uid;
			$sql = "SELECT ".$db->quoteId('id').", ".$db->quoteId('title')." FROM ".$db->quoteId('#__content')
			."\n WHERE ".$db->quoteId('created_by')." = :xuid"
			."\n ORDER BY ".$db->quoteId('created').' DESC';
			$stmt = $db->prepareLimit($sql, 0, $this->show_articles);
			$stmt->bindParam(':xuid', $uid, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $rows;
		}

	}
}


$aprofile = new modadminProf($params);
$aprofile->run();
unset($aprofile);

?>