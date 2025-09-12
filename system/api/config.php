<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : config.php
 * Decsription : Il s'agit du fichier de configuration de l'API , ici nous allons mettre toute les configuration
 * necessaire pour le bon fonctionnement des API system.
 * Date de creation : 01/08/2025
 * Date de modification : 01/08/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * 
 * Configuration de l'API , ici nous allons mettre 
 * les configurations lier a l'API et les methodes autoriser 
 * 
 */
header('Access-Control-Allow-Origin: *'); // Autoriser toutes les origines 
header('Access-Control-Allow-Methods: GET, POST'); // Autoriser les methodes HTTP
//header('Access-Control-Allow-Headers: Content-Type, Authorization'); // Autoriser les headers specifiques
header('Content-Type: application/json'); // Type de contenu JSON


/**
 * 
 * Configuration de l'application et de son interface 
 * 
 */
define('APP_NAME','EdithAI'); // Mettre le nom de l'Application 
define('APP_VERSION','1.0'); // La  version de l'application
define('API_VERSION','1.0'); // La version de l'API system


/**
 * 
 * Configuration des fichiers systemes 
 * 
 */
define('CONFIG_FILE','config.json'); // Ce fichier va contenir toutes les configurations lier a l'application et a l'API system
define('LOG_FILE','logs.txt'); // Ce fichier va contenir les journaux de l'application et de l'API system 
define('DATABASE','chat.json'); // Ce fichier va contenir la base des donnees des conversations dans l'applications 
define('USER_FILE','users.json'); // Ce fichier va contenir toutes les informations lier aux utilisateurs de l'application 


/**
 * 
 * Configuration des dossiers systemes , les dossiers de base de l'applications 
 * 
 */
define('LOGS_DIR','core/logs/'); // Dossier des logs de l'application 
define('DATA_DIR','core/data/'); // Dossier pour contenir les fichiers json
define('CACHE_DIR','core/cache/'); // Dossier pour contenir les caches de l'application
define('UPLOAD_DIR','core/uploads/'); // Dossier pour stocker les fichiers uploader 
define('TEMPLATES_DIR','core/templates/'); // Dossier pour stocker les templates comme PDF , DOCX etc...

/**
 * 
 * Configuration necessaire lier aux dossiers des sessions 
 * 
 */
//define('SESSION_DIR','sessions/'); // Dossier pour contenir les sessions des chats 
define('CHAT_SESSION_DIR','core/sessions/chats/'); // Dossier pour stocker les sessions des chats
define('USER_SESSION_DIR','core/sessions/users/'); // Dossier pour stocker les sessions des utilisateurs 
define('PROMPT_SESSION_DIR','core/sessions/prompts/'); // Dossier pour stockers les prompts par sessions 
define('MEMORY_SESSION_DIR','core/sessions/memory/'); // Dossier pour stocker les memoires des sessions


/**
 * 
 * Configuration des moteurs IA et leurs endpoint 
 * 
 */
$_MODEL = [
    "https://chat.onestepcom00.workers.dev/chat",
    "https://chat.kreatyva0.workers.dev/chat",
    "https://chat.kreatyvaprojet.workers.dev/chat",
    "https://app.networkfree714.workers.dev/chat"
];


/**
 * 
 * Configuration du gestionnaire de tool manager et MCP getter 
 * 
 */
$_MCP = [
    "tool_manager" => "../tools/", // Endpoint pour les gestionnaires de tools
    "mcp_manager" => "../mcp/" // Endpoint pour les MCP getter 
];

/**
 * 
 * Configurer le gestionnaire d'outils 
 * 
 */
define('TOOL_MANAGER_URL', 'http://localhost/EDITHAI_PROJET/system/tools');

/**
 * 
 * Autres configurations pour l'application 
 * 
 */
define('APP_USER_AGENT','EdithAI/1.0 Personnal Assistant Client');
define('NBR_MAX_REDIRECT',5); // le nombre max de redirection 
define('TIMEOUT',30); // Le temps d'attente max pour une requete
define('VERIFY_SSL',false);
define('FOLLOW_LOCATION',true); // Suivre les redirections 


/**
 * 
 * ***********************************************
 * ***********************************************
 * 
 */

/**
 * 
 * Prompt System Pour le Titre 
 * 
 */
$_TITLE_TAILLE = 65; // Taille Maximum
$_TITLE_INDICE = "uuTestoruu"; // Indice 
$_PROMPT_TITLE = "
TON TRAVAIL ICI EST DE GENERER UNIQUEMENT UN TITRE POUR CE MESSAGE , UN TITRE COURT DE ($_TITLE_TAILLE) CARACTERES 
, QUI VA DANS LE MEME SENS , DANS LA MEME IDEE , DANS LA MEME REFLEXION QUE LE MESSAGE , SANS CHANGER DE CONTEXT , 
TU VAS RIN ECRIRE D'AUTRES UNIQUMENT LE TITRE , ALORS VOICI LE MESSAGE ($_TITLE_INDICE)
";


/**
 * 
 * 
 * User Personnaliter 
 * 
 * 
 */
$_PERSONNE = [
    "name" => "Exaustan Malka",
    "job" => "Developpeur web , programmeur , pentesteur",
    "style" => "Utilie un ton formel et professionnel.Rpondre sur le ton d'une conversation.Adopter un style innovant et sortir des sentiers battus.Utiliser un ton motivant. "
]
?>