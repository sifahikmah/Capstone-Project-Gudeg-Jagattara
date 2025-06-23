<?php
session_start();
include '../koneksi.php';

// Query jumlah menu
$resultMenu = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM menu");
$totalMenu = mysqli_fetch_assoc($resultMenu)['total'] ?? 0;

// Query jumlah pesanan masuk (status = 'menunggu')
$resultPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'menunggu'");
$pesananMasuk = mysqli_fetch_assoc($resultPesanan)['total'] ?? 0;

// Query total penjualan (status = 'diterima')
$resultPenjualan = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM pesanan WHERE status = 'diterima'");
$totalPenjualan = mysqli_fetch_assoc($resultPenjualan)['total'] ?? 0;

// Query pelanggan unik
$resultPelanggan = mysqli_query($koneksi, "SELECT COUNT(DISTINCT nama_pembeli) AS total FROM pesanan");
$jumlahPelanggan = mysqli_fetch_assoc($resultPelanggan)['total'] ?? 0;

// Grafik penjualan harian
$salesQuery = "SELECT 
    DAYNAME(created_at) AS hari,
    COUNT(*) AS total
  FROM pesanan
  WHERE status = 'diterima'
  GROUP BY DAYOFWEEK(created_at)";

$salesResult = mysqli_query($koneksi, $salesQuery);

// Inisialisasi 0 untuk semua hari
$hariList = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$dataPenjualan = array_fill_keys($hariList, 0);

// Isi data dari DB
while ($row = mysqli_fetch_assoc($salesResult)) {
    $dataPenjualan[$row['hari']] = $row['total'];
}

// Ganti ke Bahasa Indonesia
$labelHari = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu',
];

$labelsJS = json_encode(array_values($labelHari));
$dataJS = json_encode(array_values($dataPenjualan));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <a href="dashboard.php" class="active">🏠 Dashboard</a>
        <a href="kelolamenu.php">🍽 Kelola Menu</a>
        <a href="pesanan.php">📥 Pesanan Masuk</a>
        <a href="laporan.php">📊 Laporan Penjualan</a>
        <a href="logout.php">🚪 Logout</a>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4">
        <h3 class="mb-4"><strong>Dashboard Admin</strong></h3>

        <!-- Summary Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="summary-box">
              Total Menu
              <div class="fs-4 text-primary mt-2"><?= $totalMenu ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="summary-box">
              Pesanan Masuk
              <div class="fs-4 text-primary mt-2"><?= $pesananMasuk ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="summary-box">
              Total Penjualan
              <div class="fs-4 text-primary mt-2">Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="summary-box">
              Pelanggan
              <div class="fs-4 text-primary mt-2"><?= $jumlahPelanggan ?></div>
            </div>
          </div>
        </div>
        <!-- Chart -->
        <canvas id="salesChart" height="100"></canvas>
      </div>
    </div>
  </div>
  
  <!-- <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
        datasets: [{
          label: 'Penjualan (Rp)',
          data: [200000, 300000, 250000, 370000, 290000, 420000, 480000],
          backgroundColor: '#002f6c'
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value.toLocaleString('id-ID');
              }
            }
          }
        }
      }
    });
  </script> -->

  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const labels = <?= $labelsJS ?>;
    const data = <?= $dataJS ?>;
    
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Pesanan Selesai',
          data: data,
          backgroundColor: '#002f6c'
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true,
            precision: 0,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  </script>
</body>
</html>
