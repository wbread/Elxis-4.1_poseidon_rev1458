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


$locale = array('hr_CS.UTF-8', 'hr_Cyrl_CS@UTF-8', 'hr_HR', 'hr', 'croatian', 'croatia');

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; 
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; 
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; 
$_lang['DATE_FORMAT_2'] = "%d %b, %Y"; 
$_lang['DATE_FORMAT_3'] = "%d %B, %Y"; 
$_lang['DATE_FORMAT_4'] = "%d %b, %Y %H:%M"; 
$_lang['DATE_FORMAT_5'] = "%d %B, %Y %H:%M"; 
$_lang['DATE_FORMAT_6'] = "%d %B, %Y %H:%M:%S"; 
$_lang['DATE_FORMAT_7'] = "%a %d %b, %Y"; 
$_lang['DATE_FORMAT_8'] = "%A %d %b, %Y"; 
$_lang['DATE_FORMAT_9'] = "%A %d %B, %Y"; 
$_lang['DATE_FORMAT_10'] = "%A %d %B, %Y %H:%M"; 
$_lang['DATE_FORMAT_11'] = "%A %d %B, %Y %H:%M:%S"; 
$_lang['DATE_FORMAT_12'] = "%a %d %B, %Y %H:%M"; 
$_lang['DATE_FORMAT_13'] = "%a %d %B, %Y %H:%M:%S"; 
$_lang['THOUSANDS_SEP'] = '.';
$_lang['DECIMALS_SEP'] = ',';
//month names
$_lang['JANUARY'] = 'Siječanj';
$_lang['FEBRUARY'] = 'Veljača';
$_lang['MARCH'] = 'Ožujak';
$_lang['APRIL'] = 'Travanj';
$_lang['MAY'] = 'Svibanj';
$_lang['JUNE'] = 'Lipanj';
$_lang['JULY'] = 'Srpanj';
$_lang['AUGUST'] = 'Kolovoz';
$_lang['SEPTEMBER'] = 'Rujan';
$_lang['OCTOBER'] = 'Listopad';
$_lang['NOVEMBER'] = 'Studeni';
$_lang['DECEMBER'] = 'Prosinac';
$_lang['JANUARY_SHORT'] = 'Sij';
$_lang['FEBRUARY_SHORT'] = 'Velj';
$_lang['MARCH_SHORT'] = 'Ožu';
$_lang['APRIL_SHORT'] = 'Tra';
$_lang['MAY_SHORT'] = 'Svi';
$_lang['JUNE_SHORT'] = 'Lip';
$_lang['JULY_SHORT'] = 'Srp';
$_lang['AUGUST_SHORT'] = 'Kol';
$_lang['SEPTEMBER_SHORT'] = 'Ruj';
$_lang['OCTOBER_SHORT'] = 'Lis';
$_lang['NOVEMBER_SHORT'] = 'Stu';
$_lang['DECEMBER_SHORT'] = 'Pros';
//day names
$_lang['MONDAY'] = 'Ponedjeljak';
$_lang['THUESDAY'] = 'Utorak';
$_lang['WEDNESDAY'] = 'Srijeda';
$_lang['THURSDAY'] = 'Četvrtak';
$_lang['FRIDAY'] = 'Petak';
$_lang['SATURDAY'] = 'Subota';
$_lang['SUNDAY'] = 'Nedjelja';
$_lang['MONDAY_SHORT'] = 'Pon';
$_lang['THUESDAY_SHORT'] = 'Uto';
$_lang['WEDNESDAY_SHORT'] = 'Sre';
$_lang['THURSDAY_SHORT'] = 'Čet';
$_lang['FRIDAY_SHORT'] = 'Pet';
$_lang['SATURDAY_SHORT'] = 'Sub';
$_lang['SUNDAY_SHORT'] = 'Ned';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis monitor performansi';
$_lang['ITEM'] = 'Stavka';
$_lang['INIT_FILE'] = 'Inicijalizacijski dokumenat ';
$_lang['EXEC_TIME'] = 'Vrijeme izvršenja ';
$_lang['DB_QUERIES'] = 'Upiti';
$_lang['ERRORS'] = 'Greške';
$_lang['SIZE'] = 'Veličina';
$_lang['ENTRIES'] = 'Unosi';

