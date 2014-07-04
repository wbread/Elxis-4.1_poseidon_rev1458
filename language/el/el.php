<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: el-GR (Hellenic - Greece) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ioannis Sannos ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('el_GR.utf8', 'el_GR.UTF-8', 'el.UTF8', 'el.UTF-8', 'en_GB', 'en', 'english', 'england');

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%d %b %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%d %B %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%d %b %Y %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%d %B %Y %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%d %B %Y %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %d %b %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %d %b %Y"; //example: Satur%day Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %d %B %Y"; //example: Satur%day December 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %d %B %Y %H:%M"; //example: Satur%day December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %d %B %Y %H:%M:%S"; //example: Satur%day December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %d %B %Y %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %d %B %Y %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = '.';
$_lang['DECIMALS_SEP'] = ',';
//month names
$_lang['JANUARY'] = 'Ιανουάριος';
$_lang['FEBRUARY'] = 'Φεβρουάριος';
$_lang['MARCH'] = 'Μάρτιος';
$_lang['APRIL'] = 'Απρίλιος';
$_lang['MAY'] = 'Μάϊος';
$_lang['JUNE'] = 'Ιούνιος';
$_lang['JULY'] = 'Ιούλιος';
$_lang['AUGUST'] = 'Αύγουστος';
$_lang['SEPTEMBER'] = 'Σεπτέμβριος';
$_lang['OCTOBER'] = 'Οκτώβριος';
$_lang['NOVEMBER'] = 'Νοέμβριος';
$_lang['DECEMBER'] = 'Δεκέμβριος';
$_lang['JANUARY_SHORT'] = 'Ιαν';
$_lang['FEBRUARY_SHORT'] = 'Φεβ';
$_lang['MARCH_SHORT'] = 'Μαρ';
$_lang['APRIL_SHORT'] = 'Απρ';
$_lang['MAY_SHORT'] = 'Μαϊ';
$_lang['JUNE_SHORT'] = 'Ιουν';
$_lang['JULY_SHORT'] = 'Ιουλ';
$_lang['AUGUST_SHORT'] = 'Αυγ';
$_lang['SEPTEMBER_SHORT'] = 'Σεπ';
$_lang['OCTOBER_SHORT'] = 'Οκτ';
$_lang['NOVEMBER_SHORT'] = 'Νοε';
$_lang['DECEMBER_SHORT'] = 'Δεκ';
//day names
$_lang['MONDAY'] = 'Δευτέρα';
$_lang['THUESDAY'] = 'Τρίτη';
$_lang['WEDNESDAY'] = 'Τετάρτη';
$_lang['THURSDAY'] = 'Πέμπτη';
$_lang['FRIDAY'] = 'Παρασκευή';
$_lang['SATURDAY'] = 'Σάββατο';
$_lang['SUNDAY'] = 'Κυριακή';
$_lang['MONDAY_SHORT'] = 'Δευ';
$_lang['THUESDAY_SHORT'] = 'Τρί';
$_lang['WEDNESDAY_SHORT'] = 'Τετ';
$_lang['THURSDAY_SHORT'] = 'Πεμ';
$_lang['FRIDAY_SHORT'] = 'Παρ';
$_lang['SATURDAY_SHORT'] = 'Σαβ';
$_lang['SUNDAY_SHORT'] = 'Κυρ';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Οθόνη Απόδοσης Elxis';
$_lang['ITEM'] = 'Αντικείμενο';
$_lang['INIT_FILE'] = 'Αρχείο εκκίνησης';
$_lang['EXEC_TIME'] = 'Χρόνος εκτέλεσης';
$_lang['DB_QUERIES'] = 'Επερωτήματα ΒΔ';
$_lang['ERRORS'] = 'Σφάλματα';
$_lang['SIZE'] = 'Μέγεθος';
$_lang['ENTRIES'] = 'Εγγραφές';

