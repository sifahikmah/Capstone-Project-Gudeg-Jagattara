<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}

include 'koneksi.php';
$id_user = $_SESSION['user']['id_user'];

$query = "SELECT m.nama_menu, m.harga, k.jumlah 
          FROM keranjang k 
          JOIN menu m ON k.id_menu = m.id_menu 
          WHERE k.id_user = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();

$pesanan = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
  $subTotal = $row['harga'] * $row['jumlah'];
  $total += $subTotal;
  $pesanan[] = array_merge($row, ['subtotal' => $subTotal]);
}

if (empty($pesanan)) {
  header("Location: keranjang.php?empty=true");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Checkout - Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #e3fadd;
      font-family: 'Segoe UI', sans-serif;
    }
    .container {
      max-width: 600px;
      margin: 0 auto;
    }
    .text-green {
      color: #0d3f15;
    }
    .card, form {
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      background-color: #ffffff;
    }
    form {
      padding: 24px;
    }
    label {
      font-weight: 500;
      color: #0d3f15;
    }
    .btn-success {
      padding: 12px;
      font-weight: bold;
      border-radius: 12px;
    }
    textarea, select, input[type="text"], input[type="file"] {
      border-radius: 10px;
    }
  </style>
</head>
<body>
<div class="container py-5">
  <h3 class="text-green fw-bold mb-4 text-center">Konfirmasi Pembayaran</h3>
  <a href="keranjang.php" class="btn btn-success mb-3" style="font-size: small;">
    <i class="bi bi-arrow-left-circle"></i> Kembali ke Keranjang
  </a>

  <!-- Ringkasan Pesanan -->
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="mb-3">Pesanan Anda:</h5>
      <ul class="list-group list-group-flush">
        <?php foreach ($pesanan as $item): ?>
          <li class="list-group-item d-flex justify-content-between">
            <div><?= htmlspecialchars($item['nama_menu']) ?> x <?= $item['jumlah'] ?></div>
            <div>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
      <hr>
      <div class="d-flex justify-content-between">
        <div>Total Pesanan</div>
        <div id="totalAwal">Rp<?= number_format($total, 0, ',', '.') ?></div>
      </div>
      <div class="d-flex justify-content-between" id="ongkirRow" style="display:none;">
        <div>Ongkos Kirim</div>
        <div id="ongkirDisplay">Rp0</div>
      </div>
      <hr>
      <div class="d-flex justify-content-between fw-bold text-success">
        <div>Total Akhir</div>
        <div id="totalAkhir">Rp<?= number_format($total, 0, ',', '.') ?></div>
      </div>
    </div>
  </div>

  <!-- Form Pembayaran -->
  <form id="formPembayaran" action="proses_checkout.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow">
    <div class="mb-3">
      <label for="pengiriman" class="form-label mb-0">Metode Pengiriman</label>
      <small class="text-muted d-block">*Jika memilih "Dikirim", ongkos kirim akan ditambahkan. Periksa kembali total akhir pesanan Anda.</small>
      <select name="pengiriman" id="pengiriman" class="form-select" required>
        <option value="">-- Pilih --</option>
        <option value="diambil">Diambil</option>
        <option value="dikirim">Dikirim</option>
      </select>
    </div>


    <div class="mb-3" id="alamatField" style="display: none;">
      <label for="alamat" class="form-label">Alamat Pengiriman</label>
      <textarea name="alamat" id="alamat" class="form-control" placeholder="Tulis alamat lengkap..."></textarea>
    </div>

    <div class="mb-3">
      <label for="pembayaran" class="form-label">Metode Pembayaran</label>
      <select name="pembayaran" id="pembayaran" class="form-select" required>
        <option value="">-- Pilih --</option>
        <option value="tunai">Tunai</option>
        <option value="transfer">Transfer Bank</option>
      </select>
    </div>

    <div class="mb-3" id="rekeningField" style="display: none;">
      <label class="form-label">Transfer ke Rekening:</label>
      <div class="bg-light p-2 rounded">
        <strong>Bank BRI</strong><br>
        No. Rekening: <strong>1234-5678-9012</strong><br>
        Atas Nama: <strong>Gudeg Jagattara</strong>
      </div>
    </div>

    <div class="mb-3" id="buktiTransferField" style="display: none;">
      <label for="bukti" class="form-label">Upload Bukti Transfer</label>
      <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*">
    </div>

    <div class="mb-3">
      <label for="catatan" class="form-label">Catatan Tambahan</label>
      <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Contoh: Tidak pedas, tambah sambal, dll."></textarea>
    </div>

    <div class="mb-3">
      <label for="wa" class="form-label">Nomor WhatsApp</label>
      <input type="text" name="wa" id="wa" class="form-control" placeholder="08xxxxxxxxxx" required>
    </div>

    <input type="hidden" name="total_akhir" id="inputTotalAkhir" value="<?= $total ?>">

    <div class="d-grid">
      <button type="submit" class="btn btn-success">Pesan Sekarang</button>
    </div>
  </form>
</div>

<script>
  const pengiriman = document.getElementById('pengiriman');
  const pembayaran = document.getElementById('pembayaran');
  const alamatField = document.getElementById('alamatField');
  const rekeningField = document.getElementById('rekeningField');
  const buktiTransferField = document.getElementById('buktiTransferField');
  const form = document.getElementById('formPembayaran');

  const ongkir = 10000;
  const ongkirRow = document.getElementById('ongkirRow');
  const ongkirDisplay = document.getElementById('ongkirDisplay');
  const totalAwal = <?= $total ?>;
  const totalAkhir = document.getElementById('totalAkhir');
  const inputTotalAkhir = document.getElementById('inputTotalAkhir');

  pengiriman.addEventListener('change', function () {
    const dikirim = this.value === 'dikirim';
    alamatField.style.display = dikirim ? 'block' : 'none';

    if (dikirim) {
      ongkirRow.style.display = 'flex';
      ongkirDisplay.innerText = 'Rp' + ongkir.toLocaleString('id-ID');
      const totalDenganOngkir = totalAwal + ongkir;
      totalAkhir.innerText = 'Rp' + totalDenganOngkir.toLocaleString('id-ID');
      inputTotalAkhir.value = totalDenganOngkir;
    } else {
      ongkirRow.style.display = 'none';
      totalAkhir.innerText = 'Rp' + totalAwal.toLocaleString('id-ID');
      inputTotalAkhir.value = totalAwal;
    }
  });

  pembayaran.addEventListener('change', function () {
    const isTransfer = this.value === 'transfer';
    rekeningField.style.display = isTransfer ? 'block' : 'none';
    buktiTransferField.style.display = isTransfer ? 'block' : 'none';
  });

  form.addEventListener('submit', function (e) {
    if (pengiriman.value === 'dikirim' && document.getElementById('alamat').value.trim() === '') {
      e.preventDefault();
      alert('Silakan isi alamat untuk pengantaran!');
    } else if (pembayaran.value === 'transfer' && document.getElementById('bukti').files.length === 0) {
      e.preventDefault();
      alert('Silakan upload bukti transfer!');
    }
  });
</script>

</body>
</html>
