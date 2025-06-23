<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Galeri Gudeg Jagattara</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
    body {
      background-color: #E3FFE8;
    }
    /* .navbar {
      background-color: #ffffff;
    } */
    .navbar-brand span {
      font-size: 1.5rem;
      color: #1b6b3a;
    }
    .navbar-nav .nav-link {
      color: #1b6b3a;
      font-weight: 500;
    }
    .nav-link.active {
      font-weight: bold;
      color: #0d3f15 !important;
      border-bottom: 2px solid #135f22;
    }
    .navbar-nav .nav-link:hover {
      color: #10733c;
    }
    .hero {
      background: url('./assets/image\ 68.png') center/cover no-repeat;
      height: 360px;
      color: white;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    .hero-overlay {
      background: rgba(0, 0, 0, 0.5);
      position: absolute;
      inset: 0;
    }
    .hero-text {
      position: relative;
      z-index: 2;
    }
    .hero-text h1 {
      font-size: 2.8rem;
      font-weight: 700;
    }
    .hero-text p {
      font-size: 1.2rem;
    }
    .section-title {
      font-weight: bold;
      font-size: 28px;
      font-family: 'Georgia', serif;
      color: #1b6b3a;
    }
    .galeri .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .galeri .card:hover {
      transform: translateY(-5px);
    }
    .galeri .card img {
      height: 220px;
      object-fit: cover;
      width: 100%;
    }
    .footer {
      font-size: 14px;
      background-color: #ffffff;
    }
    .footer a {
      color: #1b6b3a;
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
      <!-- Tombol hamburger untuk mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAlt"
        aria-controls="navbarNavAlt" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNavAlt">
        <div class="navbar-nav fw-semibold">
          <a class="nav-link me-3" href="index.php">Home</a>
          <a class="nav-link me-3" href="menu.php">Menu</a>
          <a class="nav-link active" href="index.php#tentang">Tentang Kami</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Banner -->
  <section class="hero">
    <div class="hero-overlay"></div>
    <div class="hero-text animate__animated animate__fadeIn">
      <h1 class="animate__animated animate__fadeInDown">Selamat Datang di Galeri <span class="text-warning">Gudeg Jagattara</span></h1>
      <p class="animate__animated animate__fadeInUp">Lihat suasana dan momen spesial di Gudeg Jagattara</p>
    </div>
  </section>

  <!-- Galeri -->
  <section class="container my-5 galeri">
    <h2 class="section-title text-center mb-4">Galeri Kami</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <img src="./assets/image 56.png" alt="Penyajian di lokasi" class="card-img-top rounded mb-3">
          <p class="fw-semibold">"Proses penyajian langsung di lokasi usaha kami."</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <img src="./assets/image 56 (1).png" alt="Makan bersama" class="card-img-top rounded mb-3">
          <p class="fw-semibold">"Momen makan bersama jadi lebih spesial dengan masakan Jagattara"</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 text-center">
          <img src="./assets/image 51.png" alt="Pesanan siap antar" class="card-img-top rounded mb-3">
          <p class="fw-semibold">"Pesanan siap antar! Jagattara melayani kebutuhan kuliner Anda dalam skala besar dan kecil."</p>
        </div>
      </div>
    </div>
  </section>

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
          <p>
            <img src="./assets/footer/loc.png" alt="lokasi" width="24" class="me-2 mb-1">
            Jl.Sukoharjo Km.3, RT.02/RW.03, Wonokerto, Kec.Leksono, Kab.Wonosobo (Belakang Alfamart Wonokerto)
          </p>
          <p>
            <img src="./assets/footer/clock.png" alt="jam" width="24" class="me-2 mb-1">
            Jam Buka: Setiap Hari, 07.00 – 17.00 WIB
          </p>
        </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
