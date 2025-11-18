<?php
class HomeController {
    public function index() {
        $data = [
            'title' => 'Bienvenue sur I-Shopping',
            'featured_products' => []
        ];
        
        // Stocker les données pour la vue
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>