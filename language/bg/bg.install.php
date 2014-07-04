<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: bg-BG (Bulgarian - Bulgaria) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Stefan Sultanov ( http://www.vestnikar4e.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Вход за външни лица - забранен.');


$_lang = array();
$_lang['INSTALLATION'] = 'Инсталация';
$_lang['STEP'] = 'Етап';
$_lang['VERSION'] = 'Версия';
$_lang['VERSION_CHECK'] = 'Провека на версия';
$_lang['STATUS'] = 'Статус';
$_lang['REVISION_NUMBER'] = 'Номер на ревизия';
$_lang['RELEASE_DATE'] = 'Дата на изаване';
$_lang['ELXIS_INSTALL'] = 'Elxis инсталация';
$_lang['LICENSE'] = 'Лиценз';
$_lang['VERSION_PROLOGUE'] = 'На път си да инсталираш Elxis CMS. Точната версия е показана по-долу. 
	Моля увери се пак, че това е най-новата версия на Elxis,
	достъпна на <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Преди да почнеш';
$_lang['BEFORE_DESC'] = 'Преди да продължиш нататък моля, внимателно прочети следното.';
$_lang['DATABASE'] = 'База данни';
$_lang['DATABASE_DESC'] = 'Създай празна база данни, която Elxis ще използва за да съхранява данните ти. 
	Ние горещо препоръчваме да използваш <strong>MySQL</strong> база данни. Въпреки, че Elxis има поддръжка и на други ведове бази данни, като 
	PostgreSQL и SQLite 3, тествали сме добре само MySQL. За да създадеш празна  
	MySQL база данни, трябва да отидеш в контролния панел на хостинг компанията си (CPanel, Plesk, ISP Config, и т. н.) или от 
	phpMyAdmin или друг инструмент за управление на бази данни. Просто дай на базата данни <strong>име</strong> и я създай. 
	След това, създай <strong>потребител</strong> и го обозначи към новата база данни. 
	Запиши някъде името на базата данни, потребителското име и паролата, за да ги попълниш по-късно по време на инсталацията.';
$_lang['REPOSITORY'] = 'Хранилище';
$_lang['REPOSITORY_DESC'] = 'Elxis работи в специална папка, кадето съхранява кеширани страници, файлове със записи, сесии, резерви и други. 
	По подразбиране папката се казва <strong>repository</strong> и се намира в главната папка на Elxis (root). 
	Тази папка трябва <strong>да има права за запис</strong>! Горещо препоръчваме да <strong>преименуваш</strong> папката и да я <strong>преместиш</strong> там, 
	където няма да е достъпна през интернет. След преместването ако си включил <strong>open basedir</strong> защитата в PHP 
	мож да ти се наложи да включиш новата пътека на хранилището в позволените пътеки.';
$_lang['REPOSITORY_DEFAULT'] = 'Хранилището не е преместено!';
$_lang['SAMPLE_ELXPATH'] = 'Примерна пътека на Elxis';
$_lang['DEF_REPOPATH'] = 'Пътека по подразбиране';
$_lang['REQ_REPOPATH'] = 'Препоръчителна пътека';
$_lang['CONTINUE'] = 'Нататък';
$_lang['I_AGREE_TERMS'] = 'Прочетох и съм съгласен с условията в ЕПЛ.';
$_lang['LICENSE_NOTES'] = 'Elxis CMS е безплатен софтуер издаден под <strong>Elxis Публичен Лиценз</strong> (ЕПЛ). 
	За да подължиш с инсталацията и да ползваш Elxis, трябва да се съгласиш с условията в ЕПЛ. Прочети внимателно 
	лицензът на Elxis и ако си съгласен/а постави отметката в долния край на страницата и цъкни Нататък. Ако не, 
	спри инсталацията и изтрий всички наши файлове.';
$_lang['SETTINGS'] = 'Настройки';
$_lang['SITE_URL'] = 'URL адрес на сайта';
$_lang['SITE_URL_DESC'] = 'Без затваряща наклонена черта (напр. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Абсолютната пътка към папката хранилище на Elxis. Остави празно за пътеката по подразбиране.';
$_lang['SETTINGS_DESC'] = 'Определи настройките на Elxis. Някои от тях са необходими преди Elxis да бъде инсталирана. 
	След като завърши инсталацията, влез в административната конзола и направи останалите настройки. 
	Това трябва да е първата твоя админ задача за изпълнение.';
$_lang['DEF_LANG'] = 'Език по подразбиране';
$_lang['DEFLANG_DESC'] = 'Съдържанието е написано на езика по подразбиране. Съдържанието на други езици е превод на 
	оригиналното съдържание.';
$_lang['ENCRYPT_METHOD'] = 'Метод на криптиране';
$_lang['ENCRYPT_KEY'] = 'Ключ за криптиране';
$_lang['AUTOMATIC'] = 'Автоматично';
$_lang['GEN_OTHER'] = 'Генерирай друг';
$_lang['SITENAME'] = 'Име на сайта';
$_lang['TYPE'] = 'Тип';
$_lang['DBTYPE_DESC'] = 'Горещо пепоръчваме MySQL. Можеш да избереш само поддържанте драйвери от Elxis и твоята система.';
$_lang['HOST'] = 'Хост';
$_lang['TABLES_PREFIX'] = 'Представка на таблиците';
$_lang['DSN_DESC'] = 'Вместо това можеш да осигуриш готово за ползване Data Source Name за връзка с база данни.';
$_lang['SCHEME'] = 'Схема';
$_lang['SCHEME_DESC'] = 'Абсолютна пътека към базовия файл за данни, ако ползваш база данни от типа на SQLite.';
$_lang['PORT'] = 'Порт';
$_lang['PORT_DESC'] = 'Порта по подразбиране за MySQL е 3306. Остави 0 за автоматично определяне.';
$_lang['FTPPORT_DESC'] = 'Порта по подразбиране за FTP е 21. Остави 0 за автоматично определяне.';
$_lang['USE_FTP'] = 'Ползвай FTP';
$_lang['PATH'] = 'Пътека';
$_lang['FTP_PATH_INFO'] = 'Релативна пътека от FTP папката към папката с инсталираната Elxis (пример: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Провери FTP настройките';
$_lang['CHECK_DB_SETS'] = 'Провери Настройките за база данни';
$_lang['DATA_IMPORT'] = 'Импортиране на данни';
$_lang['SETTINGS_ERRORS'] = 'Настройките съдържат грешки!';
$_lang['NO_QUERIES_WARN'] = 'Първоначалните данни са вкарани в базата данни но изглежда няма изпълнени операции. 
	Преди да продължиш се увери, че данните са вкарани наистина.';
$_lang['RETRY_PREV_STEP'] = 'Изпълни предишната стъпка наново';
$_lang['INIT_DATA_IMPORTED'] = 'Първоначалните данни са вкарани в базата данни.';
$_lang['QUERIES_EXEC'] = "Изпълнени са %s SQL операции."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Регистрация на администратор';
$_lang['CONFIRM_PASS'] = 'Потвърди парола';
$_lang['AVOID_COMUNAMES'] = 'Избягвай стандартни псевдоними като admin и administrator.';
$_lang['YOUR_DETAILS'] = 'Подробности за теб';
$_lang['PASS_NOMATCH'] = 'Паролите не съвпадат!';
$_lang['REPOPATH_NOEX'] = 'Пътеката към хранилището не съществува!';
$_lang['FINISH'] = 'Край';
$_lang['FRIENDLY_URLS'] = 'Приятелски URL-та';
$_lang['FRIENDLY_URLS_DESC'] = 'Горещо препоръчваме да включиш това. За да сработи, Elxis ще опита да преименува файла htaccess.txt 
	на <strong>.htaccess</strong> . Ако има вече друг .htaccess файл в същата папка - ще бъде изтрит.';
$_lang['GENERAL'] = 'Основни';
$_lang['ELXIS_INST_SUCC'] = 'Инсталирането на Elxis приключи успешно.';
$_lang['ELXIS_INST_WARN'] = 'Инсталирането на Elxis приключи с предупреждения.';
$_lang['CNOT_CREA_CONFIG'] = 'Неуспешно създаване на <strong>configuration.php</strong> файл в главната папка на Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Неуспешно преименуване на <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Конфигуриращ файл';
$_lang['CONFIG_FILE_MANUAL'] = 'Създай ръчно configuration.php файл, копирай следния код и го постави вътре.';
$_lang['REN_HTACCESS_MANUAL'] = 'Можеш да пеименуваш ръчно файла <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'какво следва?';
$_lang['RENAME_ADMIN_FOLDER'] = 'За да усилиш сигурността можеш да преименуваш администраторската папка (<em>estia</em>) на каквото си пожелаеш. 
	Ако направиш това, трябва да вкараш във файла .htaccess новото име.';
$_lang['LOGIN_CONFIG'] = 'Влез в зоната за администратори и определи останалите настройки.';
$_lang['VISIT_NEW_SITE'] = 'Посети новия си сайт';
$_lang['VISIT_ELXIS_SUP'] = 'Посети сайта майка на Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Благодарим, че избрахте Elxis CMS.';

?>