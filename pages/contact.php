<?php
// pages/contact_submit.php

require __DIR__ . '/../config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';

// Initialize
$errors  = [];
$name    = '';
$email   = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Trim inputs
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $message = trim($_POST['message'] ?? '');

    // 2) Sanitize
    //   name & message: remove tags
    $name    = filter_var($name,    FILTER_SANITIZE_STRING);
    $message = filter_var($message, FILTER_SANITIZE_STRING);

    // 3) Validate
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    }
    if ($message === '') {
        $errors[] = 'Message cannot be empty.';
    }

    // 4) If OK, insert into DB and show Thank You
    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO contacts (name, email, message)
            VALUES (:name, :email, :message)
        ');
        $stmt->execute([
            'name'    => $name,
            'email'   => $email,
            'message' => $message
        ]);
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
