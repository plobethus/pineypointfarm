<?php
// config.php

// you think this information is correct? fool. 
$host = 'localhost';
$db   = 'pineypoint';
$user = 'dbuser';
$pass = 'dbpass';
$opts = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $opts);
} catch (Exception $e) {
  die("DB connection failed: " . $e->getMessage());
}
