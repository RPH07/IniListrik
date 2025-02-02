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
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f7fb;
    color: #333;
}

.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 20px;
}

h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.2rem;
    font-weight: 600;
}

/* Summary Cards */
.summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

.card {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border-radius: 12px;
    padding: 1.5rem;
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    font-weight: 500;
    opacity: 0.9;
}

.card p {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 600;
}

/* Usage Table */
.usage-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    margin-top: 2rem;
}

.usage-table thead {
    background-color: #f8f9fa;
}

.usage-table th {
    padding: 1.2rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #2c3e50;
    border-bottom: 2px solid #edf2f7;
    font-size: 0.95rem;
}

.usage-table td {
    padding: 1rem;
    border-bottom: 1px solid #edf2f7;
    color: #4a5568;
}

.usage-table tbody tr:hover {
    background-color: #f8fafc;
}

.usage-table tbody tr:last-child td {
    border-bottom: none;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 0 15px;
    }
    
    .usage-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .card {
        padding: 1.2rem;
    }
    
    .card p {
        font-size: 1.5rem;
    }
    
    h1 {
        font-size: 1.8rem;
    }
}

/* Additional Styling for Empty State */
.usage-table tbody tr td[colspan="6"] {
    text-align: center;
    padding: 2rem;
    color: #718096;
    font-style: italic;
}

/* Optional: Add Animation for Loading State */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.container {
    animation: fadeIn 0.5s ease-in;
}
/* Base layout */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f7fb;
    color: #333;
    display: flex;
}

/* Sidebar Styling */
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
    color: white;
    padding: 20px;
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

/* Main Content Adjustments */
.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 20px;
    margin-left: calc(250px + 2rem); /* Adjust margin to account for sidebar */
    width: calc(100% - 250px - 4rem); /* Adjust width to account for sidebar and padding */
}

/* Rest of your existing CSS */
h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.2rem;
    font-weight: 600;
}

.summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 3rem;
}

/* Responsive Design for Sidebar */
@media (max-width: 768px) {
    .sidebar {
        width: 60px;
        padding: 20px 10px;
    }

    .sidebar h3 {
        display: none;
    }

    .sidebar ul li a {
        padding: 12px 5px;
        text-align: center;
    }

    .sidebar ul li a span {
        display: none;
    }

    .container {
        margin-left: calc(60px + 1rem);
        width: calc(100% - 60px - 2rem);
        padding: 0 10px;
    }
}
    </style>
</head>
<body>
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
