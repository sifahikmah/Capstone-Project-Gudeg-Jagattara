<?php
include '../koneksi.php';

if (isset($_GET['id_menu'])) {
  $id_menu = intval($_GET['id_menu']);

  // Hapus gambar dari folder 
  $result = $koneksi->query("SELECT gambar FROM menu WHERE id_menu = $id_menu");
  if ($result && $row = $result->fetch_assoc()) {
    $gambarPath = "../" . $row['gambar'];
    if (file_exists($gambarPath)) {
      unlink($gambarPath); // hapus file gambar
    }
  }

  // Hapus data dari database
  $koneksi->query("DELETE FROM menu WHERE id_menu = $id_menu");

  // Redirect kembali ke halaman kelola menu
  header("Location: kelolamenu.php");
  exit;
} else {
  echo "ID menu tidak ditemukan.";
}
?>
