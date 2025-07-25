<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}

include 'koneksi.php';
$id_user = $_SESSION['user']['id_user'];

// Ambil isi keranjang
$query = "SELECT m.nama_menu, m.harga, k.jumlah 
          FROM keranjang k 
          JOIN menu m ON k.id_menu = m.id_menu 
          WHERE k.id_user = $id_user";
$result = mysqli_query($koneksi, $query);

$pesanan = [];
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
  $subTotal = $row['harga'] * $row['jumlah'];
  $total += $subTotal;
  $pesanan[] = array_merge($row, ['subtotal' => $subTotal]);
}

if (empty($pesanan)) {
  // kalau keranjang kosong, redirect balik
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
  <style>
    body {
      background-color:  #e3fadd;
    }
    .text-green {
      color: #0d3f15;
    }
    .card {
      border-radius: 12px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h3 class="text-green fw-bold mb-4">Konfirmasi Pembayaran</h3>

    <!-- Ringkasan Pesanan -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <h5 class="mb-3">Pesanan Anda:</h5>
        <ul class="list-group list-group-flush">
          <?php foreach ($pesanan as $item): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <?= $item['nama_menu'] ?> x <?= $item['jumlah'] ?>
              </div>
              <div>Rp<?= number_format($item['subtotal'], 0, ',', '.') ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
        <hr>
        <div class="d-flex justify-content-between fw-bold text-success">
          <div>Total</div>
          <div>Rp<?= number_format($total, 0, ',', '.') ?></div>
        </div>
      </div>
    </div>

    <!-- Form Pembayaran -->
    <form id="formPembayaran" action="proses_checkout.php" method="POST" enctype="multipart/form-data" class="p-4 border rounded shadow">
      <!-- Metode Pengiriman -->
      <div class="mb-3">
        <label for="pengiriman" class="form-label">Metode Pengiriman</label>
        <select name="pengiriman" id="pengiriman" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="diambil">Diambil</option>
          <option value="dikirim">Dikirim</option>
        </select>
      </div>
            
      <!-- Form Alamat - Disembunyikan awalnya -->
      <div class="mb-3" id="alamatField" style="display: none;">
        <label for="alamat" class="form-label">Alamat Pengiriman</label>
        <textarea name="alamat" id="alamat" class="form-control" placeholder="Tulis alamat lengkap..."></textarea>
      </div>

      <!-- Metode Pembayaran -->
      <div class="mb-3">
        <label for="pembayaran" class="form-label">Metode Pembayaran</label>
        <select name="pembayaran" id="pembayaran" class="form-select" required>
          <option value="">-- Pilih --</option>
          <option value="tunai">Tunai</option>
          <option value="transfer">Transfer Bank</option>
        </select>
      </div>

      <!-- Info Rekening (jika transfer) -->
      <div class="mb-3" id="rekeningField" style="display: none;">
        <label class="form-label">Transfer ke Rekening:</label>
        <div class="bg-light p-2 rounded">
          <strong>Bank BRI</strong><br>
          No. Rekening: <strong>1234-5678-9012</strong><br>
          Atas Nama: <strong>Gudeg Jagattara</strong>
        </div>
      </div>

      <!-- Upload Bukti Transfer -->
      <div class="mb-3" id="buktiTransferField" style="display: none;">
        <label for="bukti" class="form-label">Upload Bukti Transfer</label>
        <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*">
      </div>

      <!-- Catatan -->
      <div class="mb-3">
        <label for="catatan" class="form-label">Catatan Tambahan</label>
        <textarea name="catatan" id="catatan" class="form-control" rows="2" placeholder="Contoh: Tidak pedas, tambah sambal, dll."></textarea>
      </div>

      <!-- Nomor WhatsApp -->
      <div class="mb-3">
        <label for="wa" class="form-label">Nomor WhatsApp</label>
        <input type="text" name="wa" id="wa" class="form-control" placeholder="08xxxxxxxxxx" required>
      </div>

      <!-- Tombol Pesan -->
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

    // Tampilkan alamat jika "dikirim"
    pengiriman.addEventListener('change', function () {
      alamatField.style.display = this.value === 'dikirim' ? 'block' : 'none';
    });

    // Tampilkan rekening & upload jika "transfer"
    pembayaran.addEventListener('change', function () {
      const isTransfer = this.value === 'transfer';
      rekeningField.style.display = isTransfer ? 'block' : 'none';
      buktiTransferField.style.display = isTransfer ? 'block' : 'none';
    });

    // Validasi submit
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
