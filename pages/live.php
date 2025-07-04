<?php
// pages/live.php
include __DIR__ . '/../includes/header.php';
?>
<main>
  <section class="section live">
    <h2>Live BeeCam Feeds</h2>
    <div class="beecam-grid">
      <?php 
      
      $feeds = [
        ['label'=>'Hive #1', 'src'=>'/camera.php'],
        // ['label'=>'Hive #2', 'src'=>'/camera2.php'],
      ];
      foreach ($feeds as $f): 
      ?>
        <div class="beecam-tile">
          <img src="<?= htmlspecialchars($f['src']) ?>" 
               alt="<?= htmlspecialchars($f['label']) ?> live feed">
          <p><?= htmlspecialchars($f['label']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
