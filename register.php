<?php
require 'config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$err = $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $e = trim($_POST['email'] ?? '');
    $p = $_POST['password'] ?? '';

    if (strlen($u) < 3) {
        $err = "Username minimum 3 characters ka hona chahiye";
    } elseif (!filter_var($e, FILTER_VALIDATE_EMAIL)) {
        $err = "Email sahi nahi hai";
    } elseif (strlen($p) < 6) {
        $err = "Password minimum 6 characters ka hona chahiye";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$u, $e]);
        if ($stmt->fetch()) {
            $err = "Username ya email pehle se registered hai";
        } else {
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $ins->execute([$u, $e, $hash]);
            $ok = "Account ban gaya! Ab login karo.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sign Up — Rohan Mods Portal</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topnav"><div class="topnav-inner">
  <div class="brand">ROHAN MODS PORTAL</div>
</div></div>

<div class="wrap">
  <div class="card">
    <div class="card-eyebrow">✨ Create Account</div>

    <form method="post">
      <label class="field-label">Username</label>
      <input type="text" name="username" required minlength="3" autocomplete="username">

      <label class="field-label">Email</label>
      <input type="email" name="email" required autocomplete="email">

      <label class="field-label">Password</label>
      <input type="password" name="password" required minlength="6" autocomplete="new-password">

      <button type="submit" class="btn btn-primary">Sign Up</button>
    </form>

    <?php if ($err): ?>
      <div class="status-line err"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <?php if ($ok): ?>
      <div class="status-line ok"><?= htmlspecialchars($ok) ?></div>
    <?php endif; ?>

    <div class="link-row">
      Pehle se account hai? <a href="login.php">Login</a>
    </div>
  </div>
</div>

</body>
</html>
