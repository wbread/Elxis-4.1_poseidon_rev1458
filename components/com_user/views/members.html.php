<?php 
/**
* @version		$Id: members.html.php 1420 2013-04-29 18:18:53Z datahell $
* @package		Elxis
* @subpackage	User component
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class membersUserView extends userView {

	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*********************/
	/* SHOW MEMBERS LIST */
	/*********************/
	public function membersList($rows, $columns, $total, $order, $page=1, $maxpage=1, $nav_links=2) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$c = count($columns) + 1;
		$baseLink = $elxis->makeURL('user:members/');

		echo '<h1>'.$eLang->get('MEMBERSLIST')."</h1>\n";
		echo '<p style="margin-bottom:20px;">'.sprintf($eLang->get('REGMEMBERSTOTAL'), '<strong>'.$total.'</strong>').' ';
		printf($eLang->get('PAGEOF'), '<strong>'.$page.'</strong>', '<strong>'.$maxpage.'</strong>');
		echo "</p>\n";

		if ($maxpage > 1) {
			$linkbase = $elxis->makeURL('user:members/?order='.$order);
			$navigation = $elxis->obj('Navigation')->navLinks($linkbase, $page, $maxpage);
			if (($nav_links == 0) || ($nav_links == 2)) { echo $navigation; }
		}

		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		if ($page > 1) {
			echo '<tr><th colspan="'.$c.'">'.$eLang->get('MEMBERSLIST').' - '.$eLang->get('PAGE').' '.$page.'</th></tr>'."\n";
		} else {
			echo '<tr><th colspan="'.$c.'">'.$eLang->get('MEMBERSLIST').'</th></tr>'."\n";
		}
		echo "<tr>\n";
		echo "\t".'<th class="elx_th_sub" width="20"></th>'."\n";
		foreach ($columns as $column) {
			$active = false;
			switch ($column) {
				case 'firstname':
					$txt = $eLang->get('FIRSTNAME');
					if (($order == 'fa') || ($order == 'fd')) { $active = true; }
				break;
				case 'lastname':
					$txt = $eLang->get('LASTNAME');
					if (($order == 'la') || ($order == 'ld')) { $active = true; }
				break;
				case 'uname':
					$txt = $eLang->get('USERNAME');
					if (($order == 'ua') || ($order == 'ud')) { $active = true; }
				break;
				case 'groupname':
					$txt = $eLang->get('GROUP');
					if (($order == 'ga') || ($order == 'gd')) { $active = true; }
				break;
				case 'preflang':
					$txt = $eLang->get('LANGUAGE');
					if (($order == 'pa') || ($order == 'pd')) { $active = true; }
				break;
				case 'country':
					$txt = $eLang->get('COUNTRY');
					if (($order == 'ca') || ($order == 'cd')) { $active = true; }
				break;
				case 'website':
					$txt = $eLang->get('WEBSITE');
					if (($order == 'wa') || ($order == 'wd')) { $active = true; }
				break;
				case 'gender':
					$txt = $eLang->get('GENDER');
					if (($order == 'gea') || ($order == 'ged')) { $active = true; }
				break;
				case 'registerdate':
					$txt = $eLang->get('REGDATE_SHORT');
					if (($order == 'ra') || ($order == 'rd')) { $active = true; }
				break;
				case 'lastvisitdate':
					$txt = $eLang->get('LASTVISIT');
					if (($order == 'lva') || ($order == 'lvd')) { $active = true; }
				break;
				case 'profile_views':
					$txt = $eLang->get('PROFILE_VIEWS');
					if (($order == 'pva') || ($order == 'pvd')) { $active = true; }
				break;
				default: $txt = $column; break;
			}

			$hlink = $baseLink.'?'.$this->base_sortLink($column, $order, $page);
			$thclass = ($active === true) ? 'elx_th_subcur' : 'elx_th_sub';
			echo "\t".'<th class="'.$thclass.'"><a href="'.$hlink.'" title="'.$txt.' - '.$eLang->get('SWITCHORDER').'">'.$txt.'</a></th>'."\n";
		}
		echo "</tr>\n";

		include(ELXIS_PATH.'/includes/libraries/elxis/language/langdb.php');
		$time = $eDate->getTS() - $elxis->getConfig('SESSION_LIFETIME');
		$proflink_in_name = (in_array('uname', $columns)) ? false : true;
		$acl_profile = $elxis->acl()->check('com_user', 'profile', 'view');

		$k = 0;
		for ($i=0; $i<count($rows); $i++) {
			$row = $rows[$i];
			echo '<tr class="elx_tr'.$k.'">'."\n";
			if (((int)$row->last_activity > 1000) && ($row->last_activity > $time)) {
				echo '<td><img src="'.$elxis->secureBase().'/components/com_user/images/online12.png" alt="online" title="'.$eLang->get('ONLINE').'" border="0" /></td>'."\n";
			} else {
				echo '<td><img src="'.$elxis->secureBase().'/components/com_user/images/offline12.png" alt="offline" title="'.$eLang->get('OFFLINE').'" border="0" /></td>'."\n";
			}

			foreach ($columns as $column) {
				switch ($column) {
					case 'firstname':
					case 'lastname':
						$txt = $row->$column;
						if ($proflink_in_name) {
							if ($acl_profile == 2) {
								$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
								$txt = '<a href="'.$link.'" title="'.$eLang->get('USERPROFILE').'">'.$row->$column.'</a>';
							} elseif (($acl_profile == 1) && ($elxis->user()->uid == $row->uid)) {
								$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
								$txt = '<a href="'.$link.'" title="'.$eLang->get('USERPROFILE').'">'.$row->$column.'</a>';
							}
						}
					break;
					case 'uname': 
						$txt = $row->uname;
						if ($acl_profile == 2) {
							$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
							$txt = '<a href="'.$link.'" title="'.$eLang->get('USERPROFILE').'">'.$row->uname.'</a>';
						} elseif (($acl_profile == 1) && ($elxis->user()->uid == $row->uid)) {
							$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
							$txt = '<a href="'.$link.'" title="'.$eLang->get('USERPROFILE').'">'.$row->uname.'</a>';
						}
					break;
					case 'groupname': $txt = $this->base_translateGroup($row->groupname, $row->gid); break;
					case 'preflang':
						$txt = '';
						if (trim($row->preflang) != '') {
							$ttl = isset($langdb[ $row->preflang ]) ? $langdb[ $row->preflang ]['NAME'] : $row->preflang;
							if (file_exists(ELXIS_PATH.'/includes/libraries/elxis/language/flags/'.$row->preflang.'.png')) {
								$txt = '<img src="'.$elxis->secureBase().'/includes/libraries/elxis/language/flags/'.$row->preflang.'.png" alt="'.$row->preflang.'" title="'.$ttl.'" border="0" />';
							} else {
								$txt .= $ttl;
							}
						}
					break;
					case 'country': $txt = $row->country; break;
					case 'website': 
						$txt = trim($row->website);
						if (($txt != '') && filter_var($txt, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
							$parsed = parse_url($row->website);
							$txt = '<a href="'.$txt.'" target="_blank" title="'.$parsed['host'].'">';
							$txt .= '<img src="'.$elxis->secureBase().'/components/com_user/images/globe16.png" alt="'.$eLang->get('WEBSITE').'" border="0" />';
							$txt .= '</a>';
							unset($parsed);
						}
					break;
					case 'gender':
						$txt = '';
						if (trim($row->gender) != '') {
							$txt = ($row->gender == 'female') ? $eLang->get('FEMALE') : $eLang->get('MALE');
						}
					break;
					case 'registerdate':
					case 'lastvisitdate':
						if ((trim($row->$column) != '') && ($row->$column != '1970-01-01 00:00:00')) {
							$txt = $eDate->formatDate($row->$column, $eLang->get('DATE_FORMAT_4'));
						} else {
							$txt = $eLang->get('NEVER');
						}
					break;
					case 'profile_views':
						$txt = (int)$row->$column;
					break;
					default: $txt = $row->$column; break;
				}
				echo "\t".'<td>'.$txt.'</td>'."\n";
			}
			echo "</tr>\n";
			$k = 1 - $k;
		}

		echo "</table>\n";
		echo "</div>\n";

		if ($maxpage > 1) {
			if (($nav_links == 1) || ($nav_links == 2)) { echo $navigation; }
			unset($navigation);
		}

		echo '<div class="elx_info">'.$eLang->get('CLICKCOLSORT')."</div>\n";

		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
		$link = $elxis->makeURL('user:/');
		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/*********************/
	/* SHOW USER PROFILE */
	/*********************/
	public function userProfile($row, $params, $userparams, $usname, $twitter, $comments) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();

		$avsize = (int)$params->get('profile_avatar_width', 80);
		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';

		echo '<h1>'.$usname."</h1>\n";
		echo '<p>'.sprintf($eLang->get('PROFILEUSERAT'), '<strong>'.$usname.'</strong>', $elxis->getConfig('SITENAME'))."\n";
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $row->uid))) {
			$link = $elxis->makeURL('user:members/edit.html?id='.$row->uid, '', true);
			echo '<br /><a href="'.$link.'" title="'.$eLang->get('EDITPROFILE').' '.$row->uname.'">'.$eLang->get('EDITPROFILE')."</a>\n";
		}
		echo "</p>\n";