/* general */
$_lang['HOME'] = 'Αρχική';
$_lang['YOU_ARE_HERE'] = 'Είστε εδώ';
$_lang['CATEGORY'] = 'Κατηγορία';
$_lang['DESCRIPTION'] = 'Περιγραφή';
$_lang['FILE'] = 'Αρχείο';
$_lang['IMAGE'] = 'Εικόνα';
$_lang['IMAGES'] = 'Εικόνες';
$_lang['CONTENT'] = 'Περιεχόμενο';
$_lang['DATE'] = 'Ημερομηνία';
$_lang['YES'] = 'Ναι';
$_lang['NO'] = 'Όχι';
$_lang['NONE'] = 'Καμία';
$_lang['SELECT'] = 'Επιλέξτε';
$_lang['LOGIN'] = 'Σύνδεση';
$_lang['LOGOUT'] = 'Αποσύνδεση';
$_lang['WEBSITE'] = 'Ιστότοπος';
$_lang['SECURITY_CODE'] = 'Κωδικός ασφαλείας';
$_lang['RESET'] = 'Αρχικοποίηση';
$_lang['SUBMIT'] = 'Υποβολή';
$_lang['REQFIELDEMPTY'] = 'Ένα ή περισσότερα υποχρεωτικά πεδία είναι κενά!';
$_lang['FIELDNOEMPTY'] = "Το πεδίο %s δεν μπορεί να είναι κενό!";
$_lang['FIELDNOACCCHAR'] = "Το πεδίο %s περιέχει μη αποδεκτούς χαρακτήρες!";
$_lang['INVALID_DATE'] = 'Άκυρη ημερομηνία!';
$_lang['INVALID_NUMBER'] = 'Άκυρος αριθμός!';
$_lang['INVALID_URL'] = 'Άκυρη διεύθυνση URL!';
$_lang['FIELDSASTERREQ'] = 'Τα πεδία με αστερίσκο * είναι υποχρεωτικά.';
$_lang['ERROR'] = 'Σφάλμα';
$_lang['REGARDS'] = 'Χαιρετισμοί';
$_lang['NOREPLYMSGINFO'] = 'Παρακαλώ μην απαντάτε σε αυτό το μήνυμα καθώς εξυπηρετεί μόνο πληροφοριακούς σκοπούς.';
$_lang['LANGUAGE'] = 'Γλώσσα';
$_lang['PAGE'] = 'Σελίδα';
$_lang['PAGEOF'] = "Σελίδα %s από %s";
$_lang['OF'] = 'από';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Εμφάνιση %s ως %s από %s αντικείμενα";
$_lang['HITS'] = 'Προβολές';
$_lang['PRINT'] = 'Εκτύπωση';
$_lang['BACK'] = 'Επιστροφή';
$_lang['PREVIOUS'] = 'Προηγούμενο';
$_lang['NEXT'] = 'Επόμενο';
$_lang['CLOSE'] = 'Κλείσιμο';
$_lang['CLOSE_WINDOW'] = 'Κλείσιμο παραθύρου';
$_lang['COMMENTS'] = 'Σχόλια';
$_lang['COMMENT'] = 'Σχόλιο';
$_lang['PUBLISH'] = 'Δημοσίευση';
$_lang['DELETE'] = 'Διαγραφή';
$_lang['EDIT'] = 'Επεξεργασία';
$_lang['COPY'] = 'Αντιγραφή';
$_lang['SEARCH'] = 'Αναζήτηση';
$_lang['PLEASE_WAIT'] = 'Παρακαλώ περιμένετε...';
$_lang['ANY'] = 'Οποιοδήποτε';
$_lang['NEW'] = 'Νέο';
$_lang['ADD'] = 'Προσθήκη';
$_lang['VIEW'] = 'Προβολή';
$_lang['MENU'] = 'Μενού';
$_lang['HELP'] = 'Βοήθεια';
$_lang['TOP'] = 'Κορυφή';
$_lang['BOTTOM'] = 'Πυθμένας';
$_lang['LEFT'] = 'Αριστερά';
$_lang['RIGHT'] = 'Δεξιά';
$_lang['CENTER'] = 'Κέντρο';

