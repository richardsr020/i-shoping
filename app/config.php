<?php
/**
 * Configuration de la plateforme i-shopping
 * Contient toutes les configurations nécessaires au fonctionnement de la plateforme
 */

// Configuration de l'environnement
define('ENVIRONMENT', 'development'); // development | production

// Configuration de la base de données MySQL
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'i_shopping');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_SCHEMA_MYSQL', __DIR__ . '/../database/mysql_schema.sql');

// Configuration des chemins
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);
define('VIEWS_PATH', __DIR__ . '/views');
define('MODELS_PATH', __DIR__ . '/models');
define('CONTROLLERS_PATH', __DIR__ . '/controllers');
define('PUBLIC_PATH', BASE_PATH . '/public');

function detectBaseUrl(): string {
    $fromEnv = trim((string)getenv('APP_BASE_URL'));
    if ($fromEnv !== '') {
        return rtrim($fromEnv, '/');
    }

    if (PHP_SAPI === 'cli') {
        return 'http://localhost';
    }

    $https = (string)($_SERVER['HTTPS'] ?? '');
    $scheme = ($https !== '' && strtolower($https) !== 'off') ? 'https' : 'http';
    if (isset($_SERVER['REQUEST_SCHEME']) && is_string($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] !== '') {
        $scheme = $_SERVER['REQUEST_SCHEME'];
    } elseif ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443) {
        $scheme = 'https';
    }

    $host = (string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost');
    $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '/index.php');
    $basePath = str_replace('\\', '/', dirname($scriptName));

    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }

    return $scheme . '://' . $host . rtrim($basePath, '/');
}

// Configuration de l'URL
define('BASE_URL', detectBaseUrl());
define('APP_URL', BASE_URL . '/index.php');

// Configuration de la session
define('SESSION_NAME', 'i_shopping_session');
define('SESSION_LIFETIME', 10800); // 3 heures

// Configuration de sécurité
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_ALGORITHM', PASSWORD_DEFAULT);

// Configuration des fichiers uploadés
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('UPLOAD_MAX_SIZE', 5242880); // 5MB en octets
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Configuration de l'application
define('APP_NAME', 'i-shopping');
define('APP_DESCRIPTION', 'Plateforme e-commerce multi-utilisateurs');

// Fonction pour obtenir la connexion à la base de données
function getDB() {
    static $db = null;
    
    if ($db === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $db = new PDO($dsn, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Initialiser les tables si elles n'existent pas
            initDatabase($db);
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
        }
    }
    
    return $db;
}

function executeSqlFileStatements(PDO $db, string $filePath): void {
    if (!is_file($filePath)) {
        throw new Exception('Fichier SQL introuvable: ' . $filePath);
    }

    $sql = (string)file_get_contents($filePath);
    $parts = preg_split('/;\s*\n/', $sql);
    if (!is_array($parts)) {
        return;
    }

    foreach ($parts as $stmt) {
        $stmt = trim($stmt);
        if ($stmt === '' || substr($stmt, 0, 2) === '--') {
            continue;
        }
        $db->exec($stmt);
    }
}

function mysqlTableExists(PDO $db, string $tableName): bool {
    $check = $db->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?');
    $check->execute([DB_NAME, $tableName]);
    return ((int)($check->fetchColumn() ?: 0)) > 0;
}

