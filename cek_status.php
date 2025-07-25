<?php
include 'koneksi.php';

$id = $_GET['id'] ?? 0;

$query = "SELECT status FROM pesanan WHERE id_pesanan = '$id'";
$result = mysqli_query($koneksi, $query);

if ($row = mysqli_fetch_assoc($result)) {
  echo json_encode(['status' => strtolower($row['status'])]);
} else {
  echo json_encode(['status' => 'menunggu']);
}
?>
