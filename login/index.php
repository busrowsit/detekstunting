<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Signup DetekStunting </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="login.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<header class="header">
    <h1 class="logo1">
    <img src="logo-01.png">
</h1>
        <nav class="nav-menu">
            <a href="../index.php">Beranda</a>
            <a href="#">Deteksi Stunting</a>
            <a href="../index.php#artikel">Artikel</a>
        </nav>
    </header>

    <section class="hero">
    <div class="container">
    <div class="form-box login">
        <form action="login.php" method="POST">
            <h1>Login</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="loginPassword" placeholder="Password" required>
                <i class='bx bx-hide' id="toggleLoginPassword"></i> <!-- Unique ID for login toggle -->
            </div>
            <div class="forgot-link">
                <a href="reset_password.php">Lupa Password?</a>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>

    <div class="form-box register">
        <form action="register.php" method="POST">
            <h1>Pendaftaran Akun</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="Username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
                <i class='bx bxs-user-detail'></i>
            </div>
            <div class="input-box">
                <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" required>
                <i class='bx bxs-calendar'></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email (Opsional)">
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="registerPassword" placeholder="Password" required>
                <i class='bx bx-hide' id="toggleRegisterPassword"></i> <!-- Unique ID for register toggle -->
            </div>
            <button type="submit" class="btn">Daftar</button>
        </form>
    </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <img src="logo-01.png" alt="Logo" class="logo">
                <h1>Selamat Datang</h1>
                <p>Belum punya akun?</p>
                <button class="btn register-btn">Daftar</button>
            </div>

            <div class="toggle-panel toggle-right">
                <img src="logo-01.png" alt="Logo" class="logo">
                <h1>Selamat Datang</h1>
                <p>Sudah mempunyai akun?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
    </section>
        <!-- FOOTER -->
        <footer class="footer">
        <p>Wsit Official Reserved - 2025</p>
    </footer>

    <script  src="./script.js"></script>
    <script  src="login.js"></script>
</body>
</html>