/* xml */
$_lang['CACHE'] = 'Προσωρινή μνήμη';
$_lang['ENABLE_CACHE_D'] = 'Ενεργοποίηση προσωρινής μνήμης σε αυτό το αντικείμενο;';
$_lang['YES_FOR_VISITORS'] = 'Ναι, για επισκέπτες';
$_lang['YES_FOR_ALL'] = 'Ναι, για όλους';
$_lang['CACHE_LIFETIME'] = 'Διάρκεια Προσ. Μνήμης';
$_lang['CACHE_LIFETIME_D'] = 'Χρόνος, σε λεπτά, έως ότου η προσωρινή μνήμη αυτού του αντικειμένου ανανεωθεί.';
$_lang['NO_PARAMS'] = 'Δεν υπάρχουν παράμετροι!';
$_lang['STYLE'] = 'Στυλ';
$_lang['ADVANCED_SETTINGS'] = 'Προχωρημένες ρυθμίσεις';
$_lang['CSS_SUFFIX'] = 'Επίθεμα CSS';
$_lang['CSS_SUFFIX_D'] = 'Ένα επίθεμα που θα προστεθεί στην CSS κλάση του module.';
$_lang['MENU_TYPE'] = 'Τύπος μενού';
$_lang['ORIENTATION'] = 'Προσανατολισμός';
$_lang['SHOW'] = 'Εμφάνιση';
$_lang['HIDE'] = 'Απόκρυψη';
$_lang['GLOBAL_SETTING'] = 'Γενική ρύθμιση';

/* users & authentication */
$_lang['USERNAME'] = 'Ψευδώνυμο';
$_lang['PASSWORD'] = 'Κωδικός πρόσβασης';
$_lang['NOAUTHMETHODS'] = 'Δεν ορίστηκαν μέθοδοι πιστοποίησης';
$_lang['AUTHMETHNOTEN'] = 'Η μέθοδος πιστοποίησης %s δεν είναι ενεργή';
$_lang['PASSTOOSHORT'] = 'Ο κωδικός σας είναι πολύ μικρός για να γίνει αποδεκτός';
$_lang['USERNOTFOUND'] = 'Ο χρήστης δεν βρέθηκε';
$_lang['INVALIDUNAME'] = 'Άκυρο ψευδώνυμο';
$_lang['INVALIDPASS'] = 'Άκυρος κωδικός';
$_lang['AUTHFAILED'] = 'Η πιστοποίηση απέτυχε';
$_lang['YACCBLOCKED'] = 'Ο λογαριασμός σας είναι φραγμένος';
$_lang['YACCEXPIRED'] = 'Ο λογαριασμός σας έχει λήξει';
$_lang['INVUSERGROUP'] = 'Άκυρη ομάδα χρήστη';
$_lang['NAME'] = 'Όνομα';
$_lang['FIRSTNAME'] = 'Όνομα';
$_lang['LASTNAME'] = 'Επίθετο';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Άκυρη διεύθυνση ηλεκτρονικού ταχυδρομίου';
$_lang['ADMINISTRATOR'] = 'Διαχειριστής';
$_lang['GUEST'] = 'Επισκέπτης';
$_lang['EXTERNALUSER'] = 'Εξωτερικός χρήστης';
$_lang['USER'] = 'Χρήστης';
$_lang['GROUP'] = 'Ομάδα';
$_lang['NOTALLOWACCPAGE'] = 'Δεν σας επιτρέπεται η πρόσβαση σε αυτή τη σελίδα!';
$_lang['NOTALLOWACCITEM'] = 'Δεν σας επιτρέπεται η πρόσβαση σε αυτό το αντικείμενο!';
$_lang['NOTALLOWMANITEM'] = 'Δεν σας επιτρέπεται η διαχείριση αυτού του αντικειμένου!';
$_lang['NOTALLOWACTION'] = 'Δεν σας επιτρέπεται η εκτέλεση αυτής της ενέργειας!';
$_lang['NEED_HIGHER_ACCESS'] = 'Απαιτείται υψηλότερο επίπεδο πρόσβασης για αυτή την ενέργεια!';
$_lang['AREYOUSURE'] = 'Είσαι σίγουρος;';

