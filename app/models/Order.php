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
                   TRIM(CONCAT(COALESCE(u.first_name, \'\'), \' \', COALESCE(u.last_name, \'\'))) AS customer_name,
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
        $stmt = $this->db->prepare('UPDATE orders SET paid = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND shop_id = ? AND COALESCE(canceled, 0) = 0');
        $ok = $stmt->execute([$orderId, $shopId]);

        // Si le client a déjà marqué satisfait, la commande devient completed
        $this->db->prepare('UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND shop_id = ? AND paid = 1 AND satisfied = 1 AND COALESCE(canceled, 0) = 0')
            ->execute(['completed', $orderId, $shopId]);

        $shopModel = new Shop();
        $shopModel->recalculateStars($shopId);

        return $ok;
    }

    public function markSatisfiedByCustomer(int $orderId, int $userId): bool {
        $stmtShop = $this->db->prepare('SELECT shop_id FROM orders WHERE id = ? AND user_id = ? AND COALESCE(canceled, 0) = 0 LIMIT 1');
        $stmtShop->execute([$orderId, $userId]);
        $shopId = (int)($stmtShop->fetchColumn() ?: 0);

        $stmt = $this->db->prepare('UPDATE orders SET satisfied = 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ? AND COALESCE(canceled, 0) = 0');
        $ok = $stmt->execute([$orderId, $userId]);

        // Si le vendeur a déjà marqué payé, la commande devient completed
        if ($shopId > 0) {
            $this->db->prepare('UPDATE orders SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ? AND paid = 1 AND satisfied = 1 AND COALESCE(canceled, 0) = 0')
                ->execute(['completed', $orderId, $userId]);
        }

        if ($shopId > 0) {
            $shopModel = new Shop();
            $shopModel->recalculateStars($shopId);
        }

        return $ok;
    }

    public function getByUserId(int $userId): array {
        // NB: une commande peut avoir plusieurs items. Pour l'affichage client "en cours",
        // on récupère le 1er item comme résumé (correlated subquery) pour éviter les duplications.
        $stmt = $this->db->prepare('
            SELECT
                o.*,
                s.name AS shop_name,
                s.slug AS shop_slug,
                s.url AS shop_url,
                s.currency AS shop_currency,
                p.name AS product_name,
                oi.quantity AS quantity,
                oi.price AS unit_price,
                oi.total AS line_total
            FROM orders o
            INNER JOIN shops s ON s.id = o.shop_id
            LEFT JOIN order_items oi ON oi.id = (
                SELECT oi2.id
                FROM order_items oi2
                WHERE oi2.order_id = o.id
                ORDER BY oi2.id ASC
                LIMIT 1
            )
            LEFT JOIN products p ON p.id = oi.product_id
            WHERE o.user_id = ?
              AND COALESCE(o.canceled, 0) = 0
              AND COALESCE(o.status, \'pending\') != \'completed\'
            ORDER BY o.created_at DESC
        ');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getKpisByShopId(int $shopId): array {
        if ($shopId <= 0) {
            return [
                'orders_count' => 0,
                'revenue_total' => 0.0,
                'customers_count' => 0,
            ];
        }

        $stmt = $this->db->prepare('
            SELECT
                COUNT(*) AS orders_count,
                COALESCE(SUM(CASE WHEN paid = 1 THEN total ELSE 0 END), 0) AS revenue_total,
                COUNT(DISTINCT CASE WHEN user_id IS NOT NULL THEN user_id END) AS customers_count
            FROM orders
            WHERE shop_id = ? AND COALESCE(canceled, 0) = 0
        ');
        $stmt->execute([$shopId]);
        $row = $stmt->fetch() ?: [];

        return [
            'orders_count' => (int)($row['orders_count'] ?? 0),
            'revenue_total' => (float)($row['revenue_total'] ?? 0),
            'customers_count' => (int)($row['customers_count'] ?? 0),
        ];
    }

    public function getRecentByShopId(int $shopId, int $limit = 5, ?string $statusFilter = null): array {
        if ($shopId <= 0) {
            return [];
        }

        $limit = max(1, min(50, $limit));
        $allowedStatuses = ['pending', 'processing', 'completed', 'paid', 'satisfied', 'canceled'];

        $where = 'o.shop_id = ?';
        $params = [$shopId];

        if ($statusFilter !== null && $statusFilter !== '' && $statusFilter !== 'all') {
            $statusFilter = trim($statusFilter);
            if (in_array($statusFilter, $allowedStatuses, true)) {
                $where .= ' AND o.status = ?';
                $params[] = $statusFilter;
            }
        }

        $sql = '
            SELECT
                o.*,
                TRIM(CONCAT(COALESCE(u.first_name, \'\'), \' \', COALESCE(u.last_name, \'\'))) AS customer_name,
                u.email AS customer_email
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            WHERE ' . $where . '
            ORDER BY o.created_at DESC
            LIMIT ' . (int)$limit . '
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSalesByDay(int $shopId, int $days = 30, bool $paidOnly = true): array {
        if ($shopId <= 0) {
            return [];
        }

        $days = max(1, min(365, $days));
        $cutoff = date('Y-m-d H:i:s', time() - ($days * 86400));
        $paidClause = $paidOnly ? ' AND paid = 1' : '';

        $stmt = $this->db->prepare('
            SELECT
                DATE(created_at) AS day,
                COALESCE(SUM(total), 0) AS total
            FROM orders
            WHERE shop_id = ?
              AND COALESCE(canceled, 0) = 0
              ' . $paidClause . '
              AND created_at >= ?
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ');

        $stmt->execute([$shopId, $cutoff]);
        return $stmt->fetchAll();
    }
}
