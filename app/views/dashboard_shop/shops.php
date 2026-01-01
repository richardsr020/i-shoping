<?php
$data = $_SESSION['view_data'] ?? [];
$shops = $data['shops'] ?? [];
$activeShopId = (int)($data['active_shop_id'] ?? 0);
$error = $data['error'] ?? null;
$canCreateShop = empty($shops);
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Mes boutiques</h2>
    <p style="color: var(--gray-dark); margin-bottom: 20px;">Créer, sélectionner la boutique active, et supprimer une boutique.</p>

    <?php if (!$canCreateShop): ?>
        <div style="color: var(--gray-dark); margin-bottom: 12px;">
            Pour le moment, un compte ne peut créer qu'une seule boutique.
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <?php if ($canCreateShop): ?>
            <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=shop_create">Créer une boutique</a>
        <?php else: ?>
            <button class="btn btn-primary" type="button" disabled style="opacity: .55; cursor: not-allowed;">Créer une boutique</button>
        <?php endif; ?>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=products">Gérer les produits</a>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 10px;">Liste des boutiques</h3>

    <?php if (empty($shops)): ?>
        <div style="color: var(--gray-dark);">Aucune boutique. Crée ta première boutique.</div>
    <?php else: ?>
        <div style="display: grid; gap: 10px;">
            <?php foreach ($shops as $shop): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--dashboard-border); border-radius: 8px;">
                    <div>
                        <div style="font-weight: 700;">
                            <?php echo htmlspecialchars($shop['name']); ?>
                            <?php if ((int)$shop['id'] === $activeShopId): ?>
                                <span style="color: var(--success); font-weight: 700; margin-left: 8px;">(active)</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($shop['description'])): ?>
                            <div style="color: var(--gray-dark); font-size: 14px;"><?php echo htmlspecialchars($shop['description']); ?></div>
                        <?php endif; ?>

                        <?php if (!empty($shop['slug']) || !empty($shop['url'])): ?>
                            <div style="margin-top: 6px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                <?php if (!empty($shop['slug'])): ?>
                                    <span style="font-size: 12px; color: var(--gray-dark);">slug: <strong><?php echo htmlspecialchars((string)$shop['slug']); ?></strong></span>
                                <?php endif; ?>
                                <?php if (!empty($shop['url'])): ?>
                                    <a href="<?php echo htmlspecialchars((string)$shop['url']); ?>" target="_blank" rel="noopener" style="font-size: 12px; color: var(--primary);">ouvrir</a>
                                    <button type="button" class="btn btn-secondary" style="padding: 6px 10px; font-size: 12px;" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars((string)$shop['url']); ?>')">Copier le lien</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <form method="post" action="<?php echo url('dashboard_shop'); ?>&tab=shops&action=set_active_shop" style="margin: 0;">
                            <input type="hidden" name="shop_id" value="<?php echo (int)$shop['id']; ?>" />
                            <button class="btn btn-secondary" type="submit">Activer</button>
                        </form>

                        <form method="post" action="<?php echo url('dashboard_shop'); ?>&tab=shops&action=delete_shop" style="margin: 0;" onsubmit="return confirm('Supprimer cette boutique ?');">
                            <input type="hidden" name="shop_id" value="<?php echo (int)$shop['id']; ?>" />
                            <button class="btn btn-primary" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
