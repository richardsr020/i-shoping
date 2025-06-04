<?php
require_once __DIR__ . '/../config.php';

class Conversation {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getOrCreate(int $shopId, int $buyerUserId): int {
        if ($shopId <= 0 || $buyerUserId <= 0) {
            throw new Exception('Paramètres invalides.');
        }

        $stmt = $this->db->prepare('SELECT id FROM conversations WHERE shop_id = ? AND buyer_user_id = ? LIMIT 1');
        $stmt->execute([$shopId, $buyerUserId]);
        $id = (int)($stmt->fetchColumn() ?: 0);
        if ($id > 0) {
            return $id;
        }

        $ins = $this->db->prepare('INSERT INTO conversations (shop_id, buyer_user_id) VALUES (?, ?)');
        $ins->execute([$shopId, $buyerUserId]);
        return (int)$this->db->lastInsertId();
    }

    public function listForUser(int $userId): array {
        // User can participate as buyer, or as vendor via shops they own
        $stmt = $this->db->prepare('
            SELECT
                c.id,
                c.shop_id,
                c.buyer_user_id,
                c.updated_at,
                s.name AS shop_name,
                s.user_id AS shop_owner_id,
                TRIM(COALESCE(u.first_name, \'\') || \' \' || COALESCE(u.last_name, \'\')) AS buyer_name,
                u.email AS buyer_email,
                (SELECT body FROM messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message,
                (SELECT created_at FROM messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_message_at
            FROM conversations c
            INNER JOIN shops s ON s.id = c.shop_id
            INNER JOIN users u ON u.id = c.buyer_user_id
            WHERE c.buyer_user_id = ? OR s.user_id = ?
            ORDER BY c.updated_at DESC
        ');
        $stmt->execute([$userId, $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $conversationId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM conversations WHERE id = ? LIMIT 1');
        $stmt->execute([$conversationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function userCanAccess(int $conversationId, int $userId): bool {
        $stmt = $this->db->prepare('
            SELECT 1
            FROM conversations c
            INNER JOIN shops s ON s.id = c.shop_id
            WHERE c.id = ? AND (c.buyer_user_id = ? OR s.user_id = ?)
            LIMIT 1
        ');
        $stmt->execute([$conversationId, $userId, $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function touch(int $conversationId): void {
        $this->db->prepare('UPDATE conversations SET updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$conversationId]);
    }
}
