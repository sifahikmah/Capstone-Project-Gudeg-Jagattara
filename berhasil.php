<?php
$id = $_GET['id'] ?? null;
if (!$id) {
  echo "<script>alert('ID Pesanan tidak ditemukan'); window.location='menu.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pesanan Berhasil</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e9fff1;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .success-box {
      background-color: white;
      padding: 40px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      max-width: 500px;
    }
  </style>
</head>
<body>

<div class="success-box">
  <h3 class="mb-3 text-success">🎉 Pesanan Berhasil Dibuat</h3>
  <p class="mb-0">Terima kasih! Pesanan Anda sudah diterima dan akan segera kami proses.</p>
  <p style="font-size: small;" class="mt-0">*Jika penjual belum menghubungi, silahkan hubungi penjual untuk informasi lebih lanjut!</p>
  <a href="nota.php?id=<?= $id ?>" class="btn btn-success ">Lihat Nota</a>
  <a href="https://wa.me/6281327456736?text=Halo%20saya%20ingin%20bertanya%20mengenai%20pemesanan%20yang%20sudah%20saya%20buat." 
        target="_blank" 
        class="btn btn-outline-success">
        Hubungi Penjual
  </a>
</div>

</body>
</html>
