<?php 
/**
* @version		$Id: mod_adminarticles.php 1135 2012-05-16 20:59:26Z datahell $
* @package		Elxis
* @subpackage	Module Administration Articles
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminArticles', false)) {
	class modadminArticles {

		private $atype = 0;
		private $limit = 3;
		private $popmonths = 0;
		private $moduleId = 0;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params, $elxmod) {
			if (isset($_GET['adartp'])) {
				$this->atype = (int)$_GET['adartp'];
				if ($this->atype != 1) { $this->atype = 0; }				
			}
			$this->getParams($params);
			$this->moduleId = $elxmod->id;
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params) {
            $this->limit = (int)$params->get('limit', 3);
            if ($this->limit < 1) { $this->limit = 5; }
            $this->popmonths = (int)$params->get('popmonths', 0);
            if ($this->popmonths < 0) { $this->popmonths = 0; }
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
?>
			<div class="gbox">
				<div class="gbox_inner">
					<div class="gbox_head">
						<ul class="gbox_tabs">
							<li>
								<a href="<?php echo $elxis->makeAURL('cpanel:/'); ?>?adartp=0"<?php if ($this->atype == 0) { echo ' class="gbox_selected"'; } ?>>
									<?php echo $eLang->get('LATEST'); ?>
								</a>
							</li>
							<li>
								<a href="<?php echo $elxis->makeAURL('cpanel:/'); ?>?adartp=1"<?php if ($this->atype == 1) { echo ' class="gbox_selected"'; } ?>>
									<?php echo $eLang->get('POPULAR'); ?>
								</a>
							</li>
						</ul>
						<h3><?php echo ($this->atype == 1) ? $eLang->get('POPULAR_ARTICLES') : $eLang->get('LATEST_ARTICLES'); ?></h3>
					</div>
					<div class="gbox_contents">
<?php 
						$this->populateArticles();

						if ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0) {
							if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
								if ($elxis->acl()->check('module', 'mod_adminarticles', 'manage', $this->moduleId) > 0) {
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

<?php 
		}


		/**************************/
		/* DISPLAY ARTICLES TABLE */
		/**************************/
		private function populateArticles() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$eFiles = eFactory::getFiles();
			$eDate = eFactory::getDate();

			$rows = $this->getArticles();
			if (!$rows) {
				echo '<div class="elx_warning">'.$eLang->get('NO_RESULTS')."</div>\n";
				return;
			}

			eFactory::getDocument()->addStyleLink($elxis->secureBase().'/modules/mod_adminarticles/css/adminarticles.css');

			$defimage = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';

			if ($elxis->acl()->check('component', 'com_content', 'manage') > 0) {
				$can_edit_art = $elxis->acl()->check('com_content', 'article', 'edit');
				$can_edit_ctg = $elxis->acl()->check('com_content', 'category', 'edit');
			} else {
				$can_edit_art = 0;
				$can_edit_ctg = 0;
			}

			if ($elxis->acl()->check('component', 'com_user', 'manage') > 0) {
				$can_edit_usr = $elxis->acl()->check('com_user', 'profile', 'edit');
			} else {
				$can_edit_usr = 0;
			}

			$edit_link_art = $elxis->makeAURL('content:articles/edit.html');
			$edit_link_ctg = $elxis->makeAURL('content:categories/edit.html');
			$edit_link_usr = $elxis->makeAURL('user:users/edit.html');

			if ($this->atype == 1) {
				if ($this->popmonths > 1) {
					$list_desc = sprintf($eLang->get('POPULAR_LAST_MONTHS'), $this->popmonths);
				} else if ($this->popmonths == 1) {
					$list_desc = $eLang->get('POPULAR_LAST_MONTH');
				} else {
					$list_desc = $eLang->get('POPULAR_ALL_TIME');
				}
			} else {
				$list_desc = $eLang->get('LATEST_ARTS_SITE');
			}

			echo '<p style="margin:0 0 5px 0; padding:0;">'.$list_desc."</p>\n";
			echo '<div class="elx_tbl_wrapper">'."\n";
			echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
			$k = 1;
			foreach ($rows as $row) {
				if ((trim($row->image) != '') && file_exists(ELXIS_PATH.'/'.$row->image)) {
					$file_info = $eFiles->getNameExtension($row->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					} else {
						$imgfile = $elxis->secureBase().'/'.$row->image;
					}
				} else {
					$imgfile = $defimage;
				}

				echo '<tr class="elx_tr'.$k.'">'."\n";
				echo '<td class="elx_td_center" width="60" style="vertical-align:top;">'."\n";
				if ($can_edit_art > 0) {
					echo '<a href="'.$edit_link_art.'?id='.$row->id.'" title="'.$eLang->get('EDIT').'">';
				}
				echo '<img src="'.$imgfile.'" class="elx_thumb" alt="preview" border="0" style="width:48px; height:48px;" />';
				if ($can_edit_art > 0) { echo '</a>'; }
				echo '<div class="modadar_hits" title="'.$row->hits.' '.$eLang->get('HITS').'">'.$row->hits."</div>\n";
				echo "</td>\n";
				echo '<td style="vertical-align:top;">'."\n";
				echo '<div style="margin:2px 0 5px 0;">'."\n";
				if ($can_edit_art > 0) {
					echo '<a href="'.$edit_link_art.'?id='.$row->id.'" title="'.$eLang->get('EDIT').'" class="gbox_head_link">'.$row->title."</a> \n";
				} else {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('NALLOW_EDIT').'" class="gbox_head_link" style="color:#666;">'.$row->title."</a> \n";
				}

				if ($row->published == 1) {
					echo '<span class="modadar_pub">'.$eLang->get('PUBLISHED')."</span>\n";
				} else {
					echo '<span class="modadar_unpub">'.$eLang->get('UNPUBLISHED')."</span>\n";
				}
				echo '</div>'."\n";
				if (trim($row->subtitle) != '') {
					$txt = $row->subtitle;
				} else {
					$txt = strip_tags($row->introtext);
					$txt = eUTF::substr($txt, 0, 180).'...';
				}
				echo '<p style="margin:0; padding:0;">'.$txt."</p>\n";
				echo '<div class="modadar_extra">';
				if ($row->catid > 0) {
					if ($can_edit_ctg > 0) {
						echo $eLang->get('IN').' <a href="'.$edit_link_ctg.'?catid='.$row->catid.'" title="'.$eLang->get('EDIT_CATEGORY').'">'.$row->cattitle."</a> \n";
					} else {
						echo $eLang->get('IN').' <a href="javascript:void(null);" title="'.$eLang->get('NALLOW_EDIT').'" style="color:#666;">'.$row->cattitle."</a> \n";
					}
				}

				if (($can_edit_usr == 2) || (($can_edit_usr == 1) && ($elxis->user()->uid == $row->created_by))) {
					echo $eLang->get('BY').' <a href="'.$edit_link_ctg.'?uid='.$row->created_by.'" title="'.$eLang->get('EDIT_USER').'">'.$row->created_by_name."</a><br />\n";
				} else {
					echo $eLang->get('BY').' <a href="javascript:void(null);" title="'.$eLang->get('NALLOW_EDIT').'" style="color:#666;">'.$row->created_by_name."</a><br />\n";
				}
				$dt = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4'));
				printf($eLang->get('CREATED_ON'), $dt);
				echo "</div>\n";
				echo "</td>\n";
				echo "</tr>\n";
				$k = 1 - $k;
			}

			echo '</table>'."\n";
			echo "</div>\n";
		}


		/**********************************/
		/* GET ARTICLES FROM THE DATABASE */
		/**********************************/
		private function getArticles() {
			$db = eFactory::getDB();

			$binds = array();
			$sql = "SELECT a.id, a.catid, a.title, a.subtitle, a.introtext, a.image, a.created, a.created_by, a.created_by_name, a.published, a.hits, c.title AS cattitle"
			."\n FROM ".$db->quoteId('#__content')." a"
			."\n LEFT JOIN ".$db->quoteId('#__categories')." c ON c.catid=a.catid";
			if ($this->atype == 1) {
				if ($this->popmonths > 0) {
					$ts = gmmktime(0, 0, 0, gmdate('m') - $this->popmonths, gmdate('d'), gmdate('Y'));
					$date = gmdate('Y-m-d H:i:s', $ts);
					$binds[] = array(':crdate', $date, PDO::PARAM_STR);
					$sql .= " WHERE a.created > :crdate ORDER BY a.hits DESC";
				} else {
					$sql .= "\n ORDER BY a.hits DESC";
				}
			} else {
				$sql .= "\n ORDER BY a.created DESC";
			}

			$stmt = $db->prepareLimit($sql, 0, $this->limit);
			if (count($binds) > 0) {
				foreach ($binds as $bind) {
					$stmt->bindParam($bind[0], $bind[1], $bind[2]);
				}
			}
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_OBJ);

            return $rows;            
        }

	}
}


$admarticles = new modadminArticles($params, $elxmod);
$admarticles->run();
unset($admarticles);

?>