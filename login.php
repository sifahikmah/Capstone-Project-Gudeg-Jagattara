<?php
session_start();
require 'koneksi.php'; // file koneksi ke database

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
      $_SESSION['user'] = $user;
      header('Location: index.php');
      exit;
    } else {
      $error = 'Password salah';
    }
  } else {
    $error = 'Username tidak ditemukan';
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
    input:focus {
        border-color: #28a745 !important;   
        box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
    }
  </style>
</head>
<body>
  <div class="container d-flex justify-content-center align-items-center login-container">
    <div class="card shadow p-4 w-100" style="max-width: 400px;">
      <h3 class="mb-4 text-center">Masuk</h3>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
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
