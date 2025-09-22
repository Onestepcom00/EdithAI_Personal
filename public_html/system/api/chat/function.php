<?php

/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : function.php
 * Decsription : Ce fichier permet de gerer les fonctions de chat dans l'application , il s'agit de ce fichier que nous allons faire la communication avec d'autres outils .
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
 * 
 * Creer la fonction chat 
 * 
 */
function sendMessage($_data){
    /**
     * 
     * Extraire le data 
     * 
     * 
     */
    $chat_id = $_data['chat_id'];
    $message = $_data['message'];
    $model = $_data['model'];
    $tools = $_data['tools'];
    $file = $_data['file'];
    

    /**
     * 
     * Charger et creer les chemins vers les fichiers json necessaires pour le sauvegarde des chats 
     * 
     */
    $_CHAT_FILE = "../" . DATA_DIR . DATABASE; // Chemin vers le fichier de la base des donnees des conversations 
    $_USER_FILE = "../" . DATA_DIR . USER_FILE; // Chemin vers le fichier de la base des utilisateurs
    $_UPLOAD_DIR = "../" . UPLOAD_DIR; // Cgemin vers le dossier 
   
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
    if(!isset($chats[$chat_id])){
        /**
         * 
         * Renvoyer une erreur car le chat id n'est pas trouver 
         * 
         */
        return api_response(404,null,[
            "error_type" => "Chat ID not found"
        ]);
    }

    /**
     * 
     * Verifier si un tool a ete appeler 
     * 
     */
    if(filter_var($tools,FILTER_VALIDATE_BOOLEAN) && $tools !== false){
        /**
         * Operation pour EXECUTER le message de l'utilisateur avec un outil 
         */

        /**
         * 
         * Lancer le MCP 
         * 
         */
        if(startMCP()){
            /**
             * 
             * Recuperer la liste des outils 
             * 
             */
            $_getMCP = getToolManager("../");

            /**
             * 
             * Verifier si une image a ete envoyer 
             * 
             */
            if(isset($file) || $file !== null){
                /**
                 * 
                 * Executer 
                 */
            }else{
                       /**
                         * 
                         * Creer une variable pour le fichier auto prompt 
                         * 
                         */
                        $chatId = str_replace('/','-',$_data['chat_id']);
                        $_autoprompt_file = $chats[$chatId]['auto_prompt_file'];

                        /**
                         * 
                         * Verifier si le titre existe , si non alors en creer 
                         * 
                         * 
                         */
                        $result = isChatMessagesEmpty($chatId, $_CHAT_FILE, $message);
                        if ($result['changed']) {
                            // Ici tu sais que le titre a été modifié
                        // return api_response(200, "Title updated", []);
                        }

                         /**
                         * 
                         * Creer un prompt et le stocker dans le fichier 
                         * 
                         */
                        
                        $r = auto_prompt($message,$_autoprompt_file);

                
                        /**
                         * 
                         * Lire le contenu de l'auto prompt 
                         * 
                         */
                        $_new_prompt = strtoupper(read_autoprompt($_autoprompt_file));

                        /**
                         * 
                         * Extraire les deux dernier message 
                         * 
                         * 
                         */
                        $lastMsg = lastMessage($_data['chat_id'],$_CHAT_FILE);
                        
                    

                        /**
                         * 
                         * Verifier si le message du bot est vide 
                         * 
                         */
                        if($lastMsg['bot'] !== ""){
                            $_lastBot = $lastMsg['bot'];
                        }else{
                            $_lastBot = "";
                        }

                        /**
                         * 
                         * Verifier si le message de l'utilisateur est vide 
                         * 
                         */
                        if($lastMsg['bot'] !== ""){
                            $_lastUser = $lastMsg['user'];
                        }else{
                            $_lastUser = "";
                        }

                        

                        /**
                         * 
                         * 
                         * Creer un prompt principal 
                         * 
                         * 
                         */

                        $_BASE_PROMPT = "
                        [SYSTEM: $_new_prompt, REPONDS A LA DEMANDE DE l'UTILISATEUR EN FONCTION , 
                        DE SA CONVERSATION RECENTE AVEC TOI , VOILA SON MESSAGE RECENT ($_lastUser)
                        ET VOICI TON MESSAGE RECENT ($_lastBot) SI CELUI CI S'Y PRETE A LA NOUVELLE DEMANDE, EN FONCTION DE LA DEMANDE DE l'UTILISATEUR RENVOIE MOI UNIQUEMENT LA ROUTE A EXECUTER SI CELUI CI EXISTE , SI AUCUN OUTIL NE CORRESPOND A LA DEMANDE DE L'UTILISATEUR ALORS RENVOIE UNIQUMENET (No_tool) , ENVOIE UNIQUEMENT LA ROUTE SANS AJOUTER DU TEXTE UNIQMENT LA ROUTE ET LA REQUETE A ENVOYER PAR EXEMPLE (/app/google_search?query=Ma+requete) FAIT EFFORT D'ENCODER CELA EN URL , VOICI LA LISTE DES OUTILS ($_getMCP)  N'INVENTE RIEN ET N'IMAGINE RIEN SI LA DEMANDE CORRESPOND A AUCUN OUTIL EXISTANT ALORS REPONDS (No_tool) BASE TOI QUE SUR LA LISTE DES OUTILS QUE JE TE FOURNIS, FAIT UN CHOIX INTELLIGENT ET LOGIQUE , SI LA DEMANDE CONTIENT PLUSIEURS FICHIERS ALORS CHOISIT LE FICHIER LE PLUS UTILE , SI LA DEMANDE CONTIENT PLUSIEURS ALORS CHOISIT LE PLUS UTILE, PEU IMPORTE LE NOMBRE DE DEMANDE DANS LE MESSAGE RENVOIE UNIQUEMENT UNE SEULE ROUTE]
                        ";

                        /**
                         * 
                         * Discuter avec l'IA 
                         * 
                         */
                        $_getIA = getIA($_BASE_PROMPT,false); // false parce que le message ne contient pas d'image
                        
                        /**
                         * 
                         * Verifier si un outil a ete detecter 
                         * 
                         */
                        if($_getIA !== "No_tool"){
                            /**
                             * 
                             * Preparer l'URL de la requete 
                             * 
                             */
                            $_url = TOOL_MANAGER_URL . $_getIA;

                            /**
                             * 
                             * Utiliser le MCP pour envoyer une requete 
                             * 
                             * 
                             */
                            $_response = MCP_sender($_url,'GET');


                            /** 
                             * 
                             * 
                             * Simuler la comprehension de la reponse 
                             * 
                             * 
                             */

                            $_BASE_PROMPT_2 = "
                            [SYSTEM: TU ES UNE IA , TU AS ETE CREER UNIQUEMENT POUR GENERER UN MESSAGE EN FONCTION DE LA REPONSE JSON TU DOIS DONNER L'IMPRESSION D'AVOIR REEU UNE REPONSE DE L'OUTIL ET TU DOIS DONNER L'IMPRESSION QUE TU AS COMPRIS LA REPONSE DE L'OUTIL , TU NE DOIS PAS DIRE QUE TU AS RECU UNE REPONSE DE L'OUTIL , TU DOIS SIMPLEMENT GENERER UN MESSAGE EN FONCTION DE LA REPONSE JSON QUE TU AS RECUE , TU NE DOIS PAS DIRE QUE TU AS RECU UNE REPONSE DE L'OUTIL , TU DOIS SIMPLEMENT GENERER UN MESSAGE EN FONCTION DE LA REPONSE JSON QUE TU AS RECUE , VOICI LA REPONSE JSON ($_response)  , ET VOICI LA CONVERSATION RECENTE AVEC L'UTILISATEUR VOICI SON MESSAGE RECENT ($_lastUser) ET VOICI TON MESSAGE RECENT ($_lastBot) , NE CONFOND PAS TA CONVERSATION ACTUELLE ET TA CONVERSATION RECENTE REPONDS EN FONCTION DE CE QUI EST MAINTENANT AU CAS LA CONVSERSATION RECENTE A LIEN ALORS TU PEUX T'Y REFERER , DESORMAIS REPONDS COMME SI  C'EST TOI QUI A FOURNIT LA REPONSE JSON COMME SI C'EST TOI QUI A EXECUTER L'OUTIL , LA REPONSE JSON QUE TU VOIS NE PROVIENT PAS DE L'UTILISATEUR MAIS PROVIENT DE LA DEMANDE DE L'UTILISATEUR, TU DOIS ENORMEMENT TENIR COMPTE DU CONTENU DE LA REPONSE JSON SI CELUI CI S'Y PRETE , SOIT ENORMEMENT CONCENTRER SUR LA NOUVELLE DEMANDE DE L'UTILISATEUR ET SUR LA REPONSE JSON QUE TU AS RECUE , TU DOIS ENORMEMENT TENIR COMPTE DU CONTENU DE LA REPONSE JSON SI CELUI CI S'Y PRETE , SOIT ENORMEMENT CONCENTRER SUR LA NOUVELLE DEMANDE DE L'UTILISATEUR ET SUR LA REPONSE JSON QUE TU AS RECUE , CONCENTRE TOI SUR LA NOUVELLE CONVERSATION , C'EST TOI L'OUTIL , LA REPONSE JSON VIENT DE TOI ET FOURNIT UNE REPONSE A L'UTILISATEUR , C'EST TOI QUI A COMMUNIQUER AVEC L'OUTIL ET L'OUTIL T'A RENVOYER UNE REPONSE JSON ALORS TU VAS DEVOIR REPONDRE A L'UTILISATEUR EN FONCTION DE CELA]
                            ";

                            /**
                             * 
                             * Discuter avec l'IA 
                             * 
                             */
                            $_newIA = getIA($_BASE_PROMPT_2,false); // false parce que le message ne contient pas d'image

                            /**
                             * 
                             * Creer la reponse de l'utilisateur 
                             * 
                             */
                            $_userDB = [
                                "role" => "user",
                                "content" => $_data['message'],
                                "attach" => [
                                    "tools" => true,
                                    "file" => null
                                ]
                            ];

                            /**
                             * 
                             * Creer la reponse du bot 
                             * 
                             */
                            $_botDB = [
                                "role" => "bot",
                                "content" => $_newIA,
                                "attach" => [
                                    "tools" => false,
                                    "file" => null
                                ]
                            ];

                            /**
                             * 
                             * Suavegarder les messages 
                             * 
                             */
                            saveChatMessages(
                                $chatId,
                                $_CHAT_FILE, 
                                $_userDB,
                                $_botDB
                            );

                            

                        

                            /**
                             * 
                             * Renvoyer un message de test 
                             * 
                             */
                            return api_response(200,"Chat Successfuly",[
                                "other_info" => [
                                    "user_prompt" => $_data['message'],
                                    "bot_response" => $_newIA, 
                                    "tool_response" => $_response,
                                    "tool_used" => $_getIA,
                                    "tool_url" => $_url,
                                    "auto_prompt" => $_new_prompt
                                ]
                            ]);
                        }

                       
                       


            }
        }else{
            /**
             * 
             * Parce que le systeme qui gere le MCP a eu un probleme coter chargement 
             * 
             */
            return api_response(500,"MCP system error",null);
        }
       
        

    }else{
        /**
         * 
         * Operation pour EXECUTER le message de l'utilisateur sans faire appel a un outil
         * 
         */

        /**
         * 
         * Verifier si un fichier a ete envoyer 
         * 
         */
        if(isset($file) || $file !== null){
            /**
             * 
             * Creer un code speciale pour ca 
             * 
             */

            /**
             * 
             * Verifier l'extension du fichier 
             * 
             * 
             */
            /*if(is_image($file['name'])){
                 /**
                 * 
                 * Puis 
                 */
                /*
                return api_response(200,"Image ...",null);

            }else{*/
                 /**
                 * 
                 * Puis 
                 *//*
                return api_response(200,"Image Is Sent",null);
            }*/

                
        
                /**
                 * 
                 * upload de l'image
                 * 
                 */
                $path = upload($file,$_UPLOAD_DIR);

                /**
                 * 
                 * lancer le fichier sur Tmp file
                 * 
                 */
                $image_url = tmp_file($path);

                /**
                 * 
                 * Generer la reponse de l'IA 
                 * 
                 * 
                 */
                /**
                 * 
                 * Creer une variable pour le fichier auto prompt 
                 * 
                 */
                $chatId = str_replace('/','-',$_data['chat_id']);
                $_autoprompt_file = $chats[$chatId]['auto_prompt_file'];

                /**
                 * 
                 * Verifier si le titre existe , si non alors en creer 
                 * 
                 * 
                 */
                $result = isChatMessagesEmpty($chatId, $_CHAT_FILE, $message);
                if ($result['changed']) {
                    // Ici tu sais que le titre a été modifié
                // return api_response(200, "Title updated", []);
                }


                /**
                 * 
                 * Creer un prompt et le stocker dans le fichier 
                 * 
                 */
                
                $r = auto_prompt($message,$_autoprompt_file);

        
                /**
                 * 
                 * Lire le contenu de l'auto prompt 
                 * 
                 */
                $_new_prompt = strtoupper(read_autoprompt($_autoprompt_file));

                /**
                 * 
                 * Extraire les deux dernier message 
                 * 
                 * 
                 */
                $lastMsg = lastMessage($_data['chat_id'],$_CHAT_FILE);
                
            

                /**
                 * 
                 * Verifier si le message du bot est vide 
                 * 
                 */
                if($lastMsg['bot'] !== ""){
                    $_lastBot = $lastMsg['bot'];
                }else{
                    $_lastBot = "";
                }

                /**
                 * 
                 * Verifier si le message de l'utilisateur est vide 
                 * 
                 */
                if($lastMsg['bot'] !== ""){
                    $_lastUser = $lastMsg['user'];
                }else{
                    $_lastUser = "";
                }

                

                /**
                 * 
                 * 
                 * Creer un prompt principal 
                 * 
                 * 
                 */

                $_BASE_PROMPT = "
                [SYSTEM:$_new_prompt , REPONDS A LA DEMANDE DE l'UTILISATEUR EN FONCTION , 
                DE SA CONVERSATION RECENTE AVEC TOI , VOILA SON MESSAGE RECENT ($_lastUser)
                ET VOICI TON MESSAGE RECENT ($_lastBot), TU N'ES PAS OBLIGER DE RAPPELER A L'UTILISATEUR 
                TOUTE LA CONVERSATION , REPONDS JUSTE A SON MESSAGE COMME DEMANDER, 
                TU DOIS ENORMEMENT TENIR COMPTE DU CONTENU DE L'IMAGE SI CELUI CI S'Y PRETE]
                ";

                /**
                 * 
                 * Discuter avec l'IA 
                 * 
                 */
                $_getIA = getIA($_BASE_PROMPT,true,$image_url); // false parce que le message ne contient pas d'image 

                /**
                 * 
                 * Creer la reponse de l'utilisateur 
                 * 
                 */
                $_userDB = [
                    "role" => "user",
                    "content" => $_data['message'],
                    "attach" => [
                        "tools" => false,
                        "file" => [
                            "name" => $file['name'],
                            "size" => $file['size']
                        ]
                    ]
                ];

                /**
                 * 
                 * Creer la reponse du bot 
                 * 
                 */
                $_botDB = [
                    "role" => "bot",
                    "content" => $_getIA,
                    "attach" => [
                        "tools" => false,
                        "file" => null
                    ]
                ];

                /**
                 * 
                 * Suavegarder les messages 
                 * 
                 */
                saveChatMessages(
                    $chatId,
                    $_CHAT_FILE, 
                    $_userDB,
                    $_botDB
                );

                

            

                /**
                 * 
                 * Renvoyer un message de test 
                 * 
                 */
                return api_response(200,"Chat Successfuly",['e' => $_getIA]);


        }

        /**
         * 
         * Creer une variable pour le fichier auto prompt 
         * 
         */
        $chatId = str_replace('/','-',$_data['chat_id']);
        $_autoprompt_file = $chats[$chatId]['auto_prompt_file'];

        /**
         * 
         * Verifier si le titre existe , si non alors en creer 
         * 
         * 
         */
        $result = isChatMessagesEmpty($chatId, $_CHAT_FILE, $message);
        if ($result['changed']) {
            // Ici tu sais que le titre a été modifié
           // return api_response(200, "Title updated", []);
        }


        /**
         * 
         * Creer un prompt et le stocker dans le fichier 
         * 
         */
        
        $r = auto_prompt($message,$_autoprompt_file);

   
        /**
         * 
         * Lire le contenu de l'auto prompt 
         * 
         */
        $_new_prompt = strtoupper(read_autoprompt($_autoprompt_file));

        /**
         * 
         * Extraire les deux dernier message 
         * 
         * 
         */
        $lastMsg = lastMessage($_data['chat_id'],$_CHAT_FILE);
        
       

        /**
         * 
         * Verifier si le message du bot est vide 
         * 
         */
        if($lastMsg['bot'] !== ""){
            $_lastBot = $lastMsg['bot'];
        }else{
            $_lastBot = "";
        }

        /**
         * 
         * Verifier si le message de l'utilisateur est vide 
         * 
         */
        if($lastMsg['bot'] !== ""){
            $_lastUser = $lastMsg['user'];
        }else{
            $_lastUser = "";
        }

        

        /**
         * 
         * 
         * Creer un prompt principal 
         * 
         * 
         */

        $_BASE_PROMPT = "
        [SYSTEM:$_new_prompt , REPONDS A LA DEMANDE DE l'UTILISATEUR EN FONCTION , 
        DE SA CONVERSATION RECENTE AVEC TOI , VOILA SON MESSAGE RECENT ($_lastUser)
        ET VOICI TON MESSAGE RECENT ($_lastBot), TU N4ES PAS OBLIGER DE RAPPELER A L'UTILISATEUR 
        TOUTE LA CONVERSATION , REPONDS JUSTE A SON MESSAGE COMME DEMANDER]
        ";

        /**
         * 
         * Discuter avec l'IA 
         * 
         */
        $_getIA = getIA($_BASE_PROMPT,false); // false parce que le message ne contient pas d'image 

        /**
         * 
         * Creer la reponse de l'utilisateur 
         * 
         */
        $_userDB = [
            "role" => "user",
            "content" => $_data['message'],
            "attach" => [
                "tools" => false,
                "file" => null
            ]
        ];

        /**
         * 
         * Creer la reponse du bot 
         * 
         */
        $_botDB = [
            "role" => "bot",
            "content" => $_getIA,
            "attach" => [
                "tools" => false,
                "file" => null
            ]
        ];

        /**
         * 
         * Suavegarder les messages 
         * 
         */
        saveChatMessages(
            $chatId,
            $_CHAT_FILE, 
            $_userDB,
            $_botDB
        );

        

      

        /**
         * 
         * Renvoyer un message de test 
         * 
         */
       return api_response(200,"Chat Successfuly",null);


    }
}

