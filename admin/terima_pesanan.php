<?php
include '../koneksi.php';

$id = $_GET['id'];
$query = "UPDATE pesanan SET status = 'terima' WHERE id_pesanan = '$id'";
mysqli_query($koneksi, $query);

header("Location: pesanan.php");
exit;
?>
