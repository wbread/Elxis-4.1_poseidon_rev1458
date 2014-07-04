<?php 
/**
* @version		$Id: amembers.html.php 1225 2012-07-02 17:25:08Z datahell $
* @package		Elxis
* @subpackage	Component User
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class amembersUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*******************/
	/* SHOW USERS LIST */
	/*******************/
	public function listusers($avlangs) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		echo '<h2>'.$eLang->get('MEMBERSLIST')."</h2>\n";

		elxisLoader::loadFile('includes/libraries/elxis/grid.class.php');
		$grid = new elxisGrid('lusers', $eLang->get('MEMBERSLIST'));
		$grid->setOption('url', $elxis->makeAURL('user:users/getusers.xml', 'inner.php'));
		$grid->setOption('sortname', 'uname');
		$grid->setOption('sortorder', 'asc');
		$grid->setOption('rp', $elxis->getCookie('rp', 10));
		$grid->setOption('showToggleBtn', true);
		$grid->setOption('singleSelect', true);
		$grid->addColumn($eLang->get('ID'), 'uid', 40, false, 'center');
		$grid->addColumn($eLang->get('NAME'), 'name', 160, true, 'auto');
		$grid->addColumn($eLang->get('USERNAME'), 'uname', 110, true, 'auto');
		$grid->addColumn($eLang->get('ACTIVE'), 'block', 80, true, 'center');
		$grid->addColumn($eLang->get('GROUP'), 'groupname', 120, true, 'auto');
		$grid->addColumn($eLang->get('EMAIL'), 'email', 120, true, 'auto');
		$grid->addColumn($eLang->get('REGDATE_SHORT'), 'registerdate', 130, true, 'auto');
		$grid->addColumn($eLang->get('LASTVISIT'), 'lastvisitdate', 130, true, 'auto');
		$grid->addColumn($eLang->get('ARTICLES'), 'articles', 80, false, 'center');
		if ($elxis->acl()->check('com_user', 'profile', 'edit') > 1) {
			$grid->addButton($eLang->get('NEW'), 'adduser', 'add', 'usersaction');
			$grid->addSeparator();
			$grid->addButton($eLang->get('EDIT'), 'edituser', 'edit', 'usersaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_user', 'profile', 'delete') > 1) {
			$grid->addButton($eLang->get('DELETE'), 'deleteuser', 'delete', 'usersaction');
			$grid->addSeparator();
		}
		if ($elxis->acl()->check('com_user', 'profile', 'block') == 1) {
			$grid->addButton($eLang->get('TOGGLE_ACTIVE'), 'toggleuser', 'toggle', 'usersaction');
			$grid->addSeparator();
		}
		$grid->addSearch($eLang->get('ID'), 'uid', false);
		$grid->addSearch($eLang->get('FIRSTNAME'), 'firstname', false);
		$grid->addSearch($eLang->get('LASTNAME'), 'lastname', false);
		$grid->addSearch($eLang->get('USERNAME'), 'uname', true);
		$grid->addSearch($eLang->get('CITY'), 'city', false);
		$grid->addSearch($eLang->get('ADDRESS'), 'address', false);
		$grid->addSearch($eLang->get('EMAIL'), 'email', false);
		$grid->addSearch($eLang->get('TELEPHONE'), 'phone', false);
		$grid->addSearch($eLang->get('MOBILE'), 'mobile', false);
		$grid->addSearch($eLang->get('WEBSITE'), 'website', false);
		$filters = array('' => '- '.$eLang->get('ANY').' -');
		foreach ($avlangs as $k => $v) { $filters[$k] = $v['LANGUAGE'].' - '.$v['NAME']; }
		$grid->addFilter($eLang->get('LANGUAGE'), 'preflang', $filters);
		$filters = array('' => '- '.$eLang->get('ANY').' -', 'male' => $eLang->get('MALE'), 'female' => $eLang->get('FEMALE'));
		$grid->addFilter($eLang->get('GENDER'), 'gender', $filters);
