<?php
// pages/store.php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$stmt     = $pdo->query("
  SELECT id, name, price_usd, stock, image_url
  FROM product
  ORDER BY name
");
$products = $stmt->fetchAll();
?>
<main>
  <section class="section store">
    <link rel="stylesheet" href="/css/store.css">
    <h2>Online Store</h2>

    <?php if (empty($products)): ?>
      <p>No products available right now.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
          <div class="product-card">
            <?php if ($p['image_url']): ?>
              <a href="/pages/product.php?id=<?= $p['id'] ?>">
                <img src="<?= htmlspecialchars($p['image_url']) ?>"
                     alt="<?= htmlspecialchars($p['name']) ?>"
                     class="product-thumb">
              </a>
            <?php endif; ?>

            <h3>
              <a href="/pages/product.php?id=<?= $p['id'] ?>">
                <?= htmlspecialchars($p['name']) ?>
              </a>
            </h3>
            <p><strong>Price:</strong> $<?= number_format($p['price_usd'],2) ?></p>
            <p><strong>In stock:</strong> <?= (int)$p['stock'] ?> jars</p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
