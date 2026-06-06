<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}
include 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> - MA Al-Amanah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.css" rel="stylesheet">
    <script>(function(){var t=localStorage.getItem('theme');if(t)document.documentElement.setAttribute('data-theme',t);})();</script>
    <style>
/* ==========================================================
   SEMANTIC DESIGN TOKENS
   ========================================================== */
:root{
    /* Backgrounds */
    --bg-primary: #f0f2f5;
    --bg-surface: rgba(255,255,255,0.72);
    --bg-surface-solid: #ffffff;
    --bg-surface-variant: rgba(240,242,245,0.85);
    /* Brand */
    --primary: #5865f2;
    --primary-hover: #4752c4;
    --primary-subtle: rgba(88,101,242,0.10);
    --secondary: #6c757d;
    --secondary-hover: #565e68;
    --secondary-subtle: rgba(108,117,125,0.10);
    /* Semantic */
    --error: #dc3545;
    --error-subtle: rgba(220,53,69,0.10);
    --success: #198754;
    --success-subtle: rgba(25,135,84,0.10);
    --warning: #e0a800;
    --warning-subtle: rgba(224,168,0,0.10);
    /* Text */
    --on-surface: #1e293b;
    --on-surface-muted: #64748b;
    --on-primary: #ffffff;
    /* Borders & Shadows */
    --border: rgba(0,0,0,0.08);
    --border-strong: rgba(0,0,0,0.14);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
    --shadow: 0 4px 16px rgba(0,0,0,0.08);
    --shadow-lg: 0 12px 40px rgba(0,0,0,0.12);
    /* Glass */
    --glass-bg: rgba(255,255,255,0.72);
    --glass-border: rgba(255,255,255,0.85);
    --glass-blur: 20px;
    /* Components */
    --sidebar-bg: rgba(255,255,255,0.62);
    --sidebar-link: #475569;
    --sidebar-link-hover: var(--primary);
    --sidebar-active-bg: var(--primary-subtle);
    --topbar-bg: rgba(255,255,255,0.75);
    --input-bg: rgba(255,255,255,0.85);
    --modal-bg: rgba(255,255,255,0.92);
    --table-stripe: rgba(0,0,0,0.018);
    --table-hover: rgba(0,0,0,0.032);
    /* Misc */
    --radius-sm: 8px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 20px;
    --transition-fast: 0.15s ease;
    --transition: 0.25s ease;
    --transition-slow: 0.4s ease;
}

[data-theme='dark']{
    --bg-primary: #0b0f1a;
    --bg-surface: rgba(16,20,34,0.65);
    --bg-surface-solid: #141828;
    --bg-surface-variant: rgba(255,255,255,0.04);
    --primary: #818cf8;
    --primary-hover: #6366f1;
    --primary-subtle: rgba(129,140,248,0.12);
    --secondary: #94a3b8;
    --secondary-hover: #cbd5e1;
    --secondary-subtle: rgba(148,163,184,0.10);
    --error: #f87171;
    --error-subtle: rgba(248,113,113,0.12);
    --success: #34d399;
    --success-subtle: rgba(52,211,153,0.12);
    --warning: #fbbf24;
    --warning-subtle: rgba(251,191,36,0.12);
    --on-surface: #e2e8f0;
    --on-surface-muted: #94a3b8;
    --on-primary: #0b0f1a;
    --border: rgba(255,255,255,0.07);
    --border-strong: rgba(255,255,255,0.12);
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.20);
    --shadow: 0 4px 16px rgba(0,0,0,0.30);
    --shadow-lg: 0 12px 40px rgba(0,0,0,0.45);
    --glass-bg: rgba(16,20,34,0.60);
    --glass-border: rgba(255,255,255,0.07);
    --sidebar-bg: rgba(14,18,30,0.68);
    --sidebar-link: #94a3b8;
    --sidebar-link-hover: #818cf8;
    --topbar-bg: rgba(14,18,30,0.65);
    --input-bg: rgba(255,255,255,0.05);
    --modal-bg: rgba(14,18,30,0.92);
    --table-stripe: rgba(255,255,255,0.015);
    --table-hover: rgba(255,255,255,0.04);
}

/* ==========================================================
   RESET & BASE
   ========================================================== */
*,*::before,*::after{box-sizing:border-box}
html{transition:background var(--transition-slow),color var(--transition-slow);overflow-x:hidden}
body{
    background:var(--bg-primary);color:var(--on-surface);
    font-family:'Segoe UI',system-ui,-apple-system,BlinkMacSystemFont,sans-serif;
    display:flex;min-height:100vh;margin:0;
    transition:background var(--transition-slow),color var(--transition-slow);
    -webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;
    line-height:1.5;font-size:14px;
    overflow-x:hidden;
    width:100%;
    max-width:100vw;
}
::selection{background:var(--primary);color:var(--on-primary)}

