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

// Membuat Nomor Invoice yang Diformat
$tanggal_format = date('Ymd', strtotime($data_pemesanan['tgl_pemesanan']));
$id_padded = str_pad($data_pemesanan['id'], 3, '0', STR_PAD_LEFT);
$nomor_invoice = "INV/" . $tanggal_format . "/" . $id_padded;

// Format tanggal Indonesia
$bulan = [
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
];

$tanggal_indonesia = str_replace(array_keys($bulan), array_values($bulan), date('d F Y', strtotime($data_pemesanan['tgl_pemesanan'])));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($nomor_invoice) ?> - Danau Toba Travel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a1a1a;
            --secondary: #6b7280;
            --accent: #f97316;
            --bg-light: #f8fafc;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-primary);
            line-height: 1.5;
            height: 100vh;
            overflow-x: hidden;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: var(--bg-card);
            min-height: 90vh;
            box-shadow: 0 0 0 1px var(--border);
        }

        /* Header */
        .invoice-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .company-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .company-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .invoice-meta {
            text-align: right;
        }

        .invoice-number {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .invoice-date {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .status-paid {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            background: #dcfce7;
            color: #166534;
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        /* Content */
        .invoice-content {
            padding: 1rem;
            height: calc(90vh - 150px);
            display: flex;
            flex-direction: column;
        }

        /* Info Cards */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--bg-light);
            border-radius: 8px;
            padding: 1.25rem;
        }

        .info-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.025em;
        }

        .info-item {
            display: flex;
            margin-bottom: 0.75rem;
        }

        .info-label {
            min-width: 80px;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Table */
        .invoice-table {
            flex: 1;
            margin-bottom: 1rem;
        }

        .table-container {
            border: 1px solid var(--border);
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background: var(--primary);
            color: white;
            font-weight: 600;
            padding: 1rem;
            border: none;
            font-size: 0.875rem;
        }

        .table tbody td {
            padding: 1.25rem 1rem;
            border-color: var(--border);
            font-size: 0.875rem;
        }

        .package-name {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .package-desc {
            color: var(--text-secondary);
            font-size: 0.75rem;
        }

        .package-type {
            background: var(--accent);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .duration-badge {
            background: var(--bg-light);
            border: 1px solid var(--border);
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Total */
        .total-section {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }

        .total-row.final {
            border-top: 1px solid var(--border);
            padding-top: 1rem;
            margin-top: 0.5rem;
            font-weight: 600;
            font-size: 1.125rem;
        }

        .total-amount {
            font-weight: 600;
            color: var(--primary);
        }

        /* Footer */
        .invoice-footer {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 800px;
            background: var(--bg-card);
            border-top: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .thank-you {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-modern {
            padding: 0.625rem 1.25rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.15s ease;
            border: 1px solid var(--border);
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
        }

        .btn-outline {
            background: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border);
        }

        .btn-outline:hover {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        .btn-solid {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-solid:hover {
            background: #404040;
            color: white;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .invoice-container {
                box-shadow: none;
                min-height: auto;
            }

            .invoice-content {
                height: auto;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {

            .invoice-header,
            .invoice-content {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .invoice-footer {
                padding: 1rem 1.5rem;
                flex-direction: column;
                gap: 1rem;
            }

            .actions {
                width: 100%;
            }

            .btn-modern {
                flex: 1;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="row align-items-start">
                <div class="col-md-6">
                    <div class="company-name">Danau Toba Travel</div>
                    <div class="company-subtitle">Wisata Terpercaya & Berpengalaman</div>
                </div>
                <div class="col-md-6">
                    <div class="invoice-meta">
                        <div class="invoice-number">Invoice <?= htmlspecialchars($nomor_invoice) ?></div>
                        <div class="invoice-date"><?= $tanggal_indonesia ?> • <?= date('H:i', strtotime($data_pemesanan['tgl_pemesanan'])) ?> WIB</div>
                        <div class="status-paid">
                            <i class="fas fa-check-circle"></i>
                            PAID
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="invoice-content">
            <!-- Customer & Order Info -->
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-title">Bill To</div>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= htmlspecialchars($data_pemesanan['first_name'] . ' ' . $data_pemesanan['last_name']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email:</span>
                        <span class="info-value"><?= htmlspecialchars($data_pemesanan['email']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone:</span>
                        <span class="info-value"><?= htmlspecialchars($data_pemesanan['phone']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address:</span>
                        <span class="info-value"><?= htmlspecialchars($data_pemesanan['address']) ?></span>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-title">Order Details</div>
                    <div class="info-item">
                        <span class="info-label">Order ID:</span>
                        <span class="info-value">#<?= str_pad($data_pemesanan['id'], 4, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date:</span>
                        <span class="info-value"><?= $tanggal_indonesia ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value">Confirmed</span>
                    </div>
                </div>
            </div>

            <!-- Package Table -->
            <div class="invoice-table">
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-center">Duration</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="package-name">Travel Package</div>
                                    <div class="package-desc">
                                        <span class="package-type"><?= ucfirst(htmlspecialchars($data_pemesanan['jenis_paket'])) ?></span>
                                        • Danau Toba Experience
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="duration-badge"><?= htmlspecialchars($data_pemesanan['lama']) ?> Days</span>
                                </td>
                                <td class="text-center"><?= htmlspecialchars($data_pemesanan['jlh_paket']) ?></td>
                                <td class="text-end">Rp <?= number_format($data_pemesanan['total_bayar'], 0, ',', '.') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Total Section -->
            <div class="total-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="total-amount">Rp <?= number_format($data_pemesanan['total_bayar'], 0, ',', '.') ?></span>
                </div>
                <div class="total-row">
                    <span>Tax & Admin Fee:</span>
                    <span class="total-amount">Included</span>
                </div>
                <div class="total-row final">
                    <span>Total Amount:</span>
                    <span class="total-amount">Rp <?= number_format($data_pemesanan['total_bayar'], 0, ',', '.') ?></span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="invoice-footer no-print">
            <div class="thank-you">
                Thank you for choosing Danau Toba Travel
            </div>
            <div class="actions">
                <a href="dashboard.php" class="btn-modern btn-outline">
                    <i class="fas fa-arrow-left"></i>
                    Dashboard
                </a>
                <button onclick="window.print()" class="btn-modern btn-solid">
                    <i class="fas fa-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>