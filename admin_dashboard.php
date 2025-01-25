<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_connection.php';

// Periksa apakah pengguna sudah login dan memiliki peran administrator
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'administrator') {
    header('Location: login.php');
    exit;
}

// Ambil data pelanggan dari database
$sql = "SELECT * FROM pelanggan";
$result = $conn->query($sql);
if (isset($_POST['input_penggunaan'])) {
    $pelanggan_id = $_POST['pelanggan_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $penggunaan = $_POST['penggunaan'];

    // Tarif per kWh (misalnya Rp1500)
    $tarif_per_kwh = 1500;
    $tagihan = $penggunaan * $tarif_per_kwh;

    // Masukkan data ke tabel penggunaan
    $sql = "INSERT INTO penggunaan (pelanggan_id, bulan, tahun, penggunaan, tagihan) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issii", $pelanggan_id, $bulan, $tahun, $penggunaan, $tagihan);
    $stmt->execute();

    header('Location: admin_dashboard.php');
    exit;
}


// Periksa apakah form telah disubmit untuk menambah atau mengedit pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_meter = $_POST['no_meter'];

    if (isset($_POST['id'])) {
        // Update pelanggan
        $id = $_POST['id'];
        $sql = "UPDATE pelanggan SET nama = ?, alamat = ?, no_meter = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $alamat, $no_meter, $id);
    } else {
        // Tambah pelanggan baru
        $sql = "INSERT INTO pelanggan (nama, alamat, no_meter) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $alamat, $no_meter);
    }
    $stmt->execute();
    header('Location: admin_dashboard.php');
    exit;
}

// Periksa apakah ada permintaan untuk menghapus pelanggan
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Hapus data penggunaan terlebih dahulu
    $sql = "DELETE FROM penggunaan WHERE pelanggan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Baru hapus data pelanggan
    $sql = "DELETE FROM pelanggan WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style/nav.css">
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="containerAdmin">
        <h2>Daftar Pelanggan</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Nomor Meter</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                        <td><?php echo htmlspecialchars($row['no_meter']); ?></td>
                        <td>
                            <a href="admin_dashboard.php?edit=<?php echo $row['id']; ?>">Edit</a>
                            <a href="admin_dashboard.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan ini?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h2><?php echo isset($_GET['edit']) ? 'Edit Pelanggan' : 'Tambah Pelanggan'; ?></h2>
        <form action="admin_dashboard.php" method="POST">
            <?php if (isset($_GET['edit'])): ?>
                <?php
                $id = $_GET['edit'];
                $sql = "SELECT * FROM pelanggan WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $pelanggan = $result->fetch_assoc();
                ?>
                <input type="hidden" name="id" value="<?php echo $pelanggan['id']; ?>">
            <?php endif; ?>
            <div class="input-group">
                <label for="nama">Nama</label>
                <input type="text" id="nama" name="nama" value="<?php echo isset($pelanggan) ? htmlspecialchars($pelanggan['nama']) : ''; ?>" required>
            </div>
            <div class="input-group">
                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" value="<?php echo isset($pelanggan) ? htmlspecialchars($pelanggan['alamat']) : ''; ?>" required>
            </div>
            <div class="input-group">
                <label for="no_meter">Nomor Meter</label>
                <input type="text" id="no_meter" name="no_meter" value="<?php echo isset($pelanggan) ? htmlspecialchars($pelanggan['no_meter']) : ''; ?>" required>
            </div>
            <button type="submit"><?php echo isset($pelanggan) ? 'Update' : 'Tambah'; ?></button>
        </form>
    </div>
    <div class="containerAdmin">
        <h2>Input Penggunaan Listrik</h2>
        <form action="admin_dashboard.php" method="POST">
            <div class="input-group">
                <label for="pelanggan_id">Pelanggan</label>
                <select id="pelanggan_id" name="pelanggan_id" required>
                    <?php
                    // Ambil daftar pelanggan untuk dropdown
                    $sql = "SELECT id, nama FROM pelanggan";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['id']}'>{$row['nama']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="bulan">Bulan</label>
                <input type="text" id="bulan" name="bulan" placeholder="Contoh: Januari" required>
            </div>
            <div class="input-group">
                <label for="tahun">Tahun</label>
                <input type="number" id="tahun" name="tahun" placeholder="Contoh: 2025" required>
            </div>
            <div class="input-group">
                <label for="penggunaan">Penggunaan (kWh)</label>
                <input type="number" id="penggunaan" name="penggunaan" required>
            </div>
            <button type="submit" name="input_penggunaan">Simpan Penggunaan</button>
        </form>

    </div>
    <h2>Riwayat Penggunaan Listrik</h2>
<table>
    <thead>
        <tr>
            <th>Nama Pelanggan</th>
            <th>Bulan</th>
            <th>Tahun</th>
            <th>Penggunaan (kWh)</th>
            <th>Tagihan (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT p.nama, g.bulan, g.tahun, g.penggunaan, g.tagihan 
                FROM penggunaan g 
                JOIN pelanggan p ON g.pelanggan_id = p.id 
                ORDER BY g.tahun DESC, g.bulan DESC";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>" . htmlspecialchars($row['nama']) . "</td>
                <td>" . htmlspecialchars($row['bulan']) . "</td>
                <td>" . htmlspecialchars($row['tahun']) . "</td>
                <td>" . htmlspecialchars($row['penggunaan']) . "</td>
                <td>" . number_format($row['tagihan'], 0, ',', '.') . "</td>
            </tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>