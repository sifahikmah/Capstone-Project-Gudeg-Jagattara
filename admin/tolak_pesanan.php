<?php
include '../koneksi.php';

$id = $_GET['id'];
$koneksi->query("UPDATE pesanan SET status = 'ditolak' WHERE id_pesanan = $id");

header("Location: pesanan.php");
exit;
?>
