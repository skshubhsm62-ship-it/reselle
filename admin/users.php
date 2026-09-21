<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requireAdmin();

$msg = '';

// Wallet adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_wallet') {
    $uid    = (int)$_POST['user_id'];
    $amount = (float)$_POST['amount'];
    if ($amount != 0) {
        $pdo->prepare("UPDATE users SET wallet = wallet + ? WHERE id = ?")->execute([$amount, $uid]);
        $pdo->prepare("INSERT INTO transactions (user_id, amount, type, note) VALUES (?,?,?,?)")
            ->execute([$uid, abs($amount), $amount > 0 ? 'credit' : 'debit', 'Admin adjustment']);
        $msg = "Wallet updated for user #$uid";
    }
}

// Delete user
if (($_GET['delete'] ?? '') !== '') {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: users.php");
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();

adminHeader('Users', 'users');
?>

<?php if ($msg): ?>
  <div class="card" style="border-color:var(--green);color:var(--green);font-weight:700;font-size:13px">
    ✅ <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-eyebrow">👥 All Users (<?= count($users) ?>)</div>

  <?php if (!$users): ?>
    <div style="color:var(--muted);text-align:center;padding:16px;font-size:13px">
      Koi user nahi hai.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>User</th><th>Email</th><th>Wallet</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><b><?= htmlspecialchars($u['username']) ?></b></td>
            <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($u['email']) ?></td>
            <td>₹<?= number_format($u['wallet'], 2) ?></td>
            <td style="white-space:nowrap">
              <button class="btn-sm btn-sm-primary"
                onclick="openWallet(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username'], ENT_QUOTES) ?>')">💰</button>
              <a class="btn-sm btn-sm-red" href="?delete=<?= $u['id'] ?>"
                 onclick="return confirm('User delete karna hai?')">🗑</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Wallet modal -->
<div id="walletModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:100;align-items:center;justify-content:center;padding:20px">
  <div class="card" style="max-width:340px;width:100%">
    <div class="card-eyebrow">💰 Adjust Wallet</div>
    <form method="post">
      <input type="hidden" name="action" value="add_wallet">
      <input type="hidden" name="user_id" id="walletUserId">
      <div style="font-size:13px;color:var(--muted);margin-bottom:10px">
        User: <b id="walletUserName" style="color:var(--gold-dark)"></b>
      </div>
      <label class="field-label">Amount (+ credit / - debit)</label>
      <input type="number" name="amount" step="0.01" required placeholder="e.g. 100 ya -50">
      <button class="btn btn-primary" type="submit" style="margin-top:12px">Apply</button>
      <button class="btn btn-ghost" type="button" onclick="closeWallet()">Cancel</button>
    </form>
  </div>
</div>

<script>
function openWallet(id, name) {
  document.getElementById('walletUserId').value = id;
  document.getElementById('walletUserName').textContent = name;
  document.getElementById('walletModal').style.display = 'flex';
}
function closeWallet() {
  document.getElementById('walletModal').style.display = 'none';
}
</script>

<?php adminFooter(); ?>
