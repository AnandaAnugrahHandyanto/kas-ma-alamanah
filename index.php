<?php
$title = 'Dashboard';
include 'includes/header.php';
include 'includes/sidebar.php';

// Hitung ringkasan
$queryMasuk  = "SELECT COALESCE(SUM(jumlah),0) AS total FROM transaksi WHERE jenis='masuk'";
$queryKeluar = "SELECT COALESCE(SUM(jumlah),0) AS total FROM transaksi WHERE jenis='keluar'";
$totalMasuk  = mysqli_fetch_assoc(mysqli_query($koneksi, $queryMasuk))['total'];
$totalKeluar = mysqli_fetch_assoc(mysqli_query($koneksi, $queryKeluar))['total'];
$saldo       = $totalMasuk - $totalKeluar;

// Transaksi terbaru
$queryTerbaru = "SELECT t.*, k.nama_kategori, u.nama_lengkap
                 FROM transaksi t
                 LEFT JOIN kategori k ON t.kategori_id = k.id
                 LEFT JOIN users u ON t.user_id = u.id
                 ORDER BY t.tanggal DESC, t.id DESC
                 LIMIT 10";
$resultTerbaru = mysqli_query($koneksi, $queryTerbaru);
?>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="glass card-stat bg-card-masuk">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Penerimaan Kas</div>
                    <div class="stat-value">Rp <?= htmlspecialchars(number_format($totalMasuk, 0, ',', '.')) ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass card-stat bg-card-keluar">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Pengeluaran Kas</div>
                    <div class="stat-value">Rp <?= htmlspecialchars(number_format($totalKeluar, 0, ',', '.')) ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass card-stat bg-card-saldo">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Saldo Kas</div>
                    <div class="stat-value">Rp <?= htmlspecialchars(number_format($saldo, 0, ',', '.')) ?></div>
                </div>
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="glass mb-4">
    <div class="d-flex justify-content-between align-items-center" style="padding:16px 20px;border-bottom:1px solid var(--border);">
        <span style="font-weight:600;"><i class="bi bi-clock-history"></i> Transaksi Terbaru</span>
        <div class="d-flex gap-2">
            <a href="penerimaan.php" class="btn btn-success btn-sm" style="border-radius:8px;"><i class="bi bi-plus-circle"></i> Penerimaan</a>
            <a href="pengeluaran.php" class="btn btn-danger btn-sm" style="border-radius:8px;"><i class="bi bi-plus-circle"></i> Pengeluaran</a>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Jenis</th>
                    <th style="text-align:right">Jumlah</th>
                    <th>Operator</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($resultTerbaru) > 0): ?>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($resultTerbaru)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal']))) ?></td>
                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                            <td>
                                <?php if ($row['jenis'] === 'masuk'): ?>
                                    <span class="badge badge-masuk">Masuk</span>
                                <?php else: ?>
                                    <span class="badge badge-keluar">Keluar</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right" class="fw-semibold">
                                Rp <?= htmlspecialchars(number_format($row['jumlah'], 0, ',', '.')) ?>
                            </td>
                            <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox" style="font-size:28px;"></i><br>
                            Belum ada transaksi.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
