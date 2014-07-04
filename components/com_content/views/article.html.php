<?php 
/**
* @version		$Id: article.html.php 1392 2013-02-23 19:28:45Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class articleContentView extends contentView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		$this->inc = rand(100,500);
		parent::__construct();
	}


	/*******************/
	/* DISPLAY ARTICLE */
	/*******************/
	public function showArticle($row, $params, $chained=null, $comments=null, $print=0) {

		if ((int)$params->get('popup_window') == 1) {
			eFactory::getDocument()->loadLightbox();
		}

		$this->wrapperStart('article', $row->id);

		$this->renderArticleTop($row, $params, $print);

		echo $this->makeImageBox($row, $params);
		if (trim($row->subtitle) != '') { echo '<p class="elx_content_subtitle">'.$row->subtitle."</p>\n"; }
		echo $row->text."\n";
		echo '<div class="clear"></div>'."\n";

		if (ELXIS_MOBILE == 1) { echo "<footer>\n"; }
		$this->renderArticleBottom($row, $params);
		if ($print == 0) {
			$this->processComments($comments, $row, $params);
			$this->renderChainedArticles($chained, $params);
		}
		if (ELXIS_MOBILE == 1) { echo "</footer>\n"; }
		$this->wrapperEnd('article');
	}


	/*******************************/
	/* SHOW COMMENTS LIST AND FORM */
	/*******************************/
	private function processComments($comments, $row, $params) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDate = eFactory::getDate();

		if ((int)$params->get('comments') <> 1) { return; }
		$comments_src = (int)$params->get('comments_src');
		if ($comments_src == 2) {
			echo '<div id="disqus_thread"></div>'."\n";
			return;
		}

		eFactory::getDocument()->addScriptLink($elxis->secureBase().'/components/com_content/js/content.js');

		echo '<h3>'.$eLang->get('COMMENTS')."</h3>\n";
		echo '<div id="elxcontools" style="display:none; margin:0; padding:0; visibility:hidden;">'.$elxis->makeURL('content:contenttools.html', 'inner.php')."</div>\n";
		if ($comments) {
			$canmail = ($elxis->acl()->getLevel() >= 70) ? true : false;
			$canpub = ($elxis->acl()->check('com_content', 'comments', 'publish') == 2) ? true : false;
			$candel = ($elxis->acl()->check('com_content', 'comments', 'delete') == 2) ? true : false;

			$avsize = 50;
			if ($eLang->getinfo('DIR') == 'rtl') {
				$dirl = 'right';
				$dirr = 'left';
			} else {
				$dirl = 'left';
				$dirr = 'right';				
			}

			echo '<ul class="elx_comments_box" id="elx_comments_list">'."\n";
			foreach ($comments as $comment) {
				$avatar = $elxis->obj('avatar')->getAvatar($comment->avatar, $avsize, 1, $comment->email);
				$msgcss = ($comment->published == 1) ? 'elx_comment_message' : 'elx_comment_message_unpub';
				echo '<li id="elx_comment_'.$comment->id.'">'."\n";
				echo '<div style="margin:0; padding:0; text-align:center; width:'.($avsize + 18).'px; float:'.$dirl.';">'."\n";
				echo '<img src="'.$avatar.'" class="elx_comment_avatar" alt="avatar" title="'.$comment->author.'" width="'.$avsize.'" />';
				echo '<div class="elx_comment_actions">'."\n";
				if ($canmail) {
					echo '<a href="mailto:'.$comment->email.'" title="'.$comment->email.'">';
					echo '<img src="'.$elxis->secureBase().'/components/com_content/images/email.png" alt="e-mail" /></a>'."\n";
				}
				if ($canpub && ($comment->published == 0)) {
					echo '<a href="javascript:void(null);"  id="elx_comment_publish_'.$comment->id.'" onclick="elxPublishComment('.$comment->id.');" title="'.$eLang->get('PUBLISH').'">';
					echo '<img src="'.$elxis->secureBase().'/components/com_content/images/tick.png" alt="publish" /></a>'."\n";
				}
				if ($candel) {
					echo '<a href="javascript:void(null);" onclick="elxDeleteComment('.$comment->id.');" title="'.$eLang->get('DELETE').'">';
					echo '<img src="'.$elxis->secureBase().'/components/com_content/images/delete.png" alt="delete" /></a>'."\n";
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div style="margin-'.$dirl.':'.($avsize + 20).'px;">'."\n";
				echo "<div>\n";
				echo '<div class="elx_comment_author">'.$comment->author."</div>\n";
				echo '<div class="elx_comment_date">'.$eDate->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'))."</div>\n";
				echo "</div>\n";
				echo '<div style="clear:'.$dirr.';"></div>'."\n";
				echo '<div class="'.$msgcss.'" id="elx_comment_message_'.$comment->id.'">'."\n";
				if ($comments_src == 1) {
					if (strpos($comment->message, '<') !== false) {//bbcode saved as html
						echo $comment->message;
					} else {//bbcode saved as text
						echo nl2br($comment->message);
					}
				} else {
					echo nl2br(strip_tags($comment->message));
				}
				echo "</div>\n";
				echo "</div>\n";
				echo '<div class="clear"></div>'."\n";
				echo "</li>\n";
			}
			echo "</ul>\n";
		} else {
			echo '<p>'.$eLang->get('NO_COMMENTS_ARTICLE')."</p>\n";
		}

		$allow_comment = (int)$elxis->acl()->check('com_content', 'comments', 'post');
		if (!$allow_comment) {
			if ($elxis->user()->gid == 7) {
				$link = $elxis->makeURL('user:login/');
				echo '<p>'.$eLang->get('NALLOW_POST_COMMENTS');
				echo ' <a href="'.$link.'" title="'.$eLang->get('LOGIN').'">'.$eLang->get('PLEASE_LOGIN').'</a>';
				echo "</p>\n";
			} else {
				echo '<p>'.$eLang->get('NALLOW_POST_COMMENTS')."</p>\n";
			}
			return;
		}

		$action = $elxis->makeURL('content:contenttools.html', 'inner.php');
		elxisLoader::loadFile('includes/libraries/elxis/form.class.php');
		$formOptions = array(
			'name' => 'fmpostcomment',
			'action' => $action,
			'idprefix' => 'pcom',
			'label_width' => 180,
			'label_align' => 'left',
			'label_top' => 0,
			'tip_style' => 2
		);
		$formOptions['jsonsubmit'] = ($comments_src == 1) ? 'elxPostBBcodeComment' : 'elxPostComment';

		$hidden = array();
		$form = new elxisForm($formOptions);
		$form->openFieldset($eLang->get('POST_COMMENT'));
		if ((int)$elxis->user()->uid  < 1) {
			if ($elxis->user()->gid == 6) {
				$name = eUTF::trim($elxis->user()->firstname.' '.$elxis->user()->lastname);
				if ($name == '') { $name = eUTF::trim($elxis->user()->uname); }
				if ($name == '') {
					$form->addText('author', $name, $eLang->get('NAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
				} else {
					$hidden[] = 'author';
				}
				if (trim($elxis->user()->email) == '') {
					$form->addEmail('email', '', $eLang->get('EMAIL'), array('required' => 1, 'size' => 60, 'tip' => $eLang->get('EMAIL_NOT_PUBLISH')));
				} else {
					$hidden[] = 'email';
				}
			} else {
				$form->addText('author', '', $eLang->get('NAME'), array('required' => 1, 'dir' => 'rtl', 'maxlength' => 60));
				$form->addEmail('email', '', $eLang->get('EMAIL'), array('required' => 1, 'size' => 60, 'tip' => $eLang->get('EMAIL_NOT_PUBLISH')));
			}
		} else {
			$hidden[] = 'author';
			$hidden[] = 'email';
		}

		if ($comments_src == 1) {
			$clang = $eLang->currentLang();
			$form->addTextarea('message', '', $eLang->get('YOUR_MESSAGE'), array('required' => 0, 'cols' => 30, 'rows' => 6, 'dir' => 'rtl', 'editor' => 'bbcode', 'contentslang' => $clang));
		} else {
			$form->addTextarea('message', '', $eLang->get('YOUR_MESSAGE'), array('required' => 1, 'dir' => 'rtl'));
		}
		$form->addCaptcha('comseccode');
		$form->addHidden('id', $row->id);
		$form->addHidden('act', 'postcomment');
		if ($hidden) {
			foreach ($hidden as $hid) { $form->addHidden($hid, ''); }
		}
		$form->addButton('sbmpc', $eLang->get('SUBMIT'), 'submit', array('tip' => $eLang->get('FIELDSASTERREQ')));
		$form->closeFieldset();
		$form->render();
	}


	/*************************/
	/* RENDER ARTICLE'S TAGS */
	/*************************/
	private function renderTags($row) {
		if (count($row->keywords['tags']) == 0) { return; }
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		
		echo '<div class="elx_tags_box">'."\n";
		echo '<span>'.$eLang->get('TAGS').":</span> \n";
		foreach ($row->keywords['tags'] as $tag) {
			$link = $elxis->makeURL('tags.html?tag='.urlencode($tag));
			$title = sprintf($eLang->get('ARTICLES_TAGGED'), $tag);
			echo '<a href="'.$link.'" title="'.$title.'">'.$tag."</a> \n";
		}
		echo "</div>\n";
	}


	/************************/
	/* RENDER ARTICLE'S TOP */
	/************************/
	private function renderArticleTop($row, $params, $print=0) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$art_dateauthor = (int)$params->get('art_dateauthor', 6);
		$art_print = (int)$params->get('art_print', 1);
		$art_email = (int)$params->get('art_email', 1);
		if ($print == 1) { $art_print = 1; $art_email = 0; }
		$float = ($eLang->getinfo('DIR') == 'rtl') ? 'right' : 'left';

		if (ELXIS_MOBILE == 1) {
			echo '<header class="elx_article_header">'."\n";
			echo '<h1>'.$row->title."</h1>\n";
		} else {
			echo '<div class="elx_article_header">'."\n";
			echo '<h1 style="float:'.$float.';">'.$row->title."</h1>\n";
		}

		if (($art_print > 0) || ($art_email > 0)) {
			echo '<div class="elx_content_icons">'."\n";
			if ($art_print == 1) {
				if (file_exists(ELXIS_PATH.'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png')) {
					$icon_print = $elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE').'/images/print.png';
				} else {
					$icon_print = $elxis->secureBase().'/templates/system/images/print.png';
				}

				echo '<div class="elx_content_icon">'."\n";
				if ($print == 1) {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINT').'" onclick="javascript:window.print();">';
				} else {
					$link = $elxis->makeURL($row->link.'?print=1', 'inner.php');
					echo '<a href="javascript:void(null);" title="'.$eLang->get('PRINTABLE_VERSION').'" onclick="elxPopup(\''.$link.'\', 600, 400);">';
				}
				echo '<img src="'.$icon_print.'" alt="print" /></a>'."\n";
				echo "</div>\n";
			}

			if ($art_email == 1) {
				if (file_exists(ELXIS_PATH.'/templates/'.$elxis->getConfig('TEMPLATE').'/images/email.png')) {
					$icon_email = $elxis->secureBase().'/templates/'.$elxis->getConfig('TEMPLATE').'/images/email.png';
				} else {
					$icon_email = $elxis->secureBase().'/templates/system/images/email.png';
				}

				$link = $elxis->makeURL('send-to-friend.html?id='.$row->id, 'inner.php');
				echo '<div class="elx_content_icon">'."\n";
				if ((int)$params->get('popup_window') == 1) {
					$eDoc->loadLightbox();
					$eDoc->addDocReady('$(\'#artmailfriend'.$row->id.'\').colorbox({iframe:true, width:600, height:440});');
					echo '<a href="'.$link.'" title="'.$eLang->get('EMAIL_TO_FRIEND').'" id="artmailfriend'.$row->id.'">';
				} else {
					echo '<a href="javascript:void(null);" title="'.$eLang->get('EMAIL_TO_FRIEND').'" onclick="elxPopup(\''.$link.'\', 500, 400);">';
				}
				echo '<img src="'.$icon_email.'" alt="email" /></a>'."\n";
				echo "</div>\n";
			}
			echo "</div>\n";
		}
		echo '<div class="clear"></div>'."\n";
		if (ELXIS_MOBILE == 1) {
			echo "</header>\n";
		} else {
			echo "</div>\n";
		}

		if ($art_dateauthor > 0) {
			if ((int)$params->get('art_dateauthor_pos', 0) == 0) {
				$allowed_any_profile = ((int)eFactory::getElxis()->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
				$dateauthor = $this->getDateAuthor($row, $art_dateauthor, $allowed_any_profile);
				if ($dateauthor != '') { echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
			}
		}
	}


	/***************************/
	/* RENDER ARTICLE'S BOTTOM */
	/***************************/
	private function renderArticleBottom($row, $params) {
		$art_dateauthor = (int)$params->get('art_dateauthor', 6);
		if ($art_dateauthor > 0) {
			if ((int)$params->get('art_dateauthor_pos', 0) == 1) {
				$allowed_any_profile = ((int)eFactory::getElxis()->acl()->check('com_user', 'profile', 'view') == 2) ? true : false;
				$dateauthor = $this->getDateAuthor($row, $art_dateauthor, $allowed_any_profile);
				if ($dateauthor != '') { echo '<div class="elx_dateauthor">'.$dateauthor.'</div>'."\n"; }
			}
		}

		if ((int)$params->get('art_hits', 1) == 1) {
			$txt = sprintf(eFactory::getLang()->get('READ_TIMES'), '<span>'.$row->hits.'</span>');
			echo '<div class="elx_hits_box">'.$txt."</div>\n";
		}

		if ((int)$params->get('art_tags') == 1) {
			$this->renderTags($row);
		}
	}


	/*********************************/
	/* RENDER PREVIOUS/NEXT ARTICLES */
	/*********************************/
	private function renderChainedArticles($chained, $params) {
		if (!$chained) { return; }
		if (($chained['previous'] === null) && ($chained['next'] === null)) { return; }
		if (ELXIS_MOBILE == 1) {
			$this->renderChainedArticlesMob($chained, $params);
			return;
		}

		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		if ($eLang->getinfo('DIR') == 'rtl') {
			$floatp = 'right';
			$floatn = 'left';
		} else {
			$floatp = 'left';
			$floatn = 'right';
		}

		$ctg_img_empty = (int)$params->get('ctg_img_empty', 1);
		$img_thumb_width = 50;

		echo '<div class="elx_cols_wrapper">'."\n";
		echo '<div class="elx_2columns" style="float:'.$floatp.';">'."\n";
		echo '<div class="elx_chain_previous">'."\n";
		if ($chained['previous'] !== null) {
			$link = $elxis->makeURL($chained['previous']->link);
			$imgfile = '';
			if ((int)$params->get('art_chain') == 2) {
				if ((trim($chained['previous']->image) == '') || !file_exists(ELXIS_PATH.'/'.$chained['previous']->image)) {
					if ($ctg_img_empty == 1) { $imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; }
				} else {
					$imgfile = $elxis->secureBase().'/'.$chained['previous']->image;
					$file_info = $eFiles->getNameExtension($chained['previous']->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					}
					unset($file_info);
				}
			}

			if ($imgfile != '') {
				echo '<a href="'.$link.'" title="'.$chained['previous']->title.'">';
				echo '<img src="'.$imgfile.'" alt="'.$chained['previous']->title.'" width="'.$img_thumb_width.'" />';
				echo "</a>\n"; 
			}
			echo '<div class="elx_chain_title">'.$eLang->get('PREVIOUS_ARTICLE')."</div>\n";
			echo '<a href="'.$link.'" title="'.$chained['previous']->title.'">'.$chained['previous']->title."</a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";

		echo '<div class="elx_2columns" style="float:'.$floatn.';">'."\n";
		echo '<div class="elx_chain_next">'."\n";
		if ($chained['next'] !== null) {
			$link = $elxis->makeURL($chained['next']->link);
			$imgfile = '';
			if ((int)$params->get('art_chain') == 2) {
				if ((trim($chained['next']->image) == '') || !file_exists(ELXIS_PATH.'/'.$chained['next']->image)) {
					if ($ctg_img_empty == 1) { $imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg'; }
				} else {
					$imgfile = $elxis->secureBase().'/'.$chained['next']->image;
					$file_info = $eFiles->getNameExtension($chained['next']->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
					}
					unset($file_info);
				}
			}

			if ($imgfile != '') {
				echo '<a href="'.$link.'" title="'.$chained['next']->title.'">';
				echo '<img src="'.$imgfile.'" alt="'.$chained['next']->title.'" width="'.$img_thumb_width.'" />';
				echo "</a>\n";
			}
			echo '<div class="elx_chain_title">'.$eLang->get('NEXT_ARTICLE')."</div>\n";
			echo '<a href="'.$link.'" title="'.$chained['next']->title.'">'.$chained['next']->title."</a>\n";
		}
		echo "</div>\n";
		echo "</div>\n";
		echo '<div class="clear">'."</div>\n";
		echo "</div>\n";
	}


	/**************************************************/
	/* RENDER PREVIOUS/NEXT ARTICLES (MOBILE VERSION) */
	/**************************************************/
	private function renderChainedArticlesMob($chained, $params) {
		if (!$chained) { return; }
		if (($chained['previous'] === null) && ($chained['next'] === null)) { return; } 
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();

		echo '<nav class="elx_chain_wrap">';
		echo '<div class="elx_chain_previous">'."\n";
		if ($chained['previous'] !== null) {
			$link = $elxis->makeURL($chained['previous']->link);
			$imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';
			if ((trim($chained['previous']->image) != '') && file_exists(ELXIS_PATH.'/'.$chained['previous']->image)) {
				$imgfile = $elxis->secureBase().'/'.$chained['previous']->image;
				$file_info = $eFiles->getNameExtension($chained['previous']->image);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
				}
				unset($file_info);
			}

			echo '<a href="'.$link.'" title="'.$chained['previous']->title.'"><img src="'.$imgfile.'" alt="'.$chained['previous']->title.'" /></a><br />'."\n"; 
			echo '<a href="'.$link.'" title="'.$chained['previous']->title.'">'.$chained['previous']->title."</a>\n";
		}
		echo "</div>\n";
		echo '<div class="elx_chain_next">'."\n";
		if ($chained['next'] !== null) {
			$link = $elxis->makeURL($chained['next']->link);
			$imgfile = $elxis->secureBase().'/templates/system/images/nopicture_article.jpg';
			if ((trim($chained['next']->image) != '') && file_exists(ELXIS_PATH.'/'.$chained['next']->image)) {
				$imgfile = $elxis->secureBase().'/'.$chained['next']->image;
				$file_info = $eFiles->getNameExtension($chained['next']->image);
				if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
					$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_thumb.'.$file_info['extension'];
				}
				unset($file_info);
			}

			echo '<a href="'.$link.'" title="'.$chained['next']->title.'"><img src="'.$imgfile.'" alt="'.$chained['next']->title.'" /></a><br />'."\n"; 
			echo '<a href="'.$link.'" title="'.$chained['next']->title.'">'.$chained['next']->title."</a>\n";
		}
		echo "</div>\n";
		echo '<div class="clear">'."</div>\n";
		echo "</nav>\n";
	}


	/********************************/
	/* GENERATE ARTICLE'S IMAGE BOX */
	/********************************/
	private function makeImageBox($row, $params) {
		if ((trim($row->image) == '') || !file_exists(ELXIS_PATH.'/'.$row->image)) { return ''; }
		$art_img = (int)$params->get('art_img', 2);
		if ($art_img < 1) { $art_img = 2; }

		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		if ($eLang->getinfo('DIR') == 'rtl') {
			if ($art_img == 2) {
				$art_img = 3;
			} elseif ($art_img == 3) {
				$art_img = 2;
			} elseif ($art_img == 5) {
				$art_img = 6;
			} elseif ($art_img == 6) {
				$art_img = 5;
			}
		}

		$img_medium_width = (int)$params->get('img_medium_width', 240);
		$imgfile = $elxis->secureBase().'/'.$row->image;
		if ($art_img < 4) {
			$lightbox = true;
			$file_info = eFactory::getFiles()->getNameExtension($row->image);
			if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_medium.'.$file_info['extension'])) {
				$imgfile = $elxis->secureBase().'/'.$file_info['name'].'_medium.'.$file_info['extension'];
			}
			$img_style = ' width="'.$img_medium_width.'" style="width:'.$img_medium_width.'px;"';
		} else {
			$lightbox = false;
			$img_style = '';
		}

		if (ELXIS_MOBILE == 1) {
			$imgbox = '<figure class="elx_content_imagebox">'."\n";
			$imgbox .= '<img src="'.$imgfile.'" alt="'.$row->title.'" />'."\n";
			if (trim($row->caption) != '') { $imgbox .= '<figcaption>'.$row->caption."</figcaption>\n"; }
			$imgbox .= "</figure>\n";	
			return $imgbox;
		}

		$img_style = '';
		$img_box_style = '';
		switch ($art_img) {
			case 1:
				$img_box_style = ($eLang->getinfo('DIR') == 'rtl') ? ' style="text-align:right;"' : ' style="text-align:left;"';
			break;
			case 2:
				$w = $img_medium_width + 10;
				$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:'.$w.'px;"';
			break;
			case 3:
				$w = $img_medium_width + 10;
				$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:'.$w.'px;"';
			break;
			case 4: break;
			case 5:
				$size = getimagesize(ELXIS_PATH.'/'.$row->image);
				$w = $size[0] + 10;
				$img_box_style = ' style="margin:0 5px 5px 0; float:left; width:'.$w.'px;"';
			break;
			case 6:
				$size = getimagesize(ELXIS_PATH.'/'.$row->image);
				$w = $size[0] + 10;
				$img_box_style = ' style="margin:0 0 5px 5px; float:right; width:'.$w.'px;"';
			break;
			default: break;
		}

		$imgbox = '<div class="elx_content_imagebox"'.$img_box_style.'>'."\n";
		if ($lightbox) {
			if ((int)$params->get('popup_window') == 0) { eFactory::getDocument()->loadLightbox(); }
			$imgbox .= '<a href="'.$elxis->secureBase().'/'.$row->image.'" title="'.$row->title.'" class="elx_litebox">';
			$imgbox .= '<img src="'.$imgfile.'" alt="'.$row->title.'"'.$img_style.' />';
			$imgbox .= "</a>\n";
		} else {
			$imgbox .= '<img src="'.$imgfile.'" alt="'.$row->title.'"'.$img_style.' />'."\n"; 
		}

		if (trim($row->caption) != '') { $imgbox .= '<div>'.$row->caption."</div>\n"; }
		$imgbox .= "</div>\n";	
		return $imgbox;
	}


	/********************************************/
	/* GET/FORMAT DATE AN AUTHOR FOR AN ARTICLE */
	/********************************************/
	private function getDateAuthor($article, $type, $allowed=false) {
		$eLang = eFactory::getLang();
		$dateauthor = '';
		switch($type) {
			case 1:	$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get('DATE_FORMAT_12')); break;
			case 2:
				$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get('DATE_FORMAT_12'));
				if ($allowed) {
					$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
					$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
				} else {
					$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
				}
			break;
			case 3:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get('DATE_FORMAT_4'));
				}
			break;
			case 4:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get('DATE_FORMAT_4'));
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				}
			break;
			case 5:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get('DATE_FORMAT_4'));
				} else {
					$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get('DATE_FORMAT_12'));
				}
			break;
			case 6:
				if ($article->modified != '1970-01-01 00:00:00') {
					$dateauthor = $eLang->get('LAST_UPDATE').' '.eFactory::getDate()->formatDate($article->modified, $eLang->get('DATE_FORMAT_4'));
					if (($article->modified_by > 0) && $allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->modified_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->modified_by_name.'">'.$article->modified_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->modified_by_name;
					}
				} else {
					$dateauthor = eFactory::getDate()->formatDate($article->created, $eLang->get('DATE_FORMAT_12'));
					if ($allowed) {
						$proflink = eFactory::getElxis()->makeURL('user:members/'.$article->created_by.'.html');
						$dateauthor .= ' '.$eLang->get('BY').' <a href="'.$proflink.'" title="'.$article->created_by_name.'">'.$article->created_by_name.'</a>';
					} else {
						$dateauthor .= ' '.$eLang->get('BY').' '.$article->created_by_name;
					}
				}
			break;
			default: break;
		}

		return $dateauthor;
	}

}

?>