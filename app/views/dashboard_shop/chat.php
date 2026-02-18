<?php
$chatUrl = url('chat');
?>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="display:flex; align-items:center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--dashboard-border);">
        <div style="font-weight: 800;"><i class="fas fa-comments" style="margin-right: 10px;"></i>Messagerie</div>
        <a class="btn btn-ghost btn-sm" href="<?php echo $chatUrl; ?>" target="_blank" rel="noopener">Ouvrir en plein écran</a>
    </div>
    <iframe
        title="Messagerie"
        src="<?php echo htmlspecialchars($chatUrl); ?>"
        style="width:100%; height: calc(100vh - 260px); border: 0; display:block; background: var(--dashboard-surface);"
    ></iframe>
</div>
