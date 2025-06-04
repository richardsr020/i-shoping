<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../models/Notification.php';

class ChatController {
    public function index(): void {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'chat';
            redirect('login');
        }

        // The chat UI is rendered by app/views/chat.php
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = [
                'title' => 'Messagerie - ' . APP_NAME,
            ];
        }
    }

    private function requireJsonPost(): array {
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Connexion requise']);
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
            exit;
        }
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Payload invalide']);
            exit;
        }
        return $payload;
    }

    public function listConversations(): void {
        header('Content-Type: application/json');
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Connexion requise']);
            return;
        }

        $conv = new Conversation();
        $items = $conv->listForUser((int)$_SESSION['user_id']);
        echo json_encode(['success' => true, 'conversations' => $items]);
    }

    public function pollMessages(): void {
        header('Content-Type: application/json');
        $payload = $this->requireJsonPost();

        $conversationId = (int)($payload['conversation_id'] ?? 0);
        $afterId = (int)($payload['after_id'] ?? 0);
        if ($conversationId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'conversation_id invalide']);
            return;
        }

        $conv = new Conversation();
        $uid = (int)$_SESSION['user_id'];
        if (!$conv->userCanAccess($conversationId, $uid)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès refusé']);
            return;
        }

        $msg = new Message();
        $messages = $msg->listByConversation($conversationId, 100, $afterId);
        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    public function sendMessage(): void {
        header('Content-Type: application/json');
        $payload = $this->requireJsonPost();

        $shopId = (int)($payload['shop_id'] ?? 0);
        $conversationId = (int)($payload['conversation_id'] ?? 0);
        $body = (string)($payload['body'] ?? '');

        $uid = (int)$_SESSION['user_id'];
        $shopModel = new Shop();
        $convModel = new Conversation();

        // Determine conversation
        if ($conversationId > 0) {
            if (!$convModel->userCanAccess($conversationId, $uid)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Accès refusé']);
                return;
            }
        } else {
            if ($shopId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'shop_id invalide']);
                return;
            }

            $shop = $shopModel->findById($shopId);
            if (!$shop) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Boutique introuvable']);
                return;
            }

            // buyer is current user; vendor is shop.user_id
            $buyerUserId = $uid;
            $conversationId = $convModel->getOrCreate($shopId, $buyerUserId);
        }

        try {
            $messageModel = new Message();
            $newId = $messageModel->send($conversationId, $uid, $body);

            // Notify shop and buyer
            $db = getDB();
            $stmt = $db->prepare('SELECT c.shop_id, c.buyer_user_id, s.user_id AS shop_owner_id, s.name AS shop_name FROM conversations c INNER JOIN shops s ON s.id = c.shop_id WHERE c.id = ? LIMIT 1');
            $stmt->execute([$conversationId]);
            $meta = $stmt->fetch() ?: [];

            $shopId = (int)($meta['shop_id'] ?? 0);
            $buyerUserId = (int)($meta['buyer_user_id'] ?? 0);
            $shopOwnerId = (int)($meta['shop_owner_id'] ?? 0);
            $shopName = (string)($meta['shop_name'] ?? '');

            $notif = new Notification();
            if ($shopId > 0) {
                $notif->create(null, $shopId, 'chat_message', 'Nouveau message', 'Nouveau message dans la conversation.', [
                    'conversation_id' => $conversationId,
                    'message_id' => $newId,
                    'from_user_id' => $uid,
                ]);
            }
            // notify receiver user (buyer or vendor)
            $receiverUserId = ($uid === $buyerUserId) ? $shopOwnerId : $buyerUserId;
            if ($receiverUserId > 0) {
                $notif->create($receiverUserId, null, 'chat_message', 'Nouveau message', $shopName !== '' ? ('Conversation avec ' . $shopName) : 'Conversation', [
                    'conversation_id' => $conversationId,
                    'message_id' => $newId,
                    'from_user_id' => $uid,
                ]);
            }

            echo json_encode(['success' => true, 'conversation_id' => $conversationId, 'message_id' => $newId]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
