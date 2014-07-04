<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: ar (Arabic) language for component Content
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Illusion Web Solutions ( http://www.illusionwebsolutions.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('ar_AE.utf8', 'ar_AE.UTF-8', 'en_US.utf8', 'en_US.UTF-8', 'en', 'english', 'england'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'يوم-شهر-سنة'; //supported formats: يوم-شهر-سنة, سنة-شهر-يوم, يوم/شهر/سنة, سنة/شهر/يوم
$_lang['DATE_FORMAT_BOX_LONG'] = 'يوم-شهر-سنة H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
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
$_lang['JANUARY'] = 'يناير';
$_lang['FEBRUARY'] = 'فبراير';
$_lang['MARCH'] = 'مارس';
$_lang['APRIL'] = 'أبريل';
$_lang['MAY'] = 'مايو';
$_lang['JUNE'] = 'يونيو';
$_lang['JULY'] = 'يوليو';
$_lang['AUGUST'] = 'أغسطس';
$_lang['SEPTEMBER'] = 'سبتمبر';
$_lang['OCTOBER'] = 'أكتوبر';
$_lang['NOVEMBER'] = 'نوفمبر';
$_lang['DECEMBER'] = 'ديسمبر';
$_lang['JANUARY_SHORT'] = 'يناير';
$_lang['FEBRUARY_SHORT'] = 'فبراير';
$_lang['MARCH_SHORT'] = 'مارس';
$_lang['APRIL_SHORT'] = 'أبريل';
$_lang['MAY_SHORT'] = 'مايو';
$_lang['JUNE_SHORT'] = 'يونيو';
$_lang['JULY_SHORT'] = 'يوليو';
$_lang['AUGUST_SHORT'] = 'أغسطس';
$_lang['SEPTEMBER_SHORT'] = 'سبتمبر';
$_lang['OCTOBER_SHORT'] = 'أكتوبر';
$_lang['NOVEMBER_SHORT'] = 'نوفمبر';
$_lang['DECEMBER_SHORT'] = 'ديسمبر';
//day names
$_lang['MONDAY'] = 'الأثنين';
$_lang['THUESDAY'] = 'الثلاثاء';
$_lang['WEDNESDAY'] = 'الأربعاء';
$_lang['THURSDAY'] = 'الخميس';
$_lang['FRIDAY'] = 'الجمعه';
$_lang['SATURDAY'] = 'السبت';
$_lang['SUNDAY'] = 'الأحد';
$_lang['MONDAY_SHORT'] = 'الأثنين';
$_lang['THUESDAY_SHORT'] = 'الثلاثاء';
$_lang['WEDNESDAY_SHORT'] = 'الأربعاء';
$_lang['THURSDAY_SHORT'] = 'الخميس';
$_lang['FRIDAY_SHORT'] = 'الجمعه';
$_lang['SATURDAY_SHORT'] = 'السبت';
$_lang['SUNDAY_SHORT'] = 'الأحد';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'مراقب أداء Elxis';
$_lang['ITEM'] = 'بند';
$_lang['INIT_FILE'] = 'ملف التهيئة';
$_lang['EXEC_TIME'] = 'وقت التنفيذ';
$_lang['DB_QUERIES'] = 'أستعلامات قاعدة البيانات';
$_lang['ERRORS'] = 'الأخطاء';
$_lang['SIZE'] = 'الحجم';
$_lang['ENTRIES'] = 'المدخلات';

/* general */
$_lang['HOME'] = 'الرئيسية';
$_lang['YOU_ARE_HERE'] = 'أنت هنا‫:‬';
$_lang['CATEGORY'] = 'القسم';
$_lang['DESCRIPTION'] = 'الوصف';
$_lang['FILE'] = 'ملف';
$_lang['IMAGE'] = 'صورة';
$_lang['IMAGES'] = 'الصور';
$_lang['CONTENT'] = 'المحتوى';
$_lang['DATE'] = 'التاريخ';
$_lang['YES'] = 'نعم';
$_lang['NO'] = 'لا';
$_lang['NONE'] = 'لا شيء';
$_lang['SELECT'] = 'حدد';
$_lang['LOGIN'] = 'تسجيل الدخول';
$_lang['LOGOUT'] = 'تسجيل الخروج';
$_lang['WEBSITE'] = 'الموقع الألكترونى';
$_lang['SECURITY_CODE'] = 'رمز الحماية';
$_lang['RESET'] = 'إعادة تعيين';
$_lang['SUBMIT'] = 'أرسال';
$_lang['REQFIELDEMPTY'] = 'واحد أو أكثر من الحقول المطلوبة فارغة!';
$_lang['FIELDNOEMPTY'] = "%s لا يمكن أن تكون فارغة";
$_lang['FIELDNOACCCHAR'] = "%s تحتوى على حروف أو مدخلات غير مقبولة";
$_lang['INVALID_DATE'] = 'تاريخ غير صالحة!';
$_lang['INVALID_NUMBER'] = 'عدد غير صالح!';
$_lang['INVALID_URL'] = 'URL غير صالح‫!‬';
$_lang['FIELDSASTERREQ'] = 'الحقول المميزة بعلامة ‫*‬ مطلوبة';
$_lang['ERROR'] = 'خطأ';
$_lang['REGARDS'] = 'فيما يتعلق';
$_lang['NOREPLYMSGINFO'] = 'برجاء عدم الرد على هذه الرسالة لانها أرسلت لأعلامك بمحتواها فقط‫.‬';
$_lang['LANGUAGE'] = 'اللغة';
$_lang['PAGE'] = 'صفحة';
$_lang['PAGEOF'] = "الصفحة %s من %s";
$_lang['OF'] = 'من';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "عرض %s الى %s من %s بند";
$_lang['HITS'] = 'مشاهدات';
$_lang['PRINT'] = 'طباعة';
$_lang['BACK'] = 'عودة';
$_lang['PREVIOUS'] = 'السابق';
$_lang['NEXT'] = 'التالى';
$_lang['CLOSE'] = 'إغلاق';
$_lang['CLOSE_WINDOW'] = 'إغلق هذه النافذة';
$_lang['COMMENTS'] = 'تعليقات';
$_lang['COMMENT'] = 'تعليق';
$_lang['PUBLISH'] = 'نشر';
$_lang['DELETE'] = 'مسح';
$_lang['EDIT'] = 'تعديل';
$_lang['COPY'] = 'نسخ';
$_lang['SEARCH'] = 'بحث';
$_lang['PLEASE_WAIT'] = 'برجاء الأنتظار ‫……‬';
$_lang['ANY'] = 'أى';
$_lang['NEW'] = 'جديد';
$_lang['ADD'] = 'أضف';
$_lang['VIEW'] = 'مشاهدة';
$_lang['MENU'] = 'قائمة';
$_lang['HELP'] = 'مساعدة';
$_lang['TOP'] = 'أعلى';
$_lang['BOTTOM'] = 'أسفل';
$_lang['LEFT'] = 'شمال';
$_lang['RIGHT'] = 'يمين';
$_lang['CENTER'] = 'منتصف';

/* xml */
$_lang['CACHE'] = 'الملفات المؤقتة ( كاش )‬';
$_lang['ENABLE_CACHE_D'] = 'تفعيل الملفات المؤقتة لهذا البند؟';
$_lang['YES_FOR_VISITORS'] = 'نعم‫,‬ للزوار';
$_lang['YES_FOR_ALL'] = 'نعم‫,‬ للكل';
$_lang['CACHE_LIFETIME'] = 'مدة الأحتفاظ بالملفات فى الذاكرة المؤقته ‫(‬ كاش ‫)‬';
$_lang['CACHE_LIFETIME_D'] = 'الوقت‫,‬ بالدقائق حتى يتم تحديث ذاكرة التخزين المؤقت لهذا البند.';
$_lang['NO_PARAMS'] = 'لا توجد معلمات!';
$_lang['STYLE'] = 'ستايل';
$_lang['ADVANCED_SETTINGS'] = 'إعدادات متقدمة';
$_lang['CSS_SUFFIX'] = 'CSS suffix';
$_lang['CSS_SUFFIX_D'] = 'الـ suffix التى سيتم أضافتها الى الـ CSS class الخاصة بالـmodule';
$_lang['MENU_TYPE'] = 'نوع القائمة';
$_lang['ORIENTATION'] = 'اتجاه';
$_lang['SHOW'] = 'أظهار';
$_lang['HIDE'] = 'أخفاء';
$_lang['GLOBAL_SETTING'] = 'الأعدادات العامة';

/* users & authentication */
$_lang['USERNAME'] = 'أسم المستخدم';
$_lang['PASSWORD'] = 'كلمة السر';
$_lang['NOAUTHMETHODS'] = 'لم يتم تحديد أسلوب المصادقة authentication methods';
$_lang['AUTHMETHNOTEN'] = 'أسلوب المصادقة ‫-Authentication method-‬ %s غير فعال‫.‬';
$_lang['PASSTOOSHORT'] = 'كلمة السر قصيرة جداً لقبولها';
$_lang['USERNOTFOUND'] = 'لم يتم العثور على المستخدم';
$_lang['INVALIDUNAME'] = 'أسم المستخدم غير صالح';
$_lang['INVALIDPASS'] = 'كلمة السر غير صالحة';
$_lang['AUTHFAILED'] = 'فشل المصادقة ‫-Authentication-‬';
$_lang['YACCBLOCKED'] = 'تم حظر حسابك';
$_lang['YACCEXPIRED'] = 'انتهت صلاحية حسابك';
$_lang['INVUSERGROUP'] = 'مجموعة المستخدمين غير صالحة';
$_lang['NAME'] = 'الأسم';
$_lang['FIRSTNAME'] = 'الأسم الأول';
$_lang['LASTNAME'] = 'الأسم الأخير';
$_lang['EMAIL'] = 'البريد الألكترونى';
$_lang['INVALIDEMAIL'] = 'بريد الكترونى غير صالح';
$_lang['ADMINISTRATOR'] = 'مدير';
$_lang['GUEST'] = 'زائر';
$_lang['EXTERNALUSER'] = 'مستخدم خارجى';
$_lang['USER'] = 'مستخدم';
$_lang['GROUP'] = 'أسم المجموعة';
$_lang['NOTALLOWACCPAGE'] = 'لا يسمح لك بالوصول إلى هذه الصفحة!';
$_lang['NOTALLOWACCITEM'] = 'لا يسمح لك بالوصول إلى هذا البند!';
$_lang['NOTALLOWMANITEM'] = 'لا يسمح لك لإدارة هذا البند!';
$_lang['NOTALLOWACTION'] = 'لا يسمح لك لتنفيذ هذا الإجراء!';
$_lang['NEED_HIGHER_ACCESS'] = 'تحتاج مستوى أعلى من الصلاحيات لهذا العمل!';
$_lang['AREYOUSURE'] = 'هل أنت واثق ؟';

/* highslide */
$_lang['LOADING'] = 'جارى التحميل ‫……‬';
$_lang['CLICK_CANCEL'] = 'أضغط للألغاء';
$_lang['MOVE'] = 'نقل';
$_lang['PLAY'] = 'تشغيل';
$_lang['PAUSE'] = 'أيقاف';
$_lang['RESIZE'] = 'تغيير حجم';

/* admin */
$_lang['ADMINISTRATION'] = 'الإدارة';
$_lang['SETTINGS'] = 'أعدادات';
$_lang['DATABASE'] = 'قاعدة بيانات';
$_lang['ON'] = 'نشط';
$_lang['OFF'] = 'غير نشط';
$_lang['WARNING'] = 'تحذير';
$_lang['SAVE'] = 'حفظ';
$_lang['APPLY'] = 'تطبيق';
$_lang['CANCEL'] = 'إلغاء';
$_lang['LIMIT'] = 'قصر';
$_lang['ORDERING'] = 'الترتيب';
$_lang['NO_RESULTS'] = 'لم يتم العثور على نتائج‫!‬';
$_lang['CONNECT_ERROR'] = 'خطأ فى الأتصال';
$_lang['DELETE_SEL_ITEMS'] = 'مسح البنود المحددة ؟';
$_lang['TOGGLE_SELECTED'] = 'تبديل الأختيارات';
$_lang['NO_ITEMS_SELECTED'] = 'لم يتم تحديد أى بند';
$_lang['ID'] = 'الرقم المعرف';
$_lang['ACTION_FAILED'] = 'فشل فى الأجراء';
$_lang['ACTION_SUCCESS'] = 'تم أنتهاء الأجراء بنجاح';
$_lang['NO_IMAGE_UPLOADED'] = 'لم يتم رفع أى صورة';
$_lang['NO_FILE_UPLOADED'] = 'لم يتم رفع أى ملف';
$_lang['MODULES'] = 'الوحدات';
$_lang['COMPONENTS'] = 'البرامج الأساسية';
$_lang['TEMPLATES'] = 'القوالب';
$_lang['SEARCH_ENGINES'] = 'محركات البحث';
$_lang['AUTH_METHODS'] = 'أساليب المصادقة';
$_lang['CONTENT_PLUGINS'] = 'أضافات المحتوى';
$_lang['PLUGINS'] = 'أضافات';
$_lang['PUBLISHED'] = 'تم نشرها';
$_lang['ACCESS'] = 'الولوج';
$_lang['ACCESS_LEVEL'] = 'مستوى الولوج';
$_lang['TITLE'] = 'العنوان';
$_lang['MOVE_UP'] = 'تحريك لاعلى';
$_lang['MOVE_DOWN'] = 'تحريك لاسفل';
$_lang['WIDTH'] = 'عرض';
$_lang['HEIGHT'] = 'أرتفاع';
$_lang['ITEM_SAVED'] = 'تم حفظ البند';
$_lang['FIRST'] = 'الأول';
$_lang['LAST'] = 'الأخير';
$_lang['SUGGESTED'] = 'مقترح';
$_lang['VALIDATE'] = 'التحقق من صحة';
$_lang['NEVER'] = 'أبداً';
$_lang['ALL'] = 'كل';
$_lang['ALL_GROUPS_LEVEL'] = "كل مجموعات المستوى %s";
$_lang['REQDROPPEDSEC'] = 'لم يتم طلبك نظراً لاسباب أمنية‫.‬ يرجى المحاولة مرة أخرى.';
$_lang['PROVIDE_TRANS'] = 'يرجى تقديم ترجمة!';
$_lang['AUTO_TRANS'] = 'الترجمة الآلية';
$_lang['STATISTICS'] = 'إحصائيات';
$_lang['UPLOAD'] = 'رفع';
$_lang['MORE'] = 'المزيد';

?>