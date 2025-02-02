<?php
include 'includes/db_connection.php'; // Pastikan path ini benar

$username = "admin";
$password = "admin123";
$hashed_password = password_hash($password, PASSWORD_BCRYPT);
$id_level = 1; // 1 untuk admin

$sql = "INSERT INTO user (username, password, id_level) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $username, $hashed_password, $id_level);

if ($stmt->execute()) {
    echo "Admin berhasil ditambahkan!";
} else {
    echo "Gagal menambahkan admin: " . $stmt->error;
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>
