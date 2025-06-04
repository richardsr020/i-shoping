<?php
/**
 * Point d'entrée unique de l'application
 * Toutes les pages doivent passer par ce fichier via index.php?page=nom_page
 */

// Inclure la configuration (démarre la session)
require_once __DIR__ . '/app/config.php';

// Obtenir la page demandée
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;

// Guard: si utilisateur connecté et suspendu, rediriger vers la page dédiée
if (isLoggedIn()) {
    $allowPages = ['suspended', 'logout'];
    if (!in_array($page, $allowPages, true)) {
        $db = getDB();
        $stmt = $db->prepare('SELECT status, suspended_until FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([(int)($_SESSION['user_id'] ?? 0)]);
        $u = $stmt->fetch() ?: [];

        $status = (string)($u['status'] ?? 'active');
        $until = $u['suspended_until'] ?? null;
        $isSuspended = ($status === 'suspended');
        if ($isSuspended && $until) {
            $isSuspended = (strtotime((string)$until) > time());
        }

        if ($isSuspended) {
            redirect('suspended');
        }
    }
}

// Routes qui ont leurs propres templates HTML complets (pas de header/footer commun)
$fullPageRoutes = ['login', 'register', 'dashboard_shop', 'dashboard_admin', 'chat', 'suspended'];

// Si c'est une route avec template complet, charger directement la vue après le contrôleur
if (in_array($page, $fullPageRoutes)) {
    // Charger le routeur et exécuter la route
    require_once __DIR__ . '/app/router.php';
    $router = new Router();
    $router->route();
    
    // Si le contrôleur n'a pas redirigé, charger la vue
    // (les vues login.php et register.php ont leur propre structure HTML complète)
    if ($page === 'login') {
        require_once __DIR__ . '/app/views/login.php';
    } elseif ($page === 'register') {
        require_once __DIR__ . '/app/views/register.php';
    } elseif ($page === 'dashboard_shop') {
        require_once __DIR__ . '/app/views/dashboard_shop.php';
    } elseif ($page === 'dashboard_admin') {
        require_once __DIR__ . '/app/views/dashboard_admin.php';
    } elseif ($page === 'chat') {
        require_once __DIR__ . '/app/views/chat.php';
    } elseif ($page === 'suspended') {
        require_once __DIR__ . '/app/views/suspended.php';
    }
    exit;
}

// Pour les autres routes, inclure header/view/footer
$headerPath = __DIR__ . '/app/views/header.php';
if (file_exists($headerPath)) {
    require_once $headerPath;
}

// Router la requête
require_once __DIR__ . '/app/router.php';
$router = new Router();
$router->route();

// Charger la vue correspondante
$viewPath = __DIR__ . '/app/views/' . $page . '.php';
if (file_exists($viewPath)) {
    require_once $viewPath;
}

// Inclure le pied de page
$footerPath = __DIR__ . '/app/views/footer.php';
if (file_exists($footerPath)) {
    require_once $footerPath;
}
?>
