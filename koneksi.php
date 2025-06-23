<?php
$koneksi = new mysqli("localhost", "root", "", "db_gudeg");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>
