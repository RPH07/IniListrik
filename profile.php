<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db_connection.php';
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan</title>
    <link rel="stylesheet" href="style/profil.css">
    <link rel="stylesheet" href="style/nav.css">
</head>
<body>
<?php include 'nav.php'; ?>
    <div class="container">
        <h4><a href="customer_dashboard.php">kembali ke beranda</a></h4>
        <h1>Profil Pelanggan</h1>
        <div class="formContainer">
            <form id="profileForm" action="process_profile.php" method="POST">
                <input type="hidden" name="id" id="id" value="">
                
                <div class="input-group">
                    <input type="text" name="nama" id="nama" placeholder=" " required>
                    <label for="nama">Nama:</label>
                </div>
                <div class="input-group">
                    <input type="text" name="no_meter" id="no_meter" placeholder=" " required>
                    <label for="no_meter">Nomor Meter:</label>
                </div>
                <div class="input-group">
                    <textarea name="alamat" id="alamat" placeholder=" " rows="4" required></textarea>
                    <label for="alamat">Alamat:</label>
                </div>
                <button type="submit" id="submitButton">Simpan</button>
            </form>
        </div>



        <h2>Data Pelanggan</h2>
        <table class="pelanggan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Nomor Meter</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="pelangganTable">
                <?php
                $query = "SELECT * FROM pelanggan WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "
                        <tr>
                            <td>{$no}</td>
                            <td>{$row['nama']}</td>
                            <td>{$row['no_meter']}</td>
                            <td>{$row['alamat']}</td>
                            <td>
                                <button class='edit-btn' onclick='editPelanggan(".json_encode($row).")'>Edit</button>
                                <a class='delete-link' href='delete_profile.php?id={$row['id']}' onclick='return confirm(\"Hapus pelanggan ini?\")'>Hapus</a>
                            </td>
                        </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada data pelanggan.</td></tr>";
                }
                ?>
            </tbody>
        </table>


    </div>

    <script>
        function editPelanggan(data) {
            document.getElementById('id').value = data.id;
            document.getElementById('nama').value = data.nama;
            document.getElementById('no_meter').value = data.no_meter;
            document.getElementById('alamat').value = data.alamat;
            document.getElementById('submitButton').textContent = 'Update';
        }
    </script>
</body>
</html>
