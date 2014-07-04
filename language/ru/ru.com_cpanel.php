<?php
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: ru-RU (Russian - Russia) language for component CPanel
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Slavakov ( http://www.ekofarm.ukrmed.info )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Доступ запрещен.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Контрольная панель';
$_lang['GENERAL_SITE_SETS'] = 'Основные настройки сайта';
$_lang['LANGS_MANAGER'] = 'Языки';
$_lang['MANAGE_SITE_LANGS'] = 'Управление языками сайта';
$_lang['USERS'] = 'Пользователи';
$_lang['MANAGE_USERS'] = 'Создание, редактирование и удаление регистрации пользователей';
$_lang['USER_GROUPS'] = 'Группы пользователей';
$_lang['MANAGE_UGROUPS'] = 'Управление группами пользователей';
$_lang['MEDIA_MANAGER'] = 'Медиаресурсы';
$_lang['MEDIA_MANAGER_INFO'] = 'Управление мульти-медийными файлами';
$_lang['ACCESS_MANAGER'] = 'Управление доступом';
$_lang['MANAGE_ACL'] = 'Управление списками контроля доступа';
$_lang['MENU_MANAGER'] = 'Меню';
$_lang['MANAGE_MENUS_ITEMS'] = 'Управление меню и пунктами меню';
$_lang['FRONTPAGE'] = 'Главная страница';
$_lang['DESIGN_FRONTPAGE'] = 'Проектирование главной страницы сайта';
$_lang['CATEGORIES_MANAGER'] = 'Категории';
$_lang['MANAGE_CONT_CATS'] = 'Управление категориями';
$_lang['CONTENT_MANAGER'] = 'Содержание сайта';
$_lang['MANAGE_CONT_ITEMS'] = 'Управление объектами содержания';
$_lang['MODULES_MANAGE_INST'] = 'Управление модулями и установка новых.';
$_lang['PLUGINS_MANAGE_INST'] = 'Управление плагинами содержания и установка новых.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Управление компонентами и установка новых.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Управление шаблонами и установка новых.';
$_lang['SENGINES_MANAGE_INST'] = 'Управление поисками и установка новых.';
$_lang['MANAGE_WAY_LOGIN'] = 'Управление способами входа на сайт.';
$_lang['TRANSLATOR'] = 'Переводчик';
$_lang['MANAGE_MLANG_CONTENT'] = 'Управление мультиязычным содержанием';
$_lang['LOGS'] = 'Отчеты';
$_lang['VIEW_MANAGE_LOGS'] = 'Просмотр и управление Лог-файлами';
$_lang['GENERAL'] = 'Основные';
$_lang['WEBSITE_STATUS'] = 'Доступ к сайту';
$_lang['ONLINE'] = 'Включен';
$_lang['OFFLINE'] = 'Отключен';
$_lang['ONLINE_ADMINS'] = 'Включен только для администраторов';
$_lang['OFFLINE_MSG'] = 'Сообщение при отключении сайта';
$_lang['OFFLINE_MSG_INFO'] = 'Если поле не будет заполнено, автоматически будет отображаться стандартное мультиязычное сообщение.';
$_lang['SITENAME'] = 'Имя сайта';
$_lang['URL_ADDRESS'] = 'URL сайта';
$_lang['REPO_PATH'] = 'Путь к Хранилищу';
$_lang['REPO_PATH_INFO'] = 'Полный путь к папке хранилища Elxis. Пустое поле - по умолчанию
	(elxis_root/repository/). Мы настоятельно рекомендуем, чтобы вы переместите эту папку выше WWW-папки
	и переименовали ее в нечто не предсказуемое!';
$_lang['FRIENDLY_URLS'] = 'Дружественные URL-ы';
$_lang['SEF_INFO'] = 'Если выберите ДА (рекомендуется), то переименуйте файл htaccess.txt на .htaccess';
$_lang['STATISTICS_INFO'] = 'Включает/выключает сбор статистики посещаемости сайта';
$_lang['GZIP_COMPRESSION'] = 'GZip компрессия';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis до отправки страницы в браузер будет сжимать его при помощи GZIP и таким образом сэкономит от 70% до 80% трафика.';
$_lang['DEFAULT_ROUTE'] = 'Маршрут по умолчанию';
$_lang['DEFAULT_ROUTE_INFO'] = 'URL сайта форматирован так, чтобы он направлял на главную страницу сайта.';
$_lang['META_DATA'] = 'META данные';
$_lang['META_DATA_INFO'] = 'Краткое описание сайта';
$_lang['KEYWORDS'] = 'Ключевые слова';
$_lang['KEYWORDS_INFO'] = 'Если ключевых слов несколько - разделяйте их запятыми';
$_lang['STYLE_LAYOUT'] = 'Стиль и макет';
$_lang['SITE_TEMPLATE'] = 'Шаблон сайта';
$_lang['ADMIN_TEMPLATE'] = 'Шаблон админпанели';
$_lang['ICONS_PACK'] = 'Пакет с иконками';
$_lang['LOCALE'] = 'Местоположение';
$_lang['TIMEZONE'] = 'Временная зона';
$_lang['MULTILINGUISM'] = 'Мультиязычность';
$_lang['MULTILINGUISM_INFO'] = 'Позволяет вводить текстовые элементы более чем не одном языке (переводы).
	Не включайте мультиязычность без необходимости, т.к. это будет безпричинно замедлять сайт. 
	Интерфейс сайта все равно останется многоязычным, даже если эта опция установлена в значение Нет.';
$_lang['CHANGE_LANG'] = 'Изменение языка';
$_lang['LANG_CHANGE_WARN'] = 'Если вы измените язык по умолчанию 
    может возникнуть несоответствие между отображаемым языком и переводами в таблице переводов.';
$_lang['CACHE'] = 'Память кэша';
$_lang['CACHE_INFO'] = 'Elxis может сохранить сгенерированный HTML код отдельных элементов в кэш для ускорения последующего повторного показа. 
	Это основная настройка. Вы можете включить кэш на элементы (например, модули), которые будут сохраняться в кэше.';
$_lang['APC_INFO'] = 'Альтернативный PHP Кеш (APC) это вариант кеширования на PHP. Он должен поддерживаться сервером.
	Не рекомендуется на виртуальных хостингах. Elxis будет использовать специальные страницы для повышения эффективности сайта.';
$_lang['APC_ID_INFO'] = 'В случае, если более 1-го сайта размещено на сервере, необходимо разделить их с APC Id - уникальным номером
   специфическим идентификатором этого сайта.';
$_lang['USERS_AND_REGISTRATION'] = 'Посетители и регистрация';
$_lang['PRIVACY_PROTECTION'] = 'Защита приватности';
$_lang['PASSWORD_NOT_SHOWN'] = 'Текущий пароль не отображается из соображений безопасности.
	Заполняйте это поле только тогда, когда вы хотите изменить текущий пароль.';
$_lang['DB_TYPE'] = 'Тип базы данных';
$_lang['ALERT_CON_LOST'] = 'Если изменить подключение к текущей базе данных, то информация будет утерена!';
$_lang['HOST'] = 'Хост';
$_lang['PORT'] = 'Порт';
$_lang['PERSISTENT_CON'] = 'Постоянное подключение';
$_lang['DB_NAME'] = 'Имя базы данных';
$_lang['TABLES_PREFIX'] = 'Префикс таблиц';
$_lang['DSN_INFO'] = 'Готовое к использованию имя источника данных (Data Source Name) для подключения к базе данных.';
$_lang['SCHEME'] = 'Маршрут';
$_lang['SCHEME_INFO'] = 'Абсолютный путь к файлу базы данных, если вы используете базу данных типа SQLite.';
$_lang['SEND_METHOD'] = 'Метод для отправки';
$_lang['SMTP_OPTIONS'] = 'SMTP настройки';
$_lang['AUTH_REQ'] = 'Требуется аутентификация';
$_lang['SECURE_CON'] = 'Безопасное соединение';
$_lang['SENDER_NAME'] = 'Имя отправителя';
$_lang['SENDER_EMAIL'] = 'Email отправителя';
$_lang['RCPT_NAME'] = 'Имя получателя';
$_lang['RCPT_EMAIL'] = 'Email получателя';
$_lang['TECHNICAL_MANAGER'] = 'Технический менеджер';
$_lang['TECHNICAL_MANAGER_INFO'] = 'Технический менеджер получает ошибки и предупреждения, связанные с безопасностью';
$_lang['USE_FTP'] = 'Использование FTP';
$_lang['PATH'] = 'Путь';
$_lang['FTP_PATH_INFO'] = 'Относительный путь от корневой папки FTP в папку установки Elxis (например: /public_html).';
$_lang['SESSION'] = 'Сессия';
$_lang['HANDLER'] = 'Режим работы';
$_lang['HANDLER_INFO'] = 'Elxis может записать файлы сессий в хранилище или в базу данных.
	Вы также можете выбрать Нет, чтобы сохранить PHP сессии в расположенную по умолчанию папку на сервере .';
$_lang['FILES'] = 'Файлы';
$_lang['LIFETIME'] = 'Продолжительность сессии';
$_lang['SESS_LIFETIME_INFO'] = 'Время окончания сессии, если пользователь не активен.';
$_lang['CACHE_TIME_INFO'] = 'После этого времени кэшированные элементы создаются заново.';
$_lang['MINUTES'] = 'минут';
$_lang['HOURS'] = 'часов';
$_lang['MATCH_IP'] = 'Сравнение IP';
$_lang['MATCH_BROWSER'] = 'Сравнение браузера';
$_lang['MATCH_REFERER'] = 'Сравнение HTTP источника';
$_lang['MATCH_SESS_INFO'] = 'Включает расширенные сессии в алгоритм проверки.';
$_lang['ENCRYPTION'] = 'Шифрование';
$_lang['ENCRYPT_SESS_INFO'] = 'Шифровать информацию о сессии?';
$_lang['ERRORS'] = 'Ошибки';
$_lang['WARNINGS'] = 'Предупреждения';
$_lang['NOTICES'] = 'Уведомления';
$_lang['NOTICE'] = 'Уведомление';
$_lang['REPORT'] = 'Отчет';
$_lang['REPORT_INFO'] = 'Уровень сообщений об ошибках. Для реальных функциональных сайтов рекомендуется Отключить.';
$_lang['LOG'] = 'Логи';
$_lang['LOG_INFO'] = 'Уровень записи ошибок. Выберите, какие ошибки вы хотите записывать 
    в системный журнал (repository/logs/).';
$_lang['ALERT'] = 'Оповещение';
$_lang['ALERT_INFO'] = 'Почта фатальных ошибок для технического менеджера сайта(ов).';
$_lang['ROTATE'] = 'Периодически';
$_lang['ROTATE_INFO'] = 'Периодическая отправка записей об ошибках в конце каждого месяца. Рекомендуется.';
$_lang['DEBUG'] = 'Отладка';
$_lang['MODULE_POS'] = 'Позиции модулей';
$_lang['MINIMAL'] = 'Минимально';
$_lang['FULL'] = 'Полностью';
$_lang['DISPUSERS_AS'] = 'Показывать посетителей как';
$_lang['USERS_REGISTRATION'] = 'Регистрация посетителей';
$_lang['ALLOWED_DOMAIN'] = 'Разрешенные домены';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Введите имя домена (например, elxis.org). Только для этих доменов система
    будет разрешать регистрацию адресов электронной почты.';
$_lang['EXCLUDED_DOMAINS'] = 'Запрещенные домены';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Список доменных имен, разделенных запятыми (например, badsite.com, hacksite.com)
    от которых не будут приниматься адреса электронной почты.';
$_lang['ACCOUNT_ACTIVATION'] = 'Способ активации регистрации';
$_lang['DIRECT'] = 'Прямо';
$_lang['MANUAL_BY_ADMIN'] = 'Администратор вручную';
$_lang['PASS_RECOVERY'] = 'Восстановление пароля';
$_lang['SECURITY'] = 'Безопасность';
$_lang['SECURITY_LEVEL'] = 'Уровень безопасности';
$_lang['SECURITY_LEVEL_INFO'] = 'При повышении уровня безопасности некоторые опции будут включены сильнее,
    в то время как некоторые функции сайта могут быть отключены. Прочтите документацию на Elxis для подробностей.';
$_lang['NORMAL'] = 'Нормальный';
$_lang['HIGH'] = 'Высокий';
$_lang['INSANE'] = 'Паранойя';
$_lang['ENCRYPT_METHOD'] = 'Метод шифрования';
$_lang['AUTOMATIC'] = 'Автоматически';
$_lang['ENCRYPTION_KEY'] = 'Ключ шифрования';
$_lang['ELXIS_DEFENDER'] = 'Защитник сайта';
$_lang['ELXIS_DEFENDER_INFO'] = 'Защитник сайта защищает ваш сайт от XSS и SQL-инъекционных атак.
    Этот мощный инструмент фильтров запросов пользователей и блокиратор атак на ваш сайт. Он также будет уведомлять вас про
    атаки и регистрировать их. Вы можете выбрать, какой применять тип фильтров или даже заблокировать в вашей системе
    важные файлы для несанкционированной модификации. Чем больше фильтров вы включите, тем медленнее ваш сайт будет работать.
    Мы рекомендуем включать опции G, C и F. Прочтите документацию на Elxis для подробностей.';
$_lang['SSL_SWITCH'] = 'Ключ SSL';
$_lang['SSL_SWITCH_INFO'] = 'Elxis будет автоматически переключаться с HTTP на HTTPS в страницах, где важна конфиденциальность.
	Для области администрации схемы HTTPS будет включены постоянно. Требуется SSL сертификат!';
$_lang['PUBLIC_AREA'] = 'Общедоступная область';
$_lang['GENERAL_FILTERS'] = 'Общие правила';
$_lang['CUSTOM_FILTERS'] = 'Специальные правила';
$_lang['FSYS_PROTECTION'] = 'Защита файловой системы';
$_lang['CHECK_FTP_SETS'] = 'Проверка настроек FTP';
$_lang['FTP_CON_SUCCESS'] = 'Подключение к серверу FTP было успешным.';
$_lang['ELXIS_FOUND_FTP'] = 'Инсталляция Elxis найдена на FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'Инсталляция Elxis не найдена на FTP! Проверьте правильность FTP пути.';
$_lang['CAN_NOT_CHANGE'] = 'Вы не можете изменить это.';
$_lang['SETS_SAVED_SUCC'] = 'Настройки сохранены успешно';
$_lang['ACTIONS'] = 'Действия';
$_lang['BAN_IP_REQ_DEF'] = 'Чтобы запретить IP-адрес, необходимо активировать хотя бы один вариант в Защитнике сайта!';
$_lang['BAN_YOURSELF'] = 'Вы хотите запретить себя?';
$_lang['IP_AL_BANNED'] = 'Этот IP уже запрещен!';
$_lang['IP_BANNED'] = 'IP адрес %s запрещен!';
$_lang['BAN_FAILED_NOWRITE'] = 'Запрет не удался! Не могу записать в файл repository/logs/defender_ban.php.';
$_lang['ONLY_ADMINS_ACTION'] = 'Только администраторы могут выполнять это действие!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Вы не можете выйти из администратора!';
$_lang['USER_LOGGED_OUT'] = 'Пользователь вышел из системы!';
$_lang['SITE_STATISTICS'] = 'Статистика сайта';
$_lang['SITE_STATISTICS_INFO'] = 'Просмотр статистики посещаемости сайта';
$_lang['BACKUP'] = 'Резервирование';
$_lang['BACKUP_INFO'] = 'Создание резервной копии сайта и управление существующими копиями.';
$_lang['BACKUP_FLIST'] = 'Список существующих резервных копий';
$_lang['TYPE'] = 'Тип';
$_lang['FILENAME'] = 'Имя файла';
$_lang['SIZE'] = 'Размер';
$_lang['NEW_DB_BACKUP'] = 'Новая резервная копия базы данных';
$_lang['NEW_FS_BACKUP'] = 'Новая резервная копия файловой системы';
$_lang['FILESYSTEM'] = 'Файловая система';
$_lang['DOWNLOAD'] = 'Загрузить';
$_lang['TAKE_NEW_BACKUP'] = 'Создать резервную копию?\nЭто потребует время, проявите терпение!';
$_lang['FOLDER_NOT_EXIST'] = "Папка %s не существует!";
$_lang['FOLDER_NOT_WRITE'] = "Папка %s не имеет прав на запись!";
$_lang['BACKUP_SAVED_INTO'] = "Файлы с резервными копиями сохраняются в %s";
$_lang['CACHE_SAVED_INTO'] = "Файлы с записями кэша сохраняются в %s";
$_lang['CACHED_ITEMS'] = 'Объекты кэширования';
$_lang['ELXIS_ROUTER'] = 'Маршрутизатор сайта';
$_lang['ROUTING'] = 'Маршрутизация';
$_lang['ROUTING_INFO'] = 'Перенаправления запросов пользователя на пользовательские адреса URL.';
$_lang['SOURCE'] = 'Источник';
$_lang['ROUTE_TO'] = 'Маршрут';
$_lang['REROUTE'] = "Изменить маршрут для %s";
$_lang['DIRECTORY'] = 'Директория';
$_lang['SET_FRONT_CONF'] = 'Установите главную страницу сайта в Настройки - Общие - Маршрут по умолчанию!';
$_lang['ADD_NEW_ROUTE'] = 'Добавить новый маршрут.';
$_lang['OTHER'] = 'Другое';
$_lang['LAST_MODIFIED'] = 'Последнее изменение';
$_lang['PERIOD'] = 'Период времени'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'Запись ошибок отключена!';
$_lang['LOG_ENABLE_ERR'] = 'Включена запись только фатальных ошибок.';
$_lang['LOG_ENABLE_ERRWARN'] = 'Включена запись ошибок и предупреждений.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Включена запись ошибок, предупреждений и уведомлений.';
$_lang['LOGROT_ENABLED'] = 'Периодическая запись включена.';
$_lang['LOGROT_DISABLED'] = 'Периодическая запись выключена!';
$_lang['SYSLOG_FILES'] = 'Файлы с системными отчетами';
$_lang['DEFENDER_BANS'] = 'Запреты Защитника сайта';
$_lang['LAST_DEFEND_NOTIF'] = 'Последнее сообщение Защитника сайта';
$_lang['LAST_ERROR_NOTIF'] = 'Последнее сообщение об ошибке';
$_lang['TIMES_BLOCKED'] = 'Время блокировки'; 
$_lang['REFER_CODE'] = 'Код справки';
$_lang['CLEAR_FILE'] = 'Очистить файл';
$_lang['CLEAR_FILE_WARN'] = 'Содержание файла будет удалено. Продолжить?';
$_lang['FILE_NOT_FOUND'] = 'Файл не найден!';
$_lang['FILE_CNOT_DELETE'] = 'Этот файл не может быть удален!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Только файл с расширением .log может быть скачан!';
$_lang['SYSTEM'] = 'Система';
$_lang['PHP_INFO'] = 'Информация о PHP';
$_lang['PHP_VERSION'] = 'Версия PHP';
$_lang['ELXIS_INFO'] = 'Информация об Elxis';
$_lang['VERSION'] = 'Версия';
$_lang['REVISION_NUMBER'] = 'Номер ревизии';
$_lang['STATUS'] = 'Статус';
$_lang['CODENAME'] = 'Кодовое имя';
$_lang['RELEASE_DATE'] = 'Дата выпуска';
$_lang['COPYRIGHT'] = 'Авторские права';
$_lang['POWERED_BY'] = 'Создано на';
$_lang['AUTHOR'] = 'Автор';
$_lang['PLATFORM'] = 'Платформа';
$_lang['HEADQUARTERS'] = 'Штаб квартира';
$_lang['ELXIS_ENVIROMENT'] = 'Среда Elxis';
$_lang['DEFENDER_LOGS'] = 'Логи Защитника сайта';
$_lang['ADMIN_FOLDER'] = 'Папка администрации';
$_lang['DEF_NAME_RENAME'] = 'Имя по умолчанию, измените!';
$_lang['INSTALL_PATH'] = 'Место для инсталляции';
$_lang['IS_PUBLIC'] = 'Общедоступно!';
$_lang['CREDITS'] = 'Заслуги';
$_lang['LOCATION'] = 'Местоположение';
$_lang['CONTRIBUTION'] = 'Вклад';
$_lang['LICENSE'] = 'Лицензия';
$_lang['MULTISITES'] = 'Мультисайты';
$_lang['MULTISITES_DESC'] = 'Управление несколькими сайтами на одной инсталяции Elxis.';
$_lang['MULTISITES_WARN'] = 'Вы можете иметь несколько сайтов на одной установке Elxis. Работа с мультисайтами
    это задача, которая требует глубоких знаний CMS Elxis. Прежде чем импортировать данные в новый
    мультисайт надо убедиться, что база данных существует. После создания нового мультисайта надо изменить файл htaccess следуя инструкции.
	Удаление мультисайта не удаляет связанные базы данных. Проконсультируйтесь с опытным специалистом,
    если вы нуждаетесь в помощи.';
$_lang['MULTISITES_DISABLED'] = 'Мультисайты отключены!';
$_lang['ENABLE'] = 'Включить';
$_lang['ACTIVE'] = 'Активность';
$_lang['URL_ID'] = 'URL идентификация';
$_lang['MAN_MULTISITES_ONLY'] = "Вы можете управлять мультисайтами только с сайта %s";
$_lang['LOWER_ALPHANUM'] = 'Маленькие буквы и цифры без интервалов.';
$_lang['IMPORT_DATA'] = 'Импорт данных';
$_lang['CNOT_CREATE_CFG_NEW'] = "Не удалось создать файл конфигурации %s для нового сайта!";
$_lang['DATA_IMPORT_FAILED'] = 'Не удалось импортировать данные!';
$_lang['DATA_IMPORT_SUC'] = 'Данные успешно импортированы!';
$_lang['ADD_RULES_HTACCESS'] = 'Добавьте следующие правила в файл htaccess';
$_lang['CREATE_REPOSITORY_NOTE'] = 'Настоятельно рекомендуем создать отдельное хранилище для каждого суб-сайта!';
$_lang['NOT_SUP_DBTYPE'] = 'Этот тип базы данных не поддерживается!';
$_lang['DBTYPES_MUST_SAME'] = 'Тип баз данных для этого сайта и новых должны быть одинаковыми!';
$_lang['DISABLE_MULTISITES'] = 'Отключить поддержку мультисайтов';
$_lang['DISABLE_MULTISITES_WARN'] = 'Все сайты, кроме одного с ID 1, будут удалены!';
$_lang['VISITS_PER_DAY'] = "Посещений за день на %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Кликов на день на %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Посещений за месяц на %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Кликов в месяц на %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Процент использования языков на %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Уникальных посетителей';
$_lang['PAGE_VIEWS'] = 'Просмотров страниц';
$_lang['TOTAL_VISITS'] = 'Всего посетителей - ';
$_lang['TOTAL_PAGE_VIEWS'] = 'Всего просмотрено страниц - ';
$_lang['LANGS_USAGE'] = 'Использование языков';
$_lang['LEGEND'] = 'Легенда';
$_lang['USAGE'] = 'Использовали';
$_lang['VIEWS'] = 'Просмотров';
//$_lang['OTHER'] = 'Другие'; //Slavakov duplicate line 231
$_lang['NO_DATA_AVAIL'] = 'Нет данных';
$_lang['PERIOD'] = 'Период';
$_lang['YEAR_STATS'] = 'Статистика года';
$_lang['MONTH_STATS'] = 'Статистика месяца';
$_lang['PREVIOUS_YEAR'] = 'Предыдущий год';
$_lang['NEXT_YEAR'] = 'Следующий год';
$_lang['STATS_COL_DISABLED'] = 'Сбор статистики отключен! Включение статистики в общих настройках.';
$_lang['DOCTYPE'] = 'Тип документа';
$_lang['DOCTYPE_INFO'] = 'Рекомендуется опция XHTML5. Elxis генерирует XHTML даже если вы установите тип документа HTML5.
  На типе документов XHTML Elxis обслужит документы с применением application/xhtml+xml mime типа для современных браузеров и text/html для устаревших.';
$_lang['ABR_SECONDS'] = 'сек';
$_lang['ABR_MINUTES'] = 'мин';
$_lang['HOUR'] = 'час';
$_lang['HOURS'] = 'часов';
$_lang['DAY'] = 'день';
$_lang['DAYS'] = 'дней';
$_lang['UPDATED_BEFORE'] = 'Обновлен до';
$_lang['CACHE_INFO'] = 'Просмотр и удаление элементов, записанных в кэш-памяти.';
$_lang['ELXISDC'] = 'Центр Загрузок Elxis';
$_lang['ELXISDC_INFO'] = 'Просмотрите ЦЗЕ в реальном времени  и проверьте наличие Дополнений';
$_lang['SITE_LANGS'] = 'Языки сайта';
$_lang['SITE_LANGS_DESC'] = 'По умолчанию отображаются все установленные языки в общедоступной области сайта. Вы можете это изменить, 
	выбрав и отметив среди установленных языков необходимые.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Performance';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minifier';
$_lang['MINIFIER_INFO'] = 'Elxis can unify individual local CSS and JS files and optionally compress them. The unified file will be saved in cache. 
So instead of having multiple CSS/JS files in your pages head section you will have only a minified one.';
$_lang['MOBILE_VERSION'] = 'Mobile version';
$_lang['MOBILE_VERSION_DESC'] = 'Enable mobile-friendly version for handheld devices?';

?>