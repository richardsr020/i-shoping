<?php
$data = $_SESSION['view_data'] ?? [];
$activeShop = $data['active_shop'] ?? null;
$error = $data['error'] ?? null;
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Créer un produit</h2>

    <?php if (!$activeShop): ?>
        <p style="color: var(--gray-dark);">Aucune boutique active. Va dans "Mes boutiques" pour en activer une.</p>
        <div style="margin-top: 15px;">
            <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=shops">Aller à Mes boutiques</a>
        </div>
    <?php else: ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Boutique active : <strong><?php echo htmlspecialchars($activeShop['name']); ?></strong></p>

        <form method="post" enctype="multipart/form-data" action="<?php echo url('dashboard_shop'); ?>&tab=product_create&action=create_product" style="display: grid; gap: 12px; max-width: 720px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nom *</label>
                <input name="name" type="text" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Nom du produit" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Description</label>
                <textarea name="description" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; min-height: 120px;" placeholder="Description..."></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Prix *</label>
                    <input name="price" type="number" step="0.01" min="0" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="0.00" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Stock</label>
                    <input name="stock" type="number" step="1" min="0" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="0" />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Prix promo</label>
                    <input name="promo_price" type="number" step="0.01" min="0" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="0.00" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">SKU</label>
                    <input name="sku" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="SKU" />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Catégorie</label>
                    <input name="category" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Catégorie" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Marque</label>
                    <input name="brand" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Marque" />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Taille</label>
                    <input name="size" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Taille" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Statut</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;">
                        <option value="active" selected>Actif</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Image principale</label>
                <input name="image_file" type="file" accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Images supplémentaires</label>
                <input name="extra_images_files[]" type="file" multiple accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Variantes</label>
                <div style="display: grid; gap: 10px;">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 10px;">
                            <input name="variant_name[]" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Nom variante" />
                            <input name="variant_color[]" type="color" value="#000000" style="width: 100%; height: 42px; padding: 4px 6px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
                            <input name="variant_additional_price[]" type="number" step="0.01" min="0" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="+Prix" />
                            <input name="variant_stock[]" type="number" step="1" min="0" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Stock" />
                        </div>
                    <?php endfor; ?>
                </div>
            </div>

            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <button class="btn btn-primary" type="submit">Créer</button>
                <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=products">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</div>
