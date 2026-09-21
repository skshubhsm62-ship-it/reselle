<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requireAdmin();

// Status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")
        ->execute([$_POST['status'], (int)$_POST['id']]);
    header("Location: orders.php");
    exit;
}

$orders = $pdo->query("SELECT o.*, u.username, p.name AS product_name
                       FROM orders o
                       JOIN users u ON u.id = o.user_id
                       JOIN products p ON p.id = o.product_id
                       ORDER BY o.id DESC")->fetchAll();

adminHeader('Orders', 'orders');
?>

<div class="card">
  <div class="card-eyebrow">🧾 All Orders (<?= count($orders) ?>)</div>

  <?php if (!$orders): ?>
    <div style="color:var(--muted);text-align:center;padding:16px;font-size:13px">
      Abhi tak koi order nahi hua.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>User</th><th>Product</th><th>Amount</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['username']) ?></td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td>₹<?= number_format($o['price'], 2) ?></td>
            <td>
              <form method="post" style="display:inline">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                <select name="status" onchange="this.form.submit()"
                        style="padding:4px 8px;font-size:11px;border-radius:6px;border:1px solid var(--border);background:var(--bg2)">
                  <?php foreach (['pending','completed','failed'] as $s): ?>
                    <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>>
                      <?= $s ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </form>
            </td>
            <td style="font-size:11px;color:var(--muted);font-family:var(--font-mono);white-space:nowrap">
              <?= date('d M', strtotime($o['created_at'])) ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php adminFooter(); ?>
