<?php
// Include file koneksi database
include 'includes/db_connection.php';

// Fetch available tarif options from the database
$sql_tarif = "SELECT id_tarif, daya, tarifperkwh FROM tarif";
$result_tarif = $conn->query($sql_tarif);
?>

<form action="process_register.php" method="POST">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <label for="nama_pelanggan">Nama Pelanggan:</label>
    <input type="text" name="nama_pelanggan" id="nama_pelanggan" required>
    
    <label for="alamat">Alamat:</label>
    <input type="text" name="alamat" id="alamat" required>
    
    <label for="id_tarif">Pilih Tarif:</label>
    <select name="id_tarif" id="id_tarif" required>
        <option value="">Pilih Tarif</option>
        <?php while ($row = $result_tarif->fetch_assoc()): ?>
            <option value="<?= $row['id_tarif'] ?>">
                <?= $row['daya'] ?> VA - Rp<?= number_format($row['tarifperkwh'], 2, ',', '.') ?>/kWh
            </option>
        <?php endwhile; ?>
    </select>

    <label for="role">Role:</label>
    <select name="role" id="role" required>
        <option value="pelanggan">Pelanggan</option>
        <option value="admin">Admin</option>
    </select>

    <button type="submit">Daftar</button>
</form>
