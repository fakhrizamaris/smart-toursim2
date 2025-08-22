<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi input bisa ditambahkan di sini

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $user['password'])) {
            // Jika cocok, simpan data penting ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['profile_picture'] = $user['profile_picture']; // Pastikan ini ada

            // Arahkan ke dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            // Password salah
            header("Location: login.html?error=wrong_password");
            exit();
        }
    } else {
        // Email tidak ditemukan
        header("Location: login.html?error=user_not_found");
        exit();
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
