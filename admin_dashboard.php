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
    </style>
</head>
<body>
<aside class="sidebar" id="sidebar">
        <h3>Menu</h3>
        <ul>
            <li><a href="customer_dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </aside>
    <h2>Daftar Pelanggan</h2>
    <table border="1">
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
            <td>
                <a href="edit_pelanggan.php?id=<?= $row['id_pelanggan']; ?>">Edit</a> | 
                <a href="hapus_pelanggan.php?id=<?= $row['id_pelanggan']; ?>" onclick="return confirm('Hapus pelanggan ini?')">Hapus</a> | 
                <a href="update_tagihan.php?id=<?= $row['id_pelanggan']; ?>">Update Tagihan</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
