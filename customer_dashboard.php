<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo "Session User ID: " . $_SESSION['user_id'];
if (isset($_SESSION['user_id'])) {
    echo "Session User ID: " . $_SESSION['user_id'];
} else {
    echo "Session User ID tidak diset.";
}



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

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
}

.dashboard-container {
    display: flex;
}

.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 5px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    transition: all 0.3s ease;
}

.sidebar h3 {
    color: #ecf0f1;
    font-size: 1.5rem;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    margin-bottom: 0.5rem;
}

.sidebar ul li a {
    color: #ecf0f1;
    text-decoration: none;
    display: block;
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar ul li a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.main-content {
    margin-left: 250px; /* Initial margin equal to sidebar width */
    padding: 20px;
    background-color: #ffffff;
    transition: margin-left 0.3s ease;
    width: calc(100% - 250px);
}

.summary {
    display: flex;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 20px;
}


.card {
    background-color: #3498db;
    color: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    width: 30%;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.usage-table {
    width: 100%;
    border-collapse: collapse;
}

.usage-table th, .usage-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
}

.usage-table th {
    background-color: #f4f4f4;
}

.usage-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.usage-table tr:hover {
    background-color: #f1f1f1;
}
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <h3>Menu</h3>
            <ul>
                <li><a href="customer_dashboard.php">Dashboard</a></li>
                <li><a href="penggunaan.php">Penggunaan</a></li>
                <li><a href="tagihan.php">Tagihan</a></li>
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