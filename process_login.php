<?php
session_start();
include 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id_user']; // Simpan id_user
            $_SESSION['username'] = $user['username'];
            $_SESSION['id_level'] = $user['id_level']; // Simpan level user

            if ($user['id_level'] == 1) { // Admin
                header('Location: admin_dashboard.php');
            } else { // Pelanggan
                $_SESSION['id_pelanggan'] = $user['pelanggan_id']; // Simpan pelanggan_id
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

if (isset($error_message)) {
    $_SESSION['error'] = $error_message;
    header('Location: login.php');
    exit;
}
?>