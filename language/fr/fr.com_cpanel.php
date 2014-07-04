<?php 
/**
* @version: 4.1
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2013 Elxis.org. All rights reserved.
* @description: fr-FR (French - Canada France) language for module Search
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Francis Dionne Canada ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$_lang = array();
$_lang['CONTROL_PANEL'] = 'Panneau de Control';
$_lang['GENERAL_SITE_SETS'] = 'Réglage général du site web';
$_lang['LANGS_MANAGER'] = 'Gestion de langue';
$_lang['MANAGE_SITE_LANGS'] = 'Gestion de langue du site';
$_lang['USERS'] = 'Utilisateurs';
$_lang['MANAGE_USERS'] = 'Créer, éditer et supprimer un compte utilisateur';
$_lang['USER_GROUPS'] = 'Groupe utilisateur';
$_lang['MANAGE_UGROUPS'] = 'Gestion des groupes utilisateurs';
$_lang['MEDIA_MANAGER'] = 'Gestion des médias';
$_lang['MEDIA_MANAGER_INFO'] = 'Gestion des fichiers multi-media';
$_lang['ACCESS_MANAGER'] = 'Gestion des accès';
$_lang['MANAGE_ACL'] = 'Gestion de liste de contrôle d`accès';
$_lang['MENU_MANAGER'] = 'Gestion de menu';
$_lang['MANAGE_MENUS_ITEMS'] = 'Gestion de menu et élément de menu';
$_lang['FRONTPAGE'] = 'Première page';
$_lang['DESIGN_FRONTPAGE'] = 'Design du site en première page';
$_lang['CATEGORIES_MANAGER'] = 'Gérer les catégories';
$_lang['MANAGE_CONT_CATS'] = 'Gérer le contenu des catégories';
$_lang['CONTENT_MANAGER'] = 'Gérer le Contenu';
$_lang['MANAGE_CONT_ITEMS'] = 'Gérer les éléments de contenu';
$_lang['MODULES_MANAGE_INST'] = 'Gérer les modules et en installer de nouveaux.';
$_lang['PLUGINS_MANAGE_INST'] = 'Gestion des plugins et en installer de nouveaux.';
$_lang['COMPONENTS_MANAGE_INST'] = 'Gérer les composants et en installer de nouveaux.';
$_lang['TEMPLATES_MANAGE_INST'] = 'Gérer les modèles et en installer de nouveaux.';
$_lang['SENGINES_MANAGE_INST'] = 'Gérer les moteurs de recherche et en installer de nouveaux.';
$_lang['MANAGE_WAY_LOGIN'] = 'Gérer la façons dont les utilisateurs peuvent ouvrir une session dans le site.';
$_lang['TRANSLATOR'] = 'Traduction';
$_lang['MANAGE_MLANG_CONTENT'] = 'Gérer le contenu multilingue';
$_lang['LOGS'] = 'connexions';
$_lang['VIEW_MANAGE_LOGS'] = 'Afficher et gérer les fichiers journaux';
$_lang['GENERAL'] = 'Général';
$_lang['WEBSITE_STATUS'] = 'Site web statut';
$_lang['ONLINE'] = 'En ligne';
$_lang['OFFLINE'] = 'Hors Ligne';
$_lang['ONLINE_ADMINS'] = 'En ligne seulement pour les administrateurs';
$_lang['OFFLINE_MSG'] = 'Hors ligne message';
$_lang['OFFLINE_MSG_INFO'] = 'Laisser ce champ vide pour afficher un message automatique multilingue';
$_lang['SITENAME'] = 'Nom du site';
$_lang['URL_ADDRESS'] = 'URL adresse';
$_lang['REPO_PATH'] = 'Chemin du répositoire';
$_lang['REPO_PATH_INFO'] = 'Le chemin d`accès complet au dossier du référentiel Elxis. Laissez-le vide pour la valeur d`emplacement par défaut
(elxis_root/repository/). Nous vous recommandons vivement de placer ce dossier au dessus du dossier WWW
et le renommer en un nom difficile à deviner!';
$_lang['FRIENDLY_URLS'] = 'Simple URL';
$_lang['SEF_INFO'] = 'Si défini à OUI (recommandé) renommer le fichier htaccess.txt à .htaccess';
$_lang['STATISTICS_INFO'] = 'Activer la collecte de statistiques sur le trafic du site?';
$_lang['GZIP_COMPRESSION'] = 'GZip compression';
$_lang['GZIP_COMPRESSION_DESC'] = 'Elxis va compresser le document avec GZIP avant de l`envoyer au navigateur et ainsi vous faire économiser de 70% à 80% de bande passante.';
$_lang['DEFAULT_ROUTE'] = 'Défaut route';
$_lang['DEFAULT_ROUTE_INFO'] = 'Un URI au format Elxis sera utilisée comme première page du site\'s';
$_lang['META_DATA'] = 'META données';
$_lang['META_DATA_INFO'] = 'Une courte description pour le site web';
$_lang['KEYWORDS'] = 'Mots clés';
$_lang['KEYWORDS_INFO'] = 'Quelques mots clés séparés par des virgules';
$_lang['STYLE_LAYOUT'] = 'Style et disposition';
$_lang['SITE_TEMPLATE'] = 'Site model';
$_lang['ADMIN_TEMPLATE'] = 'Administration des thèmes';
$_lang['ICONS_PACK'] = 'Pack d`icones';
$_lang['LOCALE'] = 'Local';
$_lang['TIMEZONE'] = 'Fuseau horaire';
$_lang['MULTILINGUISM'] = 'Multilinguisme';
$_lang['MULTILINGUISM_INFO'] = 'Vous permet de saisir des éléments de texte dans plus d`une langue (translations). 
	Don\'t activer si vous don\'t l`utiliser il va ralentir le site sans raison. Elxis d`interface
sera toujours multilingue, même si cette option est définie sur Non';
$_lang['CHANGE_LANG'] = 'Changer de langue';
$_lang['LANG_CHANGE_WARN'] = 'Si vous changez la langue par défaut il pourrait y avoir des incohérences
entre les indicateurs de la langue et les traductions dans le tableau de traduction.';
$_lang['CACHE'] = 'Cache';
$_lang['CACHE_INFO'] = 'Elxis peut enregistrer le code HTML généré par des éléments individuels dans le cache pour accélérer plus tard la re-génération.
Il s`agit d un cadre général, vous devez également activer le cache sur les éléments (modules, par exemple) que vous souhaitez mettre en cache.';
$_lang['APC_INFO'] = 'Alternative PHP Cache (APC) est un cache opcode pour PHP. Elle doit être supportée par votre serveur web.
Il n`est pas recommandé sur les environnements d`hébergement partagés. Elxis va l`utiliser sur des pages spéciales pour améliorer la performace du site.';
$_lang['APC_ID_INFO'] = 'Dans le cas où plus de 1 sites est hébergés sur le même serveur l`identifier en
leur fournissant une entrée unique pour ce site.';
$_lang['USERS_AND_REGISTRATION'] = 'Utilisateur et enregistrement';
$_lang['PRIVACY_PROTECTION'] = 'Protection privée';
$_lang['PASSWORD_NOT_SHOWN'] = 'Le mot de passe actuel n`est pas afficher pour des raisons de sécurité.
Remplisser ce champ uniquement si vous souhaitez modifier le mot de passe actuel.';
$_lang['DB_TYPE'] = 'Type de base de donnée';
$_lang['ALERT_CON_LOST'] = 'En cas de modification, la connexion à la base de données actuelle sera perdus!';
$_lang['HOST'] = 'Hôte';
$_lang['PORT'] = 'Port';
$_lang['PERSISTENT_CON'] = 'Connexion persistente';
$_lang['DB_NAME'] = 'DB Nom';
$_lang['TABLES_PREFIX'] = 'Tables prefix';
$_lang['DSN_INFO'] = 'Un prêt-à-utiliser des donnée source peut-être utilisés pour la connexion à la base de données.';
$_lang['SCHEME'] = 'Schéma';
$_lang['SCHEME_INFO'] = 'Info sur le chemin absolu vers un fichier de données si vous utilisez une base de données tels que SQLite.';
$_lang['SEND_METHOD'] = 'Envoyer la méthode';
$_lang['SMTP_OPTIONS'] = 'SMTP options';
$_lang['AUTH_REQ'] = 'Authentification requise';
$_lang['SECURE_CON'] = 'Connexion sécuritaire';
$_lang['SENDER_NAME'] = 'nom de l`expéditeur';
$_lang['SENDER_EMAIL'] = 'Expéditeur courriel';
$_lang['RCPT_NAME'] = 'Nom du destinataire';
$_lang['RCPT_EMAIL'] = 'Courriel du destinataire';
$_lang['TECHNICAL_MANAGER'] = 'Responsable technique';
$_lang['TECHNICAL_MANAGER_INFO'] = 'Le directeur technique reçoit les alertes d`erreurs à la sécurité.';
$_lang['USE_FTP'] = 'Utiliser FTP';
$_lang['PATH'] = 'Chemin';
$_lang['FTP_PATH_INFO'] = 'Le chemin relatif depuis le dossier racine FTP dans le dossier d`installation Elxis (exemple: /public_html).';
$_lang['SESSION'] = 'Session';
$_lang['HANDLER'] = 'Maître';
$_lang['HANDLER_INFO'] = 'Elxis peut enregistrer des sessions sous forme de fichiers dans le référentiel ou dans la base de données.
Vous pouvez également choisir de en pas enregistrer les sessions PHP dans le serveur\'s de location par défaut.';
$_lang['FILES'] = 'Fichiers';
$_lang['LIFETIME'] = 'Existence';
$_lang['SESS_LIFETIME_INFO'] = 'Temps jusqu`à ce que la session expire lorsque vous êtes au repos.';
$_lang['CACHE_TIME_INFO'] = 'Passé ce délai les éléments mis en cache se re-génère.';
$_lang['MINUTES'] = 'minutes';
$_lang['HOURS'] = 'heures';
$_lang['MATCH_IP'] = 'IP Correspondant';
$_lang['MATCH_BROWSER'] = 'Navigateur correspondant';
$_lang['MATCH_REFERER'] = 'Correspondant HTTP Référent';
$_lang['MATCH_SESS_INFO'] = 'Permet une validation routinière de session avancée.';
$_lang['ENCRYPTION'] = 'Chiffrement';
$_lang['ENCRYPT_SESS_INFO'] = 'Session chiffrée de données?';
$_lang['ERRORS'] = 'Erreurs';
$_lang['WARNINGS'] = 'Avertissement';
$_lang['NOTICES'] = 'Notices';
$_lang['NOTICE'] = 'Notice';
$_lang['REPORT'] = 'Rapport';
$_lang['REPORT_INFO'] = 'Signaler une erreur de niveau. Sur les sites de production, nous vous conseillons de le mettre sur off.';
$_lang['LOG'] = 'Connexion';
$_lang['LOG_INFO'] = 'Niveau de journalisation d`erreur. Sélectionner les erreurs que vous souhaitez les écrires dans le système d`
enregistrement Elxis (repository/logs/).';
$_lang['ALERT'] = 'Alerte';
$_lang['ALERT_INFO'] = 'Courriel d`erreur fatale du site\'s au directeur technique.';
$_lang['ROTATE'] = 'Rotation';
$_lang['ROTATE_INFO'] = 'Rotation des journaux d`erreurs à la fin de chaque mois. Recommandé.';
$_lang['DEBUG'] = 'Debug';
$_lang['MODULE_POS'] = 'Module positions';
$_lang['MINIMAL'] = 'Minimal';
$_lang['FULL'] = 'Plein';
$_lang['DISPUSERS_AS'] = 'Afficher les utilisateurs comme';
$_lang['USERS_REGISTRATION'] = 'Utilisateur enregistrement';
$_lang['ALLOWED_DOMAIN'] = 'Domaine autorisé';
$_lang['ALLOWED_DOMAIN_INFO'] = 'Rédiger un nom de domaine (par exemple elxis.org) seulement pour que le système
accepte d`inscrire l`adresse courriel.';
$_lang['EXCLUDED_DOMAINS'] = 'Domaines exclus';
$_lang['EXCLUDED_DOMAINS_INFO'] = 'Liste séparée par virgules des noms de domaine (c.-à-dire badsite.com, hacksite.com)
à partir de laquelle les adresses électroniques ne sont pas acceptables lors de l`inscription.';
$_lang['ACCOUNT_ACTIVATION'] = 'Activation de compte';
$_lang['DIRECT'] = 'Direct';
$_lang['MANUAL_BY_ADMIN'] = 'Manuelle par l`administrateur';
$_lang['PASS_RECOVERY'] = 'Récupération du mot de passe';
$_lang['SECURITY'] = 'Sécurité';
$_lang['SECURITY_LEVEL'] = 'Sécurité niveau';
$_lang['SECURITY_LEVEL_INFO'] = 'En augmentant le niveau de sécurité, certaines options sont activées par la force.
En contre partie certaines fonctionnalités peuvent être désactivées. Consultez la documentation Elxis pour plus d`information.';
$_lang['NORMAL'] = 'Normal';
$_lang['HIGH'] = 'Haute';
$_lang['INSANE'] = 'Dément';
$_lang['ENCRYPT_METHOD'] = 'Méthode de chiffrement';
$_lang['AUTOMATIC'] = 'Automatique';
$_lang['ENCRYPTION_KEY'] = 'Clé de chiffrement';
$_lang['ELXIS_DEFENDER'] = 'Elxis défendeur';
$_lang['ELXIS_DEFENDER_INFO'] = 'Elxis Defendeur protège votre site web à partir de XSS contre les attaques par injection SQL.
Cette demande d`outils puissants par les utilisateur filtre et bloque les attaques à votre site. Il vous informera également des
attaques et les enregistre. Vous pouvez sélectionner le type de filtres à appliquer ou même verrouiller votre système pour\'s
les dossiers cruciaux contre les modifications non autorisées. Les filtres vous permettent en plus de ralentir votre site lorsque exécuté.
Nous recommandons d activer les options G, C et F. Consulter la documentation Elxis pour plus d information.';
$_lang['SSL_SWITCH'] = 'SSL switch';
$_lang['SSL_SWITCH_INFO'] = 'Elxis passera automatiquement de HTTP à HTTPS dans les pages où l`intimité est importante.
Pour l espace d administration du schéma HTTPS sera permanente. Nécessite un certificat SSL!';
$_lang['PUBLIC_AREA'] = 'Zone publique';
$_lang['GENERAL_FILTERS'] = 'Rôle Général';
$_lang['CUSTOM_FILTERS'] = 'Rôle personnalisé';
$_lang['FSYS_PROTECTION'] = 'Fichier système protection';
$_lang['CHECK_FTP_SETS'] = 'Vérification des réglages FTP';
$_lang['FTP_CON_SUCCESS'] = 'La connexion au serveur FTP est réussie.';
$_lang['ELXIS_FOUND_FTP'] = 'Installation de Elxis a été trouvé sur FTP.';
$_lang['ELXIS_NOT_FOUND_FTP'] = 'L`installation de Elxis n`a pas été trouvé sur le FTP! Vérifier la valeur de l`option du chemin d`accès FTP.';
$_lang['CAN_NOT_CHANGE'] = 'Vous ne pouvez pas le changer.';
$_lang['SETS_SAVED_SUCC'] = 'Paramètres sauvegardés avec succès';
$_lang['ACTIONS'] = 'Actions';
$_lang['BAN_IP_REQ_DEF'] = 'Pour interdire une adresse IP, il est nécessaire de permettre au moins une option dans Elxis défenseur!';
$_lang['BAN_YOURSELF'] = 'Vous essayez de vous l`interdire?';
$_lang['IP_AL_BANNED'] = 'Cette adresse IP est déjà interdite!';
$_lang['IP_BANNED'] = 'Adresse IP %s interdit!';
$_lang['BAN_FAILED_NOWRITE'] = 'Interdiction a échoué! Référentiel de fichiers /logs/ defender_ban.php n`est pas accessible en écriture.';
$_lang['ONLY_ADMINS_ACTION'] = 'Seul l`administrateur peut faire cette action!';
$_lang['CNOT_LOGOUT_ADMIN'] = 'Vous ne pouvez pas vous connecter à l`administration!';
$_lang['USER_LOGGED_OUT'] = 'L`utilisateur a été déconnecté!';
$_lang['SITE_STATISTICS'] = 'Site statistique';
$_lang['SITE_STATISTICS_INFO'] = 'Voir les statistiques de fréquentation du site';
$_lang['BACKUP'] = 'Backup';
$_lang['BACKUP_INFO'] = 'Effectuer une sauvegarde complète du nouveau site et gérer celles qui existent déjà.';
$_lang['BACKUP_FLIST'] = 'Sauvegarde la liste des fichiers exitants';
$_lang['TYPE'] = 'Type';
$_lang['FILENAME'] = 'Nom fichier';
$_lang['SIZE'] = 'Taille';
$_lang['NEW_DB_BACKUP'] = 'Nouvelle sauvegarde de la base';
$_lang['NEW_FS_BACKUP'] = 'Nouvelle sauvegarde du système de fichier';
$_lang['FILESYSTEM'] = 'Système de fichier';
$_lang['DOWNLOAD'] = 'Téléchargement';
$_lang['TAKE_NEW_BACKUP'] = 'Faire une nouvelle sauvegarde?\n peut prendre un certain temps, s.V.P. soyez patient!';
$_lang['FOLDER_NOT_EXIST'] = "Ce répertoire %s n'existe pas!";
$_lang['FOLDER_NOT_WRITE'] = "Ce répertoire %s n`est pas accessible en écriture!";
$_lang['BACKUP_SAVED_INTO'] = "Les fichiers de sauvegarde sont sauvegardé dans %s";
$_lang['CACHE_SAVED_INTO'] = "Les fichiers cache sont sauvegardé dans %s";
$_lang['CACHED_ITEMS'] = 'Éléments mis en cache';
$_lang['ELXIS_ROUTER'] = 'Elxis routage';
$_lang['ROUTING'] = 'Routage';
$_lang['ROUTING_INFO'] = 'Re-routage dirige les utilisateurs vers des adresses URL personnalisées.';
$_lang['SOURCE'] = 'Source';
$_lang['ROUTE_TO'] = 'Routage a';
$_lang['REROUTE'] = "Re-routage %s";
$_lang['DIRECTORY'] = 'Répertoire';
$_lang['SET_FRONT_CONF'] = 'Régler la première page du site dans la configuration Elxis!';
$_lang['ADD_NEW_ROUTE'] = 'Ajouter un nouvel itinéraire';
$_lang['OTHER'] = 'Autres';
$_lang['LAST_MODIFIED'] = 'Dernière modification';
$_lang['PERIOD'] = 'Période'; //time period
$_lang['ERROR_LOG_DISABLED'] = 'Erreur l`enregistrement est désactivé!';
$_lang['LOG_ENABLE_ERR'] = 'Connexion activé uniquement pour les erreurs fatales.';
$_lang['LOG_ENABLE_ERRWARN'] = 'Journal activé pour les erreurs et les avertissements.';
$_lang['LOG_ENABLE_ERRWARNNTC'] = 'Journal activé pour les erreurs, avertissements et avis.';
$_lang['LOGROT_ENABLED'] = 'Rotation des journaux est activée.';
$_lang['LOGROT_DISABLED'] = 'Rotation des journaux est désactivée.';
$_lang['SYSLOG_FILES'] = 'Fichiers journaux du système';
$_lang['DEFENDER_BANS'] = 'Defender bannir';
$_lang['LAST_DEFEND_NOTIF'] = 'Dernière Defender notification';
$_lang['LAST_ERROR_NOTIF'] = 'Dernière erreur notification';
$_lang['TIMES_BLOCKED'] = 'Fois bloqué';
$_lang['REFER_CODE'] = 'Référence code';
$_lang['CLEAR_FILE'] = 'Effacer le fichier';
$_lang['CLEAR_FILE_WARN'] = 'Le contenu du fichier sera supprimé. Continuer?';
$_lang['FILE_NOT_FOUND'] = 'Fichier non trouvé!';
$_lang['FILE_CNOT_DELETE'] = 'Ce fichier ne peut pas être supprimé!';
$_lang['ONLY_LOG_DOWNLOAD'] = 'Seuls les fichiers avec l extension. connexion peut être téléchargé!';
$_lang['SYSTEM'] = 'Système';
$_lang['PHP_INFO'] = 'PHP information';
$_lang['PHP_VERSION'] = 'PHP version';
$_lang['ELXIS_INFO'] = 'Elxis information';
$_lang['VERSION'] = 'Version';
$_lang['REVISION_NUMBER'] = 'Revision numéro';
$_lang['STATUS'] = 'Statut';
$_lang['CODENAME'] = 'Codename';
$_lang['RELEASE_DATE'] = 'Libération date';
$_lang['COPYRIGHT'] = 'Droit d auteur';
$_lang['POWERED_BY'] = 'Propulsé par';
$_lang['AUTHOR'] = 'Auteur';
$_lang['PLATFORM'] = 'Plateforme';
$_lang['HEADQUARTERS'] = 'Quartier général';
$_lang['ELXIS_ENVIROMENT'] = 'Elxis environnement';
$_lang['DEFENDER_LOGS'] = 'Defender connexion';
$_lang['ADMIN_FOLDER'] = 'Administrateur de dossier';
$_lang['DEF_NAME_RENAME'] = 'Nom par défaut, renommer celui-ci!';
$_lang['INSTALL_PATH'] = 'Chemin installation';
$_lang['IS_PUBLIC'] = 'Est publique!';
$_lang['CREDITS'] = 'Crédits';
$_lang['LOCATION'] = 'Location';
$_lang['CONTRIBUTION'] = 'Contribution';
$_lang['LICENSE'] = 'Licence';
$_lang['MULTISITES'] = 'Multisites';
$_lang['MULTISITES_DESC'] = 'Gérer plusieurs sites sous une même installation Elxis.';
$_lang['MULTISITES_WARN'] = 'Vous pouvez avoir plusieurs sites dans une seule installation Elxis. Travailler avec multi-sites
est une tâche qui requiert des connaissances avancées de Elxis CMS. Avant d`importer des données vers un nouveau
multisite assurez-vous que la base de données existe. Après avoir créé un nouveau multisite modifier le .htaccess
déposer sur la base d `instructions des données. La suppression d`un multi-site ne supprime pas la base de données liée. Consulter un
technicien expérimenté si vous avez besoin d`aide.';
$_lang['MULTISITES_DISABLED'] = 'Multi-sites est désactivé!';
$_lang['ENABLE'] = 'Activer';
$_lang['ACTIVE'] = 'Activer';
$_lang['URL_ID'] = 'URL identifier';
$_lang['MAN_MULTISITES_ONLY'] = "Vous pouvez gérer les multi-sites seulement à partir du site %s";
$_lang['LOWER_ALPHANUM'] = 'caractères alphanumériques en minuscules sans espaces';
$_lang['IMPORT_DATA'] = 'Importer les données';
$_lang['CNOT_CREATE_CFG_NEW'] = "Impossible de créer le fichier de configuration %s pour le nouveau site!";
$_lang['DATA_IMPORT_FAILED'] = 'Échec de l`importation des données!';
$_lang['DATA_IMPORT_SUC'] = 'Données importées avec succès!';
$_lang['ADD_RULES_HTACCESS'] = 'Ajouter les règles suivantes dans le fichier .htaccess';
$_lang['CREATE_REPOSITORY_NOTE'] = 'Il est fortement recommandé de créer un répertoire distinct pour chaque sous-site!';
$_lang['NOT_SUP_DBTYPE'] = ' Type de base de données non pris en charge!';
$_lang['DBTYPES_MUST_SAME'] = 'Types de bases de données de ce site et le nouveau site doit être le même!';
$_lang['DISABLE_MULTISITES'] = 'Désactiver multi-sites';
$_lang['DISABLE_MULTISITES_WARN'] = 'Tous les sites, sauf celui avec ID 1 est retiré!';
$_lang['VISITS_PER_DAY'] = "Visites par jour %s"; //translators help: ... for {MONTH YEAR}
$_lang['CLICKS_PER_DAY'] = "Cliques par jour %s"; //translators help: ... for {MONTH YEAR}
$_lang['VISITS_PER_MONTH'] = "Visites par mois %s"; //translators help: ... for {YEAR}
$_lang['CLICKS_PER_MONTH'] = "Clques par mois %s"; //translators help: ... for {YEAR}
$_lang['LANGS_USAGE_FOR'] = "Pourcentage des langues d`usage %s"; //translators help: ... for {MONTH YEAR}
$_lang['UNIQUE_VISITS'] = 'Visiteurs uniques';
$_lang['PAGE_VIEWS'] = 'Pages visualisées';
$_lang['TOTAL_VISITS'] = 'Total visités';
$_lang['TOTAL_PAGE_VIEWS'] = 'Pages visualisées';
$_lang['LANGS_USAGE'] = 'Langue d`usage';
$_lang['LEGEND'] = 'Legende';
$_lang['USAGE'] = 'Usage';
$_lang['VIEWS'] = 'Voir';
$_lang['OTHER'] = 'Autres';
$_lang['NO_DATA_AVAIL'] = 'Pas de données disponibles';
$_lang['PERIOD'] = 'Période';
$_lang['YEAR_STATS'] = 'statistiques de l`année';
$_lang['MONTH_STATS'] = 'Statistiques du mois';
$_lang['PREVIOUS_YEAR'] = 'Année précédente';
$_lang['NEXT_YEAR'] = 'Prochaine année';
$_lang['STATS_COL_DISABLED'] = 'La collecte de données statistiques est désactivé! Activer les statistiques dans la configuration Elxis.';
$_lang['DOCTYPE'] = 'Document type';
$_lang['DOCTYPE_INFO'] = 'L`option recommandée est HTML5. Elxis va générer une sortie XHTML, même si vous définissez DOCTYPE HTML5.
Le XHTML doctypes Elxis sert de documents avec l`application/xhtml+xml mime-type sur les navigateurs modernes et avec text/html sur les anciennes.';
$_lang['ABR_SECONDS'] = 'sec';
$_lang['ABR_MINUTES'] = 'min';
$_lang['HOUR'] = 'heure';
$_lang['HOURS'] = 'heures';
$_lang['DAY'] = 'Jour';
$_lang['DAYS'] = 'Jours';
$_lang['UPDATED_BEFORE'] = 'Mise à jour avant';
$_lang['CACHE_INFO'] = 'Pour afficher et supprimer les articles sauvegardés dans la mémoire cache.';
$_lang['ELXISDC'] = 'Elxis Centre de Téléchargement';
$_lang['ELXISDC_INFO'] = 'Parcourir vivre EDC et voir les extensions disponibles';
$_lang['SITE_LANGS'] = 'Site langue';
$_lang['SITE_LANGS_DESC'] = 'Par défaut toutes les langues installées sont disponibles dans la première page du site. Vous pouvez modifier cela
en sélectionnant ci-dessous les langues que vous souhaitez uniquement être disponible en première page.';
//Elxis 4.1
$_lang['PERFORMANCE'] = 'Performance';
$_lang['MINIFIER_CSSJS'] = 'CSS/Javascript minifier';
$_lang['MINIFIER_INFO'] = 'Elxis can unify individual local CSS and JS files and optionally compress them. The unified file will be saved in cache. 
So instead of having multiple CSS/JS files in your pages head section you will have only a minified one.';
$_lang['MOBILE_VERSION'] = 'Mobile version';
$_lang['MOBILE_VERSION_DESC'] = 'Enable mobile-friendly version for handheld devices?';

?>