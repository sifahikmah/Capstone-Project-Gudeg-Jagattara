<?php
session_start();
include '../koneksi.php';

// Total Pesanan
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'diterima'");
$totalPesanan = mysqli_fetch_assoc($q1)['total'];

// Menu Terjual
$q2 = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM detail_pesanan 
    JOIN pesanan ON detail_pesanan.id_pesanan = pesanan.id_pesanan 
    WHERE pesanan.status = 'diterima'");
$menuTerjual = mysqli_fetch_assoc($q2)['total'];
if ($menuTerjual === null) $menuTerjual = 0;

// Total Pendapatan
$q3 = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM pesanan WHERE status = 'diterima'");
$totalPendapatan = mysqli_fetch_assoc($q3)['total'];
if ($totalPendapatan === null) $totalPendapatan = 0;

// Detail Semua Pesanan
$detailPesanan = mysqli_query($koneksi, "
  SELECT p.nama_pembeli, p.created_at, 
         GROUP_CONCAT(m.nama_menu SEPARATOR '<br>') AS menu,
         GROUP_CONCAT(d.jumlah SEPARATOR '<br>') AS jumlah,
         p.total
  FROM pesanan p
  JOIN detail_pesanan d ON p.id_pesanan = d.id_pesanan
  JOIN menu m ON d.id_menu = m.id_menu
  WHERE p.status = 'diterima'
  GROUP BY p.id_pesanan
");

// Rekap per Menu
$rekapMenu = mysqli_query($koneksi, "
  SELECT m.nama_menu, SUM(d.jumlah) AS total_jual, SUM(d.jumlah * d.harga_satuan) AS pendapatan
  FROM detail_pesanan d
  JOIN menu m ON d.id_menu = m.id_menu
  JOIN pesanan p ON d.id_pesanan = p.id_pesanan
  WHERE p.status = 'diterima'
  GROUP BY d.id_menu
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
            <td class="text-start"><?= htmlspecialchars($row['nama_pembeli']) ?></td>
            <td class="text-start"><?= $row['menu'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp.<?= number_format($row['total'], 0, ',', '.') ?></td>
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
            <?php $no = 1; $totalSemua = 0; while($row = mysqli_fetch_assoc($rekapMenu)) : 
              $totalSemua += $row['pendapatan'];
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td class="text-start"><?= htmlspecialchars($row['nama_menu']) ?></td>
                <td><?= $row['total_jual'] ?></td>
                <td>Rp.<?= number_format($row['pendapatan'], 0, ',', '.') ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
          <tfoot>
            <tr class="table-total">
              <td colspan="3">Total</td>
              <td class="text-center">Rp.<?= number_format($totalSemua, 0, ',', '.') ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
