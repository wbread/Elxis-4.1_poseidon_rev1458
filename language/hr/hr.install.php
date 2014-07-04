<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: hr-HR (Hrvatski - Hrvatska) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Robert Kovalek
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Instalacija';
$_lang['STEP'] = 'Korak';
$_lang['VERSION'] = 'Verzija';
$_lang['VERSION_CHECK'] = 'Provjera verzije';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Broj revizije';
$_lang['RELEASE_DATE'] = 'Datum izdavanja';
$_lang['ELXIS_INSTALL'] = 'Elxis instalacija';
$_lang['LICENSE'] = 'Licenca';
$_lang['VERSION_PROLOGUE'] = 'Upravo instalirate Elxis CMS. Točna verzija Elxis kopije 
	koju se spremate instalirati prikazana je ispod. Molimo Vas da provjerite je li to zadnja objavljena Elxis verzija  
	na <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Prije nego što počnete';
$_lang['BEFORE_DESC'] = 'Prije nego što nastavite provjerite da su svi zahtjevi ispunjeni.';
$_lang['DATABASE'] = 'Baza';
$_lang['DATABASE_DESC'] = 'Napravite praznu bazu koju će Elxis koristiti za čuvanje podataka. Preporučamo 
	upotrebu <strong>MySQL</strong> baze podataka. Iako Elxis podržava i ostale tipove baza 
	kao što su npr. PostgreSQL i SQLite 3, detaljno je testiran samo na MySQL bazama. Kako biste napravili 
	praznu MySQL bazu, učinite to putem panela (CPanel, Plesk, ISP Config, itd),   
	phpMyAdmin ili nekog drugog alata za upravljanje bazama. Samo osigurajte <strong>ime</strong> baze i napravite je. 
	Nakon toga, napravite <strong>korisnika</strong> baze dodjelite mu upravo napravljenu bazu. Zapišite negdje  
	ime baze, korisničko ime i lozinku jer će nam trebati tijekom instalacije.';
$_lang['REPOSITORY'] = 'Spremište';
$_lang['REPOSITORY_DESC'] = 'Elxis koristi poseban direktorij za čuvanje cache dokumenata, logove, sesije, sigurnosne kopije, itd.  
	Uobičajeno ime direktorija je <strong>repository</strong> a nalazi se unutar Elxis direktorija. Ovaj direktorij 
	<strong>mora biti otključan</strong>! Preporučamo da ovaj folder <strong>preimenujete</strong> i <strong>premjestite</strong> 
	ma mjesto koje nije dostupno preko interneta. Nakon ovoga, ukoliko imate <strong>open basedir</strong> zaštitu u PHP 
	morate ddodati i ovaj fdirektorij u listu dozvoljenih putanja.';
$_lang['REPOSITORY_DEFAULT'] = 'Spremište je na uobičajenom mjestu!';
$_lang['SAMPLE_ELXPATH'] = 'Pokazna Elxis putanja';
$_lang['DEF_REPOPATH'] = 'Uobičajena putanja spremišta';
$_lang['REQ_REPOPATH'] = 'Preporučena putanja spremišta';
$_lang['CONTINUE'] = 'Nastavak';
$_lang['I_AGREE_TERMS'] = 'Pročitao/la sam, razumio/jela sam i slažem se s uslovima EPL';
$_lang['LICENSE_NOTES'] = 'Elxis CMS je besplatni softver objavljen pod <strong>Elxis Public License</strong> (EPL). 
	Prije nego što instalirate Elxis morate se složiti s uslovima EPL. Pažljivo pročitajte 
	Elxis licencu i ukoliko se slažete, označite odgovarajuću opciju u podnožju stanice i kliknite Nastavak. U suprotnom, 
	prekinite instalaciju i obrišite Elxis dokumente.';
$_lang['SETTINGS'] = 'Podešenja';
$_lang['SITE_URL'] = 'URL portala';
$_lang['SITE_URL_DESC'] = 'Bez završne kose crte (npr. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Apsolutna putanja do Elxis spremišta. Ostavite prazno za uobičajenu putanju i naziv direktorija.';
$_lang['SETTINGS_DESC'] = 'Podesite parametre Elxis konfiguracije. Neke parametre je neophodno podesiti prije Elxis 
	instalacije. Po završetku instalacije prijavite se u administracijsku konzolu i podesite ostale parametre. 
	Ovo će biti Vaš prvi administratorski zadatak.';
$_lang['DEF_LANG'] = 'Uobičajeni jezik';
$_lang['DEFLANG_DESC'] = 'Sadržaj je unesen na uobičajenom jeziku. Sadržaj na drugim jezicima je 
	prijevod originalnog članka.';