/* general */
$_lang['HOME'] = 'Naslovna';
$_lang['YOU_ARE_HERE'] = 'Nalazite se ovdje';
$_lang['CATEGORY'] = 'Kategorija';
$_lang['DESCRIPTION'] = 'Opis';
$_lang['FILE'] = 'Dokumenat';
$_lang['IMAGE'] = 'Slika';
$_lang['IMAGES'] = 'Slike';
$_lang['CONTENT'] = 'Sadržaj';
$_lang['DATE'] = 'Datum';
$_lang['YES'] = 'Da';
$_lang['NO'] = 'Ne';
$_lang['NONE'] = 'Ništa';
$_lang['SELECT'] = 'Izbor';
$_lang['LOGIN'] = 'Prijava';
$_lang['LOGOUT'] = 'Odjava';
$_lang['WEBSITE'] = 'Web stranica';
$_lang['SECURITY_CODE'] = 'Sigurnosni kod';
$_lang['RESET'] = 'Reset';
$_lang['SUBMIT'] = 'Slanje';
$_lang['REQFIELDEMPTY'] = 'Jedno ili više obaveznih polja je prazno!';
$_lang['FIELDNOEMPTY'] = "%s ne može biti prazno!";
$_lang['FIELDNOACCCHAR'] = "%s sadrži neprihvatljive znakove!";
$_lang['INVALID_DATE'] = 'Pogrešan datum! ';
$_lang['INVALID_NUMBER'] = 'Pogrešan broj! ';
$_lang['INVALID_URL'] = 'Neispravan URL';
$_lang['FIELDSASTERREQ'] = 'Polja sa zvjezdicom * su obavezna. ';
$_lang['ERROR'] = 'Greška';
$_lang['REGARDS'] = 'Pozdrav';
$_lang['NOREPLYMSGINFO'] = 'Molimo vas da ne odgovarate na ovu poruku jer je poslana u informativne svrhe. ';
$_lang['LANGUAGE'] = 'Jezik';
$_lang['PAGE'] = 'Strana';
$_lang['PAGEOF'] = "Strana %s od %s";
$_lang['OF'] = 'od';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Prikazivanje %s od %s stavki %s";
$_lang['HITS'] = 'Pregledi';
$_lang['PRINT'] = 'Ispis';
$_lang['BACK'] = 'Nazad';
$_lang['PREVIOUS'] = 'Prethodno';
$_lang['NEXT'] = 'Dalje';
$_lang['CLOSE'] = 'Zatvaranje';
$_lang['CLOSE_WINDOW'] = 'Zatvaranje prozora';
$_lang['COMMENTS'] = 'Komentari';
$_lang['COMMENT'] = 'Komentar';
$_lang['PUBLISH'] = 'Objavljivanje';
$_lang['DELETE'] = 'Brisanje';
$_lang['EDIT'] = 'Izmjena';
$_lang['COPY'] = 'Kopiranje';
$_lang['SEARCH'] = 'Pretraga';
$_lang['PLEASE_WAIT'] = 'Molimo sačekajte ...';
$_lang['ANY'] = 'Sve';
$_lang['NEW'] = 'Novo';
$_lang['ADD'] = 'Dodavanje';
$_lang['VIEW'] = 'Pregledi';
$_lang['MENU'] = 'Meni';
$_lang['HELP'] = 'Pomoć';
$_lang['TOP'] = 'Gore';
$_lang['BOTTOM'] = 'Dolje';
$_lang['LEFT'] = 'Lijevo';
$_lang['RIGHT'] = 'Desno';
$_lang['CENTER'] = 'Sredina';

/* xml */
$_lang['CACHE'] = 'Keš';
$_lang['ENABLE_CACHE_D'] = 'Omogućavanje keša za ovu stavku?';
$_lang['YES_FOR_VISITORS'] = 'Da, za posjetioce ';
$_lang['YES_FOR_ALL'] = 'Da, za sve ';
$_lang['CACHE_LIFETIME'] = 'Trajanje keša';
$_lang['CACHE_LIFETIME_D'] = 'Vrijeme, u minutima, do osvježavanja keša za ovu stavku. ';
$_lang['NO_PARAMS'] = 'Nema postavki!';
$_lang['STYLE'] = 'Stil';
$_lang['ADVANCED_SETTINGS'] = 'Napredna podešavanja';
$_lang['CSS_SUFFIX'] = 'CSS sufiks';
$_lang['CSS_SUFFIX_D'] = 'Sufiks koji će biti dodan CSS klasi modula. ';
$_lang['MENU_TYPE'] = 'Tip menija ';
$_lang['ORIENTATION'] = 'Orijentacija';
$_lang['SHOW'] = 'Prikaz';
$_lang['HIDE'] = 'Skrivanje';
$_lang['GLOBAL_SETTING'] = 'Globalno podešavanje';

