<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Notification.php';

class AdminController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    private function requireAdminAccess(): void {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'dashboard_admin';
            redirect('login');
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $isAdmin = $this->userModel->hasRole($userId, 'super_admin') || $this->userModel->hasRole($userId, 'admin');
        if (!$isAdmin) {
            redirect('home');
        }
    }

    private function currentUserId(): int {
        return (int)($_SESSION['user_id'] ?? 0);
    }

    private function isSuperAdmin(int $userId): bool {
        return $this->userModel->hasRole($userId, 'super_admin');
    }

    private function isAdmin(int $userId): bool {
        return $this->userModel->hasRole($userId, 'admin');
    }

    private function requireSuperAdminAccess(): void {
        $this->requireAdminAccess();
        $userId = $this->currentUserId();
        if (!$this->isSuperAdmin($userId)) {
            redirect('dashboard_admin');
        }
    }

    private function canManageTargetUser(int $actorId, int $targetUserId): bool {
        if ($this->isSuperAdmin($actorId)) {
            return true;
        }
        if ($this->isAdmin($actorId)) {
            return !($this->isSuperAdmin($targetUserId) || $this->isAdmin($targetUserId));
        }
        return false;
    }

    public function index(): void {
        $this->requireAdminAccess();

        $tab = (string)($_GET['tab'] ?? 'overview');
        $allowedTabs = ['overview', 'notifications', 'users', 'shops', 'products', 'orders'];
        if (!in_array($tab, $allowedTabs, true)) {
            $tab = 'overview';
        }

        $db = getDB();

        $usersCount = (int)($db->query('SELECT COUNT(*) FROM users')->fetchColumn() ?: 0);
        $shopsCount = (int)($db->query('SELECT COUNT(*) FROM shops')->fetchColumn() ?: 0);
        $ordersCount = (int)($db->query('SELECT COUNT(*) FROM orders')->fetchColumn() ?: 0);

        $revenueStmt = $db->query('SELECT COALESCE(SUM(total), 0) FROM orders WHERE canceled = 0');
        $revenue = (float)($revenueStmt->fetchColumn() ?: 0);

        $currentUser = getCurrentUser();

        $unreadNotificationsCount = (int)($db->query('SELECT COUNT(*) FROM notifications WHERE COALESCE(is_read, 0) = 0')->fetchColumn() ?: 0);

        $recentNotificationsStmt = $db->query('
            SELECT
                n.*,
                u.email AS user_email,
                s.name AS shop_name
            FROM notifications n
            LEFT JOIN users u ON u.id = n.user_id
            LEFT JOIN shops s ON s.id = n.shop_id
            ORDER BY n.id DESC
            LIMIT 5
        ');
        $recentNotifications = $recentNotificationsStmt->fetchAll();

        $salesByDayStmt = $db->query("
            SELECT
                DATE(created_at) AS day,
                COALESCE(SUM(total), 0) AS total
            FROM orders
            WHERE COALESCE(canceled, 0) = 0
              AND COALESCE(paid, 0) = 1
              AND created_at >= DATETIME('now', '-30 days')
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ");
        $salesByDay = $salesByDayStmt->fetchAll();

        $shopsStmt = $db->query('
            SELECT
                s.*,
                u.first_name AS owner_first_name,
                u.last_name AS owner_last_name,
                u.email AS owner_email,
                (
                    SELECT COALESCE(SUM(o.total), 0)
                    FROM orders o
                    WHERE o.shop_id = s.id AND COALESCE(o.canceled, 0) = 0
                ) AS revenue_total
            FROM shops s
            INNER JOIN users u ON u.id = s.user_id
            ORDER BY s.created_at DESC
            LIMIT 10
        ');
        $shops = $shopsStmt->fetchAll();

        if ($tab === 'shops') {
            $shopsStmt = $db->query('
                SELECT
                    s.*,
                    u.first_name AS owner_first_name,
                    u.last_name AS owner_last_name,
                    u.email AS owner_email,
                    (
                        SELECT COALESCE(SUM(o.total), 0)
                        FROM orders o
                        WHERE o.shop_id = s.id AND COALESCE(o.canceled, 0) = 0
                    ) AS revenue_total
                FROM shops s
                INNER JOIN users u ON u.id = s.user_id
                ORDER BY s.created_at DESC
                LIMIT 200
            ');
            $shops = $shopsStmt->fetchAll();
        }

        $users = [];
        if ($tab === 'users') {
            $usersStmt = $db->query('
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email,
                    u.status,
                    u.suspended_until,
                    u.created_at,
                    COALESCE(GROUP_CONCAT(r.name, ", "), "") AS roles
                FROM users u
                LEFT JOIN user_roles ur ON ur.user_id = u.id
                LEFT JOIN roles r ON r.id = ur.role_id
                GROUP BY u.id
                ORDER BY u.created_at DESC
                LIMIT 200
            ');
            $users = $usersStmt->fetchAll();
        }

        $notifications = [];
        if ($tab === 'notifications') {
            $stmt = $db->query('
                SELECT
                    n.*,
                    u.email AS user_email,
                    s.name AS shop_name
                FROM notifications n
                LEFT JOIN users u ON u.id = n.user_id
                LEFT JOIN shops s ON s.id = n.shop_id
                ORDER BY n.id DESC
                LIMIT 200
            ');
            $notifications = $stmt->fetchAll();
        }

        $products = [];
        if ($tab === 'products') {
            $productsStmt = $db->query('
                SELECT
                    p.*,
                    s.name AS shop_name
                FROM products p
                INNER JOIN shops s ON s.id = p.shop_id
                ORDER BY p.created_at DESC
                LIMIT 200
            ');
            $products = $productsStmt->fetchAll();
        }

        $orders = [];
        if ($tab === 'orders') {
            $ordersStmt = $db->query('
                SELECT
                    o.*,
                    s.name AS shop_name,
                    u.email AS customer_email,
                    TRIM(COALESCE(u.first_name, \'\') || \' \' || COALESCE(u.last_name, \'\')) AS customer_name
                FROM orders o
                LEFT JOIN shops s ON s.id = o.shop_id
                LEFT JOIN users u ON u.id = o.user_id
                ORDER BY o.created_at DESC
                LIMIT 200
            ');
            $orders = $ordersStmt->fetchAll();
        }

        $data = [
            'title' => 'Administration - ' . APP_NAME,
            'tab' => $tab,
            'stats' => [
                'shops' => $shopsCount,
                'users' => $usersCount,
                'orders' => $ordersCount,
                'revenue' => $revenue
            ],
            'shops' => $shops
            ,
            'users' => $users,
            'notifications' => $notifications,
            'products' => $products,
            'orders' => $orders,
            'is_super_admin' => $this->isSuperAdmin($this->currentUserId()),
            'current_user' => $currentUser,
            'unread_notifications_count' => $unreadNotificationsCount,
            'recent_notifications' => $recentNotifications,
            'sales_by_day' => $salesByDay
        ];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }

    public function suspendUser(): void {
        $this->requireAdminAccess();

        $actorId = $this->currentUserId();
        $targetId = (int)($_POST['user_id'] ?? 0);
        $days = trim((string)($_POST['days'] ?? ''));

        if ($targetId <= 0 || $targetId === $actorId || !$this->canManageTargetUser($actorId, $targetId)) {
            redirect('dashboard_admin&tab=users');
        }

        $until = null;
        if ($days !== '' && $days !== 'undefined') {
            $d = (int)$days;
            if ($d > 0) {
                $until = date('Y-m-d H:i:s', time() + ($d * 86400));
            }
        }

        $db = getDB();
        $db->prepare('UPDATE users SET status = "suspended", suspended_until = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$until, $targetId]);

        redirect('dashboard_admin&tab=users');
    }

    public function unsuspendUser(): void {
        $this->requireAdminAccess();

        $actorId = $this->currentUserId();
        $targetId = (int)($_POST['user_id'] ?? 0);
        if ($targetId <= 0 || !$this->canManageTargetUser($actorId, $targetId)) {
            redirect('dashboard_admin&tab=users');
        }

        $db = getDB();
        $db->prepare('UPDATE users SET status = "active", suspended_until = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$targetId]);

        redirect('dashboard_admin&tab=users');
    }

    public function deleteUser(): void {
        $this->requireAdminAccess();

        $actorId = $this->currentUserId();
        $targetId = (int)($_POST['user_id'] ?? 0);
        if ($targetId <= 0 || $targetId === $actorId || !$this->canManageTargetUser($actorId, $targetId)) {
            redirect('dashboard_admin&tab=users');
        }

        // Orders keep history (user_id becomes NULL via FK). Shops/products/etc cascade via FKs.
        $db = getDB();
        $db->prepare('DELETE FROM users WHERE id = ?')->execute([$targetId]);

        redirect('dashboard_admin&tab=users');
    }

    public function suspendShop(): void {
        $this->requireAdminAccess();

        $shopId = (int)($_POST['shop_id'] ?? 0);
        $days = trim((string)($_POST['days'] ?? ''));
        if ($shopId <= 0) {
            redirect('dashboard_admin&tab=shops');
        }

        $until = null;
        if ($days !== '' && $days !== 'undefined') {
            $d = (int)$days;
            if ($d > 0) {
                $until = date('Y-m-d H:i:s', time() + ($d * 86400));
            }
        }

        getDB()->prepare('UPDATE shops SET status = "suspended", suspended_until = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$until, $shopId]);

        redirect('dashboard_admin&tab=shops');
    }

    public function unsuspendShop(): void {
        $this->requireAdminAccess();

        $shopId = (int)($_POST['shop_id'] ?? 0);
        if ($shopId <= 0) {
            redirect('dashboard_admin&tab=shops');
        }

        getDB()->prepare('UPDATE shops SET status = "active", suspended_until = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?')
            ->execute([$shopId]);

        redirect('dashboard_admin&tab=shops');
    }

    public function deleteShop(): void {
        $this->requireAdminAccess();

        $shopId = (int)($_POST['shop_id'] ?? 0);
        if ($shopId <= 0) {
            redirect('dashboard_admin&tab=shops');
        }

        $db = getDB();
        $db->prepare('DELETE FROM shops WHERE id = ?')->execute([$shopId]);

        redirect('dashboard_admin&tab=shops');
    }

    public function createUser(): void {
        $this->requireSuperAdminAccess();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');
        $firstName = trim((string)($_POST['first_name'] ?? ''));
        $lastName = trim((string)($_POST['last_name'] ?? ''));

        try {
            $userId = (int)$this->userModel->create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password' => $password,
            ]);

            $role = trim((string)($_POST['role'] ?? 'admin'));
            if ($role === 'super_admin') {
                $this->userModel->setRoles($userId, ['super_admin']);
            } else {
                $this->userModel->setRoles($userId, ['admin']);
            }
        } catch (Exception $e) {
            // best-effort: stay safe and redirect
        }

        redirect('dashboard_admin&tab=users');
    }

    public function setUserRole(): void {
        $this->requireSuperAdminAccess();

        $actorId = $this->currentUserId();
        $targetId = (int)($_POST['user_id'] ?? 0);
        $role = trim((string)($_POST['role'] ?? ''));

        if ($targetId <= 0 || $targetId === $actorId) {
            redirect('dashboard_admin&tab=users');
        }

        if (!$this->canManageTargetUser($actorId, $targetId)) {
            redirect('dashboard_admin&tab=users');
        }

        if (!in_array($role, ['admin', 'super_admin', 'customer', 'vendor'], true)) {
            redirect('dashboard_admin&tab=users');
        }

        try {
            $this->userModel->setRoles($targetId, [$role]);
        } catch (Exception $e) {
        }

        redirect('dashboard_admin&tab=users');
    }

    public function productActivate(): void {
        $this->requireAdminAccess();
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            redirect('dashboard_admin&tab=products');
        }

        $db = getDB();
        $db->prepare('UPDATE products SET status = "active", updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$productId]);
        redirect('dashboard_admin&tab=products');
    }

    public function productDeactivate(): void {
        $this->requireAdminAccess();
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            redirect('dashboard_admin&tab=products');
        }

        $db = getDB();
        $db->prepare('UPDATE products SET status = "inactive", updated_at = CURRENT_TIMESTAMP WHERE id = ?')->execute([$productId]);
        redirect('dashboard_admin&tab=products');
    }

    public function productDelete(): void {
        $this->requireAdminAccess();
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            redirect('dashboard_admin&tab=products');
        }

        $db = getDB();
        $db->prepare('DELETE FROM products WHERE id = ?')->execute([$productId]);
        redirect('dashboard_admin&tab=products');
    }
}
