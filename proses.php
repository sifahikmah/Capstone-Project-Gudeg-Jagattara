<?php
include 'koneksi.php';
$id = $_GET['id'] ?? $_COOKIE['id_pesanan'] ?? null;

if (!$id) {
  echo "<script>alert('Pesanan tidak ditemukan'); window.location='menu.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pembayaran Diproses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f0f4f8;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .processing-box {
      background-color: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      max-width: 450px;
      text-align: center;
    }
    .spinner-border {
      width: 3rem;
      height: 3rem;
    }
  </style>
</head>
<body>

<div class="processing-box">
  <div class="spinner-border text-success mb-4" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
  <h4 class="mb-3">Pembayaran Sedang Diproses</h4>
  <p class="text-muted">Terima kasih telah melakukan pembayaran.<br>Mohon tunggu sebentar, kami sedang memverifikasi transaksi Anda.</p>
</div>

<!-- Fetch status & redirect -->
<script>
  const interval = setInterval(() => {
    fetch('cek_status.php?id=<?= $id ?>')
      .then(res => res.json())
      .then(data => {
        console.log("Status dari server:", data.status);
        if (data.status === 'diterima') {
          clearInterval(interval);
          window.location.href = 'berhasil.php?id=<?= $id ?>';
        } else if (data.status === 'ditolak') {
          clearInterval(interval);
          window.location.href = 'gagal.php';
        }

      })
      .catch(err => console.error("Gagal memeriksa status:", err));
  }, 5000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
