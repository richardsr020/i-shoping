
<?php
require_once __DIR__ . '/../config.php';

class Product {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getByShopId(int $shopId): array {
        $stmt = $this->db->prepare('
            SELECT
                p.*,
                (SELECT COUNT(*) FROM product_images pi WHERE pi.product_id = p.id) AS extra_images_count,
                (SELECT COUNT(*) FROM product_variants pv WHERE pv.product_id = p.id) AS variants_count
            FROM products p
            WHERE p.shop_id = ?
            ORDER BY p.created_at DESC
        ');
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }

    public function findPublicById(int $productId): ?array {
        $stmt = $this->db->prepare('
            SELECT
                p.*,
                s.name AS shop_name,
                s.slug AS shop_slug,
                s.url AS shop_url,
                s.logo AS shop_logo,
                s.banner AS shop_banner,
                s.currency AS shop_currency,
                s.email_contact AS shop_email_contact,
                s.phone AS shop_phone
            FROM products p
            INNER JOIN shops s ON s.id = p.shop_id
            WHERE p.id = ? AND p.status = "active"
            LIMIT 1
        ');
        $stmt->execute([$productId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getActiveByShopIdPublic(int $shopId): array {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE shop_id = ? AND status = "active" ORDER BY created_at DESC');
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }

    public function findOwnedByUser(int $productId, int $userId): ?array {
        $stmt = $this->db->prepare('
            SELECT p.*
            FROM products p
            INNER JOIN shops s ON s.id = p.shop_id
            WHERE p.id = ? AND s.user_id = ?
        ');
        $stmt->execute([$productId, $userId]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function create(int $shopId, int $userId, array $data): int {
        $stmt = $this->db->prepare('SELECT id FROM shops WHERE id = ? AND user_id = ?');
        $stmt->execute([$shopId, $userId]);
        if (!$stmt->fetchColumn()) {
            throw new Exception('Boutique introuvable.');
        }

        $name = trim((string)($data['name'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        $price = (float)($data['price'] ?? 0);
        $promoPrice = $data['promo_price'] ?? null;
        $category = trim((string)($data['category'] ?? ''));
        $brand = trim((string)($data['brand'] ?? ''));
        $size = trim((string)($data['size'] ?? ''));
        $sku = trim((string)($data['sku'] ?? ''));
        $weight = $data['weight'] ?? null;
        $colorsJson = $data['colors_json'] ?? null;
        $tagsJson = $data['tags_json'] ?? null;
        $image = trim((string)($data['image'] ?? ''));
        $stock = (int)($data['stock'] ?? 0);
        $isPhysical = (int)($data['is_physical'] ?? 1);
        $status = trim((string)($data['status'] ?? 'active'));
        $minOrderQty = (int)($data['min_order_qty'] ?? 1);

        if ($name === '') {
            throw new Exception('Le nom du produit est requis.');
        }
        if ($price <= 0) {
            throw new Exception('Le prix doit être supérieur à 0.');
        }

        if ($minOrderQty <= 0) {
            $minOrderQty = 1;
        }

        $allowedStatus = ['active', 'inactive'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 'active';
        }

        if ($promoPrice !== null && $promoPrice !== '') {
            $promoPrice = (float)$promoPrice;
        } else {
            $promoPrice = null;
        }

        if ($weight !== null && $weight !== '') {
            $weight = (float)$weight;
        } else {
            $weight = null;
        }

        if ($colorsJson !== null && $colorsJson !== '' && is_array($colorsJson)) {
            $colorsJson = json_encode($colorsJson);
        }
        if ($tagsJson !== null && $tagsJson !== '' && is_array($tagsJson)) {
            $tagsJson = json_encode($tagsJson);
        }

        if ($isPhysical !== 0 && $isPhysical !== 1) {
            $isPhysical = 1;
        }

        $stmt = $this->db->prepare('
            INSERT INTO products (
                shop_id, name, description, price, promo_price, category, brand, size, sku, weight,
                colors_json, tags_json, image, stock, is_physical, status, min_order_qty
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([
            $shopId,
            $name,
            $description !== '' ? $description : null,
            $price,
            $promoPrice,
            $category !== '' ? $category : null,
            $brand !== '' ? $brand : null,
            $size !== '' ? $size : null,
            $sku !== '' ? $sku : null,
            $weight,
            $colorsJson,
            $tagsJson,
            $image !== '' ? $image : null,
            $stock,
            $isPhysical,
            $status,
            $minOrderQty
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function delete(int $productId, int $userId): bool {
        $product = $this->findOwnedByUser($productId, $userId);
        if (!$product) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$productId]);
    }

    public function update(int $productId, int $userId, array $data): bool {
        $product = $this->findOwnedByUser($productId, $userId);
        if (!$product) {
            throw new Exception('Produit introuvable.');
        }

        $name = trim((string)($data['name'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));
        $price = (float)($data['price'] ?? 0);
        $promoPrice = $data['promo_price'] ?? null;
        $category = trim((string)($data['category'] ?? ''));
        $brand = trim((string)($data['brand'] ?? ''));
        $size = trim((string)($data['size'] ?? ''));
        $sku = trim((string)($data['sku'] ?? ''));
        $stock = (int)($data['stock'] ?? 0);
        $status = trim((string)($data['status'] ?? ($product['status'] ?? 'active')));
        $minOrderQty = (int)($data['min_order_qty'] ?? ($product['min_order_qty'] ?? 1));

        if ($name === '') {
            throw new Exception('Le nom du produit est requis.');
        }
        if ($price <= 0) {
            throw new Exception('Le prix doit être supérieur à 0.');
        }

        if ($minOrderQty <= 0) {
            $minOrderQty = 1;
        }

        $allowedStatus = ['active', 'inactive'];
        if (!in_array($status, $allowedStatus, true)) {
            $status = 'active';
        }

        if ($promoPrice !== null && $promoPrice !== '') {
            $promoPrice = (float)$promoPrice;
        } else {
            $promoPrice = null;
        }

        $stmt = $this->db->prepare('
            UPDATE products
            SET name = ?,
                description = ?,
                price = ?,
                promo_price = ?,
                category = ?,
                brand = ?,
                size = ?,
                sku = ?,
                stock = ?,
                status = ?,
                min_order_qty = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ');

        return $stmt->execute([
            $name,
            $description !== '' ? $description : null,
            $price,
            $promoPrice,
            $category !== '' ? $category : null,
            $brand !== '' ? $brand : null,
            $size !== '' ? $size : null,
            $sku !== '' ? $sku : null,
            $stock,
            $status,
            $minOrderQty,
            $productId
        ]);
    }
}

