<?php
// Mulai sesi
session_start();

// Include file koneksi database
include 'includes/db_connection.php';

// Periksa apakah data dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk memeriksa pengguna berdasarkan username
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password
        if (password_verify ($password, $user['password'])) { // Jika tidak di-hash
            // Set session untuk pengguna yang berhasil login
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect ke halaman sesuai role
            if ($user['role'] === 'administrator') {
                header('Location: admin_dashboard.php');
            } else {
                $_SESSION['id_pelanggan'] = $user['id_user'];
                header('Location: customer_dashboard.php');
            }
            exit;
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }
} else {
    $error_message = "Metode pengiriman tidak valid.";
}

// Jika ada error, redirect kembali ke halaman login
if (isset($error_message)) {
    $_SESSION['error'] = $error_message;
    header('Location: login.php');
    exit;
}
?>
