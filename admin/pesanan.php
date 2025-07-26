<?php
session_start();
include '../koneksi.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['username'])) {
    // Jika belum login, kembalikan ke halaman login
    header("Location: login.php");
    exit();
}

// Query untuk menghitung jumlah pesanan dengan status 'menunggu'
$notifQuery = "SELECT COUNT(*) AS jumlah_baru FROM pesanan WHERE status = 'menunggu'";

// Jalankan query ke database
$notifResult = $koneksi->query($notifQuery);

// Inisialisasi jumlah notifikasi ke 0
$jumlahNotif = 0;

// Jika query berhasil dan ada hasilnya
if ($notifResult && $notifRow = $notifResult->fetch_assoc()) {
    // Ambil nilai jumlah pesanan yang 'menunggu'
    $jumlahNotif = $notifRow['jumlah_baru'];
}


// Ambil semua pesanan + user + menu (pakai JOIN langsung)
$query = "
  SELECT p.*, u.username AS nama_user, u.username,
    GROUP_CONCAT(CONCAT(
      IFNULL(m.nama_menu, d.nama_menu_manual), ' (', d.jumlah, 'x)'
    ) SEPARATOR '<br>') AS daftar_menu
  FROM pesanan p
  LEFT JOIN users u ON p.id_user = u.id_user
  LEFT JOIN detail_pesanan d ON p.id_pesanan = d.id_pesanan
  LEFT JOIN menu m ON d.id_menu = m.id_menu
  GROUP BY p.id_pesanan
  ORDER BY p.id_pesanan DESC
";
$result = $koneksi->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pesanan Masuk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f8f9fa; }
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
    .btn-add {
      background-color: #002244;
      color: white;
    }
    .content { padding: 30px; }
    .table th {
      background-color: #0d3b66;
      color: white;
      text-align: center;
    }
    .table td {
      vertical-align: middle;
      text-align: left;
    }
    .btn-aksi {
      margin: 2px 0;
      width: 70px;
    }
    .scroll-table {
      max-height: 400px;
      overflow-y: auto;
    }
    .notif-icon {
      position: relative;
      display: inline-block;
      font-size: 20px;
      text-decoration: none;
    }
    .notif-badge {
      position: absolute;
      top: -5px;       /* Naikin posisi ke atas */
      right: -8px;     /* Geser dikit ke kanan */
      background: red;
      color: white;
      border-radius: 50%;
      padding: 2px 6px;
      font-size: 10px;
      font-weight: bold;
      line-height: 1;
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

    <!-- Main content -->
    <div class="col-md-10 content">
      <div class="d-flex justify-content-end align-items-center mb-3">
          <!-- Ikon notifikasi -->
          <a href="" class="notif-icon active">
            🔔
            <!-- Jika ada pesanan baru (jumlahNotif > 0), tampilkan badge jumlahnya -->
            <?php if ($jumlahNotif > 0): ?>
              <span class="notif-badge"><?= $jumlahNotif ?></span>
            <?php endif; ?>
          </a>
      </div>

      <h4 class="fw-bold">Pesanan Masuk</h4>
      <!-- Tombol untuk menuju halaman tambah pesanan manual -->
      <a href="tambah_pesanan.php" class="btn btn-add mb-3 mt-3">+ Tambah Pesanan</a>

      <div class="scroll-table">
        <table class="table table-bordered align-middle">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Menu</th>
              <th>Total (Rp)</th>
              <th>Metode</th>
              <th>Alamat</th>
              <th>Catatan</th>
              <th>Bukti Transfer</th>
              <th>No. WA</th>
              <th>Waktu Pemesanan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php if ($result->num_rows > 0): $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <!-- Menampilkan nama dari user login atau pembeli manual -->
              <td>
                <?= htmlspecialchars($row['nama_user'] ?: $row['username'] ?: $row['nama_pembeli_manual']) ?>
              </td>
              <!-- Daftar menu yang dipesan -->
              <td><?= $row['daftar_menu'] ?: '-' ?></td>
              <!-- Total harga dengan format rupiah -->
              <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
              <!-- Metode pengantaran dan pembayaran -->
              <td><?= htmlspecialchars($row['metode_pengantaran']) ?><br><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
              <!-- Alamat pengiriman, jika kosong tampil "-" -->
              <td><?= $row['alamat'] ? htmlspecialchars($row['alamat']) : '-' ?></td>
              <td><?= $row['catatan'] ? nl2br(htmlspecialchars($row['catatan'])) : '-' ?></td>
              <!-- Tampilkan link bukti transfer jika ada -->
              <td>
                <?php if (!empty($row['bukti_transfer'])): ?>
                  <a href="../assets/bukti/<?= htmlspecialchars($row['bukti_transfer']) ?>" target="_blank">Lihat Bukti</a>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <!-- Nomor WA, otomatis ubah 08 jadi 628 dan jadi link ke WhatsApp -->
              <td>
                <?php if (!empty($row['nomor_wa'])): ?>
                  <?php 
                    $wa_nomor = preg_replace('/^0/', '62', $row['nomor_wa']); // ubah 08xx jadi 628xx
                  ?>
                  <a href="https://wa.me/<?= $wa_nomor ?>" target="_blank">
                    <?= htmlspecialchars($row['nomor_wa']) ?>
                  </a>
                <?php else: ?>
                  -
                <?php endif; ?>
              </td>
              <!-- Tanggal dan waktu pemesanan -->
              <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
              <!-- Status pesanan: menunggu / diterima / ditolak -->
              <td>
                <?php
                  $status = strtolower($row['status']);
                  if ($status === 'menunggu') echo '<span class="badge bg-warning text-dark">Menunggu</span>';
                  elseif ($status === 'terima') echo '<span class="badge bg-success">Diterima</span>';
                  elseif ($status === 'tolak') echo '<span class="badge bg-danger">Ditolak</span>';
                  else echo '-';
                ?>
              </td>
              <!-- Aksi: tombol Terima dan Tolak jika status masih menunggu -->
              <td>
                <?php if (strtolower($row['status']) === 'menunggu'): ?>
                  <a href="terima_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-success btn-sm btn-aksi">Terima</a>
                  <a href="tolak_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-danger btn-sm btn-aksi">Tolak</a>
                <?php else: ?>
                  <span class="text-muted">Selesai</span>
                <?php endif; ?>
              </td>

            </tr>
          <?php endwhile; else: ?>
            <!-- Jika belum ada data pesanan -->
            <tr><td colspan="11">Belum ada pesanan masuk.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
