<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class apluginContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***********************/
	/* CUSTOM PAGE HEADERS */
	/***********************/
	private function sendHeaders($type='text/html') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type:'.$type.'; charset=utf-8');
	}


	/********************************/
	/* DISPLAY AN ERROR PAGE (AJAX) */
	/********************************/
	public function errorResponse($message, $type='text/html') {
		$this->sendHeaders($type);
		if ($type == 'text/plain') {
			echo $message;
		} else {
			echo '<div class="elx_error">'.$message."</div>\n";
		}
		exit();
	}


	/**********************************/
	/* PLUGIN IMPORTER HTML INTERFACE */
	/**********************************/
	public function interfaceHTML($id, $fn, $plugins, $iPlugin) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
?>

		<div class="plug_wrapper">
			<div class="gbox">
				<div class="gbox_inner">
					<div class="gbox_head">
						<h3><?php echo $eLang->get('IMPORT_ELXIS_PLUGIN'); ?></h3>
					</div>
					<div class="gbox_contents">
						<table border="0" class="plug_table" dir="<?php echo $eLang->getinfo('DIR'); ?>">
						<tr>
							<td class="plug_td80"><?php echo $eLang->get('PLUGIN'); ?></td>
							<td class="plug_td200">
							<select name="plugin" id="plugin" class="selectbox" onchange="loadPlugin(<?php echo $fn; ?>);">
							<option value="0"<?php echo ($id == 0) ? ' selected="selected"' : ''; ?>>- <?php echo $eLang->get('SELECT'); ?> -</option>
<?php 
					if ($plugins) {
						foreach ($plugins as $plg) {
							$sel = ($id == $plg['id']) ? ' selected="selected"' : '';
							echo '<option value="'.$plg['id'].'"'.$sel.'>'.$plg['title'].' ('.$plg['plugin'].")</option>\n";
						}
					}
?>
							</select>
							</td>
							<td class="plug_td80"><?php echo $eLang->get('CODE'); ?></td>
							<td>
								<input type="text" name="plugincode" id="plugincode" class="plug_inputtext" value="" dir="ltr" /> 
								<button type="button" name="impplugin" id="impplugin" title="<?php echo $eLang->get('IMPORT'); ?>" class="plug_button" onclick="plugImportCode(<?php echo $fn; ?>);"><?php echo $eLang->get('IMPORT'); ?></button>							
							</td>
						</tr>
						</table>
						<div id="plug_load"><?php if ($id > 0) { $this->pluginHTML($iPlugin['row'], $iPlugin['info'], $iPlugin['plugObj'], $fn); } ?></div>
						<div class="gbox_footer">
							<a href="javascript:window.close();" class="gbox_close_link" title="Close this window"><?php echo $eLang->get('CLOSE'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="plugbase" style="display:none;" dir="ltr"><?php echo $elxis->makeAURL('content:plugin/', 'inner.php'); ?></div>
		<div id="lng_wait" style="display:none;"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
<?php 
	}


	/**********************/
	/* LOADED PLUGIN HTML */
	/**********************/
	public function pluginHTML($row, $info, $plugObj, $fn) {
		$this->xmlDetails($info);
		$this->pluginSyntax($plugObj);
		$tabs = $plugObj->tabs();
		if (is_array($tabs) && (count($tabs) > 1)) {
			$this->startTabs($tabs);
			$max = count($tabs) + 1;
			for ($idx = 1; $idx < $max; $idx++) {
				$this->openTab($idx);
				$plugObj->helper($row->id, $idx, $fn);
				$this->closeTab();
			}
			$this->endTabs();
		} else {
			$plugObj->helper($row->id, 1, $fn);
		}
	}


	/**************/
	/* START TABS */
	/**************/
	private function startTabs($tabs) {
		echo '<ul class="tabs">'."\n";
		$idx = 1;
		foreach ($tabs as $tab) {
			echo "\t".'<li><a href="#tab_plugins_'.$idx.'">'.$tab."</a></li>\n";
			$idx++;
		}
		echo "</ul>\n";
		echo '<div class="tab_container">'."\n";
	}


	/************/
	/* END TABS */
	/************/
	private function endTabs() {
		echo "</div>\n";
		echo '<div style="clear:both;"></div>'."\n";
	}


	/******************/
	/* OPEN A NEW TAB */
	/******************/
	private function openTab($idx) {
		echo '<div id="tab_plugins_'.$idx.'" class="tab_content">'."\n";
	}


	/*************/
	/* CLOSE TAB */
	/*************/
	private function closeTab() {
		echo "</div>\n";
	}


	/*************************************/
	/* SHOW PLUGIN DETAILS FROM XML FILE */
	/*************************************/
	private function xmlDetails($info) {
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		echo '<div style="margin:0 5px 5px 5px;">'."\n";
		echo '<strong>'.$info['title'].'</strong> v'.$info['version'].' '.$eLang->get('BY').' '.$info['author'].'. '.$eLang->get('DATE').' '.$eDate->formatDate($info['created'], $eLang->get('DATE_FORMAT_3'))."\n";
		echo "</div>\n";
	}


	/*********************************/
	/* DISPLAY PLUGIN GENERIC SYNTAX */
	/*********************************/
	private function pluginSyntax($plugObj) {
		$eLang = eFactory::getLang();

		$syntax = $plugObj->syntax();
		echo '<table border="0" class="plug_table" dir="'.$eLang->getinfo('DIR').'">'."\n";
		echo "<tr>\n";
		echo '<td class="plug_td150">'.$eLang->get('GENERIC_SYNTAX')."</td>\n";
		if ($syntax == '') {
			echo '<td><div class="plug_syntax"><em>'.$eLang->get('NOT_AVAILABLE')."</em></div></td>\n";
		} else {
			echo '<td><div class="plug_syntax">'.htmlspecialchars($syntax)."</div></td>\n";
		}
		echo "</tr>\n";
		echo "</table>\n";
	}

}

?>