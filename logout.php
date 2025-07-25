<?php
session_start();
session_destroy();
header("Location: index.php"); // Ganti ke halaman login kamu
exit;
?>