<?php

/**
 * Proses Registrasi User
 * File: php/register.php
 */

session_start();
require_once 'config.php';

// Hanya menerima request POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode request tidak diizinkan');
}

try {
    // Ambil data dari form
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $interest = sanitizeInput($_POST['interest'] ?? '');
    $terms = $_POST['terms'] ?? '';

    // Validasi input kosong
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password) || empty($interest)) {
        jsonResponse(false, 'Semua field harus diisi');
    }

    // Validasi checkbox terms
    if ($terms !== '1') {
        jsonResponse(false, 'Anda harus menyetujui syarat dan ketentuan');
    }

    // Validasi nama (minimal 2 karakter, hanya huruf dan spasi)
    if (strlen($firstName) < 2 || strlen($lastName) < 2) {
        jsonResponse(false, 'Nama depan dan belakang minimal 2 karakter');
    }

    if (!preg_match("/^[a-zA-Z\s]+$/", $firstName) || !preg_match("/^[a-zA-Z\s]+$/", $lastName)) {
        jsonResponse(false, 'Nama hanya boleh mengandung huruf dan spasi');
    }

    // Validasi email
    if (!isValidEmail($email)) {
        jsonResponse(false, 'Format email tidak valid');
    }

    // Validasi nomor telepon
    if (!isValidPhone($phone)) {
        jsonResponse(false, 'Format nomor telepon tidak valid. Gunakan format 08xxxxxxxxxx');
    }

    // Validasi password
    if (strlen($password) < 8) {
        jsonResponse(false, 'Password minimal 8 karakter');
    }

    // Validasi minat wisata
    $validInterests = ['budaya', 'alam', 'kuliner', 'fotografi', 'petualangan'];
    if (!in_array($interest, $validInterests)) {
        jsonResponse(false, 'Minat wisata tidak valid');
    }

    // Koneksi ke database
    $conn = getConnection();

    // Cek apakah email sudah terdaftar
    $checkEmailQuery = "SELECT id FROM users WHERE email = :email";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        jsonResponse(false, 'Email sudah terdaftar. Silakan gunakan email lain atau login.');
    }

    // Cek apakah nomor telepon sudah terdaftar
    $checkPhoneQuery = "SELECT id FROM users WHERE phone = :phone";
    $stmt = $conn->prepare($checkPhoneQuery);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        jsonResponse(false, 'Nomor telepon sudah terdaftar. Silakan gunakan nomor lain.');
    }

    // Hash password
    $hashedPassword = hashPassword($password);

    // Insert user baru
    $insertQuery = "INSERT INTO users (first_name, last_name, email, phone, password, interest) 
                    VALUES (:first_name, :last_name, :email, :phone, :password, :interest)";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bindParam(':first_name', $firstName);
    $stmt->bindParam(':last_name', $lastName);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':interest', $interest);

    if ($stmt->execute()) {
        // Ambil ID user yang baru dibuat
        $userId = $conn->lastInsertId();

        // Set session untuk user yang baru register
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['logged_in'] = true;

        // Log aktivitas registrasi (opsional)
        $logQuery = "INSERT INTO activity_logs (user_id, activity, ip_address) VALUES (:user_id, 'register', :ip)";
        $logStmt = $conn->prepare($logQuery);
        $logStmt->bindParam(':user_id', $userId);
        $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
        $logStmt->execute();

        jsonResponse(true, "Selamat datang {$firstName}! Pendaftaran berhasil.", [
            'user_id' => $userId,
            'name' => $firstName . ' ' . $lastName,
            'email' => $email
        ]);
    } else {
        jsonResponse(false, 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.');
    }
} catch (PDOException $e) {
    // Log error ke file untuk debugging
    error_log("Registration Error: " . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan sistem. Silakan coba lagi nanti.');
} catch (Exception $e) {
    error_log("General Registration Error: " . $e->getMessage());
    jsonResponse(false, 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi.');
}
