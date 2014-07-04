<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-20132 Elxis.org. All rights reserved.
* @description: hr-HR (Hrvatski - Hrvatska) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Robert Kovalek
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] ='Upravljačka ploča';
$_lang['GENERAL_SITE_SETS'] = 'Opća podešenja portala';
$_lang['LANGS_MANAGER'] = 'Upravitelj jezika';
$_lang['MANAGE_SITE_LANGS'] = 'Upravljanje jezicima portala ';
$_lang['USERS'] = 'Korisnici';
$_lang['MANAGE_USERS'] = 'Kreiranje, uređivanje i brisanje korisničkih naloga ';
$_lang['USER_GROUPS'] = 'Korisničke grupe';
$_lang['MANAGE_UGROUPS'] = 'Upravljanje grupama korisnika ';
$_lang['MEDIA_MANAGER'] = 'Upravitelj medija';
$_lang['MEDIA_MANAGER_INFO'] = 'Upravljanje multimedijskim dokumentima ';
$_lang['ACCESS_MANAGER'] = 'Upravitelj pristupa';
$_lang['MANAGE_ACL'] = 'Upravljanje listama za kontrolu pristupa ';
$_lang['MENU_MANAGER'] = 'Upravitelj menia';
$_lang['MANAGE_MENUS_ITEMS'] = 'Upravljanje menijima i stavkama menia ';
$_lang['FRONTPAGE'] = 'Naslovna';
$_lang['DESIGN_FRONTPAGE'] = 'Dizajn naslovnice portala';
$_lang['CATEGORIES_MANAGER'] = 'Upravitelj kategorija ';
$_lang['MANAGE_CONT_CATS'] = 'Upravljanje kategorijama sadržaja';
$_lang['CONTENT_MANAGER'] = 'Upravitelj sadržaja';
$_lang['MANAGE_CONT_ITEMS'] = 'Upravljanje stavkama sadržaja';
$_lang['MODULES_MANAGE_INST'] = 'Upravljanje modulima i instalacija novih. ';
$_lang['PLUGINS_MANAGE_INST'] = 'Upravljanje priključcima sadržaja i instalacija novih. ';
$_lang['COMPONENTS_MANAGE_INST'] = 'Upravljanje komponentama i instalacija novih ';
$_lang['TEMPLATES_MANAGE_INST'] = 'Upravljanje šablonama i instalacija novih. ';
$_lang['SENGINES_MANAGE_INST'] = 'Upravljanje pretraživačima i instalacija novih. ';
$_lang['MANAGE_WAY_LOGIN'] = 'Upravljanje načinima prijave korisnika na portal. ';
$_lang['TRANSLATOR'] = 'Prevoditelj';
$_lang['MANAGE_MLANG_CONTENT'] = 'Upravljanje višejezičkim sadržajem ';
$_lang['LOGS'] = 'Logovi';
$_lang['VIEW_MANAGE_LOGS'] = 'Prikaz i upravljanje dokumentima evidencije';
$_lang['GENERAL'] = 'Općenito';
$_lang['WEBSITE_STATUS'] = 'Status portala';
$_lang['ONLINE'] = 'Dostupan';
$_lang['OFFLINE'] = 'Nedostupan';
$_lang['ONLINE_ADMINS'] = 'Dostupan samo za administratore ';
$_lang['OFFLINE_MSG'] = 'Poruka o nedopstupnosti';
$_lang['OFFLINE_MSG_INFO'] = 'Ostavite ovo polje prazno za prikaz automatske višejezičke poruke';
$_lang['SITENAME'] = 'Ime portala';
$_lang['URL_ADDRESS'] = 'URL adresa';
$_lang['REPO_PATH'] = 'Putanja spremišta';
$_lang['REPO_PATH_INFO'] = 'Puna putanja do Elxis spremišta. Ostavite prazno za standardnu
	lokaciju (elxis_root/repository/). Savjetujemo da ovaj direktorij preimenujete i premjestite iznad WWW direktorija!';
