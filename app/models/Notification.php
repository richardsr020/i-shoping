<?php
require_once __DIR__ . '/../config.php';

class Notification {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function create(?int $userId, ?int $shopId, string $type, string $title, ?string $body = null, ?array $data = null): int {
        $type = trim($type);
        $title = trim($title);
        if ($type === '' || $title === '') {
            throw new Exception('Notification invalide.');
        }

        $dataJson = null;
        if (is_array($data)) {
            $dataJson = json_encode($data);
        }

        $stmt = $this->db->prepare('INSERT INTO notifications (user_id, shop_id, type, title, body, data_json) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([
            $userId && $userId > 0 ? $userId : null,
            $shopId && $shopId > 0 ? $shopId : null,
            $type,
            $title,
            $body,
            $dataJson,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function listForUser(int $userId, int $limit = 50): array {
        $limit = max(1, min(200, $limit));
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT ' . (int)$limit);
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function listForShop(int $shopId, int $limit = 50): array {
        $limit = max(1, min(200, $limit));
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE shop_id = ? ORDER BY id DESC LIMIT ' . (int)$limit);
        $stmt->execute([$shopId]);
        return $stmt->fetchAll();
    }

    public function markAllReadForUser(int $userId): void {
        $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')->execute([$userId]);
    }

    public function markAllReadForShop(int $shopId): void {
        $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE shop_id = ?')->execute([$shopId]);
    }
}
