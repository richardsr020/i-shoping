SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS roles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  level INT NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password VARCHAR(255) NOT NULL,
  birth_date DATE NULL,
  gender VARCHAR(30) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  suspended_until DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_roles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  role_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_roles_user_role (user_id, role_id),
  KEY idx_user_roles_user_id (user_id),
  KEY idx_user_roles_role_id (role_id),
  CONSTRAINT fk_user_roles_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_user_roles_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS shops (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NULL,
  url VARCHAR(255) NULL,
  description TEXT NULL,
  logo TEXT NULL,
  banner TEXT NULL,
  email_contact VARCHAR(190) NULL,
  phone VARCHAR(50) NULL,
  address VARCHAR(255) NULL,
  city VARCHAR(120) NULL,
  country VARCHAR(120) NULL,
  currency VARCHAR(10) NOT NULL DEFAULT 'USD',
  stars DOUBLE NOT NULL DEFAULT 0,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  suspended_until DATETIME NULL,
  payment_methods_json TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_shops_user_id (user_id),
  UNIQUE KEY uq_shops_slug (slug),
  CONSTRAINT fk_shops_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  shop_id INT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  description TEXT NULL,
  price DOUBLE NOT NULL,
  promo_price DOUBLE NULL,
  category VARCHAR(120) NULL,
  brand VARCHAR(120) NULL,
  size VARCHAR(60) NULL,
  sku VARCHAR(120) NULL,
  weight DOUBLE NULL,
  colors_json TEXT NULL,
  tags_json TEXT NULL,
  image TEXT NULL,
  stock INT NOT NULL DEFAULT 0,
  is_physical TINYINT(1) NOT NULL DEFAULT 1,
  status VARCHAR(20) NOT NULL DEFAULT 'active',
  min_order_qty INT NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_products_shop_id (shop_id),
  KEY idx_products_category (category),
  KEY idx_products_status (status),
  CONSTRAINT fk_products_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_images (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  image TEXT NOT NULL,
  PRIMARY KEY (id),
  KEY idx_product_images_product_id (product_id),
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_variants (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  variant_name VARCHAR(190) NULL,
  color_hex VARCHAR(10) NULL,
  additional_price DOUBLE NOT NULL DEFAULT 0,
  stock INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_product_variants_product_id (product_id),
  CONSTRAINT fk_product_variants_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  shop_id INT UNSIGNED NOT NULL,
  total DOUBLE NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'pending',
  paid TINYINT(1) NOT NULL DEFAULT 0,
  satisfied TINYINT(1) NOT NULL DEFAULT 0,
  canceled TINYINT(1) NOT NULL DEFAULT 0,
  payment_method VARCHAR(60) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_orders_user_id (user_id),
  KEY idx_orders_shop_id (shop_id),
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_orders_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DOUBLE NOT NULL,
  total DOUBLE NOT NULL,
  color VARCHAR(60) NULL,
  size VARCHAR(60) NULL,
  PRIMARY KEY (id),
  KEY idx_order_items_order_id (order_id),
  KEY idx_order_items_product_id (product_id),
  CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS product_reviews (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  user_id INT UNSIGNED NULL,
  rating INT NOT NULL,
  comment TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_product_reviews_product_id (product_id),
  KEY idx_product_reviews_user_id (user_id),
  CONSTRAINT fk_product_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_product_reviews_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS conversations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  shop_id INT UNSIGNED NOT NULL,
  buyer_user_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_conversations_shop_buyer (shop_id, buyer_user_id),
  KEY idx_conversations_shop_id (shop_id),
  KEY idx_conversations_buyer_user_id (buyer_user_id),
  CONSTRAINT fk_conversations_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE,
  CONSTRAINT fk_conversations_buyer FOREIGN KEY (buyer_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  conversation_id INT UNSIGNED NOT NULL,
  sender_user_id INT UNSIGNED NOT NULL,
  body TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_messages_conversation_id (conversation_id),
  KEY idx_messages_sender_user_id (sender_user_id),
  CONSTRAINT fk_messages_conversation FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
  CONSTRAINT fk_messages_sender FOREIGN KEY (sender_user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS notifications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  shop_id INT UNSIGNED NULL,
  type VARCHAR(50) NOT NULL,
  title VARCHAR(190) NOT NULL,
  body TEXT NULL,
  data_json TEXT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_notifications_user_id (user_id),
  KEY idx_notifications_shop_id (shop_id),
  KEY idx_notifications_is_read (is_read),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_notifications_shop FOREIGN KEY (shop_id) REFERENCES shops(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
