<?php
/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : loader.php
 * Decsription : Ce fichier permet de gerer la configuration du MCP pour l'API , en fin d'interagir avec le ChatBot et les differents outils 
 * Date de creation : 18/08/2025
 * Date de modification : 18 /08/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * 
 * Mettre les chemins d'acces vers le tools 
 * 
 */
define('TOOLS_PATH','tools/');

/**
 * 
 * Mettre le nom du gestionnaire des outils (Tool manangement )
 * 
 */
define('TOOL_MANAGER','tools.json');

/**
 * 
 * Mettre le chemin d'acces vers le centre d'application (App Center )
 * 
 */
define('APP_CENTER','app/');

/**
 * 
 * Creer la fonction pour acceder au gestionnaire des outils 
 * 
 */
function getToolManager($trouth = ""){
    /**
     * 
     * creer une varaibles 
     * 
     */
    $_tools = $trouth . "../" . TOOLS_PATH . TOOL_MANAGER;
    $_app = $trouth . "../" . APP_CENTER;

    /**
     * 
     * Verifier si les fichiers existent 
     * 
     */
    if(file_exists($_tools) && is_dir($_app)){
        /**
         * 
         * Acceder au contenu 
         * 
         */
        $_tools = file_get_contents($_tools);
        $_tools = json_decode($_tools,true);

        /**
         * 
         * Renvoyer le contenu en tableau 
         * 
         */
        // 🔥 Retourner le tableau JSON sous forme de string lisible par l'IA
        return json_encode($_tools, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    }else{
        /**
         * 
         * Creer 
         * 
         */
        mkdir($_app,0777,true);
       // file_put_contents($_tools, '{}', LOCK_EX);
    }
    
}


?>