<?php
session_start();
include '../koneksi.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, kembalikan ke halaman login
    header("Location: login.php");
    exit();
}

// Total Pesanan
// Query untuk menghitung jumlah total pesanan dengan status 'terima'
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'terima'");

// Jika query berhasil dijalankan, ambil nilai 'total', jika gagal set default ke 0
$totalPesanan = $q1 ? mysqli_fetch_assoc($q1)['total'] : 0;


// Menu Terjual
$q2 = mysqli_query($koneksi, "
    SELECT SUM(d.jumlah) AS total 
    FROM detail_pesanan d
    JOIN pesanan p ON d.id_pesanan = p.id_pesanan 
    WHERE p.status = 'terima'
");
$menuTerjual = $q2 ? mysqli_fetch_assoc($q2)['total'] : 0;
if ($menuTerjual === null) $menuTerjual = 0;

// Total Pendapatan
$q3 = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM pesanan WHERE status = 'terima'");
$totalPendapatan = $q3 ? mysqli_fetch_assoc($q3)['total'] : 0;
if ($totalPendapatan === null) $totalPendapatan = 0;

// Detail Semua Pesanan yang statusnya 'terima'
$detailPesanan = mysqli_query($koneksi, "
  SELECT 
    COALESCE(u.username, p.nama_pembeli_manual) AS nama_user,
    p.created_at,
    GROUP_CONCAT(COALESCE(m.nama_menu, d.nama_menu_manual) SEPARATOR '<br>') AS menu,
    GROUP_CONCAT(d.jumlah SEPARATOR '<br>') AS jumlah,
    p.total
  FROM pesanan p
  LEFT JOIN users u ON p.id_user = u.id_user
  JOIN detail_pesanan d ON p.id_pesanan = d.id_pesanan
  LEFT JOIN menu m ON d.id_menu = m.id_menu
  WHERE p.status = 'terima'
  GROUP BY p.id_pesanan
");

// Rekap per Menu yang terjual dari pesanan yang sudah diterima
$rekapMenu = mysqli_query($koneksi, "
  SELECT 
    -- Nama menu tetap bersih
    COALESCE(m.nama_menu, d.nama_menu_manual) AS nama_menu,
    
    -- Kode unik untuk pemisah 'Paket 1' dari tabel dan 'Paket 1' manual
    COALESCE(CONCAT('DB-', d.id_menu), CONCAT('MANUAL-', d.nama_menu_manual)) AS kode_unik_menu,
    
    SUM(d.jumlah) AS total_jual,
    SUM(d.jumlah * COALESCE(m.harga, d.harga_satuan)) AS pendapatan
  FROM detail_pesanan d
  JOIN pesanan p ON d.id_pesanan = p.id_pesanan
  LEFT JOIN menu m ON d.id_menu = m.id_menu
  WHERE p.status = 'terima'
  GROUP BY kode_unik_menu
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Penjualan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
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
      font-size: 15.1px;
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
      background-color: #002244;
      color: white;
    }
    .table td {
      background-color: white;
    }
    .table-total td {
      background-color: #eeeeee;
      font-weight: bold;
      text-align: right;
    }
    .section-title {
      font-weight: bold;
      font-size: 18px;
      margin: 40px 0 20px;
    }
    .summary-box {
      background-color: #e9ecef;
      padding: 20px;
      border-radius: 8px;
      text-align: center;
      font-weight: bold;
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
      <a href="pesanan.php">📥 Pesanan Masuk</a>
      <a href="laporan.php" class="active">📊 Laporan Penjualan</a>
      <a href="logout.php">🚪 Logout</a>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 content">
      <h3 class="fw-bold mb-4" style="color:#0b3b66;">Laporan Penjualan</h3>

      <!-- Ringkasan -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="summary-box">
            Total Pesanan
            <div class="fs-4 text-primary mt-2"><?= $totalPesanan ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="summary-box">
            Menu Terjual
            <div class="fs-4 text-primary mt-2"><?= $menuTerjual ?></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="summary-box">
            Total Pendapatan
            <div class="fs-4 text-primary mt-2">Rp<?= number_format($totalPendapatan, 0, ',', '.') ?></div>
          </div>
        </div>
      </div>

      <!-- Detail Semua Pesanan -->
      <div class="section-title">Detail Semua Pesanan</div>
      <table class="table table-bordered text-center">
        <thead>
        <tr>
          <th>NO</th>
          <th>Nama</th>
          <th style="width: 200px;">Menu</th>
          <th>Jumlah</th>
          <th>Total</th>
          <th>Waktu</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; while($row = mysqli_fetch_assoc($detailPesanan)) : ?>
          <tr>
            <td><?= $no++ ?></td>
            <!-- Nama pembeli (dilindungi dari karakter spesial HTML) -->
            <td class="text-start"><?= htmlspecialchars($row['nama_user']) ?></td>
            <td class="text-start"><?= $row['menu'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <!-- Format total harga ke format Rupiah -->
            <td>Rp.<?= number_format($row['total'], 0, ',', '.') ?></td>
            <!-- Tampilkan tanggal pemesanan dalam format d-m-Y H:i -->
            <td><?= date('d-m-Y H:i', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
        </tbody>
      </table>

      <!-- Rekap Penjualan Per Menu -->    
      <div class="section-title">Rekap Penjualan Per Menu</div>
      <table class="table table-bordered text-center">
        <thead>
          <tr>
            <th>NO</th>
            <th>Menu</th>
            <th>Jumlah Terjual</th>
            <th>Total Pendapatan</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1; 
          $totalSemua = 0; // Inisialisasi total seluruh pendapatan
          while ($row = mysqli_fetch_assoc($rekapMenu)) : 
            $totalSemua += $row['pendapatan']; // Tambahkan pendapatan per menu ke total
          ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="text-start"><?= htmlspecialchars($row['nama_menu']) ?></td>
              <!-- Format jumlah terjual -->
              <td><?= number_format($row['total_jual'], 0, ',', '.') ?></td>
              <!-- Format total pendapatan per menu -->
              <td>Rp<?= number_format($row['pendapatan'], 0, ',', '.') ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
        <!-- Tampilkan total keseluruhan pendapatan -->
        <tfoot>
          <tr class="table-total">
            <td colspan="3">Total</td>
            <td class="text-center">Rp<?= number_format($totalSemua, 0, ',', '.') ?></td>
          </tr>
        </tfoot>
      </table>

</body>
</html>
