<?php
require_once __DIR__ . '/../config.php';

$data = $_SESSION['view_data'] ?? [];
$stats = $data['stats'] ?? [];
$shops = $data['shops'] ?? [];
$users = $data['users'] ?? [];
$notifications = $data['notifications'] ?? [];
$products = $data['products'] ?? [];
$orders = $data['orders'] ?? [];
$isSuperAdmin = (bool)($data['is_super_admin'] ?? false);
$currentUser = $data['current_user'] ?? null;
$unreadNotificationsCount = (int)($data['unread_notifications_count'] ?? 0);
$recentNotifications = $data['recent_notifications'] ?? [];
$salesByDay = $data['sales_by_day'] ?? [];
$tab = (string)($data['tab'] ?? 'overview');

$shopsCount = (int)($stats['shops'] ?? 0);
$usersCount = (int)($stats['users'] ?? 0);
$ordersCount = (int)($stats['orders'] ?? 0);
$revenue = (float)($stats['revenue'] ?? 0);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string)($data['title'] ?? 'Administration - iShopping')); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        :root {
            --primary: #ff4500;
            --primary-dark: #e03d00;
            --secondary: #1e90ff;
            --dark: #000;
            --light: #fff;
            --gray: #f8f9fa;
            --gray-dark: #6c757d;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            display: flex;
            min-height: 100vh;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 14px;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-secondary {
            background-color: var(--secondary);
            color: white;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 280px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .logo i {
            margin-right: 10px;
            color: var(--primary);
            font-size: 24px;
        }

        .logo h1 {
            font-size: 20px;
        }

        .admin-info {
            padding: 0 20px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }

        .admin-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .admin-details h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }

        .admin-details p {
            font-size: 12px;
            color: #ccc;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 5px;
        }

        .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ccc;
            transition: all 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            background-color: #333;
            color: white;
            border-left: 4px solid var(--primary);
        }

        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .nav-section {
            padding: 0 20px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 12px;
            color: #777;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === CONTENU PRINCIPAL === */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .top-header {
            background-color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            margin: 0 20px;
        }

        .search-bar input {
            width: 100%;
            padding: 10px 15px;
            border-radius: 20px;
            border: 1px solid #ddd;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-action-item {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .header-action-item i {
            font-size: 18px;
            color: var(--gray-dark);
        }

        .notification-badge {
            background-color: var(--primary);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .notification-wrapper {
            position: relative;
        }

        /* Contenu du tableau de bord */
        .dashboard-content {
            padding: 30px;
            flex: 1;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), #ff6b35);
            color: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-text h2 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-info h3 {
            font-size: 14px;
            color: var(--gray-dark);
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: var(--color-text, var(--dark));
        }

        [data-theme="dark"] .stat-value {
            color: #fff;
        }

        .stat-change {
            font-size: 12px;
            color: var(--success);
            display: flex;
            align-items: center;
        }

        .stat-change.down {
            color: var(--danger);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.shops {
            background-color: rgba(30, 144, 255, 0.1);
            color: var(--secondary);
        }

        .stat-icon.users {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .stat-icon.orders {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .stat-icon.revenue {
            background-color: rgba(255, 69, 0, 0.1);
            color: var(--primary);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 18px;
        }

        .chart-placeholder {
            height: 300px;
            background-color: #f8f9fa;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-dark);
        }

        .recent-activities {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .activities-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .activities-header h3 {
            font-size: 18px;
        }

        .activities-list {
            list-style: none;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(30, 144, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            margin-right: 15px;
            flex-shrink: 0;
        }

        .activity-details {
            flex: 1;
        }

        .activity-text {
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: var(--gray-dark);
        }

        /* Gestion des boutiques */
        .shops-section {
            margin-top: 30px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-header h2 {
            font-size: 22px;
        }

        .shops-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            background-color: #f8f9fa;
            font-weight: 500;
            color: var(--gray-dark);
            border-bottom: 1px solid #eee;
        }

        .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .users-table .table-header,
        .users-table .table-row {
            grid-template-columns: 0.6fr 1fr 1.2fr 1.2fr 1fr 1.2fr 2fr;
        }

        .table-row:last-child {
            border-bottom: none;
        }

        .shop-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .shop-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
        }

        .shop-details h4 {
            margin-bottom: 5px;
        }

        .shop-details p {
            font-size: 12px;
            color: var(--gray-dark);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-badge.active {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .status-badge.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .status-badge.suspended {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            background: none;
            border: none;
            color: var(--gray-dark);
            cursor: pointer;
            font-size: 16px;
        }

        .action-btn:hover {
            color: var(--primary);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .action-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(255, 69, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 15px;
        }

        .action-card h4 {
            margin-bottom: 10px;
        }

        .action-card p {
            font-size: 14px;
            color: var(--gray-dark);
            margin-bottom: 15px;
        }

        /* === RESPONSIVE === */
        @media (max-width: 1200px) {
            .stats-cards, .quick-actions {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                padding: 10px 0;
            }
            
            .nav-links {
                display: flex;
                overflow-x: auto;
            }
            
            .nav-links li {
                flex: 0 0 auto;
                margin-bottom: 0;
            }
            
            .nav-links a {
                padding: 10px 15px;
                border-left: none;
                border-bottom: 4px solid transparent;
            }
            
            .nav-links a:hover, .nav-links a.active {
                border-left: none;
                border-bottom: 4px solid var(--primary);
            }
            
            .stats-cards, .quick-actions {
                grid-template-columns: 1fr;
            }
            
            .top-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .search-bar {
                max-width: 100%;
                margin: 0;
            }
            
            .table-header, .table-row {
                grid-template-columns: 1fr;
                gap: 10px;
            }
            
            .table-header {
                display: none;
            }
            
            .table-row {
                border: 1px solid #eee;
                border-radius: 8px;
                margin-bottom: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- === SIDEBAR === -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-shopping-bag"></i>
            <h1>iShopping Admin</h1>
        </div>
        
        <div class="admin-info">
            <div class="admin-avatar">
                <?php
                $initials = 'AD';
                if (is_array($currentUser)) {
                    $fn = trim((string)($currentUser['first_name'] ?? ''));
                    $ln = trim((string)($currentUser['last_name'] ?? ''));
                    $initials = strtoupper(substr($fn, 0, 1) . substr($ln, 0, 1));
                    $initials = $initials !== '' ? $initials : 'AD';
                }
                echo htmlspecialchars($initials);
                ?>
            </div>
            <div class="admin-details">
                <h3>
                    <?php
                    $name = 'Admin';
                    if (is_array($currentUser)) {
                        $name = trim((string)($currentUser['first_name'] ?? '') . ' ' . (string)($currentUser['last_name'] ?? ''));
                        $name = $name !== '' ? $name : (string)($currentUser['email'] ?? 'Admin');
                    }
                    echo htmlspecialchars($name);
                    ?>
                </h3>
                <p><?php echo is_array($currentUser) ? htmlspecialchars((string)($currentUser['email'] ?? '')) : ''; ?></p>
            </div>
        </div>
        
        <div class="nav-section">Tableau de bord</div>
        <ul class="nav-links">
            <li><a href="<?php echo url('home'); ?>"><i class="fas fa-arrow-left"></i> Accueil</a></li>
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=overview" class="<?php echo $tab === 'overview' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Vue d'ensemble</a></li>
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=notifications" class="<?php echo $tab === 'notifications' ? 'active' : ''; ?>"><i class="fas fa-bell"></i> Notifications</a></li>
        </ul>
        
        <div class="nav-section">Gestion</div>
        <ul class="nav-links">
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=shops" class="<?php echo $tab === 'shops' ? 'active' : ''; ?>"><i class="fas fa-store"></i> Boutiques</a></li>
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=users" class="<?php echo $tab === 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=orders" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
            <li><a href="<?php echo url('dashboard_admin'); ?>&tab=products" class="<?php echo $tab === 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Produits</a></li>
            <li><a href="#"><i class="fas fa-tags"></i> Catégories</a></li>
        </ul>
        
        <div class="nav-section">Système</div>
        <ul class="nav-links">
            <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
            <li><a href="#"><i class="fas fa-shield-alt"></i> Sécurité</a></li>
            <li><a href="#"><i class="fas fa-file-invoice"></i> Rapports</a></li>
            <li><a href="#"><i class="fas fa-question-circle"></i> Support</a></li>
        </ul>
    </div>

    <!-- === CONTENU PRINCIPAL === -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="search-bar">
                <input type="text" placeholder="Rechercher...">
            </div>
            <div class="header-actions">
                <div class="header-action-item">
                    <div class="notification-wrapper">
                        <i class="far fa-bell"></i>
                        <?php if ($unreadNotificationsCount > 0): ?>
                            <div class="notification-badge"><?php echo (int)$unreadNotificationsCount; ?></div>
                        <?php endif; ?>
                    </div>
                    <span>Notifications</span>
                </div>
            </div>
        </header>

        <!-- Contenu du tableau de bord -->
        <div class="dashboard-content">
            <?php if ($tab === 'notifications'): ?>
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Notifications</h2>
                        <p>Journal des événements (commandes, messages, actions admin)</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=overview">Retour</a>
                </div>

                <div class="shops-section">
                    <div class="section-header">
                        <h2>Événements récents</h2>
                    </div>
                    <div class="shops-table">
                        <div class="table-header">
                            <div>Date</div>
                            <div>Type</div>
                            <div>Détails</div>
                        </div>
                        <?php if (empty($notifications)): ?>
                            <div class="table-row">
                                <div>—</div>
                                <div>—</div>
                                <div>Aucune notification.</div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $n): ?>
                                <?php
                                $createdAt = (string)($n['created_at'] ?? '');
                                $createdPretty = $createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : '';
                                $type = (string)($n['type'] ?? '');
                                $title = (string)($n['title'] ?? '');
                                $body = (string)($n['body'] ?? '');
                                $userEmail = (string)($n['user_email'] ?? '');
                                $shopName = (string)($n['shop_name'] ?? '');
                                $scope = '';
                                if ($shopName !== '') {
                                    $scope = 'Boutique: ' . $shopName;
                                } elseif ($userEmail !== '') {
                                    $scope = 'Utilisateur: ' . $userEmail;
                                }
                                ?>
                                <div class="table-row">
                                    <div><?php echo htmlspecialchars($createdPretty !== '' ? $createdPretty : '—'); ?></div>
                                    <div><?php echo htmlspecialchars($type); ?></div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($title); ?></strong>
                                        <?php if ($scope !== ''): ?>
                                            <div style="color: var(--gray-dark); font-size: 12px; margin-top: 4px;">
                                                <?php echo htmlspecialchars($scope); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($body !== ''): ?>
                                            <div style="color: var(--gray-dark); font-size: 12px; margin-top: 4px;">
                                                <?php echo htmlspecialchars($body); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($tab === 'users'): ?>
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Gestion des utilisateurs</h2>
                        <p>Liste des comptes et rôles (lecture)</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=overview">Retour</a>
                </div>

                <div class="shops-section">
                    <div class="section-header">
                        <h2>Utilisateurs</h2>
                        <?php if ($isSuperAdmin): ?>
                            <button class="btn btn-primary" onclick="document.getElementById('create-admin-form').style.display = (document.getElementById('create-admin-form').style.display === 'none' ? 'block' : 'none'); return false;">Nouvel admin</button>
                        <?php else: ?>
                            <button class="btn btn-primary" disabled>Nouvel utilisateur</button>
                        <?php endif; ?>
                    </div>

                    <?php if ($isSuperAdmin): ?>
                        <form id="create-admin-form" method="POST" action="<?php echo url('dashboard_admin'); ?>&action=user_create" style="display:none; margin: 12px 0;">
                            <div style="display:flex; gap: 8px; flex-wrap: wrap;">
                                <input name="first_name" placeholder="Prénom" required>
                                <input name="last_name" placeholder="Nom" required>
                                <input name="email" placeholder="Email" required>
                                <input name="password" placeholder="Mot de passe" required>
                                <select name="role">
                                    <option value="admin" selected>admin</option>
                                    <option value="super_admin">super_admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary">Créer</button>
                            </div>
                        </form>
                    <?php endif; ?>

                    <div class="shops-table users-table">
                        <div class="table-header">
                            <div>ID</div>
                            <div>Nom</div>
                            <div>Email</div>
                            <div>Téléphone boutique</div>
                            <div>Rôles</div>
                            <div>Statut</div>
                            <div>Actions</div>
                        </div>

                        <?php if (empty($users)): ?>
                            <div class="table-row">
                                <div style="padding: 14px; color: var(--gray-dark);">Aucun utilisateur.</div>
                                <div></div><div></div><div></div><div></div><div></div><div></div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <?php
                                $fullName = trim((string)($u['first_name'] ?? '') . ' ' . (string)($u['last_name'] ?? ''));
                                $ustatus = (string)($u['status'] ?? 'active');
                                $until = $u['suspended_until'] ?? null;
                                $untilPretty = $until ? date('d/m/Y H:i', strtotime((string)$until)) : '';
                                ?>
                                <div class="table-row">
                                    <div><?php echo (int)($u['id'] ?? 0); ?></div>
                                    <div><?php echo htmlspecialchars($fullName); ?></div>
                                    <div><?php echo htmlspecialchars((string)($u['email'] ?? '')); ?></div>
                                    <div>
                                        <?php
                                        $phones = trim((string)($u['shop_phones'] ?? ''));
                                        $shopsCount = (int)($u['shops_count'] ?? 0);
                                        if ($shopsCount > 0 && $phones !== '') {
                                            echo htmlspecialchars($phones);
                                        } elseif ($shopsCount > 0) {
                                            echo '—';
                                        } else {
                                            echo '<span style="color: var(--gray-dark);">Aucune boutique</span>';
                                        }
                                        ?>
                                    </div>
                                    <div><?php echo htmlspecialchars((string)($u['roles'] ?? '')); ?></div>
                                    <div><?php echo $ustatus === 'suspended' ? ('Suspendu' . ($untilPretty ? ' jusqu\'au ' . htmlspecialchars($untilPretty) : ' (indéfini)')) : 'Actif'; ?></div>
                                    <div>
                                        <?php if ($isSuperAdmin): ?>
                                            <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=user_set_role" style="display:inline-block;">
                                                <input type="hidden" name="user_id" value="<?php echo (int)($u['id'] ?? 0); ?>">
                                                <select name="role">
                                                    <option value="customer">customer</option>
                                                    <option value="vendor">vendor</option>
                                                    <option value="admin">admin</option>
                                                    <option value="super_admin">super_admin</option>
                                                </select>
                                                <button type="submit" class="btn btn-secondary btn-sm">Rôle</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=user_suspend" style="display:inline-block;">
                                            <input type="hidden" name="user_id" value="<?php echo (int)($u['id'] ?? 0); ?>">
                                            <select name="days">
                                                <option value="7">7 jours</option>
                                                <option value="30">30 jours</option>
                                                <option value="">Indéfini</option>
                                            </select>
                                            <button type="submit" class="btn btn-secondary btn-sm">Suspendre</button>
                                        </form>
                                        <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=user_unsuspend" style="display:inline-block;">
                                            <input type="hidden" name="user_id" value="<?php echo (int)($u['id'] ?? 0); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Réactiver</button>
                                        </form>
                                        <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=user_delete" style="display:inline-block;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            <input type="hidden" name="user_id" value="<?php echo (int)($u['id'] ?? 0); ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">Supprimer</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($tab === 'shops'): ?>
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Gestion des boutiques</h2>
                        <p>Liste des boutiques</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=overview">Retour</a>
                </div>

                <div class="shops-section">
                    <div class="shops-table">
                        <div class="table-header">
                            <div>ID</div><div>Boutique</div><div>Statut</div><div>Revenus</div><div>Actions</div>
                        </div>
                        <?php foreach ($shops as $s): ?>
                            <div class="table-row">
                                <div><?php echo (int)($s['id'] ?? 0); ?></div>
                                <div><?php echo htmlspecialchars((string)($s['name'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars((string)($s['status'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars(number_format((float)($s['revenue_total'] ?? 0), 0, ',', ' ')); ?></div>
                                <div>
                                    <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=shop_suspend" style="display:inline-block;">
                                        <input type="hidden" name="shop_id" value="<?php echo (int)($s['id'] ?? 0); ?>">
                                        <input type="hidden" name="days" value="">
                                        <button type="submit" class="btn btn-secondary btn-sm">Suspendre</button>
                                    </form>
                                    <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=shop_unsuspend" style="display:inline-block;">
                                        <input type="hidden" name="shop_id" value="<?php echo (int)($s['id'] ?? 0); ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Réactiver</button>
                                    </form>
                                    <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=shop_delete" style="display:inline-block;" onsubmit="return confirm('Supprimer cette boutique ?');">
                                        <input type="hidden" name="shop_id" value="<?php echo (int)($s['id'] ?? 0); ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($tab === 'products'): ?>
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Gestion des produits</h2>
                        <p>Liste des produits</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=overview">Retour</a>
                </div>

                <div class="shops-section">
                    <div class="shops-table">
                        <div class="table-header">
                            <div>ID</div><div>Produit</div><div>Boutique</div><div>Prix</div><div>Statut</div><div>Actions</div>
                        </div>
                        <?php foreach ($products as $p): ?>
                            <div class="table-row">
                                <div><?php echo (int)($p['id'] ?? 0); ?></div>
                                <div><?php echo htmlspecialchars((string)($p['name'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars((string)($p['shop_name'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars(number_format((float)($p['price'] ?? 0), 0, ',', ' ')); ?></div>
                                <div><?php echo htmlspecialchars((string)($p['status'] ?? '')); ?></div>
                                <div>
                                    <?php if ((string)($p['status'] ?? '') !== 'active'): ?>
                                        <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=product_activate" style="display:inline-block;">
                                            <input type="hidden" name="product_id" value="<?php echo (int)($p['id'] ?? 0); ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Activer</button>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=product_deactivate" style="display:inline-block;">
                                            <input type="hidden" name="product_id" value="<?php echo (int)($p['id'] ?? 0); ?>">
                                            <button type="submit" class="btn btn-secondary btn-sm">Désactiver</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo url('dashboard_admin'); ?>&action=product_delete" style="display:inline-block;" onsubmit="return confirm('Supprimer ce produit ?');">
                                        <input type="hidden" name="product_id" value="<?php echo (int)($p['id'] ?? 0); ?>">
                                        <button type="submit" class="btn btn-outline btn-sm">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php elseif ($tab === 'orders'): ?>
                <div class="welcome-banner">
                    <div class="welcome-text">
                        <h2>Gestion des commandes</h2>
                        <p>Liste des commandes</p>
                    </div>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=overview">Retour</a>
                </div>

                <div class="shops-section">
                    <div class="shops-table">
                        <div class="table-header">
                            <div>ID</div><div>Client</div><div>Boutique</div><div>Total</div><div>Statut</div><div>Date</div>
                        </div>
                        <?php foreach ($orders as $o): ?>
                            <?php $createdAt = (string)($o['created_at'] ?? ''); ?>
                            <div class="table-row">
                                <div><?php echo (int)($o['id'] ?? 0); ?></div>
                                <div><?php echo htmlspecialchars((string)($o['customer_name'] ?? ($o['customer_email'] ?? ''))); ?></div>
                                <div><?php echo htmlspecialchars((string)($o['shop_name'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars(number_format((float)($o['total'] ?? 0), 0, ',', ' ')); ?></div>
                                <div><?php echo htmlspecialchars((string)($o['status'] ?? '')); ?></div>
                                <div><?php echo htmlspecialchars($createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : ''); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
            <!-- Bannière de bienvenue -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h2>Tableau de bord d'administration</h2>
                    <p>Vue d'ensemble de la plateforme iShopping</p>
                </div>
                <button class="btn btn-primary">Générer rapport</button>
            </div>

            <!-- Cartes de statistiques -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Boutiques</h3>
                        <div class="stat-value"><?php echo (int)$shopsCount; ?></div>
                        <div class="stat-change"><i class="fas fa-arrow-up"></i> 12% ce mois</div>
                    </div>
                    <div class="stat-icon shops">
                        <i class="fas fa-store"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Utilisateurs</h3>
                        <div class="stat-value"><?php echo (int)$usersCount; ?></div>
                        <div class="stat-change"><i class="fas fa-arrow-up"></i> 8% ce mois</div>
                    </div>
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Commandes</h3>
                        <div class="stat-value"><?php echo (int)$ordersCount; ?></div>
                        <div class="stat-change"><i class="fas fa-arrow-up"></i> 15% ce mois</div>
                    </div>
                    <div class="stat-icon orders">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3>Revenus</h3>
                        <div class="stat-value"><?php echo htmlspecialchars(number_format($revenue, 0, ',', ' ')); ?></div>
                        <div class="stat-change"><i class="fas fa-arrow-up"></i> 22% ce mois</div>
                    </div>
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <!-- Grille principale -->
            <div class="dashboard-grid">
                <!-- Graphiques -->
                <div class="chart-container">
                    <div class="chart-header">
                        <h3>Performance de la plateforme</h3>
                        <select>
                            <option>30 derniers jours</option>
                            <option>7 derniers jours</option>
                            <option>90 derniers jours</option>
                        </select>
                    </div>
                    <div class="chart-placeholder" style="height: auto; min-height: 120px;">
                        <?php if (empty($salesByDay)): ?>
                            <div style="color: var(--gray-dark);">Aucune vente payée sur la période.</div>
                        <?php else: ?>
                            <div style="display: grid; gap: 8px;">
                                <?php foreach ($salesByDay as $row): ?>
                                    <div style="display: flex; justify-content: space-between; gap: 12px;">
                                        <span style="font-size: 12px; color: var(--gray-dark);">
                                            <?php echo htmlspecialchars((string)($row['day'] ?? '')); ?>
                                        </span>
                                        <strong><?php echo htmlspecialchars(number_format((float)($row['total'] ?? 0), 0, ',', ' ')); ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Activités récentes -->
                <div class="recent-activities">
                    <div class="activities-header">
                        <h3>Activités récentes</h3>
                        <a href="<?php echo url('dashboard_admin'); ?>&tab=notifications">Voir tout</a>
                    </div>
                    <ul class="activities-list">
                        <?php if (empty($recentNotifications)): ?>
                            <li class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="activity-details">
                                    <div class="activity-text">Aucune activité récente.</div>
                                    <div class="activity-time">—</div>
                                </div>
                            </li>
                        <?php else: ?>
                            <?php foreach ($recentNotifications as $n): ?>
                                <?php
                                $type = (string)($n['type'] ?? '');
                                $title = (string)($n['title'] ?? '');
                                $shopName = (string)($n['shop_name'] ?? '');
                                $userEmail = (string)($n['user_email'] ?? '');
                                $createdAt = (string)($n['created_at'] ?? '');
                                $createdPretty = $createdAt ? date('d/m/Y H:i', strtotime($createdAt)) : '';

                                $icon = 'fa-bell';
                                if (strpos($type, 'order_') === 0) {
                                    $icon = 'fa-shopping-cart';
                                } elseif ($type === 'chat_message') {
                                    $icon = 'fa-comment';
                                } elseif (strpos($type, 'user_') === 0) {
                                    $icon = 'fa-user';
                                } elseif (strpos($type, 'shop_') === 0) {
                                    $icon = 'fa-store';
                                }

                                $scope = '';
                                if ($shopName !== '') {
                                    $scope = 'Boutique: ' . $shopName;
                                } elseif ($userEmail !== '') {
                                    $scope = 'Utilisateur: ' . $userEmail;
                                }
                                ?>
                                <li class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas <?php echo htmlspecialchars($icon); ?>"></i>
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-text">
                                            <?php echo htmlspecialchars($title !== '' ? $title : $type); ?>
                                            <?php if ($scope !== ''): ?>
                                                <span style="color: var(--gray-dark); font-size: 12px;">(<?php echo htmlspecialchars($scope); ?>)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="activity-time"><?php echo htmlspecialchars($createdPretty !== '' ? $createdPretty : '—'); ?></div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <!-- Gestion des boutiques -->
            <div class="shops-section">
                <div class="section-header">
                    <h2>Gestion des boutiques</h2>
                    <button class="btn btn-primary">Nouvelle boutique</button>
                </div>

                <div class="shops-table">
                    <div class="table-header">
                        <div>Boutique</div>
                        <div>Propriétaire</div>
                        <div>Statut</div>
                        <div>Revenus</div>
                        <div>Actions</div>
                    </div>

                    <?php if (empty($shops)): ?>
                        <div class="table-row">
                            <div style="padding: 14px; color: var(--gray-dark);">Aucune boutique.</div>
                            <div></div><div></div><div></div><div></div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($shops as $s): ?>
                            <?php
                            $status = (string)($s['status'] ?? 'active');
                            $statusClass = 'pending';
                            $statusLabel = 'En attente';
                            if ($status === 'active') {
                                $statusClass = 'active';
                                $statusLabel = 'Active';
                            } elseif ($status === 'inactive') {
                                $statusClass = 'suspended';
                                $statusLabel = 'Suspendue';
                            }
                            $created = (string)($s['created_at'] ?? '');
                            $createdPretty = $created ? date('d/m/Y', strtotime($created)) : '';
                            $ownerName = trim((string)($s['owner_first_name'] ?? '') . ' ' . (string)($s['owner_last_name'] ?? ''));
                            ?>
                            <div class="table-row">
                                <div class="shop-info">
                                    <div class="shop-avatar">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="shop-details">
                                        <h4><?php echo htmlspecialchars((string)($s['name'] ?? '')); ?></h4>
                                        <p><?php echo $createdPretty !== '' ? 'Créée le ' . htmlspecialchars($createdPretty) : ''; ?></p>
                                    </div>
                                </div>
                                <div><?php echo htmlspecialchars($ownerName !== '' ? $ownerName : (string)($s['owner_email'] ?? '')); ?></div>
                                <div><span class="status-badge <?php echo htmlspecialchars($statusClass); ?>"><?php echo htmlspecialchars($statusLabel); ?></span></div>
                                <div><?php echo htmlspecialchars(number_format((float)($s['revenue_total'] ?? 0), 0, ',', ' ')); ?></div>
                                <div class="action-buttons">
                                    <button class="action-btn" title="Éditer"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn" title="Analytics"><i class="fas fa-chart-bar"></i></button>
                                    <button class="action-btn" title="Suspendre"><i class="fas fa-ban"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="quick-actions">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-cog"></i>
                    </div>
                    <h4>Gestion des utilisateurs</h4>
                    <p>Gérez les comptes utilisateurs et permissions</p>
                    <a class="btn btn-primary" href="<?php echo url('dashboard_admin'); ?>&tab=users">Accéder</a>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h4>Rapports financiers</h4>
                    <p>Générez des rapports détaillés sur les revenus</p>
                    <button class="btn btn-secondary">Générer</button>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Sécurité</h4>
                    <p>Surveillez et gérez la sécurité de la plateforme</p>
                    <button class="btn btn-primary">Vérifier</button>
                </div>
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h4>Paramètres système</h4>
                    <p>Configurez les paramètres globaux de la plateforme</p>
                    <button class="btn btn-secondary">Configurer</button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Script pour le tableau de bord d'administration
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion des liens actifs dans la sidebar
            const navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });

            // Simulation de données pour les cartes de statistiques
            function updateStats() {
                return;
                // Dans une application réelle, on récupérerait ces données via une API
                const stats = [
                    { id: 'shops', value: 248, change: 12 },
                    { id: 'users', value: 15842, change: 8 },
                    { id: 'orders', value: 3427, change: 15 },
                    { id: 'revenue', value: 284592, change: 22 }
                ];

                stats.forEach(stat => {
                    const valueElement = document.querySelector(`.stat-card:nth-child(${stats.indexOf(stat)+1}) .stat-value`);
                    const changeElement = document.querySelector(`.stat-card:nth-child(${stats.indexOf(stat)+1}) .stat-change`);
                    
                    if (valueElement) {
                        valueElement.textContent = stat.id === 'revenue' 
                            ? `$${stat.value.toLocaleString()}` 
                            : stat.value.toLocaleString();
                    }
                    
                    if (changeElement) {
                        changeElement.innerHTML = `<i class="fas fa-arrow-up"></i> ${stat.change}% ce mois`;
                    }
                });
            }

            // Mettre à jour les statistiques toutes les 30 secondes (simulation)
            setInterval(updateStats, 30000);

            // Gestion des boutons d'action des boutiques
            const actionButtons = document.querySelectorAll('.action-btn');
            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    return;
                    const icon = this.querySelector('i').className;
                    const shopName = this.closest('.table-row').querySelector('h4').textContent;
                    
                    if (icon.includes('fa-edit')) {
                        alert(`Édition de la boutique: ${shopName}`);
                    } else if (icon.includes('fa-chart-bar')) {
                        alert(`Analytics de la boutique: ${shopName}`);
                    } else if (icon.includes('fa-ban') || icon.includes('fa-trash')) {
                        if (confirm(`Êtes-vous sûr de vouloir suspendre/supprimer ${shopName}?`)) {
                            alert(`Boutique ${shopName} suspendue/supprimée`);
                        }
                    } else if (icon.includes('fa-check')) {
                        alert(`Boutique ${shopName} approuvée`);
                    } else if (icon.includes('fa-play')) {
                        alert(`Boutique ${shopName} réactivée`);
                    }
                });
            });
        });
    </script>
</body>
</html>
