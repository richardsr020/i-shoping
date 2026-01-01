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
    <h2 style="margin-bottom: 10px;">Paramètres</h2>

    <?php if (!$activeShop): ?>
        <p style="color: var(--gray-dark);">Aucune boutique active. Va dans "Mes boutiques" pour en activer une.</p>
        <div style="margin-top: 15px;">
            <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=shops">Aller à Mes boutiques</a>
        </div>
    <?php else: ?>
        <p style="color: var(--gray-dark); margin-bottom: 20px;">Modifier la boutique active : <strong><?php echo htmlspecialchars($activeShop['name']); ?></strong></p>

        <form method="post" enctype="multipart/form-data" action="<?php echo url('dashboard_shop'); ?>&tab=settings&action=update_shop" style="display: grid; gap: 15px; max-width: 520px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nom de la boutique</label>
                <input name="name" type="text" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid var(--dashboard-border); background: var(--dashboard-surface); color: var(--color-text);" value="<?php echo htmlspecialchars($activeShop['name']); ?>" />
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Description</label>
                <textarea name="description" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid var(--dashboard-border); background: var(--dashboard-surface); color: var(--color-text); min-height: 100px;" placeholder="Description..."><?php echo htmlspecialchars((string)($activeShop['description'] ?? '')); ?></textarea>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Logo</label>
                <?php if (!empty($activeShop['logo'])): ?>
                    <div style="color: var(--gray-dark); font-size: 13px; margin-bottom: 6px;">Actuel: <?php echo htmlspecialchars((string)$activeShop['logo']); ?></div>
                <?php endif; ?>
                <input name="logo_file" type="file" accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Bannière</label>
                <?php if (!empty($activeShop['banner'])): ?>
                    <div style="color: var(--gray-dark); font-size: 13px; margin-bottom: 6px;">Actuelle: <?php echo htmlspecialchars((string)$activeShop['banner']); ?></div>
                <?php endif; ?>
                <input name="banner_file" type="file" accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email de contact</label>
                <input name="email_contact" type="email" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['email_contact'] ?? '')); ?>" placeholder="contact@..." />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Téléphone</label>
                <input name="phone" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['phone'] ?? '')); ?>" placeholder="+..." />
            </div>

            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Adresse</label>
                <input name="address" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['address'] ?? '')); ?>" placeholder="Adresse" />
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Ville</label>
                    <input name="city" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['city'] ?? '')); ?>" placeholder="Ville" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Pays</label>
                    <input name="country" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['country'] ?? '')); ?>" placeholder="Pays" />
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Devise</label>
                    <input name="currency" type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" value="<?php echo htmlspecialchars((string)($activeShop['currency'] ?? 'USD')); ?>" placeholder="USD" />
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 6px;">Statut</label>
                    <select name="status" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;">
                        <option value="active" <?php echo (($activeShop['status'] ?? 'active') === 'active') ? 'selected' : ''; ?>>Actif</option>
                        <option value="inactive" <?php echo (($activeShop['status'] ?? '') === 'inactive') ? 'selected' : ''; ?>>Inactif</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn btn-primary" type="submit">Enregistrer</button>
                <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=overview">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</div>
