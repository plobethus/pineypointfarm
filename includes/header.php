<?php
// includes/header.php
session_start();                    // make $_SESSION available site-wide
$current = basename($_SERVER['SCRIPT_NAME']);
function isActive($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="description" content="Your local apiary for fresh honey & bee products">
  <title>Piney Point Farm</title>
  <link rel="stylesheet" href="/css/global.css">
</head>
<body>

<nav class="navbar">
  <a href="/index.php" class="logo-link">
    <img src="/images/logo.png" alt="Piney Point Farm" class="logo-img">
    
  </a>
  <ul class="nav-links">
    <li><a href="/index.php"             class="<?= isActive('index.php') ?>">Home</a></li>
    <li><a href="/pages/about.php"       class="<?= isActive('about.php') ?>">About</a></li>
    <li><a href="/pages/contact.php"     class="<?= isActive('contact.php') ?>">Contact</a></li>
    <li><a href="/pages/live.php"        class="<?= isActive('live.php') ?>">Live</a></li>
    <li><a href="/pages/map.php"         class="<?= isActive('map.php') ?>">Map</a></li>
    <li><a href="/pages/store.php"       class="<?= isActive('store.php') ?>">Store</a></li>
    <li><a href="/pages/newsletter.php"  class="<?= isActive('newsletter.php') ?>">Newsletter</a></li>
  </ul>
</nav>