$_lang['FRIENDLY_URLS'] = 'Prijateljski URL-ovi';
$_lang['SEF_INFO'] = 'Ako se podesi na DA (preporučeno) preimenujte htaccess.txt dokumeat u .htaccess.';
$_lang['STATISTICS_INFO'] = 'Omogućiti prikupljanje statističkih podataka o posjećenosti portala? ';
$_lang['GZIP_COMPRESSION'] = 'Gzip kompresija';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis će kompresirati dokument koristeći gzip prije slanja u preglednik i tako uštedjeti 70% do 80% propusnosti.';
$_lang['DEFAULT_ROUTE'] = 'Uobičajena putanja';
$_lang['DEFAULT_ROUTE_INFO'] = 'Elxis će formatirani URI koji će se koristiti kao Naslovnica portala';
$_lang['META_DATA'] = 'META podataci';
$_lang['META_DATA_INFO'] = 'Kratak opis web portala';
$_lang['KEYWORDS'] = 'Ključne riječi';
$_lang['KEYWORDS_INFO'] = 'Nekoliko riječi razdvojenih zarezom';
$_lang['STYLE_LAYOUT'] = 'Stil i raspored ';
$_lang['SITE_TEMPLATE'] = 'Šablona portala';
$_lang['ADMIN_TEMPLATE'] = 'Administratorska šablona';
$_lang['ICONS_PACK'] = 'Paket ikona';
$_lang['LOCALE'] = 'Lokal';
$_lang['TIMEZONE'] = 'Vremenska zona';
$_lang['MULTILINGUISM'] = 'Karakteristike';
$_lang['MULTILINGUISM_INFO'] = 'Omogućava vam da unesete tekstualne elemente na više od jednog jezika (prevodi).
	Ne uključujte ovo ako nećete koristiti jer će bez potrebe usporiti portal. Elxis interfejs 
	će i dalje biti višejezičan, čak i ako je ovde postavljeno NE.';
$_lang['CHANGE_LANG'] = "Promjena jezika";
$_lang['LANG_CHANGE_WARN'] = 'Ako ste promjenili glavni jezik možda postoji nedosljednost 
	između indikatora jezika i prijevoda u tabeli s prevodima.';
$_lang['CACHE'] = "Keš";
$_lang['CACHE_INFO'] = 'Elxis može sačuvati generirani XHTML kod pojedinačnih elemenata u cache-u za brže kasnije generiranje stranice.
	Ovo je glavno podešavanje, morate uključiti cache i za pojedinačne elemente (npr. module) za koje želite da budu cache.';
$_lang['APC_INFO'] = 'Alternativni PHP cache (APC) je Opcode cache za PHP. On mora biti podržan od strane vašeg web servera.
	Nije preporučljivo za okruženje dijeljenog hostinga. Elxis će koristiti ovo na posebnim stranicama kako bi ubrzao rad portala.';
$_lang['APC_ID_INFO'] = 'U slučaju više od 1 portala instaliranih na istom serveru identificirajte
	ih jedinstvenim brojem.';
$_lang['USERS_AND_REGISTRATION'] = 'Korisnici i registracija ';
$_lang['PRIVACY_PROTECTION'] = 'Zaštita privatnosti ';
$_lang['PASSWORD_NOT_SHOWN'] = 'Trenutna lozinka nije prikazana iz sigurnosnih razloga.
	Popunite ovo polje samo ako želite promjeniti lozinku.';
