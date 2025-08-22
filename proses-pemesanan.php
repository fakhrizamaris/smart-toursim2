<?php
// Memanggil file koneksi Anda yang menggunakan MySQLi
require_once 'koneksi.php'; // $conn dari file ini

// Memastikan script dijalankan karena ada pengiriman data via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Mengambil data dari form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $jlh_paket = $_POST['jlh_paket'];
    $jenis_paket = $_POST['jenis_paket'];
    $lama = $_POST['lama'];
    
    // Kalkulasi ulang total bayar di server untuk keamanan
    $harga_paket = 0;
    if ($jenis_paket == 'premium') {
        $harga_paket = 500000;
    } elseif ($jenis_paket == 'standart') {
        $harga_paket = 300000;
    } elseif ($jenis_paket == 'hemat') {
        $harga_paket = 150000;
    }
    
    $total_bayar = (int)$jlh_paket * $harga_paket * (int)$lama;

    // Menyiapkan query INSERT dengan placeholder (?) untuk keamanan
    $sql = "INSERT INTO pemesanan (first_name, last_name, email, phone, address, jlh_paket, jenis_paket, lama, total_bayar) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Mengikat parameter ke placeholder
        // s = string, i = integer
        mysqli_stmt_bind_param($stmt, "sssssisii", 
            $first_name, 
            $last_name, 
            $email, 
            $phone, 
            $address, 
            $jlh_paket, 
            $jenis_paket, 
            $lama, 
            $total_bayar
        );

        // Menjalankan query
        if (mysqli_stmt_execute($stmt)) {
            // Jika berhasil, ambil ID terakhir
            $last_id = mysqli_insert_id($conn); // Ini pengganti lastInsertId() untuk MySQLi
            
            // Arahkan ke halaman invoice
            header("Location: invoice.php?id=" . $last_id);
            exit();
        } else {
            die("Error: Gagal menjalankan statement. " . mysqli_stmt_error($stmt));
        }
        
        // Menutup statement
        mysqli_stmt_close($stmt);

    } else {
        die("Error: Gagal menyiapkan statement. " . mysqli_error($conn));
    }

    // Menutup koneksi
    mysqli_close($conn);

} else {
    // Jika file diakses langsung, arahkan ke form input
    header("Location: form-input.php");
    exit();
}
?>