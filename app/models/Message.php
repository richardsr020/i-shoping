<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Conversation.php';

class Message {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function listByConversation(int $conversationId, int $limit = 50, int $afterId = 0): array {
        $limit = max(1, min(200, $limit));
        $afterId = max(0, $afterId);

        $stmt = $this->db->prepare('
            SELECT m.*, u.first_name, u.last_name
            FROM messages m
            INNER JOIN users u ON u.id = m.sender_user_id
            WHERE m.conversation_id = ? AND m.id > ?
            ORDER BY m.id ASC
            LIMIT ' . (int)$limit . '
        ');
        $stmt->execute([$conversationId, $afterId]);
        return $stmt->fetchAll();
    }

    public function send(int $conversationId, int $senderUserId, string $body): int {
        $body = trim($body);
        if ($conversationId <= 0 || $senderUserId <= 0) {
            throw new Exception('Paramètres invalides.');
        }
        if ($body === '') {
            throw new Exception('Message vide.');
        }
        if (mb_strlen($body) > 2000) {
            throw new Exception('Message trop long.');
        }

        $ins = $this->db->prepare('INSERT INTO messages (conversation_id, sender_user_id, body) VALUES (?, ?, ?)');
        $ins->execute([$conversationId, $senderUserId, $body]);

        $conv = new Conversation();
        $conv->touch($conversationId);

        return (int)$this->db->lastInsertId();
    }
}
