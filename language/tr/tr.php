<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: tr-TR (Turkish - Turkey) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Hakan Gür ( http://www.dildersleri.gen.tr )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Doğrudan erişime izin yok..');


$locale = array('en_GB.utf8', 'en_GB.UTF-8', 'en.UTF8', 'en.UTF-8', 'en_GB', 'en', 'english', 'england'); //utf-8 locales array; DO NOT CHANGE / DEĞİŞTİRMEYİN!

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%d %b %Y"; //example: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%d %B %Y"; //example: December 25, 2010
$_lang['DATE_FORMAT_4'] = "%d %b %Y, %H:%M"; //example: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%d %b %Y, %H:%M"; //example: December 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%d %b %Y, %H:%M:%S"; //example: December 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //example: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //example: Saturday Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //example: Saturday December 25, 2010
$_lang['DATE_FORMAT_10'] = "%B %d %Y, %A, %H:%M"; //example: Saturday December 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%B %d %Y, %A, %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%B %d %Y, %a,  %H:%M"; //example: Sat December 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%B %d %Y, %A, %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Ocak';
$_lang['FEBRUARY'] = 'Şubat';
$_lang['MARCH'] = 'Mart';
$_lang['APRIL'] = 'Nisan';
$_lang['MAY'] = 'Mayıs';
$_lang['JUNE'] = 'Haziran';
$_lang['JULY'] = 'Temmuz';
$_lang['AUGUST'] = 'Ağustos';
$_lang['SEPTEMBER'] = 'Eylül';
$_lang['OCTOBER'] = 'Ekim';
$_lang['NOVEMBER'] = 'Kasım';
$_lang['DECEMBER'] = 'Aralık';
$_lang['JANUARY_SHORT'] = 'Ocak';
$_lang['FEBRUARY_SHORT'] = 'Şubat';
$_lang['MARCH_SHORT'] = 'Mart';
$_lang['APRIL_SHORT'] = 'Nisan';
$_lang['MAY_SHORT'] = 'Mayıs';
$_lang['JUNE_SHORT'] = 'Haziran';
$_lang['JULY_SHORT'] = 'Temmuz';
$_lang['AUGUST_SHORT'] = 'Ağustos';
$_lang['SEPTEMBER_SHORT'] = 'Eylül';
$_lang['OCTOBER_SHORT'] = 'Ekim';
$_lang['NOVEMBER_SHORT'] = 'Kasım';
$_lang['DECEMBER_SHORT'] = 'Aralık';
//day names
$_lang['MONDAY'] = 'Pazartesi';
$_lang['THUESDAY'] = 'Salı';
$_lang['WEDNESDAY'] = 'Çarşamba';
$_lang['THURSDAY'] = 'Perşembe';
$_lang['FRIDAY'] = 'Cuma';
$_lang['SATURDAY'] = 'Cumartesi';
$_lang['SUNDAY'] = 'Pazar';
$_lang['MONDAY_SHORT'] = 'Pzt';
$_lang['THUESDAY_SHORT'] = 'Sal';
$_lang['WEDNESDAY_SHORT'] = 'Çar';
$_lang['THURSDAY_SHORT'] = 'Per';
$_lang['FRIDAY_SHORT'] = 'Cum';
$_lang['SATURDAY_SHORT'] = 'Cmt';
$_lang['SUNDAY_SHORT'] = 'Paz';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Performans Monitörü';
$_lang['ITEM'] = 'Madde';
$_lang['INIT_FILE'] = 'Başlangıç dosyası';
$_lang['EXEC_TIME'] = 'Sürdürme zamanı';
$_lang['DB_QUERIES'] = 'DB talepleri';
$_lang['ERRORS'] = 'Hatalar';
$_lang['SIZE'] = 'Boyut';
$_lang['ENTRIES'] = 'Girişler';

/* general */
$_lang['HOME'] = 'Ana Sayfa';
$_lang['YOU_ARE_HERE'] = 'Buradasınız';
$_lang['CATEGORY'] = 'Kategori';
$_lang['DESCRIPTION'] = 'Açıklama';
$_lang['FILE'] = 'Dosya';
$_lang['IMAGE'] = 'Resim';
$_lang['IMAGES'] = 'Resimler';
$_lang['CONTENT'] = 'İçerik';
$_lang['DATE'] = 'Tarih';
$_lang['YES'] = 'Evet';
$_lang['NO'] = 'Hayır';
$_lang['NONE'] = 'Hiç';
$_lang['SELECT'] = 'Seç';
$_lang['LOGIN'] = 'Giriş';
$_lang['LOGOUT'] = 'Çıkış';
$_lang['WEBSITE'] = 'Web sitesi';
$_lang['SECURITY_CODE'] = 'Güvenlik kodu';
$_lang['RESET'] = 'Yenile';
$_lang['SUBMIT'] = 'Gönder';
$_lang['REQFIELDEMPTY'] = 'Gereken bir ya da daha fazla alan boş!';
$_lang['FIELDNOEMPTY'] = "%s boş olmamalı!";
$_lang['FIELDNOACCCHAR'] = "%s kabul edilmeyen harfler içeriyor!";
$_lang['INVALID_DATE'] = 'Geçersiz tarih!';
$_lang['INVALID_NUMBER'] = 'Geçersiz sayı!';
$_lang['INVALID_URL'] = 'Geçersiz web adresi!';
$_lang['FIELDSASTERREQ'] = 'Formda * ile belirlenen alanlar boş kalamaz.';
$_lang['ERROR'] = 'Hata';
$_lang['REGARDS'] = 'Saygılar';
$_lang['NOREPLYMSGINFO'] = 'Bilgi amaçlı gönderilen bu mesaja yanıt yazmayın.';
$_lang['LANGUAGE'] = 'Dil';
$_lang['PAGE'] = 'Sayfa';
$_lang['PAGEOF'] = "Sayfa: %s / %s";
$_lang['OF'] = '/';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "%s - %s / %s başlık sergileniyor";
$_lang['HITS'] = 'Tıklanma';
$_lang['PRINT'] = 'Yazdır';
$_lang['BACK'] = 'Geri';
$_lang['PREVIOUS'] = 'Önceki';
$_lang['NEXT'] = 'Sonraki';
$_lang['CLOSE'] = 'Kapat';
$_lang['CLOSE_WINDOW'] = 'Pencereyi kapat';
$_lang['COMMENTS'] = 'Yorumlar';
$_lang['COMMENT'] = 'Yorum';
$_lang['PUBLISH'] = 'Yayınla';
$_lang['DELETE'] = 'Sil';
$_lang['EDIT'] = 'Güncelle';
$_lang['COPY'] = 'Kopyala';
$_lang['SEARCH'] = 'Ara';
$_lang['PLEASE_WAIT'] = 'Bekleyin ...';
$_lang['ANY'] = 'Herhangi';
$_lang['NEW'] = 'Yeni';
$_lang['ADD'] = 'Ekle';
$_lang['VIEW'] = 'İzle';
$_lang['MENU'] = 'Mönü';
$_lang['HELP'] = 'Yardım';
$_lang['TOP'] = 'Üst';
$_lang['BOTTOM'] = 'Alt';
$_lang['LEFT'] = 'Sol';
$_lang['RIGHT'] = 'Sağ';
$_lang['CENTER'] = 'Orta';

/* xml */
$_lang['CACHE'] = 'Ön bellek';
$_lang['ENABLE_CACHE_D'] = 'Bu madde için ön bellek açılsın mı?';
$_lang['YES_FOR_VISITORS'] = 'Evet, konuklar için';
$_lang['YES_FOR_ALL'] = 'Evet, tümü için';
$_lang['CACHE_LIFETIME'] = 'Ön bellek saklama süresi';
$_lang['CACHE_LIFETIME_D'] = 'Dakika türünden, bu madde için ön belleğin yenilenme süresi.';
$_lang['NO_PARAMS'] = 'Parametre yok!';
$_lang['STYLE'] = 'Stil';
$_lang['ADVANCED_SETTINGS'] = 'İleri düzey ayarlar';
$_lang['CSS_SUFFIX'] = 'CSS öntakısı';
$_lang['CSS_SUFFIX_D'] = 'Modül CSS sınıfına bir öntakı eklenecek.';
$_lang['MENU_TYPE'] = 'Mönü tipi';
$_lang['ORIENTATION'] = 'Yerleşim';
$_lang['SHOW'] = 'Göster';
$_lang['HIDE'] = 'Gizle';
$_lang['GLOBAL_SETTING'] = 'Genel ayarlar';

/* users & authentication */
$_lang['USERNAME'] = 'Kullanıcı adı';
$_lang['PASSWORD'] = 'Şifre';
$_lang['NOAUTHMETHODS'] = 'Hiçbir kimlik doğrulama yöntemi belirlenmedi';
$_lang['AUTHMETHNOTEN'] = '%s doğrulama yöntemi açık değil';
$_lang['PASSTOOSHORT'] = 'Şifreniz çok kısa';
$_lang['USERNOTFOUND'] = 'Kullanıcı bulunamadı';
$_lang['INVALIDUNAME'] = 'Geçersiz kullanıcı adı';
$_lang['INVALIDPASS'] = 'Geçersiz şifre';
$_lang['AUTHFAILED'] = 'Kimlik doğrulama başarısız';
$_lang['YACCBLOCKED'] = 'Hesabınız durduruldu';
$_lang['YACCEXPIRED'] = 'Hesabınızın süresi doldu';
$_lang['INVUSERGROUP'] = 'Geçersiz kullanıcı grubu';
$_lang['NAME'] = 'Ad';
$_lang['FIRSTNAME'] = 'Ad';
$_lang['LASTNAME'] = 'Soyadı';
$_lang['EMAIL'] = 'E-posta';
$_lang['INVALIDEMAIL'] = 'Geçersiz e-posta adresi';
$_lang['ADMINISTRATOR'] = 'Yönetici';
$_lang['GUEST'] = 'Konuk';
$_lang['EXTERNALUSER'] = 'Harici kullanıcı';
$_lang['USER'] = 'Kullanıcı';
$_lang['GROUP'] = 'Grup';
$_lang['NOTALLOWACCPAGE'] = 'Bu sayfaya erişim izniniz yok!';
$_lang['NOTALLOWACCITEM'] = 'Bu maddeye erişim izniniz yok!';
$_lang['NOTALLOWMANITEM'] = 'Bu maddeyi yönetme izniniz yok!';
$_lang['NOTALLOWACTION'] = 'Bu eylemi gerçekleştirme izniniz yok!';
$_lang['NEED_HIGHER_ACCESS'] = 'Bu eylem için daha yüksek bir erişim düzeyi gerekli!';
$_lang['AREYOUSURE'] = 'Emin misiniz?';

/* highslide */
$_lang['LOADING'] = 'Yükleniyor ...';
$_lang['CLICK_CANCEL'] = 'İptal için tıklayın';
$_lang['MOVE'] = 'Taşı';
$_lang['PLAY'] = 'Oynat';
$_lang['PAUSE'] = 'Duraklat';
$_lang['RESIZE'] = 'Boyutunu değiştir';

/* admin */
$_lang['ADMINISTRATION'] = 'Yönetim';
$_lang['SETTINGS'] = 'Ayarlar';
$_lang['DATABASE'] = 'Veri tabanı';
$_lang['ON'] = 'Açık';
$_lang['OFF'] = 'Kapalı';
$_lang['WARNING'] = 'Uyarı';
$_lang['SAVE'] = 'Kaydet';
$_lang['APPLY'] = 'Uygula';
$_lang['CANCEL'] = 'İptal';
$_lang['LIMIT'] = 'Sınır';
$_lang['ORDERING'] = 'Sıralama';
$_lang['NO_RESULTS'] = 'Sonuç bulunmadı!';
$_lang['CONNECT_ERROR'] = 'Bağlantı hatası';
$_lang['DELETE_SEL_ITEMS'] = 'Seçilenler silinsin mi?';
$_lang['TOGGLE_SELECTED'] = 'Seçilenleri değiştir';
$_lang['NO_ITEMS_SELECTED'] = 'Seçili madde yok!';
$_lang['ID'] = 'Kimlik';
$_lang['ACTION_FAILED'] = 'Eylem başarısız!';
$_lang['ACTION_SUCCESS'] = 'Eylem başarıyla tamamlandı!';
$_lang['NO_IMAGE_UPLOADED'] = 'Resim yüklenmedi';
$_lang['NO_FILE_UPLOADED'] = 'Dosya yüklenmedi';
$_lang['MODULES'] = 'Modüller';
$_lang['COMPONENTS'] = 'Bileşenler';
$_lang['TEMPLATES'] = 'Şablonlar';
$_lang['SEARCH_ENGINES'] = 'Arama motorları';
$_lang['AUTH_METHODS'] = 'Kimlik doğrulama yöntemleri';
$_lang['CONTENT_PLUGINS'] = 'İçerik eklentileri';
$_lang['PLUGINS'] = 'Eklentiler';
$_lang['PUBLISHED'] = 'Yayınlandı';
$_lang['ACCESS'] = 'Erişim';
$_lang['ACCESS_LEVEL'] = 'Erişim düzeyi';
$_lang['TITLE'] = 'Başlık';
$_lang['MOVE_UP'] = 'Yukarı';
$_lang['MOVE_DOWN'] = 'Aşağı';
$_lang['WIDTH'] = 'Genişlik';
$_lang['HEIGHT'] = 'Yükseklik';
$_lang['ITEM_SAVED'] = 'Kaydedildi';
$_lang['FIRST'] = 'İlk';
$_lang['LAST'] = 'Son';
$_lang['SUGGESTED'] = 'Önerilen';
$_lang['VALIDATE'] = 'Doğrula';
$_lang['NEVER'] = 'Hiç';
$_lang['ALL'] = 'Tümü';
$_lang['ALL_GROUPS_LEVEL'] = "%s düzeyin tüm grupları";
$_lang['REQDROPPEDSEC'] = 'İsteğiniz güvenlik gerekçesiyle gerçekleşemedi. Yeniden deneyin.';
$_lang['PROVIDE_TRANS'] = 'Çeviri ekleyin!';
$_lang['AUTO_TRANS'] = 'Otomatik çeviri';
$_lang['STATISTICS'] = 'İstatistik';
$_lang['UPLOAD'] = 'Yükle';
$_lang['MORE'] = 'Devamı';

?>