?>

		<script type="text/javascript">
		/* <![CDATA[ */
		function usersaction(task, grid) {
			if (task == 'adduser') {
				var newurl = '<?php echo $elxis->makeAURL('user:users/edit.html'); ?>?uid=0';
				location.href = newurl;
			} else if (task == 'edituser') {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					var newurl = '<?php echo $elxis->makeAURL('user:users/edit.html'); ?>?uid=';
					var items = $('.trSelected',grid);
					newurl += items[0].id.substr(3);
					location.href = newurl;
				}
			} else if ((task == 'toggleuser') || (task == 'deleteuser')) {
				var nsel = $('.trSelected', grid).length;
				if (nsel < 1) {
					alert('<?php echo $eLang->get('NO_ITEMS_SELECTED'); ?>');
					return false;
				} else {
					if ((task == 'toggleuser') || ((task == 'deleteuser') && confirm('<?php echo htmlspecialchars($eLang->get('SURE_DEL_USER')); ?>'))) {
					var items = $('.trSelected',grid);
					var myid = <?php echo $elxis->user()->uid; ?>;
					var userid = parseInt(items[0].id.substr(3));
					if (userid == myid) {
						alert('<?php echo htmlspecialchars($eLang->get('CNOT_ACTION_SELF')); ?>');
						return false;
					}
					var edata = {'uid': userid};
					if (task == 'toggleuser') {
						var eurl = '<?php echo $elxis->makeAURL('user:users/toggleuser', 'inner.php'); ?>';
					} else {
						var eurl = '<?php echo $elxis->makeAURL('user:users/deleteuser', 'inner.php'); ?>';
					}
					var successfunc = function(xreply) {
						var rdata = new Array();
						rdata = xreply.split('|');
						var rok = parseInt(rdata[0]);
						if (rok == 1) {
							$("#lusers").flexReload();
						} else {
							alert(rdata[1]);
						}
					}
					elxAjax('POST', eurl, edata, null, null, successfunc, null);
					}
				}
			} else {
				alert('Invalid request!');
			}
		}
		/* ]]> */
		</script>

