<?php


/**
 * 
 * *************************************
 * Projet : EdithAI Personnal Assistant 
 * Nom du fichier : index.php
 * Decsription : Il s'agit du fichier index de l'application , cette partis PHP va faire toute les verifications coter serveur necessaire .
 * Date de creation : 09/09/2025
 * Date de modification : 21/09/2025 
 * version : 1.0
 * Auteur : Exaustan Malka
 * Stacks : PHP , JSON
 * *************************************
 * 
 */

/**
 * 
 * 
 * Initialiser la session 
 * 
 * 
 */
session_start();

/**
 * 
 * Installer les fichiers des configurations de base 
 * 
 */
require_once 'config.php'; // Le fichier qui gere les config de l'app 
require_once 'loader.php'; // Le fichier qui gere le fonction du chargement de l'app 

/**
 * 
 * Verifier si l'id du chat existe 
 * 
 */
if (isset($_GET['id'])) {
    $_chat_id = $_GET['id'] ?? null;
    $_url = API_VERIFY_CHAT . $_chat_id;

    // Requête cURL vers l'API
    $_response = request($_url, 'GET');

    if (!empty($_response)) {
        $_data = $_response;

        // Si le chat n'existe pas → retour à l'accueil
        if ($_data['data']['status'] !== 'success') {
            if (!isset($_GET['m'])) { // empêcher boucle
                header('Location: ' . APP_URL . '?m=1');
                exit();
            }
        }

        // Si succès → stocker en session
        $_SESSION['data'] = [
            "chat_id" => $_data['data']['id'],
            "title"   => $_data['data']['title']
        ];
    }
}

/**
 * 
 * 
 * Verifier si la session existe ou non 
 * 
 * 
 */if (!isset($_SESSION['data']) || !isset($_SESSION['data']['chat_id'])) {
    if (!isset($_GET['m'])) { // empêcher boucle
        header('Location: ' . APP_URL . '?m=1');
        exit();
    }
}

?>
<html lang="en">
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

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
        
        .glass {
            background: rgba(20, 21, 25, 0.85);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(139, 92, 246, 0.15);
        }
        
        .glass-light {
            background: rgba(25, 26, 31, 0.8);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }
        
        .glass-sidebar {
            background: rgba(12, 13, 16, 0.9);
            backdrop-filter: blur(20px) saturate(200%);
            border-right: 1px solid rgba(139, 92, 246, 0.1);
        }

        .message-bubble {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(139, 92, 246, 0.1);
        }

        .user-bubble {
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            box-shadow: 0 4px 16px rgba(124, 58, 237, 0.3);
        }

        .toast {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .typing-indicator {
            opacity: 0.7;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(139, 92, 246, 0.1);
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(139, 92, 246, 0.3);
            border-radius: 2px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(139, 92, 246, 0.5);
        }

        .prose-custom {
            color: #e5e7eb;
        }

        .prose-custom h1, .prose-custom h2, .prose-custom h3 {
            color: #f9fafb;
        }

        .prose-custom strong {
            color: #f3f4f6;
            font-weight: 600;
        }

        .prose-custom code {
            background: rgba(139, 92, 246, 0.1);
            color: #a78bfa;
            padding: 0.2em 0.4em;
            border-radius: 0.25rem;
            font-size: 0.875em;
        }

        .prose-custom pre {
            background: rgba(17, 17, 19, 0.8);
            border: 1px solid rgba(139, 92, 246, 0.2);
            border-radius: 0.5rem;
            padding: 1rem;
            overflow-x: auto;
        }

        .prose-custom pre code {
            background: none;
            color: #e5e7eb;
            padding: 0;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar-mobile {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 40;
            }
            
            .sidebar-mobile.open {
                transform: translateX(0);
            }
            
            .mobile-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 30;
            }
            
            .mobile-overlay.open {
                display: block;
            }
            
            .mobile-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 20;
                background: rgba(10, 11, 15, 0.95);
                backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(139, 92, 246, 0.1);
            }
            
            .mobile-input-container {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 20;
                background: rgba(10, 11, 15, 0.95);
                backdrop-filter: blur(20px);
                border-top: 1px solid rgba(139, 92, 246, 0.1);
            }
            
            .chat-container-mobile {
                padding-top: 5rem; /* Plus d'espace pour le header mobile */
                padding-bottom: 9rem; /* Plus d'espace pour l'input mobile */
                min-height: 100vh;
            }
            
            .message-bubble {
                max-width: 85%;
            }
            
            .user-bubble {
                max-width: 85%;
            }

            /* Espacement supplémentaire pour le premier message sur mobile */
            .first-message-mobile {
                margin-top: 2rem !important;
            }
        }
    </style>
