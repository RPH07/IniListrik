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

    // Ambil semua tagihan berdasarkan id_penggunaan yang ditemukan
    if (!empty($id_penggunaan_list)) {
        $sql = "SELECT * FROM tagihan WHERE id_penggunaan IN (" . implode(',', $id_penggunaan_list) . ")";
        $result = $conn->query($sql);
    }

    // Ambil meter terakhir untuk pelanggan
    $sql_last_meter = "SELECT meter_akhir FROM penggunaan 
                      WHERE id_pelanggan = ? 
                      ORDER BY id_penggunaan DESC LIMIT 1";
    $stmt_last = $conn->prepare($sql_last_meter);
    $stmt_last->bind_param("i", $id_pelanggan);
    $stmt_last->execute();
    $last_meter = $stmt_last->get_result()->fetch_assoc();
    $meter_awal = $last_meter ? $last_meter['meter_akhir'] : 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status & Tambah Tagihan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 100%;
            display: flex;
            justify-content: center;
            gap: 5rem;
            margin-top: 50px;
        }
        .card {
            border-radius: 10px;
            margin-bottom: 20px;
        }
        #card-updt {
            width: 30vw;
        }
        .btn-update {
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .btn-update:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
            #card-updt, #card-tmbh {
                width: 90vw;
            }
            
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Card untuk Update Status -->
    <div id="card-updt" class="card shadow-sm">
        <div class="card-header text-center bg-primary text-white">
            <h4>Update Status Tagihan</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Tagihan yang Ingin Diupdate:</label>
                    <?php 
                    if (!empty($id_penggunaan_list) && $result->num_rows > 0) {
                        while ($tagihan = $result->fetch_assoc()) : 
                    ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tagihan_ids[]" value="<?= $tagihan['id_tagihan'] ?>"> 
                            <label class="form-check-label">
                                <?= $tagihan['bulan'] ?> <?= $tagihan['tahun'] ?> - <strong class="text-<?= ($tagihan['status'] == 'LUNAS') ? 'success' : 'danger' ?>"><?= $tagihan['status'] ?></strong>
                            </label>
                        </div>
                    <?php 
                        endwhile;
                    } else {
                        echo "<div class='alert alert-warning'>Tidak ada tagihan untuk pelanggan ini.</div>";
                    }
                    ?>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status Baru:</label>
                    <select class="form-select" name="status">
                        <option value="BELUM_LUNAS">Belum Lunas</option>
                        <option value="LUNAS">Lunas</option>
                    </select>
                </div>

                <div class="text-center d-flex gap-2">
                    <a href="admin_dashboard.php" class="btn btn-danger w-25">Batal</a>
                    <button type="submit" class="btn btn-update w-75">Update Status</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card untuk Tambah Penggunaan Baru -->
    <div id="card-tmbh" class="card shadow-sm">
        <div class="card-header text-center bg-success text-white">
            <h4>Tambah Penggunaan Baru</h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="add_penggunaan">
                <input type="hidden" name="id_pelanggan" value="<?= $id_pelanggan ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Bulan</label>
                        <select class="form-select" name="bulan" required>
                            <option value="">Pilih Bulan</option>
                            <option value="Januari">Januari</option>
                            <option value="Februari">Februari</option>
                            <option value="Maret">Maret</option>
                            <option value="April">April</option>
                            <option value="Mei">Mei</option>
                            <option value="Juni">Juni</option>
                            <option value="Juli">Juli</option>
                            <option value="Agustus">Agustus</option>
                            <option value="September">September</option>
                            <option value="Oktober">Oktober</option>
                            <option value="November">November</option>
                            <option value="Desember">Desember</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Tahun</label>
                        <input type="number" class="form-control" name="tahun" required min="2020" max="2099" value="<?= date('Y') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Meter Awal</label>
                        <input type="number" class="form-control" name="meter_awal" required value="<?= $meter_awal ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Meter Akhir</label>
                        <input type="number" class="form-control" name="meter_akhir" required min="<?= $meter_awal ?>">
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-success w-100">Tambah Penggunaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'update_status' && !empty($_POST['tagihan_ids'])) {
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
    } 
    elseif ($_POST['action'] === 'add_penggunaan') {
        $id_pelanggan = $_POST['id_pelanggan'];
        $bulan = $_POST['bulan'];
        $tahun = $_POST['tahun'];
        $meter_awal = $_POST['meter_awal'];
        $meter_akhir = $_POST['meter_akhir'];
        
        // Insert penggunaan baru
        $sql_insert = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isiii", $id_pelanggan, $bulan, $tahun, $meter_awal, $meter_akhir);
        
        if ($stmt_insert->execute()) {
            echo "<div class='alert alert-success text-center mt-3'>Penggunaan baru berhasil ditambahkan.</div>";
            echo "<script>setTimeout(() => { window.location.href = 'admin_dashboard.php'; }, 2000);</script>";
        } else {
            echo "<div class='alert alert-danger text-center mt-3'>Gagal menambahkan penggunaan baru.</div>";
        }
    }
    else {
        echo "<div class='alert alert-warning text-center mt-3'>Pilih minimal satu tagihan untuk diperbarui.</div>";
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>