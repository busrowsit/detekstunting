<?php
include '../config.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $email = !empty($_POST['email']) ? $_POST['email'] : NULL; // Email opsional
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $role = "user"; // Default role sebagai user

    // Siapkan query
    $stmt = $conn->prepare("INSERT INTO users (username, nama_lengkap, tanggal_lahir, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $nama_lengkap, $tanggal_lahir, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<script>alert('Pendaftaran berhasil!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Pendaftaran gagal! Coba lagi.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
