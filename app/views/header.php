<?php
/**
 * Header global pour toutes les pages
 * Inclut le système de thème et la navigation
 */
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
            <div class="header-content" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--spacing-md); padding: var(--spacing-md) 0;">
                <a href="<?php echo url('home'); ?>" class="logo">
                    <i class="fas fa-shopping-bag"></i>
                    <span>i shopping</span>
                </a>
                
                <?php if ($currentPage === 'home'): ?>
                    <!-- Barre de recherche pour la page d'accueil -->
                    <div class="search-bar" style="display: flex; align-items: center; flex: 0 1 500px; margin: 0 var(--spacing-md);">
                        <input id="home-search-input" type="text" placeholder="Rechercher..." style="flex: 1;">
                        <button id="home-search-button" style="margin-left: var(--spacing-sm);"><i class="fas fa-search"></i></button>
                    </div>
                <?php endif; ?>
                
                <div style="display: flex; align-items: center; gap: var(--spacing-md);">
                    <?php if (isLoggedIn()): ?>
                        <a href="<?php echo url('dashboard_shop'); ?>" class="btn btn-ghost btn-sm">
                            <i class="fas fa-store"></i> Boutiques
                        </a>
                        <a href="<?php echo url('orders'); ?>" class="btn btn-ghost btn-sm">
                            <i class="fas fa-receipt"></i> Commandes
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

