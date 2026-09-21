<?php
require 'config.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard — Rohan Mods Portal</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">ROHAN MODS PORTAL</div>
  <a href="logout.php" style="font-size:12px;color:var(--red);font-weight:700">Logout</a>
</div></div>

<div class="wrap">

  <div class="wallet-card">
    <div>
      <div style="font-size:12px;color:var(--gold-dark);font-weight:600">👛 Wallet Balance</div>
      <b>₹ <?= number_format($user['wallet'], 2) ?></b>
    </div>
    <a href="#" class="btn btn-primary" style="width:auto;padding:8px 14px;margin:0;font-size:12px">+ Add Money</a>
  </div>

  <div class="card">
    <div class="card-eyebrow">👋 Welcome, <?= htmlspecialchars($user['username']) ?></div>
    <p style="font-size:13.5px;color:var(--muted);line-height:1.5">
      Yahan se aap products browse kar sakte ho aur apna wallet manage kar sakte ho.
    </p>
  </div>

  <div class="card">
    <div class="card-eyebrow">🔥 Latest Products</div>

    <?php if (!$products): ?>
      <div style="color:var(--muted);text-align:center;padding:16px;font-size:13px">
        Abhi koi product available nahi hai.
      </div>
    <?php else: ?>
      <?php foreach ($products as $p): ?>
        <div class="product-item">
          <div>
            <div class="product-name"><?= htmlspecialchars($p['name']) ?></div>
            <div class="product-meta"><?= htmlspecialchars($p['category']) ?> · Stock: <?= $p['stock'] ?></div>
          </div>
          <div class="product-price">₹<?= number_format($p['price'], 2) ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <a href="products.php" class="btn btn-ghost" style="margin-top:14px">View All Products →</a>
  </div>

</div>

</body>
</html>
