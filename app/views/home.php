<?php
/**
 * Page d'accueil - Entièrement gérée par JavaScript
 */
require_once __DIR__ . '/../config.php';
?>

<!-- Contenu principal -->
<main class="main-content container">
    <div id="products-container" class="products-grid">
        <!-- Les produits seront chargés dynamiquement par JavaScript -->
        <div style="text-align: center; padding: var(--spacing-2xl); color: var(--color-text-muted);">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; margin-bottom: var(--spacing-md);"></i>
            <p>Chargement des produits...</p>
        </div>
                </div>
</main>

<!-- Menu flottant -->
<button id="floating-menu-btn" class="floating-menu-btn" aria-label="Menu">
    <i class="fas fa-plus"></i>
</button>

<div id="floating-menu-items" class="floating-menu-items">
    <button id="menu-user" class="floating-menu-item" title="Mon compte">
        <i class="fas fa-user"></i>
    </button>
    <button id="menu-cart" class="floating-menu-item" title="Panier">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge"></span>
    </button>
    <button id="menu-filters" class="floating-menu-item" title="Filtres">
        <i class="fas fa-filter"></i>
    </button>
    <button id="menu-notifications" class="floating-menu-item" title="Notifications">
        <i class="fas fa-bell"></i>
        <span class="badge"></span>
    </button>
    <button id="menu-add" class="floating-menu-item" title="Créer un produit">
        <i class="fas fa-plus"></i>
    </button>
            </div>

<!-- Modal Panier -->
<div id="cart-modal" class="modal-overlay">
    <!-- <div class="modal">
        <div class="modal-header">
            <h2>Mon panier</h2>
            <button class="modal-close" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="cart-modal-body" class="modal-body">
                Contenu chargé dynamiquement -->
        <!-- </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="alert('Fonctionnalité de commande à venir')">
                Passer la commande
            </button>
                </div>
            </div>
        </div> --> 

<!-- Modal Filtres -->
<div id="filters-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Filtres</h2>
            <button class="modal-close" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
                </div>
        <div id="filters-modal-body" class="modal-body">
            <!-- Contenu chargé dynamiquement -->
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="document.getElementById('filters-modal').classList.remove('active')">
                Fermer
            </button>
                </div>
            </div>
        </div>

<!-- Modal Notifications -->
<div id="notifications-modal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h2>Notifications</h2>
            <button class="modal-close" aria-label="Fermer">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="notifications-modal-body" class="modal-body">
            <!-- Contenu chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- JS pour la page d'accueil -->
<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>/public/js/home.js"></script>
