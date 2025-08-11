<?php
/**
 * Helper untuk Authentication
 * File: php/auth.php
 */

session_start();
require_once 'config.php';

/**
 * Cek apakah user sudah login
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Ambil data user yang sedang login
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['user_email'] ?? null,
        'name' => $_SESSION['user_name'] ?? null
    ];
}

/**
 * Cek remember me token dan auto login
 */
function checkRememberToken() {
    if (isLoggedIn()) {
        return true;
    }

    if (!isset($_COOKIE['remember_token'])) {
        return false;
    }

    try {
        $conn = getConnection();
        $token = $_COOKIE['remember_token'];

        $query = "SELECT us.user_id, u.first_name, u.last_name, u.email, u.is_active 
                  FROM user_sessions us 
                  JOIN users u ON us.user_id = u.id 
                  WHERE us.session_token = :token 
                  AND us.expires_at > NOW()
                  AND u.is_active = 1";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['logged_in'] = true;

            return true;
        } else {
            // Token tidak valid atau expired, hapus cookie
            setcookie('remember_token', '', time() - 3600, '/');
            return false;
        }
    } catch (PDOException $e) {
        error_log("Remember token check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user
 */
function logout() {
    try {
        $conn = getConnection();
        
        // Hapus remember token jika ada
        if (isset($_COOKIE['remember_token']) && isset($_SESSION['user_id'])) {
            $deleteToken = "DELETE FROM user_sessions WHERE user_id = :user_id";
            $stmt = $conn->prepare($deleteToken);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
            
            // Hapus cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Log aktivitas logout
        if (isset($_SESSION['user_id'])) {
            try {
                $logQuery = "INSERT INTO activity_logs (user_id, activity, ip_address) VALUES (:user_id, 'logout', :ip)";
                $logStmt = $conn->prepare($logQuery);
                $logStmt->bindParam(':user_id', $_SESSION['user_id']);
                $logStmt->bindParam(':ip', $_SERVER['REMOTE_ADDR']);
                $