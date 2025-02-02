<?php
include 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];
    $sql = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();
    $pelanggan = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];

    $sql_update = "UPDATE pelanggan SET nama_pelanggan = ?, alamat = ? WHERE id_pelanggan = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssi", $nama_pelanggan, $alamat, $id_pelanggan);

    if ($stmt_update->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Gagal mengupdate data.";
    }
}
?>

<form method="POST">
    <label>Nama:</label>
    <input type="text" name="nama_pelanggan" value="<?= $pelanggan['nama_pelanggan']; ?>" required><br>

    <label>Alamat:</label>
    <input type="text" name="alamat" value="<?= $pelanggan['alamat']; ?>" required><br>

    <button type="submit">Simpan</button>
</form>