/**
 * 
 * Creer une fonction pour upload un fichier 
 * 
 */
function upload($file,$path = "../".UPLOAD_DIR){
    /**
     * 
     * Creer une variable 
     * 
     */
    $fileData = $file;
    $uploadDir = $path;

    /**
     * 
     * Vérifier si un fichier a été envoyé correctement
     *
     */ 
    if (!isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    /**
     * 
     * Créer le dossier d’upload s’il n’existe pas
     * 
     */
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /**
     * 
     * Nettoyer le nom du fichier
     * 
     */
    $fileName = basename($fileData['name']);
    $fileName = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $fileName);

    /**
     * 
     * Générer un chemin complet
     * 
     */
    $targetPath = $uploadDir . uniqid() . "_" . $fileName;

    /**
     * 
     * 
     * Déplacer le fichier temporaire vers le dossier final
     * 
     */
    if (move_uploaded_file($fileData['tmp'], $targetPath)) {
        return $targetPath;
    }

    return null;
}


/**
 * 
 * 
 * Creer une fonction pour GENERER DES REPONSES AVEC L'IA 
 * 
 * 
 */
function getIA($messages,$image = false,$image_link = null){
    /**
     * 
     * Importer les variables globals 
     * 
     */
    global $_PERSONNE;

    /**
     * 
     * Les resssources importantes 
     * 
     */
     $_SYSTEM_MEMORY = "../" . MEMORY_SESSION_DIR . "system.md"; // Le fichier du systeme 
     $_SYSTEM_STAT = "../". MEMORY_SESSION_DIR. "bot_memory.txt"; // Le fichier de la memoire 

     /**
      * Lecture 
      */
     $_system = read_autoprompt($_SYSTEM_MEMORY);
    

     /**
      * 
      * Remplacer 
      *
      */
    
     /**
      *
      * Contexte 
      *
      */
     $_context = $_system; // ." ".$_stat_system; 

     /**
      *
      */

    /**
     * 
     * Acceder au varaible global 
     * 
     */
    global $_MODEL;

    /**
     * 
     * Creer une URL 
     * 
     */
    $_url = getRandKey($_MODEL);
    $_url = $_url."?message=".urlencode($messages)."&system=".urlencode($_system);


    /**
     * 
     * Verifier si image est en true ou false 
     * 
     */
    if($image !== false){
        /**
         * 
         * Alors mettre aussi le lien de l'image 
         * 
         */
        $_url = $_url."&image=".$image_link;
    }

    /**
     * 
     * Preparer la requete 
     * 
     */
    $_r = request($_url);

    /**
     * 
     * verification 
     * 
     */
    if(!empty($_r)){
        /**
         * 
         * Verifier si le message est en success 
         * 
         */
        if($_r['data']['status'] === 'success'){
            /**
             * 
             * Renvoyer le message 
             * 
             */
            return $_r['data']['message'];
        }else{
            /**
             * 
             * Renvoyer juste une erreur 
             * 
             *
             */
            return 'Une erreur est survenue lors de la communication avec le chatbot';
        }
    }

}

