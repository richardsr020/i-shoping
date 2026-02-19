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
        :root {
            --chat-vvh: 100dvh;
            --chat-border: rgba(0, 0, 0, 0.12);
            --chat-shadow: 0 12px 32px rgba(0, 0, 0, 0.18);
            --chat-shell-height-desktop: var(--chat-vvh, 100dvh);
            --chat-shell-height-mobile: calc(
                var(--chat-vvh, 100dvh)
                - max(8px, env(safe-area-inset-top))
                - max(8px, env(safe-area-inset-bottom))
            );
        }

        [data-theme="dark"] {
            --chat-border: rgba(255, 255, 255, 0.16);
            --chat-shadow: 0 14px 34px rgba(0, 0, 0, 0.45);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: var(--color-bg-secondary);
            color: var(--color-text);
            height: 100vh;
            height: var(--chat-vvh, 100dvh);
            overflow: hidden;
        }

        .main-content {
            width: 100%;
            height: var(--chat-shell-height-desktop);
            max-height: var(--chat-shell-height-desktop);
            min-width: 0;
            min-height: 0;
            padding: 0 !important;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-header {
            display: none;
        }

        .messaging-content {
            flex: 1;
            min-width: 0;
            min-height: 0;
            display: grid;
            grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
            grid-template-rows: minmax(0, 1fr);
            background: var(--color-bg-secondary);
            gap: 0;
            height: var(--chat-shell-height-desktop);
            max-height: var(--chat-shell-height-desktop);
            overflow: hidden;
        }

        .conversations-sidebar {
            min-width: 0;
            min-height: 0;
            background: var(--color-bg);
            border-right: 1px solid var(--chat-border);
            display: flex;
            flex-direction: column;
        }

        .conversations-header {
            padding: 16px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            grid-template-areas:
                "title actions"
                "back back";
            gap: 10px 12px;
            border-bottom: 1px solid var(--chat-border);
        }

        .conversations-header h2 {
            grid-area: title;
            margin: 0;
            font-size: 18px;
            line-height: 1.2;
        }

        .header-actions {
            grid-area: actions;
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
        }

        .theme-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .theme-toggle:hover {
            background: var(--color-primary);
            color: var(--color-white);
        }

        .back-btn {
            grid-area: back;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            max-width: 100%;
            padding: 8px 12px;
            border-radius: 999px;
            border: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            font-size: 13px;
            font-weight: 700;
        }

        .back-btn i {
            color: var(--color-text-muted);
        }

        .conversations-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 8px 0;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            margin: 0 8px;
            border-radius: 12px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .conversation-item:hover {
            background: var(--color-bg-secondary);
        }

        .conversation-item.active {
            background: var(--color-bg-tertiary);
        }

        .conversation-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--color-bg-tertiary);
            color: var(--color-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 44px;
            font-size: 18px;
        }

        .conversation-details {
            min-width: 0;
            flex: 1;
        }

        .conversation-header {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .conversation-name {
            font-weight: 700;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 11px;
            color: var(--color-text-muted);
            flex: 0 0 auto;
        }

        .conversation-preview {
            font-size: 12px;
            color: var(--color-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-area {
            min-width: 0;
            min-height: 0;
            background: var(--color-bg-secondary);
            display: grid;
            grid-template-rows: auto auto minmax(0, 1fr);
            height: 100%;
            max-height: 100%;
            overflow: hidden;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 0;
            padding: 14px 20px;
            background: var(--color-bg);
            border-bottom: 1px solid var(--chat-border);
        }

        .conversation-toggle-btn {
            display: none;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            cursor: pointer;
            flex: 0 0 34px;
        }

        .chat-partner {
            min-width: 0;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-partner-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--color-bg-tertiary);
            color: var(--color-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            font-size: 16px;
        }

        .chat-partner-info {
            min-width: 0;
        }

        .chat-partner-info h3 {
            margin: 0;
            font-size: 15px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-partner-info p {
            margin: 2px 0 0;
            font-size: 12px;
            color: var(--color-text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .chat-status {
            border-bottom: 1px solid var(--chat-border);
        }

        .chat-messages-shell {
            min-width: 0;
            min-height: 0;
            height: 100%;
            max-height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .messages-container {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: min(78%, 640px);
            padding: 11px 14px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.4;
            word-break: break-word;
            overflow-wrap: anywhere;
            box-shadow: var(--shadow-sm);
        }

        .message.sent {
            align-self: flex-end;
            color: var(--color-white);
            background: var(--color-primary);
            border-bottom-right-radius: 6px;
        }

        .message.received {
            align-self: flex-start;
            color: var(--color-text);
            background: var(--color-bg-tertiary);
            border-bottom-left-radius: 6px;
        }

        .message-time {
            margin-top: 4px;
            font-size: 11px;
            opacity: 0.8;
            text-align: right;
        }

        .message.received .message-time {
            text-align: left;
        }

        .message-product {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            padding: 6px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.06);
        }

        .message.sent .message-product {
            background: rgba(255, 255, 255, 0.18);
        }

        .message-product-thumb {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            flex: 0 0 50px;
        }

        .message-product-name {
            font-size: 12px;
            line-height: 1.25;
            font-weight: 700;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

        .message-input-container {
            width: 100%;
            padding: 12px 16px;
            background: transparent;
            border-top: none;
            box-shadow: none;
        }

        .message-input-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            min-width: 0;
        }

        .message-input {
            flex: 1;
            min-width: 0;
            min-height: 44px;
            max-height: 140px;
            padding: 11px 14px;
            border: none;
            border-radius: 18px;
            resize: none;
            background: var(--color-bg-tertiary);
            color: var(--color-text);
            font-family: inherit;
            font-size: 14px;
            line-height: 1.35;
        }

        .message-input:focus {
            outline: 2px solid var(--color-primary);
            outline-offset: 2px;
        }

        .send-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: none;
            background: var(--color-primary);
            color: var(--color-white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s ease, background-color 0.2s ease;
            flex: 0 0 44px;
        }

        .send-btn:hover {
            background: var(--color-primary-dark);
            transform: scale(1.04);
        }

        .send-btn:disabled {
            background: var(--color-bg-tertiary);
            color: var(--color-text-muted);
            transform: none;
            cursor: not-allowed;
        }

        .compose-fab {
            position: fixed;
            right: max(16px, env(safe-area-inset-right));
            bottom: max(16px, env(safe-area-inset-bottom));
            width: 56px;
            height: 56px;
            border: none;
            border-radius: 50%;
            background: var(--color-primary);
            color: var(--color-white);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 14px 26px rgba(0,0,0,0.24);
            cursor: pointer;
            z-index: 1002;
            transition: transform 0.15s ease, background-color 0.2s ease;
            touch-action: none;
            user-select: none;
            -webkit-user-select: none;
        }

        .compose-fab:hover {
            background: var(--color-primary-dark);
            transform: scale(1.05);
        }

        .compose-fab.dragging {
            transition: none;
            transform: none;
            cursor: grabbing;
        }

        .compose-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(8, 12, 20, 0.32);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            z-index: 1100;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 16px;
            padding-right: max(16px, env(safe-area-inset-right));
            padding-bottom: max(16px, env(safe-area-inset-bottom));
        }

        .compose-modal.open {
            display: flex;
        }

        .compose-card {
            width: min(560px, calc(100vw - 24px));
            background: rgba(255, 255, 255, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 16px;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.22);
            overflow: hidden;
        }

        [data-theme="dark"] .compose-card {
            background: rgba(24, 26, 30, 0.62);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .compose-input-container {
            padding: 10px 12px;
        }

        .chat-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 1000;
        }

        .chat-overlay.open {
            display: block;
        }

        @media (max-width: 1024px) {
            .messaging-content {
                grid-template-columns: minmax(270px, 320px) minmax(0, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                padding-left: max(8px, env(safe-area-inset-left)) !important;
                padding-right: max(8px, env(safe-area-inset-right)) !important;
                padding-top: max(8px, env(safe-area-inset-top)) !important;
                padding-bottom: 0 !important;
                height: var(--chat-shell-height-mobile);
                max-height: var(--chat-shell-height-mobile);
            }

            .messaging-content {
                grid-template-columns: minmax(0, 1fr);
                height: 100%;
                max-height: 100%;
            }

            .chat-area {
                border-radius: 14px;
                border: 1px solid var(--chat-border);
                background: var(--color-bg-secondary);
            }

            .conversation-toggle-btn {
                display: inline-flex;
            }

            .chat-header {
                padding: 12px;
            }

            .messages-container {
                padding: 12px;
            }

            .message {
                max-width: 88%;
            }

            .message-input {
                font-size: 16px;
            }

            .send-btn {
                width: 40px;
                height: 40px;
                flex-basis: 40px;
            }

            .compose-fab {
                width: 52px;
                height: 52px;
            }

            .compose-modal {
                justify-content: center;
                align-items: flex-end;
                padding: 12px;
                padding-right: max(12px, env(safe-area-inset-right));
                padding-bottom: max(12px, env(safe-area-inset-bottom));
            }

            .compose-card {
                width: calc(100vw - 24px);
            }

            .conversations-sidebar {
                position: fixed;
                top: max(8px, env(safe-area-inset-top));
                left: max(8px, env(safe-area-inset-left));
                bottom: max(8px, env(safe-area-inset-bottom));
                width: min(360px, calc(100vw - 16px));
                max-width: calc(100vw - 16px);
                border: 1px solid var(--chat-border);
                border-radius: 16px;
                box-shadow: var(--chat-shadow);
                z-index: 1001;
                transform: translateX(-108%);
                transition: transform 0.2s ease;
            }

            .conversations-sidebar.open {
                transform: translateX(0);
            }
        }

        @media (max-width: 420px) {
            .conversations-header {
                padding: 12px;
            }

            .conversation-item {
                margin: 0 6px;
                padding: 10px;
            }

            .message {
                max-width: 92%;
                font-size: 13px;
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

                <div class="chat-messages-shell">
                    <div class="messages-container">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="compose-fab" id="compose-fab" aria-label="Nouveau message">
        <i class="fas fa-pen"></i>
    </button>

    <div class="compose-modal" id="compose-modal" aria-hidden="true">
        <div class="compose-card" role="dialog" aria-modal="true" aria-label="Écrire un message">
            <div class="message-input-container compose-input-container">
                <div class="message-input-wrapper">
                    <textarea class="message-input" placeholder="Tapez votre message..." rows="3"></textarea>
                    <button class="send-btn" type="button">
                        <i class="fas fa-paper-plane"></i>
                    </button>
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
                const safeHeight = Math.round(height || window.innerHeight || 0);
                if(safeHeight < 180) return;
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
            const fab = qs('#compose-fab');
            const modal = qs('#compose-modal');
            if(!fab || !modal) return;
            let suppressClickUntil = 0;
            let dragState = null;

            function openCompose(){
                if(Date.now() < suppressClickUntil) return;
                modal.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
                const input = modal.querySelector('.message-input');
                if(input){ setTimeout(function(){ input.focus(); }, 30); }
            }

            function closeCompose(){
                modal.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            }

            function clamp(value, min, max){
                return Math.min(Math.max(value, min), max);
            }

            function clampFabPosition(){
                const left = parseFloat(fab.style.left);
                const top = parseFloat(fab.style.top);
                if(!Number.isFinite(left) || !Number.isFinite(top)) return;
                const rect = fab.getBoundingClientRect();
                const maxLeft = Math.max(0, window.innerWidth - rect.width);
                const maxTop = Math.max(0, window.innerHeight - rect.height);
                fab.style.left = clamp(left, 0, maxLeft) + 'px';
                fab.style.top = clamp(top, 0, maxTop) + 'px';
            }

            function onPointerMove(e){
                if(!dragState || e.pointerId !== dragState.pointerId) return;
                const dx = e.clientX - dragState.startX;
                const dy = e.clientY - dragState.startY;

                if(!dragState.dragged && Math.hypot(dx, dy) > 6){
                    dragState.dragged = true;
                    fab.classList.add('dragging');
                }
                if(!dragState.dragged) return;

                const maxLeft = Math.max(0, window.innerWidth - dragState.width);
                const maxTop = Math.max(0, window.innerHeight - dragState.height);
                const nextLeft = clamp(dragState.originLeft + dx, 0, maxLeft);
                const nextTop = clamp(dragState.originTop + dy, 0, maxTop);

                fab.style.left = nextLeft + 'px';
                fab.style.top = nextTop + 'px';
                fab.style.right = 'auto';
                fab.style.bottom = 'auto';
                e.preventDefault();
            }

            function endPointerDrag(e){
                if(!dragState || e.pointerId !== dragState.pointerId) return;
                if(dragState.dragged){
                    suppressClickUntil = Date.now() + 260;
                }
                dragState = null;
                fab.classList.remove('dragging');
                if(typeof fab.releasePointerCapture === 'function'){
                    try { fab.releasePointerCapture(e.pointerId); } catch(_err) {}
                }
            }

            fab.addEventListener('pointerdown', function(e){
                if(e.button !== 0 && e.pointerType !== 'touch' && e.pointerType !== 'pen') return;
                const rect = fab.getBoundingClientRect();
                dragState = {
                    pointerId: e.pointerId,
                    startX: e.clientX,
                    startY: e.clientY,
                    originLeft: rect.left,
                    originTop: rect.top,
                    width: rect.width,
                    height: rect.height,
                    dragged: false
                };
                if(typeof fab.setPointerCapture === 'function'){
                    try { fab.setPointerCapture(e.pointerId); } catch(_err) {}
                }
            });

            window.addEventListener('pointermove', onPointerMove, { passive: false });
            window.addEventListener('pointerup', endPointerDrag);
            window.addEventListener('pointercancel', endPointerDrag);
            window.addEventListener('resize', clampFabPosition, { passive: true });

            fab.addEventListener('click', function(e){
                if(Date.now() < suppressClickUntil){
                    e.preventDefault();
                    return;
                }
                openCompose();
            });

            modal.addEventListener('click', function(e){
                if(e.target === modal){ closeCompose(); }
            });

            window.addEventListener('keydown', function(e){
                if(e.key === 'Escape' && modal.classList.contains('open')){ closeCompose(); }
            });

            document.addEventListener('chat:message-sent', closeCompose);
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
