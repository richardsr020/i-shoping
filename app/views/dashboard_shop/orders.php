<?php
$data = $_SESSION['view_data'] ?? [];
$activeShop = $data['active_shop'] ?? null;
$orders = $data['orders'] ?? [];
$error = $data['error'] ?? null;
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Commandes</h2>
    <?php if ($activeShop): ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Boutique active : <strong><?php echo htmlspecialchars($activeShop['name']); ?></strong></p>
    <?php else: ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Aucune boutique active. Va dans "Mes boutiques" pour en créer/activer une.</p>
    <?php endif; ?>
</div>

<div class="card">
    <h3 style="margin-bottom: 10px;">Liste des commandes</h3>

    <?php if (!$activeShop): ?>
        <div style="color: var(--gray-dark);">Sélectionne une boutique pour voir ses commandes.</div>
    <?php elseif (empty($orders)): ?>
        <div style="color: var(--gray-dark);">Aucune commande pour cette boutique.</div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="orders-table" style="min-width: 860px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Acheteur</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Paid</th>
                        <th>Satisfied</th>
                        <th>Créée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>#<?php echo (int)$o['id']; ?></td>
                            <td>
                                <div style="font-weight: 700;"><?php echo htmlspecialchars((string)($o['customer_name'] ?? '')); ?></div>
                                <div style="font-size: 12px; color: var(--gray-dark);"><?php echo htmlspecialchars((string)($o['customer_email'] ?? '')); ?></div>
                            </td>
                            <td><strong><?php echo htmlspecialchars(number_format((float)($o['total'] ?? 0), 0, ',', ' ')); ?></strong></td>
                            <td>
                                <?php $st = (string)($o['status'] ?? 'pending'); ?>
                                <span class="status <?php echo htmlspecialchars($st); ?>"><?php echo htmlspecialchars($st); ?></span>
                            </td>
                            <td><?php echo ((int)($o['paid'] ?? 0) === 1) ? 'true' : 'false'; ?></td>
                            <td><?php echo ((int)($o['satisfied'] ?? 0) === 1) ? 'true' : 'false'; ?></td>
                            <td style="font-size: 12px; color: var(--gray-dark);"><?php echo htmlspecialchars((string)($o['created_at'] ?? '')); ?></td>
                            <td>
                                <?php if ((int)($o['paid'] ?? 0) !== 1): ?>
                                    <form method="post" action="<?php echo url('dashboard_shop'); ?>&tab=orders&action=mark_order_paid" style="margin: 0;">
                                        <input type="hidden" name="order_id" value="<?php echo (int)$o['id']; ?>" />
                                        <button class="btn btn-secondary" type="submit">Marquer payé</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: var(--success); font-weight: 700;">Payé</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
