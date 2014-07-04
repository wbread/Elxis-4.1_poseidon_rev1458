<?php 
/**
* @version		$Id: mod_adminmenu.php 1440 2013-05-07 11:18:58Z datahell $
* @package		Elxis
* @subpackage	Module Administration menu
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('modadminMenu', false)) {
	class modadminMenu {

		private $items = array();
		private $show_icons = 1;
		private $expand_ctg = 1;
		private $expand_com = 1;
		private $lock = false;
		

		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->getParams($params);
		}


		/*************************/
		/* GET MODULE PARAMETERS */
		/*************************/
        private function getParams($params){
            $this->show_icons = (int)$params->get('show_icons', 1);
            $this->expand_ctg = (int)$params->get('expand_ctg', 1);
            $this->expand_com = (int)$params->get('expand_com', 1);
        }
  
  
		/****************************/
		/* REGISTER A NEW MENU ITEM */
		/****************************/
		private function setItem($title, $link='', $icon='', $target='', $separator=false) {
			$item = new stdClass;
			$item->title = $title;
			$item->link = $link;
			$item->icon = $icon;
			$item->target = $target;
			$item->separator = (bool)$separator;
			$item->children = array();
			return $item;
		}


		/**********************/
		/* COLLECT MENU ITEMS */
		/**********************/
		private function collect() {
			$eURI = eFactory::getURI();

			$segs = $eURI->getSegments();
			$n = count($segs);
			if ($n > 0) {
				$last_segment = $segs[$n - 1];
				if (in_array($last_segment, array('add.html', 'edit.html', 'new.html', 'config.html'))) { $this->lock = true; }
				unset($last_segment);
			}
			unset($segs, $n);

			if ($this->lock) {
				$this->items[] = $this->setItem(eFactory::getLang()->get('HOME'));
			} else {
				$this->items[] = $this->setItem(eFactory::getLang()->get('HOME'), $eURI->makeAURL());
			}

			$this->makeSite();
			$this->makeContent();
			$this->makeMenu();
			$this->makeUsers();
			$this->makeExtensions();
			$this->makeSystem();
		}


		/***********************/
		/* MAKE "MENU" SECTION */
		/***********************/
		private function makeMenu() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$db = eFactory::getDB();

			if ($elxis->acl()->check('component', 'com_emenu', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('MENU'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}
			$icon = $elxis->icon('menu', 16);
			$menu->children[] = $this->setItem($eLang->get('MENUS_MANAGER'), $elxis->makeAURL('emenu:/'), $icon);

			$section = 'frontend';
			$sql = "SELECT ".$db->quoteId('collection')." FROM ".$db->quoteId('#__menu')
			."\n WHERE ".$db->quoteId('section')." = :xsection GROUP BY ".$db->quoteId('collection')
			."\n ORDER BY ".$db->quoteId('collection')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xsection', $section, PDO::PARAM_STR);
			$stmt->execute();
			$collections = $stmt->fetchCol();
			if ($collections) {
				foreach ($collections as $collection) {
					$link = $elxis->makeAURL('emenu:mitems/'.$collection.'.html');
					$menu->children[] = $this->setItem($collection, $link, $icon);
				}
			}

			$this->items[] = $menu;
		}


		/*****************************/
		/* MAKE "EXTENSIONS" SECTION */
		/*****************************/
		private function makeExtensions() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();
			$db = eFactory::getDB();

			$menu = $this->setItem($eLang->get('EXTENSIONS'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}

			if ($elxis->acl()->check('component', 'com_extmanager', 'manage') > 0) {
				$c = $elxis->acl()->check('com_extmanager', 'components', 'install');
				$c += $elxis->acl()->check('com_extmanager', 'modules', 'install');
				$c += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
				$c += $elxis->acl()->check('com_extmanager', 'templates', 'install');
				$c += $elxis->acl()->check('com_extmanager', 'engines', 'install');
				$c += $elxis->acl()->check('com_extmanager', 'auth', 'install');
				if ($c > 0) {
					$icon = $elxis->icon('package', 16);
					if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE > 1)) {
						$title = $eLang->get('SYNCHRONIZE');
					} else {
						$title = $eLang->get('INSTALL').' &amp; '.$eLang->get('UPDATE');
					}
					$link = $elxis->makeAURL('extmanager:/');
					$menu->children[] = $this->setItem($title, $link, $icon);
				}

				$icon = $elxis->icon('elxis', 16);
				$link = $elxis->makeAURL('extmanager:browse/');
				$menu->children[] = $this->setItem($eLang->get('BROWSE_EDC'), $link, $icon);
				$menu->children[] = $this->setItem('', '', '', '', true);

				if ($elxis->acl()->check('com_extmanager', 'components', 'edit') > 0) {
					$icon = $elxis->icon('component', 16);
					$link = $elxis->makeAURL('extmanager:components/');
					$menu->children[] = $this->setItem($eLang->get('COMPONENTS'), $link, $icon);
				}
				if ($elxis->acl()->check('com_extmanager', 'modules', 'edit') > 0) {
					$icon = $elxis->icon('module', 16);
					$link = $elxis->makeAURL('extmanager:modules/');
					$menu->children[] = $this->setItem($eLang->get('MODULES'), $link, $icon);
				}
				if ($elxis->acl()->check('com_extmanager', 'plugins', 'edit') > 0) {
					$icon = $elxis->icon('plugin', 16);
					$link = $elxis->makeAURL('extmanager:plugins/');
					$menu->children[] = $this->setItem($eLang->get('CONTENT_PLUGINS'), $link, $icon);
				}
				if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) {
					$icon = $elxis->icon('template', 16);
					$link = $elxis->makeAURL('extmanager:templates/');
					$tpl = $this->setItem($eLang->get('TEMPLATES'), $link, $icon);

					$icon = $elxis->icon('module', 16);
					$link = $elxis->makeAURL('extmanager:templates/positions.html');
					$tpl->children[] = $this->setItem($eLang->get('MOD_POSITIONS'), $link, $icon);
					$menu->children[] = $tpl;
					unset($tpl);
				}
				if ($elxis->acl()->check('com_extmanager', 'engines', 'edit') > 0) {
					$icon = $elxis->icon('engine', 16);
					$link = $elxis->makeAURL('extmanager:engines/');
					$menu->children[] = $this->setItem($eLang->get('SEARCH_ENGINES'), $link, $icon);
				}
				if ($elxis->acl()->check('com_extmanager', 'auth', 'edit') > 0) {
					$icon = $elxis->icon('auth', 16);
					$link = $elxis->makeAURL('extmanager:auth/');
					$menu->children[] = $this->setItem($eLang->get('AUTH_METHODS'), $link, $icon);
				}
			}

			$iscore = 0;
			$sql = "SELECT ".$db->quoteId('name').", ".$db->quoteId('component')." FROM ".$db->quoteId('#__components')
			."\n WHERE ".$db->quoteId('iscore')." = :xcore ORDER BY ".$db->quoteId('name')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xcore', $iscore, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if ($rows) {
				$menu->children[] = $this->setItem('', '', '', '', true);
				$comicon = $elxis->icon('component', 16);
				foreach ($rows as $row) {
					if ($elxis->acl()->check('component', $row['component'], 'manage') > 0) {
						$component = preg_replace('/^(com_)/', '', $row['component']);
						$link = $elxis->makeAURL($component.':/');
						$com = $this->setItem($row['name'], $link, $comicon);
						if ($this->expand_com == 1) {
							$xmlmenus = $this->getComponentMenu($component);
							if ($xmlmenus) {
								$mygid = (int)$elxis->user()->gid;
								$mylevel = (int)$elxis->acl()->getLevel();
								$xmlmenu = $xmlmenus[0];
								if (is_array($xmlmenu->items) && (count($xmlmenu->items) > 0)) {
									foreach ($xmlmenu->items as $item) {
										if (($item->gid > 0) && ($item->gid <> $mygid)) { continue; }
										if ($item->alevel > $mylevel) { continue; }

										$is_separator = false;
										if ($item->menu_type == 'link') {
											$link = $elxis->makeAURL($item->link, $item->file);
										} else if ($item->menu_type == 'url') {
											$link = $item->link;
										} elseif ($item->menu_type == 'separator') {
											$link = '';
											$is_separator = true;
										} else {
											continue;
										}

										$icon = ($item->icon == '') ? $comicon : $elxis->icon($item->icon, 16);
										$xcom = $this->setItem($item->title, $link, $icon, $item->target, $is_separator);
										if (count($item->children) > 0) {
											foreach ($item->children as $level1) {
												if (($level1->gid > 0) && ($level1->gid <> $mygid)) { continue; }
												if ($level1->alevel > $mylevel) { continue; }
												$is_separator = false;
												if ($level1->menu_type == 'link') {
													$link = $elxis->makeAURL($level1->link, $level1->file);
												} else if ($level1->menu_type == 'url') {
													$link = $level1->link;
												} elseif ($level1->menu_type == 'separator') {
													$link = '';
													$is_separator = true;
												} else {
													continue;
												}
												$icon = ($level1->icon == '') ? $comicon : $elxis->icon($level1->icon, 16);
												$xcom1 = $this->setItem($level1->title, $link, $icon, $level1->target, $is_separator);
												if (count($level1->children) > 0) {
													foreach ($level1->children as $level2) {
														if (($level2->gid > 0) && ($level2->gid <> $mygid)) { continue; }
														if ($level2->alevel > $mylevel) { continue; }
														$is_separator = false;
														if ($level2->menu_type == 'link') {
															$link = $elxis->makeAURL($level2->link, $level2->file);
														} else if ($level2->menu_type == 'url') {
															$link = $level2->link;
														} elseif ($level2->menu_type == 'separator') {
															$link = '';
															$is_separator = true;
														} else {
															continue;
														}
														$icon = ($level2->icon == '') ? $comicon : $elxis->icon($level2->icon, 16);
														$xcom1->children[] = $this->setItem($level2->title, $link, $icon, $level2->target, $is_separator);
													}
												}
												$xcom->children[] = $xcom1;
												unset($xcom1);
											}
										}
										$com->children[] = $xcom;
										unset($xcom);
									}
								}
								unset($xmlmenu);
							}
							unset($xmlmenus);
						}
						$menu->children[] = $com;
						unset($com);
					}
				}
			}

			$this->items[] = $menu;
		}


		/**********************************************/
		/* GET COMPONENT'S BACKEND MENU FROM XML FILE */
		/**********************************************/
		private function getComponentMenu($component) {
			$file = ELXIS_PATH.'/components/com_'.$component.'/'.$component.'.menu.xml';
			if (!file_exists($file)) { return false; }

			elxisLoader::loadFile('includes/libraries/elxis/menu.xml.php');
			$xmenu = new elxisXMLMenu(null);
			$xmlmenus = $xmenu->getAllMenus($component, 'backend');
			return $xmlmenus;
		}


		/***********************/
		/* MAKE "SITE" SECTION */
		/***********************/
		private function makeSite() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$menu = $this->setItem($eLang->get('SITE'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}
			if ($elxis->acl()->check('com_cpanel', 'settings', 'edit') > 0) {
				$icon = $elxis->icon('settings', 16);
				$link = $elxis->makeAURL('cpanel:config.html');
				$menu->children[] = $this->setItem($eLang->get('SETTINGS'), $link, $icon);
			}

			if ($elxis->acl()->check('com_cpanel', 'multisites', 'edit') > 0) {
				$icon = $elxis->icon('multisites', 16);
				$link = $elxis->makeAURL('cpanel:multisites/');
				$menu->children[] = $this->setItem($eLang->get('MULTISITES'), $link, $icon);
			}

			if ($elxis->acl()->check('component', 'com_languages', 'manage') > 0) {
				$icon = $elxis->icon('language', 16);
				$link = $elxis->makeAURL('languages:/');
				$menu->children[] = $this->setItem($eLang->get('LANGUAGES'), $link, $icon);
			}
			if ($elxis->acl()->check('component', 'com_emedia', 'manage') > 0) {
				$icon = $elxis->icon('media', 16);
				$link = $elxis->makeAURL('emedia:/');
				$menu->children[] = $this->setItem($eLang->get('MEDIA'), $link, $icon);
			}
			if ($elxis->acl()->check('com_cpanel', 'statistics', 'view') > 0) {
				$icon = $elxis->icon('statistics', 16);
				$link = $elxis->makeAURL('cpanel:stats/');
				$menu->children[] = $this->setItem($eLang->get('STATISTICS'), $link, $icon);
			}
			$menu->children[] = $this->setItem('', '', '', '', true);
			if ($elxis->acl()->check('component', 'com_etranslator', 'manage') > 0) {
				$icon = $elxis->icon('translator', 16);
				$link = $elxis->makeAURL('etranslator:/');
				$menu->children[] = $this->setItem($eLang->get('TRANSLATOR'), $link, $icon);
			}
			if ($elxis->acl()->check('com_cpanel', 'backup', 'edit') > 0) {
				$icon = $elxis->icon('backup', 16);
				$link = $elxis->makeAURL('cpanel:backup/');
				$menu->children[] = $this->setItem($eLang->get('BACKUP'), $link, $icon);
			}
			if ($elxis->acl()->check('com_cpanel', 'cache', 'manage') > 0) {
				$icon = $elxis->icon('cache', 16);
				$link = $elxis->makeAURL('cpanel:cache/');
				$menu->children[] = $this->setItem($eLang->get('CACHE'), $link, $icon);
			}
			if ($elxis->acl()->check('com_cpanel', 'routes', 'manage') > 0) {
				$icon = $elxis->icon('network', 16);
				$link = $elxis->makeAURL('cpanel:routing/');
				$menu->children[] = $this->setItem($eLang->get('ROUTING'), $link, $icon);
			}
			if ($elxis->acl()->check('com_cpanel', 'logs', 'manage') > 0) {
				$icon = $elxis->icon('logs', 16);
				$link = $elxis->makeAURL('cpanel:logs/');
				$menu->children[] = $this->setItem($eLang->get('LOGS'), $link, $icon);
			}
			$menu->children[] = $this->setItem('', '', '', '', true);
			$icon = $elxis->icon('help', 16);
			$menu->children[] = $this->setItem($eLang->get('HELP'), 'http://www.elxis.net/docs/', $icon, '_blank');
			$this->items[] = $menu;
		}


		/**************************/
		/* MAKE "CONTENT" SECTION */
		/**************************/
		private function makeContent() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if ($elxis->acl()->check('component', 'com_content', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('CONTENT'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}

			if ($elxis->acl()->check('com_content', 'frontpage', 'edit') > 0) {
				$icon = $elxis->icon('home', 16);
				$link = $elxis->makeAURL('content:fpage/');
				$menu->children[] = $this->setItem($eLang->get('FRONTPAGE'), $link, $icon);
			}

			$icon = $elxis->icon('folder', 16);
			$link = $elxis->makeAURL('content:categories/');
			$ctg_menu = $this->setItem($eLang->get('CATEGORIES'), $link, $icon);

			if ($this->expand_ctg > 0) {
				$cats = $this->getCategories();
				if ($cats) {
					$icon = $elxis->icon('folderpage', 16);
					$link = $elxis->makeAURL('content:articles/');
					foreach ($cats as $cat) {
						$title = (eUTF::strlen($cat['title']) > 20) ? eUTF::substr($cat['title'], 0, 17).'...' : $cat['title'];
						$mitem = $this->setItem($title, $link.'?catid='.$cat['catid'], $icon);
						if ($cat['subcats']) {
							foreach ($cat['subcats'] as $subcat) {
								$title = (eUTF::strlen($subcat['title']) > 20) ? eUTF::substr($subcat['title'], 0, 17).'...' : $subcat['title'];
								$mitem->children[] = $this->setItem($title, $link.'?catid='.$subcat['catid'], $icon);
							}
						}
						$ctg_menu->children[] = $mitem;
					}
				}
			}
			
			$menu->children[] = $ctg_menu;
			unset($ctg_menu);

			if ($elxis->acl()->check('com_content', 'category', 'add') > 0) {
				$icon = $elxis->icon('add', 16);
				$link = $elxis->makeAURL('content:categories/add.html');
				$menu->children[] = $this->setItem($eLang->get('NEW_CATEGORY'), $link, $icon);
			}

			$icon = $elxis->icon('document', 16);
			$link = $elxis->makeAURL('content:articles/');
			$menu->children[] = $this->setItem($eLang->get('ALL_ARTICLES'), $link, $icon);

			$icon = $elxis->icon('document', 16);
			$link = $elxis->makeAURL('content:articles/?catid=0');
			$menu->children[] = $this->setItem($eLang->get('AUTONOMOUS_PAGES'), $link, $icon);


			if ($elxis->acl()->check('com_content', 'article', 'add') > 0) {
				$icon = $elxis->icon('add', 16);
				$link = $elxis->makeAURL('content:articles/add.html');
				$menu->children[] = $this->setItem($eLang->get('NEW_ARTICLE'), $link, $icon);
			}

			$this->items[] = $menu;
		}


		/**************************/
		/* GET CONTENT CATEGORIES */
		/**************************/
		private function getCategories() {
			$elxis = eFactory::getElxis();
			$db = eFactory::getDB();

			$parent = 0;
			$cats = array();
			$lowlevel = $elxis->acl()->getLowLevel();
			$exactlevel = $elxis->acl()->getExactLevel();

			$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('title').", ".$db->quoteId('alevel')." FROM ".$db->quoteId('#__categories')
			."\n WHERE ".$db->quoteId('parent_id')." = :xpar"
			."\n ORDER BY ".$db->quoteId('title')." ASC";
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':xpar', $parent, PDO::PARAM_INT);
			$stmt->execute();
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if (!$rows) { return $cats; }

			foreach ($rows as $row) {
				if (($row['alevel'] > $lowlevel) && ($row['alevel'] <> $exactlevel)) { continue; }
				$cat = array('catid' => $row['catid'], 'title' => $row['title'], 'subcats' => array());
				if ($this->expand_ctg > 1) {
					$stmt->bindParam(':xpar', $row['catid'], PDO::PARAM_INT);
					$stmt->execute();
					$cat['subcats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
				}
				$cats[] = $cat;
			}
			return $cats;
		}
	

		/************************/
		/* MAKE "USERS" SECTION */
		/************************/
		private function makeUsers() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if ($elxis->acl()->check('component', 'com_user', 'manage') < 1) { return; }

			$menu = $this->setItem($eLang->get('USERS'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}

			$icon = $elxis->icon('users', 16);
			$link = $elxis->makeAURL('user:users/');
			$menu->children[] = $this->setItem($eLang->get('MANAGE_USERS'), $link, $icon);

			if ($elxis->acl()->check('com_user', 'groups', 'manage') > 0) {
				$icon = $elxis->icon('usergroup', 16);
				$link = $elxis->makeAURL('user:groups/');
				$menu->children[] = $this->setItem($eLang->get('USER_GROUPS'), $link, $icon);
			}

			if ($elxis->acl()->check('com_user', 'acl', 'manage') > 0) {
				$icon = $elxis->icon('security', 16);
				$link = $elxis->makeAURL('user:acl/');
				$menu->children[] = $this->setItem($eLang->get('ACCESS_MANAGER'), $link, $icon);
			}

			$this->items[] = $menu;
		}


		/************************/
		/* MAKE "SYSTEM" SECTION */
		/************************/
		private function makeSystem() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			$menu = $this->setItem($eLang->get('SYSTEM'));
			if ($this->lock) {
				$this->items[] = $menu;
				return;
			}

			$icon = $elxis->icon('elxis', 16);
			$link = $elxis->makeAURL('cpanel:sys/elxis.html');
			$menu->children[] = $this->setItem($eLang->get('ELXIS_INFO'), $link, $icon);

			$icon = $elxis->icon('php', 16);
			$link = $elxis->makeAURL('cpanel:sys/php.html');
			$menu->children[] = $this->setItem($eLang->get('PHP_INFO'), $link, $icon);

			$this->items[] = $menu;
		}


		/*****************/
		/* POPULATE MENU */
		/*****************/
		private function populate($items, $level=0) {
			$elxis = eFactory:: getElxis();
			if (!$items) { return; }

			if ($level == 0) {
				$lclass = ($this->lock) ? ' elx_menu_lock' : '';
				echo '<ul id="elx_amenu" class="elx_menu'.$lclass.'">'."\n";
			} else {
				echo "<ul>\n";
			}

			$deficon = $elxis->icon('not_found', 16);
			foreach ($items as $key => $item) {
				$c = count($item->children);
				$aclass = '';
				if (($level > 0) && ($c > 0)) { $aclass = ' class="elx_menu-sub"'; }
				$atarget = '';
				if ($item->target != '') { $atarget = ' target="'.$item->target.'"'; }

				$icontxt = '';
				if ($this->show_icons == 1) {
					if ($level > 0) {
						$icontxt = ($item->icon == '') ? '<img src="'.$deficon.'" alt="icon" /> ' : '<img src="'.$item->icon.'" alt="icon" /> ';
					}
				}

				echo '<li>';
				if ($item->separator === true) {
					echo '<a href="javascript:void(null);"'.$aclass.' style="cursor:default;"><hr style="background-color:#bbb; height:1px; margin:0; padding:0 2px;" /></a>';
				} else if ($item->link != '') {
					echo '<a href="'.$item->link.'" title="'.$item->title.'"'.$aclass.''.$atarget.'>'.$icontxt.$item->title.'</a>';
				} else {
					echo '<a href="javascript:void(null);" title="'.$item->title.'"'.$aclass.''.$atarget.'>'.$icontxt.$item->title.'</a>';
				}
				if (count($item->children) > 0) {
					$this->populate($item->children, $level+1);
				}
				echo "</li>\n";
			}
			echo "</ul>\n";
		}


		/********************/
		/* RUN FOREST, RUN! */
		/********************/
		public function run() {
			if (!defined('ELXIS_ADMIN')) {
				echo '<div class="elx_warning">'.eFactory::getLang()->get('MOD_AVAILABLE_ADMIN')."</div>\n";
				return;
			}

			if (ELXIS_INNER == 1) { return; }

			$this->collect();
			$this->populate($this->items, 0);
		}

	}
}


$amenu = new modadminMenu($params);
$amenu->run();
unset($amenu);

?>