/**
 * 
 * Function pour generer des titres de chat avec l'IA 
 * 
 */
function getTitle($_MSG){
    /**
     * 
     * Faire appel aux fonctions global pour les endpoints 
     * 
     */
    global $_MODEL;

    /**
     * 
     * Faire appel a un prompt system 
     * 
     * 
     */
    global $_TITLE_INDICE;
    global $_PROMPT_TITLE;

    /**
     * 
     * Faire une extraction 
     * 
     */
    $_PROMPT = str_replace($_TITLE_INDICE,$_MSG,$_PROMPT_TITLE);

    /**
     * 
     * Une fois l'operation effectuer on va alors lancer une requete vers l'API
     * 
     */
    $_U = getRandKey($_MODEL);

    /**
     * 
     * Creer une URL 
     * 
     */
    $_URL = $_U."?message=".urlencode($_PROMPT)."&system=".urlencode($_PROMPT);

    /**
     * 
     * Lancer une requete 
     * 
     */
    $_R = request($_URL,'GET');

    /**
     * 
     * Recuperer la reponse et faire une verification 
     * 
     */
    if(!empty($_R)){
        /**
         * 
         * Creer une variable
         * 
         * 
         */
        $_data = $_R;

        /**
         * 
         * verifier le status de la requete 
         * 
         * 
         */
        if($_data['data']['status'] !== 'success'){
            /**
             * 
             * Renvoyer un message vide 
             * 
             */
            return 'New Chat';
        }

        /**
         * 
         * Renvoyer juste ke titre 
         * 
         * 
         */
        return $_data['data']['message'];


    }
}

