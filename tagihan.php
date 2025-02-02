<?php
// Start session
session_start();

// Include database connection
include 'includes/db_connection.php';

// Check if customer ID exists in session
if (!isset($_SESSION['id_pelanggan'])) {
    die("ID pelanggan tidak ditemukan. Silakan login kembali.");
}
$id_pelanggan = $_SESSION['id_pelanggan'];

// Query for paid bills
$query_lunas = "
    SELECT 
        t.bulan,
        t.tahun,
        t.jumlah_meter,
        t.status,
        ((p.meter_akhir - p.meter_awal) * tar.tarifperkwh) AS tagihan
    FROM tagihan t
    JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN tarif tar ON pel.id_tarif = tar.id_tarif
    WHERE p.id_pelanggan = ? AND t.status = 'LUNAS'
    ORDER BY t.tahun DESC, t.bulan DESC
";
$stmt_lunas = $conn->prepare($query_lunas);
$stmt_lunas->bind_param("i", $id_pelanggan);
$stmt_lunas->execute();
$result_lunas = $stmt_lunas->get_result();

// Query for unpaid bills
$query_belum_lunas = "
    SELECT 
        t.bulan,
        t.tahun,
        t.jumlah_meter,
        t.status,
        ((p.meter_akhir - p.meter_awal) * tar.tarifperkwh) AS tagihan
    FROM tagihan t
    JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN tarif tar ON pel.id_tarif = tar.id_tarif
    WHERE p.id_pelanggan = ? AND t.status = 'BELUM_LUNAS'
    ORDER BY t.tahun DESC, t.bulan DESC
";
$stmt_belum_lunas = $conn->prepare($query_belum_lunas);
$stmt_belum_lunas->bind_param("i", $id_pelanggan);
$stmt_belum_lunas->execute();
$result_belum_lunas = $stmt_belum_lunas->get_result();

// Calculate total unpaid bills
$total_belum_lunas_sql = "
    SELECT COALESCE(SUM((p.meter_akhir - p.meter_awal) * tar.tarifperkwh), 0) AS total_belum_lunas
    FROM tagihan t
    JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
    JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
    JOIN tarif tar ON pel.id_tarif = tar.id_tarif
    WHERE p.id_pelanggan = ? AND t.status = 'BELUM_LUNAS'
";
$stmt_total_belum_lunas = $conn->prepare($total_belum_lunas_sql);
$stmt_total_belum_lunas->bind_param("i", $id_pelanggan);
$stmt_total_belum_lunas->execute();
$total_belum_lunas_result = $stmt_total_belum_lunas->get_result()->fetch_assoc();
$total_belum_lunas = $total_belum_lunas_result['total_belum_lunas'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan Listrik</title>
    
    <style>
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

/* Main Content */
.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 20px;
    margin-left: calc(250px + 2rem);
    width: calc(100% - 250px - 4rem);
}

h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.2rem;
    font-weight: 600;
}

h2 {
    color: #2c3e50;
    margin: 2rem 0 1rem 0;
    font-size: 1.5rem;
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

/* Table Styling */
.table-wrapper {
    margin-bottom: 2rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.usage-table {
    width: 100%;
    border-collapse: collapse;
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

/* Status Badges */
.status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    display: inline-block;
}

.status.paid {
    background-color: #d1fae5;
    color: #065f46;
}

.status.unpaid {
    background-color: #fee2e2;
    color: #991b1b;
}

/* Responsive Design */
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

    .card p {
        font-size: 1.5rem;
    }

    .usage-table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    .status {
        padding: 4px 8px;
        font-size: 0.75rem;
    }
}

/* Empty State Styling */
.usage-table tbody tr td[colspan] {
    text-align: center;
    padding: 2rem;
    color: #718096;
    font-style: italic;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.container {
    animation: fadeIn 0.5s ease-in;
}
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="customer_dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="penggunaan.php"><i class="fas fa-bolt"></i> <span>Penggunaan</span></a></li>
            <li><a href="tagihan.php"><i class="fas fa-file-invoice"></i> <span>Tagihan</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div class="container">
        <h1>Tagihan Listrik</h1>

        <!-- Summary Card -->
        <div class="summary">
            <div class="card">
                <h3>Total Tagihan Belum Lunas</h3>
                <p>Rp<?php echo number_format($total_belum_lunas, 0, ',', '.'); ?></p>
            </div>
        </div>

        <!-- Unpaid Bills -->
        <h2>Tagihan Belum Lunas</h2>
        <div class="table-wrapper">
            <table class="usage-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah Meter (kWh)</th>
                        <th>Tagihan (Rp)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_belum_lunas->num_rows > 0) {
                        while ($row = $result_belum_lunas->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['bulan']}</td>
                                <td>{$row['tahun']}</td>
                                <td>{$row['jumlah_meter']} kWh</td>
                                <td>Rp" . number_format($row['tagihan'], 0, ',', '.') . "</td>
                                <td><span class='status unpaid'>{$row['status']}</span></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Tidak ada tagihan yang belum lunas.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Paid Bills -->
        <h2>Riwayat Pembayaran</h2>
        <div class="table-wrapper">
            <table class="usage-table">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Jumlah Meter (kWh)</th>
                        <th>Tagihan (Rp)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result_lunas->num_rows > 0) {
                        while ($row = $result_lunas->fetch_assoc()) {
                            echo "<tr>
                                <td>{$row['bulan']}</td>
                                <td>{$row['tahun']}</td>
                                <td>{$row['jumlah_meter']} kWh</td>
                                <td>Rp" . number_format($row['tagihan'], 0, ',', '.') . "</td>
                                <td><span class='status paid'>{$row['status']}</span></td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Tidak ada riwayat pembayaran.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>