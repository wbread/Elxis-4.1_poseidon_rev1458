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


$_lang = array();
$_lang['INSTALLATION'] = 'التثبيت';
$_lang['STEP'] = 'خطوة';
$_lang['VERSION'] = 'الأصدار';
$_lang['VERSION_CHECK'] = 'التحقق من الأصدار';
$_lang['STATUS'] = 'الحالة';
$_lang['REVISION_NUMBER'] = 'رقم المراجعة';
$_lang['RELEASE_DATE'] = 'تاريخ الأصدار';
$_lang['ELXIS_INSTALL'] = 'تثبيت Elxis';
$_lang['LICENSE'] = 'الترخيص';
$_lang['VERSION_PROLOGUE'] = 'أنت على وشك تثبيت Elxis CMS‫.‬ الأصدار الذى انت على وشك تثبيته ظاهر أدناة‫.‬ برجاء التأكد من أنه أخر أصدار لـ Elxis من خلال  <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'قبل أن تبدأ';
$_lang['BEFORE_DESC'] = 'قبل التقدم برجاء قرأة التالى بعناية‫.‬';
$_lang['DATABASE'] = 'قاعدة البيانات';
$_lang['DATABASE_DESC'] = 'إنشاء قاعدة بيانات فارغة والتي سوف يتم استخدامها من قبل Elxis لتخزين البيانات الخاصة بك. ونحن
نوصي بشدة لاستخدام قاعدة بيانات <strong>MySQL</strong> على الرغم من أن Elxis لديه دعم لأنواع قواعد البيانات الأخرى مثل PostgreSQL and SQLite 3 فقد تم اختبارها بشكل جيد فقط مع MySQL لأنشاء قاعدة بيانات  MySQL فارغة يمكنك القيام بذلك من لوحة تحكم الأستضافة الخاصة بك (CPanel, Plesk, ISP Config, etc) أو من خلال أداة phpMyAdmin أو من خلال أى أداة لادارة قواعد البيانات فقط بأعطاء أسم لقاعدة البيانات ثم قم بأنشائها. بعد ذلك قم بأنشاء <strong>أسم مستخدم</strong> و عيينه إلى قاعدة البيانات التي تم إنشاؤها حديثا. اكتب اسم قاعدة البيانات في مكان ما، اسم المستخدم وكلمة المرور، ونحن في حاجة إليها في وقت لاحق خلال التثبيت.';
$_lang['REPOSITORY'] = 'المستودع';
$_lang['REPOSITORY_DESC'] = 'Elxis يستخدم مجلد خاص لتخزين الصفحات المخزنة مؤقتا، ملفات السجل، والدورات، والنسخ الاحتياطية وأكثر من ذلك. بشكل افتراضي يدعى هذا المجلد <strong>مستودع </strong> ويتم وضعها داخل المجلد الجذر Elxis. يجب أن يكون هذا المجلد <strong>قابل للكتابة</strong> ! و نحن نوصى بشدة <strong>أعادة تسمية</strong> هذا المجلد و <strong>نقلة</strong> الى مكان على السيرفر لا يمكن الوصول له من المتصفح. بعد هذه الخطوة إذا كانت قمت بتفعيل حماية <strong>open basedir</strong> فى أعدادات الـ PHP قد تحتاج أيضا إلى تضمين مسار المستودع في المسارات المسموح بها. ';
$_lang['REPOSITORY_DEFAULT'] = 'المستودع فى المسار الأفتراضى!';
$_lang['SAMPLE_ELXPATH'] = 'مثال لمسار Elxis';
$_lang['DEF_REPOPATH'] = 'المسار الأفتراضى للمستودع repository';
$_lang['REQ_REPOPATH'] = 'المسار الموصى به للمستودع repository';
$_lang['CONTINUE'] = 'متابعة';
$_lang['I_AGREE_TERMS'] = 'لقد قرأت وفهمت و أوافق على شروط و أحكام EPL';
$_lang['LICENSE_NOTES'] = 'Elxis CMS هو برنامج مجانى تم طرحه بموجب ترخيص <strong>Elxis Public License - EPL</strong> 
. لمواصلة تثبيت Elxis يجب أن توافق على شروط و أحكام EPL. أقراء بعناية ترخيص 
Elxis وإذا وافقت تحقق من خانة الاختيار في أسفل الصفحة وانقر فوق متابعة. إن لم 
يكن برجاء وقف التثبيت و مسح ملفات Elxis';
$_lang['SETTINGS'] = 'إعدادات';
$_lang['SITE_URL'] = 'URL الموقع';
$_lang['SITE_URL_DESC'] = 'بدون سلاشات (مثال. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'المسار المطلق إلى مجلد مستودع Elxis. أتركها فارغة فارغة للمسار و الأسم الأفتراضى';
$_lang['SETTINGS_DESC'] = 'تعيين معلمات التكوين المطلوبة لـ Elxis. ويلزم بعض المعلمات إلى أن يتم تعيينها قبل تثبيت Elxis.  بعد التثبيت هو الدخول الكامل في وحدة التحكم و الإدارة وتكوين بقية المعلمات و يجب أن تكون هذه هى أول مهامك كمدير للموقع';
$_lang['DEF_LANG'] = 'اللغة الأفتراضية';
$_lang['DEFLANG_DESC'] = 'يتم كتابة المحتوى في اللغة الافتراضية. المحتوى بلغات أخرى هو ترجمة من المحتوى الأصلي في اللغة الافتراضية.';
$_lang['ENCRYPT_METHOD'] = 'طريقة التشفير';
$_lang['ENCRYPT_KEY'] = 'مفتاح التشفير';
$_lang['AUTOMATIC'] = 'أوتوماتيك';
$_lang['GEN_OTHER'] = 'توليد أخرى';
$_lang['SITENAME'] = 'أسم الموقع';
$_lang['TYPE'] = 'النوع';
$_lang['DBTYPE_DESC'] = 'ونحن نوصي بشدة MySQL‫.‬ الأختيارات هى فقط ما هو مدعم من نظام أستضافتك و Elxis.';
$_lang['HOST'] = 'المضيف';
$_lang['TABLES_PREFIX'] = 'بادئة الجداول';
$_lang['DSN_DESC'] = 'يمكنك بدلا من ذلك توفير مصدر بيانات جاهز للاتصال بقاعدة البيانات.';
$_lang['SCHEME'] = 'مخطط ‫-Scheme‬ ‫-‬';
$_lang['SCHEME_DESC'] = 'المسار المطلق لملف قاعدة البيانات إذا كنت تستخدم قاعدة بيانات مثل SQLite.';
$_lang['PORT'] = 'المنفذ';
$_lang['PORT_DESC'] = 'المنفذ الافتراضي لـ MySQL هو 3306 أكتب ٠ للأختيار التلقائى';
$_lang['FTPPORT_DESC'] = 'المنفذ الافتراضي لـ FTP هو ٢١. أكتب ٠ للأختيار التلقائى.';
$_lang['USE_FTP'] = 'أستخدام FTP';
$_lang['PATH'] = 'المسار';
$_lang['FTP_PATH_INFO'] = 'المسار النسبي من المجلد الجذر FTP إلى مجلد التثبيت الخاص بـ Elxis (مثال: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'التحقق من أعدادات الـ FTP';
$_lang['CHECK_DB_SETS'] = 'التحقق من أعدادات قاعدة البيانات';
$_lang['DATA_IMPORT'] = 'أستيراد البيانات';
$_lang['SETTINGS_ERRORS'] = 'الإعدادات التي أعطيتها تحتوي على أخطاء!';
$_lang['NO_QUERIES_WARN'] = 'تم أستيراد البيانات الأولية الى قاعدة البيانات. ولكن يبدو عدم أجراء أى استعلامات على قاعدة البيانات. تأكد من استيراد البيانات بالفعل قبل المضى قدماً';
$_lang['RETRY_PREV_STEP'] = 'أعادة محاولة تنفيذ الخطوة السابقة';
$_lang['INIT_DATA_IMPORTED'] = 'تم أستيراد البيانات الأولية الى قاعدة البيانات';
$_lang['QUERIES_EXEC'] = "%s أستعلامات الـ SQL تم تنفيذها"; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'حساب المسؤول';
$_lang['CONFIRM_PASS'] = 'تأكيد كلمة المرور';
$_lang['AVOID_COMUNAMES'] = 'تجنب الأسماء الشائعة مثل مشرف ومسؤول admin و administrator';
$_lang['YOUR_DETAILS'] = 'بياناتك';
$_lang['PASS_NOMATCH'] = 'كلمات المرور غير متطابقة';
$_lang['REPOPATH_NOEX'] = 'مسار المستودع ‫-Repository-‬ غير موجود';
$_lang['FINISH'] = 'إنهاء';
$_lang['FRIENDLY_URLS'] = 'URLs صديقة';
$_lang['FRIENDLY_URLS_DESC'] = 'نوصى بشدة بتفعيل هذا الخيار‫.‬ لضمان عمل هذا الأختيار سيقوم Elxis بأعادة تسمية ملف  htaccess.txt الى <strong>htaccess.</strong>‫‬ و أذا كان هناك بالفعل ملف أخر بأسم htaccess. سيتم حذفه';
$_lang['GENERAL'] = 'عام';
$_lang['ELXIS_INST_SUCC'] = 'تم أكتمال تثبيت Elxis بنجاح‫!‬';
$_lang['ELXIS_INST_WARN'] = 'تم أكتمال بتثبيت Elxis مع وجود تحذيرات‫.‬';
$_lang['CNOT_CREA_CONFIG'] = 'تعذر أنشاء ملف <strong>configuration.php</strong> داخل المجلد الرئيسى لـ Elxis‫!‬';
$_lang['CNOT_REN_HTACC'] = 'تعذر أعادة تسمية الملف <strong>htaccess.txt</strong> الى <strong>htaccess.</strong>‫';
$_lang['CONFIG_FILE'] = 'ملف التكوين ‫-Configuration file-‬';
$_lang['CONFIG_FILE_MANUAL'] = 'إنشاء ملف configuration.php يدويا، قم بنسخ التعليمات البرمجية التالية والصقه داخله.';
$_lang['REN_HTACCESS_MANUAL'] = 'برجاء أعادة تسمية ملف <strong>htaccess.txt</strong> الى <strong>.htaccess</strong> يدوياً';
$_lang['WHAT_TODO'] = 'ماذا تفعل بعد ذلك؟';
$_lang['RENAME_ADMIN_FOLDER'] = 'لزيادة مستوى الأمان يمكنك أعادة تسمية ملف  (<em>estia</em>)  الى اى شئ يحلو لك‫.‬ في حالة القيام بذلك يجب عليك أيضا تحديث ملف htaccess. مع الاسم الجديد.';
$_lang['LOGIN_CONFIG'] = 'الدخول الى قسم المديرين و تعين باقى الخيارات‫.‬';
$_lang['VISIT_NEW_SITE'] = 'زيارة موقعك الجديد';
$_lang['VISIT_ELXIS_SUP'] = 'زيارة موقع دعم Elxis';
$_lang['THANKS_USING_ELXIS'] = 'شكراً لاستخدامك Elxis.';

?>