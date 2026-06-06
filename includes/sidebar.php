<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        MA Al-Amanah
        <small>Kas Accounting System</small>
    </div>
    <nav class="flex-column mt-3">
        <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <a href="penerimaan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'penerimaan.php' ? 'active' : '' ?>">
            <i class="bi bi-plus-circle"></i> Penerimaan Kas
        </a>
        <a href="pengeluaran.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'pengeluaran.php' ? 'active' : '' ?>">
            <i class="bi bi-dash-circle"></i> Pengeluaran Kas
        </a>

        <div class="sidebar-divider"></div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="kategori.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'kategori.php' ? 'active' : '' ?>">
            <i class="bi bi-tags"></i> Kategori
        </a>
        <a href="users.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Pengguna
        </a>
        <div class="sidebar-divider"></div>
        <?php endif; ?>

        <a href="laporan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'laporan.php' ? 'active' : '' ?>">
            <i class="bi bi-file-earmark-text"></i> Laporan
        </a>
    </nav>
    <div class="sidebar-footer d-flex align-items-center justify-content-between px-3 py-3">
        <button class="theme-toggle" id="themeToggle" title="Toggle theme">
            <i class="bi bi-moon-fill"></i>
        </button>
        <a href="logout.php" class="nav-link text-danger" style="margin:0;padding:8px 12px;border-radius:8px;font-size:13px;">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="document.getElementById('sidebar').classList.remove('show');this.classList.remove('show')"></div>

<div class="main-wrapper">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm no-print" style="background:var(--glass-bg);color:var(--on-surface);border:1px solid var(--border);border-radius:8px;" onclick="document.getElementById('sidebar').classList.toggle('show');document.getElementById('sidebarOverlay').classList.toggle('show')">
                <i class="bi bi-list"></i>
            </button>
            <span class="page-title"><?= htmlspecialchars($title ?? 'Dashboard') ?></span>
        </div>
        <div class="user-info">
            <span class="badge <?= isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? 'badge-admin' : 'badge-bendahara' ?>">
                <?= isset($_SESSION['role']) ? (($_SESSION['role'] === 'admin') ? 'Admin' : 'Bendahara') : 'User' ?>
            </span>
            <span style="font-size:14px;color:var(--on-surface-muted)"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'User') ?></span>
        </div>
    </div>
    <div class="content">