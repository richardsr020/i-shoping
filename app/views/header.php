<?php
/**
 * Header global pour toutes les pages
 * Inclut le système de thème et la navigation
 */
require_once __DIR__ . '/../models/User.php';
$pageTitle = isset($_SESSION['view_data']['title']) ? $_SESSION['view_data']['title'] : APP_NAME;
$currentPage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Thème CSS global -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/theme.css">
    
    <!-- CSS des pages -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/pages.css">
    
    <!-- CSS menu flottant (pour la page d'accueil) -->
    <?php if ($currentPage === 'home'): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/floating-menu.css">
    <?php endif; ?>
    
    <!-- Styles spécifiques de la page (si nécessaire) -->
    <?php if (isset($additionalCSS)): ?>
        <?php echo $additionalCSS; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header Navigation -->
    <header>
        <div class="container">
            <div class="header-content">
                <a href="<?php echo url('home'); ?>" class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span>i-shopping</span>
                </a>
                
                <?php if ($currentPage === 'home'): ?>
                    <!-- Barre de recherche pour la page d'accueil -->
                    <div class="search-bar">
                        <input id="home-search-input" type="text" placeholder="Rechercher...">
                        <button id="home-search-button"><i class="fas fa-search"></i></button>
                    </div>
                <?php endif; ?>
                
                <div class="header-actions">
                    <?php if (isLoggedIn()): ?>
                        <?php
                        $userModel = new User();
                        $uid = (int)($_SESSION['user_id'] ?? 0);
                        $canSeeAdmin = $uid > 0 && ($userModel->hasRole($uid, 'super_admin') || $userModel->hasRole($uid, 'admin'));
                        ?>
                        <?php if ($canSeeAdmin): ?>
                            <a href="<?php echo url('dashboard_admin'); ?>" class="btn btn-ghost btn-sm">
                                <i class="fas fa-shield-halved"></i> Admin
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo url('dashboard_shop'); ?>" class="btn btn-ghost btn-sm">
                            <i class="fas fa-store"></i> Boutiques
                        </a>
                        <a href="<?php echo url('logout'); ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="<?php echo url('login'); ?>" class="btn btn-ghost btn-sm">
                            <i class="fas fa-sign-in-alt"></i> Connexion
                        </a>
                        <a href="<?php echo url('register'); ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus"></i> Inscription
                        </a>
                    <?php endif; ?>
                    
                    <!-- Sélecteur de thème -->
                    <button id="theme-toggle" class="theme-toggle" aria-label="Changer de thème">
                        <i class="fas fa-moon"></i>
                    </button>
                </div>
            </div>
        </div>

    </header>
    
    <!-- Script de gestion du thème -->
    <script src="<?php echo BASE_URL; ?>/public/js/theme.js"></script>