?>
		<div style="margin:15px 0;">
			<div class="elx_profile_summary">
<?php 
		if (ELXIS_MOBILE == 0) {
?>
				<div class="elx_avatar_box" style="width:<?php echo ($avsize + 10); ?>px; float:<?php echo $float; ?>;">
					<img src="<?php echo $row->avatar; ?>" alt="<?php echo $row->uname; ?>" width="<?php echo $avsize; ?>" />
				</div>				
				<div style="padding-<?php echo $float; ?>: <?php echo $avsize + 12; ?>px;">
				<h3 class="elx_user_title"><?php echo $row->firstname.' '.$row->lastname.' <span dir="ltr">('.$row->uname.')</span>'; ?></h3>
				<?php echo $this->base_translateGroup($row->groupname, $row->gid); ?><br />
				<?php 
				$img = 'offline12.png';
				$txt = $eLang->get('OFFLINE');
				if ($row->is_online == 1) {
					$img = 'online12.png';
					$txt = $eLang->get('ONLINE');
				}
				echo '<img src="'.$elxis->secureBase().'/components/com_user/images/'.$img.'" alt="'.$txt.'" title="'.$txt.'" border="0" align="bottom" /> ';
				echo $txt."<br />\n";
				echo $eLang->get('MEMBERSINCE').' ';
				echo $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_5'));
				echo '<br />';
				echo $eLang->get('LASTVISIT').' ';
				if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
					echo $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_5'));
				} else {
					echo $eLang->get('NEVER');
				}	
				?>
				</div>
