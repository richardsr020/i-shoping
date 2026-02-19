<?php
require_once __DIR__ . '/../config.php';

$data = $_SESSION['view_data'] ?? [];
$shop = $data['shop'] ?? null;
$products = $data['products'] ?? [];

$currency = (string)($shop['currency'] ?? 'XOF');

$shopPm = [];
if ($shop && !empty($shop['payment_methods_json'])) {
    $decoded = json_decode((string)$shop['payment_methods_json'], true);
    if (is_array($decoded)) {
        $shopPm = $decoded;
    }
}

if (!$shop) {
    echo '<main class="main-content container"><h2>Boutique introuvable</h2></main>';
    return;
}

$resolveImageUrl = static function ($path): string {
    $path = trim((string)$path);
    if ($path === '') {
        return '';
    }
    if (preg_match('/^https?:\/\//i', $path)) {
        return $path;
    }
    if (strpos($path, '/') === 0) {
        return rtrim((string)BASE_URL, '/') . $path;
    }
    return rtrim((string)BASE_URL, '/') . '/' . ltrim($path, '/');
};

$shopBannerUrl = $resolveImageUrl((string)($shop['banner'] ?? ''));
$shopLogoUrl = $resolveImageUrl((string)($shop['logo'] ?? ''));
?>

<main class="main-content container shop-main-content" style="padding-top: var(--spacing-lg);">
    <style>
        @media (max-width: 992px) {
            .shop-main-content {
                padding-left: max(clamp(16px, 5.5vw, 28px), env(safe-area-inset-left)) !important;
                padding-right: max(clamp(16px, 5.5vw, 28px), env(safe-area-inset-right)) !important;
            }
        }

        .shop-banner { width: 100%; border-radius: 16px; overflow: hidden; background: var(--color-bg-secondary); box-shadow: var(--shadow-md); }
        .shop-banner-media { position: relative; width: 100%; padding-top: 28%; }
        @media (max-width: 900px) { .shop-banner-media { padding-top: 40%; } }
        .shop-banner-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; display: block; }
    </style>

    <div style="margin-bottom: var(--spacing-lg); display:flex; justify-content:space-between; gap: var(--spacing-md); flex-wrap: wrap; align-items:center;">
        <a href="<?php echo url('home'); ?>" class="btn btn-ghost btn-sm">Retour</a>
        <div style="color: var(--color-text-muted); font-size: 14px;">Boutique</div>
    </div>

    <?php if (!empty($shopBannerUrl)): ?>
        <div class="shop-banner" style="margin-bottom: var(--spacing-lg);">
            <div class="shop-banner-media">
                <img src="<?php echo htmlspecialchars((string)$shopBannerUrl); ?>" alt="Bannière <?php echo htmlspecialchars((string)$shop['name']); ?>" onerror="this.style.display='none'" />
            </div>
        </div>
    <?php endif; ?>

    <div style="display:flex; gap: var(--spacing-lg); align-items:center; flex-wrap: wrap; margin-bottom: var(--spacing-lg);">
        <div style="width: 72px; height: 72px; border-radius: 18px; overflow:hidden; background: var(--color-bg-secondary); flex: 0 0 72px;">
            <?php if (!empty($shopLogoUrl)): ?>
                <img src="<?php echo htmlspecialchars((string)$shopLogoUrl); ?>" alt="<?php echo htmlspecialchars((string)$shop['name']); ?>" style="width:72px; height:72px; object-fit:cover; display:block;" onerror="this.style.display='none'" />
            <?php endif; ?>
        </div>
        <div style="flex: 1; min-width: 240px;">
            <h1 style="margin:0; font-size: 28px; line-height: 1.2;"><?php echo htmlspecialchars((string)$shop['name']); ?></h1>
            <?php if (!empty($shop['description'])): ?>
                <?php
                $descRaw = (string)$shop['description'];
                $descPlain = trim($descRaw);
                $len = function_exists('mb_strlen') ? mb_strlen($descPlain) : strlen($descPlain);
                $cut = (int)floor($len / 2);
                $short = $len > 0 ? (function_exists('mb_substr') ? mb_substr($descPlain, 0, max(1, $cut)) : substr($descPlain, 0, max(1, $cut))) : '';
                $needsToggle = $len > 140;
                ?>

                <div style="margin-top: 6px; color: var(--color-text-muted); line-height: 1.6;">
                    <div id="shop-desc-short" style="display: <?php echo $needsToggle ? 'block' : 'none'; ?>;">
                        <?php echo nl2br(htmlspecialchars($short)); ?>...
                    </div>
                    <div id="shop-desc-full" style="display: <?php echo $needsToggle ? 'none' : 'block'; ?>;">
                        <?php echo nl2br(htmlspecialchars($descPlain)); ?>
                    </div>
                    <?php if ($needsToggle): ?>
                        <button type="button" class="btn btn-ghost btn-sm" style="padding: 6px 10px; font-size: 12px; margin-top: 6px;" onclick="toggleShopDesc()">
                            Voir plus
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div style="margin-top: 10px;"></div>

            <?php if (!empty($shopPm)): ?>
                <div style="margin-top: 10px; display:flex; gap: 10px; flex-wrap: wrap; align-items:center;">
                    <div style="color: var(--color-text-muted); font-size: 13px;">Paiements:</div>
                    <?php
                    $pmMeta = [
                        'orange_money' => ['label' => 'Orange Money', 'icon' => 'fa-wallet'],
                        'mpesa' => ['label' => 'M-Pesa', 'icon' => 'fa-wallet'],
                        'airtel_money' => ['label' => 'Airtel Money', 'icon' => 'fa-wallet'],
                        'crypto_usdt' => ['label' => 'Crypto USDT', 'icon' => 'fa-bitcoin'],
                    ];
                    foreach ($shopPm as $k) {
                        if (!isset($pmMeta[$k])) continue;
                        $m = $pmMeta[$k];
                        echo '<span title="' . htmlspecialchars($m['label']) . '" style="display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; border: 1px solid rgba(0,0,0,0.12); background: var(--color-bg); color: var(--color-text); font-size: 12px;">';
                        echo '<i class="fas ' . htmlspecialchars($m['icon']) . '"></i>';
                        echo htmlspecialchars($m['label']);
                        echo '</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <h2 class="section-title">Produits</h2>
    <?php if (empty($products)): ?>
        <div style="color: var(--color-text-muted);">Aucun produit.</div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
                <?php
                $productImageUrl = $resolveImageUrl((string)($p['image'] ?? ''));
                $productImageSrc = $productImageUrl !== '' ? $productImageUrl : 'https://via.placeholder.com/300';
                ?>
                <div class="product-card">
                    <a href="<?php echo url('product_detail'); ?>&id=<?php echo (int)$p['id']; ?>" style="display:block;">
                        <img class="product-image" src="<?php echo htmlspecialchars((string)$productImageSrc); ?>" alt="<?php echo htmlspecialchars((string)$p['name']); ?>" onerror="this.src='https://via.placeholder.com/300'" />
                    </a>
                    <div class="product-info">
                        <h3 class="product-name" title="<?php echo htmlspecialchars((string)$p['name']); ?>"><?php echo htmlspecialchars((string)$p['name']); ?></h3>
                        <?php
                        $price = (float)($p['price'] ?? 0);
                        $promo = (float)($p['promo_price'] ?? 0);
                        $hasPromo = $promo > 0 && $promo < $price;
                        $minOrderQty = (int)($p['min_order_qty'] ?? 1);
                        if ($minOrderQty <= 0) {
                            $minOrderQty = 1;
                        }
                        ?>
                        <div style="color: var(--color-text-muted); font-size: 12px; margin-bottom: var(--spacing-xs);">
                            <span style="font-weight: 800;">Min:</span> <?php echo (int)$minOrderQty; ?>
                            <span style="margin-left: 8px;">•</span>
                            <span style="margin-left: 8px;">Prix: par unité</span>
                        </div>
                        <div class="product-price-row">
                            <span class="product-price-regular"><?php echo htmlspecialchars(number_format($hasPromo ? $promo : $price, 0, ',', ' ')); ?> <?php echo htmlspecialchars($currency); ?></span>
                            <?php if ($hasPromo): ?>
                                <span class="product-price-old"><?php echo htmlspecialchars(number_format($price, 0, ',', ' ')); ?> <?php echo htmlspecialchars($currency); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-cta-row">
                            <a class="btn btn-cta-discreet" href="<?php echo url('product_detail'); ?>&id=<?php echo (int)$p['id']; ?>">Voir</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    function toggleShopDesc() {
        const shortEl = document.getElementById('shop-desc-short');
        const fullEl = document.getElementById('shop-desc-full');
        const btn = event && event.target ? event.target : null;
        if (!shortEl || !fullEl) return;

        const isShortVisible = shortEl.style.display !== 'none';
        shortEl.style.display = isShortVisible ? 'none' : 'block';
        fullEl.style.display = isShortVisible ? 'block' : 'none';
        if (btn) btn.textContent = isShortVisible ? 'Voir moins' : 'Voir plus';
    }
</script>
