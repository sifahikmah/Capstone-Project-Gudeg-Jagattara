<?php 
session_start();
include '../koneksi.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, kembalikan ke halaman login
    header("Location: login.php");
    exit();
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
    .table th {
      background-color: #0d3b66;
      color: white;
    }
    .btn-add {
      background-color: #002244;
      color: white;
    }
    .btn-edit {
      background-color: hsl(50, 100%, 60%);
      color: white;
      border: none;
    }
    .btn-delete {
      background-color: #e74c3c;
      color: white;
      border: none;
    }
    img.menu-thumb {
      width: 60px;
      border-radius: 5px;
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

      <!-- Main content -->
      <div class="col-md-9 col-lg-10 content">
        <h4 class="fw-bold">Kelola Menu</h4>
        <!-- Tombol untuk menuju halaman tambah menu baru -->
        <a href="tambahmenu.php" class="btn btn-add mb-3 mt-3">+ Tambah Menu</a>

        <!-- Tabel daftar menu -->
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Menu</th>
              <th>Harga</th>
              <th>Deskripsi</th>
              <th>Gambar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            // Ambil semua data dari tabel menu
            $result = $koneksi->query("SELECT * FROM menu");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
              <!-- Tampilkan data menu -->
              <td><?= $no++ ?></td>
              <td><?= htmlspecialchars($row['nama_menu']) ?></td>
              <td>Rp.<?= number_format($row['harga'], 0, ',', '.') ?></td>
              <td><?= htmlspecialchars($row['deskripsi']) ?></td>
              <td>
                <!-- Tampilkan gambar jika ada, jika tidak beri teks -->
                <?php if (!empty($row['gambar']) && file_exists('../' . $row['gambar'])): ?>
                  <img src="../<?= htmlspecialchars($row['gambar']) ?>" class="menu-thumb" alt="Menu">
                <?php else: ?>
                  <small class="text-muted">Tidak ada gambar</small>
                <?php endif; ?>
              </td>
              <td>
                <!-- Tombol edit dan hapus menu -->
                <div class="d-flex gap-2">
                  <a href="editmenu.php?id_menu=<?= urlencode($row['id_menu']) ?>" class="btn btn-sm btn-edit">Edit</a>
                  <a href="hapusmenu.php?id_menu=<?= urlencode($row['id_menu']) ?>" onclick="return confirm('Yakin ingin menghapus menu ini?')" class="btn btn-sm btn-delete">Hapus</a>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>
</html>