<?php 
		} else {
				echo '<div class="elx_avatar_box" style="width:60px; float:'.$float.';">'."\n";
				echo '<img src="'.$row->avatar.'" alt="'.$row->uname.'" />'."\n";
				echo "</div>\n";
				echo '<div style="padding-'.$float.':62px;">'."\n";
				echo '<h3 class="elx_user_title">'.$row->firstname.' '.$row->lastname.' <span dir="ltr">('.$row->uname.')</span></h3>';
				echo $this->base_translateGroup($row->groupname, $row->gid);
				echo "</div>\n";
		}
?>
				<div class="clear"></div>
			</div>

			<div class="elx_profile_details">
			<h3><?php echo $eLang->get('ACCOUNT_DETAILS'); ?></h3>
			<ul class="elx_ulist">
<?php 
			if (trim($row->gender) != '') {
				$allowed = $elxis->acl()->check('com_user', 'profile', 'viewgender');
				if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
					$txt = ($row->gender == 'female') ? $eLang->get('FEMALE') : $eLang->get('MALE');
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('GENDER')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$txt."</div>\n</li>\n";
				}
			}

			if (trim($row->birthdate) != '') {
				$allowed = $elxis->acl()->check('com_user', 'profile', 'viewage');
				if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
					$row->birthdate .= (strlen($row->birthdate) == 10) ? ' 12:00:00' : '';
					$parts = preg_split("/[\s-]+/", $row->birthdate, -1, PREG_SPLIT_NO_EMPTY);
					if ($parts && (count($parts) == 4)) {
						if (checkdate($parts[1], $parts[2], $parts[0]) === true) {
							$age = date('Y') - $parts[0];
							if (($age > 5) && ($age < 120)) {
								echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('AGE')."</div>\n";
								echo "\t".'<div class="elx_column2">'."\n";
								echo "\t\t".$age.' <span class="elx_user_small" dir="ltr">(';
								echo $eDate->formatDate($row->birthdate, $eLang->get('DATE_FORMAT_2'));
								echo ")</span>\n";
								echo "\t</div>\n</li>\n";
							}
						}
					}
				}
			}

			if (trim($row->occupation) != '') {
				echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('OCCUPATION')."</div>\n";
				echo "\t".'<div class="elx_column2">'.$row->occupation."</div>\n</li>\n";
			}

			if (trim($row->preflang) != '') {
				$lnginfo = $eLang->getallinfo($row->preflang);
				if (isset($lnginfo['NAME'])) {
?>
				<li>
					<div class="elx_column1"><?php echo $eLang->get('LANGUAGE'); ?></div>
					<div class="elx_column2">
						<?php echo $lnginfo['NAME']; ?> 
						<span class="elx_user_small" dir="ltr">
							<?php echo $lnginfo['NAME_ENG'].' ('.$lnginfo['LANGUAGE'].'-'.$lnginfo['REGION']; ?>)
						</span>
					</div>
				</li>
<?php 			}
				unset($lnginfo);
			}


			if (trim($row->timezone) != '') {
?>
				<li>
					<div class="elx_column1"><?php echo $eLang->get('TIMEZONE'); ?></div>
					<div class="elx_column2">
						<?php echo $row->timezone; ?><br />
						<span class="elx_user_small"><?php echo $eLang->get('LOCALTIME').' '.$eDate->worldDate('now', $row->timezone, $eLang->get('DATE_FORMAT_4')); ?></span>
					</div>
				</li>
<?php 
			}

			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewaddress');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				$address = array();
				if (trim($row->address) != '') { $address[] = $row->address; }
				if (trim($row->postalcode) != '') { $address[] = $row->postalcode; }
				if (trim($row->city) != '') { $address[] = $row->city; }
				if (trim($row->country) != '') { $address[] = $row->country; }
				if (count($address) > 0) {
?>
				<li>
					<div class="elx_column1"><?php echo $eLang->get('ADDRESS'); ?></div>
					<div class="elx_column2"><?php echo implode(', ', $address); ?></div>
				</li>
<?php 
				}
			}

			$allowed = $elxis->acl()->check('com_user', 'profile', 'viewemail');
			if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
				$eparts = str_split($row->email);
				$encmail = '';
				for ($i = 0; $i < count($eparts); $i++) { $encmail .= '&#'.ord($eparts[$i]).';'; }
