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
        if ($this->page === 'suspended') {
            if (!isLoggedIn()) {
                redirect('login');
            }

            $db = getDB();
            $stmt = $db->prepare('SELECT suspended_until FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([(int)($_SESSION['user_id'] ?? 0)]);
            $row = $stmt->fetch() ?: [];

            $_SESSION['view_data'] = [
                'title' => 'Compte suspendu - ' . APP_NAME,
                'suspended_until' => $row['suspended_until'] ?? null,
            ];
            return;
        }
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
            require_once CONTROLLERS_PATH . '/AdminController.php';
            $controller = new AdminController();

            if ($this->action === 'user_create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->createUser();
            } elseif ($this->action === 'user_set_role' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->setUserRole();
            } elseif ($this->action === 'product_activate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->productActivate();
            } elseif ($this->action === 'product_deactivate' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->productDeactivate();
            } elseif ($this->action === 'product_delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->productDelete();
            } elseif ($this->action === 'user_suspend' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->suspendUser();
            } elseif ($this->action === 'user_unsuspend' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->unsuspendUser();
            } elseif ($this->action === 'user_delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->deleteUser();
            } elseif ($this->action === 'shop_suspend' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->suspendShop();
            } elseif ($this->action === 'shop_unsuspend' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->unsuspendShop();
            } elseif ($this->action === 'shop_delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->deleteShop();
            } else {
                $controller->index();
            }
            return;
        }
        
        // Route chat
        if ($this->page === 'chat') {
            require_once CONTROLLERS_PATH . '/ChatController.php';
            $controller = new ChatController();
            if ($this->action === 'list' && $_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->listConversations();
            } elseif ($this->action === 'poll' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->pollMessages();
            } elseif ($this->action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->sendMessage();
            } else {
                $controller->index();
            }
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

