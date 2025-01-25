<?php
include 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // Hapus penggunaan dulu
    $conn->query("DELETE FROM penggunaan WHERE pelanggan_id = $id");
    
    // Baru hapus pelanggan
    $conn->query("DELETE FROM pelanggan WHERE id = $id");
    
    header('Location: profile.php?success=1');
    exit;
}

$conn->close();
?>