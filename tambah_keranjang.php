<?php
session_start();
require 'koneksi.php'; // ganti jika nama file koneksi berbeda

if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}


$id_user = $_SESSION['user']['id_user'];
$id_menu = $_POST['id_menu'];

// Cek apakah sudah ada
$cek = $koneksi->prepare("SELECT * FROM keranjang WHERE id_user = ? AND id_menu = ?");
$cek->bind_param('ii', $id_user, $id_menu);
$cek->execute();
$res = $cek->get_result();

if ($res->num_rows > 0) {
  $stmt = $koneksi->prepare("UPDATE keranjang SET jumlah = jumlah + 1 WHERE id_user = ? AND id_menu = ?");
  $stmt->bind_param('ii', $id_user, $id_menu);
  $stmt->execute();
} else {
  $stmt = $koneksi->prepare("INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES (?, ?, 1)");
  $stmt->bind_param('ii', $id_user, $id_menu);
  $stmt->execute();
}

header('Location: menu.php?pesan=ditambahkan');
?>
