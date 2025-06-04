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
$shopId = (int)($payload['shop_id'] ?? 0);
if ($orderId <= 0 || $shopId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
    exit;
}

try {
    $order = new Order();
    $ok = $order->markPaidByVendor($orderId, $shopId);

    if ($ok) {
        try {
            $db = getDB();
            $stmt = $db->prepare('SELECT user_id FROM orders WHERE id = ? AND shop_id = ? LIMIT 1');
            $stmt->execute([$orderId, $shopId]);
            $buyerUserId = (int)($stmt->fetchColumn() ?: 0);
            if ($buyerUserId > 0) {
                $notif = new Notification();
                $notif->create($buyerUserId, null, 'order_paid', 'Commande payée', 'Votre commande a été marquée comme payée par le vendeur.', [
                    'order_id' => $orderId,
                    'shop_id' => $shopId,
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
