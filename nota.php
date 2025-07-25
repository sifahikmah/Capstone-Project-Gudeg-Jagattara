<?php
include 'koneksi.php';

$id = $_GET['id'] ?? 0;

// Ambil data pesanan + nama user
$stmt = $koneksi->prepare("SELECT p.*, u.username AS nama_pembeli FROM pesanan p JOIN users u ON p.id_user = u.id_user WHERE p.id_pesanan = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$pesanan = $stmt->get_result()->fetch_assoc();
$stmt->close();



// Ambil detail menu
$menuQuery = "
  SELECT IFNULL(m.nama_menu, d.nama_menu_manual) AS nama, d.jumlah, d.harga_satuan
  FROM detail_pesanan d
  LEFT JOIN menu m ON m.id_menu = d.id_menu
  WHERE d.id_pesanan = ?
";
$stmtMenu = $koneksi->prepare($menuQuery);
$stmtMenu->bind_param("i", $id);
$stmtMenu->execute();
$menuResult = $stmtMenu->get_result();
$stmtMenu->close();

function formatRupiah($angka) {
  return 'Rp. ' . number_format($angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Nota</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #e7ffe7;
      padding: 20px;
    }
    .nota {
      background-color: #fff;
      padding: 30px;
      border: 1px solid #ccc;
      max-width: 500px;
      margin-top: 20px;
    }
    .nota h5,h6 {
      font-weight: bold;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="nota mx-auto">
  <div class="col-md-12 text-center">
    <img src="./assets/logo.png" alt="Logo" width="150">
  </div>
  <h5>NOTA PEMBELIAN</h5>
  <h6>Gudeg Jagattara</h6>
  <p style="font-size: x-small; text-align: center;">
    Jl.Sukoharjo Km.3 RT.02/RW.03,Wonokerto,Kec.Leksono, Kab.Wonosobo (Belakang Alfamart Wonokerto) +6282313452222
  </p>
  <hr>

  <table class="table table-borderless table-sm w-100">
    <tbody>
      <tr>
        <td><strong>Nama Pembeli</strong></td>
        <td>: <?= htmlspecialchars($pesanan['nama_pembeli']) ?></td>
      </tr>
      <tr>
        <td><strong>Tanggal</strong></td>
        <td>: <?= date('d M Y', strtotime($pesanan['created_at'])) ?></td>
      </tr>
      <tr>
        <td><strong>Metode Pembayaran</strong></td>
        <td>: <?= htmlspecialchars($pesanan['metode_pembayaran']) ?></td>
      </tr>
      <tr>
        <td><strong>Pengantaran</strong></td>
        <td>: <?= htmlspecialchars($pesanan['metode_pengantaran']) ?></td>
      </tr>
      <tr>
        <td><strong>Alamat Kirim</strong></td>
        <td>: <?= $pesanan['alamat'] ? htmlspecialchars($pesanan['alamat']) : '-' ?></td>
      </tr>
    </tbody>
  </table>

  <hr>
  <p><strong>Pesanan:</strong></p>
  <table class="table table-borderless table-sm">
    <tbody>
      <?php $total = 0; while ($item = $menuResult->fetch_assoc()): 
        $subtotal = $item['jumlah'] * $item['harga_satuan'];
        $total += $subtotal;
      ?>
      <tr>
        <td><?= htmlspecialchars($item['nama']) ?></td>
        <td>x<?= $item['jumlah'] ?></td>
        <td class="text-end"><?= formatRupiah($subtotal) ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <table class="table table-borderless table-sm">
    <tbody>
      <tr>
        <td colspan="2" class="text-end"><strong>Total Pembayaran:</strong></td>
        <td class="text-end"><strong><?= formatRupiah($total) ?></strong></td>
      </tr>
    </tbody>
  </table>

  <hr>
  <p class="text-center"><strong>Terima kasih atas pesanan Anda</strong></p>
</div>
<div class="text-center mt-4 d-flex justify-content-center gap-3">
  <a href="index.php" class="btn btn-success px-4">
    <i class="bi bi-arrow-left-circle"></i> Dashboard
  </a>
  <a href="https://wa.me/6281327456736?text=Halo%20saya%20ingin%20bertanya%20mengenai%20pemesanan%20yang%20sudah%20saya%20buat."  class="btn btn-outline-success px-4" target="_blank">
    <i class="bi bi-whatsapp"></i> Hubungi Penjual
  </a>
</div>

</body>
</html>
