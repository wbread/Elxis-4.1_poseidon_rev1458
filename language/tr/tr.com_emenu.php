<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: tr-TR (Turkish - Turkey) language for component eMenu
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Hakan Gür ( http://www.dildersleri.gen.tr )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Doğrudan erişime izin yok.');


$_lang = array();
$_lang['MENU'] = 'Mönü';
$_lang['MENU_MANAGER'] = 'Mönü yöneticisi';
$_lang['MENU_ITEM_COLLECTIONS'] = 'Mönü maddesi koleksiyonları';
$_lang['SN'] = 'S/N'; //serial number
$_lang['MENU_ITEMS'] = 'Mönü maddeleri';
$_lang['COLLECTION'] = 'Koleksiyon';
$_lang['WARN_DELETE_COLLECT'] = 'Bu işlem koleksiyonu, onun TÜM mönü maddelerini ve onunla bağlantılı modülü siler!';
$_lang['CNOT_DELETE_MAINMENU'] = 'Ana mönü koleksiyonunu silemezsiniz!';
$_lang['MODULE_TITLE'] = 'Modül başlığı';
$_lang['COLLECT_NAME_INFO'] = 'Koleksiyon adı benzersiz olmalı ve boşluk içermeden Latin harflerden oluşmalı!';
$_lang['ADD_NEW_COLLECT'] = 'Yeni koleksiyon ekle';
$_lang['EXIST_COLLECT_NAME'] = 'Bu adla bir koleksiyon zaten var!';
$_lang['MANAGE_MENU_ITEMS'] = 'Mönü maddelerini yönet';
$_lang['EXPAND'] = 'Genişlet';
$_lang['FULL'] = 'Tam';
$_lang['LIMITED'] = 'Sınırlı';
$_lang['TYPE'] = 'Tip';
$_lang['LEVEL'] = 'Düzey';
$_lang['MAX_LEVEL'] = 'En fazla düzey';
$_lang['LINK'] = 'Link';
$_lang['ELXIS_LINK'] = 'Elxis linki';
$_lang['SEPARATOR'] = 'Ayırıcı';
$_lang['WRAPPER'] = 'Sarmalayıcı';
$_lang['WARN_DELETE_MENUITEM'] = 'Bu mönü maddesine silmek istediğinize emin misiniz? Onunla bağlantılı alt maddeler de silinecek!';
$_lang['SEL_MENUITEM_TYPE'] = 'Mönü maddesi tipini seçin';
$_lang['LINK_LINK_DESC'] = 'Bir Elxis sayfasına link.';
$_lang['LINK_URL_DESC'] = 'Harici bir sayfaya standart link.';
$_lang['LINK_SEPARATOR_DESC'] = 'Link olmadan metin.';
$_lang['LINK_WRAPPER_DESC'] = 'Site içinde gösterilecek harici bir sayfaya link.';
$_lang['EXPAND_DESC'] = 'Eğer destekleniyorsa, bir alt mönü oluşturur. Sınırlı genişletme ilk maddesi, tam genişletme ise ağacın tümünü gösterir.';
$_lang['LINK_TARGET'] = 'Linkin hedefi';
$_lang['SELF_WINDOW'] = 'Mevcut pencerede';
$_lang['NEW_WINDOW'] = 'Yeni pencerede';
$_lang['PARENT_WINDOW'] = 'Ana pencerede';
$_lang['TOP_WINDOW'] = 'Üst pencerede';
$_lang['NONE'] = 'Hiçbiri';
$_lang['ELXIS_INTERFACE'] = 'Elxis arayüzü';
$_lang['ELXIS_INTERFACE_DESC'] = 'index.php için linkler modüller içeren normal sayfalar üretirken inner.php için linkler yalnızca ana bileşen sayfasının görünür olduğu sayfalar üretir (açılır pencereler için kullanışlıdır).';
$_lang['FULL_PAGE'] = 'Tam sayfa';
$_lang['ONLY_COMPONENT'] = 'Yalnızca bileşen';
$_lang['POPUP_WINDOW'] = 'Açılır pencere';
$_lang['TYPICAL_POPUP'] = 'Tipik açılır pencere';
$_lang['LIGHTBOX_WINDOW'] = 'Hafif kutu türü açılır pencere';
$_lang['PARENT_ITEM'] = 'Ana Madde';
$_lang['PARENT_ITEM_DESC'] = 'Başka bir mönü maddesini seçerek bu mönü maddesini onun alt mönüsü yapın.';
$_lang['POPUP_WIDTH_DESC'] = 'Piksel cinsinden açılır pencere ya da sarmalayıcı genişliği. Otomatik seçim için 0.';
$_lang['POPUP_HEIGHT_DESC'] = 'Piksel cinsinden açılır pencere ya da sarmalayıcı yüksekliği. Otomatik seçim için 0.';
$_lang['MUST_FIRST_SAVE'] = 'Önce bu maddeyi kaydetmelisiniz!';
$_lang['CONTENT'] = 'İçerik';
$_lang['SECURE_CONNECT'] = 'Güvenli bağlantı';
$_lang['SECURE_CONNECT_DESC'] = 'Ancak ve ancak genel ayarlarda açıksa bir SSL sertifikası yüklüyse.';
$_lang['SEL_COMPONENT'] = 'Bileşen seç';
$_lang['LINK_GENERATOR'] = 'Link oluşturma';
$_lang['URL_HELPER'] = 'Link oluşturmak istediğiniz harici sayfanın tam adresini yazıp linke bir ad verin. 
	Bu linki açılır pencerede ya da hafif kutu pencerede açabilirsiniz. Seçeneklerle açılır / hafif kutu pencerenin boyutlarını 
	dilediğiniz gibi ayarlayın.';
$_lang['SEPARATOR_HELPER'] = 'Ayırıcı bir link değil bir metindir. Bu nedenle de Link seçeneği önem taşımaz. 
	Alt mönüleri ayırmak için ya da başka amaçlarla, tıklanamaz bir başlık olarak kullanın.';
$_lang['WRAPPER_HELPER'] = 'Sarmalayıcı HERHANGİ bir sayfanın sitenizde i-frame içinde gösterilmesini sağlar. 
	Harici sayfalar sitenizin parçası gibi görünecektir. Sarmalanan sayfanın tam adresini 
	yazmalısınız. Linki açılır pencerede ya da hafif kutu pencerede açabilirsiniz. Seçeneklerle açılır / hafif kutu pencerenin boyutlarını 
	dilediğiniz gibi ayarlayın.';
$_lang['TIP_INTERFACE'] = '<strong>İpucu</strong><br />Açılır / Hafif kutu pencere amaçlıyorsanız <strong>Yalnızca Bileşen</strong>i Elxis arayüzü olarak  seçin.';
$_lang['COMP_NO_PUBLIC_IFACE'] = 'Bu bileşenin bir kullanıcı arayüzü yok!';
$_lang['STANDARD_LINKS'] = 'Standart linkler';
$_lang['BROWSE_ARTICLES'] = 'Yazıları tara';
$_lang['ACTIONS'] = 'Eylemler';
$_lang['LINK_TO_ITEM'] = 'Bu maddeye link';
$_lang['LINK_TO_CAT_RSS'] = 'Kategorinin RSS beslemesine link';
$_lang['LINK_TO_CAT_ATOM'] = 'Kategorinin ATOM beslemesine link';
$_lang['LINK_TO_CAT_OR_ARTICLE'] = 'Kategori ya da yazıya link';
$_lang['ARTICLE'] = 'Yazı';
$_lang['ARTICLES'] = 'Yazı';
$_lang['ASCENDING'] = 'Artan';
$_lang['DESCENDING'] = 'Azalan';
$_lang['LAST_MODIFIED'] = 'En son değiştirilme';
$_lang['CAT_CONT_ART'] = "%s kategorisinde %s yazı var."; //fill in by CATEGORY NAME and NUMBER
$_lang['ART_WITHOUT_CAT'] = "Kategorisiz %s yazı var."; //fill in by NUMBER
$_lang['NO_ITEMS_DISPLAY'] = 'Gösterilecek madde yok!';
$_lang['ROOT'] = 'Kök'; //root category
$_lang['COMP_FRONTPAGE'] = "%s içeriğin ön sayfası"; //fill in by COMPONENT NAME
$_lang['LINK_TO_CAT'] = 'İçerik kategorisine link';
$_lang['LINK_TO_CAT_ARTICLE'] = 'Kategori yazısına link';
$_lang['LINK_TO_AUT_PAGE'] = 'Bağımsız sayfaya link';
$_lang['SPECIAL_LINK'] = 'Özel link';
$_lang['FRONTPAGE'] = 'Ön sayfa';
$_lang['BASIC_SETTINGS'] = 'Temel ayarlar';
$_lang['OTHER_OPTIONS'] = 'Diğer seçenekler';

?>