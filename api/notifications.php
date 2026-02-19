<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../app/models/Notification.php';
require_once __DIR__ . '/../app/models/Shop.php';

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Connexion requise']);
    exit;
}

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
$userId = (int)($_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Session invalide']);
    exit;
}

function parseJsonBody(): array {
    $raw = file_get_contents('php://input');
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function normalizeScope(?string $scope): string {
    $scope = strtolower(trim((string)$scope));
    if (in_array($scope, ['shop', 'user', 'auto'], true)) {
        return $scope;
    }
    return 'auto';
}

function resolveContext(int $userId, string $requestedScope, int $requestedShopId = 0): array {
    $shopModel = new Shop();
    $scope = $requestedScope;

    $isOwnedShop = static function (Shop $model, int $shopId, int $uid): bool {
        if ($shopId <= 0) {
            return false;
        }
        return $model->findOwnedByUser($shopId, $uid) !== null;
    };

    $activeShopId = (int)($_SESSION['active_shop_id'] ?? 0);
    if ($requestedShopId > 0 && $isOwnedShop($shopModel, $requestedShopId, $userId)) {
        $_SESSION['active_shop_id'] = $requestedShopId;
        return ['scope' => 'shop', 'shop_id' => $requestedShopId];
    }

    if ($scope === 'shop') {
        if ($isOwnedShop($shopModel, $activeShopId, $userId)) {
            return ['scope' => 'shop', 'shop_id' => $activeShopId];
        }
        $shops = $shopModel->getByUserId($userId);
        if (!empty($shops)) {
            $firstShopId = (int)($shops[0]['id'] ?? 0);
            if ($firstShopId > 0) {
                $_SESSION['active_shop_id'] = $firstShopId;
                return ['scope' => 'shop', 'shop_id' => $firstShopId];
            }
        }
        return ['scope' => 'shop', 'shop_id' => 0];
    }

    if ($scope === 'user') {
        return ['scope' => 'user', 'shop_id' => 0];
    }

    // auto: shop si disponible, sinon user
    if ($isOwnedShop($shopModel, $activeShopId, $userId)) {
        return ['scope' => 'shop', 'shop_id' => $activeShopId];
    }

    $shops = $shopModel->getByUserId($userId);
    if (!empty($shops)) {
        $firstShopId = (int)($shops[0]['id'] ?? 0);
        if ($firstShopId > 0) {
            $_SESSION['active_shop_id'] = $firstShopId;
            return ['scope' => 'shop', 'shop_id' => $firstShopId];
        }
    }

    return ['scope' => 'user', 'shop_id' => 0];
}

function decodeNotificationRow(array $row): array {
    $dataJson = (string)($row['data_json'] ?? '');
    $data = null;
    if ($dataJson !== '') {
        $decoded = json_decode($dataJson, true);
        if (is_array($decoded)) {
            $data = $decoded;
        }
    }

    return [
        'id' => (int)($row['id'] ?? 0),
        'type' => (string)($row['type'] ?? ''),
        'title' => (string)($row['title'] ?? ''),
        'body' => isset($row['body']) ? (string)$row['body'] : '',
        'is_read' => (int)($row['is_read'] ?? 0) === 1,
        'created_at' => (string)($row['created_at'] ?? ''),
        'data' => $data,
    ];
}

function respondSnapshot(Notification $model, int $userId, string $scope, int $shopId, int $limit = 100): void {
    $limit = max(1, min(200, $limit));

    if ($scope === 'shop' && $shopId > 0) {
        $rows = $model->listForShop($shopId, $limit);
        $unreadTotal = $model->countUnreadForShop($shopId);
        $unreadOrders = $model->countUnreadOrderEventsForShop($shopId);
    } elseif ($scope === 'user') {
        $rows = $model->listForUser($userId, $limit);
        $unreadTotal = $model->countUnreadForUser($userId);
        $unreadOrders = $model->countUnreadOrderEventsForUser($userId);
    } else {
        $rows = [];
        $unreadTotal = 0;
        $unreadOrders = 0;
    }

    $notifications = array_map('decodeNotificationRow', $rows);
    $latestId = !empty($notifications) ? (int)($notifications[0]['id'] ?? 0) : 0;

    echo json_encode([
        'success' => true,
        'scope' => $scope,
        'shop_id' => $shopId,
        'latest_id' => $latestId,
        'counts' => [
            'unread_total' => $unreadTotal,
            'unread_orders' => $unreadOrders,
        ],
        'notifications' => $notifications,
    ]);
}

try {
    $payload = $method === 'POST' ? parseJsonBody() : [];
    $scope = normalizeScope((string)($payload['scope'] ?? ($_GET['scope'] ?? 'auto')));
    $requestedShopId = (int)($payload['shop_id'] ?? ($_GET['shop_id'] ?? 0));
    $ctx = resolveContext($userId, $scope, $requestedShopId);
    $notificationModel = new Notification();

    if ($method === 'GET') {
        $limit = (int)($_GET['limit'] ?? 100);
        respondSnapshot($notificationModel, $userId, (string)$ctx['scope'], (int)$ctx['shop_id'], $limit);
        exit;
    }

    if ($method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        exit;
    }

    $action = strtolower(trim((string)($payload['action'] ?? '')));
    $ctxScope = (string)$ctx['scope'];
    $ctxShopId = (int)$ctx['shop_id'];

    if ($action === 'mark_all_read') {
        if ($ctxScope === 'shop' && $ctxShopId > 0) {
            $notificationModel->markAllReadForShop($ctxShopId);
        } elseif ($ctxScope === 'user') {
            $notificationModel->markAllReadForUser($userId);
        }
    } elseif ($action === 'mark_read') {
        $ids = $payload['ids'] ?? [];
        if (!is_array($ids)) {
            $ids = [];
        }
        if ($ctxScope === 'shop' && $ctxShopId > 0) {
            $notificationModel->markReadByIdsForShop($ctxShopId, $ids);
        } elseif ($ctxScope === 'user') {
            $notificationModel->markReadByIdsForUser($userId, $ids);
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Action invalide']);
        exit;
    }

    $limit = (int)($payload['limit'] ?? 100);
    respondSnapshot($notificationModel, $userId, $ctxScope, $ctxShopId, $limit);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
