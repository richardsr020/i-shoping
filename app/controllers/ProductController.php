<?php
class ProductController {
    public function create() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'dashboard_shop';
            redirect('login');
        }

        redirect('dashboard_shop&tab=product_create');
    }

    public function detail() {
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/ProductImage.php';
        require_once __DIR__ . '/../models/ProductVariant.php';

        $productId = (int)($_GET['id'] ?? 0);
        if ($productId <= 0) {
            redirect('home');
        }

        $productModel = new Product();
        $imageModel = new ProductImage();
        $variantModel = new ProductVariant();

        $product = $productModel->findPublicById($productId);
        if (!$product) {
            http_response_code(404);
            $data = ['title' => 'Produit introuvable', 'product' => null];
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['view_data'] = $data;
            }
            return;
        }

        $images = $imageModel->getByProductId($productId);
        $variants = $variantModel->getByProductId($productId);

        $data = [
            'title' => $product['name'],
            'product' => $product,
            'images' => $images,
            'variants' => $variants
        ];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
    
    public function myProducts() {
        $data = ['title' => 'Mes produits'];
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
?>