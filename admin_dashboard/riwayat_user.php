<?php
include '../config.php';
session_start();

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

$query = "SELECT * FROM riwayat_deteksi WHERE user_id = ?";
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


// Logika untuk download Print dan Excel
if (isset($_GET['download'])) {
    $download_type = $_GET['download'];

    // Ambil semua data tanpa paginasi
    $query_all = "SELECT * FROM riwayat_deteksi WHERE user_id = ? ORDER BY created_at DESC";
    $stmt_all = $conn->prepare($query_all);
    $stmt_all->bind_param("i", $user_id);
    $stmt_all->execute();
    $result_all = $stmt_all->get_result();

    if ($download_type === 'pdf') {
        // Ganti PDF menjadi Print
        echo "<html>
        <head>
            <title>Riwayat Deteksi Stunting</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid black; padding: 8px; text-align: center; }
                th { background-color: #f2f2f2; }
            </style>
        </head>
        <body onload='window.print(); window.close();'>

        <h2>Riwayat Deteksi Stunting</h2>
        <table>
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
            </tr>";

        $no = 1;
        while ($row = $result_all->fetch_assoc()) {
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

        echo "</table>
        </body>
        </html>";
        exit;

    } elseif ($download_type === 'excel') {
        // Header untuk Excel
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=Riwayat_Deteksi.csv");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Buat header kolom
        echo "No,Nama Lengkap,Kategori Usia,Kategori LILA,Kategori TB,Kategori Anak,Kategori TTD,Kategori ANC,Kategori TD,Kategori HB,Hasil Deteksi,Tanggal Deteksi\n";

        $no = 1;
        while ($row = $result_all->fetch_assoc()) {
            echo "{$no},{$row['nama_lengkap']},{$row['kategori_usia']},{$row['kategori_lila']},{$row['kategori_tb']},{$row['kategori_anak']},{$row['kategori_ttd']},{$row['kategori_anc']},{$row['kategori_td']},{$row['kategori_hb']},{$row['hasil_deteksi']},{$row['created_at']}\n";
            $no++;
        }
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Deteksi Pengguna</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .table {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            overflow: hidden;
        }

        .table th {
            background-color: #343a40 !important;
            color: white !important;
            text-align: center;
        }

        .table td, .table th {
            padding: 12px;
            text-align: center;
            vertical-align: middle;
        }
    </style>
        <link rel="stylesheet" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="header">
    <h1 class="logo">
        <img src="logo-01.png">
    </h1>

    <nav class="nav-menu">
        <a href="index.php#main">Beranda</a>
        <a href="kelola_hasildetek.php">Kelola Hasil Deteksi Stunting</a>
        <a href="kelola_user.php">Kelola Akun Pengguna</a>
        <a href="kelola_artikel.php">Kelola Artikel</a>

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
    <h2 class="big-title" style="color: white;">Riwayat Deteksi Pengguna</h2><br>

<!-- Tombol Tambah Artikel -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex gap-3">
        <a href="?user_id=<?= $user_id; ?>&download=pdf" class="btn btn-danger">Download PDF</a>
        <a href="?user_id=<?= $user_id; ?>&download=excel" class="btn btn-success">Download Excel</a>
    </div>

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


    <table class="table table-bordered shadow">
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
    $no = $start + 1; // Nomor urut sesuai halaman
    if ($result->num_rows > 0) {
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
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Belum ada data deteksi untuk pengguna ini.</td></tr>";
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

    <a href="kelola_hasildetek.php" class="btn btn-secondary">Kembali</a>
</div>
</div><br><br><br><br>
<footer class="footer">
    <p style="color: white; font-family: 'Poppins', sans-serif;">WSIT Official Reserved - 2025</p>
</footer>

</body>
</html>
