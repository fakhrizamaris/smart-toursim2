<?php
/**
 * Konfigurasi Database untuk Danau Toba Travel
 * File: php/config.php
 */

// Pengaturan database
define('DB_HOST', 'localhost');
define('DB_NAME', 'pariwisata');
define('DB_USER', 'root'); // sesuaikan dengan username database Anda
define('DB_PASS', ''); // sesuaikan dengan password database Anda

// Pengaturan timezone
date_default_timezone_set('Asia/Jakarta');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn = null;

    /**
     * Membuat koneksi ke database
     */
    public function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }

        return $this->conn;
    }

    /**
     * Menutup koneksi database
     */
    public function disconnect() {
        $this->conn = null;
    }
}

/**
 * Fungsi helper untuk mendapatkan koneksi database
 */
function getConnection() {
    $database = new Database();
    return $database->connect();
}

/**
 * Fungsi untuk membuat respons JSON
 */
function jsonResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Fungsi untuk validasi email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Fungsi untuk validasi nomor telepon Indonesia
 */
function isValidPhone($phone) {
    // Menghapus karakter selain angka
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Cek apakah dimulai dengan 08 atau 62
    if (preg_match('/^(08|62)[0-9]{8,12}$/', $phone)) {
        return true;
    }
    
    return false;
}

/**
 * Fungsi untuk sanitasi input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Fungsi untuk hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Fungsi untuk verifikasi password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Fungsi untuk generate token random
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * SQL untuk membuat tabel users (jalankan sekali saat setup database)
 */
$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    interest ENUM('budaya', 'alam', 'kuliner', 'fotografi', 'petualangan') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    reset_token VARCHAR(255) NULL,
    reset_token_expires DATETIME NULL,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

/**
 * SQL untuk membuat tabel user_sessions (untuk remember me functionality)
 */
$createSessionsTable = "
CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Uncomment baris berikut untuk membuat tabel secara otomatis saat pertama kali dijalankan
/*
try {
    $conn = getConnection();
    $conn->exec($createUsersTable);
    $conn->exec($createSessionsTable);
    echo "Tabel berhasil dibuat!";
} catch(PDOException $e) {
    echo "Error membuat tabel: " . $e->getMessage();
}
*/
?>