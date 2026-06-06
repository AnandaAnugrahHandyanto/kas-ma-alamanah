<?php
session_start();
if (!isset($_SESSION['login'])) { header("Location: login.php"); exit; }
include 'koneksi.php';

$msg = ''; $error = '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
        $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
        $jumlah = floatval($_POST['jumlah']);
        $kategori_id = isset($_POST['kategori_id']) && $_POST['kategori_id'] ? intval($_POST['kategori_id']) : 'NULL';
        if ($tanggal && $keterangan && $jumlah > 0) {
            $sql = "INSERT INTO transaksi (tanggal, keterangan, jenis, jumlah, kategori_id, user_id) VALUES ('$tanggal', '$keterangan', 'keluar', $jumlah, $kategori_id, {$_SESSION['user_id']})";
            if (mysqli_query($koneksi, $sql)) { header("Location: pengeluaran.php?msg=added"); exit; }
            else { $error = "Gagal: " . mysqli_error($koneksi); }
        } else { $error = "Semua field harus diisi dengan benar!"; }
    }
    if (isset($_POST['edit_id'])) {
        $id = intval($_POST['edit_id']);
        $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
        $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
        $jumlah = floatval($_POST['jumlah']);
        $kategori_id = isset($_POST['kategori_id']) && $_POST['kategori_id'] ? intval($_POST['kategori_id']) : 'NULL';
        if ($tanggal && $keterangan && $jumlah > 0) {
            $sql = "UPDATE transaksi SET tanggal='$tanggal', keterangan='$keterangan', jumlah=$jumlah, kategori_id=$kategori_id WHERE id=$id AND user_id={$_SESSION['user_id']}";
            if (mysqli_query($koneksi, $sql)) { header("Location: pengeluaran.php?msg=updated"); exit; }
            else { $error = "Gagal update: " . mysqli_error($koneksi); }
        } else { $error = "Semua field harus diisi dengan benar!"; }
    }
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM transaksi WHERE id = $id AND user_id = {$_SESSION['user_id']} AND jenis = 'keluar'";
    if (mysqli_query($koneksi, $sql)) { header("Location: pengeluaran.php?msg=deleted"); exit; }
    else { $error = "Gagal hapus: " . mysqli_error($koneksi); }
}

$where = "WHERE t.jenis = 'keluar'";
if ($search) {
    $where .= " AND (t.keterangan LIKE '%$search%' OR k.nama_kategori LIKE '%$search%')";
}

$query = "SELECT t.*, k.nama_kategori FROM transaksi t LEFT JOIN kategori k ON t.kategori_id = k.id $where ORDER BY t.tanggal DESC, t.id DESC";
$result = mysqli_query($koneksi, $query);
$title = 'Pengeluaran Kas';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="font-weight:600;"><i class="bi bi-arrow-up-circle" style="color:var(--error);"></i> Pengeluaran Kas</h4>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php if (isset($_GET['msg'])): $m = $_GET['msg']; $txt = ['added'=>'Data berhasil ditambah.','updated'=>'Data berhasil diupdate.','deleted'=>'Data berhasil dihapus.'][$m] ?? ''; if($txt): ?>
<div class="alert alert-success alert-dismissible fade show glass"><?= $txt ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show glass"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="glass card-form mb-4">
    <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-plus-circle"></i> Tambah Pengeluaran</h5>
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="col-md-5"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" placeholder="eg: Belanja ATK, Honor Guru" required></div>
            <div class="col-md-2"><label class="form-label">Kategori</label><select name="kategori_id" class="form-select"><option value="">-- Pilih --</option><?php $kat = mysqli_query($koneksi, "SELECT * FROM kategori WHERE jenis='keluar' ORDER BY nama_kategori"); while ($r = mysqli_fetch_assoc($kat)) echo "<option value='{$r['id']}'>{$r['nama_kategori']}</option>"; ?></select></div>
            <div class="col-md-2"><label class="form-label">Jumlah (Rp)</label><input type="number" name="jumlah" class="form-control" min="0.01" step="0.01" placeholder="0" required></div>
            <div class="col-12"><button type="submit" name="add" class="btn btn-danger w-100"><i class="bi bi-check-lg"></i> Simpan Pengeluaran</button></div>
        </div>
    </form>
</div>

<div class="d-flex gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input type="text" name="search" class="form-control" placeholder="Cari keterangan atau kategori..." value="<?= htmlspecialchars($search) ?>" style="max-width:320px;">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
        <a href="pengeluaran.php" class="btn btn-outline-secondary">Reset</a>
    </form>
</div>

<div class="glass">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);font-weight:600;"><i class="bi bi-table"></i> Daftar Pengeluaran Kas</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th style="width:40px">No</th><th>Tanggal</th><th>Keterangan</th><th>Kategori</th><th style="text-align:right">Jumlah</th><th style="width:100px;text-align:center">Aksi</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></td>
                    <td style="text-align:right;color:var(--error);" class="fw-semibold">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                    <td style="text-align:center">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                        <a href="pengeluaran.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus?')" title="Hapus"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="6" class="text-center py-4 text-muted"><i class="bi bi-inbox" style="font-size:28px;"></i><br>Belum ada data pengeluaran kas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $res2 = mysqli_query($koneksi, "SELECT t.*, k.nama_kategori FROM transaksi t LEFT JOIN kategori k ON t.kategori_id = k.id WHERE t.jenis = 'keluar' ORDER BY t.tanggal DESC, t.id DESC"); while ($row = mysqli_fetch_assoc($res2)): ?>
<div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Pengeluaran Kas</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><div class="modal-body">
        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
        <div class="mb-3"><label class="form-label">Tanggal</label><input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal'] ?>" required></div>
        <div class="mb-3"><label class="form-label">Keterangan</label><input type="text" name="keterangan" class="form-control" value="<?= htmlspecialchars($row['keterangan']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Kategori</label><select name="kategori_id" class="form-select"><option value="">-- Pilih --</option><?php $kat = mysqli_query($koneksi, "SELECT * FROM kategori WHERE jenis='keluar' ORDER BY nama_kategori"); while ($r = mysqli_fetch_assoc($kat)) echo "<option value='{$r['id']}' " . ($r['id'] == $row['kategori_id'] ? 'selected' : '') . ">{$r['nama_kategori']}</option>"; ?></select></div>
        <div class="mb-3"><label class="form-label">Jumlah (Rp)</label><input type="number" name="jumlah" class="form-control" min="0.01" step="0.01" value="<?= number_format($row['jumlah'], 2, '.', '') ?>" required></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button></div></form>
</div></div></div>
<?php endwhile; ?>
<?php include 'includes/footer.php'; ?>
