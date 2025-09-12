<?php
/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : call.php
 * Decsription : Fichier d'interface d'appel vocal avec l'assistant IA
 * Date de creation : 02/08/2025
 * Date de modification : 02/08/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * Initialiser la session 
 */
session_start();

/**
 * Installer les fichiers des configurations de base 
 */
require_once 'config.php'; // Le fichier qui gere les config de l'app 
require_once 'loader.php'; // Le fichier qui gere le fonction du chargement de l'app 

/**
 * Verifier si l'id du chat existe 
 */
if(isset($_GET['id'])){
    /**
     * Recuperer la valeur de l'id du chat 
     */
    $_chat_id = $_GET['id'] ?? null; 

    /**
     * Recuperer une URL pour verifier le chat 
     */
    $_url = API_VERIFY_CHAT . $_chat_id;

    /**
     * Lancer une requete cURL vers l'APi pour verifier si le chat existe 
     */
    $_response = request($_url,'GET');

    /**
     * Verifier si la reponse est vide ou non 
     */
    if(!empty($_response)){
        /**
         * Convertir la reponse en tableau associatif 
         */
        $_data = $_response;

        /**
         * Verifier si le chat existe ou non 
         */
        if($_data['data']['status'] !== 'success'){

            /**
             * Si le chat n'existe pas , on va rediriger vers la page d'accueil 
             */
            header('Location:' . APP_URL);
            exit();
        }

        /**
         * On va creer une session pour stocker l'id du chat et d'autres petites informations 
         */
        $_SESSION['data'] = [
            "chat_id" => $_data['data']['id'],
            "title" => $_data['data']['title']
        ];


}
}

/**
 * Verifier si la session existe ou non 
 */
