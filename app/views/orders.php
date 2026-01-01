<?php
$data = $_SESSION['view_data'] ?? [];
$orders = $data['orders'] ?? [];
$pendingTotal = (float)($data['pending_total'] ?? 0);
?>

<script>
    window.BASE_URL = <?php echo json_encode((string)BASE_URL); ?>;
</script>

<div class="container" style="padding: var(--spacing-lg) 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
        <h1 style="margin: 0;">Mes commandes</h1>
        <a class="btn btn-ghost" href="<?php echo url('home'); ?>">Retour à l'accueil</a>
    </div>

    <?php if (!empty($orders)): ?>
        <?php $pendingCurrency = (string)($orders[0]['shop_currency'] ?? ''); ?>
        <div class="card" style="padding: var(--spacing-md); margin-bottom: var(--spacing-md); display: flex; align-items: center; justify-content: space-between; gap: var(--spacing-md); flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 10px; color: var(--muted);">
                <i class="fas fa-hourglass-half"></i>
                <span style="font-size: 13px;">Total en attente</span>
            </div>
            <div style="font-weight: 800; font-size: 14px;">
                <?php echo htmlspecialchars(number_format($pendingTotal, 0, ',', ' ')); ?>
                <?php if ($pendingCurrency !== ''): ?>
                    <span style="color: var(--muted); font-weight: 700; font-size: 12px; margin-left: 6px;">
                        <?php echo htmlspecialchars($pendingCurrency); ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="card" style="padding: var(--spacing-lg);">
            <div style="color: var(--muted);">Aucune commande.</div>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: var(--spacing-md);">
                    <?php foreach ($orders as $o): ?>
                        <?php
                        $orderId = (int)($o['id'] ?? 0);
                        $paid = (int)($o['paid'] ?? 0) === 1;
                        $satisfied = (int)($o['satisfied'] ?? 0) === 1;
                        $canceled = (int)($o['canceled'] ?? 0) === 1;
                        $canCancel = !$paid && !$satisfied && !$canceled;
                        $canSatisfy = $paid && !$satisfied && !$canceled;
                        $st = (string)($o['status'] ?? 'pending');
                        ?>
                        <?php
                        $currency = (string)($o['shop_currency'] ?? '');
                        $productName = (string)($o['product_name'] ?? '');
                        $qty = (int)($o['quantity'] ?? 0);
                        $unitPrice = (float)($o['unit_price'] ?? 0);
                        $lineTotal = (float)($o['line_total'] ?? 0);
                        ?>

                        <div class="card" style="padding: var(--spacing-md); border: 1px solid var(--border); border-radius: 12px;">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;">
                                <div style="min-width: 0;">
                                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 13px;">
                                        <i class="fas fa-store" style="color: var(--muted);"></i>
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars((string)($o['shop_name'] ?? '')); ?>
                                        </span>
                                    </div>
                                    <div style="margin-top: 6px; color: var(--muted); font-size: 12px; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-tag"></i>
                                        <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?php echo htmlspecialchars($productName !== '' ? $productName : 'Produit'); ?>
                                        </span>
                                    </div>
                                </div>

                                <div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 999px; border: 1px solid var(--border); font-size: 11px; color: var(--muted); flex: 0 0 auto;">
                                    <i class="fas fa-clock"></i>
                                    <span><?php echo htmlspecialchars($st); ?></span>
                                </div>
                            </div>

                            <div style="margin-top: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 12px; color: var(--muted);">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-cubes"></i>
                                    <span>Qté: <strong style="color: var(--color-text); font-weight: 800;"><?php echo (int)$qty; ?></strong></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px; justify-content: flex-end;">
                                    <i class="fas fa-coins"></i>
                                    <span>
                                        <strong style="color: var(--color-text); font-weight: 800;">
                                            <?php echo htmlspecialchars(number_format($unitPrice, 0, ',', ' ')); ?>
                                        </strong>
                                        <?php if ($currency !== ''): ?>
                                            <span style="font-size: 11px; margin-left: 4px;"><?php echo htmlspecialchars($currency); ?></span>
                                        <?php endif; ?>
                                        <span style="font-size: 11px; margin-left: 4px;">/u</span>
                                    </span>
                                </div>
                            </div>

                            <div style="margin-top: 10px; display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted);">
                                    <i class="fas fa-receipt"></i>
                                    <span>Total:</span>
                                </div>
                                <div style="font-weight: 900; font-size: 13px;">
                                    <?php echo htmlspecialchars(number_format($lineTotal > 0 ? $lineTotal : (float)($o['total'] ?? 0), 0, ',', ' ')); ?>
                                    <?php if ($currency !== ''): ?>
                                        <span style="color: var(--muted); font-weight: 700; font-size: 11px; margin-left: 6px;">
                                            <?php echo htmlspecialchars($currency); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="margin-top: 12px; display: flex; justify-content: flex-end; gap: 8px;">
                                <?php if ($canceled): ?>
                                    <span style="color: var(--muted); font-weight: 700; font-size: 12px;">Annulée</span>
                                <?php elseif ($canSatisfy): ?>
                                    <button type="button" class="btn btn-primary btn-sm" style="padding: 6px 10px; font-size: 12px;" onclick="markSatisfied(<?php echo $orderId; ?>, this)">
                                        <i class="fas fa-check" style="margin-right: 6px;"></i> Satisfait
                                    </button>
                                <?php elseif ($canCancel): ?>
                                    <button type="button" class="btn btn-outline btn-sm" style="padding: 6px 10px; font-size: 12px;" onclick="cancelOrder(<?php echo $orderId; ?>, this)">
                                        <i class="fas fa-xmark" style="margin-right: 6px;"></i> Annuler
                                    </button>
                                <?php else: ?>
                                    <span style="color: var(--muted); font-size: 12px;">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
        </div>

        <script>
            async function markSatisfied(orderId, btn) {
                if (!orderId) return;
                if (!confirm('Confirmer que cette commande est satisfaite ?')) return;

                const oldText = btn ? btn.textContent : '';
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Validation...';
                }

                try {
                    const res = await fetch(`${window.BASE_URL}/api/mark_order_satisfied.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId })
                    });

                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data || !data.success) {
                        throw new Error(data && data.error ? data.error : 'Action impossible');
                    }

                    window.location.reload();
                } catch (e) {
                    alert(e && e.message ? e.message : 'Erreur');
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = oldText;
                    }
                }
            }

            async function cancelOrder(orderId, btn) {
                if (!orderId) return;
                if (!confirm('Annuler cette commande ?')) return;

                const oldText = btn ? btn.textContent : '';
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Annulation...';
                }

                try {
                    const res = await fetch(`${window.BASE_URL}/api/cancel_order.php`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ order_id: orderId })
                    });

                    const data = await res.json().catch(() => ({}));
                    if (!res.ok || !data || !data.success) {
                        throw new Error(data && data.error ? data.error : 'Annulation impossible');
                    }

                    window.location.reload();
                } catch (e) {
                    alert(e && e.message ? e.message : 'Erreur');
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = oldText;
                    }
                }
            }
        </script>
    <?php endif; ?>
</div>
