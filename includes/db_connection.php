<?php
// Konfigurasi database
$host = 'localhost'; // Ganti dengan host database Anda (default: localhost)
$dbname = 'listrik_bill'; // Ganti sesuai nama database Anda
$username = 'root'; // Ganti sesuai username database Anda
$password = ''; // Ganti sesuai password database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengatur charset
$conn->set_charset("utf8");
?>
