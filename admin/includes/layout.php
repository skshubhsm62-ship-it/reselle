<?php
function adminHeader($title = 'Admin', $active = '') {
    $menu = [
        'dashboard' => ['📊', 'Dashboard', 'dashboard.php'],
        'users'     => ['👥', 'Users', 'users.php'],
        'products'  => ['📦', 'Products', 'products.php'],
        'orders'    => ['🧾', 'Orders', 'orders.php'],
    ];
    ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title) ?> — Admin Panel</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">🛡️ ADMIN PANEL</div>
  <a href="logout.php" style="font-size:12px;color:var(--red);font-weight:700">Logout</a>
</div></div>

<div class="admin-nav">
  <?php foreach ($menu as $key => $item): ?>
    <a href="<?= $item[2] ?>" class="<?= $active === $key ? 'active' : '' ?>">
      <span><?= $item[0] ?></span> <?= $item[1] ?>
    </a>
  <?php endforeach; ?>
</div>

<div class="wrap">
<?php
}

function adminFooter() {
    ?>
</div>
</body>
</html>
<?php
}
?>
