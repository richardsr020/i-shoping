<?php
require_once __DIR__ . '/../config.php';

$data = $_SESSION['view_data'] ?? [];
$shop = $data['shop'] ?? null;
$products = $data['products'] ?? [];

if (!$shop) {
    echo '<main class="main-content container"><h2>Boutique introuvable</h2></main>';
    return;
}
?>

<main class="main-content container" style="padding-top: var(--spacing-lg);">
    <div style="margin-bottom: var(--spacing-lg); display:flex; justify-content:space-between; gap: var(--spacing-md); flex-wrap: wrap; align-items:center;">
        <a href="<?php echo url('home'); ?>" class="btn btn-ghost btn-sm">Retour</a>
        <div style="color: var(--color-text-muted); font-size: 14px;">Boutique</div>
    </div>

    <div style="display:flex; gap: var(--spacing-lg); align-items:center; flex-wrap: wrap; margin-bottom: var(--spacing-lg);">
        <div style="width: 72px; height: 72px; border-radius: 18px; overflow:hidden; background: var(--color-bg-secondary); flex: 0 0 72px;">
            <?php if (!empty($shop['logo'])): ?>
                <img src="<?php echo htmlspecialchars((string)$shop['logo']); ?>" alt="<?php echo htmlspecialchars((string)$shop['name']); ?>" style="width:72px; height:72px; object-fit:cover; display:block;" onerror="this.style.display='none'" />
            <?php endif; ?>
        </div>
        <div style="flex: 1; min-width: 240px;">
            <h1 style="margin:0; font-size: 28px; line-height: 1.2;"><?php echo htmlspecialchars((string)$shop['name']); ?></h1>
            <?php if (!empty($shop['description'])): ?>
                <div style="margin-top: 6px; color: var(--color-text-muted); line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars((string)$shop['description'])); ?>
                </div>
            <?php endif; ?>
            <div style="margin-top: 10px; display:flex; gap: 12px; flex-wrap: wrap; color: var(--color-text-muted); font-size: 14px;">
                <?php if (!empty($shop['email_contact'])): ?>
                    <div>Email: <strong><?php echo htmlspecialchars((string)$shop['email_contact']); ?></strong></div>
                <?php endif; ?>
                <?php if (!empty($shop['phone'])): ?>
                    <div>Tél: <strong><?php echo htmlspecialchars((string)$shop['phone']); ?></strong></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h2 class="section-title">Produits</h2>
    <?php if (empty($products)): ?>
        <div style="color: var(--color-text-muted);">Aucun produit.</div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <a href="<?php echo url('product_detail'); ?>&id=<?php echo (int)$p['id']; ?>" style="display:block;">
                        <img class="product-image" src="<?php echo !empty($p['image']) ? htmlspecialchars((string)$p['image']) : 'https://via.placeholder.com/300'; ?>" alt="<?php echo htmlspecialchars((string)$p['name']); ?>" onerror="this.src='https://via.placeholder.com/300'" />
                    </a>
                    <div class="product-info">
                        <h3 class="product-name" title="<?php echo htmlspecialchars((string)$p['name']); ?>"><?php echo htmlspecialchars((string)$p['name']); ?></h3>
                        <?php
                        $price = (float)($p['price'] ?? 0);
                        $promo = (float)($p['promo_price'] ?? 0);
                        $hasPromo = $promo > 0 && $promo < $price;
                        ?>
                        <div class="product-price-row">
                            <span class="product-price-regular"><?php echo htmlspecialchars(number_format($hasPromo ? $promo : $price, 0, ',', ' ')); ?> XOF</span>
                            <?php if ($hasPromo): ?>
                                <span class="product-price-old"><?php echo htmlspecialchars(number_format($price, 0, ',', ' ')); ?> XOF</span>
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
