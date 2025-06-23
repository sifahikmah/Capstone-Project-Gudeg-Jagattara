<?php include '../koneksi.php'; 

$query = "
  SELECT p.*, GROUP_CONCAT(CONCAT(m.nama_menu, ' (', d.jumlah, 'x)') SEPARATOR '<br>') as daftar_menu
  FROM pesanan p
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
      text-align: center;
    }
    .btn-aksi {
      margin: 2px 0;
      width: 70px;
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
        <h4 class="fw-bold">Pesanan Masuk</h4>
                <a href="tambahmenu.php" class="btn btn-add mb-3 mt-3">+ Tambah Menu</a>

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
              <th>Waktu Pemesanan</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows > 0): $no = 1; while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                <td><?= $row['daftar_menu'] ?></td>
                <td>Rp<?= number_format($row['total'], 0, ',', '.') ?></td>
                <td><?= $row['metode_pengantaran'] ?><br><?= $row['metode_pembayaran'] ?></td>
                <td><?= $row['alamat'] ? htmlspecialchars($row['alamat']) : '-' ?></td>
                <td><?= $row['catatan'] ? nl2br(htmlspecialchars($row['catatan'])) : '-' ?></td>
                <td>
                  <?php if (!empty($row['bukti_transfer'])): ?>
                    <a href="../<?= $row['bukti_transfer'] ?>" target="_blank">Lihat Bukti</a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
              <td>
                <?php if ($row['status'] === 'menunggu'): ?>
                  <a href="terima_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-success btn-sm btn-aksi">Terima</a>
                  <a href="tolak_pesanan.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-danger btn-sm btn-aksi">Tolak</a>
                <?php else: ?>
                  <span class="text-muted">Selesai</span>
                <?php endif; ?>
              </td>
              </tr>
            <?php endwhile; else: ?>
              <tr><td colspan="11">Belum ada pesanan masuk.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
