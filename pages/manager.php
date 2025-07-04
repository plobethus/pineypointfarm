<?php
// pages/manager.php
session_start();
require __DIR__ . '/../config.php';

// 1) Auth guard
if (empty($_SESSION['admin_user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

// 2) Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: /pages/login.php');
    exit;
}

// 3) Which section?
$action = $_GET['action'] ?? 'dashboard';

// 4) Handle Product Types form submission
if ($action === 'types' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim(filter_input(INPUT_POST, 'type_name', FILTER_SANITIZE_STRING));
    if ($name) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO product_types (name) VALUES (?)");
        $stmt->execute([$name]);
    }
    header('Location: /pages/manager.php?action=types');
    exit;
}

// 5) Handle Products form submission (Add or Update)
if ($action === 'products' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Common fields
    $name    = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $price   = filter_input(INPUT_POST, 'price_usd', FILTER_VALIDATE_FLOAT);
    $details = trim(filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING));
    $stock   = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
    $type_id = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT) ?: null;
    $edit_id = filter_input(INPUT_POST, 'edit_id', FILTER_VALIDATE_INT);

    // Handle image upload
    $imgUrl = null;
    if (!empty($_FILES['image']['tmp_name'])) {
        $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file = uniqid() . ".$ext";
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            __DIR__ . "/../images/products/$file"
        );
        $imgUrl = "/images/products/$file";
    }

    if ($edit_id) {
        // UPDATE existing product
        $sql = "
          UPDATE product
          SET name = ?, price_usd = ?, details = ?, stock = ?, product_type_id = ?,
              image_url = COALESCE(?, image_url)
          WHERE id = ?
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name, $price, $details, $stock, $type_id, $imgUrl, $edit_id
        ]);
    } else {
        // INSERT new product
        $sql = "
          INSERT INTO product
            (name, price_usd, details, stock, product_type_id, image_url)
          VALUES (?, ?, ?, ?, ?, ?)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name, $price, $details, $stock, $type_id, $imgUrl
        ]);
    }

    header('Location: /pages/manager.php?action=products');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manager Dashboard</title>
  <link rel="stylesheet" href="/css/global.css">
  <style>
    /* Quick admin-area styles */
    body { font-family: Arial, sans-serif; }
    header { background: #1A3320; color: #fff; padding: 1rem; }
    nav.admin-nav a { color: #fff; margin-right: 1rem; text-decoration: none; }
    nav.admin-nav a:hover { text-decoration: underline; }
    main { max-width: 900px; margin: 2rem auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
    th,td { border: 1px solid #ddd; padding: 0.5rem; }
    form > div { margin-top: 0.5rem; }
    img.admin-thumb { height: 40px; }
    .action-link { margin-right: 0.5rem; }
  </style>
</head>
<body>
  <header>
    <h1>Manager Dashboard</h1>
    <nav class="admin-nav">
      <a href="?action=dashboard">Home</a>
      <a href="?action=types">Product Types</a>
      <a href="?action=products">Products</a>
      <a href="?action=messages">Contacts</a>
      <a href="?action=logout">Logout</a>
    </nav>
  </header>

  <main>
    <?php if ($action === 'dashboard'): ?>
      <section>
        <h2>Welcome!</h2>
        <p>Use the links above to manage your site content.</p>
      </section>

    <?php elseif ($action === 'types'): 
      // Fetch all types
      $types = $pdo->query("SELECT * FROM product_types ORDER BY name")->fetchAll();
    ?>
      <section>
        <h2>Product Types</h2>
        <form method="post">
          <div>
            <label>New Type:
              <input type="text" name="type_name" required>
            </label>
            <button type="submit">Add Type</button>
          </div>
        </form>
        <table>
          <tr><th>ID</th><th>Name</th></tr>
          <?php foreach ($types as $t): ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td><?= htmlspecialchars($t['name']) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

    <?php elseif ($action === 'products'): 
      // Fetch types for dropdown
      $types = $pdo->query("SELECT * FROM product_types ORDER BY name")->fetchAll();

      // If editing, load that product
      $edit_id     = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
      $editProduct = null;
      if ($edit_id) {
        $stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->execute([$edit_id]);
        $editProduct = $stmt->fetch();
      }

      // Fetch all products
      $products = $pdo->query("
        SELECT p.*, pt.name AS type_name
          FROM product p
          LEFT JOIN product_types pt ON p.product_type_id = pt.id
        ORDER BY p.name
      ")->fetchAll();
    ?>
      <section>
        <h2>Products</h2>

        <!-- Add/Edit Form -->
        <form method="post" enctype="multipart/form-data">
          <?php if ($editProduct): ?>
            <input type="hidden" name="edit_id" value="<?= $editProduct['id'] ?>">
          <?php endif; ?>

          <div>
            <label>Name:
              <input
                type="text"
                name="name"
                required
                value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>"
              >
            </label>
          </div>
          <div>
            <label>Price (USD):
              <input
                type="number"
                step="0.01"
                name="price_usd"
                required
                value="<?= htmlspecialchars($editProduct['price_usd'] ?? '') ?>"
              >
            </label>
          </div>
          <div>
            <label>Details:
              <textarea name="details" rows="3"><?= htmlspecialchars($editProduct['details'] ?? '') ?></textarea>
            </label>
          </div>
          <div>
            <label>Stock:
              <input
                type="number"
                name="stock"
                required
                value="<?= htmlspecialchars($editProduct['stock'] ?? '') ?>"
              >
            </label>
          </div>
          <div>
            <label>Type:
              <select name="type_id">
                <option value="">— none —</option>
                <?php foreach ($types as $t): ?>
                  <option value="<?= $t['id'] ?>"
                    <?= (isset($editProduct['product_type_id']) && $editProduct['product_type_id']==$t['id'])
                       ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
          <div>
            <label>Image:
              <input type="file" name="image" accept="image/*">
            </label>
            <?php if (!empty($editProduct['image_url'])): ?>
              <br>
              <img
                src="<?= htmlspecialchars($editProduct['image_url']) ?>"
                alt=""
                class="admin-thumb"
              >
            <?php endif; ?>
          </div>
          <div>
            <button type="submit">
              <?= $editProduct ? 'Update Product' : 'Add Product' ?>
            </button>
            <?php if ($editProduct): ?>
              <a href="/pages/manager.php?action=products">Cancel</a>
            <?php endif; ?>
          </div>
        </form>

        <!-- Products Table -->
        <table>
          <tr>
            <th>ID</th><th>Name</th><th>Type</th>
            <th>Price</th><th>Stock</th><th>Image</th><th>Actions</th>
          </tr>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['type_name'] ?? '—') ?></td>
              <td>$<?= number_format($p['price_usd'], 2) ?></td>
              <td><?= (int)$p['stock'] ?></td>
              <td>
                <?php if ($p['image_url']): ?>
                  <img src="<?= htmlspecialchars($p['image_url']) ?>"
                       alt=""
                       class="admin-thumb"
                  >
                <?php endif; ?>
              </td>
              <td>
                <a
                  href="?action=products&edit=<?= $p['id'] ?>"
                  class="action-link"
                >Edit</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

    <?php elseif ($action === 'messages'): 
      $msgs = $pdo
        ->query("SELECT * FROM contacts ORDER BY submitted_at DESC")
        ->fetchAll();
    ?>
      <section>
        <h2>Contact Messages</h2>
        <table>
          <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>When</th>
          </tr>
          <?php foreach ($msgs as $m): ?>
            <tr>
              <td><?= $m['id'] ?></td>
              <td><?= htmlspecialchars($m['name']) ?></td>
              <td><?= htmlspecialchars($m['email']) ?></td>
              <td><?= nl2br(htmlspecialchars($m['message'])) ?></td>
              <td><?= $m['submitted_at'] ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

    <?php else: ?>
      <section>
        <p>Unknown section.</p>
      </section>
    <?php endif; ?>
  </main>

  <footer style="text-align:center;padding:1rem;background:#f4f4f4;">
    &copy; <?= date('Y') ?> Piney Point Farm Admin
  </footer>
</body>
</html>
