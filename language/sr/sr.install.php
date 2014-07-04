<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: sr-RS (Srpski - Srbija) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Ivan Trebješanin ( http://www.elxis-srbija.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Instalacija';
$_lang['STEP'] = 'Korak';
$_lang['VERSION'] = 'Verzija';
$_lang['VERSION_CHECK'] = 'Provera verzije';
$_lang['STATUS'] = 'Status';
$_lang['REVISION_NUMBER'] = 'Broj revizije';
$_lang['RELEASE_DATE'] = 'Datum izdavanja';
$_lang['ELXIS_INSTALL'] = 'Elxis instalacija';
$_lang['LICENSE'] = 'Licenca';
$_lang['VERSION_PROLOGUE'] = 'Upravo instalirate Elxis CMS. Tačna verzija Elxis kopije 
	koju se spremate da instalirate prikazana je ispod. Molimo Vas da se uverite da je to poslednja objavljena Elxis verzija  
	na <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Pre nego što počnete';
$_lang['BEFORE_DESC'] = 'Pre nego što nastavite uverite se da su svi zahtevi ispunjeni.';
$_lang['DATABASE'] = 'Baza';
$_lang['DATABASE_DESC'] = 'Napravite praznu bazu koju će Elxis koristiti za čuvanje podataka. Preporučujemo 
	upotrebu <strong>MySQL</strong> baze podataka. Iako Elxis podržava i ostale tipove baza 
	kao što su npr. PostgreSQL i SQLite 3, detaljno je testiran samo na MySQL bazama. Kako biste napravili 
	praznu MySQL bazu, učinite to putem panela (CPanel, Plesk, ISP Config, itd),   
	phpMyAdmin ili nekog drugog alata za upravljanje bazama. Samo obezbedite <strong>ime</strong> baze i napravite je. 
	Nakon toga, napravite <strong>korisnika</strong> baze dodelite mu upravo napravljenu bazu. Zapišite negde  
	ime baze, korisničko ime i lozinku jer će nam trebati tokom instalacije.';
$_lang['REPOSITORY'] = 'Spremište';
$_lang['REPOSITORY_DESC'] = 'Elxis koristi poseban folder za čuvanje keširanih fajlova, logove, sesije, bekape, itd.  
	Uobičajeno ime folder je <strong>repository</strong> a smešten je unutar Elxis foldera. Ovaj folder 
	<strong>mora biti otključan</strong>! Preporučujemo da ovaj folder <strong>preimenujete</strong> i <strong>premestite</strong> 
	ma mesto koje nije dostupno preko interneta. Nakon ovoga, ukoliko imate <strong>open basedir</strong> zaštitu u PHP 
	morate da dodate i ovaj folder u listu dozvoljenih putanja.';
$_lang['REPOSITORY_DEFAULT'] = 'Spremište je na uobičajenom mestu!';
$_lang['SAMPLE_ELXPATH'] = 'Pokazna Elxis putanja';
$_lang['DEF_REPOPATH'] = 'Uobičajena putanja spremišta';
$_lang['REQ_REPOPATH'] = 'Preporučena putanja spremišta';
$_lang['CONTINUE'] = 'Nastavak';
$_lang['I_AGREE_TERMS'] = 'Pročitao-la sam, razumeo-la i slažem se sa uslovima EPL';
$_lang['LICENSE_NOTES'] = 'Elxis CMS je besplatni softver objavnjen pod <strong>Elxis Public License</strong> (EPL). 
	Pre nego što instalirate Elxis morate se složiti sa uslovima EPL. Pažljivo pročitajte 
	Elxis licencu i ukoliko se slažete, štiklirajte odgovarajuću opciju u podnožju stane i kliknite Nastavak. U suprotnom, 
	prekinite instalaciju i obrišite Elxis fajlove.';
$_lang['SETTINGS'] = 'Podešavanja';
$_lang['SITE_URL'] = 'URL sajta';
$_lang['SITE_URL_DESC'] = 'Bez završne kose crte (npr. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Apsolutna putanja do Elxis spremišta. Ostavite prazno za uobičajenu putanju i naziv foldera.';
$_lang['SETTINGS_DESC'] = 'Podesite parametre Elxis konfiguracije. Neke parametre je neophodno podesiti pre Elxis 
	instalacije. Po završetku instalacije prijavite se u administracionu konzolu i podesite ostale parametre. 
	Ovo će biti Vaš prvi administratorski zadatak.';
$_lang['DEF_LANG'] = 'Uobičajeni jezik';
$_lang['DEFLANG_DESC'] = 'Sadržaj je unet na uobičajenom jeziku. Sadržaj na drugim jezicima je 
	prevod originalnog članka.';
$_lang['ENCRYPT_METHOD'] = 'Metod šifrovanja';
$_lang['ENCRYPT_KEY'] = 'Ključ šifrovanja';
$_lang['AUTOMATIC'] = 'Automatski';
$_lang['GEN_OTHER'] = 'Pravljenje novog';
$_lang['SITENAME'] = 'Ime sajta';
$_lang['TYPE'] = 'Tip';
$_lang['DBTYPE_DESC'] = 'Preporučujemo MySQL. Moguće je izabrati samo stavke podržana od strane servera i Elxis instalacije.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Prefiks tabela';
$_lang['DSN_DESC'] = 'Mođete izabrati i Data Source Name za povezivanje s bazom.';
$_lang['SCHEME'] = 'Shema';
$_lang['SCHEME_DESC'] = 'Apsolutna putanja do baze, ukoliko koristite bazu nalik SQLite.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'Uobičajeni port za MySQL je 3306. Ostavite na 0 za automatski izbor.';
$_lang['FTPPORT_DESC'] = 'Uobičajeni port za FTP je 21. Ostavite na 0 za automatski izbor.';
$_lang['USE_FTP'] = 'Upotreba FTP';
$_lang['PATH'] = 'Putanja';
$_lang['FTP_PATH_INFO'] = 'Relativna putanja od FTP korena do foldera Elxis instalacije (primer: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Provera FTP podešavanja';
$_lang['CHECK_DB_SETS'] = 'Provera podešavanja baze';
$_lang['DATA_IMPORT'] = 'Uvoz podataka';
$_lang['SETTINGS_ERRORS'] = 'Navedena podešavanja sadrže greške!';
$_lang['NO_QUERIES_WARN'] = 'Inicijalni podaci su uvezeni u bazu, ali izgleda da nijedan upit nije izvršen. Proverite 
	da li su podaci zaista i uvezeni, pre nego što nastavite.';
$_lang['RETRY_PREV_STEP'] = 'Ponovni pokušaj';
$_lang['INIT_DATA_IMPORTED'] = 'Inicijalni podaci su uvezeni u bazu.';
$_lang['QUERIES_EXEC'] = "Izvršeno je %s SQL upita."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Administratorski nalog';
$_lang['CONFIRM_PASS'] = 'Potvrda lozinke';
$_lang['AVOID_COMUNAMES'] = 'Izbegavajte očekivana korisnička imena, kao npr. admin, administrator, itd.';
$_lang['YOUR_DETAILS'] = 'Vaši podaci';
$_lang['PASS_NOMATCH'] = 'Lozinke se ne podudaraju!';
$_lang['REPOPATH_NOEX'] = 'Putanja do spremišta ne postoji!';
$_lang['FINISH'] = 'Kraj';
$_lang['FRIENDLY_URLS'] = 'Prijateljski URL-ovi';
$_lang['FRIENDLY_URLS_DESC'] = 'Preporučujemo da ovo uključite. Kako bi sve funkcionisalo, Elxis će probati da preimenuje htaccess.txt u 
	<strong>.htaccess</strong> . Ukoliko već postoji .htaccess u istom folderu, on će biti obrisan.';
$_lang['GENERAL'] = 'Opšte';
$_lang['ELXIS_INST_SUCC'] = 'Elxis instalacija je uspešno završena.';
$_lang['ELXIS_INST_WARN'] = 'Elxis instalacija je završena uz upozorenja.';
$_lang['CNOT_CREA_CONFIG'] = 'Nije moguće napraviti <strong>configuration.php</strong> fajl u Elxis folderu.';
$_lang['CNOT_REN_HTACC'] = 'Nije m oguće preimenovati <strong>htaccess.txt</strong> fajl u <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'Konfiguracioni fajl';
$_lang['CONFIG_FILE_MANUAL'] = 'Napravite ručno configuration.php fajl, kopirajte sledeći kod i zalepite ga u fajl.';
$_lang['REN_HTACCESS_MANUAL'] = 'Preimenujte ručno <strong>htaccess.txt</strong> fajl u <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Šta dalje?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Radi poboljšanja sigurnosti, preimenujte administracioni folder (<em>estia</em>) u bilo koje drugo ime. 
	Ukoliko to i uradite, morate izmenjeno ime uneti i u .htaccess fajl.';
$_lang['LOGIN_CONFIG'] = 'Prijavite se u administracioni deo i podesite ostale konfiguracione parametre.';
$_lang['VISIT_NEW_SITE'] = 'Posetite svoj sajt';
$_lang['VISIT_ELXIS_SUP'] = 'Posetite sajt Elxis podrške';
$_lang['THANKS_USING_ELXIS'] = 'Hvala što koristite Elxis CMS.';

?>
