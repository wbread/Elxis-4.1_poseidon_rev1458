<?php 
/**
* @version: 4.0
* @package: Elxis CMS
* @subpackage: Elxis Language
* @author: Elxis Team ( http://www.elxis.org )
* @copyright: (C) 2006-2012 Elxis.org. All rights reserved.
* @description: fr-FR (French - Canada France) language for Elxis CMS
* @license: Elxis public license http://www.elxis.org/elxis-public-license.html
* @translator: Francis Dionne Canada ( http://www.elxis.org )
*
* ---- THIS FILE MUST BE ENCODED AS UTF-8! ----
*
*****************************************************************************/

defined('_ELXIS_') or die ('Direct access to this location is not allowed.');


$locale = array('fr_FR.utf8', 'fr_FR.UTF-8', 'fr_FR', 'fr', 'french'); //utf-8 locales array

$_lang = array();
//date formats
$_lang['DATE_FORMAT_BOX'] = 'd-m-Y'; //formats supportés: d-m-Y, Y-m-d, d/m/Y, Y/m/d
$_lang['DATE_FORMAT_BOX_LONG'] = 'd-m-Y H:i:s'; //formats supportés: d-m-Y H:i:s, Y-m-d H:i:s, d/m/Y H:i:s, Y/m/d H:i:s
$_lang['DATE_FORMAT_1'] = "%d/%m/%Y"; //exemple: 25/12/2010
$_lang['DATE_FORMAT_2'] = "%b %d, %Y"; //exemple: Dec 25, 2010
$_lang['DATE_FORMAT_3'] = "%B %d, %Y"; //exemple: Décembre 25, 2010
$_lang['DATE_FORMAT_4'] = "%b %d, %Y %H:%M"; //exemple: Dec 25, 2010 12:34
$_lang['DATE_FORMAT_5'] = "%B %d, %Y %H:%M"; //exemple: Décembre 25, 2010 12:34
$_lang['DATE_FORMAT_6'] = "%B %d, %Y %H:%M:%S"; //exemple: Décembre 25, 2010 12:34:45
$_lang['DATE_FORMAT_7'] = "%a %b %d, %Y"; //exemple: Sat Dec 25, 2010
$_lang['DATE_FORMAT_8'] = "%A %b %d, %Y"; //exemple: Samedi Dec 25, 2010
$_lang['DATE_FORMAT_9'] = "%A %B %d, %Y"; //exemple: Samedi Décembre 25, 2010
$_lang['DATE_FORMAT_10'] = "%A %B %d, %Y %H:%M"; //exemple: Samedi Décembre 25, 2010 12:34
$_lang['DATE_FORMAT_11'] = "%A %B %d, %Y %H:%M:%S"; //exemple: Samedi Décembre 25, 2010 12:34:45
$_lang['DATE_FORMAT_12'] = "%a %B %d, %Y %H:%M"; //exemple: Sat Décembre 25, 2010 12:34
$_lang['DATE_FORMAT_13'] = "%a %B %d, %Y %H:%M:%S"; //exemple: Sat Décembre 25, 2010 12:34:45
$_lang['THOUSANDS_SEP'] = ',';
$_lang['DECIMALS_SEP'] = '.';
//month names
$_lang['JANUARY'] = 'Janvier';
$_lang['FEBRUARY'] = 'Fevrier';
$_lang['MARCH'] = 'Mars';
$_lang['APRIL'] = 'April';
$_lang['MAY'] = 'Mai';
$_lang['JUNE'] = 'Juin';
$_lang['JULY'] = 'Juillet';
$_lang['AUGUST'] = 'Août';
$_lang['SEPTEMBER'] = 'Septembre';
$_lang['OCTOBER'] = 'Octobre';
$_lang['NOVEMBER'] = 'Novembre';
$_lang['DECEMBER'] = 'Decembre';
$_lang['JANUARY_SHORT'] = 'Jan';
$_lang['FEBRUARY_SHORT'] = 'Feb';
$_lang['MARCH_SHORT'] = 'Mar';
$_lang['APRIL_SHORT'] = 'Apr';
$_lang['MAY_SHORT'] = 'Mai';
$_lang['JUNE_SHORT'] = 'Jun';
$_lang['JULY_SHORT'] = 'Jul';
$_lang['AUGUST_SHORT'] = 'Aug';
$_lang['SEPTEMBER_SHORT'] = 'Sep';
$_lang['OCTOBER_SHORT'] = 'Oct';
$_lang['NOVEMBER_SHORT'] = 'Nov';
$_lang['DECEMBER_SHORT'] = 'Dec';
//day names
$_lang['MONDAY'] = 'Lundi';
$_lang['THUESDAY'] = 'Mardi';
$_lang['WEDNESDAY'] = 'Mercredi';
$_lang['THURSDAY'] = 'Jeudi';
$_lang['FRIDAY'] = 'Vendredi';
$_lang['SATURDAY'] = 'Samedi';
$_lang['SUNDAY'] = 'Dimanche';
$_lang['MONDAY_SHORT'] = 'Mon';
$_lang['THUESDAY_SHORT'] = 'Mar';
$_lang['WEDNESDAY_SHORT'] = 'Mer';
$_lang['THURSDAY_SHORT'] = 'Jeu';
$_lang['FRIDAY_SHORT'] = 'Ven';
$_lang['SATURDAY_SHORT'] = 'Sam';
$_lang['SUNDAY_SHORT'] = 'Dim';
/* elxis performance monitor */
$_lang['ELX_PERF_MONITOR'] = 'Elxis Moniteur de Performance';
$_lang['ITEM'] = 'Item';
$_lang['INIT_FILE'] = 'fichier d`initialisation';
$_lang['EXEC_TIME'] = 'Temps Exécution';
$_lang['DB_QUERIES'] = 'DB requêtes';
$_lang['ERRORS'] = 'Erreurs';
$_lang['SIZE'] = 'Taille';
$_lang['ENTRIES'] = 'Inscriptions';

