<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : config.php
 * Decsription : Il s'agit du fichier de configuration de l'application EdithAI coter client 
 * Date de creation : 02/08/2025
 * Date de modification : 02/08/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * 
 * 
 * Interface details 
 * 
 * 
 */

$_TEXT = [
    "sidebar" => [
        "name" => "EdithAI",
        "sub_name" => "Powered by AI",
        "btn_nchat" => "New Chat",
        "btn_search" => "Search",
        "btn_tools" => "Tools" 
    ],
    "user" => [
        "name" => "Rendy Safaitrien",
        "plan" => "Premium Plan"
    ],
    "bot" => [
        "default" => "Ask me anything, I'm here to help",
        "default_message" => "Salut , Je suis EdithAi 👋 , votre assistant personnel je suis la pour vous aider dans vos taches courant !",
        "status" => "Online"
    ],
    "page" => [
        "input_placeholder" => "Ask me anything...",
        "typing_message" => "AI is thinking..",
        "send_message" => "Send"
    ],
    "info" => "AI can make mistakes. Consider checking important information.",
    "call" => [
        "screen_share" => "Share Screen",
        "end_call" => "End Call"
    ]
    
];

/**
 * 
 * Creer un tableau contenant la liste des models 
 * 
 */
$_MODELS = [
    [
        "name" => "GPT-4",
        "details" => "Most Capable GPT-4 model",
        "code" => ""
    ],
    [
        "name" => "GPT-3.5",
        "details" => "Fast and cost-effective GPT-3.5 model",
        "code" => ""
    ],
    [
        "name" => "Claude 2",
        "details" => "Advanced AI model by Anthropic",
        "code" => ""
    ],
    [
        "name" => "Gemini Pro",
        "details" => "Google's latest AI model",
        "code" => ""
    ],
    [
        "name" => "Llama 3",
        "details" => "Meta's powerful Llama 3 model",
        "code" => ""
    ]
];


/**
 * 
 * Configurer les constantes de l'application 
 * 
 * 
 */
define('APP_NAME','EdithAI Personal Assistant'); // Nom de l'application
// Recuperer l'URL du domaine et le protocole HTTP ou HTTPS 
define('APP_URL', (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']); // URL de l'application
/**
 * 
 * 
 * Configurer les routes vers les API 
 * 
 * 
 */
define('API_BASE_URL','http://localhost/EDITHAI_PROJET/system/api/'); // URL de base de l'API 
define('API_VERIFY_CHAT', API_BASE_URL . 'news_chat/?verify='); // On va juste ajouter l'ID du chat qu'on veut verifier 

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
 * Installation CSS 
 * 
 */
$_CSS = [
    "tailwind" => "assets/css/tailwind.min.css",
    "design" => "assets/css/design.css"
];

/**
 * 
 * Installation JS 
 * 
 */
$_JS = [
    "tailwind" => "assets/js/lucide.js",
    "marked" => "assets/js/marked.min.js",
    "tailwind_config" => "assets/js/tailwind.config.js",
    "tailwind" => "assets/js/tailwind.js"
];

?>