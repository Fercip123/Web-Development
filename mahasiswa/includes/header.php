<?php
// Pastikan session & auth sudah di-include sebelum header
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?><?= APP_NAME ?></title>
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #4f46e5;
            --primary-dark: #3730a3;
            --sidebar-bg: #1e1b4b;
            --sidebar-hover: rgba(255,255,255,0.1);
        }
        * { font-family: 'Inter', sans-serif; }
        body { background-color: #f1f5f9; min-height: 100vh; }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            transition: transform .3s ease;
        }
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: .95rem;
            margin: 0;
        }
        .sidebar-brand small { color: rgba(255,255,255,.5); font-size: .75rem; }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
            padding: .6rem 1.5rem;
            border-radius: 8px;
            margin: 2px 8px;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all .2s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--sidebar-hover);
            color: #fff;
        }
        .sidebar .nav-link.active { background: var(--primary); }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; }
        .sidebar-section {
            padding: .5rem 1.5rem .25rem;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            color: rgba(255,255,255,.35);
            letter-spacing: .08em;
            margin-top: .5rem;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .content-area { padding: 1.5rem; flex: 1; }

        /* ── CARDS ── */
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,.08); border-radius: 12px; }
        .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; border-radius: 12px 12px 0 0 !important; padding: 1rem 1.25rem; }
        .stat-card { border-radius: 12px; padding: 1.25rem; color: #fff; }
        .stat-card .stat-icon { font-size: 2rem; opacity: .8; }
        .stat-card .stat-value { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label { opacity: .85; font-size: .85rem; }

        /* ── TABLES ── */
        .table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; font-weight: 600; background: #f8fafc; }
        .table td { vertical-align: middle; font-size: .875rem; }
        .table-hover tbody tr:hover { background: #f8fafc; }

        /* ── BUTTONS ── */
        .btn-primary { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-sm { font-size: .8rem; }

        /* ── AVATAR ── */
        .avatar { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: .85rem; }

        /* ── MOBILE ── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        /* Page header */
        .page-header { margin-bottom: 1.5rem; }
        .page-header h4 { font-weight: 700; color: #1e293b; margin: 0; }
        .page-header p { color: #64748b; margin: 0; font-size: .875rem; }

        /* Badge fix */
        .badge { font-weight: 500; }
    </style>
</head>
<body>

<?php if (isLoggedIn()): ?>
<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:36px;height:36px;background:var(--primary);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-mortarboard-fill text-white"></i>
            </div>
            <div>
                <h5>SIMAK</h5>
                <small>Sistem Informasi Mahasiswa</small>
            </div>
        </div>
    </div>
    <div class="py-3">
        <div class="sidebar-section">Menu Utama</div>
        <a href="<?= getBasePath() ?>index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="<?= getBasePath() ?>mahasiswa.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'mahasiswa.php' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Data Mahasiswa
        </a>
        <a href="<?= getBasePath() ?>jurusan.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'jurusan.php' ? 'active' : '' ?>">
            <i class="bi bi-building"></i> Data Jurusan
        </a>

        <?php if (isAdmin()): ?>
        <div class="sidebar-section">Administrasi</div>
        <a href="<?= getBasePath() ?>users.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'users.php' ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i> Manajemen User
        </a>
        <a href="<?= getBasePath() ?>log.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'log.php' ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Log Aktivitas
        </a>
        <?php endif; ?>

        <div class="sidebar-section">Akun</div>
        <a href="<?= getBasePath() ?>profile.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> Profil Saya
        </a>
        <a href="<?= getBasePath() ?>logout.php" class="nav-link text-danger-emphasis">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="main-content">
    <!-- TOPBAR -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-sm btn-light d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                <i class="bi bi-list fs-5"></i>
            </button>
            <nav aria-label="breadcrumb" class="d-none d-md-block">
                <ol class="breadcrumb mb-0" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="<?= getBasePath() ?>index.php" class="text-decoration-none">Home</a></li>
                    <?php if (isset($page_title)): ?>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($page_title) ?></li>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <!-- Dropdown user + tombol logout -->
            <div class="dropdown">
                <button class="btn btn-light d-flex align-items-center gap-2 px-2 py-1 border rounded-3"
                    style="font-size:.875rem;" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar" style="background:var(--primary);color:#fff;width:32px;height:32px;flex-shrink:0;">
                        <?= strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="d-none d-sm-block text-start">
                        <div style="font-size:.8rem;font-weight:600;color:#1e293b;line-height:1.2;"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></div>
                        <div style="font-size:.7rem;"><?= roleBadge($_SESSION['role'] ?? '') ?></div>
                    </div>
                    <i class="bi bi-chevron-down text-muted" style="font-size:.7rem;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-1" style="min-width:200px;">
                    <li>
                        <div class="px-3 py-2 border-bottom">
                            <div style="font-size:.8rem;font-weight:600;color:#1e293b;"><?= htmlspecialchars($_SESSION['nama'] ?? '') ?></div>
                            <div style="font-size:.75rem;color:#64748b;">@<?= htmlspecialchars($_SESSION['username'] ?? '') ?></div>
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item py-2" href="<?= getBasePath() ?>profile.php">
                            <i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 text-danger fw-semibold" href="<?= getBasePath() ?>logout.php"
                            onclick="return confirm('Yakin ingin logout?')">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- CONTENT -->
    <div class="content-area">
        <?= showAlerts() ?>
<?php endif; ?>