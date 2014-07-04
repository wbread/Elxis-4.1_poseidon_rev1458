<?php 
/**
* @version		$Id: generic.html.php 1426 2013-05-02 19:09:47Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class genericContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/***************************************/
	/* DISPLAY LIST OF AVAILABLE XML FEEDS */
	/***************************************/
	public function feedsCentral($rows) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$this->wrapperStart('feeds', 0);
		echo '<h1>'.$eLang->get('RSS_ATOM_FEEDS_CENTRAL')."</h1>\n";
		echo '<p>'.sprintf($eLang->get('LIST_OF_FEEDS'), '<strong>'.$elxis->getConfig('SITENAME').'</strong>')."</p>\n";

		$rss_icon = $elxis->icon('rss', 24);
		$rss_no_icon = $elxis->icon('rss_no', 24);
		$atom_icon = $elxis->icon('atom', 24);
		$atom_no_icon = $elxis->icon('atom_no', 24);

		$link1 = $elxis->makeURL('content:rss.xml');
		$link2 = $elxis->makeURL('content:atom.xml');
		echo '<table width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_feeds_tbl">'."\n";
		echo "<tr>\n";
		echo '<td colspan="2"><h3>'.$eLang->get('SITE_FEED').'</h3>'."</td>\n";
		echo '<td width="100">&#160;'."</td>\n";
		echo '<td width="40"><a href="'.$link1.'" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
		echo '<td width="40"><a href="'.$link2.'" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
		echo "</tr>\n";
		echo "</table>\n";
		unset($link1, $link2);

		echo '<p>'.$eLang->get('FEEDS_CONTAIN_ARTS')."</p>\n";

		if (!$rows) {
			echo '<div class="elx_warning">'.$eLang->get('NO_FEEDS_AV')."</div>\n";
			$this->wrapperEnd('feeds');
			return;
		}

		echo '<table width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_feeds_tbl">'."\n";
		foreach ($rows as $row) {
			$link = $elxis->makeURL($row->seotitle.'/');
			echo "<tr>\n";
			echo '<td colspan="2"><h3>'.$row->title.'</h3>'."</td>\n";
			echo '<td width="100">';
			if ($row->articles > -1) {
				echo '<span>'.$row->articles.' ';
				echo ($row->articles == 1) ? $eLang->get('ARTICLE') : $eLang->get('ARTICLES');
				echo '</span>';			
			} else {
				echo '&#160;';
			}
			echo "</td>\n";
			if ($row->articles == 0) {
				echo '<td width="40"><img src="'.$rss_no_icon.'" alt="RSS" title="RSS" /></td>'."\n";
				echo '<td width="40"><img src="'.$atom_no_icon.'" alt="ATOM" title="ATOM" /></td>'."\n";
			}  else {
				echo '<td width="40"><a href="'.$link.'rss.xml" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
				echo '<td width="40"><a href="'.$link.'atom.xml" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
			}
			echo "</tr>\n";
			if (count($row->categories) > 0) {
				foreach ($row->categories as $sub) {
					$link = $elxis->makeURL($row->seotitle.'/'.$sub->seotitle.'/');
					echo "<tr>\n";
					echo '<td width="40">&#160;</td>'."\n";
					echo '<td>'.$sub->title."</td>\n";
					echo '<td width="100">';
					if ($sub->articles > -1) {
						echo '<span>'.$sub->articles.' ';
						echo ($sub->articles == 1) ? $eLang->get('ARTICLE') : $eLang->get('ARTICLES');
						echo '</span>';
					} else {
						echo '&#160;';
					}
					echo "</td>\n";
					if ($sub->articles == 0) {
						echo '<td width="40"><img src="'.$rss_no_icon.'" alt="RSS" title="RSS" /></td>'."\n";
						echo '<td width="40"><img src="'.$atom_no_icon.'" alt="ATOM" title="ATOM" /></td>'."\n";
					}  else {
						echo '<td width="40"><a href="'.$link.'rss.xml" title="RSS" target="_blank"><img src="'.$rss_icon.'" alt="RSS" /></a></td>'."\n";
						echo '<td width="40"><a href="'.$link.'atom.xml" title="ATOM" target="_blank"><img src="'.$atom_icon.'" alt="ATOM" /></a></td>'."\n";
					}
					echo "</tr>\n";
				}
			}
		}
		echo "</table>\n";
		$this->wrapperEnd('feeds');
	}


	/*****************************/
	/* SHOW TAGGED ARTICLES LIST */
	/*****************************/
	public function showTagArticles($rows, $tag, $params) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();
		$eDate = eFactory::getDate();

		$c = (!$rows) ? 0 : count($rows);
		$this->wrapperStart('tags', 0);
		echo '<h2>'.sprintf($eLang->get('ARTICLES_TAGGED'), $tag)."</h2>\n";
		echo '<p>'.sprintf($eLang->get('ARTS_FOUND_TAG'), '<strong>'.$c.'</strong>')."</p>\n";
		if (!$rows) {
			echo '<div class="elx_back">'."\n";
			echo '<a href="javascript:void(null);" onclick="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a>\n";
			echo "</div>\n";
			$this->wrapperEnd('tags');
			return;
		}

		$img_thumb_width = (int)$params->get('img_thumb_width', 100);
		$img_style = ' width="'.$img_thumb_width.'" style="width:'.$img_thumb_width.'px;"';
		$w = $img_thumb_width + 10;
		if ($eLang->getinfo('DIR') == 'rtl') {
			$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:'.$w.'px;"';
		} else {
			$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:'.$w.'px;"';
		}

		foreach ($rows as $row) {
			$link = $elxis->makeURL($row->link);
			$imgbox = '';
			$imgurl = '';
			if ((trim($row->image) != '') && file_exists(ELXIS_PATH.'/'.$row->image)) {
				$imgfile = $elxis->secureBase().'/'.$row->image;
				$file_info = $eFiles->getNameExtension($row->image);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					$imgurl = $imgfile;
				}
				unset($file_info);

				$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
				$imgbox .= '<a href="'.$link.'" title="'.$row->title.'">';
				$imgbox .= '<img src="'.$imgfile.'" alt="'.$row->title.'"'.$img_style.' />'; 
				$imgbox .= "</a>\n";
				$imgbox .= "</div>\n";	
			}

			if (ELXIS_MOBILE == 0) {
				echo '<div class="elx_short_box">'."\n";
				echo $imgbox;
				echo '<h3><a href="'.$link.'" title="'.$row->title.'">'.$row->title.'</a></h3>'."\n";
				echo '<div class="elx_dateauthor">';
				echo $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_4'));
				if ($row->catid > 0) {
					$link = $elxis->makeURL($row->catlink);
					echo ' '.$eLang->get('IN').' <a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a>';
				}
				echo '</div>'."\n";
				if (trim($row->subtitle) != '') {
					echo '<p class="elx_content_short">'.$row->subtitle."</p>\n";
				}
				echo '<div class="clear"></div>'."\n";
				echo "</div>\n";
			} else {
				echo '<article class="elx_short_box">'."\n";
				if ($imgurl != '') {
					echo '<figure><a href="'.$link.'" title="'.$row->title.'"><img src="'.$imgurl.'" alt="'.$row->title.'" /></a></figure>'."\n";
				}
				echo '<div class="elx_short_textbox">'."\n";
				echo '<h3><a href="'.$link.'" title="'.$row->title.'">'.$row->title.'</a></h3>'."\n";
				if (trim($row->subtitle) != '') {
					echo '<p class="elx_content_short">'.$row->subtitle."</p>\n";
				}
				echo '<div class="elx_dateauthor">';
				echo $eDate->formatDate($row->created, $eLang->get('DATE_FORMAT_3'));
				if ($row->catid > 0) {
					$link = $elxis->makeURL($row->catlink);
					echo ' '.$eLang->get('IN').' <a href="'.$link.'" title="'.$row->category.'">'.$row->category.'</a>';
				}
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</article>\n";
			}
		}

		echo '<div class="elx_back">'."\n";
		echo '<a href="javascript:void(null);" onclick="javascript:window.history.go(-1);" title="'.$eLang->get('BACK').'">'.$eLang->get('BACK')."</a>\n";
		echo "</div>\n";
		$this->wrapperEnd('tags');
	}


	/****************************/
	/* SEND TO FRIEND HTML FORM */
	/****************************/
	public function sendToFriendHTML($row, $data, $errormsg='', $successmsg='') {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$action = $elxis->makeURL('send-to-friend.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmsendfriend',
			'action' => $action,
			'idprefix' => 'sf',
			'label_width' => 180,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);

		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('EMAIL_TO_FRIEND'));
		$form->addText('sender_name', $data->sender_name, $eLang->get('YOUR_NAME'), array('required' => 1, 'dir' => 'rtl', 'size' => 30));
		$form->addEmail('sender_email', $data->sender_email, $eLang->get('YOUR_EMAIL'), array('required' => 1, 'dir' => 'ltr', 'size' => 30));
		$form->addText('friend_name', $data->friend_name, $eLang->get('FRIEND_NAME'), array('required' => 1, 'dir' => 'rtl', 'size' => 30));
		$form->addEmail('friend_email', $data->friend_email, $eLang->get('FRIEND_EMAIL'), array('required' => 1, 'dir' => 'ltr', 'size' => 30));
		$form->addHidden('article_id', $row->id);
		$form->addButton('sbmsf', $eLang->get('SEND'), 'submit', array('tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();

		echo '<h3 style="padding:0; margin:0 0 10px 0;">'.$row->title."</h3>\n";
		echo '<p style="padding:0; margin:0 0 10px 0;">'.$eLang->get('SENT_ARTICLE_FRIEND')."</p>\n";
		if ($errormsg != '') {
			echo '<div class="elx_error" style="margin-bottom: 15px;">'.$errormsg."</div>\n";
		} elseif ($successmsg != '') {
			echo '<div class="elx_success" style="margin-bottom: 15px;">'.$successmsg."</div>\n";
		}

		$form->render();
	}

}

?>