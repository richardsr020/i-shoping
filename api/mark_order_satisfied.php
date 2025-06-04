<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Notification.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Connexion requise']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Payload invalide']);
    exit;
}

$orderId = (int)($payload['order_id'] ?? 0);
if ($orderId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'order_id invalide']);
    exit;
}

try {
    $order = new Order();
    $ok = $order->markSatisfiedByCustomer($orderId, (int)$_SESSION['user_id']);

    if ($ok) {
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT shop_id FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
            $stmt->execute([$orderId, (int)$_SESSION['user_id']]);
            $shopId = (int)($stmt->fetchColumn() ?: 0);
            if ($shopId > 0) {
                $notif = new Notification();
                $notif->create(null, $shopId, 'order_satisfied', 'Commande satisfaite', 'Un client a marqué une commande comme satisfaite.', [
                    'order_id' => $orderId,
                    'buyer_user_id' => (int)$_SESSION['user_id'],
                ]);
            }
        } catch (Exception $e) {
            // best-effort
        }
    }
    echo json_encode(['success' => (bool)$ok]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
