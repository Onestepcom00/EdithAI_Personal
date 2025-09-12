<?php
/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : loader.php
 * Decsription : Ce fichier va contenir toute les fonctions necessaires pour le bon fonctionnement de l'app coter client .
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
 * Creer une fonction qui renvoie le username en court 
 * 
 */
function shortName($name) {
    /**
     * 
     * Supprime les espaces superflus et sépare les mots
     * 
     */
    $words = preg_split('/\s+/', trim($name));
    $initials = '';
    
    foreach ($words as $word) {
        /**
         * 
         * 
         * Prend la première lettre de chaque mot et la met en majuscule
         * 
         * 
         */
        $initials .= strtoupper(substr($word, 0, 1));
    }
    
    /**
     * 
     * Retourne les initiales (maximum 2 caractères si plus d'un mot)
     * 
     */
    return (count($words) > 1) ? substr($initials, 0, 2) : substr($initials, 0, 1);
}
?>