/**
 * 
 * 
 * Fonction pour verifier si la cle *messages* est vide 
 * 
 * 
 */
function isChatMessagesEmpty($chat_id, $jsonPath, $newTitle) {

    /**
     * 
     * Verifier si le fichier existe 
     * 
     * 
     */
    if (!file_exists($jsonPath)) {
        return ["changed" => false, "reason" => "file_not_found"];
    }

    /**
     * 
     * acceder au fichier 
     * 
     * 
     */
    $data = json_decode(file_get_contents($jsonPath), true);

    /**
     * 
     * Verifier si le chatID existe 
     * 
     * 
     */
    if (!isset($data[$chat_id])) {
        return ["changed" => false, "reason" => "chat_not_found"];
    }

    /**
     * 
     * Faire mise en jour 
     * 
     * 
     */
    if (empty($data[$chat_id]['messages']) || !is_array($data[$chat_id]['messages'])) {
        $data[$chat_id]['title'] = getTitle($newTitle);
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return ["changed" => true, "reason" => "title_updated"];
    }

    /**
     * 
     * le message existe deja 
     * 
     */
    return ["changed" => false, "reason" => "messages_exist"];
}

/**
 * 
 * Fonction pour creer un prompt 
 * 
 * 
 */
function auto_prompt($prompt,$_autoprompt_file){
    /**
     * 
     * Importer les variables global 
     * 
     */
    global $_MODEL;

    /**
     * 
     * creer un chemin pour le fichier 
     * 
     */
    $_AUTO_PROMPT = $_autoprompt_file;

   
    /**
     * 
     * La variable pour les prompts 
     * 
     */
    $_prompt = "
    [SYSTEM: TU ES UNE IA , TU AS ETE CREER UNIQUEMENT POUR GENERER DES PROMPTS 
    , EN FONCTION DU MESSAGE DE l'UTILISATEUR CREE UN PROMPT PARFAIT , UN PROMPT 
    QUI VA FACILITER LA TACHE A D'AUTRES IA DE MIEUX COMPRENDRE LE CONTEXTE , GENERE
    UN PROMPT TRES COMPREHENSIBLE POUR LES AUTRES IA , LE PROMPT DOIT ETRE DETAILLER 
    TRES BIEN EXPLIQUER ET TRES CLAIRE POUR PERMETTRE A UNE IA DE MIEUX COMPRENDRE ,
    ALORS JE PENSE QUE TU AS DEJA COMPRIS MON SOUCIS ALORS VOICI LE MESSAGE QUE 
    TU DOIS CONVERTIR EN PROMPT , NE REPONDS PLUS A RIEN RENVOIE UNIQUEMENT LE PROMPT
    , NE MODIFIE PAS LE SENS OU LE CONTEXTE DU MESSAGE , RENDS LE MESSAGE JUSTE TRES COMPREHENSIBLE
    , L'UTILISATEUR DEMANDE  ($prompt) , LE MESSAGE DOIT ETRE SIMILAIRE A CELUI CI (L'utilisateur a besoin 
    que tu puisse ameliorer son code et que tu reponde a sa question) , C'EST PAS A TOI QU'ON s'ADRESSE]
    ";

    /**
     * 
     * Creer une URL 
     * 
     */
    $_url = getRandKey($_MODEL);
    $_url = $_url."?message=".urlencode($_prompt)."&system=".urlencode($_prompt);

    /**
     * 
     * Lancer une requete 
     * 
     */
    $_r = request($_url,"GET");

    /**
     * 
     * Faire verif
     * 
     */
    
    if(!empty($_r)){
        /**
         * 
         * Creer une variable
         * 
         * 
         */
        $_data = $_r;

        /**
         * 
         * verifier le status de la requete 
         * 
         * 
         */
        if($_data['data']['status'] == 'success'){
            /**
             * 
             * Enregistrer la valeur dans le fichier 
             * 
             */
           $v =  SaveToTxt($_data['data']['message'],$_AUTO_PROMPT);

            /**
             * 
             * Renvoyer une reponse en true 
             * 
             */
            return $v;//$_data['data']['message'];
        }

        /**
         * 
         * Renvoyer un false 
         * 
         */
        return false;

    }
    /**
     * 
     * Renvoyer un false 
     * 
     */
    return false;
}

