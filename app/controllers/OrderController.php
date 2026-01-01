<?php
require_once __DIR__ . '/../models/Order.php';

class OrderController {
    public function index() {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'orders';
            redirect('login');
        }

        $orderModel = new Order();
        $orders = $orderModel->getByUserId((int)$_SESSION['user_id']);

        $pendingTotal = 0.0;
        foreach ($orders as $o) {
            $st = (string)($o['status'] ?? 'pending');
            if ($st === 'pending') {
                $pendingTotal += (float)($o['total'] ?? 0);
            }
        }

        $data = [
            'title' => 'Mes commandes',
            'orders' => $orders,
            'pending_total' => $pendingTotal,
        ];

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['view_data'] = $data;
        }
    }
}
