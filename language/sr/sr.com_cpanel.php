<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: sr-RS (Srpski - Srbija) language for component CPanel
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ivan Trebješanin ( http://www.elxis-srbija.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] ='Kontrolna tabla';
$_lang['GENERAL_SITE_SETS'] = 'Opšta podešavanja sajta';
$_lang['LANGS_MANAGER'] = 'Menadžer jezika';
$_lang['MANAGE_SITE_LANGS'] = 'Upravljanje jezicima sajta ';
$_lang['USERS'] = 'Korisnici';
$_lang['MANAGE_USERS'] = 'Kreiranje, uređivanje i brisanje korisničkih naloga ';
$_lang['USER_GROUPS'] = 'Korisničke grupe';
$_lang['MANAGE_UGROUPS'] = 'Upravljanje grupama korisnika ';
$_lang['MEDIA_MANAGER'] = 'Menadžer medija';
$_lang['MEDIA_MANAGER_INFO'] = 'Upravljanje multimedijskim fajlovima ';
$_lang['ACCESS_MANAGER'] = 'Menadžer pristupa';
$_lang['MANAGE_ACL'] = 'Upravljanje listama za kontrolu pristupa ';
$_lang['MENU_MANAGER'] = 'Menadžer menija';
$_lang['MANAGE_MENUS_ITEMS'] = 'Upravljanje menijima i stavkama menija ';
$_lang['FRONTPAGE'] = 'Naslovna';
$_lang['DESIGN_FRONTPAGE'] = 'Dizajn naslovnice sajta';
$_lang['CATEGORIES_MANAGER'] = 'Kategorije menadžer ';
$_lang['MANAGE_CONT_CATS'] = 'Upravljanje kategorijama sadržaja';
$_lang['CONTENT_MANAGER'] = 'Menadžer sadržaja';
$_lang['MANAGE_CONT_ITEMS'] = 'Upravljanje stavkama sadržaja';
$_lang['MODULES_MANAGE_INST'] = 'Upravljanje modulima i instaliracija novih. ';
$_lang['PLUGINS_MANAGE_INST'] = 'Upravljanje priključcima sadržaja i instalacija novih. ';
$_lang['COMPONENTS_MANAGE_INST'] = 'Upravljanje komponentama i instalacija novih ';
$_lang['TEMPLATES_MANAGE_INST'] = 'Upravljanje šablonima i instalacija novih. ';
$_lang['SENGINES_MANAGE_INST'] = 'Upravljanje pretraživačima i instalacija novih. ';
$_lang['MANAGE_WAY_LOGIN'] = 'Upravljanje načinima prijave korisnika na sajt. ';
$_lang['TRANSLATOR'] = 'Prevodilac';
$_lang['MANAGE_MLANG_CONTENT'] = 'Upravljanje višejezičkim sadržajem ';
$_lang['LOGS'] = 'Logovi';
$_lang['VIEW_MANAGE_LOGS'] = 'Prikaz i upravljanje fajlovima evidencije';
$_lang['GENERAL'] = 'Opšte';
$_lang['WEBSITE_STATUS'] = 'Status sajta';
$_lang['ONLINE'] = 'Dostupan';
$_lang['OFFLINE'] = 'Nedostupan';
$_lang['ONLINE_ADMINS'] = 'Dostupan samo za administratore ';
$_lang['OFFLINE_MSG'] = 'Poruka o nedopstupnosti';
$_lang['OFFLINE_MSG_INFO'] = 'Ostavite ovo polje prazno za prikaz automatske višejezičke poruke';
$_lang['SITENAME'] = 'Ime sajta';
$_lang['URL_ADDRESS'] = 'URL adresa';
$_lang['REPO_PATH'] = 'Putanja spremišta';
$_lang['REPO_PATH_INFO'] = 'Puna putanja do Elxis spremišta. Ostavite prazno za podrazumevanu
	lokaciju (elxis_root/repository/). Savetujemo da ovaj folder preimenujete i premestite iznad WWW 
	foldera!';
$_lang['FRIENDLY_URLS'] = 'Prijateljski URL-ovi';
$_lang['SEF_INFO'] = 'Ako se podesi na DA (preporučeno) preimenujte htaccess.txt fajl u .htaccess.';
$_lang['STATISTICS_INFO'] = 'Omogućiti prikupljanje statističkih podataka o posećenosti sajta? ';
$_lang['GZIP_COMPRESSION'] = 'Gzip kompresija';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis će komprimovati dokument koristeći gzip pre slanja u pregledač i tako uštedeti 70% do 80% propusnog opsega.';
$_lang['DEFAULT_ROUTE'] = 'Uobičajena putanja';
$_lang['DEFAULT_ROUTE_INFO'] = 'Elxis formatirani URI koji će se koristiti kao Naslovna sajta';
$_lang['META_DATA'] = 'META podataci';
$_lang['META_DATA_INFO'] = 'Kratak opis veb sajta';
$_lang['KEYWORDS'] = 'Ključne reči';
$_lang['KEYWORDS_INFO'] = 'Nekoliko reči razdvojenih zarezom';
$_lang['STYLE_LAYOUT'] = 'Stil i raspored ';
$_lang['SITE_TEMPLATE'] = 'Šablon sajta';
$_lang['ADMIN_TEMPLATE'] = 'Administratorski šablon';
$_lang['ICONS_PACK'] = 'Paket ikona';
$_lang['LOCALE'] = 'Lokal';
$_lang['TIMEZONE'] = 'Vremenska zona';
$_lang['MULTILINGUISM'] = 'Karakteristike';
$_lang['MULTILINGUISM_INFO'] = 'Omogućava vam da unesete tekstualne elemente na više od jednog jezika (prevodi).
	Ne uključujte ovo ako nećete koristiti jer će bez potrebe usporiti sajt. Elxis interfejs 
	će i dalje biti višejezičan, čak i ako je ovde postavljeno NE.';
$_lang['CHANGE_LANG'] = "Promena jezika";
$_lang['LANG_CHANGE_WARN'] = 'Ako ste promenili podrazumevani jezik možda postoji nedoslednost 
	između indikatora jezika i prevoda u tabeli sa prevodima.';
$_lang['CACHE'] = "Keš";
$_lang['CACHE_INFO'] = 'Elxis može sačuvati generisani XHTML kod pojedinačnih elemenata u kešu za bržu kasniju generaciju stranice.
	Ovo je opšte podešavanje, morate uključiti keš i za pojedinačne elemente (nprg. module) za koje želite da budu keširani.';
$_lang['APC_INFO'] = 'Alternativni PHP Cache (APC) je Opcode cache za PHP. On mora biti podržan od strane vašeg veb servera.
	Nije preporučljivo za okruženje deljenog hostinga. Elxis će koristiti ovo na posebnim stranicama kako bi ubrzao rad sajta.';
$_lang['APC_ID_INFO'] = 'U slučaju više od 1 sajta hostovanih na istom serveru identifikujte
	ih jedinstvenim brojem.';
$_lang['USERS_AND_REGISTRATION'] = 'Korisnici i registracija ';
$_lang['PRIVACY_PROTECTION'] = 'Zaštita privatnosti ';
$_lang['PASSWORD_NOT_SHOWN'] = 'Trenutna lozinka nije prikazana iz bezbednosnih razloga.
	Popunite ovo polje samo ukoliko želite da promenite lozinku.';
$_lang['DB_TYPE'] = 'Tip baze podataka';
$_lang['ALERT_CON_LOST'] = 'Ako ovo promente, veza sa aktuelnom bazom podataka će biti izgubljena!';
$_lang['HOST'] = 'Host';
$_lang['PORT'] = 'Port';
$_lang['PERSISTENT_CON'] = 'Stalna veza';
$_lang['DB_NAME'] = 'Ime baze';
$_lang['TABLES_PREFIX'] = 'Prefiks tabela';
$_lang['DSN_INFO'] = 'Pripremljena DSN linija za povezivanje na bazu podataka.';
$_lang['SCHEME'] = 'Šema';
$_lang['SCHEME_INFO'] = 'Apsolutna putanja do fajla baze podataka, ako koristite bazu podataka kao što su SQLite. ';
$_lang['SEND_METHOD'] = 'Metod slanja';
$_lang['SMTP_OPTIONS'] = 'SMTP opcije ';
$_lang['AUTH_REQ'] = 'Potrebna je potvrda identiteta ';
$_lang['SECURE_CON'] = 'Sigurna veza';
$_lang['SENDER_NAME'] = 'Ime pošiljaoca';
$_lang['SENDER_EMAIL'] = 'e-mail pošiljaoca';
$_lang['RCPT_NAME'] = 'Ime primaoca';
$_lang['RCPT_EMAIL'] = 'e-mail primaoca';
$_lang['TECHNICAL_MANAGER'] = 'Tehnički menadžer';
$_lang['TECHNICAL_MANAGER_INFO'] = 'Tehnički menadžer prima obaveštenja o grešakama i bezbednosti.';
$_lang['USE_FTP'] = 'Upotreba FTP';
$_lang['PATH'] = 'Putanja';
$_lang['FTP_PATH_INFO'] = 'Relativna putanja od vrhovnog FTP foldera do Elxis instalacije';
$_lang['SESSION'] = 'Sesija';
$_lang['HANDLER'] = 'Rukovalac';
$_lang['HANDLER_INFO'] = 'Elxis može da čuva sesije kao fajlove u spremištu, ili u bazi podataka.
	Takođe, možete izabrati NIŠTA ako želite da PHP čuva sesije na mestu predviđenom u podešavanjima servera.';
$_lang['FILES'] = 'Fajlovi';
$_lang['LIFETIME'] = "Trajanje";
$_lang['SESS_LIFETIME_INFO'] = 'Vreme neaktivnosti nakon koga sesija ističe. ';
$_lang['CACHE_TIME_INFO'] = 'Posle tog vremena sačuvane stavke se ponovo generiše. ';
$_lang['MINUTES'] = 'Minuta';
$_lang['HOURS'] = 'Sata';
$_lang['MATCH_IP'] = 'Meč IP ';
$_lang['MATCH_BROWSER'] = 'Poređelje pregledača';
$_lang['MATCH_REFERER'] = 'Poređelje HTTP referera';
$_lang['MATCH_SESS_INFO'] = 'Omogućava naprednu validaciju sesije. ';
$_lang['ENCRYPTION'] = 'Enkripcija';
$_lang['ENCRYPT_SESS_INFO'] = 'Šifrovanje podataka sesije?';
$_lang['ERRORS'] = 'Greške';
$_lang['WARNINGS'] = 'Upozorenja';
$_lang['NOTICES'] = 'Obaveštenja';
$_lang['NOTICE'] = 'Obaveštenje';
$_lang['REPORT'] = 'Izveštaj';
$_lang['REPORT_INFO'] = 'Nivo prijave grešaka. Na javnim sajtovima preporučeno je isključiti.';
$_lang['LOG'] = 'Log';
$_lang['LOG_INFO'] = 'Nivo beleženja grešaka. Izaberite koje greške želite da Elxis beleži u sistemskom
	logu (repository/logs/).';
$_lang['ALERT'] = 'Upozorenje';
$_lang['ALERT_INFO'] = 'Slanje poruke o fatalnoj grešci tehničkom menadžeru sajta.';
$_lang['ROTATE'] = 'Rotacija';
$_lang['ROTATE_INFO'] = 'Rotiranje logova sa greškama na kraju svakog meseca. Preporučljivo.';
$_lang['DEBUG'] = 'Debug';
$_lang['MODULE_POS'] = 'Pozicije modula';
$_lang['MINIMAL'] = 'Minimalno';
$_lang['FULL'] = 'Puno';
$_lang['DISPUSERS_AS'] = 'Prikazivanje korisnika kao';
$_lang['USERS_REGISTRATION'] = 'Registracija korisnika';
$_lang['ALLOWED_DOMAIN'] = 'Dozvoljeni domeni';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Napišite ime domena (npr. elxis.org) sa kojih će 
	sistem uvek prihvatati e-mail adrese.';
$_lang['EXCLUDED_DOMAINS'] = 'Izuzeti domeni ';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Zarezom odvojena lista imena domena (npr. lossajt.com, hakeri.com...)
	sa kojih e-mail adresa neće biti prihvaćena tokom registracije.';
$_lang['ACCOUNT_ACTIVATION'] = 'Aktiviranje naloga ';
$_lang['DIRECT'] = 'Direktno';
$_lang['MANUAL_BY_ADMIN'] = 'Ručno';
$_lang['PASS_RECOVERY'] = 'Zaboravljena lozinka';
$_lang['SECURITY'] = 'Bezbednost';
$_lang['SECURITY_LEVEL'] = 'Nivo sigurnosti';
$_lang['SECURITY_LEVEL_INFO'] = 'Povećanjem nivo bezbednosti neke opcije su omogućene prinudno 
	dok su neke isključene. Konsultujte Elxis dokumentaciju za više informacija.';
$_lang['NORMAL'] = 'Normalno';
$_lang['HIGH'] = 'Bisoko';
$_lang['INSANE'] = 'Paranoično';
$_lang['ENCRYPT_METHOD'] = 'Metod šifrovanja';
$_lang['AUTOMATIC'] = 'Automatski ';
$_lang['ENCRYPTION_KEY'] = 'Ključ šifre';
$_lang['ELXIS_DEFENDER'] = 'Elxis zaštitnik';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis zaštitnik štiti vaš veb sajt od XSS i SQL napada.
	Ovaj moćni alat filtrira korisničke zahteve i blokira napade na Vaš sajt. Takođe, obavestiće Vas o 
	napadu i ubeležiće ga. Možete izabrati tip filtera koji će biti primenjeni ili čak da zaključa važne sistemske 
	fajlove. Što više filtera uključite, sajt će sporije raditi. 
	Preporučljivo je izabrati G, C i F opcije. Konsultujte Elxis dokumentaciju za više informacija.';
$_lang['SSL_SWITCH'] = 'SSL prekidač ';
$_lang['SSL_SWITCH_INFO'] = 'Elxis će se automatski prebaciti sa HTTP-a za HTTPS na stranama gde je privatnost važna.
	Za administracionu konzolu HTTPS će uvek biti uključen. Ovo zahteva SSL sertifikat!';
$_lang['PUBLIC_AREA'] = 'Javni prostor ';
$_lang['GENERAL_FILTERS'] = 'Opšta pravila';
$_lang['CUSTOM_FILTERS'] = 'Posebna pravila ';
$_lang['FSYS_PROTECTION'] = 'Zaštita fajlova';
$_lang['CHECK_FTP_SETS'] = 'Provera FTP podešavanja ';
$_lang['FTP_CON_SUCCESS'] = 'Povezivanje sa FTP serverom je bilo uspešno.';
$_lang['ELXIS_FOUND_FTP'] = 'Elxis instalacija je pronađena na FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] ='Elxis instalacija nije pronađena na FTP.';
$_lang['CAN_NOT_CHANGE'] = 'Ne može da izmeni. ';
$_lang['SETS_SAVED_SUCC'] = 'Podešavanja uspešno sačuvana';
$_lang['ACTIONS'] = 'Akcije ';
$_lang['BAN_IP_REQ_DEF'] = 'Kako biste zabranili IP adresu, obavezno je da omogućite najmanje jednu opciju u Elxis zaštitniku';
$_lang['BAN_YOURSELF'] = 'Da li pokušavate da blokirate samog sebe? ';
$_lang['IP_AL_BANNED'] = 'Ovaj IP je već zabranjen! ';
$_lang['IP_BANNED'] = "IP adresa %s je zabranjena!";
$_lang['BAN_FAILED_NOWRITE'] = 'Ban nije uspeo! Fajl repository/logs/defender_ban.php je zaključan.';
$_lang['ONLY_ADMINS_ACTION'] = 'Samo administratori mogu da obavljaju ovu akciju!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Ne možete da se odjavite administratora! ';
$_lang['USER_LOGGED_OUT'] = 'Korisnik je odjavljen! ';
$_lang['SITE_STATISTICS'] = 'Statistike sajta';
$_lang['SITE_STATISTICS_INFO'] = 'Pogledajte statistiku posećenosti sajta';
$_lang['BACKUP'] = 'Bekap';
$_lang['BACKUP_INFO'] = 'Pravljenje rezervne kopije sajta i upravljanje postojećim kopijama.';
$_lang['BACKUP_FLIST'] = 'Lista postojećih bekapa';
$_lang['TYPE'] = 'Tip';
$_lang['FILENAME'] = 'Ime fajla';
$_lang['SIZE'] = 'Veličina';
$_lang['NEW_DB_BACKUP'] = 'Novi bekap baze podataka';
$_lang['NEW_FS_BACKUP'] = 'Novi bekap fajlova';
$_lang['FILESYSTEM'] = 'Sistem fajlova ';
$_lang['DOWNLOAD'] = 'Preuzimanje';
$_lang['TAKE_NEW_BACKUP'] = "Napravite novi bekap?\nOvo može da potraje, budite strpljivi!";
$_lang['FOLDER_NOT_EXIST'] = "Folder %s ne postoji!";
$_lang['FOLDER_NOT_WRITE'] = "Folder %s nije otključan!";
$_lang['BACKUP_SAVED_INTO'] = "Bekap fajlovi se čuvaju u %s";
$_lang['CACHE_SAVED_INTO'] = "Keš fajlovi se čuvaju u %s";
$_lang['CACHED_ITEMS'] = 'Keširane stavke';
$_lang['ELXIS_ROUTER'] = 'Elxis ruter';
$_lang['ROUTING'] = 'Rutiranje';
$_lang['ROUTING_INFO'] = 'Preusmeravanje korisničkih zahteva na proizvoljnu URL adresu.';
$_lang['SOURCE'] = 'Izvor';
$_lang['ROUTE_TO'] = 'Put do ';
$_lang['REROUTE'] = "Preusmeravanje %s";
$_lang['DIRECTORY'] = 'Direktorijum';
$_lang['SET_FRONT_CONF'] = 'Podesite naslovnicu u Elxis konfiguraciji!';
$_lang['ADD_NEW_ROUTE'] = 'Dodajte novu rutu';
$_lang['OTHER'] = "Ostalo";
$_lang['LAST_MODIFIED'] = 'Poslednji put ';
$_lang['PERIOD'] = 'Period'; 
$_lang['ERROR_LOG_DISABLED'] = 'Evidentiranje grešaka je onemogućeno!';
$_lang['LOG_ENABLE_ERR'] = 'Evidentiranje je omogućeno samo za fatalne greške. ';
$_lang['LOG_ENABLE_ERRWARN'] = 'Evidentiranje je omogućeno za greške i upozorenja. ';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Evidentiranje je omogućeno za greške, upozorenja i obaveštenja. ';
$_lang['LOGROT_ENABLED'] = 'Rotacija zapisa je omogućena. ';
$_lang['LOGROT_DISABLED'] = 'Rotacija tapisa je onemogućena! ';
$_lang['SYSLOG_FILES'] = 'Sistemski log datoteke ';
$_lang['DEFENDER_BANS'] = 'Zabrane zaštitnika';
$_lang['LAST_DEFEND_NOTIF'] = 'Poslednje obaveštenje zaštitnika';
$_lang['LAST_ERROR_NOTIF'] = 'Poslednje obaveštenje o grešci';
$_lang['TIMES_BLOCKED'] = 'Puta blokiran';
$_lang['REFER_CODE'] = 'Referentni kod';
$_lang['CLEAR_FILE'] = 'Ispraznite fajl';
$_lang['CLEAR_FILE_WARN'] = 'Sadržaj fajla biće uklonjen. Želite li da nastavite?';
$_lang['FILE_NOT_FOUND'] = 'Fajl nije pronađen!';
$_lang['FILE_CNOT_DELETE'] = 'Ovaj fajl ne može biti obrisan! ';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Samo fajlovi sa ekstenzijom .log mogu se preuzeti!';
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
$_lang['HEADQUARTERS'] = 'Sedište';
$_lang['ELXIS_ENVIROMENT'] = 'Elxis okruženje';
$_lang['DEFENDER_LOGS'] = 'Logovi zaštitnika';
$_lang['ADMIN_FOLDER'] = 'Administratorski folder';
$_lang['DEF_NAME_RENAME'] = 'Uobičajeno ime, preimenujte ga! ';
$_lang['INSTALL_PATH'] = 'Instalacija put ';
$_lang['IS_PUBLIC'] = 'Javno!';
$_lang['CREDITS'] = "Zahvalnice";
$_lang['LOCATION'] = 'Lokacija';
$_lang['CONTRIBUTION'] = "Doprinos";
$_lang['LICENSE'] = 'Licenca ';
$_lang['MULTISITES'] = 'Multisajtovi';
$_lang['MULTISITES_DESC'] = 'Upravljajte mnogim sajtovim pod jednom Elxis instalacijom.';
$_lang['MULTISITES_WARN'] = 'Možete imati više sajtova pod jednom instalacijom Elxis-a. Rad sa multisajtovima 
	je zadatak koji zahteva napredno poznavanje Elxis CMS-a. Pre nego što podatke uvezete u novi 
	multisajt uverite se da baza postoji. Nakon pravljenja novog multisajta uredite .htaccess
	fajl prema datim uputstvima. Brisanje multisajta ne briše i bazu. Konsultujte 
	iskusnog tehničara ukoliko Vam je potrebna pomoć.';
$_lang['MULTISITES_DISABLED'] = 'Multisajtovi su isključeni!';
$_lang['ENABLE'] = 'Enable';
$_lang['ACTIVE'] = 'Aktivna';
$_lang['URL_ID'] = 'URL identifikator';
$_lang['MAN_MULTISITES_ONLY'] = "Možete upravljati multisajtovima samo sa sajta %s";
$_lang['LOWER_ALPHANUM'] = 'Mala slova alfanumeričkih znakova bez razmaka';
$_lang['IMPORT_DATA'] = 'Uvoz podataka ';
$_lang['CNOT_CREATE_CFG_NEW'] = 'Ne mogu da napravim konfiguracioni fajl %s za novi sajt!';
$_lang['DATA_IMPORT_FAILED'] = 'Uvoz podataka nije uspeo!';
$_lang['DATA_IMPORT_SUC'] = 'Podaci su uspešno uvezeni!';
$_lang['ADD_RULES_HTACCESS'] = 'Dodajte sledeća pravila u htaccess fajl ';
$_lang['CREATE_REPOSITORY_NOTE'] = 'Preporučuje se da pravljenje zasebnog spremišta za svaki podsajt! ';
$_lang['NOT_SUP_DBTYPE'] = 'Nije podržan tip baze! ';
$_lang['DBTYPES_MUST_SAME'] = 'Vrste baze ovog sajta i novog moraju da budu isti! ';
$_lang['DISABLE_MULTISITES'] = 'Isključivanje multisajtova';
$_lang['DISABLE_MULTISITES_WARN'] = 'Svi sajtovi osim onog sa ID 1 će biti uklonjeni!';
$_lang['VISITS_PER_DAY'] = "Poseta dnevno za %s"; 
$_lang['CLICKS_PER_DAY'] = "Klikovi po danu za %s"; 
$_lang['VISITS_PER_MONTH'] = "Poseta mesečno za %s"; 
$_lang['CLICKS_PER_MONTH'] = "Klikova mesečno za %s";
$_lang['LANGS_USAGE_FOR'] = "Procenat korišćenja jezika za %s"; 
$_lang['UNIQUE_VISITS'] = 'Jedinstvenih poseta ';
$_lang['PAGE_VIEWS'] = 'Pregledanih strana';
$_lang['TOTAL_VISITS'] = 'Ukupno poseta';
$_lang['TOTAL_PAGE_VIEWS'] = 'Pregleda strana';
$_lang['LANGS_USAGE'] = 'Upotreba jezika';
$_lang['LEGEND'] = 'Legenda';
$_lang['USAGE'] = 'Upotreba ';
$_lang['VIEWS'] = 'Pregledi';
$_lang['OTHER'] = 'Ostalo';
$_lang['NO_DATA_AVAIL'] = 'Nema dostupnih podataka';
$_lang['PERIOD'] = 'Period';
$_lang['YEAR_STATS'] = 'Godišnja statistika';
$_lang['MONTH_STATS'] = 'Mesečna statistika';
$_lang['PREVIOUS_YEAR'] = 'Prethodna godina';
$_lang['NEXT_YEAR'] = 'Sledeća godina';
$_lang['STATS_COL_DISABLED'] = 'Prikupljanje statističkih podataka je onemogućen! Omogućite statistiku u Elxis konfiguraciji.';
$_lang['DOCTYPE'] = 'Tip dokumenta ';
$_lang['DOCTYPE_INFO'] = 'Preporučena opcija je HTML5. Elxis će generisati XHTML izlaz, čak i ako ste postavili DOCTYPE na XHTML5. 
Kod XHTML tipova dokumenta Elxis isporučuje dokumente sa application/xhtml+xml mime tipom za moderne pregledače i sa text/html za starije.';
$_lang['ABR_SECONDS'] = 's';
$_lang['ABR_MINUTES'] = 'Min';
$_lang['HOUR'] ='S';
$_lang['HOURS'] = 'Sati';
$_lang['DAY'] = 'Dan';
$_lang['DAYS'] = 'Dana';
$_lang['UPDATED_BEFORE'] = 'Ažurirano pre';
$_lang['CACHE_INFO'] = 'Pogledajte i obrišite stavke memorisane u kešu.';
$_lang['ELXISDC'] = 'Elxis Downloads Center';
$_lang['ELXISDC_INFO'] = 'Pregledajte uživo EDC i pogledajte dostupne ekstenzije';
$_lang['SITE_LANGS'] = 'Site languages';
$_lang['SITE_LANGS_DESC'] = 'By default all installed languages are available in site frontend area. You can change this 
	by selecting below the languages you wish only to be available in frontend.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Performance';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minifier';
$_lang['MINIFIER_INFO'] = 'Elxis can unify individual local CSS and JS files and optionally compress them. The unified file will be saved in cache. 
So instead of having multiple CSS/JS files in your pages head section you will have only a minified one.';
$_lang['MOBILE_VERSION'] = 'Mobile version';
$_lang['MOBILE_VERSION_DESC'] = 'Enable mobile-friendly version for handheld devices?';

?>