/* general */
$_lang['HOME'] = 'Accueil';
$_lang['YOU_ARE_HERE'] = 'Vous êtes ici';
$_lang['CATEGORY'] = 'Catégorie';
$_lang['DESCRIPTION'] = 'Description';
$_lang['FILE'] = 'Fichier';
$_lang['IMAGE'] = 'Image';
$_lang['IMAGES'] = 'Images';
$_lang['CONTENT'] = 'Contenu';
$_lang['DATE'] = 'Date';
$_lang['YES'] = 'Oui';
$_lang['NO'] = 'Non';
$_lang['NONE'] = 'Aucun';
$_lang['SELECT'] = 'Sélection';
$_lang['LOGIN'] = 'Connexion';
$_lang['LOGOUT'] = 'Déconnexion';
$_lang['WEBSITE'] = 'Site web';
$_lang['SECURITY_CODE'] = 'Code de sécurité';
$_lang['RESET'] = 'Rafrachir';
$_lang['SUBMIT'] = 'Soumettre';
$_lang['REQFIELDEMPTY'] = 'Un ou plusieurs champs obligatoires doivent-être rempli!';
$_lang['FIELDNOEMPTY'] = "%s ne doit pas être vide!";
$_lang['FIELDNOACCCHAR'] = "%s contient des caractères non valide!";
$_lang['INVALID_DATE'] = 'Date invalide!';
$_lang['INVALID_NUMBER'] = 'Nombre invalide!';
$_lang['INVALID_URL'] = 'Adresse URL invalide!';
$_lang['FIELDSASTERREQ'] = 'Les champs avec astérisque* sont requis.';
$_lang['ERROR'] = 'Erreur';
$_lang['REGARDS'] = 'Cordialement';
$_lang['NOREPLYMSGINFO'] = 'S.V.P. ne répondez pas à ce message car il a été envoyé uniquement à titre d`information.';
$_lang['LANGUAGE'] = 'Langue';
$_lang['PAGE'] = 'Page';
$_lang['PAGEOF'] = "Page %s de %s";
$_lang['OF'] = 'de';
$_lang['DISPLAY_FROM_TO_TOTAL'] = "Afficher %s à %s de %s articles";
$_lang['HITS'] = 'Cliques';
$_lang['PRINT'] = 'Imprimer';
$_lang['BACK'] = 'Retour';
$_lang['PREVIOUS'] = 'Prévisualiser';
$_lang['NEXT'] = 'Prochain';
$_lang['CLOSE'] = 'Fermer';
$_lang['CLOSE_WINDOW'] = 'Fermer la fenêtre';
$_lang['COMMENTS'] = 'Commentaires';
$_lang['COMMENT'] = 'Commentaire';
$_lang['PUBLISH'] = 'Publier';
$_lang['DELETE'] = 'Supprimer';
$_lang['EDIT'] = 'Editer';
$_lang['COPY'] = 'Copier';
$_lang['SEARCH'] = 'Recherche';
$_lang['PLEASE_WAIT'] = 'S.V.P. Attender...';
$_lang['ANY'] = 'Tous';
$_lang['NEW'] = 'Nouveau';
$_lang['ADD'] = 'Ajouter';
$_lang['VIEW'] = 'Voir';
$_lang['MENU'] = 'Menu';
$_lang['HELP'] = 'Aide';
$_lang['TOP'] = 'Haut';
$_lang['BOTTOM'] = 'Bas';
$_lang['LEFT'] = 'Gauche';
$_lang['RIGHT'] = 'Droite';
$_lang['CENTER'] = 'Centre';