/**
 * 
 * 
 * Creer une fonction de sauvegarde 
 * 
 * 
 */
function SaveToTxt($data,$file){
    /**
     * 
     * Verifier si le fichier existe 
     * 
     * 
     */
    if(file_exists($file)){
        /**
         * 
         * Supprimer le fichier 
         * 
         */
        unlink($file);

        /**
         * 
         * Si le fichier existe alors on ouvre le fichier puis on ecit dessus  
         * 
         */
         $fp = fopen($file,'a');
         fwrite($fp,$data);
         fclose($fp);

         /**
          * Renvoyer un true
          */
         return true;
    }else{
        /**
         * 
         * Renvoyer un false 
         * 
         */
        return "File not found";
    }

   
}

/**
 * 
 * Lire l'auto prompt 
 * 
 * 
 */
function read_autoprompt($file){
    /**
     * 
     * Verifier l'existance du fichier auto prompt 
     * 
     */
    if(file_exists($file)){
        /**
         * 
         * Lire le fichier et recuperer le donnees 
         * 
         */
        $fp = fopen($file,'r');
        $_read = fread($fp,filesize($file));
        fclose($fp);

        return $_read;
    }
}

/**
 * 
 * Function pour extraire uniquement les deux dernier message 
 * 
 */
function lastMessage($chat_id, $jsonPath) {
    /**
     * 
     * Vérifier si le fichier existe
     * 
     */
    if (!file_exists($jsonPath)) {
        return null; // ou []
    }

    /**
     * 
     * Charger le JSON
     * 
     */
    $data = json_decode(file_get_contents($jsonPath), true);

    /**
     * 
     * Vérifier que le chat existe
     * 
     */
    if (!isset($data[$chat_id]['messages']) || !is_array($data[$chat_id]['messages'])) {
        return null; // ou []
    }

    /**
     * 
     * Récupérer les messages
     * 
     */
    $messages = $data[$chat_id]['messages'];

    /**
     * 
     * Prendre les 2 derniers messages
     * 
     */
    $lastTwo = array_slice($messages, -2);

    /**
     * 
     * Initialiser le tableau résultat
     * 
     */
    $result = [
        "user" => null,
        "bot"  => null
    ];

    /**
     * 
     * Parcourir les 2 derniers messages et remplir le tableau
     * 
     */
    foreach ($lastTwo as $msg) {
        if ($msg['role'] === "user") {
            $result['user'] = $msg['content'];
        } elseif ($msg['role'] === "bot") {
            $result['bot'] = $msg['content'];
        }
    }

    return $result;
}

