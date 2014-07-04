<?php 
/**
* @version		$Id: mod_login.php 1390 2013-02-22 20:01:35Z datahell $
* @package		Elxis
* @subpackage	Module login
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


if (!class_exists('moduleLogin', false)) {
	class moduleLogin {

		private $icon_w = 32;
		private $ext_auths = 1;
		private $ext_help = 1;
		private $pretext = '';
		private $posttext = '';
		private $login_redir = 0;
		private $displayname = 0;
		private $avatar = 1;
		private $avatar_w = 40;
		private $gravatar = 0;
		private $usergroup = 0;
		private $timeonline = 0;
		private $authmethod = 0;
		private $rand = 1;


		/*********************/
		/* MAGIC CONSTRUCTOR */
		/*********************/
		public function __construct($params) {
			$this->icon_w = (int)$params->get('icon_w', 32);
			if ($this->icon_w < 16) { $this->icon_w = 32; }
			$this->ext_auths = (int)$params->get('ext_auths', 1);
			$this->auth_help = (int)$params->get('auth_help', 1);
			$this->pretext = eUTF::trim($params->get('pretext', ''));
			$this->posttext = eUTF::trim($params->get('posttext', ''));
			$this->login_redir = (int)$params->get('login_redir', 0);
			$this->displayname = (int)$params->get('displayname', 0);
			$this->avatar = (int)$params->get('avatar', 1);
			$this->avatar_w = (int)$params->get('avatar_w', 40);
			if ($this->avatar_w < 30) { $this->avatar_w = 40; }
			$this->gravatar = (int)$params->get('gravatar', 0);
			$this->usergroup = (int)$params->get('usergroup', 0);
			$this->timeonline = (int)$params->get('timeonline', 0);
			$this->authmethod = (int)$params->get('authmethod', 0);
			$this->rand = rand(1, 999);

			if (ELXIS_MOBILE == 1) {
				$this->ext_auths = 0;
				$this->auth_help = 0;
				$this->pretext = '';
				$this->posttext = '';
				$this->avatar = 0;
				$this->gravatar = 0;
				$this->usergroup = 0;
				$this->timeonline = 0;
				$this->authmethod = 0;
			}
		}


		/******************/
		/* MODULE EXECUTE */
		/******************/
		public function run() {
			if (eFactory::getElxis()->user()->gid <> 7) {
				$this->logoutForm();
			} else {
				$this->loginForm();
			}
		}


		/**********************/
		/* DISPLAY LOGIN FORM */
		/**********************/
		private function loginForm() {
			elxisLoader::loadInit('libraries:elxis:auth.class', 'eAuth', 'elxisAuth');
			$eAuth = eRegistry::get('eAuth');
			if ($eAuth->getError() != '') {
				$this->showError($eAuth->getError());
				return;
			}

			$auths = $eAuth->getAuths();
			if (!$auths) {
				$this->showError('There are no public Authentication methods!');
				return;
			}

			echo '<div class="modlogin_wrapper">'."\n";
			if ($this->pretext != '') {
				echo '<div class="modlogin_pretext">'.$this->pretext."</div>\n";
			}
			$this->elxisLogin($auths);
			$this->showProviders($auths);
			if ($this->posttext != '') {
				echo '<div class="modlogin_posttext">'.$this->posttext."</div>\n";
			}
			echo "</div>\n";
		}


		/********************/
		/* ELXIS LOGIN FORM */
		/********************/
		private function elxisLogin($auths) {
			$eLang = eFactory::getLang();
			$elxis = eFactory::getElxis();

			if (!isset($auths['elxis'])) { return; }

			$token = md5(uniqid(rand(), true));
			eFactory::getSession()->set('token_loginform', $token);
			$action = $elxis->makeURL('user:login/elxis.html', '', true, false);
			if ($this->login_redir == 1) {
				$return = base64_encode(eFactory::getURI()->getRealUriString());
			} else {
				$return = base64_encode($elxis->makeURL('user:/'));
			}

			$js = 'elxAutocompOff(\'uname'.$this->rand.'\'); elxAutocompOff(\'pword'.$this->rand.'\');';
			eFactory::getDocument()->addScript($js);

			echo '<form name="loginform" method="post" action="'.$action.'">'."\n";
			echo '<div class="modlogin_uname_row">'."\n";
			echo '<label for="uname'.$this->rand.'">'.$eLang->get('USERNAME')."</label>\n";
			echo '<input type="text" name="uname" id="uname'.$this->rand.'" dir="ltr" class="inputbox" size="10" title="'.$eLang->get('USERNAME').'" />'."\n";
			echo "</div>\n";
			echo '<div class="modlogin_pword_row">'."\n";
			echo '<label for="pword'.$this->rand.'">'.$eLang->get('PASSWORD_SHORT')."</label>\n";
			echo '<input type="password" name="pword" id="pword'.$this->rand.'" dir="ltr" class="inputbox" size="10" title="'.$eLang->get('PASSWORD').'" />'."\n";
			echo "</div>\n";
			echo '<div class="modlogin_remember_row">'."\n";
			echo '<label for="remember'.$this->rand.'">'.$eLang->get('REMEMBER_ME')."</label>\n";
			echo '<input type="checkbox" name="remember" id="remember'.$this->rand.'" value="1" />'."\n";
			echo "</div>\n";
			echo '<input type="hidden" name="auth_method" value="elxis" />'."\n";
			echo '<input type="hidden" name="return" value="'.$return.'" />'."\n";
			echo '<input type="hidden" name="modtoken" value="'.$token.'" />'."\n";
			echo '<button type="submit" name="sublogin" id="sublogin'.$this->rand.'">'.$eLang->get('LOGIN').'</button>'."\n";
			echo "</form>\n";

			if (($elxis->getConfig('REGISTRATION') == 1) || ($elxis->getConfig('PASS_RECOVER') == 1)) {
				echo '<div class="modlogin_linksbox">'."\n";
				if ($elxis->getConfig('REGISTRATION') == 1) {
					$link = $elxis->makeURL('user:register.html', '', true, false);
					echo '<a href="'.$link.'" title="'.$eLang->get('CREATE_ACCOUNT').'">'.$eLang->get('CREATE_ACCOUNT')."</a> \n";
				}
				if ($elxis->getConfig('PASS_RECOVER') == 1) {
					$link = $elxis->makeURL('user:recover-pwd.html', '', true, false);
					echo '<a href="'.$link.'" title="'.$eLang->get('PASS_RECOVERY').'">'.$eLang->get('PASS_RECOVERY')."</a>\n";
				}
				echo "</div>\n";
			}
		}


		/*********************************************/
		/* DISPLAY EXTERNAL AUTHENTICATION PROVIDERS */
		/*********************************************/
		private function showProviders($auths) {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if ($this->ext_auths == 0) { return; }
			if (!$auths) { return; }
			$n = 0;
			foreach ($auths as $auth => $data) {
				if ($auth != 'elxis') { $n++; }
			}
			if ($n == 0) { return; }

			$imgbase = $elxis->secureBase().'/components/com_user/auth';
			$defw = 600;
			$defh = 400;

			echo '<div class="modlogin_authbox">'."\n";
			if ($this->auth_help == 1) {
				echo '<p>'.$eLang->get('LOGIN_EXACC_PROVIDERS')."</p>\n";
			}

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
				echo '<img src="'.$imgbase.'/'.$auth.'/'.$auth.'.png" alt="'.$auth.'" border="0" width="'.$this->icon_w.'" height="'.$this->icon_w.'" />';
				echo "</a> \n";
			}
			echo "</div>\n";
		}


		/***********************/
		/* DISPLAY LOGOUT FORM */
		/***********************/
		private function logoutForm() {
			$elxis = eFactory::getElxis();
			$eLang = eFactory::getLang();

			if ($elxis->user()->gid != 6) {
				switch ($this->displayname) {
					case 1: $name = $elxis->user()->firstname.' '.$elxis->user()->lastname; break;
					case 2: $name = $elxis->user()->uname; break;
					case 0: default:
						$name = ($elxis->getConfig('REALNAME') == 1) ? $elxis->user()->firstname.' '.$elxis->user()->lastname : $elxis->user()->uname; break;
					break;
				}
			} else {
				switch ($this->displayname) {
					case 1: 
						if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					 break;
					case 2:
						if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					break;
					case 0: default:
						if ($elxis->user()->firstname != '') {
							$name = $elxis->user()->firstname.' '.$elxis->user()->lastname;
						} else if ($elxis->user()->uname != '') {
							$name = $elxis->user()->uname;
						} else if ($elxis->user()->email != '') {
							$name = $elxis->user()->email;
						} else {
							$name = $eLang->get('UNKNOWN');
						}
					break;
				}
			}

			$logout_link = $elxis->makeURL('user:logout.html', '', true, false);

			echo '<div class="modlogin_wrapper">'."\n";
			if ($this->avatar == 1) {
				$boxw = $this->avatar_w + 10;
				if ($eLang->getinfo('DIR') == 'rtl') {
					$float = 'right';
					$margin = '0 '.$boxw.'px 0 0';
				} else {
					$float = 'left';
					$margin = '0 0 0 '.$boxw.'px;';
				}
				$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, $this->avatar_w, $this->gravatar, $elxis->user()->email);
				echo '<div class="elx_avatar_box" style="width:'.$boxw.'px; float:'.$float.';">'."\n";
				echo '<img src="'.$avatar.'" alt="'.$name.'" width="'.$this->avatar_w.'" height="'.$this->avatar_w.'" border="0" />';
				echo "</div>\n";
				echo '<div style="margin:'.$margin.'">'."\n";
			} else {
				echo '<div>'."\n";
			}

			if ($elxis->user()->gid != 6) {
				$utitle = $eLang->get('MY_PROFILE');
				$ulink = $elxis->makeURL('user:members/myprofile.html');
			} else {
				$utitle = $eLang->get('USERS_CENTRAL');
				$ulink = $elxis->makeURL('user:/');
			}
			echo '<a href="'.$ulink.'" title="'.$utitle.'" class="modlogin_profile">'.$name.'</a>';

			if ($this->usergroup == 1) {
				switch ($elxis->user()->gid) {
					case 1: $groupname = $eLang->get('ADMINISTRATOR'); break;
					case 5: $groupname = $eLang->get('USER'); break;
					case 6: $groupname = $eLang->get('EXTERNALUSER'); break;
					default: $groupname = $elxis->user()->groupname; break;
				}
				echo '<div class="modlogin_group">'.$groupname."</div>\n";
			}

			if ($this->timeonline == 1) {
				$dt = eFactory::getDate()->getTS() - $elxis->session()->first_activity;
				$min = floor($dt/60);
				$sec = $dt - ($min * 60);
				$duration = $min.':'.sprintf("%02d", $sec);
				echo '<div class="modlogin_online">'.sprintf($eLang->get('ONLINE_FOR'), $duration)."</div>\n";
			}

			if ($this->authmethod == 1) {
				if ($elxis->session()->login_method != 'elxis') {
					$authm = ucfirst($elxis->session()->login_method);
					$login_method_desc = sprintf($eLang->get('LOGGED_VIA'), $authm);
					echo '<div class="modlogin_method">'.$login_method_desc."</div>\n";
				}
			}

			echo '<a href="'.$logout_link.'" title="'.$eLang->get('LOGOUT').'" class="modlogin_logout">'.$eLang->get('LOGOUT')."</a>\n";
			echo "</div>\n";
			echo '<div style="clear:both;"></div>'."\n";
			echo "</div>\n";
		}


		/**********************/
		/* SHOW ERROR MESSAGE */
		/**********************/
		private function showError($msg) {
			echo '<div class="elx_error">'.$msg."</div>\n";
		}

	}
}

$modlogin = new moduleLogin($params);
$modlogin->run();
unset($modlogin);


?>