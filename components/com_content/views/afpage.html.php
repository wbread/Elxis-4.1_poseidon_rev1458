<?php 
/**
* @version		$Id: afpage.html.php 1394 2013-02-23 20:12:13Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class afpageContentView extends contentView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************/
	/* HTML FRONTPAGE DESIGNER */
	/***************************/
	public function design($layout, $positions) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$accordion = $elxis->obj('accordion', 'helper', true);
		$accordion->setCollapsible(true);
?>

		<h2><?php echo $eLang->get('FRONTPAGE_DESIGNER'); ?></h2>
		<p class="elx_sminfo"><?php echo $eLang->get('MOBILE_VERSION_FRONTPAGE'); ?></p>

		<div class="fpwrap">
			<div class="fpboxeswrap">
<?php 
			$accordion->open(true);
			$accordion->openItem($eLang->get('POSITIONS'), true);
?>
			<ul class="fpboxes" id="fpboxes">
<?php 
			if ($positions) {
				foreach ($positions as $pos) {
					if ($pos->position == 'hidden') { continue; }
					if ($pos->position == 'tools') { continue; }
					if ($pos->position == 'menu') { continue; }
					if (strpos($pos->position, 'category') === 0) { continue; }
					if (in_array($pos->position, $layout->positions)) { continue; }
					echo '<li class="laybox" id="'.$pos->position.'">'.$pos->position.' ('.$pos->modules.")</li>\n";
				}
			}
?>
			</ul>
<?php 
			$accordion->closeItem();
			$accordion->openItem($eLang->get('DIMENSIONS'), false);

			$range = range(0, 100, 5);
			echo '<div style="margin: 0 0 10px 0; padding:0;">'."\n";
			echo '<span class="fpwlabel">'.$eLang->get('LEFT').'</span> <select name="fpwleft" id="fpwleft" class="smallselect" onchange="fpcalculateCols(1)">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wl == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n</div>\n";
			echo '<div style="margin: 0 0 10px 0; padding:0;">'."\n";
			echo '<span class="fpwlabel">'.$eLang->get('CENTER').'</span> <select name="fpwcenter" id="fpwcenter" class="smallselect" onchange="fpcalculateCols(2)">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wc == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n</div>\n";
			echo '<div style="margin: 0 0 10px 0; padding:0;">'."\n";
			echo '<span class="fpwlabel">'.$eLang->get('RIGHT').'</span> <select name="fpwright" id="fpwright" class="smallselect">'."\n";
			foreach ($range as $w) {
				$sel = ($layout->wr == $w) ? ' selected="selected"' : '';
				echo '<option value="'.$w.'"'.$sel.'>'.$w.'%</option>'."\n";
			}
			echo "</select>\n</div>\n";
?>
			<a href="javascript:void(null);" onclick="applyWidth();" class="elx_button-22"><span><?php echo $eLang->get('APPLY'); ?></span></a>
<?php 
			$accordion->closeItem();
			$accordion->openItem($eLang->get('FINISH'), true);
			echo '<a href="javascript:void(null);" onclick="saveLayout();" class="elx_button-22"><span>'.$eLang->get('SAVE')."</span></a>\n";
			echo '<div id="fp_message" class="fpmessage" style="display:none;"></div>'."\n";
			$accordion->closeItem();
			$accordion->close();
?>
			</div>
			<div class="fplayout" id="fplayout">
				<div id="fpleftcol">
					<ul class="lay100" id="lay1">
						<?php $this->populateCell($layout->c1, $positions); ?>
					</ul>
				</div>
				<div id="fpmidcol">
					<ul class="lay100" id="lay2">
						<?php $this->populateCell($layout->c2, $positions); ?>
					</ul>
					<div style="margin:0; padding:0;">
						<ul class="lay240" id="lay4">
							<?php $this->populateCell($layout->c4, $positions); ?>
						</ul>
						<ul class="lay240" id="lay5">
							<?php $this->populateCell($layout->c5, $positions); ?>
						</ul>
						<div style="clear:both;"></div>
					</div>

					<div style="margin:0; padding:0;">
						<ul class="lay320" id="lay6">
							<?php $this->populateCell($layout->c6, $positions); ?>
						</ul>
						<ul class="lay160" id="lay7">
							<?php $this->populateCell($layout->c7, $positions); ?>
						</ul>
						<div style="clear:both;"></div>
					</div>

					<div style="margin:0; padding:0;">
						<ul class="lay160" id="lay8">
							<?php $this->populateCell($layout->c8, $positions); ?>
						</ul>
						<ul class="lay320" id="lay9">
							<?php $this->populateCell($layout->c9, $positions); ?>
						</ul>
						<div style="clear:both;"></div>
					</div>

					<ul class="lay100" id="lay10"></ul>
					<div style="margin:0; padding:0;">
						<ul class="lay160" id="lay11">
							<?php $this->populateCell($layout->c11, $positions); ?>
						</ul>
						<ul class="lay160" id="lay12">
							<?php $this->populateCell($layout->c12, $positions); ?>
						</ul>
						<ul class="lay160" id="lay13">
							<?php $this->populateCell($layout->c13, $positions); ?>
						</ul>
						<div style="clear:both;"></div>
					</div>
					<ul class="lay100" id="lay14"></ul>
					<div style="margin:0; padding:0;">
						<ul class="lay240" id="lay15">
							<?php $this->populateCell($layout->c15, $positions); ?>
						</ul>
						<ul class="lay240" id="lay16">
							<?php $this->populateCell($layout->c16, $positions); ?>
						</ul>
						<div style="clear:both;"></div>
					</div>
					<ul class="lay100" id="lay17">
						<?php $this->populateCell($layout->c17, $positions); ?>
					</ul>
				</div>
				<div id="fprightcol">
					<ul class="lay100" id="lay3">
						<?php $this->populateCell($layout->c3, $positions); ?>
					</ul>
				</div>
				<div style="clear:both;"></div>
			</div>
			<div style="clear:both;"></div>
		</div>
		<div style="display:none;" id="fp_lng_w100"><?php echo $eLang->get('WIDTHS_SUM_100'); ?></div>
		<div style="display:none;" id="fp_lng_wait"><?php echo $eLang->get('PLEASE_WAIT'); ?></div>
		<div style="display:none;" id="fp_saveurl" dir="ltr"><?php echo $elxis->makeAURL('content:fpage/save', 'inner.php'); ?></div>
		<div style="display:none;" id="fp_saves" dir="ltr">
<?php 
		for ($i=1; $i < 18; $i++) {
			$prop = 'c'.$i;
			echo 'lay'.$i.'='.implode(',', $layout->$prop).'!';
		}
?>
		</div>

<?php 
	}


	/************************/
	/* POPULATE LAYOUT CELL */
	/************************/
	private function populateCell($laycell, $positions) {
		if (count($laycell) == 0) { return; }
		foreach ($laycell as $laypos) {
			$nummods = 0;
			if ($positions) {
				foreach ($positions as $pos) {
					if ($pos->position == $laypos) {
						$nummods = $pos->modules;
						break;
					}
				}
			}
			echo '<li class="laybox" id="'.$laypos.'">'.$laypos.' ('.$nummods.")</li>\n";
		}
	}
		
}

?>