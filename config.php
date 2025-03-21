<?php
$host = "localhost"; // Ganti jika bukan lokal
$user = "root"; // Username MySQL (default root)
$pass = ""; // Password MySQL (kosong jika default XAMPP)
$dbname = "detekstunting";

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
