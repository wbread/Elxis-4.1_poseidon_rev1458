<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class editorMediaView extends emediaView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}

	/***************************************/
	/* MEDIA MANAGER EDITOR USER INTERFACE */
	/***************************************/
	public function editorUI() {
		$elxis = eFactory::getElxis();
?>
		<form id="uploader" method="post">
			<button id="home" name="home" type="button" value="Home" class="emed_btn">&#160;</button>
			<h3></h3>
			<div id="uploadresponse"></div>
			<input id="mode" name="mode" type="hidden" value="add" dir="ltr" /> 
			<input id="currentpath" name="currentpath" type="text" value="" dir="ltr" /> 
			<input	id="newfile" name="newfile" type="file" />
			<button id="upload" name="upload" type="submit" value="Upload" class="emed_btn"></button>
			<button id="newfolder" name="newfolder" type="button" value="New Folder" class="emed_btn"></button>
			<button id="grid" type="button" class="emed_btn ON">&#160;</button>
			<button id="list" type="button" class="emed_btn">&#160;</button>
		</form>
		<div id="splitter">
			<div id="filetree"></div>
			<div id="fileinfo">
				<h3></h3>
			</div>
		</div>
		<ul id="itemOptions" class="contextMenu">
			<li class="select"><a href="#select"></a></li>
			<li class="download"><a href="#download"></a></li>
			<li class="rename"><a href="#rename"></a></li>
			<li class="resize"><a href="#resize"></a></li>
			<li class="compress"><a href="#compress"></a></li>
			<li class="delete separator"><a href="#delete"></a></li>
		</ul>

<?php 
	}

}

?>