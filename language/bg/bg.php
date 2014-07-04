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


$locale = array('bg_BG.utf8', 'bg_BG.UTF-8', 'bg_BG', 'bg', 'bulgarian', 'bulgaria'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'д-м-Г'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'д-м-Г Ч:м:с'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
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
$_lang['THOUSANDS_SEP'] = ' ';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Януари';
$_lang['FEBRUARY'] = 'Февруари';
$_lang['MARCH'] = 'Март';
$_lang['APRIL'] = 'Април';
$_lang['MAY'] = 'Май';
$_lang['JUNE'] = 'Юни';
$_lang['JULY'] = 'Юли';
$_lang['AUGUST'] = 'Август';
$_lang['SEPTEMBER'] = 'Септември';
$_lang['OCTOBER'] = 'Октомври';
$_lang['NOVEMBER'] = 'Ноември';
$_lang['DECEMBER'] = 'Декември';
$_lang['JANUARY_SHORT'] = 'Яну';
$_lang['FEBRUARY_SHORT'] = 'Фев';
$_lang['MARCH_SHORT'] = 'Мар';
$_lang['APRIL_SHORT'] = 'Апр';
$_lang['MAY_SHORT'] = 'Май';
$_lang['JUNE_SHORT'] = 'Юни';
$_lang['JULY_SHORT'] = 'Юли';
$_lang['AUGUST_SHORT'] = 'Авг';
$_lang['SEPTEMBER_SHORT'] = 'Сеп';
$_lang['OCTOBER_SHORT'] = 'Окт';
$_lang['NOVEMBER_SHORT'] = 'Ное';
$_lang['DECEMBER_SHORT'] = 'Дек';
//day names
$_lang['MONDAY'] = 'Понеделник';
$_lang['THUESDAY'] = 'Вторник';
$_lang['WEDNESDAY'] = 'Сряда';
$_lang['THURSDAY'] = 'Четвъртък';
$_lang['FRIDAY'] = 'Петък';
$_lang['SATURDAY'] = 'Събота';
$_lang['SUNDAY'] = 'Неделя';
$_lang['MONDAY_SHORT'] = 'Пн';
$_lang['THUESDAY_SHORT'] = 'Вт';
$_lang['WEDNESDAY_SHORT'] = 'Ср';
$_lang['THURSDAY_SHORT'] = 'Чтв';
$_lang['FRIDAY_SHORT'] = 'Птк';
$_lang['SATURDAY_SHORT'] = 'Сб';
$_lang['SUNDAY_SHORT'] = 'Нд';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Монитор на ефективността';
$_lang['ITEM'] = 'Обект';
$_lang['INIT_FILE'] = 'Файл за инициализация';
$_lang['EXEC_TIME'] = 'Време за изпълнение';
$_lang['DB_QUERIES'] = 'Заявки към БД';
$_lang['ERRORS'] = 'Грешки';
$_lang['SIZE'] = 'Големина';
$_lang['ENTRIES'] = 'Записи';

/* general */
$_lang['HOME'] = 'Начало';
$_lang['YOU_ARE_HERE'] = 'Ти си тук';
$_lang['CATEGORY'] = 'Категория';
$_lang['DESCRIPTION'] = 'Описание';
$_lang['FILE'] = 'Файл';
$_lang['IMAGE'] = 'Изображение';
$_lang['IMAGES'] = 'Изображения';
$_lang['CONTENT'] = 'Съдържание';
$_lang['DATE'] = 'Дата';
$_lang['YES'] = 'Да';
$_lang['NO'] = 'Не';
$_lang['NONE'] = 'Без';
$_lang['SELECT'] = 'Избери';
$_lang['LOGIN'] = 'Вход';
$_lang['LOGOUT'] = 'Изход';
$_lang['WEBSITE'] = 'Уеб сайт';
$_lang['SECURITY_CODE'] = 'Код за сигурност';
$_lang['RESET'] = 'Нулирай';
$_lang['SUBMIT'] = 'Прати';
$_lang['REQFIELDEMPTY'] = 'Едно или повече полета са празни!';
$_lang['FIELDNOEMPTY'] = "%s не може да е празно!";
$_lang['FIELDNOACCCHAR'] = "%s съдържа неприемливи символи!";
$_lang['INVALID_DATE'] = 'Невалидна дата!';
$_lang['INVALID_NUMBER'] = 'Невалидно число!';
$_lang['INVALID_URL'] = 'Невалиден URL адрес!';
$_lang['FIELDSASTERREQ'] = 'Полетата означени с * са задължителни.';
$_lang['ERROR'] = 'Грешка';
$_lang['REGARDS'] = 'С уважение';
$_lang['NOREPLYMSGINFO'] = 'Моля, не отговаряй на това съобщение. То е само за информация.';
$_lang['LANGUAGE'] = 'Език';
$_lang['PAGE'] = 'Страница';
$_lang['PAGEOF'] = "Страница %s от %s";
$_lang['OF'] = 'на';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Показани са %s до %s от общо %s обекта";
$_lang['HITS'] = 'Импресии';
$_lang['PRINT'] = 'Печат';
$_lang['BACK'] = 'Назад';
$_lang['PREVIOUS'] = 'Предишно';
$_lang['NEXT'] = 'Следващо';
$_lang['CLOSE'] = 'Затвори';
$_lang['CLOSE_WINDOW'] = 'Затвори прозореца';
$_lang['COMMENTS'] = 'Коментари';
$_lang['COMMENT'] = 'Коментар';
$_lang['PUBLISH'] = 'Публикувай';
$_lang['DELETE'] = 'Изтрий';
$_lang['EDIT'] = 'Редактирай';
$_lang['COPY'] = 'Копирай';
$_lang['SEARCH'] = 'Търсене';
$_lang['PLEASE_WAIT'] = 'Моля, изчакай...';
$_lang['ANY'] = 'Всички';
$_lang['NEW'] = 'Ново';
$_lang['ADD'] = 'Добави';
$_lang['VIEW'] = 'Виж';
$_lang['MENU'] = 'Меню';
$_lang['HELP'] = 'Помощ';
$_lang['TOP'] = 'Горе';
$_lang['BOTTOM'] = 'Долу';
$_lang['LEFT'] = 'Ляво';
$_lang['RIGHT'] = 'Дясно';
$_lang['CENTER'] = 'Център';

/* xml */
$_lang['CACHE'] = 'Кеш';
$_lang['ENABLE_CACHE_D'] = 'Да разреша ли кеш памет за обекта?';
$_lang['YES_FOR_VISITORS'] = 'Да, за поесетители';
$_lang['YES_FOR_ALL'] = 'Да, за всички';
$_lang['CACHE_LIFETIME'] = 'Продължителност на кеш-паметта';
$_lang['CACHE_LIFETIME_D'] = 'Време за съхранение на кешовата памет за този обект.';
$_lang['NO_PARAMS'] = 'Няма параметри!';
$_lang['STYLE'] = 'Стил';
$_lang['ADVANCED_SETTINGS'] = 'Разширени настройки';
$_lang['CSS_SUFFIX'] = 'CSS наставка';
$_lang['CSS_SUFFIX_D'] = 'Наставка, която се добавя към CSS класа на модула.';
$_lang['MENU_TYPE'] = 'Тип меню';
$_lang['ORIENTATION'] = 'Ориентация';
$_lang['SHOW'] = 'Покажи';
$_lang['HIDE'] = 'Скрий';
$_lang['GLOBAL_SETTING'] = 'Основна настройка';

/* users & authentication */
$_lang['USERNAME'] = 'Псевдоним';
$_lang['PASSWORD'] = 'Парола';
$_lang['NOAUTHMETHODS'] = 'Няма определен метод за разпознаване';
$_lang['AUTHMETHNOTEN'] = 'Метод за разпознаване %s не е активиран';
$_lang['PASSTOOSHORT'] = 'Паролата е неприемливо къса';
$_lang['USERNOTFOUND'] = 'Потребителя не бе намерен';
$_lang['INVALIDUNAME'] = 'Невалиден псевдоним';
$_lang['INVALIDPASS'] = 'Невалидна парола';
$_lang['AUTHFAILED'] = 'Неуспешно разпознаване';
$_lang['YACCBLOCKED'] = 'Твоята регистрация е блокирана';
$_lang['YACCEXPIRED'] = 'Твоята регистрация е изтекла';
$_lang['INVUSERGROUP'] = 'Невалидна членска група';
$_lang['NAME'] = 'Име';
$_lang['FIRSTNAME'] = 'Собствено име';
$_lang['LASTNAME'] = 'Фамилия';
$_lang['EMAIL'] = 'Имейл';
$_lang['INVALIDEMAIL'] = 'Невалиден имейл адрес';
$_lang['ADMINISTRATOR'] = 'Администратор';
$_lang['GUEST'] = 'Гост';
$_lang['EXTERNALUSER'] = 'Външен потребител';
$_lang['USER'] = 'Член';
$_lang['GROUP'] = 'Група';
$_lang['NOTALLOWACCPAGE'] = 'Не ти е позволен достъпа до тази страница!';
$_lang['NOTALLOWACCITEM'] = 'Не ти е позволен достъпа до този обект!';
$_lang['NOTALLOWMANITEM'] = 'Не ти е позволено да се разпореждаш с този обект!';
$_lang['NOTALLOWACTION'] = 'Не ти е позволено тоа действие!';
$_lang['NEED_HIGHER_ACCESS'] = 'Имаш нужда от по-горно ниво на достъп за това действие!';
$_lang['AREYOUSURE'] = 'Ама наистина ли?';

/* highslide */
$_lang['LOADING'] = 'Зареждане...';
$_lang['CLICK_CANCEL'] = 'Цъкни за отмяна';
$_lang['MOVE'] = 'Преместване';
$_lang['PLAY'] = 'Пусни';
$_lang['PAUSE'] = 'Пауза';
$_lang['RESIZE'] = 'Преоразмери';

/* admin */
$_lang['ADMINISTRATION'] = 'Администрация';
$_lang['SETTINGS'] = 'Настройки';
$_lang['DATABASE'] = 'База данни';
$_lang['ON'] = 'Включено';
$_lang['OFF'] = 'Изключено';
$_lang['WARNING'] = 'Предупреждение';
$_lang['SAVE'] = 'Запис';
$_lang['APPLY'] = 'Приложи';
$_lang['CANCEL'] = 'Отмяна';
$_lang['LIMIT'] = 'Лимит';
$_lang['ORDERING'] = 'Подредба';
$_lang['NO_RESULTS'] = 'Няма намерени резултати!';
$_lang['CONNECT_ERROR'] = 'Грешка при свързване';
$_lang['DELETE_SEL_ITEMS'] = 'Изтрий избраните обекти?';
$_lang['TOGGLE_SELECTED'] = 'Промяна на избора';
$_lang['NO_ITEMS_SELECTED'] = 'Няма избрани обекти!';
$_lang['ID'] = 'Ид. Номер';
$_lang['ACTION_FAILED'] = 'Неуспешно действие!';
$_lang['ACTION_SUCCESS'] = 'Успешно действие!';
$_lang['NO_IMAGE_UPLOADED'] = 'Не е качено изображение';
$_lang['NO_FILE_UPLOADED'] = 'Не е качен файл';
$_lang['MODULES'] = 'Модули';
$_lang['COMPONENTS'] = 'Компоненти';
$_lang['TEMPLATES'] = 'Шаблони';
$_lang['SEARCH_ENGINES'] = 'Търсачки';
$_lang['AUTH_METHODS'] = 'Методи за разпознаване';
$_lang['CONTENT_PLUGINS'] = 'Плъгини към съдържанието';
$_lang['PLUGINS'] = 'Плъгини';
$_lang['PUBLISHED'] = 'Публикувано';
$_lang['ACCESS'] = 'Достъп';
$_lang['ACCESS_LEVEL'] = 'Ниво на достъп';
$_lang['TITLE'] = 'Заглавие';
$_lang['MOVE_UP'] = 'Премести нагоре';
$_lang['MOVE_DOWN'] = 'Премести надолу';
$_lang['WIDTH'] = 'Ширина';
$_lang['HEIGHT'] = 'Височина';
$_lang['ITEM_SAVED'] = 'Обекта е записан';
$_lang['FIRST'] = 'Първо';
$_lang['LAST'] = 'Последно';
$_lang['SUGGESTED'] = 'Препоръчително';
$_lang['VALIDATE'] = 'Валидирай';
$_lang['NEVER'] = 'Никога';
$_lang['ALL'] = 'Всички';
$_lang['ALL_GROUPS_LEVEL'] = "Всички групи от ниво %s";
$_lang['REQDROPPEDSEC'] = 'Твоята заявка бе пропусната поради съображения за сигурност. Моля опитай отново.';
$_lang['PROVIDE_TRANS'] = 'Моля осигурете превод!';
$_lang['AUTO_TRANS'] = 'Автоматичен превод';
$_lang['STATISTICS'] = 'Статистики';
$_lang['UPLOAD'] = 'Качване';
$_lang['MORE'] = 'Още';

?>