$_lang['DB_TYPE'] = 'Tip baze podataka';
$_lang['ALERT_CON_LOST'] = 'Ako ovo promjenite, veza sa trenutnom bazom podataka će biti izgubljena!';
$_lang['HOST'] = 'Host';
$_lang['PORT'] = 'Port';
$_lang['PERSISTENT_CON'] = 'Stalna veza';
$_lang['DB_NAME'] = 'Ime baze';
$_lang['TABLES_PREFIX'] = 'Prefiks tabela';
$_lang['DSN_INFO'] = 'Pripremljena DSN linija za povezivanje na bazu podataka.';
$_lang['SCHEME'] = 'Šema';
$_lang['SCHEME_INFO'] = 'Apsolutna putanja do dokumenta baze podataka, ako koristite bazu podataka kao što su SQLite. ';
$_lang['SEND_METHOD'] = 'Metoda slanja';
$_lang['SMTP_OPTIONS'] = 'SMTP opcije ';
$_lang['AUTH_REQ'] = 'Potrebna je potvrda identiteta ';
$_lang['SECURE_CON'] = 'Sigurna veza';
$_lang['SENDER_NAME'] = 'Ime pošiljatelja';
$_lang['SENDER_EMAIL'] = 'e-mail pošiljatelja';
$_lang['RCPT_NAME'] = 'Ime primaoca';
$_lang['RCPT_EMAIL'] = 'e-mail primaoca';
$_lang['TECHNICAL_MANAGER'] = 'Tehnički upravitelj';
$_lang['TECHNICAL_MANAGER_INFO'] = 'Tehnički upravitelj prima obavještenja o grešakama i sigurnosti.';
$_lang['USE_FTP'] = 'Upotreba FTP';
$_lang['PATH'] = 'Putanja';
$_lang['FTP_PATH_INFO'] = 'Relativna putanja od vršnog FTP direktorij Elxis instalacije';
$_lang['SESSION'] = 'Sesija';
$_lang['HANDLER'] = 'Rukovoditelj';
$_lang['HANDLER_INFO'] = 'Elxis može čuvati sesije kao dokumente u spremištu, ili u bazi podataka.
	Takođe, možete izabrati NIŠTA ako želite da PHP čuva sesije na mjestu predviđenom u postavkama servera.';
$_lang['FILES'] = 'Dokumenti';
$_lang['LIFETIME'] = "Trajanje";
$_lang['SESS_LIFETIME_INFO'] = 'Vrijeme neaktivnosti nakon kojega ističe sesija. ';
$_lang['CACHE_TIME_INFO'] = 'Poslije tog vremena sačuvane stavke se ponovo generiraju. ';
$_lang['MINUTES'] = 'Minuta';
$_lang['HOURS'] = 'Sata';
$_lang['MATCH_IP'] = 'Usporedi IP ';
$_lang['MATCH_BROWSER'] = 'Usporedi preglednike';
$_lang['MATCH_REFERER'] = 'Usporedi HTTP';
$_lang['MATCH_SESS_INFO'] = 'Omogućava naprednu usporedbu sesije. ';
$_lang['ENCRYPTION'] = 'Šifriranje';
$_lang['ENCRYPT_SESS_INFO'] = 'Šifriranje podataka sesije?';
$_lang['ERRORS'] = 'Greške';
$_lang['WARNINGS'] = 'Upozorenja';
$_lang['NOTICES'] = 'Obavijesti';
$_lang['NOTICE'] = 'Obavijest';
$_lang['REPORT'] = 'Izvještaj';
$_lang['REPORT_INFO'] = 'Nivo prijave grešaka. Na javnim portalima preporučeno je isključiti.';
$_lang['LOG'] = 'Log';
$_lang['LOG_INFO'] = 'Nivo bilježenja grešaka. Izaberite koje greške želite da Elxis zabilježi u sistemskom
	logu (repository/logs/).';
$_lang['ALERT'] = 'Upozorenje';
$_lang['ALERT_INFO'] = 'Slanje poruke o fatalnoj grešci tehničkom upravitelju portala.';
$_lang['ROTATE'] = 'Rotacija';
$_lang['ROTATE_INFO'] = 'Rotiranje logova s greškama na kraju svakog mjeseca. Preporučljivo.';
$_lang['DEBUG'] = 'Debug';
$_lang['MODULE_POS'] = 'Pozicije modula';
$_lang['MINIMAL'] = 'Minimalno';
$_lang['FULL'] = 'Puno';
$_lang['DISPUSERS_AS'] = 'Prikazivanje korisnika kao';
$_lang['USERS_REGISTRATION'] = 'Registracija korisnika';
$_lang['ALLOWED_DOMAIN'] = 'Dozvoljene domene';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Napišite ime domene (npr. elxis.org) s kojih će 
	sistem uvijek prihvaćati e-mail adrese.';
