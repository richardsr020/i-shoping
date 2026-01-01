<?php
$data = $_SESSION['view_data'] ?? [];
$activeShop = $data['active_shop'] ?? null;
$products = $data['products'] ?? [];
$error = $data['error'] ?? null;
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Produits</h2>
    <?php if ($activeShop): ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Boutique active : <strong><?php echo htmlspecialchars($activeShop['name']); ?></strong></p>
    <?php else: ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Aucune boutique active. Va dans "Mes boutiques" pour en créer/activer une.</p>
    <?php endif; ?>

    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a class="btn btn-primary" href="<?php echo url('dashboard_shop'); ?>&tab=product_create">Ajouter un produit</a>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=shops">Changer de boutique</a>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 10px;">Liste des produits</h3>

    <?php if (!$activeShop): ?>
        <div style="color: var(--gray-dark);">Sélectionne une boutique pour voir ses produits.</div>
    <?php elseif (empty($products)): ?>
        <div style="color: var(--gray-dark);">Aucun produit dans cette boutique.</div>
    <?php else: ?>
        <div style="display: grid; gap: 10px;">
            <?php foreach ($products as $product): ?>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 12px; border: 1px solid var(--dashboard-border); border-radius: 8px;">
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="width: 56px; height: 56px; border-radius: 10px; overflow: hidden; background: var(--dashboard-surface-2); flex: 0 0 56px;">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars((string)$product['image']); ?>" alt="<?php echo htmlspecialchars((string)$product['name']); ?>" style="width: 56px; height: 56px; object-fit: cover;" onerror="this.style.display='none'" />
                            <?php endif; ?>
                        </div>
                        <div>
                            <div style="font-weight: 700;">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </div>
                            <div style="color: var(--gray-dark); font-size: 14px;">
                                <?php echo htmlspecialchars((string)($product['category'] ?? '')); ?>
                                <?php if (!empty($product['brand'])): ?>
                                    - <?php echo htmlspecialchars($product['brand']); ?>
                                <?php endif; ?>
                            </div>
                            <div style="margin-top: 4px;">
                                <?php if (!empty($product['promo_price']) && (float)$product['promo_price'] > 0 && (float)$product['promo_price'] < (float)$product['price']): ?>
                                    <strong style="color: var(--primary); font-size: 16px;"><?php echo htmlspecialchars((string)$product['promo_price']); ?></strong>
                                    <span style="color: var(--gray-dark); font-size: 13px; margin-left: 6px; text-decoration: line-through;">
                                        <?php echo htmlspecialchars((string)$product['price']); ?>
                                    </span>
                                <?php else: ?>
                                    <strong style="color: var(--primary); font-size: 16px;"><?php echo htmlspecialchars((string)$product['price']); ?></strong>
                                <?php endif; ?>
                                <span style="color: var(--gray-dark); font-size: 14px; margin-left: 8px;">Stock: <?php echo (int)($product['stock'] ?? 0); ?></span>
                                <span style="color: var(--gray-dark); font-size: 12px; margin-left: 8px;">Images: <?php echo (int)($product['extra_images_count'] ?? 0); ?></span>
                                <span style="color: var(--gray-dark); font-size: 12px; margin-left: 8px;">Variantes: <?php echo (int)($product['variants_count'] ?? 0); ?></span>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=product_edit&product_id=<?php echo (int)$product['id']; ?>">Modifier</a>
                        <form method="post" action="<?php echo url('dashboard_shop'); ?>&tab=products&action=delete_product" style="margin: 0;" onsubmit="return confirm('Supprimer ce produit ?');">
                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>" />
                            <button class="btn btn-primary" type="submit">Supprimer</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
