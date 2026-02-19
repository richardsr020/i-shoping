<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../models/Notification.php';

class ChatController {
    private function resolveAssetUrl(string $path): string {
        $path = trim($path);
        if ($path === '') {
            return '';
        }
        if (preg_match('/^https?:\/\//i', $path)) {
            return $path;
        }
        if (strpos($path, '/') === 0) {
            return rtrim((string)BASE_URL, '/') . $path;
        }
        return rtrim((string)BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    public function index(): void {
        if (!isLoggedIn()) {
            $shopId = (int)($_GET['shop_id'] ?? 0);
            $productId = (int)($_GET['product_id'] ?? 0);
            if ($shopId > 0) {
                $target = 'chat&shop_id=' . $shopId;
                if ($productId > 0) {
                    $target .= '&product_id=' . $productId;
                }
                $_SESSION['redirect_after_login'] = $target;
            } else {
                $_SESSION['redirect_after_login'] = 'chat';
            }
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

    public function startConversation(): void {
        header('Content-Type: application/json');
        $payload = $this->requireJsonPost();

        $shopId = (int)($payload['shop_id'] ?? 0);
        if ($shopId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'shop_id invalide']);
            exit;
        }

        $uid = (int)$_SESSION['user_id'];
        try {
            $shopModel = new Shop();
            $shop = $shopModel->findById($shopId);
            if (!$shop) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Boutique introuvable']);
                exit;
            }

            $convModel = new Conversation();
            $conversationId = $convModel->getOrCreate($shopId, $uid);
            echo json_encode(['success' => true, 'conversation_id' => $conversationId]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }

        exit;
    }

    public function listConversations(): void {
        header('Content-Type: application/json');
        if (!isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Connexion requise']);
            exit;
        }

        $conv = new Conversation();
        $items = $conv->listForUser((int)$_SESSION['user_id']);
        echo json_encode(['success' => true, 'conversations' => $items]);

        exit;
    }

    public function pollMessages(): void {
        header('Content-Type: application/json');
        $payload = $this->requireJsonPost();

        $conversationId = (int)($payload['conversation_id'] ?? 0);
        $afterId = (int)($payload['after_id'] ?? 0);
        if ($conversationId <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'conversation_id invalide']);
            exit;
        }

        $conv = new Conversation();
        $uid = (int)$_SESSION['user_id'];
        if (!$conv->userCanAccess($conversationId, $uid)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Accès refusé']);
            exit;
        }

        $msg = new Message();
        $messages = $msg->listByConversation($conversationId, 100, $afterId);
        echo json_encode(['success' => true, 'messages' => $messages]);

        exit;
    }

    public function sendMessage(): void {
        header('Content-Type: application/json');
        $payload = $this->requireJsonPost();

        $shopId = (int)($payload['shop_id'] ?? 0);
        $conversationId = (int)($payload['conversation_id'] ?? 0);
        $productId = (int)($payload['product_id'] ?? 0);
        $body = (string)($payload['body'] ?? '');

        error_log('[chat.send] called uid=' . (int)($_SESSION['user_id'] ?? 0) . ' conversation_id=' . $conversationId . ' shop_id=' . $shopId . ' body_len=' . strlen(trim($body)));

        $uid = (int)$_SESSION['user_id'];
        $shopModel = new Shop();
        $convModel = new Conversation();

        // Determine conversation
        if ($conversationId > 0) {
            if (!$convModel->userCanAccess($conversationId, $uid)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Accès refusé']);
                exit;
            }
        } else {
            if ($shopId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'shop_id invalide']);
                exit;
            }

            $shop = $shopModel->findById($shopId);
            if (!$shop) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Boutique introuvable']);
                exit;
            }

            // buyer is current user; vendor is shop.user_id
            $buyerUserId = $uid;
            $conversationId = $convModel->getOrCreate($shopId, $buyerUserId);
        }

        try {
            $db = getDB();
            $metaStmt = $db->prepare('SELECT c.shop_id, c.buyer_user_id, s.user_id AS shop_owner_id, s.name AS shop_name FROM conversations c INNER JOIN shops s ON s.id = c.shop_id WHERE c.id = ? LIMIT 1');
            $metaStmt->execute([$conversationId]);
            $meta = $metaStmt->fetch() ?: [];
            if (!$meta) {
                throw new Exception('Conversation introuvable.');
            }

            $conversationShopId = (int)($meta['shop_id'] ?? 0);
            $messageMeta = [];
            if ($productId > 0 && $conversationShopId > 0) {
                $productStmt = $db->prepare('SELECT id, shop_id, name, image FROM products WHERE id = ? LIMIT 1');
                $productStmt->execute([$productId]);
                $product = $productStmt->fetch() ?: null;
                if ($product && (int)($product['shop_id'] ?? 0) === $conversationShopId) {
                    $thumb = $this->resolveAssetUrl((string)($product['image'] ?? ''));
                    if ($thumb !== '') {
                        $messageMeta['product'] = [
                            'id' => (int)($product['id'] ?? 0),
                            'name' => (string)($product['name'] ?? ''),
                            'image' => $thumb,
                        ];
                    }
                }
            }

            $messageModel = new Message();
            $newId = $messageModel->send($conversationId, $uid, $body, $messageMeta);

            error_log('[chat.send] inserted message_id=' . $newId . ' conversation_id=' . $conversationId);

            // Notify shop and buyer
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
        } catch (Throwable $e) {
            error_log('[chat.send] error: ' . get_class($e) . ': ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }

        exit;
    }
}