$_lang['EXCLUDED_DOMAINS'] = 'Izuzete domene ';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Zarezom odvojena lista imena domena (npr. lossajt.com, hakeri.com...)
	s kojih e-mail adresa neće biti prihvaćena tokom registracije.';
$_lang['ACCOUNT_ACTIVATION'] = 'Aktiviranje računa ';
$_lang['DIRECT'] = 'Direktno';
$_lang['MANUAL_BY_ADMIN'] = 'Ručno';
$_lang['PASS_RECOVERY'] = 'Zaboravljena lozinka';
$_lang['SECURITY'] = 'Sigurnost';
$_lang['SECURITY_LEVEL'] = 'Nivo sigurnosti';
$_lang['SECURITY_LEVEL_INFO'] = 'Povećanjem nivo sigurnosti neke opcije su omogućene prinudno 
	dok su neke isključene. Provjerite Elxis dokumentaciju za više informacija.';
$_lang['NORMAL'] = 'Normalno';
$_lang['HIGH'] = 'Visoko';
$_lang['INSANE'] = 'Paranoično';
$_lang['ENCRYPT_METHOD'] = 'Metoda šifriranja';
$_lang['AUTOMATIC'] = 'Automatski ';
$_lang['ENCRYPTION_KEY'] = 'Ključ šifre';
$_lang['ELXIS_DEFENDER'] = 'Elxis zaštitnik';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis zaštitnik štiti vaš portal od XSS i SQL napada.
	Ovaj moćni alat filtrira korisničke zahtjeve i blokira napade na Vaš portal. Isto tako, obavjesti će Vas o 
	napadu i zabilježiti ga. Možete izabrati tip filtera koji će biti primjenjeni ili čak da zaključa važne sistemske 
	dokumente. Što više filtera uključite, portal će sporije raditi. 
	Preporučljivo je izabrati G, C i F opcije. Provjerite Elxis dokumentaciju za više informacija.';
$_lang['SSL_SWITCH'] = 'SSL prekidač ';
$_lang['SSL_SWITCH_INFO'] = 'Elxis će se automatski prebaciti sa HTTP-a za HTTPS na stranicama gde je privatnost važna.
	Za administracijsku konzolu HTTPS će uvijek biti uključen. Ovo zahtjeva SSL certifikat!';
