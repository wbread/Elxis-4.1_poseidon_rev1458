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


$_lang = array();
$_lang['INSTALLATION'] = 'Installazione';
$_lang['STEP'] = 'Step';
$_lang['VERSION'] = 'Versione';
$_lang['VERSION_CHECK'] = 'Controllo versione';
$_lang['STATUS'] = 'Stato';
$_lang['REVISION_NUMBER'] = 'Numero di revisione';
$_lang['RELEASE_DATE'] = 'Data di realizzazione';
$_lang['ELXIS_INSTALL'] = 'Installazione Elxis';
$_lang['LICENSE'] = 'Licenza';
$_lang['VERSION_PROLOGUE'] = 'Si sta per installare Elxis CMS. L\'esatta versione della copia di Elxis
che si sta per installare è mostrata sotto. Assicurarsi che questa sia l\'ultima versione di Elxis rilasciata
su <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Prima di iniziare';
$_lang['BEFORE_DESC'] = 'Prima di procedere ulteriormente per favore leggere attentamente le seguenti istruzioni.';
$_lang['DATABASE'] = 'Database';
$_lang['DATABASE_DESC'] = 'Creare un database vuoto che verrà utilizzato per memorizzare i dati di Elxis. Si consiglia vivamente di utilizzare un database <strong>MySQL</strong>. Sebbene Elxis ha il supporto di back-end per altri tipi di database come PostgreSQL e SQLite 3, il prodotto è ben testato solo con MySQL. Potete creare un database MySQL direttamente dal pannello di controllo fornito dal vostro hosting (CPanel, Plesk, ISP Config, ecc.) o da phpMyAdmin come con altri strumenti per la gestione di database. Basta fornire un <strong>nome</strong> al database e crearlo. Dopo di che, creare un <strong>utente</strong> con relativa <strong>password</strong> e assegnarlo al database appena creato. Scrivere da qualche parte il nome del database, il nome utente e password. Questi dati saranno richiesti durante l\'installazione.';
$_lang['REPOSITORY'] = 'Repository';
$_lang['REPOSITORY_DESC'] = 'Elxis utilizza una speciale cartella per memorizzare le pagine nella cache, i file di log, le sessioni, backup e altro. La cartella di default
si chiamata <strong>repository</strong> ed è all\'interno della cartella principale (root) di Elxis. Questa cartella <strong>deve essere scrivibile</strong>! Si consiglia di <strong>rinominare</strong> questa cartella e di <strong>spostarla</strong> in un\'area non raggiungibile via web. Dopo aver spostato questa cartella e se hai abilitato nel PHP la protezione <strong>open basedir</strong>, potrebbe essere necessario includere il percorso (path) di repository nei percorsi consentiti.';
$_lang['REPOSITORY_DEFAULT'] = 'Repository è nella posizione predefinita!';
$_lang['SAMPLE_ELXPATH'] = 'Percorso (path) di esempio Elxis';
$_lang['DEF_REPOPATH'] = 'Percorso (path) predefinito repository';
$_lang['REQ_REPOPATH'] = 'Percorso (path) repository raccomandato';
$_lang['CONTINUE'] = 'Continua';
$_lang['I_AGREE_TERMS'] = 'Ho letto, capito e accettato i termini e le condizioni EPL.';
$_lang['LICENSE_NOTES'] = 'Elxis CMS è un software libero rilasciato sotto <strong>licenza pubblica di Elxis</strong> (EPL).
Per continuare l\'installazione e utilizzare Elxis è necessario accettare i termini e le condizioni di EPL. Leggere attentamente la licenza Elxis e se siete d\'accordo barrare la casella nella parte inferiore della pagina e cliccare su continuare. Se così non fosse,
interrompere l\'installazione ed eliminare i file Elxis.';
$_lang['SETTINGS'] = 'Impostazioni';
$_lang['SITE_URL'] = 'URL del sito';
$_lang['SITE_URL_DESC'] = 'Senza barra finale (eg. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Il percorso (path) assoluto della cartella repository di Elxis. Lasciare vuoto per usare il nome e il percorso predefinito da Elxis.';
$_lang['SETTINGS_DESC'] = 'Impostare i parametri di configurazione richiesti Elxis. Alcuni parametri devono essere impostate prima di installare Elxis. Quando l\'installazaione è terminata, effettuare il login nella console di amministrazione e configurare i rimanenti parametri. Questo dovrebbe essere il vostro primo compito come amministratore..';
$_lang['DEF_LANG'] = 'Lingua di default';
$_lang['DEFLANG_DESC'] = 'Il contenuto è scritto nella lingua predefinita. I contenuti in altre lingue sono traduzioni del testo originale della lingua di default.';
$_lang['ENCRYPT_METHOD'] = 'Metodo di crittografia';
$_lang['ENCRYPT_KEY'] = 'Chiave crittografata';
$_lang['AUTOMATIC'] = 'Automatico';
$_lang['GEN_OTHER'] = 'Genera altra chiave';
$_lang['SITENAME'] = 'Nome sito';
$_lang['TYPE'] = 'Tipo';
$_lang['DBTYPE_DESC'] = ' 	  	
Consigliamo fortemente MySQL. Quelli selezionabili sono solo i driver supportati dal vostro sistema e da Elxis installer.';
$_lang['HOST'] = 'Host';
$_lang['TABLES_PREFIX'] = 'Prefisso delle tabelle';
$_lang['DSN_DESC'] = 'È invece possibile fornire Nome della fonte dati pronta all\'uso per la connessione al database.';
$_lang['SCHEME'] = 'Schema';
$_lang['SCHEME_DESC'] = 'Il percorso (path) assoluto al file del database se usi database come SQLite.';
$_lang['PORT'] = 'Porta';
$_lang['PORT_DESC'] = 'La porta di default MySQL è 3306. Lascia 0 per la selezione automatica.';
$_lang['FTPPORT_DESC'] = 'La porta di default FTP è 21. Lascia 0 per la selezione automatica.';
$_lang['USE_FTP'] = 'Usare FTP';
$_lang['PATH'] = 'Percorso (Path)';
$_lang['FTP_PATH_INFO'] = 'Il percorso (path) relativo dalla cartella principale FTP a quella dove è stato installato Elxis (esempio: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Controllo impostazioni FTP';
$_lang['CHECK_DB_SETS'] = 'Controllo impostazioni Database';
$_lang['DATA_IMPORT'] = 'Importazione dati';
$_lang['SETTINGS_ERRORS'] = 'Le impostazioni contengono degli errori!';
$_lang['NO_QUERIES_WARN'] = 'Dati iniziali importati nel database, ma sembra che nessuna query ssia stata eseguita. Assicurarsi che i dati sono stati effettivamente importati prima di continuare.';
$_lang['RETRY_PREV_STEP'] = 'Ripetere lo step precedente';
$_lang['INIT_DATA_IMPORTED'] = 'Dati iniziali importati nel database.';
$_lang['QUERIES_EXEC'] = "%s queries SQL eseguite."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Account Amministrativo';
$_lang['CONFIRM_PASS'] = 'Conferma password';
$_lang['AVOID_COMUNAMES'] = 'Evitare nomi comuni come admin e amministratore.';
$_lang['YOUR_DETAILS'] = 'Tuoi dettagli';
$_lang['PASS_NOMATCH'] = 'Le password non corrispondono!';
$_lang['REPOPATH_NOEX'] = 'Il percorso (path) Repository non esiste!';
$_lang['FINISH'] = 'Finito';
$_lang['FRIENDLY_URLS'] = 'URLs amichevoli';
$_lang['FRIENDLY_URLS_DESC'] = 'Si consiglia di abilitarle. Per poter lavorare, Elxis proverà a rinominare il file htaccess in
<strong>.htaccess</strong>. Se nella stessa cartella esiste già un altro file .htaccess, esso sarà cancellato.';
$_lang['GENERAL'] = 'Generale';
$_lang['ELXIS_INST_SUCC'] = 'Installazione di Elxis completata con successo.';
$_lang['ELXIS_INST_WARN'] = 'Installazione di Elxis completata con errori.';
$_lang['CNOT_CREA_CONFIG'] = 'Non è possibile creare il file <strong>configuration.php</strong> nella cartella principale (root folder) di Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Non è possibile rinominare il file <strong>htaccess.txt</strong> in <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = 'File di configurazione';
$_lang['CONFIG_FILE_MANUAL'] = 'Puoi creare manualmente il fileconfiguration.php, copiando il seguente codice e copiarlo all\'interno di esso.';
$_lang['REN_HTACCESS_MANUAL'] = 'Per favore rinomina manualmente il file <strong>htaccess.txt</strong> in <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Cosa fare dopo?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Per migliorare la sicurezza, è possibile rinominare la cartella di amministrazione (<em> estia </em>) come desiderate.
In questo caso è necessario aggiornare anche il file .htaccess con il nuovo nome da voi scelto.';
$_lang['LOGIN_CONFIG'] = 'Login sezione Amministrativa per impostare correttamente le restanti opzioni di configurazione.';
$_lang['VISIT_NEW_SITE'] = 'Visita il tuo nuovo sito web';
$_lang['VISIT_ELXIS_SUP'] = 'Visita il sito di supporto Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Grazie per usareElxis CMS.';

?>