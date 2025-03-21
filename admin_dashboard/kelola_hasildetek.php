<?php
include '../config.php';
session_start();

// Ambil jumlah data per halaman (default 5)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

// Tentukan halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$queryTotal = "SELECT COUNT(DISTINCT u.id) AS total FROM users u WHERE u.role = 'user'";
$resultTotal = $conn->query($queryTotal);
$rowTotal = $resultTotal->fetch_assoc();
$totalData = $rowTotal['total'];

// Hitung jumlah halaman
$totalPages = ceil($totalData / $limit);

// Ambil data dengan limit dan offset
$query = "SELECT u.id AS user_id, u.nama_lengkap, 
                 COALESCE(r.usia, '-') AS usia, 
                 COALESCE(r.lila, '-') AS lila, 
                 COALESCE(r.tb_ibu, '-') AS tb_ibu, 
                 COALESCE(r.jumlah_anak, '-') AS jumlah_anak, 
                 COALESCE(r.hasil_deteksi, '-') AS hasil_deteksi, 
                 COALESCE(r.created_at, '-') AS created_at
          FROM users u
          LEFT JOIN (
              SELECT r1.* FROM riwayat_deteksi r1
              JOIN (SELECT user_id, MAX(created_at) AS max_date FROM riwayat_deteksi GROUP BY user_id) r2
              ON r1.user_id = r2.user_id AND r1.created_at = r2.max_date
          ) r ON u.id = r.user_id
          WHERE u.role = 'user'
          ORDER BY r.created_at DESC
          LIMIT $limit OFFSET $offset";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Hasil Deteksi</title>
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

        .btn {
            border-radius: 5px;
            padding: 8px 12px;
            text-decoration: none;
        }

        .btn-detail {
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-detail:hover {
            background-color: darkblue;
        }

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
    </style>
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
    <h2 class="big-title" style="color: white;">Kelola Riwayat Deteksi Pengguna</h2><br>

    <!-- Form untuk memilih jumlah data per halaman -->
    <div class="d-flex align-items-center justify-content-between mb-3">
    <form method="GET" action="" class="d-flex align-items-center">
        <label for="limit" class="me-2" style="color: white;">Tampilkan:</label>
        <select name="limit" id="limit" class="form-select" style="width: 150px;" onchange="this.form.submit()">
            <option value="5" <?= ($limit == 5) ? 'selected' : ''; ?>>5</option>
            <option value="10" <?= ($limit == 10) ? 'selected' : ''; ?>>10</option>
            <option value="20" <?= ($limit == 20) ? 'selected' : ''; ?>>20</option>
            <option value="50" <?= ($limit == 50) ? 'selected' : ''; ?>>50</option>
        </select>
    </form>
    </div>

    <table class="table table-bordered shadow">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nama Lengkap</th>
                <th>Usia</th>
                <th>LILA</th>
                <th>TB Ibu</th>
                <th>Jumlah Anak</th>
                <th>Hasil Deteksi</th>
                <th>Tanggal Deteksi</th>
                <th>Opsi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
                <td><?= $row['user_id']; ?></td>
                <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                <td><?= $row['usia'] !== '-' ? $row['usia'] . ' tahun' : '-'; ?></td>
                <td><?= $row['lila'] !== '-' ? $row['lila'] . ' cm' : '-'; ?></td>
                <td><?= $row['tb_ibu'] !== '-' ? $row['tb_ibu'] . ' cm' : '-'; ?></td>
                <td><?= $row['jumlah_anak']; ?></td>
                <td><?= htmlspecialchars($row['hasil_deteksi']); ?></td>
                <td><?= $row['created_at']; ?></td>
                <td>
                    <a href="riwayat_user.php?user_id=<?= $row['user_id']; ?>" class="btn btn-primary btn-sm">Lihat Riwayat</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Navigasi Pagination -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?limit=<?= $limit; ?>&page=<?= $i; ?>"><?= $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    </div>
</div>

<footer class="footer">
    <p style="color: white; font-family: 'Poppins', sans-serif;">WSIT Official Reserved - 2025</p>
</footer>

</body>
</html>