$_lang['PUBLIC_AREA'] = 'Javni prostor ';
$_lang['GENERAL_FILTERS'] = 'Općenita pravila';
$_lang['CUSTOM_FILTERS'] = 'Posebna pravila ';
$_lang['FSYS_PROTECTION'] = 'Zaštita dokumenata';
$_lang['CHECK_FTP_SETS'] = 'Provjera FTP postavki ';
$_lang['FTP_CON_SUCCESS'] = 'Povezivanje sa FTP serverom je bilo uspješno.';
$_lang['ELXIS_FOUND_FTP'] = 'Elxis instalacija je pronađena na FTP-u.';
$_lang['ELXIS_NOT_FOUND_FTP'] ='Elxis instalacija nije pronađena na FTP-u.';
$_lang['CAN_NOT_CHANGE'] = 'Ne može se izmjeniti. ';
$_lang['SETS_SAVED_SUCC'] = 'Postavke uspješno sačuvane';
$_lang['ACTIONS'] = 'Akcije ';
$_lang['BAN_IP_REQ_DEF'] = 'Kako biste zabranili IP adresu, obavezno je da omogućite najmanje jednu opciju u Elxis zaštitniku';
$_lang['BAN_YOURSELF'] = 'Da li pokušavate blokirati samog sebe? ';
$_lang['IP_AL_BANNED'] = 'Ovaj IP je već zabranjen! ';
$_lang['IP_BANNED'] = "IP adresa %s je zabranjena!";
$_lang['BAN_FAILED_NOWRITE'] = 'Ban nije uspio! Dokumenat repository/logs/defender_ban.php je zaključan.';
$_lang['ONLY_ADMINS_ACTION'] = 'Samo administratori mogu obavljati ovu akciju!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Ne možete odjaviti administratora! ';
$_lang['USER_LOGGED_OUT'] = 'Korisnik je odjavljen! ';
$_lang['SITE_STATISTICS'] = 'Statistike portala';
$_lang['SITE_STATISTICS_INFO'] = 'Pogledajte statistiku posjećenosti portala';
$_lang['BACKUP'] = 'Sigurnosna kopija';
$_lang['BACKUP_INFO'] = 'Kreiranje rezervne kopije portala i upravljanje postojećim kopijama.';
$_lang['BACKUP_FLIST'] = 'Lista postojećih kopija';
$_lang['TYPE'] = 'Tip';
$_lang['FILENAME'] = 'Ime dokumenta';
$_lang['SIZE'] = 'Veličina';
$_lang['NEW_DB_BACKUP'] = 'Nova sigurnosna kopija baze podataka';
$_lang['NEW_FS_BACKUP'] = 'Nova sigurnosna kopija dokumenata';
$_lang['FILESYSTEM'] = 'Sistem dokumenata ';
$_lang['DOWNLOAD'] = 'Preuzimanje';
$_lang['TAKE_NEW_BACKUP'] = "Napravite novu sigurnosnu kopiju?\Ovo može potrajati, budite strpljivi!";
$_lang['FOLDER_NOT_EXIST'] = "Direktorij %s ne postoji!";
$_lang['FOLDER_NOT_WRITE'] = "Direktorij %s nije otključan!";
$_lang['BACKUP_SAVED_INTO'] = "Sigurnosne kopije se čuvaju u %s";
$_lang['CACHE_SAVED_INTO'] = "Cache dokumenti se čuvaju u %s";
$_lang['CACHED_ITEMS'] = 'Cache stavke';
$_lang['ELXIS_ROUTER'] = 'Elxis router';
$_lang['ROUTING'] = 'Preusmjeravanje';
$_lang['ROUTING_INFO'] = 'Preusmeravanje korisničkih zahtjeva na proizvoljnu URL adresu.';
$_lang['SOURCE'] = 'Izvor';
$_lang['ROUTE_TO'] = 'Put do ';
$_lang['REROUTE'] = "Preusmjeravanje %s";
$_lang['DIRECTORY'] = 'Direktorij';
$_lang['SET_FRONT_CONF'] = 'Podesite naslovnicu u Elxis konfiguraciji!';
$_lang['ADD_NEW_ROUTE'] = 'Dodajte novu rutu';
$_lang['OTHER'] = "Ostalo";
$_lang['LAST_MODIFIED'] = 'Posljednji put promjenjeno ';
$_lang['PERIOD'] = 'Period'; 
$_lang['ERROR_LOG_DISABLED'] = 'Bilježenje grešaka je onemogućeno!';
$_lang['LOG_ENABLE_ERR'] = 'Bilježenje je omogućeno samo za fatalne greške. ';
$_lang['LOG_ENABLE_ERRWARN'] = 'Bilježenje je omogućeno za greške i upozorenja. ';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Bilježenje je omogućeno za greške, upozorenja i obavijesti. ';
$_lang['LOGROT_ENABLED'] = 'Rotacija zapisa je omogućena. ';
$_lang['LOGROT_DISABLED'] = 'Rotacija zapisa je onemogućena! ';
$_lang['SYSLOG_FILES'] = 'Sistemski log ';
$_lang['DEFENDER_BANS'] = 'Zabrane zaštitnika';
$_lang['LAST_DEFEND_NOTIF'] = 'Poslijednje obavijesti zaštitnika';
$_lang['LAST_ERROR_NOTIF'] = 'Poslijednje obavijesti o grešci';
$_lang['TIMES_BLOCKED'] = 'Puta blokiran';
$_lang['REFER_CODE'] = 'Referentni kod';
$_lang['CLEAR_FILE'] = 'Ispraznite dokument';
$_lang['CLEAR_FILE_WARN'] = 'Sadržaj dokumenta bit će obrisan. Želite li nastaviti?';
$_lang['FILE_NOT_FOUND'] = 'Dokumenat nije pronađen!';
$_lang['FILE_CNOT_DELETE'] = 'Ovaj dokumenat ne može biti obrisan! ';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Samo dokumenti sa ekstenzijom .log mogu se preuzeti!';
$_lang['SYSTEM'] = 'Sistem';
$_lang['PHP_INFO'] = 'PHP informacije';
$_lang['PHP_VERSION'] = 'PHP verzija';
$_lang['ELXIS_INFO'] = 'Elxis informacije';
$_lang['VERSION'] = 'Verzija';
$_lang['REVISION_NUMBER'] = 'Broj revizije';
$_lang['STATUS'] = 'Status';
$_lang['CODENAME'] = 'Kodno ime';
$_lang['RELEASE_DATE'] = 'Datum objavljivanja';
$_lang['COPYRIGHT'] = 'Autorska prava';
$_lang['POWERED_BY'] = 'Pokreće';
$_lang['AUTHOR'] = 'Autor';
$_lang['PLATFORM'] = 'Platforma';
$_lang['HEADQUARTERS'] = 'Sjedište';
$_lang['ELXIS_ENVIROMENT'] = 'Elxis okruženje';
$_lang['DEFENDER_LOGS'] = 'Logovi zaštitnika';
$_lang['ADMIN_FOLDER'] = 'Administratorski direktorij';
$_lang['DEF_NAME_RENAME'] = 'Uobičajeno ime, promjenite ga! ';
$_lang['INSTALL_PATH'] = 'Instalacijska putanja ';
$_lang['IS_PUBLIC'] = 'Javno!';
$_lang['CREDITS'] = "Zahvalnice";
$_lang['LOCATION'] = 'Lokacija';
$_lang['CONTRIBUTION'] = "Doprinos";
$_lang['LICENSE'] = 'Licenca ';
$_lang['MULTISITES'] = 'Multiportali';
$_lang['MULTISITES_DESC'] = 'Upravljajte sa više portala pod jednom Elxis instalacijom.';
$_lang['MULTISITES_WARN'] = 'Možete imati više portala pod jednom instalacijom Elxis-a. Rad s multiportalima 
	je zadatak koji zahtjeva napredno poznavanje Elxis CMS-a. Prije nego što podatke uvezete u novi 
	portal uvjerite se da baza postoji. Nakon kreiranja novog portala uredite .htaccess
	dokumenat prema datim uputstvima. Brisanje multiportala ne briše i bazu. Konzultirajte
	iskusnog tehničara ukoliko Vam je potrebna pomoć.';
