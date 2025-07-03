<?php 
include __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

// Handle subscription form
if ($_SERVER['REQUEST_METHOD']==='POST' && filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
  $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers(email) VALUES(?)");
  $stmt->execute([$_POST['email']]);
  $message = "Thanks for subscribing!";
}

// Fetch past newsletters
$mails = $pdo->query("SELECT id,title,DATE_FORMAT(sent_at,'%M %Y') AS month FROM newsletters ORDER BY sent_at DESC")->fetchAll();
?>
<section class="section">
  <h2>Monthly Newsletters</h2>
  <?php if(isset($message)): ?>
    <p><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>
  <form method="post">
    <input type="email" name="email" placeholder="Your email" required>
    <button type="submit">Subscribe</button>
  </form>

  <ul>
    <?php foreach($mails as $n): ?>
      <li>
        <a href="view_newsletter.php?id=<?= $n['id'] ?>">
          <?= htmlspecialchars($n['title']) ?> (<?= $n['month'] ?>)
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
