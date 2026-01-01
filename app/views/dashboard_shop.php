<?php
$tab = $_SESSION['view_data']['tab'] ?? ($_GET['tab'] ?? 'overview');
$allowedTabs = ['overview', 'shops', 'products', 'orders', 'settings', 'shop_create', 'product_create', 'product_edit'];
if (!in_array($tab, $allowedTabs, true)) {
    $tab = 'overview';
}

$pageTitle = $_SESSION['view_data']['title'] ?? 'Tableau de bord boutique';
$dashboardBaseUrl = url('dashboard_shop');
$data = $_SESSION['view_data'] ?? [];
$currentUser = $data['current_user'] ?? null;
$activeShop = $data['active_shop'] ?? null;
$activeShopName = is_array($activeShop) && !empty($activeShop['name']) ? (string)$activeShop['name'] : 'i shopping';
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/pages.css">
    <style>
        /* === STYLES GÉNÉRAUX === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        :root {
            --primary: var(--color-primary);
            --primary-dark: var(--color-primary-dark);
            --secondary: var(--color-primary);
            --dark: var(--color-black);
            --light: var(--color-white);
            --gray: var(--color-bg-secondary);
            --gray-dark: var(--color-text-muted);
            --success: #28a745;
            --warning: #ffc107;
            --dashboard-surface: var(--color-bg);
            --dashboard-surface-2: var(--color-bg-secondary);
            --dashboard-border: rgba(0, 0, 0, 0.12);
        }

        [data-theme="dark"] {
            --dashboard-border: rgba(255, 255, 255, 0.14);
        }

        body {
            background-color: var(--dashboard-surface-2);
            color: var(--color-text);
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

        .card {
            background: var(--dashboard-surface);
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            padding: 20px;
            margin-bottom: 20px;
        }

        /* === SIDEBAR === */
        .sidebar {
            width: 250px;
            background-color: var(--dark);
            color: white;
            padding: 20px 0;
            transition: all 0.3s;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--dashboard-border);
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
            color: var(--color-text-muted);
            transition: all 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            background-color: var(--color-bg-tertiary);
            color: var(--color-text);
            border-left: 4px solid var(--primary);
        }

        .nav-links i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        /* === CONTENU PRINCIPAL === */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .top-header {
            background-color: var(--dashboard-surface);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow-sm);
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
            border: 1px solid var(--dashboard-border);
            background: var(--dashboard-surface);
            color: var(--color-text);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Contenu du tableau de bord */
        .dashboard-content {
            padding: 30px;
            flex: 1;
        }

        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--color-primary-light));
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
            background: var(--dashboard-surface);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-md);
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
            color: var(--dark);
        }

        .stat-change {
            font-size: 12px;
            color: var(--success);
            display: flex;
            align-items: center;
        }

        .stat-change.down {
            color: var(--primary);
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

        .stat-icon.orders {
            background-color: rgba(30, 144, 255, 0.1);
            color: var(--secondary);
        }

        .stat-icon.revenue {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .stat-icon.customers {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .stat-icon.products {
            background-color: rgba(255, 69, 0, 0.1);
            color: var(--primary);
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .chart-container {
            background: var(--dashboard-surface);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-md);
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
            background-color: var(--dashboard-surface-2);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--color-text-muted);
            text-align: center;
            padding: 20px;
        }

        .recent-orders {
            background: var(--dashboard-surface);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-md);
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .orders-header h3 {
            font-size: 18px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
        }

        .orders-table th, .orders-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--dashboard-border);
        }

        .orders-table th {
            font-weight: 500;
            color: var(--gray-dark);
            font-size: 14px;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status.completed {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }

        .status.pending {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }

        .status.processing {
            background-color: rgba(30, 144, 255, 0.1);
            color: var(--secondary);
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 30px;
        }

        .action-card {
            background: var(--dashboard-surface);
            border-radius: 8px;
            padding: 20px;
            box-shadow: var(--shadow-md);
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
            .stats-cards {
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
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .quick-actions {
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
        }
    </style>
</head>
<body>
    <!-- === SIDEBAR === -->
    <div class="sidebar">
        <div class="logo">
            <i class="fas fa-crown"></i>
            <h1><?php echo htmlspecialchars($activeShopName); ?></h1>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo url('home'); ?>"><i class="fas fa-arrow-left"></i> Accueil</a></li>
            <li><a href="<?php echo $dashboardBaseUrl; ?>&tab=overview" class="<?php echo $tab === 'overview' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Tableau de bord</a></li>
            <li><a href="<?php echo $dashboardBaseUrl; ?>&tab=shops" class="<?php echo $tab === 'shops' ? 'active' : ''; ?>"><i class="fas fa-store"></i> Mes boutiques</a></li>
            <li><a href="<?php echo $dashboardBaseUrl; ?>&tab=products" class="<?php echo $tab === 'products' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> Produits</a></li>
            <li><a href="<?php echo $dashboardBaseUrl; ?>&tab=orders" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Commandes</a></li>
            <li><a href="<?php echo $dashboardBaseUrl; ?>&tab=settings" class="<?php echo $tab === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Paramètres</a></li>
        </ul>
    </div>

    <!-- === CONTENU PRINCIPAL === -->
    <div class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div class="user-menu">
                <button id="theme-toggle" class="theme-toggle" aria-label="Changer de thème" style="margin-right: 10px;">
                    <i class="fas fa-moon"></i>
                </button>
                <div class="notifications">
                    <i class="far fa-bell"></i>
                </div>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php
                        $initials = 'U';
                        if (is_array($currentUser)) {
                            $fn = trim((string)($currentUser['first_name'] ?? ''));
                            $ln = trim((string)($currentUser['last_name'] ?? ''));
                            $initials = strtoupper(substr($fn, 0, 1) . substr($ln, 0, 1));
                            $initials = $initials !== '' ? $initials : 'U';
                        }
                        echo htmlspecialchars($initials);
                        ?>
                    </div>
                    <div>
                        <div class="user-name">
                            <?php
                            if (is_array($currentUser)) {
                                echo htmlspecialchars(trim((string)($currentUser['first_name'] ?? '') . ' ' . (string)($currentUser['last_name'] ?? '')));
                            } else {
                                echo 'Utilisateur';
                            }
                            ?>
                        </div>
                        <div class="user-role"><?php echo is_array($currentUser) ? htmlspecialchars((string)($currentUser['email'] ?? '')) : ''; ?></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Contenu du tableau de bord -->
        <div class="dashboard-content">
            <?php
            $tabPath = __DIR__ . '/dashboard_shop/' . $tab . '.php';
            if (file_exists($tabPath)) {
                require $tabPath;
            }
            ?>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/public/js/theme.js"></script>
    <script></script>
</body>
</html>