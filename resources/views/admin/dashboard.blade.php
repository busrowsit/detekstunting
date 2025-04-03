{{-- <?php
include '../config.php';

session_start();

// Ambil data artikel dari database
$sql = "SELECT * FROM artikel ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>  --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halloween</title>
    <link rel="stylesheet" href="{{ asset('style-dashboard-admin.css') }}">
    <script defer src="{{ asset('js-dashboard-admin.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
</head>
<body>

    <!-- HEADER -->
    <header class="header">
    <h1 class="logo">
        <img src="{{ asset('assets/img/logo-01.png') }}">
    </h1>

    <nav class="nav-menu">
        <a href="#main">Beranda</a>
        <a href="{{ route('admin.hasilDeteksi.index') }}">Kelola Hasil Deteksi Stunting</a>
        <a href="{{ route('admin.pengguna.index') }}">Kelola Akun Pengguna</a>
        <a href="{{ route('admin.artikel.index') }}">Kelola Artikel</a>

        @if (Auth::check())
        <div class="user-dropdown">
            <button class="btn btn-secondary" style="background-color: orange; color: white; border: 2px solid orange; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
                {{ Auth::user()->username }} ▼
            </button>
            <div class="dropdown-content">
                <a href="{{ route('admin.profile.show', Auth::user()->id) }}">Profile</a>
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
    @endif
    
    </nav>
</header>



    <!-- HERO SECTION -->
    <section id="main" class="hero">
        @if (Auth::check())
        <div class="hero-content">
            <p class="top-text">#CEGAH & LAWAN STUNTING</p>
            <h2 class="big-title">SELAMAT DATANG <br> {{ Auth::user()->nama_lengkap }} </h2>
            <p class="desc">Lakukan update informasi secara berkala untuk layanan masyarakat yang lebih optimal</p>
            <div class="buttons">
                <a href="{{ route('admin.hasilDeteksi.index') }}" class="btn btn-primary">Kelola Hasil Deteksi</a>
                <a href="#pengertian" class="btn btn-secondary">Pelajari Stunting</a>
            </div>
        </div>
        @endif
        <img src="{{ asset('assets/img/adminn.png') }}" alt="Pumpkin" class="hero-img">
    </section>

    <section id="pengertian" class="hero hidden">
        <div class="hero-content">
            <p class="top-text">SIAPA YANG TAU APA SI ITU</p>
            <h2 class="big-title">STUNTING?</h2>
            <p class="desc">Stunting adalah kondisi di mana tinggi badan anak 
                lebih rendah dari standar usianya, yang merupakan indikasi kekurangan 
                gizi kronis dan gangguan pertumbuhan. Kondisi ini umumnya terjadi akibat
                 malnutrisi yang berkepanjangan, infeksi berulang, dan kurangnya stimulasi 
                 yang memadai pada masa awal kehidupan.</p>

            <p class="desc">
            Di Indonesia, stunting merupakan masalah kesehatan yang signifikan. 
            Menurut data Survei Status Gizi Indonesia (SSGI) 2022, prevalensi stunting 
            masih berada pada angka yang cukup tinggi, meskipun telah terjadi penurunan dalam 
            beberapa tahun terakhir. Pemerintah Indonesia telah menargetkan penurunan prevalensi 
            stunting menjadi 14% pada tahun 2024 melalui berbagai intervensi, seperti perbaikan 
            akses terhadap gizi, sanitasi, dan layanan kesehatan ibu dan anak.</p>
        </div>
        <img src="{{ asset('assets/img/anak 2-01.png') }}" alt="Pumpkin" style="max-width: 400px; animation: slideIn 1s ease-in-out;">
    </section>

    <section id="artikel" class="scrollable-section">
    <h2 class="section-title">Artikel tentang Stunting</h2>
    <div class="scroll-wrapper">
        <div class="scroll-container">
        @foreach ($artikels as $artikel )
        <div class="scroll-item">
            <a href="{{ route('admin.artikel.show', $artikel->id) }}">
                    <img src="{{ asset($artikel->gambar) }}" alt="{{ $artikel->judul }}">
                    <p>{{ $artikel->judul }}</p>
                    <span>{{ Str::limit( $artikel->deskripsi,100) }}...<span>
                </a>
            </div>
        @endforeach
        </div>
    </div>
</section> <br><br><br><br><br><br>

    <!-- FOOTER -->
    <footer class="footer">
        <p>Wsit Official Reserved - 2025</p>
    </footer>


    <script>
document.addEventListener("DOMContentLoaded", function () {
    const scrollContainer = document.querySelector(".scroll-container");
    let intervalTime = 2000; // Geser setiap 2 detik

    function slide() {
        let firstItem = scrollContainer.firstElementChild; // Ambil elemen pertama

        scrollContainer.style.transition = "transform 0.5s ease-in-out";
        scrollContainer.style.transform = `translateX(-230px)`; // Geser ke kiri

        setTimeout(() => {
            scrollContainer.style.transition = "none"; // Matikan animasi sejenak
            scrollContainer.appendChild(firstItem); // Pindahkan elemen pertama ke akhir
            scrollContainer.style.transform = "translateX(0)"; // Reset posisi
        }, 500); // Setelah animasi selesai
    }

    setInterval(slide, intervalTime);
});

document.addEventListener("DOMContentLoaded", function () {
    let elements = document.querySelectorAll(".hidden");

    function checkScroll() {
        elements.forEach((element) => {
            let position = element.getBoundingClientRect().top;
            let screenHeight = window.innerHeight;

            if (position < screenHeight - 100) {
                element.classList.add("show");
            }
        });
    }

    window.addEventListener("scroll", checkScroll);
    checkScroll(); // Cek saat halaman pertama kali dimuat
});

    document.addEventListener("DOMContentLoaded", function () {
        let scrollSection = document.querySelector(".scrollable-section");
        let scrollItems = document.querySelectorAll(".scroll-item");

        function checkScroll() {
            let sectionTop = scrollSection.getBoundingClientRect().top;
            let windowHeight = window.innerHeight;

            if (sectionTop < windowHeight - 100) {
                scrollSection.classList.add("show");
                scrollItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add("show");
                    }, index * 200); // Animasi satu per satu dengan delay
                });
                window.removeEventListener("scroll", checkScroll);
            }
        }

        window.addEventListener("scroll", checkScroll);
        checkScroll(); // Cek saat halaman pertama kali dibuka
    });
</script>

</body>
</html>
