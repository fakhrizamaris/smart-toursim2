<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Reservasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3>Form Pemesanan Reservasi</h3>
                    </div>
                    <div class="card-body">
                        <form action="proses-pemesanan.php" method="POST" oninput="calculateTotal()">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Nama Depan</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Nama Belakang</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="jlh_paket" class="form-label">Jumlah Paket</label>
                                <input type="number" class="form-control" id="jlh_paket" name="jlh_paket" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_paket" class="form-label">Jenis Paket</label>
                                <select class="form-select" id="jenis_paket" name="jenis_paket" required>
                                    <option value="">Pilih Jenis Paket</option>
                                    <option value="premium" data-harga="500000">Premium - Rp 500.000</option>
                                    <option value="standart" data-harga="300000">Standart - Rp 300.000</option>
                                    <option value="hemat" data-harga="150000">Hemat - Rp 150.000</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="lama" class="form-label">Lama (hari)</label>
                                <input type="number" class="form-control" id="lama" name="lama" min="1" value="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="total_bayar" class="form-label">Total Bayar</label>
                                <input type="text" class="form-control" id="total_bayar" name="total_bayar" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Pesan Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function calculateTotal() {
            const jlhPaket = document.getElementById('jlh_paket').value;
            const jenisPaket = document.getElementById('jenis_paket');
            const lama = document.getElementById('lama').value;
            const totalBayar = document.getElementById('total_bayar');

            if (jlhPaket && jenisPaket.value && lama) {
                const hargaPerPaket = jenisPaket.options[jenisPaket.selectedIndex].dataset.harga;
                const total = parseInt(jlhPaket) * parseInt(hargaPerPaket) * parseInt(lama);
                totalBayar.value = 'Rp ' + total.toLocaleString('id-ID');
            } else {
                totalBayar.value = '';
            }
        }
    </script>
</body>

</html>