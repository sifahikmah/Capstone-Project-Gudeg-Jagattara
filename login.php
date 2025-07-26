<?php
session_start();
require 'koneksi.php';

$error = '';
$max_attempts = 5;
$lockout_time = 300; // 5 menit dalam detik

// Inisialisasi session percobaan login
if (!isset($_SESSION['login_attempts'])) {
  $_SESSION['login_attempts'] = 0;
  $_SESSION['last_attempt'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  // Jika melebihi batas dan belum lewat 5 menit
  if ($_SESSION['login_attempts'] >= $max_attempts && time() - $_SESSION['last_attempt'] < $lockout_time) {
    $error = 'Terlalu banyak percobaan login. Coba lagi dalam 5 menit.';
  } else {
    // Validasi karakter
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
      $error = 'Username hanya boleh huruf, angka, dan underscore (_)';
    } elseif (strlen($username) < 3 || strlen($password) < 6) {
      $error = 'Username minimal 3 karakter dan password minimal 6 karakter.';
    } else {
      $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
      $stmt->bind_param("s", $username);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
          $_SESSION['user'] = $user;
          $_SESSION['login_attempts'] = 0; // reset kalau berhasil login
          header('Location: index.php');
          exit;
        } else {
          $error = 'Password salah';
          $_SESSION['login_attempts']++;
          $_SESSION['last_attempt'] = time();
        }
      } else {
        $error = 'Username tidak ditemukan';
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #e3fadd;
    }
    .login-container {
      min-height: 100vh;
    }
    .card {
      border-radius: 1rem;
    }
    .logo img {
      display: block;
      margin: 0 auto 1rem;
    }
    input:focus {
      border-color: #28a745 !important;
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center login-container">
    <div class="card shadow p-4 w-100" style="max-width: 400px;">
      <div class="logo">
        <img src="./assets/logo.png" alt="Logo Gudeg" width="180">
      </div>
      <h3 class="mb-4 text-center">Masuk</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST">
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Kata Sandi</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-success">Masuk</button>
        </div>
        <div class="text-center">
          Belum punya akun? <a href="signup.php">Daftar di sini</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
