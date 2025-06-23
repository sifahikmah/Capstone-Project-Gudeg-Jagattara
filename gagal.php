<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pesanan Ditolak</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e9fff1;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .box {
      background: white;
      padding: 40px;
      border-radius: 16px;
      max-width: 500px;
      text-align: center;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .icon {
      font-size: 48px;
      /* color: #dc3545; */
    }
  </style>
</head>
<body>

<div class="box">
    <div class="icon mb-3">❌</div>
    <h4>Pesanan Gagal</h4>
    <p class="text-muted">
    Maaf, pesanan Anda tidak dapat kami proses saat ini.<br>
    Silakan coba pesan ulang nanti atau hubungi penjual 😊
    </p>
    <div class="d-flex justify-content-center gap-2 mt-4">
      <a href="menu.php" class="btn btn-success">Kembali ke Menu</a>
      <a href="https://wa.me/6281327456736?text=Halo%20saya%20ingin%20bertanya%20tentang%20pesanan%20yang%20gagal." 
        target="_blank" 
        class="btn btn-outline-success">
        Hubungi Penjual
      </a>
    </div>
</div>

</body>
</html>