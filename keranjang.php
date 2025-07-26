<?php
session_start();
if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}
include 'koneksi.php';

$id_user = $_SESSION['user']['id_user'];

$query = "SELECT k.id_keranjang, m.nama_menu, m.harga, m.gambar, k.jumlah 
          FROM keranjang k
          JOIN menu m ON k.id_menu = m.id_menu
          WHERE k.id_user = $id_user";
$result = mysqli_query($koneksi, $query);

// ambil data keranjang dalam bentuk array
$dataKeranjang = [];
while ($row = mysqli_fetch_assoc($result)) {
  $dataKeranjang[] = $row;
}

// untuk badge jumlah keranjang
$total_keranjang = mysqli_query($koneksi, "SELECT SUM(jumlah) AS total FROM keranjang WHERE id_user = $id_user");
$jumlah_keranjang = mysqli_fetch_assoc($total_keranjang)['total'] ?? 0;
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Keranjang Anda - Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #e3fadd;
    }
    .text-green {
      color: #0d3f15;
    }
    .nav-link.active {
      font-weight: bold;
      color: #0d3f15 !important;
      border-bottom: 2px solid #135f22;
    }
    .card h6 {
      font-weight: 600;
    }

    .sticky-footer {
      position: sticky;
      bottom: 0;
      z-index: 999;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
      .sticky-footer {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
      }

      .sticky-footer strong {
        font-size: 16px;
      }

      .sticky-footer .btn {
        width: 100%;
      }
    }

    /* New responsive padding & spacing */
    .card-body .row {
      margin-bottom: 0.25rem;
    }

    .card-body {
      padding: 1rem 0.5rem;
    }

    .img-fluid {
      width: 100%;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
    }

    @media (min-width: 768px) {
      .container {
        max-width: 750px;
      }

      .card-body {
        padding: 1rem;
      }
    }
    /* Bulat dan kecilkan tombol +/- */
    .qty-btn {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      padding: 0;
      font-size: 16px;
      line-height: 1;
      display: inline-flex;
      justify-content: center;
      align-items: center;
    }

    /* Subtotal dan tombol delete mepet kanan */
    .subtotal-delete {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 0.3rem;
      padding-right: 0.25rem;
    }

    @media (max-width: 576px) {
      .subtotal-delete {
        padding-right: 0;
        align-items: flex-start;
        text-align: left;
      }
    }
  </style>

</head>

<body>
  <div class="container py-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold text-green m-0">Keranjang Anda</h4>
      <a href="keranjang.php" class="position-relative">
        <i class="fas fa-shopping-cart fa-lg text-success"></i>
        <span id="jumlahKeranjang" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
          <?= $jumlah_keranjang ?>
        </span>
      </a>
    </div>

    <?php if (empty($dataKeranjang)): ?>
      <div class="alert alert-warning text-center">Keranjang Anda Masih Kosong. Silahkan <a href="menu.php" class=" text-decoration-underline text-danger">Klik Disini</a> untuk Pilih Pesanan😉
      </div>
    <?php else: ?>
      <?php $grandTotal = 0; ?>
      <div class="card shadow-sm border-0 mb-3">
        <div class="card-body px-3 py-2">
          <?php foreach ($dataKeranjang as $index => $row): ?>
            <?php
              $subTotal = $row['harga'] * $row['jumlah'];
              $grandTotal += $subTotal;
            ?>
            <div class="row align-items-center g-3 py-3 <?= $index < count($dataKeranjang) - 1 ? 'border-bottom' : '' ?>">
              <!-- Gambar -->
              <div class="col-3 col-md-2">
                <img src="<?= $row['gambar'] ?>" class="img-fluid rounded" alt="<?= $row['nama_menu']; ?>">
              </div>

              <!-- Info & Form -->
              <div class="col-6 col-md-6">
                <div class="fw-semibold"><?= $row['nama_menu']; ?></div>
                <small class="text-muted">Rp<?= number_format($row['harga'], 0, ',', '.'); ?></small>

                <form method="post" action="update_keranjang.php" class="mt-2 d-flex align-items-center gap-2">
                  <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang']; ?>">
                  <button name="action" value="decrease" class="btn btn-outline-secondary qty-btn">−</button>
                  <span><?= $row['jumlah']; ?></span>
                  <button name="action" value="increase" class="btn btn-outline-secondary qty-btn">+</button>
                </form>
              </div>

              <!-- Subtotal & Hapus -->
              <div class="col-3 subtotal-delete">
                <div class="text-success fw-semibold small">Subtotal: Rp<?= number_format($subTotal, 0, ',', '.'); ?></div>
                <form method="post" action="hapus_keranjang.php" class="mt-1">
                  <input type="hidden" name="id_keranjang" value="<?= $row['id_keranjang']; ?>">
                  <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body d-flex justify-content-between align-items-center">
          <strong class="text-success">Total Pesanan</strong>
          <strong class="text-success">Rp<?= number_format($grandTotal, 0, ',', '.'); ?></strong>
        </div>
      </div>
      <!-- Tombol Tambah Pesanan dan Checkout -->
      <div class="d-flex justify-content-between mt-4">
        <!-- Tombol Tambah Pesanan -->
        <a href="menu.php" class="btn btn-outline-success">
           <i class="fa-solid fa-backward"></i> Kembali ke Menu
        </a>

        <!-- Tombol Checkout -->
        <a href="checkout.php" class="btn btn-success">
           Checkout
        </a>
      </div>

    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
 