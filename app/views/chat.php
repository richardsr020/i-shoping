<?php
require_once __DIR__ . '/../config.php';

$activeShopName = APP_NAME;

$backUrl = url('home');
$ref = (string)($_SERVER['HTTP_REFERER'] ?? '');
if ($ref !== '') {
    $u = parse_url($ref);
    $baseHost = (string)(parse_url((string)BASE_URL, PHP_URL_HOST) ?? '');
    $refHost = (string)($u['host'] ?? '');
    if ($baseHost !== '' && $refHost === $baseHost) {
        $backUrl = $ref;
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie - iShopping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/pages.css">
    <script>
        window.APP_URL = <?php echo json_encode((string)APP_URL); ?>;
    </script>
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        :root {
            --primary: var(--color-primary);
            --primary-dark: var(--color-primary-dark);
            --secondary: var(--color-primary);
            --dark: var(--color-black);
            --light: var(--color-white);
            --gray: var(--color-bg-secondary);
            --gray-dark: var(--color-text-muted);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --chat-vvh: 100dvh;
        }

        body {
            background-color: var(--color-bg-secondary);
            color: var(--color-text);
            line-height: 1.6;
            display: flex;
            height: 100vh;
            height: var(--chat-vvh, 100dvh);
            overflow: hidden;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .card {
            background: var(--color-bg);
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 350px;
            background-color: var(--color-bg);
            color: var(--color-text);
            padding: 20px 0;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: none;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 24px;
        }

        .logo h1 {
            font-size: 20px;
        }

        .user-info {
            padding: 0 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: none;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .user-details h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .user-details p {
            font-size: 12px;
            color: #ccc;
        }

        .nav-links {
            list-style: none;
            padding: 0 20px;
        }

        .nav-links li {
            margin-bottom: 5px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #ccc;
            transition: all 0.3s;
            border-radius: 4px;
        }

        .nav-links a:hover, .nav-links a.active {
            background-color: var(--color-bg-tertiary);
            color: var(--color-text);
        }

        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* === CONTENU PRINCIPAL === */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
        }

        /* Header */
        .top-header {
            background-color: var(--color-bg);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            border: none;
            background: var(--color-bg-tertiary);
            cursor: pointer;
            font-weight: 700;
        }

        .back-btn i {
            color: var(--color-text-muted);
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 20px;
            border: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-action-item {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .header-action-item i {
            font-size: 18px;
            color: var(--color-text-muted);
        }

        .notification-badge {
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .notification-wrapper {
            position: relative;
        }

        /* Contenu de la messagerie */
        .messaging-content {
            flex: 1;
            display: flex;
            padding: 0;
            min-width: 0;
            min-height: 0;
        }

        /* Liste des conversations */
        .conversations-sidebar {
            width: 350px;
            background-color: var(--color-bg);
            border-right: none;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .conversations-header {
            padding: 20px;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .conversations-header h2 {
            font-size: 18px;
        }

        .conversation-filters {
            padding: 15px 20px;
            border-bottom: none;
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 20px;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
        }

        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 15px 20px;
            border-bottom: none;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .conversation-item:hover {
            background-color: var(--color-bg-secondary);
        }

        .conversation-item.active {
            background-color: var(--color-bg-tertiary);
            border-left: none;
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--color-bg-tertiary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 20px;
            flex-shrink: 0;
        }

        .conversation-details {
            flex: 1;
            min-width: 0;
        }

        .conversation-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }

        .conversation-name {
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 12px;
            color: var(--color-text-muted);
        }

        .conversation-preview {
            font-size: 13px;
            color: var(--color-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-badge {
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            margin-top: 5px;
        }

        /* Zone de discussion */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--color-bg-secondary);
            min-width: 0;
            min-height: 0;
            height: 100%;
            max-height: 100%;
            overflow: hidden;
        }

        .chat-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 999;
        }

        .conversation-toggle-btn {
            display: none;
            border: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            padding: 8px 10px;
            border-radius: 10px;
            cursor: pointer;
            margin-right: 10px;
        }

        .chat-header {
            padding: 15px 25px;
            background-color: var(--color-bg);
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 0;
            position: relative;
            z-index: 5;
        }

        .chat-partner {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
            flex: 1;
        }

        .chat-partner-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: var(--color-bg-tertiary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .chat-partner-info {
            min-width: 0;
        }

        .chat-partner-info h3 {
            font-size: 16px;
            margin-bottom: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-partner-info p {
            font-size: 13px;
            color: var(--color-text-muted);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-actions {
            display: flex;
            gap: 15px;
        }

        .chat-action-btn {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            font-size: 16px;
        }

        .chat-action-btn:hover {
            color: var(--primary);
        }

        .messages-container {
            flex: 1 1 auto;
            padding: 20px;
            overflow-y: auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
        }

        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            margin: 0 4px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .message.sent {
            align-self: flex-end;
            background-color: var(--primary);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.received {
            align-self: flex-start;
            background-color: var(--color-bg-tertiary);
            border: none;
            border-bottom-left-radius: 5px;
            color: var(--color-text);
        }

        .message-time {
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.8;
            text-align: right;
        }

        .message.received .message-time {
            text-align: left;
        }

        .message-status {
            font-size: 12px;
            margin-top: 3px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 3px;
        }

        /* Zone de saisie */
        .message-input-container {
            flex: 0 0 auto;
            padding: 20px;
            background-color: var(--color-bg);
            border-top: none;
            width: 100%;
            min-width: 0;
            position: sticky;
            bottom: 0;
            z-index: 6;
            box-shadow: 0 -6px 16px rgba(0,0,0,0.08);
        }

        .input-actions {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .input-action-btn {
            background: none;
            border: none;
            color: var(--color-text-muted);
            cursor: pointer;
            font-size: 18px;
        }

        .input-action-btn:hover {
            color: var(--primary);
        }

        .message-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            width: 100%;
            min-width: 0;
        }

        .message-input {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: 24px;
            resize: none;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.4;
            min-height: 44px;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            width: 100%;
            min-width: 0;
            max-height: 140px;
        }

        .message-input:focus {
            outline: none;
        }

        .send-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .send-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }

        .send-btn:disabled {
            background-color: var(--color-bg-tertiary);
            color: var(--color-text-muted);
            cursor: not-allowed;
            transform: none;
        }

        /* État vide */
        .empty-state {
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            color: var(--color-text-muted);
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: var(--color-text-muted);
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--color-text);
        }

        /* === RESPONSIVE === */
        @media (max-width: 1200px) {
            .sidebar {
                width: 300px;
            }
            
            .conversations-sidebar {
                width: 300px;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                width: 80px;
                overflow: hidden;
            }
            
            .logo h1, .user-details, .nav-links span {
                display: none;
            }
            
            .logo, .user-info {
                justify-content: center;
                padding: 15px 10px;
            }
            
            .nav-links {
                padding: 0 10px;
            }
            
            .nav-links a {
                justify-content: center;
                padding: 15px;
            }
            
            .nav-links i {
                margin-right: 0;
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
                height: 100vh;
                height: var(--chat-vvh, 100dvh);
                overflow: hidden;
            }

            .main-content {
                padding-left: max(12px, env(safe-area-inset-left));
                padding-right: max(12px, env(safe-area-inset-right));
                padding-bottom: max(10px, env(safe-area-inset-bottom));
            }

            .conversation-toggle-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .messaging-content {
                flex-direction: column;
                height: 100%;
                overflow: hidden;
            }

            .chat-area {
                border-radius: 14px;
                overflow: hidden;
            }

            .chat-header {
                padding: 12px 14px;
            }

            .messages-container {
                padding: 14px 12px;
            }

            .message-input-container {
                padding: 12px;
                padding-bottom: calc(12px + env(safe-area-inset-bottom));
            }

            .message-input-wrapper {
                gap: 8px;
            }

            .message-input {
                font-size: 16px;
            }

            .send-btn {
                width: 40px;
                height: 40px;
            }

            .conversations-sidebar {
                position: fixed;
                top: max(8px, env(safe-area-inset-top));
                left: max(8px, env(safe-area-inset-left));
                bottom: max(8px, env(safe-area-inset-bottom));
                width: min(360px, calc(100vw - 16px));
                max-width: calc(100vw - 16px);
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                z-index: 1000;
                border-right: none;
                border-bottom: none;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                border-radius: 14px;
            }

            .conversations-sidebar.open {
                transform: translateX(0);
            }

            .chat-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0,0,0,0.5);
                z-index: 999;
            }

            .chat-overlay.open {
                display: block;
            }
            
            .top-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-bar {
                max-width: 100%;
                margin: 0;
            }
            
            .message {
                max-width: 85%;
            }
        }
    </style>
</head>
<body>

    <div class="chat-overlay" id="chat-overlay"></div>

    <!-- === CONTENU PRINCIPAL === -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            
        </header>

        <!-- Contenu de la messagerie -->
        <div class="messaging-content">
            <!-- Liste des conversations -->
            <div class="conversations-sidebar">
                <div class="conversations-header">
                    <h2>Messages</h2>
                    <a class="back-btn" href="<?php echo htmlspecialchars((string)url('home')); ?>" title="Retour">
                        <i class="fas fa-arrow-left"></i>
                        Retour
                    </a>
                    <!-- <div class="search-bar">
                        <input type="text" placeholder="Rechercher une conversation...">
                    </div> -->
                    <div class="header-actions">
                        <button id="theme-toggle" class="theme-toggle" aria-label="Changer de thème">
                            <i class="fas fa-moon"></i>
                        </button>
                        
                    </div>
                </div>


                <div class="conversations-list">
                    
                </div>
            </div>

            <!-- Zone de discussion -->
            <div class="chat-area">
                <div class="chat-header">
                    <button type="button" class="conversation-toggle-btn" id="conversation-toggle-btn" aria-label="Conversations">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="chat-partner">
                        <div class="chat-partner-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="chat-partner-info">
                            <h3></h3>
                            <p></p>
                        </div>
                    </div>
                    
                </div>

                <div class="chat-status" style="display:none; padding: 10px 20px; background: var(--color-bg); border-bottom: none; color: var(--color-text-muted);"></div>

                <div class="messages-container">
                    
                </div>

                <!-- Zone de saisie -->
                <div class="message-input-container">
                    
                    <div class="message-input-wrapper">
                        <textarea class="message-input" placeholder="Tapez votre message..." rows="1"></textarea>
                        <button class="send-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.CURRENT_USER_ID = <?php echo (int)($_SESSION['user_id'] ?? 0); ?>;
    </script>
    <script src="<?php echo htmlspecialchars((string)BASE_URL); ?>/public/js/theme.js"></script>
    <script src="<?php echo htmlspecialchars((string)BASE_URL); ?>/public/js/chat.js"></script>
    <script>
        (function(){
            const root = document.documentElement;
            let rafId = 0;

            function applyViewportHeight(){
                rafId = 0;
                const vv = window.visualViewport;
                const height = vv ? vv.height : window.innerHeight;
                const safeHeight = Math.max(320, Math.round(height || 0));
                root.style.setProperty('--chat-vvh', safeHeight + 'px');
            }

            function scheduleApply(){
                if(rafId) return;
                rafId = window.requestAnimationFrame(applyViewportHeight);
            }

            applyViewportHeight();
            window.addEventListener('resize', scheduleApply, { passive: true });
            window.addEventListener('orientationchange', scheduleApply);

            if(window.visualViewport){
                window.visualViewport.addEventListener('resize', scheduleApply);
                window.visualViewport.addEventListener('scroll', scheduleApply);
            }

            const input = document.querySelector('.message-input');
            if(input){
                input.addEventListener('focus', function(){ setTimeout(scheduleApply, 60); });
                input.addEventListener('blur', function(){ setTimeout(scheduleApply, 60); });
            }
        })();
    </script>
    <script>
        (function(){
            function qs(sel){ return document.querySelector(sel); }
            const sidebar = qs('.conversations-sidebar');
            const overlay = qs('#chat-overlay');
            const btn = qs('#conversation-toggle-btn');
            if(!sidebar || !overlay || !btn) return;

            function isMobile(){
                try { return window.matchMedia('(max-width: 768px)').matches; } catch(e) { return false; }
            }

            function openSidebar(){
                sidebar.classList.add('open');
                overlay.classList.add('open');
            }

            function closeSidebar(){
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            }

            btn.addEventListener('click', function(){
                if(!isMobile()) return;
                if(sidebar.classList.contains('open')) closeSidebar(); else openSidebar();
            });

            overlay.addEventListener('click', closeSidebar);

            const list = qs('.conversations-list');
            if(list){
                list.addEventListener('click', function(e){
                    const item = e.target && e.target.closest ? e.target.closest('.conversation-item') : null;
                    if(item && isMobile()) closeSidebar();
                });
            }

            window.addEventListener('resize', function(){
                if(!isMobile()) closeSidebar();
            });
        })();
    </script>
</body>
</html>
