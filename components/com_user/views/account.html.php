<?php 
/**
* @version		$Id: account.html.php 1420 2013-04-29 18:18:53Z datahell $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class accountUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************/
	/* DISPLAY USERS CENTRAL */
	/*************************/
	public function usersCentral($avatar='', $avsize=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';

		echo '<h1>'.$eLang->get('USERSCENTRAL')."</h1>\n";
		if (ELXIS_MOBILE == 0) {
			echo '<p>'.$eLang->get('USERSCENTRALDESC')."</p>\n";
		}

		if ($elxis->user()->gid == 7) {
			$link = $elxis->makeURL('user:login/', '', true);
			echo '<h3><a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('LOGIN')."</a></h3>\n";
			echo '<p>'.$eLang->get('LOGINOWNACC')."</p>\n";
			if ($elxis->getConfig('REGISTRATION') == 1) {
				$link = $elxis->makeURL('user:register.html', '', true);
				echo '<h3><a href="'.$link.'" title="'.$eLang->get('REGISTER').'">'.$eLang->get('REGISTER')."</a></h3>\n";
				echo '<p>'.$eLang->get('CRNEWUSERACC')."</p>\n";
			}
			if ($elxis->getConfig('PASS_RECOVER') == 1) {
				$link = $elxis->makeURL('user:recover-pwd.html', '', true);
				echo '<h3><a href="'.$link.'" title="'.$eLang->get('RECOVERPASS').'">'.$eLang->get('RECOVERPASS')."</a></h3>\n";
				echo '<p>'.$eLang->get('CRPASSACCFORG')."</p>\n";
			}
		} else {
			echo '<div style="margin:15px 0;">'."\n".'<div class="elx_profile_summary">'."\n";
			if (ELXIS_MOBILE == 0) {
				echo '<div class="elx_avatar_box" style="width:'.($avsize + 10).'px; float:'.$float.';">'."\n";
				echo '<img src="'.$avatar.'" alt="'.$elxis->user()->uname.'" width="'.$avsize.'" />'."\n";
				echo "</div>\n";
				echo '<div style="padding-'.$float.':'.($avsize + 12).'px;">'."\n";
				if (trim($elxis->user()->firstname) != '') {
					echo $eLang->get('HI').' '.$elxis->user()->firstname.' '.$elxis->user()->lastname.",<br />\n";
				} else {
					echo $eLang->get('HI').",<br />\n";
				}
				if ($elxis->user()->gid == 6) {
					printf($eLang->get('YLOGGEDINASAUTHEXT'), '<strong>'.$elxis->user()->uname.'</strong>', '<strong>'.$elxis->session()->login_method.'</strong>');
				} else {
					printf($eLang->get('YLOGGEDINAS'), '<strong>'.$elxis->user()->uname.'</strong>');
				}
				echo "</div>\n";
			} else {
				echo '<div class="elx_avatar_box" style="width:60px; float:'.$float.';">'."\n";
				echo '<img src="'.$avatar.'" alt="'.$elxis->user()->uname.'" />'."\n";
				echo "</div>\n";
				echo '<div style="padding-'.$float.':62px;">'."\n";
				if (trim($elxis->user()->firstname) != '') {
					echo '<h3 class="elx_user_title">'.$elxis->user()->firstname.' '.$elxis->user()->lastname.' <span dir="ltr">('.$elxis->user()->uname.')</span></h3>';
				} else {
					echo '<h3 class="elx_user_title">'.$elxis->user()->uname.'</h3>';
				}
				if ($elxis->user()->gid == 6) { echo 'via <strong>'.$elxis->session()->login_method.'</strong>'; }
				echo "</div>\n";
			}
			echo '<div class="clear"></div>'."\n";
			echo "</div>\n</div>\n";

			$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
			echo '<h3>'.$eLang->get('USEROPTIONS')."</h3>\n";
			echo '<'.$navtag.' class="elx_user_links">';
			if ($elxis->acl()->getLevel() > 1) {
				if ($elxis->acl()->check('com_user', 'profile', 'view') > 0) {
					$link = $elxis->makeURL('user:members/'.$elxis->user()->uid.'.html');
					echo '<a href="'.$link.'" title="'.$eLang->get('PROFILE').'">'.$eLang->get('PROFILE')."</a> \n";
				}
				if ($elxis->acl()->check('com_user', 'profile', 'edit') > 0) {
					$link = $elxis->makeURL('user:members/edit.html?id='.$elxis->user()->uid, '', true);
					echo '<a href="'.$link.'" title="'.$eLang->get('EDITPROFILE').'">'.$eLang->get('EDITPROFILE')."</a> \n";
				}
			}

			if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
				$link = $elxis->makeURL('user:members/');
				echo '<a href="'.$link.'" title="'.$eLang->get('MEMBERSLIST').'">'.$eLang->get('MEMBERSLIST')."</a> \n";
			}
			$link = $elxis->makeURL('user:logout.html', '', true);
			echo '<a href="'.$link.'" title="'.$eLang->get('LOGOUT').'">'.$eLang->get('LOGOUT').'</a></'.$navtag.">\n";
		}

		$langs_info = $eLang->getSiteLangs(true);
		if ($langs_info) {
			$lang_current = $eLang->currentLang();
			echo '<h3>'.$eLang->get('LANGUAGE')."</h3>\n";
			echo '<p>'.$eLang->get('SETPREFLANG')."</p>\n";
			echo '<div class="elx_tbl_wrapper">'."\n";
			echo '<table cellspacing="2" cellpadding="0" border="0" width="100%" style="width:100%; border:0;" dir="'.$eLang->getinfo('DIR').'">'."\n";
			$o = 0;
			$i = 0;
			$langimgbase = $elxis->secureBase().'/includes/libraries/elxis/language/flags/';
			foreach ($langs_info as $lng => $info) {
				$extrastyle = ($lng == $lang_current) ? 'font-weight:bold; text-decoration:none;' : 'text-decoration:none;';
				$data = '<a href="'.$elxis->makeURL($lng.':user:/').'" title="'.$info['NAME_ENG'].'" style="'.$extrastyle.'">';
				$data .= '<img src="'.$langimgbase.$lng.'.png" alt="'.$lng.'" style="vertical-align:bottom; border:none;" /> ';
				$data .= $info['NAME'].'</a> <span dir="ltr">('.$info['LANGUAGE'].'-'.$info['REGION'].')</span>';
				if ($i % 3 == 0) { echo "<tr>\n"; }
				echo '<td width="33%">'.$data."</td>\n";
				$o++;
				if (($i+1) % 3 == 0) { echo "</tr>\n"; }
				$i++;
			}

			$rem = $o % 3;
			if ($rem > 0) {
				echo str_repeat("<td></td>\n", 3 - $rem);
				echo "</tr>\n";
			}
			echo "</table>\n</div>\n<br />\n";
		}

		echo '<h3>'.$eLang->get('TIMEZONE')."</h3>\n";
		echo '<p>'.$eLang->get('CHATIMELOCAL')."</p>\n";
		$action = $elxis->makeURL('user:changetz.html', 'inner.php', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmchangetz',
			'action' => $action,
			'idprefix' => 'tz',
			'label_width' => 160,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$eDate = eFactory::getDate();
		$tz = $eDate->getTimezone();
		$current_daytime = $eDate->formatDate('now', $eLang->get('DATE_FORMAT_12'));
		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('TIMEZONE'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $tz, array('tip' => $current_daytime));
		$form->addButton('sbmtz', $eLang->get('SUBMIT'));
		$form->closeFieldset();
		$form->render();
		unset($form);
	}


	/**********************************/
	/* DISPLAY USER REGISTRATION FORM */
	/**********************************/
	public function registrationForm($row, $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$action = $elxis->makeURL('user:register.html', '', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmregister',
			'action' => $action,
			'idprefix' => 'reg',
			'label_width' => 200,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('CRNEWUSERACC'));
		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 1, 'size' => 30));
		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), array('required' => 1, 'tip' => $eLang->get('MINCHARDIGSYM')));
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), 
			array(
				'required' => 1, 
				'maxlength' => 60,
				'tip' => $eLang->get('MINLENGTH6'),
				'password_meter' => 1,
				'onkeyup' => 'elxPasswordMeter(\'fmregister\', \'regpword\', \'reguname\');'
			)
		);
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), array('required' => 1, 'maxlength' => 60, 'match' => 'regpword'));
		$form->addCaptcha('seccode');
		$form->addButton('sbmreg', $eLang->get('REGISTER'), 'submit', array('class' => 'elxbutton-save', 'tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();

		echo '<h1>'.$eLang->get('REGISTRATION')."</h1>\n";
		echo '<p style="margin-bottom: 20px;">'.$eLang->get('REGISTERDESC')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx_error" style="margin-bottom: 20px;">'.$errormsg."</div>\n";
		}
		$form->render();
		unset($form);
		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';

		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		$link = $elxis->makeURL('user:login/', '', true);
		echo '<a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('LOGIN')."</a> \n";
		if ($elxis->getConfig('PASS_RECOVER') == 1) {
			$link = $elxis->makeURL('user:recover-pwd.html', '', true);
			echo '<a href="'.$link.'" title="'.$eLang->get('CRPASSACCFORG').'">'.$eLang->get('RECOVERPASS')."</a> \n";
		}
		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/****************************************/
	/* DISPLAY REGISTRATION SUCCESS MESSAGE */
	/****************************************/
	public function registrationSuccess($row, $msg) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$fplink = $elxis->makeURL('');
		echo '<h1>'.$eLang->get('SUCCESSREG')."</h1>\n";
		echo '<div class="elx_success">'.$eLang->get('REGCOMPLSUCC')."</div>\n";
		echo '<p>'.$msg."</p>\n";
		echo '<div style="margin: 20px 0 30px 0;">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";
	}


	/**************************************/
	/* DISPLAY ACTIVATION SUCCESS MESSAGE */
	/**************************************/
	public function activationSuccess($uname) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$fplink = $elxis->makeURL();
		$login_link = $elxis->makeURL('user:login/', '', true);
		echo '<h1>'.$eLang->get('ACCOUNTACT')."</h1>\n";
		echo '<div class="elx_success">'.$eLang->get('YACCACTSUCC')."</div>\n";
		echo '<p>'.sprintf($eLang->get('YOUMAYLOGIN'), '<strong>'.$uname.'</strong>')."<br />\n";
		echo '<a href="'.$login_link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('CLICKTOLOGIN')."</a>\n";
		echo "</p>\n";
		echo '<div style="margin: 20px 0 30px 0;">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";
	}


	/**********************************/
	/* DISPLAY PASSWORD RECOVERY FORM */
	/**********************************/
	public function recoverForm($row, $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$action = $elxis->makeURL('user:recover-pwd.html', '', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmrecover',
			'action' => $action,
			'idprefix' => 'rec',
			'label_width' => 200,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('RECOVERPASS'));
		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), array('required' => 1));
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 1, 'size' => 30));
		$form->addCaptcha('seccode');
		$form->addButton('sbmrec', $eLang->get('SUBMIT'), 'submit', array('tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();

		echo '<h1>'.$eLang->get('RECOVERPASS')."</h1>\n";
		echo '<p style="margin-bottom: 20px;">'.$eLang->get('PASSRECOVDESC')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx_error" style="margin-bottom: 20px;">'.$errormsg."</div>\n";
		}
		$form->render();
		unset($form);

		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		$link = $elxis->makeURL('user:login/', '', true);
		echo '<a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('LOGIN')."</a> \n";
		if ($elxis->getConfig('REGISTRATION') == 1) {
			$link = $elxis->makeURL('user:register.html', '', true);
			echo '<a href="'.$link.'" title="'.$eLang->get('CRNEWUSERACC').'">'.$eLang->get('REGISTER')."</a> \n";
		}
		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/**************************************/
	/* DISPLAY ACTIVATION SUCCESS MESSAGE */
	/**************************************/
	public function recoverSuccess() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$fplink = $elxis->makeURL();
		echo '<h1>'.$eLang->get('RECOVERPASS')."</h1>\n";
		echo '<div class="elx_success">'.$eLang->get('NEWPASSSUCCGEN')."</div>\n";
		echo '<div style="margin: 20px 0 30px 0;">'."\n";
		echo $eLang->get('THANKYOU')."<br />\n";
		echo '<a href="'.$fplink.'" title="'.$elxis->getConfig('SITENAME').'">'.$elxis->getConfig('SITENAME')."</a>\n";
		echo "</div>\n";
	}


	/**********************/
	/* DISPLAY LOGIN FORM */
	/**********************/
	public function loginForm($auth='', $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eAuth = eRegistry::get('eAuth');

		if (($auth == '') || ($auth == 'elxis')) {
			echo '<h1>'.$eLang->get('LOGIN')."</h1>\n";
		}

		if ($auth != '') {
			$eAuth->loginForm();
		}

		if (($auth == '') || ($auth == 'elxis')) {
			$auths = $eAuth->getAuths();
			$this->showProviders($auths);

			$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
			echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
			if ($elxis->getConfig('REGISTRATION') == 1) {
				$link = $elxis->makeURL('user:register.html', '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('CRNEWUSERACC').'">'.$eLang->get('REGISTER')."</a> \n";
			}
			if ($elxis->getConfig('PASS_RECOVER') == 1) {
				$link = $elxis->makeURL('user:recover-pwd.html', '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('CRPASSACCFORG').'">'.$eLang->get('RECOVERPASS')."</a> \n";
			}
			$link = $elxis->makeURL('user:/');
			echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
			echo '</'.$navtag.">\n";
		}
	}


	/*********************************************/
	/* DISPLAY EXTERNAL AUTHENTICATION PROVIDERS */
	/*********************************************/
	private function showProviders($auths) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if (!$auths) { return; }
		$n = 0;
		foreach ($auths as $auth => $data) {
			if ($auth != 'elxis') { $n++; }
		}
		if ($n == 0) { return; }

		$imgbase = $elxis->secureBase().'/components/com_user/auth';
		$defw = 600;
		$defh = 400;

		echo '<p>'.$eLang->get('LOGIN_EXACC_PROVIDERS')."</p>\n";
		echo '<div style="margin:10px 0;">'."\n";
		foreach ($auths as $auth => $data) {
			if ($auth == 'elxis') { continue; }
			switch ($auth) {
				case 'gmail': case 'ldap': $w = 600; $h = 300; break;
				case 'twitter': $w = 700; $h = 550; break;
				case 'openid': $w = 900; $h = 550; break;
				default: $w = $defw; $h = $defh; break;
			}

			$link = $elxis->makeURL('user:login/'.$auth.'.html', 'inner.php', true);
			$title = sprintf($eLang->get('LOGIN_WITH'), $data['title']);

			$baseurl = $elxis->secureBase().'/components/com_user/';
			echo '<a href="javascript:void(null);" title="'.$title.'" onclick="elxPopup(\''.$link.'\', '.$w.', '.$h.', \'Login with '.$auth.'\')">';
			echo '<img src="'.$imgbase.'/'.$auth.'/'.$auth.'.png" alt="'.$auth.'" border="0" />';
			echo "</a> \n";
		}
		echo "</div>\n";
	}


	/***********************************/
	/* CLOSE OPENED WINDOW AFTER LOGIN */
	/***********************************/
	public function closeAfterLogin($return) {
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if ($return == '') {
			$jscode = 'window.opener.location.reload(); window.close();'; 
		} else {
			$jscode = 'window.opener.location.href=\''.$return.'\'; window.close();'; 
		}

		$js = 'if (window.addEventListener) {
			window.addEventListener(\'load\', function() { '.$jscode.' }, false);
		} else if (window.attachEvent) {
			window.attachEvent(\'onload\', function() { '.$jscode.' });
		}
		function reloadAndClose() { '.$jscode.' }';
		$eDoc->addScript($js);

		$eDoc->setTitle($eLang->get('LOGIN').' - Success');

		echo '<div class="elx_success">'.$eLang->get('SUCC_LOGGED')."</div>\n";
		echo '<div style="margin:20px 0; text-align:center;">'."\n";
		echo '<a href="javascript:void(null);" onclick="reloadAndClose();">'.$eLang->get('CLOSEWIN_IFNOTAUTO')."</a>";
		echo "</div>\n";
	}

}

?>