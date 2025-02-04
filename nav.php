<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between; /* Atur jarak antara logo dan item navigasi */
}

nav ul li {
    display: inline;
}

nav ul li.logo {
    flex-grow: 1; /* Membuat logo tetap di kiri */
}

nav ul li a {
    text-decoration: none;
    padding: 10px 10px;
    display: block;
    color: black;
    width: max-content;
}

nav ul li a button {
    padding: 5px 10px;
    border: none; /* Hilangkan border default */
    background-color: #007BFF; /* Warna latar belakang */
    color: white; /* Warna teks */
    cursor: pointer; /* Ubah kursor saat hover */
    border-radius: 5px; /* Sudut membulat */
}

nav ul li a button:hover {
    background-color: #0056b3; /* Warna latar belakang saat hover */
}
    </style>
</head>
<body>
    <nav>
    <ul>
        <li class="logo"><a href="index.php">IniListrik</a></li>
        <li><a href="login.php"><button>Login</button></a></li>
    </ul>
</nav>
</body>
</html>
