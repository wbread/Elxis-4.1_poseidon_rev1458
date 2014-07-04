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


$locale = array('ru_RU.utf8', 'ru_RU.UTF-8', 'ru_RU', 'ru', 'russian', 'russia'); //utf-8 locales array

$_lang = array();
//Форматы дат
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //поддерживаемые форматы: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //поддерживаемые форматы: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //пример: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //пример: Дек 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //пример: Декабрь 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //пример: Дек 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //пример: Декабрь 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //пример: Декабрь 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //пример: Сб Дек 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //пример: Суббота Дек 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //пример: Суббота Декабрь 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //пример: Суббота Декабрь 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //пример: Суббота Декабрь 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //пример: Сб Декабрь 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //пример: Сб Декабрь 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ' ';
$_lang['DECIMALS_SEP'] = '.';
//названия месяцев
$_lang['JANUARY'] = 'Январь';
$_lang['FEBRUARY'] = 'Февраль';
$_lang['MARCH'] = 'Март';
$_lang['APRIL'] = 'Апрель';
$_lang['MAY'] = 'Май';
$_lang['JUNE'] = 'Июнь';
$_lang['JULY'] = 'Июль';
$_lang['AUGUST'] = 'Август';
$_lang['SEPTEMBER'] = 'Сентябрь';
$_lang['OCTOBER'] = 'Октябрь';
$_lang['NOVEMBER'] = 'Ноябрь';
$_lang['DECEMBER'] = 'Декабрь';
$_lang['JANUARY_SHORT'] = 'Янв';
$_lang['FEBRUARY_SHORT'] = 'Фев';
$_lang['MARCH_SHORT'] = 'Мар';
$_lang['APRIL_SHORT'] = 'Апр';
$_lang['MAY_SHORT'] = 'Май';
$_lang['JUNE_SHORT'] = 'Июн';
$_lang['JULY_SHORT'] = 'Июл';
$_lang['AUGUST_SHORT'] = 'Авг';
$_lang['SEPTEMBER_SHORT'] = 'Сен';
$_lang['OCTOBER_SHORT'] = 'Окт';
$_lang['NOVEMBER_SHORT'] = 'Ноя';
$_lang['DECEMBER_SHORT'] = 'Дек';
//название дней
$_lang['MONDAY'] = 'Понедельник';
$_lang['THUESDAY'] = 'Вторник';
$_lang['WEDNESDAY'] = 'Среда';
$_lang['THURSDAY'] = 'Четверг';
$_lang['FRIDAY'] = 'Пятница';
$_lang['SATURDAY'] = 'Суббота';
$_lang['SUNDAY'] = 'Воскресенье';
$_lang['MONDAY_SHORT'] = 'Пн';
$_lang['THUESDAY_SHORT'] = 'Вт';
$_lang['WEDNESDAY_SHORT'] = 'Ср';
$_lang['THURSDAY_SHORT'] = 'Чт';
$_lang['FRIDAY_SHORT'] = 'Пт';
$_lang['SATURDAY_SHORT'] = 'Сб';
$_lang['SUNDAY_SHORT'] = 'Вс';
/* elxis монитор производительности */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Монитор Производительности';
$_lang['ITEM'] = 'Объект';
$_lang['INIT_FILE'] = 'Файл инициализации';
$_lang['EXEC_TIME'] = 'Продолжительность выполнения';
$_lang['DB_QUERIES'] = 'Запросы к БД';
$_lang['ERRORS'] = 'Ошибки';
$_lang['SIZE'] = 'Размер';
$_lang['ENTRIES'] = 'Записи';

