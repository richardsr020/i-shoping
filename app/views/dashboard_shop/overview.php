<?php
$data = $_SESSION['view_data'] ?? [];
$overview = $data['overview'] ?? [];
$kpis = $overview['kpis'] ?? ['orders_count' => 0, 'revenue_total' => 0.0, 'customers_count' => 0];
$productsCount = (int)($overview['products_count'] ?? 0);
$days = (int)($overview['days'] ?? 30);
$orderStatus = (string)($overview['order_status'] ?? 'all');
$salesByDay = $overview['sales_by_day'] ?? [];
$recentOrders = $overview['recent_orders'] ?? [];
?>

<div class="welcome-banner">
    <div class="welcome-text">
        <h2>Bon retour !</h2>
        <p>Voici un aperçu de votre activité aujourd'hui</p>
    </div>
    <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=product_create">Ajouter un produit</a>
</div>

<div class="stats-cards">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Commandes</h3>
            <div class="stat-value"><?php echo (int)($kpis['orders_count'] ?? 0); ?></div>
        </div>
        <div class="stat-icon orders">
            <i class="fas fa-shopping-cart"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Revenus</h3>
            <div class="stat-value"><?php echo htmlspecialchars(number_format((float)($kpis['revenue_total'] ?? 0), 0, ',', ' ')); ?></div>
        </div>
        <div class="stat-icon revenue">
            <i class="fas fa-dollar-sign"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Clients</h3>
            <div class="stat-value"><?php echo (int)($kpis['customers_count'] ?? 0); ?></div>
        </div>
        <div class="stat-icon customers">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Produits</h3>
            <div class="stat-value"><?php echo (int)$productsCount; ?></div>
        </div>
        <div class="stat-icon products">
            <i class="fas fa-box"></i>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="chart-container">
        <div class="chart-header">
            <h3>Ventes des <?php echo (int)$days; ?> derniers jours</h3>
            <form method="get" action="<?php echo url('dashboard_shop'); ?>" style="margin: 0;">
                <input type="hidden" name="tab" value="overview" />
                <input type="hidden" name="order_status" value="<?php echo htmlspecialchars($orderStatus); ?>" />
                <select name="days" onchange="this.form.submit()">
                    <option value="30" <?php echo $days === 30 ? 'selected' : ''; ?>>30 derniers jours</option>
                    <option value="7" <?php echo $days === 7 ? 'selected' : ''; ?>>7 derniers jours</option>
                    <option value="90" <?php echo $days === 90 ? 'selected' : ''; ?>>90 derniers jours</option>
                </select>
            </form>
        </div>
        <?php if (empty($salesByDay)): ?>
            <div class="chart-placeholder">Aucune vente payée sur la période.</div>
        <?php else: ?>
            <div style="display: grid; gap: 6px;">
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

    <div class="recent-orders">
        <div class="orders-header">
            <h3>Commandes récentes</h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <form method="get" action="<?php echo url('dashboard_shop'); ?>" style="margin: 0;">
                    <input type="hidden" name="tab" value="overview" />
                    <input type="hidden" name="days" value="<?php echo (int)$days; ?>" />
                    <select name="order_status" onchange="this.form.submit()">
                        <option value="all" <?php echo $orderStatus === 'all' ? 'selected' : ''; ?>>Tous</option>
                        <option value="pending" <?php echo $orderStatus === 'pending' ? 'selected' : ''; ?>>pending</option>
                        <option value="processing" <?php echo $orderStatus === 'processing' ? 'selected' : ''; ?>>processing</option>
                        <option value="completed" <?php echo $orderStatus === 'completed' ? 'selected' : ''; ?>>completed</option>
                        <option value="canceled" <?php echo $orderStatus === 'canceled' ? 'selected' : ''; ?>>canceled</option>
                    </select>
                </form>
                <a href="<?php echo url('dashboard_shop'); ?>&tab=orders">Voir tout</a>
            </div>
        </div>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>N° Commande</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recentOrders)): ?>
                    <tr>
                        <td colspan="4" style="color: var(--gray-dark); padding: 12px 0;">Aucune commande.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recentOrders as $o): ?>
                        <?php $st = (string)($o['status'] ?? 'pending'); ?>
                        <tr>
                            <td>#<?php echo (int)($o['id'] ?? 0); ?></td>
                            <td><?php echo htmlspecialchars((string)($o['customer_name'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars(number_format((float)($o['total'] ?? 0), 0, ',', ' ')); ?></td>
                            <td><span class="status <?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="quick-actions">
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-plus"></i>
        </div>
        <h4>Ajouter un produit</h4>
        <p>Créez une nouvelle fiche produit pour votre boutique</p>
        <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=product_create">Commencer</a>
    </div>
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-cog"></i>
        </div>
        <h4>Paramètres boutique</h4>
        <p>Modifiez les paramètres de votre boutique</p>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=settings">Configurer</a>
    </div>
</div>
