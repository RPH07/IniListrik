<?php
// Include file koneksi database
include 'includes/db_connection.php';

// Periksa apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $nama_pelanggan = $_POST['nama_pelanggan'];
    $alamat = $_POST['alamat'];
    $id_tarif = $_POST['id_tarif']; // Dapatkan id_tarif dari form

    // Validasi id_tarif
    $sql_validate_tarif = "SELECT COUNT(*) as count FROM tarif WHERE id_tarif = ?";
    $stmt_validate_tarif = $conn->prepare($sql_validate_tarif);
    $stmt_validate_tarif->bind_param("i", $id_tarif);
    $stmt_validate_tarif->execute();
    $result_validate_tarif = $stmt_validate_tarif->get_result();
    $row_validate_tarif = $result_validate_tarif->fetch_assoc();

    if ($row_validate_tarif['count'] == 0) {
        die("Tarif tidak valid.");
    }

    // Cek apakah username sudah terdaftar
    $sql_check = "SELECT * FROM user WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        die("Username sudah terdaftar!");
    } else {
        // Hash password untuk keamanan
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Tambahkan user baru
        $sql_insert_user = "INSERT INTO user (username, password, id_level) VALUES (?, ?, ?)";
        $id_level = ($role === 'pelanggan') ? 2 : 1;
        $stmt_insert_user = $conn->prepare($sql_insert_user);
        $stmt_insert_user->bind_param("ssi", $username, $hashed_password, $id_level);

        if ($stmt_insert_user->execute()) {
            // Jika role adalah pelanggan, tambahkan data pelanggan
            if ($role === 'pelanggan') {
                $id_user = $stmt_insert_user->insert_id; // Dapatkan ID user yang baru saja dimasukkan
                $sql_insert_pelanggan = "INSERT INTO pelanggan (username, password, nomor_kwh, nama_pelanggan, alamat, id_tarif) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt_insert_pelanggan = $conn->prepare($sql_insert_pelanggan);

                $nomor_kwh = uniqid(); // Nomor KWH bisa digenerate otomatis
                $stmt_insert_pelanggan->bind_param("sssssi", $username, $hashed_password, $nomor_kwh, $nama_pelanggan, $alamat, $id_tarif);

                if ($stmt_insert_pelanggan->execute()) {
                    header("Location: login.php");
                    exit;
                } else {
                    die("Gagal menyimpan data pelanggan: " . $stmt_insert_pelanggan->error);
                }
            }
        } else {
            die("Pendaftaran gagal: " . $stmt_insert_user->error);
        }
    }
}
?>
