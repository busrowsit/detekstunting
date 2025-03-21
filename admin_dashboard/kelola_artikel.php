<?php
include '../config.php'; // Pastikan file koneksi database tersedia

session_start();

// Query untuk mengambil data artikel
$query = "SELECT * FROM artikel ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);

// Jika tombol hapus diklik
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $sql = "DELETE FROM artikel WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Artikel berhasil dihapus!'); window.location='kelola_artikel.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus artikel!');</script>";
    }
}
// Jumlah data per halaman (default 10)
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 5;

// Halaman saat ini (default halaman 1)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Hitung offset
$start = ($page - 1) * $per_page;

// Hitung total data
$total_query = "SELECT COUNT(*) as total FROM artikel";
$total_result = mysqli_query($conn, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $per_page);

// Query untuk ambil data sesuai limit dan urutan terbaru
$query = "SELECT * FROM artikel ORDER BY tanggal DESC LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Artikel</title>
    <link rel="stylesheet" href="style.css">
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
    <h2 class="big-title" style="color: white;">Kelola Artikel</h2>
    
    <!-- Tombol Tambah Artikel -->
    <div class="d-flex align-items-center justify-content-between mb-3">
    <a href="tambah_artikel.php" class="btn btn-primary" style="background-color: orange; color: white; border: 2px solid orange; padding: 10px 15px; border-radius: 5px; cursor: pointer;">Tambah User</a>
    
    <form method="GET" action="" class="d-flex align-items-center">
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
            <th>ID</th>
            <th>Judul</th>
            <th>Gambar</th>
            <th>Deskripsi</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= htmlspecialchars($row['judul']); ?></td>
            <td><img src="../images/<?= htmlspecialchars($row['gambar']); ?>" width="100"></td>
            <td><?= substr(htmlspecialchars($row['deskripsi']), 0, 50); ?>...</td>
            <td><?= $row['tanggal']; ?></td>
            <td>
                <a href="artikel.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Buka</a>
                <a href="edit_artikel.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm" style="background-color: orange; color: white;">Edit</a>
                <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <!-- Tombol Previous -->
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $page - 1; ?>">Previous</a>
        </li>

        <!-- Nomor Halaman -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $page + 1; ?>">Next</a>
        </li>
    </ul>
</nav>

</div></div><br><br><br>

    <!-- FOOTER -->
    <footer class="footer">
    <p style="color: white; font-family: 'Poppins', sans-serif;">Wsit Official Reserved - 2025</p>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