/* ==========================================================
   ACCESSIBILITY: FOCUS
   ========================================================== */
:focus-visible{
    outline:2px solid var(--primary);
    outline-offset:2px;
    border-radius:var(--radius-sm);
}
.btn:focus-visible,.form-control:focus-visible,.form-select:focus-visible,.nav-link:focus-visible{
    outline:2px solid var(--primary);
    outline-offset:2px;
}
a{color:var(--primary);text-decoration:none;transition:color var(--transition-fast)}
a:hover{color:var(--primary-hover)}

/* ==========================================================
   GLASS COMPONENT
   ========================================================== */
.glass{
    background:var(--glass-bg);
    backdrop-filter:blur(var(--glass-blur));
    -webkit-backdrop-filter:blur(var(--glass-blur));
    border:1px solid var(--glass-border);
    border-radius:var(--radius-lg);
    box-shadow:var(--shadow);
    color:var(--on-surface);
    transition:background var(--transition),border-color var(--transition),box-shadow var(--transition);
}

/* ==========================================================
   SIDEBAR
   ========================================================== */
.sidebar{
    width:260px;background:var(--sidebar-bg);
    backdrop-filter:blur(var(--glass-blur));-webkit-backdrop-filter:blur(var(--glass-blur));
    border-right:1px solid var(--border);
    color:var(--sidebar-link);display:flex;flex-direction:column;
    position:fixed;top:0;left:0;height:100vh;z-index:1000;
    transition:transform var(--transition),background var(--transition);
}
.sidebar-brand{
    padding:22px 20px 16px;font-size:17px;font-weight:700;color:var(--on-surface);
    border-bottom:1px solid var(--border);letter-spacing:-0.2px;
}
.sidebar-brand small{
    display:block;font-weight:500;font-size:11px;color:var(--on-surface-muted);
    margin-top:4px;letter-spacing:0.8px;text-transform:uppercase;
}
.sidebar .nav-link{
    color:var(--sidebar-link);padding:10px 16px;margin:2px 12px;font-size:14px;
    display:flex;align-items:center;gap:10px;border-radius:var(--radius-md);
    transition:all var(--transition-fast);text-decoration:none;
    min-height:42px;font-weight:450;
}
.sidebar .nav-link:hover{
    color:var(--sidebar-link-hover);background:var(--sidebar-active-bg);
    transform:translateX(2px);
}
.sidebar .nav-link.active{
    color:var(--sidebar-link-hover);background:var(--sidebar-active-bg);
    font-weight:600;box-shadow:inset 3px 0 0 var(--primary);
}
.sidebar .nav-link i{font-size:18px;width:20px;text-align:center;flex-shrink:0}
.sidebar-divider{border-top:1px solid var(--border);margin:8px 20px}
.sidebar-footer{margin-top:auto;border-top:1px solid var(--border);padding:12px 16px}
.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:999;backdrop-filter:blur(2px)}
.sidebar-overlay.show{display:block;animation:fadeIn var(--transition-fast) ease}

/* ==========================================================
   TOPBAR
   ========================================================== */
.main-wrapper{margin-left:260px;flex:1;display:flex;flex-direction:column;min-height:100vh;min-width:0;max-width:calc(100vw - 260px);overflow-x:hidden}
.topbar{
    height:60px;background:var(--topbar-bg);
    backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);
    border-bottom:1px solid var(--border);
    display:flex;align-items:center;justify-content:space-between;
    padding:0 28px;position:sticky;top:0;z-index:900;
    transition:background var(--transition);
}
.topbar .page-title{font-weight:600;font-size:17px;color:var(--on-surface)}
.topbar .user-info{display:flex;align-items:center;gap:12px;font-size:14px;color:var(--on-surface)}
.content{padding:28px;flex:1;animation:fadeInUp 0.35s ease}

/* ==========================================================
   STAT CARDS
   ========================================================== */
