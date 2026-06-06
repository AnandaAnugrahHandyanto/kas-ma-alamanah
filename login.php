<?php
session_start();
if (isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}
require_once 'koneksi.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['login']        = true;
            $_SESSION['user_id']      = $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Username atau password salah';
        }
    } else {
        $error = 'Username atau password salah';
    }
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MA Al-Amanah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <script>(function(){var t=localStorage.getItem('theme');if(t)document.documentElement.setAttribute('data-theme',t);})();</script>
    <style>
:root{
    --bg-primary: #f0f2f5;
    --bg-surface: rgba(255,255,255,0.72);
    --primary: #5865f2;
    --primary-hover: #4752c4;
    --primary-subtle: rgba(88,101,242,0.10);
    --error: #dc3545;
    --error-subtle: rgba(220,53,69,0.10);
    --on-surface: #1e293b;
    --on-surface-muted: #64748b;
    --border: rgba(0,0,0,0.08);
    --shadow: 0 4px 16px rgba(0,0,0,0.08);
    --glass-bg: rgba(255,255,255,0.72);
    --glass-border: rgba(255,255,255,0.85);
    --glass-blur: 20px;
    --input-bg: rgba(255,255,255,0.85);
    --transition: 0.3s;
}
[data-theme='dark']{
    --bg-primary: #0b0f1a;
    --bg-surface: rgba(16,20,34,0.65);
    --primary: #818cf8;
    --primary-hover: #6366f1;
    --primary-subtle: rgba(129,140,248,0.12);
    --error: #f87171;
    --error-subtle: rgba(248,113,113,0.12);
    --on-surface: #e2e8f0;
    --on-surface-muted: #94a3b8;
    --border: rgba(255,255,255,0.07);
    --shadow: 0 4px 16px rgba(0,0,0,0.30);
    --glass-bg: rgba(16,20,34,0.60);
    --glass-border: rgba(255,255,255,0.07);
    --input-bg: rgba(255,255,255,0.06);
}
*{box-sizing:border-box}
body{
    background:var(--bg-primary);
    min-height:100vh;display:flex;align-items:center;justify-content:center;
    margin:0;color:var(--on-surface);
    transition:background var(--transition),color var(--transition);
    font-family:'Segoe UI',system-ui,-apple-system,sans-serif;
}
.login-card{
    background:var(--glass-bg);
    backdrop-filter:blur(var(--glass-blur));
    -webkit-backdrop-filter:blur(var(--glass-blur));
    border:1px solid var(--glass-border);
    border-radius:20px;padding:40px;width:100%;max-width:420px;
    box-shadow:var(--shadow);color:var(--on-surface);
}
.form-control{
    background:var(--input-bg);border:1px solid var(--border);color:var(--on-surface);
    border-radius:10px;padding:12px 16px;transition:all 0.2s;font-size:14px;
    min-height:42px;
}
.form-control:focus{
    background:var(--input-bg);border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-subtle);color:var(--on-surface);
}
.form-control::placeholder{color:var(--on-surface-muted);opacity:0.6}
.form-label{font-weight:600;color:var(--on-surface);margin-bottom:6px;font-size:14px}
.btn-login{
    background:var(--primary);border:none;border-radius:10px;padding:12px;
    font-weight:600;width:100%;color:#fff;transition:all 0.2s;
    min-height:44px;cursor:pointer;font-size:14px;
}
.btn-login:hover{background:var(--primary-hover);transform:translateY(-1px);box-shadow:0 4px 12px rgba(88,101,242,0.3)}
.btn-login:active{transform:translateY(0)}
.error-msg{
    background:var(--error-subtle);color:var(--error);
    border:1px solid var(--error);border-radius:10px;padding:12px 16px;
    font-size:14px;margin-bottom:20px;
}
.login-title{font-weight:700;font-size:20px;margin:0}
.login-subtitle{color:var(--on-surface-muted);font-size:14px;margin-bottom:28px;margin-top:0}
.login-footer{text-align:center;margin-top:24px;font-size:13px;color:var(--on-surface-muted)}
.theme-toggle-login{
    position:fixed;top:20px;right:20px;width:44px;height:44px;border-radius:50%;
    border:1px solid var(--glass-border);background:var(--glass-bg);
    backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);
    color:var(--on-surface);cursor:pointer;display:flex;align-items:center;
    justify-content:center;font-size:18px;z-index:100;transition:all 0.3s;
}
.theme-toggle-login:hover{background:var(--primary-subtle);color:var(--primary);transform:scale(1.05)}
.mb-3{margin-bottom:1rem}
.mb-4{margin-bottom:1.5rem}
.text-center{text-align:center}
.display-4{font-size:2.5rem}
.mt-3{margin-top:1rem}
/* Mobile responsive */
*{box-sizing:border-box;overflow-wrap:break-word}
html,body{overflow-x:hidden;width:100%;max-width:100vw}
@media(max-width:480px){
    body{padding:12px}
    .login-card{padding:24px;border-radius:16px;max-width:100%}
    .login-title{font-size:18px}
    .login-subtitle{font-size:13px}
    .btn-login{min-height:42px;font-size:13px}
    .form-control{font-size:13px;padding:10px 14px}
    .theme-toggle-login{top:12px;right:12px;width:40px;height:40px;font-size:16px}
}
@media(max-width:360px){
    .login-card{padding:20px}
}
    </style>
</head>
<body>
    <button class="theme-toggle-login" id="themeToggle" title="Toggle theme">
        <i class="bi bi-moon-fill"></i>
    </button>
    <div class="login-card">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock display-4" style="color:var(--primary);"></i>
            <h5 class="login-title mt-3">Sign In</h5>
            <p class="login-subtitle">Sistem Akuntansi Kas - MA Al-Amanah</p>
        </div>
        <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username" required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>
        <div class="login-footer">MA Al-Amanah &copy; <?= date('Y') ?></div>
    </div>
    <script>
    (function(){
        var btn = document.getElementById('themeToggle');
        var html = document.documentElement;
        var icon = btn.querySelector('i');
        function setTheme(t){
            html.setAttribute('data-theme', t);
            localStorage.setItem('theme', t);
            icon.className = t === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
        }
        var current = html.getAttribute('data-theme') || 'light';
        setTheme(current);
        btn.addEventListener('click', function(){
            var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            setTheme(next);
        });
    })();
    </script>
</body>
</html>
