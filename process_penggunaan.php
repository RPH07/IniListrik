<?php
// Include file koneksi database
include 'includes/db_connection.php';

// Periksa apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pelanggan_id = $_POST['pelanggan_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $penggunaan = $_POST['penggunaan'];

    // Masukkan data ke tabel penggunaan
    $query = "INSERT INTO penggunaan (pelanggan_id, bulan, tahun, penggunaan, tagihan) 
              VALUES ('$pelanggan_id', '$bulan', '$tahun', '$penggunaan', '$tagihan')";

    if ($conn->query($query)) {
        header('Location: dashboard.php?success=1');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
// Tutup koneksi
$conn->close();
?>
