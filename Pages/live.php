<?php 
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<section id="live" class="section live">
  <h2>Live BeeCam Feed</h2>
  <img src="/camera.php" alt="BeeCam live stream" style="max-width:100%;height:auto;"
       onerror="this.alt='Unable to load live stream.'">
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
