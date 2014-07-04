<?php 
/**
* @version		$Id: users.db.php 249 2011-03-29 07:31:02Z datahell $
* @package		Elxis
* @subpackage	Database
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


class usersDbTable extends elxisDbTable {


	/*************************************************/
	/* CONSTRUCT PARENT CLASS AND SET INITIAL VALUES */
	/*************************************************/
	public function __construct() {
		parent::__construct('#__users', 'uid');

		$this->columns = array(
			'uid' => array('type' => 'integer', 'value' => null),
			'firstname' => array('type' => 'string', 'value' => null),
			'lastname' => array('type' => 'string', 'value' => null),
			'uname' => array('type' => 'string', 'value' => null),
			'pword' => array('type' => 'string', 'value' => null),
			'block' => array('type' => 'bit', 'value' => 0),
			'activation' => array('type' => 'string', 'value' => null),
			'gid' => array('type' => 'integer', 'value' => 7),
			'groupname' => array('type' => 'string', 'value' => 'Guest'),
			'avatar' => array('type' => 'string', 'value' => null),
			'preflang' => array('type' => 'string', 'value' => null),
			'timezone' => array('type' => 'string', 'value' => null),
			'country' => array('type' => 'string', 'value' => null),
			'city' => array('type' => 'string', 'value' => null),
			'address' => array('type' => 'string', 'value' => null),
			'postalcode' => array('type' => 'string', 'value' => null),
			'website' => array('type' => 'string', 'value' => null),
			'email' => array('type' => 'string', 'value' => null),
			'phone' => array('type' => 'string', 'value' => null),
			'mobile' => array('type' => 'string', 'value' => null),
			'gender' => array('type' => 'string', 'value' => null),
			'birthdate' => array('type' => 'string', 'value' => null),
			'occupation' => array('type' => 'string', 'value' => null),
			'registerdate' => array('type' => 'string', 'value' => null),
			'lastvisitdate' => array('type' => 'string', 'value' => null),
			'expiredate' => array('type' => 'string', 'value' => null),
			'profile_views' => array('type' => 'integer', 'value' => 0),
			'times_online' => array('type' => 'integer', 'value' => 0),
			'params' => array('type' => 'text', 'value' => null)
		);

		$eDate = eFactory::getDate();
		$this->setValue('lastvisitdate', $eDate->getDate());
		$this->setValue('expiredate', '2060-01-01 00:00:00');
	}


	/**********************/
	/* CHECK ROW VALIDITY */
	/**********************/
	public function check() {
		/**
		Note by datahell: to reduce script execution time and db queries the full check is performed only 
		for new users or on edit user profile by calling manually the fullCheck method before saving data. 
		*/
		$this->gid = (int)$this->gid;
		if (($this->gid < 1) || ($this->gid == 7) || ($this->gid == 6)) {
			$this->errorMsg = eFactory::getLang()->get('INVUSERGROUP');
			return false;
		}

		$this->uname = trim($this->uname);
		if ($this->uname == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('USERNAME'));
			return false;
		}
		$uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $this->uname);
		if ($uname != $this->uname) {
			$this->errorMsg = eFactory::getLang()->get('INVALIDUNAME');
			return false;
		}

		if (strlen($this->uname) < 4) {
			$this->errorMsg = eFactory::getLang()->get('INVALIDUNAME');
			return false;
		}

		$this->email = trim($this->email);
		if ($this->email == '') {
			$eLang = eFactory::getLang();
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('EMAIL'));
			return false;
		}

		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->errorMsg = eFactory::getLang()->get('INVALIDEMAIL');
			return false;
		}

		$uid = (int)$this->uid;
		if ($uid < 1) {
			$this->lastvisitdate = '1970-01-01 00:00:00';
		} else {
			$parts = preg_split("/[\s-]+/", $this->lastvisitdate);
			if (!$parts || (count($parts) != 4)) {
				$this->lastvisitdate = eFactory::getDate()->getDate();
			} else {
				if (!checkdate($parts[1], $parts[2], $parts[0])) {
					$this->lastvisitdate = eFactory::getDate()->getDate();
				}
			}
			unset($parts);
		}

		return true;
	}


	/*******************************************************************/
	/* FULL CHECK OF ROW VALIDITY (USE ON NEW ACCOUNT OR EDIT PROFILE) */
	/*******************************************************************/
	public function fullCheck() {
		$uid = (int)$this->uid;
		$eLang = eFactory::getLang();
		$this->gid = (int)$this->gid;
		if (($this->gid < 1) || ($this->gid == 7) || ($this->gid == 6)) {
			$this->errorMsg = $eLang->get('INVUSERGROUP');
			return false;
		}

		$elxis = eFactory::getElxis();
		if (($uid < 1) && (!defined('ELXIS_ADMIN'))) {
			$this->gid = 5;
			if ($elxis->getConfig('REGISTRATION') !== 1) {
				$this->errorMsg = 'New users registration is not allowed!';
				return false;
			}
		}

		$stmt = $this->db->prepare("SELECT ".$this->db->quoteId('groupname')." FROM ".$this->db->quoteId('#__groups')." WHERE ".$this->db->quoteId('gid')." = :groupid");
		$stmt->bindParam(':groupid', $this->columns['gid']['value'], PDO::PARAM_INT); //avoid PHP 5.2 overload bug with $this->gid
		$stmt->execute();
		$gname = (string)$stmt->fetchResult();
		if (!$gname || ($gname == '')) {
			$this->errorMsg = $eLang->get('INVUSERGROUP');
			return false;
		}
		$this->groupname = $gname;
		unset($gname);

		$this->uname = trim($this->uname);
		if ($this->uname == '') {
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('USERNAME'));
			return false;
		}
		$uname = preg_replace('/[^A-Z\-\_0-9]/i', '', $this->uname);
		if ($uname != $this->uname) {
			$this->errorMsg = $eLang->get('INVALIDUNAME');
			return false;
		}
		if (strlen($this->uname) < 4) {
			$this->errorMsg = $eLang->get('INVALIDUNAME');
			return false;
		}

		$stmt = $this->db->prepare("SELECT COUNT(".$this->db->quoteId('uid').") FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('uname')." = :username");
		$stmt->bindParam(':username', $this->columns['uname']['value'], PDO::PARAM_STR); //avoid PHP 5.2 overload bug with $this->uname
		$stmt->execute();
		$c = (int)$stmt->fetchResult();
		if (($uid < 1) && ($c > 0)) {
			$this->errorMsg = sprintf($eLang->get('UNAMETAKEN'), $this->uname);
			return false;
		} else if (($uid > 0) && ($c <> 1)) {
			$this->errorMsg = 'You can not change username!';
			return false;
		}

		$this->pword = trim($this->pword);
		if ($this->pword == '') {
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('PASSWORD'));
			return false;
		}
		$pword = filter_var($this->pword, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if ($pword != $this->pword) {
			$this->errorMsg = $eLang->get('INVALIDPASS');
			return false;
		}
		if (eUTF::strlen($this->pword) < 6) { //valid check for both unencrypted and encrypted password
			$this->errorMsg = $eLang->get('INVALIDPASS');
			return false;
		}

		$this->email = trim($this->email);
		if ($this->email == '') {
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('EMAIL'));
			return false;
		}

		if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
			$this->errorMsg = $eLang->get('INVALIDEMAIL');
			return false;
		}

		if (($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') || ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '')) {
			$parts = explode('@', $this->email);
			if (!$parts || !is_array($parts) || (count($parts) != 2)) {
				$this->errorMsg = $eLang->get('INVALIDEMAIL');
				return false;
			}
			$emaildomain = strtolower($parts[1]);
			if ($elxis->getConfig('REGISTRATION_EMAIL_DOMAIN') != '') {
				if ($emaildomain != $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN')) {
					if ($eLang->exist('ONLYMAILFROMALLOW')) {
						$this->errorMsg = sprintf($eLang->get('ONLYMAILFROMALLOW'), $elxis->getConfig('REGISTRATION_EMAIL_DOMAIN'));
					} else {
						$this->errorMsg = 'Only email addresses from '.$elxis->getConfig('REGISTRATION_EMAIL_DOMAIN').' are allowed!';
					}
					return false;
				}
			}
			if ($elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS') != '') {
				$exdomains = explode(',', $elxis->getConfig('REGISTRATION_EXCLUDE_EMAIL_DOMAINS'));
				if ($exdomains && is_array($exdomains) && (count($exdomains) > 0)) {
					foreach ($exdomains as $exdomain) {
						if ($emaildomain == $exdomain) {
							if ($eLang->exist('EMAILADDRNOTACC')) {
								$this->errorMsg = sprintf($eLang->get('EMAILADDRNOTACC'), $emaildomain);
							} else {
								$this->errorMsg = 'Email addresses from '.$emaildomain.' are not acceptable!';
							}
							return false;
						}
					}
				}
			}
			unset($parts, $emaildomain);
		}

		$stmt = $this->db->prepare("SELECT ".$this->db->quoteId('uid')." FROM ".$this->db->quoteId('#__users')." WHERE ".$this->db->quoteId('email')." = :mail");
		$stmt->bindParam(':mail', $this->columns['email']['value'], PDO::PARAM_STR); //avoid PHP 5.2 overload bug with $this->email
		$stmt->execute();
		$uids = $stmt->fetchCol();
		$c = count($uids);
		if ($c > 1) {
			$this->errorMsg = 'There are '.$c.' users sharing the same email address! Please change your email!';
			return false;
		} else if ($c == 1) {
			if ($uids[0] != $uid) {
				if ($eLang->exist('EMAILTAKEN')) {
					$this->errorMsg = sprintf($eLang->get('EMAILTAKEN'), $this->email);
				} else {
					$this->errorMsg = 'Email '.$this->email.' is already in use by an other user. Please select another.';
				}
				return false;
			}
		}

		$pat = "#([\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$this->firstname = filter_var($this->firstname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->firstname = eUTF::trim(preg_replace($pat, '', $this->firstname));
		$this->lastname = filter_var($this->lastname, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->lastname = eUTF::trim(preg_replace($pat, '', $this->lastname));
		if (trim($this->firstname) == '') {
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('FIRSTNAME'));
			return false;
		}
		if (trim($this->lastname) == '') {
			$this->errorMsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('LASTNAME'));
			return false;
		}

		$this->country = (trim($this->country) == '') ? null : filter_var($this->country, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->city = (trim($this->city) == '') ? null : filter_var($this->city, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->address = (trim($this->address) == '') ? null : filter_var($this->address, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->postalcode = (trim($this->postalcode) == '') ? null : filter_var($this->postalcode, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		if (($this->gender != 'male') && ($this->gender != 'female')) { $this->gender = null; }
		if (trim($this->website) != '') {
			if (!filter_var($this->website, FILTER_VALIDATE_URL)) { $this->website = null; }
		} else {
			$this->website = null;
		}
		$this->phone = (trim($this->phone) == '') ? null : filter_var($this->phone, FILTER_SANITIZE_NUMBER_INT);
		$this->mobile = (trim($this->mobile) == '') ? null : filter_var($this->mobile, FILTER_SANITIZE_NUMBER_INT);
		$this->occupation = (trim($this->occupation) == '') ? null : filter_var($this->occupation, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$this->timezone = (trim($this->timezone) == '') ? null : filter_var($this->timezone, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		if (trim($this->preflang) != '') {
			if (!file_exists(ELXIS_PATH.'/language/'.$this->preflang.'/'.$this->preflang.'.php')) { $this->preflang = null; }
		}
		$this->params = (trim($this->params) == '') ? null : $this->params;
		if (trim($this->avatar) != '') {
			$ext = strtolower(substr($this->avatar, strrpos($this->avatar, '.') + 1));
			if (!in_array($ext, array('png', 'jpg', 'jpeg', 'gif'))) { $this->avatar = null; }
			unset($ext);
		} else {
			$this->avatar = null;
		}

		if ((trim($this->expiredate) == '') || ($this->expiredate == '2060-01-01 00:00:00')) {
			$this->expiredate == '2060-01-01 00:00:00';
		} else {
			$parts = preg_split("/[\s-]+/", $this->expiredate);
			if (!$parts || (count($parts) != 4)) {
				$this->errorMsg = $eLang->exist('INVACCEXPDATE') ? $eLang->get('INVACCEXPDATE') :' Invalid account expiration date!';
				return false;
			} else {
				if (!checkdate($parts[1], $parts[2], $parts[0])) {
					$this->errorMsg = $eLang->exist('INVACCEXPDATE') ? $eLang->get('INVACCEXPDATE') :' Invalid account expiration date!';
					return false;
				}
			}
			unset($parts);
		}
		
		if ($uid < 1) {
			$this->registerdate = eFactory::getDate()->getDate();
		} else {
			$parts = preg_split("/[\s-]+/", $this->registerdate);
			if (!$parts || (count($parts) != 4)) {
				$this->registerdate = eFactory::getDate()->getDate();
			} else {
				if (!checkdate($parts[1], $parts[2], $parts[0])) {
					$this->registerdate = eFactory::getDate()->getDate();
				}
			}
			unset($parts);
		}

		if (trim($this->birthdate) != '') {
			$parts = preg_split("/[\s-]+/", $this->birthdate);
			if (!$parts || (count($parts) < 3) || (count($parts) > 4)) {
				$this->birthdate = null;
			} else {
				if (!checkdate($parts[1], $parts[2], $parts[0])) {
					$this->birthdate = null;
				}
			}
			unset($parts);
		} else {
			$this->birthdate = null;
		}

		$this->block = (int)$this->block;
		$this->profile_views = (int)$this->profile_views;
		$this->times_online = (int)$this->times_online;
		if ($uid < 1) {
			if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 0) {
				$this->activation = null;
				$this->block = defined('ELXIS_ADMIN') ? $this->block : 0;
			} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') === 2) {
				$this->activation = null;
				$this->block = defined('ELXIS_ADMIN') ? $this->block : 1;
			} else {
				if (trim($this->activation) == '') {
					$act = '';
					while (strlen($act) < 40) { $act .= mt_rand(0, mt_getrandmax()); }
					$this->activation = sha1($act);
					$this->block = defined('ELXIS_ADMIN') ? $this->block : 1;
				}
			}
		} else {
			$this->activation = (trim($this->activation) == '') ? null : filter_var($this->activation, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
		}

		if ($uid < 1) {
			$this->lastvisitdate = '1970-01-01 00:00:00';
		} else {
			$parts = preg_split("/[\s-]+/", $this->lastvisitdate);
			if (!$parts || (count($parts) != 4)) {
				$this->lastvisitdate = eFactory::getDate()->getDate();
			} else {
				if (!checkdate($parts[1], $parts[2], $parts[0])) {
					$this->lastvisitdate = eFactory::getDate()->getDate();
				}
			}
			unset($parts);
		}

		return true;
	}

}

?>