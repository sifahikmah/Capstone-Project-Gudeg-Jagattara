<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id_keranjang'] ?? null;
  if ($id) {
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id_keranjang = $id");
  }
}

header('Location: keranjang.php');
exit();
