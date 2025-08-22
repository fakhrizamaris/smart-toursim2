<?php
// Pengaturan koneksi database
$host = "localhost";    // Nama host database
$user = "root";         // Username database (default 'root')
$pass = "";             // Password database (kosongkan jika tidak ada)
$db   = "db_mhs";       // Nama database Anda yang baru

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi gagal: " . mysqli_connect_error());
}
