<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';

$dari = isset($_GET['dari']) ? $_GET['dari'] : date('Y-m-01');
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : date('Y-m-t');
$jenis_laporan = isset($_GET['jenis_laporan']) ? $_GET['jenis_laporan'] : 'semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

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
    die("Query Laporan Error: " . mysqli_error($koneksi));
}
$result = $res_query;

$q_total = "SELECT 
    COALESCE(SUM(CASE WHEN t.jenis='masuk' THEN t.jumlah ELSE 0 END),0) as total_masuk,
    COALESCE(SUM(CASE WHEN t.jenis='keluar' THEN t.jumlah ELSE 0 END),0) as total_keluar
FROM transaksi t
LEFT JOIN kategori k ON t.kategori_id = k.id
WHERE t.tanggal BETWEEN '$dari' AND '$sampai' $filter_jenis $filter_search";
$res_total = mysqli_query($koneksi, $q_total);
if (!$res_total) {
    die("Query Summary Error: " . mysqli_error($koneksi));
}
$total_data = mysqli_fetch_assoc($res_total);
$saldo = $total_data['total_masuk'] - $total_data['total_keluar'];

$lap_title = 'Laporan Keuangan';
if ($jenis_laporan == 'masuk') $lap_title = 'Laporan Penerimaan Kas';
elseif ($jenis_laporan == 'keluar') $lap_title = 'Laporan Pengeluaran Kas';
elseif ($jenis_laporan == 'saldo') $lap_title = 'Laporan Saldo Kas';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($lap_title) ?> - MA Al-Amanah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', sans-serif; font-size: 12px; margin: 30px; color: #333; background: #fff; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #333; padding-bottom: 14px; }
        .header h1 { font-size: 18px; margin: 0; text-transform: uppercase; letter-spacing: 1px; }
        .header h2 { font-size: 14px; margin: 6px 0 0; color: #555; font-weight: normal; }
        .header p { font-size: 12px; color: #888; margin: 6px 0 0; }
        .meta { margin-bottom: 16px; font-size: 11px; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #f0f0f0; font-weight: 600; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background: #f8f9fa; }
        .footer { margin-top: 24px; font-size: 11px; color: #888; text-align: center; border-top: 1px solid #ccc; padding-top: 10px; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; }
        .badge-masuk { background: #d1fae5; color: #065f46; }
        .badge-keluar { background: #fee2e2; color: #991b1b; }
        @media print { body { margin: 15px; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>MA AL-AMANAH</h1>
        <h2><?= htmlspecialchars($lap_title) ?></h2>
        <p>Periode: <?= date('d M Y', strtotime($dari)) ?> s.d. <?= date('d M Y', strtotime($sampai)) ?></p>
    </div>
    <div class="meta">
        <strong>Dicetak:</strong> <?= date('d M Y H:i') ?> &nbsp;|&nbsp; <strong>Oleh:</strong> <?= htmlspecialchars($_SESSION['nama_lengkap']) ?>
        <?php if ($search): ?> &nbsp;|&nbsp; <strong>Pencarian:</strong> <?= htmlspecialchars($search) ?><?php endif; ?>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th class="text-right">Penerimaan (Rp)</th>
                <th class="text-right">Pengeluaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                        <td><span class="badge badge-<?= $row['jenis'] == 'masuk' ? 'masuk' : 'keluar' ?>"><?= $row['jenis'] == 'masuk' ? 'Masuk' : 'Keluar' ?></span></td>
                        <td class="text-right"><?= $row['jenis'] == 'masuk' ? number_format($row['jumlah'], 0, ',', '.') : '-' ?></td>
                        <td class="text-right"><?= $row['jenis'] == 'keluar' ? number_format($row['jumlah'], 0, ',', '.') : '-' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" style="text-align:center;padding:20px;">Tidak ada data transaksi.</td></tr>
            <?php endif; ?>
        </tbody>
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total</td>
                <td class="text-right"><?= number_format($total_data['total_masuk'], 0, ',', '.') ?></td>
                <td class="text-right"><?= number_format($total_data['total_keluar'], 0, ',', '.') ?></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>
    <div class="footer">
        <p>Sistem Informasi Akuntansi Penerimaan & Pengeluaran Kas - MA Al-Amanah</p>
    </div>
    <div class="text-center no-print" style="margin-top:20px;">
        <button class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
        <a href="laporan.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