if(!isset($_SESSION['data']) || !isset($_SESSION['data']['chat_id'])){
    /**
     * Si la session n'existe pas , on va rediriger vers la page d'accueil 
     */
    header('Location:' . APP_URL);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- UTF-8 Character Set --> 
    <meta charset="UTF-8">

    <!-- Viewport Meta Tag -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title --> 
    <title>EdithAI - <?=$_SESSION['data']['title'];?></title>

    <!-- Script Installation -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <!-- Preconnect for fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="assets/image/logo-edithai.ico">

    <!-- Icons --> 
    <link rel="icon" href="assets/image/logo-edithai.ico" type="image/x-icon">
    <link rel="shortcut icon" href="assets/image/logo-edithai.ico" type="image/x-icon">

    <!-- Couleur de la barre d'adresse sur mobile -->
    <meta name="theme-color" content="#7c3aed">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="msapplication-TileColor" content="#7c3aed">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gray: {
                            950: '#0a0a0b',
                            925: '#111113',
                            900: '#16171a',
                            875: '#1c1d21',
                            850: '#212226',
                            825: '#26272b',
                            800: '#2b2c31',
                            750: '#35363a',
                            700: '#3f4045',
                            650: '#49494f'
                        },
                        purple: {
                            950: '#1a0b2e',
                            900: '#2d1b4e',
                            800: '#3730a3',
                            700: '#4338ca',
                            600: '#5b21b6',
                            500: '#7c3aed',
                            400: '#8b5cf6',
                            300: '#a78bfa',
                            200: '#c4b5fd',
                            100: '#ddd6fe'
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        .glass {
            background: rgba(20, 21, 25, 0.85);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(139, 92, 246, 0.15);
        }

        .speaking-animation {
            position: relative;
        }

        .speaking-animation::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 9999px;
            border: 2px solid rgba(34, 197, 94, 0.6);
            animation: pulse-speak 1.5s ease-in-out infinite;
            opacity: 0;
        }

        @keyframes pulse-speak {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.5;
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }
            50% { 
                transform: scale(1.05);
                opacity: 1;
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }
        }

        .speaking-rings {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 9999px;
            border: 2px solid rgba(34, 197, 94, 0.4);
            animation: ripple 2s linear infinite;
            opacity: 0;
        }

        @keyframes ripple {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .voice-visualizer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2px;
            height: 30px;
            margin-top: 1rem;
        }

        .voice-bar {
            width: 3px;
            background: linear-gradient(to top, #22c55e, #16a34a);
            border-radius: 2px;
            animation: voice-wave 1.5s ease-in-out infinite;
        }

        .voice-bar:nth-child(1) { animation-delay: 0s; }
        .voice-bar:nth-child(2) { animation-delay: 0.1s; }
        .voice-bar:nth-child(3) { animation-delay: 0.2s; }
        .voice-bar:nth-child(4) { animation-delay: 0.3s; }
        .voice-bar:nth-child(5) { animation-delay: 0.4s; }

        @keyframes voice-wave {
            0%, 100% { height: 10px; }
            50% { height: 20px; }
        }

        .call-time {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Modal styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 50;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background: #1c1d21;
            border-radius: 16px;
            padding: 24px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(139, 92, 246, 0.2);
        }
        
        /* Push to talk animation */
        .talking-indicator {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 9999px;
            background-color: rgba(34, 197, 94, 0.4);
            opacity: 0;
            animation: none;
        }
        
        .talking-indicator.active {
            animation: pulse-push 1s ease-in-out infinite;
        }
        
        @keyframes pulse-push {
            0%, 100% { 
                transform: scale(1);
                opacity: 0.7;
            }
            50% { 
                transform: scale(1.1);
                opacity: 0.4;
            }
        }

        /* Console styles */
        .console-container {
            max-height: 200px;
            overflow-y: auto;
            font-family: monospace;
            font-size: 12px;
            padding: 10px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            margin-top: 10px;
        }

        .console-text {
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .capture-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased">
    <!-- Main Call Interface -->
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <div class="p-6 border-b border-gray-850">
            <div class="flex items-center justify-between max-w-6xl mx-auto">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                        <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-100"><?=$_TEXT['sidebar']['name'];?></h1>
                        <div class="flex items-center space-x-2 mt-1">
                            <div id="status-indicator" class="h-2 w-2 bg-green-500 rounded-full pulse"></div>
                            <span id="status-text" class="text-sm text-green-400">Connected</span>
                            <span class="text-sm text-gray-500">•</span>
                            <span class="text-sm text-gray-400 call-time" id="callTimer">00:00</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Screen Share Button -->
                    <button class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition-all text-white font-medium flex items-center space-x-2" id="screenShareBtn">
                        <i data-lucide="monitor" class="w-4 h-4"></i>
                        <span><?=$_TEXT['call']['screen_share'];?></span>
                    </button>
                    <button class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg transition-all text-white font-medium flex items-center space-x-2" id="endCallBtn">
                        <i data-lucide="phone-off" class="w-4 h-4"></i>
                        <span><?=$_TEXT['call']['end_call'];?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Call Area -->
        <div class="flex-1 p-8">
            <div class="max-w-4xl mx-auto">
                <!-- Participants Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- AI Assistant -->
                    <div class="glass rounded-2xl p-8 text-center relative">
                        <div class="mb-6">
                            <div class="w-32 h-32 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-full flex items-center justify-center mx-auto shadow-2xl relative speaking-animation" id="aiAvatar">
                                <i data-lucide="sparkles" class="w-12 h-12 text-white"></i>
                                <!-- Speaking rings -->
                                <div class="speaking-rings" id="aiSpeakingRings"></div>
                            </div>
                            <!-- Voice visualizer for AI -->
                            <div class="voice-visualizer opacity-0" id="aiVoiceVisualizer">
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-100 mb-2">AI Assistant</h3>
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-2 h-2 bg-gray-400 rounded-full" id="aiMicStatus"></div>
                            <span class="text-sm text-gray-400" id="aiStatus">Listening...</span>
                        </div>
                    </div>

                    <!-- User -->
                    <div class="glass rounded-2xl p-8 text-center relative">
                        <div class="mb-6">
                            <div class="w-32 h-32 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center mx-auto shadow-2xl relative speaking-animation" id="userAvatar">
                                <span class="text-2xl font-semibold text-white"><?= shortName($_TEXT['user']['name']); ?></span>
                                <!-- Speaking rings -->
                                <div class="speaking-rings" id="userSpeakingRings"></div>
                            </div>
                            <!-- Voice visualizer for User -->
                            <div class="voice-visualizer opacity-0" id="userVoiceVisualizer">
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                                <div class="voice-bar"></div>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-100 mb-2">You</h3>
                        <div class="flex items-center justify-center space-x-2">
                            <div class="w-2 h-2 bg-green-400 rounded-full" id="userMicStatus"></div>
                            <span class="text-sm text-gray-400" id="userStatus">Ready to speak</span>
                        </div>
                    </div>
                </div>

                <!-- Call Controls -->
                <div class="glass rounded-2xl p-6">
                    <div class="flex items-center justify-center space-x-6">
                        <!-- Mute Button -->
                        <button class="w-14 h-14 bg-gray-800/60 hover:bg-gray-700/60 rounded-full flex items-center justify-center transition-all group" id="muteBtn" title="Mute microphone">
                            <i data-lucide="mic" class="w-6 h-6 text-gray-300 group-hover:text-white"></i>
                        </button>

                        <!-- Push to Talk Button -->
                        <button class="w-16 h-16 bg-green-600 hover:bg-green-500 rounded-full flex items-center justify-center transition-all shadow-lg hover:shadow-green-500/25 relative" id="pushToTalkBtn" title="Hold to speak">
                            <i data-lucide="mic" class="w-7 h-7 text-white"></i>
                            <div class="talking-indicator" id="talkingIndicator"></div>
                        </button>

                        <!-- Speaker Button -->
                        <button class="w-14 h-14 bg-gray-800/60 hover:bg-gray-700/60 rounded-full flex items-center justify-center transition-all group" id="speakerBtn" title="Speaker settings">
                            <i data-lucide="volume-2" class="w-6 h-6 text-gray-300 group-hover:text-white"></i>
                        </button>

                        <!-- Voice Recognition Controls -->
                        <button id="start-recognition-button" class="w-14 h-14 bg-blue-600 hover:bg-blue-500 rounded-full flex items-center justify-center transition-all pulse" title="Démarrer reconnaissance vocale">
                            <i data-lucide="mic" class="w-6 h-6 text-white"></i>
                        </button>
                        
                        <button id="stop-recognition-button" class="w-14 h-14 bg-red-600 hover:bg-red-500 rounded-full flex items-center justify-center transition-all hidden" title="Arrêter reconnaissance vocale">
                            <i data-lucide="square" class="w-6 h-6 text-white"></i>
                        </button>
                        
                        <button id="stop-voice-button" class="w-14 h-14 bg-yellow-600 hover:bg-yellow-500 rounded-full flex items-center justify-center transition-all hidden" title="Arrêter la voix">
                            <i data-lucide="square" class="w-6 h-6 text-white"></i>
                        </button>
                    </div>
                    
                    <!-- Instructions -->
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-400">Hold the green button to speak, release to let AI respond</p>
                        <div class="flex items-center justify-center space-x-4 mt-3 text-xs text-gray-500">
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                                <span>Speaking</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                <span>Listening</span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-blue-400" id="recognition-status">Reconnaissance vocale: en attente</div>
                    </div>
                </div>

                <!-- Live Transcript -->
                <div class="glass rounded-2xl p-6 mt-6">
                    <h4 class="text-lg font-medium text-gray-100 mb-4 flex items-center space-x-2">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span>Live Transcript</span>
                    </h4>
                    <div class="space-y-3 max-h-32 overflow-y-auto" id="transcript">
                        <div class="text-sm text-gray-300">
                            <span class="text-purple-400 font-medium">AI:</span> 
                            <span>Hello! I'm ready to have a voice conversation with you. What would you like to discuss?</span>
                        </div>
                    </div>
                </div>

                <!-- Console de débogage -->
                <div class="glass rounded-2xl p-6 mt-6">
                    <h4 class="text-lg font-medium text-gray-100 mb-4 flex items-center space-x-2">
                        <i data-lucide="terminal" class="w-5 h-5"></i>
                        <span>Console</span>
                    </h4>
                    <div class="console-container" id="console"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay de capture d'écran -->
    <div id="capture-overlay" class="capture-overlay hidden">
        <div class="bg-gray-800 p-4 rounded-lg flex items-center">
            <i data-lucide="camera" class="w-6 h-6 text-white mr-2"></i>
            <span class="text-white">Capture d'écran en cours...</span>
        </div>
    </div>

    <!-- Modal for Screen Share -->
    <div class="modal-overlay" id="screenShareModal">
        <div class="modal-content">
            <div class="flex items-center justify-center mb-4">
                <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center">
                    <i data-lucide="monitor" class="w-6 h-6 text-white"></i>
                </div>
            </div>
            <h3 class="text-xl font-semibold text-gray-100 text-center mb-2">Screen Sharing</h3>
            <p class="text-gray-400 text-center mb-6">This feature will be available soon. We're working hard to implement screen sharing functionality.</p>
            <div class="flex justify-center">
                <button class="px-6 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg text-white font-medium" id="modalCloseBtn">Got it</button>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Variables globales pour la reconnaissance vocale
        let screenStream, captureInterval;
        let recognition;
        let isSpeaking = false, speechQueue = [];
        let isListening = false;
        let streamPaused = false;
        let conversationHistory = []; // ⚡ Historique complet
        let recognitionActive = false;
        let recognitionStarting = false; // Nouveau flag pour éviter les démarrages multiples

        // Call timer
        let callStartTime = Date.now();
        let callTimer;

        function updateCallTimer() {
            const elapsed = Date.now() - callStartTime;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            document.getElementById('callTimer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Start call timer
        callTimer = setInterval(updateCallTimer, 1000);

        // Modal functionality
        const screenShareBtn = document.getElementById('screenShareBtn');
        const screenShareModal = document.getElementById('screenShareModal');
        const modalCloseBtn = document.getElementById('modalCloseBtn');

        screenShareBtn.addEventListener('click', function() {
            screenShareModal.style.display = 'flex';
        });

        modalCloseBtn.addEventListener('click', function() {
            screenShareModal.style.display = 'none';
        });

        // Close modal when clicking outside
        screenShareModal.addEventListener('click', function(e) {
            if (e.target === screenShareModal) {
                screenShareModal.style.display = 'none';
            }
        });

        // === Logger ===
        function logConsole(text, type="system") {
            const consoleDiv = document.getElementById("console");
            const now = new Date();
            const time = now.toLocaleTimeString("fr-FR", { hour12: false });
            
            const div = document.createElement("div");
            div.className = "console-text";
            
            // Différents styles selon le type de message
            if (type === "system") {
                div.innerHTML = `<span class="text-purple-400">[SYSTEM]</span> <span class="text-gray-400">${time}</span> ${text}`;
            } else if (type === "ai") {
                div.innerHTML = `<span class="text-green-400">[IA]</span> <span class="text-gray-400">${time}</span> ${text}`;
            } else if (type === "user") {
                div.innerHTML = `<span class="text-blue-400">[UTILISATEUR]</span> <span class="text-gray-400">${time}</span> ${text}`;
            } else if (type === "error") {
                div.innerHTML = `<span class="text-red-400">[ERREUR]</span> <span class="text-gray-400">${time}</span> ${text}`;
            } else if (type === "voice") {
                div.innerHTML = `<span class="text-yellow-400">[VOIX]</span> <span class="text-gray-400">${time}</span> ${text}`;
            }
            
            consoleDiv.appendChild(div);
            consoleDiv.scrollTop = consoleDiv.scrollHeight;
        }

        // Mise à jour du statut
        function updateStatus(text, isActive = false) {
            const indicator = document.getElementById("status-indicator");
            const statusText = document.getElementById("status-text");
            
            statusText.textContent = text;
            indicator.className = `h-2 w-2 rounded-full ${isActive ? 'bg-green-500 pulse' : 'bg-red-500'}`;
        }

        // Mise à jour du statut de reconnaissance vocale
        function updateRecognitionStatus(text) {
            document.getElementById("recognition-status").textContent = `Reconnaissance vocale: ${text}`;
        }

        // === Upload capture ===
        async function uploadTmpFile(blob) {
            // Afficher l'indicateur de capture
            document.getElementById("capture-overlay").classList.remove("hidden");
            
            const formData = new FormData();
            formData.append("file", blob, "screenshot.png");
            
            try {
                const res = await fetch("https://tmpfiles.org/api/v1/upload", { method: "POST", body: formData });
                const data = await res.json();
                if (data.status !== "success") throw new Error("Erreur upload");
                
                const parts = data.data.url.split("/");
                const url = `${parts[0]}//${parts[2]}/dl/${parts[3]}/${parts[4]}`;
                logConsole("Capture uploadée avec succès", "system");
                return url;
            } catch (err) {
                throw err;
            } finally {
                // Masquer l'indicateur de capture
                document.getElementById("capture-overlay").classList.add("hidden");
            }
        }

        // === Envoi à IA ===
        async function sendToAI(message, imageUrl) {
            if (!message) return;
            
            // Formater l'historique de conversation pour l'envoi
            const historyForAI = conversationHistory.map(msg => ({
                role: msg.sender === "user" ? "user" : "assistant",
                content: msg.text
            }));
            
            const apiUrl = `/proxy.php?message=${encodeURIComponent(message)}&image_url=${encodeURIComponent(imageUrl)}`;
            logConsole("Envoi de la requête à l'IA...", "system");

            try {
                const res = await fetch(apiUrl);
                const data = await res.json();
                
                if (data.status === "success") {
                    const safeMsg = data.message.replace(/</g, "&lt;").replace(/>/g, "&gt;");
                    logConsole(safeMsg, "ai");
                    conversationHistory.push({ sender: "ai", text: safeMsg });
                    enqueueSpeech(safeMsg);
                } else {
                    logConsole("Erreur de l'IA: " + JSON.stringify(data), "error");
                }
            } catch (err) {
                logConsole("Erreur de connexion avec l'IA: " + err.message, "error");
            }
        }

        // === Capture immédiate ===
        async function captureAndSend(message) {
            // Ajouter le message de l'utilisateur à l'historique
            conversationHistory.push({ sender: "user", text: message });
            logConsole(message, "user");
            
            if (!screenStream) { 
                await sendToAI(message, ""); 
                return; 
            }
            
            const video = document.createElement("video");
            video.srcObject = screenStream;
            video.play();
            
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            
            // Attendre que la vidéo soit prête
            setTimeout(() => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                canvas.toBlob(async (blob) => {
                    try {
                        const directUrl = await uploadTmpFile(blob);
                        await sendToAI(message, directUrl);
                    } catch (err) { 
                        logConsole("Échec de l'upload: " + err.message, "error");
                        // Envoyer quand même le message sans image
                        await sendToAI(message, "");
                    }
                }, "image/png");
            }, 500);
        }

        // === Partage d'écran ===
        async function startScreenShare() {
            try {
                screenStream = await navigator.mediaDevices.getDisplayMedia({ 
                    video: { cursor: "always" }, 
                    audio: false 
                });
                
                updateStatus("Partage d'écran actif", true);
                logConsole("Partage d'écran démarré avec succès", "system");
                
                // Gérer la fin du partage d'écran
                screenStream.getTracks().forEach(track => {
                    track.onended = () => {
                        stopScreenShare();
                        logConsole("Partage d'écran interrompu par l'utilisateur", "system");
                    };
                });
                
            } catch (err) {
                logConsole("Erreur de partage d'écran: " + err.message, "error");
                updateStatus("Erreur de partage d'écran");
            }
        }

        function stopScreenShare() {
            if (captureInterval) clearInterval(captureInterval);
            if (screenStream) {
                screenStream.getTracks().forEach(track => track.stop());
            }
            updateStatus("Partage d'écran arrêté");
            logConsole("Partage d'écran arrêté", "system");
        }

        // === Reconnaissance vocale ===
        function setupSpeechRecognition() {
            if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                recognition = new SpeechRecognition();
                recognition.lang = "fr-FR";
                recognition.continuous = false;
                recognition.interimResults = false;
                recognition.maxAlternatives = 1;

                recognition.onstart = () => {
                    isListening = true;
                    recognitionStarting = false; // Réinitialiser le flag de démarrage
                    streamPaused = true;
                    stopVoice(); // Arrêter la voix IA si elle parle
                    updateStatus("Micro actif - écoute en cours", true);
                    updateRecognitionStatus("écoute en cours");
                    logConsole("Micro activé - écoute en cours", "user");
                    
                    // Changer l'état des boutons microphone
                    document.getElementById("start-recognition-button").classList.add("hidden");
                    document.getElementById("stop-recognition-button").classList.remove("hidden");
                    
                    // Mettre à jour l'interface utilisateur
                    document.getElementById("userStatus").textContent = 'Speaking...';
                    document.getElementById("userMicStatus").className = 'w-2 h-2 bg-green-400 rounded-full animate-pulse';
                    document.getElementById("userSpeakingRings").style.opacity = '1';
                    document.getElementById("userVoiceVisualizer").style.opacity = '1';
                };
                
                recognition.onend = () => {
                    isListening = false;
                    streamPaused = false;
                    updateStatus("Partage d'écran actif", true);
                    updateRecognitionStatus("en attente");
                    logConsole("Micro désactivé", "user");
                    
                    // Mettre à jour l'interface utilisateur
                    document.getElementById("userStatus").textContent = 'Ready to speak';
                    document.getElementById("userMicStatus").className = 'w-2 h-2 bg-green-400 rounded-full';
                    document.getElementById("userSpeakingRings").style.opacity = '0';
                    document.getElementById("userVoiceVisualizer").style.opacity = '0';
                    
                    // Réactiver l'écoute après un court délai si toujours actif
                    if (recognitionActive && !recognitionStarting) {
                        setTimeout(() => {
                            if (!isSpeaking && recognitionActive) {
                                safeStartRecognition();
                            }
                        }, 800);
                    }
                    
                    // Remettre les boutons dans l'état initial si la reconnaissance n'est plus active
                    if (!recognitionActive) {
                        document.getElementById("start-recognition-button").classList.remove("hidden");
                        document.getElementById("stop-recognition-button").classList.add("hidden");
                    }
                };
                
                recognition.onresult = (e) => {
                    const transcript = e.results[0][0].transcript.trim();
                    if (!transcript) return;
                    
                    // Ajouter à la transcription en direct
                    addToTranscript('You', transcript);
                    
                    // Envoyer immédiatement la transcription et capturer l'écran
                    captureAndSend(transcript);
                };
                
                recognition.onerror = (e) => {
                    if (e.error === 'no-speech') {
                        // Cette erreur est normale quand l'utilisateur ne parle pas
                        logConsole("Aucune parole détectée", "system");
                        updateRecognitionStatus("aucune parole détectée");
                    } else if (e.error === 'audio-capture') {
                        logConsole("Impossible de capturer l'audio - vérifiez votre microphone", "error");
                        updateRecognitionStatus("erreur de microphone");
                    } else if (e.error === 'not-allowed') {
                        logConsole("Microphone non autorisé - vérifiez les permissions", "error");
                        updateRecognitionStatus("permissions refusées");
                        recognitionActive = false;
                    } else {
                        logConsole("Erreur de reconnaissance vocale: " + e.error, "error");
                        updateRecognitionStatus("erreur");
                    }
                    
                    // Réessayer après une erreur, sauf si l'erreur est grave
                    if (e.error !== 'not-allowed' && recognitionActive && !recognitionStarting) {
                        setTimeout(() => {
                            if (!isSpeaking && recognitionActive) {
                                safeStartRecognition();
                            }
                        }, 1000);
                    }
                };

                // Fonction pour démarrer la reconnaissance de manière sécurisée
                window.safeStartRecognition = function() {
                    if (recognitionStarting) {
                        logConsole("Démarrage déjà en cours", "system");
                        return;
                    }
                    
                    try {
                        recognitionStarting = true;
                        recognition.start();
                        logConsole("Démarrage de la reconnaissance vocale", "system");
                    } catch (err) {
                        recognitionStarting = false;
                        if (err.message.includes('already started')) {
                            logConsole("Reconnaissance déjà active - attente...", "system");
                            // Ne pas réessayer immédiatement pour éviter la boucle
                            setTimeout(() => {
                                recognitionStarting = false;
                            }, 2000);
                        } else {
                            logConsole("Erreur lors du démarrage: " + err.message, "error");
                            // Réessayer après un délai en cas d'autres erreurs
                            setTimeout(() => {
                                recognitionStarting = false;
                                if (recognitionActive) safeStartRecognition();
                            }, 1000);
                        }
                    }
                };

                // Configurer les boutons
                document.getElementById("start-recognition-button").onclick = () => {
                    recognitionActive = true;
                    safeStartRecognition();
                };
                
                document.getElementById("stop-recognition-button").onclick = () => {
                    recognitionActive = false;
                    try {
                        recognition.stop();
                    } catch (e) {
                        logConsole("Erreur lors de l'arrêt: " + e.message, "error");
                    }
                };
                
                // Démarrer automatiquement la reconnaissance au chargement
                setTimeout(() => {
                    recognitionActive = true;
                    safeStartRecognition();
                }, 1000);
            } else {
                logConsole("Reconnaissance vocale non supportée par ce navigateur", "error");
                updateRecognitionStatus("non supportée");
                document.getElementById("start-recognition-button").disabled = true;
                document.getElementById("start-recognition-button").classList.remove("pulse");
                document.getElementById("start-recognition-button").classList.add("opacity-50");
            }
        }

        // === Synthèse vocale avec file d'attente ===
        function enqueueSpeech(text) {
            speechQueue.push(text);
            if (!isSpeaking) playNextSpeech();
        }

        function playNextSpeech() {
            if (speechQueue.length === 0) {
                isSpeaking = false;
                document.getElementById("stop-voice-button").classList.add("hidden");
                
                // Mettre à jour l'interface IA
                document.getElementById("aiStatus").textContent = 'Listening...';
                document.getElementById("aiMicStatus").className = 'w-2 h-2 bg-gray-400 rounded-full';
                document.getElementById("aiSpeakingRings").style.opacity = '0';
                document.getElementById("aiVoiceVisualizer").style.opacity = '0';
                
                return;
            }
            
            if (!("speechSynthesis" in window)) {
                logConsole("Synthèse vocale non supportée", "error");
                return;
            }
            
            const text = speechQueue.shift();
            const utter = new SpeechSynthesisUtterance(text);
            utter.lang = "fr-FR";
            utter.rate = 1.0;
            utter.pitch = 1.0;
            
            isSpeaking = true;
            document.getElementById("stop-voice-button").classList.remove("hidden");
            
            // Mettre à jour l'interface IA
            document.getElementById("aiStatus").textContent = 'Responding...';
            document.getElementById("aiMicStatus").className = 'w-2 h-2 bg-green-400 rounded-full animate-pulse';
            document.getElementById("aiSpeakingRings").style.opacity = '1';
            document.getElementById("aiVoiceVisualizer").style.opacity = '1';
            
            logConsole("Début de la synthèse vocale", "voice");

            utter.onend = () => {
                logConsole("Fin de la synthèse vocale", "voice");
                isSpeaking = false;
                
                // Mettre à jour l'interface IA
                document.getElementById("aiStatus").textContent = 'Listening...';
                document.getElementById("aiMicStatus").className = 'w-2 h-2 bg-gray-400 rounded-full';
                document.getElementById("aiSpeakingRings").style.opacity = '0';
                document.getElementById("aiVoiceVisualizer").style.opacity = '0';
                
                playNextSpeech();
            };
            
            utter.onerror = (e) => {
                logConsole("Erreur de synthèse vocale: " + e.error, "error");
                isSpeaking = false;
                
                // Mettre à jour l'interface IA
                document.getElementById("aiStatus").textContent = 'Listening...';
                document.getElementById("aiMicStatus").className = 'w-2 h-2 bg-gray-400 rounded-full';
                document.getElementById("aiSpeakingRings").style.opacity = '0';
                document.getElementById("aiVoiceVisualizer").style.opacity = '0';
                
                playNextSpeech();
            };

            speechSynthesis.speak(utter);
        }

        function stopVoice() {
            if (isSpeaking || speechSynthesis.speaking) {
                speechSynthesis.cancel();
                isSpeaking = false;
                speechQueue = [];
                document.getElementById("stop-voice-button").classList.add("hidden");
                
                // Mettre à jour l'interface IA
                document.getElementById("aiStatus").textContent = 'Listening...';
                document.getElementById("aiMicStatus").className = 'w-2 h-2 bg-gray-400 rounded-full';
                document.getElementById("aiSpeakingRings").style.opacity = '0';
                document.getElementById("aiVoiceVisualizer").style.opacity = '0';
                
                logConsole("Synthèse vocale arrêtée", "voice");
            }
        }

        document.getElementById("stop-voice-button").onclick = stopVoice;

        // Add message to transcript
        function addToTranscript(sender, message) {
            const transcript = document.getElementById("transcript");
            const messageDiv = document.createElement("div");
            messageDiv.className = 'text-sm text-gray-300';
            
            let senderClass = 'text-purple-400';
            if (sender === 'You') {
                senderClass = 'text-blue-400';
            } else if (sender === 'System') {
                senderClass = 'text-gray-500';
            }
            
            messageDiv.innerHTML = `<span class="${senderClass} font-medium">${sender}:</span> <span>${message}</span>`;
            transcript.appendChild(messageDiv);
            transcript.scrollTop = transcript.scrollHeight;
        }

        // Push to talk functionality
        const pushToTalkBtn = document.getElementById('pushToTalkBtn');
        let isPressed = false;

        pushToTalkBtn.addEventListener('mousedown', function() {
            if (!isPressed) {
                isPressed = true;
                this.style.transform = 'scale(0.95)';
                this.style.backgroundColor = '#dc2626';
                
                // Démarrer la reconnaissance vocale manuellement
                if (!recognitionActive) {
                    recognitionActive = true;
                    safeStartRecognition();
                }
            }
        });

        pushToTalkBtn.addEventListener('mouseup', function() {
            if (isPressed) {
                isPressed = false;
                this.style.transform = 'scale(1)';
                this.style.backgroundColor = '#16a34a';
            }
        });

        pushToTalkBtn.addEventListener('mouseleave', function() {
            if (isPressed) {
                isPressed = false;
                this.style.transform = 'scale(1)';
                this.style.backgroundColor = '#16a34a';
            }
        });

        // Mute functionality
        let isMuted = false;
        document.getElementById('muteBtn').addEventListener('click', function() {
            isMuted = !isMuted;
            const icon = this.querySelector('i');
            
            if (isMuted) {
                icon.setAttribute('data-lucide', 'mic-off');
                this.classList.add('bg-red-600/20', 'hover:bg-red-600/30');
                this.classList.remove('bg-gray-800/60', 'hover:bg-gray-700/60');
                icon.classList.add('text-red-400');
                icon.classList.remove('text-gray-300');
                document.getElementById('userStatus').textContent = 'Microphone muted';
                document.getElementById('userMicStatus').className = 'w-2 h-2 bg-red-400 rounded-full';
                
                // Arrêter la reconnaissance vocale
                recognitionActive = false;
                try {
                    recognition.stop();
                } catch (e) {
                    console.error("Erreur lors de l'arrêt:", e);
                }
            } else {
                icon.setAttribute('data-lucide', 'mic');
                this.classList.remove('bg-red-600/20', 'hover:bg-red-600/30');
                this.classList.add('bg-gray-800/60', 'hover:bg-gray-700/60');
                icon.classList.remove('text-red-400');
                icon.classList.add('text-gray-300');
                document.getElementById('userStatus').textContent = 'Ready to speak';
                document.getElementById('userMicStatus').className = 'w-2 h-2 bg-green-400 rounded-full';
                
                // Redémarrer la reconnaissance vocale
                recognitionActive = true;
                safeStartRecognition();
            }
            
            lucide.createIcons();
        });

        // End call functionality
        document.getElementById('endCallBtn').addEventListener('click', function() {
            clearInterval(callTimer);
            document.getElementById('callTimer').textContent = 'Call ended';
            document.getElementById('status-indicator').className = 'h-2 w-2 bg-red-400 rounded-full';
            document.getElementById('status-text').textContent = 'Disconnected';
            document.getElementById('status-text').className = 'text-sm text-red-400';
            
            // Arrêter toutes les fonctionnalités
            recognitionActive = false;
            try {
                recognition.stop();
            } catch (e) {
                console.error("Erreur lors de l'arrêt:", e);
            }
            stopVoice();
            stopScreenShare();
            
            // Disable all buttons
            document.querySelectorAll('button').forEach(btn => {
                if (btn.id !== 'endCallBtn') {
                    btn.disabled = true;
                    btn.classList.add('opacity-50');
                }
            });
            
            // Show call ended message
            addToTranscript('System', 'Call ended. Thank you for using our AI assistant.');
        });

        // Initialiser les icônes et la reconnaissance vocale
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            setupSpeechRecognition();
            
            // Démarrer le partage d'écran automatiquement
            startScreenShare();
        });
    </script>
</body>
</html>