<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-t');
$jenis_laporan = isset($_GET['jenis_laporan']) ? $_GET['jenis_laporan'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$title = 'Laporan';
include 'includes/header.php';
include 'includes/sidebar.php';

$filter_jenis = '';
if ($jenis_laporan == 'masuk') $filter_jenis = "AND t.jenis = 'masuk'";
elseif ($jenis_laporan == 'keluar') $filter_jenis = "AND t.jenis = 'keluar'";

$filter_search = $search ? "AND (t.keterangan LIKE '%$search%' OR k.nama_kategori LIKE '%$search%')" : '';

$query = "SELECT t.*, k.nama_kategori, u.nama_lengkap 
          FROM transaksi t 
          LEFT JOIN kategori k ON t.kategori_id = k.id 
          LEFT JOIN users u ON t.user_id = u.id 
          WHERE t.tanggal BETWEEN '$dari' AND '$sampai' $filter_jenis $filter_search
          ORDER BY t.tanggal ASC, t.id ASC";
$res_query = mysqli_query($koneksi, $query);
if (!$res_query) {
    echo "<div class='alert alert-danger glass mb-4'>Query Laporan Error: " . mysqli_error($koneksi) . "</div>";
    $result = false;
} else {
    $result = $res_query;
}

$q_total = "SELECT 
    COALESCE(SUM(CASE WHEN t.jenis='masuk' THEN t.jumlah ELSE 0 END),0) as total_masuk,
    COALESCE(SUM(CASE WHEN t.jenis='keluar' THEN t.jumlah ELSE 0 END),0) as total_keluar
FROM transaksi t
LEFT JOIN kategori k ON t.kategori_id = k.id
WHERE t.tanggal BETWEEN '$dari' AND '$sampai' $filter_jenis $filter_search";
$res_total = mysqli_query($koneksi, $q_total);
if (!$res_total) {
    echo "<div class='alert alert-danger glass mb-4'>Query Summary Error: " . mysqli_error($koneksi) . "</div>";
    $total_data = ['total_masuk' => 0, 'total_keluar' => 0];
} else {
    $total_data = mysqli_fetch_assoc($res_total);
}
$saldo_ = $total_data['total_masuk'] - $total_data['total_keluar'];

$lap_title = 'Laporan Keuangan';
if ($jenis_laporan == 'masuk') $lap_title = 'Laporan Penerimaan Kas';
elseif ($jenis_laporan == 'keluar') $lap_title = 'Laporan Pengeluaran Kas';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="font-weight:600;"><i class="bi bi-file-earmark-text" style="color:var(--primary);"></i> Laporan Keuangan</h4>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<!-- Filter -->
<div class="glass mb-4" style="padding:20px 24px;">
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-2"><label class="form-label">Dari</label><input type="date" name="dari" class="form-control" value="<?= htmlspecialchars($dari) ?>"></div>
        <div class="col-md-2"><label class="form-label">Sampai</label><input type="date" name="sampai" class="form-control" value="<?= htmlspecialchars($sampai) ?>"></div>
        <div class="col-md-2"><label class="form-label">Jenis</label><select name="jenis_laporan" class="form-select">
            <option value="semua" <?= $jenis_laporan == 'semua' ? 'selected' : '' ?>>Semua Transaksi</option>
            <option value="masuk" <?= $jenis_laporan == 'masuk' ? 'selected' : '' ?>>Penerimaan Kas</option>
            <option value="keluar" <?= $jenis_laporan == 'keluar' ? 'selected' : '' ?>>Pengeluaran Kas</option>
        </select></div>
        <div class="col-md-3"><label class="form-label">Cari</label><input type="text" name="search" class="form-control" placeholder="Cari keterangan..." value="<?= htmlspecialchars($search) ?>"></div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tampilkan</button>
            <a href="laporan.php" class="btn btn-outline-secondary">Reset</a>
            <a href="laporan_cetak.php?dari=<?= urlencode($dari) ?>&sampai=<?= urlencode($sampai) ?>&jenis_laporan=<?= urlencode($jenis_laporan) ?>&search=<?= urlencode($search) ?>" class="btn btn-outline-danger" target="_blank"><i class="bi bi-printer"></i> Cetak</a>
        </div>
    </form>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="glass card-stat bg-card-masuk">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Total Penerimaan</div><div class="stat-value">Rp <?= number_format($total_data['total_masuk'], 0, ',', '.') ?></div></div>
                <div class="stat-icon"><i class="bi bi-arrow-down-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass card-stat bg-card-keluar">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Total Pengeluaran</div><div class="stat-value">Rp <?= number_format($total_data['total_keluar'], 0, ',', '.') ?></div></div>
                <div class="stat-icon"><i class="bi bi-arrow-up-circle"></i></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="glass card-stat bg-card-saldo">
            <div class="d-flex justify-content-between align-items-start">
                <div><div class="stat-label">Saldo Kas</div><div class="stat-value">Rp <?= number_format($saldo_, 0, ',', '.') ?></div></div>
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="glass">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);font-weight:600;"><i class="bi bi-table"></i> <?= htmlspecialchars($lap_title) ?> - <?= date('d M Y', strtotime($dari)) ?> s.d. <?= date('d M Y', strtotime($sampai)) ?></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th>Jenis</th><th style="text-align:right">Penerimaan</th><th style="text-align:right">Pengeluaran</th><th>User</th></tr></thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                    <td><span class="badge badge-<?= $row['jenis'] ?>"><?= $row['jenis'] == 'masuk' ? 'Masuk' : 'Keluar' ?></span></td>
                    <td style="text-align:right;color:var(--success);"><?= $row['jenis'] == 'masuk' ? 'Rp ' . number_format($row['jumlah'], 0, ',', '.') : '-' ?></td>
                    <td style="text-align:right;color:var(--error);"><?= $row['jenis'] == 'keluar' ? 'Rp ' . number_format($row['jumlah'], 0, ',', '.') : '-' ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap'] ?? '-') ?></td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="8" class="text-center py-4 text-muted"><i class="bi bi-inbox" style="font-size:28px;"></i><br>Tidak ada data transaksi pada periode ini.</td></tr>
                <?php endif; ?>
            </tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <tfoot><tr style="background:var(--bg-surface-variant);font-weight:600;">
                <td colspan="5" class="text-right">Total</td>
                <td style="text-align:right;color:var(--success);">Rp <?= number_format($total_data['total_masuk'], 0, ',', '.') ?></td>
                <td style="text-align:right;color:var(--error);">Rp <?= number_format($total_data['total_keluar'], 0, ',', '.') ?></td>
                <td></td>
            </tr></tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
