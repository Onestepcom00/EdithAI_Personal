<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : loader.php
 * Decsription : Ce fichier va contenir toute les functions utiles pour le fonctionnement de l'API , ses fonctions ne vont pas dependre des differentes routes , c'est des fonctions accessibles a toutes les routes de l'API.
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
 * Function pour afficher la reponses de l'API 
 * 
 */
function api_response($status,$message = null,$data = null){
   
    /**
     * 
     * Creer un tableau des reponses de l'API , chaque status equivaut a une reponses de l'API 
     * 
     */
    $_response = [
        200 => ["success",$message ?? "Request was successful."],
        404 => ["error","The requested resource was not found."],
        400 => ["error","Bad request. Please check your input."],
        500 => ["error","Internal server error. Please try again later."],
        401 => ["error","Unauthorized access. Please provide valid credentials."],
        403 => ["error","Forbidden access. You do not have permission to access this resource."],
        422 => ["error","Unprocessable entity. The request was well-formed but was unable to be followed due to semantic errors."],
        429 => ["error","Too many requests. Please slow down your request rate."],
        503 => ["error","Service unavailable. The server is currently unable to handle the request due to temporary overload or maintenance."]
    ];

    /**
     * 
     * verifier su le status est valide , et creer un tableau de reponse correspondant a la reponse 
     * 
     */
    if(array_key_exists($status,$_response)){
        $response = [
            "status" => $_response[$status][0],
            "message" => $_response[$status][1]
        ];
    }
    
    /**
     * 
     * Verifier si les donnees *data* ne sont pas null et les ajouter a la reponse
     * 
     */
    if($data !== null){
        /**
         * Ajouter les donnees a la reponse , sachant bien que *data* sera un tableau 
         * 
         */
        $response += $data;
    }

    /**
     * 
     * Mettre le bon status de la reponse au header 
     * 
     */
    http_response_code($status);
    /**
     * 
     * Renvoyer la reponse de l'API au format JSON 
     * 
     */
    return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit; // stopper l'execution du script
}


/**
 * 
 * Creer une fonction pour les sauvegarde des chats dans un fichier JSON 
 * 
 */
function json_save($file,$data){
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), LOCK_EX);
}

/**
 * 
 * Creer une fonction pour lire le contenu d'un fichier JSON 
 * 
 */
function json_read($file){
    /**
     * 
     * Le code va verifier si le fichier existe et s'il est lisible 
     * 
     */
    if(!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}
/**
 * 
 * Fonction pour ajouter un data 
 * 
 */
function json_add($file, $id, $entry){
    /**
     * 
     * Faire appel aux fonctions de base (Lecture et Sauvegarde)
     * 
     */
    $data = json_read($file);
    $data[$id] = $entry;  
    json_save($file, $data);
}

/**
 * 
 * Creer une fonction pour update les donnees d'un fichier JSON 
 * 
 */
function json_update($file, $index, $newData){
    /**
     * 
     * Faire appel aux fonctions de base (Lecture et Sauvegarde)
     * 
     */
    $data = json_read($file);
    if(isset($data[$index])){
        $data[$index] = $newData;
        json_save($file, $data);
        return true;
    }
    return false;
}


/**
 * 
 * Creer une fonction pour l'envoie des requetes avec cURL avec toute les options necessaires 
 * 
 * 
 */
function request($url, $method = 'GET', $data = null){
    /**
     * 
     * Initailiser la requete via l'url de l'entree 
     * 
     */
    $ch = curl_init($url);

    /**
     * 
     * Lancer les options curl en mode tableau 
     * 
     */
    $options = [
        CURLOPT_RETURNTRANSFER => true, // Pour retourner la reponse au lieu de l'afficher
        CURLOPT_SSL_VERIFYPEER => VERIFY_SSL, // Bloquer la verification du certificat SSL
        CURLOPT_USERAGENT => APP_USER_AGENT, // Utiliser l'agent utilisateur de l'application
        CURLOPT_FOLLOWLOCATION => FOLLOW_LOCATION, // Suivre les redirections
        CURLOPT_TIMEOUT => TIMEOUT, // Temps d'attente max pour la requete
        CURLOPT_MAXREDIRS => NBR_MAX_REDIRECT, // Nombre max de redirection
    ];

    /**
     * 
     * Verifier le type de la requete et ajouter les options necessaires en fonction de la methode 
     * 
     */
    if ($method === 'POST') {
        /**
         * 
         * On va ajouter la methode POST et autoriser l'envoie des donnees 
         * 
         */
        $options[CURLOPT_POST] = true; // Indiquer que c'est une requete POST
        if ($data) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data); // Ajouter les donnees a envoyer
        }
    }
    elseif($method === 'GET'){
        /**
         * 
         * On va ajouter la methode GET et autoriser l'envoie des donnees 
         * 
         */
        $options[CURLOPT_HTTPGET] = true; // Indiquer que c'est une requete GET
        if ($data) {
            $url .= '?' . http_build_query($data); // Ajouter les donnees a l'URL
            curl_setopt($ch, CURLOPT_URL, $url); // Mettre a jour l'URL avec les parametres
        }
    }
    else{
        /**
         * 
         * Si la methode n'est pas reconnue, on va lancer une exception 
         * 
         */
       return ["success" => false]; // Retourner false si la methode n'est pas reconnue
    }

    /**
     * 
     * Une fois les options ajouter , on va les appliquer a la requete cURL 
     * 
     */
    curl_setopt_array($ch, $options);

    /**
     * 
     * Executer la requete cURL et recuperer la reponse 
     * 
     */
    $response = curl_exec($ch);

    /**
     * 
     * Verifier si il y a une erreur dans la requete cURL 
     * 
     */
    if (curl_errno($ch)) {
        /**
         * 
         * Si il y a une erreur, on va retourner false
         * 
         */
        $error = curl_error($ch);
        curl_close($ch);
        return ["success" => false, "error" => $error];
    }

    /**
     * 
     * Fermer la session cURL et retourner la reponse 
     * 
     */
    curl_close($ch);
    return ["success" => true, "data" => json_decode($response, true)];

}
/**
 * 
 * Fonction pour choisir aleatoirement une valeur dans un tableau 
 * 
 */
