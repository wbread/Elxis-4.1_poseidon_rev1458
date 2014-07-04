<?php
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: ru-RU (Russian - Russia) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Slavakov ( http://www.ekofarm.ukrmed.info )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Доступ запрещен.');


$_lang = array();
$_lang['MENU'] = 'Меню';
$_lang['MENU_MANAGER'] = 'Управление меню';
$_lang['MENU_ITEM_COLLECTIONS'] = 'Списки меню с пунктами';
$_lang['SN'] = '№'; //serial number
$_lang['MENU_ITEMS'] = 'Пункты меню';
$_lang['COLLECTION'] = 'Список меню';
$_lang['WARN_DELETE_COLLECT'] = 'Это удалит меню, все его пункты меню и модуль, связанный с ним!';
$_lang['CNOT_DELETE_MAINMENU'] = 'Нельзя удалять основное меню (maimenu)!';
$_lang['MODULE_TITLE'] = 'Название модуля';
$_lang['COLLECT_NAME_INFO'] = 'Название меню должно быть уникальным и состоять из латинских букв и цифр без пробелов';
$_lang['ADD_NEW_COLLECT'] = 'Добавить новое меню';
$_lang['EXIST_COLLECT_NAME'] = 'Меню с таким названием уже существует!';
$_lang['MANAGE_MENU_ITEMS'] = 'Управление пунктами';
$_lang['EXPAND'] = 'Развернуть';
$_lang['FULL'] = 'Полностью';
$_lang['LIMITED'] = 'Ограничено';
$_lang['TYPE'] = 'Тип';
$_lang['LEVEL'] = 'Уровень';
$_lang['MAX_LEVEL'] = 'Максимальный уровень';
$_lang['LINK'] = 'Ссылка';
$_lang['ELXIS_LINK'] = 'Внутренняя ссылка сайта';
$_lang['SEPARATOR'] = 'Разделитель';
$_lang['WRAPPER'] = 'Фрейм';
$_lang['WARN_DELETE_MENUITEM'] = 'Вы уверены, что хотите удалить этот пункт меню? Подпункты также будут удалены!';
$_lang['SEL_MENUITEM_TYPE'] = 'Выберите тип пункта меню';
$_lang['LINK_LINK_DESC'] = 'Внутренняя ссылка к странице сайта.';
$_lang['LINK_URL_DESC'] = 'Стандартная ссылка к внешней странице.';
$_lang['LINK_SEPARATOR_DESC'] = 'Текст строки без ссылки.';
$_lang['LINK_WRAPPER_DESC'] = 'Ссылка на внешнюю страницу, которая отображается на сайте во фрейме.';
$_lang['EXPAND_DESC'] = 'Создает, если поддерживается, подменю. Ограниченно - показывает только первый уровень, полное развертывание - все дерево.';
$_lang['LINK_TARGET'] = 'Цель ссылки';
$_lang['SELF_WINDOW'] = 'Тоже окно';
$_lang['NEW_WINDOW'] = 'Новое окно';
$_lang['PARENT_WINDOW'] = 'Родительское окно';
$_lang['TOP_WINDOW'] = 'Верхнее окно';
$_lang['NONE'] = 'Без';
$_lang['ELXIS_INTERFACE'] = 'Интерфейс Elxis';
$_lang['ELXIS_INTERFACE_DESC'] = 'Ссылка index.php генерирует обычную страницу с модулями, ссылка inner.php - страницы, которые отображают функции только главного компонента (полезно для всплывающих окон).';
$_lang['FULL_PAGE'] = 'Страница полностью';
$_lang['ONLY_COMPONENT'] = 'Только компонент';
$_lang['POPUP_WINDOW'] = 'Popup-окно';
$_lang['TYPICAL_POPUP'] = 'Стандартное окно';
$_lang['LIGHTBOX_WINDOW'] = 'Lightbox окно';
$_lang['PARENT_ITEM'] = 'Вышестоящий пункт';
$_lang['PARENT_ITEM_DESC'] = 'Сделать этот пункт меню как подменю другого пункта меню, выбрав его в качестве родителя.';
$_lang['POPUP_WIDTH_DESC'] = 'Ширина всплывающего окна или фрейма в пикселях. 0 для автоматического выбора.';
$_lang['POPUP_HEIGHT_DESC'] = 'Высота всплывающего окна или фрейма в пикселях. 0 для автоматического выбора.';
$_lang['MUST_FIRST_SAVE'] = 'Вы должны сначала сохранить этот пункт!';
$_lang['CONTENT'] = 'Содержание';
$_lang['SECURE_CONNECT'] = 'Безопасное соединение';
$_lang['SECURE_CONNECT_DESC'] = 'Только если это разрешено в основных настройках и у вас есть установленный SSL сертификат.';
$_lang['SEL_COMPONENT'] = 'Выберите компонент';
$_lang['LINK_GENERATOR'] = 'Генератор ссылки';
$_lang['URL_HELPER'] = 'Напишите полную ссылку на внешнюю страницу и заголовок ссылки. 
    Вы можете открыть ссылку во всплывающем окне или как рамку в качестве основы в lightbox.
	Настройте ширину и высоту всплывающего окна или фрейма lightbox.';
$_lang['SEPARATOR_HELPER'] = 'Разделитель является простым текстом, а не ссылкой. Т.е. ссылка не имеет значения.
	Используйте его как заголовок без ссылки для вашего подменю или для других целей.';
$_lang['WRAPPER_HELPER'] = 'Фрейм позволяет показать любую внешнюю страницу в рамке, называемой i-frame.
	Внешние страницы будет выглядеть как часть вашего сайта. Вы должны указать полный путь к странице во фрейме.
	Вы можете открыть ссылку во всплывающем окне или в рамке lightbox. 
	Настройте ширину и высоту всплывающего окна или фрейма lightbox.';
$_lang['TIP_INTERFACE'] = '<strong>Совет</strong><br />Выберите <strong>Только компонент</strong> в Другие настройки - Интерфейс Elxis,
	если вы планируете  открыть ссылку  в  popup-окне/фрейме lightbox.';
$_lang['COMP_NO_PUBLIC_IFACE'] = 'Этот компонент не имеет публичного интерфейса для отображения!';
$_lang['STANDARD_LINKS'] = 'Стандартная ссылка';
$_lang['BROWSE_ARTICLES'] = 'Просмотр статей';
$_lang['ACTIONS'] = 'Действия';
$_lang['LINK_TO_ITEM'] = 'Ссылка для этого элемента';
$_lang['LINK_TO_CAT_RSS'] = 'Ссылка на RSS ленту категории';
$_lang['LINK_TO_CAT_ATOM'] = 'Ссылка на ATOM поток категории';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = 'Ссылка для категории или статьи';
$_lang['ARTICLE'] = 'Статья';
$_lang['ARTICLES'] = 'Статьи';
$_lang['ASCENDING'] = 'По возрастанию';
$_lang['DESCENDING'] = 'По убыванию';
$_lang['LAST_MODIFIED'] = 'Последняя модификация';
$_lang['CAT_CONT_ART'] = "Категория %s содержит статей - %s."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "Без категории статей - %s."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = 'Нет объектов для показа!';
$_lang['ROOT'] = 'Основная категория'; //root category
$_lang['COMP_FRONTPAGE'] = "Главная страница для компонента %s"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = 'Ссылка на категорию со статьями';
$_lang['LINK_TO_CAT_ARTICLE'] = 'Ссылка на статью в категории';
$_lang['LINK_TO_AUT_PAGE'] = 'Ссылка на автономную страницу';
$_lang['SPECIAL_LINK'] = 'Специальная ссылка';
$_lang['FRONTPAGE'] = 'Главная страница';
$_lang['BASIC_SETTINGS'] = 'Общие настройки';
$_lang['OTHER_OPTIONS'] = 'Другие настройки';

?>