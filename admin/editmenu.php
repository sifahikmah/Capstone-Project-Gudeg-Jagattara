<?php
include '../koneksi.php';

if (!isset($_GET['id_menu'])) {
  echo "ID tidak ditemukan.";
  exit;
}

$id_menu = intval($_GET['id_menu']);
$result = $koneksi->query("SELECT * FROM menu WHERE id_menu = $id_menu");

if (!$result || $result->num_rows == 0) {
  echo "Data menu tidak ditemukan.";
  exit;
}

$data = $result->fetch_assoc();
$gambarLama = $data['gambar'];

// Proses saat form disubmit
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = $_POST['nama_menu'];
  $harga = $_POST['harga'];
  $deskripsi = $_POST['deskripsi'];
  $gambarBaru = $gambarLama;

  // Cek apakah ada gambar baru diupload
  if (!empty($_FILES['gambar']['name'])) {
    $namaFile = basename($_FILES['gambar']['name']);
    $pathSimpan = '../assets/menu/' . $namaFile;
    $gambarBaru = 'assets/menu/' . $namaFile;

    // Hapus gambar lama jika ada
    if (!empty($gambarLama) && file_exists("../" . $gambarLama)) {
      unlink("../" . $gambarLama);
    }

    // Simpan gambar baru
    move_uploaded_file($_FILES['gambar']['tmp_name'], $pathSimpan);
  }

  // Update data
  $stmt = $koneksi->prepare("UPDATE menu SET nama_menu=?, harga=?, deskripsi=?, gambar=? WHERE id_menu=?");
  $stmt->bind_param("sissi", $nama, $harga, $deskripsi, $gambarBaru, $id_menu);
  $stmt->execute();
  $stmt->close();

  echo "<script>alert('Menu berhasil diperbarui'); window.location.href='kelolamenu.php';</script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Edit Menu</title>
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
<<<<<<< HEAD
      <a href="pesanan.php">📥 Pesanan Masuk</a>
      <a href="laporan.php">📊 Laporan Penjualan</a>
      <a href="#">🚪 Logout</a>
=======
>>>>>>> cedf297f3dce935fb40d21287ec77d21c9e038e4
    </div>

    <!-- Main Content -->
    <div class="col-md-10 content">
      <h3 class="fw-bold mb-4" style="color:#0b3b66;">Edit Menu</h3>

      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Nama Menu</label>
          <input type="text" name="nama_menu" class="form-control" value="<?= htmlspecialchars($data['nama_menu']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Harga</label>
          <input type="number" name="harga" class="form-control" value="<?= $data['harga'] ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Deskripsi</label>
          <textarea name="deskripsi" class="form-control" rows="2"><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        </div>

        <?php if (!empty($data['gambar'])): ?>
          <div class="mb-3">
            <label class="form-label">Gambar Saat Ini</label><br>
            <img src="../<?= htmlspecialchars($data['gambar']) ?>" width="120" class="img-thumbnail">
          </div>
        <?php endif; ?>

        <div class="mb-3">
          <label class="form-label">Upload Gambar Baru (Opsional)</label>
          <input type="file" name="gambar" class="form-control" accept="image/*">
        </div>
        <button type="submit" class="btn btn-save">Simpan Perubahan</button>
        <a href="kelolamenu.php" class="btn btn-secondary">Kembali</a>
      </form>
    </div>
  </div>
</div>
</body>
</html>
