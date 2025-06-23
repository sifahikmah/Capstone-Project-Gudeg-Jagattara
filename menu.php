<?php
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
  <style>
    body {
      background-color: #e6ffe6;
    }
    .nav-link.active {
      font-weight: bold;
      color: #0d3f15 !important;
      border-bottom: 2px solid #135f22;
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
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-transparent">
  <div class="container">
    <a class="navbar-brand" href="index.php">
      <img src="./assets/logo.png" alt="Gudeg Jagattara" width="150">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAlt"
      aria-controls="navbarNavAlt" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNavAlt">
      <div class="navbar-nav fw-semibold">
        <a class="nav-link me-3" style="color: #135f22;" href="index.php">Home</a>
        <a class="nav-link active me-3" style="color: #135f22;" href="menu.php">Menu</a>
        <a class="nav-link" style="color: #135f22;" href="index.php#tentang">Tentang Kami</a>
      </div>
    </div>
  </div>
</nav>

<!-- Menu Section -->
<div class="menu-container mt-5 mb-5">
  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="menu-card">
        <div class="img-wrapper">
            <img src="<?= $row['gambar'] ?>" alt="<?= $row['nama_menu'] ?>">
        </div>
        <h3><?= $row['nama_menu'] ?></h3>
        <p><?= $row['deskripsi'] ?></p>
        <strong class="d-block mb-2">Rp.<?= number_format($row['harga'], 0, ',', '.') ?></strong>
        <button 
        class="btn btn-success d-block add-to-cart mx-auto mt-2"
        data-id='<?= $row['id_menu'] ?>'
        data-nama='<?= $row['nama_menu'] ?>'
        data-harga='<?= $row['harga'] ?>'
        >
        Pesan Disini
        </button>
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
        <p><img src="./assets/footer/whatsapp.png" width="25" class="me-2"> Hubungi Kami Sekarang</p>
      </div>
    </div>
  </div>
  <p class="text-muted small">© 2025 Gudeg Jagattara – Cita Rasa Rumahan</p>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript Simpan ke localStorage -->
<script>
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function () {
      const id = parseInt(this.dataset.id);
      const nama = this.dataset.nama;
      const harga = parseInt(this.dataset.harga);

      let pesanan = JSON.parse(localStorage.getItem('pesanan')) || [];

      const existing = pesanan.find(item => item.id === id);
      if (existing) {
        existing.jumlah += 1;
      } else {
        pesanan.push({ id, nama, harga, jumlah: 1 });
      }

      localStorage.setItem('pesanan', JSON.stringify(pesanan));

      // Hapus notif lama kalau ada
      const existingNotif = document.querySelector('.alert');
      if (existingNotif) existingNotif.remove();

      // Tampilkan notifikasi
      const notif = document.createElement('div');
      notif.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4 shadow';
      notif.style.zIndex = 1050;
      notif.innerHTML = `
        <strong>Pesanan Ditambahkan!</strong><br>
        <span>${nama} berhasil ditambahkan ke pesanan.</span>
        <div class="mt-2">
          <a href="pesanan.php" class="btn btn-sm btn-light">Lihat Pesanan</a>
          <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      `;
      document.body.appendChild(notif);

      // Hapus otomatis setelah 4 detik (opsional)
      setTimeout(() => {
        notif.remove();
      }, 4000);
    });
  });
</script>



</body>
</html>
