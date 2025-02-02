<?php
include 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    $sql_delete = "DELETE FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_pelanggan);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Gagal menghapus data.";
    }
}
?>
