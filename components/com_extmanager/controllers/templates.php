<?php 
/**
* @version		$Id: templates.php 1224 2012-07-01 18:56:53Z datahell $
* @package		Elxis
* @subpackage	Component Extensions Manager
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class templatesExtmanagerController extends extmanagerController {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $task='', $model=null) {
		parent::__construct($view, $task, $model);
	}


	/*********************************/
	/* PREPARE TO DISPLAY TEMPLATES */
	/*********************************/
	public function listtempls() {
		$eLang = eFactory::getLang();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('TEMPLATES'), 'extmanager:templates/');
		$eDoc->setTitle($eLang->get('TEMPLATES'));

		$this->view->listtemplates();
	}


	/**********************************************************/
	/* RETURN LIST OF TEMPLATES FOR GRID IN XML FORMAT (AJAX) */
	/**********************************************************/
	public function gettemplates() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		$sortcols = array('title', 'template');
		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'title', 'sortorder' => 'asc',
			'qtype' => '', 'query' => '',
			'section' => 'frontend',
			'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortname'] = (isset($_POST['sortname'])) ? trim($_POST['sortname']) : 'title';
		if (($options['sortname'] == '') || !in_array($options['sortname'], $sortcols)) { $options['sortname'] = 'title'; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }
		$options['section'] = (isset($_POST['section'])) ? trim($_POST['section']) : 'frontend';
		if ($options['section'] != 'backend') { $options['section'] = 'frontend'; }

		$total = $this->model->countTemplates($options['section']);

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getTemplates($options);
			if ($rows) {
				$exttype = ($options['section'] == 'backend') ? 'atemplate' : 'template';
				elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
				$exml = new extensionXML();
				foreach ($rows as $k => $row) {
					$info = $exml->quickXML($exttype, $row->template);
					$rows[$k]->version = $info['version'];
					$rows[$k]->created = $info['created'];
					$rows[$k]->author = $info['author'];
					$rows[$k]->authorurl = $info['authorurl'];
					unset($info);
				}
				unset($exml);
			}
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');

		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows && (count($rows) > 0)) {
			$i = 0;
			$pubicon = $elxis->icon('tick', 16);
			$unpubicon = $elxis->icon('error', 16);
			$sectiontxt = ($options['section'] == 'backend') ? $eLang->get('BACKEND') : $eLang->get('FRONTEND');
			$cur_template = ($options['section'] == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');
			$canedit = ($elxis->acl()->check('com_extmanager', 'templates', 'edit') > 0) ? true : false;
			$edit_link = $elxis->makeAURL('extmanager:templates/edit.html');
			foreach ($rows as $row) {
				$sn = $options['limitstart'] + $i + 1;
				$titletxt = ($row->title == '') ? '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>' : $row->title;
				$deficon = ($row->template == $cur_template) ? $pubicon : $unpubicon;
				$authortxt = '';
				if (trim($row->author) != '') {
					if (trim($row->authorurl) != '') {
						$authortxt = '<a href="'.$row->authorurl.'" target="_blank" style="text-decoration:none;">'.$row->author.'</a>';
					} else {
						$authortxt = $row->author;
					}
				}
				if ($authortxt == '') { $authortxt = '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>'; }
				$datetxt = '';
				if (trim($row->created) != '') {
					$datetxt = $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_3'));
				}
				if ($datetxt == '') { $datetxt = '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>'; }
				$versiontxt = ($row->version == 0) ? '<span style="color:#666;">'.$eLang->get('NOT_AVAILABLE').'</span>' : $row->version;

				echo '<row id="'.$row->id.'">'."\n";
				echo '<cell>'.$sn."</cell>\n";
				if ($canedit) {
					echo '<cell><![CDATA[<a href="'.$edit_link.'?id='.$row->id.'" title="'.$eLang->get('EDIT').'" style="text-decoration:none;">'.$titletxt."</a>]]></cell>\n";
				} else {
					echo '<cell><![CDATA['.$titletxt."]]></cell>\n";
				}
				echo '<cell><![CDATA['.$row->template."]]></cell>\n";
				echo '<cell><![CDATA['.$versiontxt."]]></cell>\n";
				echo '<cell><![CDATA[<img src="'.$deficon.'" alt="icon" border="0" />]]></cell>'."\n";
       			echo '<cell><![CDATA['.$datetxt."]]></cell>\n";
       			echo '<cell><![CDATA['.$authortxt."]]></cell>\n";
       			echo '<cell><![CDATA['.$sectiontxt."]]></cell>\n";
				echo "</row>\n";
				$i++;
			}
		}
		echo '</rows>';
		exit();
	}


	/*****************/
	/* EDIT TEMPLATE */
	/*****************/
	public function edittemplate() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();
		$eDoc = eFactory::getDocument();

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
		$row = new templatesDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:templates/');
			$elxis->redirect($link, 'Template not found!', true);
		}

		$reldir = ($row->section == 'backend') ? 'templates/admin/' : 'templates/';
		$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/'.$row->template.'.xml';
		if (!file_exists($xmlfile)) {
			$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/templateDetails.xml'; //elxis 2009.x compatibility
			if (!file_exists($xmlfile)) {
				$link = $elxis->makeAURL('extmanager:templates/');
				$elxis->redirect($link, 'Template XML file was not found!', true);
			}
		}
		
		if (file_exists(ELXIS_PATH.'/'.$reldir.$row->template.'/'.$row->template.'.png')) {
			$tplthumb = $elxis->secureBase().'/'.$reldir.$row->template.'/'.$row->template.'.png';
		} elseif (file_exists(ELXIS_PATH.'/'.$reldir.$row->template.'/template_thumbnail.png')) { //elxis 2009.x compatibility
			$tplthumb = $elxis->secureBase().'/'.$reldir.$row->template.'/template_thumbnail.png';
		} else {
			$tplthumb = '';
		}

		$exttype = ($row->section == 'backend') ? 'atemplate' : 'template';
		$eLang->load($row->template, $exttype);

		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$exml->parse($xmlfile, true);
		$exml->checkDependencies();

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('TEMPLATES'), 'extmanager:templates/');

		$pgtitle = sprintf($eLang->get('EDIT_TEMPLATE_X'), $row->title);
		$eDoc->setTitle($pgtitle);
		$pathway->addNode($eLang->get('EDIT').' '.$row->title);

		$toolbar = $elxis->obj('toolbar');
		$toolbar->add($eLang->get('SAVE'), 'save', false, '', 'elxSubmit(\'save\');');
		$toolbar->add($eLang->get('APPLY'), 'saveedit', false, '', 'elxSubmit(\'apply\');');
		$toolbar->add($eLang->get('CANCEL'), 'cancel', false, $elxis->makeAURL('extmanager:templates/'));

		$this->view->editTemplate($row, $exml, $xmlfile, $tplthumb);
	}


	/*****************/
	/* SAVE TEMPLATE */
	/*****************/
	public function savetemplate() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$sess_token = trim($eSession->get('token_elxisform'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			exitPage::make('403', 'CEXT-0010', $eLang->get('REQDROPPEDSEC'));
		}

		$task = isset($_POST['task']) ? $_POST['task'] : 'save';
		if ($task != 'apply') { $task = 'save'; }
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$msg = $eLang->get('NOTALLOWACTION');
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $msg, true);
		}

		$row = new templatesDbTable();
		if (!$row->load($id)) {
			$link = $elxis->makeAURL('extmanager:templates/');
			$elxis->redirect($link, 'Template not found!', true);
		}

		$reldir = ($row->section == 'backend') ? 'templates/admin/' : 'templates/';
		$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/'.$row->template.'.xml';
		$hasxml = true;
		if (!file_exists($xmlfile)) {
			$xmlfile = ELXIS_PATH.'/'.$reldir.$row->template.'/templateDetails.xml'; //elxis 2009.x compatibility
			if (!file_exists($xmlfile)) { $hasxml = false; }
		}

		if ($hasxml) {
			elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
			$params = new elxisParameters('', $xmlfile, 'template');
			$row->params = $params->toString($_POST['params']);
			unset($params);
		} else {
			$row->params = null;
		}

		if (!$row->update()) {
			$redirurl = $elxis->makeAURL('extmanager:templates/edit.html?id='.$id);
			$elxis->redirect($redirurl, $row->getErrorMsg(), true);
		}

		$eSession->set('token_elxisform');
		$task = filter_input(INPUT_POST, 'task', FILTER_UNSAFE_RAW);
		$redirurl = ($task == 'apply') ? $elxis->makeAURL('extmanager:templates/edit.html?id='.$row->id) : $elxis->makeAURL('extmanager:templates/');
		$elxis->redirect($redirurl, $eLang->get('ITEM_SAVED'));
	}


	/*******************/
	/* DELETE TEMPLATE */
	/*******************/
	public function deletetemplate() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_extmanager', 'templates', 'install') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('NOTALLOWACTION'));
			exit();
		}

		if (defined('ELXIS_MULTISITE') && (ELXIS_MULTISITE != 1)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('UNINST_EXT_MOTHERSITE'));
			exit();
		}

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		if ($id < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|Template not found!';
			exit();
		}

		$row = new templatesDbTable();
		if (!$row->load($id)) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$row->getErrorMsg();
			exit();
		}

		if ($elxis->getConfig('SECURITY_LEVEL') > 0) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('UNINST_NALLOW_SECLEVEL'));
			return $response;
		}

		if ($row->iscore == 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($eLang->get('CNOT_UNINST_CORE_EXTS'));
			exit();
		}

		$cur_template = ($row->section == 'backend') ? $elxis->getConfig('ATEMPLATE') : $elxis->getConfig('TEMPLATE');
		if ($cur_template == $row->template) {
			$this->ajaxHeaders('text/plain');
			echo '0|You can not uninstall the current template!';
			exit();
		}

		$exttype = ($row->section == 'backend') ? 'atemplate' : 'template';
		elxisLoader::loadFile('components/com_extmanager/includes/extension.xml.php');
		$exml = new extensionXML();
		$info = $exml->quickXML($exttype, $row->template);
		$tpl_version = $info['version'];
		unset($exl);

		elxisLoader::loadFile('components/com_extmanager/includes/installer.class.php');
		$installer = new elxisInstaller();
		$ok = $installer->uninstall($exttype, $row->template, $row->id, $tpl_version);
		if (!$ok) {
			$msg = $installer->getError();
			if ($msg == '') { $msg = $eLang->get('ACTION_FAILED'); }
			$this->ajaxHeaders('text/plain');
			echo '0|'.addslashes($msg);
			exit();
		}

		$this->ajaxHeaders('text/plain');
		echo '1|Success';
		exit();
	}


	/************************************/
	/* PREPARE TO LIST MODULE POSITIONS */
	/************************************/
	public function listpositions() {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();
		$elxis = eFactory::getElxis();
		$pathway = eFactory::getPathway();

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$link = $elxis->makeAURL('extmanager:/');
			$elxis->redirect($link, $eLang->get('NOTALLOWACTION'), true);
		}

		$pathway->addNode($eLang->get('EXTENSIONS'), 'extmanager:/');
		$pathway->addNode($eLang->get('TEMPLATES'), 'extmanager:templates/');
		$pathway->addNode($eLang->get('MODULE_POSITIONS'));
		$eDoc->setTitle($eLang->get('MODULE_POSITIONS'));

		$this->view->listpositions();
	}


	/*****************************************************************/
	/* RETURN LIST OF MODULE POSITIONS FOR GRID IN XML FORMAT (AJAX) */
	/*****************************************************************/
	public function getpositions() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$this->ajaxHeaders('text/xml');
			echo "<rows><page>1</page><total>0</total></rows>\n";
			exit();
		}

		$options = array(
			'rp' => 10, 'page' => 1,
			'sortname' => 'position', 'sortorder' => 'asc',
			'qtype' => '', 'query' => '', 'limitstart' => 0
		);

		$options['rp'] = (isset($_POST['rp'])) ? (int)$_POST['rp'] : 10;
		if ($options['rp'] < 1) { $options['rp'] = 10; }
		$elxis->updateCookie('rp', $options['rp']);
		$options['page'] = (isset($_POST['page'])) ? (int)$_POST['page'] : 1;
		if ($options['page'] < 1) { $options['page'] = 1; }
		$options['sortorder'] = (isset($_POST['sortorder'])) ? trim($_POST['sortorder']) : 'asc';
		if ($options['sortorder'] != 'desc') { $options['sortorder'] = 'asc'; }

		$total = $this->model->countPositions();

		$maxpage = ceil($total/$options['rp']);
		if ($maxpage < 1) { $maxpage = 1; }
		if ($options['page'] > $maxpage) { $options['page'] = $maxpage; }
		$options['limitstart'] = (($options['page'] - 1) * $options['rp']);
		if ($total > 0) {
			$rows = $this->model->getFullPositions($options, true);
		} else {
			$rows = array();
		}

		$this->ajaxHeaders('text/xml');
		echo "<rows>\n";
		echo '<page>'.$options['page']."</page>\n";
		echo '<total>'.$total."</total>\n";
		if ($rows) {
			$editicon = $elxis->icon('edit', 16);
			$delicon = $elxis->icon('delete', 16);
			$delofficon = $elxis->icon('delete_off', 16);
			foreach ($rows as $i => $row) {
				$sn = $options['limitstart'] + $i + 1;
				echo '<row id="'.$row->id.'">'."\n";
				echo '<cell>'.$sn."</cell>\n";
				echo '<cell><![CDATA['.$row->position."]]></cell>\n";
				echo '<cell>'.$row->modules."</cell>\n";
				echo '<cell><![CDATA['.$row->description."]]></cell>\n";
				echo "</row>\n";
			}
		}
		echo '</rows>';
		exit();
	}


	/*********************/
	/* ADD/EDIT POSITION */
	/*********************/
	public function editposition() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		eFactory::getDocument()->setContentType('text/html'); //colorbox wont work correctly with xhtml

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			echo '<div class="elx_warning">'.$eLang->get('NOTALLOWACCPAGE').'</div>';
			return;
		}

		$id = (isset($_GET['id'])) ? (int)$_GET['id'] : 0;
		if ($id < 0) { $id = 0; }

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/tplpositions.db.php');
		$row = new tplpositionsDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				echo '<div class="elx_warning">The requested position was not found!</div>'."\n";
				return;
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmposition',
			'action' => $elxis->makeAURL('extmanager:templates/saveposition.html', 'inner.php'),
			'idprefix' => 'mp',
			'label_width' => 140,
			'label_align' => 'left',
			'tip_style' => 1,
			'attributes' => 'id="jfmposition"'
		);

		if ($id == 0) {
			$notetxt = $eLang->get('NEW_MOD_POSITION');
		} else {
			$notetxt = sprintf($eLang->get('EDIT_MOD_POSITION'), '<strong>'.$row->position.'</strong>');
		}

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('MODULE_POSITIONS'));
		$form->addNote($notetxt, 'elx_sminfo');	
		$form->addText('position', $row->position, $eLang->get('POSITION'), array('dir' => 'ltr', 'size' => 20, 'maxlength' => 20, 'required' => 1));
		$form->addText('description', $row->description, $eLang->get('DESCRIPTION'), array('dir' => 'ltr', 'size' => 50, 'required' => 0));
		$form->addHidden('id', $id, array('dir' => 'ltr'));
		$form->addButton('psave');
		$form->closeFieldset();
		$form->render();
