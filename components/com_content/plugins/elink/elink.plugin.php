<?php 
/**
* @version		$Id: elink.plugin.php 1444 2013-05-21 17:03:04Z datahell $
* @package		Elxis
* @subpackage	Component Content / Plugins
* @copyright	Copyright (c) 2006-2013 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class elinkPlugin implements contentPlugin {


	/********************/
	/* MAGIC CONTRUCTOR */
	/********************/
	public function __construct() {
	}


	/***********************************/
	/* EXECUTE PLUGIN ON THE GIVEN ROW */
	/***********************************/
	public function process(&$row, $published, $params) {
		$regex = '~href="#elink:(.*?)"~';
    	if (!$published) {
    		$row->text = preg_replace($regex, 'href="javascript:void(null);"', $row->text);
    		return true;
    	}

		preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER);
		if ($matches) {
			$elxis = eFactory::getElxis();
			foreach ($matches[1] as $k => $elxis_uri) {
				$href = 'href="'.$elxis->makeURL($elxis_uri).'"';
				$row->text = str_replace($matches[0][$k], $href, $row->text);
			}
		}
		return true;
	}


	/************************/
	/* GENERIC SYNTAX STYLE */
	/************************/
	public function syntax() {
		return '<a href="#elink:elxis_uri_here">text</a>';
	}


	/***********************/
	/* LIST OF HELPER TABS */
	/***********************/
	public function tabs() {
		$eLang = eFactory::getLang();
		$tabs = array($eLang->get('LINK_CONTENT'), $eLang->get('OTHER_COMPONENTS'), $eLang->get('HELP'));
		return $tabs;
	}


	/*****************/
	/* PLUGIN HELPER */
	/*****************/
	public function helper($pluginid, $tabidx, $fn) {
		switch ($tabidx) {
			case 1: $this->contentLinker($pluginid, $fn); break;
			case 2: $this->otherComponents(); break;
			case 3: $this->showHelp(); break;
			default: break;
		}
	}


	/***************************************************/
	/* RETURN REQUIRED CSS AND JS FILES FOR THE HELPER */
	/***************************************************/
	public function head() {
		$elxis = eFactory::getElxis();

		$response = array(
			'js' => array($elxis->secureBase().'/components/com_content/plugins/elink/includes/elink.js'),
			'css' => array()
		);

		return $response;	
	}


	/*******************************/
	/* PLUGIN SPECIAL TASK HANDLER */
	/*******************************/
	public function handler($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$db = eFactory::getDB();
		$eLang = eFactory::getLang();

		$catid = (isset($_POST['catid'])) ? (int)$_POST['catid'] : '';
		if ($catid <0) { $catid = 0; }

		$seolink = '';
		if ($catid > 0) {
			$sql = "SELECT ".$db->quoteId('seolink')." FROM ".$db->quoteId('#__categories')." WHERE ".$db->quoteId('catid')." = :xid";
			$stmt = $db->prepareLimit($sql, 0, 1);
			$stmt->bindParam(':xid', $catid, PDO::PARAM_INT);
			$stmt->execute();
			$seolink = $stmt->fetchResult();
			if (!$seolink) {
				$this->ajaxHeaders('text/html');
				echo '<div class="elx_error">The category was not found!</div>'."\n";
				exit();
			}
		}

		$sql = "SELECT ".$db->quoteId('id').", ".$db->quoteId('title').", ".$db->quoteId('seotitle').", ".$db->quoteId('published')
		."\n FROM ".$db->quoteId('#__content')." WHERE ".$db->quoteId('catid')." = :xid ORDER BY ".$db->quoteId('title')." ASC";
		$stmt = $db->prepareLimit($sql, 0, 500);
		$stmt->bindParam(':xid', $catid, PDO::PARAM_INT);
		$stmt->execute();
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (!$rows) {
			$this->ajaxHeaders('text/html');
			echo '<div class="elx_warning">'.$eLang->get('NO_ARTICLES_FOUND')."</div>\n";
			exit();
		}

		$k = 1;
		$i = 1;
		$img = $elxis->secureBase().'/components/com_content/plugins/elink/includes/link.png';
		$this->ajaxHeaders('text/html');

		echo '<div class="elx_sminfo">'.sprintf($eLang->get('MAX_ARTS_ORDERED'), '<strong>500</strong>')."</div>\n";
		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_subcenter" width="40">Inc'."</th>\n";
		echo '<th class="elx_th_subcenter" width="60">'.$eLang->get('ID')."</th>\n";
		echo '<th class="elx_th_subcenter" width="140">'.$eLang->get('LINK')."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('TITLE')."</th>\n";
		echo '<th class="elx_th_subcenter">'.$eLang->get('PUBLISHED')."</th>\n";
		echo "</tr>\n";
		foreach ($rows as $row) {
			$pcode = htmlspecialchars('<a href="#elink:content:'.$seolink.$row['seotitle'].'.html">'.$row['title'].'</a>');
			$pub = ($row['published'] == 1) ? '<span style="color:#008000">'.$eLang->get('YES').'</span>' : '<span style="color:#FF0000">'.$eLang->get('NO').'</span>';
			echo '<tr class="elx_tr'.$k.'">'."\n";
			echo '<td class="elx_td_center">'.$i."</td>\n";
			echo '<td class="elx_td_center">'.$row['id']."</td>\n";
			echo '<td class="elx_td_center"><a href="javascript:void(null);" onclick="addPluginCode(\''.$pcode.'\')" title="'.$eLang->get('INSERT_LINK_ART').'">';
			echo '<img src="'.$img.'" alt="link" border="0" /></a></td>'."\n";
			echo '<td>'.$row['title']."</td>\n";
			echo '<td class="elx_td_center">'.$pub."</td>\n";
			echo "</tr>\n";
			$k = 1 - $k;
			$i++;
		}
		echo "</table>\n";
		echo "</div>\n";
		exit();
	}


	/***********************************/
	/* CREATE A LINK TO A CONTENT ITEM */
	/***********************************/
	private function contentLinker($pluginid, $fn) {
		$elxis = eFactory::getElxis();
		$eLang = eFactory::getLang();

		$categories = $this->getCategoriesTree();

		echo '<div style="margin:0 0 10px 0; padding:0;">'."\n";
		echo $eLang->get('CATEGORY');
		echo ' <select name="elinkctg" id="elinkctg" class="selectbox">'."\n";
		echo '<option value="" selected="selected" id="ctg_0">- '.$eLang->get('NONE').' - </option>'."\n";
		if ($categories) {
			foreach ($categories as $category) {
				echo '<option value="'.$category->seolink.'" id="ctg_'.$category->catid.'">'.$category->treename."</option>\n";
			}
		}
		echo "</select> \n";
		echo '&#160; <a href="javascript:void(null);" onclick="elinkToCategory()" title="'.$eLang->get('INSERT_LINK_CURCTG').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/elink/includes/link.png" alt="link" border="0" /></a> &#160; '."\n";
		echo '&#160; <a href="javascript:void(null);" onclick="elinkBrowseCategory('.$pluginid.', '.$fn.')" title="'.$eLang->get('BROWSE_CTG_ARTS').'">'."\n";
		echo '<img src="'.$elxis->secureBase().'/components/com_content/plugins/elink/includes/folder.png" alt="link" border="0" /></a>'."\n";
		echo "</div>\n";
		echo '<div id="elinkarticles"></div>'."\n";
	}


	/*******************************************/
	/* LINK TO OTHER (THAN CONTENT) COMPONENTS */
	/*******************************************/
	private function otherComponents() {
		$eLang = eFactory::getLang();

		$clinks = $this->getComponentsLinks();
		echo '<div class="elx_tbl_wrapper">'."\n";
		echo '<table cellspacing="0" cellpadding="2" border="1" width="100%" dir="'.$eLang->getinfo('DIR').'" class="elx_tbl_list">'."\n";
		echo "<tr>\n";
		echo '<th class="elx_th_sub" width="200">Component'."</th>\n";
		echo '<th class="elx_th_sub">'.$eLang->get('PAGE')."</th>\n";
		echo "</tr>\n";
		if ($clinks) {
			$k = 1;
			foreach ($clinks as $clink) {
				$name = $clink->name;
				if ($clink->links) {
					foreach ($clink->links as $link) {
						echo '<tr class="elx_tr'.$k.'">'."\n";
						echo '<td>'.$name."</td>\n";
						echo '<td><a href="javascript:void(null);" title="'.$eLang->get('SELECT').'" onclick="addPluginCode(\'&lt;a href=&quot;#elink:'.$link[1].'&quot;&gt;'.$link[0].'&lt;/a&gt;\');" class="plug_link">'.$link[0]."</a></td>\n";
						echo "</tr>\n";
						$k = 1 - $k;					
					}
				}
			}
		} else {
			echo '<tr class="elx_trx">'."\n";
			echo '<td colspan="2" class="elx_td_center">There are no links to display!'."</td>\n";
			echo "</tr>\n";
		}

		echo "</table>\n";
		echo "</div>\n";
	}


	/*************************************/
	/* RETURN TREE OF CONTENT CATEGORIES */
	/*************************************/
	private function getCategoriesTree() {
		$db = eFactory::getDB();

		$sql = "SELECT ".$db->quoteId('catid').", ".$db->quoteId('parent_id').", ".$db->quoteId('title').", ".$db->quoteId('seolink')
		."\n FROM ".$db->quoteId('#__categories')." ORDER BY ".$db->quoteId('parent_id')." ASC, ".$db->quoteId('ordering')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$cats = $stmt->fetchAll(PDO::FETCH_OBJ);
		if (!$cats) { return array(); }

		$tree = eFactory::getElxis()->obj('tree');
		$tree->setOptions(array('itemid' => 'catid', 'parentid' => 'parent_id', 'itemname' => 'title', 'html' => false));
		$rows = $tree->makeTree($cats, 5);
		return $rows;
	}


	/******************************/
	/* GET OTHER COMPONENTS LINKS */
	/******************************/
	private function getComponentsLinks() {
		$db = eFactory::getDB();
		$eLang = eFactory::getLang();
		$eFiles = eFactory::getFiles();

		$sql = "SELECT ".$db->quoteId('name').", ".$db->quoteId('component')
		."\n FROM ".$db->quoteId('#__components')." ORDER BY ".$db->quoteId('name')." ASC";
		$stmt = $db->prepare($sql);
		$stmt->execute();
		$comps = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if (!$comps) { return array(); }

		$rows = array();
		$exclude = array('com_content', 'com_cpanel', 'com_emedia', 'com_emenu', 'com_etranslator', 'com_extmanager', 'com_languages', 'com_wrapper');
		foreach ($comps as $comp) {
			$cmp = $comp['component'];
			if (in_array($cmp, $exclude)) { continue; }
			$base = str_replace('com_', '', $cmp);

			$row = new stdClass;
			$row->component = $comp['component'];
			$row->name = $comp['name'];
			$row->links = array();
			switch ($cmp) {
				case 'com_user':
					$row->links[] = array($eLang->get('USERS_CENTRAL'), 'user:/');
					$row->links[] = array($eLang->get('LOGIN'), 'user:login/');
					$row->links[] = array($eLang->get('REGISTER'), 'user:register.html');
					$row->links[] = array($eLang->get('RECOVER_PASS'), 'user:recover-pwd.html');
					$row->links[] = array($eLang->get('MEMBERS_LIST'), 'user:members/');
					$row->links[] = array($eLang->get('MY_PROFILE'), 'user:members/myprofile.html');
					$row->links[] = array($eLang->get('LOGOUT'), 'user:logout.html');
				break;
				case 'com_search':
						$engs = $eFiles->listFolders('components/com_search/engines/');
						$row->links[] = array($eLang->get('SEARCH'), 'search:/');
						if ($engs) {
							foreach ($engs as $eng) {
								if (!file_exists(ELXIS_PATH.'/components/com_search/engines/'.$eng.'/'.$eng.'.engine.php')) { continue; }
								$title = $eLang->get('SEARCH').' '.ucfirst($eng);
								$row->links[] = array($title, 'search:'.$eng.'.html');
							}
						}
						unset($engs);
				break;
				default:
					$row->links[] = array($comp['name'], $base.':/');
				break;
			}

			$rows[] = $row;
		}


		return $rows;
	}


	/***************************************/
	/* ECHO PAGE HEADERS FOR AJAX REQUESTS */
	/***************************************/
	private function ajaxHeaders($type='text/plain') {
		if(ob_get_length() > 0) { ob_end_clean(); }
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s').'GMT');
		header('Cache-Control: no-cache, must-revalidate');
		header('Pragma: no-cache');
		header('Content-type: '.$type.'; charset=utf-8');
	}


	/********/
	/* HELP */
	/********/
	private function showHelp() {
?>
		<p>Add links in the editor area as anchors pointing to <strong>#elink:</strong> and followed by the <strong>Elxis URI</strong> you want to link to. 
		All links point to index.php (no popups) and are multi-lingual (except if mentioned differently).<br />
		The generic Elxis URI syntax is: <em>language:component:page</em> where language can be ommited for auto-language linking.</p>
		<h3>Examples</h3>
		<p><strong>Link to a content category</strong>: &lt;a href=&quot;<span style="color:green;">#elink:content:sports/</span>&quot;&gt;Sport news&lt;/a&gt;<br />
		<strong>Link to an article</strong>: &lt;a href=&quot;<span style="color:green;">#elink:content:sports/football.html</span>&quot;&gt;Football news&lt;/a&gt;<br />
		<strong>Link to an article (autonomous page)</strong>: &lt;a href=&quot;<span style="color:green;">#elink:content:terms.html</span>&quot;&gt;Terms and conditions&lt;/a&gt;<br />
		<strong>Link to users central (component user)</strong>: &lt;a href=&quot;<span style="color:green;">#elink:user:/</span>&quot;&gt;Users central&lt;/a&gt;<br />
		<strong>Link to users central, forced to the French version of the site</strong>: &lt;a href=&quot;<span style="color:green;">#elink:fr:user:/</span>&quot;&gt;Users central (french)&lt;/a&gt;<br />
		<strong>Link to images search engine (component search)</strong>: &lt;a href=&quot;<span style="color:green;">#elink:search:images.html</span>&quot;&gt;Search for images&lt;/a&gt;<br />
		<strong>Link to site frontpage</strong>: &lt;a href=&quot;<span style="color:green;">#elink:content:/</span>&quot;&gt;Frontpage&lt;/a&gt;</p>
<?php 
	}

}

?>