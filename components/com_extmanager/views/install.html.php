<?php 
/**
* @version		$Id: install.html.php 1288 2012-09-15 18:03:19Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class installExtmanagerView extends extmanagerView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/********************************/
	/* SHOW COMPONENT CONTROL PANEL */
	/********************************/
	public function ipanel($sync) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$align = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		$types = array(
			'components' => array('icon' => 'component', 'title' => $eLang->get('COMPONENTS'), 'descr' => $eLang->get('MANAGE_COMPONENTS')),
			'modules' => array('icon' => 'module', 'title' => $eLang->get('MODULES'), 'descr' => $eLang->get('MANAGE_MODULES')),
			'plugins' => array('icon' => 'plugin', 'title' => $eLang->get('CONTENT_PLUGINS'), 'descr' => $eLang->get('MANAGE_CONTENT_PLUGINS')),
			'templates' => array('icon' => 'template', 'title' => $eLang->get('TEMPLATES'), 'descr' => $eLang->get('MANAGE_TEMPLATES')),
			'engines' => array('icon' => 'engine', 'title' => $eLang->get('SEARCH_ENGINES'), 'descr' => $eLang->get('MANAGE_SEARCH_ENGINES')),
			'auth' => array('icon' => 'auth', 'title' => $eLang->get('AUTH_METHODS'), 'descr' => $eLang->get('MANAGE_AUTH_METHODS'))
		);

		$is_subsite = false;
		if (defined('ELXIS_MULTISITE')) {
			$is_subsite = (ELXIS_MULTISITE == 1) ? false : true;
		}

		$can_install = $elxis->acl()->check('com_extmanager', 'components', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'modules', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'templates', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'engines', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'auth', 'install');
		$can_install += $elxis->acl()->check('com_extmanager', 'plugins', 'install');
?>
		<h2><?php echo $eLang->get('EXTENSIONS'); ?></h2>
		<div class="extman_wrapper">
			<div class="extman_wraplinks<?php echo $eLang->getinfo('RTLSFX'); ?>">
				<div class="extman_inner<?php echo $eLang->getinfo('RTLSFX'); ?>">
<?php 
		foreach ($types as $k => $type) {
			$icon = $elxis->icon($type['icon'], 32);
			$link = $elxis->makeAURL('extmanager:'.$k.'/');	
			$box_class = 'extman_box'.$eLang->getinfo('RTLSFX').' extman_not'.$eLang->getinfo('RTLSFX');

			if ($elxis->acl()->check('com_extmanager', $k, 'edit') > 0) {
				$box_class = 'extman_box'.$eLang->getinfo('RTLSFX');
			}

			echo '<div class="'.$box_class.'">'."\n";
			echo '<img src="'.$icon.'" alt="'.$k.'" border="0" align="'.$align.'" /> ';
			if ($elxis->acl()->check('com_extmanager', $k, 'edit') > 0) {
				echo '<a href="'.$link.'" class="extman_a">'.$type['title']."</a><br />\n";
				echo $type['descr']."\n";
			} else {
				echo '<strong>'.$type['title']."</strong><br />\n";
				echo $eLang->get('ACCESS_DENIED')."\n";
			}
			echo "</div>\n";						
		}
?>
				</div>
			</div>
			<div class="extman_wrapform<?php echo $eLang->getinfo('RTLSFX'); ?>">
				<div class="extman_browsebox<?php echo $eLang->getinfo('RTLSFX'); ?>">
					<div class="extman_edctitle"><?php echo $eLang->get('ELXISDC'); ?></div>
					<a href="<?php echo $elxis->makeAURL('extmanager:browse/'); ?>" title="EDC live" class="extman_edca"><?php echo $eLang->get('BROWSE_EXTS_LIVE'); ?></a>
				</div>

<?php 
		if ($can_install > 0) {
			if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
				if ($elxis->user()->gid == 1) {
					if ($is_subsite) {
						$this->synchroForm($sync);
					} else {
						$this->installForm($sync);
					}
				}
			} else {
				if ($is_subsite) {
					$this->synchroForm($sync);
				} else {
					$this->installForm($sync);
				}
			}
		}
?>
			</div>
			<div style="clear:both;"></div>
		</div>