/* xml */
$_lang['CACHE'] = 'Cache';
$_lang['ENABLE_CACHE_D'] = 'Activer le cache pour cet article?';
$_lang['YES_FOR_VISITORS'] = 'Oui, pour les visiteurs';
$_lang['YES_FOR_ALL'] = 'Oui, pour tous';
$_lang['CACHE_LIFETIME'] = 'Cache durée de vie';
$_lang['CACHE_LIFETIME_D'] = 'Temps, en minutes, jusqu`à ce que le cache est été actualisé pour cet article.';
$_lang['NO_PARAMS'] = 'Il n`existe aucun paramètre!';
$_lang['STYLE'] = 'Style';
$_lang['ADVANCED_SETTINGS'] = 'Paramètre avancé';
$_lang['CSS_SUFFIX'] = 'CSS suffix';
$_lang['CSS_SUFFIX_D'] = 'Un suffixe qui sera ajouté à la classe du module CSS.';
$_lang['MENU_TYPE'] = 'Menu type';
$_lang['ORIENTATION'] = 'Orientation';
$_lang['SHOW'] = 'Afficher';
$_lang['HIDE'] = 'Cacher';
$_lang['GLOBAL_SETTING'] = 'Paramètre global';

/* users & authentication */
$_lang['USERNAME'] = 'Nom d`usager';
$_lang['PASSWORD'] = 'Mot de passe';
$_lang['NOAUTHMETHODS'] = 'Aucune méthode d`authentification ont été mis en';
$_lang['AUTHMETHNOTEN'] = 'Méthode d`authentification %s n`est pas activé';
$_lang['PASSTOOSHORT'] = 'Votre mot de passe est trop court pour être acceptable';
$_lang['USERNOTFOUND'] = 'Usager non trouvé';
$_lang['INVALIDUNAME'] = 'Nom d`usager invalide';
$_lang['INVALIDPASS'] = 'Mot de passe invalide';
$_lang['AUTHFAILED'] = 'Authentification manquée';
$_lang['YACCBLOCKED'] = 'Votre compte est bloqué';
$_lang['YACCEXPIRED'] = 'Votre compte est expiré';
$_lang['INVUSERGROUP'] = 'Groupe d`usager invalide';
$_lang['NAME'] = 'Nom';
$_lang['FIRSTNAME'] = 'Prénom';
$_lang['LASTNAME'] = 'Nom famille';
$_lang['EMAIL'] = 'Courriel';
$_lang['INVALIDEMAIL'] = 'Adresse courriel invalide';
$_lang['ADMINISTRATOR'] = 'Administrateur';
$_lang['GUEST'] = 'Invité';
$_lang['EXTERNALUSER'] = 'Usager externe';
$_lang['USER'] = 'Usager';
$_lang['GROUP'] = 'Groupe';
$_lang['NOTALLOWACCPAGE'] = 'Vous n`êtes pas autorisé à accéder à cette page';
$_lang['NOTALLOWACCITEM'] = 'Vous n`êtes pas autorisé à accéder à cet article!';
$_lang['NOTALLOWMANITEM'] = 'Vous n`êtes pas autorisé à gérer cet article!';
$_lang['NOTALLOWACTION'] = 'Vous n`êtes pas autorisé à effectuer cette action!';
$_lang['NEED_HIGHER_ACCESS'] = 'Vous avez besoin d`un niveau d`accès pour cette action!';
$_lang['AREYOUSURE'] = 'Ëtes-vous sûr?';