</head>
<body class="bg-gray-950 text-gray-100 font-sans antialiased">
    <!-- Toast Container -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="mobile-overlay lg:hidden"></div>

    <!-- Sidebar -->
    <div class="fixed left-0 top-0 h-full w-72 glass-sidebar p-5 flex flex-col sidebar-mobile lg:translate-x-0">
        <!-- Mobile Header -->
        <div class="lg:hidden flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-100">
                        <?=$_TEXT['sidebar']['name'];?>
                    </h1>
                </div>
            </div>
            <button id="closeSidebar" class="p-2 hover:bg-gray-800/50 rounded-lg">
                <i data-lucide="x" class="w-5 h-5 text-gray-400"></i>
            </button>
        </div>

        <!-- Logo (Desktop only) -->
        <div class="mb-8 px-1 hidden lg:block">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="sparkles" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg font-semibold text-gray-100">
                        <?=$_TEXT['sidebar']['name'];?>
                    </h1>
                    <p class="text-xs text-gray-400">
                        <?=$_TEXT['sidebar']['sub_name'];?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Navigation Items -->
        <div class="space-y-2 mb-8">
            <button class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl glass-light hover:bg-purple-600/20 transition-all duration-200 group new-chat-btn">
                <i data-lucide="plus" class="w-4 h-4 text-gray-400 group-hover:text-purple-400"></i>
                <span class="text-sm font-medium text-gray-300 group-hover:text-gray-100">
                    <?=$_TEXT['sidebar']['btn_nchat'];?>
                </span>
            </button>
            <button class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl hover:bg-gray-850/60 transition-all duration-200 group search-btn">
                <i data-lucide="search" class="w-4 h-4 text-gray-500 group-hover:text-gray-400"></i>
                <span class="text-sm text-gray-400 group-hover:text-gray-300">
                    <?=$_TEXT['sidebar']['btn_search'];?>
                </span>
            </button>
            <button class="w-full flex items-center space-x-3 px-4 py-2.5 rounded-xl hover:bg-gray-850/60 transition-all duration-200 group tools-btn">
                <i data-lucide="wrench" class="w-4 h-4 text-gray-500 group-hover:text-gray-400"></i>
                <span class="text-sm text-gray-400 group-hover:text-gray-300">
                    <?=$_TEXT['sidebar']['btn_tools'];?>
                </span>
            </button>
        </div>

        <!-- Chat History -->
        <div class="flex-1 overflow-y-auto scrollbar-thin">
            <h3 class="text-xs font-medium text-gray-500 mb-4 px-1 uppercase tracking-wider">Recent Chats</h3>
            <div class="space-y-1" id="chatHistory">
                <!-- L'historique sera chargé dynamiquement -->
            </div>
        </div>

        <!-- Profile Card -->
        <div class="glass-light rounded-xl p-4 mt-6">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center">
                    <span class="text-sm font-semibold text-white">
                        <?= shortName($_TEXT['user']['name']); ?> 
                    </span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-200">
                        <?= $_TEXT['user']['name']; ?>
                    </p>
                    <div class="flex items-center space-x-1 mt-0.5">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <p class="text-xs text-gray-400">
                            <?= $_TEXT['user']['plan']; ?>
                        </p>
                    </div>
                </div>
                <button class="p-1 hover:bg-gray-700/50 rounded-lg transition-all">
                    <i data-lucide="settings" class="w-4 h-4 text-gray-400"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-72 flex flex-col h-screen">
        <!-- Mobile Header -->
        <div class="mobile-header lg:hidden px-4 py-3 flex items-center justify-between">
            <button id="menuButton" class="p-2 hover:bg-gray-800/50 rounded-lg">
                <i data-lucide="menu" class="w-5 h-5 text-gray-400"></i>
            </button>
            
            <div class="flex items-center space-x-2">
                <!-- Call Button -->
                <button id="callButton" class="flex items-center space-x-2 px-3 py-1.5 bg-green-600/20 hover:bg-green-600/30 rounded-lg border border-green-500/30 transition-all">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-xs text-green-400 font-medium">Call</span>
                    <i data-lucide="phone" class="w-3 h-3 text-green-400"></i>
                </button>
                
                <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-900/60 rounded-lg border border-gray-800">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-xs text-gray-400">
                        <?= $_TEXT['bot']['status']; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="border-b border-gray-850 px-6 py-4 hidden lg:block">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-100">
                        <?= $_SESSION['data']['title'] ?? 'New Chat'; ?>
                    </h2>
                    <p class="text-sm text-gray-400">
                        <?= $_TEXT['bot']['default']; ?>
                    </p>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Call Button Desktop -->
                    <button id="callButtonDesktop" class="flex items-center space-x-2 px-3 py-1.5 bg-green-600/20 hover:bg-green-600/30 rounded-lg border border-green-500/30 transition-all">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <span class="text-xs text-green-400 font-medium">Call</span>
                        <i data-lucide="phone" class="w-3 h-3 text-green-400"></i>
                    </button>
                    
                    <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-900/60 rounded-lg border border-gray-800">
                        <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                        <span class="text-xs text-gray-400">
                            <?= $_TEXT['bot']['status']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="flex-1 p-6 overflow-y-auto scrollbar-thin chat-container-mobile" id="chatContainer">
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Bot Welcome Message - Ajout de la classe first-message-mobile pour l'espacement sur mobile -->
                <div class="flex items-start space-x-4 first-message-mobile">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                        <i data-lucide="sparkles" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="message-bubble rounded-2xl p-5 max-w-3xl">
                        <div class="prose-custom max-w-none" id="botMessage">
                            <p>
                                <?=$_TEXT['bot']['default_message'];?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-850 p-6 mobile-input-container lg:relative">
            <div class="max-w-4xl mx-auto">
                <div class="glass rounded-2xl p-4">
                    <!-- Text Input Area -->
                    <div class="mb-4">
                        <textarea 
                            id="messageInput"
                            placeholder="<?=$_TEXT['page']['input_placeholder'];?>" 
                            class="w-full bg-transparent border-none outline-none resize-none text-gray-100 placeholder-gray-500 text-sm leading-relaxed min-h-[44px] max-h-32"
                            rows="1"
                        ></textarea>
                        
                        <!-- File Preview Area -->
                        <div id="filePreview" class="hidden mt-3 p-2 bg-gray-850/60 rounded-lg border border-gray-800/60">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div id="fileIcon" class="w-6 h-6 rounded flex items-center justify-center"></div>
                                    <div>
                                        <p id="fileName" class="text-xs font-medium text-gray-200"></p>
                                        <p id="fileSize" class="text-xs text-gray-500"></p>
                                    </div>
                                </div>
                                <button id="removeFile" class="p-1 hover:bg-gray-700/50 rounded transition-all">
                                    <i data-lucide="x" class="w-3 h-3 text-gray-400"></i>
                                </button>
                            </div>
                            <div id="imagePreview" class="hidden mt-2">
                                <img id="previewImg" class="max-w-full h-16 object-cover rounded border border-gray-700/50" alt="Preview">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Controls Row -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <!-- Add Button -->
                            <button class="p-2 hover:bg-gray-800/60 rounded-lg transition-all duration-200 group" id="addBtn" title="Attach file">
                                <i data-lucide="paperclip" class="w-4 h-4 text-gray-500 group-hover:text-gray-400"></i>
                            </button>
                            <input type="file" id="fileInput" class="hidden" accept="*/*">
                            
                            <!-- Tools Button -->
                            <button class="p-2 hover:bg-gray-800/60 rounded-lg transition-all duration-200 group" id="toolsBtn" title="Tools">
                                <i data-lucide="wrench" class="w-4 h-4 text-gray-500 group-hover:text-gray-400"></i>
                            </button>
                            
                            <!-- Model Selector -->
                            <button class="px-3 py-2 hover:bg-gray-800/60 rounded-lg transition-all duration-200 text-xs text-gray-400 hover:text-gray-300 border border-gray-800/60 hover:border-gray-700 hidden lg:flex" id="modelBtn">
                                <span class="flex items-center space-x-2">
                                    <span>GPT-4</span>
                                    <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                </span>
                            </button>
                        </div>
                        
                        <!-- Send Button -->
                        <button 
                            id="sendBtn"
                            class="px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 rounded-lg transition-all duration-200 opacity-0 pointer-events-none disabled:opacity-50 shadow-lg hover:shadow-purple-500/25"
                            disabled
                        >
                            <div class="flex items-center space-x-2">
                                <i data-lucide="send" class="w-4 h-4 text-white"></i>
                                <span class="text-sm font-medium text-white hidden lg:inline">
                                    <?=$_TEXT['page']['send_message'];?>
                                </span>
                            </div>
                        </button>
                    </div>
                </div>
                
                <!-- Footer Info -->
                <p class="text-xs text-gray-500 text-center mt-3 hidden lg:block">
                    <?= $_TEXT['info'];?>
                </p>
            </div>
        </div>
    </div>

   <script src="assets/js/config.js"></script>
   <script>
    // Initialize Lucide icons
    lucide.createIcons();

    // Mobile sidebar functionality
    const menuButton = document.getElementById('menuButton');
    const closeSidebar = document.getElementById('closeSidebar');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const sidebar = document.querySelector('.sidebar-mobile');

    function openSidebar() {
        sidebar.classList.add('open');
        mobileOverlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebarFunc() {
        sidebar.classList.remove('open');
        mobileOverlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    if (menuButton) {
        menuButton.addEventListener('click', openSidebar);
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeSidebarFunc);
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', closeSidebarFunc);
    }

    // Call button functionality
    function handleCall() {
        showToast('Call feature coming soon!', 'info', 3000);
    }

    const callButton = document.getElementById('callButton');
    const callButtonDesktop = document.getElementById('callButtonDesktop');

    if (callButton) {
        callButton.addEventListener('click', handleCall);
    }

    if (callButtonDesktop) {
        callButtonDesktop.addEventListener('click', handleCall);
    }

    // Model management
    const availableModels = ['EdithAI-P1', 'EdithAI-P2', 'LLama-3.1'];
    let currentModel = localStorage.getItem('selectedModel') || 'EdithAI-P1';
    let toolsSelected = false;
    let uploadedFile = null;

    // DOM elements
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const chatContainer = document.getElementById('chatContainer');
    const addBtn = document.getElementById('addBtn');
    const fileInput = document.getElementById('fileInput');
    const filePreview = document.getElementById('filePreview');
    const toolsBtn = document.getElementById('toolsBtn');
    const modelBtn = document.getElementById('modelBtn');
    const toastContainer = document.getElementById('toastContainer');
    let pendingMessage = null;

    // Initialize model button text
    if (modelBtn) {
        modelBtn.innerHTML = `
            <span class="flex items-center space-x-2">
                <span>${currentModel}</span>
                <i data-lucide="chevron-down" class="w-3 h-3"></i>
            </span>
        `;
    }

    /**
     * 
     * Une fonction pour gerer les toast notification 
     * 
     * 
     */
    function showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        const icons = {
            success: 'check-circle',
            error: 'x-circle',
            warning: 'alert-triangle',
            info: 'info'
        };
        
        const colors = {
            success: 'from-green-600 to-green-700 border-green-500/20',
            error: 'from-red-600 to-red-700 border-red-500/20',
            warning: 'from-amber-600 to-amber-700 border-amber-500/20',
            info: 'from-purple-600 to-purple-700 border-purple-500/20'
        };
        
        toast.className = `toast glass rounded-xl p-4 max-w-sm flex items-center space-x-3 bg-gradient-to-r ${colors[type]} shadow-xl`;
        toast.innerHTML = `
            <i data-lucide="${icons[type]}" class="w-5 h-5 text-white flex-shrink-0"></i>
            <p class="text-sm text-white font-medium">${message}</p>
            <button class="ml-auto hover:bg-white/10 rounded-lg p-1 transition-all" onclick="this.parentElement.remove()">
                <i data-lucide="x" class="w-4 h-4 text-white"></i>
            </button>
        `;
        
        toastContainer.appendChild(toast);
        lucide.createIcons();
        
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.animation = 'slideInRight 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }
        }, duration);
    }

    /**
     * 
     * Faire un resize automatique du textarea 
     * 
     * 
     */
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 128) + 'px';
        
        const hasContent = this.value.trim();
        if (hasContent) {
            sendBtn.classList.remove('opacity-0', 'pointer-events-none');
            sendBtn.classList.add('opacity-100');
            sendBtn.disabled = false;
        } else {
            sendBtn.classList.add('opacity-0', 'pointer-events-none');
            sendBtn.classList.remove('opacity-100');
            sendBtn.disabled = true;
        }
    });

    /***
     * 
     * La fonction qui gere l'envoie de messages
     * 
     * 
     */
   async function sendMessage() {
        const message = messageInput.value.trim();
        if (!message && !uploadedFile) return;

        // Désactive le bouton
        sendBtn.disabled = true;
        sendBtn.innerHTML = `
            <div class="flex items-center space-x-2">
                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                <span class="text-sm font-medium text-white">Envoi...</span>
            </div>
        `;

        // Ajoute immédiatement le message de l'utilisateur à l'UI
        addUserMessage(message, uploadedFile, toolsSelected);

        // Reset input
        messageInput.value = "";
        messageInput.style.height = "auto";
        
        // Récupération du chatId depuis les paramètres d'URL
        const urlParams = new URLSearchParams(window.location.search);
        let chatId = urlParams.get('id');
        
        // Si aucun chatId n'est trouvé, on va créer un nouveau chat
        if (!chatId) {
            // Sauvegarder le message temporairement
            pendingMessage = {
                text: message,
                file: uploadedFile,
                tools: toolsSelected
            };
            
            // Affiche le typing
            showTypingIndicator();
            
            // Créer un nouveau chat
            const newChatId = await startNewChat();
            
            if (newChatId) {
                // Rediriger vers le nouveau chat
                const currentUrl = new URL(window.location.href);
                const baseUrl = currentUrl.origin + currentUrl.pathname;
                window.location.href = `${baseUrl}?m=1&id=${newChatId}`;
            } else {
                // En cas d'erreur
                hideTypingIndicator();
                addBotMessage("Erreur lors de la création du chat. Veuillez réessayer.");
                showToast("Erreur de création de chat", "error", 3000);
                
                // Réactiver le bouton
                resetSendButton();
            }
            return;
        }

        // Si on a un chatId, envoyer normalement le message
        const formData = new FormData();
        formData.append("message", message);
        formData.append("chat_id", chatId);
        formData.append("model", currentModel || "gpt-4");

        if (toolsSelected) {
            formData.append("tools", "true"); 
        }

        if (uploadedFile) {
            formData.append("file", uploadedFile);
        }

        // Reset file upload
        uploadedFile = null;
        filePreview.classList.add("hidden");
        fileInput.value = "";

        // Affiche le typing
        showTypingIndicator();

        try {
            const response = await fetch(BASE_API_URL+"/api/chat/", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            hideTypingIndicator();

            if (data.status === "success") {
                // Actualise la conversation et l'historique
                loadChatMessages(chatId);
                loadChatHistory();
            } else {
                addBotMessage("Erreur lors de l'envoi du message.");
                showToast("Erreur API", "error", 3000);
            }
        } catch (error) {
            hideTypingIndicator();
            addBotMessage("Erreur de connexion, veuillez réessayer.");
            showToast("Erreur réseau", "error", 3000);
        } finally {
            // Réactive le bouton
            resetSendButton();
        }
    }

    function resetSendButton() {
        sendBtn.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="send" class="w-4 h-4 text-white"></i>
                <span class="text-sm font-medium text-white">Envoyer</span>
            </div>
        `;
        sendBtn.classList.add("opacity-0", "pointer-events-none");
        sendBtn.classList.remove("opacity-100");
        sendBtn.disabled = true;
        lucide.createIcons();
    }

    function smoothScrollToBottom() {
        setTimeout(() => {
            chatContainer.scrollTo({
                top: chatContainer.scrollHeight,
                behavior: 'smooth'
            });
        }, 100);
    }

    function addUserMessage(message, file, toolsEnabled) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-4 justify-end';
        
        let fileHtml = '';
        if (file) {
            const extension = file.name.split('.').pop().toLowerCase();
            let iconHtml = '';
            let bgClass = '';
            
            if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension)) {
                iconHtml = '<i data-lucide="image" class="w-4 h-4 text-green-400"></i>';
                bgClass = 'bg-green-600/20 border-green-500/30';
            } else if (['js', 'html', 'css', 'py', 'java', 'cpp', 'c', 'php', 'rb', 'go', 'rs'].includes(extension)) {
                iconHtml = '<i data-lucide="code" class="w-4 h-4 text-blue-400"></i>';
                bgClass = 'bg-blue-600/20 border-blue-500/30';
            } else {
                iconHtml = '<i data-lucide="file" class="w-4 h-4 text-purple-400"></i>';
                bgClass = 'bg-purple-600/20 border-purple-500/30';
            }
            
            fileHtml = `
                <div class="flex items-center space-x-2 mt-3 p-2 ${bgClass} border rounded-lg">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center ${bgClass}">
                        ${iconHtml}
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-200">${file.name}</p>
                        <p class="text-xs text-gray-400">${formatFileSize(file.size)}</p>
                    </div>
                </div>
            `;
        }
        
        let toolsHtml = '';
        if (toolsEnabled) {
            toolsHtml = `
                <div class="flex items-center space-x-1 mt-2 text-xs text-purple-400">
                    <i data-lucide="wrench" class="w-3 h-3"></i>
                    <span>Tools Selected</span>
                </div>
            `;
        }
        
        messageDiv.innerHTML = `
            <div class="user-bubble rounded-2xl px-5 py-4 max-w-3xl">
                <p class="text-white text-sm leading-relaxed">${escapeHtml(message)}</p>
                ${fileHtml}
                ${toolsHtml}
            </div>
            <div class="w-8 h-8 bg-gradient-to-br from-gray-600 to-gray-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                <span class="text-xs font-semibold text-white"><?=shortName($_TEXT['user']['name']);?></span>
            </div>
        `;
        
        const chatContent = chatContainer.querySelector('.max-w-4xl');
        chatContent.appendChild(messageDiv);
        lucide.createIcons();
        
        // Ajouter un délai pour garantir que le DOM est mis à jour
        setTimeout(smoothScrollToBottom, 150);
    }

    function addBotMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-4';
        messageDiv.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                <i data-lucide="sparkles" class="w-4 h-4 text-white"></i>
            </div>
            <div class="message-bubble rounded-2xl p-5 max-w-3xl">
                <div class="prose-custom max-w-none">${marked.parse(message)}</div>
            </div>
        `;
        
        const chatContent = chatContainer.querySelector('.max-w-4xl');
        chatContent.appendChild(messageDiv);
        lucide.createIcons();
        
        // Ajouter un délai pour garantir que le DOM est mis à jour
        setTimeout(smoothScrollToBottom, 150);
    }

    // Fonction pour ajuster automatiquement l'espacement sur mobile
    function adjustMobileSpacing() {
        if (window.innerWidth < 768) {
            const chatContent = chatContainer.querySelector('.max-w-4xl');
            if (chatContent) {
                const messages = chatContent.querySelectorAll('.flex.items-start');
                if (messages.length > 0) {
                    // Premier message
                    messages[0].classList.add('first-message-mobile');
                    
                    // Dernier message
                    messages[messages.length - 1].classList.add('last-message-mobile');
                }
            }
        }
    }

    // Appeler cette fonction après chaque ajout de message
    setTimeout(adjustMobileSpacing, 200);

    // Et aussi lors du redimensionnement de la fenêtre
    window.addEventListener('resize', adjustMobileSpacing);

    
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typingIndicator';
        typingDiv.className = 'flex items-start space-x-4 typing-indicator';
        typingDiv.innerHTML = `
            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                <i data-lucide="sparkles" class="w-4 h-4 text-white"></i>
            </div>
            <div class="message-bubble rounded-2xl p-5">
                <div class="flex items-center space-x-2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                    <span class="text-sm text-gray-400 ml-2">AI is thinking...</span>
                </div>
            </div>
        `;
        
        const chatContent = chatContainer.querySelector('.max-w-4xl');
        chatContent.appendChild(typingDiv);
        lucide.createIcons();
        smoothScrollToBottom();
    }

    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    function smoothScrollToBottom() {
        chatContainer.scrollTo({
            top: chatContainer.scrollHeight,
            behavior: 'smooth'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    addBtn.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        uploadedFile = file;
        displayFilePreview(file);
    });

    function displayFilePreview(file) {
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const fileIcon = document.getElementById('fileIcon');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        // Reset previews
        imagePreview.classList.add('hidden');
        fileIcon.innerHTML = '';
        fileIcon.className = 'w-6 h-6 rounded flex items-center justify-center';
        
        const extension = file.name.split('.').pop().toLowerCase();
        
        if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension)) {
            // Image preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
            
            fileIcon.className += ' bg-green-600/20 border border-green-500/30';
            fileIcon.innerHTML = '<i data-lucide="image" class="w-3 h-3 text-green-400"></i>';
        } else if (['js', 'html', 'css', 'py', 'java', 'cpp', 'c', 'php', 'rb', 'go', 'rs'].includes(extension)) {
            // Code file
            fileIcon.className += ' bg-blue-600/20 border border-blue-500/30';
            fileIcon.innerHTML = '<i data-lucide="code" class="w-3 h-3 text-blue-400"></i>';
        } else if (['txt', 'md', 'rtf'].includes(extension)) {
            // Text file
            fileIcon.className += ' bg-gray-600/20 border border-gray-500/30';
            fileIcon.innerHTML = '<i data-lucide="file-text" class="w-3 h-3 text-gray-400"></i>';
        } else if (['pdf'].includes(extension)) {
            // PDF file
            fileIcon.className += ' bg-red-600/20 border border-red-500/30';
            fileIcon.innerHTML = '<i data-lucide="file-text" class="w-3 h-3 text-red-400"></i>';
        } else if (['docx', 'doc', 'odt'].includes(extension)) {
            // Document file
            fileIcon.className += ' bg-blue-600/20 border border-blue-500/30';
            fileIcon.innerHTML = '<i data-lucide="file-text" class="w-3 h-3 text-blue-400"></i>';
        } else {
            // Generic file
            fileIcon.className += ' bg-purple-600/20 border border-purple-500/30';
            fileIcon.innerHTML = '<i data-lucide="file" class="w-3 h-3 text-purple-400"></i>';
        }
        
        filePreview.classList.remove('hidden');
        lucide.createIcons();
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    document.getElementById('removeFile').addEventListener('click', function() {
        uploadedFile = null;
        filePreview.classList.add('hidden');
        fileInput.value = '';
    });

    toolsBtn.addEventListener('click', function() {
        toolsSelected = !toolsSelected;
        
        if (toolsSelected) {
            this.classList.add('bg-purple-600/20', 'border', 'border-purple-500/30');
            this.querySelector('i').classList.remove('text-gray-500');
            this.querySelector('i').classList.add('text-purple-400');
            showToast('Tools enabled for next message', 'info', 2000);
        } else {
            this.classList.remove('bg-purple-600/20', 'border', 'border-purple-500/30');
            this.querySelector('i').classList.add('text-gray-500');
            this.querySelector('i').classList.remove('text-purple-400');
            showToast('Tools disabled', 'info', 2000);
        }
    });

    // Event listeners
    sendBtn.addEventListener('click', sendMessage);

    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    /**
     * 
     * Fonction pour le choix d'un model et le sauvegarde directe dans le localStorage
     * 
     * 
     */
    if (modelBtn) {
        modelBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Create enhanced dropdown
            const dropdown = document.createElement('div');
            dropdown.className = 'absolute bottom-full mb-2 left-0 glass rounded-xl py-2 min-w-[200px] z-50 shadow-2xl border border-purple-500/20';
            dropdown.innerHTML = availableModels.map(model => {
                const isSelected = model === currentModel;
                const descriptions = {
                    'GPT-4': 'Most capable model',
                    'GPT-3.5-Turbo': 'Fast and efficient',
                    'Claude-3': 'Great for analysis',
                    'Gemini-Pro': 'Multimodal AI',
                    'GPT-4-Turbo': 'Latest GPT-4 version'
                };
                
                return `
                    <button class="w-full text-left px-4 py-3 hover:bg-purple-600/20 transition-all text-sm ${isSelected ? 'bg-purple-600/10' : ''} group" data-model="${model}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="font-medium ${isSelected ? 'text-purple-400' : 'text-gray-200'}">${model}</div>
                                <div class="text-xs text-gray-500">${descriptions[model] || 'AI Model'}</div>
                            </div>
                            ${isSelected ? '<i data-lucide="check" class="w-4 h-4 text-purple-400"></i>' : ''}
                        </div>
                    </button>
                `;
            }).join('');
            
            this.parentElement.style.position = 'relative';
            this.parentElement.appendChild(dropdown);
            lucide.createIcons();
            
            // Handle model selection
            dropdown.addEventListener('click', function(e) {
                if (e.target.closest('[data-model]')) {
                    const selectedModel = e.target.closest('[data-model]').getAttribute('data-model');
                    currentModel = selectedModel;
                    localStorage.setItem('selectedModel', currentModel);
                    
                    modelBtn.innerHTML = `
                        <span class="flex items-center space-x-2">
                            <span>${currentModel}</span>
                            <i data-lucide="chevron-down" class="w-3 h-3"></i>
                        </span>
                    `;
                    lucide.createIcons();
                    dropdown.remove();
                    showToast(`Switched to ${selectedModel}`, 'info', 2000);
                }
            });
            
            // Close dropdown when clicking outside
            setTimeout(() => {
                document.addEventListener('click', function closeDropdown() {
                    if (dropdown.parentElement) {
                        dropdown.remove();
                    }
                    document.removeEventListener('click', closeDropdown);
                });
            }, 0);
        });
    }

    /**
     * 
     * 
     * Fonction pour lancer un nouveau Chat avec l'IA 
     * 
     * 
     */
    async function startNewChat() {
        const apiUrl = BASE_API_URL+"/api/news_chat/?start=true";

        try {
            const response = await fetch(apiUrl, {
                method: "GET"
            });

            if (!response.ok) {
                throw new Error("Erreur HTTP: " + response.status);
            }

            const data = await response.json();
            console.log("Réponse API:", data);

            const chatId = data.id;
            return chatId;
        } catch (error) {
            console.error("Erreur lors de la création du chat :", error);
            return null;
        }
    }

    /**
     * 
     * 
     * Fonction pour envoyer le message en attente après la création du chat
     * 
     * 
     */
    async function sendPendingMessage(chatId) {
        if (!pendingMessage) return;
        
        addUserMessage(pendingMessage.text, pendingMessage.file, pendingMessage.tools);
        
        showTypingIndicator();
        
        const formData = new FormData();
        formData.append("message", pendingMessage.text);
        formData.append("chat_id", chatId);
        formData.append("model", currentModel || "gpt-4");
        formData.append("tools", pendingMessage.tools ? "true" : "false");

        if (pendingMessage.tools) {
            formData.append("tools", "true"); 
        }

        if (pendingMessage.file) {
            formData.append("file", pendingMessage.file);
        }

        try {
            const response = await fetch(BASE_API_URL+"/api/chat/", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            hideTypingIndicator();

            if (data.status === "success") {
                loadChatMessages(chatId);
                loadChatHistory();
            } else {
                addBotMessage("Erreur lors de l'envoi du message.");
                showToast("Erreur API", "error", 3000);
            }
        } catch (error) {
            hideTypingIndicator();
            addBotMessage("Erreur de connexion, veuillez réessayer.");
            showToast("Erreur réseau", "error", 3000);
        } finally {
            pendingMessage = null;
        }
    }

    /**
     * 
     * Fonction pour afficher les historique de chat 
     * 
     * 
     */
    async function loadChatHistory() {
        const container = document.getElementById("chatHistory");

        try {
            const response = await fetch(BASE_API_URL+"/api/chat_list/?getHistory=true");
            if (!response.ok) throw new Error("Erreur API");

            const data = await response.json();

            container.innerHTML = "";

            if (data.status === "success" && data.chat) {
                Object.values(data.chat).forEach(chat => {
                    const div = document.createElement("div");
                    div.className = "px-3 py-2.5 rounded-lg hover:bg-gray-850/60 transition-all cursor-pointer group";

                    const currentUrl = new URL(window.location.href);
                    const baseUrl = currentUrl.origin + currentUrl.pathname;
                    
                    div.addEventListener("click", () => {
                        window.location.href = `${baseUrl}?m=1&id=${chat.id}`;
                    });

                    const title = document.createElement("p");
                    title.className = "text-sm text-gray-300 truncate group-hover:text-gray-100";
                    title.textContent = chat.title;

                    const time = document.createElement("p");
                    time.className = "text-xs text-gray-500 mt-0.5";
                    time.textContent = chat.time_go;

                    div.appendChild(title);
                    div.appendChild(time);
                    container.appendChild(div);
                });
            } else {
                container.innerHTML = "<p class='text-gray-500 text-sm px-3'>Aucun historique trouvé</p>";
            }
        } catch (error) {
            console.error("Erreur lors du chargement de l'historique :", error);
            container.innerHTML = "<p class='text-red-500 text-sm px-3'>Erreur de chargement</p>";
        }
    }

    /**
     * 
     * Une fonction pour afficher les conversations dans l'interface 
     * 
     * 
     */
    async function loadChatMessages(chatId) {
        const chatContainer = document.getElementById("chatContainer");

        try {
            const response = await fetch(BASE_API_URL+`/api/chat_list/?getChatID=${encodeURIComponent(chatId)}`);
            if (!response.ok) throw new Error("Erreur API");

            const data = await response.json();

            // Vider le conteneur de chat
            const chatContent = chatContainer.querySelector('.max-w-4xl');
            if (chatContent) {
                chatContent.innerHTML = '';
            } else {
                chatContainer.innerHTML = '<div class="max-w-4xl mx-auto space-y-6"></div>';
            }

            const newChatContent = chatContainer.querySelector('.max-w-4xl');

            // Si pas de chat → message par défaut
            if (!data.chat || data.chat.length === 0) {
                newChatContent.innerHTML = `
                    <div class="flex items-start space-x-4 first-message-mobile">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 via-purple-600 to-purple-700 rounded-full flex items-center justify-center flex-shrink-0 shadow-lg">
                            <i data-lucide="sparkles" class="w-4 h-4 text-white"></i>
                        </div>
                        <div class="message-bubble rounded-2xl p-5 max-w-3xl">
                            <div class="prose-custom max-w-none">
                                <p><?=$_TEXT['bot']['default_message'];?></p>
                            </div>
                        </div>
                    </div>
                `;
                lucide.createIcons();
                return;
            }

            // Afficher les messages du chat
            data.chat.forEach(msg => {
                if (msg.role === "user") {
                    addUserMessage(msg.content, null, false);
                } else if (msg.role === "bot") {
                    addBotMessage(msg.content);
                }
            });

            lucide.createIcons();

        } catch (error) {
            console.error("Erreur lors du chargement du chat :", error);
            chatContainer.innerHTML = "<p class='text-red-500 p-4'>Erreur de chargement des messages.</p>";
        }
    }

    /**
     * 
     * Executer le fonction dans la page 
     * 
     */
    document.addEventListener("DOMContentLoaded", () => {
        // Récupérer l'ID du chat depuis les paramètres d'URL
        const urlParams = new URLSearchParams(window.location.search);
        const chatId = urlParams.get('id');
        
        if (chatId) {
            if (pendingMessage) {
                sendPendingMessage(chatId);
            }
            loadChatMessages(chatId);
        }
        
        // Charger l'historique des chats
        loadChatHistory();

        // Gestion des boutons de navigation
        document.querySelector('.new-chat-btn').addEventListener('click', function() {
            startNewChat().then(chatId => {
                if (chatId) {
                    const currentUrl = new URL(window.location.href);
                    const baseUrl = currentUrl.origin + currentUrl.pathname;
                    window.location.href = `${baseUrl}?m=1&id=${chatId}`;
                }
            });
        });

        // Fermer le sidebar sur mobile après clic sur un élément
        document.querySelectorAll('#chatHistory div').forEach(item => {
            item.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebarFunc();
                }
            });
        });
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 1024) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnMenuButton = menuButton && menuButton.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnMenuButton && sidebar.classList.contains('open')) {
                closeSidebarFunc();
            }
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebarFunc();
        }
    });
   </script>
</body>
</html>