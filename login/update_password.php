<?php
include "../config.php"; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST["nama_lengkap"];
    $password_baru = password_hash($_POST["password_baru"], PASSWORD_DEFAULT);

    // Cek apakah nama ada di database
    $query = "SELECT * FROM users WHERE nama_lengkap = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_lengkap);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update password jika nama ditemukan
        $update_query = "UPDATE users SET password = ? WHERE nama_lengkap = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $password_baru, $nama_lengkap);
        $update_stmt->execute();

        echo "<script>alert('Password berhasil direset! Silakan login dengan password baru.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Nama tidak ditemukan!'); window.location='reset_password.php';</script>";
    }
}
?>
