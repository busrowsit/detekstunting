<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Dini</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #3d4917, #b8860b);
            color: white;
            text-align: center;
            padding: 20px;
        }
    
        .container {
            max-width: 400px;
            margin: auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
    
        .hasil-deteksi {
            background: linear-gradient(to right, #6a7324, #8a7725);
            padding: 15px;
            border-radius: 10px;
            color: white;
        }
    
        .rekomendasi-box {
            padding: 15px;
            border-radius: 10px;
            color: white; 
        }

        .hasil-judul {
            font-size: 20px;
            font-weight: bold;
        }
    
        .hasil-prediksi {
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
        }
    
        .normal {
            color: #28a745;
            /* Hijau */
        }
    
        .stunting {
            color: #dc3545;
            /* Merah */
        }
    
        .hasil-catatan {
            font-size: 12px;
            font-style: italic;
            color: #f1f1f1;
            margin-top: 8px;
        }
    
        .step {
            display: none;
        }
    
        .step.active {
            display: block;
        }
    
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
        }
    
        button {
            background: orange;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    
        button:hover {
            background: darkorange;
        }
    
        .input-container {
            display: flex;
            align-items: center;
            gap: 10px;
            /* Jarak antara input dan kategori */
        }
    
        .kategori {
            font-weight: bold;
            color: orange;
        }
    
        .input-box input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            /* Tambah padding kanan untuk ikon */
            background: #fff;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
    
        .input-box i {
            position: absolute;
            right: 15px;
            /* Geser ikon ke kanan */
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #888;
            cursor: pointer;
            /* Biar bisa diklik */
        }

/* Modal overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.3); /* latar belakang gelap */
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

/* Modal box */
.modal-box {
  background-color: #fff; /* putih */
  padding: 30px;
  border-radius: 10px;
  width: 85%;
  max-width: 85%;
  color: #000; /* teks hitam */
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
  overflow-y: auto;
  max-height: 90vh;
}

/* Judul */
.hasil-judul {
  font-size: 22px;
  margin-bottom: 15px;
  font-weight: bold;
  color: #333;
}

/* Catatan */
.hasil-catatan {
  font-size: 14px;
  color: #555;
  margin-top: 20px;
}

/* Rekomendasi box */
.rekomendasi-box {
  background-color: #ffffff !important;
  border-radius: 10px;
  padding: 20px;
  margin-top: 20px;
  color: #000; /* teks jadi hitam jika masih ada yang putih */
}


/* Tombol tutup */
.modal-box button {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #007bff;
  border: none;
  color: #fff;
  border-radius: 6px;
  cursor: pointer;
}

.input-merah {
    background-color: #dc3545;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    width: 100%;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.input-hijau {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    width: 100%;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.rekomendasi-item {
    display: flex;
    align-items: flex-start; /* agar label tetap di atas jika teks input tinggi */
    margin-bottom: 12px;
    gap: 10px; /* jarak antara label dan input */
}

.rekomendasi-item strong {
    width: 220px; /* lebar tetap untuk semua label agar rata kiri */
    flex-shrink: 0; /* supaya label tidak mengecil */
    display: inline-block;
    color: #333;
}

.rekomendasi-item input {
    flex: 1; /* input akan mengisi sisa ruang */
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

        </style>
    <link rel="stylesheet" href="{{ asset('user-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <header class="header">
        <h1 class="logo">
            <img src="{{ asset('assets/img/logo-01.png') }}">
        </h1>

        <nav class="nav-menu">
            <a href="{{ route('user.dashboard') }}">Beranda</a>
            <a href="">Deteksi Stunting</a>
            <a href="{{ route('user.deteksi.show', Auth::user()->id) }}">Riwayat Deteksi</a>
            <a href="{{ route('user.dashboard')}}#artikel">Artikel</a>

            @if (Auth::check())
        <div class="user-dropdown">
            <button class="btn btn-secondary" style="background-color: orange; color: white; border: 2px solid orange; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
                {{ Auth::user()->username }}  ▼
            </button>
        <div class="dropdown-content">
            <a href="{{ route('user.profile.show', Auth::user()->id) }}">Profile</a>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
        </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
            @endauth
        </nav>
    </header>

    <section id="main" class="hero">
        <div class="hero-content">
            <p class="top-text">#AYO DETEKSI STUNTING SEKARANG</p>
            <h2 class="big-title">ISI DENGAN SEKSAMA YA</h2>

            @if (session('success'))
            <p style="color: lightgreen;">{{ session('success') }}</p>
            @endif

            <form id="deteksiForm" method="POST" action="{{ route('user.deteksi.store') }}" enctype="multipart/form-data" onsubmit="return validateForm()">
                @csrf
                <div class="input-box">
                    <div class="step active">
                    <p class="top-text" style="font-size: 25px;">INFORMASI PENTING!</p>
                    <label style="display: block; margin-bottom: 15px;">
                        Untuk melakukan deteksi dini stunting, Anda harus sudah pernah melakukan pemeriksaan kehamilan (ANC) minimal pada kunjungan 3 di trimester kedua kehamilan, agar prediksi yang dihasilkan akurat.
                    </label>
                        <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
                    </div>
                </div>

                
                <div class="input-box">
                    <div class="step">
                    <input type="text" name="nama" value="{{ $user->nama_lengkap }}" required readonly style="background-color:rgb(155, 155, 155); cursor: not-allowed;">
                    <input type="text" name="tanggal_lahir" value="{{ $user->tanggal_lahir }}" required readonly style="background-color:rgb(155, 155, 155); cursor: not-allowed;">

                        <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button"
                            onclick="prevStep()">Kembali</button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
                    </div>
                </div>

                <div class="input-box">
                    <div class="step">
                        <label>HPHT (Hari Pertama Haid Terakhir)</label>
                        <div class="input-container">
                            <input type="date" name="hpht" id="inputHPHT" required oninput="hitungUsia()">
                        </div>
                        <label>Usia Ibu Saat Hamil</label>
                        <div class="input-container">
                            <input type="number" name="usia" id="inputUsia" required readonly style="background-color:rgb(155, 155, 155); cursor: not-allowed;">
                            <span class="kategori" id="kategoriUsia"></span>
                        </div>
                        <label>Berapa Ukuran Lingkar Lengan Atas (LILA) Anda?</label>
                        <div class="input-container">
                            <input type="number" step="0.1" name="lila" required oninput="updateKategori(this, 'kategoriLila')" placeholder="Dalam centimeter (cm)">
                            <span class="kategori" id="kategoriLila"></span>
                        </div>

                        <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button"
                            onclick="prevStep()">Kembali</button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
                    </div>
                </div>

                <div class="input-box">
                    <div class="step">
                        <label>Berapa Tinggi Badan Anda?</label>
                        <div class="input-container">
                            <input type="number" step="0.1" name="tb_ibu" required oninput="updateKategori(this, 'kategoriTB')" placeholder="Dalam centimeter (cm)">
                            <span class="top-text" id="kategoriTB"></span><br>
                        </div>

                        <label>Berapa Jumlah Anak Anda?</label>
                        <div class="input-container">
                            <input type="number" name="jumlah_anak" required
                                oninput="updateKategori(this, 'kategoriAnak')" placeholder="Contoh: 1">
                            <span class="top-text" id="kategoriAnak"></span><br>
                        </div>

                        <label>Berapa Jumlah Tablet Tambah Darah (TTD) yang Anda Konsumsi?</label>
                        <div class="input-container">
                            <input type="number" name="jumlah_ttd" required
                                oninput="updateKategori(this, 'kategoriTTD')" placeholder="Contoh: 120">
                            <span class="top-text" id="kategoriTTD"></span><br>
                        </div>

                        <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button"
                            onclick="prevStep()">Kembali</button>
                        <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
                    </div>
                </div>

                <div class="input-box">
                    <div class="step">
                        <label>Berapa Jumlah Pemeriksaan Kehamilan yang Anda Lakukan ke Dokter/Bidan/Fasyankes?</label>
                        <div class="input-container">
                            <input type="number" name="jumlah_anc" required oninput="updateKategori(this, 'kategoriANC')" placeholder="Contoh: 6">
                            <span class="top-text" id="kategoriANC"></span><br>
                        </div>

                        <label>Berapa Tekanan Darah Anda?</label>
                        <div class="input-container">
                            <input type="text" name="tekanan_darah" id="tekananDarahInput" 
                                    required placeholder="Contoh: 120/80"
                                    oninput="updateKategoriTekananDarah(this); validateTekananDarahFormat(this)">
                            <span class="top-text" id="kategoriTekananDarah"></span><br>
                            <small id="tekananDarahError" style="color: #ff0000; font-weight: bold; display: none;">Format harus 120/80</small>
                        </div>

                        <label>Berapa Kadar Hemoglobin (HB) Anda?</label>
                        <div class="input-container">
                            <input type="number" step="0.1" name="hb_ibu" required oninput="updateKategoriHB(this)" placeholder="Contoh: 11">
                            <span class="top-text" id="kategoriHB"></span><br>
                        </div>
                        <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button"
                            onclick="prevStep()">Kembali</button>
                        <button class="btn btn-danger btn-sm" type="submit">Cek Stunting</button>
                    </div>
                </div>
            </form>

            @if (session('hasil_deteksi'))
@php
    $hasil_prediksi = session('hasil_deteksi');
    $warna = ($hasil_prediksi == "NORMAL") ? "normal" : "stunting";
    $rekomendasi = session('rekomendasi');
@endphp

<!-- Modal Pop-up -->
<div id="popupModal" class="modal-overlay" style="display: none;">
    <div class="modal-box">
        <div class="modal-content" style="display: flex; gap: 20px; align-items: stretch; height: 100%;">
            <!-- Kolom Teks -->
            <div style="flex: 2;">
                <h3 class="hasil-judul">Hasil Deteksi Stunting</h3>
                <p style="color: #333;">
                    <strong>Prediksi Kelahiran Anak Anda:</strong>
                    <span class="{{ $warna }}" style="font-size: 28px; font-weight: bold; text-transform: uppercase;">
                        {{ $hasil_prediksi }}
                    </span>
                </p>
                <p class="hasil-catatan">
                    <strong>NB:</strong>
                    <em>Sistem DetekStunting dapat melakukan kesalahan dalam prediksi.</em> 
                    Hasil di atas hanya sebagai analisis awal. Selalu lakukan pemeriksaan kesehatan rutin 
                    selama kehamilan dan kunjungi fasilitas kesehatan jika Anda mengalami masalah kesehatan.
                </p>

                @if ($rekomendasi)
                <div class="rekomendasi-box">
                    <h3 class="hasil-judul">Rekomendasi untuk Anda:</h3>
                    <ul style="list-style: none; padding-left: 0; margin: 0;">
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">Usia Ibu:</strong>
                            <input type="text" value="{{ $rekomendasi['usia']['teks'] }}" readonly
                                class="{{ $rekomendasi['usia']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">LILA:</strong>
                            <input type="text" value="{{ $rekomendasi['lila']['teks'] }}" readonly
                                class="{{ $rekomendasi['lila']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">Tinggi Badan Ibu:</strong>
                            <input type="text" value="{{ $rekomendasi['tb']['teks'] }}" readonly
                                    class="{{ $rekomendasi['tb']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">Jumlah Anak:</strong>
                            <input type="text" value="{{ $rekomendasi['anak']['teks'] }}" readonly
                                    class="{{ $rekomendasi['anak']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">Tablet Tambah Darah (TTD):</strong>
                            <input type="text" value="{{ $rekomendasi['ttd']['teks'] }}" readonly
                                class="{{ $rekomendasi['ttd']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">ANC (Pemeriksaan Kehamilan):</strong>
                            <input type="text" value="{{ $rekomendasi['anc']['teks'] }}" readonly
                                    class="{{ $rekomendasi['anc']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                        <div class="rekomendasi-item">
                            <strong style="color: #333;">Tekanan Darah:</strong>
                            <input type="text" value="{{ $rekomendasi['td']['teks'] }}" readonly
                                    class="{{ $rekomendasi['td']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                        </div>
                        </li>
                        <li>
                            <div class="rekomendasi-item">
                            <strong style="color: #333;">Kadar HB:</strong>
                            <input type="text" value="{{ $rekomendasi['hb']['teks'] }}" readonly
                                    class="{{ $rekomendasi['hb']['status'] == 'merah' ? 'input-merah' : 'input-hijau' }}">
                            </div>
                        </li>
                    </ul>
                </div>
                @endif
            </div> <!-- akhir kolom teks -->

                        <!-- Kolom Gambar -->
            <div style="flex: 1; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset('assets/img/hasil.png') }}" alt="Ilustrasi Hasil" style="width: 100%; height: 100%; object-fit: contain; border-radius: 10px;">
            </div>
        </div> <!-- akhir modal-content -->

        <button onclick="closePopup()">Tutup</button>
    </div>
</div>
@endif

        
        </div>
        <img src="{{ asset('assets/img/momandson.png') }}" alt="Pumpkin" class="hero-img" style="width: auto; height: auto;">
    </section>

    <footer class="footer">
        <p>Wsit Official Reserved - 2025</p>
    </footer>

    <script>
        function validateForm() {
    const tekananInput = document.getElementById("tekananDarahInput");
    const value = tekananInput.value.trim();
    const errorText = document.getElementById("tekananDarahError");
    const regex = /^\d{2,3}\/\d{2,3}$/;

    if (!regex.test(value)) {
        errorText.style.display = "inline";
        tekananInput.focus();
        return false;
    }

    return true; // izinkan submit kalau valid
}

        function validateTekananDarahFormat(input) {
    const value = input.value.trim();
    const pattern = /^\d{2,3}\/\d{2,3}$/; // Format: 2–3 digit / 2–3 digit
    const errorMsg = document.getElementById("tekananDarahError");

    if (!pattern.test(value)) {
        errorMsg.style.display = "inline";
        input.style.borderColor = "red";
    } else {
        errorMsg.style.display = "none";
        input.style.borderColor = "";
    }
}

        let currentStep = 0;
        const steps = document.querySelectorAll(".step");
    
        function showStep(step) {
            steps.forEach((el, index) => {
                el.classList.toggle("active", index === step);
            });
        }
    
        function nextStep() {
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        }
    
        function prevStep() {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        }
    
        function hitungUsia() {
    let hphtInput = document.getElementById("inputHPHT").value;
    let tanggalLahirStr = "{{ $user->tanggal_lahir }}"; // Ambil tanggal lahir sebagai string
    let kategori = document.getElementById("kategoriUsia");

    if (hphtInput && tanggalLahirStr) {
        let hphtDate = new Date(hphtInput);
        let tanggalLahirParts = tanggalLahirStr.split('-'); // Asumsi format YYYY-MM-DD
        let tanggalLahir = new Date(tanggalLahirParts[0], tanggalLahirParts[1] - 1, tanggalLahirParts[2]); // Buat objek Date

        let usia = hphtDate.getFullYear() - tanggalLahir.getFullYear();

        // Koreksi perhitungan jika bulan/tanggal belum lewat dalam tahun berjalan
        if (
            hphtDate.getMonth() < tanggalLahir.getMonth() ||
            (hphtDate.getMonth() === tanggalLahir.getMonth() && hphtDate.getDate() < tanggalLahir.getDate())
        ) {
            usia--;
        }

        // Tampilkan hasil
        document.getElementById("inputUsia").value = usia;
        kategori.innerText = (usia < 20 || usia > 35) ? "Berisiko" : "Tidak berisiko";
    } else {
        document.getElementById("inputUsia").value = "";
        kategori.innerText = "";
    }
}
        function updateKategori(input, targetId) {
            let value = parseFloat(input.value);
            let kategori = "";
    
            if (targetId === "kategoriLila") {
                kategori = value < 23.3 ? "Berisiko" : "Tidak berisiko";
            } else if (targetId === "kategoriTB") {
                kategori = value < 150 ? "Pendek" : "Tinggi";
            } else if (targetId === "kategoriAnak") {
                kategori = value > 2 ? "Berisiko" : "Tidak berisiko";
            } else if (targetId === "kategoriTTD") {
                kategori = value < 90 ? "Kurang" : "Cukup";
            } else if (targetId === "kategoriANC") {
                kategori = value < 6 ? "Kurang" : "Lengkap";
            }
    
    
            document.getElementById(targetId).innerText = kategori;
        }
    
        function updateKategoriHB(input) {
            let value = parseFloat(input.value);
            let kategori = value < 7 ? "Anemia berat" : value >= 7 && value <= 8 ? "Anemia sedang" : value >= 9 && value <=
                10 ? "Anemia ringan" : "Normal";
            document.getElementById("kategoriHB").innerText = kategori;
        }
    
        function updateKategoriTekananDarah(input) {
            let tekanan = input.value.split("/");
            if (tekanan.length === 2) {
                let sistolik = parseInt(tekanan[0]);
                let diastolik = parseInt(tekanan[1]);
                let kategori = "";
    
                if (sistolik >= 130 || diastolik >= 90) {
                    kategori = "Hipertensi";
                } else if (sistolik <= 100 || diastolik <= 60) {
                    kategori = "Hipotensi";
                } else {
                    kategori = "Normal";
                }
    
                document.getElementById("kategoriTekananDarah").innerText = kategori;
            } else {
                document.getElementById("kategoriTekananDarah").innerText = "";
            }
        }

        window.onload = function() {
  document.getElementById("popupModal").style.display = "flex";
}
  function closePopup() {
    document.getElementById("popupModal").style.display = "none";
  }

  // Cek apakah hasil deteksi ada (pakai blade)
  @if(session('hasil_deteksi'))
    window.onload = function() {
      document.getElementById("popupModal").style.display = "flex";
    };
  @endif
        </script>
</body>
</html>