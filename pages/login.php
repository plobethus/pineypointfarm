<?php
// pages/login.php
session_start();
require __DIR__ . '/../config.php';

// If already logged in, go to manager:
if (!empty($_SESSION['admin_user_id'])) {
    header('Location: /pages/manager.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    // Fetch user
    $stmt = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE username = ?");
    $stmt->execute([$user]);
    $row  = $stmt->fetch();

    if ($row && password_verify($pass, $row['password_hash'])) {
        // Success
        $_SESSION['admin_user_id'] = $row['id'];
        header('Location: /pages/manager.php');
        exit;
    } else {
        $errors[] = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/css/global.css">
</head>
<body>
  <main style="max-width:400px;margin:2rem auto;">
    <h2>Manager Login</h2>
    <?php if ($errors): ?>
      <ul style="color:red;">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <form method="post">
      <div>
        <label>Username<br>
          <input type="text" name="username" required>
        </label>
      </div>
      <div style="margin-top:1rem;">
        <label>Password<br>
          <input type="password" name="password" required>
        </label>
      </div>
      <div style="margin-top:1rem;">
        <button type="submit">Login</button>
      </div>
    </form>
  </main>
</body>
</html>
