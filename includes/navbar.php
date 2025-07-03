<?php 
  $current = basename($_SERVER['SCRIPT_NAME']);
  function isActive($page) {
    global $current;
    return $current === $page ? 'active' : '';
  }
?>
<nav class="nav">
  <ul>
    <li><a href="/index.php"          class="<?= isActive('index.php') ?>">Home</a></li>
    <li><a href="/pages/about.php"    class="<?= isActive('about.php') ?>">About</a></li>
    <li><a href="/pages/live.php"     class="<?= isActive('live.php') ?>">Live</a></li>
    <li><a href="/pages/products.php" class="<?= isActive('products.php') ?>">Products</a></li>
    <li><a href="/pages/newsletter.php" class="<?= isActive('newsletter.php') ?>">Newsletter</a></li>
    <li><a href="/pages/contact.php"  class="<?= isActive('contact.php') ?>">Contact</a></li>
  </ul>
</nav>
