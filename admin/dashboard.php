<?php
session_start();
include '../koneksi.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Query untuk menghitung total jumlah menu dari tabel 'menu'
$resultMenu = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM menu");

// Inisialisasi variabel $totalMenu dengan nilai default 0
$totalMenu = 0;

// Jika query berhasil dijalankan, ambil nilai total dari hasilnya
if ($resultMenu) {
    $totalMenu = mysqli_fetch_assoc($resultMenu)['total'] ?? 0;
}

// Query jumlah pesanan masuk (status = 'menunggu')
$resultPesanan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM pesanan WHERE status = 'menunggu'");
$pesananMasuk = 0;
if ($resultPesanan) {
    $pesananMasuk = mysqli_fetch_assoc($resultPesanan)['total'] ?? 0;
}

// Query total penjualan (status = 'terima')
$resultPenjualan = mysqli_query($koneksi, "SELECT SUM(total) AS total FROM pesanan WHERE status = 'terima'");
$totalPenjualan = 0;
if ($resultPenjualan) {
    $totalPenjualan = mysqli_fetch_assoc($resultPenjualan)['total'] ?? 0;
}

// Query untuk ambil jumlah pelanggan unik dari user dan manual
// Hitung jumlah pelanggan unik dari tabel pesanan, baik yang login (id_user) maupun pembeli manual
$resultPelanggan = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total FROM (
        SELECT DISTINCT id_user FROM pesanan WHERE id_user IS NOT NULL
        UNION
        SELECT DISTINCT nama_pembeli_manual FROM pesanan WHERE id_user IS NULL AND nama_pembeli_manual IS NOT NULL
    ) AS unique_customers
");

$jumlahPelanggan = 0;
if ($resultPelanggan) {
    $jumlahPelanggan = mysqli_fetch_assoc($resultPelanggan)['total'] ?? 0;
}

// Grafik penjualan harian
// Ambil total pesanan yang diterima ('terima') per hari (Senin-Minggu)
$salesQuery = "SELECT DAYNAME(created_at) AS hari, COUNT(*) AS total FROM pesanan WHERE status = 'terima' GROUP BY DAYOFWEEK(created_at)";
$salesResult = mysqli_query($koneksi, $salesQuery);

// Siapkan struktur data awal untuk semua hari (nilai default = 0)
$hariList = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$dataPenjualan = array_fill_keys($hariList, 0);

// Isi data penjualan sesuai hasil query
if ($salesResult) {
    while ($row = mysqli_fetch_assoc($salesResult)) {
        $dataPenjualan[$row['hari']] = $row['total'];
    }
}

// Ubah nama hari ke Bahasa Indonesia
$labelHari = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Minggu',
];

// Ubah ke format JSON untuk digunakan di JavaScript (Chart.js)
$labelsJS = json_encode(array_values($labelHari));
$dataJS = json_encode(array_values($dataPenjualan));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

      <!-- Bagian kanan atas: Menampilkan nama pengguna yang login -->
      <!-- <div class="d-flex justify-content-end align-items-center mb-3">
          <div class="me-2">👤 <strong><?= $_SESSION['username']; ?></strong></div>
      </div> -->
        
      <h3 class="mb-4"><strong>Dashboard Admin</strong></h3>

        <!-- Kartu ringkasan informasi -->
        <div class="row mb-4">
          <!-- Total menu tersedia -->
          <div class="col-md-3">
            <div class="summary-box">
              Total Menu
              <div class="fs-4 text-primary mt-2"><?= $totalMenu ?></div>
            </div>
          </div>
          <div class="col-md-3">
            
            <!-- Jumlah pesanan masuk dengan status 'menunggu' -->
            <div class="summary-box">
              Pesanan Masuk
              <div class="fs-4 text-primary mt-2"><?= $pesananMasuk ?></div>
            </div>
          </div>
          <div class="col-md-3">

            <!-- Total pendapatan dari semua pesanan -->
            <div class="summary-box">
              Total Penjualan
              <div class="fs-4 text-primary mt-2">Rp <?= number_format($totalPenjualan, 0, ',', '.') ?></div>
            </div>
          </div>
          <div class="col-md-3">

            <!-- Jumlah pelanggan unik (login dan pembeli manual) -->
            <div class="summary-box">
              Pelanggan
              <div class="fs-4 text-primary mt-2"><?= $jumlahPelanggan ?></div>
            </div>
          </div>
        </div>

        <!-- Grafik penjualan (akan dirender oleh Chart.js) -->
        <canvas id="salesChart" height="100"></canvas>
      </div>
    </div>
  </div>



  <script>
  // Ambil elemen canvas tempat grafik akan ditampilkan
  const ctx = document.getElementById('salesChart').getContext('2d');

  // Data label (nama hari) dari PHP
  const labels = <?= $labelsJS ?>;

  // Data jumlah pesanan selesai dari PHP
  const data = <?= $dataJS ?>;

  // Buat grafik batang (bar chart)
  new Chart(ctx, {
    type: 'bar', // Jenis grafik: batang
    data: {
      labels: labels, // Label sumbu X 
      datasets: [{
        label: 'Jumlah Pesanan Selesai', 
        data: data, // Angka jumlah pesanan
        backgroundColor: '#002f6c' // Warna batang grafik
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true, // Sumbu Y dimulai dari 0
          precision: 0, // Angka bulat (tidak pakai koma)
          ticks: { stepSize: 1 } // Jarak angka di sumbu Y per 1
        }
      }
    }
  });
</script>

</body>
</html>
