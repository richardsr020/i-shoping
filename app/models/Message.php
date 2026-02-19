<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Conversation.php';

class Message {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    private function sanitizeBody(string $body): string {
        $body = trim($body);
        if ($body === '') {
            return '';
        }

        $replacements = [
            '/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}\b/i' => '[coordonnées supprimées]',
            '/\b(?:https?:\/\/)?(?:www\.)?(?:t\.me|telegram\.me|telegram\.dog)\/[A-Za-z0-9_]{3,}\b/i' => '[coordonnées supprimées]',
            '/\b(?:https?:\/\/)?(?:wa\.me|api\.whatsapp\.com)\/[0-9]+\b/i' => '[coordonnées supprimées]',
            '/\b(?:whatsapp|telegram|tg|t\.me)\b/i' => '[coordonnées supprimées]',
            '/\+?\d[\d\s().-]{7,}\d/' => '[coordonnées supprimées]',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $body = preg_replace($pattern, $replacement, $body);
        }

        $body = preg_replace('/\[coordonnées supprimées\](?:\s*\[coordonnées supprimées\])+/i', '[coordonnées supprimées]', $body);
        $body = trim($body);
        return $body;
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
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $meta = [];
            $rawMeta = (string)($row['meta_json'] ?? '');
            if ($rawMeta !== '') {
                $decoded = json_decode($rawMeta, true);
                if (is_array($decoded)) {
                    $meta = $decoded;
                }
            }
            $row['meta'] = $meta;
            unset($row['meta_json']);
        }
        unset($row);
        return $rows;
    }

    public function send(int $conversationId, int $senderUserId, string $body, array $meta = []): int {
        $body = $this->sanitizeBody($body);
        if ($conversationId <= 0 || $senderUserId <= 0) {
            throw new Exception('Paramètres invalides.');
        }
        if ($body === '') {
            throw new Exception('Message vide.');
        }
        $len = function_exists('mb_strlen') ? mb_strlen($body) : strlen($body);
        if ($len > 2000) {
            throw new Exception('Message trop long.');
        }

        $metaJson = null;
        if (!empty($meta)) {
            $metaJson = json_encode($meta, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!is_string($metaJson)) {
                throw new Exception('Métadonnées du message invalides.');
            }
            if (strlen($metaJson) > 6000) {
                throw new Exception('Métadonnées du message trop volumineuses.');
            }
        }

        $ins = $this->db->prepare('INSERT INTO messages (conversation_id, sender_user_id, body, meta_json) VALUES (?, ?, ?, ?)');
        $ins->execute([$conversationId, $senderUserId, $body, $metaJson]);

        $conv = new Conversation();
        $conv->touch($conversationId);

        return (int)$this->db->lastInsertId();
    }
}
