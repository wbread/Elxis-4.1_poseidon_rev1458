<?php 
/**
* @version		$Id: date.interface.php 19 2011-01-18 19:13:58Z datahell $
* @package		Elxis
* @subpackage	Date
* @copyright	Copyright (c) 2006-2011 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


interface elxisLocalDate {

	public function __construct();

    public function local_strftime($format, $ts);

    public function local_to_elxis($date, $offset);

    public function elxis_to_local($date, $offset);

}

?>