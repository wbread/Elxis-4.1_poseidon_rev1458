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

defined('_ELXIS_') or die ('Doğrudan erişime izin yok.');


$_lang = array();
$_lang['INSTALLATION'] = 'Kurulum';
$_lang['STEP'] = 'Adım';
$_lang['VERSION'] = 'Sürüm';
$_lang['VERSION_CHECK'] = 'Sürüm kontrolü';
$_lang['STATUS'] = 'Statü';
$_lang['REVISION_NUMBER'] = 'Revizyon numarası';
$_lang['RELEASE_DATE'] = 'Yayınlanma tarihi';
$_lang['ELXIS_INSTALL'] = 'Elxis kurulumu';
$_lang['LICENSE'] = 'Lisans';
$_lang['VERSION_PROLOGUE'] = 'Elxis CMS kurulumu başlamak üzere. Yükleyeceğiniz Elxis dosyasının sürümü  
	aşağıda gösterilmekte. Bunun <a href="http://www.elxis.org" target="_blank">elxis.org</a> adresindeki en son sürüm olduğundan eminseniz devam edin';
$_lang['BEFORE_BEGIN'] = 'Başlamadan önce';
$_lang['BEFORE_DESC'] = 'İlerlemeden önce aşağıdakileri dikkatle okuyun.';
$_lang['DATABASE'] = 'Veri tabanı';
$_lang['DATABASE_DESC'] = 'Elxis tarafından verilerin depolanması için boş bir veri tabanı oluşturun.  
	Bir <strong>MySQL</strong> veritabanı kullanılmasını öneririz. Elxis PostgreSQL ve SQLite 3 gibi  
	veri tiplerini desteklese de yalnızca MySQL üzerinde tamamen sınandı.  
	Boş MySQL veri tabanını sitenizin hosting kontrol panelinden (CPanel, Plesk, ISP Config, vs.) ya da  
	phpMyAdmin gibi bir veri tabanı kullanarak yapın. Veri tabanı için bir <strong>ad</strong> belirleyip veri tabanını oluşturun. 
	Ardından bir veritabanı <strong>kullanıcısı</strong> belirleyip yeni oluşturduğunuz veri tabanına atayın.  
	Veri tabanını, kullanıcı ve şifreyi not edin; kurulum esnasında gerekecektir.';
$_lang['REPOSITORY'] = 'Depolama';
$_lang['REPOSITORY_DESC'] = 'Elxis ön bellek dosyalarını, kütük dosyalarını, oturumları, yedeklemeleri saklamak için özel bir dizin kullanır. Kurulumda bu dizin <strong>repository</strong> adını taşır ve Elxis kök dizin içinde yer alır. 
Bu dizin	<strong>yazılabilir olmalıdır</strong>! Önerimiz bu dizini <strong>yeniden adlandırmanız</strong> ve web üzerinden erişilemeyecek bir yere <strong>taşımanızdır</strong>. Bu taşıma sonrasında, eğer <strong>open basedir</strong> korumasını PHP içinde gerçekleştirdiyseniz izin verilen yollar içine yeni depolama yolunu da eklemeniz gerekir.';
$_lang['REPOSITORY_DEFAULT'] = 'Depolama varsayılan konumunda!';
$_lang['SAMPLE_ELXPATH'] = 'Örnek Elxis yolu';
$_lang['DEF_REPOPATH'] = 'Varsayılan depolama yolu';
$_lang['REQ_REPOPATH'] = 'Önerilen depolama yolu';
$_lang['CONTINUE'] = 'Devam';
$_lang['I_AGREE_TERMS'] = 'EPL koşullarını okudum, anladım ve kabul ediyorum';
$_lang['LICENSE_NOTES'] = 'Elxis CMS <strong>Elxis Public License</strong> (EPL) ile dağıtılan ücretsiz bir yazılımdır. 
	Kuruluma devam etmek ve Elxis yazılımını kullanmak için EPL koşullarını okuyup kabul etmelisiniz. Elxis lisansını dikkatle okuyun ve kabul ediyorsanız sayfa sonundaki kutuyu işaretleyin. Kabul etmiyorsanız kurulumu durdurup Elxis dosyalarını silin.';
$_lang['SETTINGS'] = 'Ayarlar';
$_lang['SITE_URL'] = 'Site adresi';
$_lang['SITE_URL_DESC'] = 'Ardında eğik çizgi olmadan (örneğin. http://www.ornek.com)';
$_lang['REPOPATH_DESC'] = 'Elxis depolama yolu için eksiksiz yol. Varsayılan yol ve ad korunacaksa boş bırakın.';
$_lang['SETTINGS_DESC'] = 'Gerekli Elxis ayarlarını belirleyin. Bazı parametrelerin Elxis kurulumundan önce belirlenmesi gerekir. Kurlum sonrasında yönetim paneline giriş yapıp kalan parametrelerin tamamlayın. 
	Bu sizin yönetici olarak ilk göreviniz olmalı.';
