<?php 
/**
* @version		$Id: ldap.auth.php 1349 2012-11-07 17:17:16Z datahell $
* @package		Elxis
* @subpackage	Component User / Authentication
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class ldapAuthentication {

	private $host = '';
	private $port = 389;
	private $ldap_version = 3;
	private $tls = 0;
	private $referrals = 0;
	private $base_dn = '';
	private $user_dn = '';
	private $ldap_filter = '';
	private $ldap_firstname = '';
	private $ldap_lastname = '';
	private $ldap_fullname = '';
	private $ldap_email = '';
	private $ldap_username = '';
	private $ad_errormsg = '';


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct($params) {
		$this->host = trim($params->get('host', ''));
		$this->port = (int)$params->get('port', 389);
		$this->ldap_version = (int)$params->get('ldap_version', 3);
		$this->tls = (int)$params->get('tls', 0);
		$this->referrals = (int)$params->get('referrals', 0);
		$this->base_dn = trim($params->get('base_dn', ''));
		$this->user_dn = trim($params->get('user_dn', ''));
		$this->ldap_filter = trim($params->get('ldap_filter', ''));
		$this->ldap_firstname = trim($params->get('ldap_firstname', ''));
		$this->ldap_lastname = trim($params->get('ldap_lastname', ''));
		$this->ldap_fullname = trim($params->get('ldap_fullname', ''));
		$this->ldap_email = trim($params->get('ldap_email', ''));
		$this->ldap_username = trim($params->get('ldap_username', ''));
	}


	/*****************************/
	/* AUTHENTICATE AN LDAP USER */
	/*****************************/
	public function authenticate(&$response, $options=array()) {
		$uname = (isset($options['uname'])) ? trim($options['uname']) : '';
		$pword = (isset($options['pword'])) ? trim($options['pword']) : '';
		if ($uname == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('USERNAME'));
			return false;
		}
		if ($pword == '') {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
			return false;
		}

		if (strlen($pword) < 3) {
			$response->errormsg = eFactory::getLang()->get('PASSTOOSHORT');
			return false;
		}

		if (!function_exists('ldap_connect')) {
			$response->errormsg = 'LDAP is not supported by the web server!';
			return false;
		}

		$uname_san = filter_var($uname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$pword_san = filter_var($pword, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ($uname !== $uname_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('USERNAME'));
			return false;
		}
		if ($pword !== $pword_san) {
			$eLang = eFactory::getLang();
			$response->errormsg = sprintf($eLang->get('FIELDNOACCCHAR'), $eLang->get('PASSWORD'));
			return false;
		}

		$udata = $this->ldapUser($uname, $pword);
		if (($udata === false) || (!is_object($udata))) {
			$response->errormsg = ($this->ad_errormsg != '') ? $this->ad_errormsg : eFactory::getLang()->get('AUTHFAILED');
			return false;
		}

		$response->uname = (trim($udata->uname) != '') ? $udata->uname : $uname;
		$response->firstname = $udata->firstname;
		$response->lastname = $udata->lastname;
		$response->email = $udata->email;
		return true;
	}


	/************************/
	/* SHOW LDAP LOGIN FORM */
	/************************/
	public function loginForm() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDoc = eFactory::getDocument();
		$eAuth = eRegistry::get('eAuth');

		$js = 'elxAutocompOff(\'uloguname\'); elxAutocompOff(\'ulogpword\');';
		$eDoc->addScript($js);

		$action = $elxis->makeURL('user:login/ldap.html', 'inner.php', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmuserlogin',
			'action' => $action,
			'idprefix' => 'ulog',
			'label_width' => 200,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$return = base64_encode($elxis->makeURL('user:/'));

		$data = $eAuth->getAuthData('ldap');
		$title = sprintf($eLang->get('LOGIN_WITH'), $data['title']);

		$eDoc->setTitle($eLang->get('LOGIN').' - '.$data['title']);
		$eDoc->setDescription($title);

		$form = new elxisForm($formOptions);
		$form->openFieldset($title);
		$form->addText('uname', '', $eLang->get('USERNAME'), array('required' => 1));
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), array('required' => 1, 'maxlength' => 60));
		$form->addHidden('return', $return);
		$form->addHidden('auth_method', 'ldap');
		$form->addButton('sbmlog', $eLang->get('LOGIN'), 'submit', array('tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();
		$form->render();
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
	}


	/*******************************************************************/
	/* AUTHENTICATE USER TO LDAP HOST AND GET ACCOUNT INFORMATION DATA */
	/*******************************************************************/
	private function ldapUser($username, $password) {
		if ($this->host == '') {
			$this->ad_errormsg = 'LDAP host can not be empty!';
			return false;
		}

        if ($this->port < 1) {
        	$this->port = (preg_match('/^(ldaps)/i', $this->host)) ? 636 : 389;
		}

		$this->user_dn = str_replace('{username}', $username, $this->user_dn);
		$this->user_dn = str_replace('{password}', $password, $this->user_dn);

		$adconn = @ldap_connect($this->host, $this->port);
		if (!$adconn) {
			$this->ad_errormsg = 'Could not connect to LDAP host!';
			return false;
		}
		
		if ($this->ldap_version !== 2) {
			if (!@ldap_set_option($adconn, LDAP_OPT_PROTOCOL_VERSION, 3)) {
				ldap_close($adconn);
				$this->ad_errormsg = 'Could not set LDAP protocol!';
				return false;
			}
		}

        @ldap_set_option($adconn, LDAP_OPT_REFERRALS, $this->referrals);
  
        if ($this->tls == 1) {
            if (!@ldap_start_tls($adconn)) {
            	ldap_close($adconn);
            	$this->ad_errormsg = 'Could not start TLS';
            	return false;
            }
        }

		if ($this->base_dn == '') { $this->base_dn = $this->getBaseDN($adconn, $this->host); }
		if ($this->user_dn == '') { $this->user_dn = $this->getUserDN($adconn, $this->base_dn, $username, $this->ldap_username); }

		$bind = @ldap_bind($adconn, $this->user_dn, $password);
        if (!$bind) {
        	$err = @ldap_error($adconn);
        	$this->ad_errormsg = eFactory::getLang()->get('AUTHFAILED').'. '.$err;
            ldap_close($adconn);
			return false;
		}

		if ($this->ldap_filter != '') {
			$this->ldap_filter = str_replace('{username}', $username, $this->ldap_filter);
		} else if ($this->ldap_username != '') {
			$this->ldap_filter = '(|(cn='.$username.')('.$this->ldap_username.'='.$username.'))';
		} else {
			$this->ldap_filter = '(|(samaccountname='.$username.')(uid='.$username.')(mail='.$username.'))';
		}

		$attributes = array();
		if ($this->ldap_username != '') { $attributes[] = $this->ldap_username; }
		if ($this->ldap_firstname != '') { $attributes[] = $this->ldap_firstname; }
		if ($this->ldap_lastname != '') { $attributes[] = $this->ldap_lastname; }
		if ($this->ldap_fullname != '') { $attributes[] = $this->ldap_fullname; }
		if ($this->ldap_email != '') {
			if (strpos($this->ldap_email, '{') === false) { $attributes[] = $this->ldap_email; }
		}

		if (!isset($attributes['firstname'])) { $attributes[] = 'firstname'; }
		if (!isset($attributes['lastname'])) { $attributes[] = 'lastname'; }
		if (!isset($attributes['fullname'])) { $attributes[] = 'fullname'; }
		if (!isset($attributes['givenname'])) { $attributes[] = 'givenname'; }
		if (!isset($attributes['mail'])) { $attributes[] = 'mail'; }
		if (!isset($attributes['email'])) { $attributes[] = 'email'; }
		if (!isset($attributes['sn'])) { $attributes[] = 'sn'; }
		if (!isset($attributes['cn'])) { $attributes[] = 'cn'; }
		if (!isset($attributes['samaccountname'])) { $attributes[] = 'samaccountname'; }

		$result = ldap_search($adconn, $this->base_dn, $this->ldap_filter, $attributes);
		$entries = ldap_get_entries($adconn, $result);
		ldap_close($adconn);

		$udata = new stdClass;
		$udate->uname = null;
		$udata->firstname = null;
		$udata->lastname = null;
		$udata->fullname = null;
		$udata->email = null;

		if (strpos($this->ldap_email, '{') !== false) {
			$udata->email = str_replace('{username}', $username, $this->ldap_email);
		} elseif ($entries && ($entries["count"] > 0)) {
			if (($this->ldap_email != '') && isset($entries[0][ $this->ldap_email ][0])) {
				$udata->email = $entries[0][ $this->ldap_email ][0];
			} elseif (isset($entries[0]['mail'][0])) {
				$udata->email = $entries[0]['mail'][0];
			} elseif (isset($entries[0]['email'][0])) {
				$udata->email = $entries[0]['email'][0];
			}
		}

		if ($entries && ($entries["count"] > 0)) {
			if (($this->ldap_firstname != '') && isset($entries[0][ $this->ldap_firstname ][0])) {
				$udata->firstname = $entries[0][ $this->ldap_firstname ][0];
			} elseif (isset($entries[0]['firstname'][0])) {
				$udata->firstname = $entries[0]['firstname'][0];
			} elseif (isset($entries[0]['givenname'][0])) {
				$udata->firstname = $entries[0]['givenname'][0];
			}
		}

		if ($entries && ($entries["count"] > 0)) {
			if (($this->ldap_lastname != '') && isset($entries[0][ $this->ldap_lastname ][0])) {
				$udata->lastname = $entries[0][ $this->ldap_lastname ][0];
			} elseif (isset($entries[0]['lastname'][0])) {
				$udata->lastname = $entries[0]['lastname'][0];
			} elseif (isset($entries[0]['sn'][0])) {
				$udata->lastname = $entries[0]['sn'][0];
			}
		}

		if ($entries && ($entries["count"] > 0)) {
			if (($this->ldap_fullname != '') && isset($entries[0][ $this->ldap_fullname ][0])) {
				$udata->fullname = $entries[0][ $this->ldap_fullname ][0];
			} elseif (isset($entries[0]['fullname'][0])) {
				$udata->fullname = $entries[0]['fullname'][0];
			} elseif (isset($entries[0]['cn'][0])) {
				$udata->fullname = $entries[0]['cn'][0];
			}
		}

		if ($entries && ($entries["count"] > 0)) {
			if (($this->ldap_username != '') && isset($entries[0][ $this->ldap_username ][0])) {
				$udata->uname = $entries[0][ $this->ldap_username ][0];
			} elseif (isset($entries[0]['samaccountname'][0])) {
				$udata->uname = $entries[0]['samaccountname'][0];
			}
		}

		if (trim($udata->uname) == '') {
			$parts = preg_split('/\@/', $username, 2, PREG_SPLIT_NO_EMPTY);
			$udata->uname = $parts[0];
		}

		if ((trim($udata->firstname) == '') && (trim($udata->fullname) != '')) {
			$parts = preg_split('/[\s]/', $udata->fullname, 2, PREG_SPLIT_NO_EMPTY);
			if ($parts) {
				$udata->firstname = $parts[0];
				if (trim($udata->lastname) == '') {
					if (isset($parts[1]) && (trim($parts[1]) != '')) {
						$udata->lastname = $parts[1];
					}
				}
			}
		}

		if (trim($udata->firstname) == '') { $udata->firstname = $udate->uname; }

		return $udata;
	}


	/****************/
	/* FIND USER DN */
	/****************/
	private function getUserDN($adconn, $base_dn, $username, $ldap_username) {
		if ($ldap_username == '') { $ldap_username = 'uid'; }
		if (!@ldap_bind($adconn)) { return false; }
		$res_id = @ldap_search($adconn, $base_dn, $ldap_username.'='.$username);
		if (!$res_id) { return false; }
		if (ldap_count_entries($adconn, $res_id) <> 1) { return false; }
		$entry_id = @ldap_first_entry($adconn, $res_id);
		if (!$entry_id) { return false; }
		$user_dn = ldap_get_dn($adconn, $entry_id);
		return $user_dn;
	}


	/*************************************/
	/* FIND BASE DN OF DOMAIN CONTROLLER */
	/*************************************/
    private function getBaseDN($adconn, $host) {
        $sr = @ldap_read($adconn, NULL, 'objectClass=*', array('defaultnamingcontext'));
        if (!$sr) {	return $this->getBaseDNfromHost($host); }
        $namingContext = @ldap_get_entries($adconn, $sr);
		if (isset($namingContext[0]['defaultnamingcontext'][0])) {
			return $namingContext[0]['defaultnamingcontext'][0];
		} else if (isset($namingContext[0]['dn'])) {
			return (is_array($namingContext[0]['dn'])) ? $namingContext[0]['dn'][0] : $namingContext[0]['dn'];
		} else {
			return $this->getBaseDNfromHost($host);
		}
    }


	/************************************/
	/* GUESS BASE DN FROM LDAP HOSTNAME */
	/************************************/
	private function getBaseDNfromHost($host) {
		$parts = preg_split('/\./', $host, -1, PREG_SPLIT_NO_EMPTY);
		if ($parts) {
			$c = count($parts) - 1;
			$out = 'dc='.$parts[$c];
			$c = $c - 1;
			if (isset($parts[$c])) {
				$out = 'dc='.$parts[$c].','.$out;
			}
			return $out;
		}
		return null;
	}

}

?>