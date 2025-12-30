<?php
require_once __DIR__ . '/../config.php';

class ProductVariant {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getByProductId(int $productId): array {
        $stmt = $this->db->prepare('SELECT * FROM product_variants WHERE product_id = ? ORDER BY id ASC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function addVariants(int $productId, array $variants): void {
        if (empty($variants)) {
            return;
        }

        $stmt = $this->db->prepare('
            INSERT INTO product_variants (product_id, variant_name, color_hex, additional_price, stock)
            VALUES (?, ?, ?, ?, ?)
        ');

        foreach ($variants as $v) {
            $variantName = isset($v['variant_name']) ? trim((string)$v['variant_name']) : '';
            $colorHex = isset($v['color_hex']) ? trim((string)$v['color_hex']) : '';
            $additionalPrice = $v['additional_price'] ?? 0;
            $stock = $v['stock'] ?? 0;

            if ($additionalPrice !== null && $additionalPrice !== '') {
                $additionalPrice = (float)$additionalPrice;
            } else {
                $additionalPrice = 0;
            }

            if ($stock !== null && $stock !== '') {
                $stock = (int)$stock;
            } else {
                $stock = 0;
            }

            $stmt->execute([
                $productId,
                $variantName !== '' ? $variantName : null,
                $colorHex !== '' ? $colorHex : null,
                $additionalPrice,
                $stock
            ]);
        }
    }

    public function deleteByProductId(int $productId): void {
        $stmt = $this->db->prepare('DELETE FROM product_variants WHERE product_id = ?');
        $stmt->execute([$productId]);
    }
}