function mysqlColumnExists(PDO $db, string $tableName, string $columnName): bool {
    $check = $db->prepare('
        SELECT COUNT(*)
        FROM information_schema.columns
        WHERE table_schema = ? AND table_name = ? AND column_name = ?
    ');
    $check->execute([DB_NAME, $tableName, $columnName]);
    return ((int)($check->fetchColumn() ?: 0)) > 0;
}

function mysqlIndexExists(PDO $db, string $tableName, string $indexName): bool {
    $check = $db->prepare('
        SELECT COUNT(*)
        FROM information_schema.statistics
        WHERE table_schema = ? AND table_name = ? AND index_name = ?
    ');
    $check->execute([DB_NAME, $tableName, $indexName]);
    return ((int)($check->fetchColumn() ?: 0)) > 0;
}

// Fonction pour initialiser la base de données
function initDatabase($db) {
    try {
        $requiredTables = [
            'roles',
            'users',
            'user_roles',
            'shops',
            'products',
            'product_images',
            'product_variants',
            'orders',
            'order_items',
            'product_reviews',
            'conversations',
            'messages',
            'notifications',
        ];

        $missingTable = false;
        foreach ($requiredTables as $table) {
            if (!mysqlTableExists($db, $table)) {
                $missingTable = true;
                break;
            }
        }

        if ($missingTable) {
            executeSqlFileStatements($db, DB_SCHEMA_MYSQL);
        }

        $requiredColumns = [
            'users' => [
                'status' => "ALTER TABLE users ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'",
                'suspended_until' => 'ALTER TABLE users ADD COLUMN suspended_until DATETIME NULL',
            ],
            'shops' => [
                'slug' => 'ALTER TABLE shops ADD COLUMN slug VARCHAR(190) NULL',
                'url' => 'ALTER TABLE shops ADD COLUMN url VARCHAR(255) NULL',
                'stars' => 'ALTER TABLE shops ADD COLUMN stars DOUBLE NOT NULL DEFAULT 0',
                'suspended_until' => 'ALTER TABLE shops ADD COLUMN suspended_until DATETIME NULL',
                'payment_methods_json' => 'ALTER TABLE shops ADD COLUMN payment_methods_json TEXT NULL',
            ],
            'products' => [
                'min_order_qty' => 'ALTER TABLE products ADD COLUMN min_order_qty INT NOT NULL DEFAULT 1',
            ],
            'orders' => [
                'paid' => 'ALTER TABLE orders ADD COLUMN paid TINYINT(1) NOT NULL DEFAULT 0',
                'satisfied' => 'ALTER TABLE orders ADD COLUMN satisfied TINYINT(1) NOT NULL DEFAULT 0',
                'canceled' => 'ALTER TABLE orders ADD COLUMN canceled TINYINT(1) NOT NULL DEFAULT 0',
            ],
        ];

        foreach ($requiredColumns as $tableName => $columns) {
            if (!mysqlTableExists($db, $tableName)) {
                continue;
            }
            foreach ($columns as $columnName => $sql) {
                if (!mysqlColumnExists($db, $tableName, $columnName)) {
                    $db->exec($sql);
                }
            }
        }

        if (mysqlTableExists($db, 'user_roles') && !mysqlIndexExists($db, 'user_roles', 'uq_user_roles_user_role')) {
            $db->exec('
                DELETE ur1
                FROM user_roles ur1
                INNER JOIN user_roles ur2
                    ON ur1.user_id = ur2.user_id
                   AND ur1.role_id = ur2.role_id
                   AND ur1.id > ur2.id
            ');
            $db->exec('ALTER TABLE user_roles ADD UNIQUE KEY uq_user_roles_user_role (user_id, role_id)');
        }

        $db->exec("INSERT IGNORE INTO roles (id, name, level) VALUES (1, 'super_admin', 1)");
        $db->exec("INSERT IGNORE INTO roles (id, name, level) VALUES (2, 'admin', 2)");
        $db->exec("INSERT IGNORE INTO roles (id, name, level) VALUES (3, 'vendor', 3)");
        $db->exec("INSERT IGNORE INTO roles (id, name, level) VALUES (4, 'customer', 4)");

        $superAdminEmail = 'admin@ishop.local';
        $superAdminPassword = 'richardI022IS';

        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$superAdminEmail]);
        $superAdminId = (int)($stmt->fetchColumn() ?: 0);

        if ($superAdminId <= 0) {
            $hashed = password_hash($superAdminPassword, PASSWORD_ALGORITHM);
            $ins = $db->prepare('INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)');
            $ins->execute(['Super', 'Admin', $superAdminEmail, $hashed]);
            $superAdminId = (int)$db->lastInsertId();
        }

        if ($superAdminId > 0) {
            $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ? LIMIT 1');
            $roleStmt->execute(['super_admin']);
            $roleId = (int)($roleStmt->fetchColumn() ?: 0);
            if ($roleId > 0) {
                $db->prepare('INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)')->execute([$superAdminId, $roleId]);
            }
        }

        return;
    } catch (Throwable $e) {
        error_log('Erreur initDatabase MySQL: ' . $e->getMessage());
        throw $e;
    }
}

function ensureUploadDir(string $subdir): string {
    $base = rtrim(UPLOAD_PATH, '/');
    if (!is_dir($base) && !mkdir($base, 0777, true) && !is_dir($base)) {
        throw new Exception('Impossible de créer le dossier de stockage des images.');
    }
    @chmod($base, 0777);
    if (!is_writable($base)) {
        throw new Exception('Le dossier de stockage des images n\'est pas inscriptible.');
    }

    $target = $base . '/' . trim($subdir, '/');
    if (!is_dir($target) && !mkdir($target, 0777, true) && !is_dir($target)) {
        throw new Exception('Impossible de créer le dossier de destination des images.');
    }
    @chmod($target, 0777);
    if (!is_writable($target)) {
        throw new Exception('Le dossier de destination des images n\'est pas inscriptible.');
    }
    return $target;
}

function saveUploadedImage(array $file, string $subdir): ?string {
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors de l\'upload de l\'image.');
    }
    if (!isset($file['type']) || !in_array($file['type'], ALLOWED_IMAGE_TYPES, true)) {
        throw new Exception('Type d\'image non autorisé.');
    }
    if (!isset($file['size']) || (int)$file['size'] > UPLOAD_MAX_SIZE) {
        throw new Exception('Image trop volumineuse (max 5MB).');
    }
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new Exception('Fichier upload invalide.');
    }

    $dir = ensureUploadDir($subdir);
    $ext = '';
    if (!empty($file['name']) && is_string($file['name'])) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== '') {
            $ext = '.' . preg_replace('/[^a-z0-9]+/', '', $ext);
        }
    }

    $filename = bin2hex(random_bytes(16)) . $ext;
    $targetPath = rtrim($dir, '/') . '/' . $filename;

    if (!is_writable($dir)) {
        throw new Exception('Le dossier de destination des images n\'est pas inscriptible.');
    }

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        error_log('Echec move_uploaded_file vers ' . $targetPath . ' depuis ' . (string)($file['tmp_name'] ?? ''));
        throw new Exception('Impossible de sauvegarder l\'image. Vérifiez les permissions du dossier public/uploads.');
    }

    return '/public/uploads/' . trim($subdir, '/') . '/' . $filename;
}

// Fonction pour démarrer la session de manière sécurisée
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration de la session
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', ENVIRONMENT === 'production' ? 1 : 0);
        
        session_name(SESSION_NAME);
        session_start();
        
        // Régénérer l'ID de session périodiquement pour la sécurité
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > SESSION_LIFETIME) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fonction pour obtenir l'utilisateur actuel
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT id, first_name, last_name, email, birth_date, gender, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Fonction pour rediriger vers une page
function redirect($page) {
    header("Location: " . APP_URL . "?page=" . $page);
    exit();
}

// Fonction pour obtenir l'URL d'une page
function url($page) {
    return APP_URL . "?page=" . $page;
}

function bootstrapDatabaseOnIndexLoad(): void {
    static $done = false;
    if ($done) {
        return;
    }

    if (PHP_SAPI === 'cli') {
        return;
    }

    $script = basename((string)($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($script !== 'index.php') {
        return;
    }

    // Force l'initialisation MySQL au chargement d'index.php.
    getDB();
    $done = true;
}

// Démarrer la session sécurisée
startSecureSession();
bootstrapDatabaseOnIndexLoad();
