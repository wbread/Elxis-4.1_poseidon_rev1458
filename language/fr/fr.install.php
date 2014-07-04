<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: fr-FR (French - Canada France) language for module Search
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Francis Dionne Canada ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['INSTALLATION'] = 'Installation';
$_lang['STEP'] = 'Étape';
$_lang['VERSION'] = 'Version';
$_lang['VERSION_CHECK'] = 'Version vérification';
$_lang['STATUS'] = 'Statut';
$_lang['REVISION_NUMBER'] = 'Revision numéro';
$_lang['RELEASE_DATE'] = 'Date de libération';
$_lang['ELXIS_INSTALL'] = 'Elxis installation';
$_lang['LICENSE'] = 'Licence';
$_lang['VERSION_PROLOGUE'] = 'Vous êtes sur le point d`installer Elxis CMS. La version exacte de la copie Elxis
vous vous apprêtez à installer est indiqué ci-dessous. S`il vous plaît assurez-vous que celle-ci est la dernière version de Elxis publié
sur <a href="http://www.elxis.org" target="_blank">elxis.org</a>.';
$_lang['BEFORE_BEGIN'] = 'Avant que vous débutiez';
$_lang['BEFORE_DESC'] = 'Avant d`aller plus loin, S.V.P. lire attentivement ce qui suit:';
$_lang['DATABASE'] = 'Base de données';
$_lang['DATABASE_DESC'] = 'Créer une base de données vide, qui sera utilisé par Elxis pour stocker vos données. Nous
recommandons fortement d`utiliser une Base de données <strong>MySQL</strong>. Bien que Elxis soutien backend pour
tous les types de bases de données tel que PostgreSQL et SQLite 3, Elxis a été testé uniquement avec MySQL. Pour créer une
base de données MySQL vide le faire à partir de votre panneau de contrôle d`hébergement (CPanel, Plesk, fournisseur d`accès Internet Config, etc) ou par
phpMyAdmin ainsi que d`autres outils de gestions de bases de données. Vous n`avez qu`à fournir un <strong>nom</strong> pour créer cette base de donnée. 
Après cela, créer la base de donnée <strong>utilisateurs</strong> et l`affecter à la base de données nouvellement créée. Prenez en note le
du nom de la base de données, le nom d`utilisateur et le mot de passe, vous en aurez besoin plus tard lors de l`installation.';
$_lang['REPOSITORY'] = 'Répertoire';
$_lang['REPOSITORY_DESC'] = 'Elxis utilise un dossier spécial pour stocker les pages mises en cache, les fichiers journaux, les sessions, les sauvegardes et plus encore. Par défaut ce dossier est nommé <strong>repository</strong> celui-ci est placé à l`intérieur du dossier racine Elxis. Ce dossier <strong>doit-être accessible en écriture</strong>! Nous recommandons fortement de <strong>renommer</strong> ce dossier et qu`il soit <strong>déplacer</strong> dans un endroit non accessible depuis le web. Après l`avoir déplacer, et si vous avez activé <strong>open basedir</strong> protection en PHP vous pourriez aussi avoir besoin d`inclure le chemin du référentiel dans les chemins autorisés.';
$_lang['REPOSITORY_DEFAULT'] = 'Répertoire emplacement par défaut!';
$_lang['SAMPLE_ELXPATH'] = 'Exemple de chemin Elxis';
$_lang['DEF_REPOPATH'] = 'Défaut chemin du répertoire';
$_lang['REQ_REPOPATH'] = 'Chemin de répertoire recommander';
$_lang['CONTINUE'] = 'Continuer';
$_lang['I_AGREE_TERMS'] = 'J`ai lu, compris et accepté les termes et les conditions de protection de l`emploi';
$_lang['LICENSE_NOTES'] = 'Elxis CMS est un logiciel libre publié sous <strong>Elxis Public License</strong> (EPL). 
Pour continuer l`utilisation de cette installation d`Elxis, vous devez accepter les termes et conditions de la LPE. Lisez-le attentivement
la licence Elxis et si vous acceptez, cocher la case en bas de la page et cliquer sur Continuer. Dans le cas contraire,
arrêter cette installation et supprimer les fichiers Elxis.';
$_lang['SETTINGS'] = 'Réglages';
$_lang['SITE_URL'] = 'Site URL';
$_lang['SITE_URL_DESC'] = 'Sans couper (eg. http://www.example.com)';
$_lang['REPOPATH_DESC'] = 'Le chemin absolue aux dossiers répertoires d`Elxis. Laisser vide pour le chemin par défaut et le nom.';
$_lang['SETTINGS_DESC'] = 'Régler les paramètres requis de configuration Elxis. Certains paramètres doivent-être fait avant les étapes d`installation Elxis. Une fois l`installation terminée, rendez-vous au journal dans la console d administration afin de configurer les paramètres restants.
Ceci devrait-être votre première tâche comme administrateur.';
$_lang['DEF_LANG'] = 'Langue par Défaut';
$_lang['DEFLANG_DESC'] = 'Le contenu est rédigé dans la langue par défaut. Le contenu dans d`autres langues est fait en traduisant le
contenu original de la langue par défaut.';
$_lang['ENCRYPT_METHOD'] = 'Méthode deChiffrement';
$_lang['ENCRYPT_KEY'] = 'Clé de Chiffrement';
$_lang['AUTOMATIC'] = 'Automatique';
$_lang['GEN_OTHER'] = 'En générer une autre';
$_lang['SITENAME'] = 'Nom du Ste';
$_lang['TYPE'] = 'Type';
$_lang['DBTYPE_DESC'] = 'Nous recommandons fortement MySQL. Sélectionner que les pilotes pris en charge par votre système d`installation Elxis.';
$_lang['HOST'] = 'Hôte';
$_lang['TABLES_PREFIX'] = 'Tables prefix';
$_lang['DSN_DESC'] = 'Vous pouvez à la place fournir un nom prêt à être employé à la base de données pour se connecter à la base.';
$_lang['SCHEME'] = 'Programme';
$_lang['SCHEME_DESC'] = 'Chemin absolu à une base de données si vous employez une base de données telle que SQLite.';
$_lang['PORT'] = 'Port';
$_lang['PORT_DESC'] = 'Le port par défaut pour MySQL est 3306. Laisser à 0 pour une auto sélection.';
$_lang['FTPPORT_DESC'] = 'Le port par défaut pour FTP est 21. Laisser à 0 pour une auto sélection.';
$_lang['USE_FTP'] = 'Utiliser FTP';
$_lang['PATH'] = 'Chemin';
$_lang['FTP_PATH_INFO'] = 'Chemin relatif depuis le dossier racine FTP dans le dossier d`installation Elxis (exemple: /public_html).';
$_lang['CHECK_FTP_SETS'] = 'Vérifier réglages FTP';
$_lang['CHECK_DB_SETS'] = 'Vérifier réglages base de donnée';
$_lang['DATA_IMPORT'] = 'Data importation';
$_lang['SETTINGS_ERRORS'] = 'Les paramètres que vous avez données contiennent des erreurs!';
$_lang['NO_QUERIES_WARN'] = 'Données initialement importées dans la base de données, il semble qu`aucune n`ont été exécutées. Assurez-vous que
les données ont bien été importés avant de poursuivre.';
$_lang['RETRY_PREV_STEP'] = 'Refaire l`étape précédente';
$_lang['INIT_DATA_IMPORTED'] = 'Les données initiales importées dans la base de données';
$_lang['QUERIES_EXEC'] = "%s requêtes SQL exécutées."; //translators help: {NUMBER} SQL queries executed
$_lang['ADMIN_ACCOUNT'] = 'Compte administrateur';
$_lang['CONFIRM_PASS'] = 'Confirmer mot de passe';
$_lang['AVOID_COMUNAMES'] = 'Éviter les noms d`utilisateurs commun comme admin ou administrateur.';
$_lang['YOUR_DETAILS'] = 'Vos détails';
$_lang['PASS_NOMATCH'] = 'Les mots de passe ne correspondent pas';
$_lang['REPOPATH_NOEX'] = 'Le chemin du répertoire n`exite pas!';
$_lang['FINISH'] = 'Terminer';
$_lang['FRIENDLY_URLS'] = 'Simple URL';
$_lang['FRIENDLY_URLS_DESC'] = 'Nous vous recommandons fortement de l`activer afin que cela fonctionne bien. Elxis va essayer de renommer htaccess.txt fichier dans
<strong>.htaccess</strong> . S`il y a déjà un autre fichier .Htaccess dans le même dossier, il sera supprimé.';
$_lang['GENERAL'] = 'Général';
$_lang['ELXIS_INST_SUCC'] = 'Installation Elxis complété avec succès.';
$_lang['ELXIS_INST_WARN'] = 'Installation Elxis complété avec certains avertissements.';
$_lang['CNOT_CREA_CONFIG'] = 'Impossible de créer le fichier <strong>configuration.php</strong> dans le dossier racine Elxis.';
$_lang['CNOT_REN_HTACC'] = 'Impossible de renommer le fichier <strong>htaccess.txt</strong> à <strong>.htaccess</strong>';
$_lang['CONFIG_FILE'] = ' Fichier configuration';
$_lang['CONFIG_FILE_MANUAL'] = 'Créer manuellement le fichier de configuration.php, et copier le code suivant et coller à l`intérieur.';
$_lang['REN_HTACCESS_MANUAL'] = 'S.V.P. renommer manuellement le fichier <strong>htaccess.txt</strong> à <strong>.htaccess</strong>';
$_lang['WHAT_TODO'] = 'Que faire ensuite?';
$_lang['RENAME_ADMIN_FOLDER'] = 'Pour renforcir la sécurité, vous pouvez renommer le dossier d`administration (<em>estia</em>) pour le nom que vous souhaitez.
Si vous choisissez de le faites, vous devez également mettre à jour le fichier .Htaccess avec le nouveau nom attribué.';
$_lang['LOGIN_CONFIG'] = 'Connectez-vous à la zone administration et réglé correctement les options de configuration.';
$_lang['VISIT_NEW_SITE'] = 'Visiter votre nouveau site web';
$_lang['VISIT_ELXIS_SUP'] = 'Visiter le site de support Elxis';
$_lang['THANKS_USING_ELXIS'] = 'Merci d`utiliser Elxis CMS.';

?>