$_lang['MULTISITES_DISABLED'] = 'Multiportali su isključeni!';
$_lang['ENABLE'] = 'Uključi';
$_lang['ACTIVE'] = 'Aktivno';
$_lang['URL_ID'] = 'URL identifikator';
$_lang['MAN_MULTISITES_ONLY'] = "Možete upravljati multiportalima samo sa portala %s";
$_lang['LOWER_ALPHANUM'] = 'Mala slova alfanumeričkih znakova bez razmaka';
$_lang['IMPORT_DATA'] = 'Uvoz podataka ';
$_lang['CNOT_CREATE_CFG_NEW'] = 'Ne mogu napraviti upravljački dokument %s za novi portal!';
$_lang['DATA_IMPORT_FAILED'] = 'Uvoz podataka nije uspio!';
$_lang['DATA_IMPORT_SUC'] = 'Podaci su uspješno uvezeni!';
$_lang['ADD_RULES_HTACCESS'] = 'Dodajte sljedeća pravila u htaccess dokumenat ';
$_lang['CREATE_REPOSITORY_NOTE'] = 'Preporučuje se kreiranje zasebnog spremišta za svaki portal! ';
$_lang['NOT_SUP_DBTYPE'] = 'Nije podržan tip baze! ';
$_lang['DBTYPES_MUST_SAME'] = 'Vrste baze ovog portala i novog moraju biti iste! ';
$_lang['DISABLE_MULTISITES'] = 'Isključivanje multiportala';
$_lang['DISABLE_MULTISITES_WARN'] = 'Svi portali osim onog sa ID 1 će biti obrisani!';
$_lang['VISITS_PER_DAY'] = "Dnevnih posjeta za %s"; 
$_lang['CLICKS_PER_DAY'] = "Klikovi po danu za %s"; 
$_lang['VISITS_PER_MONTH'] = "Mjesečne posjete za %s"; 
$_lang['CLICKS_PER_MONTH'] = "Mjesečni klikovi za %s";
$_lang['LANGS_USAGE_FOR'] = "Postotak korištenja jezika za %s"; 
$_lang['UNIQUE_VISITS'] = 'Jedinstvenih posjeta ';
$_lang['PAGE_VIEWS'] = 'Pregledanih stranica';
$_lang['TOTAL_VISITS'] = 'Ukupno posjeta';
$_lang['TOTAL_PAGE_VIEWS'] = 'Ukupno pregledanih stranica';
$_lang['LANGS_USAGE'] = 'Upotreba jezika';
$_lang['LEGEND'] = 'Legenda';
$_lang['USAGE'] = 'Upotreba ';
$_lang['VIEWS'] = 'Pregledi';
$_lang['OTHER'] = 'Ostalo';
$_lang['NO_DATA_AVAIL'] = 'Nema dostupnih podataka';
$_lang['PERIOD'] = 'Period';
$_lang['YEAR_STATS'] = 'Godišnja statistika';
$_lang['MONTH_STATS'] = 'Mjesečna statistika';
$_lang['PREVIOUS_YEAR'] = 'Prethodna godina';
$_lang['NEXT_YEAR'] = 'Sljedeća godina';
$_lang['STATS_COL_DISABLED'] = 'Prikupljanje statističkih podataka je onemogućeno! Omogućite statistiku u Elxis konfiguraciji.';
$_lang['DOCTYPE'] = 'Tip dokumenta ';
$_lang['DOCTYPE_INFO'] = 'Preporučena opcija je HTML5. Elxis će generisati XHTML izlaz, čak i ako ste postavili DOCTYPE na XHTML5. 
Kod XHTML tipova dokumenta Elxis isporučuje dokumente sa application/xhtml+xml mime tipom za moderne preglednike i sa text/html za starije.';
$_lang['ABR_SECONDS'] = 's';
$_lang['ABR_MINUTES'] = 'Min';
$_lang['HOUR'] ='S';
$_lang['HOURS'] = 'Sati';
$_lang['DAY'] = 'Dan';
$_lang['DAYS'] = 'Dana';
$_lang['UPDATED_BEFORE'] = 'Ažurirano prije';
$_lang['CACHE_INFO'] = 'Pogledajte i obrišite stavke memorirane u kešu.';
$_lang['ELXISDC'] = 'Elxis Downloads Center';
$_lang['ELXISDC_INFO'] = 'Pregledajte uživo EDC i pogledajte dostupne ekstenzije';
$_lang['SITE_LANGS'] = 'Jezici portala';
$_lang['SITE_LANGS_DESC'] = 'Standardno su svi jezici omogućeni u koriničkoj verziji portala. To možete promjeniti doljnjim odbirom željenih jezika.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Performanse';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minifier';
$_lang['MINIFIER_INFO'] = 'Elxis can unify individual local CSS and JS files and optionally compress them. The unified file will be saved in cache. 
So instead of having multiple CSS/JS files in your pages head section you will have only a minified one.';
$_lang['MOBILE_VERSION'] = 'Mobilna verzija';
$_lang['MOBILE_VERSION_DESC'] = 'Omogučite mobile-friendly verziju za mobilne uređaje?';

?>