function getRandKey(array $models) {
    /**
     * 
     * Vérifie si le tableau n'est pas vide
     * 
     */
    if (empty($models)) {
        throw new InvalidArgumentException("Le tableau des modèles est vide");
    }
    
    /**
     * 
     * Choisit une clé aléatoire dans le tableau
     * 
     */
    $randomKey = array_rand($models);
    
    /**
     * 
     * Retourne l'URL correspondante
     * 
     */
    return $models[$randomKey];
}

/**
 * 
 * Fonction pour require automatiquement le MCP 
 * 
 */
function startMCP(){
    /**
     * 
     * Faire appel aux varaibles globals 
     * 
     */
    global $_MCP;

    /**
     * 
     * Creer les variables pour les endpoints des MCP 
     * 
     * 
     */
    $mcp_config =  "../". $_MCP['mcp_manager'] . "config.php";
    $mcp_app = "../". $_MCP['mcp_manager'] . "system.php";


    /**
     * 
     * Verifier l'existence des fichiers MCP 
     * 
     */
    if(file_exists($mcp_config) && file_exists($mcp_app)){
        /**
         * 
         * Les requires 
         * 
         */
        require_once $mcp_config;
        require_once $mcp_app;

        /**
         * 
         * Reenvoyer un true 
         * 
         */
        return true;
    }else{
        /**
         * 
         * Reenvoyer un false 
         * 
         */
        return false;
    }
}

/**
 * 
 * Creer une fonction speciale pour envoyer les requetes vers le tool 
 * 
 */
function MCP_sender($url, $method = 'GET',$data = null){
    /**
     * 
     * Initailiser la requete via l'url de l'entree 
     * 
     */
    $ch = curl_init($url);

    /**
     * 
     * Lancer les options curl en mode tableau 
     * 
     */
    $options = [
        CURLOPT_RETURNTRANSFER => true, // Pour retourner la reponse au lieu de l'afficher
        CURLOPT_SSL_VERIFYPEER => VERIFY_SSL, // Bloquer la verification du certificat SSL
        CURLOPT_USERAGENT => APP_USER_AGENT, // Utiliser l'agent utilisateur de l'application
        CURLOPT_FOLLOWLOCATION => FOLLOW_LOCATION, // Suivre les redirections
        CURLOPT_TIMEOUT => TIMEOUT, // Temps d'attente max pour la requete
        CURLOPT_MAXREDIRS => NBR_MAX_REDIRECT, // Nombre max de redirection
    ];

    /**
     * 
     * Verifier le type de la requete et ajouter les options necessaires en fonction de la methode 
     * 
     */
    if ($method === 'POST') {
        /**
         * 
         * On va ajouter la methode POST et autoriser l'envoie des donnees 
         * 
         */
        $options[CURLOPT_POST] = true; // Indiquer que c'est une requete POST
        if ($data) {
            $options[CURLOPT_POSTFIELDS] = http_build_query($data); // Ajouter les donnees a envoyer
        }
    }
    elseif($method === 'GET'){
        /**
         * 
         * On va ajouter la methode GET et autoriser l'envoie des donnees 
         * 
         */
        $options[CURLOPT_HTTPGET] = true; // Indiquer que c'est une requete GET
        if ($data) {
            $url .= '?' . http_build_query($data); // Ajouter les donnees a l'URL
            curl_setopt($ch, CURLOPT_URL, $url); // Mettre a jour l'URL avec les parametres
        }
    }
    else{
        /**
         * 
         * Si la methode n'est pas reconnue, on va lancer une exception 
         * 
         */
       //return ["success" => false]; // Retourner false si la methode n'est pas reconnue
       return false;
    }

    /**
     * 
     * Une fois les options ajouter , on va les appliquer a la requete cURL 
     * 
     */
    curl_setopt_array($ch, $options);

    /**
     * 
     * Executer la requete cURL et recuperer la reponse 
     * 
     */
    $response = curl_exec($ch);

    /**
     * 
     * Verifier si il y a une erreur dans la requete cURL 
     * 
     */
    if (curl_errno($ch)) {
        /**
         * 
         * Si il y a une erreur, on va retourner false
         * 
         */
        $error = curl_error($ch);
        curl_close($ch);
       // return ["success" => false, "error" => $error];
       return false;
    }

    /**
     * 
     * Fermer la session cURL et retourner la reponse 
     * 
     */
    curl_close($ch);
   // return ["success" => true, "data" => json_decode($response, true)];
    return json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

}


?>