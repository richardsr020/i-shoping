<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie - iShopping</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --primary: #ff4500;
            --primary-dark: #e03d00;
            --secondary: #1e90ff;
            --dark: #000;
            --light: #fff;
            --gray: #f8f9fa;
            --gray-dark: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
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
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 350px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
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
            border-bottom: 1px solid #333;
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
            background-color: #333;
            color: white;
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
        }

        /* Header */
        .top-header {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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
            border: 1px solid #ddd;
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
            color: var(--gray-dark);
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
        }

        /* Liste des conversations */
        .conversations-sidebar {
            width: 350px;
            background-color: white;
            border-right: 1px solid #eee;
            display: flex;
            flex-direction: column;
        }

        .conversations-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .conversations-header h2 {
            font-size: 18px;
        }

        .conversation-filters {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .filter-btn {
            padding: 8px 15px;
            border: 1px solid #eee;
            border-radius: 20px;
            background: white;
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
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .conversation-item:hover {
            background-color: #f9f9f9;
        }

        .conversation-item.active {
            background-color: #f0f7ff;
            border-left: 3px solid var(--secondary);
        }

        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-dark);
            font-size: 18px;
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
            color: var(--gray-dark);
        }

        .conversation-preview {
            font-size: 13px;
            color: var(--gray-dark);
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
            background-color: #f9f9f9;
        }

        .chat-header {
            padding: 15px 25px;
            background-color: white;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chat-partner {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .chat-partner-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-dark);
            font-size: 16px;
        }

        .chat-partner-info h3 {
            font-size: 16px;
            margin-bottom: 3px;
        }

        .chat-partner-info p {
            font-size: 13px;
            color: var(--gray-dark);
        }

        .chat-actions {
            display: flex;
            gap: 15px;
        }

        .chat-action-btn {
            background: none;
            border: none;
            color: var(--gray-dark);
            cursor: pointer;
            font-size: 16px;
        }

        .chat-action-btn:hover {
            color: var(--primary);
        }

        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            line-height: 1.4;
        }

        .message.sent {
            align-self: flex-end;
            background-color: var(--primary);
            color: white;
            border-bottom-right-radius: 5px;
        }

        .message.received {
            align-self: flex-start;
            background-color: white;
            border: 1px solid #eee;
            border-bottom-left-radius: 5px;
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
            padding: 20px;
            background-color: white;
            border-top: 1px solid #eee;
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
            color: var(--gray-dark);
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
        }

        .message-input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 24px;
            resize: none;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.4;
            max-height: 120px;
            min-height: 44px;
        }

        .message-input:focus {
            outline: none;
            border-color: var(--primary);
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
        }

        .send-btn:hover {
            background-color: var(--primary-dark);
            transform: scale(1.05);
        }

        .send-btn:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        /* État vide */
        .empty-state {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px;
            color: var(--gray-dark);
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #ddd;
        }

        .empty-state h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #666;
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
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                padding: 10px 0;
            }
            
            .logo, .user-info {
                display: none;
            }
            
            .nav-links {
                display: flex;
                overflow-x: auto;
                padding: 0 10px;
            }
            
            .nav-links li {
                flex: 0 0 auto;
                margin-bottom: 0;
            }
            
            .nav-links a {
                padding: 10px 15px;
                border-radius: 20px;
                margin-right: 5px;
            }
            
            .messaging-content {
                flex-direction: column;
            }
            
            .conversations-sidebar {
                width: 100%;
                height: 300px;
                border-right: none;
                border-bottom: 1px solid #eee;
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
    <!-- === SIDEBAR === -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-shopping-bag"></i>
            <h1>iShopping</h1>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">FB</div>
            <div class="user-details">
                <h3>Fashionista Boutique</h3>
                <p>En ligne</p>
            </div>
        </div>
        
        <ul class="nav-links">
            <li><a href="#"><i class="fas fa-home"></i> <span>Tableau de bord</span></a></li>
            <li><a href="#"><i class="fas fa-shopping-bag"></i> <span>Produits</span></a></li>
            <li><a href="#"><i class="fas fa-receipt"></i> <span>Commandes</span></a></li>
            <li><a href="#" class="active"><i class="fas fa-comments"></i> <span>Messagerie</span></a></li>
            <li><a href="#"><i class="fas fa-chart-bar"></i> <span>Analytics</span></a></li>
            <li><a href="#"><i class="fas fa-cog"></i> <span>Paramètres</span></a></li>
        </ul>
    </div>

    <!-- === CONTENU PRINCIPAL === -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="search-bar">
                <input type="text" placeholder="Rechercher une conversation...">
            </div>
            <div class="header-actions">
                <div class="header-action-item">
                    <div class="notification-wrapper">
                        <i class="far fa-bell"></i>
                    </div>
                    <span>Notifications</span>
                </div>
                <div class="header-action-item">
                    <i class="far fa-question-circle"></i>
                    <span>Aide</span>
                </div>
            </div>
        </header>

        <!-- Contenu de la messagerie -->
        <div class="messaging-content">
            <!-- Liste des conversations -->
            <div class="conversations-sidebar">
                <div class="conversations-header">
                    <h2>Messages</h2>
                    <button class="btn btn-primary">Nouveau message</button>
                </div>

                <div class="conversation-filters">
                    <button class="filter-btn active">Tous</button>
                    <button class="filter-btn">Non lus</button>
                    <button class="filter-btn">Acheteurs</button>
                    <button class="filter-btn">Boutiques</button>
                </div>

                <div class="conversations-list">
                    
                </div>
            </div>

            <!-- Zone de discussion -->
            <div class="chat-area">
                <div class="chat-header">
                    <div class="chat-partner">
                        <div class="chat-partner-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="chat-partner-info">
                            <h3></h3>
                            <p></p>
                        </div>
                    </div>
                    <div class="chat-actions">
                        <button class="chat-action-btn"><i class="fas fa-phone"></i></button>
                        <button class="chat-action-btn"><i class="fas fa-video"></i></button>
                        <button class="chat-action-btn"><i class="fas fa-info-circle"></i></button>
                    </div>
                </div>

                <div class="messages-container">
                    
                </div>

                <!-- Zone de saisie -->
                <div class="message-input-container">
                    <div class="input-actions">
                        <button class="input-action-btn"><i class="fas fa-paperclip"></i></button>
                        <button class="input-action-btn"><i class="fas fa-image"></i></button>
                        <button class="input-action-btn"><i class="fas fa-smile"></i></button>
                    </div>
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
    <script src="<?php echo htmlspecialchars((string)BASE_URL); ?>/public/js/chat.js"></script>
</body>
</html>