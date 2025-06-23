<?php
include '../koneksi.php';

$id = $_GET['id'];
$koneksi->query("UPDATE pesanan SET status = 'diterima' WHERE id_pesanan = $id");

header("Location: pesanan.php");
exit;
?>
