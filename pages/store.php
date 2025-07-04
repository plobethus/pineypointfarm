<?php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM stores WHERE id = ?");
$stmt->execute([$id]);
$store = $stmt->fetch();
?>
<main>
  <section class="section store">
    <?php if (!$store): ?>
      <p>Store not found.</p>
    <?php else: ?>
      <h2><?= htmlspecialchars($store['name']) ?></h2>
      <p><strong>Honey in stock:</strong> <?= htmlspecialchars($store['stock']) ?> jars</p>
      <!-- Later: Stripe “Buy” button for this store’s honey -->
    <?php endif; ?>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