$_lang['ENCRYPT_METHOD'] = 'Metoda šifriranja';
$_lang['ENCRYPT_KEY'] = 'Ključ šifriranja';
$_lang['AUTOMATIC'] = 'Automatski';
$_lang['GEN_OTHER'] = 'Generiranje novog';
$_lang['SITENAME'] = 'Ime portala';
$_lang['TYPE'] = 'Tip';
$_lang['DBTYPE_DESC'] = 'Preporučamo MySQL. Moguće je izabrati samo stavke podržana od strane servera i Elxis instalacije.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Prefiks tabela';
$_lang['DSN_DESC'] = 'Možete izabrati i Data Source Name za povezivanje s bazom.';
$_lang['SCHEME'] = 'Šema';
$_lang['SCHEME_DESC'] = 'Apsolutna putanja do baze, ukoliko koristite bazu nalik SQLite.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'Uobičajeni port za MySQL je 3306. Ostavite na 0 za automatski odabir.';
$_lang['FTPPORT_DESC'] = 'Uobičajeni port za FTP je 21. Ostavite na 0 za automatski odabir.';
$_lang['USE_FTP'] = 'Upotreba FTP';
$_lang['PATH'] = 'Putanja';
$_lang['FTP_PATH_INFO'] = 'Relativna putanja od FTP glavnog direktorija do Elxis instalacije (primer: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Provjera FTP postavki';
$_lang['CHECK_DB_SETS'] = 'Provjera postavki baze';
$_lang['DATA_IMPORT'] = 'Uvoz podataka';
$_lang['SETTINGS_ERRORS'] = 'Navedene postavke imaju greške!';
$_lang['NO_QUERIES_WARN'] = 'Inicijalni podaci su uvezeni u bazu, ali izgleda da nijedan upit nije izvršen. Provjerite 
	jesu li podaci zaista i uvezeni, prije nego što nastavite.';
$_lang['RETRY_PREV_STEP'] = 'Ponovni pokušaj';
$_lang['INIT_DATA_IMPORTED'] = 'Inicijalni podaci su uvezeni u bazu.';
$_lang['QUERIES_EXEC'] = "Izvršeno je %s SQL upita."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Administratorski nalog';
$_lang['CONFIRM_PASS'] = 'Potvrda lozinke';
$_lang['AVOID_COMUNAMES'] = 'Izbjegavajte očekivana korisnička imena, kao npr. admin, administrator, itd.';
$_lang['YOUR_DETAILS'] = 'Vaši podaci';
$_lang['PASS_NOMATCH'] = 'Lozinke se ne podudaraju!';
$_lang['REPOPATH_NOEX'] = 'Putanja do spremišta ne postoji!';
$_lang['FINISH'] = 'Kraj';
$_lang['FRIENDLY_URLS'] = 'Prijateljski URL-ovi';
$_lang['FRIENDLY_URLS_DESC'] = 'Preporučamo da ovo uključite. Kako bi sve funkcioniralo, Elxis će probati preimenovati htaccess.txt u 
	<strong>.htaccess</strong> . Ukoliko već postoji .htaccess u istom direktoriju, on će biti obrisan.';
$_lang['GENERAL'] = 'Općenito';
$_lang['ELXIS_INST_SUCC'] = 'Elxis instalacija je uspješno završena.';
$_lang['ELXIS_INST_WARN'] = 'Elxis instalacija je završena uz upozorenja.';
$_lang['CNOT_CREA_CONFIG'] = 'Nije moguće napraviti <strong>configuration.php</strong> dokumenat u Elxisdirektoriju.';
$_lang['CNOT_REN_HTACC'] = 'Nije moguće preimenovati <strong>htaccess.txt</strong> fajl u <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Konfiguracijski dokumenat';
$_lang['CONFIG_FILE_MANUAL'] = 'Napravite ručno configuration.php dokumenat, kopirajte sljedeći kod i zaljepite ga u dokumenat.';
$_lang['REN_HTACCESS_MANUAL'] = 'Preimenujte ručno <strong>htaccess.txt</strong> dokumenat u <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Što dalje?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Radi poboljšanja sigurnosti, preimenujte administracijski direktorij (<em>estia</em>) u bilo koje drugo ime. 
	Ukoliko to i napravie, morate izmenjeno ime unjeti i u .htaccess dokumenat.';
$_lang['LOGIN_CONFIG'] = 'Prijavite se u administracijski dio i podesite ostale konfiguracijske parametre.';
$_lang['VISIT_NEW_SITE'] = 'Posjetite svoj portal';
$_lang['VISIT_ELXIS_SUP'] = 'Posjetite web stranice Elxis podrške';
$_lang['THANKS_USING_ELXIS'] = 'Hvala što koristite Elxis CMS.';

?>