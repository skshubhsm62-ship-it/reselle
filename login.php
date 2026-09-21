<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $err = "Galat username ya password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login — Rohan Mods Portal</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">ROHAN MODS PORTAL</div>
</div></div>

<div class="wrap">
  <div class="card">
    <div class="card-eyebrow">🔐 Secure Login</div>

    <form method="post">
      <label class="field-label">Username / Email</label>
      <input type="text" name="username" required autocomplete="username" autofocus>

      <label class="field-label">Password</label>
      <input type="password" name="password" required autocomplete="current-password">

      <button type="submit" class="btn btn-primary">Sign In</button>
    </form>

    <?php if ($err): ?>
      <div class="status-line err"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <div class="link-row">
      Account nahi hai? <a href="register.php">Sign Up</a>
    </div>
  </div>
</div>

</body>
</html>
