<?php
/**
 * Modèle User
 * Gère toutes les opérations liées aux utilisateurs
 */

require_once __DIR__ . '/../config.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }

    public function hasRole(int $userId, string $roleName): bool {
        $stmt = $this->db->prepare('
            SELECT 1
            FROM user_roles ur
            INNER JOIN roles r ON r.id = ur.role_id
            WHERE ur.user_id = ? AND r.name = ?
            LIMIT 1
        ');
        $stmt->execute([$userId, $roleName]);
        return (bool)$stmt->fetchColumn();
    }

    public function addRoleIfMissing(int $userId, string $roleName): void {
        if ($this->hasRole($userId, $roleName)) {
            return;
        }

        $roleStmt = $this->db->prepare('SELECT id FROM roles WHERE name = ?');
        $roleStmt->execute([$roleName]);
        $roleId = (int)$roleStmt->fetchColumn();
        if ($roleId <= 0) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
        $stmt->execute([$userId, $roleId]);
    }
    
    /**
     * Créer un nouvel utilisateur
     */
    public function create($data) {
        // Validation des données
        if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || empty($data['password'])) {
            throw new Exception("Tous les champs obligatoires doivent être remplis.");
        }
        
        // Vérifier si l'email existe déjà
        if ($this->emailExists($data['email'])) {
            throw new Exception("Cet email est déjà utilisé.");
        }
        
        // Valider l'email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide.");
        }
        
        // Valider le mot de passe
        if (strlen($data['password']) < PASSWORD_MIN_LENGTH) {
            throw new Exception("Le mot de passe doit contenir au moins " . PASSWORD_MIN_LENGTH . " caractères.");
        }
        
        // Hasher le mot de passe
        $hashedPassword = password_hash($data['password'], PASSWORD_ALGORITHM);
        
        // Préparer les données
        $firstName = trim($data['first_name']);
        $lastName = trim($data['last_name']);
        $email = trim(strtolower($data['email']));
        $birthDate = !empty($data['birth_date']) ? $data['birth_date'] : null;
        $gender = !empty($data['gender']) ? $data['gender'] : null;
        
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO users (first_name, last_name, email, password, birth_date, gender)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $birthDate, $gender]);
            
            $userId = (int)$this->db->lastInsertId();

            // Attribuer le rôle par défaut: customer
            $roleStmt = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
            $roleStmt->execute(['customer']);
            $roleId = (int)$roleStmt->fetchColumn();
            if ($roleId > 0) {
                $urStmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                $urStmt->execute([$userId, $roleId]);
            }

            $this->db->commit();

            return $userId;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            throw new Exception("Erreur lors de la création du compte. Veuillez réessayer.");
        }
    }
    
    /**
     * Authentifier un utilisateur
     */
    public function authenticate($email, $password) {
        if (empty($email) || empty($password)) {
            throw new Exception("Email et mot de passe requis.");
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([trim(strtolower($email))]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception("Email ou mot de passe incorrect.");
        }
        
        // Vérifier le mot de passe
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Email ou mot de passe incorrect.");
        }
        
        // Retourner les données utilisateur sans le mot de passe
        unset($user['password']);
        return $user;
    }
    
    /**
     * Vérifier si un email existe
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([trim(strtolower($email))]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Obtenir un utilisateur par son ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, birth_date, gender, created_at, updated_at
            FROM users WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Obtenir un utilisateur par son email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, birth_date, gender, created_at, updated_at
            FROM users WHERE email = ?
        ");
        $stmt->execute([trim(strtolower($email))]);
        return $stmt->fetch();
    }
    
    /**
     * Mettre à jour un utilisateur
     */
    public function update($id, $data) {
        $user = $this->findById($id);
        if (!$user) {
            throw new Exception("Utilisateur non trouvé.");
        }
        
        $updates = [];
        $params = [];
        
        if (isset($data['first_name'])) {
            $updates[] = "first_name = ?";
            $params[] = trim($data['first_name']);
        }
        
        if (isset($data['last_name'])) {
            $updates[] = "last_name = ?";
            $params[] = trim($data['last_name']);
        }
        
        if (isset($data['email'])) {
            $email = trim(strtolower($data['email']));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Format d'email invalide.");
            }
            if ($this->emailExists($email) && $user['email'] !== $email) {
                throw new Exception("Cet email est déjà utilisé.");
            }
            $updates[] = "email = ?";
            $params[] = $email;
        }
        
        if (isset($data['birth_date'])) {
            $updates[] = "birth_date = ?";
            $params[] = $data['birth_date'] ?: null;
        }
        
        if (isset($data['gender'])) {
            $updates[] = "gender = ?";
            $params[] = $data['gender'] ?: null;
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $updates[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;
        
        try {
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            throw new Exception("Erreur lors de la mise à jour du profil.");
        }
    }
    
    /**
     * Changer le mot de passe
     */
    public function changePassword($id, $currentPassword, $newPassword) {
        $user = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $user->execute([$id]);
        $userData = $user->fetch();
        
        if (!$userData || !password_verify($currentPassword, $userData['password'])) {
            throw new Exception("Mot de passe actuel incorrect.");
        }
        
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            throw new Exception("Le nouveau mot de passe doit contenir au moins " . PASSWORD_MIN_LENGTH . " caractères.");
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_ALGORITHM);
        
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
    
    /**
     * Obtenir toutes les boutiques d'un utilisateur
     */
    public function getShops($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM shops WHERE user_id = ? ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}




