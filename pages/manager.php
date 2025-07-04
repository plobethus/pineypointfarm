<?php
// pages/manager.php
session_start();
require __DIR__ . '/../config.php';

// 1) Protect this page
if (empty($_SESSION['admin_user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

// 2) Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: /pages/login.php');
    exit;
}

// 3) Determine section
$action = $_GET['action'] ?? 'dashboard';

// 4) Handle form submissions
if ($action === 'types' && $_SERVER['REQUEST_METHOD']==='POST') {
    // Add new type
    $name = trim(filter_input(INPUT_POST,'type_name',FILTER_SANITIZE_STRING));
    if ($name) {
      $stmt = $pdo->prepare("INSERT IGNORE INTO product_types(name) VALUES(?)");
      $stmt->execute([$name]);
    }
}

if ($action === 'products' && $_SERVER['REQUEST_METHOD']==='POST') {
    // Add new product
    $n   = trim(filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING));
    $p   = filter_input(INPUT_POST,'price_usd',FILTER_VALIDATE_FLOAT);
    $d   = trim(filter_input(INPUT_POST,'details',FILTER_SANITIZE_STRING));
    $s   = filter_input(INPUT_POST,'stock',FILTER_VALIDATE_INT);
    $tid = filter_input(INPUT_POST,'type_id',FILTER_VALIDATE_INT);

    // Image upload?
    $imgUrl = null;
    if (!empty($_FILES['image']['tmp_name'])) {
      $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $file = uniqid() . ".$ext";
      move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/../images/products/$file");
      $imgUrl = "/images/products/$file";
    }

    if ($n && $p !== false && $s !== false) {
      $stmt = $pdo->prepare("
        INSERT INTO product
          (name, price_usd, details, stock, product_type_id, image_url)
        VALUES (?, ?, ?, ?, ?, ?)
      ");
      $stmt->execute([$n,$p,$d,$s,$tid,$imgUrl]);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/css/global.css">
  <style>
    /* Quick admin overrides */
    body { font-family: Arial, sans-serif; }
    nav.admin-nav a { margin-right:1rem; text-decoration:none; }
    section { max-width: 900px; margin: 2rem auto; }
    table { width:100%; border-collapse: collapse; margin-top:1rem; }
    th,td { border:1px solid #ddd; padding:0.5rem; }
    form > div { margin-top:0.5rem; }
  </style>
</head>
<body>
  <header style="background:#1A3320;padding:1rem;color:#fff;">
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
          <?php foreach($types as $t): ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td><?= htmlspecialchars($t['name']) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

    <?php elseif ($action === 'products'): 
      // Fetch types for dropdown
      $types    = $pdo->query("SELECT * FROM product_types")->fetchAll();
      $products = $pdo->query("
        SELECT p.*, pt.name AS type_name
          FROM product p
          LEFT JOIN product_types pt ON p.product_type_id = pt.id
        ORDER BY p.name
      ")->fetchAll();
    ?>
      <section>
        <h2>Products</h2>
        <form method="post" enctype="multipart/form-data">
          <div>
            <label>Name:
              <input type="text" name="name" required>
            </label>
          </div>
          <div>
            <label>Price (USD):
              <input type="number" step="0.01" name="price_usd" required>
            </label>
          </div>
          <div>
            <label>Details:
              <textarea name="details" rows="3"></textarea>
            </label>
          </div>
          <div>
            <label>Stock:
              <input type="number" name="stock" required>
            </label>
          </div>
          <div>
            <label>Type:
              <select name="type_id">
                <option value="">— none —</option>
                <?php foreach($types as $t): ?>
                  <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
          <div>
            <label>Image:
              <input type="file" name="image" accept="image/*">
            </label>
          </div>
          <div>
            <button type="submit">Add Product</button>
          </div>
        </form>

        <table>
          <tr>
            <th>ID</th><th>Name</th><th>Type</th>
            <th>Price</th><th>Stock</th><th>Image</th>
          </tr>
          <?php foreach($products as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['name']) ?></td>
              <td><?= htmlspecialchars($p['type_name'] ?? '—') ?></td>
              <td>$<?= number_format($p['price_usd'],2) ?></td>
              <td><?= (int)$p['stock'] ?></td>
              <td>
                <?php if ($p['image_url']): ?>
                  <img src="<?= htmlspecialchars($p['image_url']) ?>"
                       alt="" style="height:40px;">
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </section>

    <?php elseif ($action === 'messages'): 
      $msgs = $pdo->query("SELECT * FROM contacts ORDER BY submitted_at DESC")
                  ->fetchAll();
    ?>
      <section>
        <h2>Contact Messages</h2>
        <table>
          <tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>When</th></tr>
          <?php foreach($msgs as $m): ?>
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
