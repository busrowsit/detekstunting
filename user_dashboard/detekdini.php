<?php
session_start();
include '../config.php'; // Koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user dari database
$sql = "SELECT nama_lengkap, tanggal_lahir FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil tanggal lahir ibu dari database
$tanggal_lahir = new DateTime($user['tanggal_lahir']);
$tanggal_lahir_str = $tanggal_lahir->format('Y-m-d'); // Format untuk JavaScript
// Ambil HPHT dari input atau database
$hpht = isset($_POST['hpht']) ? new DateTime($_POST['hpht']) : null;

if ($hpht) {
    // Hitung usia ibu saat hamil (HPHT - Tanggal Lahir)
    $usia = $hpht->diff($tanggal_lahir)->y;
} else {
    $usia = null; // Jika HPHT belum diisi
}

$hasil_deteksi_terbaru = null; // Variabel untuk menampilkan hasil setelah submit

function deteksiStunting($hb, $ttd, $lila, $usia, $jumlah_anak, $tinggi_badan_ibu, $tekanan_darah, $anc) {
    if ($hb == "Anemia ringan") {
        if ($lila == "Berisiko") {
            if ($jumlah_anak == "Berisiko") {
                return "STUNTING";
            } else { // jumlah anak = Tidak Berisiko
                return "NORMAL";
            }
        } else { // lila = Tidak Berisiko
            return "NORMAL";
        }
    } else if ($hb == "Anemia sedang") {
        if ($usia == "Berisiko") {
            return "STUNTING";
        } else { // usia = Tidak Berisiko
            return "NORMAL";
        }
    } else if ($hb == "Normal") {
        if ($tekanan_darah == "Hipertensi") {
            if ($tinggi_badan_ibu == "Pendek") {
                if ($usia == "Berisiko") {
                    return "NORMAL";
                } else if ($usia == "Tidak Berisiko") {
                    if ($jumlah_anak == "Berisiko") {
                        return "NORMAL";
                    } else { // jumlah anak = Tidak Berisiko
                        return "STUNTING";
                    }
                }
            } else { // tinggi badan ibu = Tinggi
                return "NORMAL";
            }
        } else if ($tekanan_darah == "Hipotensi") {
            if ($tinggi_badan_ibu == "Pendek") {
                if ($usia == "Berisiko") {
                    if ($lila == "Berisiko") {
                        return "NORMAL";
                    } else { // lila = Tidak Berisiko
                        if ($anc == "Kurang") {
                            return "STUNTING";
                        } else { // anc = Lengkap
                            return "NORMAL";
                        }
                    }
                } else { // usia = Tidak Berisiko
                    if ($anc == "Kurang") {
                        if ($jumlah_anak == "Berisiko") {
                            return "NORMAL";
                        } else { // jumlah anak = Tidak Berisiko
                            return "STUNTING";
                        }
                    } else { // anc = Lengkap
                        if ($jumlah_anak == "Berisiko") {
                            return "STUNTING";
                        } else { // jumlah anak = Tidak Berisiko
                            if ($lila == "Berisiko") {
                                return "NORMAL";
                            } else { // lila = Tidak Berisiko
                                return "STUNTING";
                            }
                        }
                    }
                }
            } else { // tinggi badan ibu = Tinggi
                if ($jumlah_anak == "Berisiko") {
                    return "STUNTING";
                } else { // jumlah anak = Tidak Berisiko
                    if ($lila == "Berisiko") {
                        return "STUNTING";
                    } else { // lila = Tidak Berisiko
                        return "NORMAL";
                    }
                }
            }
        } else if ($tekanan_darah == "Normal") {
            if ($ttd == "Kurang") {
                return "STUNTING";
            } else if ($ttd == "Cukup") {
                if ($tinggi_badan_ibu == "Pendek") {
                    if ($lila == "Berisiko") {
                        if ($usia == "Berisiko") {
                            if ($anc == "Kurang") {
                                return "STUNTING";
                            } else { // anc = Lengkap
                                return "NORMAL";
                            }
                        } else { // usia = Tidak Berisiko
                            return "STUNTING";
                        }
                    } else { // lila = Tidak Berisiko
                        if ($usia == "Berisiko") {
                            return "STUNTING";
                        } else { // usia = Tidak Berisiko
                            if ($anc == "Kurang") {
                                return "NORMAL";
                            } else { // anc = Lengkap
                                if ($jumlah_anak == "Berisiko") {
                                    return "NORMAL";
                                } else { // jumlah anak = Tidak Berisiko
                                    return "STUNTING";
                                }
                            }
                        }
                    }
                } else { // tinggi badan ibu = Tinggi
                    if ($anc == "Kurang") {
                        if ($usia == "Berisiko") {
                            return "STUNTING";
                        } else { // usia = Tidak Berisiko
                            if ($lila == "Berisiko") {
                                return "STUNTING";
                            } else { // lila = Tidak Berisiko
                                return "NORMAL";
                            }
                        }
                    } else { // anc = Lengkap
                        if ($lila == "Berisiko") {
                            return "NORMAL";
                        } else { // lila = Tidak Berisiko
                            if ($jumlah_anak == "Berisiko") {
                                return "STUNTING";
                            } else { // jumlah anak = Tidak Berisiko
                                return "NORMAL";
                            }
                        }
                    }
                }
            }
        }
    }
    return "mbuh"; // Default jika tidak masuk ke kondisi mana pun
}





// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hpht = $_POST['hpht'];
    $lila = $_POST['lila'];
    $tb_ibu = floatval($_POST['tb_ibu']);
    $jumlah_anak = $_POST['jumlah_anak'];
    $jumlah_ttd = $_POST['jumlah_ttd'];
    $jumlah_anc = $_POST['jumlah_anc'];
    $tekanan_darah = $_POST['tekanan_darah'];
    $hb_ibu = floatval($_POST['hb_ibu']);

    // **Konversi kategori**
    $kategori_usia = ($usia < 20 || $usia > 35) ? 'Berisiko' : 'Tidak berisiko';
    $kategori_lila = ($lila < 23.3) ? 'Berisiko' : 'Tidak berisiko';
    $kategori_tb = ($tb_ibu < 150) ? 'Pendek' : 'Tinggi';
    $kategori_anak = ($jumlah_anak > 2) ? 'Berisiko' : 'Tidak berisiko';
    $kategori_ttd = ($jumlah_ttd < 90) ? 'Kurang' : 'Cukup';
    $kategori_anc = ($jumlah_anc < 6) ? 'Kurang' : 'Lengkap';

    // **Kategori Tekanan Darah**
    if (strpos($tekanan_darah, "/") === false) {
        $message = "Format tekanan darah harus dalam bentuk '120/80'";
    } else {
        list($sistolik, $diastolik) = explode("/", $tekanan_darah);
        $sistolik = (int) trim($sistolik);
        $diastolik = (int) trim($diastolik);
    
        if (!is_numeric($sistolik) || !is_numeric($diastolik)) {
            $message = "Tekanan darah harus berupa angka valid.";
        } else {
            if ($sistolik <= 100 && $diastolik <= 60) {
                $kategori_td = 'Hipotensi';
            } elseif ($sistolik >= 130 && $diastolik >= 90) {
                $kategori_td = 'Hipertensi';
            } else {
                $kategori_td = 'Normal';
            }
        }
    }
    
    // **hpht**
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $hpht)) {
        $message = "Format HPHT tidak valid. Gunakan format YYYY-MM-DD.";
    } else {
        $hpht_date = DateTime::createFromFormat('Y-m-d', $hpht);
        if (!$hpht_date) {
            $message = "Tanggal HPHT tidak valid.";
        }
    }

    
    // **Kategori HB Ibu**
    if ($hb_ibu < 7) {
        $kategori_hb = 'Anemia berat';
    } elseif ($hb_ibu >= 7 && $hb_ibu <= 8) {
        $kategori_hb = 'Anemia sedang';
    } elseif ($hb_ibu >= 9 && $hb_ibu <= 10) {
        $kategori_hb = 'Anemia ringan';
    } else {
        $kategori_hb = 'Normal';
    }

    // **Space untuk Decision Tree (hasil prediksi)**
    $hasil_deteksi = deteksiStunting($kategori_hb, $kategori_ttd, $kategori_lila, $kategori_usia, $kategori_anak, $kategori_tb, $kategori_td, $kategori_anc);

    // **Simpan ke database**
    $sql = "INSERT INTO riwayat_deteksi (
    user_id, nama_lengkap, tanggal_lahir, usia, kategori_usia, hpht, 
    lila, kategori_lila, tb_ibu, kategori_tb, jumlah_anak, kategori_anak, 
    jumlah_ttd, kategori_ttd, jumlah_anc, kategori_anc, tekanan_darah, kategori_td, 
    hb, kategori_hb, hasil_deteksi
) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ississdsdssssssssssss", // "s" untuk string, "i" untuk integer, "d" untuk decimal
        $user_id, 
        $user['nama_lengkap'], 
        $user['tanggal_lahir'], 
        $usia, 
        $kategori_usia, 
        $hpht, // HPHT tetap string
        $lila, 
        $kategori_lila, 
        $tb_ibu, 
        $kategori_tb, 
        $jumlah_anak, 
        $kategori_anak, 
        $jumlah_ttd, 
        $kategori_ttd, 
        $jumlah_anc, 
        $kategori_anc, 
        $tekanan_darah, 
        $kategori_td, 
        $hb_ibu, 
        $kategori_hb, 
        $hasil_deteksi
    );

    if (empty($hpht) || empty($lila) || empty($tb_ibu) || empty($jumlah_anak) || empty($jumlah_ttd) || empty($jumlah_anc) || empty($tekanan_darah) || empty($hb_ibu)) {
        $message = "Semua kolom harus diisi.";
    } else {
        // Proses penyimpanan data ke database
    }
    

    if ($stmt->execute()) {
        $message = "Data berhasil disimpan!";
        $hasil_deteksi_terbaru = [
            "Prediksi Kelahiran Anak Anda" => $hasil_deteksi,];
    } else {
        $message = "Terjadi kesalahan, coba lagi!";
    }
}
?>

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
        color: #28a745; /* Hijau */
    }
    .stunting {
        color: #dc3545; /* Merah */
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
        gap: 10px; /* Jarak antara input dan kategori */
    }
    .kategori {
        font-weight: bold;
        color: orange;
    }
    .input-box input {
        width: 100%;
        padding: 12px 45px 12px 15px; /* Tambah padding kanan untuk ikon */
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
        right: 15px; /* Geser ikon ke kanan */
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        color: #888;
    cursor: pointer; /* Biar bisa diklik */
}
    </style>
            <link rel="stylesheet" href="style.css">
            <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
        <!-- HEADER -->
        <header class="header">
    <h1 class="logo">
        <img src="logo-01.png">
    </h1>

    <nav class="nav-menu">
        <a href="index.php#main">Beranda</a>
        <a href="detekdini.php">Deteksi Stunting</a>
        <a href="riwayatdetek.php">Riwayat Deteksi</a>
        <a href="index.php#artikel">Artikel</a>

        <?php if (isset($_SESSION['username'])): ?>
            <div class="user-dropdown">
            <button class="btn btn-secondary" style="background-color: orange; color: white; border: 2px solid orange; padding: 10px 15px; border-radius: 5px; cursor: pointer;">
            <?= $_SESSION['username']; ?> ▼
            </button>
                <div class="dropdown-content">
                    <a href="edit_profil.php">Profiles</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login/index.php" class="btn btn-secondary">Login</a>
        <?php endif; ?>
    </nav>
