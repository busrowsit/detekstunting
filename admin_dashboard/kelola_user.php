<?php
include '../config.php';
session_start();

// Ambil data user (hanya role 'user')
$query = "SELECT * FROM users WHERE role = 'user'";
$result = mysqli_query($conn, $query);

// Hapus user
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM users WHERE id = $id");
    echo "<script>alert('User berhasil dihapus!'); window.location='kelola_user.php';</script>";
}

// Reset password user
if (isset($_GET['reset'])) {
    $id = $_GET['reset'];
    $default_password = password_hash("password123", PASSWORD_BCRYPT); // Password default
    mysqli_query($conn, "UPDATE users SET password='$default_password' WHERE id = $id");
    echo "<script>alert('Password berhasil direset ke password123!'); window.location='kelola_user.php';</script>";
}
// Ambil jumlah data per halaman dari dropdown (default 10)
$per_page = isset($_GET['per_page']) ? intval($_GET['per_page']) : 5;

// Ambil halaman saat ini dari URL (default halaman 1)
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $per_page;

// Ambil total data untuk menghitung jumlah halaman
$total_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$total_result = mysqli_query($conn, $total_query);
$total_data = mysqli_fetch_assoc($total_result)['total'];

// Hitung total halaman
$total_pages = ceil($total_data / $per_page);

// Ambil data sesuai halaman dan limit
$query = "SELECT * FROM users WHERE role = 'user' LIMIT $start, $per_page";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pengguna</title>
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
    <h2 class="big-title" style="color: white;">Kelola Akun Pengguna</h2>
    
    <!-- Tombol Tambah Artikel -->
    <div class="d-flex align-items-center justify-content-between mb-3">
    <a href="tambah_user.php" class="btn btn-primary" style="background-color: orange; color: white; border: 2px solid orange; padding: 10px 15px; border-radius: 5px; cursor: pointer;">Tambah User</a>
    
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
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Tanggal Lahir</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td><?= htmlspecialchars($row['username']); ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                <td><?= htmlspecialchars($row['email'] ?? '-'); ?></td>
                <td><?= $row['tanggal_lahir']; ?></td>
                <td>
                    <a href="?reset=<?= $row['id']; ?>" class="btn btn-warning btn-sm" style="background-color: orange; color: white;" onclick="return confirm('Reset password user ini?')">Reset Password</a>
                    <a href="?hapus=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $page - 1; ?>">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
            <a class="page-link" href="?per_page=<?= $per_page; ?>&page=<?= $page + 1; ?>">Next</a>
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
