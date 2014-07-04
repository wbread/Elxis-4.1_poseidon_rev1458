<?php 
/**
* @version		$Id: twitter.auth.php 966 2012-03-11 19:07:28Z datahell $
* @package		Elxis
* @subpackage	Component User / Authentication
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class twitterAuthentication {

	private $consumer_key;
	private $consumer_secret;


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->consumer_key = trim($params->get('consumer_key'));
		$this->consumer_secret = trim($params->get('consumer_secret'));
	}


	/*******************************/
	/* AUTHENTICATE A TWITTER USER */
	/*******************************/
	public function authenticate(&$response, $options=array()) {
		if (($this->consumer_key == '') || ($this->consumer_secret == '')) {
			$response->errormsg = 'Required Twitter consumer parameters are empty!';
			return false;
		}

		include(ELXIS_PATH.'/components/com_user/auth/twitter/includes/EpiOAuth.php');
		include(ELXIS_PATH.'/components/com_user/auth/twitter/includes/EpiTwitter.php');

		$twitter = new EpiTwitter($this->consumer_key, $this->consumer_secret);

		if (isset($_GET['oauth_token']) || (isset($_COOKIE['twitter_oauth_token']) && isset($_COOKIE['twitter_oauth_token_secret']))) {
			if (!isset($_COOKIE['twitter_oauth_token']) || !isset($_COOKIE['twitter_oauth_token_secret'])) {
				$twitter->setToken($_GET['oauth_token']);
				$token = $twitter->getAccessToken();
				setcookie('twitter_oauth_token', $token->oauth_token);
				setcookie('twitter_oauth_token_secret', $token->oauth_token_secret);
				$twitter->setToken($token->oauth_token, $token->oauth_token_secret);
			} else {
				$twitter->setToken($_COOKIE['twitter_oauth_token'], $_COOKIE['twitter_oauth_token_secret']);
			}

			$user = $twitter->get_accountVerify_credentials();

			$response->uname = $user->screen_name;
			$name = trim($user->name);
			if ($name != '') {
				$parts = preg_split('/\s/', $name, 2, PREG_SPLIT_NO_EMPTY);
				$response->firstname = $parts[0];
				if (isset($parts[1])) {
					$response->lastname = $parts[1];
				}
			}

			$response->email = '';
			$response->uid = 0;
			$response->gid = 6;
			if (trim($user->profile_image_url_https) != '') {
				$response->avatar = $user->profile_image_url_https;
			} else {
				$response->avatar = $user->profile_image_url;
			}

			return true;
		} elseif (isset($_GET['denied'])) {
			$response->errormsg = 'You must sign in through twitter first';
			return false;
		} else {
			$response->errormsg = 'You are not logged in';
			return false;
		}
	}


	/*************************/
	/* SHOW GMAIL LOGIN FORM */
	/*************************/
	public function loginForm() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();

		$title = sprintf($eLang->get('LOGIN_WITH'), 'Twitter');
		$eDoc->setTitle($eLang->get('LOGIN').' - Twitter');
		$eDoc->setDescription($title);

		if (($this->consumer_key == '') || ($this->consumer_secret == '')) {
		$eDoc->setTitle($eLang->get('ERROR').' - Twitter login');
			echo '<div class="elx_error">Required Twitter consumer parameters are empty!</div>'."\n";
			return;
		}

		include(ELXIS_PATH.'/components/com_user/auth/twitter/includes/EpiOAuth.php');
		include(ELXIS_PATH.'/components/com_user/auth/twitter/includes/EpiTwitter.php');

		$twitter = new EpiTwitter($this->consumer_key, $this->consumer_secret);
		$link = $twitter->getAuthorizationUrl();
		$elxis->redirect($link);
	}


	/***********************/
	/* EXECUTE CUSTOM TASK */
	/***********************/
	public function runTask($etask) {
		if (ob_get_length() > 0) { @ob_end_clean(); }
		header('content-type:text/plain; charset=utf-8');
		echo 'Invalid request';
		exit();
	}


	/***************************************/
	/* CUSTOM ACTIONS TO PERFORM ON LOGOUT */
	/***************************************/
	public function logout() {
		$ts = time() - 100000;
		if (isset($_COOKIE['twitter_oauth_token'])) {
			setcookie('twitter_oauth_token', '', $ts); 
		}
		if (isset($_COOKIE['twitter_oauth_token_secret'])) {
			setcookie('twitter_oauth_token_secret', '', $ts); 
		}
	}

}

?>