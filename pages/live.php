<?php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<main>
  <section class="section live">
    <h2>Live BeeCam Feeds</h2>
    <!-- Link your page-specific styles here -->
    <link rel="stylesheet" href="/css/live.css">

    <div class="beecam-grid">
      <!-- Example tile; duplicate or loop as needed -->
      <div class="beecam-tile">
        <img src="/camera.php" alt="BeeCam 1 live feed">
        <p>Hive #1</p>
      </div>
      <!-- …other tiles… -->
    </div>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
