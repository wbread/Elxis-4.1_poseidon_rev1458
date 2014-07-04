<?php 
/**
* @version		$Id: afpage.php 1343 2012-11-02 19:21:39Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class afpageContentController extends contentController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/*******************************/
	/* PREPARE TO DESIGN FRONTPAGE */
	/*******************************/
	public function design() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$pathway = eFactory::getPathway();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_content', 'frontpage', 'edit') < 1) {
			$link = $elxis->makeAURL('cpanel:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}

		$eDoc->addStyleLink($elxis->secureBase().'/components/com_content/css/layout.css');
		$eDoc->addJQuery();
		$eDoc->addLibrary('iqueryui', $elxis->secureBase().'/components/com_content/js/jquery-ui.min.js', '1.9.1');
		$eDoc->addScriptLink($elxis->secureBase().'/components/com_content/js/layout.js');

		$positions = $this->model->getTplPositions();
		if ($positions) {
			usort($positions, array('afpageContentController', 'orderPositions'));
		}

		$layout = $this->getLayout($positions);

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('FRONTPAGE_DESIGNER'));
		$eDoc->setTitle($eLang->get('FRONTPAGE_DESIGNER'));

		$this->view->design($layout, $positions);
	}


	/***************************************************************/
	/* RE-ORDER POSITION BASED ON THEIR NUMBER OF ASSIGNED MODULES */
	/***************************************************************/
	public static function orderPositions($a, $b) {
		if ($a->modules == $b->modules) { return 0; }
		return ($a->modules < $b->modules) ? 1 : -1;
	}


	/**********************/
	/* GET CURRENT LAYOUT */
	/**********************/
	private function getLayout($positions) {
		$layout = new stdClass;
		$layout->wl = 20;
		$layout->wc = 60;
		$layout->wr = 20;
		$layout->positions = array();
		for ($i=1; $i<18; $i++) {
			$property = 'c'.$i;
			$layout->$property = array();
		}

		$rows = $this->model->getFrontpage();
		if ($rows) {
			foreach ($rows as $row) {
				$pname = $row['pname'];
				switch ($pname) {
					case 'wl': case 'wc': case 'wr':
						$layout->$pname = (int)$row['pval'];
					break;
					default:
						$pval = trim($row['pval']);
						if ($pval != '') {
							$cellpos = explode(',', $pval);
							$final = array();
							if ($cellpos && $positions) {
								foreach ($positions as $pos) {
									if (in_array($pos->position, $cellpos)) {
										$final[] = $pos->position;
										$layout->positions[] = $pos->position;
									}
								}
							}
							$layout->$pname = $final;
						}
					break;
				}
			}
		}

		return $layout;
	}


	/****************************************/
	/* GET LAYOUT DATA FROM USER SUBMISSION */
	/****************************************/
	private function getUserLayout() {
		$userLayout = array();
		$userLayout['wl'] = (isset($_POST['wl'])) ? (int)$_POST['wl'] : 0;
		$userLayout['wc'] = (isset($_POST['wc'])) ? (int)$_POST['wc'] : 0;
		$userLayout['wr'] = (isset($_POST['wr'])) ? (int)$_POST['wr'] : 0;
		for ($i=1; $i<18; $i++) {
			$pname = 'c'.$i;
			$pval = trim(filter_input(INPUT_POST, $pname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$userLayout[$pname] = ($pval != '') ? explode(',', $pval) : array();
		}
		return $userLayout;
	}


	/***************/
	/* SAVE LAYOUT */
	/***************/
	public function savelayout() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->ajaxHeaders('text/plain');
		if ($elxis->acl()->check('com_content', 'frontpage', 'edit') < 1) {
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit();
		}

		$userLayout = $this->getUserLayout();

		if ($userLayout['wl'] + $userLayout['wc'] + $userLayout['wr'] != 100) {
			echo '0|'.addslashes($eLang->get('WIDTHS_SUM_100'));
			exit();
		}

		if ($userLayout['wl'] == 0) { $userLayout['c1'] = array(); }
		if ($userLayout['wc'] == 0) {
			$userLayout['c2'] = array();
			$userLayout['c4'] = array();
			$userLayout['c5'] = array();
			$userLayout['c6'] = array();
			$userLayout['c7'] = array();
			$userLayout['c8'] = array();
			$userLayout['c9'] = array();
			$userLayout['c10'] = array();
			$userLayout['c11'] = array();
			$userLayout['c12'] = array();
			$userLayout['c13'] = array();
			$userLayout['c14'] = array();
			$userLayout['c15'] = array();
			$userLayout['c16'] = array();
			$userLayout['c17'] = array();
		}
		if ($userLayout['wr'] == 0) { $userLayout['c3'] = array(); }

		$positions = $this->model->getTplPositions();
		$allowed_positions = array();
		if ($positions) {
			foreach ($positions as $pos) {
				if ($pos->position == 'hidden') { continue; }
				if ($pos->position == 'tools') { continue; }
				if ($pos->position == 'menu') { continue; }
				if (strpos($pos->position, 'category') === 0) { continue; }
				$allowed_positions[] = $pos->position;
			}
		}
		unset($positions);

		for ($i=1; $i < 18; $i++) {
			$pname = 'c'.$i;
			if (count($userLayout[$pname]) > 0) {
				$final = array();
				foreach ($userLayout[$pname] as $userPos) {
					if (in_array($userPos, $allowed_positions)) { $final[] = $userPos; }
				}
				$userLayout[$pname] = implode(',', $final);
			} else {
				$userLayout[$pname] = '';
			}
		}

		$dbrows = $this->model->getFrontpage();

		$rows = array();
		foreach ($userLayout as $pname => $pval) {
			$is_int = (in_array($pname, array('wl', 'wc', 'wr'))) ? true : false;
			$row = new stdClass;
			$row->id = 0;
			$row->pname = $pname;
			$row->pval = $is_int ? (int)$pval : $pval;
			$row->is_int = $is_int;
			$rows[$pname] = $row;
			unset($row);
		}
		unset($userLayout);

		if ($dbrows) {
			foreach ($dbrows as $dbrow) {
				$pname = $dbrow['pname'];
				if (isset($rows[$pname])) {
					$rows[$pname]->id = (int)$dbrow['id'];
				}
			}
		}
		unset($dbrows);

		$this->model->saveFrontpage($rows);

		echo '1|'.$eLang->get('ITEM_SAVED');
		exit();	
	}

}

?>