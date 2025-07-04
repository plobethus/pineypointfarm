<?php
include __DIR__ . '/../includes/header.php';

?>
<main>
  <section class="section contact">
    <h2>Contact Us</h2>
    <form action="/pages/contact_submit.php" method="post" class="contact-form">
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>
      </div>
  
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
      </div>
  
      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" rows="5" required></textarea>
      </div>
  
      <button type="submit">Send Message</button>
    </form>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>