<?php
// =============================================
// SESSION & AUTENTIKASI
// =============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect ke login jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . getBasePath() . 'login.php');
        exit;
    }
}

// Ambil role user saat ini
function getUserRole() {
    return $_SESSION['role'] ?? '';
}

// Cek apakah user adalah admin
function isAdmin() {
    return getUserRole() === 'admin';
}

// Cek apakah user adalah operator atau admin
function isOperatorOrAdmin() {
    return in_array(getUserRole(), ['admin', 'operator']);
}

// Cek privilege akses
function hasPrivilege($required_role) {
    $role_hierarchy = ['viewer' => 1, 'operator' => 2, 'admin' => 3];
    $user_level = $role_hierarchy[getUserRole()] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 999;
    return $user_level >= $required_level;
}

// Require privilege tertentu
function requirePrivilege($required_role) {
    requireLogin();
    if (!hasPrivilege($required_role)) {
        $_SESSION['flash_error'] = 'Anda tidak memiliki akses untuk halaman ini.';
        header('Location: ' . getBasePath() . 'index.php');
        exit;
    }
}

// Flash message
function setFlash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function getFlash($type) {
    $msg = $_SESSION['flash_' . $type] ?? '';
    unset($_SESSION['flash_' . $type]);
    return $msg;
}

// Tampilkan alert Bootstrap
function showAlerts() {
    $types = ['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
    $html = '';
    foreach ($types as $key => $bsClass) {
        $msg = getFlash($key);
        if ($msg) {
            $icons = ['success' => 'check-circle', 'danger' => 'x-circle', 'warning' => 'exclamation-triangle', 'info' => 'info-circle'];
            $icon = $icons[$bsClass] ?? 'info-circle';
            $html .= '<div class="alert alert-' . $bsClass . ' alert-dismissible fade show" role="alert">
                <i class="bi bi-' . $icon . '-fill me-2"></i>' . htmlspecialchars($msg) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
    }
    return $html;
}

// Log aktivitas
function logActivity($pdo, $aksi, $tabel = null, $id_target = null, $keterangan = '') {
    if (!isLoggedIn()) return;
    try {
        $stmt = $pdo->prepare("INSERT INTO log_aktivitas (user_id, aksi, tabel_target, id_target, keterangan, ip_address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $aksi,
            $tabel,
            $id_target,
            $keterangan,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    } catch (Exception $e) {
        // Gagal log tidak menghentikan proses
    }
}

// Helper base path
function getBasePath() {
    return '/mahasiswa/';
}

// Badge role
function roleBadge($role) {
    $badges = [
        'admin'    => '<span class="badge bg-danger"><i class="bi bi-shield-fill me-1"></i>Admin</span>',
        'operator' => '<span class="badge bg-primary"><i class="bi bi-person-gear me-1"></i>Operator</span>',
        'viewer'   => '<span class="badge bg-secondary"><i class="bi bi-eye me-1"></i>Viewer</span>',
    ];
    return $badges[$role] ?? '<span class="badge bg-dark">' . htmlspecialchars($role) . '</span>';
}

// Status badge mahasiswa
function statusBadge($status) {
    $badges = [
        'Aktif'  => '<span class="badge bg-success">Aktif</span>',
        'Cuti'   => '<span class="badge bg-warning text-dark">Cuti</span>',
        'Lulus'  => '<span class="badge bg-info text-dark">Lulus</span>',
        'DO'     => '<span class="badge bg-danger">DO</span>',
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
}
