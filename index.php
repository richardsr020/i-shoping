<?php
// i-shoping/index.php

// Démarrer la session
session_start();

// Déterminer la page demandée
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Inclure l'en-tête
require_once __DIR__ . '/app/views/header.php';

// Charger la page demandée
switch($page) {
    case 'home':
        require_once __DIR__ . '/app/views/home.php';
        break;
        
    case 'login':
        require_once __DIR__ . '/app/views/login.php';
        break;
        
    case 'register':
        require_once __DIR__ . '/app/views/register.php';
        break;
        
    case 'create_shop':
        require_once __DIR__ . '/app/views/create_shop.php';
        break;
        
    case 'dashboard_shop':
        require_once __DIR__ . '/app/views/dashboard_shop.php';
        break;
        
    case 'profile_shop':
        require_once __DIR__ . '/app/views/profile_shop.php';
        break;
    case 'dashboard_admin':
        require_once __DIR__ . '/app/views/dashboard_admin.php';
        break;
    case 'create_product':
        require_once __DIR__ . '/app/views/create_product.php';
        break;
        
    case 'product_detail':
        require_once __DIR__ . '/app/views/product_detail.php';
        break;
    
    case 'chat':
        require_once __DIR__ . '/app/views/chat.php';
        break;
        
    default:
        // Page non trouvée
        echo '<div class="container">';
        echo '<h1>404 - Page non trouvée</h1>';
        echo '<p>La page que vous recherchez n\'existe pas.</p>';
        echo '</div>';
        break;
}

// Inclure le pied de page
require_once __DIR__ . '/app/views/footer.php';
?>