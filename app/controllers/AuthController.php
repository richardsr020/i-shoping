<?php
/**
 * Contrôleur d'authentification
 * Gère l'inscription, la connexion et la déconnexion
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegister() {
        // Si déjà connecté, rediriger vers la page d'accueil
        if (isLoggedIn()) {
            redirect('home');
        }
        
        $data = [
            'title' => 'Inscription - ' . APP_NAME,
            'errors' => isset($_SESSION['errors']) ? $_SESSION['errors'] : [],
            'form_data' => isset($_SESSION['form_data']) ? $_SESSION['form_data'] : []
        ];
        
        unset($_SESSION['errors'], $_SESSION['form_data']);
        $_SESSION['view_data'] = $data;
    }
    
    /**
     * Traiter l'inscription
     */
    public function register() {
        // Si déjà connecté, rediriger
        if (isLoggedIn()) {
            redirect('home');
        }
        
        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('register');
        }
        
        // Récupérer les données du formulaire
        $data = [
            'first_name' => $_POST['firstName'] ?? '',
            'last_name' => $_POST['lastName'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirmPassword'] ?? '',
            'birth_date' => $_POST['birthDate'] ?? '',
            'gender' => $_POST['gender'] ?? ''
        ];
        
        // Validation
        $errors = $this->validateRegister($data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $data;
            redirect('register');
        }
        
        try {
            // Créer l'utilisateur
            $userId = $this->userModel->create($data);
            
            // Connecter automatiquement l'utilisateur
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_email'] = $data['email'];
            $_SESSION['success_message'] = 'Inscription réussie ! Bienvenue sur ' . APP_NAME;
            
            redirect('home');
        } catch (Exception $e) {
            $_SESSION['errors'] = ['general' => $e->getMessage()];
            $_SESSION['form_data'] = $data;
            redirect('register');
        }
    }
    
    /**
     * Valider les données d'inscription
     */
    private function validateRegister($data) {
        $errors = [];
        
        // Prénom
        if (empty(trim($data['first_name']))) {
            $errors['first_name'] = 'Le prénom est requis.';
        }
        
        // Nom
        if (empty(trim($data['last_name']))) {
            $errors['last_name'] = 'Le nom est requis.';
        }
        
        // Email
        if (empty(trim($data['email']))) {
            $errors['email'] = 'L\'email est requis.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Format d\'email invalide.';
        }
        
        // Mot de passe
        if (empty($data['password'])) {
            $errors['password'] = 'Le mot de passe est requis.';
        } elseif (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            $errors['password'] = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères.';
        }
        
        // Confirmation du mot de passe
        if (empty($data['confirm_password'])) {
            $errors['confirm_password'] = 'Veuillez confirmer votre mot de passe.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
        }
        
        return $errors;
    }
    
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin() {
        // Si déjà connecté, rediriger vers la page d'accueil
        if (isLoggedIn()) {
            redirect('home');
        }
        
        $data = [
            'title' => 'Connexion - ' . APP_NAME,
            'errors' => isset($_SESSION['errors']) ? $_SESSION['errors'] : [],
            'form_data' => isset($_SESSION['form_data']) ? $_SESSION['form_data'] : []
        ];
        
        unset($_SESSION['errors'], $_SESSION['form_data']);
        $_SESSION['view_data'] = $data;
    }
    
    /**
     * Traiter la connexion
     */
    public function login() {
        // Si déjà connecté, rediriger
        if (isLoggedIn()) {
            redirect('home');
        }
        
        // Vérifier que c'est une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('login');
        }
        
        // Récupérer les données du formulaire
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        // Validation basique
        $errors = [];
        if (empty($email)) {
            $errors['email'] = 'L\'email est requis.';
        }
        if (empty($password)) {
            $errors['password'] = 'Le mot de passe est requis.';
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = ['email' => $email];
            redirect('login');
        }
        
        try {
            // Authentifier l'utilisateur
            $user = $this->userModel->authenticate($email, $password);
            
            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Si "Se souvenir de moi" est coché, prolonger la session
            if ($remember) {
                // Régénérer l'ID de session pour plus de sécurité
                session_regenerate_id(true);
            }
            
            $_SESSION['success_message'] = 'Connexion réussie ! Bienvenue ' . $user['first_name'];
            
            // Rediriger vers la page demandée ou la page d'accueil
            $redirect = $_SESSION['redirect_after_login'] ?? 'home';
            unset($_SESSION['redirect_after_login']);
            redirect($redirect);
        } catch (Exception $e) {
            $_SESSION['errors'] = ['general' => $e->getMessage()];
            $_SESSION['form_data'] = ['email' => $email];
            redirect('login');
        }
    }
    
    /**
     * Déconnexion
     */
    public function logout() {
        // Détruire la session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        redirect('home');
    }
}