/* highslide */
$_lang['LOADING'] = 'Φορτώνει...';
$_lang['CLICK_CANCEL'] = 'Πατήστε για ακύρωση';
$_lang['MOVE'] = 'Μετακίνηση';
$_lang['PLAY'] = 'Αναπαραγωγή';
$_lang['PAUSE'] = 'Παύση';
$_lang['RESIZE'] = 'Αναδιάσταση';

/* admin */
$_lang['ADMINISTRATION'] = 'Διαχείριση';
$_lang['SETTINGS'] = 'Ρυθμίσεις';
$_lang['DATABASE'] = 'Βάση δεδομένων';
$_lang['ON'] = 'Εντός';
$_lang['OFF'] = 'Εκτός';
$_lang['WARNING'] = 'Προειδοποίηση';
$_lang['SAVE'] = 'Αποθήκευση';
$_lang['APPLY'] = 'Εφαρμογή';
$_lang['CANCEL'] = 'Ακύρωση';
$_lang['LIMIT'] = 'Όριο';
$_lang['ORDERING'] = 'Κατάταξη';
$_lang['NO_RESULTS'] = 'Δεν βρέθηκαν αποτελέσματα!';
$_lang['CONNECT_ERROR'] = 'Σφάλμα σύνδεσης';
$_lang['DELETE_SEL_ITEMS'] = 'Διαγραφή επιλεγμένων αντικειμένων;';
$_lang['TOGGLE_SELECTED'] = 'Αλλαγή επιλεγμένων';
$_lang['NO_ITEMS_SELECTED'] = 'Δεν επιλέχθηκαν αντικείμενα!';
$_lang['ID'] = 'Κωδ';
$_lang['ACTION_FAILED'] = 'Η ενέργεια απέτυχε!';
$_lang['ACTION_SUCCESS'] = 'Η ενέργεια ολοκληρώθηκε με επιτυχία!';
$_lang['NO_IMAGE_UPLOADED'] = 'Δεν στάλθηκε εικόνα';
$_lang['NO_FILE_UPLOADED'] = 'Δεν στάλθηκε αρχείο';
$_lang['MODULES'] = 'Modules';
$_lang['COMPONENTS'] = 'Components';
$_lang['TEMPLATES'] = 'Θέματα';
$_lang['SEARCH_ENGINES'] = 'Μηχανές αναζήτησης';
$_lang['AUTH_METHODS'] = 'Μέθοδοι πιστοποίησης';
$_lang['CONTENT_PLUGINS'] = 'Πρόσθετα περιεχομένου';
$_lang['PLUGINS'] = 'Πρόσθετα';
$_lang['PUBLISHED'] = 'Δημοσιευμένο';
$_lang['ACCESS'] = 'Πρόσβαση';
$_lang['ACCESS_LEVEL'] = 'Επίπεδο πρόσβασης';
$_lang['TITLE'] = 'Τίτλος';
$_lang['MOVE_UP'] = 'Μετακίνηση πάνω';
$_lang['MOVE_DOWN'] = 'Μετακίνηση κάτω';
$_lang['WIDTH'] = 'Πλάτος';
$_lang['HEIGHT'] = 'Ύψος';
$_lang['ITEM_SAVED'] = 'Το αντικείμενο αποθηκεύτηκε';
$_lang['FIRST'] = 'Πρώτο';
$_lang['LAST'] = 'Τελευταίο';
$_lang['SUGGESTED'] = 'Προτεινόμενος';
$_lang['VALIDATE'] = 'Επικύρωση';
$_lang['NEVER'] = 'Ποτέ';
$_lang['ALL'] = 'Όλο';
$_lang['ALL_GROUPS_LEVEL'] = "Όλες οι ομάδες του επιπέδου %s";
$_lang['REQDROPPEDSEC'] = 'Η αίτησή σας απερρίφθη για λόγους ασφαλείας. Παρακαλώ ξαναπροσπαθήστε.';
$_lang['PROVIDE_TRANS'] = 'Παρακαλώ δώστε μία μετάφραση!';
$_lang['AUTO_TRANS'] = 'Αυτόματη μετάφραση';
$_lang['STATISTICS'] = 'Στατιστικά';
$_lang['UPLOAD'] = 'Αποστολή';
$_lang['MORE'] = 'Περισσότερα';

?>