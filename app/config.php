<?php
/**
 * Configuration de la plateforme i-shopping
 * Contient toutes les configurations nécessaires au fonctionnement de la plateforme
 */

// Configuration de l'environnement
define('ENVIRONMENT', 'development'); // development | production

// Configuration de la base de données SQLite
define('DB_PATH', __DIR__ . '/../database/shopping.db');
define('DB_INIT_SCRIPT', __DIR__ . '/../database/init.sql');

// Configuration des chemins
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', __DIR__);
define('VIEWS_PATH', __DIR__ . '/views');
define('MODELS_PATH', __DIR__ . '/models');
define('CONTROLLERS_PATH', __DIR__ . '/controllers');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Configuration de l'URL
define('BASE_URL', 'http://localhost:8000');
define('APP_URL', BASE_URL . '/index.php');

// Configuration de la session
define('SESSION_NAME', 'i_shopping_session');
define('SESSION_LIFETIME', 3600); // 1 heure

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
        // Créer le dossier database s'il n'existe pas
        $dbDir = dirname(DB_PATH);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        try {
            $db = new PDO('sqlite:' . DB_PATH);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db->exec('PRAGMA foreign_keys = ON');
            
            // Initialiser les tables si elles n'existent pas
            initDatabase($db);
            
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez contacter l'administrateur.");
        }
    }
    
    return $db;
}

