<?php
require_once __DIR__ . '/../models/Shop.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ProductImage.php';
require_once __DIR__ . '/../models/ProductVariant.php';
require_once __DIR__ . '/../models/Order.php';

class ShopController {
    public function create() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'dashboard_shop';
            redirect('login');
        }

        $shopModel = new Shop();
        if ($shopModel->countByUserId((int)$_SESSION['user_id']) >= 1) {
            redirect('dashboard_shop&tab=shops');
        }

        redirect('dashboard_shop&tab=shop_create');
    }
    
    public function dashboard() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'dashboard_shop';
            redirect('login');
        }

        $shopModel = new Shop();
        $productModel = new Product();
        $orderModel = new Order();
        $userModel = new User();
        $productImageModel = new ProductImage();
        $productVariantModel = new ProductVariant();

        $tab = $_GET['tab'] ?? 'overview';
        $action = $_GET['action'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if ($action === 'create_shop') {
                    if ($shopModel->countByUserId((int)$_SESSION['user_id']) >= 1) {
                        throw new Exception('Pour le moment, un compte ne peut créer qu\'une seule boutique.');
                    }

                    $logoPath = null;
                    if (isset($_FILES['logo_file'])) {
                        $logoPath = saveUploadedImage($_FILES['logo_file'], 'shops');
                    }
                    $bannerPath = null;
                    if (isset($_FILES['banner_file'])) {
                        $bannerPath = saveUploadedImage($_FILES['banner_file'], 'shops');
                    }

                    $shopId = $shopModel->create((int)$_SESSION['user_id'], [
                        'name' => $_POST['name'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'logo' => $logoPath ?? '',
                        'banner' => $bannerPath ?? '',
                        'email_contact' => $_POST['email_contact'] ?? '',
                        'phone' => $_POST['phone'] ?? '',
                        'address' => $_POST['address'] ?? '',
                        'city' => $_POST['city'] ?? '',
                        'country' => $_POST['country'] ?? '',
                        'currency' => $_POST['currency'] ?? 'USD',
                        'status' => $_POST['status'] ?? 'active'
                    ]);

                    // Règle: si un customer crée une boutique, il devient vendor
                    $userId = (int)$_SESSION['user_id'];
                    if ($userModel->hasRole($userId, 'customer')) {
                        $userModel->addRoleIfMissing($userId, 'vendor');
                    }

                    $_SESSION['active_shop_id'] = $shopId;
                    redirect('dashboard_shop&tab=shops');
                }

                if ($action === 'set_active_shop') {
                    $shopId = (int)($_POST['shop_id'] ?? 0);
                    $shop = $shopModel->findOwnedByUser($shopId, (int)$_SESSION['user_id']);
                    if (!$shop) {
                        throw new Exception('Boutique introuvable.');
                    }
                    $_SESSION['active_shop_id'] = $shopId;
                    redirect('dashboard_shop&tab=products');
                }

                if ($action === 'delete_shop') {
                    $shopId = (int)($_POST['shop_id'] ?? 0);
                    $shopModel->delete($shopId, (int)$_SESSION['user_id']);
                    if (isset($_SESSION['active_shop_id']) && (int)$_SESSION['active_shop_id'] === $shopId) {
                        unset($_SESSION['active_shop_id']);
                    }
                    redirect('dashboard_shop&tab=shops');
                }

                if ($action === 'create_product') {
                    $shopId = (int)($_SESSION['active_shop_id'] ?? 0);
                    if ($shopId <= 0) {
                        throw new Exception('Veuillez sélectionner une boutique.');
                    }

                    $mainImagePath = null;
                    if (isset($_FILES['image_file'])) {
                        $mainImagePath = saveUploadedImage($_FILES['image_file'], 'products');
                    }

                    $productId = $productModel->create($shopId, (int)$_SESSION['user_id'], [
                        'name' => $_POST['name'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'price' => $_POST['price'] ?? 0,
                        'promo_price' => $_POST['promo_price'] ?? null,
                        'category' => $_POST['category'] ?? '',
                        'brand' => $_POST['brand'] ?? '',
                        'size' => $_POST['size'] ?? '',
                        'sku' => $_POST['sku'] ?? '',
                        'stock' => $_POST['stock'] ?? 0,
                        'status' => $_POST['status'] ?? 'active',
                        'image' => $mainImagePath ?? ''
                    ]);

                    if (isset($_FILES['extra_images_files']) && isset($_FILES['extra_images_files']['name']) && is_array($_FILES['extra_images_files']['name'])) {
                        $count = count($_FILES['extra_images_files']['name']);
                        $paths = [];
                        for ($i = 0; $i < $count; $i++) {
                            $file = [
                                'name' => $_FILES['extra_images_files']['name'][$i] ?? null,
                                'type' => $_FILES['extra_images_files']['type'][$i] ?? null,
                                'tmp_name' => $_FILES['extra_images_files']['tmp_name'][$i] ?? null,
                                'error' => $_FILES['extra_images_files']['error'][$i] ?? null,
                                'size' => $_FILES['extra_images_files']['size'][$i] ?? null,
                            ];
                            $p = saveUploadedImage($file, 'products');
                            if ($p) {
                                $paths[] = $p;
                            }
                        }
                        if (!empty($paths)) {
                            $productImageModel->addImages($productId, $paths);
                        }
                    }

                    $variantNames = $_POST['variant_name'] ?? [];
                    $variantColors = $_POST['variant_color'] ?? [];
                    $variantAdditional = $_POST['variant_additional_price'] ?? [];
                    $variantStocks = $_POST['variant_stock'] ?? [];

                    if (is_array($variantNames) || is_array($variantColors) || is_array($variantAdditional) || is_array($variantStocks)) {
                        $max = max(
                            is_array($variantNames) ? count($variantNames) : 0,
                            is_array($variantColors) ? count($variantColors) : 0,
                            is_array($variantAdditional) ? count($variantAdditional) : 0,
                            is_array($variantStocks) ? count($variantStocks) : 0
                        );

                        $variants = [];
                        for ($i = 0; $i < $max; $i++) {
                            $vn = isset($variantNames[$i]) ? trim((string)$variantNames[$i]) : '';
                            $vc = isset($variantColors[$i]) ? trim((string)$variantColors[$i]) : '';
                            $vap = $variantAdditional[$i] ?? 0;
                            $vs = $variantStocks[$i] ?? 0;

                            if ($vn === '' && $vc === '' && ($vap === '' || $vap === null) && ($vs === '' || $vs === null)) {
                                continue;
                            }

                            if ($vc !== '' && !preg_match('/^#[0-9a-fA-F]{6}$/', $vc)) {
                                $vc = '';
                            }

                            $variants[] = [
                                'variant_name' => $vn !== '' ? $vn : null,
                                'color_hex' => $vc !== '' ? $vc : null,
                                'additional_price' => $vap,
                                'stock' => $vs
                            ];
                        }

                        if (!empty($variants)) {
                            $productVariantModel->addVariants($productId, $variants);
                        }
                    }

                    redirect('dashboard_shop&tab=products');
                }

                if ($action === 'delete_product') {
                    $productId = (int)($_POST['product_id'] ?? 0);
                    $productModel->delete($productId, (int)$_SESSION['user_id']);
                    redirect('dashboard_shop&tab=products');
                }

                if ($action === 'update_shop') {
                    $shopId = (int)($_SESSION['active_shop_id'] ?? 0);
                    if ($shopId <= 0) {
                        throw new Exception('Veuillez sélectionner une boutique.');
                    }

                    $payload = [
                        'name' => $_POST['name'] ?? '',
                        'description' => $_POST['description'] ?? '',
                        'email_contact' => $_POST['email_contact'] ?? '',
                        'phone' => $_POST['phone'] ?? '',
                        'address' => $_POST['address'] ?? '',
                        'city' => $_POST['city'] ?? '',
                        'country' => $_POST['country'] ?? '',
                        'currency' => $_POST['currency'] ?? 'USD',
                        'status' => $_POST['status'] ?? 'active'
                    ];

                    if (isset($_FILES['logo_file']) && ($_FILES['logo_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                        $payload['logo'] = saveUploadedImage($_FILES['logo_file'], 'shops') ?? '';
                    }
                    if (isset($_FILES['banner_file']) && ($_FILES['banner_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                        $payload['banner'] = saveUploadedImage($_FILES['banner_file'], 'shops') ?? '';
                    }

                    $shopModel->update($shopId, (int)$_SESSION['user_id'], $payload);
                    redirect('dashboard_shop&tab=settings');
                }

                if ($action === 'mark_order_paid') {
                    $shopId = (int)($_SESSION['active_shop_id'] ?? 0);
                    if ($shopId <= 0) {
                        throw new Exception('Veuillez sélectionner une boutique.');
                    }
                    $shop = $shopModel->findOwnedByUser($shopId, (int)$_SESSION['user_id']);
                    if (!$shop) {
                        throw new Exception('Boutique introuvable.');
                    }

                    $orderId = (int)($_POST['order_id'] ?? 0);
                    if ($orderId <= 0) {
                        throw new Exception('Commande introuvable.');
                    }

                    $orderModel->markPaidByVendor($orderId, $shopId);
                    redirect('dashboard_shop&tab=orders');
                }
            } catch (Exception $e) {
                $_SESSION['dashboard_error'] = $e->getMessage();
                redirect('dashboard_shop&tab=' . urlencode($tab));
            }
        }

        $shops = $shopModel->getByUserId((int)$_SESSION['user_id']);

        if (!isset($_SESSION['active_shop_id']) && !empty($shops)) {
            $_SESSION['active_shop_id'] = (int)$shops[0]['id'];
        }

        $activeShopId = (int)($_SESSION['active_shop_id'] ?? 0);
        $activeShop = $activeShopId > 0 ? $shopModel->findOwnedByUser($activeShopId, (int)$_SESSION['user_id']) : null;
        if (!$activeShop) {
            $activeShopId = 0;
            unset($_SESSION['active_shop_id']);
        }

        $products = $activeShopId > 0 ? $productModel->getByShopId($activeShopId) : [];
        $orders = $activeShopId > 0 ? $orderModel->getByShopId($activeShopId) : [];

        $currentUser = getCurrentUser();

        $data = [
            'title' => 'Tableau de bord boutique',
            'tab' => $tab,
            'shops' => $shops,
            'active_shop_id' => $activeShopId,
            'active_shop' => $activeShop,
            'products' => $products,
            'orders' => $orders,
            'current_user' => $currentUser,
            'error' => $_SESSION['dashboard_error'] ?? null
        ];

        unset($_SESSION['dashboard_error']);
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function profile() {
        $shopId = (int)($_GET['id'] ?? 0);
        if ($shopId <= 0) {
            redirect('home');
        }

        $shopModel = new Shop();
        $productModel = new Product();

        $shop = $shopModel->findById($shopId);
        if (!$shop) {
            http_response_code(404);
            $data = ['title' => 'Boutique introuvable', 'shop' => null, 'products' => []];
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['view_data'] = $data;
            }
            return;
        }

        $products = $productModel->getActiveByShopIdPublic($shopId);
        $data = ['title' => (string)$shop['name'], 'shop' => $shop, 'products' => $products];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>