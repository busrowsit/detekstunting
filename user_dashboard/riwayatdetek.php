<?php
session_start();
include '../config.php'; // Pastikan file config.php berisi koneksi ke database

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil user ID dari sesi login

$query = "SELECT * FROM riwayat_deteksi WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// Ambil jumlah data per halaman dari dropdown (default 10)
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 5;

// Ambil halaman saat ini dari URL (default halaman 1)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $per_page;

// Ambil total data
$total_query = "SELECT COUNT(*) as total FROM riwayat_deteksi WHERE user_id = ?";
$stmt_total = $conn->prepare($total_query);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result()->fetch_assoc();
$total_data = $total_result['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $per_page);

// Ambil data sesuai limit dari yang terbaru ke yang lama
$query = "SELECT * FROM riwayat_deteksi WHERE user_id = ? ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $user_id, $start, $per_page);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Deteksi</title>
    <style>
        .table {
    background-color: rgba(255, 255, 255, 0.2); /* Transparansi lebih jelas */
    border-radius: 10px; /* Efek rounded */
    overflow: hidden; /* Agar radius berlaku pada seluruh tabel */
}

.table th {
    background-color: #343a40 !important; /* Warna header lebih gelap */
    color: white !important;
    text-align: center;
}

.table td, .table th {
    padding: 12px; /* Jarak lebih nyaman */
    text-align: center;
    vertical-align: middle;
}

.table img {
    border-radius: 8px; /* Gambar juga rounded */
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); /* Efek bayangan */
}

.btn {
    border-radius: 5px; /* Tombol lebih smooth */
}
/* Efek fade-in hanya untuk tabel */
.table-container {
    opacity: 0;
    transform: translateY(15px);
    animation: fadeInTable 0.8s ease-out forwards;
    animation-delay: 0.2s;
}

@keyframes fadeInTable {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Animasi untuk setiap baris tabel */
.table tbody tr {
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInRow 0.6s ease-out forwards;
    animation-delay: 0.3s;
}

@keyframes fadeInRow {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hindari efek animasi pada header dan footer */
.header,
.footer {
    opacity: 1 !important;
    transform: none !important;
    animation: none !important;
}
    </style>
    <link rel="stylesheet" href="style.css">
<script defer src="script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
<div class="container mt-4">
<div class="table-container">
<br><br><br>
<h2 class="big-title" style="color: white;">Riwayat Deteksi Dini</h2><br>

<div class="d-flex align-items-center justify-content-between mb-3">
<form method="GET" action="" class="d-flex align-items-center">
    <input type="hidden" name="user_id" value="<?= $user_id; ?>">
    <label for="per_page" class="me-2" style="color: white;">Tampilkan: </label>
    <select name="per_page" id="per_page" class="form-select" style="width: 150px;" onchange="this.form.submit()">
        <option value="5" <?= $per_page == 5 ? 'selected' : '' ?>>5</option>
        <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
        <option value="20" <?= $per_page == 20 ? 'selected' : '' ?>>20</option>
    </select>
</form>
</div>

    <table class="table table-bordered shadow" style="background-color: rgba(255, 255, 255, 0.2); border-radius: 10px; overflow: hidden;">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Kategori Usia</th>
            <th>Kategori LILA</th>
            <th>Kategori TB</th>
            <th>Kategori Anak</th>
            <th>Kategori TTD</th>
            <th>Kategori ANC</th>
            <th>Kategori TD</th>
            <th>Kategori HB</th>
            <th>Hasil Deteksi</th>
            <th>Tanggal Deteksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$no}</td>
                <td>{$row['nama_lengkap']}</td>
                <td>{$row['kategori_usia']}</td>
                <td>{$row['kategori_lila']}</td>
                <td>{$row['kategori_tb']}</td>
                <td>{$row['kategori_anak']}</td>
                <td>{$row['kategori_ttd']}</td>
                <td>{$row['kategori_anc']}</td>
                <td>{$row['kategori_td']}</td>
                <td>{$row['kategori_hb']}</td>
                <td>{$row['hasil_deteksi']}</td>
                <td>{$row['created_at']}</td>
            </tr>";
            $no++;
        }
        ?>
    </tbody>
</table>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?user_id=<?= $user_id; ?>&per_page=<?= $per_page; ?>&page=<?= $page - 1; ?>">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?user_id=<?= $user_id; ?>&per_page=<?= $per_page; ?>&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?user_id=<?= $user_id; ?>&per_page=<?= $per_page; ?>&page=<?= $page + 1; ?>">Next</a>
        </li>
    </ul>
</nav>
</div></div><br><br><br>

    <!-- FOOTER -->
    <footer class="footer">
    <p style="color: white; font-family: 'Poppins', sans-serif;">Wsit Official Reserved - 2025</p>
    </footer>
</body>
</html>
