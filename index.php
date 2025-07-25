
<?php
session_start();
include 'koneksi.php';

// Ambil 3 menu terlaris
$query = "
  SELECT 
    COALESCE(m.nama_menu, d.nama_menu_manual) AS nama_menu,
    m.gambar,
    SUM(d.jumlah) AS total_terjual
  FROM detail_pesanan d
  JOIN pesanan p ON d.id_pesanan = p.id_pesanan
  LEFT JOIN menu m ON d.id_menu = m.id_menu
  WHERE p.status = 'diterima'
  GROUP BY nama_menu, m.gambar
  ORDER BY total_terjual DESC
  LIMIT 3
";
$terlaris = mysqli_query($koneksi, $query);

if (!$terlaris) {
  die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    body {
      background-color: #e3fadd;
    }
    .nav-link.active {
      font-weight: bold;
      color: #0d3f15 !important;
      border-bottom: 2px solid #135f22;
    }
    .hero-img {
      max-width: 100%;
      height: auto;
    }
    .btn-green {
      background-color: #114d1a;
      color: white;
    }
    .btn-green:hover {
      background-color: #1c8430;
    }
    h5,h6 {
      color: #135f22;
    }
    p {
      color: #418a4f;
    }
    .menu-card {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      width: 200px;
      padding: 1rem;
      text-align: center;
    }
    .img-wrapper-small {
      background-color: #366b30;
      border-radius: 50%;
      width: 140px;
      height: 140px;
      margin: 0 auto 0.5rem;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .img-wrapper-small img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
    }
    .menu-card h5 {
      font-size: 1rem;
      color: #135f22;
    }
    .menu-card:hover {
      transform: translateY(-10px);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-transparent">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="./assets/logo.png" alt="Gudeg Jagattara" width="150">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAlt">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAlt">
      <div class="navbar-nav fw-semibold">
        <a class="nav-link active me-3" style="color: #135f22;" href="index.php">Home</a>
        <a class="nav-link me-3" style="color: #135f22;" href="menu.php">Menu</a>
        <a class="nav-link me-3" style="color: #135f22;" href="#tentang">Tentang Kami</a>
      </div>
      <!-- Login / Dropdown -->
      <?php if (isset($_SESSION['user'])): ?>
        <!-- Sudah login -->
        <div class="dropdown ">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #135f22; font-weight:bold;">
            Halo, <?= htmlspecialchars($_SESSION['user']['username']) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="profile.php">Profil</a></li>
            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <!-- Belum login -->
        <a href="login.php" class="btn btn-outline-success me-2">Masuk</a>
        <a href="signup.php" class="btn btn-green">Daftar</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="pt-3 pb-5 position-relative">
  <div class="container">
    <div class="row"> 
      <div class="col-md-6 hero-text animate__animated animate__fadeIn"> 
        <h1 style="color: #135f22;" class="fw-bolder mb-4 mt-5 animate__animated animate__fadeInDown">
          Bersantap dengan Rasa,<br>Jagattara Selalu Ada!
        </h1>
        <p style="color: #418a4f;" class="fw-bold mb-5 fs-6 animate__animated animate__fadeInUp">
          Gudeg Jagattara menyediakan berbagai masakan tradisional Indonesia dengan “Gudeg” sebagai menu spesialnya. 
        </p>
        <a href="menu.php" class="btn btn-green me-2">Lihat Menu</a>
      </div>
      <div class="col-md-6 position-relative">
        <img src="./assets/gudeg2.png" alt="Gudeg" class="hero-img position-relative z-1 img-fluid">
      </div>
    </div>
  </div>
</section>

<!-- Fitur Section -->
  <section class="py-5 bg-white text-center">
    <div class="container-fluid px-4 px-md-5">
      <h3 class="mb-5 fw-bold" style="color: #135f22;" >Pilihan Cerdas Pecinta Kuliner</h3>
      <div class="row justify-content-center">
        <div class="col-12 col-sm-6 col-md-4 mb-4">
          <img src="./assets/symbols_food.png" width="40" alt="Makanan Enak">
          <h5 class="mt-4">Makanan Enak</h5>
          <p class="px-2 mt-4">Dibuat dari resep rumahan dan bahan berkualitas, setiap sajian penuh cita rasa otentik yang bikin nagih.</p>
        </div>
        <div class="col-12 col-sm-6 col-md-4 mb-4">
          <img src="./assets/symbols_discount.png" width="40" alt="Harga Terjangkau">
          <h5 class="mt-4">Harga Terjangkau</h5>
          <p class="px-2 mt-4">Harga bersahabat tanpa mengurangi kualitas rasa. Pilihan tepat untuk semua kalangan.</p>
        </div>
        <div class="col-12 col-sm-6 col-md-4 mb-4">
          <img src="./assets/symbols_delivery.png" width="40" alt="Delivery">
          <h5 class="mt-4">Delivery</h5>
          <p class="px-2 mt-4">Mager keluar? Tenang, tinggal klik, pesanan langsung meluncur ke rumahmu.</p>
        </div>
      </div>
    </div>
  </section>

        <!-- Menu Favorit -->
        <section class="py-5 text-center">
        <div class="container">
        <h3 class="mb-5 fw-bold" style="color: #135f22;">Menu Favorit</h3>
        <div class="row justify-content-center">
        <?php while ($row = mysqli_fetch_assoc($terlaris)) : ?>
        <div class="col-md-4 d-flex justify-content-center mb-4">
          <div class="menu-card">
            <?php
              // ambil nama menu dari database
              $nama_menu = strtolower($row['nama_menu']);

              // tentukan gambar berdasarkan nama menu
              switch ($nama_menu) {
                case 'paket 1':
                  $gambar = 'paket1.png';
                  break;
                case 'paket 2':
                  $gambar = 'paket2.png';
                  break;
                case 'paket 3':
                  $gambar = 'paket3.png';
                  break;
                case 'paket 4':
                  $gambar = 'paket4.png';
                  break;
                case 'opor':
                  $gambar = 'opor.png';
                  break;
                default:
                  $gambar = 'default.png'; // fallback kalau nama gak cocok
                  break;
              }
            ?>
            <div class="img-wrapper-small">
              <img src="./assets/menu/<?= $gambar ?>" alt="<?= htmlspecialchars($row['nama_menu']) ?>">
            </div>
            <h5 class="mt-3"><?= htmlspecialchars($row['nama_menu']) ?></h5>
            <p class="text-muted small">Terjual <?= $row['total_terjual'] ?> porsi</p>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <a href="menu.php" class="btn btn-green mt-4">Lihat Selengkapnya</a>
  </div>
</section>

<!-- Tentang -->
<section id="tentang" class="py-5 bg-white text-center">
  <div class="container-fluid px-4 px-md-5">
    <img src="./assets/image-tk.png" alt="decoration" width="60">
    <h3 class="mt-4 mb-4 fw-bold" style="color: #135f22;" >Tentang Gudeg Jagattara</h3>
    <div class="justify-content-center fw-semibold">
      <p>
        Gudeg Jagattara adalah UMKM rumahan dari Desa Wonokerto, Wonosobo, yang berdiri sejak 2019. Didirikan oleh Ibu Nur Hikmah, usaha ini lahir dari semangat belajar otodidak dan kecintaannya pada kuliner tradisional Indonesia, terutama Gudeg. Berkat dedikasi dan eksperimen dapur, terciptalah cita rasa Gudeg khas yang menggugah selera.
        Kami mengutamakan bahan berkualitas, rasa autentik, dan pelayanan ramah.
      </p>
      <a href="galeri.php" class="btn btn-green mt-4">Lihat Galeri</a>
    </div>
  </div>
</section>

<!-- Footer -->
  <footer class="text-center">
    <div class="container py-4">
      <div class="row justify-content-center">
        <div class="col-md-12 text-center">
          <img src="./assets/logo.png" alt="Logo" width="180" class="mb-4">
        </div>
      </div>
      <div class="row justify-content-center text-start align-items-start">
        <!-- Kolom 1 -->
        <div class="col-md-6 mb-3 pe-md-5">
          <p>
            <img src="./assets/footer/loc.png" alt="lokasi" width="24" class="me-2 mb-1">
            Jl.Sukoharjo Km.3, RT.02/RW.03, Wonokerto, Kec.Leksono, Kab.Wonosobo (Belakang Alfamart Wonokerto)
          </p>
          <p>
            <img src="./assets/footer/clock.png" alt="jam" width="24" class="me-2 mb-1">
            Jam Buka: Setiap Hari, 07.00 – 17.00 WIB
          </p>
        </div>

        <!-- Kolom 2 -->
        <div class="col-md-6 mb-3 ps-md-5">
          <p>
            <img src="./assets/footer/layanan.png" alt="layanan" width="24" class="me-2 mb-1">
            Layanan: Dine-in | Take Away | Pre-order via WhatsApp
          </p>
          <p>
            <img src="./assets/footer/deliv.png" alt="delivery" width="24" class="me-2 mb-1">
            Pesan Antar tersedia untuk daerah sekitar
          </p>
        </div>
      </div>
      <!-- Hubungi Kami -->
      <div class="row mt-1">
        <div class="col text-center">
          <a href="https://wa.me/6281327456736" target="_blank" style="text-decoration: none; color: inherit;">
            <img src="./assets/footer/whatsapp.png" alt="whatsapp" width="25" class="me-2">
            Hubungi Kami Sekarang
          </a>
        </div>
      </div>
    </div>
    <p class="text-muted small">© 2025 Gudeg Jagattara – Cita Rasa Rumahan</p>
  </footer>

</body>
</html>
