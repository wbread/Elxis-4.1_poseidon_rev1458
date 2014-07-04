<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: sr-RS (Српски - Србија) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ivan Trebješanin ( http://www.elxis-srbija.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Инсталација';
$_lang['STEP'] = 'Корак';
$_lang['VERSION'] = 'Верзија';
$_lang['VERSION_CHECK'] = 'Провера верзије';
$_lang['STATUS'] = 'Статус';
$_lang['REVISION_NUMBER'] = 'Број ревизије';
$_lang['RELEASE_DATE'] = 'Датум издавања';
$_lang['ELXIS_INSTALL'] = 'Elxis инсталација';
$_lang['LICENSE'] = 'Лиценца';
$_lang['VERSION_PROLOGUE'] = 'Управо инсталирате Elxis CMS. Тачна верзија Elxis копије 
	коју се спремате да инсталирате приказана је испод. Молимо Вас да се уверите да је то последња објављена Elxis верзија  
	на <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Пре него што почнете';
$_lang['BEFORE_DESC'] = 'Пре него што наставите уверите се да су сви захтеви испуњени.';
$_lang['DATABASE'] = 'База';
$_lang['DATABASE_DESC'] = 'Направите празну базу коју ће Elxis користити за чување података. Препоручујемо 
	употребу <strong>MySQL</strong> базе података. Иако Elxis подржава и остале типове база 
	као што су нпр. PostgreSQL и SQLite 3, детаљно је тестиран само на MySQL базама. Како бисте направили 
	празну MySQL базу, учините то путем панела (CPanel, Plesk, ISP Config, итд),   
	phpMyAdmin или неког другог алата за управљање базама. Само обезбедите <strong>име</strong> базе и направите је. 
	Након тога, направите <strong>корисника</strong> базе доделите му управо направљену базу. Запишите негде  
	име базе, корисничко име и лозинку јер ће нам требати током инсталације.';
$_lang['REPOSITORY'] = 'Спремиште';
$_lang['REPOSITORY_DESC'] = 'Elxis користи посебан фолдер за чување кешираних фајлова, логове, сесије, бекапе, итд.  
	Уобичајено име фолдер је <strong>repository</strong> а смештен је унутар Elxis фолдера. Овај фолдер 
	<strong>мора бити откључан</strong>! Препоручујемо да овај фолдер <strong>преименујете</strong> и <strong>преместите</strong> 
	ма место које није доступно преко интернета. Након овога, уколико имате <strong>open basedir</strong> заштиту у PHP 
	морате да додате и овај фолдер у листу дозвољених путања.';
$_lang['REPOSITORY_DEFAULT'] = 'Спремиште је на уобичајеном месту!';
$_lang['SAMPLE_ELXPATH'] = 'Показна Elxis путања';
$_lang['DEF_REPOPATH'] = 'Уобичајена путања спремишта';
$_lang['REQ_REPOPATH'] = 'Препоручена путања спремишта';
$_lang['CONTINUE'] = 'Наставак';
$_lang['I_AGREE_TERMS'] = 'Прочитао-ла сам, разумео-ла и слажем се са условима EPL';
$_lang['LICENSE_NOTES'] = 'Elxis CMS је бесплатни софтвер објавњен под <strong>Elxis Public License</strong> (EPL). 
	Пре него што инсталирате Elxis морате се сложити са условима EPL. Пажљиво прочитајте 
	Elxis лиценцу и уколико се слажете, штиклирајте одговарајућу опцију у подножју стане и кликните Наставак. У супротном, 
	прекините инсталацију и обришите Elxis фајлове.';
$_lang['SETTINGS'] = 'Подешавања';
$_lang['SITE_URL'] = 'URL сајта';
$_lang['SITE_URL_DESC'] = 'Без завршне косе црте (нпр. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Апсолутна путања до Elxis спремишта. Оставите празно за уобичајену путању и назив фолдера.';
$_lang['SETTINGS_DESC'] = 'Подесите параметре Elxis конфигурације. Неке параметре је неопходно подесити пре Elxis 
	инсталације. По завршетку инсталације пријавите се у администрациону конзолу и подесите остале параметре. 
	Ово ће бити Ваш први администраторски задатак.';
$_lang['DEF_LANG'] = 'Уобичајени језик';
$_lang['DEFLANG_DESC'] = 'Садржај је унет на уобичајеном језику. Садржај на другим језицима је 
	превод оригиналног чланка.';
$_lang['ENCRYPT_METHOD'] = 'Метод шифровања';
$_lang['ENCRYPT_KEY'] = 'Кључ шифровања';
$_lang['AUTOMATIC'] = 'Аутоматски';
$_lang['GEN_OTHER'] = 'Прављење новог';
$_lang['SITENAME'] = 'Име сајта';
$_lang['TYPE'] = 'Тип';
$_lang['DBTYPE_DESC'] = 'Препоручујемо MySQL. Могуће је изабрати само ставке подржана од стране сервера и Elxis инсталације.';
$_lang['HOST'] = 'Хост';
$_lang['TABLES_PREFIX'] = 'Префикс табела';
$_lang['DSN_DESC'] = 'Мођете изабрати и Data Source Name за повезивање с базом.';
$_lang['SCHEME'] = 'Схема';
$_lang['SCHEME_DESC'] = 'Апсолутна путања до базе, уколико користите базу налик SQLite.';
$_lang['PORT'] = 'Порт';
$_lang['PORT_DESC'] = 'Уобичајени порт за MySQL је 3306. Оставите на 0 за аутоматски избор.';
$_lang['FTPPORT_DESC'] = 'Уобичајени порт за FTP је 21. Оставите на 0 за аутоматски избор.';
$_lang['USE_FTP'] = 'Употреба FTP';
$_lang['PATH'] = 'Путања';
$_lang['FTP_PATH_INFO'] = 'Релативна путања од FTP корена до фолдера Elxis инсталације (пример: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Провера FTP подешавања';
$_lang['CHECK_DB_SETS'] = 'Провера подешавања базе';
$_lang['DATA_IMPORT'] = 'Увоз података';
$_lang['SETTINGS_ERRORS'] = 'Наведена подешавања садрже грешке!';
$_lang['NO_QUERIES_WARN'] = 'Иницијални подаци су увезени у базу, али изгледа да ниједан упит није извршен. Проверите 
	да ли су подаци заиста и увезени, пре него што наставите.';
$_lang['RETRY_PREV_STEP'] = 'Поновни покушај';
$_lang['INIT_DATA_IMPORTED'] = 'Иницијални подаци су увезени у базу.';
$_lang['QUERIES_EXEC'] = "Извршено је %s SQL упита."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Администраторски налог';
$_lang['CONFIRM_PASS'] = 'Потврда лозинке';
$_lang['AVOID_COMUNAMES'] = 'Избегавајте очекивана корисничка имена, као нпр. admin, administrator, итд.';
$_lang['YOUR_DETAILS'] = 'Ваши подаци';
$_lang['PASS_NOMATCH'] = 'Лозинке се не подударају!';
$_lang['REPOPATH_NOEX'] = 'Путања до спремишта не постоји!';
$_lang['FINISH'] = 'Крај';
$_lang['FRIENDLY_URLS'] = 'Пријатељски URL-ови';
$_lang['FRIENDLY_URLS_DESC'] = 'Препоручујемо да ово укључите. Како би све функционисало, Elxis ће пробати да преименује htaccess.txt у 
	<strong>.htaccess</strong> . Уколико већ постоји .htaccess у истом фолдеру, он ће бити обрисан.';
$_lang['GENERAL'] = 'Опште';
$_lang['ELXIS_INST_SUCC'] = 'Elxis инсталација је успешно завршена.';
$_lang['ELXIS_INST_WARN'] = 'Elxis инсталација је завршена уз упозорења.';
$_lang['CNOT_CREA_CONFIG'] = 'Није могуће направити <strong>configuration.php</strong> фајл у Elxis фолдеру.';
$_lang['CNOT_REN_HTACC'] = 'Није м огуће преименовати <strong>htaccess.txt</strong> фајл у <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Конфигурациони фајл';
$_lang['CONFIG_FILE_MANUAL'] = 'Направите ручно configuration.php фајл, копирајте следећи код и залепите га у фајл.';
$_lang['REN_HTACCESS_MANUAL'] = 'Преименујте ручно <strong>htaccess.txt</strong> фајл у <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Шта даље?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Ради побољшања сигурности, преименујте администрациони фолдер (<em>estia</em>) у било које друго име. 
	Уколико то и урадите, морате измењено име унети и у .htaccess фајл.';
$_lang['LOGIN_CONFIG'] = 'Пријавите се у администрациони део и подесите остале конфигурационе параметре.';
$_lang['VISIT_NEW_SITE'] = 'Посетите свој сајт';
$_lang['VISIT_ELXIS_SUP'] = 'Посетите сајт Elxis подршке';
$_lang['THANKS_USING_ELXIS'] = 'Хвала што користите Elxis CMS.';

?>