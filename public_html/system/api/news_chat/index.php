<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : index.php
 * Decsription : Ce fichier est un API qui permet de lancer un nouveau chat avec l'assistant il va generer tout les fichiers necessaures pour le fonctionnement du chat.
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
    $_start = $_GET['start'] ?? null;
    $_verify = $_GET['verify'] ?? null;
    // Verifier si le parametre start a ete lancer en true 
    if($_start === 'true') {
       /**
        *
        *  lancer la fonction pour generer un nouveau chat et l'afficher 
        *
        */
        echo start_new_chat();

    }
    elseif(isset($_verify)){
        /**
         * 
         * Lancer la fonction pour verifier si le id du chat existe 
         * 
         * 
         */
        echo verify_chat_id($_verify);

    }else {
        /**
         * 
         * Renvoyer une erreur parce que le parametre start n'est pas lancer avec true 
         * 
         */
       echo api_response(400,null,null);
    }
} else {
    /**
     * 
     * Renvoyer une erreur parce que la methode utiliser n'est pas autoriser 
     * 
     */
   echo api_response(405,null,null);
}

?>