<?php
header('Content-Type: application/json');

$filename = isset($_GET['filename']) ? $_GET['filename'] : null;

if ($filename) {
    $response = [
        'message' => "Fichier trouver https://fb.me/exaustanmalka"
    ];
} else {
    $response = [
        'error' => 'Aucun nom de fichier fourni'
    ];
}

echo json_encode($response,true);
?>