<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rincian Pembayaran</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #E3FFE8; }
    .total { text-align: center; font-size: 28px; margin-bottom: 30px; font-weight: bold; }
    .note { font-size: 12px; color: #333; margin-top: 10px; margin-bottom: 25px; }
    .btn-kirim-kecil {
      background-color: #40d463;
      border: none;
      font-weight: bold;
      font-size: 14px;
      color: #ffffff;
      border-radius: 5px;
      text-align: center;
    }
    .btn-kirim-kecil:hover { background-color: #2cb34e; }
  </style>
</head>
<body>
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 bg-white p-4 shadow rounded">
        <h1 class="text-center mb-3">Rincian Pembayaran</h1>
        <div class="total" id="totalDisplay">Rp 0</div>

        <form id="formPembayaran" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="pengantaran" class="form-label">Metode Pengantaran</label>
            <select class="form-select" id="pengantaran" name="metode_pengantaran" required>
              <option value="">Pilih metode pengantaran</option>
              <option value="Diantar">Di antar</option>
              <option value="Diambil">Di ambil</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="pembayaran" class="form-label">Metode Pembayaran</label>
            <select class="form-select" id="pembayaran" name="metode_pembayaran" required>
              <option value="">Pilih metode pembayaran</option>
              <option value="Tunai">Tunai</option>
              <option value="Transfer">Transfer</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="rekening" class="form-label">Nomor Rekening Penjual</label>
            <input type="text" class="form-control" id="rekening" value="1234567890" readonly>
          </div>

          <div class="mb-3">
            <label for="nama" class="form-label">Nama Pembeli</label>
            <input type="text" class="form-control" id="nama" name="nama_pembeli" placeholder="Masukan Nama Anda" required>
          </div>

          <div class="mb-3">
            <label for="alamat" class="form-label">Alamat Pembeli</label>
            <input type="text" class="form-control" id="alamat" name="alamat" placeholder="Masukan Alamat">
            <div class="note">*Jika memilih metode pengantaran (ambil di tempat), tidak perlu menulis alamat.</div>
          </div>

          <div class="mb-3">
            <label for="catatan" class="form-label">Catatan Tambahan</label>
            <textarea id="catatan" name="catatan" class="form-control" placeholder="Isi jika ada catatan tambahan..."></textarea>
          </div>

          <div class="mb-4">
            <label for="bukti" class="form-label">Bukti Transfer</label>
            <input type="file" class="form-control" id="bukti" name="bukti_transfer" accept="image/*">
          </div>

          <input type="hidden" name="pesanan" id="daftarPesananJson">
          <input type="hidden" name="total" id="totalHidden">

          <div class="text-center">
            <button type="submit" class="btn btn-kirim-kecil px-4 py-2">Kirim</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const pesanan = JSON.parse(localStorage.getItem("pesanan")) || [];
      let total = 0;

      if (pesanan.length === 0) {
        alert("Tidak ada pesanan. Silakan pilih menu terlebih dahulu.");
        window.location.href = "menu.php";
        return;
      }

      pesanan.forEach(item => {
        total += item.harga * item.jumlah;
      });

      document.getElementById("totalDisplay").textContent = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(total);
      document.getElementById("totalHidden").value = total;
      document.getElementById("daftarPesananJson").value = JSON.stringify(pesanan);

      document.getElementById("formPembayaran").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        if (!formData.get("nama_pembeli") || !formData.get("metode_pengantaran") || !formData.get("metode_pembayaran")) {
          alert("Harap lengkapi semua data.");
          return;
        }

        fetch("simpan_pesanan.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(res => {
          if (res.status === "success") {
            localStorage.removeItem("pesanan");
            window.location.href = "proses.php?id=" + res.id_pesanan;
          } else {
            alert("Gagal menyimpan pesanan.");
          }
        })
        .catch(err => {
          alert("Terjadi kesalahan saat mengirim pesanan.");
          console.error(err);
        });

      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
