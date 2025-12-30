<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Product.php';

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

$productId = (int)($payload['product_id'] ?? 0);
$quantity = (int)($payload['quantity'] ?? 1);
$color = isset($payload['color']) ? (string)$payload['color'] : null;
$size = isset($payload['size']) ? (string)$payload['size'] : null;
$paymentMethod = isset($payload['payment_method']) ? (string)$payload['payment_method'] : 'cash';

if ($productId <= 0 || $quantity <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Paramètres invalides']);
    exit;
}

try {
    $productModel = new Product();
    $product = $productModel->findPublicById($productId);
    if (!$product) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Produit introuvable']);
        exit;
    }

    $shopId = (int)($product['shop_id'] ?? 0);
    if ($shopId <= 0) {
        throw new Exception('Boutique introuvable.');
    }

    $order = new Order();
    $orderId = $order->createWithItems((int)$_SESSION['user_id'], $shopId, [
        [
            'product_id' => $productId,
            'quantity' => $quantity,
            'color' => $color,
            'size' => $size
        ]
    ], $paymentMethod);

    echo json_encode(['success' => true, 'order_id' => $orderId]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