/* общие */
$_lang['HOME'] = 'Главная';
$_lang['YOU_ARE_HERE'] = 'НАВИГАТОР: ';//slavakov alt Вы сейчас здесь:
$_lang['CATEGORY'] = 'Категория';
$_lang['DESCRIPTION'] = 'Описание';
$_lang['FILE'] = 'Файл';
$_lang['IMAGE'] = 'Изображение';
$_lang['IMAGES'] = 'Изображения';
$_lang['CONTENT'] = 'Содержание';
$_lang['DATE'] = 'Дата';
$_lang['YES'] = 'Да';
$_lang['NO'] = 'Нет';
$_lang['NONE'] = 'Нет';  // Slavakov Без
$_lang['SELECT'] = 'Выбрать';
$_lang['LOGIN'] = 'Войти';
$_lang['LOGOUT'] = 'Выйти';
$_lang['WEBSITE'] = 'Веб сайт';
$_lang['SECURITY_CODE'] = 'Код безопасности';
$_lang['RESET'] = 'Восстановить';
$_lang['SUBMIT'] = 'Выполнить';
$_lang['REQFIELDEMPTY'] = 'Одно или несколько полей не заполнены!';
$_lang['FIELDNOEMPTY'] = "%s не может быть пустым!";
$_lang['FIELDNOACCCHAR'] = "%s содержит недопустимые символы!";
$_lang['INVALID_DATE'] = 'Неверная дата!';
$_lang['INVALID_NUMBER'] = 'Неверное число!';
$_lang['INVALID_URL'] = 'Неверный URL!';
$_lang['FIELDSASTERREQ'] = 'Поля, обозначенные * обязательно должны быть заполнены.';
$_lang['ERROR'] = 'Ошибка';
$_lang['REGARDS'] = 'С уважением';
$_lang['NOREPLYMSGINFO'] = 'Пожалуйста, не отвечайте на это письмо, оно создано автоматически и отправлено только в информационных целях.';
$_lang['LANGUAGE'] = 'Язык';
$_lang['PAGE'] = 'Страница';
$_lang['PAGEOF'] = "Страница %s от %s";
$_lang['OF'] = 'из';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Показано объектов с %s по %s. Всего объектов - %s";
$_lang['HITS'] = 'Хиты';
$_lang['PRINT'] = 'Печать';
$_lang['BACK'] = 'Назад';
$_lang['PREVIOUS'] = 'Предыдущий';
$_lang['NEXT'] = 'Следующий';
$_lang['CLOSE'] = 'Закрыть';
$_lang['CLOSE_WINDOW'] = 'Закрыть окно';
$_lang['COMMENTS'] = 'Комментарии';
$_lang['COMMENT'] = 'Комментарий';
$_lang['PUBLISH'] = 'Вкл/Откл';
$_lang['DELETE'] = 'Удалить';
$_lang['EDIT'] = 'Редактировать';
$_lang['COPY'] = 'Копировать';
$_lang['SEARCH'] = 'Поиск';
$_lang['PLEASE_WAIT'] = 'Пожалуйста, обождите...';
$_lang['ANY'] = 'Все';
$_lang['NEW'] = 'Добавить';  // Новый
$_lang['ADD'] = 'Добавить';
$_lang['VIEW'] = 'Просмотр';
$_lang['MENU'] = 'Меню';
$_lang['HELP'] = 'Помощь';
$_lang['TOP'] = 'Вверху';
$_lang['BOTTOM'] = 'Внизу';
$_lang['LEFT'] = 'Слева';
$_lang['RIGHT'] = 'Справа';
$_lang['CENTER'] = 'По центру';

/* xml */
$_lang['CACHE'] = 'Кэш';
$_lang['ENABLE_CACHE_D'] = 'Включить кэш для этого объекта?';
$_lang['YES_FOR_VISITORS'] = 'Да, для пользователей';
$_lang['YES_FOR_ALL'] = 'Да, для всех';
$_lang['CACHE_LIFETIME'] = 'Продолжительность кэш-памяти';
$_lang['CACHE_LIFETIME_D'] = 'Время до обновления кэша, в минутах.';
$_lang['NO_PARAMS'] = 'Нет параметров!';
$_lang['STYLE'] = 'Стиль';
$_lang['ADVANCED_SETTINGS'] = 'Расширенные настройки';
$_lang['CSS_SUFFIX'] = 'CSS суффикс';
$_lang['CSS_SUFFIX_D'] = 'Суффикс, который будет добавлен к CSS классу модуля.';
$_lang['MENU_TYPE'] = 'Тип меню';
$_lang['ORIENTATION'] = 'Ориентация';
$_lang['SHOW'] = 'Показывать';
$_lang['HIDE'] = 'Скрыть';
$_lang['GLOBAL_SETTING'] = 'Общие настройки';

/* пользователи и регистрация */
$_lang['USERNAME'] = 'Логин';
$_lang['PASSWORD'] = 'Пароль';
$_lang['NOAUTHMETHODS'] = 'Не определен способ аутентификации';
$_lang['AUTHMETHNOTEN'] = 'Способ аутентификации %s не активирован';
$_lang['PASSTOOSHORT'] = 'Ваш пароль не приемлем, т.к. является слишком коротким.';
$_lang['USERNOTFOUND'] = 'Такой пользователь не найден';
$_lang['INVALIDUNAME'] = 'Неправильный логин';
$_lang['INVALIDPASS'] = 'Неправильный пароль';
$_lang['AUTHFAILED'] = 'Ошибка аутентификации';
$_lang['YACCBLOCKED'] = 'Ваша учетная запись заблокирована';
$_lang['YACCEXPIRED'] = 'Ваша регистрация закончилась';
$_lang['INVUSERGROUP'] = 'Неверная группа пользователей';
$_lang['NAME'] = 'Имя';
$_lang['FIRSTNAME'] = 'Имя';
$_lang['LASTNAME'] = 'Фамилия';
$_lang['EMAIL'] = 'Email';
$_lang['INVALIDEMAIL'] = 'Неправильный email';
$_lang['ADMINISTRATOR'] = 'Администратор';
$_lang['GUEST'] = 'Гость';
$_lang['EXTERNALUSER'] = 'Внешний посетитель';
$_lang['USER'] = 'Пользователь';
$_lang['GROUP'] = 'Группа';
$_lang['NOTALLOWACCPAGE'] = 'У вас нет прав для доступа к этой странице! Требуется регистрация.';
$_lang['NOTALLOWACCITEM'] = 'У вас нет прав для доступа к этому объекту! Требуется регистрация.';
$_lang['NOTALLOWMANITEM'] = 'У вас нет прав для управления этим объектом! Требуется регистрация.';
$_lang['NOTALLOWACTION'] = 'У вас нет прав для этого действия! Требуется регистрация.';
$_lang['NEED_HIGHER_ACCESS'] = 'У вас должен быть более высокий уровень доступа для этого действия!';
$_lang['AREYOUSURE'] = 'Вы уверены?';