/* highslide */
$_lang['LOADING'] = 'Téléchargement...';
$_lang['CLICK_CANCEL'] = 'Cliquez ici pour annuler';
$_lang['MOVE'] = 'Déplacer';
$_lang['PLAY'] = 'Lecture';
$_lang['PAUSE'] = 'Pause';
$_lang['RESIZE'] = 'Redimentionner';

/* admin */
$_lang['ADMINISTRATION'] = 'Administration';
$_lang['SETTINGS'] = 'Parmètres';
$_lang['DATABASE'] = 'Base de données';
$_lang['ON'] = 'On';
$_lang['OFF'] = 'Off';
$_lang['WARNING'] = 'Avertissement';
$_lang['SAVE'] = 'Sauver';
$_lang['APPLY'] = 'Appliquer';
$_lang['CANCEL'] = 'Annuler';
$_lang['LIMIT'] = 'Limite';
$_lang['ORDERING'] = 'Commande';
$_lang['NO_RESULTS'] = 'Aucun résultat trouvé!';
$_lang['CONNECT_ERROR'] = 'Erreur de connection';
$_lang['DELETE_SEL_ITEMS'] = 'Supprimer les éléments sélectionnés?';
$_lang['TOGGLE_SELECTED'] = 'basculer la sélection';
$_lang['NO_ITEMS_SELECTED'] = 'Aucun élément selectionné!';
$_lang['ID'] = 'Id';
$_lang['ACTION_FAILED'] = 'Échec de l`action !';
$_lang['ACTION_SUCCESS'] = 'Action complété avec succès!';
$_lang['NO_IMAGE_UPLOADED'] = 'Aucune image télécharger';
$_lang['NO_FILE_UPLOADED'] = 'Aucun fichier télécharger';
$_lang['MODULES'] = 'Modules';
$_lang['COMPONENTS'] = 'Composants';
$_lang['TEMPLATES'] = 'Templates';
$_lang['SEARCH_ENGINES'] = 'Moteur de recherche';
$_lang['AUTH_METHODS'] = ' méthodes d`authentification';
$_lang['CONTENT_PLUGINS'] = 'Plugins contenu';
$_lang['PLUGINS'] = 'Plugins';
$_lang['PUBLISHED'] = 'Éditeur';
$_lang['ACCESS'] = 'Accès';
$_lang['ACCESS_LEVEL'] = 'Niveau d`accès';
$_lang['TITLE'] = 'Titre';
$_lang['MOVE_UP'] = 'Déplacer vers le haut';
$_lang['MOVE_DOWN'] = 'Déplacer vers le bas';
$_lang['WIDTH'] = 'Largeur';
$_lang['HEIGHT'] = 'Hauter';
$_lang['ITEM_SAVED'] = 'Articles sauvegardés';
$_lang['FIRST'] = 'Premier';
$_lang['LAST'] = 'Dernier';
$_lang['SUGGESTED'] = 'Suggestion';
$_lang['VALIDATE'] = 'Validation';
$_lang['NEVER'] = 'Jamais';
$_lang['ALL'] = 'Tous';
$_lang['ALL_GROUPS_LEVEL'] = "Tous les groupes de niveau %s";
$_lang['REQDROPPEDSEC'] = 'Votre demande a diminué pour des raisons de sécurité. S.V.P. essayer de nouveau.';
$_lang['PROVIDE_TRANS'] = 'S.V.P. fournir une traduction!';
$_lang['AUTO_TRANS'] = 'Tranduction automatique';
$_lang['STATISTICS'] = 'Statistiques';
$_lang['UPLOAD'] = 'Envoyer';
$_lang['MORE'] = 'Plus';

?>