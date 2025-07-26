<?php
session_start();
include '../koneksi.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, kembalikan ke halaman login
    header("Location: login.php");
    exit();
}

// Mengecek apakah request yang dikirim adalah POST (biasanya saat form disubmit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Mengambil data dari input form dan membersihkan spasi di awal/akhir
  $nama     = trim($_POST['nama_pembeli']);
  $menu     = trim($_POST['menu']);
  $jumlah   = intval($_POST['jumlah']);
  $total    = intval(str_replace(['Rp', '.', ','], '', $_POST['total']));
  $catatan  = trim($_POST['catatan']) ?: '-';

  // Validasi: jika ada data yang kosong/tidak valid, hentikan proses dan beri pesan error
  if ($nama === '' || $menu === '' || $jumlah <= 0 || $total <= 0) {
    die("Input tidak valid. Silakan isi semua data dengan benar.");
  }

  // Default untuk pesanan manual (langsung di toko)
  $id_user         = null;
  $metode_pembayaran  = 'tunai';
  $metode_pengantaran = 'diambil';
  $bukti_transfer     = null;
  $status             = 'terima';
  $nomor_wa           = '-';
  $alamat             = '-';
  $ongkir             = 0;

  // Menyiapkan query untuk menyimpan data pesanan ke tabel `pesanan`
  $stmt = $koneksi->prepare("INSERT INTO pesanan 
    (id_user, total, metode_pembayaran, metode_pengantaran, catatan, bukti_transfer, created_at, status, nomor_wa, alamat, ongkir, nama_pembeli_manual) 
    VALUES (?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, ?)");

  // Mengikat parameter ke query (jenis datanya: i = integer, s = string)
  $stmt->bind_param(
    "iisssssssis",
    $id_user,                 // i
    $total,                   // i
    $metode_pembayaran,       // s
    $metode_pengantaran,      // s
    $catatan,                 // s
    $bukti_transfer,          // s (bisa NULL)
    $status,                  // s
    $nomor_wa,                // s
    $alamat,                  // s
    $ongkir,                  // i
    $nama                     // s
  );

  // Eksekusi query penyimpanan pesanan
  if (!$stmt->execute()) {
    die("Gagal menyimpan pesanan: " . $stmt->error);
  }

  // Ambil ID pesanan yang baru disimpan
  $id_pesanan = $stmt->insert_id;
  $stmt->close();

  // Hitung harga satuan
  $harga_satuan = intval($total / max(1, $jumlah));

  // Siapkan dan simpan detail pesanan ke tabel `detail_pesanan`
  $stmtDetail = $koneksi->prepare("INSERT INTO detail_pesanan 
    (id_pesanan, id_menu, jumlah, harga_satuan, total_harga, nama_menu_manual) 
    VALUES (?, NULL, ?, ?, ?, ?)");

  // id_menu diset NULL karena data menu diinput manual
  $stmtDetail->bind_param("iiiis", $id_pesanan, $jumlah, $harga_satuan, $total, $menu);

  if (!$stmtDetail->execute()) {
    die("Gagal menyimpan detail pesanan: " . $stmtDetail->error);
  }

  $stmtDetail->close();

  // Redirect
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
    .sidebar a:hover,
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
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="kelolamenu.php">🍽 Kelola Menu</a>
        <a href="pesanan.php" class="active">📥 Pesanan Masuk</a>
        <a href="laporan.php">📊 Laporan Penjualan</a>
        <a href="logout.php">🚪 Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 content">
        <h4 class="fw-bold mb-4">Pesanan Masuk</h4>
        <h6 class="text-center mb-4">Tambah Pesanan Baru</h6>

        <!-- Form tambah pesanan baru -->
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
            <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 10" min="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Total (Rp)</label>
            <input type="text" name="total" class="form-control" placeholder="Contoh: 75000" pattern="^[0-9.,Rp\s]+$" required>
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
