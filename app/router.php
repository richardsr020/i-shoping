<?php
/**
 * Routeur de l'application
 * Gère toutes les routes et appelle les contrôleurs appropriés
 */

require_once __DIR__ . '/config.php';

class Router {
    private $page;
    private $action;
    
    public function __construct() {
        $this->page = $_GET['page'] ?? 'home';
        $this->action = $_GET['action'] ?? null;
    }
    
    /**
     * Définir les routes et leurs contrôleurs
     */
    public function route() {
        // Routes d'authentification
        if ($this->page === 'login') {
            require_once CONTROLLERS_PATH . '/AuthController.php';
            $controller = new AuthController();
            
            if ($this->action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login(); // Cette méthode redirige, donc on ne continue pas
            } else {
                $controller->showLogin(); // Préparer les données pour la vue
            }
            return;
        }
        
        if ($this->page === 'register') {
            require_once CONTROLLERS_PATH . '/AuthController.php';
            $controller = new AuthController();
            
            if ($this->action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->register(); // Cette méthode redirige, donc on ne continue pas
            } else {
                $controller->showRegister(); // Préparer les données pour la vue
            }
            return;
        }
        
        if ($this->page === 'logout') {
            require_once CONTROLLERS_PATH . '/AuthController.php';
            $controller = new AuthController();
            $controller->logout();
            return;
        }
        
        // Routes des boutiques
        if ($this->page === 'create_shop') {
            require_once CONTROLLERS_PATH . '/ShopController.php';
            $controller = new ShopController();
            $controller->create();
            return;
        }
        
        if ($this->page === 'dashboard_shop') {
            require_once CONTROLLERS_PATH . '/ShopController.php';
            $controller = new ShopController();
            $controller->dashboard();
            return;
        }
        
        if ($this->page === 'profile_shop') {
            require_once CONTROLLERS_PATH . '/ShopController.php';
            $controller = new ShopController();
            $controller->profile();
            return;
        }
        
        // Routes des produits
        if ($this->page === 'create_product') {
            require_once CONTROLLERS_PATH . '/ProductController.php';
            $controller = new ProductController();
            $controller->create();
            return;
        }
        
        if ($this->page === 'product_detail') {
            require_once CONTROLLERS_PATH . '/ProductController.php';
            $controller = new ProductController();
            $controller->detail();
            return;
        }

        // Route commandes (client)
        if ($this->page === 'orders') {
            require_once CONTROLLERS_PATH . '/OrderController.php';
            $controller = new OrderController();
            $controller->index();
            return;
        }
        
        // Route admin
        if ($this->page === 'dashboard_admin') {
            require_once CONTROLLERS_PATH . '/HomeController.php';
            $controller = new HomeController();
            $controller->admin();
            return;
        }
        
        // Route chat
        if ($this->page === 'chat') {
            // Charger la vue directement
            return;
        }
        
        // Route par défaut : page d'accueil
        if ($this->page === 'home') {
            require_once CONTROLLERS_PATH . '/HomeController.php';
            $controller = new HomeController();
            $controller->index();
            return;
        }
        
        // Page 404 - page non trouvée
        $this->notFound();
    }
    
    /**
     * Page 404
     */
    private function notFound() {
        http_response_code(404);
        echo '<div class="container">';
        echo '<h1>404 - Page non trouvée</h1>';
        echo '<p>La page que vous recherchez n\'existe pas.</p>';
        echo '<a href="' . url('home') . '">Retour à l\'accueil</a>';
        echo '</div>';
        exit;
    }
}

