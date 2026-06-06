<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }
include 'koneksi.php';

$error = '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
        $jenis = $_POST['jenis'];
        if ($nama && in_array($jenis, ['masuk', 'keluar'])) {
            $sql = "INSERT INTO kategori (nama_kategori, jenis) VALUES ('$nama', '$jenis')";
            if (mysqli_query($koneksi, $sql)) { header("Location: kategori.php?msg=added"); exit; }
            else { $error = "Gagal: " . mysqli_error($koneksi); }
        } else { $error = "Semua field harus diisi!"; }
    }
    if (isset($_POST['edit_id'])) {
        $id = intval($_POST['edit_id']);
        $nama = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
        $jenis = $_POST['jenis'];
        if ($nama && in_array($jenis, ['masuk', 'keluar'])) {
            $sql = "UPDATE kategori SET nama_kategori='$nama', jenis='$jenis' WHERE id=$id";
            if (mysqli_query($koneksi, $sql)) { header("Location: kategori.php?msg=updated"); exit; }
            else { $error = "Gagal update: " . mysqli_error($koneksi); }
        } else { $error = "Semua field harus diisi!"; }
    }
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql = "DELETE FROM kategori WHERE id = $id";
    if (mysqli_query($koneksi, $sql)) { header("Location: kategori.php?msg=deleted"); exit; }
    else { $error = "Gagal hapus: " . mysqli_error($koneksi); }
}

$where = "WHERE 1=1";
if ($search) { $where .= " AND nama_kategori LIKE '%$search%'"; }
$query = "SELECT * FROM kategori $where ORDER BY jenis, nama_kategori";
$result = mysqli_query($koneksi, $query);

$title = 'Kategori';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="font-weight:600;"><i class="bi bi-tags" style="color:var(--primary);"></i> Kategori Transaksi</h4>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php if (isset($_GET['msg'])): $m = $_GET['msg']; $txt = ['added'=>'Kategori berhasil ditambah.','updated'=>'Kategori berhasil diupdate.','deleted'=>'Kategori berhasil dihapus.'][$m] ?? ''; if($txt): ?>
<div class="alert alert-success alert-dismissible fade show glass"><?= $txt ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show glass"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="glass card-form mb-4">
    <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-plus-circle"></i> Tambah Kategori</h5>
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Nama Kategori</label><input type="text" name="nama_kategori" class="form-control" placeholder="eg: Dana BOS, Belanja ATK" required></div>
            <div class="col-md-4"><label class="form-label">Jenis</label><select name="jenis" class="form-select" required><option value="masuk">Penerimaan (Masuk)</option><option value="keluar">Pengeluaran (Keluar)</option></select></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" name="add" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Simpan</button></div>
        </div>
    </form>
</div>

<div class="d-flex gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input type="text" name="search" class="form-control" placeholder="Cari nama kategori..." value="<?= htmlspecialchars($search) ?>" style="max-width:320px;">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
        <a href="kategori.php" class="btn btn-outline-secondary">Reset</a>
    </form>
</div>

<div class="glass">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);font-weight:600;"><i class="bi bi-table"></i> Daftar Kategori</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th style="width:40px">No</th><th>Nama Kategori</th><th>Jenis</th><th style="width:100px;text-align:center">Aksi</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_kategori']) ?></td>
                    <td><span class="badge badge-<?= $row['jenis'] == 'masuk' ? 'masuk' : 'keluar' ?>"><?= $row['jenis'] == 'masuk' ? 'Masuk' : 'Keluar' ?></span></td>
                    <td style="text-align:center">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                        <a href="kategori.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus kategori ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-inbox" style="font-size:28px;"></i><br>Belum ada kategori.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $res2 = mysqli_query($koneksi, "SELECT * FROM kategori $where ORDER BY jenis, nama_kategori"); while ($row = mysqli_fetch_assoc($res2)): ?>
<div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit Kategori</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><div class="modal-body">
        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
        <div class="mb-3"><label class="form-label">Nama Kategori</label><input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($row['nama_kategori']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Jenis</label><select name="jenis" class="form-select" required><option value="masuk" <?= $row['jenis'] == 'masuk' ? 'selected' : '' ?>>Penerimaan (Masuk)</option><option value="keluar" <?= $row['jenis'] == 'keluar' ? 'selected' : '' ?>>Pengeluaran (Keluar)</option></select></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button></div></form>
</div></div></div>
<?php endwhile; ?>
<?php include 'includes/footer.php'; ?>
