<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : function.php
 * Decsription : Ce fichier va cntenir toute les fonctions ncessaires au fonctionnement de l'API qui genere des nouveaux chats .
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
 * Creer la fonctions qui genere les nouveax chats
 * 
 */
function start_new_chat(){
    /**
     * 
     * Charger et creer les chemins vers les fichiers json necessaires pour le sauvegarde des chats 
     * 
     */
    $_CHAT_FILE = "../" . DATA_DIR . DATABASE; // Chemin vers le fichier de la base des donnees des conversations 
    $_USER_FILE = "../" . DATA_DIR . USER_FILE; // Chemin vers le fichier de la base des utilisateurs

    /**
     * 
     * Verifier si le fichier de la base des donnees des conversations existe , sinon le creer 
     * 
     */
    if(!file_exists($_CHAT_FILE)){
        /**
         * 
         * Renvoyer une erreur et bloquer l'execution du script 
         * 
         */
        return api_response(404,null,null);
    }

    /**
     * 
     * Verifier si le fichier de la base des utilisateurs existe , sinon le creer 
     * 
     */
    if(!file_exists($_USER_FILE)){
        /**
         * 
         * Renvoyer une erreur et bloquer l'execution du script 
         * 
         */
        return api_response(404,null,null);
    }

    /**
     * 
     * Generer un ID unique pour le nouveau chat 
     * 
     */
    $_chat_id = uniqid('chat-', true);
    $_chat_id = str_replace('.', '',$_chat_id);

    /**
     * 
     * Creer le fichier de l'auto-prompter de l'IA 
     * 
    */
    $_auto_prompt_file = "../" . PROMPT_SESSION_DIR . $_chat_id . '.txt';
    if(!file_exists($_auto_prompt_file)){
        file_put_contents($_auto_prompt_file, ""); // Creer le fichier vide
    }

   
    /**
     * 
     * Creer un tableau pour stocker les informations du nouveau chat 
     * 
     */
    $_chat_tb = [
        "id" => $_chat_id,
        "title" => "New Chat",
        "created_at" => date('Y-m-d H:i:s'),
        "auto_prompt_file" => $_auto_prompt_file,
        "messages" => []
    ];

    /**
     * 
     * Creer un tableau pour la reponse qui sera afficher dans l'API 
     * 
     */
    $_chat_response = [
        "id" => $_chat_id
    ];

    /**
     * 
     * Sauvegarder le nouveau chat dans le fichier de la base des donnees des conversations 
     * 
     */
    json_add($_CHAT_FILE, $_chat_id, $_chat_tb);

    /**
     * 
     * Renvoyer une reponse success avec l'id du nouveau chat 
     * 
     */
    return api_response(200, "New chat created successfully.", $_chat_response);

}


/**
 * 
 * Creer la fonction qui verifie si le chat existe 
 * 
 */
function verify_chat_id($_chat_id){
   /**
     * 
     * Charger et creer les chemins vers les fichiers json necessaires pour le sauvegarde des chats 
     * 
     */
    $_CHAT_FILE = "../" . DATA_DIR . DATABASE; // Chemin vers le fichier de la base des donnees des conversations 
    $_USER_FILE = "../" . DATA_DIR . USER_FILE; // Chemin vers le fichier de la base des utilisateurs

    /**
     * 
     * Verifier si le fichier de la base des donnees des conversations existe , sinon le creer 
     * 
     */
    if(!file_exists($_CHAT_FILE)){
        /**
         * 
         * Renvoyer une erreur et bloquer l'execution du script 
         * 
         */
        return api_response(404,null,null);
    }

    /**
     * 
     * Verifier si le fichier de la base des utilisateurs existe , sinon le creer 
     * 
     */
    if(!file_exists($_USER_FILE)){
        /**
         * 
         * Renvoyer une erreur et bloquer l'execution du script 
         * 
         */
        return api_response(404,null,null);
    }

    /**
     * 
     * Lire le contenu du fichier de la base des donnees des conversations 
     * 
     * 
     */
    $_read = json_read($_CHAT_FILE);

    /**
     * 
     * Verifier si le chat existe dans la base des donnees 
     * 
     */
    if(array_key_exists($_chat_id, $_read)){
        /**
         * 
         * Renvoyer une reponse success avec les informations du chat 
         * 
         */
        return api_response(200, "Chat exists.", $_read[$_chat_id]);
    }else{
        /**
         * 
         * 
         * Renvoyer une reponse error si le chat n'existe pas 
         * 
         */
        return api_response(404,null,null);
    }
    
}
?>