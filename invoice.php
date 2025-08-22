<?php
require_once 'koneksi.php'; // Pastikan Anda punya file ini

// Cek apakah ada ID yang dikirim
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID pemesanan tidak valid. <a href='dashboard.php'>Kembali ke Dashboard</a>");
}

$id_pemesanan = $_GET['id'];

// Menggunakan koneksi mysqli dari file koneksi.php Anda
$stmt = $conn->prepare("SELECT * FROM pemesanan WHERE id = ?");
$stmt->bind_param("i", $id_pemesanan);
$stmt->execute();
$result = $stmt->get_result();
$data_pemesanan = $result->fetch_assoc();

if (!$data_pemesanan) {
    die("Data pemesanan tidak ditemukan.");
}

// --- PERUBAHAN DIMULAI DI SINI ---

// 1. Membuat Nomor Invoice yang Diformat
$tanggal_format = date('Ymd', strtotime($data_pemesanan['tgl_pemesanan']));
$id_padded = str_pad($data_pemesanan['id'], 3, '0', STR_PAD_LEFT); // Menambahkan angka 0 di depan ID (contoh: 0042)
$nomor_invoice = "INV/" . $tanggal_format . "/" . $id_padded;

// --- AKHIR DARI PERUBAHAN ---

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Invoice <?= htmlspecialchars($nomor_invoice) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3>Invoice: <?= htmlspecialchars($nomor_invoice) ?></h3>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">Detail Pelanggan:</h5>
                        <p class="mb-1"><strong>Nama:</strong> <?= htmlspecialchars($data_pemesanan['first_name'] . ' ' . $data_pemesanan['last_name']) ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?= htmlspecialchars($data_pemesanan['email']) ?></p>
                        <p class="mb-1"><strong>Telepon:</strong> <?= htmlspecialchars($data_pemesanan['phone']) ?></p>
                        <p class="mb-1"><strong>Alamat:</strong> <?= htmlspecialchars($data_pemesanan['address']) ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h5 class="mb-3">Tanggal Pesan:</h5>
                        <p><?= date('d F Y, H:i', strtotime($data_pemesanan['tgl_pemesanan'])) ?> WIB</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Paket</th>
                                <th class="text-center">Lama Menginap</th>
                                <th class="text-center">Jumlah Paket</th>
                                <th class="text-end">Total Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Paket <?= ucfirst(htmlspecialchars($data_pemesanan['jenis_paket'])) ?></td>
                                <td class="text-center"><?= htmlspecialchars($data_pemesanan['lama']) ?> hari</td>
                                <td class="text-center"><?= htmlspecialchars($data_pemesanan['jlh_paket']) ?></td>
                                <td class="text-end">Rp <?= number_format($data_pemesanan['total_bayar'], 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">Grand Total</td>
                                <td class="text-end bg-light">Rp <?= number_format($data_pemesanan['total_bayar'], 0, ',', '.') ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 text-center no-print">
                    <p>Terima kasih telah melakukan pemesanan.</p>
                </div>

                <div class="mt-4 d-flex justify-content-end gap-2 no-print">
                    <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-tachometer-alt me-2"></i>Ke Dashboard</a>
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print me-2"></i>Cetak Invoice</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>