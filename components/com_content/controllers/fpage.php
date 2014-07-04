<?php 
/**
* @version		$Id: fpage.php 1419 2013-04-28 17:59:58Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fpageContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/*********************************/
	/* PREPARE TO GENERATE FRONTPAGE */
	/*********************************/
	public function frontpage() {
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$metaKeys = array();
		$keys = explode(',', $elxis->getConfig('METAKEYS'));
		if ($keys) {
			foreach ($keys as $key) { $metaKeys[] = eUTF::trim($key); }
		}

		if (count($metaKeys) < 10) {
			$metaKeys[] = eFactory::getLang()->get('HOME');
			$metaKeys[] = 'elxis';
		}

		$eDoc->setTitle($elxis->getConfig('SITENAME'));
		$eDoc->setDescription($elxis->getConfig('METADESC'));
		$eDoc->setKeywords($metaKeys);
		unset($keys, $metaKeys);

		$rsslink = $elxis->makeURL('content:rss.xml');
		$atomlink = $elxis->makeURL('content:atom.xml');
		$eDoc->addLink($rsslink, 'application/rss+xml', 'alternate', 'title="'.$elxis->getConfig('SITENAME').' - RSS"');
		$eDoc->addLink($atomlink, 'application/rss+xml', 'alternate', 'title="'.$elxis->getConfig('SITENAME').' - ATOM"');
		unset($rsslink, $atomlink);

		$layout = $this->getLayout();

		$this->view->showFrontpage($layout);
	}


	/**********************/
	/* GET CURRENT LAYOUT */
	/**********************/
	private function getLayout() {
		if (ELXIS_MOBILE == 0) {
			if ($layout = elxisAPC::fetch('layout', 'frontpage')) { return $layout; }
		}

		$layout = new stdClass;
		$layout->wl = 20;
		$layout->wc = 60;
		$layout->wr = 20;
		$layout->positions = array();
		for ($i=1; $i<18; $i++) {
			$property = 'c'.$i;
			$layout->$property = array();
		}

		if (ELXIS_MOBILE == 1) {
			$layout->wl = 0;
			$layout->wc = 100;
			$layout->wr = 0;
			$layout->c2 = array('mobilefront');
			return $layout;
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
							$layout->$pname = explode(',', $pval);
						}
					break;
				}
			}
		}

		elxisAPC::store('layout', 'frontpage', $layout, 7200);
		return $layout;
	}

}

?>