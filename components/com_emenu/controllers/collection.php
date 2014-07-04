<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component eMenu
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class collectionEmenuController extends emenuController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/***************************************/
	/* PREPARE TO DISPLAY COLLECTIONS LIST */
	/***************************************/
	public function listcollections() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		$pathway->deleteAllNodes();
		$pathway->addNode($eLang->get('MENU_MANAGER'), 'emenu:/');
		$pathway->addNode($eLang->get('MENU_ITEM_COLLECTIONS'));

		eFactory::getDocument()->setTitle($eLang->get('MENU_ITEM_COLLECTIONS').' - '.$elxis->getConfig('SITENAME'));

		$this->view->listcollections();
	}


	/************************************************************/
	/* RETURN LIST OF COLLECTIONS FOR GRID IN XML FORMAT (AJAX) */
	/************************************************************/
	public function getcollections() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$options = array(
			'rp' => 50, 'page' => 1,
			'sortname' => 'collection', 'sortorder' => 'asc',
			'qtype' => '', 'query' => '', 'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 50;
		if ($options['rp'] < 1) { $options['rp'] = 50; }
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'collection';
		if (($options['sortname'] == '') || !in_array($options['sortname'], array('collection', 'items'))) { $options['sortname'] = 'collection'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }

		$rows = $this->model->getCollections();
		$total = count($rows);

		if ($total > 1) {
			$rows = $this->sortCollections($rows, $options['sortname'], $options['sortorder']);
			$limitstart = 0;
			$maxpage = ceil($total/$options['rp']);
			if ($maxpage < 1) { $maxpage = 1; }
			if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
			$options['limitstart'] = (($options['page'] - 1) * $options['rp']);

			if ($total > $options['rp']) {
				$limitrows = array();
				$end = $options['limitstart'] + $options['rp'];
				foreach ($rows as $key => $row) {
					if ($key < $options['limitstart']) { continue; }
					if ($key >= $end) { break; }
					$limitrows[] = $row;
				}
				$rows = $limitrows;
			}
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 1;
			foreach ($rows as $row) {
				$modsarr = array();
				if ($row->modules) {
					foreach ($row->modules as $mod) {
						$modsarr[] = $mod['title'].' <span dir="ltr">('.$mod['position'].')</span>';
					}
				}
				$link = $elxis->makeAURL('emenu:mitems/'.$row->collection.'.html');
				echo '<row id="'.$row->collection.'">'."\n";
				echo '<cell>'.$i."</cell>\n";
				echo '<cell><![CDATA[<a href="'.$link.'" title="'.$eLang->get('MANAGE_MENU_ITEMS').'">'.$row->collection."</a>]]></cell>\n";
				echo '<cell>'.$row->items."</cell>\n";
				echo '<cell><![CDATA['.implode(', ', $modsarr)."]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/*********************/
	/* ORDER COLLECTIONS */
	/*********************/
	private function sortCollections($rows, $sortname, $sortorder) {
		switch ($sortname) {
			case 'items':
				$code = 'if ($a->'.$sortname.' != $b->'.$sortname.') {';
				if ($sortorder == 'asc') {
					$code .= 'return ($a->'.$sortname.' < $b->'.$sortname.' ? -1 : 1); }';
				} else {
					$code .= 'return ($a->'.$sortname.' < $b->'.$sortname.' ? 1 : -1); }';
				}
				$code .= 'return 0;';
			break;
			case 'collection': default:
				if ($sortorder == 'desc') {
					return array_reverse($rows);
				} else {
					return $rows;
				}
			break;
		}

        $compare = create_function('$a,$b', $code);
        usort($rows, $compare);
        return $rows;
    }


	/******************************************/
	/* PREPARE TO DISPLAY ADD COLLECTION FORM */
	/******************************************/
	public function addcollection() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_emenu', 'menu', 'add') < 1) {
			echo '<div class="elx_error">'.$eLang->get('NOTALLOWACCPAGE').'</div>';
			return;
		}

		$collection = '';
		$modtitle = '';
		$errormsg = '';
		$sucmsg = '';
		if (isset($_POST['colbtn'])) {
			$collection = trim(preg_replace('/[^A-Za-z0-9]/', '', $_POST['collection']));
			$modtitle = filter_input(INPUT_POST, 'modtitle', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
			$modtitle = eUTF::trim(preg_replace($pat, '', $modtitle));

			if ($collection == '') {
				$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('COLLECTION'));
			} else if ($modtitle == '') {
				$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('MODULE_TITLE'));
			} else if ($collection != $_POST['collection']) {
				$errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('COLLECTION'));
			} else {
				$collection = strtolower($collection);
				$response = $this->model->saveCollection($collection, $modtitle);
				if ($response['success'] === false) {
					$errormsg = $response['message'];
				} else {
					$sucmsg = $response['message'];
				}
			}
		}

		$this->view->addCollection($collection, $modtitle, $errormsg, $sucmsg);
	}


	/****************************/
	/* DELETE A MENU COLLECTION */
	/****************************/
	public function deletecollection() {
		if (!isset($_POST['collection'])) {
			$this->ajaxHeaders('text/plain');
			echo '0|No collection set!';
			exit();
		}

		$collection = trim(preg_replace('/[^a-z0-9]/', '', $_POST['collection']));
		if (($collection == '') || ($collection != $_POST['collection'])) {
			$this->ajaxHeaders('text/plain');
			echo '0|Invalid collection!';
			exit();
		}

		$response = $this->model->deleteCollection($collection); //includes acl check
		$this->ajaxHeaders('text/plain');
		if ($response['success'] === false) {
			echo '0|'.$response['message'];
		} else {
			echo '1|'.$response['message'];
		}
		exit();
	}

}
	
?>