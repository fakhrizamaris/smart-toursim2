<?php
/**
 * Proses Login User
 * File: php/login.php
 */

session_start();
require_once 'config.php';

// Hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode request tidak diizinkan');
}

try {
    // Ambil data dari form
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = $_POST['remember'] ?? '0';

    // Validasi input kosong
    if (empty($email) || empty($password)) {
        jsonResponse(false, 'Email dan password harus diisi');
    }

    // Validasi format email
    if (!isValidEmail($email)) {
        jsonResponse(false, 'Format email tidak valid');
    }

    // Koneksi ke database
    $conn = getConnection();

    // Cari user berdasarkan email
    $query = "SELECT id, first_name, last_name, email, password, is_active FROM users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Email atau password salah');
    }

    $user = $stmt->fetch();

    // Cek apakah akun aktif
    if (!$user['is_active']) {
        jsonResponse(false, 'Akun Anda telah dinonaktifkan. Hubungi administrator.');
    }

    // Verifikasi password
    if (!verifyPassword($password, $user['password'])) {
        jsonResponse(false, 'Email atau password salah');
    }

    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['logged_in'] = true;

    // Jika remember me dicentang, buat session token
    if ($remember === '1') {
        $sessionToken = generateToken(64);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

        // Hapus session token lama untuk user ini
        $deleteOldTokens = "DELETE FROM user_sessions WHERE user_id = :user_id";
        $deleteStmt = $conn->prepare($deleteOldTokens);
        $deleteStmt->bindParam(':user_id', $user['id']);
        $deleteStmt->execute();

        // Insert session token baru
        $insertToken = "INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (:user_id, :token, :expires)";
        $tokenStmt = $conn->prepare($insertToken);
        $tokenStmt->bindParam(':user_id', $user['id']);
        $tokenStmt->bindParam(':token', $sessionToken);
        $tokenStmt->bindParam(':expires', $expiresAt);
        $tokenStmt->execute();

        // Set cookie untuk remember me
        setcookie('remember_token', $sessionToken, strtotime('+30 days'), '/', '', false, true);
    }

    // Update last login time
    $updateLogin = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = :id";
    $updateStmt = $conn->prepare($updateLogin);
    $updateStmt->bindParam(':id', $user['id']);
    $updateStmt->execute();

    // Log aktivitas login (opsional)
    try {
        $logQuery = "INSERT INTO activity_logs (user_id, activity, ip_address) VALUES (:user_id, 'login', :ip)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bindParam(':user_id', $user['id']);
        $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $logStmt->execute();
    } catch (PDOException $e) {
        // Jika tabel activity_logs belum ada, tidak masalah
    }

    jsonResponse(true, 'Login berhasil! Selamat datang kembali.', [
        'user_id' => $user['id'],
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'email' => $user['email']
    ]);

} catch (PDOException $e) {
    error_log("Login Error: " . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi.');
} catch (Exception $e) {
    error_log("General Login Error: " . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.');
}
?>