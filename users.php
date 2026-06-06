<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') { header("Location: index.php"); exit; }
include 'koneksi.php';

$error = '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $username = mysqli_real_escape_string($koneksi, $_POST['username']);
        $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
        $role = $_POST['role'];
        $password = $_POST['password'];
        if ($username && $nama_lengkap && $password && in_array($role, ['admin', 'bendahara'])) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $check = mysqli_query($koneksi, "SELECT id FROM users WHERE username = '$username'");
            if (mysqli_num_rows($check) > 0) {
                $error = "Username sudah digunakan!";
            } else {
                $sql = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$hash', '$nama_lengkap', '$role')";
                if (mysqli_query($koneksi, $sql)) { header("Location: users.php?msg=added"); exit; }
                else { $error = "Gagal: " . mysqli_error($koneksi); }
            }
        } else { $error = "Semua field harus diisi!"; }
    }
    if (isset($_POST['edit_id'])) {
        $id = intval($_POST['edit_id']);
        $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
        $role = $_POST['role'];
        $password = $_POST['password'];
        if ($nama_lengkap && in_array($role, ['admin', 'bendahara'])) {
            $pass_clause = '';
            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pass_clause = ", password='$hash'";
            }
            $sql = "UPDATE users SET nama_lengkap='$nama_lengkap', role='$role' $pass_clause WHERE id=$id";
            if (mysqli_query($koneksi, $sql)) { header("Location: users.php?msg=updated"); exit; }
            else { $error = "Gagal update: " . mysqli_error($koneksi); }
        } else { $error = "Field tidak valid!"; }
    }
}
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id == $_SESSION['user_id']) {
        $error = "Tidak bisa menghapus akun sendiri!";
    } else {
        $count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM users"))['total'];
        if ($count <= 1) { $error = "Tidak bisa menghapus user terakhir!"; }
        else {
            $sql = "DELETE FROM users WHERE id = $id";
            if (mysqli_query($koneksi, $sql)) { header("Location: users.php?msg=deleted"); exit; }
            else { $error = "Gagal hapus: " . mysqli_error($koneksi); }
        }
    }
}

$where = "WHERE 1=1";
if ($search) { $where .= " AND (username LIKE '%$search%' OR nama_lengkap LIKE '%$search%')"; }
$query = "SELECT * FROM users $where ORDER BY role, username";
$result = mysqli_query($koneksi, $query);

$title = 'Pengguna';
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 style="font-weight:600;"><i class="bi bi-people" style="color:var(--primary);"></i> Kelola Pengguna</h4>
    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<?php if (isset($_GET['msg'])): $m = $_GET['msg']; $txt = ['added'=>'User berhasil ditambah.','updated'=>'User berhasil diupdate.','deleted'=>'User berhasil dihapus.'][$m] ?? ''; if($txt): ?>
<div class="alert alert-success alert-dismissible fade show glass"><?= $txt ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show glass"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="glass card-form mb-4">
    <h5 class="mb-3" style="font-weight:600;"><i class="bi bi-person-plus"></i> Tambah Pengguna</h5>
    <form method="POST">
        <div class="row g-3">
            <div class="col-md-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" placeholder="username" required></div>
            <div class="col-md-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="password" required></div>
            <div class="col-md-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" placeholder="nama lengkap" required></div>
            <div class="col-md-2"><label class="form-label">Role</label><select name="role" class="form-select" required><option value="bendahara">Bendahara</option><option value="admin">Admin</option></select></div>
            <div class="col-md-1 d-flex align-items-end"><button type="submit" name="add" class="btn btn-primary w-100" title="Tambah"><i class="bi bi-check-lg"></i></button></div>
        </div>
    </form>
</div>

<div class="d-flex gap-2 mb-3">
    <form method="GET" class="d-flex gap-2 flex-grow-1">
        <input type="text" name="search" class="form-control" placeholder="Cari username atau nama..." value="<?= htmlspecialchars($search) ?>" style="max-width:320px;">
        <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
        <a href="users.php" class="btn btn-outline-secondary">Reset</a>
    </form>
</div>

<div class="glass">
    <div style="padding:14px 20px;border-bottom:1px solid var(--border);font-weight:600;"><i class="bi bi-table"></i> Daftar Pengguna</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th style="width:40px">No</th><th>Username</th><th>Nama Lengkap</th><th>Role</th><th style="width:100px;text-align:center">Aksi</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['username']) ?><?php if ($row['id'] == $_SESSION['user_id']) echo ' <span class="badge badge-admin">You</span>'; ?></td>
                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td><span class="badge badge-<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                    <td style="text-align:center">
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>" title="Edit"><i class="bi bi-pencil"></i></button>
                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <a href="users.php?hapus=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus user ini?')" title="Hapus"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-inbox" style="font-size:28px;"></i><br>Belum ada user.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $res2 = mysqli_query($koneksi, "SELECT * FROM users $where ORDER BY role, username"); while ($row = mysqli_fetch_assoc($res2)): ?>
<div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Edit User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST"><div class="modal-body">
        <input type="hidden" name="edit_id" value="<?= $row['id'] ?>">
        <div class="mb-3"><label class="form-label">Username (tidak dapat diubah)</label><input type="text" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" disabled></div>
        <div class="mb-3"><label class="form-label">Password Baru (kosongkan jika tidak diubah)</label><input type="password" name="password" class="form-control" placeholder="biarkan kosong jika tidak diubah"></div>
        <div class="mb-3"><label class="form-label">Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required></div>
        <div class="mb-3"><label class="form-label">Role</label><select name="role" class="form-select" required><option value="bendahara" <?= $row['role'] == 'bendahara' ? 'selected' : '' ?>>Bendahara</option><option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option></select></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Update</button></div></form>
</div></div></div>
<?php endwhile; ?>
<?php include 'includes/footer.php'; ?>
