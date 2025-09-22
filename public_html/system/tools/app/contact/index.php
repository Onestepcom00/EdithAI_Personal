<?php
/**
 * Contact Tools 
 * Permet d'accéder à une liste des contacts par nom
 */

if (isset($_GET['name'])) {
    // On nettoie et on désencode les entités HTML
    $name = html_entity_decode($_GET['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $name = trim($name);

    $contacts = [
        "Exaustan Malka" => [
            "number" => "+243977482151",
            "email" => "exaustanmalka@icloud.com"
        ],
        "Ma Mere" => [
            "number" => "+243851771060",
            "email" => null
        ],
        "Miss P" => [
            "number" => "+243827087952",
            "email" => null
        ],
        "El Jonas" => [
            "number" => "",
            "email" => ""
        ]
    ];

    $found = false;

    foreach ($contacts as $contactName => $contactInfo) {
        // Comparaison insensible à la casse
        if (strcasecmp($contactName, $name) === 0) {
            $found = true;
            $response = [
                "name" => $contactName,
                "number" => $contactInfo['number'],
                "email" => $contactInfo['email']
            ];
            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;
        }
    }

    if (!$found) {
        $response = [
            "error" => "Contact not found"
        ];
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}
