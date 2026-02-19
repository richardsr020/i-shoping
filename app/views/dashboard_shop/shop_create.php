<?php
$data = $_SESSION['view_data'] ?? [];
$error = $data['error'] ?? null;
$shops = $data['shops'] ?? [];
$canCreateShop = is_array($shops) && count($shops) < 4;
?>

<?php if ($error): ?>
    <div class="card" style="border-left: 4px solid var(--primary);">
        <strong><?php echo htmlspecialchars($error); ?></strong>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="margin-bottom: 10px;">Créer une boutique</h2>
    <p style="color: var(--gray-dark); margin-bottom: 20px;">Renseigne les informations de ta boutique.</p>

    <?php if (!$canCreateShop): ?>
        <div style="color: var(--gray-dark);">
            Limite atteinte: un compte ne peut créer que 4 boutiques au maximum.
        </div>
    <?php endif; ?>

    <?php if ($canCreateShop): ?>
    <form method="post" enctype="multipart/form-data" action="<?php echo url('dashboard_shop'); ?>&tab=shop_create&action=create_shop" style="display: grid; gap: 12px; max-width: 620px;">
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Nom *</label>
            <input name="name" type="text" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Nom de la boutique" />
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Description *</label>
            <textarea name="description" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; min-height: 120px;" placeholder="Description..."></textarea>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Logo *</label>
            <input name="logo_file" type="file" required accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Bannière *</label>
            <input name="banner_file" type="file" required accept="image/*" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd; background: #fff;" />
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Email de contact *</label>
            <input name="email_contact" required type="email" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="contact@..." />
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Téléphone *</label>
            <input name="phone" required type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="+..." />
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Adresse *</label>
            <input name="address" required type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Adresse" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Ville *</label>
                <input name="city" required type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Ville" />
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Pays *</label>
                <input name="country" required type="text" style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;" placeholder="Pays" />
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Devise *</label>
                <?php $cur = 'USD'; ?>
                <select name="currency" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;">
                    <?php
                    $currencies = [
                        'XOF','XAF','GHS','NGN','MAD','DZD','TND','EGP','KES','UGX','TZS','RWF','BIF','ZAR','USD','EUR','GBP','CHF','CAD','AUD','NZD','JPY','CNY','HKD','SGD','INR','PKR','BDT','TRY','BRL','ARS','CLP','COP','PEN','MXN','KRW','IDR','MYR','THB','VND','PHP','AED','SAR','QAR','KWD','OMR','BHD','ILS'
                    ];
                    foreach ($currencies as $c) {
                        $sel = ($cur === $c) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($c) . '" ' . $sel . '>' . htmlspecialchars($c) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">Statut *</label>
                <select name="status" required style="width: 100%; padding: 10px 12px; border-radius: 6px; border: 1px solid #ddd;">
                    <option value="active" selected>Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">Moyens de paiement acceptés *</label>
            <div style="display:flex; gap: 12px; flex-wrap: wrap;">
                <label style="display:flex; align-items:center; gap:8px;">
                    <input class="shop-payment-method" type="checkbox" name="payment_methods[]" value="orange_money" />
                    Orange Money
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input class="shop-payment-method" type="checkbox" name="payment_methods[]" value="mpesa" />
                    M-Pesa
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input class="shop-payment-method" type="checkbox" name="payment_methods[]" value="airtel_money" />
                    Airtel Money
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input class="shop-payment-method" type="checkbox" name="payment_methods[]" value="crypto_usdt" />
                    Crypto USDT
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <button class="btn btn-primary" type="submit">Créer</button>
            <a class="btn btn-secondary" href="<?php echo url('dashboard_shop'); ?>&tab=shops">Annuler</a>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = Array.from(document.querySelectorAll('.shop-payment-method'));
    if (!inputs.length) return;

    const syncRequired = () => {
        const anyChecked = inputs.some((input) => input.checked);
        inputs.forEach((input) => {
            input.required = !anyChecked;
        });
    };

    inputs.forEach((input) => input.addEventListener('change', syncRequired));
    syncRequired();
});
</script>
