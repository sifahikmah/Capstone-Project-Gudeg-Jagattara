<?php
include 'koneksi.php';

$id = $_GET['id'] ?? 0;

$query = $koneksi->query("SELECT status FROM pesanan WHERE id_pesanan = $id");

if ($query && $query->num_rows > 0) {
  $row = $query->fetch_assoc();
  echo json_encode(['status' => $row['status']]);
} else {
  echo json_encode(['status' => 'not_found']);
}
