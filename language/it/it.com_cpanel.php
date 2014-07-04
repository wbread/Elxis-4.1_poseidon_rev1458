<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: it-IT (Italian - Italy) language for component CPanel
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Duilio ( Speck -  http://www.elxisitalia.com )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Pannello di controllo';
$_lang['GENERAL_SITE_SETS'] = 'Impostazioni generali del sito';
$_lang['LANGS_MANAGER'] = 'Gestione lingue';
$_lang['MANAGE_SITE_LANGS'] = 'Gestione lingue del sito';
$_lang['USERS'] = 'Utenti';
$_lang['MANAGE_USERS'] = 'Crea, modifica e cancella account utenti';
$_lang['USER_GROUPS'] = 'Gruppi utente';
$_lang['MANAGE_UGROUPS'] = 'Gestione gruppi utente';
$_lang['MEDIA_MANAGER'] = 'Gestione media';
$_lang['MEDIA_MANAGER_INFO'] = 'Gestione files multimediali';
$_lang['ACCESS_MANAGER'] = 'Gestione accessi';
$_lang['MANAGE_ACL'] = 'Gestione lista controllo accessi';
$_lang['MENU_MANAGER'] = 'Gesione menu';
$_lang['MANAGE_MENUS_ITEMS'] = 'Gestione menu ed elemento menu';
$_lang['FRONTPAGE'] = 'Frontpage';
$_lang['DESIGN_FRONTPAGE'] = 'Progettazione frontpage sito';
$_lang['CATEGORIES_MANAGER'] = 'Gestione categorie';
$_lang['MANAGE_CONT_CATS'] = 'Gestione contenuti categorie';
$_lang['CONTENT_MANAGER'] = 'Gestione Contenuti';
$_lang['MANAGE_CONT_ITEMS'] = 'Gestione Elementi contenuti';
$_lang['MODULES_MANAGE_INST'] = 'Gestione Moduli e installazione nuovi moduli.';
$_lang['PLUGINS_MANAGE_INST'] = 'Gestione  Plugins e installazione nuovi plugin.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Gestione Componenti e installazione nuovi componenti.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Gestione Templates e installazione nuovi componenti.';
$_lang['SENGINES_MANAGE_INST'] = 'Gestione Motori di Ricerca e installazione nuovi motori di ricerca.';
$_lang['MANAGE_WAY_LOGIN'] = 'Gestione modalità login utenti nel sito.';
$_lang['TRANSLATOR'] = 'Traduzione';
$_lang['MANAGE_MLANG_CONTENT'] = 'Gestione contenuti multilingua';
$_lang['LOGS'] = 'Logs';
$_lang['VIEW_MANAGE_LOGS'] = 'Vedi e gestisci i files di log';
$_lang['GENERAL'] = 'Generale';
$_lang['WEBSITE_STATUS'] = 'Stato Sito Web';
$_lang['ONLINE'] = 'Online';
$_lang['OFFLINE'] = 'Offline';
$_lang['ONLINE_ADMINS'] = 'Online solo per gli amministratori';
$_lang['OFFLINE_MSG'] = 'Messaggio Offline';
$_lang['OFFLINE_MSG_INFO'] = 'Lasciare questo campo vuoto per visualizzare in automatico un messaggio multilingua';
$_lang['SITENAME'] = 'Nome del sito';
$_lang['URL_ADDRESS'] = 'Indirizzo URL';
$_lang['REPO_PATH'] = 'Percorso (path) Repository';
$_lang['REPO_PATH_INFO'] = 'Percorso completo alla cartella Repository di Elxis. Lasciare vuoto per impostazione posizione predefinita 
	(elxis_root/repository/). Si consiglia di spostare questa cartella sopra la cartella WWW 
	e rinominarla in qualcosa di non prevedibile!';
$_lang['FRIENDLY_URLS'] = 'URLs amichevoli';
$_lang['SEF_INFO'] = 'Se impostato a SÌ (raccomandato) rinominare il file htaccess.txt in .htaccess';
$_lang['STATISTICS_INFO'] = 'Attivare la raccolta di statistiche sul traffico del sito?';
$_lang['GZIP_COMPRESSION'] = 'Compressione GZip';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis comprimerà il documento in formato GZIP prima di inviarlo al browser consentendo così di risparmiare il 70-80% di larghezza banda.';
$_lang['DEFAULT_ROUTE'] = 'Instradamento di default';
$_lang['DEFAULT_ROUTE_INFO'] = 'Un URI formattato Elxis che verrà utilizzato come frontpage del sito';
$_lang['META_DATA'] = 'META data';
$_lang['META_DATA_INFO'] = 'Breve descrizione del sito web';
$_lang['KEYWORDS'] = 'Parole Chiave';
$_lang['KEYWORDS_INFO'] = 'Alcune parole chiave separate da virgola';
$_lang['STYLE_LAYOUT'] = 'Stile e layout';
$_lang['SITE_TEMPLATE'] = 'Template del sito';
$_lang['ADMIN_TEMPLATE'] = 'Amministrazione Templates';
$_lang['ICONS_PACK'] = 'Icons pack';
$_lang['LOCALE'] = 'Locale';
$_lang['TIMEZONE'] = 'Ora locale';
$_lang['MULTILINGUISM'] = 'Multilingua';
$_lang['MULTILINGUISM_INFO'] = 'Consente di inserire elementi di testo in più lingue (traduzioni).
Non abilitarla se si non si desidera usare questa opzione in quanto rallenta il sito. L\'interfaccia di Elxis
sarà sempre con più lingue, anche se questa opzione è impostata su No.';
$_lang['CHANGE_LANG'] = 'Cambia lingua';
$_lang['LANG_CHANGE_WARN'] = 'Se si modifica la lingua di default ci potrebbero essere delle incongruenze
tra gli indici delle lingua e le traduzioni nella tabella traduzioni.';
$_lang['CACHE'] = 'Cache';
$_lang['CACHE_INFO'] = 'Per velocizzare la successiva rigenerazione dei contenuti presenti nel vostro sito, con Elxis è possibile salvare il codice HTML generato da singoli elementi nella cache.
Si tratta di una impostazione generale, è necessario attivare anche la cache sugli elementi (es. moduli) che si desiderano memorizzare nella cache.';
$_lang['APC_INFO'] = 'L\'alternativa PHP Cache (APC) è una cache opcode per PHP. Essa deve essere supportata dal web server.
Questa ozpzione non è raccomandata in ambienti di hosting condiviso. Elxis la userà nelle apposite pagine del sito per migliorare la performance.';
$_lang['APC_ID_INFO'] = 'Nel caso in cui più siti sono ospitati sullo stesso server, identificali 
	fornendo un valore integrale univoco a questo sito.';
$_lang['USERS_AND_REGISTRATION'] = 'Utenti e Registrazione';
$_lang['PRIVACY_PROTECTION'] = 'Protezione Privacy';
$_lang['PASSWORD_NOT_SHOWN'] = 'La password corrente non è mostrata per ragioni di sicurezza. 
	Compila questo campo solo se si desidera cambiare la password corrente.';
$_lang['DB_TYPE'] = 'Tipo do Database';
$_lang['ALERT_CON_LOST'] = 'Se si cambia la connessione al database corrente, sarà persa!';
$_lang['HOST'] = 'Host';
$_lang['PORT'] = 'Porta';
$_lang['PERSISTENT_CON'] = 'Connessione persistente';
$_lang['DB_NAME'] = 'Nome DB';
$_lang['TABLES_PREFIX'] = 'Prefisso tabelle';
$_lang['DSN_INFO'] = 'Stringa pronta da usare come Nome della fonte dati per la connessione al Database.';
$_lang['SCHEME'] = 'Schema';
$_lang['SCHEME_INFO'] = 'Percorso assoluto (path assoluta) al file del database se si usa un database come SQLite.';
$_lang['SEND_METHOD'] = 'Metodo d\'invio';
$_lang['SMTP_OPTIONS'] = 'Opsioni SMTP';
$_lang['AUTH_REQ'] = 'Autenticazione richiesta';
$_lang['SECURE_CON'] = 'Connessione sicura';
$_lang['SENDER_NAME'] = 'Nome mittente';
$_lang['SENDER_EMAIL'] = 'E-mail mittente';
$_lang['RCPT_NAME'] = 'Nome del destinatario';
$_lang['RCPT_EMAIL'] = 'E-mail del destinatario';
$_lang['TECHNICAL_MANAGER'] = 'Direttore Tecnico';
$_lang['TECHNICAL_MANAGER_INFO'] = 'Il responsabile tecnico riceve un avviso relativo a errori di sicurezza.';
$_lang['USE_FTP'] = 'Usa FTP';
$_lang['PATH'] = 'Percorso (path)';
$_lang['FTP_PATH_INFO'] = 'Percorso relativo (Path relativa) dalla cartella principale FTP alla cartella di installazione di Elxis (esempio: /public_html).';
$_lang['SESSION'] = 'Sessione';
$_lang['HANDLER'] = 'Handler';
$_lang['HANDLER_INFO'] = 'Elxis può salvare le sessioni come files nella cartella Repository o nel database. 
	Si può anche scegliere Nessuno per lasciare a PHP il salvataggio delle sessioni nella posizione di default del server.';
$_lang['FILES'] = 'Files';
$_lang['LIFETIME'] = 'Durata';
$_lang['SESS_LIFETIME_INFO'] = 'Tempo di durata della sessione quando si è inattivi.';
$_lang['CACHE_TIME_INFO'] = 'Dopo questo tempo gli oggetti saranno rigenerati nella cache.';
$_lang['MINUTES'] = 'minuti';
$_lang['HOURS'] = 'ore';
$_lang['MATCH_IP'] = 'IP corrispondente';
$_lang['MATCH_BROWSER'] = 'Browser corrispondente';
$_lang['MATCH_REFERER'] = 'HTTP Referrer corrispondente';
$_lang['MATCH_SESS_INFO'] = 'Abilita una sessione avanzata della routine di validazone.';
$_lang['ENCRYPTION'] = 'Crittografia';
$_lang['ENCRYPT_SESS_INFO'] = 'Crittografia dei dati di sessione?';
$_lang['ERRORS'] = 'Errori';
$_lang['WARNINGS'] = 'Avvertenze';
$_lang['NOTICES'] = 'Notizie';
$_lang['NOTICE'] = 'Notizia';
$_lang['REPORT'] = 'Report';
$_lang['REPORT_INFO'] = 'Livello del report di errori. Sui siti in fase di aggiornamento si consiglia di impostare su off.';
$_lang['LOG'] = 'Log';
$_lang['LOG_INFO'] = 'Livello di errore del log. Seleziona quali errori desideri vengono segnalati da Elxis nel file log di sistema (repository/logs/).';
$_lang['ALERT'] = 'Allarme';
$_lang['ALERT_INFO'] = 'Invia Errori fatali di posta al responsabile tecnico del sito.';
$_lang['ROTATE'] = 'Rotazione';
$_lang['ROTATE_INFO'] = 'Rotazione dei log di errore alla fine di ogni mese. Raccomandato.';
$_lang['DEBUG'] = 'Debug';
$_lang['MODULE_POS'] = 'Posizione moduli';
$_lang['MINIMAL'] = 'Ridotto';
$_lang['FULL'] = 'Completo';
$_lang['DISPUSERS_AS'] = 'Visualizza utenti come';
$_lang['USERS_REGISTRATION'] = 'Registrazione utenti';
$_lang['ALLOWED_DOMAIN'] = 'Domini consentiti';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Scrivi un nome dominio (es.: elxis.org) solo per il quale il sistema accetterà gli indirizzi e-mail di registrazione.';
$_lang['EXCLUDED_DOMAINS'] = 'Domini esclusi';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Lista di domini separati da virgola (es.: badsite.com,hacksite.com) 
	dei quali gli indirizzi e-mail non saranno accettati in fase di registrazione.';
$_lang['ACCOUNT_ACTIVATION'] = 'Attivazione account';
$_lang['DIRECT'] = 'Diretta';
$_lang['MANUAL_BY_ADMIN'] = 'Manuale da parte dell\'amministratore';
$_lang['PASS_RECOVERY'] = 'Recupero password';
$_lang['SECURITY'] = 'Sicurezza';
$_lang['SECURITY_LEVEL'] = 'Livello di sicurezza';
$_lang['SECURITY_LEVEL_INFO'] = 'Aumentando il livello di sicurezza, alcune opzioni sono forzatamente abilitate
mentre alcune caratteristiche possono essere disattivate. Consultare la documentazione di Elxis per saperne di più.';
$_lang['NORMAL'] = 'Normale';
$_lang['HIGH'] = 'Alta';
$_lang['INSANE'] = 'Folle';
$_lang['ENCRYPT_METHOD'] = 'Metodo crittografia';
$_lang['AUTOMATIC'] = 'Automatico';
$_lang['ENCRYPTION_KEY'] = 'Chiave crittografata';
$_lang['ELXIS_DEFENDER'] = 'Elxis defender';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis defender protegge il tuo sito web dagli attacchi di iniezione XSS e SQL.
Questo potente strumento filtra le richieste degli utenti e blocca gli attacchi al tuo sito. Sarai anche informato anche per
un attacco e un accesso (log-it). È possibile selezionare quale tipo di filtri da applicare o addirittura bloccare i file di sistema più importanti per modifiche non autorizzate. Più sono i filtri abilitati più lento sarà il vostro sito. Si consiglia l\'attivazione delle opzioni G, C e F.  Consultare la documentazione di Elxis per saperne di più.';
$_lang['SSL_SWITCH'] = 'Passa a SSL';
$_lang['SSL_SWITCH_INFO'] = 'Nelle pagine dove la privacy è importante, Elxis passa automaticamente da HTTP a HTTPS.
Per l\'area di amministrazione lo schema HTTPS sarà permanente. Richiede un certificato SSL!';
$_lang['PUBLIC_AREA'] = 'Area pubblica';
$_lang['GENERAL_FILTERS'] = 'Regole generali';
$_lang['CUSTOM_FILTERS'] = 'Regole personalizzate';
$_lang['FSYS_PROTECTION'] = 'Protezione del file di sistema';
$_lang['CHECK_FTP_SETS'] = 'Controlla settaggi FTP';
$_lang['FTP_CON_SUCCESS'] = 'La connessione FTP è avvenuta con successo.';
$_lang['ELXIS_FOUND_FTP'] = 'Installazione Elxis è stata trovato su FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'Installazione Elxis non è stata trovato su FTP! COntrolla il valore dell\'opzione del percorso (path) FTP.';
$_lang['CAN_NOT_CHANGE'] = 'Non può essere cambiata.';
$_lang['SETS_SAVED_SUCC'] = 'Impostazioni salvate con successo';
$_lang['ACTIONS'] = 'Azioni';
$_lang['BAN_IP_REQ_DEF'] = 'Per bannare un indirizzo IP è richiesta l\'abilitazione di almeno una opzione di Elxid defender!';
$_lang['BAN_YOURSELF'] = 'Stai tendando di bannare te stesso?';
$_lang['IP_AL_BANNED'] = 'Questo IP è già bannato!';
$_lang['IP_BANNED'] = 'Indirizzi IP %s bannati!';
$_lang['BAN_FAILED_NOWRITE'] = 'Ban fallito! Il file repository/logs/defender_ban.php non è scrivibile.';
$_lang['ONLY_ADMINS_ACTION'] = 'Solo gli amministratori possono eseguire questa azione!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Non è possibile disconnettere un amministratore!';
$_lang['USER_LOGGED_OUT'] = 'L\'utente è stato sconnesso!';
$_lang['SITE_STATISTICS'] = 'Statistiche del sito';
$_lang['SITE_STATISTICS_INFO'] = 'Vedere le statistiche del traffico del sito';
$_lang['BACKUP'] = 'Backup';
$_lang['BACKUP_INFO'] = 'Prendi un nuovo backup completo del sito e gestisci quelli esistenti.';
$_lang['BACKUP_FLIST'] = 'Lista files di Backup esistenti';
$_lang['TYPE'] = 'Tipo';
$_lang['FILENAME'] = 'Nome file';
$_lang['SIZE'] = 'Dimensione';
$_lang['NEW_DB_BACKUP'] = 'Nuovo backup database';
$_lang['NEW_FS_BACKUP'] = 'Nuovo backup del file di sistema';
$_lang['FILESYSTEM'] = 'File di sistema';
$_lang['DOWNLOAD'] = 'Download';
$_lang['TAKE_NEW_BACKUP'] = 'Prendi un nuovo backup? \nQuesto può richiedere tempo, sei pregato di pazientare!';
$_lang['FOLDER_NOT_EXIST'] = "Cartella %s non esiste!";
$_lang['FOLDER_NOT_WRITE'] = "Cartella %s non è scrivibile!";
$_lang['BACKUP_SAVED_INTO'] = "I files di backup sono stati salvati in %s";
$_lang['CACHE_SAVED_INTO'] = "Il files di cache sono stati salvati in %s";
$_lang['CACHED_ITEMS'] = 'Elementi memorizzati nella cache';
$_lang['ELXIS_ROUTER'] = 'Instradamento Elxis';
$_lang['ROUTING'] = 'Instradamento';
$_lang['ROUTING_INFO'] = 'Il ri-instradamento dell\'utente richiede un URL personalizzato.';
$_lang['SOURCE'] = 'Fonte';
$_lang['ROUTE_TO'] = 'Instradamento a';
$_lang['REROUTE'] = "Ri-instradamento %s";
$_lang['DIRECTORY'] = 'Directory';
$_lang['SET_FRONT_CONF'] = 'Imposta il frontpage del sito nella configurazione ElxisS!';
$_lang['ADD_NEW_ROUTE'] = 'Inserisci un nuovo instradamento';
$_lang['OTHER'] = 'Altro';
$_lang['LAST_MODIFIED'] = 'Ultima modifica';
$_lang['PERIOD'] = 'Orario'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'La registrazione degli errori è disabilitata!';
$_lang['LOG_ENABLE_ERR'] = 'Il registro (log) è abilitato solo per errori irreversibili.';
$_lang['LOG_ENABLE_ERRWARN'] = 'Il registro (log) è abilitato per errori e avvisi.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Il registro (log) è abilitato per errori, avvisi e comunicazioni.';
$_lang['LOGROT_ENABLED'] = 'Rotazione dei registri (logs) è abilitato.';
$_lang['LOGROT_DISABLED'] = 'Rotazione dei registri (logs) è disabilitato!';
$_lang['SYSLOG_FILES'] = 'Files log di sistema';
$_lang['DEFENDER_BANS'] = 'Defender banaggi';
$_lang['LAST_DEFEND_NOTIF'] = 'Ultima notifica del Defender';
$_lang['LAST_ERROR_NOTIF'] = 'Ultima notifica di Erroe';
$_lang['TIMES_BLOCKED'] = 'Tempo bloccato';
$_lang['REFER_CODE'] = 'Codice di riferimento';
$_lang['CLEAR_FILE'] = 'Pulisci file';
$_lang['CLEAR_FILE_WARN'] = 'I contenuti del file saranno rimossi. Vuoi continuare?';
$_lang['FILE_NOT_FOUND'] = 'File non trovato!';
$_lang['FILE_CNOT_DELETE'] = 'Questo file non può essere cancellato!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Solo i file con estensione .log possono essere scaricati!';
$_lang['SYSTEM'] = 'Sistema';
$_lang['PHP_INFO'] = 'Informazioni PHP';
$_lang['PHP_VERSION'] = 'Versione PHP';
$_lang['ELXIS_INFO'] = 'Informazioni Elxis';
$_lang['VERSION'] = 'Versione';
$_lang['REVISION_NUMBER'] = 'Numero di revisione';
$_lang['STATUS'] = 'Stato';
$_lang['CODENAME'] = 'Nome in codice';
$_lang['RELEASE_DATE'] = 'Data di rilascio';
$_lang['COPYRIGHT'] = 'Copyright';
$_lang['POWERED_BY'] = 'Powered by';
$_lang['AUTHOR'] = 'Autore';
$_lang['PLATFORM'] = 'Piattaforma';
$_lang['HEADQUARTERS'] = 'Sede centrale';
$_lang['ELXIS_ENVIROMENT'] = 'Ambiente Elxis';
$_lang['DEFENDER_LOGS'] = 'Logs del defender';
$_lang['ADMIN_FOLDER'] = 'Cartella di Amministrazione';
$_lang['DEF_NAME_RENAME'] = 'Nome di default, rinominalo!';
$_lang['INSTALL_PATH'] = 'Percorso (path) di installazione';
$_lang['IS_PUBLIC'] = 'È pubblico!';
$_lang['CREDITS'] = 'Credits';
$_lang['LOCATION'] = 'Posizione';
$_lang['CONTRIBUTION'] = 'Contributo';
$_lang['LICENSE'] = 'Licenza';
$_lang['MULTISITES'] = 'Siti multipli';
$_lang['MULTISITES_DESC'] = 'Gestisci siti multipli sotto una installazione di Elxis.';
$_lang['MULTISITES_WARN'] = 'È possibile avere più siti in una sola installazione di Elxis. Lavorare con più siti
è un compito che richiede conoscenze avanzate di Elxis CMS. Prima di importare dati in un nuovo
Multisito, assicurarsi che il database esiste. Dopo aver creato una nuovo sito multiplo, il file htaccess
si basa sulle istruzioni date. L\'eliminazione di un multisito non elimina il database collegato. Se hai bisogno di aiuto, consultare un
tecnico con esperienza';
$_lang['MULTISITES_DISABLED'] = 'Siti multipli sono disabilitati!';
$_lang['ENABLE'] = 'Abilita';
$_lang['ACTIVE'] = 'Attivo';
$_lang['URL_ID'] = 'Identificativo URL';
$_lang['MAN_MULTISITES_ONLY'] = "Puoi gestire siti multipli solo dal sito %s";
$_lang['LOWER_ALPHANUM'] = 'Caratteri alfanumerici minuscoli senza spazi';
$_lang['IMPORT_DATA'] = 'Importazione dati';
$_lang['CNOT_CREATE_CFG_NEW'] = "Per il nuovo sito non devi creare il file di configurazione %s!";
$_lang['DATA_IMPORT_FAILED'] = 'Importazione dati non riuscita!';
$_lang['DATA_IMPORT_SUC'] = 'Dati importati con successo!';
$_lang['ADD_RULES_HTACCESS'] = 'Aggiungi i seguenti ruoli nel file htaccess';
$_lang['CREATE_REPOSITORY_NOTE'] = 'È fortemente consigliato di creare una cartella repositary separata per ogni sotto-sito';
$_lang['NOT_SUP_DBTYPE'] = 'Tipo Database non supportato!';
$_lang['DBTYPES_MUST_SAME'] = 'I tipi di Database di questo sito e di quelli nuovi devono essere identici!';
$_lang['DISABLE_MULTISITES'] = 'Disabilita siti multipli';
$_lang['DISABLE_MULTISITES_WARN'] = 'Tutti i siti ad eccezione di quello con id 1 saranno rimossi!';
$_lang['VISITS_PER_DAY'] = "Visite al giorno nel %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Clicks al giorno nel %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Visite al mese nel %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Clicks al mese nel %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Percentuale lingue usate nel %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Visite uniche';
$_lang['PAGE_VIEWS'] = 'Visite pagina';
$_lang['TOTAL_VISITS'] = 'Visite totali';
$_lang['TOTAL_PAGE_VIEWS'] = 'Pagine viste';
$_lang['LANGS_USAGE'] = 'Lingue usate';
$_lang['LEGEND'] = 'Leggenda';
$_lang['USAGE'] = 'Utilizzo';
$_lang['VIEWS'] = 'Visualizzazioni';
$_lang['OTHER'] = 'Altro';
$_lang['NO_DATA_AVAIL'] = 'Non sono disponibili dati';
$_lang['PERIOD'] = 'Periodo';
$_lang['YEAR_STATS'] = 'Statistiche anno';
$_lang['MONTH_STATS'] = 'Statistiche mese';
$_lang['PREVIOUS_YEAR'] = 'Anno precedente';
$_lang['NEXT_YEAR'] = 'Anno successivo';
$_lang['STATS_COL_DISABLED'] = 'La raccolta di dati statistici è disabilitata! Attivare le statistiche nella configurazione Elxis.';
$_lang['DOCTYPE'] = 'Tipo documento';
$_lang['DOCTYPE_INFO'] = 'L\'opzione consigliata è HTML5. Anche se si imposta il DOCTYPE di HTML5, Elxis genera output XHTML. 
	Su XHTML doctypes Elxis serve documenti con l application/xhtml + xml mime type su browser moderni e con testo/html su quelle più vecchie.';
$_lang['ABR_SECONDS'] = 'sec';
$_lang['ABR_MINUTES'] = 'min';
$_lang['HOUR'] = 'ora';
$_lang['HOURS'] = 'ore';
$_lang['DAY'] = 'giorno';
$_lang['DAYS'] = 'giorni';
$_lang['UPDATED_BEFORE'] = 'Aggiornato prima';
$_lang['CACHE_INFO'] = 'Mostra ed elimina gli elementi salvati nella cache.';
$_lang['ELXISDC'] = 'Elxis Downloads Center';
$_lang['ELXISDC_INFO'] = 'Sfoglia in diretta EDC per le estensioni disponibili';
$_lang['SITE_LANGS'] = 'Lingue del sito';
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