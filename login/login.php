<?php
include '../config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username; // Simpan nama pengguna
            $_SESSION['role'] = $role;

            if ($role == "admin") {
                echo "<script>alert('Login sebagai Admin berhasil!'); window.location.href='../admin_dashboard/index.php';</script>";
            } else {
                echo "<script>alert('Login sebagai User berhasil!'); window.location.href='../user_dashboard/index.php';</script>";
            }
        } else {
            echo "<script>alert('Password salah!'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan!'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
