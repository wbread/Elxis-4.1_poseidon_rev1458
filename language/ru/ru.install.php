<?php
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: ru-RU (Russian - Russia) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Slavakov ( http://www.ekofarm.ukrmed.info )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Доступ запрещен.');


$_lang = array();
$_lang['INSTALLATION'] = 'Установка';
$_lang['STEP'] = 'Этап';
$_lang['VERSION'] = 'Версия';
$_lang['VERSION_CHECK'] = 'Проверка версии';
$_lang['STATUS'] = 'Статус';
$_lang['REVISION_NUMBER'] = 'Номер ревизии';
$_lang['RELEASE_DATE'] = 'Дата релиза';
$_lang['ELXIS_INSTALL'] = 'Установка Elxis';
$_lang['LICENSE'] = 'Лицензия';
$_lang['VERSION_PROLOGUE'] = 'Вы собираетесь установить Elxis CMS. Ниже указана текущая версия Elxis,
    которую вы собираетесь установить.
    Проверьте на <a href="http://www.elxis.org" target="_blank">elxis.org</a>, что это последняя версия Elxis.';
$_lang['BEFORE_BEGIN'] = 'Перед установкой Elxis';
$_lang['BEFORE_DESC'] = 'Прежде чем начать установку Elxis, внимательно прочтите нижеследующее.';
$_lang['DATABASE'] = 'База данных';
$_lang['DATABASE_DESC'] = 'Создайте пустую базу данных, которую Elxis будет использовать для хранения данных.
	Мы настоятельно рекомендуем использовать базу данных <strong>MySQL</strong>. Хотя Elxis имеет поддержку других баз данных, таких как
    PostgreSQL и SQLite 3, хорошо проверена только MySQL. <br> Для создания пустой
    MySQL-базы данных, зайдите в панель управления вашей хостинг-компании (CPanel, Plesk, ISP Config, и т.д.), в
	phpMyAdmin или в другой инструмент управления базами данных и создайте пустую базу данных <strong>с желаемым названием</strong>.
	Затем присвойте <strong>пользователю БД</strong> вновь созданную базу данных.
	Запишите название базы данных, имя пользователя и пароль, чтобы заполнить их позже, во время установки.';
$_lang['REPOSITORY'] = 'Хранилище';
$_lang['REPOSITORY_DESC'] = 'Elxis использует специальную папку для хранения кэшированных страниц, лог-файлов, резервных копий и другого.
	По умолчанию это папка называется <strong>repository (хранилище)</strong> и находится в корневой папке Elxis.
	Эта папка должна иметь <strong>права на запись</strong>! Мы настоятельно рекомендуем <strong>переименовать</strong> эту папку и <strong>переместить</strong> ее,
	в место недоступное из интернета. После перемещения, если включена в PHP защита <strong>open basedir</strong> 
	Вам также необходимо включить путь к хранилищу среди разрешенных путей.'; 
$_lang['REPOSITORY_DEFAULT'] = 'Хранилище (repository) находится в папке по умолчанию!';
$_lang['SAMPLE_ELXPATH'] = 'Пример пути к Elxis';
$_lang['DEF_REPOPATH'] = 'Путь по умолчанию';
$_lang['REQ_REPOPATH'] = 'Пример рекомендуемого пути';
$_lang['CONTINUE'] = 'Продолжить';
$_lang['I_AGREE_TERMS'] = 'Я прочел ЕПЛ и согласен с ее условиями.';
$_lang['LICENSE_NOTES'] = 'CMS Elxis это бесплатное программное обеспечение, изданное под <strong>Elxis Публичной Лицензией (ЕПЛ)</strong> .
	Для продолжения установки и использования Elxis, вам необходимо согласиться с условиями ЕПЛ. Прочтите внимательно
	лицензию на Elxis  и если вы согласны  поставьте отметку в низу страницы и нажмите кнопку <strong>Продолжить</strong>. Если вы не согласны, то
    прекратите установку и удалите все наши файлы.';
$_lang['SETTINGS'] = 'Основные настройки';
$_lang['SITE_URL'] = 'URL сайта';
$_lang['SITE_URL_DESC'] = 'Без наклонной черты (напр. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Абсолютный путь к папке хранилища Elxis. Оставьте пустым для пути и имени по умолчанию.';
$_lang['SETTINGS_DESC'] = 'Введите основные настройки для этого сайта. Некоторые параметры должны быть указаны до установки Elxis.
	<strong>После завершения установки войдите в административный раздел и настройте остальные параметры.
	Это первое, что обязательно должен сделать администратор!</strong>';
$_lang['DEF_LANG'] = 'Язык по умолчанию';
$_lang['DEFLANG_DESC'] = 'Содержание написано на языке по умолчанию. Содержание на других языках переводится с оригинального содержания
    на языке по умолчанию.';
$_lang['ENCRYPT_METHOD'] = 'Способ шифрования';
$_lang['ENCRYPT_KEY'] = 'Ключ шифрования';
$_lang['AUTOMATIC'] = 'Автоматически';
$_lang['GEN_OTHER'] = 'Нажмите, чтобы сгенерировать другой ключ';
$_lang['SITENAME'] = 'Название сайта';
$_lang['TYPE'] = 'Тип БД';
$_lang['DBTYPE_DESC'] = 'Мы настоятельно рекомендуем MySQL. Вы можете выбрать драйверы, которые поддерживаются Elxis и вашей системой.';
$_lang['HOST'] = 'Хост';
$_lang['TABLES_PREFIX'] = 'Префикс таблиц';
$_lang['DSN_DESC'] = 'Как альтернативу, вы можете использовать готовый Data Source Name (DNS) для соединения с базой данных.';
$_lang['SCHEME'] = 'Схема';
$_lang['SCHEME_DESC'] = 'Абсолютный путь к файлам базы данных, если вы используете базу данных, типа SQLite.';
$_lang['PORT'] = 'Порт';
$_lang['PORT_DESC'] = 'Порт по умолчанию для MySQL - 3306. Оставьте 0 для автоматического выбора.';
$_lang['FTPPORT_DESC'] = 'Порт по умолчанию для FTP - 21. Оставьте 0 для автоматического выбора.';
$_lang['USE_FTP'] = 'Использование FTP';
$_lang['PATH'] = 'Путь';
$_lang['FTP_PATH_INFO'] = 'Относительный путь от корневой папки FTP к папке установки Elxis (например: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Проверьте настройки FTP';
$_lang['CHECK_DB_SETS'] = 'Проверьте настройки базы данных';
$_lang['DATA_IMPORT'] = 'Импорт данных';
$_lang['SETTINGS_ERRORS'] = 'Параметры, которые вы дали содержат ошибки!';
$_lang['NO_QUERIES_WARN'] = 'Исходные данные импортированы в базу данных, но не похожи на запросы и не выполнены.
	Прежде чем продолжить, убедитесь, что в данных нет ошибки.';
$_lang['RETRY_PREV_STEP'] = 'Повторите предыдущий шаг';
$_lang['INIT_DATA_IMPORTED'] = 'Исходные данные импортированы в базу данных.';
$_lang['QUERIES_EXEC'] = "Выполнено %s SQL запросов."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Регистрация администратора сайта';
$_lang['CONFIRM_PASS'] = 'Подтвердите пароль';
$_lang['AVOID_COMUNAMES'] = 'Избегайте стандартных логинов, таких как admin и administrator.'; 
$_lang['YOUR_DETAILS'] = 'Информация об администраторе';
$_lang['PASS_NOMATCH'] = 'Пароли не совпадают!';
$_lang['REPOPATH_NOEX'] = 'Путь не существует!';
$_lang['FINISH'] = 'Конец';
$_lang['FRIENDLY_URLS'] = 'Дружественные URL-ы';
$_lang['FRIENDLY_URLS_DESC'] = 'Мы настоятельно рекомендуем включить их. После этого, Elxis будет пытаться переименовать файл htaccess.txt
	в <strong>.htaccess</strong>. Если уже такой файл есть в этой папке, то существующий файл .htaccess будет удален.';
$_lang['GENERAL'] = 'Основные';
$_lang['ELXIS_INST_SUCC'] = 'Установка Elxis прошла успешно.';
$_lang['ELXIS_INST_WARN'] = 'Установка Elxis прошла с предупреждениями.';
$_lang['CNOT_CREA_CONFIG'] = 'Не удалось создать файл <strong>configuration.php</strong> в корневой папке Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Не удалось переименовать файл <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Файл конфигурации';
$_lang['CONFIG_FILE_MANUAL'] = 'Создайте вручную файл configuration.php, скопируйте следующий код и вставьте его в этот файл.';
$_lang['REN_HTACCESS_MANUAL'] = 'Переименуйте вручную файл <strong>htaccess.txt</strong> на <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Что делать дальше?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Для повышения безопасности можно изменить имя административной папки (<em>estia</em>) на любое иное имя.
	В этом случае, необходимо вписать в файл .htaccess новое имя.';
$_lang['LOGIN_CONFIG'] = '<strong>Войдите в административную часть и определите остальные настройки.</strong>';
$_lang['VISIT_NEW_SITE'] = 'Перейти на мой новый сайт';
$_lang['VISIT_ELXIS_SUP'] = 'Посетите сайт поддержки Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Благодарим вас за выбор CMS Elxis.';

?>