<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id_keranjang = $_POST['id_keranjang'];
  $action = $_POST['action'];

  // Ambil jumlah sekarang
  $query = mysqli_query($koneksi, "SELECT jumlah FROM keranjang WHERE id_keranjang = $id_keranjang");
  $data = mysqli_fetch_assoc($query);
  $jumlah = $data['jumlah'];

  if ($action == 'increase') {
    $jumlah += 1;
  } elseif ($action == 'decrease' && $jumlah > 1) {
    $jumlah -= 1;
  }

  mysqli_query($koneksi, "UPDATE keranjang SET jumlah = $jumlah WHERE id_keranjang = $id_keranjang");
}

header("Location: keranjang.php");
exit();
