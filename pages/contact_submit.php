<?php
// pages/contact_submit.php

// 1) Bootstrap: load DB and layout
require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

// 2) Prepare variables
$errors  = [];
$name    = '';
$email   = '';
$message = '';

// 3) Only process on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Trim raw input
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // Sanitize
    $name    = filter_var($name,    FILTER_SANITIZE_STRING);
    $message = filter_var($message, FILTER_SANITIZE_STRING);

    // Validate
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if ($message === '') {
        $errors[] = 'Message cannot be empty.';
    }

    // If valid, insert into DB
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO contacts (name, email, message)
            VALUES (:name, :email, :message)
        ");
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':message' => $message
        ]);
        // Show thank-you and exit
        ?>
        <main>
          <section class="section contact">
            <h2>Thank You!</h2>
            <p>Your message has been sent. We’ll get back to you shortly.</p>
          </section>
        </main>
        <?php
        include __DIR__ . '/../includes/footer.php';
        exit;
    }
}

// 4) If GET or there were errors, re-display the form
?>
<main>
  <section class="section contact">
    <h2>Contact Us</h2>

    <?php if ($errors): ?>
      <ul class="form-errors">
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <form action="/pages/contact_submit.php" method="post" class="contact-form">
      <div class="form-group">
        <label for="name">Name</label>
        <input
          type="text"
          id="name"
          name="name"
          required
          value="<?= htmlspecialchars($name) ?>"
        >
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input
          type="email"
          id="email"
          name="email"
          required
          value="<?= htmlspecialchars($email) ?>"
        >
      </div>

      <div class="form-group">
        <label for="message">Message</label>
        <textarea
          id="message"
          name="message"
          rows="5"
          required
        ><?= htmlspecialchars($message) ?></textarea>
      </div>

      <button type="submit">Send Message</button>
    </form>
  </section>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
