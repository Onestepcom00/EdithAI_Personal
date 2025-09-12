<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : index.php
 * Decsription : Ce fichier , est l'api qui permet de renvoyer l'historique des chats de l'utilisateur , et permet aussi de renvoyer kes informations de chaque chat et l'historique complet . 
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
 * Installer les fichiers de configurations de l'API 
 * 
 */
require_once '../config.php';
require_once '../loader.php';
require_once 'function.php';

/**
 * 
 * Recuperer la requete GET 
 * 
 */
if($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    /**
     * 
     * Recuperer les parametres de la requete GET 
     * 
     */
    $_getHistory = $_GET['getHistory'] ?? null;
    $_getChatID = $_GET['getChatID'] ?? null;

    /**
     * 
     * Verifier si la demande de l'historique a ete lancer
     * 
     */
    if($_getHistory === 'true') {
        /**
         * 
         * Lancer la fonction pour recuperer l'historique des chats 
         * 
         */
        echo getHistory();
    }
    /**
     * 
     * 
     * Recuperer la liste des chats par id
     * 
     * 
     */
    elseif(isset($_getChatID)){
        /**
         * 
         * Lancer la fonction qui va afficher la liste des chats 
         * 
         * 
         */
        echo getChatbyID($_getChatID);

    }
    else{
        /**
         * 
         * renvoyer une erreur car la demande n'est pas connu 
         * 
         */
        echo api_response(400, null, null);
    }

}else{
    /**
     * 
     * Renvoyer une erreur car la methode de la requete n'est pas GET 
     * 
     */
    echo api_response(405, null, null);
}


?>