<?php 
	}


	/*****************************/
	/* SHOW SYNCRHONIZATION FORM */
	/*****************************/
	private function synchroForm($sync) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$can_sync = 0;
		$options_str = '';
		if ($elxis->acl()->check('com_extmanager', 'components', 'install') > 0) {
			$can_sync++;
			if (count($sync['components']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('COMPONENTS').'">'."\n";
				foreach ($sync['components'] as $comp) {
					$options_str .= '<option value="'.$comp.'">'.$comp."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'modules', 'install') > 0) {
			$can_sync++;
			if (count($sync['modules']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('MODULES').'">'."\n";
				foreach ($sync['modules'] as $mod) {
					$options_str .= '<option value="'.$mod.'">'.$mod."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'plugins', 'install') > 0) {
			$can_sync++;
			if (count($sync['plugins']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('CONTENT_PLUGINS').'">'."\n";
				foreach ($sync['plugins'] as $plg) {
					$options_str .= '<option value="'.$plg.'">'.$plg."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') > 0) {
			$can_sync++;
			if (count($sync['templates']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('TEMPLATES').'">'."\n";
				foreach ($sync['templates'] as $tpl) {
					$options_str .= '<option value="'.$tpl.'">'.$tpl."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'engines', 'install') > 0) {
			$can_sync++;
			if (count($sync['engines']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('SEARCH_ENGINES').'">'."\n";
				foreach ($sync['engines'] as $eng) {
					$options_str .= '<option value="'.$eng.'">'.$eng."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($elxis->acl()->check('com_extmanager', 'auth', 'install') > 0) {
			$can_sync++;
			if (count($sync['auths']) > 0) {
				$options_str .= '<optgroup label="'.$eLang->get('AUTH_METHODS').'">'."\n";
				foreach ($sync['auths'] as $eng) {
					$options_str .= '<option value="'.$eng.'">'.$eng."</option>\n";
				}
				$options_str .= "</optgroup>\n";
			}
		}

		if ($can_sync == 0) { return; }

		$align = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		$padding = ($eLang->getinfo('DIR') == 'rtl') ? '0 200px 0 0' : '0 0 0 200px';
		$token = md5(uniqid(rand(), true));
		eFactory::getSession()->set('token_extmansync', $token);
?>
		<form name="extsyncform" class="elx_form" method="post" action="">
			<fieldset class="elx_form_fieldset">
				<legend class="elx_form_legend"><?php echo $eLang->get('SYNCHRONIZATION'); ?></legend>
				<div class="elx_form_row">
					<div class="elx_info"><?php echo $eLang->get('SYNCHRONIZATION_INFO'); ?></div>
				</div>
<?php 
		if ($options_str == '') {
			echo "\t\t\t".'<div class="elx_form_row"><div class="elx_sminfo">'.$eLang->get('ALL_EXT_SYNCHRO')."</div></div>\n";
		} else {
?>
				<div class="elx_form_row">
					<label for="extman_extension" class="elx_form_label" style="width:200px; text-align:<?php echo $align; ?>;"><?php echo $eLang->get('EXTENSION'); ?>*</label> 
					<select name="extension" id="extman_extension" title="<?php echo $eLang->get('EXTENSION'); ?>" class="selectbox" dir="ltr">
					<option value="" selected="selected">- <?php echo $eLang->get('SELECT'); ?> -</option>
					<?php echo $options_str; ?>
					</select>
					<input type="hidden" name="token" id="extman_token" value="<?php echo $token; ?>" />
					<button type="button" name="syncsub" id="extm_syncsub" title="<?php echo $eLang->get('SYNCHRONIZE'); ?>" class="extman_button" dir="ltr" onclick="syncElxisExtension();"><?php echo $eLang->get('SYNCHRONIZE'); ?></button>
				</div>
				<div style="margin:10px 0; padding:<?php echo $padding; ?>;">
					<div id="extman_loading" style="display:none;">
						<?php echo $eLang->get('SYNCHRO_IN_PROGRESS'); ?><br />
						<img src="<?php echo $elxis->secureBase(); ?>/components/com_extmanager/css/progress_bar.gif" alt="loading" border="0" />
					</div>
					<div id="extman_response"></div>
				</div>
<?php 
		}
?>
			</fieldset>
		</form>
		<div id="extman_syncurl" class="extman_invisible"><?php echo $elxis->makeAURL('extmanager:install/synchro', 'inner.php'); ?></div>
		<div id="extman_lng_noext" class="extman_invisible"><?php echo $eLang->get('NO_EXT_SELECTED'); ?></div>
		<div id="extman_lng_wait" class="extman_invisible"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
		<div id="extman_lng_synchronize" class="extman_invisible"><?php echo $eLang->get('SYNCHRONIZE'); ?></div>
		<div id="extman_lng_synsuccess" class="extman_invisible"><?php printf($eLang->get('EXT_SYNC_SUCCESS'), 'X1', 'X2'); ?></div>
<?php 
	}


	/*********************/
	/* SHOW INSTALL FORM */
	/*********************/
	private function installForm($sync=false) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$align = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
		$padding = ($eLang->getinfo('DIR') == 'rtl') ? '0 200px 0 0' : '0 0 0 200px';
		$token = md5(uniqid(rand(), true));
		eFactory::getSession()->set('token_extmaninst', $token);		
		$action = $elxis->makeAURL('extmanager:install/install.html', 'inner.php');
?>
		<form name="extinstform" class="elx_form" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data" target="extman_uptarget" onsubmit="return uploadElxisExtension();">
			<fieldset class="elx_form_fieldset">
				<legend class="elx_form_legend"><?php echo $eLang->get('INSTALL').' /'.$eLang->get('UPDATE'); ?></legend>
				<div class="elx_form_row">
					<div class="elx_info"><?php echo $eLang->get('SEL_PACKAGE_INSTALL').'<br />'.$eLang->get('UPDATE_UPLOAD_NEW').'<br />'.$eLang->get('CONSIDER_DEV_NOTES_UPD'); ?></div>
				</div>
				<div class="elx_form_row">
					<label for="extman_package" class="elx_form_label" style="width:200px; text-align:<?php echo $align; ?>;"><?php echo $eLang->get('PACKAGE'); ?>*</label> 
					<input type="file" name="package" id="extman_package" value="" title="<?php echo $eLang->get('PACKAGE'); ?>" class="filebox" dir="ltr" accept="application/zip|application/x-zip|application/x-zip-compressed" />
					<input type="hidden" name="token" id="extman_token" value="<?php echo $token; ?>" />
					<button type="submit" name="instsub" id="extm_instsub" title="<?php echo $eLang->get('UPLOAD_INSTALL'); ?>" class="extman_button" dir="ltr"><?php echo $eLang->get('UPLOAD_INSTALL'); ?></button>
				</div>
				<div style="margin:10px 0; padding:<?php echo $padding; ?>;">
					<div id="extman_loading" style="display:none;">
						<?php echo $eLang->get('INSTALL_IN_PROGRESS'); ?><br />
						<img src="<?php echo $elxis->secureBase(); ?>/components/com_extmanager/css/progress_bar.gif" alt="loading" border="0" />
					</div>
					<div id="extman_response" style="display:none;"></div>
				</div>

				<iframe id="extman_uptarget" name="extman_uptarget" src="" style="margin:0;padding:0;border:none;width:0;height:0;"></iframe>
			</fieldset>
		</form>

		<div id="extman_baseurl" class="extman_invisible"><?php echo $elxis->makeAURL('extmanager:/', 'inner.php'); ?></div>
		<div id="extman_lng_nopack" class="extman_invisible"><?php echo $eLang->get('NO_PACK_SELECTED'); ?></div>
		<div id="extman_lng_mustzip" class="extman_invisible"><?php echo $eLang->get('ELXIS_PACK_MUST_ZIP'); ?></div>
		<div id="extman_lng_wait" class="extman_invisible"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
		<div id="extman_lng_upinstall" class="extman_invisible"><?php echo $eLang->get('UPLOAD_INSTALL'); ?></div>
		<div id="extman_lng_syswarns" class="extman_invisible"><?php echo $eLang->get('SYSTEM_WARNINGS'); ?></div>
		<div id="extman_lng_continst" class="extman_invisible"><?php echo $eLang->get('CONTINUE_INSTALL'); ?></div>
		<div id="extman_lng_aboutinstall" class="extman_invisible"><?php printf($eLang->get('ABOUT_TO_INSTALL'), 'X1', 'X2'); ?></div>
		<div id="extman_lng_aboutupdate" class="extman_invisible"><?php printf($eLang->get('ABOUT_TO_UPDATE'), 'X1', 'X2', 'X3'); ?></div>
		<div id="extman_lng_insuccess" class="extman_invisible"><?php printf($eLang->get('EXT_INST_SUCCESS'), 'X1', 'X2'); ?></div>
<?php 
	}


	/***************************/
	/* SHOW INSTALLATION ERROR */
	/***************************/
	public function installError($system, $errormsg) {
		if ($errormsg == '') { $errormsg = 'Installation failed! Unknown error.'; }
		if ($system) {
			$this->ajaxHeaders('application/json');
		}
		$response = array('error' => 1, 'errormsg' => addslashes($errormsg));
		echo json_encode($response);
		if ($system) { exit(); }
	}


	/******************/
	/* CONFIRM UPDATE */
	/******************/
	public function confirmUpdate($installer) {
		$current = $installer->getCurrent();
		$head = $installer->getHead();
		$response = array (
			'extension' => $head->name,
			'exttype' => $head->type,
			'version' => $head->version,
			'curversion' => $current['version'],
			'error' => 0,
			'errormsg' => '',
			'confirmup' => 1,
			'confirmin' => 0,
			'ufolder' => $installer->getUfolder(),
			'warnings' => $installer->getWarnings(),
			'success' => 0
		);

		echo json_encode($response);
	}


	/*******************/
	/* CONFIRM INSTALL */
	/*******************/
	public function confirmInstall($installer) {
		$head = $installer->getHead();
		$response = array (
			'extension' => $head->name,
			'exttype' => $head->type,
			'version' => $head->version,
			'curversion' => 0,
			'error' => 0,
			'errormsg' => '',
			'confirmup' => 0,
			'confirmin' => 1,
			'ufolder' => $installer->getUfolder(),
			'warnings' => $installer->getWarnings(),
			'success' => 0
		);

		echo json_encode($response);
	}


	/*****************************/
	/* SHOW INSTALLATION SUCCESS */
	/*****************************/
	public function installSuccess($system, $installer) {
		$head = $installer->getHead();
		if ($system) {
			$this->ajaxHeaders('application/json');
		}

		$response = array (
			'extension' => $head->name,
			'exttype' => $head->type,
			'version' => $head->version,
			'curversion' => 0,
			'error' => 0,
			'errormsg' => '',
			'confirmup' => 0,
			'confirmin' => 0,
			'ufolder' => '',
			'warnings' => $installer->getWarnings(),
			'success' => 1
		);

		echo json_encode($response);
		if ($system) { exit(); }
	}

}

?>