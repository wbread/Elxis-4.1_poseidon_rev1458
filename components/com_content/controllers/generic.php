<?php 
/**
* @version		$Id: generic.php 1386 2013-02-17 10:47:51Z datahell $
* @package		Elxis
* @subpackage	Component Content
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class genericContentController extends contentController {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct($view=null, $model=null, $format='') {
		parent::__construct($view, $model, $format);
	}


	/**************************************/
	/* PREPARE TO DISPLAY TAGGED ARTICLES */
	/**************************************/
	public function tags() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		if (isset($_GET['tag'])) {
			$tag = filter_input(INPUT_GET, 'tag', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			$pat = "#([\']|[\!]|[\;]|[\"]|[\$]|[\/]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\\\])#u";
			$tag = eUTF::trim(preg_replace($pat, '', $tag));
			if (eUTF::strlen($tag) < 4) { $tag = ''; }
		} else {
			$tag = '';
		}
		
		if ($tag == '') {
			$pathway = eFactory::getPathway();
			$pathway->addNode($eLang->get('TAGS'));
			$pathway->addNode($eLang->get('ERROR'));
			$eDoc->setTitle($eLang->get('TAGS').' - '.$elxis->getConfig('SITENAME'));
			$this->view->base_errorScreen($eLang->get('NO_TAG_SPECIFIED'), $eLang->get('ERROR'), false, true, true);
			return;
		}

		$rows = $this->loadTagArticles($tag);

		$global_str = (string)$this->model->componentParams();
		elxisLoader::loadFile('includes/libraries/elxis/parameters.class.php');
		$params = new elxisParameters($global_str, '', 'component');
		if ((int)$params->def('img_thumb_width', 100) < 10) {
			$params->set('img_thumb_width', 100);
		}

		$eDoc->setTitle($eLang->get('TAG').' '.$tag.' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('ARTICLES_TAGGED'), $tag);
		$eDoc->setDescription($desc.'. '.$elxis->getConfig('SITENAME'));
		$eDoc->setKeywords(array($tag, $eLang->get('TAGS')));

		$pathway = eFactory::getPathway();
		$pathway->addNode($eLang->get('TAGS'));
		$pathway->addNode($tag);

		$this->view->showTagArticles($rows, $tag, $params);
	}


	/**************************************************/
	/* PREPARE TO DISPLAY LIST OF AVAILABLE XML FEEDS */
	/**************************************************/
	public function feeds() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eDoc = eFactory::getDocument();

		$rows = $this->loadFeedCategories();

		$eDoc->setTitle($eLang->get('RSS_ATOM_FEEDS_CENTRAL').' - '.$elxis->getConfig('SITENAME'));
		$desc = sprintf($eLang->get('XML_FEEDS_FROM'), $elxis->getConfig('SITENAME'));
		$eDoc->setDescription($desc);
		$eDoc->setKeywords(array('RSS', 'ATOM', 'XML', 'news feeds', 'syndication', 'feeds', $eLang->get('RSS_ATOM_FEEDS_CENTRAL')));

		$pathway = eFactory::getPathway();
		$pathway->addNode($eLang->get('RSS_ATOM_FEEDS_CENTRAL'));

		$this->view->feedsCentral($rows);
	}


	/***********************************/
	/* DISPLAY SITE FEED IN RSS FORMAT */
	/***********************************/
	public function rssfeed() {
		$this->viewXMLsite('rss');
	}


	/************************************/
	/* DISPLAY SITE FEED IN ATOM FORMAT */
	/************************************/
	public function atomfeed() {
		$this->viewXMLsite('atom');
	}


	/****************************************/
	/* PREPARE TO DISPLAY XML FEED FOR SITE */
	/****************************************/
	private function viewXMLsite($type='rss') {
		$elxis = eFactory::getElxis();
		$eFiles = eFactory::getFiles();
		$eLang = eFactory::getLang();

		$feeditems = 10;
		$cachefile = $type.'-'.$eLang->currentLang().'.xml';
		$feed_cache = 14400; //4 hours
		$repo_path = rtrim($elxis->getConfig('REPO_PATH'), '/');
		if ($repo_path == '') { $repo_path = ELXIS_PATH.'/repository'; }

		if (file_exists($repo_path.'/cache/feeds/'.$cachefile)) {
			$ts = filemtime($repo_path.'/cache/feeds/'.$cachefile);
			if (($ts + $feed_cache) > time()) {
				if (@ob_get_length() > 0) { @ob_end_clean(); }
				@header("Content-type:text/xml; charset=utf-8");
				echo file_get_contents($repo_path.'/cache/feeds/'.$cachefile);
				exit();
			}
		}

		$articles = $this->loadFeedArticles(10);

		elxisLoader::loadFile('includes/libraries/elxis/feed.class.php');
		$feed = new elxisFeed($type);
		if (!file_exists($repo_path.'/cache/feeds/')) {
			$eFiles->createFolder('cache/feeds/', 0755, true);
		}

		$ttl = intval($feed_cache / 60);
		$feed->setTTL($ttl);

		$channel_title = $elxis->getConfig('SITENAME');
		$channel_link = $elxis->getConfig('URL');

		$feed->addChannel($channel_title, $channel_link, $elxis->getConfig('METADESC'));

		if ($articles) {
			$ePlugin = eFactory::getPlugin();
			foreach ($articles as $article) {
				$enclosure = null;
				$itemdesc = '';
				if (trim($article->subtitle) != '') {
					$itemdesc = '<strong>'.$article->subtitle.'</strong><br />'."\n";
				}
				if (trim($article->introtext) != '') {
					$desc = $ePlugin->removePlugins($article->introtext);
					$desc = strip_tags($desc);
					$itemdesc .= $desc;
				}

				if (trim($article->image != '')) {
					$enclosure = $article->image;
					$file_info = $eFiles->getNameExtension($article->image);
					if (file_exists(ELXIS_PATH.'/'.$file_info['name'].'_thumb.'.$file_info['extension'])) {
						$enclosure = $file_info['name'].'_thumb.'.$file_info['extension'];
						$itemdesc = '<img style="margin:5px; float:left;" src="'.$elxis->getConfig('URL').'/'.$enclosure.'" alt="'.$row->title.'" /> '.$itemdesc;
					} elseif (!file_exists(ELXIS_PATH.'/'.$article->image)) {
						$enclosure = null;
					}
				}

				if ($article->catid > 0) {
					$link = $elxis->makeURL($article->seolink.$article->seotitle.'.html');
				} else {
					$link = $elxis->makeURL($article->seotitle.'.html');
				}

				$feed->addItem(
					$article->title,
					$itemdesc,
					$link,
					strtotime($article->created),
					$article->created_by_name,
					$enclosure
				);
			}
		}

		$action = ($feed_cache > 0) ? 'saveshow' : 'show';
		$feed->makeFeed($action, 'cache/feeds/'.$cachefile);
	}


	/***************************************/
	/* SEND AN ARTICLE TO A FRIEND (POPUP) */
	/***************************************/
	public function sendtofriend() {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();
		eFactory::getDocument()->setTitle($eLang->get('EMAIL_TO_FRIEND'));
		eFactory::getDocument()->setDescription($eLang->get('SENT_ARTICLE_FRIEND'));

		if (isset($_POST['article_id'])) {
			$id = (int)$_POST['article_id'];
		} else if (isset($_GET['id'])) {
			$id = (int)$_GET['id'];
		} else {
			$id = 0;
		}

		if ($id < 1) {
			$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
			return;
		}
		
		$row = $this->loadArticle('', $id);
		if (!$row) {
			$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
			return;
		}

		$category_link = '';
		if ($row->catid > 0) {
			$tree = $this->loadCategoryTree($row->catid);
			if (!$tree) {
				$this->view->base_errorScreen($eLang->get('ARTICLE_NOT_FOUND'));
				return;
			}
			$n = count($tree) - 1;
			$category_title = $tree[$n]->title;
			$category_link = $tree[$n]->link;
			$row->link = $category_link.$row->seotitle.'.html';
		} else {
			$row->link = $row->seotitle.'.html';
			$category_title = '';
		}

		$errormsg = '';
		$successmsg = '';
		$data = new stdClass;
		$data->sender_name = ($elxis->user()->firstname != '') ? $elxis->user()->firstname.' '.$elxis->user()->lastname : '';
		$data->sender_email = $elxis->user()->email;
		$data->friend_name = '';
		$data->friend_email = '';
		if (isset($_POST['sbmsf'])) {
			$sess_token = trim(eFactory::getSession()->get('token_fmsendfriend'));
			$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW));
			if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
				$errormsg = $eLang->get('REQDROPPEDSEC');
			}

			$data->sender_name = filter_input(INPUT_POST, 'sender_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($data->sender_name == '') { $errormsg = $eLang->get('PROVIDE_YOUR_NAME'); }
			$data->sender_email = filter_input(INPUT_POST, 'sender_email', FILTER_SANITIZE_EMAIL);
			if (!filter_var($data->sender_email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			}
			$data->friend_name = filter_input(INPUT_POST, 'friend_name', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
			if ($data->friend_name == '') { $errormsg = $eLang->get('PROVIDE_FRIEND_NAME'); }
			$data->friend_email = filter_input(INPUT_POST, 'friend_email', FILTER_SANITIZE_EMAIL);
			if (!filter_var($data->friend_email, FILTER_VALIDATE_EMAIL)) {
				$errormsg = $eLang->get('INVALIDEMAIL');
			}

			if ($errormsg == '') {
				$ok = $this->sendMailToFriend($row, $data, $category_link, $category_title);
				if (!$ok) {
					$errormsg = 'Could not send email!';
				} else {
					$successmsg = $eLang->get('MSG_SENT_SUCCESS');
					$data->sender_name = '';
					$data->sender_email = '';
					$data->friend_name = '';
					$data->friend_email = '';
				}
			}
		}

		$this->view->sendToFriendHTML($row, $data, $errormsg, $successmsg);
	}


	/************************/
	/* SEND EMAIL TO FRIEND */
	/************************/
	private function sendMailToFriend($row, $data, $category_link='', $category_title='') {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$subject = $eLang->get('INTERESTING_ARTICLE');
		$body = $eLang->get('HI').' '.$data->friend_name.",\n";
		$body .= sprintf($eLang->get('LINK_ARTICLE_INTEREST'), $data->sender_name)."\n\n";
		$body .= $row->title."\n";
		$body .= $elxis->makeURL($row->link)."\n\n";
		if (($category_link != '') && ($category_title != '')) {
			$body .= $eLang->get('CATEGORY').": \t".$category_title."\n";
			$body .= $elxis->makeURL($category_link)."\n\n";
		}
		$body .= $eLang->get('FRIEND_NAME').": \t".$data->sender_name."\n";
		$body .= $eLang->get('FRIEND_EMAIL').": \t".$data->sender_email."\n\n\n";
		$body .= $eLang->get('REGARDS')."\n";
		$body .= $elxis->getConfig('SITENAME')."\n";
		$body .= $elxis->getConfig('URL')."\n\n\n\n";
		$body .= "_______________________________________________________________\n";
		$body .= $eLang->get('NOREPLYMSGINFO');

		$to = $data->friend_email.','.$data->friend_name;
		$ok = $elxis->sendmail($subject, $body, '', null, 'plain', $to);
		return $ok;
	}


	/*****************/
	/* CONTENT TOOLS */
	/*****************/
	public function contenttools() {
		$act = trim(filter_input(INPUT_POST, 'act', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		switch($act) {
			case 'pubcomment': $this->publishComment(); break;
			case 'delcomment': $this->deleteComment(); break;
			case 'postcomment': $this->postComment(); break;
			default: $this->ajaxResponse('0|Invalid request'); break;
		}
	}


	/*******************************/
	/* GENERIC AJAX REQUEST (AJAX) */
	/*******************************/
	public function genericajax() {
		$f = '';
		if (isset($_POST['f'])) {
			$pat = "#([\']|[\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
			$f = trim(filter_input(INPUT_POST, 'f', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
			$f = preg_replace('@^(\/)@', '', $f);

			$f2 = trim(strip_tags(preg_replace($pat, '', $f)));
			$f2 = str_replace('..', '', $f2);
			$f2 = str_replace('\/\/', '', $f2);
		
			if ($f2 != $f) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}

			if (strpos($f, 'modules/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_content/plugins/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_user/auth/') === 0) {
				$ok = true;
			} else if (strpos($f, 'components/com_search/engines/') === 0) {
				$ok = true;
			} else {
				$ok = false;
			}

			if (!$ok) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!preg_match('@(\.php)$@', $f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
			if (!is_file(ELXIS_PATH.'/'.$f) || !file_exists(ELXIS_PATH.'/'.$f)) {
				$this->ajaxHeaders('text/plain');
				die('BAD');
			}
		}

		$this->ajaxHeaders('text/plain');
		if ($f == '') {
			echo 'BAD';
		} else {
			include(ELXIS_PATH.'/'.$f);
		}

		exit;
	}


	/**************************/
	/* PUBLISH COMMENT (AJAX) */
	/**************************/
	private function publishComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$pubaccess = (int)$elxis->acl()->check('com_content', 'comments', 'publish');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		if ($id < 1) { $this->ajaxResponse('0|Invalid request'); }
		if ($pubaccess < 1) { $this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION')); }
		$comment = $this->model->fetchComment($id);
		if (!$comment) { $this->ajaxResponse('0|The requested comment was not found!'); }
		if ($comment->published == 1) { $this->ajaxResponse('0|The comment is already published!'); }
		if ($pubaccess == 1) {
			if (($comment->uid == 0) || ($comment->uid != $elxis->user()->uid)) {
				$this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION'));
			}
		} elseif ($pubaccess <> 2) { //just in case
			$this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION'));
		}

		$artid = (int)$comment->elid;
		$row = $this->model->fetchArticle('', $artid);
		if (!$row) { $this->ajaxResponse('0|'.$eLang->get('ARTICLE_NOT_FOUND')); }
		if ($row->catid > 0) {
			$tree = $this->model->categoryTree($row->catid);
			if (!$tree) { $this->ajaxResponse('0|'.$eLang->get('NOTALLOWACCPAGE')); }
		}

		if ($row->catid > 0) {
			$n = count($tree) - 1;
			$article_link = $elxis->makeURL($tree[$n]->link.$row->seotitle.'.html');
			unset($tree);
		} else {
			$article_link = $elxis->makeURL($row->seotitle.'.html');
		}

		$ok = $this->model->publishComment($id);
		if ($ok) {
			$this->notifyPublishComment($comment->author, $comment->email, $row->title, $article_link);
			$this->ajaxResponse('1|Comment published successfully!');
		} else {
			$this->ajaxResponse('0|Could not publish comment!');
		}
	}


	/*************************/
	/* DELETE COMMENT (AJAX) */
	/*************************/
	private function deleteComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$delaccess = (int)$elxis->acl()->check('com_content', 'comments', 'delete');
		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;

		if ($id < 1) { $this->ajaxResponse('0|Invalid request'); }
		if ($delaccess < 1) { $this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION')); }
		$comment = $this->model->fetchComment($id);
		if (!$comment) { $this->ajaxResponse('0|The requested comment was not found!'); }
		if ($delaccess == 1) {
			if (($comment->uid == 0) || ($comment->uid != $elxis->user()->uid)) {
				$this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION'));
			}
		} elseif ($delaccess <> 2) { //just in case
			$this->ajaxResponse('0|'.$eLang->get('NOTALLOWACTION'));
		}

		$artid = (int)$comment->elid;
		$row = $this->model->fetchArticle('', $artid);
		if (!$row) { $this->ajaxResponse('0|'.$eLang->get('ARTICLE_NOT_FOUND')); }
		if ($row->catid > 0) {
			$tree = $this->model->categoryTree($row->catid);
			if (!$tree) { $this->ajaxResponse('0|'.$eLang->get('NOTALLOWACCPAGE')); }
		}

		$ok = $this->model->deleteComment($id);
		if ($ok) {
			$this->ajaxResponse('1|Comment deleted successfully!');
		} else {
			$this->ajaxResponse('0|Could not delete comment!');
		}
	}


	/***********************/
	/* POST COMMENT (AJAX) */
	/***********************/
	private function postComment() {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();
		$eSession = eFactory::getSession();

		$id = (isset($_POST['id'])) ? (int)$_POST['id'] : 0;
		$isajax = (isset($_POST['rnd'])) ? true : false;
		$token = trim(filter_input(INPUT_POST, 'token', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
		$comseccode = (isset($_POST['comseccode'])) ? (int)$_POST['comseccode'] : 0;
		$author = filter_input(INPUT_POST, 'author', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
		$sess_token = trim($eSession->get('token_fmpostcomment'));
		$sess_captcha = trim($eSession->get('captcha_comseccode'));

		$errormsg = '';
		if ((int)$elxis->acl()->check('com_content', 'comments', 'post') !== 1) {
			$errormsg = $eLang->get('NALLOW_POST_COMMENTS');
		} else if (($token == '') || ($sess_token == '') || ($sess_token != $token)) {
			$errormsg = $eLang->get('REQDROPPEDSEC');
		} else if (($sess_captcha == '') || ($comseccode == '') || ($comseccode != $sess_captcha)) {
			$errormsg = $eLang->get('INVALIDSECCODE');
		} else if ($id < 1) {
			$errormsg = $eLang->get('ARTICLE_NOT_FOUND');
		}

		if ($errormsg != '') {
			if ($isajax) {
				$this->ajaxResponse('0|'.$errormsg);
			} else {
				exitPage::make('error', 'CCON-0009', $errormsg);
			}
		}

		$row = $this->model->fetchArticle('', $id);
		if (!$row) {
			if ($isajax) {
				$this->ajaxResponse('0|'.$eLang->get('ARTICLE_NOT_FOUND'));
			} else {
				exitPage::make('404', 'CCON-0010', $eLang->get('ARTICLE_NOT_FOUND'));
			}
		}

		if ($row->catid > 0) {
			$tree = $this->model->categoryTree($row->catid);
			if (!$tree) {
				if ($isajax) {
					$this->ajaxResponse('0|'.$eLang->get('NOTALLOWACCPAGE'));
				} else {
					exitPage::make('403', 'CCON-0011', $eLang->get('NOTALLOWACCPAGE'));
				}
			}
		}

		$params = $this->combinedArticleParams($row->params, $row->catid);
		$comallowed = (int)$params->get('comments', 0);
		if ($comallowed !== 1) {
			if ($isajax) {
				$this->ajaxResponse('0|'.$eLang->get('COMMENTS_NALLOW_ARTICLE'));
			} else {
				exitPage::make('403', 'CCON-0012', $eLang->get('COMMENTS_NALLOW_ARTICLE'));
			}
		}

		if ($row->catid > 0) {
			$n = count($tree) - 1;
			$article_link = $elxis->makeURL($tree[$n]->link.$row->seotitle.'.html');
			unset($tree);
		} else {
			$article_link = $elxis->makeURL($row->seotitle.'.html');
		}

		$pat = "#([\!]|[\(]|[\)]|[\;]|[\"]|[\$]|[\#]|[\<]|[\>]|[\*]|[\%]|[\~]|[\`]|[\^]|[\|]|[\{]|[\}]|[\\\])#u";
		$uid = (int)$elxis->user()->uid;
		if ($uid  > 0) {
			$email = $elxis->user()->email;
			if ($elxis->getConfig('REALNAME') == 1) {
				$author = $elxis->user()->firstname.' '.$elxis->user()->lastname;
			} else {
				$author = $elxis->user()->uname;
			}
		} else {
			if ($elxis->user()->gid == 6) {
				$name = eUTF::trim($elxis->user()->firstname.' '.$elxis->user()->lastname);
				if ($name == '') { $name = eUTF::trim($elxis->user()->uname); }
				if ($name != '') {
					$author = $name;
				} else {
					$author = eUTF::trim(preg_replace($pat, '', $author));
					if ($author == '') {
						$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('NAME'));
					}
				}

				if (trim($elxis->user()->email) != '') {
					$email = $elxis->user()->email;
				} else {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						$errormsg = $eLang->get('INVALIDEMAIL');
					}
				}
			} else {
				$author = eUTF::trim(preg_replace($pat, '', $author));
				if ($author == '') {
					$errormsg = sprintf($eLang->get('FIELDNOEMPTY'), $eLang->get('NAME'));
				}
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$errormsg = $eLang->get('INVALIDEMAIL');
				}
			}
		}

		if ($errormsg != '') {
			if ($isajax) {
				$this->ajaxResponse('0|'.$errormsg);
			} else {
				$elxis->redirect($article_link, $errormsg, true);
			}
		}

		$comments_src = (int)$params->get('comments_src', 0);
		$message = '';
		if (isset($_POST['message'])) { //filter_input destroys line breaks
			$message = strip_tags($_POST['message']);
			if ($comments_src == 1) { //bbcode
				$pat = "#([\']|[\$]|[\%]|[\~]|[\`]|[\<]|[\>]|[\\\])#u";
				$message = eUTF::trim(preg_replace($pat, '', $message));
				$bbcode = $elxis->obj('bbcode');
				$message = $bbcode->toHTML($message);
				unset($bbcode);
			} else if ($comments_src == 0) {
				$pat = "#([\"]|[\']|[\$]|[\%]|[\~]|[\`]|[\<]|[\>]|[\|]|[\\\])#u";
				$message = eUTF::trim(preg_replace($pat, '', $message));
				$message = htmlspecialchars($message);
			} else { //just in case
				if ($isajax) {
					$this->ajaxResponse('0|Invalid comments source!');
				} else {
					$elxis->redirect($article_link, 'Invalid comments source!', true);
				}
			}
		}

		if ($message == '') {
			if ($isajax) {
				$this->ajaxResponse('0|'.$eLang->get('MUST_WRITE_MSG'));
			} else {
				$elxis->redirect($article_link, $eLang->get('MUST_WRITE_MSG'), true);
			}
		}

		elxisLoader::loadFile('includes/libraries/elxis/database/tables/comments.db.php');
		$comment = new commentsDbTable();
		$comment->element = 'com_content';
		$comment->elid = $id;
		$comment->message = $message;
		$comment->uid = $uid;
		$comment->author = $author;
		$comment->email = $email;
		$comment->published = (intval($elxis->acl()->check('com_content', 'comments', 'publish') > 0)) ? 1 : 0;

		if (!$comment->store()) {
			if ($isajax) {
				$this->ajaxResponse('0|'.$comment->getErrorMsg());
			} else {
				$elxis->redirect($article_link, $comment->getErrorMsg(), true);
			}
		}

		$this->commentNotifyAdmin($row, $comment, $article_link);

		if ($comment->published == 0) {
			if ($isajax) {
				$this->ajaxResponse('1|'.$eLang->get('COM_PUBLISH_APPROVAL'));
			} else {
				$elxis->redirect($article_link, $eLang->get('COM_PUBLISH_APPROVAL'), false);
			}
		}

		if (!$isajax) { $elxis->redirect($article_link); }

		if ($eLang->getinfo('DIR') == 'rtl') {
			$dirl = 'right';
			$dirr = 'left';
		} else {
			$dirl = 'left';
			$dirr = 'right';				
		}

		$avatar = $elxis->obj('avatar')->getAvatar($elxis->user()->avatar, 50, 1, $comment->email);

		$response = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\n";
		$response = "<eresponse>\n";
		$response .= '<artid>'.$comment->elid."</artid>\n";
		$response .= '<comid>'.$comment->id."</comid>\n";
		$response .= '<dirl>'.$dirl."</dirl>\n";
		$response .= '<dirr>'.$dirr."</dirr>\n";
		$response .= '<curtime>'.time()."</curtime>\n";
		$response .= '<author>'.$comment->author."</author>\n";
		$response .= '<avatar>'.$avatar."</avatar>\n";
		$response .= '<created>'.eFactory::getDate()->formatDate($comment->created, $eLang->get('DATE_FORMAT_5'))."</created>\n";
		if ($comments_src == 1) {
			$response .= '<message><![CDATA['.$comment->message."]]></message>\n";
		} else {
			$response .= '<message><![CDATA['.nl2br($comment->message)."]]></message>\n";
		}
		$response .= '</eresponse>';

		$this->ajaxHeaders('text/xml');
		echo $response;
		exit();
	}


	/*********************************/
	/* SHOW RESPONSE TO AJAX REQUEST */
	/*********************************/
	private function ajaxResponse($msg='0|Invalid request!') {
		$this->ajaxHeaders('text/plain');
		echo $msg;
		exit();	
	}


	/*********************************/
	/* SEND NEW COMMENT NOTIFICATION */
	/*********************************/
	private function commentNotifyAdmin($row, $comment, $article_link) {
		$eLang = eFactory::getLang();
		$elxis = eFactory::getElxis();

		$admins = $this->model->getAdmins();
		if (!$admins) { return; }

		$original_language = $eLang->currentLang();
		$curlang = $original_language;

		$clear_message = strip_tags($comment->message);
		$ip_address = eFactory::getSession()->getIP();
		foreach ($admins as $admin) {
			if ($admin->uid == $elxis->user()->uid) { continue; } //dont notify himself!
			$userlang = trim($admin->preflang);
			if (($userlang != '') && ($userlang != $curlang)) {
				$eLang->switchLanguage($userlang);
				$curlang = $userlang;
			}

			$subject = $eLang->get('NEW_COMMENT_NOTIF');
			$body = $eLang->get('HI').' '.$admin->firstname.' '.$admin->lastname.",\n";
			if ($comment->published == 1) {
				$body .= $eLang->get('NEW_COMMENT_PUBLISHED')."\n\n";
			} else {
				$body .= $eLang->get('NEW_COMMENT_WAIT_APPR')."\n\n";
			}
			$body .= $eLang->get('ARTICLE').": \t".$row->title."\n";
			$body .= $article_link."\n\n";
			$body .= $eLang->get('COMMENTED_BY').": \t".$comment->author.' ('.$comment->email.")\n";
			$body .= 'IP address: '.$ip_address."\n\n";
			$body .= $eLang->get('COMMENT').' #'.$comment->id.":\n";
			$body .= $clear_message."\n\n\n";
			$body .= $eLang->get('REGARDS')."\n";
			$body .= $elxis->getConfig('SITENAME')."\n";
			$body .= $elxis->getConfig('URL')."\n\n\n\n";
			$body .= "_______________________________________________________________\n";
			$body .= $eLang->get('NOREPLYMSGINFO');			

			$to = $admin->email.','.$admin->firstname.' '.$admin->lastname;
			$elxis->sendmail($subject, $body, '', null, 'plain', $to);			
		}

		if ($curlang != $original_language) {
			$eLang->switchLanguage($original_language);
		}
	}


	/************************/
	/* SHOW MINIFIED CSS/JS */
	/************************/
	public function minify() {
		$segs = eFactory::getURI()->getSegments();
		$last = count($segs) - 1;
		$error = false;
		$gzip = false;
		$path = '';
		$type = 'plain';
		if ($last < 0) {
			$error = true;
		} else if (preg_match('/(\.css)$/', $segs[$last])) {
			$type = 'css';
			$path = eFactory::getFiles()->elxisPath('cache/minify/'.$segs[$last], true);
			$gzip = (eFactory::getElxis()->getConfig('MINICSS') == 2) ? true : false;
		} else if (preg_match('/(\.js)$/', $segs[$last])) {
			$type = 'javascript';
			$path = eFactory::getFiles()->elxisPath('cache/minify/'.$segs[$last], true);
			$gzip = (eFactory::getElxis()->getConfig('MINIJS') == 2) ? true : false;
		} else {
			$error = true;
		}
		
		if (!$error) {
			if (!file_exists($path)) { $error = true; }
		}

		if (ob_get_length() > 0) { ob_end_clean(); }
		if ($gzip) {
			ob_start('ob_gzhandler');
		}
		header('content-type:text/'.$type.'; charset:UTF-8');
		if (!$error) {
			header("cache-control: must-revalidate");
			$expire = 'expires: '.gmdate("D, d M Y H:i:s", time() + 864000)." GMT";
			header($expire);
			include($path);
   		}
   		exit();
	}

}

?>