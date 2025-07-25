<?php
session_start();
require 'koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $konfirmasi = $_POST['konfirmasi'];

  if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    $error = 'Username hanya boleh huruf, angka, dan underscore (_)';
  } elseif (strlen($password) < 6) {
    $error = 'Password minimal 6 karakter.';
  } elseif ($password !== $konfirmasi) {
    $error = 'Konfirmasi password tidak cocok';
  } else {
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $error = 'Username sudah terdaftar';
    } else {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $insert = $koneksi->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
      $insert->bind_param("ss", $username, $hashed);

      if ($insert->execute()) {
        $success = 'Berhasil mendaftar. Silakan masuk.';
      } else {
        $error = 'Gagal mendaftar, silakan coba lagi.';
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
  <title>Daftar - Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #e3fadd;
    }
    .register-container {
      min-height: 100vh;
    }
    .card {
      border-radius: 1rem;
    }
    input:focus {
      border-color: #28a745 !important;
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
    }
    .logo img {
      display: block;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center register-container">
    <div class="card shadow p-4 w-100" style="max-width: 400px;">
      <div class="logo text-center mb-3">
        <img src="./assets/logo.png" alt="Logo" width="180">
      </div>
      <h3 class="mb-4 text-center">Daftar</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?> <a href="login.php">Login di sini</a></div>
      <?php endif; ?>

      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Kata Sandi</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label for="konfirmasi" class="form-label">Konfirmasi Kata Sandi</label>
          <input type="password" name="konfirmasi" id="konfirmasi" class="form-control" required>
        </div>
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-success">Daftar</button>
        </div>
        <div class="text-center">
          Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
