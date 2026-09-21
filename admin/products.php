<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/layout.php';
requireAdmin();

$msg = '';

// Add product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $name  = trim($_POST['name'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $cat   = trim($_POST['category'] ?? '');
    if ($name !== '' && $price >= 0) {
        $pdo->prepare("INSERT INTO products (name, price, stock, category) VALUES (?,?,?,?)")
            ->execute([$name, $price, $stock, $cat]);
        $msg = "Product add ho gaya!";
    }
}

// Update product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id    = (int)$_POST['id'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $pdo->prepare("UPDATE products SET price = ?, stock = ? WHERE id = ?")->execute([$price, $stock, $id]);
    $msg = "Product update ho gaya!";
}

// Delete
if (($_GET['delete'] ?? '') !== '') {
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([(int)$_GET['delete']]);
    header("Location: products.php");
    exit;
}

$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

adminHeader('Products', 'products');
?>

<?php if ($msg): ?>
  <div class="card" style="border-color:var(--green);color:var(--green);font-weight:700;font-size:13px">
    ✅ <?= htmlspecialchars($msg) ?>
  </div>
<?php endif; ?>

<!-- Add new product -->
<div class="card">
  <div class="card-eyebrow">➕ Add New Product</div>
  <form method="post">
    <input type="hidden" name="action" value="add">

    <label class="field-label">Product Name</label>
    <input type="text" name="name" required>

    <label class="field-label">Price (₹)</label>
    <input type="number" name="price" step="0.01" required>

    <label class="field-label">Stock</label>
    <input type="number" name="stock" value="10" required>

    <label class="field-label">Category</label>
    <input type="text" name="category" placeholder="Android / PC / iOS">

    <button class="btn btn-primary" type="submit">Add Product</button>
  </form>
</div>

<!-- Products list -->
<div class="card">
  <div class="card-eyebrow">📦 All Products (<?= count($products) ?>)</div>

  <?php if (!$products): ?>
    <div style="color:var(--muted);text-align:center;padding:16px;font-size:13px">
      Koi product nahi hai.
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td><b><?= htmlspecialchars($p['name']) ?></b></td>
            <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($p['category']) ?></td>
            <td>₹<?= number_format($p['price'], 2) ?></td>
            <td>
              <span class="badge <?= $p['stock'] > 0 ? 'badge-ok' : 'badge-err' ?>">
                <?= $p['stock'] ?>
              </span>
            </td>
            <td style="white-space:nowrap">
              <button class="btn-sm btn-sm-primary"
                onclick="editProduct(<?= $p['id'] ?>, <?= $p['price'] ?>, <?= $p['stock'] ?>)">✏️</button>
              <a class="btn-sm btn-sm-red" href="?delete=<?= $p['id'] ?>"
                 onclick="return confirm('Delete karna hai?')">🗑</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Edit modal -->
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:100;align-items:center;justify-content:center;padding:20px">
  <div class="card" style="max-width:340px;width:100%">
    <div class="card-eyebrow">✏️ Edit Product #<span id="editIdLabel"></span></div>
    <form method="post">
      <input type="hidden" name="action" value="update">
      <input type="hidden" name="id" id="editId">

      <label class="field-label">Price (₹)</label>
      <input type="number" name="price" id="editPrice" step="0.01" required>

      <label class="field-label">Stock</label>
      <input type="number" name="stock" id="editStock" required>

      <button class="btn btn-primary" type="submit" style="margin-top:12px">Save</button>
      <button class="btn btn-ghost" type="button" onclick="closeEdit()">Cancel</button>
    </form>
  </div>
</div>

<script>
function editProduct(id, price, stock) {
  document.getElementById('editId').value = id;
  document.getElementById('editIdLabel').textContent = id;
  document.getElementById('editPrice').value = price;
  document.getElementById('editStock').value = stock;
  document.getElementById('editModal').style.display = 'flex';
}
function closeEdit() {
  document.getElementById('editModal').style.display = 'none';
}
</script>

<?php adminFooter(); ?>
