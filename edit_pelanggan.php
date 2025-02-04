<?php
include 'includes/db_connection.php';

if (isset($_GET['id'])) {
    $id_pelanggan = $_GET['id'];
    $sql = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pelanggan);
    $stmt->execute();
    $result = $stmt->get_result();
    $pelanggan = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];

    $sql_update = "UPDATE pelanggan SET nama_pelanggan = ?, alamat = ? WHERE id_pelanggan = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssi", $nama_pelanggan, $alamat, $id_pelanggan);

    if ($stmt_update->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Gagal mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .form-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-label {
            position: absolute;
            left: 0.75rem;
            top: 0.75rem;
            padding: 0 0.25rem;
            background-color: #fff;
            color: #666;
            font-size: 1rem;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control:focus,
        .form-control:not(:placeholder-shown) {
            border-color: #4a90e2;
            outline: none;
        }

        .form-control:focus + .form-label,
        .form-control:not(:placeholder-shown) + .form-label {
            top: -0.5rem;
            left: 0.5rem;
            font-size: 0.85rem;
            color: #4a90e2;
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primary {
            background-color: #4a90e2;
            color: white;
        }

        .btn-primary:hover {
            background-color: #357abd;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="POST">
            <div class="form-group">
                <input type="text" 
                       class="form-control" 
                       name="nama_pelanggan" 
                       value="<?= $pelanggan['nama_pelanggan']; ?>" 
                       placeholder=" "
                       required>
                <label class="form-label">Nama</label>
            </div>

            <div class="form-group">
                <input type="text" 
                       class="form-control" 
                       name="alamat" 
                       value="<?= $pelanggan['alamat']; ?>" 
                       placeholder=" "
                       required>
                <label class="form-label">Alamat</label>
            </div>

            <div class="button-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="admin_dashboard.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>