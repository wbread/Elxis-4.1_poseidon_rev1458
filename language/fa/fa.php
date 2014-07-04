<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: fa-IR (Persian - Iran) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Farhad Sakhaei ( http://parsmizban.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('fa_IR.utf8', 'fa_IR.UTF-8', 'fa_IR', 'fa', 'persian', 'iran'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'Y-m-d'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'Y-m-d H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%Y/%m/%d"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%d %B %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%d %b %Y، %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%d %B %Y، %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%d %B %Y %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a،%d %b %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A،%d %b %Y"; //example: Saturday Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A،%d %B %Y"; //example: Saturday December 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %d %B %Y، %H:%M"; //example: Saturday December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %d %B %Y، %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %d %B %Y، %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %d %B %Y، %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '/';
//month names
$_lang['JANUARY'] = 'فروردین';
$_lang['FEBRUARY'] = 'اردیبهشت';
$_lang['MARCH'] = 'خرداد';
$_lang['APRIL'] = 'تیر';
$_lang['MAY'] = 'مرداد';
$_lang['JUNE'] = 'شهریور';
$_lang['JULY'] = 'مهر';
$_lang['AUGUST'] = 'آبان';
$_lang['SEPTEMBER'] = 'آذر';
$_lang['OCTOBER'] = 'دی';
$_lang['NOVEMBER'] = 'بهمن';
$_lang['DECEMBER'] = 'اسفند';
$_lang['JANUARY_SHORT'] = 'فروردین';
$_lang['FEBRUARY_SHORT'] = 'اردیبهشت';
$_lang['MARCH_SHORT'] = 'خرداد';
$_lang['APRIL_SHORT'] = 'تیر';
$_lang['MAY_SHORT'] = 'مرداد';
$_lang['JUNE_SHORT'] = 'شهریور';
$_lang['JULY_SHORT'] = 'مهر';
$_lang['AUGUST_SHORT'] = 'آبان';
$_lang['SEPTEMBER_SHORT'] = 'آذر';
$_lang['OCTOBER_SHORT'] = 'دی';
$_lang['NOVEMBER_SHORT'] = 'بهمن';
$_lang['DECEMBER_SHORT'] = 'اسفند';
//day names
$_lang['MONDAY'] = 'دوشنبه';
$_lang['THUESDAY'] = 'سه شنبه';
$_lang['WEDNESDAY'] = 'چهارشنبه';
$_lang['THURSDAY'] = 'پنج شنبه';
$_lang['FRIDAY'] = 'جمعه';
$_lang['SATURDAY'] = 'شنبه';
$_lang['SUNDAY'] = 'یکشنبه';
$_lang['MONDAY_SHORT'] = 'دوشنبه';
$_lang['THUESDAY_SHORT'] = 'سه شنبه';
$_lang['WEDNESDAY_SHORT'] = 'چهارشنبه';
$_lang['THURSDAY_SHORT'] = 'پنج شنبه';
$_lang['FRIDAY_SHORT'] = 'جمعه';
$_lang['SATURDAY_SHORT'] = 'شنبه';
$_lang['SUNDAY_SHORT'] = 'یکشنبه';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'نظارت بر عملکرد';
$_lang['ITEM'] = 'آیتم';
$_lang['INIT_FILE'] = 'فایل اجرای اولیه';
$_lang['EXEC_TIME'] = 'زمان اجرا';
$_lang['DB_QUERIES'] = 'درخواست های پایگاه دادهDB queries';
$_lang['ERRORS'] = 'خطا';
$_lang['SIZE'] = 'اندازه';
$_lang['ENTRIES'] = 'موجودی';

/* general */
$_lang['HOME'] = 'خانه';
$_lang['YOU_ARE_HERE'] = 'شما در اینجا هستید:';
$_lang['CATEGORY'] = 'دسته';
$_lang['DESCRIPTION'] = 'توضیح';
$_lang['FILE'] = 'فایل';
$_lang['IMAGE'] = 'تصویر';
$_lang['IMAGES'] = 'تصویر';
$_lang['CONTENT'] = 'محتوا';
$_lang['DATE'] = 'تاریخ';
$_lang['YES'] = 'بله';
$_lang['NO'] = 'خیر';
$_lang['NONE'] = 'هیچ کدام';
$_lang['SELECT'] = 'انتخاب';
$_lang['LOGIN'] = 'ورود';
$_lang['LOGOUT'] = 'خروج';
$_lang['WEBSITE'] = 'وب سایت';
$_lang['SECURITY_CODE'] = 'کد امنیتی';
$_lang['RESET'] = 'تنظیم مجدد';
$_lang['SUBMIT'] = 'ارسال';
$_lang['REQFIELDEMPTY'] = 'یک یا چند فیلد اجباری ، خالی است!';
$_lang['FIELDNOEMPTY'] = "%s نمی تواند خالی باشد!";
$_lang['FIELDNOACCCHAR'] = "%s حاوی کاراکتر های غیر قابل پذیرش می باشد!";
$_lang['INVALID_DATE'] = 'تاریخ نامعتبر!';
$_lang['INVALID_NUMBER'] = 'عدد نامعتبر!';
$_lang['INVALID_URL'] = 'آدرس URL نامعتبر!';
$_lang['FIELDSASTERREQ'] = 'فیلد های ستاره دار * اجباری هستند.';
$_lang['ERROR'] = 'خطا';
$_lang['REGARDS'] = 'با احترام';
$_lang['NOREPLYMSGINFO'] = 'لطفا به این پیغام پاسخ ندهید زیرا تنها جهت اطلاع رسانی ارسال شده است.';
$_lang['LANGUAGE'] = 'زبان';
$_lang['PAGE'] = 'صفحه';
$_lang['PAGEOF'] = "صفحه %s از %s";
$_lang['OF'] = 'از';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "نمایش %s تا %s از %s آیتم";
$_lang['HITS'] = 'بازدید';
$_lang['PRINT'] = 'چاپ';
$_lang['BACK'] = 'بازگشت';
$_lang['PREVIOUS'] = 'قبلی';
$_lang['NEXT'] = 'بعدی';
$_lang['CLOSE'] = 'بستن';
$_lang['CLOSE_WINDOW'] = 'بستن پنجره';
$_lang['COMMENTS'] = 'نظر';
$_lang['COMMENT'] = 'نظر';
$_lang['PUBLISH'] = 'انتشار';
$_lang['DELETE'] = 'حذف';
$_lang['EDIT'] = 'ویرایش';
$_lang['COPY'] = 'کپی';
$_lang['SEARCH'] = 'جستجو';
$_lang['PLEASE_WAIT'] = 'لطفا منتظر بمانید...';
$_lang['ANY'] = 'هر';
$_lang['NEW'] = 'جدید';
$_lang['ADD'] = 'افزودن';
$_lang['VIEW'] = 'نمایش';
$_lang['MENU'] = 'منو';
$_lang['HELP'] = 'راهنمایی';
$_lang['TOP'] = 'بالا';
$_lang['BOTTOM'] = 'پایین';
$_lang['LEFT'] = 'چپ';
$_lang['RIGHT'] = 'راست';
$_lang['CENTER'] = 'وسط';

/* xml */
$_lang['CACHE'] = 'کش (ذخیره گاه)';
$_lang['ENABLE_CACHE_D'] = 'فعال سازی کش (ذخیره گاه) برای این آیتم؟';
$_lang['YES_FOR_VISITORS'] = 'بله، برای بازدید کنندگان';
$_lang['YES_FOR_ALL'] = 'بله، برای همه';
$_lang['CACHE_LIFETIME'] = 'زمان ماندگاری کش (ذخیره گاه)';
$_lang['CACHE_LIFETIME_D'] = 'زمان، به دقیقه، تا هنگامی که کش برای این آیتم تازه سازی شود.';
$_lang['NO_PARAMS'] = 'هیچ پارامتری وجود ندارد!';
$_lang['STYLE'] = 'استیل';
$_lang['ADVANCED_SETTINGS'] = 'تنظیمات پیشرفته';
$_lang['CSS_SUFFIX'] = 'پسوند CSS';
$_lang['CSS_SUFFIX_D'] = 'یک پسوند که به کلاس CSS ماژول افزودن می شود.';
$_lang['MENU_TYPE'] = 'نوع منو';
$_lang['ORIENTATION'] = 'جهت';
$_lang['SHOW'] = 'نمایش';
$_lang['HIDE'] = 'عدم نمایش';
$_lang['GLOBAL_SETTING'] = 'تنظیمات سراسری';

/* users & authentication */
$_lang['USERNAME'] = 'نام کاربری';
$_lang['PASSWORD'] = 'کلمه عبور';
$_lang['NOAUTHMETHODS'] = 'هیچ روش احراز هویتی تنظیم نشده است';
$_lang['AUTHMETHNOTEN'] = 'روش احراز هویت %s فعال نمی باشد';
$_lang['PASSTOOSHORT'] = 'کلمه عبور شما بسیار کوتاه است و مورد پذیرش نمی باشد';
$_lang['USERNOTFOUND'] = 'کاربر پیدا نشد';
$_lang['INVALIDUNAME'] = 'نام کاربری نامعتبر';
$_lang['INVALIDPASS'] = 'کلمه عبور نامعتبر';
$_lang['AUTHFAILED'] = 'احراز هویت ناموفق';
$_lang['YACCBLOCKED'] = 'حساب کاربری شما مسدود شده است';
$_lang['YACCEXPIRED'] = 'حساب کاربری شما منقضی شده است';
$_lang['INVUSERGROUP'] = 'گروه کاربری نامعتبر';
$_lang['NAME'] = 'نام';
$_lang['FIRSTNAME'] = 'نام';
$_lang['LASTNAME'] = 'نام خانوادگی';
$_lang['EMAIL'] = 'ایمیل';
$_lang['INVALIDEMAIL'] = 'آدرس ایمیل نامعتبر';
$_lang['ADMINISTRATOR'] = 'مدیر';
$_lang['GUEST'] = 'میهمان';
$_lang['EXTERNALUSER'] = 'کاربر خارجی';
$_lang['USER'] = 'کاربر';
$_lang['GROUP'] = 'گروه';
$_lang['NOTALLOWACCPAGE'] = 'شما مجاز به دستیابی به این صفحه نمی باشید!';
$_lang['NOTALLOWACCITEM'] = 'شما مجاز به دستیابی به این آیتم نمی باشید!';
$_lang['NOTALLOWMANITEM'] = 'شما مجاز به مدیریت این آیتم نمی باشید!';
$_lang['NOTALLOWACTION'] = 'شما مجاز به انجام این عمل نمی باشید!';
$_lang['NEED_HIGHER_ACCESS'] = 'شما نیاز به یک سطح دسترسی بالاتر برای این عمل دارید!';
$_lang['AREYOUSURE'] = 'آیا مطمئن هستید؟';

/* highslide */
$_lang['LOADING'] = 'در حال بارگذاری...';
$_lang['CLICK_CANCEL'] = 'جهت انصراف کلیک نمائید';
$_lang['MOVE'] = 'انتقال';
$_lang['PLAY'] = 'پخش';
$_lang['PAUSE'] = 'مکث';
$_lang['RESIZE'] = 'تغییر اندازه';

/* admin */
$_lang['ADMINISTRATION'] = 'مدیریت';
$_lang['SETTINGS'] = 'تنظیمات';
$_lang['DATABASE'] = 'پایگاه داده';
$_lang['ON'] = 'روشن';
$_lang['OFF'] = 'خاموش';
$_lang['WARNING'] = 'هشدار';
$_lang['SAVE'] = 'ذخیره';
$_lang['APPLY'] = 'اعمال';
$_lang['CANCEL'] = 'لغو';
$_lang['LIMIT'] = 'محدود کردن';
$_lang['ORDERING'] = 'ترتیب';
$_lang['NO_RESULTS'] = 'هیچ نتیجه ای یافته نشد!';
$_lang['CONNECT_ERROR'] = 'خطای ارتباط';
$_lang['DELETE_SEL_ITEMS'] = 'حذف آیتم های انتخاب شده؟';
$_lang['TOGGLE_SELECTED'] = 'تعویض انتخاب';
$_lang['NO_ITEMS_SELECTED'] = 'هیچ آیتمی انتخاب نشده است!';
$_lang['ID'] = 'شناسه';
$_lang['ACTION_FAILED'] = 'عمل ناموفق شد!';
$_lang['ACTION_SUCCESS'] = 'عمل با موفقیت کامل شد!';
$_lang['NO_IMAGE_UPLOADED'] = 'هیچ تصویری آپلود نشده است';
$_lang['NO_FILE_UPLOADED'] = 'هیچ فایلی آپلود نشده استNo file uploaded';
$_lang['MODULES'] = 'ماژول ها';
$_lang['COMPONENTS'] = 'اجزاء';
$_lang['TEMPLATES'] = 'قالب ها';
$_lang['SEARCH_ENGINES'] = 'موتورهای جستجو';
$_lang['AUTH_METHODS'] = 'روش های احراز هویت';
$_lang['CONTENT_PLUGINS'] = 'افزونه های محتوا';
$_lang['PLUGINS'] = 'افزونه ها';
$_lang['PUBLISHED'] = 'منتشر شده';
$_lang['ACCESS'] = 'دستیابی';
$_lang['ACCESS_LEVEL'] = 'سطح دسترسی';
$_lang['TITLE'] = 'عنوان';
$_lang['MOVE_UP'] = 'انتقال به بالا';
$_lang['MOVE_DOWN'] = 'انتقال به پایین';
$_lang['WIDTH'] = 'پهنا';
$_lang['HEIGHT'] = 'ارتفاع';
$_lang['ITEM_SAVED'] = 'آیتم ذخیره شد';
$_lang['FIRST'] = 'اول';
$_lang['LAST'] = 'آخر';
$_lang['SUGGESTED'] = 'پیشنهاد شده';
$_lang['VALIDATE'] = 'معتبر سازی';
$_lang['NEVER'] = 'هرگز';
$_lang['ALL'] = 'همه';
$_lang['ALL_GROUPS_LEVEL'] = "تمامی گروه های سطح %s";
$_lang['REQDROPPEDSEC'] = 'درخواست شما به دلایل امنیتی از بین رفت. لطفا مجددا سعی نمائید.';
$_lang['PROVIDE_TRANS'] = 'لطفا یک ترجمه ارائه نمائید!';
$_lang['AUTO_TRANS'] = 'ترجمه اتوماتیک';
$_lang['STATISTICS'] = 'آمار';
$_lang['UPLOAD'] = 'آپلود';
$_lang['MORE'] = 'More';

?>