?>
				<li>
					<div class="elx_column1"><?php echo $eLang->get('EMAIL'); ?></div>
					<div class="elx_column2"><?php echo $encmail; ?></div>
				</li>
<?php 
				unset($eparts, $encmail);
			}

			if (trim($row->phone) != '') {
				$allowed = $elxis->acl()->check('com_user', 'profile', 'viewphone');
				if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('TELEPHONE')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$row->phone."</div>\n</li>\n";
				}
			}

			if (trim($row->mobile) != '') {
				$allowed = $elxis->acl()->check('com_user', 'profile', 'viewmobile');
				if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('MOBILE')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$row->mobile."</div>\n</li>\n";
				}
			}

			if (trim($row->website) != '') {
				$allowed = $elxis->acl()->check('com_user', 'profile', 'viewwebsite');
				if (($allowed == 2) || (($allowed == 1) && ($row->uid = $elxis->user()->uid))) {
					$txt = (strlen($row->website) > 30) ? substr($row->website, 0, 27).'...' : $row->website;
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('WEBSITE')."</div>\n";
					echo "\t".'<div class="elx_column2"><a href="'.$row->website.'" target="_blank">'.$txt."</a></div>\n</li>\n";
					unset($txt);
				}
			}

			echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('PROFILE_VIEWS')."</div>\n";
			echo "\t".'<div class="elx_column2">'.$row->profile_views."</div>\n</li>\n";
			echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('TIMES_ONLINE')."</div>\n";
			echo "\t".'<div class="elx_column2">'.$row->times_online."</div>\n</li>\n";

			if ($elxis->acl()->getLevel() >= 70) {
				if ((trim($row->expiredate) != '') && ($row->expiredate != '2060-01-01 00:00:00')) {
					$txt = $eDate->formatDate($row->expiredate, $eLang->get('DATE_FORMAT_4'));
				} else {
					$txt = $eLang->get('NEVER');
				}
				echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('EXPIRATION_DATE')."</div>\n";
				echo "\t".'<div class="elx_column2">'.$txt."</div>\n</li>\n";
				if ($row->is_online) {
					$mins = floor($row->time_online / 60);
					$secs = $row->time_online - ($mins * 60);
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('IP_ADDRESS')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$row->ip_address."</div>\n</li>\n";
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('BROWSER')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$row->browser."</div>\n</li>\n";
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('TIME_ONLINE')."</div>\n";
					echo "\t".'<div class="elx_column2">';
					if ($mins > 0) { echo $mins.' min, '; }
					echo $secs.' sec';
					echo"</div>\n</li>\n";
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('CLICKS')."</div>\n";
					echo "\t".'<div class="elx_column2">'.$row->clicks."</div>\n</li>\n";
					echo "<li>\n\t".'<div class="elx_column1">'.$eLang->get('CURRENT_PAGE')."</div>\n";
					echo "\t".'<div class="elx_column2">';
					echo '<a href="'.$elxis->getConfig('URL').'/'.$row->current_page.'">'.$row->current_page."</a></div>\n</li>\n";
				}
			}
