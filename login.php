<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PLN</title>
    <link rel="stylesheet" href="style/nav.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
        }

        /* Wrapper untuk konten utama */
        .main-content {
            min-height: calc(100vh - 60px);
            padding-top: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-bottom: 20px;
        }

        .container {
            max-width: 400px;
            width: 100%;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            margin: 0 20px; /* Tambah margin kiri-kanan */
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

        .btn-login {
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
            margin-bottom: 20px;
        }

        .btn-login:hover {
            background: linear-gradient(-135deg, #71b7e6, #9b59b6);
        }

        .register-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #9b59b6;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        /* Style khusus untuk navbar jika diperlukan */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: white; /* atau sesuaikan dengan style nav Anda */
        }

        @media screen and (max-width: 480px) {
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <div class="main-content">
        <div class="container">
            <h2 class="title">Login</h2>
            <form action="process_login.php" method="POST">
                <div class="form-group">
                    <input type="text" class="form-control" id="username" name="username" placeholder=" " required>
                    <label for="username" class="form-label">Username</label>
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder=" " required>
                    <label for="password" class="form-label">Password</label>
                </div>

                <button type="submit" class="btn-login">Login</button>
                
                <div class="register-link">
                    Belum punya akun? <a href="register.php">Daftar di sini</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>