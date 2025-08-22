<?php
session_start();
include 'koneksi.php';

// Menyalakan laporan error untuk debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Pastikan tombol "Simpan Semua Perubahan" yang ditekan
if (isset($_POST['update_profile'])) {

    // --- BAGIAN 1: PROSES UPLOAD FOTO PROFIL (JIKA ADA) ---
    // Cek apakah ada file baru yang diupload dan tidak ada error
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file = $_FILES['profile_picture'];

        // Pengaturan file
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $fileName = basename($file['name']);
        $fileSize = $file['size'];
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        // Validasi file
        if (!in_array($fileType, $allowedTypes)) {
            header("Location: profil.php?status=filetype");
            exit();
        }
        if ($fileSize > 5 * 1024 * 1024) { // Maks 5MB
            header("Location: profil.php?status=filelarge");
            exit();
        }

        // Buat nama file unik untuk menghindari tumpang tindih
        $newFileName = uniqid('user' . $user_id . '_', true) . '.' . $fileType;
        $target_file = $target_dir . $newFileName;

        // Pindahkan file ke folder 'uploads'
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            // Update nama file di database
            $sql_update_pic = "UPDATE users SET profile_picture = ? WHERE id = ?";
            $stmt_pic = mysqli_prepare($conn, $sql_update_pic);
            mysqli_stmt_bind_param($stmt_pic, "si", $newFileName, $user_id);
            mysqli_stmt_execute($stmt_pic);

            // Perbarui session agar gambar di navbar langsung berubah!
            $_SESSION['profile_picture'] = $newFileName;
        } else {
            header("Location: profil.php?status=uploadfail");
            exit();
        }
    }

    // --- BAGIAN 2: PROSES UPDATE DATA DIRI ---
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    $sql_update_details = "UPDATE users SET 
                first_name = ?, last_name = ?, email = ?, phone = ?, 
                address = ?, gender = ?, dob = ? 
            WHERE id = ?";

    $stmt_details = mysqli_prepare($conn, $sql_update_details);
    mysqli_stmt_bind_param(
        $stmt_details,
        "sssssssi",
        $first_name,
        $last_name,
        $email,
        $phone,
        $address,
        $gender,
        $dob,
        $user_id
    );

    // Jalankan query update data diri
    if (mysqli_stmt_execute($stmt_details)) {
        // Perbarui session jika nama depan berubah
        $_SESSION['first_name'] = $first_name;
        // Arahkan kembali ke profil dengan pesan sukses
        header("Location: profil.php?status=success");
        exit();
    } else {
        // Arahkan kembali dengan pesan error
        header("Location: profil.php?status=error");
        exit();
    }
} else {
    // Jika ada yang mencoba mengakses file ini secara langsung
    echo "Akses tidak diizinkan.";
    exit();
}
