<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama     = $_POST['nama_pembeli'];
  $menu     = $_POST['menu'];
  $jumlah   = intval($_POST['jumlah']);
  $total    = intval(str_replace(['Rp.', '.', ','], '', $_POST['total']));
  $catatan  = trim($_POST['catatan']) ?: '-';

  // Siapkan default
  $alamat   = '-';
  $pengantaran = '-';
  $pembayaran  = '-';
  $status   = 'diterima';

  // Simpan ke tabel pesanan terlebih dahulu
  $stmt = $koneksi->prepare("INSERT INTO pesanan 
    (nama_pembeli, alamat, catatan, metode_pengantaran, metode_pembayaran, total, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssis", $nama, $alamat, $catatan, $pengantaran, $pembayaran, $total, $status);
  $stmt->execute();
  $id_pesanan = $stmt->insert_id;
  $stmt->close();

  // Simpan ke tabel detail_pesanan dengan menu manual
  $stmtDetail = $koneksi->prepare("INSERT INTO detail_pesanan 
    (id_pesanan, id_menu, jumlah, harga_satuan, nama_menu_manual) 
    VALUES (?, NULL, ?, ?, ?)");
  $stmtDetail->bind_param("iiis", $id_pesanan, $jumlah, $total, $menu);
  $stmtDetail->execute();
  $stmtDetail->close();

  header("Location: pesanan.php");
  exit;
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Pesanan Baru</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .sidebar {
      background-color: #003366;
      min-height: 100vh;
      color: white;
    }
    .sidebar a {
      color: white;
      text-decoration: none;
      display: block;
      padding: 15px;
    }
    .sidebar a:hover {
      background-color: #002244;
    }
    .sidebar a.active {
      background-color: #002244;
      font-weight: bold;
    }
    .content {
      padding: 30px;
    }
    .form-label {
      font-weight: bold;
    }
    .btn-cancel {
      background-color: #dcdcdc;
      color: black;
    }
    .btn-submit {
      background-color: #002244;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar d-flex flex-column">
        <div class="text-center my-2">
          <img src="../assets/logo2.png" alt="Logo" width="180">
        </div>
        <a href="dashboard.html">🏠 Dashboard</a>
        <a href="kelolamenu.html">🍽 Kelola Menu</a>
        <a href="pesanan.html" class="active">📥 Pesanan Masuk</a>
        <a href="laporan.html">📊 Laporan Penjualan</a>
        <a href="#">🚪 Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-9 col-lg-10 content">
        <h4 class="fw-bold mb-4">Pesanan Masuk</h4>
        <h6 class="text-center mb-4">Tambah Pesanan Baru</h6>

<form method="POST" action="tambah_pesanan.php">
  <div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="nama_pembeli" class="form-control" placeholder="Contoh: Lana Del Rey" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Menu</label>
    <input type="text" name="menu" class="form-control" placeholder="Contoh: Paket 2" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Jumlah</label>
    <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 10" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Total (Rp)</label>
    <input type="text" name="total" class="form-control" placeholder="Contoh: 75000" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Catatan</label>
    <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Minta sambalnya ditambah"></textarea>
  </div>
  <div class="text-end">
    <button type="reset" class="btn btn-cancel me-2">Batal</button>
    <button type="submit" class="btn btn-submit">Simpan</button>
  </div>
</form>

      </div>
    </div>
  </div>
</body>
</html>
