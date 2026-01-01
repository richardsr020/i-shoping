<?php
require_once __DIR__ . '/../config.php';

$data = $_SESSION['view_data'] ?? [];
$product = $data['product'] ?? null;
$images = $data['images'] ?? [];
$variants = $data['variants'] ?? [];

if (!$product) {
    echo '<main class="main-content container"><h2>Produit introuvable</h2></main>';
    return;
}

$mainImage = $product['image'] ?? null;
if ((!$mainImage || $mainImage === '') && !empty($images)) {
    $mainImage = $images[0]['image'] ?? null;
}

$price = (float)($product['price'] ?? 0);
$promo = (float)($product['promo_price'] ?? 0);
$hasPromo = $promo > 0 && $promo < $price;
$currency = (string)($product['shop_currency'] ?? 'XOF');
$minOrderQty = (int)($product['min_order_qty'] ?? 1);
if ($minOrderQty <= 0) {
    $minOrderQty = 1;
}
?>

<style>
    .pd-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: var(--spacing-2xl); align-items: start; }
    @media (max-width: 900px) { .pd-grid { grid-template-columns: 1fr; } }

    .pd-qty-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); display: none; align-items: center; justify-content: center; padding: 16px; z-index: 9999; }
    .pd-qty-overlay.active { display: flex; }
    .pd-qty-card { width: 100%; max-width: 360px; background: var(--color-bg); color: var(--color-text); border-radius: 14px; box-shadow: var(--shadow-xl); border: 1px solid rgba(0,0,0,0.12); overflow: hidden; }
    .pd-qty-head { padding: 14px 14px 0 14px; display:flex; justify-content: space-between; align-items: center; gap: 10px; }
    .pd-qty-title { font-weight: 900; font-size: 14px; }
    .pd-qty-body { padding: 12px 14px 14px 14px; display:grid; gap: 12px; }
    .pd-qty-row { display:flex; gap: 10px; align-items: center; }
    .pd-qty-input { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid rgba(0,0,0,0.12); background: var(--color-bg); color: var(--color-text); }
    .pd-qty-actions { display:flex; justify-content: flex-end; gap: 10px; }
    .pd-qty-close { border: none; background: transparent; color: var(--color-text-muted); cursor: pointer; padding: 6px 8px; }
</style>