// Fonction pour initialiser la base de données
function initDatabase($db) {
    $db->exec('PRAGMA foreign_keys = ON');

    $ensureColumns = function(string $table, array $columns) use ($db) {
        $existing = [];
        $stmt = $db->query("PRAGMA table_info($table)");
        foreach ($stmt->fetchAll() as $col) {
            $existing[$col['name']] = true;
        }
        foreach ($columns as $name => $definition) {
            if (!isset($existing[$name])) {
                $db->exec("ALTER TABLE $table ADD COLUMN $name $definition");
            }
        }
    };

    // Table roles
    $db->exec("CREATE TABLE IF NOT EXISTS roles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE,
        level INTEGER NOT NULL
    )");

    // Table users
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name TEXT NOT NULL,
        last_name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        birth_date DATE,
        gender TEXT,
        status TEXT DEFAULT 'active',
        suspended_until DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Table user_roles
    $db->exec("CREATE TABLE IF NOT EXISTS user_roles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        role_id INTEGER NOT NULL,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
    )");

    // Table shops (schéma export.sql)
    $db->exec("CREATE TABLE IF NOT EXISTS shops (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        slug TEXT,
        url TEXT,
        description TEXT,
        logo TEXT,
        banner TEXT,
        email_contact TEXT,
        phone TEXT,
        address TEXT,
        city TEXT,
        country TEXT,
        currency TEXT DEFAULT 'USD',
        stars REAL DEFAULT 0,
        status TEXT DEFAULT 'active',
        suspended_until DATETIME,
        payment_methods_json TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $ensureColumns('shops', [
        'payment_methods_json' => 'TEXT'
    ]);

    // Table products (schéma export.sql)
    $db->exec("CREATE TABLE IF NOT EXISTS products (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        shop_id INTEGER NOT NULL,
        name TEXT NOT NULL,
        description TEXT,
        price REAL NOT NULL,
        promo_price REAL,
        category TEXT,
        brand TEXT,
        size TEXT,
        sku TEXT,
        weight REAL,
        colors_json TEXT,
        tags_json TEXT,
        image TEXT,
        stock INTEGER DEFAULT 0,
        is_physical INTEGER DEFAULT 1,
        status TEXT DEFAULT 'active',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE
    )");

    // Tables additionnelles
    $db->exec("CREATE TABLE IF NOT EXISTS product_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        image TEXT NOT NULL,
        FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS product_variants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        variant_name TEXT,
        color_hex TEXT,
        additional_price REAL DEFAULT 0,
        stock INTEGER DEFAULT 0,
        FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS orders (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        shop_id INTEGER NOT NULL,
        total REAL NOT NULL,
        status TEXT DEFAULT 'pending',
        paid INTEGER DEFAULT 0,
        satisfied INTEGER DEFAULT 0,
        canceled INTEGER DEFAULT 0,
        payment_method TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
        FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        order_id INTEGER NOT NULL,
        product_id INTEGER NOT NULL,
        quantity INTEGER NOT NULL DEFAULT 1,
        price REAL NOT NULL,
        total REAL NOT NULL,
        color TEXT,
        size TEXT,
        FOREIGN KEY(order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS product_reviews (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        product_id INTEGER NOT NULL,
        user_id INTEGER,
        rating INTEGER NOT NULL,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS conversations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        shop_id INTEGER NOT NULL,
        buyer_user_id INTEGER NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE(shop_id, buyer_user_id),
        FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE,
        FOREIGN KEY(buyer_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        conversation_id INTEGER NOT NULL,
        sender_user_id INTEGER NOT NULL,
        body TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
        FOREIGN KEY(sender_user_id) REFERENCES users(id) ON DELETE CASCADE
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        shop_id INTEGER,
        type TEXT NOT NULL,
        title TEXT NOT NULL,
        body TEXT,
        data_json TEXT,
        is_read INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE
    )");

    // Migrer shops/products si DB existante plus ancienne
    $ensureColumns('users', [
        'status' => "TEXT DEFAULT 'active'",
        'suspended_until' => 'DATETIME'
    ]);

    $ensureColumns('shops', [
        'slug' => 'TEXT',
        'url' => 'TEXT',
        'banner' => 'TEXT',
        'email_contact' => 'TEXT',
        'phone' => 'TEXT',
        'address' => 'TEXT',
        'city' => 'TEXT',
        'country' => 'TEXT',
        'currency' => "TEXT DEFAULT 'USD'",
        'stars' => 'REAL DEFAULT 0',
        'status' => "TEXT DEFAULT 'active'",
        'suspended_until' => 'DATETIME'
    ]);

    $ensureColumns('products', [
        'promo_price' => 'REAL',
        'sku' => 'TEXT',
        'weight' => 'REAL',
        'colors_json' => 'TEXT',
        'tags_json' => 'TEXT',
        'is_physical' => 'INTEGER DEFAULT 1',
        'min_order_qty' => 'INTEGER DEFAULT 1'
    ]);

    $ensureColumns('orders', [
        'paid' => 'INTEGER DEFAULT 0',
        'satisfied' => 'INTEGER DEFAULT 0',
        'canceled' => 'INTEGER DEFAULT 0'
    ]);

    // Seeds rôles
    $db->exec("INSERT OR IGNORE INTO roles (id, name, level) VALUES (1, 'super_admin', 1)");
    $db->exec("INSERT OR IGNORE INTO roles (id, name, level) VALUES (2, 'admin', 2)");
    $db->exec("INSERT OR IGNORE INTO roles (id, name, level) VALUES (3, 'vendor', 3)");
    $db->exec("INSERT OR IGNORE INTO roles (id, name, level) VALUES (4, 'customer', 4)");

    // Seed super_admin (idempotent)
    $superAdminEmail = 'admin@ishop.local';
    $superAdminPassword = 'richardI022IS';

    $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$superAdminEmail]);
    $superAdminId = (int)($stmt->fetchColumn() ?: 0);

    if ($superAdminId <= 0) {
        $hashed = password_hash($superAdminPassword, PASSWORD_ALGORITHM);
        $db->beginTransaction();
        try {
            $ins = $db->prepare('INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)');
            $ins->execute(['Super', 'Admin', $superAdminEmail, $hashed]);
            $superAdminId = (int)$db->lastInsertId();
            $db->commit();
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    if ($superAdminId > 0) {
        $roleStmt = $db->prepare('SELECT id FROM roles WHERE name = ?');
        $roleStmt->execute(['super_admin']);
        $roleId = (int)($roleStmt->fetchColumn() ?: 0);
        if ($roleId > 0) {
            $check = $db->prepare('SELECT 1 FROM user_roles WHERE user_id = ? AND role_id = ? LIMIT 1');
            $check->execute([$superAdminId, $roleId]);
            if (!$check->fetchColumn()) {
                $ur = $db->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
                $ur->execute([$superAdminId, $roleId]);
            }
        }
    }

    // Index pour améliorer les performances
    $db->exec("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_shops_user_id ON shops(user_id)");
    $db->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_shops_slug_unique ON shops(slug)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_products_shop_id ON products(shop_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_products_category ON products(category)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_products_status ON products(status)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_orders_user_id ON orders(user_id)");
    $db->exec("CREATE INDEX IF NOT EXISTS idx_orders_shop_id ON orders(shop_id)");
}

function ensureUploadDir(string $subdir): string {
    $base = rtrim(UPLOAD_PATH, '/');
    if (!is_dir($base)) {
        mkdir($base, 0755, true);
    }

    $target = $base . '/' . trim($subdir, '/');
    if (!is_dir($target)) {
        mkdir($target, 0755, true);
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

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Impossible de sauvegarder l\'image.');
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

// Démarrer la session sécurisée
startSecureSession();
