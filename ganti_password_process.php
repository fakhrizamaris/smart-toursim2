<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Cek apakah password baru cocok
    if ($new_password !== $confirm_new_password) {
        header("Location: profil.php?status=pwdmismatch");
        exit();
    }

    // Ambil password lama dari DB
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password lama
    if (password_verify($current_password, $user['password'])) {
        // Hash password baru
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password baru di DB
        $update_sql = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($update_stmt, "si", $hashed_new_password, $user_id);

        if (mysqli_stmt_execute($update_stmt)) {
            header("Location: profil.php?status=pwdsuccess");
        } else {
            header("Location: profil.php?status=error");
        }
    } else {
        header("Location: profil.php?status=wrongpwd");
    }
    exit();
}
