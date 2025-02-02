<?php
include 'includes/db_connection.php';

// Pastikan ID pelanggan diterima
if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];

    // Cari semua ID penggunaan berdasarkan ID pelanggan
    $sql_penggunaan = "SELECT id_penggunaan FROM penggunaan WHERE id_pelanggan = ?";
    $stmt_penggunaan = $conn->prepare($sql_penggunaan);
    $stmt_penggunaan->bind_param("i", $id_pelanggan);
    $stmt_penggunaan->execute();
    $result_penggunaan = $stmt_penggunaan->get_result();

    // Kumpulkan semua id_penggunaan
    $id_penggunaan_list = [];
    while ($row = $result_penggunaan->fetch_assoc()) {
        $id_penggunaan_list[] = $row['id_penggunaan'];
    }

    // Jika tidak ada penggunaan, tampilkan pesan
    if (empty($id_penggunaan_list)) {
        die("<div class='alert alert-warning text-center'>Tidak ada tagihan untuk pelanggan ini.</div>");
    }

    // Ambil semua tagihan berdasarkan id_penggunaan yang ditemukan
    $sql = "SELECT * FROM tagihan WHERE id_penggunaan IN (" . implode(',', $id_penggunaan_list) . ")";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Tagihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
        }
        .btn-update {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .btn-update:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow-sm">
        <div class="card-header text-center bg-primary text-white">
            <h4>Update Status Tagihan</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Tagihan yang Ingin Diupdate:</label>
                    <?php while ($tagihan = $result->fetch_assoc()) : ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tagihan_ids[]" value="<?= $tagihan['id_tagihan'] ?>"> 
                            <label class="form-check-label">
                                <?= $tagihan['bulan'] ?> <?= $tagihan['tahun'] ?> - <strong class="text-<?= ($tagihan['status'] == 'LUNAS') ? 'success' : 'danger' ?>"><?= $tagihan['status'] ?></strong>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status Baru:</label>
                    <select class="form-select" name="status">
                        <option value="BELUM_LUNAS">Belum Lunas</option>
                        <option value="LUNAS">Lunas</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-update w-100">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['tagihan_ids'])) {
        $status = $_POST['status'];
        $tagihan_ids = $_POST['tagihan_ids'];

        foreach ($tagihan_ids as $id_tagihan) {
            $sql_update = "UPDATE tagihan SET status = ? WHERE id_tagihan = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $status, $id_tagihan);
            $stmt_update->execute();
        }

        echo "<div class='alert alert-success text-center mt-3'>Status tagihan berhasil diperbarui.</div>";
        echo "<script>setTimeout(() => { window.location.href = 'admin_dashboard.php'; }, 2000);</script>";
    } else {
        echo "<div class='alert alert-warning text-center mt-3'>Pilih minimal satu tagihan untuk diperbarui.</div>";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