<?php 
		$grid->render();
		unset($grid);
	}


	/******************/
	/* EDIT USER HTML */
	/******************/
	public function editUser($row, $info, $userparams) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$action = $elxis->makeAURL('user:users/save.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'elxisform',
			'action' => $action,
			'idprefix' => 'epr',
			'label_width' => 220,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 1,
			'autocomplete_off' => true,
			'jsonsubmit' => 'document.elxisform.submit()'
		);

		$form = new elxisForm($formOptions);
		$form->openTab($eLang->get('ACCOUNT_DETAILS'));
		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
		$form->addUsergroup('gid', $eLang->get('GROUP'), $row->gid, 2, $elxis->acl()->getLevel());
		$readonly = (intval($row->uid) > 0) ? 1 : 0;
		$form->addText('uname', $row->uname, $eLang->get('USERNAME'), array('required' => 1, 'readonly' => $readonly, 'dir' => 'ltr', 'maxlength' => 60));
		$passreq = 0;
		$passtip = $eLang->get('ONLY_IF_CHANGE');
		if (intval($row->uid) < 1) {
			$passreq = 1;
			$passtip = '';
		}
		$form->addPassword('pword', '', $eLang->get('PASSWORD'), 
			array(
				'required' => $passreq,
				'maxlength' => 60,
				'tip' => $passtip,
				'password_meter' => 1,
				'onkeyup' => 'elxPasswordMeter(\'elxisform\', \'eprpword\', \'epruname\');'
			)
		);
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), array('required' => $passreq, 'maxlength' => 60, 'match' => 'eprpword'));
		$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 1,  'dir' => 'ltr', 'size' => 30));
		$form->addYesNo('block', $eLang->get('BLOCKUSER'), $row->block, array('vertical_options' => 0));

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$avatar = (trim($row->avatar) != '') ? 'media/images/avatars/'.$row->avatar : '';
			$form->addImage('avatar', $avatar, $eLang->get('AVATAR'), array('tip' => $eLang->get('AVATAR_D')));
		}

		if ($row->uid > 0) {
			$form->addInfo($eLang->get('MEMBERSINCE'), $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_10')));
			if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
				$form->addInfo($eLang->get('LASTVISIT'), $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_10')));
			} else {
				$form->addInfo($eLang->get('LASTVISIT'), $eLang->get('NEVER'));
			}
		}

		$val = $eDate->elxisToLocal($row->expiredate, true);
		$datetime = new DateTime($val);
		$val = $datetime->format($eLang->get('DATE_FORMAT_BOX'));
		$form->addDate('expiredate', $val, $eLang->get('EXPIRATION_DATE'), array('required' => 1));
		$form->closeTab();

		$form->openTab($eLang->get('PERSONAL_DETAILS'));
		$options = array();
		$options[] = $form->makeOption('male', $eLang->get('MALE'));
		$options[] = $form->makeOption('female', $eLang->get('FEMALE'));
		$form->addRadio('gender', $eLang->get('GENDER'), $row->gender, $options, array('vertical_options' => 0, 'dir' => 'rtl'));
		$val = '';
		if (trim($row->birthdate) != '') {
			$val = $eDate->elxisToLocal($row->birthdate, true);
			$datetime = new DateTime($val);
			$val = $datetime->format($eLang->get('DATE_FORMAT_BOX'));
		}
		$form->addDate('birthdate', $val, $eLang->get('BIRTHDATE'));
		$form->addText('occupation', $row->occupation, $eLang->get('OCCUPATION'), array('dir' => 'rtl', 'size' => 35, 'maxlength' => 120));
		$val = (trim($row->country) == '') ? $eLang->getinfo('REGION') : $row->country;
		$form->addCountry('country', $eLang->get('COUNTRY'), $val, array('dir' => 'rtl'));
		$form->addText('city', $row->city, $eLang->get('CITY'), array('dir' => 'rtl'));
		$form->addText('postalcode', $row->postalcode, $eLang->get('POSTAL_CODE'));
		$form->addText('address', $row->address, $eLang->get('ADDRESS'), array('dir' => 'rtl', 'size' => 35, 'maxlength' => 120));
		$form->addText('phone', $row->phone, $eLang->get('TELEPHONE'), array('maxlength' => 40));
		$form->addText('mobile', $row->mobile, $eLang->get('MOBILE'), array('maxlength' => 40));
		$form->addURL('website', $row->website, $eLang->get('WEBSITE'), array('size' => 35, 'maxlength' => 120));
		$val = $userparams->get('twitter', '');
		$form->addText('params_twitter', $val, $eLang->get('TWITACCOUNT'), array('tip' => $eLang->get('TWITACCOUNT_D'), 'maxlength' => 60));
		$form->closeTab();

		$form->openTab($eLang->get('PREFERENCES'));
		$val = (trim($row->preflang) == '') ? $eLang->getinfo('LANGUAGE') : $row->preflang;
		$form->addLanguage('preflang', $eLang->get('LANGUAGE'), $val, array('tip' => $eLang->get('SETPREFLANG')));
		$tz = ($elxis->user()->uid == $row->uid) ? $eDate->getTimezone() : $row->timezone;
		if (trim($tz) == '') { $tz = $eDate->getTimezone(); }
		$user_daytime = $eDate->worldDate('now', $tz, $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $tz, array('tip' => 'info:'.$user_daytime));
		$form->addHidden('uid', $row->uid);
		$form->addHidden('task', '');
		$form->closeTab();

		$form->openTab($eLang->get('ACTIVITY'));
		$form->addInfo($eLang->get('ARTICLES'), $info->articles);
		$form->addInfo($eLang->get('COMMENTS'), $info->comments);
		$form->addInfo($eLang->get('PROFILE_VIEWS'), $row->profile_views);
		$form->addInfo($eLang->get('TIMES_ONLINE'), $row->times_online);
		$form->closeTab();

		if ($row->uid > 0) {
			echo '<h1>'.$eLang->get('EDITPROFILE').' '.$row->uname."</h1>\n";
		} else {
			echo '<h1>'.$eLang->get('NEW_USER')."</h1>\n";
		}

		$form->render();
		unset($form);
	}

}

?>