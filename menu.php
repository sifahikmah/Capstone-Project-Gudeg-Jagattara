<?php
$koneksi = new mysqli("localhost", "root", "", "db_gudeg");

session_start(); 
$koneksi = new mysqli("localhost", "root", "", "db_gudeg");


// Ambil data dari tabel menu
$result = $koneksi->query("SELECT * FROM menu");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Menu - Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #e6ffe6;
    }
    .nav-link.active {
      font-weight: bold;
      color: #0d3f15 !important;
      border-bottom: 2px solid #135f22;
    }
    .btn-green {
      background-color: #114d1a;
      color: white;
    }
    .btn-green:hover {
      background-color: #1c8430;
    }
    .menu-container {
      display: flex;
      gap: 2rem;
      justify-content: center;
      flex-wrap: wrap;
    }
    .menu-card {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 260px;
      padding: 1.5rem 1rem;
      text-align: center;
    }
    .img-wrapper {
      background-color: #366b30;
      border-radius: 50%;
      width: 200px;
      height: 200px;
      margin: 0 auto 1rem;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .img-wrapper img {
      width: 180px;
      height: 180px;
      border-radius: 50%;
      object-fit: cover;
    }
    h3 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    p {
      font-size: 14px;
      margin-bottom: 1rem;
    }
    strong {
      font-size: 18px;
      font-weight: bold;
      color: #000;
    }
    .menu-card:hover {
      transform: translateY(-10px);
    }
    /* Overlay blur saat notifikasi tampil */
    .blur-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      backdrop-filter: blur(4px);
      background-color: rgba(0, 0, 0, 0.2);
      z-index: 1049;
    }

    /* Notifikasi custom */
    .custom-alert {
      max-width: 400px;
      padding: 1.5rem;
      border-radius: 1rem;
      background: #fff;
      border-left: 6px solid #28a745;
      box-shadow: 0 10px 30px rgba(0,0,0,0.15);
      animation: fadeInScale 0.4s ease-out;
    }

    @keyframes fadeInScale {
      0% { opacity: 0; transform: scale(0.9); }
      100% { opacity: 1; transform: scale(1); }
    }

      .small-swal {
    max-width: 350px !important;
    font-size: 0.95rem;
  }

  .swal2-popup .btn-green {
    background-color: #198754 !important; /* Bootstrap's 'success' */
    color: #fff !important;
    border: none;
    padding: 8px 18px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9rem;
  }

  .swal2-popup .btn-green:hover {
    background-color: #146c43 !important;
  }

  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-transparent">
    <div class="container">
      <!-- Left: Logo -->
      <a class="navbar-brand" href="index.php">
        <img src="./assets/logo.png" alt="Gudeg Jagattara" width="150">
      </a>

      <!-- Right: Keranjang + Hamburger -->
      <div class="d-flex align-items-center">
        <!-- Keranjang -->
        <a href="keranjang.php" class="position-relative me-3 d-lg-none">
          <i class="fas fa-shopping-cart fa-lg text-success"></i>
          <?php
          $jumlahKeranjang = 0;
          if (isset($_SESSION['user'])) {
            $id_user = $_SESSION['user']['id_user'];
            $resultKeranjang = $koneksi->query("SELECT SUM(jumlah) as total FROM keranjang WHERE id_user = $id_user");
            $dataKeranjang = $resultKeranjang->fetch_assoc();
            $jumlahKeranjang = $dataKeranjang['total'] ?? 0;
          }
          ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
            <?= $jumlahKeranjang ?>
          </span>
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAlt"
          aria-controls="navbarNavAlt" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Menu -->
      <div class="collapse navbar-collapse justify-content-end mt-2 mt-lg-0" id="navbarNavAlt">
        <div class="navbar-nav fw-semibold">
          <a class="nav-link me-3" style="color: #135f22;" href="index.php">Home</a>
          <a class="nav-link active me-3" style="color: #135f22;" href="menu.php">Menu</a>
          <a class="nav-link me-3" style="color: #135f22;" href="index.php#tentang">Tentang Kami</a>
        </div>

        <!-- Right nav: login or dropdown + keranjang -->
        <div class="d-flex align-items-center">
          <?php if (isset($_SESSION['user'])): ?>
            <div class="dropdown me-3">
              <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #135f22; font-weight: bold;">
                Halo, <?= htmlspecialchars($_SESSION['user']['username']) ?>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <!-- <li><a class="dropdown-item" href="profile.php">Profil</a></li> -->
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a href="login.php" class="btn btn-outline-success me-2">Masuk</a>
            <a href="signup.php" class="btn btn-green me-3">Daftar</a>
          <?php endif; ?>

          <!-- Keranjang (desktop only) -->
          <a href="keranjang.php" class="position-relative d-none d-lg-inline-block">
            <i class="fas fa-shopping-cart fa-lg text-success"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
              <?= $jumlahKeranjang ?>
            </span>
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Notif Harus Login -->
  <?php if (isset($_GET['notif']) && $_GET['notif'] == 'login'): ?>
    <style>
      /* Blur semua konten di belakang */
      .blur-content *:not(.custom-alert):not(.custom-alert *) {
        filter: blur(5px);
        pointer-events: none;
        user-select: none;
      }

      .custom-alert {
        animation: fadeIn 0.5s ease;
        width: 90%;
        max-width: 550px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 40px 30px;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translate(-50%, -60%);
        }
        to {
          opacity: 1;
          transform: translate(-50%, -50%);
        }
      }

      .custom-alert h5 {
        font-size: 1.25rem;
        margin-bottom: 25px;
        color: #333;
      }

      .custom-alert .btn {
        padding: 10px 25px;
        font-size: 1rem;
      }
    </style>

    <div class="position-fixed top-50 start-50 translate-middle custom-alert text-center z-3">
      <h5>Oops! Kamu belum login.<br>Yuk login dulu biar bisa pesan gudeg-nya!</h5>
      <div>
        <a href="login.php" class="btn btn-success me-2">Login Sekarang</a>
        <a href="menu.php" class="btn btn-outline-success">Nanti Dulu</a>
      </div>
    </div>
  <?php endif; ?>

  <!-- Menu Section -->
  <div class="menu-container mt-5 mb-5 blur-content">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="menu-card">
          <div class="img-wrapper">
              <img src="<?= $row['gambar'] ?>" alt="<?= $row['nama_menu'] ?>">
          </div>
          <h3><?= $row['nama_menu'] ?></h3>
          <p><?= $row['deskripsi'] ?></p>
          <strong class="d-block mb-2">Rp.<?= number_format($row['harga'], 0, ',', '.') ?></strong>
          <form action="tambah_keranjang.php" method="POST">
            <input type="hidden" name="id_menu" value="<?= $row['id_menu'] ?>">
            <button type="submit" class="btn btn-success d-block mx-auto mt-2">
              <i class="fa-solid fa-cart-plus"></i> Tambah ke Keranjang
            </button>
          </form>

      </div>
    <?php endwhile; ?>
  </div>

  <!-- Footer -->
  <footer class="text-center bg-white">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-md-12 text-center">
          <img src="./assets/logo.png" alt="Logo" width="180" class="mb-4">
        </div>
      </div>
      <div class="row justify-content-center text-start align-items-start">
        <div class="col-md-6 mb-3 pe-md-5">
          <p><img src="./assets/footer/loc.png" width="24" class="me-2 mb-1"> Jl.Sukoharjo Km.3, RT.02/RW.03, Wonokerto, Kec.Leksono, Kab.Wonosobo (Belakang Alfamart Wonokerto)</p>
          <p><img src="./assets/footer/clock.png" width="24" class="me-2 mb-1"> Jam Buka: Setiap Hari, 07.00 – 17.00 WIB</p>
        </div>
        <div class="col-md-6 mb-3 ps-md-5">
          <p><img src="./assets/footer/layanan.png" width="24" class="me-2 mb-1"> Layanan: Dine-in | Take Away | Pre-order via WhatsApp</p>
          <p><img src="./assets/footer/deliv.png" width="24" class="me-2 mb-1"> Pesan Antar tersedia untuk daerah sekitar</p>
        </div>
      </div>
      <div class="row mt-1">
          <div class="col text-center">
            <a href="https://wa.me/6281327456736" target="_blank" style="text-decoration: none; color: inherit;">
              <img src="./assets/footer/whatsapp.png" alt="whatsapp" width="25" class="me-2">
              Hubungi Kami Sekarang
            </a>
          </div>
        </div>
      </div>
    </div>
    <p class="text-muted small">© 2025 Gudeg Jagattara – Cita Rasa Rumahan</p>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <?php if (isset($_SESSION['notif_keranjang']) && $_SESSION['notif_keranjang'] === 'success'): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Pesanan berhasil ditambahkan!',
      html: 'Silakan klik ikon <b>keranjang</b> di pojok atas untuk melihat dan melanjutkan pesanan.',
      confirmButtonText: 'Oke, Mengerti',
      customClass: {
        popup: 'small-swal',
        confirmButton: 'btn-green'
      },
      showCloseButton: true,
      allowOutsideClick: false
    });

  </script>
  <?php unset($_SESSION['notif_keranjang']); ?>
  <?php endif; ?>
</body>
</html>