</header>
<section id="main" class="hero">
<div class="hero-content">
<p class="top-text">#AYO DETEKSI STUNTING SEKARANG</p>
<h2 class="big-title">ISI DENGAN SEKSAMA YA</h2>

        <?php if (isset($message)): ?>
            <p style="color: lightgreen;"><?= $message; ?></p>
        <?php endif; ?>

        <form id="deteksiForm" method="POST">
        <div class="input-box">
            <div class="step active">
                <input type="text" name="nama" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required readonly>
                <input type="text" name="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" required readonly>

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
                    <input type="number" name="usia" id="inputUsia" required readonly>
                        <span class="kategori" id="kategoriUsia"></span>
                </div>
                    <label>Lingkar Lengan Atas (cm)</label>
                        <div class="input-container">
                            <input type="number" name="lila" required oninput="updateKategori(this, 'kategoriLila')">
                                <span class="kategori" id="kategoriLila"></span>
                        </div>

                <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button" onclick="prevStep()">Kembali</button>
                <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
            </div>
        </div>

        <div class="input-box">
            <div class="step">
            <label>Tinggi Badan Ibu (cm)</label>
                        <div class="input-container">
                            <input type="number" name="tb_ibu" required oninput="updateKategori(this, 'kategoriTB')">
                                <span class="top-text" id="kategoriTB"></span><br>
                        </div>

                <label>Jumlah Anak</label>
                    <div class="input-container">
                        <input type="number" name="jumlah_anak" required oninput="updateKategori(this, 'kategoriAnak')">
                            <span class="top-text"id="kategoriAnak"></span><br>
                        </div>

                <label>Jumlah Konsumsi TTD</label>
                    <div class="input-container">
                        <input type="number" name="jumlah_ttd" required oninput="updateKategori(this, 'kategoriTTD')">
                            <span class="top-text" id="kategoriTTD"></span><br>
                    </div>

                <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button" onclick="prevStep()">Kembali</button>
                <button class="btn btn-danger btn-sm" type="button" onclick="nextStep()">Lanjut</button>
            </div>
        </div>

        <div class="input-box">
            <div class="step">
                <label>Jumlah Kunjungan ANC</label>
                    <div class="input-container">
                        <input type="number" name="jumlah_anc" required oninput="updateKategori(this, 'kategoriANC')">
                            <span class="top-text" id="kategoriANC"></span><br>
                    </div>

                    <label>Tekanan Darah (misal: 120/80)</label>
                        <div class="input-container">
                            <input type="text" name="tekanan_darah" required oninput="updateKategoriTekananDarah(this)">
                                <span class="top-text" id="kategoriTekananDarah"></span><br>
                        </div>

                    <label>HB Ibu (g/dL)</label>
                        <div class="input-container">
                            <input type="number" step="0.1" name="hb_ibu" required oninput="updateKategoriHB(this)">
                                <span class="top-text" id="kategoriHB"></span><br>
                        </div>
                <button class="btn btn-danger btn-sm" style="background-color: red; color: white;" type="button" onclick="prevStep()">Kembali</button>
                <button class="btn btn-danger btn-sm" type="submit">Cek Stunting</button>
            </div>
        </div>
        </form>

        <br>
