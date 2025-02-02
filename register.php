<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - PLN</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            padding: 20px;
        }

        .container {
            max-width: 500px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        }

        .title {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-control {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background-color: white;
            padding: 0 5px;
            color: #666;
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control:focus,
        .form-control:not(:placeholder-shown) {
            border-color: #9b59b6;
            outline: none;
        }

        .form-control:focus + .form-label,
        .form-control:not(:placeholder-shown) + .form-label {
            top: 0;
            font-size: 14px;
            color: #9b59b6;
        }

        select.form-control {
            appearance: none;
            cursor: pointer;
        }

        select.form-control + .form-label {
            background: white;
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            border: none;
            color: white;
            font-size: 16px;
            font-weight: 500;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: linear-gradient(-135deg, #71b7e6, #9b59b6);
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php
    include 'includes/db_connection.php';
    $sql_tarif = "SELECT id_tarif, daya, tarifperkwh FROM tarif";
    $result_tarif = $conn->query($sql_tarif);
    ?>

    <div class="container">
        <h2 class="title">Register Pelanggan</h2>
        <form action="process_register.php" method="POST">
            <div class="form-group">
                <input type="text" class="form-control" id="username" name="username" placeholder=" " required>
                <label for="username" class="form-label">Username</label>
            </div>

            <div class="form-group">
                <input type="password" class="form-control" id="password" name="password" placeholder=" " required>
                <label for="password" class="form-label">Password</label>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="nama_pelanggan" name="nama_pelanggan" placeholder=" " required>
                <label for="nama_pelanggan" class="form-label">Nama Pelanggan</label>
            </div>

            <div class="form-group">
                <input type="text" class="form-control" id="alamat" name="alamat" placeholder=" " required>
                <label for="alamat" class="form-label">Alamat</label>
            </div>

            <div class="form-group">
                <select class="form-control" id="id_tarif" name="id_tarif" required>
                    <option value="" disabled selected></option>
                    <?php while ($row = $result_tarif->fetch_assoc()): ?>
                        <option value="<?= $row['id_tarif'] ?>">
                            <?= $row['daya'] ?> VA - Rp<?= number_format($row['tarifperkwh'], 2, ',', '.') ?>/kWh
                        </option>
                    <?php endwhile; ?>
                </select>
                <label for="id_tarif" class="form-label">Pilih Tarif</label>
            </div>

            <button type="submit" class="btn-register">Daftar</button>
        </form>
    </div>
</body>
</html>