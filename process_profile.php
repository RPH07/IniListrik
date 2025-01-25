<?php
session_start();
include 'includes/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id']; // Ambil user_id dari session

// Ambil data dari form
$id = isset($_POST['id']) ? $_POST['id'] : '';
$nama = isset($_POST['nama']) ? $_POST['nama'] : '';
$no_meter = isset($_POST['no_meter']) ? $_POST['no_meter'] : '';
$alamat = isset($_POST['alamat']) ? $_POST['alamat'] : '';

// Validasi data
if (empty($nama) || empty($no_meter) || empty($alamat)) {
    die("Semua field harus diisi.");
}

if ($id) {
    // Update data pelanggan
    $query = "UPDATE pelanggan SET nama = ?, no_meter = ?, alamat = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $nama, $no_meter, $alamat, $id, $user_id);
} else {
    // Tambah data pelanggan baru
    $query = "INSERT INTO pelanggan (user_id, nama, no_meter, alamat) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isss", $user_id, $nama, $no_meter, $alamat);
}

// Eksekusi query
if ($stmt->execute()) {
    header('Location: profile.php'); // Redirect ke halaman profil
    exit;
} else {
    die("Error: " . $stmt->error);
}
?>