/* highslide */
$_lang['LOADING'] = 'Загрузка...';
$_lang['CLICK_CANCEL'] = 'Нажмите для отмены';
$_lang['MOVE'] = 'Перемещение';
$_lang['PLAY'] = 'Пуск';
$_lang['PAUSE'] = 'Пауза';
$_lang['RESIZE'] = 'Изменение размера';

/* admin */
$_lang['ADMINISTRATION'] = 'Администрация';
$_lang['SETTINGS'] = 'Основные настройки';
$_lang['DATABASE'] = 'База данных';
$_lang['ON'] = 'Включено';
$_lang['OFF'] = 'Отключено';
$_lang['WARNING'] = 'Предупреждение';
$_lang['SAVE'] = 'Сохранить';
$_lang['APPLY'] = 'Применить';
$_lang['CANCEL'] = 'Отмена';
$_lang['LIMIT'] = 'Лимит';
$_lang['ORDERING'] = 'Порядок';
$_lang['NO_RESULTS'] = 'Нет найденных результатов!';
$_lang['CONNECT_ERROR'] = 'Ошибка соединения';
$_lang['DELETE_SEL_ITEMS'] = 'Удалить выбранные объекты?';
$_lang['TOGGLE_SELECTED'] = 'Переключить выбранное';
$_lang['NO_ITEMS_SELECTED'] = 'Нет выбранных объектов!';
$_lang['ID'] = 'ID';
$_lang['ACTION_FAILED'] = 'Действие не удалось!';
$_lang['ACTION_SUCCESS'] = 'Действие завершено успешно!';
$_lang['NO_IMAGE_UPLOADED'] = 'Изображение не загружено';
$_lang['NO_FILE_UPLOADED'] = 'Файл не загружен';
$_lang['MODULES'] = 'Модули';
$_lang['COMPONENTS'] = 'Компоненты';
$_lang['TEMPLATES'] = 'Шаблоны';
$_lang['SEARCH_ENGINES'] = 'Поисковики';
$_lang['AUTH_METHODS'] = 'Способы аутентификации';
$_lang['CONTENT_PLUGINS'] = 'Плагины содержания';
$_lang['PLUGINS'] = 'Плагины';
$_lang['PUBLISHED'] = 'Опубликовано';
$_lang['ACCESS'] = 'Доступ';
$_lang['ACCESS_LEVEL'] = 'Уровень доступа';
$_lang['TITLE'] = 'Заголовок';
$_lang['MOVE_UP'] = 'Переместить выше';
$_lang['MOVE_DOWN'] = 'Переместить ниже';
$_lang['WIDTH'] = 'Ширина';
$_lang['HEIGHT'] = 'Высота';
$_lang['ITEM_SAVED'] = 'Данные сохранены';
$_lang['FIRST'] = 'Первый';
$_lang['LAST'] = 'Последний';
$_lang['SUGGESTED'] = 'Предпочтительно';
$_lang['VALIDATE'] = 'Утвердить';
$_lang['NEVER'] = 'Никогда';
$_lang['ALL'] = 'Все';
$_lang['ALL_GROUPS_LEVEL'] = "Все группы уровня %s";
$_lang['REQDROPPEDSEC'] = 'Ваш запрос снижен по соображениям безопасности. Пожалуйста, попробуйте еще раз.';
$_lang['PROVIDE_TRANS'] = 'Пожалуйста, предоставьте перевод!';
$_lang['AUTO_TRANS'] = 'Автоматический перевод';
$_lang['STATISTICS'] = 'Статистика';
$_lang['UPLOAD'] = 'Закачки';
$_lang['MORE'] = 'Далее...';

?>