<?php
require_once __DIR__ . '/../config.php';

$data = $_SESSION['view_data'] ?? [];
$title = (string)($data['title'] ?? ('Compte suspendu - ' . APP_NAME));
$until = $data['suspended_until'] ?? null;
$untilPretty = '';
if ($until) {
    $ts = strtotime((string)$until);
    if ($ts) {
        $untilPretty = date('d/m/Y H:i', $ts);
    }
}
?>

<main class="main-content container" style="max-width: 720px; padding: var(--spacing-2xl) 0;">
    <div class="card" style="padding: var(--spacing-xl);">
        <h1 style="margin-bottom: var(--spacing-md);"><?php echo htmlspecialchars($title); ?></h1>
        <p style="color: var(--color-text-muted); margin-bottom: var(--spacing-md);">
            Votre compte est actuellement suspendu.
        </p>
        <?php if ($untilPretty !== ''): ?>
            <p style="margin-bottom: var(--spacing-md);">
                Fin de suspension prévue le <strong><?php echo htmlspecialchars($untilPretty); ?></strong>.
            </p>
        <?php else: ?>
            <p style="margin-bottom: var(--spacing-md);">
                Suspension à durée indéterminée.
            </p>
        <?php endif; ?>
        <div style="display:flex; gap: var(--spacing-md); flex-wrap: wrap;">
            <a class="btn btn-outline" href="<?php echo url('logout'); ?>">Déconnexion</a>
            <a class="btn btn-primary" href="<?php echo url('home'); ?>">Retour accueil</a>
        </div>
    </div>
</main>
