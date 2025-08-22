<?php
session_start();
include 'koneksi.php';

// Jika belum login, redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Ambil data user lengkap dari database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($conn);

// Logika untuk path foto profil
$foto_profil = $user['profile_picture'] ?? 'default.png';
$path_foto_profil = ($foto_profil === 'default.png') ? 'img/' . $foto_profil : 'uploads/' . $foto_profil;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Danau Toba Travel</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profil.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm fixed-top   ">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Danau Toba Travel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo htmlspecialchars($path_foto_profil); ?>" alt="Profil" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                            <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                            <li><a class="dropdown-item active" href="profil.php">Profil Saya</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container light-style flex-grow-1 container-p-y" style="margin-top: 80px;">
        <h4 class="font-weight-bold py-3 mb-2">
            Pengaturan Akun
        </h4>

        <div id="notification"></div>

        <div class="card overflow-hidden">
            <div class="row g-0">
                <div class="col-md-3 pt-0">
                    <div class="list-group list-group-flush account-settings-links">
                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#account-general">Umum</a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#account-change-password">Ganti Password</a>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="account-general">
                            <form action="update_profil_process.php" method="POST" enctype="multipart/form-data">

                                <div class="card-body d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($path_foto_profil); ?>" alt="Foto Profil" class="d-block ui-w-80" id="profileImagePreview">
                                    <div class="ms-4">
                                        <label class="btn btn-outline-primary">
                                            Pilih foto baru...
                                            <input type="file" class="account-settings-fileinput" name="profile_picture" id="profileImageInput" accept="image/png, image/jpeg">
                                        </label>
                                        <div class="text-muted small mt-1">Hanya JPG atau PNG. Ukuran maks 5MB.</div>
                                    </div>
                                </div>
                                <hr class="border-light m-0">

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label">Nama Depan</label>
                                            <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label">Nama Belakang</label>
                                            <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Telepon</label>
                                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Alamat</label>
                                        <textarea class="form-control" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select class="form-select" name="gender" required>
                                                <option value="pria" <?php if ($user['gender'] == 'pria') echo 'selected'; ?>>Pria</option>
                                                <option value="wanita" <?php if ($user['gender'] == 'wanita') echo 'selected'; ?>>Wanita</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group mb-3">
                                            <label class="form-label">Tanggal Lahir</label>
                                            <input type="date" class="form-control" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="text-end mt-3 p-3">
                                        <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" name="update_profile" class="btn btn-primary">Simpan Semua Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="account-change-password">
                            <div class="card-body pb-2">
                                <form action="ganti_password_process.php" method="POST">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Password Saat Ini</label>
                                        <input type="password" name="current_password" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Password Baru</label>
                                        <input type="password" name="new_password" class="form-control" required>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Ulangi Password Baru</label>
                                        <input type="password" name="confirm_new_password" class="form-control" required>
                                    </div>
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary">Ganti Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const profileImageInput = document.getElementById('profileImageInput');
            const profileImagePreview = document.getElementById('profileImagePreview');

            if (profileImageInput && profileImagePreview) {
                profileImageInput.addEventListener('change', function(event) {
                    if (event.target.files && event.target.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            profileImagePreview.src = e.target.result;
                        };
                        reader.readAsDataURL(event.target.files[0]);
                    }
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            const notificationDiv = document.getElementById('notification');
            if (status) {
                let message = '';
                let type = 'danger';
                if (status === 'success') {
                    message = 'Profil berhasil diperbarui!';
                    type = 'success';
                } else if (status === 'pwdsuccess') {
                    message = 'Password berhasil diganti!';
                    type = 'success';
                } else if (status === 'pwdmismatch') {
                    message = 'Konfirmasi password baru tidak cocok!';
                } else if (status === 'wrongpwd') {
                    message = 'Password saat ini salah!';
                } else if (status === 'filelarge') {
                    message = 'Ukuran file terlalu besar!';
                } else if (status === 'filetype') {
                    message = 'Tipe file tidak diizinkan!';
                } else {
                    message = 'Terjadi kesalahan!';
                }
                notificationDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
            }
        });
    </script>
</body>

</html>