$_lang['DEF_LANG'] = 'Varsayılan dil';
$_lang['DEFLANG_DESC'] = 'İçerik varsayılan dilde yazılır. Diğer dillerde içerik ise varsayılan dildeki içeriğin çevirisidir.';
$_lang['ENCRYPT_METHOD'] = 'Şifreleme yöntemi';
$_lang['ENCRYPT_KEY'] = 'Şifreleme anahtarı';
$_lang['AUTOMATIC'] = 'Otomatik';
$_lang['GEN_OTHER'] = 'Yenisini üret';
$_lang['SITENAME'] = 'Sitenin adı';
$_lang['TYPE'] = 'Tip';
$_lang['DBTYPE_DESC'] = 'MySQL kullanmanızı öneririz. Listede sisteminiz ve Elxis kurulumu tarafından desteklenen sürücüler yer almaktadır.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Tablolar için öntakı';
$_lang['DSN_DESC'] = 'Veri tabanına bağlanmak için kullanıma hazır bir Veri Kaynak Adı da verebilirsiniz.';
$_lang['SCHEME'] = 'Şema';
$_lang['SCHEME_DESC'] = 'SQLite gibi bir veri tabanı kullanılacaksa, dosyanın adresi.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'MySQL için varsayılan port 3306. Otomatik seçim içim 0 olarak bırakın.';
$_lang['FTPPORT_DESC'] = 'FTP için varsayılan port 21. Otomatik seçim için 0 olarak bırakın.';
$_lang['USE_FTP'] = 'FTP kullan';
$_lang['PATH'] = 'Yol';
$_lang['FTP_PATH_INFO'] = 'FTP kök dizininden Elxis kurulumuna göreli yol (örneğin: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'FTP ayarlarını kontrol et';
$_lang['CHECK_DB_SETS'] = 'Veri tabanı ayarlarını kontrol et';
$_lang['DATA_IMPORT'] = 'Data aktarımı';
$_lang['SETTINGS_ERRORS'] = 'Verdiğiniz ayarlara hatalar var!';
$_lang['NO_QUERIES_WARN'] = 'İlk veriler veri tabanına aktarıldı ama herhangi bir işlem yapılmamış gibi görünüyor. Devam etmeden önce verilerin aktarılıp aktarılmadığını kontrol edin.';
$_lang['RETRY_PREV_STEP'] = 'Önceki adımı yeniden dene';
$_lang['INIT_DATA_IMPORTED'] = 'İlk verilen veri tabanına aktarıldı.';
$_lang['QUERIES_EXEC'] = "%s SQL işlemi gerçekleştirildi."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Yönetici hesabı';
$_lang['CONFIRM_PASS'] = 'Şifreyi onayla';
$_lang['AVOID_COMUNAMES'] = 'admin ya da administrator gibi bildik adlardan kaçının.';
$_lang['YOUR_DETAILS'] = 'Ayrıntılar';
$_lang['PASS_NOMATCH'] = 'Şifreler aynı değil!';
$_lang['REPOPATH_NOEX'] = 'Depolama yolu mevcut değil!';
$_lang['FINISH'] = 'Bitir';
$_lang['FRIENDLY_URLS'] = 'Dostane URLler';
$_lang['FRIENDLY_URLS_DESC'] = 'Açık hale getirmenizi öneririz. Elxis htaccess.txt dosyasını <strong>.htaccess</strong> olarak adlandırmaya çalışacak. Eğer bir .htaccess dosyası aynı dizinde mevcutsa mevcut dosya silinir.';
$_lang['GENERAL'] = 'Genel';
$_lang['ELXIS_INST_SUCC'] = 'Elxis kurulumu başarıyla tamamlandı.';
$_lang['ELXIS_INST_WARN'] = 'Elxis kurulumu uyarılarla tamamlandı.';
$_lang['CNOT_CREA_CONFIG'] = 'Kök Elxis dizininde <strong>configuration.php</strong> dosyası yaratılamadı.';
$_lang['CNOT_REN_HTACC'] = '<strong>htaccess.txt</strong> dosyası <strong>.htaccess</strong> olarak adlandırılamadı';
$_lang['CONFIG_FILE'] = 'Ayarlar dosyası';
$_lang['CONFIG_FILE_MANUAL'] = 'Kendiniz configuration.php adında bir dosya oluşturun ve aşağıdaki kodu kopyalayıp dosyanın içine yapıştırın.';
$_lang['REN_HTACCESS_MANUAL'] = '<strong>htaccess.txt</strong> dosyasını kendiniz <strong>.htaccess</strong> olarak yeniden adlandırın';
$_lang['WHAT_TODO'] = 'Sırada ne var?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Güvenliği artırmak için yönetici dizinini (<em>estia</em>) yeniden adlandırabilirsiniz. 
	Eğer bunu yaparsanız .htaccess file dosyası içinde de bu ad değişikliğini gerçekleştirmelisiniz.';
$_lang['LOGIN_CONFIG'] = 'Yönetim paneline giriş yapın ve diğer kurulum ayarlarını tamamlayın.';
$_lang['VISIT_NEW_SITE'] = 'Yeni web sitesine gidin';
$_lang['VISIT_ELXIS_SUP'] = 'Elxis destek sitesine gidin';
$_lang['THANKS_USING_ELXIS'] = 'Elxis CMS kullandığınız için teşekkürler';

?>