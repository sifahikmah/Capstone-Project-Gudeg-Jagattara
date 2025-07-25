<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}

$id_user = $_SESSION['user']['id_user'];
$metode_pengantaran = $_POST['pengiriman'];
$alamat = $_POST['alamat'] ?? '';
$metode_pembayaran = $_POST['pembayaran'];
$wa = $_POST['wa'];
$catatan = $_POST['catatan'] ?? '';
$status = 'menunggu';
$bukti_nama = null;

// Simpan alamat hanya kalau dikirim
if ($metode_pengantaran === 'dikirim') {
  $alamat = mysqli_real_escape_string($koneksi, $alamat);
} else {
  $alamat = ''; // kosongkan jika diambil
}

// Hitung ongkir
$ongkir = $metode_pengantaran === 'dikirim' ? 5000 : 0;

// Upload bukti transfer jika perlu
if ($metode_pembayaran === 'transfer' && isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
  $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
  $bukti_nama = uniqid() . '.' . $ext;
  move_uploaded_file($_FILES['bukti']['tmp_name'], 'assets/bukti/' . $bukti_nama);
}

// Hitung total pesanan
$query = "SELECT m.harga, k.jumlah 
          FROM keranjang k 
          JOIN menu m ON k.id_menu = m.id_menu 
          WHERE k.id_user = $id_user";
$result = mysqli_query($koneksi, $query);

$total = $ongkir;
while ($row = mysqli_fetch_assoc($result)) {
  $total += $row['harga'] * $row['jumlah'];
}

// Simpan ke tabel pesanan pakai prepared statement
$stmt = $koneksi->prepare("INSERT INTO pesanan 
  (id_user, nomor_wa, alamat, metode_pembayaran, metode_pengantaran, total, catatan, bukti_transfer, status, created_at) 
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$stmt->bind_param("issssisss", 
  $id_user, 
  $wa, 
  $alamat, 
  $metode_pembayaran, 
  $metode_pengantaran, 
  $total, 
  $catatan, 
  $bukti_nama, 
  $status
);

$stmt->execute();
$id_pesanan = $stmt->insert_id;
$stmt->close();

// Simpan detail pesanan
$keranjang = mysqli_query($koneksi, "SELECT k.*, m.harga FROM keranjang k JOIN menu m ON k.id_menu = m.id_menu WHERE k.id_user = $id_user");

$stmt_detail = $koneksi->prepare("INSERT INTO detail_pesanan 
  (id_pesanan, id_menu, jumlah, harga_satuan, total_harga) 
  VALUES (?, ?, ?, ?, ?)");

while ($item = mysqli_fetch_assoc($keranjang)) {
  $id_menu = $item['id_menu'];
  $jumlah = $item['jumlah'];
  $harga = $item['harga'];
  $subtotal = $harga * $jumlah;

  $stmt_detail->bind_param("iiidd", $id_pesanan, $id_menu, $jumlah, $harga, $subtotal);
  $stmt_detail->execute();
}
$stmt_detail->close();

// Kosongkan keranjang
mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_user = $id_user");

// Redirect ke halaman status
header("Location: status_pesanan.php?id=$id_pesanan");
exit;
?>