?>
		<script type="text/javascript">
		/* <![CDATA[ */
		$('#jfmposition').submit(function(event) {
   			event.preventDefault();
			var $form = $( this ),
			mpposition = $('#mpposition').val(),
			mpdescription = $('#mpdescription').val(),
			mpid = $('#mpid').val(),
			url = $form.attr('action');
			$.post(url, 
				{id: mpid, position:mpposition, description:mpdescription},
				function(xreply) {
					var rdata = new Array();
					rdata = xreply.split('|');
					var rok = parseInt(rdata[0], 10);
					if (rok == 1) {
						if (document.getElementById('lpos')) {
							$("#lpos").flexReload();
						}
						$.colorbox.close();
					} else {
						alert(rdata[1]);
					}
				}
			);
		});
		/* ]]> */
		</script>
<?php 
	}


	/*******************************/
	/* SAVE MODULE POSITION (AJAX) */
	/*******************************/
	public function saveposition() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		if ($id < 0) { $id = 0; }
		$position = strtolower(trim(filter_input(INPUT_POST, 'position', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW)));
		$description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$position2 = preg_replace("/[^a-z0-9]/", '', $position);

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			echo '0|'.$eLang->get('NOTALLOWACCPAGE');
			exit();
		}
		if (($position == '') || ($position != $position2)) {
			echo '0|'.$eLang->get('POS_NAME_CHARS');
			exit();
		}

		$oldname = '';
		elxisLoader::loadFile('includes/libraries/elxis/database/tables/tplpositions.db.php');
		$row = new tplpositionsDbTable();
		if ($id > 0) {
			if (!$row->load($id)) {
				echo '0|The requested position was not found!';
				exit();
			}
			if ($row->position != $position) {
				$oldname = $row->position;
				$num = $this->model->countPositionName($position);
				if ($num > 0) {
					echo '0|'.$eLang->get('ALREADY_POS_NAME');
					exit();
				}
			}
		} else {
			$num = $this->model->countPositionName($position);
			if ($num > 0) {
				echo '0|'.$eLang->get('ALREADY_POS_NAME');
				exit();
			}
		}

		$row->position = $position;
		$row->description = $description;

		if (!$row->store()) {
			echo '0|Could not save position!';
			exit();
		}

		if (($id > 0) && ($oldname != '')) {
			$this->model->updateModulesPositions($oldname, $row->position);
		}

		echo '1|Success';
		exit();
	}


	/**************************/
	/* DELETE MODULE POSITION */
	/**************************/
	public function deleteposition() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

		$this->ajaxHeaders('text/plain');

		if ($elxis->acl()->check('com_extmanager', 'templates', 'edit') < 1) {
			$this->ajaxHeaders('text/plain');
			echo '0|'.$eLang->get('NOTALLOWACCPAGE');
			exit();
		}

		if ($id < 1) {
			echo '0|Requested position does not exist!';
			exit();
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/tplpositions.db.php');
		$row = new tplpositionsDbTable();
		if (!$row->load($id)) {
			echo '0|The requested position was not found!';
			exit();
		}

		$options = array('qtype' => '', 'query' => '', 'section' => 'frontend', 'position' => $row->position);
		$num = $this->model->countModules($options);
		if ($num > 0) {
			echo '0|'.$eLang->get('CNOT_DELETE_POSMODS');
			exit();
		}

		$ok = $row->delete();
		if ($ok) {
			echo '1|Success';
		} else {
			echo '0|Could not delete module position!';
		}
		exit();
	}

}
	
?>