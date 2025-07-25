<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Menunggu Konfirmasi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e3fadd;
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

  <script>
    const id = <?= json_encode($id) ?>;
    const interval = setInterval(() => {
      fetch('cek_status.php?id=' + id)
        .then(res => res.json())
        .then(data => {
          console.log("Status dari server:", data.status);
          if (data.status === 'terima') {
            clearInterval(interval);
            window.location.href = 'berhasil.php?id=' + id;
          } else if (data.status === 'tolak') {
            clearInterval(interval);
            window.location.href = 'gagal.php';
          }
        })
        .catch(err => {
          console.error("Gagal memeriksa status:", err);
        });
    }, 5000);
  </script>
</body>
</html>
