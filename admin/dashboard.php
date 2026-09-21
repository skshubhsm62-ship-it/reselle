<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requireAdmin();

$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue  = $pdo->query("SELECT COALESCE(SUM(price),0) FROM orders WHERE status='completed'")->fetchColumn();

$recentOrders = $pdo->query("SELECT o.*, u.username, p.name AS product_name
                              FROM orders o
                              JOIN users u ON u.id = o.user_id
                              JOIN products p ON p.id = o.product_id
                              ORDER BY o.id DESC LIMIT 5")->fetchAll();

adminHeader('Dashboard', 'dashboard');
?>

<div class="card">
  <div class="card-eyebrow">📊 Overview</div>

  <div class="stat-grid">
    <div class="stat-card">
      <div class="stat-icon">👥</div>
      <div class="stat-label">Total Users</div>
      <div class="stat-value"><?= $totalUsers ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📦</div>
      <div class="stat-label">Products</div>
      <div class="stat-value"><?= $totalProducts ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🧾</div>
      <div class="stat-label">Orders</div>
      <div class="stat-value"><?= $totalOrders ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-label">Revenue</div>
      <div class="stat-value">₹<?= number_format($totalRevenue, 0) ?></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-eyebrow">🕐 Recent Orders</div>

  <?php if (!$recentOrders): ?>
    <div style="color:var(--muted);font-size:13px;text-align:center;padding:16px">
      Abhi tak koi order nahi hua.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>User</th><th>Product</th><th>Amount</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['username']) ?></td>
            <td><?= htmlspecialchars($o['product_name']) ?></td>
            <td>₹<?= number_format($o['price'], 0) ?></td>
            <td>
              <?php
                $cls = ['completed'=>'badge-ok','pending'=>'badge-warn','failed'=>'badge-err'][$o['status']] ?? 'badge-warn';
              ?>
              <span class="badge <?= $cls ?>"><?= $o['status'] ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php adminFooter(); ?>
