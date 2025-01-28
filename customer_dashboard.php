<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// echo "Session User ID: " . $_SESSION['user_id'];
// if (isset($_SESSION['user_id'])) {
//     echo "Session User ID: " . $_SESSION['user_id'];
// } else {
//     echo "Session User ID tidak diset.";
// }



include 'includes/db_connection.php';

// Initialize variables
$total_penggunaan = 0;
$total_tagihan = 0;
$bulan_aktif = 'Tidak ada data';

// Ambil ID pelanggan dari session
$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan data pelanggan
$sql = "SELECT p.*
        FROM pelanggan p
        JOIN user u ON u.pelanggan_id = p.id_pelanggan
        WHERE u.id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $pelanggan = $result->fetch_assoc();
    
    // Total Penggunaan (Electricity Usage)
    $total_usage_sql = "SELECT COALESCE(SUM(meter_akhir - meter_awal), 0) AS total_penggunaan 
                        FROM penggunaan 
                        WHERE id_pelanggan = ?";
    $total_usage_stmt = $conn->prepare($total_usage_sql);
    $total_usage_stmt->bind_param("i", $pelanggan['id_pelanggan']);
    $total_usage_stmt->execute();
    $total_usage_result = $total_usage_stmt->get_result()->fetch_assoc();
    $total_penggunaan = $total_usage_result['total_penggunaan'];

    // Total Tagihan (Total Bill)
    $total_tagihan_sql = "SELECT COALESCE(SUM(t.jumlah_meter * tar.tarifperkwh), 0) AS total_tagihan 
                          FROM tagihan t
                          JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
                          JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
                          JOIN tarif tar ON pel.id_tarif = tar.id_tarif
                          WHERE p.id_pelanggan = ?";
    $total_tagihan_stmt = $conn->prepare($total_tagihan_sql);
    $total_tagihan_stmt->bind_param("i", $pelanggan['id_pelanggan']);
    $total_tagihan_stmt->execute();
    $total_tagihan_result = $total_tagihan_stmt->get_result()->fetch_assoc();
    $total_tagihan = $total_tagihan_result['total_tagihan'];

    // Bulan Aktif (Most Recent Active Month)
    $bulan_aktif_sql = "SELECT bulan, tahun 
                        FROM penggunaan 
                        WHERE id_pelanggan = ? 
                        ORDER BY tahun DESC, 
                        CASE bulan 
                            WHEN 'Januari' THEN 1 
                            WHEN 'Februari' THEN 2 
                            WHEN 'Maret' THEN 3 
                            WHEN 'April' THEN 4 
                            WHEN 'Mei' THEN 5 
                            WHEN 'Juni' THEN 6 
                            WHEN 'Juli' THEN 7 
                            WHEN 'Agustus' THEN 8 
                            WHEN 'September' THEN 9 
                            WHEN 'Oktober' THEN 10 
                            WHEN 'November' THEN 11 
                            WHEN 'Desember' THEN 12 
                        END DESC 
                        LIMIT 1";
    $bulan_aktif_stmt = $conn->prepare($bulan_aktif_sql);
    $bulan_aktif_stmt->bind_param("i", $pelanggan['id_pelanggan']);
    $bulan_aktif_stmt->execute();
    $bulan_aktif_result = $bulan_aktif_stmt->get_result()->fetch_assoc();
    $bulan_aktif = $bulan_aktif_result ? $bulan_aktif_result['bulan'] . ' ' . $bulan_aktif_result['tahun'] : 'Tidak ada data';

    // Query untuk mengambil data penggunaan
    $usage_sql = "SELECT * FROM penggunaan WHERE id_pelanggan = ?";
    $usage_stmt = $conn->prepare($usage_sql);
    $usage_stmt->bind_param("i", $pelanggan['id_pelanggan']);
    $usage_stmt->execute();
    $usage_result = $usage_stmt->get_result();
} else {
    $error_message = "Data pelanggan tidak ditemukan.";
}

// Debug Total Penggunaan
echo "Total Penggunaan Query: ";
$total_usage_stmt->execute();
$total_usage_result = $total_usage_stmt->get_result();
while ($row = $total_usage_result->fetch_assoc()) {
    print_r($row);
}

// Debug Total Tagihan
echo "Total Tagihan Query: ";
$total_tagihan_stmt->execute();
$total_tagihan_result = $total_tagihan_stmt->get_result();
while ($row = $total_tagihan_result->fetch_assoc()) {
    print_r($row);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="style/dashboard.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h3>Menu</h3>
            <ul>
                <li><a href="#">Dashboard</a></li>
                <li><a href="penggunaan.php">Penggunaan</a></li>
                <li><a href="#">Tagihan</a></li>
                <li><a href="#">Pengaturan</a></li>
                <li><a href="logout.php">logout</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <h2>Dashboard</h2>
            <div class="summary">
                <div class="card">
                    <h3>Total Penggunaan</h3>
                    <p><?php echo number_format($total_penggunaan, 0, ',', '.'); ?> kWh</p>
                </div>
                <div class="card">
                    <h3>Total Tagihan</h3>
                    <p>Rp<?php echo number_format($total_tagihan, 0, ',', '.'); ?></p>
                </div>
                <div class="card">
                    <h3>Bulan Aktif</h3>
                    <p><?php echo $bulan_aktif; ?></p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>