/**
 * Sauvegarder le message de l'utilisateur et la réponse du bot
 *
 * @param string $chat_id   ID du chat (ex: "chat-68979e8d7672e309486194")
 * @param string $jsonPath  Chemin vers le fichier JSON des chats
 * @param array  $userMsg   Tableau du message user (role, content, attach)
 * @param array  $botMsg    Tableau du message bot (role, content, attach)
 * @return bool
 */
function saveChatMessages($chat_id, $jsonPath, $userMsg, $botMsg) {
    /**
     * 
     * Vérifier si le fichier existe
     * 
     */
    if (!file_exists($jsonPath)) {
        return false;
    }

    /**
     * 
     * Charger le contenu JSON
     * 
     */
    $data = json_decode(file_get_contents($jsonPath), true);

    /**
     * 
     * 
     * Vérifier que le chat existe
     * 
     */
    if (!isset($data[$chat_id])) {
        return false;
    }

    /**
     * 
     * Vérifier si "messages" existe et est un tableau
     * 
     */
    if (!isset($data[$chat_id]['messages']) || !is_array($data[$chat_id]['messages'])) {
        $data[$chat_id]['messages'] = [];
    }

    /**
     * 
     * Ajouter les messages
     * 
     */
    $data[$chat_id]['messages'][] = $userMsg;
    $data[$chat_id]['messages'][] = $botMsg;

    /**
     * 
     * 
     * Sauvegarder dans le fichier
     * 
     */
    $saved = file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    return $saved !== false;
}

