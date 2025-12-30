<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Shop.php';

class Order {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function createWithItems(int $userId, int $shopId, array $items, string $paymentMethod = 'cash'): int {
        if ($userId <= 0) {
            throw new Exception('Utilisateur introuvable.');
        }
        if ($shopId <= 0) {
            throw new Exception('Boutique introuvable.');
        }
        if (empty($items)) {
            throw new Exception('Panier vide.');
        }

        $this->db->beginTransaction();
        try {
            $total = 0.0;
            $productStmt = $this->db->prepare('SELECT id, shop_id, price, promo_price, status FROM products WHERE id = ? LIMIT 1');

            foreach ($items as $it) {
                $productId = (int)($it['product_id'] ?? 0);
                $qty = (int)($it['quantity'] ?? 1);
                if ($productId <= 0 || $qty <= 0) {
                    throw new Exception('Item invalide.');
                }

                $productStmt->execute([$productId]);
                $p = $productStmt->fetch();
                if (!$p || (string)$p['status'] !== 'active') {
                    throw new Exception('Produit introuvable ou inactif.');
                }
                if ((int)$p['shop_id'] !== $shopId) {
                    throw new Exception('Tous les produits doivent appartenir à la même boutique.');
                }

                $price = (float)($p['price'] ?? 0);
                $promo = (float)($p['promo_price'] ?? 0);
                $unit = ($promo > 0 && $promo < $price) ? $promo : $price;
                $total += $unit * $qty;
            }

            $orderStmt = $this->db->prepare('INSERT INTO orders (user_id, shop_id, total, status, paid, satisfied, canceled, payment_method) VALUES (?, ?, ?, ?, 0, 0, 0, ?)');
            $orderStmt->execute([$userId, $shopId, $total, 'pending', $paymentMethod]);
            $orderId = (int)$this->db->lastInsertId();

            $itemStmt = $this->db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, total, color, size) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $productStmt2 = $this->db->prepare('SELECT id, price, promo_price FROM products WHERE id = ? LIMIT 1');

            foreach ($items as $it) {
                $productId = (int)($it['product_id'] ?? 0);
                $qty = (int)($it['quantity'] ?? 1);
                $color = isset($it['color']) ? trim((string)$it['color']) : null;
                $size = isset($it['size']) ? trim((string)$it['size']) : null;

                $productStmt2->execute([$productId]);
                $p = $productStmt2->fetch();

                $price = (float)($p['price'] ?? 0);
                $promo = (float)($p['promo_price'] ?? 0);
                $unit = ($promo > 0 && $promo < $price) ? $promo : $price;
                $lineTotal = $unit * $qty;

                $itemStmt->execute([
                    $orderId,
                    $productId,
                    $qty,
                    $unit,
                    $lineTotal,
                    $color !== '' ? $color : null,
                    $size !== '' ? $size : null
                ]);
            }

            $this->db->commit();

            // Mettre à jour la cote boutique (nouvelle commande)
            $shopModel = new Shop();
            $shopModel->recalculateStars($shopId);
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function cancelByCustomer(int $orderId, int $userId): bool {
        if ($orderId <= 0 || $userId <= 0) {
            throw new Exception('Paramètres invalides.');
        }

        $stmt = $this->db->prepare('SELECT id, shop_id, paid, satisfied, canceled FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$orderId, $userId]);
        $o = $stmt->fetch();
        if (!$o) {
            throw new Exception('Commande introuvable.');
        }

        if ((int)($o['canceled'] ?? 0) === 1) {
            return true;
        }
        if ((int)($o['paid'] ?? 0) === 1) {
            throw new Exception('Commande déjà payée: annulation impossible.');
        }
        if ((int)($o['satisfied'] ?? 0) === 1) {
            throw new Exception('Commande déjà satisfaite: annulation impossible.');
        }

        $upd = $this->db->prepare('UPDATE orders SET canceled = 1, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?');
        $ok = $upd->execute(['canceled', $orderId, $userId]);

        $shopId = (int)($o['shop_id'] ?? 0);
        if ($shopId > 0) {
            $shopModel = new Shop();
            $shopModel->recalculateStars($shopId);
        }

        return (bool)$ok;
    }

    public function getByShopId(int $shopId): array {
        $stmt = $this->db->prepare('
            SELECT o.*,
                   TRIM(COALESCE(u.first_name, \'\') || \' \' || COALESCE(u.last_name, \'\')) AS customer_name,
                   u.email AS customer_email
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            WHERE o.shop_id = ?
            ORDER BY o.created_at DESC
        ');
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }

    public function markPaidByVendor(int $orderId, int $shopId): bool {
        $stmt = $this->db->prepare('UPDATE orders SET paid = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND shop_id = ?');
        $ok = $stmt->execute([$orderId, $shopId]);

        $shopModel = new Shop();
        $shopModel->recalculateStars($shopId);

        return $ok;
    }

    public function markSatisfiedByCustomer(int $orderId, int $userId): bool {
        $stmtShop = $this->db->prepare('SELECT shop_id FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $stmtShop->execute([$orderId, $userId]);
        $shopId = (int)($stmtShop->fetchColumn() ?: 0);

        $stmt = $this->db->prepare('UPDATE orders SET satisfied = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?');
        $ok = $stmt->execute([$orderId, $userId]);

        if ($shopId > 0) {
            $shopModel = new Shop();
            $shopModel->recalculateStars($shopId);
        }

        return $ok;
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare('
            SELECT
                o.*,
                s.name AS shop_name,
                s.slug AS shop_slug,
                s.url AS shop_url
            FROM orders o
            INNER JOIN shops s ON s.id = o.shop_id
            WHERE o.user_id = ?
            ORDER BY o.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
