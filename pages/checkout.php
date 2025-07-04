<?php
// pages/checkout.php
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>
<main>
  <section class="section checkout">
    <link rel="stylesheet" href="/css/checkout.css">

    <h2>Checkout</h2>
    <p>Cart total, shipping form, and Stripe integration go here.</p>
    <p><em>Placeholder: connect to Stripe & collect shipping details.</em></p>

    <a href="/pages/cart.php" class="button">&larr; Back to Cart</a>
  </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