<main class="main-content container" style="padding-top: var(--spacing-lg);">
    <div style="margin-bottom: var(--spacing-lg);">
        <a href="<?php echo url('home'); ?>" class="btn btn-ghost btn-sm">Retour</a>
    </div>

    <div class="pd-grid">
        <div>
            <div style="border-radius: var(--radius-lg); overflow:hidden; background: var(--color-bg-secondary); box-shadow: var(--shadow-md);">
                <?php if (!empty($mainImage)): ?>
                    <img id="pd-main-image" src="<?php echo htmlspecialchars((string)$mainImage); ?>" alt="<?php echo htmlspecialchars((string)$product['name']); ?>" style="width:100%; height:520px; object-fit:cover; display:block;" onerror="this.style.display='none'" />
                <?php else: ?>
                    <div style="height:520px; display:flex; align-items:center; justify-content:center; color: var(--color-text-muted);">Aucune image</div>
                <?php endif; ?>
            </div>

            <div style="display:flex; gap: 10px; margin-top: var(--spacing-lg); flex-wrap: wrap;">
                <button type="button" class="btn btn-primary" onclick="pdOpenQtyModal(<?php echo (int)$product['id']; ?>)">Ajouter au panier</button>
                <a class="btn btn-ghost" href="<?php echo url('profile_shop'); ?>&id=<?php echo (int)($product['shop_id'] ?? 0); ?>">Voir la boutique</a>
            </div>

            <?php if (!empty($images)): ?>
                <div style="display:flex; gap:10px; margin-top: var(--spacing-md); overflow-x:auto; padding-bottom:6px;">
                    <?php foreach ($images as $img): ?>
                        <?php if (empty($img['image'])) continue; ?>
                        <button type="button" style="border: 1px solid rgba(0,0,0,0.08); background: var(--color-bg); padding:0; border-radius:12px; overflow:hidden; width:78px; height:78px; flex:0 0 auto; cursor:pointer;" onclick="document.getElementById('pd-main-image').src='<?php echo htmlspecialchars((string)$img['image']); ?>'">
                            <img src="<?php echo htmlspecialchars((string)$img['image']); ?>" alt="" style="width:78px; height:78px; object-fit:cover; display:block;" onerror="this.style.display='none'" />
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h1 style="margin:0 0 var(--spacing-sm) 0; font-size:32px; line-height:1.15;"><?php echo htmlspecialchars((string)$product['name']); ?></h1>
            <div style="color: var(--color-text-muted); margin-bottom: var(--spacing-md);">
                Boutique:
                <a href="<?php echo url('profile_shop'); ?>&id=<?php echo (int)($product['shop_id'] ?? 0); ?>" style="color: var(--color-primary); font-weight: 800;">
                    <?php echo htmlspecialchars((string)($product['shop_name'] ?? '')); ?>
                </a>
            </div>

            <?php if (!empty($product['shop_email_contact']) || !empty($product['shop_phone'])): ?>
                <div style="display:flex; gap: 12px; flex-wrap: wrap; align-items:center; color: var(--color-text-muted); font-size: 14px; margin-bottom: var(--spacing-md);">
                    <?php if (!empty($product['shop_email_contact'])): ?>
                        <div>Email: <strong><?php echo htmlspecialchars((string)$product['shop_email_contact']); ?></strong></div>
                    <?php endif; ?>
                    <?php if (!empty($product['shop_phone'])): ?>
                        <div>Tél: <strong><?php echo htmlspecialchars((string)$product['shop_phone']); ?></strong></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div style="display:flex; align-items:baseline; gap:10px; margin: var(--spacing-md) 0 var(--spacing-lg) 0;">
                <div style="font-size:30px; font-weight:900; color: var(--color-primary);">
                    <?php echo htmlspecialchars(number_format($hasPromo ? $promo : $price, 0, ',', ' ')); ?> <?php echo htmlspecialchars($currency); ?>
                </div>
                <?php if ($hasPromo): ?>
                    <div style="font-size:14px; color: var(--color-text-muted); text-decoration: line-through;">
                        <?php echo htmlspecialchars(number_format($price, 0, ',', ' ')); ?> <?php echo htmlspecialchars($currency); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="color: var(--color-text-muted); font-size: 14px; margin: -10px 0 var(--spacing-lg) 0;">
                <span style="font-weight: 800;">Min:</span>
                <?php echo (int)$minOrderQty; ?>
                <span style="margin-left: 10px;">•</span>
                <span style="margin-left: 10px;"><span style="font-weight: 800;">Prix:</span> par unité</span>
            </div>

            <?php if (!empty($product['description'])): ?>
                <div style="color: var(--color-text); line-height:1.8; margin-bottom: var(--spacing-lg);">
                    <?php echo nl2br(htmlspecialchars((string)$product['description'])); ?>
                </div>
            <?php endif; ?>

            <div style="display:grid; gap:10px; background: var(--color-bg); border: 1px solid rgba(0,0,0,0.08); border-radius: var(--radius-lg); padding: var(--spacing-md);">
                <?php if (!empty($product['category'])): ?>
                    <div style="display:flex; justify-content:space-between; gap:10px;"><span style="color: var(--color-text-muted);">Catégorie</span><strong><?php echo htmlspecialchars((string)$product['category']); ?></strong></div>
                <?php endif; ?>
                <?php if (!empty($product['brand'])): ?>
                    <div style="display:flex; justify-content:space-between; gap:10px;"><span style="color: var(--color-text-muted);">Marque</span><strong><?php echo htmlspecialchars((string)$product['brand']); ?></strong></div>
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; gap:10px;"><span style="color: var(--color-text-muted);">Quantité minimale</span><strong><?php echo (int)$minOrderQty; ?></strong></div>
                <div style="display:flex; justify-content:space-between; gap:10px;"><span style="color: var(--color-text-muted);">Stock</span><strong><?php echo (int)($product['stock'] ?? 0); ?></strong></div>
            </div>

            <?php if (!empty($variants)): ?>
                <div style="margin-top: var(--spacing-xl);">
                    <div style="font-weight:800; margin-bottom:10px;">Variantes</div>
                    <div style="display:grid; gap:10px;">
                        <?php foreach ($variants as $v): ?>
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; padding:12px; border: 1px solid rgba(0,0,0,0.08); border-radius:12px; background: var(--color-bg);">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <?php if (!empty($v['color_hex'])): ?>
                                        <span style="width:16px; height:16px; border-radius:50%; background: <?php echo htmlspecialchars((string)$v['color_hex']); ?>; border: 1px solid rgba(0,0,0,0.15);"></span>
                                    <?php endif; ?>
                                    <div>
                                        <div style="font-weight:700;"><?php echo htmlspecialchars((string)($v['variant_name'] ?? 'Variante')); ?></div>
                                        <div style="font-size:12px; color: var(--color-text-muted);">Stock: <?php echo (int)($v['stock'] ?? 0); ?></div>
                                    </div>
                                </div>
                                <?php if (!empty($v['additional_price']) && (float)$v['additional_price'] != 0.0): ?>
                                    <div style="font-weight:800; color: var(--color-primary);">+<?php echo htmlspecialchars(number_format((float)$v['additional_price'], 0, ',', ' ')); ?> <?php echo htmlspecialchars($currency); ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div id="pd-qty-modal" class="pd-qty-overlay" aria-hidden="true">
    <div class="pd-qty-card" role="dialog" aria-modal="true" aria-label="Quantité">
        <div class="pd-qty-head">
            <div class="pd-qty-title"><i class="fas fa-cart-shopping" style="margin-right: 8px;"></i>Quantité</div>
            <button type="button" class="pd-qty-close" onclick="pdCloseQtyModal()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="pd-qty-body">
            <div style="font-size: 12px; color: var(--color-text-muted);">Choisis le nombre d'unités à commander (min: <?php echo (int)$minOrderQty; ?>).</div>
            <div class="pd-qty-row">
                <input id="pd-qty-input" class="pd-qty-input" type="number" min="<?php echo (int)$minOrderQty; ?>" step="1" value="<?php echo (int)$minOrderQty; ?>" />
            </div>
            <div class="pd-qty-actions">
                <button type="button" class="btn btn-ghost btn-sm" onclick="pdCloseQtyModal()">Annuler</button>
                <button id="pd-qty-confirm" type="button" class="btn btn-primary btn-sm" onclick="pdConfirmQty()">Ajouter</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.BASE_URL = '<?php echo BASE_URL; ?>';
    window.PD_MIN_ORDER_QTY = <?php echo (int)$minOrderQty; ?>;

    let pdPendingProductId = null;

    function pdOpenQtyModal(productId) {
        pdPendingProductId = productId;
        const overlay = document.getElementById('pd-qty-modal');
        const input = document.getElementById('pd-qty-input');
        const minQty = Math.max(1, parseInt(window.PD_MIN_ORDER_QTY || '1', 10) || 1);
        if (input) {
            input.min = String(minQty);
            input.value = String(minQty);
        }
        if (overlay) {
            overlay.classList.add('active');
            overlay.setAttribute('aria-hidden', 'false');
        }
        setTimeout(() => { if (input) input.focus(); }, 0);
    }

    function pdCloseQtyModal() {
        const overlay = document.getElementById('pd-qty-modal');
        if (overlay) {
            overlay.classList.remove('active');
            overlay.setAttribute('aria-hidden', 'true');
        }
        pdPendingProductId = null;
    }

    async function pdConfirmQty() {
        const qtyInput = document.getElementById('pd-qty-input');
        const minQty = Math.max(1, parseInt(window.PD_MIN_ORDER_QTY || '1', 10) || 1);
        const qty = Math.max(minQty, parseInt((qtyInput && qtyInput.value) ? qtyInput.value : String(minQty), 10) || minQty);
        const productId = pdPendingProductId;
        if (!productId) return;

        const btn = document.getElementById('pd-qty-confirm');
        const oldText = btn ? btn.textContent : '';
        if (btn) {
            btn.disabled = true;
            btn.textContent = '...';
        }

        try {
            await pdCreateOrder(productId, qty);
            pdCloseQtyModal();
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.textContent = oldText;
            }
        }
    }

    document.addEventListener('keydown', (e) => {
        const overlay = document.getElementById('pd-qty-modal');
        if (!overlay || !overlay.classList.contains('active')) return;
        if (e.key === 'Escape') {
            pdCloseQtyModal();
        }
        if (e.key === 'Enter') {
            e.preventDefault();
            pdConfirmQty();
        }
    });

    document.addEventListener('click', (e) => {
        const overlay = document.getElementById('pd-qty-modal');
        if (!overlay || !overlay.classList.contains('active')) return;
        if (e.target === overlay) {
            pdCloseQtyModal();
        }
    });

    async function pdCreateOrder(productId, quantity) {
        try {
            const res = await fetch(`${window.BASE_URL}/api/create_order.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, quantity: quantity })
            });

            if (res.status === 401) {
                window.location.href = `${window.BASE_URL}/index.php?page=login`;
                return;
            }

            const data = await res.json();
            if (data && data.success) {
                alert(`Commande #${data.order_id} créée`);
                return;
            }

            alert((data && data.error) ? data.error : 'Erreur lors de la création de la commande');
        } catch (e) {
            console.error(e);
            alert('Erreur réseau');
        }
    }
