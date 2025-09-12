<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : function.php
 * Decsription : Ce fichier permet de gerer toute les fonctions ncessaires au fonctionnnement de cette API , ici il y a des fonctions special juste pour recuperer liste des chats et les afficher 
 * Date de creation : 09/08/2025
 * Date de modification : 09/08/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * 
 * Cree la fonction qui permet de recuperer l'historique des chats 
 * 
 */
function getHistory(){
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
     */
    $chats = json_decode(file_get_contents($_CHAT_FILE), true);

    /**
     * 
     *  Trier par created_at DESC (plus récent en premier)
     * 
     */
    uasort($chats, function ($a, $b) {
        return strtotime($b['created_at']) <=> strtotime($a['created_at']);
    });


    /**
     * 
     * Faire un foreach pour parcourir les chats et recuperer les details necessaires 
     * 
     */
    $chat_list = [];

       foreach ($chats as $chat_id => $chat) {
        $chat_list['chat'][$chat_id] = [
            'id' => $chat_id,
            'title' => $chat['title'] ?? 'No Title',
            'time_go' => timeAgo($chat['created_at']) ?? 'Unknown',
            'auto_prompt_file' => $chat['auto_prompt_file'] ?? null
            //'messages' => $chat['messages'] ?? []
        ];
    }

    /**
     * 
     * Renvoyer la liste des chats 
     * 
     */
    return api_response(200, "Chat list retrieved successfully.", $chat_list);

}

/**
 * 
 * 
 * Creer une fonction pour recuperer le chat par ID 
 * 
 * 
 */
function getChatbyID($chat_id){
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
     * 
     * Convertir la forme du chatId au cas ou c'est mal envoyer 
     * 
     * 
     */
    $chat_id = str_replace("/","-",$chat_id);

    /**
     * 
     * Lire le contenu du fichier de la base des donnees des conversations
     * 
     */
    $chats = json_decode(file_get_contents($_CHAT_FILE), true);

    /**
     * 
     * Verifier si le chat existe 
     * 
     * 
     */
    if(array_key_exists($chat_id,$chats)){
        /**
         * 
         * Acceder est recuperer uniquement l'id et la conversation 
         * 
         * 
         */
        $_id = $chats[$chat_id]['id'] ?? null;
        $_messages = $chats[$chat_id]['messages'] ?? null;

        /***
         * 
         * Creer une reponse 
         * 
         * 
         */
        $_response = [
            "id" => $_id,
            "chat" => $_messages
        ];

        /**
         * 
         * Renvoyer la reponse 
         * 
         */
        return api_response(200,"Chat Successfuly",$_response);

    }else{
        /**
         * 
         * Renvoyer une erreur 
         * 
         */
        return api_response(404,null,null);
    }

}

/**
 * 
 * 
 * Creer une fonction pour afficher les chats en fonction du temps 
 * 
 * 
 */
function timeAgo($datetime) {
    /**
     * 
     * Recuperer le time & date puis le mettre au propre 
     * 
     */
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    /**
     * 
     * 
     * Verifier la difference , si la date est inferieur a 1 donc poster a l'instant
     * 
     */
    if ($diff < 1) return "à l'instant";

    /**
     * 
     * Creer un tableau d'unites de conversion 
     * 
     * 
     */
    $units = [
        31536000 => 'an',
        2592000  => 'mois',
        604800   => 'semaine',
        86400    => 'jour',
        3600     => 'heure',
        60       => 'min',
        1        => 'seconde'
    ];

    /**
     * 
     * lancer une boucle de conversion en fonction du datetime et sa difference d'ecart 
     * 
     */
    foreach ($units as $secs => $str) {
        $count = floor($diff / $secs);
        if ($count >= 1) {
            return "il y a " . $count . " " . $str . ($count > 1 && $str !== 'mois' ? 's' : '');
        }
    }
}

?>