<?php
class ShopController {
    public function create() {
        $data = ['title' => 'Créer une boutique'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function dashboard() {
        $data = ['title' => 'Tableau de bord boutique'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function profile() {
        $data = ['title' => 'Profil boutique'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>