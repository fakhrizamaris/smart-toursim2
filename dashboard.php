<?php
session_start();
include 'koneksi.php';


if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit();
}

// 4. Ambil data profil dari sesi
$nama_file_foto = $_SESSION['profile_picture'] ?? 'default.png';
$path_foto_navbar = ($nama_file_foto === 'default.png') ? 'img/' . $nama_file_foto : 'uploads/' . $nama_file_foto;


// --- PENAMBAHAN KODE DIMULAI DI SINI ---

// 5. Siapkan array untuk menampung data pemesanan
$data_pemesanan = [];

// 6. Query untuk mengambil semua data dari tabel pemesanan
$sql = "SELECT id, first_name, last_name, jenis_paket, total_bayar, tgl_pemesanan FROM pemesanan ORDER BY tgl_pemesanan DESC";
$result = mysqli_query($conn, $sql);

// 7. Cek apakah query berhasil dan ada datanya
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $data_pemesanan[] = $row;
  }
} elseif (!$result) {
  // Jika query gagal, tampilkan pesan error
  die("Error saat mengambil data pemesanan: " . mysqli_error($conn));
}

// --- AKHIR DARI PENAMBAHAN KODE ---

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Danau Toba Travel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        <div class="card shadow-sm">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Daftar Pemesanan</h5>
            <a href="form-input.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Tambah Pesanan</a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>ID</th>
                    <th>Nama Pelanggan</th>
                    <th>Jenis Paket</th>
                    <th>Total Bayar</th>
                    <th>Tanggal Pesan</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($data_pemesanan)) : ?>
                    <?php foreach ($data_pemesanan as $data) : ?>
                      <tr>
                        <td><?= $data['id'] ?></td>
                        <td><?= htmlspecialchars($data['first_name'] . ' ' . $data['last_name']) ?></td>
                        <td><?= ucfirst($data['jenis_paket']) ?></td>
                        <td>Rp <?= number_format($data['total_bayar'], 0, ',', '.') ?></td>
                        <td><?= date('d M Y', strtotime($data['tgl_pemesanan'])) ?></td>
                        <td>
                          <a href="invoice.php?id=<?= $data['id'] ?>" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> Lihat Invoice
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else : ?>
                    <tr>
                      <td colspan="6" class="text-center">Belum ada data pemesanan.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>

  <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>