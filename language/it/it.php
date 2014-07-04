<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: it-IT (Italian - Italy) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Duilio ( Speck -  http://www.elxisitalia.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('it_IT.utf8', 'it_IT.UTF-8', 'it_IT', 'it', 'italian', 'italy'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //supported formats: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //supported formats: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //example: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%d %b %Y"; //example: Dec 25, 2010 -> italian 25 Dic. 2010
$_lang['DATE_FORMAT_3'] = "%d %B %Y"; //example: December 25, 2010 -> italian 25 Dicembre 2010
$_lang['DATE_FORMAT_4'] = "%d %b %Y %H:%M"; //example: Dec 25, 2010 12:34  -> italian 25 Dic 2010 12:34
$_lang['DATE_FORMAT_5'] = "%d %B %Y %H:%M"; //example: December 25, 2010 12:34 -> italian 25 Dicembre 2010 12:34
$_lang['DATE_FORMAT_6'] = "%d %B %Y %H:%M:%S"; //example: December 25, 2010 12:34:45 -> italian 25 Dicembre 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%d %a %b %Y"; //example: Sat Dec 25, 2010  -> italian Sab 25 Dic 2010
$_lang['DATE_FORMAT_8'] = "%d %A %b %Y"; //example: Saturday Dec 25, 2010  -> italian Sabato 25 Dic 2010
$_lang['DATE_FORMAT_9'] = "%d %A %B %Y"; //example: Saturday December 25, 2010 -> italian Sabato 25 Dicembre 2010
$_lang['DATE_FORMAT_10'] = "%d %A %B %Y %H:%M"; //example: Saturday December 25, 2010 12:34 -> italian Sabato 25 Dicembre 2010 12:34
$_lang['DATE_FORMAT_11'] = "%d %A %B %Y %H:%M:%S"; //example: Saturday December 25, 2010 12:34:45 -> italian Sabato 25 Dicembre 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %d %B %Y %H:%M"; //example: Sat December 25, 2010 12:34 -> italian Sab 25 Dicembre 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %d %B %Y %H:%M:%S"; //example: Sat December 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Gennaio';
$_lang['FEBRUARY'] = 'Febbraio';
$_lang['MARCH'] = 'Marzo';
$_lang['APRIL'] = 'Aprile';
$_lang['MAY'] = 'Maggio';
$_lang['JUNE'] = 'Giugno';
$_lang['JULY'] = 'Luglio';
$_lang['AUGUST'] = 'Agosto';
$_lang['SEPTEMBER'] = 'Settembre';
$_lang['OCTOBER'] = 'Ottobre';
$_lang['NOVEMBER'] = 'Novembre';
$_lang['DECEMBER'] = 'Dicembre';
$_lang['JANUARY_SHORT'] = 'Gen';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Apr';
$_lang['MAY_SHORT'] = 'Mag';
$_lang['JUNE_SHORT'] = 'Giu';
$_lang['JULY_SHORT'] = 'Lug';
$_lang['AUGUST_SHORT'] = 'Ago';
$_lang['SEPTEMBER_SHORT'] = 'Sett';
$_lang['OCTOBER_SHORT'] = 'Ott';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dic';
//day names
$_lang['MONDAY'] = 'Lunedì';
$_lang['THUESDAY'] = 'Martedì';
$_lang['WEDNESDAY'] = 'Mercoledì';
$_lang['THURSDAY'] = 'Giovedì';
$_lang['FRIDAY'] = 'Venerdì';
$_lang['SATURDAY'] = 'Sabato';
$_lang['SUNDAY'] = 'Domenica';
$_lang['MONDAY_SHORT'] = 'Lun';
$_lang['THUESDAY_SHORT'] = 'Mar';
$_lang['WEDNESDAY_SHORT'] = 'Mer';
$_lang['THURSDAY_SHORT'] = 'Gio';
$_lang['FRIDAY_SHORT'] = 'Ven';
$_lang['SATURDAY_SHORT'] = 'Sab';
$_lang['SUNDAY_SHORT'] = 'Dom';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Performance Monitor';
$_lang['ITEM'] = 'Articoli';
$_lang['INIT_FILE'] = 'File di inizializzazione';
$_lang['EXEC_TIME'] = 'Tempo di esecuzione';
$_lang['DB_QUERIES'] = 'DB queries';
$_lang['ERRORS'] = 'Errori';
$_lang['SIZE'] = 'Dimensioni';
$_lang['ENTRIES'] = 'Voci';

/* general */
$_lang['HOME'] = 'Home';
$_lang['YOU_ARE_HERE'] = 'Sei qui';
$_lang['CATEGORY'] = 'Categoria';
$_lang['DESCRIPTION'] = 'Descizione';
$_lang['FILE'] = 'File';
$_lang['IMAGE'] = 'Immagine';
$_lang['IMAGES'] = 'Immagini';
$_lang['CONTENT'] = 'Contenuti';
$_lang['DATE'] = 'Data';
$_lang['YES'] = 'Sì';
$_lang['NO'] = 'No';
$_lang['NONE'] = 'Nessuno';
$_lang['SELECT'] = 'Seleziona';
$_lang['LOGIN'] = 'Login';
$_lang['LOGOUT'] = 'Logout';
$_lang['WEBSITE'] = 'Sito Web';
$_lang['SECURITY_CODE'] = 'Codice di Sicurezza';
$_lang['RESET'] = 'Reset';
$_lang['SUBMIT'] = 'Invia';
$_lang['REQFIELDEMPTY'] = 'Uno o più campi sono vuoti!';
$_lang['FIELDNOEMPTY'] = "%s non può essere vuoto!";
$_lang['FIELDNOACCCHAR'] = "%s contiene caratteri non accettati!";
$_lang['INVALID_DATE'] = 'Data non valida!';
$_lang['INVALID_NUMBER'] = 'Numero non valido!';
$_lang['INVALID_URL'] = 'Indirizzo URL non valido!';
$_lang['FIELDSASTERREQ'] = 'I campi con asterisco * sono obbligatori.';
$_lang['ERROR'] = 'Errore';
$_lang['REGARDS'] = 'Saluti';
$_lang['NOREPLYMSGINFO'] = 'Per favore non rispondere a questo messaggio. Il messaggio è stato inviato solo a scopo informativo.';
$_lang['LANGUAGE'] = 'Lingua';
$_lang['PAGE'] = 'Pagina';
$_lang['PAGEOF'] = "Pagina %s di %s";
$_lang['OF'] = 'di';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Visualizzazione %s a %s di %s articoli";
$_lang['HITS'] = 'Hits';
$_lang['PRINT'] = 'Stampa';
$_lang['BACK'] = 'Torna indietro';
$_lang['PREVIOUS'] = 'Precedente';
$_lang['NEXT'] = 'Successivo';
$_lang['CLOSE'] = 'Chiudi';
$_lang['CLOSE_WINDOW'] = 'Chiudi finestra';
$_lang['COMMENTS'] = 'Commenti';
$_lang['COMMENT'] = 'Commento';
$_lang['PUBLISH'] = 'Pubblica';
$_lang['DELETE'] = 'Cancella';
$_lang['EDIT'] = 'Modifica';
$_lang['COPY'] = 'Copia';
$_lang['SEARCH'] = 'Ricerca';
$_lang['PLEASE_WAIT'] = 'Per favore attendi ...';
$_lang['ANY'] = 'Qualsiasi';
$_lang['NEW'] = 'Nuovo';
$_lang['ADD'] = 'Aggiungi';
$_lang['VIEW'] = 'Vedi';
$_lang['MENU'] = 'Menu';
$_lang['HELP'] = 'Aiuto';
$_lang['TOP'] = 'In Alto';
$_lang['BOTTOM'] = 'In basso';
$_lang['LEFT'] = 'Sinistra';
$_lang['RIGHT'] = 'Destra';
$_lang['CENTER'] = 'Centro';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Abilitare la cache per questo elemento?';
$_lang['YES_FOR_VISITORS'] = 'Sì, per i visitatori';
$_lang['YES_FOR_ALL'] = 'Sì, per tutti';
$_lang['CACHE_LIFETIME'] = 'Durata Cache';
$_lang['CACHE_LIFETIME_D'] = 'Tempo, in minuti, fino a quando la cache viene aggiornata per questo elemento.';
$_lang['NO_PARAMS'] = 'Non ci sono parametri!';
$_lang['STYLE'] = 'Stile';
$_lang['ADVANCED_SETTINGS'] = 'Impostazioni avanzate';
$_lang['CSS_SUFFIX'] = 'Suffisso CSS';
$_lang['CSS_SUFFIX_D'] = 'Un suffisso che sarà aggiunto alla classe CSS del modulo.';
$_lang['MENU_TYPE'] = 'Tipo Menu';
$_lang['ORIENTATION'] = 'Orientamento';
$_lang['SHOW'] = 'Mostra';
$_lang['HIDE'] = 'Nascondi';
$_lang['GLOBAL_SETTING'] = 'Impostazioni globali';

/* users & authentication */
$_lang['USERNAME'] = 'Username';
$_lang['PASSWORD'] = 'Password';
$_lang['NOAUTHMETHODS'] = 'Nessun metodo di autenticazione è stato impostato';
$_lang['AUTHMETHNOTEN'] = 'Metodo di autenticazione %s non è abilitato';
$_lang['PASSTOOSHORT'] = 'La vostra password è troppo corta per essere accettata';
$_lang['USERNOTFOUND'] = 'Utente non trovato';
$_lang['INVALIDUNAME'] = 'Username non valida';
$_lang['INVALIDPASS'] = 'Password non valida';
$_lang['AUTHFAILED'] = 'Autenticazione non riuscita';
$_lang['YACCBLOCKED'] = 'Il tuo account è stato bloccato';
$_lang['YACCEXPIRED'] = 'Il tuo account è scaduto';
$_lang['INVUSERGROUP'] = 'Gruppo utenti non valido';
$_lang['NAME'] = 'Nome';
$_lang['FIRSTNAME'] = 'Nome';
$_lang['LASTNAME'] = 'Cognome';
$_lang['EMAIL'] = 'E-mail';
$_lang['INVALIDEMAIL'] = 'Indirizzo e-mail non valido';
$_lang['ADMINISTRATOR'] = 'Amministratore';
$_lang['GUEST'] = 'Ospite';
$_lang['EXTERNALUSER'] = 'Utente esterno';
$_lang['USER'] = 'Utente';
$_lang['GROUP'] = 'Gruppo';
$_lang['NOTALLOWACCPAGE'] = 'Non sei autorizzato ad accedere a questa pagina!';
$_lang['NOTALLOWACCITEM'] = 'Non sei autorizzato ad accedere a questo elemento!';
$_lang['NOTALLOWMANITEM'] = 'Non sei autorizzato a gestire questo elemento!';
$_lang['NOTALLOWACTION'] = 'Non sei autorizzato a gestire questa azione!';
$_lang['NEED_HIGHER_ACCESS'] = 'Per questa azione devi disporre un un livello di accesso superiore!';
$_lang['AREYOUSURE'] = 'Sei sicuro?';

/* highslide */
$_lang['LOADING'] = 'Sto caricando...';
$_lang['CLICK_CANCEL'] = 'Clicca per cancellare';
$_lang['MOVE'] = 'Muovi';
$_lang['PLAY'] = 'Play';
$_lang['PAUSE'] = 'Pausa';
$_lang['RESIZE'] = 'Ridimensiona';

/* admin */
$_lang['ADMINISTRATION'] = 'Amministazione';
$_lang['SETTINGS'] = 'Impostazioni';
$_lang['DATABASE'] = 'Database';
$_lang['ON'] = 'On';
$_lang['OFF'] = 'Off';
$_lang['WARNING'] = 'Avviso';
$_lang['SAVE'] = 'Salva';
$_lang['APPLY'] = 'Applica';
$_lang['CANCEL'] = 'Cancella';
$_lang['LIMIT'] = 'Limite';
$_lang['ORDERING'] = 'Ordinamento';
$_lang['NO_RESULTS'] = 'Nessun risultato trovato!';
$_lang['CONNECT_ERROR'] = 'Errore di connessione';
$_lang['DELETE_SEL_ITEMS'] = 'Cancellare le voci selezionate?';
$_lang['TOGGLE_SELECTED'] = 'Toggle selezionato';
$_lang['NO_ITEMS_SELECTED'] = 'Nessuna voce selezionata!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'Azione fallita!';
$_lang['ACTION_SUCCESS'] = 'Azione completata con successo!';
$_lang['NO_IMAGE_UPLOADED'] = 'Nessuna immagine caricata';
$_lang['NO_FILE_UPLOADED'] = 'Nessun file caricato';
$_lang['MODULES'] = 'Moduli';
$_lang['COMPONENTS'] = 'Componenti';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Motori di Ricerca';
$_lang['AUTH_METHODS'] = 'Metodi di autenticazione';
$_lang['CONTENT_PLUGINS'] = 'Plugin contenuti';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Pubblicato';
$_lang['ACCESS'] = 'Accesso';
$_lang['ACCESS_LEVEL'] = 'Livello di accesso';
$_lang['TITLE'] = 'Titolo';
$_lang['MOVE_UP'] = 'Muovi sopra';
$_lang['MOVE_DOWN'] = 'Muovi sotto';
$_lang['WIDTH'] = 'Larghezza';
$_lang['HEIGHT'] = 'Altezza';
$_lang['ITEM_SAVED'] = 'Voce salvata';
$_lang['FIRST'] = 'Primo';
$_lang['LAST'] = 'Ultimo';
$_lang['SUGGESTED'] = 'Suggerito';
$_lang['VALIDATE'] = 'Convalidato';
$_lang['NEVER'] = 'Mai';
$_lang['ALL'] = 'Tutto';
$_lang['ALL_GROUPS_LEVEL'] = "Tutti i gruppi di livello %s";
$_lang['REQDROPPEDSEC'] = 'La richiesta non è stata accettata per motivi di sicurezza. Si prega di riprovare.';
$_lang['PROVIDE_TRANS'] = 'Si prega di fornire una traduzione!';
$_lang['AUTO_TRANS'] = 'Traduzione automatica';
$_lang['STATISTICS'] = 'Statistiche';
$_lang['UPLOAD'] = 'Upload';
$_lang['MORE'] = 'Di più...';

?>