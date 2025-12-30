<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/models/Order.php';

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
    echo json_encode(['success' => (bool)$ok]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
