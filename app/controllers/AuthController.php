<?php
class AuthController {
    public function showLogin() {
        $data = ['title' => 'Connexion'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function showRegister() {
        $data = ['title' => 'Inscription'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>