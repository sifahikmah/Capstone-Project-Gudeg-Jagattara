<?php
session_start();
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($koneksi, $query); // pakai $koneksi, bukan $conn

    if (mysqli_num_rows($result) === 1) {
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location='dashboard.php';</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e8e9f3;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-card {
      background-color: #062f57;
      padding: 40px;
      border-radius: 10px;
      width: 100%;
      max-width: 400px;
      color: white;
      text-align: center;
    }

    .login-card h2 {
      margin-bottom: 30px;
    }

    .form-control {
      margin-bottom: 20px;
    }

    .btn-login {
      background-color: rgb(112, 109, 109);
      border: none;
      color: white;
      font-weight: bold;
    }

  </style>
</head>
<body>
  <div class="login-card">
    <div class="logo">
      <img src="../assets/logo2.png" alt="Logo" width="200px">
    </div>
    <h2>Login Admin</h2>
    <form action="login.php" method="POST">
      <input type="text" name="username" class="form-control" placeholder="Username" required>
      <input type="password" name="password" class="form-control" placeholder="Password" required>
      <button type="submit" class="btn btn-login w-100 mt-2">Login</button>
    </form>
  </div>

</body>
</html>
