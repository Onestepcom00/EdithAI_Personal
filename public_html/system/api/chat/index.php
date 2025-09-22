<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : function.php
 * Decsription :Ce fichier permte de gerer l'entree de message et de faire une verification .
 * Date de creation : 10/08/2025
 * Date de modification : 10/08/2025 
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
 * Verifier l'entree de messages en POST et si un fichier a ete envoyer 
 * 
 */
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    /**
     * 
     * Verification le parametre *message* 
     * 
     */
    if(!isset($_POST['message']) || trim($_POST['message']) === ''){
        /**
         * 
         * Renvoyer une reponse en bad request car la donnee utile n'est pas envoyer 
         * 
         */
        echo api_response(400,null,[
            "error_type" => "User Message is not defined"
        ]);
    }

    /**
     * 
     * Verifier le chatID car cela est enormement imoportant 
     * 
     */
    if(!isset($_POST['chat_id']) || trim($_POST['chat_id']) === ''){
        /**
         * 
         * Renvoyer une reponse en bad request car la donnee utile n'est pas envoyer 
         * 
         */
        echo api_response(400,null,[
            "error_type" => "ChatID is not defined"
        ]);
    }

    /**
     * 
     * Verifier si un model a ete chosis 
     * 
     */
    $model = isset($_POST['model']) ? trim($_POST['model']) : null;

    /**
     * 
     * Verifier si un tool a ete selectionner 
     * 
     * 
     */
    $tools = isset($_POST['tools']) ? trim($_POST['tools']) : null;

    /**
     * 
     * Verifier si un fichier a ete envoyer 
     * 
     */
    $fileData = null;
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileData = [
            'name'  => $_FILES['file']['name'],
            'size'  => $_FILES['file']['size'],
            'type'  => $_FILES['file']['type'],
            'tmp'   => $_FILES['file']['tmp_name'],
            'error' => $_FILES['file']['error']
        ];
    }

    /**
     * 
     * Si message a ete envoyer alors on va creer une variable 
     * 
     */
    $message = trim(htmlspecialchars($_POST['message'])) ?? null;
    $chat_id = $_POST['chat_id'] ?? null;

    /**
     * 
     * Creer un tableau des donnees 
     * 
     * 
     */
    $_data = [
        "chat_id" => $chat_id,
        "message" => $message,
        "model" => $model,
        "tools" => $tools,
        "file" => $fileData
    ];

    /**
     * 
     * Executer la fonction 
     * 
     * 
     */
    echo sendMessage($_data);

   

}else{
    /**
     * 
     * Renvoyer une erreur car la methode demander n'est pas autoriser 
     * 
     */
    echo api_response(405,null,null);
}
?>


