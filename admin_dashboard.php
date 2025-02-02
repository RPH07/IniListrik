<?php
include 'includes/db_connection.php';

// Ambil data pelanggan
$sql = "SELECT p.id_pelanggan, p.nama_pelanggan, p.alamat, p.nomor_kwh, t.daya, u.username 
        FROM pelanggan p
        JOIN user u ON p.username = u.username
        JOIN tarif t ON p.id_tarif = t.id_tarif";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f5f7fb;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: white;
            padding: 20px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar h3 {
            font-size: 24px;
            padding: 20px 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 15px 20px;
            border-radius: 8px;
            transition: all 0.3s;
            margin-bottom: 5px;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 250px;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .header h2 {
            color: #333;
            font-size: 24px;
        }

        /* Table Styling */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        /* Action Buttons */
        .action-buttons a {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            margin: 0 5px;
            transition: all 0.3s;
        }

        .btn-edit {
            background: #71b7e6;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-update {
            background: #9b59b6;
            color: white;
        }

        .action-buttons a:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                width: 70px;
                padding: 10px;
            }

            .sidebar h3 {
                display: none;
            }

            .main-content {
                margin-left: 70px;
            }

            .action-buttons {
                display: flex;
                flex-direction: column;
                gap: 5px;
            }

            .action-buttons a {
                text-align: center;
                margin: 2px 0;
            }
        }
    </style>
</head>
<body>
<aside class="sidebar" id="sidebar">
        <h3>Admin Panel</h3>
        <ul>
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </aside>
    
        <div class="main-content">
            <div class="header">
                <h2>Daftar Pelanggan</h2>
            </div>
            <div class="table-container">
                <table>
                    <tr>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>Nomor KWH</th>
                        <th>Daya</th>
                        <th>Aksi</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= $row['nama_pelanggan']; ?></td>
                        <td><?= $row['alamat']; ?></td>
                        <td><?= $row['nomor_kwh']; ?></td>
                        <td><?= $row['daya']; ?> VA</td>
                        <td class="action-buttons">
                            <a href="edit_pelanggan.php?id=<?= $row['id_pelanggan']; ?>" class="btn-edit" >Edit</a> | 
                            <a href="hapus_pelanggan.php?id=<?= $row['id_pelanggan']; ?>" onclick="return confirm('Hapus pelanggan ini?')" class="btn-delete">Hapus</a> | 
                            <a href="update_tagihan.php?id=<?= $row['id_pelanggan']; ?>" class="btn-update">Update Tagihan</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
        </div>
</body>
</html>
