<?php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';

// Fetch hive locations
$stmt   = $pdo->query("SELECT id,name,stock,x_coord,y_coord FROM stores");
$stores = $stmt->fetchAll();
?>
<main>
  <section class="section products">
    <h2>Hive Map of Houston</h2>
    <link rel="stylesheet" href="/css/map.css">

    <div class="map-container">
      <img src="/images/houston-map.png" alt="Map of Houston">

      <?php foreach ($stores as $s): ?>
        <a
          href="/pages/store.php?id=<?= $s['id'] ?>"
          class="map-point"
          style="left: <?= $s['x_coord'] ?>%; top: <?= $s['y_coord'] ?>%;"
          title="<?= htmlspecialchars("{$s['name']} — {$s['stock']} jars in stock") ?>">
        </a>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
