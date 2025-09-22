<?php
header('Content-Type: application/json');

// Vérifier si le paramètre 'video_name' est présent
if (!isset($_GET['video_name']) || empty(trim($_GET['video_name']))) {
    echo json_encode([
        'error' => 'Le paramètre "video_name" est requis.'
    ]);
    exit;
}

$videoName = trim($_GET['video_name']);

// Générer un lien YouTube fictif basé sur le nom de la vidéo
// (Remplacez cette logique par une recherche réelle si besoin)
$youtubeBaseUrl = 'https://www.youtube.com/watch?v=';
$videoId = substr(md5($videoName), 0, 11); // Génère un ID fictif

$response = [
    'video_name' => $videoName,
    'youtube_link' => $youtubeBaseUrl . $videoId
];

echo json_encode($response);