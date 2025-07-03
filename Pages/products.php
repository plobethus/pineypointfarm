<?php 
include __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

// Fetch store locations
$stores = $pdo->query("SELECT name,lat,lng,url FROM stores")->fetchAll();
?>
<section class="section products">
  <h2>Where to Find Our Honey</h2>
  <div id="map" style="height: 500px;"></div>
</section>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet/dist/leaflet.css"
/>
<script>
  const map = L.map('map').setView([29.7604, -95.3698], 10);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  const stores = <?= json_encode($stores) ?>;
  stores.forEach(s => {
    L.marker([s.lat, s.lng]).addTo(map)
      .bindPopup(`<strong>${s.name}</strong>`)
      .on('click', () => window.location.href = s.url);
  });
</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
