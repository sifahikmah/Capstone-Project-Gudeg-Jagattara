<?php
session_start();
require 'koneksi.php'; // ganti jika nama file koneksi berbeda

if (!isset($_SESSION['user'])) {
  header("Location: menu.php?notif=login");
  exit;
}

$id_user = $_SESSION['user']['id_user'];
$id_menu = $_POST['id_menu'];

// Cek apakah user sudah punya isi keranjang sebelumnya
$cek_keranjang = $koneksi->prepare("SELECT COUNT(*) as total FROM keranjang WHERE id_user = ?");
$cek_keranjang->bind_param('i', $id_user);
$cek_keranjang->execute();
$result_total = $cek_keranjang->get_result()->fetch_assoc();
$total_sebelumnya = (int)$result_total['total'];

// Cek apakah menu sudah ada
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

// Jika total sebelumnya 0, berarti ini pertama kali user menambahkan
if ($total_sebelumnya === 0) {
  $_SESSION['notif_keranjang'] = 'first_added';
}

// Redirect ke menu
header("Location: menu.php");
exit;
?>
