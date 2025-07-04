<?php
// pages/cart.php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

// Initialize cart in session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle actions: add, update, remove
$action = $_REQUEST['action'] ?? '';
switch ($action) {
  case 'add':
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $_SESSION['cart'][$pid] = ($_SESSION['cart'][$pid] ?? 0) + $qty;
    header('Location: /pages/cart.php');
    exit;

  case 'update':
    foreach ($_POST['qty'] as $pid => $qty) {
      $pid = (int)$pid;
      $qty = max(0, (int)$qty);
      if ($qty > 0) {
        $_SESSION['cart'][$pid] = $qty;
      } else {
        unset($_SESSION['cart'][$pid]);
      }
    }
    header('Location: /pages/cart.php');
    exit;

  case 'clear':
    $_SESSION['cart'] = [];
    header('Location: /pages/cart.php');
    exit;
}

// Fetch product details for IDs in cart
$items = [];
$total = 0.0;
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("
      SELECT id, name, price_usd, image_url
      FROM product
      WHERE id IN ($placeholders)
    ");
    $stmt->execute($ids);
    $products = $stmt->fetchAll(PDO::FETCH_KEY_PAIR | PDO::FETCH_GROUP);
    // build items
    foreach ($ids as $id) {
      if (!isset($products[$id])) continue;
      $p = $products[$id][0];
      $qty = $_SESSION['cart'][$id];
      $subtotal = $p['price_usd'] * $qty;
      $items[] = [
        'id'       => $id,
        'name'     => $p['name'],
        'price'    => $p['price_usd'],
        'image'    => $p['image_url'],
        'quantity' => $qty,
        'subtotal' => $subtotal,
      ];
      $total += $subtotal;
    }
}
?>
<main>
  <section class="section cart">
    <link rel="stylesheet" href="/css/cart.css">
    <h2>Your Shopping Cart</h2>

    <?php if (empty($items)): ?>
      <p>Your cart is empty. <a href="/pages/store.php">Go shopping</a>.</p>
    <?php else: ?>
      <form action="/pages/cart.php?action=update" method="post">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <?php if ($it['image']): ?>
                  <img src="<?= htmlspecialchars($it['image']) ?>" alt="" class="cart-thumb">
                <?php endif; ?>
                <?= htmlspecialchars($it['name']) ?>
              </td>
              <td>$<?= number_format($it['price'],2) ?></td>
              <td>
                <input 
                  type="number"
                  name="qty[<?= $it['id'] ?>]"
                  value="<?= $it['quantity'] ?>"
                  min="0"
                >
              </td>
              <td>$<?= number_format($it['subtotal'],2) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3"><strong>Total</strong></td>
              <td><strong>$<?= number_format($total,2) ?></strong></td>
            </tr>
          </tfoot>
        </table>

        <div class="cart-actions">
          <button type="submit">Update Cart</button>
          <a href="/pages/cart.php?action=clear" class="button">Clear Cart</a>
          <a href="/pages/checkout.php" class="button primary">Proceed to Checkout</a>
        </div>
      </form>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
