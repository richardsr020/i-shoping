<?php
$data = $_SESSION['view_data'] ?? [];
$notifications = $data['notifications'] ?? [];
?>

<div class="card">
  <h2 style="margin-top:0;">Notifications</h2>
  <?php if (empty($notifications)): ?>
    <div style="color: var(--gray-dark);">Aucune notification.</div>
  <?php else: ?>
    <div style="display:grid;gap:10px;">
      <?php foreach ($notifications as $n): ?>
        <div style="border:1px solid var(--dashboard-border);border-radius:10px;padding:12px;">
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
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
