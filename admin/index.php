<?php
require_once __DIR__ . '/includes/auth.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$u]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($p, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "Galat admin credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login — Rohan Mods Portal</title>
<link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">🛡️ ADMIN PANEL</div>
</div></div>

<div class="wrap">
  <div class="card">
    <div class="card-eyebrow">🔐 Admin Login</div>

    <form method="post">
      <label class="field-label">Username</label>
      <input type="text" name="username" required autofocus autocomplete="username">

      <label class="field-label">Password</label>
      <input type="password" name="password" required autocomplete="current-password">

      <button type="submit" class="btn btn-primary">Login as Admin</button>
    </form>

    <?php if ($err): ?>
      <div class="status-line err"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
