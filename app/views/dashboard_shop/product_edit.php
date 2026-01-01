<?php
$data = $_SESSION['view_data'] ?? [];
$activeShop = $data['active_shop'] ?? null;
$error = $data['error'] ?? null;
$product = $data['edit_product'] ?? null;

$categories = [
    'Automotive',
    'Baby',
    'Beauty & Personal Care',
    'Books',
    'Clothing, Shoes & Jewelry',
    'Computers',
    'Electronics',
    'Health & Household',
    'Home & Kitchen',
    'Mobile Phones & Accessories',
    'Office Products',
    'Pet Supplies',
    'Sports & Outdoors',
    'Tools & Home Improvement',
    'Toys & Games',
    'Video Games',
];
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Modifier un produit</h2>

    <?php if (!$activeShop): ?>
        <p style="color: var(--gray-dark);">Aucune boutique active.</p>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=shops">Mes boutiques</a>
    <?php elseif (!$product): ?>
        <p style="color: var(--gray-dark);">Produit introuvable.</p>
        <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=products">Retour</a>
    <?php else: ?>
        <form method="post" action="<?php echo url('dashboard_shop'); ?>&tab=product_edit&action=update_product&product_id=<?php echo (int)$product['id']; ?>" style="display: grid; gap: 12px; max-width: 720px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nom *</label>
                <input name="name" type="text" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($product['name'] ?? '')); ?>" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Prix *</label>
                    <input name="price" type="number" step="0.01" min="0" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($product['price'] ?? 0)); ?>" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Stock</label>
                    <input name="stock" type="number" step="1" min="0" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo (int)($product['stock'] ?? 0); ?>" />
                </div>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Quantité minimale</label>
                <input name="min_order_qty" type="number" step="1" min="1" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo (int)($product['min_order_qty'] ?? 1); ?>" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Catégorie</label>
                <?php $currentCat = (string)($product['category'] ?? ''); ?>
                <select name="category" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="">—</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $currentCat === $cat ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button class="btn btn-primary" type="submit">Enregistrer</button>
                <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=products">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</div>
