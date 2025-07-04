<?php
// pages/product.php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
?>
<main>
  <section class="section product-detail">
    <link rel="stylesheet" href="/css/product.css">

    <?php if (!$p): ?>
      <h2>Product Not Found</h2>
      <p><a href="/pages/store.php">&larr; Back to Store</a></p>
    <?php else: ?>
      <div class="detail-container">
        <?php if ($p['image_url']): ?>
          <img
            src="<?= htmlspecialchars($p['image_url']) ?>"
            alt="<?= htmlspecialchars($p['name']) ?>"
            class="product-image"
          >
        <?php endif; ?>

        <div class="product-info">
          <h2><?= htmlspecialchars($p['name']) ?></h2>
          <p class="price">$<?= number_format($p['price_usd'], 2) ?></p>
          <?php if (!empty($p['details'])): ?>
            <div class="details"><?= nl2br(htmlspecialchars($p['details'])) ?></div>
          <?php endif; ?>
          <p><strong>In stock:</strong> <?= (int)$p['stock'] ?> jars</p>

          <form action="/pages/cart.php" method="post" class="add-to-cart-form">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <label for="qty">Quantity:</label>
            <input
              id="qty"
              name="quantity"
              type="number"
              min="1"
              max="<?= (int)$p['stock'] ?>"
              value="1"
              required
            >
            <button type="submit">Add to Cart</button>
          </form>

          <p><a href="/pages/store.php">&larr; Back to Store</a></p>
        </div>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