<!-- Hasil Deteksi Terbaru -->
<?php if ($hasil_deteksi_terbaru): ?>
    <div class="hasil-deteksi">
        <h3 class="hasil-judul">Hasil Deteksi Stunting</h3>
        <ul>
            <?php foreach ($hasil_deteksi_terbaru as $key => $value): ?>
                <?php if ($key == "Prediksi Kelahiran Anak Anda"): ?>
                    <?php 
                        $warna = ($value == "NORMAL") ? "normal" : "stunting";
                    ?>
                    <li><strong><?= $key ?>:</strong> <span class="hasil-prediksi <?= $warna ?>"><?= $value ?></span></li>
                <?php else: ?>
                    <li><strong><?= $key ?>:</strong> <?= $value ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <p class="hasil-catatan"><strong>NB:</strong> Sistem DetekStunting dapat melakukan kesalahan dalam prediksi. Hasil di atas hanya sebagai analisis awal. Selalu lakukan pemeriksaan kesehatan rutin selama kehamilan dan kunjungi fasilitas kesehatan jika Anda mengalami masalah kesehatan.</p>
    </div>
<?php endif; ?>


    </div>
    <img src="8073548.png" alt="Pumpkin" class="hero-img" style="width: auto; height: auto;">
</section>

<!-- FOOTER -->
<footer class="footer">
    <p>Wsit Official Reserved - 2025</p>
</footer>
    <script>
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
    let tanggalLahir = new Date("<?= $tanggal_lahir_str ?>"); // Ambil tanggal lahir ibu dari PHP
    let kategori = document.getElementById("kategoriUsia");

    if (hphtInput) {
        let hphtDate = new Date(hphtInput);
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
    let kategori = value < 7 ? "Anemia berat" : value >= 7 && value <= 8 ? "Anemia sedang" : value >= 9 && value <= 10 ? "Anemia ringan" : "Normal";
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
    </script>

</body>
</html>
