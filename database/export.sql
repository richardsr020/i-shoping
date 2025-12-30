PRAGMA foreign_keys=OFF;
BEGIN TRANSACTION;

-- ==========================
-- TABLE ROLES (hiérarchie)
-- ==========================
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,   -- super_admin / admin / vendor / customer
    level INTEGER NOT NULL       -- hiérarchie (1=super_admin > 2 > 3 ... )
);

INSERT INTO roles (name, level) VALUES
('super_admin', 1),
('admin', 2),
('vendor', 3),
('customer', 4);

-- ==========================
-- TABLE USERS
-- ==========================
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    birth_date DATE,
    gender TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLE liaison utilisateur ↔ rôle
CREATE TABLE user_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY(role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- ==========================
-- TABLE SHOPS
-- ==========================
CREATE TABLE shops (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT,
    logo TEXT,
    banner TEXT,
    email_contact TEXT,
    phone TEXT,
    address TEXT,
    city TEXT,
    country TEXT,
    currency TEXT DEFAULT 'USD',
    status TEXT DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ==========================
-- TABLE PRODUCTS
-- ==========================
CREATE TABLE products (
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
);

-- Images supplémentaires
CREATE TABLE product_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    image TEXT NOT NULL,
    FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Variantes produit (ex: taille / couleur spécifique)
CREATE TABLE product_variants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    variant_name TEXT,
    color_hex TEXT,
    additional_price REAL DEFAULT 0,
    stock INTEGER DEFAULT 0,
    FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ==========================
-- COMMANDES
-- ==========================
CREATE TABLE orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    shop_id INTEGER NOT NULL,
    total REAL NOT NULL,
    status TEXT DEFAULT 'pending',  -- pending / paid / shipped / delivered / cancelled
    payment_method TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY(shop_id) REFERENCES shops(id) ON DELETE CASCADE
);

CREATE TABLE order_items (
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
);

-- ==========================
-- AVIS PRODUITS
-- ==========================
CREATE TABLE product_reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    user_id INTEGER,
    rating INTEGER NOT NULL,  -- 1 à 5
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ==========================
-- INDEX
-- ==========================
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_shops_user_id ON shops(user_id);
CREATE INDEX idx_products_shop_id ON products(shop_id);
CREATE INDEX idx_products_category ON products(category);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_shop_id ON orders(shop_id);

-- ==========================
-- INSERT SUPER ADMIN USER
-- ==========================
-- Mot de passe à remplacer ensuite par un hash bcrypt réel
INSERT INTO users (first_name, last_name, email, password)
VALUES ('Super', 'Admin', 'admin@example.com', '$2y$10$REPLACE_THIS_HASH');

INSERT INTO user_roles (user_id, role_id)
VALUES (1, 1);  -- user_id = 1 → role super_admin

COMMIT;