</script>

<?php return; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>i shopping - Classic Leather Tote</title>
    <style>
        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f8f8f8;
            color: #333;
            line-height: 1.6;
            padding-bottom: 40px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
        }

        .logo span {
            color: #e74c3c;
        }

        .nav-icons {
            display: flex;
            gap: 20px;
        }

        .nav-icons i {
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .nav-icons i:hover {
            color: #e74c3c;
        }

        /* Section produit principal */
        .product-main {
            display: flex;
            gap: 60px;
            margin-bottom: 60px;
        }

        .product-images {
            flex: 1;
        }

        .main-image {
            width: 100%;
            height: 500px;
            margin-bottom: 20px;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #8B4513, #A0522D);
        }

        .bag-design {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 350px;
            background: linear-gradient(135deg, #D2691E, #CD853F);
            border-radius: 15px;
            border: 3px solid rgba(139, 69, 19, 0.8);
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .bag-handle {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 25px;
            background: linear-gradient(135deg, #8B4513, #A0522D);
            border-radius: 15px;
            border: 2px solid #5D4037;
        }

        .bag-pocket {
            position: absolute;
            top: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 200px;
            height: 180px;
            background: linear-gradient(135deg, #CD853F, #D2691E);
            border-radius: 10px;
            border: 2px solid #8B4513;
        }

        .bag-strap {
            position: absolute;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: 180px;
            height: 15px;
            background: linear-gradient(135deg, #8B4513, #A0522D);
            border-radius: 8px;
        }

        .thumbnail-images {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .thumbnail {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            overflow: hidden;
            position: relative;
        }

        .thumbnail:hover {
            transform: scale(1.1);
            border-color: #e74c3c;
        }

        .thumbnail.active {
            border-color: #e74c3c;
            transform: scale(1.05);
        }

        .thumbnail-1 { background: linear-gradient(135deg, #8B4513, #A0522D); }
        .thumbnail-2 { background: linear-gradient(135deg, #2F4F4F, #708090); }
        .thumbnail-3 { background: linear-gradient(135deg, #000000, #333333); }
        .thumbnail-4 { background: linear-gradient(135deg, #D2691E, #CD853F); }

        .product-info {
            flex: 1;
        }

        .product-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }

        .product-subtitle {
            font-size: 20px;
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .product-price {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #000;
        }

        .product-description {
            margin-bottom: 30px;
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }

        .product-details {
            margin-bottom: 30px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eee;
        }

        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #000;
        }

        .detail-value {
            color: #666;
        }

        .product-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 15px 35px;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-primary {
            background-color: #000;
            color: #fff;
            flex: 2;
        }

        .btn-secondary {
            background-color: transparent;
            border: 2px solid #000;
            color: #000;
            flex: 1;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-primary:hover {
            background-color: #e74c3c;
        }

        .btn-secondary:hover {
            background-color: #000;
            color: #fff;
        }

        /* Section produits similaires */
        .similar-products {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #000;
            border-bottom: 2px solid #e74c3c;
            padding-bottom: 10px;
            display: inline-block;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .product-card-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        /* Produit 1 - Sac à main élégant */
        .product-card-1 { background: linear-gradient(135deg, #8B4513, #A0522D); }
        .fashion-bag {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 80px;
            background: linear-gradient(135deg, #D2691E, #CD853F);
            border-radius: 10px;
            border: 2px solid #8B4513;
        }
        .fashion-handle {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 8px;
            background: #8B4513;
            border-radius: 4px;
        }

        /* Produit 2 - Sac à dos */
        .product-card-2 { background: linear-gradient(135deg, #2F4F4F, #708090); }
        .backpack {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 120px;
            background: linear-gradient(135deg, #696969, #808080);
            border-radius: 8px;
            border: 2px solid #2F4F4F;
        }
        .backpack-straps {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 40px;
            border: 3px solid #2F4F4F;
            border-bottom: none;
            border-radius: 20px 20px 0 0;
        }

        /* Produit 3 - Sac design */
        .product-card-3 { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .designer-bag {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            height: 120px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px 5px 15px 5px;
            border: 2px solid #4facfe;
        }
        .designer-strap {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #4facfe;
            border-radius: 2px;
        }

        /* Produit 4 - Mini sac */
        .product-card-4 { background: linear-gradient(135deg, #43e97b, #38f9d7); }
        .mini-bag {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 60px;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            border-radius: 8px;
            border: 2px solid #43e97b;
        }
        .mini-strap {
            position: absolute;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #43e97b;
            border-radius: 1.5px;
        }

        .product-card-info {
            padding: 20px;
        }

        .product-card-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
            font-size: 16px;
        }

        .product-card-description {
            font-size: 14px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .product-card-price {
            font-weight: bold;
            color: #e74c3c;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .product-main {
                flex-direction: column;
                gap: 30px;
            }
            
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .thumbnail-images {
                flex-wrap: wrap;
            }
            
            .main-image {
                height: 400px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
            <div class="logo">i <span>shopping</span></div>
            <div class="nav-icons">
                <i class="fas fa-search"></i>
                <i class="fas fa-shopping-bag"></i>
                <i class="fas fa-user"></i>
            </div>
        </header>

        <!-- Section produit principal -->
        <section class="product-main">
            <div class="product-images">
                <div class="main-image" id="mainImage">
                    <div class="bag-design">
                        <div class="bag-handle"></div>
                        <div class="bag-strap"></div>
                        <div class="bag-pocket"></div>
                    </div>
                </div>
                <div class="thumbnail-images">
                    <div class="thumbnail thumbnail-1 active" data-color="brown"></div>
                    <div class="thumbnail thumbnail-2" data-color="gray"></div>
                    <div class="thumbnail thumbnail-3" data-color="black"></div>
                    <div class="thumbnail thumbnail-4" data-color="tan"></div>
                </div>
            </div>
            <div class="product-info">
                <h1 class="product-title">CLASSIC LEATHER TOTE</h1>
                <h2 class="product-subtitle">$299.00</h2>
                <p class="product-price">$299.00</p>
                <p class="product-description">
                    Crafted of the premium Italian leather, sports and camping options, and hourly use.
                </p>
                <div class="product-details">
                    <div class="detail-item">
                        <span class="detail-label">Dimensions:</span>
                        <span class="detail-value">26" x 18" x 12"</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Material:</span>
                        <span class="detail-value">Premium Italian Leather</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Color:</span>
                        <span class="detail-value" id="currentColor">Brown</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Style:</span>
                        <span class="detail-value">Tote Bag</span>
                    </div>
                </div>
                <div class="product-actions">
                    <button class="btn btn-primary">ADD TO CART</button>
                    <button class="btn btn-secondary">ADD TO WISHLIST</button>
                </div>
            </div>
        </section>

        <!-- Section produits similaires -->
        <section class="similar-products">
            <h2 class="section-title">SIMILAR PRODUCTS</h2>
            <div class="product-grid">
                <!-- Produit 1 -->
                <div class="product-card">
                    <div class="product-card-image product-card-1">
                        <div class="fashion-bag">
                            <div class="fashion-handle"></div>
                        </div>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">SOLO BY: FASHIONETA BOUTIQUE</h3>
                        <p class="product-card-description">
                            Your destination is now a new design in India.
                        </p>
                        <p class="product-card-price">$211.00</p>
                    </div>
                </div>

                <!-- Produit 2 -->
                <div class="product-card">
                    <div class="product-card-image product-card-2">
                        <div class="backpack">
                            <div class="backpack-straps"></div>
                        </div>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">LEATHER BACKPACK</h3>
                        <p class="product-card-description">
                            Premium leather backpack for daily use and travel.
                        </p>
                        <p class="product-card-price">$189.00</p>
                    </div>
                </div>

                <!-- Produit 3 -->
                <div class="product-card">
                    <div class="product-card-image product-card-3">
                        <div class="designer-bag">
                            <div class="designer-strap"></div>
                        </div>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">DESIGNER HANDBAG</h3>
                        <p class="product-card-description">
                            Elegant handbag perfect for special occasions.
                        </p>
                        <p class="product-card-price">$349.00</p>
                    </div>
                </div>

                <!-- Produit 4 -->
                <div class="product-card">
                    <div class="product-card-image product-card-4">
                        <div class="mini-bag">
                            <div class="mini-strap"></div>
                        </div>
                    </div>
                    <div class="product-card-info">
                        <h3 class="product-card-title">MINI CROSSBODY</h3>
                        <p class="product-card-description">
                            Compact crossbody bag for your daily essentials.
                        </p>
                        <p class="product-card-price">$159.00</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Script pour la galerie d'images
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainImage = document.getElementById('mainImage');
            const currentColor = document.getElementById('currentColor');
            
            // Couleurs disponibles
            const colorMap = {
                'brown': { 
                    name: 'Brown', 
                    gradient: 'linear-gradient(135deg, #8B4513, #A0522D)',
                    thumbnail: 'thumbnail-1'
                },
                'gray': { 
                    name: 'Gray', 
                    gradient: 'linear-gradient(135deg, #2F4F4F, #708090)',
                    thumbnail: 'thumbnail-2'
                },
                'black': { 
                    name: 'Black', 
                    gradient: 'linear-gradient(135deg, #000000, #333333)',
                    thumbnail: 'thumbnail-3'
                },
                'tan': { 
                    name: 'Tan', 
                    gradient: 'linear-gradient(135deg, #D2691E, #CD853F)',
                    thumbnail: 'thumbnail-4'
                }
            };
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    const color = this.getAttribute('data-color');
                    const colorData = colorMap[color];
                    
                    // Mettre à jour l'image principale
                    mainImage.style.background = colorData.gradient;
                    
                    // Mettre à jour la couleur actuelle
                    currentColor.textContent = colorData.name;
                    
                    // Mettre à jour l'état actif des miniatures
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Script pour les boutons d'action
            const addToCartBtn = document.querySelector('.btn-primary');
            const addToWishlistBtn = document.querySelector('.btn-secondary');
            
            addToCartBtn.addEventListener('click', function() {
                const originalText = this.textContent;
                this.textContent = 'ADDED TO CART!';
                this.style.background = '#27ae60';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '#000';
                }, 2000);
            });
            
            addToWishlistBtn.addEventListener('click', function() {
                const originalText = this.textContent;
                this.textContent = 'ADDED TO WISHLIST!';
                this.style.background = '#e74c3c';
                this.style.borderColor = '#e74c3c';
                this.style.color = '#fff';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = 'transparent';
                    this.style.borderColor = '#000';
                    this.style.color = '#000';
                }, 2000);
            });
            
            // Script pour les produits similaires
            const productCards = document.querySelectorAll('.product-card');
            
            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    const productName = this.querySelector('.product-card-title').textContent;
                    const productPrice = this.querySelector('.product-card-price').textContent;
                    
                    // Animation de sélection
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'translateY(-8px)';
                    }, 150);
                    
                    alert(`Vous avez sélectionné: ${productName} - ${productPrice}`);
                });
            });
        });
    </script>
</body>
</html>