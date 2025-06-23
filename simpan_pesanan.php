<?php
include 'koneksi.php';

// Ambil dan bersihkan data
$nama        = $_POST['nama_pembeli'];
$alamat      = $_POST['alamat'] ?? '-';
$catatan     = trim($_POST['catatan']) ?: '-';
$pengantaran = $_POST['metode_pengantaran'];
$pembayaran  = $_POST['metode_pembayaran'];
$total       = intval($_POST['total']); // dari input hidden total
$pesanan     = json_decode($_POST['pesanan'], true); // dari input hidden pesanan

// Upload bukti transfer (jika ada)
$bukti_transfer = '';
if (!empty($_FILES['bukti_transfer']['name'])) {
  $ext = pathinfo($_FILES['bukti_transfer']['name'], PATHINFO_EXTENSION);
  $bukti_transfer = 'assets/uploads/' . uniqid() . '.' . $ext;
  move_uploaded_file($_FILES['bukti_transfer']['tmp_name'], $bukti_transfer);
}

$response = [];

try {
  // Simpan ke tabel pesanan
  $stmt = $koneksi->prepare("INSERT INTO pesanan 
    (nama_pembeli, alamat, catatan, metode_pengantaran, metode_pembayaran, total, bukti_transfer, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'menunggu')"
  );
  $stmt->bind_param("sssssis", $nama, $alamat, $catatan, $pengantaran, $pembayaran, $total, $bukti_transfer);
  $stmt->execute();
  $id_pesanan = $stmt->insert_id;
  $stmt->close();

  // Simpan ke tabel detail_pesanan
  foreach ($pesanan as $item) {
    $id_menu      = intval($item['id']);
    $jumlah       = intval($item['jumlah']);
    $harga_satuan = intval($item['harga']);

    $stmtDetail = $koneksi->prepare("INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan) VALUES (?, ?, ?, ?)");
    $stmtDetail->bind_param("iiii", $id_pesanan, $id_menu, $jumlah, $harga_satuan);
    $stmtDetail->execute();
    $stmtDetail->close();
  }

  // Kirim respons sukses
  header('Content-Type: application/json');
  echo json_encode([
    'status' => 'success',
    'message' => 'Pesanan berhasil disimpan.',
    'id_pesanan' => $id_pesanan
  ]);
} catch (Exception $e) {
  // Tangani error
  http_response_code(500);
  echo json_encode([
    'status' => 'error',
    'message' => 'Terjadi kesalahan saat menyimpan pesanan: ' . $e->getMessage()
  ]);
}
?>