.card-stat{
    padding:22px 24px;border-radius:var(--radius-lg);color:#fff;
    overflow:hidden;position:relative;
    transition:transform var(--transition),box-shadow var(--transition);
}
.card-stat:hover{transform:translateY(-2px);box-shadow:var(--shadow-lg)}
.card-stat .stat-label{font-size:13px;opacity:0.9;margin-bottom:6px;font-weight:500}
.card-stat .stat-value{font-size:20px;font-weight:700;letter-spacing:-0.3px}
.card-stat .stat-icon{font-size:38px;opacity:0.22;position:absolute;top:18px;right:20px}
.bg-card-masuk{background:linear-gradient(135deg,#059669,#10b981)}
.bg-card-keluar{background:linear-gradient(135deg,#dc2626,#ef4444)}
.bg-card-saldo{background:linear-gradient(135deg,#4338ca,#6366f1)}

/* ==========================================================
   TABLES — Full dark-mode support
   ========================================================== */
.table{--bs-table-bg:transparent!important;--bs-table-color:var(--on-surface)!important;--bs-table-border-color:var(--border)!important;color:var(--on-surface)}
.table > :not(caption) > * > *{
    background-color:var(--bs-table-bg);color:var(--on-surface);
    transition:background var(--transition-fast);
}
.table thead{background:var(--bg-surface-variant)}
.table thead th{
    font-weight:600;font-size:13px;text-transform:uppercase;letter-spacing:0.5px;
    color:var(--on-surface-muted);padding:12px 16px;border-bottom:2px solid var(--border);
}
.table tbody td{padding:12px 16px;border-bottom:1px solid var(--border);vertical-align:middle}
.table tbody tr{transition:background var(--transition-fast)}
.table tbody tr:nth-child(even) td{background:var(--table-stripe)}
.table tbody tr:hover td{background:var(--table-hover)}
.table-light{--bs-table-bg:transparent!important;--bs-table-color:var(--on-surface)!important}
.table tfoot td{padding:12px 16px;font-weight:600;border-top:2px solid var(--border-strong);background:var(--bg-surface-variant)}

/* ==========================================================
   BADGES
   ========================================================== */
.badge{font-weight:500;padding:4px 10px;border-radius:var(--radius-sm);font-size:12px;letter-spacing:0.2px}
.badge-masuk{background:var(--success-subtle);color:var(--success)}
.badge-keluar{background:var(--error-subtle);color:var(--error)}
.badge-admin{background:var(--primary-subtle);color:var(--primary)}
.badge-bendahara{background:var(--secondary-subtle);color:var(--secondary)}

/* ==========================================================
   FORMS
   ========================================================== */
.form-control,.form-select{
    background:var(--input-bg);border:1px solid var(--border);color:var(--on-surface);
    border-radius:var(--radius-md);padding:10px 14px;
    transition:all var(--transition-fast);font-size:14px;min-height:42px;
}
.form-control:focus,.form-select:focus{
    background:var(--input-bg);border-color:var(--primary);
    box-shadow:0 0 0 3px var(--primary-subtle);color:var(--on-surface);
}
.form-label{font-weight:600;font-size:14px;color:var(--on-surface);margin-bottom:6px}
.form-control::placeholder{color:var(--on-surface-muted);opacity:0.65}
.form-check-input:checked{background-color:var(--primary);border-color:var(--primary)}

/* ==========================================================
   BUTTONS
   ========================================================== */
.btn{
    border-radius:var(--radius-md);font-weight:500;font-size:14px;
    transition:all var(--transition-fast);
    display:inline-flex;align-items:center;justify-content:center;gap:6px;
    min-height:40px;padding:8px 18px;cursor:pointer;
    text-decoration:none;border:1px solid transparent;
}
.btn:active{transform:scale(0.97)}
.btn-primary{background:var(--primary);border:none;color:var(--on-primary)}
.btn-primary:hover{background:var(--primary-hover);box-shadow:0 4px 12px rgba(88,101,242,0.3)}
.btn-success{background:var(--success);border:none;color:#fff}
.btn-success:hover{background:#15803d;box-shadow:0 4px 12px rgba(25,135,84,0.3)}
.btn-danger{background:var(--error);border:none;color:#fff}
.btn-danger:hover{background:#b91c1c;box-shadow:0 4px 12px rgba(220,53,69,0.3)}
.btn-outline-primary{border-color:var(--primary);color:var(--primary);background:transparent}
.btn-outline-primary:hover{background:var(--primary);color:var(--on-primary);border-color:var(--primary)}
.btn-outline-danger{border-color:var(--error);color:var(--error);background:transparent}
.btn-outline-danger:hover{background:var(--error);color:#fff;border-color:var(--error)}
.btn-outline-secondary{border-color:var(--border-strong);color:var(--on-surface-muted);background:transparent}
.btn-outline-secondary:hover{background:var(--bg-surface-variant);color:var(--on-surface);border-color:var(--border-strong)}
.btn-secondary{background:var(--bg-surface-variant);border:1px solid var(--border);color:var(--on-surface)}
.btn-secondary:hover{background:var(--primary-subtle);color:var(--primary);border-color:var(--primary)}
.btn-sm{min-height:34px;padding:5px 12px;font-size:13px;border-radius:var(--radius-sm)}
.btn-sm i{font-size:14px}

/* ==========================================================
   ALERTS
   ========================================================== */
.alert{
    border-radius:var(--radius-md);padding:14px 18px;
    transition:all var(--transition-fast);font-size:14px;
}
.alert-success{background:var(--success-subtle);border:1px solid rgba(25,135,84,0.2);color:var(--on-surface)}
.alert-danger{background:var(--error-subtle);border:1px solid rgba(220,53,69,0.2);color:var(--on-surface)}
.alert-info{background:var(--primary-subtle);border:1px solid rgba(88,101,242,0.2);color:var(--on-surface)}

/* ==========================================================
   MODALS — Glassmorphism + Animation
   ========================================================== */
.modal-content{
    background:var(--modal-bg);backdrop-filter:blur(var(--glass-blur));-webkit-backdrop-filter:blur(var(--glass-blur));
    border:1px solid var(--glass-border);border-radius:var(--radius-xl);color:var(--on-surface);
    box-shadow:var(--shadow-lg);max-width:100%;
}
.modal-dialog{max-width:min(500px,calc(100vw - 24px));margin:1.75rem auto}
.modal-header{border-bottom:1px solid var(--border);padding:18px 24px}
.modal-header .modal-title{font-weight:600;font-size:16px}
.modal-body{padding:24px}
.modal-footer{border-top:1px solid var(--border);padding:14px 24px}
.btn-close{filter:none;opacity:0.5;transition:opacity var(--transition-fast)}
.btn-close:hover{opacity:1}
/* Modal animation */
.modal.fade .modal-dialog{transform:translateY(20px) scale(0.97);transition:transform 0.3s ease}
.modal.show .modal-dialog{transform:translateY(0) scale(1)}

/* ==========================================================
   THEME TOGGLE
   ========================================================== */
.theme-toggle{
    width:40px;height:40px;border-radius:50%;
    border:1px solid var(--border);background:var(--glass-bg);
    color:var(--on-surface-muted);cursor:pointer;
    display:flex;align-items:center;justify-content:center;
    transition:all var(--transition-fast);font-size:16px;
}
.theme-toggle:hover{background:var(--sidebar-active-bg);color:var(--sidebar-link-hover);transform:rotate(30deg)}

/* ==========================================================
   CARD FORM
   ========================================================== */
.card-form{
    background:var(--glass-bg);backdrop-filter:blur(var(--glass-blur));-webkit-backdrop-filter:blur(var(--glass-blur));
    border:1px solid var(--glass-border);border-radius:var(--radius-lg);
    padding:28px;box-shadow:var(--shadow);color:var(--on-surface);
}
.card-form .form-label{font-weight:600;font-size:14px}

/* ==========================================================
   PAGINATION
   ========================================================== */
.page-link{background:var(--glass-bg);border:1px solid var(--border);color:var(--on-surface);border-radius:var(--radius-sm);margin:0 2px;transition:all var(--transition-fast)}
.page-item.active .page-link{background:var(--primary);border-color:var(--primary);color:var(--on-primary)}
.page-link:hover{background:var(--primary-subtle);color:var(--primary)}

/* ==========================================================
   SCROLLBAR
   ========================================================== */
::-webkit-scrollbar{width:8px;height:8px}
::-webkit-scrollbar-track{background:transparent}
::-webkit-scrollbar-thumb{background:var(--border-strong);border-radius:4px}
::-webkit-scrollbar-thumb:hover{background:var(--on-surface-muted)}

/* ==========================================================
   ANIMATIONS
   ========================================================== */
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
@keyframes fadeInUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes slideInRight{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
@keyframes scaleIn{from{opacity:0;transform:scale(0.95)}to{opacity:1;transform:scale(1)}}

/* Card hover lift */
.glass:hover{box-shadow:var(--shadow-lg)}

/* Row animation on table */
.table tbody tr{animation:fadeIn 0.25s ease backwards}
.table tbody tr:nth-child(1){animation-delay:0.02s}
.table tbody tr:nth-child(2){animation-delay:0.04s}
.table tbody tr:nth-child(3){animation-delay:0.06s}
.table tbody tr:nth-child(4){animation-delay:0.08s}
.table tbody tr:nth-child(5){animation-delay:0.10s}
.table tbody tr:nth-child(6){animation-delay:0.12s}
.table tbody tr:nth-child(7){animation-delay:0.14s}
.table tbody tr:nth-child(8){animation-delay:0.16s}
.table tbody tr:nth-child(9){animation-delay:0.18s}
.table tbody tr:nth-child(10){animation-delay:0.20s}

/* ==========================================================
   RESPONSIVE
   ========================================================== */
@media(max-width:1024px){
    .sidebar{width:240px}
    .main-wrapper{margin-left:240px}
    .content{padding:24px}
    .topbar{padding:0 20px}
}
@media(max-width:768px){
    .sidebar{transform:translateX(-100%);width:280px}
    .sidebar.show{transform:translateX(0)}
    .sidebar-overlay.show{display:block}
    .main-wrapper{margin-left:0;width:100%;max-width:100vw}
    .content{padding:16px}
    .topbar{height:56px;padding:0 12px}
    .topbar .page-title{font-size:15px}
    .card-stat{padding:16px 18px}
    .card-stat .stat-value{font-size:18px}
    .card-stat .stat-icon{font-size:30px;top:14px;right:16px}
    .card-form{padding:18px}
    .modal-body{padding:16px}
    .modal-header{padding:14px 18px}
    .modal-footer{padding:12px 18px}
    .modal-dialog{margin:12px}
    .row.g-3{--bs-gutter-x:10px;--bs-gutter-y:10px}
    .table thead th,.table tbody td{padding:8px 10px;font-size:12.5px}
    .table thead th{font-size:12px}
    .btn{min-height:38px;padding:6px 14px;font-size:13px}
    .btn-sm{min-height:32px;padding:4px 10px;font-size:12px}
    .topbar .user-info{gap:6px;font-size:13px}
    .topbar .user-info .badge{font-size:10px;padding:2px 6px}
    .col-md-1,.col-md-2,.col-md-3,.col-md-4,.col-md-6{flex:0 0 100%;max-width:100%}
    .sidebar .nav-link{padding:8px 14px;margin:1px 10px;font-size:13px;min-height:38px}
    .card-stat .stat-icon{display:none}
}
@media(max-width:480px){
    .content{padding:12px}
    .topbar{height:50px;padding:0 10px}
    .topbar .page-title{font-size:13px}
    .card-stat{padding:12px 14px}
    .card-stat .stat-label{font-size:12px}
    .card-stat .stat-value{font-size:16px}
    .d-flex.gap-2{flex-wrap:wrap;width:100%}
    .d-flex.gap-2 > *{flex:1 1 auto;min-width:0}
    .btn{min-height:36px;padding:5px 12px;font-size:12px}
    .btn-sm{min-height:30px;padding:3px 8px}
    .form-control,.form-select{font-size:13px;min-height:38px;padding:8px 12px}
    .form-label{font-size:13px}
    .glass{backdrop-filter:blur(12px)}
    .main-wrapper{width:100%;max-width:100vw;overflow:hidden}
    .sidebar{width:100%;max-width:320px}
    .modal-dialog{margin:8px}
    .modal-header{padding:12px 14px}
    .modal-body{padding:14px}
    .topbar .user-info span:last-child{display:none}
}
/* Table responsive fallback for many columns */
@media(max-width:768px){
    .table-wrapper{overflow-x:auto;-webkit-overflow-scrolling:touch}
    .table-wrapper .table{min-width:600px}
    .table-responsive table{min-width:600px}
}
@media(max-width:480px){
    .table-wrapper .table{min-width:480px}
    .table-responsive table{min-width:480px}
}

/* ==========================================================
   PRINT
   ========================================================== */
@media print{
    .sidebar,.topbar,.sidebar-overlay,.no-print{display:none!important}
    .main-wrapper{margin-left:0!important}
    .content{padding:0!important}
    .glass{
        backdrop-filter:none;-webkit-backdrop-filter:none;
        background:#fff!important;border:1px solid #ddd!important;
        box-shadow:none!important;color:#000!important;
    }
    .table{color:#000!important}
    .table thead{background:#f0f0f0!important}
    .table td,.table th{color:#000!important;border-color:#ddd!important}
    .badge-masuk,.badge-keluar,.badge-admin,.badge-bendahara{color:#000!important;background:#eee!important}
}

/* ==========================================================
   PREFERS REDUCED MOTION
   ========================================================== */
@media(prefers-reduced-motion:reduce){
    *,*::before,*::after{animation-duration:0.01ms!important;transition-duration:0.01ms!important}
}
    </style>
</head>
<body>
