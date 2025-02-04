<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style/nav.css">
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        /* Hero section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1552965734-5b9e868808b6?q=80&w=1080&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .hero-content {
            max-width: 800px;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* Features section */
        .features {
            padding: 80px 20px;
            background-color: #f5f5f5;
        }

        .features h2 {
            text-align: center;
            margin-bottom: 50px;
            color: #333;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }

        .feature-card i {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin-bottom: 15px;
            color: #333;
        }

        /* CTA section */
        .cta {
            padding: 80px 20px;
            text-align: center;
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: white;
        }

        .cta h2 {
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            background-color: white;
            color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-3px);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="hero">
        <div class="hero-content">
            <h1>Pengelolaan Listrik Modern</h1>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quidem necessitatibus fugit atque unde dolor laboriosam perferendis cumque molestiae animi quod quos explicabo, ea possimus. Cupiditate.</p>
        </div>
    </section>

    <section class="features">
        <h2>Fitur Utama</h2>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-bolt"></i>
                <h3>Monitoring Real-time</h3>
                <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur, dicta.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Analisis Penggunaan</h3>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Minima, nesciunt.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Penghematan Biaya</h3>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Culpa, consectetur.</p>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Mulai Kelola Listrik Anda Sekarang</h2>
        <a href="login.php" class="btn">Pelajari Lebih Lanjut</a>
    </section>
</body>
</html>