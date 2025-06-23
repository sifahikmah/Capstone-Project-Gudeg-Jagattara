<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = isset($_POST['nama_menu']) ? $_POST['nama_menu'] : '';
  $harga = isset($_POST['harga']) ? $_POST['harga'] : 0;
  $deskripsi = isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '';

  $gambar = '';
  if (!empty($_FILES['gambar']['name'])) {
    $namaFile = basename($_FILES['gambar']['name']);
    $lokasiSimpan = '../assets/menu/' . $namaFile;
    $gambar = 'assets/menu/' . $namaFile;

    move_uploaded_file($_FILES['gambar']['tmp_name'], $lokasiSimpan);
  }

  // Pastikan data tidak kosong sebelum insert
  if ($nama && $harga && $deskripsi) {
    $stmt = $koneksi->prepare("INSERT INTO menu (nama_menu, harga, deskripsi, gambar) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $nama, $harga, $deskripsi, $gambar);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Menu berhasil ditambahkan'); window.location.href='kelolamenu.php';</script>";
  } else {
    echo "<script>alert('Semua field harus diisi');</script>";
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kelola Menu</title>
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
      font-weight: 600;
    }
    .btn-cancel {
      background-color: #d9d9d9;
      color: black;
      border: none;
      padding: 8px 24px;
    }
    .btn-save {
      background-color: #002244;
      color: white;
      border: none;
      padding: 8px 24px;
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
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="kelolamenu.php" class="active">🍽 Kelola Menu</a>
        <a href="pesanan.php">📥 Pesanan Masuk</a>
        <a href="laporan.php">📊 Laporan Penjualan</a>
        <a href="logout.php">🚪 Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <h3 class="fw-bold mb-4" style="color:#0b3b66;">Kelola Menu</h3>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="namaMenu" class="form-label">Nama Menu</label>
              <input type="text" class="form-control" id="namaMenu" name="nama_menu" placeholder="Contoh : Paket 1">
            </div>
            <div class="mb-3">
              <label for="harga" class="form-label">Harga</label>
              <input type="number" class="form-control" id="harga" name="harga" placeholder="Contoh : 25000">
            </div>
            <div class="mb-3">
              <label for="deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="deskripsi" name="deskripsi" rows="1" placeholder="Contoh : Ikan Goreng+Sambel+Nasi"></textarea>
            </div>
            <div class="mb-3">
              <label for="gambar" class="form-label">Gambar</label>
              <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
            </div>
            <div class="d-flex justify-content-end gap-2">
              <button type="button" class="btn btn-cancel">Batal</button>
              <button type="submit" class="btn btn-save">Simpan</button>
            </div>
        </form>

      </div>
    </div>
  </div>

</body>
</html>
