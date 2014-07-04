<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: bg-BG (Bulgarian - Bulgaria) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Stefan Sultanov ( http://www.vestnikar4e.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Вход за външни лица - забранен.');


$_lang = array();
$_lang['MENU'] = 'Меню';
$_lang['MENU_MANAGER'] = 'Управител на менюта';
$_lang['MENU_ITEM_COLLECTIONS'] = 'Колекции с бутони';
$_lang['SN'] = 'Сер./№'; //serial number
$_lang['MENU_ITEMS'] = 'Бутони';
$_lang['COLLECTION'] = 'Колекция';
$_lang['WARN_DELETE_COLLECT'] = 'Това ще изтрие колекцията, всички бутони и модули свързани с нея!';
$_lang['CNOT_DELETE_MAINMENU'] = 'Не можете да изтриване колекция на основното меню!';
$_lang['MODULE_TITLE'] = 'Заглавие на модул';
$_lang['COLLECT_NAME_INFO'] = 'Името за колекция трябва да съдържа само латински символи и цифри без интервали!';
$_lang['ADD_NEW_COLLECT'] = 'Добави нова колекция';
$_lang['EXIST_COLLECT_NAME'] = 'Вече има колекция с това име!';
$_lang['MANAGE_MENU_ITEMS'] = 'Управление на бутоните';
$_lang['EXPAND'] = 'Разгъни';
$_lang['FULL'] = 'Пълно';
$_lang['LIMITED'] = 'Ограничено';
$_lang['TYPE'] = 'Тип';
$_lang['LEVEL'] = 'Ниво';
$_lang['MAX_LEVEL'] = 'Максимално ниво';
$_lang['LINK'] = 'Връзка';
$_lang['ELXIS_LINK'] = 'Elxis Връзка';
$_lang['SEPARATOR'] = 'Разделител';
$_lang['WRAPPER'] = 'Рамка';
$_lang['WARN_DELETE_MENUITEM'] = 'Сигурно ли е, че трием този бутон? Подвластните бутони ще бъдат изтрити също!';
$_lang['SEL_MENUITEM_TYPE'] = 'Избери тип на бутона';
$_lang['LINK_LINK_DESC'] = 'Връзка към страница на Elxis.';
$_lang['LINK_URL_DESC'] = 'Стандартна връзка към външна страница.';
$_lang['LINK_SEPARATOR_DESC'] = 'Тесктов елемент без свързване.';
$_lang['LINK_WRAPPER_DESC'] = 'Връзка към външна страница, която се показва вътре в сайта през рамка.';
$_lang['EXPAND_DESC'] = 'При налицие на поддръжка генерира подменю. Ограниченото разгъване показва само първото ниво, пълното разгъване - всичките три.';
$_lang['LINK_TARGET'] = 'Цел на връзката';
$_lang['SELF_WINDOW'] = 'Същият прозорец';
$_lang['NEW_WINDOW'] = 'Нов прозорец';
$_lang['PARENT_WINDOW'] = 'Предния прозорец';
$_lang['TOP_WINDOW'] = 'Горния прозорец';
$_lang['NONE'] = 'Без';
$_lang['ELXIS_INTERFACE'] = 'Интерфейс на Elxis';
$_lang['ELXIS_INTERFACE_DESC'] = 'Връзките към index.php генерират нормални страници с модули, докато връзките към inner.php - страници на които се виждат само функции на главния компонент (удобно за изскачащи прозорци).';
$_lang['FULL_PAGE'] = 'Пълна страница';
$_lang['ONLY_COMPONENT'] = 'Само компонент';
$_lang['POPUP_WINDOW'] = 'Изскачащ прозорец';
$_lang['TYPICAL_POPUP'] = 'Стандартен изскачащ';
$_lang['LIGHTBOX_WINDOW'] = 'Lightbox прозорец';
$_lang['PARENT_ITEM'] = 'Вишестоящ бутон';
$_lang['PARENT_ITEM_DESC'] = 'Направи текущия бутон част от подменю на вишестоящ бутон по избор.';
$_lang['POPUP_WIDTH_DESC'] = 'Ширината на изскачащият прозорец или рамка в пиксели. Въвведи 0 за автоконтрол.';
$_lang['POPUP_HEIGHT_DESC'] = 'Височината на изскачащият прозорец или рамка в пиксели. Въвведи 0 за автоконтрол.';
$_lang['MUST_FIRST_SAVE'] = 'Трябва първо да запишеш обекта!';
$_lang['CONTENT'] = 'Съдържание';
$_lang['SECURE_CONNECT'] = 'Сигурна връзка';
$_lang['SECURE_CONNECT_DESC'] = 'Само ако е разрешено в основните настройки и имаш инсталиран SSL сертификат.';
$_lang['SEL_COMPONENT'] = 'Избери компонент';
$_lang['LINK_GENERATOR'] = 'Генератор на връзки';
$_lang['URL_HELPER'] = 'Напиши пълното URL към въпросната външна страница и заглавие за връзката. 
	Можеш да настроиш връзката като изскачащ прозорец или като рамка с lightbox. Настройките за ширина и височина контролират размерите на изскачащия прозорец или рамка.';
$_lang['SEPARATOR_HELPER'] = 'Разделителя не е връзка, а текст. Така, че полето връзка няма значение. 
	Ползва се, като неактивна заглавка за подменютата или друго.';
$_lang['WRAPPER_HELPER'] = 'Рамката ви позволява да показвате ВСЯКА страница като част от сайта чрез рамка, наречена i-frame. 
	Външните страници ще изглеждат сякаш са част от собствения ти сайт. Трябва да напишеш пълно URL към въпросната страница.
	Можеш да отвориш връзката в изскачащ прозорец или в lightbox рамка. Настройките за Ширина и Височина контролират 
	размерите на рамкирания регион, изскачащия прозорец или рамката.';
$_lang['TIP_INTERFACE'] = '<strong>Съвет</strong><br />Избери <strong>Само компонент</strong> за интерфейс на Elxis 
	ако мислиш да правиш връзка за изскачащ прозорец или лайтбокс рамка.';
$_lang['COMP_NO_PUBLIC_IFACE'] = 'Този компонент няма публичен интерфейс за показване!';
$_lang['STANDARD_LINKS'] = 'Стандартна връзка';
$_lang['BROWSE_ARTICLES'] = 'Разглеждане на статии';
$_lang['ACTIONS'] = 'Действия';
$_lang['LINK_TO_ITEM'] = 'Връзка към обекта';
$_lang['LINK_TO_CAT_RSS'] = 'Връзка към RSS емисия за категория';
$_lang['LINK_TO_CAT_ATOM'] = 'Връзка към ATOM емисия за категория';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = 'Връзка към категория или статия';
$_lang['ARTICLE'] = 'Статия';
$_lang['ARTICLES'] = 'Статии';
$_lang['ASCENDING'] = 'Възходящо';
$_lang['DESCENDING'] = 'Низходящо';
$_lang['LAST_MODIFIED'] = 'Последна промяна';
$_lang['CAT_CONT_ART'] = "Категорията %s съдържа %s статии."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "Има %s статии без категория."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = 'Няма нищо за показване!';
$_lang['ROOT'] = 'Основна категория'; //root category
$_lang['COMP_FRONTPAGE'] = "Начална страница за компонент %s"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = 'Връзка към категория със съдържание';
$_lang['LINK_TO_CAT_ARTICLE'] = 'Връзка към статия в категория';
$_lang['LINK_TO_AUT_PAGE'] = 'Връзка към самостоятелна страница';
$_lang['SPECIAL_LINK'] = 'Специална връзка';
$_lang['FRONTPAGE'] = 'Първа страница';
$_lang['BASIC_SETTINGS'] = 'Прости настройки';
$_lang['OTHER_OPTIONS'] = 'Други настройки';

?>