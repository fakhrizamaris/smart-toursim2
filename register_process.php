<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];

    // Validasi password
    if ($password != $cpassword) {
        header("Location: register.html?error=password_mismatch");
        exit();
    }

    // Cek duplikasi email
    $sql_check = "SELECT id FROM users WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "s", $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        header("Location: register.html?error=email_exists");
        exit();
    }
    mysqli_stmt_close($stmt_check);

    // Enkripsi (hash) password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query INSERT yang sudah disesuaikan
    $sql_insert = "INSERT INTO users (first_name, last_name, email, password, phone, address, gender, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);

    // Bind parameter sesuai tipe data dan urutan
    mysqli_stmt_bind_param(
        $stmt_insert,
        "ssssssss",
        $first_name,
        $last_name,
        $email,
        $hashed_password,
        $phone,
        $address,
        $gender,
        $dob
    );

    if (mysqli_stmt_execute($stmt_insert)) {
        header("Location: login.html?success=registration_complete");
        exit();
    } else {
        header("Location: register.html?error=db_error");
        exit();
    }
    mysqli_stmt_close($stmt_insert);
}
mysqli_close($conn);
