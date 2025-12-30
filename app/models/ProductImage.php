<?php
require_once __DIR__ . '/../config.php';

class ProductImage {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getByProductId(int $productId): array {
        $stmt = $this->db->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY id ASC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addImages(int $productId, array $urls): void {
        if (empty($urls)) {
            return;
        }

        $stmt = $this->db->prepare('INSERT INTO product_images (product_id, image) VALUES (?, ?)');
        foreach ($urls as $url) {
            $url = trim((string)$url);
            if ($url === '') {
                continue;
            }
            $stmt->execute([$productId, $url]);
        }
    }

    public function deleteByProductId(int $productId): void {
        $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = ?');
        $stmt->execute([$productId]);
    }
}
