<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Rincian Pemesanan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #E8FDE8;
      font-family: 'Segoe UI', sans-serif;
    }

    .order-summary {
      background: #ffffff;
      border-radius: 16px;
      padding: 2rem;
      margin: 3rem auto;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      max-width: 100%;
    }

    .order-summary h4 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 1.5rem;
      color: #333;
    }

    .order-table th, .order-table td {
      text-align: center;
      vertical-align: middle;
    }

    .order-table tfoot td {
      font-weight: bold;
      background-color: #f0f0f0;
    }

    .btn-custom-yellow {
      background-color: #FFF176;
      color: #333;
      font-weight: 600;
      border: none;
    }

    .btn-custom-yellow:hover {
      background-color: #FDD835;
    }

    .btn-custom-green {
      background-color: #66BB6A;
      color: #fff;
      font-weight: 600;
      border: none;
    }

    .btn-custom-green:hover {
      background-color: #4CAF50;
    }

    .qty-btn {
      padding: 4px 10px;
      font-size: 14px;
    }

    @media (max-width: 576px) {
      .qty-btn {
        padding: 2px 6px;
        font-size: 12px;
      }

      .order-summary {
        padding: 1rem;
        margin: 1rem;
      }

      .btn-custom-yellow,
      .btn-custom-green {
        width: 100%;
        margin-bottom: 0.5rem;
      }

      .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="order-summary">
      <h4>Pesanan Anda</h4>
      <div class="table-responsive">
        <table class="table table-bordered order-table" id="orderTable">
          <thead class="table-light">
            <tr>
              <th>Pesanan</th>
              <th>Jumlah</th>
              <th>Harga</th>
              <th>Total</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="orderBody">
            <!-- Akan diisi lewat JavaScript -->
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end">Total</td>
              <td id="grandTotal">Rp0</td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- <div class="mb-3">
        <label for="catatan" class="form-label fw-medium">Catatan Tambahan</label>
        <textarea id="catatan" class="form-control" rows="2" placeholder="Isi jika ada catatan tambahan..."></textarea>
      </div> -->

      <div class="d-flex justify-content-between mt-4 flex-wrap gap-2">
        <a href="menu.php" class="btn btn-custom-yellow px-4">Tambah Pesanan</a>
        <a href="pembayaran.php" class="btn btn-custom-green px-4">Checkout</a>
      </div>
    </div>
  </div>

  <script>
    function formatRupiah(angka) {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
      }).format(angka);
    }

    function renderPesanan() {
      const pesanan = JSON.parse(localStorage.getItem('pesanan')) || [];
      const tbody = document.getElementById('orderBody');
      tbody.innerHTML = ''; // kosongkan dulu

      let grandTotal = 0;

      pesanan.forEach((item, index) => {
        const total = item.harga * item.jumlah;
        grandTotal += total;

        const row = document.createElement('tr');
        row.innerHTML = `
          <td>${item.nama}</td>
          <td>
            <button class="btn btn-sm btn-danger qty-btn" onclick="updateQty(${index}, -1)">-</button>
            <span class="mx-2 qty">${item.jumlah}</span>
            <button class="btn btn-sm btn-success qty-btn" onclick="updateQty(${index}, 1)">+</button>
          </td>
          <td class="harga" data-harga="${item.harga}">${formatRupiah(item.harga)}</td>
          <td class="total-item" data-total="${total}">${formatRupiah(total)}</td>
          <td><button class="btn btn-sm btn-outline-danger" onclick="hapusPesanan(${index})">Hapus</button></td>
        `;
        tbody.appendChild(row);
      });

      document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
    }

    function updateQty(index, delta) {
      const pesanan = JSON.parse(localStorage.getItem('pesanan')) || [];
      pesanan[index].jumlah += delta;
      if (pesanan[index].jumlah <= 0) {
        pesanan.splice(index, 1); // hapus
      }
      localStorage.setItem('pesanan', JSON.stringify(pesanan));
      renderPesanan();
    }

    function hapusPesanan(index) {
      const pesanan = JSON.parse(localStorage.getItem('pesanan')) || [];
      pesanan.splice(index, 1);
      localStorage.setItem('pesanan', JSON.stringify(pesanan));
      renderPesanan();
    }

    // Jalankan saat halaman dimuat
    renderPesanan();
  </script>
</body>
</html>
