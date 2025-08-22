<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

$nama_file_foto = $_SESSION['profile_picture'] ?? 'default.png';
$path_foto_navbar = ($nama_file_foto === 'default.png') ? 'img/' . $nama_file_foto : 'uploads/' . $nama_file_foto;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Danau Toba Travel</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold ms-5" href="dashboard.php">Danau Toba Travel</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center mx-5" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="<?php echo htmlspecialchars($path_foto_navbar); ?>" alt="Profil" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
              <span><?php echo htmlspecialchars($_SESSION['first_name']); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
              <li><a class="dropdown-item" href="profil.php">Profil Saya</a></li>
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

  <div class="container-fluid">
    <div class="row">
      <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
        <div class="sidebar-sticky pt-3">
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" aria-current="page" href="dashboard.php">
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="profil.php">
                Profil Saya
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Dashboard</h1>
        </div>

        <div class="p-5 mb-4 rounded-3">
          <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Selamat Datang, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>

            <a href="index.html" class="btn btn-outline-danger btn-sm">Kembali ke Beranda</a>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>