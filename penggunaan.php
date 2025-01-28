<?php
// Mulai sesi
session_start();

// Include file koneksi database
include 'includes/db_connection.php';

// Periksa apakah id_pelanggan tersedia di sesi
if (!isset($_SESSION['id_pelanggan'])) {
    die("ID pelanggan tidak ditemukan. Silakan login kembali.");
}
$id_pelanggan = $_SESSION['id_pelanggan'];

// Query untuk data detail penggunaan
$query_penggunaan = "
    SELECT 
        p.bulan, 
        p.tahun, 
        p.meter_awal, 
        p.meter_akhir, 
        (p.meter_akhir - p.meter_awal) AS jumlah_meter, 
        (p.meter_akhir - p.meter_awal) * tar.tarifperkwh AS tagihan
    FROM penggunaan p
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN tarif tar ON pel.id_tarif = tar.id_tarif
    WHERE p.id_pelanggan = ?
    ORDER BY p.tahun DESC, p.bulan DESC
";
$stmt_penggunaan = $conn->prepare($query_penggunaan);
$stmt_penggunaan->bind_param("i", $id_pelanggan);
$stmt_penggunaan->execute();
$result_penggunaan = $stmt_penggunaan->get_result();

// Query untuk total tagihan
$total_tagihan_sql = "
    SELECT COALESCE(SUM((p.meter_akhir - p.meter_awal) * tar.tarifperkwh), 0) AS total_tagihan
    FROM penggunaan p
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN tarif tar ON pel.id_tarif = tar.id_tarif
    WHERE p.id_pelanggan = ?
";
$total_tagihan_stmt = $conn->prepare($total_tagihan_sql);
$total_tagihan_stmt->bind_param("i", $id_pelanggan);
$total_tagihan_stmt->execute();
$total_tagihan_result = $total_tagihan_stmt->get_result()->fetch_assoc();
$total_tagihan = $total_tagihan_result['total_tagihan'];

// Query untuk total penggunaan
$total_penggunaan_sql = "
    SELECT COALESCE(SUM(p.meter_akhir - p.meter_awal), 0) AS total_penggunaan
    FROM penggunaan p
    WHERE p.id_pelanggan = ?
";
$total_penggunaan_stmt = $conn->prepare($total_penggunaan_sql);
$total_penggunaan_stmt->bind_param("i", $id_pelanggan);
$total_penggunaan_stmt->execute();
$total_penggunaan_result = $total_penggunaan_stmt->get_result()->fetch_assoc();
$total_penggunaan = $total_penggunaan_result['total_penggunaan'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penggunaan Pelanggan</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Penggunaan Pelanggan</h1>

        <!-- Ringkasan -->
        <div class="summary">
            <div class="card">
                <h3>Total Penggunaan</h3>
                <p><?php echo number_format($total_penggunaan, 0, ',', '.'); ?> kWh</p>
            </div>
            <div class="card">
                <h3>Total Tagihan</h3>
                <p>Rp<?php echo number_format($total_tagihan, 0, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Tabel Detail Penggunaan -->
        <table class="usage-table">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Meter Awal</th>
                    <th>Meter Akhir</th>
                    <th>Jumlah Meter (kWh)</th>
                    <th>Tagihan (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result_penggunaan->num_rows > 0) {
                    while ($row = $result_penggunaan->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['bulan']}</td>
                            <td>{$row['tahun']}</td>
                            <td>{$row['meter_awal']}</td>
                            <td>{$row['meter_akhir']}</td>
                            <td>{$row['jumlah_meter']} kWh</td>
                            <td>Rp" . number_format($row['tagihan'], 0, ',', '.') . "</td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>Tidak ada data penggunaan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
