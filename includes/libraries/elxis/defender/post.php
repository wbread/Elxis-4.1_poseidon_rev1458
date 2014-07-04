<?php 
/**
* @version		$Id: post.php 422 2011-06-21 20:47:54Z datahell $
* @package		Elxis
* @subpackage	Elxis Defender
* @copyright	Copyright (c) 2006-2012 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


/* POST (Last update: 2011-06-21 20:50:10) */
$signatures = array(
	array('inmatch', 'rawpost', '<', 'POST unescaped < POST-08.'),
	array('inmatch', 'rawpost', '>', 'POST unescaped > POST-09.'),
	array('inmatch', 'rawpost', '(', 'POST unescaped ( POST-10.'),
	array('inmatch', 'rawpost', ')', 'POST unescaped ) POST-11.'),
	array('inmatch', 'rawpost', '"', 'POST unescaped " POST-12.'),
	array('inmatch', 'rawpost', '\'', 'POST unescaped \' POST-13.'),
	array('inmatch', 'rawpost', ';','POST unescaped ; POST-14.'),
	array('inmatch', 'rawpost', '#', 'POST unescaped \# POST-57.'),
	array('inmatch', 'rawpost', 'echo\'', 'POST print attempt POST-33.'),
	array('inmatch', 'rawpostsws', 'wgethttp://', 'POST RFI POST-34.'),
	array('inmatch', 'rawpostsws', 'lwp-downloadhttp://', 'POST RFI POST-35.'),
	array('inmatch', 'rawpostsws', 'fetchhttp://', 'POST RFI POST-36.'),
	array('inmatch', 'rawpostsws', 'wgetftp://', 'POST RFI POST-37.'),
	array('inmatch', 'rawpost', 'lwp-download ftp://', 'POST RFI POST-38.'),
	array('inmatch', 'rawpost', 'fetch ftp://', 'POST RFI POST-39.'),
	array('inmatch', 'rawpost', 'wget https://', 'POST RFI POST-40.'),
	array('inmatch', 'rawpost', 'lwp-download https://', 'POST RFI POST-41.'),
	array('inmatch', 'rawpostsws', 'fetchhttps://', 'POST RFI POST-42.'),
	array('inmatch', 'rawpostsws', '<methodCall>', 'POST RFI POST-44.'),
	array('inmatch', 'rawpostsws', '<methodcall>', 'POST RFI POST-45.'),
	array('inmatch', 'rawpostsws', '<methodName>', 'POST RFI POST-46.'),
	array('inmatch', 'rawpostsws', '<methodname>', 'POST RFI POST-47.'),
	array('inmatch', 'rawpostsws', '<params>', 'POST RFI POST-48.'),
	array('inmatch', 'rawpostsws', '<param>', 'POST RFI POST-49.'),
	array('inmatch', 'rawpostsws', '<value>', 'POST RFI POST-50.'),
	array('inmatch', 'rawpostsws', '<name>', 'POST RFI POST-51.'),
	array('inmatch', 'lcpost', '&path=http', 'POST RFI POST-01.'),
	array('inmatch', 'lcpost', '&path=ftp', 'POST RFI POST-02.'),
	array('inmatch', 'lcpost', '&path=gopher', 'POST RFI POST-03.'),
	array('inmatch', 'lcpost', '&path=mms', 'POST RFI POST-04.'),
	array('inmatch', 'lcpost', '&path=rstp', 'POST RFI POST-05.'),
	array('inmatch', 'lcpost', '&path=rtp', 'POST RFI POST-06.'),
	array('inmatch', 'lcpost', '&path=telnet', 'POST RFI POST-07.'),
	array('inmatch', 'rawpostsws', '=ftp:', 'POST RFI POST-02.1.'),
	array('inmatch', 'rawpostsws', '=gopher:', 'POST RFI POST-03.1 .'),
	array('inmatch', 'rawpostsws', '=mms:', 'POST RFI POST-04.1 .'),
	array('inmatch', 'rawpostsws', '=rstp:', 'POST RFI POST-05.1 .'),
	array('inmatch', 'rawpostsws', '=rtp:', 'POST RFI POST-06.1 .'),
	array('inmatch', 'rawpostsws', '=telnet:', 'POST RFI POST-07.1 .'),
	array('inmatch', 'rawpostsws', 'eval(', 'POST EX POST-15. '),
	array('inmatch', 'rawpostsws', 'gzinflate(', 'POST CLOAK POST-16.'),
	array('inmatch', 'rawpostsws', 'thru(', 'POST EX POST-18.'),
	array('inmatch', 'rawpostsws', 'echo(', 'POST EX POST-19.'),
	array('inmatch', 'rawpostsws', 'system(', 'POST EX POST-20.'),
	array('inmatch', 'lcpost', '&modez=', 'POST BOTCOM POST-23.'),
	array('inmatch', 'lcpost', '=shellz', 'POST BOTCOM POST-24.'),
	array('inmatch', 'lcpost', '=scannerz', 'POST BOTCOM POST-25.'),
	array('inmatch', 'lcpost', '=botz', 'POST BOTCOM POST-26.'),
	array('inmatch', 'lcpost', '=psybnc', 'POST BOTCOM POST-27.'),
	array('inmatch', 'rawpostsws', 'include(', 'POST RFI POST-28. '),
	array('inmatch', 'rawpostsws', 'php_uname(', 'POST INJ POST-29. '),
	array('inmatch', 'rawpostsws', 'exec(', 'POST EX POST-30. '),
	array('inmatch', 'rawpost', '=%5Bphp%5D', 'POST EECEX POST-31.0.'),
	array('inmatch', 'rawpost', '=%5bphp%5d', 'POST BBCEX POST-31.1.'),
	array('inmatch', 'lcpost', '=[php]', 'POST BBCEX POST-32.'),
	array('inmatch', 'rawpostsws', 'require(', 'POST RFI POST-52.'),
	array('inmatch', 'rawpost', '\\]', 'POST BBCESC escaping POST-53.'),
	array('inmatch', 'rawpost', '=[\\', 'POST BBCESC POST-54.'),
	array('inmatch', 'rawpost', '%5C]', 'POST BBCESC POST-55.'),
	array('inmatch', 'rawpost', '=[%5C', 'POST BBCESC POST-56.'),
	array('inmatch', 'rawpostsws', '<script', 'POST JS POST-58.'),
	array('inmatch', 'rawpostsws', '</script', 'POST JS POST-59.'),
	array('inmatch', 'rawpostsws', 'unction(', 'POST EX POST-60.'),
	array('inmatch', 'rawpostsws', 'nt(', 'POST EX POST-61.'),
	array('inmatch', 'rawpostsws', 'varstopit=', 'POST INJ POST-62.'),
	array('inmatch', 'rawpostsws', 'ex(', 'POST EX POST-63.'),
	array('inmatch', 'rawpostsws', 'eregi(', 'POST EX POST-64.'),
	array('inmatch', 'rawpostsws', 'if(', 'POST EX POST-65.'),
	array('inmatch', 'rawpostsws', 'exists(', 'POST EX POST-66.'),
	array('inmatch', 'rawpostsws', 'while(', 'POST EX POST-68.'),
	array('inmatch', 'rawpostsws', 'start(', 'POST EX POST-69.'),
	array('inmatch', 'rawpostsws', 'restore(', 'POST EX POST-70.'),
	array('inmatch', 'rawpostsws', 'code(', 'POST CLOAK POST-71.'),
	array('inmatch', 'rawpostsws', 'open(', 'POST EX POST-72.'),
	array('inmatch', 'rawpostsws', 'contents(', 'POST RFI POST-73.'),
	array('inmatch', 'rawpostsws', 'escape(', 'POST CLOAK POST-74.'),
	array('inmatch', 'rawpostsws', 'write(', 'POST RFI POST-75.'),
	array('inmatch', 'rawpostsws', 'once(', 'POST RFI POST-76.'),
	array('inmatch', 'rawpostsws', 'ereg(', 'POST EX POST-77.'),
	array('inmatch', 'rawpostsws', 'rot13(', 'POST CLOAK POST-78.'),
	array('inmatch', 'rawpostsws', 'plode(', 'POST CLOAK POST-79.'),
	array('inmatch', 'rawpostsws', 'ashes(', 'POST CLOAK POST-80.'),
	array('inmatch', 'rawpostsws', 'place(', 'POST CLOAK POST-81.'),
	array('inmatch', 'rawpostsws', 'open(', 'POST RFI POST-82.'),
	array('inmatch', 'rawpostsws', 'puts(', 'POST RFI POST-83.'),
	array('inmatch', 'rawpostsws', 'read(', 'POST RFI POST-84.'),
	array('inmatch', 'rawpostsws', 'file(', 'POST RFI POST-85.'),
	array('inmatch', 'rawpostsws', 'able(', 'POST RFI POST-86.'),
	array('inmatch', 'rawpostsws', 'getc(', 'POST RFI POST-87.'),
	array('inmatch', 'rawpostsws', 'getcsv(', 'POST RFI POST-88.'),
	array('inmatch', 'rawpostsws', 'gets(', 'POST RFI POST-89.'),
	array('inmatch', 'rawpostsws', 'getss(', 'POST RFI POST-90.'),
	array('inmatch', 'rawpost', '8585072011', 'POST CRASH ATTEMPT POST-91.'),
	array('inmatch', 'rawpostsws', '&author_name=%5B', 'Bot Detection.'),
	array('inmatch', 'rawpostsws', '&author_name=[', 'Bot Detection.')
);

?>