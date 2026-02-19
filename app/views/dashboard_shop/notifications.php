<?php
$data = $_SESSION['view_data'] ?? [];
$notifications = $data['notifications'] ?? [];
?>

<div class="card">
  <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;">
    <h2 style="margin:0;">Notifications</h2>
    <button id="dashboard-mark-all-read" class="btn btn-secondary" type="button">Tout marquer lu</button>
  </div>
  <div id="dashboard-notification-list" style="display:grid;gap:10px;">
    <?php if (empty($notifications)): ?>
      <div id="dashboard-notification-empty" style="color: var(--gray-dark);">Aucune notification.</div>
    <?php else: ?>
      <?php foreach ($notifications as $n): ?>
        <?php
        $nid = (int)($n['id'] ?? 0);
        $isRead = (int)($n['is_read'] ?? 0) === 1;
        ?>
        <article
          class="dashboard-notification-item <?php echo $isRead ? '' : 'unread'; ?>"
          data-notification-id="<?php echo $nid; ?>"
          style="border:1px solid var(--dashboard-border);border-radius:10px;padding:12px;cursor:pointer;"
        >
          <div style="font-weight:800;"><?php echo htmlspecialchars((string)($n['title'] ?? '')); ?></div>
          <?php if (!empty($n['body'])): ?>
            <div style="color: var(--gray-dark);font-size:13px;margin-top:4px;"><?php echo htmlspecialchars((string)$n['body']); ?></div>
          <?php endif; ?>
          <div style="color: var(--gray-dark);font-size:12px;margin-top:6px;">
            <?php echo htmlspecialchars((string)($n['type'] ?? '')); ?>
            <?php if (!empty($n['created_at'])): ?>
              · <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime((string)$n['created_at']))); ?>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>
