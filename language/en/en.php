<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: en-GB (English - Great Britain) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ioannis Sannos ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('en_GB.utf8', 'en_GB.UTF-8', 'en_GB', 'en', 'english', 'england'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //example: Saturday Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //example: Saturday December 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //example: Saturday December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'January';
$_lang['FEBRUARY'] = 'February';
$_lang['MARCH'] = 'March';
$_lang['APRIL'] = 'April';
$_lang['MAY'] = 'May';
$_lang['JUNE'] = 'June';
$_lang['JULY'] = 'July';
$_lang['AUGUST'] = 'August';
$_lang['SEPTEMBER'] = 'September';
$_lang['OCTOBER'] = 'October';
$_lang['NOVEMBER'] = 'November';
$_lang['DECEMBER'] = 'December';
$_lang['JANUARY_SHORT'] = 'Jan';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Apr';
$_lang['MAY_SHORT'] = 'May';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Aug';
$_lang['SEPTEMBER_SHORT'] = 'Sep';
$_lang['OCTOBER_SHORT'] = 'Oct';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dec';
//day names
$_lang['MONDAY'] = 'Monday';
$_lang['THUESDAY'] = 'Tuesday';
$_lang['WEDNESDAY'] = 'Wednesday';
$_lang['THURSDAY'] = 'Thursday';
$_lang['FRIDAY'] = 'Friday';
$_lang['SATURDAY'] = 'Saturday';
$_lang['SUNDAY'] = 'Sunday';
$_lang['MONDAY_SHORT'] = 'Mon';
$_lang['THUESDAY_SHORT'] = 'Tue';
$_lang['WEDNESDAY_SHORT'] = 'Wed';
$_lang['THURSDAY_SHORT'] = 'Thu';
$_lang['FRIDAY_SHORT'] = 'Fri';
$_lang['SATURDAY_SHORT'] = 'Sat';
$_lang['SUNDAY_SHORT'] = 'Sun';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Performance Monitor';
$_lang['ITEM'] = 'Item';
$_lang['INIT_FILE'] = 'Initialization file';
$_lang['EXEC_TIME'] = 'Execution time';
$_lang['DB_QUERIES'] = 'DB queries';
$_lang['ERRORS'] = 'Errors';
$_lang['SIZE'] = 'Size';
$_lang['ENTRIES'] = 'Entries';

/* general */
$_lang['HOME'] = 'Home';
$_lang['YOU_ARE_HERE'] = 'You are here';
$_lang['CATEGORY'] = 'Category';
$_lang['DESCRIPTION'] = 'Description';
$_lang['FILE'] = 'File';
$_lang['IMAGE'] = 'Image';
$_lang['IMAGES'] = 'Images';
$_lang['CONTENT'] = 'Content';
$_lang['DATE'] = 'Date';
$_lang['YES'] = 'Yes';
$_lang['NO'] = 'No';
$_lang['NONE'] = 'None';
$_lang['SELECT'] = 'Select';
$_lang['LOGIN'] = 'Login';
$_lang['LOGOUT'] = 'Logout';
$_lang['WEBSITE'] = 'Web site';
$_lang['SECURITY_CODE'] = 'Security code';
$_lang['RESET'] = 'Reset';
$_lang['SUBMIT'] = 'Submit';
$_lang['REQFIELDEMPTY'] = 'One or more required fields are empty!';
$_lang['FIELDNOEMPTY'] = "%s can not be empty!";
$_lang['FIELDNOACCCHAR'] = "%s contains not acceptable characters!";
$_lang['INVALID_DATE'] = 'Invalid date!';
$_lang['INVALID_NUMBER'] = 'Invalid number!';
$_lang['INVALID_URL'] = 'Invalid URL address!';
$_lang['FIELDSASTERREQ'] = 'Fields with asterisk * are required.';
$_lang['ERROR'] = 'Error';
$_lang['REGARDS'] = 'Regards';
$_lang['NOREPLYMSGINFO'] = 'Please do not reply to this message as it was sent only for informational purposes.';
$_lang['LANGUAGE'] = 'Language';
$_lang['PAGE'] = 'Page';
$_lang['PAGEOF'] = "Page %s of %s";
$_lang['OF'] = 'of';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Displaying %s to %s of %s items";
$_lang['HITS'] = 'Hits';
$_lang['PRINT'] = 'Print';
$_lang['BACK'] = 'Back';
$_lang['PREVIOUS'] = 'Previous';
$_lang['NEXT'] = 'Next';
$_lang['CLOSE'] = 'Close';
$_lang['CLOSE_WINDOW'] = 'Close window';
$_lang['COMMENTS'] = 'Comments';
$_lang['COMMENT'] = 'Comment';
$_lang['PUBLISH'] = 'Publish';
$_lang['DELETE'] = 'Delete';
$_lang['EDIT'] = 'Edit';
$_lang['COPY'] = 'Copy';
$_lang['SEARCH'] = 'Search';
$_lang['PLEASE_WAIT'] = 'Please wait...';
$_lang['ANY'] = 'Any';
$_lang['NEW'] = 'New';
$_lang['ADD'] = 'Add';
$_lang['VIEW'] = 'View';
$_lang['MENU'] = 'Menu';
$_lang['HELP'] = 'Help';
$_lang['TOP'] = 'Top';
$_lang['BOTTOM'] = 'Bottom';
$_lang['LEFT'] = 'Left';
$_lang['RIGHT'] = 'Right';
$_lang['CENTER'] = 'Center';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Enable cache for this item?';
$_lang['YES_FOR_VISITORS'] = 'Yes, for visitors';
$_lang['YES_FOR_ALL'] = 'Yes, for all';
$_lang['CACHE_LIFETIME'] = 'Cache lifetime';
$_lang['CACHE_LIFETIME_D'] = 'Time, in minutes, till the cache is refreshed for this item.';
$_lang['NO_PARAMS'] = 'There are no parameters!';
$_lang['STYLE'] = 'Style';
$_lang['ADVANCED_SETTINGS'] = 'Advanced settings';
$_lang['CSS_SUFFIX'] = 'CSS suffix';
$_lang['CSS_SUFFIX_D'] = 'A suffix that will be added to the module CSS class.';
$_lang['MENU_TYPE'] = 'Menu type';
$_lang['ORIENTATION'] = 'Orientation';
$_lang['SHOW'] = 'Show';
$_lang['HIDE'] = 'Hide';
$_lang['GLOBAL_SETTING'] = 'Global setting';

/* users & authentication */
$_lang['USERNAME'] = 'Username';
$_lang['PASSWORD'] = 'Password';
$_lang['NOAUTHMETHODS'] = 'No authentication methods have been set';
$_lang['AUTHMETHNOTEN'] = 'Authentication method %s is not enabled';
$_lang['PASSTOOSHORT'] = 'Your password is too short to be acceptable';
$_lang['USERNOTFOUND'] = 'User not found';
$_lang['INVALIDUNAME'] = 'Invalid username';
$_lang['INVALIDPASS'] = 'Invalid password';
$_lang['AUTHFAILED'] = 'Authentication failed';
$_lang['YACCBLOCKED'] = 'Your account is blocked';
$_lang['YACCEXPIRED'] = 'Your account has expired';
$_lang['INVUSERGROUP'] = 'Invalid user group';
$_lang['NAME'] = 'Name';
$_lang['FIRSTNAME'] = 'First name';
$_lang['LASTNAME'] = 'Last name';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Invalid e-mail address';
$_lang['ADMINISTRATOR'] = 'Administrator';
$_lang['GUEST'] = 'Guest';
$_lang['EXTERNALUSER'] = 'External user';
$_lang['USER'] = 'User';
$_lang['GROUP'] = 'Group';
$_lang['NOTALLOWACCPAGE'] = 'You are not allowed to access this page!';
$_lang['NOTALLOWACCITEM'] = 'You are not allowed to access this item!';
$_lang['NOTALLOWMANITEM'] = 'You are not allowed to manage this item!';
$_lang['NOTALLOWACTION'] = 'You are not allowed to perform this action!';
$_lang['NEED_HIGHER_ACCESS'] = 'You need a higher access level for this action!';
$_lang['AREYOUSURE'] = 'Are you sure?';

/* highslide */
$_lang['LOADING'] = 'Loading...';
$_lang['CLICK_CANCEL'] = 'Click to cancel';
$_lang['MOVE'] = 'Move';
$_lang['PLAY'] = 'Play';
$_lang['PAUSE'] = 'Pause';
$_lang['RESIZE'] = 'Resize';

/* admin */
$_lang['ADMINISTRATION'] = 'Administration';
$_lang['SETTINGS'] = 'Settings';
$_lang['DATABASE'] = 'Database';
$_lang['ON'] = 'On';
$_lang['OFF'] = 'Off';
$_lang['WARNING'] = 'Warning';
$_lang['SAVE'] = 'Save';
$_lang['APPLY'] = 'Apply';
$_lang['CANCEL'] = 'Cancel';
$_lang['LIMIT'] = 'Limit';
$_lang['ORDERING'] = 'Ordering';
$_lang['NO_RESULTS'] = 'No results found!';
$_lang['CONNECT_ERROR'] = 'Connection Error';
$_lang['DELETE_SEL_ITEMS'] = 'Delete selected items?';
$_lang['TOGGLE_SELECTED'] = 'Toggle selected';
$_lang['NO_ITEMS_SELECTED'] = 'No items selected!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'Action failed!';
$_lang['ACTION_SUCCESS'] = 'Action completed successfully!';
$_lang['NO_IMAGE_UPLOADED'] = 'No image uploaded';
$_lang['NO_FILE_UPLOADED'] = 'No file uploaded';
$_lang['MODULES'] = 'Modules';
$_lang['COMPONENTS'] = 'Components';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Search engines';
$_lang['AUTH_METHODS'] = 'Authentication methods';
$_lang['CONTENT_PLUGINS'] = 'Content plugins';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Published';
$_lang['ACCESS'] = 'Access';
$_lang['ACCESS_LEVEL'] = 'Access level';
$_lang['TITLE'] = 'Title';
$_lang['MOVE_UP'] = 'Move up';
$_lang['MOVE_DOWN'] = 'Move down';
$_lang['WIDTH'] = 'Width';
$_lang['HEIGHT'] = 'Height';
$_lang['ITEM_SAVED'] = 'Item saved';
$_lang['FIRST'] = 'First';
$_lang['LAST'] = 'Last';
$_lang['SUGGESTED'] = 'Suggested';
$_lang['VALIDATE'] = 'Validate';
$_lang['NEVER'] = 'Never';
$_lang['ALL'] = 'All';
$_lang['ALL_GROUPS_LEVEL'] = "All groups of level %s";
$_lang['REQDROPPEDSEC'] = 'Your request dropped for security reasons. Please try again.';
$_lang['PROVIDE_TRANS'] = 'Please provide a translation!';
$_lang['AUTO_TRANS'] = 'Automatic translation';
$_lang['STATISTICS'] = 'Statistics';
$_lang['UPLOAD'] = 'Upload';
$_lang['MORE'] = 'More';

?>