/* users & authentication */
$_lang['USERNAME'] = 'Korisničko ime';
$_lang['PASSWORD'] = 'Lozinka';
$_lang['NOAUTHMETHODS'] = 'Nema postavljenih autentikacijskih metoda';
$_lang['AUTHMETHNOTEN'] = 'Autentikacijska metoda %s nije omogućena';
$_lang['PASSTOOSHORT'] = 'Vaša lozinka je prekratka';
$_lang['USERNOTFOUND'] = 'Korisnik nije pronađen ';
$_lang['INVALIDUNAME'] = 'Pogrešno korisničko ime';
$_lang['INVALIDPASS'] = 'Neispravna lozinka';
$_lang['AUTHFAILED'] = 'Autentikacija nije uspjela';
$_lang['YACCBLOCKED'] = 'Vaš račun je blokiran ';
$_lang['YACCEXPIRED'] = 'Vaš račun je istekao ';
$_lang['INVUSERGROUP'] = 'Pogrešna grupa korisnika ';
$_lang['NAME'] = 'Ime';
$_lang['FIRSTNAME'] = 'Ime';
$_lang['LASTNAME'] = 'Prezime';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Nevažeća e-mail adresa';
$_lang['ADMINISTRATOR'] = 'Administrator';
$_lang['GUEST'] = 'Gost';
$_lang['EXTERNALUSER'] = 'Vanjski korisnik';
$_lang['USER'] = 'Korisnik';
$_lang['GROUP'] = 'Grupa';
$_lang['NOTALLOWACCPAGE'] = 'Nije Vam dozvoljen pristup ovoj stranici! ';
$_lang['NOTALLOWACCITEM'] = 'Nije Vam dozvoljen pristup ovom članku! ';
$_lang['NOTALLOWMANITEM'] = 'Nije Vam dozvoljeno upravljanje ovom stavkom! ';
$_lang['NOTALLOWACTION'] = 'Nije Vam dozvoljeno obavljanje ove radnje! ';
$_lang['NEED_HIGHER_ACCESS'] = 'Morate imati viši nivo pristupa za ovu radnju!';
$_lang['AREYOUSURE'] = 'Jeste li sigurni?';

/* highslide */
$_lang['LOADING'] = 'Učitavanje ...';
$_lang['CLICK_CANCEL'] = 'Kliknite za odustanak';
$_lang['MOVE'] = 'Premještanje';
$_lang['PLAY'] = 'Pokretanje';
$_lang['PAUSE'] = 'Pauza';
$_lang['RESIZE'] = 'Povećavanje';

/* admin */
$_lang['ADMINISTRATION'] = 'Administracija';
$_lang['SETTINGS'] = 'Postavke';
$_lang['DATABASE'] = 'Baza podataka';
$_lang['ON'] = 'Uključeno';
$_lang['OFF'] = 'Isključeno';
$_lang['WARNING'] = 'Upozorenje';
$_lang['SAVE'] = 'Spremanje';
$_lang['APPLY'] = 'Primjena';
$_lang['CANCEL'] = 'Odustjanje';
$_lang['LIMIT'] = 'Limit';
$_lang['ORDERING'] = 'Poredak';
$_lang['NO_RESULTS'] = 'Nema rezultata!';
$_lang['CONNECT_ERROR'] = 'Greška pri povezivanju';
$_lang['DELETE_SEL_ITEMS'] = 'Brisanje izabrane stavke?';
$_lang['TOGGLE_SELECTED'] = 'Promjenite izabrano';
$_lang['NO_ITEMS_SELECTED'] = 'Nema izabranih stavki';
$_lang['ID'] = 'ID ';
$_lang['ACTION_FAILED'] = 'Proces nije uspio! ';
$_lang['ACTION_SUCCESS'] = 'Proces je uspješno završen!';
$_lang['NO_IMAGE_UPLOADED'] = 'Nema postavljene slike';
$_lang['NO_FILE_UPLOADED'] = 'Ne postoji dokumenat';
$_lang['MODULES'] = 'Moduli';
$_lang['COMPONENTS'] = 'Komponente';
$_lang['TEMPLATES'] = 'Šablone';
$_lang['SEARCH_ENGINES'] = 'Pretraživači';
$_lang['AUTH_METHODS'] = 'Metoda autentikacije';
$_lang['CONTENT_PLUGINS'] = 'Dodatci sadržaja';
$_lang['PLUGINS'] = 'Dodatci';
$_lang['PUBLISHED'] = 'Objavljeno';
$_lang['ACCESS'] = 'Pristup ';
$_lang['ACCESS_LEVEL'] = 'Pristupni nivo';
$_lang['TITLE'] = 'Naslov';
$_lang['MOVE_UP'] = 'Gore';
$_lang['MOVE_DOWN'] = 'Dolje';
$_lang['WIDTH'] = 'Širina';
$_lang['HEIGHT'] = 'Visina ';
$_lang['ITEM_SAVED'] = 'Dokumenat je spremljen';
$_lang['FIRST'] = 'Prvo ';
$_lang['LAST'] = 'Posljednje ';
$_lang['SUGGESTED'] = 'Predloženo ';
$_lang['VALIDATE'] = 'Provjera';
$_lang['NEVER'] = 'Nikada';
$_lang['ALL'] = 'Sve';
$_lang['ALL_GROUPS_LEVEL'] = "Sve grupe nivoa %s";
$_lang['REQDROPPEDSEC'] = 'Vaš zahtjev je odbijen iz sigurnosnih razloga. Pokušajte ponovo. ';
$_lang['PROVIDE_TRANS'] = 'Molimo da dostavite prijevod!';
$_lang['AUTO_TRANS'] = 'Automatski prijevod';
$_lang['STATISTICS'] = 'Statistika';
$_lang['UPLOAD'] = 'Dodavanje';
$_lang['MORE'] = 'I još';

?>