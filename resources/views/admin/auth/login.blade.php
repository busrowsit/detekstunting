<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Signup DetekStunting</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="{{ asset('style-login.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<header class="header">
    <h1 class="logo1">
        <img src="{{ asset('assets/img/logo-01.png') }}">
    </h1>
    <nav class="nav-menu">
        <a href="{{ url('/') }}">Beranda</a>
        <a href="{{ url('/deteksi-stunting') }}">Deteksi Stunting</a>
        <a href="{{ url('/#artikel') }}">Artikel</a>
    </nav>
</header>

<section class="hero">
    <div class="container">
        <div class="form-box login">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1>Login</h1>
                @if ($errors->any())
                    <div class="error-message">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="input-box">
                    <input type="text" id="login_username" name="username" placeholder="Username" value="{{ old('username') }}" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="loginPassword" placeholder="Password" value="{{ old('password') }}" required>
                    <i class='bx bx-hide' id="toggleLoginPassword"></i>
                </div>
                <div class="forgot-link">
                    <a href="{{ route('reset-password') }}">Lupa Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>

        <div class="form-box register">
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1>Pendaftaran Akun</h1>
                <div class="input-box">
                    <input type="text" id="register_username" name="username" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="text" name="nama_lengkap" id="nama_lengkap" placeholder="Nama Lengkap" required>
                    <i class='bx bxs-user-detail'></i>
                </div>
                <div class="input-box">
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" required>
                    <i class='bx bxs-calendar'></i>
                </div>
                <div class="input-box">
                    <input type="email" name="email" id="email" placeholder="Email (123@g.com jika tidak punya)" value="{{ old('email') }}">
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="registerPassword" placeholder="Password" required>
                    <i class='bx bx-hide' id="toggleRegisterPassword"></i>
                </div>
                <button type="submit" class="btn">Daftar</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <img src="{{ asset('assets/img/logo-01.png') }}" alt="Logo" class="logo">
                <h1>Selamat Datang</h1>
                <p>Belum punya akun?</p>
                <button class="btn register-btn">Daftar</button>
            </div>

            <div class="toggle-panel toggle-right">
                <img src="{{ asset('assets/img/logo-01.png') }}" alt="Logo" class="logo">
                <h1>Selamat Datang</h1>
                <p>Sudah mempunyai akun?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <p>Wsit Official Reserved - 2025</p>
    @auth
    {{-- <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn">Logout</button>
    </form> --}}
    @endauth
</footer>

<script src="{{ asset('script.js') }}"></script>
<script src="{{ asset('login.js') }}"></script>
</body>
</html>
