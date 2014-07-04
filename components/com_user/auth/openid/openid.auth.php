<?php 
/**
* @version		$Id: openid.auth.php 970 2012-03-17 20:55:40Z datahell $
* @package		Elxis
* @subpackage	Component User / Authentication
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class openidAuthentication {

	private $host = 'localhost';
	private $providers = array();


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->host = eFactory::getElxis()->secureBase();
		$this->buildProviders();
	}


	/*******************************/
	/* BUILT OPENID PROVIDERS LIST */
	/*******************************/
	private function buildProviders() {
		$this->providers = array(
			'openid' => array('name' => 'OpenId', 'userinput' => true, 'url' => 'http://{uname}'),
			'aol' => array('name' => 'AOL', 'userinput' => true, 'url' => 'http://openid.aol.com/{uname}'),
			'google' => array('name' => 'Google', 'userinput' => false, 'url' => 'https://www.google.com/accounts/o8/id'),
			'yahoo' => array('name' => 'Yahoo', 'userinput' => false, 'url' => 'http://yahoo.com/'),
			'flickr' => array('name' => 'Flickr', 'userinput' => true, 'url' => 'http://www.flickr.com/{uname}/'),
			'myspace' => array('name' => 'My Space', 'userinput' => true, 'url' => 'http://www.myspace.com/{uname}'),
			'blogger' => array('name' => 'Blogger', 'userinput' => true, 'url' => 'http://{uname}.blogspot.com/'),
			'technorati' => array('name' => 'Technorati', 'userinput' => true, 'url' => 'http://technorati.com/people/technorati/{uname}/'),
			'livejournal' => array('name' => 'Live Journal', 'userinput' => true, 'url' => 'http://{uname}.livejournal.com/'),
			'verisign' => array('name' => 'Verisign', 'userinput' => true, 'url' => 'http://{uname}.pip.verisignlabs.com/'),
			'wordpress' => array('name' => 'Wordpress', 'userinput' => true, 'url' => 'http://{uname}.wordpress.com'),
			'myopenid' => array('name' => 'MyOpenId', 'userinput' => true, 'url' => 'http://{uname}.myopenid.com/'),
			'claimid' => array('name' => 'ClaimId', 'userinput' => true, 'url' => 'http://claimid.com/{uname}'),
			'myvidoop' => array('name' => 'MyVidoop', 'userinput' => true, 'url' => 'http://{uname}.myvidoop.com/')
		);
	}


	/***********************/
	/* AUTHENTICATE A USER */
	/***********************/
	public function authenticate(&$response, $options=array()) {
		$elxis = eFactory::getElxis();

		if (!isset($_GET['eprov'])) {
			$response->errormsg = 'No OpenID provider set!';
			return false;
		}

		$provider = trim(filter_input(INPUT_GET, 'eprov', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($provider == '') || !isset($this->providers[$provider])) {
			$response->errormsg = 'Invalid OpenID provider!';
			return false;
		}

		elxisLoader::loadFile('components/com_user/auth/openid/includes/openid.php');
		try {
			$openid = new LightOpenID($this->host);
			if (!$openid->mode) {
				$response->errormsg = eFactory::getLang()->get('AUTHFAILED');
				return false;
			} elseif ($openid->mode == 'cancel') {
				$response->errormsg = eFactory::getLang()->get('AUTHFAILED');
				return false;
			} elseif($openid->validate()) {
				$openid_identity = $openid->identity;
				$user_profile = $openid->getAttributes();

				if (!is_array($user_profile) || (count($user_profile) == 0)) {
					$response->errormsg = 'Login succeed but your user information are empty!';
					return false;
				}

				$response->uid = 0;
				$response->gid = 6;
				if (isset($user_profile['namePerson/first']) && ($user_profile['namePerson/first'] != '')) {
					$response->firstname = $user_profile['namePerson/first'];
					if (isset($user_profile['namePerson/last']) && ($user_profile['namePerson/last'] != '')) {
						$response->lastname = $user_profile['namePerson/last'];
					}
				} else if (isset($user_profile['namePerson']) && ($user_profile['namePerson'] != '')) {
					$parts = preg_split('/\s/', $user_profile['namePerson'], 2, PREG_SPLIT_NO_EMPTY);
					$response->firstname = $parts[0];
					if (isset($parts[1])) { $response->lastname = $parts[1]; }
				} else if (isset($user_profile['fullname']) && ($user_profile['fullname'] != '')) {
					$parts = preg_split('/\s/', $user_profile['fullname'], 2, PREG_SPLIT_NO_EMPTY);
					$response->firstname = $parts[0];
					if (isset($parts[1])) { $response->lastname = $parts[1]; }
				} else if (isset($user_profile['company/name']) && ($user_profile['company/name'] != '')) {
					$parts = preg_split('/\s/', $user_profile['company/name'], 2, PREG_SPLIT_NO_EMPTY);
					$response->firstname = $parts[0];
					if (isset($parts[1])) { $response->lastname = $parts[1]; }
				} else if (isset($user_profile['company/title']) && ($user_profile['company/title'] != '')) {
					$parts = preg_split('/\s/', $user_profile['company/title'], 2, PREG_SPLIT_NO_EMPTY);
					$response->firstname = $parts[0];
					if (isset($parts[1])) { $response->lastname = $parts[1]; }
				}

				if (isset($user_profile['contact/email']) && (strpos($user_profile['contact/email'], '@') !== false)) {
					$response->email = $user_profile['contact/email'];
				} else if (isset($user_profile['contact/internet/email']) && (strpos($user_profile['contact/internet/email'], '@') !== false)) {
					$response->email = $user_profile['contact/internet/email'];
				}

				if (isset($user_profile['media/image']) && ($user_profile['media/image'] != '')) {
					$response->avatar = $user_profile['media/image'];
				} else if (isset($user_profile['media/image/default']) && ($user_profile['media/image/default'] != '')) {
					$response->avatar = $user_profile['media/image/default'];
				} else if (isset($user_profile['media/image/64x64']) && ($user_profile['media/image/64x64'] != '')) {
					$response->avatar = $user_profile['media/image/64x64'];
				} else if (isset($user_profile['media/image/80x80']) && ($user_profile['media/image/80x80'] != '')) {
					$response->avatar = $user_profile['media/image/80x80'];
				} else if (isset($user_profile['media/image/128x128']) && ($user_profile['media/image/128x128'] != '')) {
					$response->avatar = $user_profile['media/image/128x128'];
				}

				if (isset($user_profile['namePerson/friendly']) && ($user_profile['namePerson/friendly'] != '')) {
					$response->uname = $user_profile['namePerson/friendly'];
				} else if (isset($user_profile['nickname']) && ($user_profile['nickname'] != '')) {
					$response->uname = $user_profile['nickname'];
				} else {
					$open_user = trim(eFactory::getSession()->get('open_user'));
					if ($open_user != '') {
						$n = strpos($open_user, '@');
						if (($n !== false) && ($n !== 0)) {
							$response->uname = substr($open_user, 0, $n);
						} else {
							$response->uname = $open_user;
						}
					} else {
						if ($response->email != '') {
							$parts = preg_split('/\@/', $response->email, 2, PREG_SPLIT_NO_EMPTY);
							$response->uname = $parts[0];
						} else {
							$response->uname = $provider.'_'.rand(10000, 99999);
						}
					}
				}
				return true;
			} else {
				$response->errormsg = eFactory::getLang()->get('AUTHFAILED');
				return false;
			}
		} catch(ErrorException $e) {
			$this->showError($e->getMessage());
			return false;
		}
	}


	/*******************/
	/* SHOW LOGIN FORM */
	/*******************/
	public function loginForm() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

        if (!function_exists('curl_init') && !in_array('https', stream_get_wrappers())) {
            echo '<div class="elx_error">You must have either https wrappers or CURL enabled to use OpenId authentication!</div>'."\n";
            return;
        }

		$baseurl = $elxis->secureBase().'/components/com_user/auth/openid';
		$eDoc->addScriptLink($baseurl.'/includes/openid.js');

		if ($eLang->getinfo('DIR') == 'rtl') {
			$float = 'right';
			$margin = '0 0 0 20px';
		} else {
			$float = 'left';
			$margin = '0 20px 0 0';
		}

		$action = $elxis->makeURL('user:login/openid.html', 'inner.php', true, false);
		$return = base64_encode($elxis->makeURL('user:/'));
		$token = md5(uniqid(rand(), true));
		eFactory::getSession()->set('token_fmopenid', $token);

		echo '<p style="color:#666; font-weight:bold; margin:10px 0; padding:0;">'.$eLang->get('SEL_OPENID_PROVIDER')."</p>\n";
		echo "<div>\n";
		foreach ($this->providers as $provider => $data) {
			$bgcolor = ($provider == 'openid') ? '#FFFF99;' : 'transparent';
			echo '<div id="open_'.$provider.'" style="margin:0 10px 10px 0; padding:2px; width:64px; float:left; text-align:center; background-color:'.$bgcolor.';">'."\n";
			echo '<a href="javascript:void(null);" onclick="setoidProvider(\''.$provider.'\')" title="'.$data['name'].'">';
			echo '<img src="'.$baseurl.'/includes/images/'.$provider.'.png" alt="'.$data['name'].'" /></a>'."\n";
			echo "</div>\n";
		}
		echo '<div style="clear:both;"></div>'."\n";
		echo "</div>\n";
?>
		<div style="margin:20px 0; padding:0;">
			<div id="open_label" style="font-weight:bold;"><?php printf($eLang->get('LOGIN_WITH'), 'OpenID'); ?></div>
			<form name="fmopenid" id="fmopenid" class="elx_form" method="post" action="<?php echo $action; ?>" onsubmit="return elxformvalopenid();" style="margin:10px 0; padding:0;">
				<div style="background-color:#f1f1f1; border:1px solid #ddd; color:#555; width:480px; padding:5px; margin:<?php echo $margin; ?>; float:<?php echo $float; ?>;">
					<span id="lblock">http://</span>
					<input type="text" name="openid_identifier" id="openid_identifier" size="20" value="" style="width:240px;" />
					<span id="rblock"></span>
				</div>
				<div style="width:190px; float:<?php echo $float; ?>;">
					<input type="hidden" name="provider" id="openid_provider" value="openid" dir="ltr" />
					<input type="hidden" name="auth_method" value="openid" dir="ltr" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" dir="ltr" />
					<input type="hidden" name="etask" value="sbm" dir="ltr" />
					<input type="hidden" name="token" value="<?php echo $token; ?>" />
					<button type="submit" name="sbmlog" title="<?php echo $eLang->get('LOGIN'); ?>" class="elxbutton" dir="ltr"><?php echo $eLang->get('LOGIN'); ?></button>
				</div>
				<div style="clear:both;"></div>
			</form>
			<div id="lng_openid_loginwith" style="margin:0; padding:0; display:none;"><?php printf($eLang->get('LOGIN_WITH'), 'zzz'); ?></div>
			<div id="lng_openid_reqfempty" style="margin:0; padding:0; display:none;"><?php echo $eLang->get('REQFIELDEMPTY'); ?></div>
		</div>

		<p style="margin:60px 0 0 0; padding:5px; border-top:1px solid #ddd; font-size:11px; color:#555; text-align:justify;">
			<strong>What is OpenID?</strong><br />
			OpenID allows you to use an existing account to sign in to multiple websites, without needing to create 
			new accounts. You can also control how much of that information is shared with the websites you visit. 
			With OpenID, your password is only given to your identity provider, and that 
			provider then confirms your identity to the websites you visit. Other than your provider, no website ever sees 
			your password, so you don't need to worry about an unscrupulous or insecure website compromising your identity.
		</p>

<?php 
	}


	/***********************/
	/* EXECUTE CUSTOM TASK */
	/***********************/
	public function runTask($etask) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$errormsg = '';
		if ($etask != 'sbm') {
			$this->showError();
			return;
		}

		$sess_token = trim(eFactory::getSession()->get('token_fmopenid'));
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
		if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$this->showError($eLang->get('REQDROPPEDSEC'));
			return;
		}

		$provider = trim(filter_input(INPUT_POST, 'provider', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		if (($provider == '') || !isset($this->providers[$provider])) {
			$this->showError('Invalid OpenID provider!');
			return;
		}

		$open_user = '';
		if ($this->providers[$provider]['userinput'] == true) {
			$openid_identifier = trim(filter_input(INPUT_POST, 'openid_identifier', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			if ($provider == 'openid') {
				if (!preg_match('#^http(s?)\:\/\/#i', $openid_identifier)) {
					$openid_identifier = 'http://'.$openid_identifier;
				}
				if (!filter_var($openid_identifier, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
					$this->showError('Invalid OpenID provider URL!');
					return;
				}
				$openlink = $openid_identifier;
			} else {
				if ($openid_identifier == '') {
					$this->showError($eLang->get('REQFIELDEMPTY'));
					return;
				}
				$openlink = $this->providers[$provider]['url'];
				$openlink = str_replace('{uname}', $openid_identifier, $openlink);
				$open_user = $openid_identifier;
			}
		} else {
			$openlink = $this->providers[$provider]['url'];
		}

		if ($open_user != '') {
			eFactory::getSession()->set('open_user', $open_user);
		}

		$returnurl = $elxis->makeURL('user:login/openid.html?etask=auth&eprov='.$provider, 'inner.php', true, false);
		elxisLoader::loadFile('components/com_user/auth/openid/includes/openid.php');
		try {
			$openid = new LightOpenID($this->host);
			if (!$openid->mode) {
				$openid->identity = $openlink;
				$openid->required = array(
					'namePerson',
					'namePerson/friendly',
					'namePerson/last',
					'contact/email',
					'namePerson/first',
					'nickname', 
    				'fullname',
    				'contact/internet/email',
    				'media/image',
    				'media/image/default',
    				'media/image/64x64',
    				'media/image/80x80',
    				'media/image/128x128',
    				'company/name',
    				'company/title'
				);

				$openid->returnUrl = $returnurl;
				if (ob_get_length() > 0) { @ob_end_clean(); }
				header('Location: '.$openid->authUrl());
			} elseif ($openid->mode == 'cancel') {
				$this->showError('User has canceled authentication!');
				return;
			} elseif($openid->validate()) {
				$elxis->redirect($returnurl);
			} else {
				$this->showError('Something is wrong!');
				return;
			}
		} catch(ErrorException $e) {
			$this->showError($e->getMessage());
			return;
		}
	}


	/**********************/
	/* SHOW ERROR MESSAGE */
	/**********************/
	private function showError($msg='') {
		$eLang = eFactory::getLang();

		if ($msg == '') { $msg = $eLang->get('REQUEST_DROP'); }
		$link = eFactory::getElxis()->makeURL('user:login/openid.html', 'inner.php', true);

		echo '<h2>'.$eLang->get('ERROR')."</h2>\n";
		echo '<div class="elx_error">'.$msg."</div>\n";
		echo '<div style=" margin: 20px 0;">'."\n";
		echo '<a href="'.$link.'">'.$eLang->get('RETRY')."</a>\n";
		echo "</div>\n";
	}


	/***************************************/
	/* CUSTOM ACTIONS TO PERFORM ON LOGOUT */
	/***************************************/
	public function logout() {
	}

}

?>