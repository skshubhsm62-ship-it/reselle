<?php
require 'config.php';
requireLogin();

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>All Products — Rohan Mods Portal</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">ALL PRODUCTS</div>
  <a href="dashboard.php" style="font-size:12px;color:var(--gold);font-weight:700">← Back</a>
</div></div>

<div class="wrap">

  <div class="card">
    <div class="card-eyebrow">📦 Available Products (<?= count($products) ?>)</div>

    <?php if (!$products): ?>
      <div style="color:var(--muted);text-align:center;padding:16px;font-size:13px">
        Abhi koi product available nahi hai.
      </div>
    <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="product-item">
          <div>
            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="product-meta">
              <?= htmlspecialchars($p['category']) ?> · Stock: <?= $p['stock'] ?>
            </div>
          </div>
          <div class="product-price">₹<?= number_format($p['price'], 2) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</div>

</body>
</html>
