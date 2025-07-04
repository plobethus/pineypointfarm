<?php
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';

// Handle signup (later you’ll save to DB)
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  $message = "Thanks for joining our mailing list!";
  // later: INSERT INTO subscribers (email) VALUES (?)
}

// Fetch past newsletters (later from DB)
$newsletters = []; // placeholder
?>
<main>
  <section class="section newsletter">
    <h2>Monthly Newsletter</h2>
    <form method="post" class="newsletter-form">
      <input type="email" name="email" placeholder="Your email address" required>
      <button type="submit">Subscribe</button>
    </form>

    <?php if ($message): ?>
      <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h3>Past Editions</h3>
    <ul class="newsletter-list">
      <?php foreach ($newsletters as $n): ?>
        <li>
          <a href="/pages/view_newsletter.php?id=<?= $n['id'] ?>">
            <?= htmlspecialchars($n['title']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
