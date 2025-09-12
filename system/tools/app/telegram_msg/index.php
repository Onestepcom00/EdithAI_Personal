<?php
// Configuration
$token = '7786093573:AAEV3REIAu3Zm6Ss8r-dfI35FtWy_MA6VlA';
$chat_id = 6136321453;

// Récupération et validation du paramètre 'message'
$message = isset($_GET['message']) ? trim($_GET['message']) : '';

if ($message === '') {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Le paramètre "message" est requis.']);
    exit;
}

// Préparation de la requête Telegram
$url = "https://api.telegram.org/bot{$token}/sendMessage";
$postFields = [
    'chat_id' => $chat_id,
    'text'    => $message
];

// Envoi du message via cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Gestion de la réponse
header('Content-Type: application/json');
if ($httpCode === 200 && $response) {
    echo $response;
} else {
    echo json_encode([
        'error' => 'Erreur lors de l\'envoi du message.',
        'details' => $error ?: $response
    ]);
}
?>
