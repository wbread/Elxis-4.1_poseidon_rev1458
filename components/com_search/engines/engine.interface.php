<?php 
/**
* @version		$Id$
* @package		Elxis
* @subpackage	Component Search
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


interface searchEngine {

	public function __construct($params);
	public function engineInfo();
	public function searchForm();
	public function search();
	public function getTotal();
	public function getLimit();
	public function getLimitStart();
	public function getPage();
	public function getMaxPage();
	public function getOptions();
	public function getResults();
	public function showResults();
}

?>