/**
 * 
 * Verifier les extensions de images 
 * 
 */
function is_image($filename){
    /**
     * 
     * Creer un tableau d'extension image 
     * 
     */
    $_ext = ["png","jpg","jpeg","gif"];

    /**
     * 
     * Recuperer l'extension 
     * 
     */
    $_extension = strtolower(pathinfo($filename,PATHINFO_EXTENSION));

    /**
     * 
     * Verifier l'extension 
     * 
     */
    in_array($_extension,$_ext);
}

/**
 * 
 * Fonction pour upload sur  tmp_file 
 * 
 * 
 */

function tmp_file($filePath) {

    /**
     * 
     * Verifier si le fichier existe 
     */
    if (!file_exists($filePath)) {
        return ["status" => false, "message" => "Fichier introuvable"];
    }

    /**
     * 
     * Preparer la requete cURL 
     * 
     * 
     */
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://tmpfiles.org/api/v1/upload");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'file' => new CURLFile($filePath)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    /**
     * 
     * Executer la requete 
     * 
     */
    $response = curl_exec($ch);

    /**
     * 
     * Verifier les erreurs 
     * 
     */
    if (curl_errno($ch)) {
        return ["status" => false, "message" => curl_error($ch)];
    }

    /**
     * 
     * Fermer la connexion 
     * 
     * 
     */
    curl_close($ch);

    /**
     * 
     * Decoder la reponse JSON 
     * 
     * 
     */
    $data = json_decode($response, true);

    /**
     * 
     * Verifier le status 
     * 
     * 
     */
    if (!isset($data['status']) || $data['status'] !== "success") {
        return ["status" => false, "message" => "Erreur dans la réponse API"];
    }

    /**
     * 
     * URL renvoyée
     * 
     */
    $originalUrl = $data['data']['url'];

    /**
     * 
     * Exemple : http://tmpfiles.org/10440939/image.png
     * 
     */
    /**
     * 
     * On ajoute /dl/ après le domaine
     * 
     */
    $parts = explode("/", $originalUrl); // ["http:", "", "tmpfiles.org", "10440939", "image.png"]

    /**
     * 
     * Extraire le format lisable 
     * 
     */
    if (count($parts) >= 5) {
        $directUrl = $parts[0] . "//" . $parts[2] . "/dl/" . $parts[3] . "/" . $parts[4];
    } else {
        return ["status" => false, "message" => "URL inattendue"];
    }

    /**
     * 
     * Creer une reponse 
     * 
     * 
     */
    /*return [
        "status" => true,
        "original_url" => $originalUrl,
        "direct_url" => $directUrl
    ];*/
    return $directUrl;
}


?>