?>
			</ul>
			</div>
		</div>

<?php 
		if ($comments) {
			echo '<div class="elx_profile_twitter">'."\n";
			echo '<h3>'.$eLang->get('COMMENTS')."</h3>\n";
			echo '<ul class="elx_ulist">'."\n";
			foreach ($comments as $comment) {
				$link = $elxis->makeURL('content:'.$comment->link);
				echo '<li>'."\n";
				echo '<div style="margin:0; width:60px; float:'.$float.'; text-align: center;">'."\n";
				echo '<img src="'.$row->avatar.'" alt="'.$row->uname.'" border="0" width="48" style="padding:2px; border:1px solid #ddd;" />'."\n";
				echo "</div>\n";
				echo '<div style="margin:0; padding:0;">'."\n";
				echo nl2br(strip_tags($comment->message));
				echo "<br />\n";
				echo '<span class="elx_user_small">'."\n";
				echo $eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'));
				echo ' | <a href="'.$link.'">'.$comment->title."</a></span>\n";
				echo "</div>\n";
				echo '<div style="clear:both;"></div>'."\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
			echo "</div>\n";
		}

		if ($twitter && isset($twitter->screen_name)) {
?>
			<div class="elx_profile_twitter">
				<h3><?php printf($eLang->get('ONTWITTER'), $row->firstname.' '.$row->lastname); ?></h3>
				<div class="elx_profile_twitter_user">
					<div style="margin: 0; width: 60px; float: <?php echo $float; ?>; text-align: center;">
					<a href="http://twitter.com/<?php echo $twitter->screen_name; ?>" title="<?php echo $twitter->name; ?> - Twitter" target="_blank">
						<img src="<?php echo $twitter->profile_image_url; ?>" alt="<?php echo $twitter->screen_name; ?>" border="0" width="48" style="padding: 2px; border: 1px solid #ddd;" />
					</a>
					</div>
					<div class="elx_profile_twitter_summary">
						<a href="http://twitter.com/<?php echo $twitter->screen_name; ?>" title="<?php echo $twitter->name; ?> - Twitter" target="_blank" style="font-weight:bold;">
							<?php echo $twitter->name; ?>
						</a><br />
						<?php echo $twitter->description; ?><br />
						<span class="elx_user_small"><?php 
						echo $eLang->get('FOLLOWERS').' <strong>'.$twitter->followers_count.'</strong>, ';
						echo $eLang->get('FRIENDS').' <strong>'.$twitter->friends_count.'</strong>, ';
						echo $eLang->get('FAVORITES').' <strong>'.$twitter->favourites_count.'</strong>, ';
						echo $eLang->get('STATUSES').' <strong>'.$twitter->statuses_count.'</strong></span>';
						?>
					</div>
					<div style="clear: both;"></div>
				</div>
<?php 
			if (is_array($twitter->tweets) && (count($twitter->tweets) > 0)) {
				echo "\n\t\t".'<ul class="elx_ulist">'."\n";
				foreach ($twitter->tweets as $tweet) {
?>
				<li>
					<div style="margin: 0; width: 60px; float: <?php echo $float; ?>; text-align: center;">
					<a href="<?php echo $tweet->author_uri; ?>" title="<?php echo $tweet->author; ?> - Twitter" target="_blank">
						<img src="<?php echo $tweet->avatar; ?>" alt="<?php echo $tweet->author; ?>" border="0" width="48" style="padding: 2px; border: 1px solid #ddd;" />
					</a>
					</div>
					<div style="margin: 0; padding: 0;">
						<?php echo $tweet->content; ?><br />
						<span class="elx_user_small">
							<?php echo $tweet->published; ?> <?php echo $eLang->get('BY'); ?> 
							<a href="<?php echo $tweet->author_uri; ?>" title="<?php echo $tweet->author; ?> - Twitter" target="_blank">
								<?php echo $tweet->author; ?>
							</a>
<?php 
							if ($tweet->permalink != '') {
								echo ' | <a href="'.$tweet->permalink.'" title="'.$eLang->get('PERMALINK').'" target="_blank">'.$eLang->get('PERMALINK').'</a>';
							}
?>
						</span>
					</div>
					<div style="clear:both;"></div>
				</li>
<?php 
				}
				echo "\t\t</ul>\n";
			} else if (isset($twitter->status_text) && ($twitter->status_text != '')) {
				echo "\n\t\t".'<ul class="elx_ulist">'."\n";
				echo '<li>'.$twitter->status_text.'<br />';
				echo '<span class="elx_user_small">'.$twitter->status_created_at.'</span></li>';
				echo "\t\t</ul>\n"; 
			}
?>
			</div>
<?php 
		}

		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		$allowed = $elxis->acl()->check('com_user', 'profile', 'edit');
		if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $row->uid))) {
			$link = $elxis->makeURL('user:members/edit.html?id='.$row->uid, '', true);
			echo '<a href="'.$link.'" title="'.$eLang->get('EDITPROFILE').' '.$row->uname.'">'.$eLang->get('EDITPROFILE')."</a> \n";
		}

		if ($row->gid != 1) {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'block');
			if (($allowed == 1) && ($elxis->user()->uid != $row->uid)) {
				$link = $elxis->makeURL('user:members/block.html?id='.$row->uid, '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('BLOCKUSER').' '.$row->uname.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');">'.$eLang->get('BLOCKUSER')."</a> \n";
			}

			$allowed = $elxis->acl()->check('com_user', 'profile', 'delete');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $row->uid))) {
				$link = $elxis->makeURL('user:members/delete.html?id='.$row->uid, '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('DELETEACCOUNT').' '.$row->uname.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');">'.$eLang->get('DELETEACCOUNT')."</a> \n";
			}
		}

		if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
			$link = $elxis->makeURL('user:members/');
			echo '<a href="'.$link.'" title="'.$eLang->get('MEMBERSLIST').'">'.$eLang->get('MEMBERSLIST')."</a> \n";
		}
		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/**************************/
	/* HTML EDIT USER PROFILE */
	/**************************/
	public function editProfile($row, $userparams, $errormsg='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eDate = eFactory::getDate();
		$action = $elxis->makeURL('user:members/save.html', '', true, false);
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmeditprof',
			'action' => $action,
			'idprefix' => 'epr',
			'label_width' => 200,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('ACCOUNT_DETAILS'));
		$form->addText('firstname', $row->firstname, $eLang->get('FIRSTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
		$form->addText('lastname', $row->lastname, $eLang->get('LASTNAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));

		if ($elxis->getConfig('SECURITY_LEVEL') < 2) {
			if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 2) {
				$email_desc = ($elxis->user()->gid == 1) ? '' : $eLang->get('CHMAILADMACT');
			} else if ($elxis->getConfig('REGISTRATION_ACTIVATION') == 1) {
				$email_desc = ($elxis->user()->gid == 1) ? '' : $eLang->get('CHMAILREACT');
			} else {
				$email_desc = '';
			}
			$form->addEmail('email', $row->email, $eLang->get('EMAIL'), array('required' => 1, 'size' => 30, 'tip' => $email_desc));
		}

		if ($elxis->acl()->check('com_user', 'profile', 'uploadavatar') == 1) {
			$form->addFile('avatar', $eLang->get('AVATAR'), array('tip' => $eLang->get('AVATAR_D')));
		}

		$form->addPassword('pword', '', $eLang->get('PASSWORD'), 
			array(
				'required' => 0, 
				'maxlength' => 60,
				'tip' => $eLang->get('ONLY_IF_CHANGE'),
				'password_meter' => 1,
				'onkeyup' => 'elxPasswordMeter(\'fmeditprof\', \'eprpword\', \'epruname\');'
			)
		);
		$form->addPassword('pword2', '', $eLang->get('PASSWORD_AGAIN'), array('required' => 0, 'maxlength' => 60, 'match' => 'eprpword'));

		$options = array();
		$options[] = $form->makeOption('male', $eLang->get('MALE'));
		$options[] = $form->makeOption('female', $eLang->get('FEMALE'));
		$form->addRadio('gender', $eLang->get('GENDER'), $row->gender, $options, array('vertical_options' => 0, 'dir' => 'rtl'));

		$val = (trim($row->birthdate) != '') ? substr($row->birthdate, 0, 10) : '';
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

		$form->closeFieldset();

		$form->openFieldset($eLang->get('PREFERENCES'));
		$val = (trim($row->preflang) == '') ? $eLang->getinfo('LANGUAGE') : $row->preflang;
		$form->addLanguage('preflang', $eLang->get('LANGUAGE'), $val, array('tip' => $eLang->get('SETPREFLANG')));

		$tz = ($elxis->user()->uid == $row->uid) ? $eDate->getTimezone() : $row->timezone;
		if (trim($tz) == '') { $tz = $eDate->getTimezone(); }
		$user_daytime = $eDate->worldDate('now', $tz, $eLang->get('DATE_FORMAT_12'));
		$form->addTimezone('timezone', $eLang->get('TIMEZONE'), $tz, array('tip' => $user_daytime));
		$form->closeFieldset();

		$form->openFieldset();
		$form->addCaptcha('seccode');
		$form->addHidden('uid', $row->uid);
		$form->addHidden('uname', $row->uname);
		$form->addButton('sbmepr', $eLang->get('SUBMIT'), 'submit', array('class' => 'elxbutton-save', 'tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();

		echo '<h1>'.$eLang->get('EDITPROFILE').' '.$row->uname."</h1>\n";
		if ($errormsg != '') {
			echo '<div class="elx_error" style="margin-bottom: 20px;">'.$errormsg."</div>\n";
		}

		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';
?>
		<div style="margin:15px 0;">
			<div class="elx_profile_summary">
<?php 
		if (ELXIS_MOBILE == 0) {
?>
				<div class="elx_avatar_box" style="width: 58px; float: <?php echo $float; ?>;">
					<img src="<?php echo $row->avatar; ?>" alt="<?php echo $row->uname; ?>" width="48" />
				</div>				
				<div style="padding-<?php echo $float; ?>: 60px;">
				<h3 class="elx_user_title"><?php echo $row->firstname.' '.$row->lastname.' <span dir="ltr">('.$row->uname.')</span>'; ?></h3>
				<?php echo $this->base_translateGroup($row->groupname, $row->gid); ?><br />
				<?php 
				echo $eLang->get('MEMBERSINCE').' ';
				echo $eDate->formatDate($row->registerdate, $eLang->get('DATE_FORMAT_5'));
				echo '<br />';
				echo $eLang->get('LASTVISIT').' ';
				if ((trim($row->lastvisitdate) != '') && ($row->lastvisitdate != '1970-01-01 00:00:00')) {
					echo $eDate->formatDate($row->lastvisitdate, $eLang->get('DATE_FORMAT_5'));
				} else {
					echo $eLang->get('NEVER');
				}	
				?>
				</div>
<?php 
		} else {
				echo '<div class="elx_avatar_box" style="width:60px; float:'.$float.';">'."\n";
				echo '<img src="'.$row->avatar.'" alt="'.$row->uname.'" />'."\n";
				echo "</div>\n";
				echo '<div style="padding-'.$float.':62px;">'."\n";
				echo '<h3 class="elx_user_title">'.$row->firstname.' '.$row->lastname.' <span dir="ltr">('.$row->uname.')</span></h3>';
				echo $this->base_translateGroup($row->groupname, $row->gid);
				echo "</div>\n";
		}
?>
				<div class="clear"></div>
			</div>
		</div>
<?php 

		$form->render();
		unset($form);

		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		if ($row->gid != 1) {
			$allowed = $elxis->acl()->check('com_user', 'profile', 'block');
			if (($allowed == 1) && ($elxis->user()->uid != $row->uid)) {
				$link = $elxis->makeURL('user:members/block.html?id='.$row->uid, '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('BLOCKUSER').' '.$row->uname.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');">'.$eLang->get('BLOCKUSER')."</a> \n";
			}

			$allowed = $elxis->acl()->check('com_user', 'profile', 'delete');
			if (($allowed == 2) || (($allowed == 1) && ($elxis->user()->uid == $row->uid))) {
				$link = $elxis->makeURL('user:members/delete.html?id='.$row->uid, '', true);
				echo '<a href="'.$link.'" title="'.$eLang->get('DELETEACCOUNT').' '.$row->uname.'" onclick="return confirm(\''.addslashes($eLang->get('AREYOUSURE')).'\');">'.$eLang->get('DELETEACCOUNT')."</a> \n";
			}
		}

		if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
			$link = $elxis->makeURL('user:members/');
			echo '<a href="'.$link.'" title="'.$eLang->get('MEMBERSLIST').'">'.$eLang->get('MEMBERSLIST')."</a> \n";
		}
		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}


	/****************************************/
	/* DISPLAY PROFILE SAVE SUCCESS MESSAGE */
	/****************************************/
	public function profileSuccess($row, $msg) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$navtag = (in_array($elxis->getConfig('DOCTYPE'), array('xhtml5', 'html5'))) ? 'nav' : 'div';
		echo '<h1>'.$eLang->get('EDITPROFILE')."</h1>\n";
		echo '<div class="elx_success">'.$msg."</div>\n";
		echo '<'.$navtag.' class="elx_user_bottom_links">'."\n";
		if ($row->block == 0) {
			$link = $elxis->makeURL('user:members/'.$row->uid.'.html');
			echo '<a href="'.$link.'" title="'.$eLang->get('PROFILE').'">'.$eLang->get('PROFILE').' '.$row->uname."</a> \n";
		}
		if ($elxis->acl()->check('com_user', 'memberslist', 'view') > 0) {
			$link = $elxis->makeURL('user:members/');
			echo '<a href="'.$link.'" title="'.$eLang->get('MEMBERSLIST').'">'.$eLang->get('MEMBERSLIST')."</a> \n";
		}
		$link = $elxis->makeURL('user:/');
		echo '<a href="'.$link.'" title="'.$eLang->get('USERSCENTRAL').'">'.$eLang->get('USERSCENTRAL')."</a>\n";
		echo '</'.$navtag.">\n";
	}

}

?>