<?php
class ProductController {
    public function create() {
        $data = ['title' => 'Créer un produit'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function myProducts() {
        $data = ['title' => 'Mes produits'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>